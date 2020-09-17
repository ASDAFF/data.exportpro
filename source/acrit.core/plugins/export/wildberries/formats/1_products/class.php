<?
/**
 * Acrit Core: Wildberries plugin
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\Export\ExportDataTable as ExportData;

Loc::loadMessages(__FILE__);

class WildberriesProducts extends Wildberries {
	
	CONST DATE_UPDATED = '2019-11-01';


	/**
	 * Base constructor
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}

	/* START OF BASE STATIC METHODS */

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return parent::getCode().'_PRODUCTS';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}

	/**
	 *	Is it subclass?
	 */
	public static function isSubclass(){
		return true;
	}

	/**
	 *	Are categories export?
	 */
	public function areCategoriesExport(){
		return true;
	}

	/* END OF BASE STATIC METHODS */

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		global $DB;
		$DB->Query("SET wait_timeout=28800");

//		$arParams = [
//			'order' => ["SORT" => "ASC"],
//			'filter' => [],
//		];
//		$res = BitrixRest::executeMethod('crm.product.property.list', $this->strModuleId, $arParams, $intProfileID);
//		echo '<pre>'; print_r($res); echo '</pre>';

		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);

		$order_id = $this->arProfile['PARAMS']['ORDER'];
		$arOrderRes = $this->request('new/'.$order_id);
		$arFList = $arOrderRes['Data'][0]['Fields'];

		if (!empty($arFList)) {
			$i = 0;
			foreach ($arFList as $arItem) {
				$arDefault = array();
				$arParams = array();
				$arField = array(
					'CODE' => $arItem['Id'],
					'DISPLAY_CODE' => $arItem['Id'],
					'NAME' => $arItem['Name'],
					'SORT' => $i,
					'DESCRIPTION' => '',
//					'MULTIPLE' => $arItem['isMultiple'],
				);
//				if ($arItem['IsRequired']) {
//					$arField['REQUIRED'] = true;
//				}
				if ($this->arProfile['PARAMS']['WB_DICT_LOAD'] == 'Y') {
					$arValues = $this->getWbDictionary($arItem['Id']);
					if ( ! empty($arValues)) {
						$arField['ALLOWED_VALUES']       = $arValues;
						$arField['POPUP_ALLOWED_VALUES'] = true;
					}
				}
				if (!empty($arDefault)) {
					$arField['DEFAULT_VALUE'] = $arDefault;
				}
				if (!empty($arParams)) {
					$arField['PARAMS'] = $arParams;
				}
				$arResult[] = new Field($arField);
				$i++;
			}
		}

		#
		$this->sortFields($arResult);
		return $arResult;
	}

	/**
	 *	Process single element
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];

		# Build exported data
		$arApiFields = [];
//		Log::getInstance($this->strModuleId)->add('(processElement) $arProfile: ' . print_r($arProfile, true), $intProfileID);
//		Log::getInstance($this->strModuleId)->add('(processElement) $arFields: ' . print_r($arFields, true), $intProfileID);
		foreach ($arFields as $code => $arItem) {
//			if (!Helper::isEmpty($arFields[$code])) {
				$arApiFields[$code] = Json::addValue($arFields[$code]);
//			}
		}
//		Log::getInstance($this->strModuleId)->add('(processElement) $arApiFields: ' . print_r($arApiFields, true), $intProfileID);
		# build JSON
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnWildberriesGoodsJson') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arApiFields, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# build result
		$arResult = array(
			'TYPE' => 'JSON',
			'DATA' => Json::encode($arApiFields),
			'CURRENCY' => '',
			'SECTION_ID' => $this->getElement_SectionID($intProfileID, $arElement),
			'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
			'DATA_MORE' => array(),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnWildberriesGoodsResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arApiFields, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# after..
		unset($intProfileID, $intElementID, $arApiFields);
		return $arResult;
	}


	/**
	 *	Get steps
	 */
	public function getSteps(){
		$arResult = array();
		$arResult['EXPORT'] = array(
			'NAME' => static::getMessage('STEP_EXPORT'),
			'SORT' => 100,
			'FUNC' => array($this, 'stepExport'),
		);
		return $arResult;
	}

	/**
	 *	Step: Export
	 */
	public function stepExport($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		$bIsCron = $arData['IS_CRON'];

		$intOrderId = $this->arProfile['PARAMS']['ORDER'];
		$intWbIdField = $this->arProfile['PARAMS']['WB_ID'];
		$arOrderRes = $this->request('new/'.$intOrderId);
		$arSpecData = $arOrderRes['Data'][0]['Data'];
		$intWbTemplateId = $arOrderRes['Data'][0]['Template']['Id'];

		if (!$intWbIdField) {
			Log::getInstance($this->strModuleId)->add('Empty Wildberries item identifier', $intProfileID);
			return;
		}

		// Get export data
		$arQuery = [
			'PROFILE_ID' => $intProfileID,
			'!TYPE' => ExportData::TYPE_DUMMY,
		];
		$intExportCount = Helper::call($this->strModuleId, 'ExportData', 'getCount', [$arQuery]);
		$intOffset = 0;
		$intIndex = 0;
		while ($intIndex < $intExportCount) {
			$intLimit = 1000;
			$strSortOrder = ToUpper($arData['PROFILE']['PARAMS']['SORT_ORDER']);
			if (!in_array($strSortOrder, array('ASC', 'DESC'))) {
				$strSortOrder = 'ASC';
			}
			$arQuery = [
				'filter' => [
					'PROFILE_ID' => $intProfileID,
					'!TYPE' => ExportData::TYPE_DUMMY,
				],
				'order'  => [
					'SORT' => $strSortOrder,
				],
				'select' => [
					'IBLOCK_ID',
					'ELEMENT_ID',
					'SECTION_ID',
					'TYPE',
					'DATA',
				],
				'limit' => $intLimit,
				'offset' => $intOffset * $intLimit,
			];
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$intExportedCount = 0;
			$arWbFindedItems = [];
			// Export item
			while ($arItem = $resItems->fetch()) {
				$arItemData = Json::decode($arItem['DATA']);
//				Log::getInstance($this->strModuleId)->add('$arItemData: ' . print_r($arItemData, true), $intProfileID);

				// Find WB item for this IB item
				$intWbItemIndex = false;
				foreach ($arSpecData as $r => $arRow) {
					foreach ($arRow as $arFields) {
						if ($arFields['FieldId'] == $intWbIdField && $arFields['Value'] && $arItemData[$arFields['FieldId']]
						    && $arFields['Value'] == $arItemData[$arFields['FieldId']]) {
							$intWbItemIndex = $r;
							$arWbFindedItems[] = $intWbItemIndex;
						}
					}
					if ($intWbItemIndex) {
						break;
					}
				}
//				Log::getInstance($this->strModuleId)->add('$intWbItemRowIndex ' . $intWbItemIndex, $intProfileID);
				// Update WB item data
				if ($intWbItemIndex !== false) {
					foreach ($arItemData as $id => $value) {
						$arWbItemFilledFields = [];
						foreach ($arSpecData[$intWbItemIndex] as $arFields) {
							if ($arFields['Value']) {
								$arWbItemFilledFields[] = $arFields['FieldId'];
							}
						}
						if (strlen((string)$value) > 0 && !in_array($id, $arWbItemFilledFields)) {
							$arSpecData[$intWbItemIndex][] = [
								'FieldId' => $id,
								'Value' => $value,
							];
						}
					}
				}

				$intIndex++;
			}

			// Not found in the catalog
			foreach ($arSpecData as $r => $arRow) {
				if (!in_array($r, $arWbFindedItems)) {
					$strProductName = '';
					foreach ($arRow as $arField) {
						if ($arField['FieldId'] == $intWbIdField) {
							$strProductName = $arField['Value'];
						}
					}
					Log::getInstance($this->strModuleId)->add(static::getMessage('PROCESS_NOT_FOUND') . $strProductName, $intProfileID);
				}
			}

			// Count result
			$arData['SESSION']['EXPORT']['INDEX'] += $intExportedCount;
			$intOffset++;
		}

		// Send specifications data
		$this->stepExport_send($intProfileID, $arSpecData, $intWbTemplateId);

		return Exporter::RESULT_SUCCESS;
	}

	// Send specification for check
	private function stepExport_send($intProfileID, $arData, $intWbTemplateId) {
		$order_id = $this->arProfile['PARAMS']['ORDER'];
		$arSpecData = [
			'Preorders' => [
				$order_id
			],
			'Template' => [
				'Id' => $intWbTemplateId,
			],
			'Data' => $arData,
		];
//		Log::getInstance($this->strModuleId)->add('load data: ' . print_r($arSpecData, true), $intProfileID);
		$arResp = $this->request('load', [], 'post', \Bitrix\Main\Web\Json::encode($arSpecData));
		if ($arResp['ResultCode']) {
			Log::getInstance($this->strModuleId)->add(static::getMessage('PROCESS_LOAD_RESPONSE') . $arResp['Message'] . ' [' . $arResp['ResultCode'] . ']', $intProfileID);
		}
		else {
			Log::getInstance($this->strModuleId)->add(static::getMessage('PROCESS_LOAD_RESPONSE') . 'Success', $intProfileID);
		}
		if ($arResp['Data']['Errors']) {
//			Log::getInstance($this->strModuleId)->add('resp data '.print_r($arResp['Data']['Data'], 1), $intProfileID);
			$this->stepExport_displayErrors($intProfileID, $arResp);
		}
	}

	private function stepExport_displayErrors($intProfileID, $arResp) {
		$intWbIdField = $this->arProfile['PARAMS']['WB_ID'];
		foreach ($arResp['Data']['Data'] as $r => $arRow) {
			$strFieldName = '';
			$arErrorGroups = [];
			foreach ($arRow as $arItem) {
				// Product identifier name
				if ($arItem['FieldId'] == $intWbIdField) {
					$strFieldName = $arItem['Value'];
				}
				// Errors
				if ($arItem['ErrorId']) {
					// Error title
					$title = '';
					$err_group_i = false;
					foreach ($arResp['Data']['Errors'] as $err_i => $arErGroup) {
						foreach ($arErGroup['Ids'] as $intErId) {
							if ($intErId == $arItem['ErrorId']) {
								$title = $arErGroup['Msg'];
								$err_group_i = $err_i;
							}
						}
					}
					if ($err_group_i !== false) {
						$arErrorGroups[$err_group_i]['title'] = $title;
						// Error fields
						$title = '';
						foreach ($arResp['Data']['Fields'] as $arField) {
							if ($arField['Id'] == $arItem['FieldId']) {
								$title = $arField['Name'];
							}
						}
						$arErrorGroups[$err_group_i]['items'][] = $title ? $title : $arItem['FieldId'];
					}
				}
			}
			foreach ($arErrorGroups as $arItem) {
				$arErrors = $arItem['items'];
				Log::getInstance($this->strModuleId)->add($strFieldName.' - '.$arItem['title'].': ' . implode(", ", $arErrors), $intProfileID);
			}
		}
	}


	/**
	 * Ajax actions
	 */

	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		parent::ajaxAction($strAction, $arParams, $arJsonResult);
		#$arProfile = Profile::getProfiles($arParams['PROFILE_ID']);
		$strVkGroupId = strval($this->arProfile['PARAMS']['GROUP_ID']);
		$strVkOwnerId = intval('-' . $strVkGroupId);
		switch ($strAction) {
		}
	}

	/**
	 * Get Wildberries field dictionary
	 */
	protected function getWbDictionary($strFieldId) {
		$arList = false;
		$arResp = $this->request('dictionary/' . $strFieldId);
		if (!$arResp['ResultCode']) {
			$arList = [];
			foreach ($arResp['Data'] as $value) {
				$arList[$value] = $value;
			}
		}
		return $arList;
	}
}

?>