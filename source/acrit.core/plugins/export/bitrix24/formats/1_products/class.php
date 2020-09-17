<?
/**
 * Acrit Core: Bitrix24 plugin
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\Plugins\Bitrix24Rest as BitrixRest;

Loc::loadMessages(__FILE__);

class Bitrix24Products extends Bitrix24 {
	
	CONST DATE_UPDATED = '2019-05-01';


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

//		$arParams = [
//			'order' => ["SORT" => "ASC"],
//			'filter' => [],
//		];
//		$res = BitrixRest::executeMethod('crm.product.property.list', $this->strModuleId, $arParams, $intProfileID);
//		echo '<pre>'; print_r($res); echo '</pre>';

		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);

		$arFList = BitrixRest::executeMethod('crm.product.fields', $this->strModuleId, [], $intProfileID);
		if (!empty($arFList)) {
			$i = 0;
			foreach ($arFList as $code => $arItem) {
				switch ($code) {
					// Hidden fields
					case 'ID':
					case 'XML_ID':
					case 'DATE_CREATE':
					case 'TIMESTAMP_X':
						break;
					// Visible fields
					default:
						$arDefault = array();
						$arParams = array();
						switch ($code) {
							case 'NAME':
								$arDefault = array(
									array(
										'TYPE' => 'FIELD',
										'VALUE' => 'NAME',
									),
								);
								break;
							case 'ACTIVE':
								$arDefault = array(
									array(
										'TYPE' => 'FIELD',
										'VALUE' => 'ACTIVE',
									),
								);
								break;
							case 'SORT':
								$arDefault = array(
									array(
										'TYPE' => 'FIELD',
										'VALUE' => 'SORT',
									),
								);
								break;
							case 'DESCRIPTION':
								$arDefault = array(
									array(
										'TYPE' => 'FIELD',
										'VALUE' => 'DETAIL_TEXT',
										'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
									),
								);
								break;
							case 'DESCRIPTION_TYPE':
								$arDefault = array(
									array(
										'TYPE' => 'CONST',
										'CONST' => 'html',
									),
								);
								break;
							case 'CURRENCY_ID':
								$arDefault = array(
									array(
										'TYPE' => 'CONST',
										'CONST' => 'RUB',
									),
								);
								break;
							case 'PREVIEW_PICTURE':
								$arDefault = array(
									array(
										'TYPE' => 'FIELD',
										'VALUE' => 'PREVIEW_PICTURE',
										'MULTIPLE' => 'first',
									),
								);
								break;
							case 'DETAIL_PICTURE':
								$arDefault = array(
									array(
										'TYPE' => 'FIELD',
										'VALUE' => 'DETAIL_PICTURE',
										'MULTIPLE' => 'first',
									),
								);
								break;
							case 'SECTION_ID':
								$arDefault = array(
									array(
										'TYPE' => 'FIELD',
										'VALUE' => 'SECTION__NAME',
									),
								);
//								$arParams = array(
//									'MAXLENGTH' => '256',
//								);
								break;
						}
						$arField = array(
							'CODE' => $code,
							'DISPLAY_CODE' => $code,
							'NAME' => $arItem['title'],
							'SORT' => $i,
							'DESCRIPTION' => '',
							'REQUIRED' => $arItem['isRequired'],
							'MULTIPLE' => $arItem['isMultiple'],
						);
						if (!empty($arDefault)) {
							$arField['DEFAULT_VALUE'] = $arDefault;
						}
						if (!empty($arParams)) {
							$arField['PARAMS'] = $arParams;
						}
						$arResult[] = new Field($arField);
				}
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
		$arApiFields = array(
			'XML_ID' => $arElement['ID'],
		);
//		Log::getInstance($this->strModuleId)->add('(processElement) $arProfile: ' . print_r($arProfile, true), $intProfileID);
//		Log::getInstance($this->strModuleId)->add('(processElement) $arFields: ' . print_r($arFields, true), $intProfileID);
		foreach ($arFields as $code => $arItem) {
//			if (!Helper::isEmpty($arFields[$code])) {
				$arApiFields[$code] = Json::addValue($arFields[$code]);
//			}
		}
//		Log::getInstance($this->strModuleId)->add('(processElement) $arApiFields: ' . print_r($arApiFields, true), $intProfileID);
		# build JSON
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBitrix24GoodsJson') as $arHandler) {
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
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBitrix24GoodsResult') as $arHandler) {
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
		$intProcessLimit = intval($arData['PROFILE']['PARAMS']['PROCESS_LIMIT']);
		$intProcessNextPos = intval($arData['PROFILE']['PARAMS']['PROCESS_NEXT_POS']);

		// Fields data
		$arFieldsInfo = array();
		$arFList = BitrixRest::executeMethod('crm.product.fields', $this->strModuleId, [], $intProfileID);
		if (!empty($arFList)) {
			foreach ($arFList as $code => $arItem) {
				$arFieldsInfo[$code] = $arItem;
			}
		}
//		Log::getInstance($this->strModuleId)->add('$arFieldsInfo: ' . print_r($arFieldsInfo, true), $intProfileID);

		// Get export data
		$arQuery = [
			'PROFILE_ID' => $intProfileID,
			'!TYPE' => ExportData::TYPE_DUMMY,
		];
		$intExportCount = Helper::call($this->strModuleId, 'ExportData', 'getCount', [$arQuery]);
		$intOffset = 0;
		$intIndex = 0;
		$intProcessIndex = $intProcessNextPos;
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
			// Export item
			while ($arItem = $resItems->fetch()) {
				// Phased import
				if ($intProcessLimit) {
					if ($intIndex < $intProcessNextPos || $intIndex >= $intProcessNextPos + $intProcessLimit) {
						$intIndex++;
						continue;
					}
				}
				// Item data
				$arItemData = Json::decode($arItem['DATA']);
//				Log::getInstance($this->strModuleId)->add('$arItemData: ' . print_r($arItemData, true), $intProfileID);
				if ($arItemData['XML_ID']) {
					// Try to find item
					$arParams = [
						'order'  => ['SORT' => 'ASC'],
						'filter' => [
							'XML_ID' => $arItemData['XML_ID'],
						],
					];
					$arRemoteList = BitrixRest::executeMethod('crm.product.list', $this->strModuleId, $arParams, $intProfileID);
					$intRemoteItemID = $arRemoteList[0]['ID'];
					if ($intRemoteItemID) {
						$arRemoteItem = BitrixRest::executeMethod('crm.product.get', $this->strModuleId, ['id' => $intRemoteItemID], $intProfileID);
//						Log::getInstance($this->strModuleId)->add('$arRemoteItem: ' . print_r($arRemoteItem, true), $intProfileID);
						$arFields = $this->stepExport_prepareFields($arItemData, $arFieldsInfo, $arRemoteItem);
						if ($arFields['SECTION_ID']) {
							$arFields['SECTION_ID'] = $this->stepExport_findRemoteSection($intProfileID, $arFields['SECTION_ID'], $arRemoteItem);
						}
//						Log::getInstance($this->strModuleId)->add('$arFields: ' . print_r($arFields, true), $intProfileID);
						$res = BitrixRest::executeMethod('crm.product.update', $this->strModuleId, [
							'id' => $arRemoteItem['ID'],
							'fields' => $arFields,
						], $intProfileID);
//						Log::getInstance($this->strModuleId)->add('crm.product.update: ' . print_r($res, true), $intProfileID);
					}
					else {
						$arFields = $this->stepExport_prepareFields($arItemData, $arFieldsInfo);
						if ($arFields['SECTION_ID']) {
							$arFields['SECTION_ID'] = $this->stepExport_findRemoteSection($intProfileID, $arFields['SECTION_ID']);
						}
//						Log::getInstance($this->strModuleId)->add('$arFields: ' . print_r($arFields, true), $intProfileID);
						$res = BitrixRest::executeMethod('crm.product.add', $this->strModuleId, [
							'fields' => $arFields
						], $intProfileID);
//						Log::getInstance($this->strModuleId)->add('crm.product.add: ' . print_r($res, true), $intProfileID);
					}
				}
				$intIndex++;
				$intProcessIndex++;
			}
			// Count result
			$arData['SESSION']['EXPORT']['INDEX'] += $intExportedCount;
			$intOffset++;
		}
		// Phased import: reset start position
		if ($intProcessLimit) {
			if ($intProcessIndex == $intExportCount) {
				#Profile::setParam($intProfileID, array('PROCESS_NEXT_POS' => 0));
				Helper::call($this->strModuleId, 'Profile', 'setParam', [$intProfileID, array('PROCESS_NEXT_POS' => 0)]);
				Log::getInstance($this->strModuleId)->add(static::getMessage('PROCESS_PHASED_END_ALL'), $intProfileID);
			}
			else {
				#Profile::setParam($intProfileID, array('PROCESS_NEXT_POS' => $intProcessNextPos + $intProcessLimit));
				Helper::call($this->strModuleId, 'Profile', 'setParam', [$intProfileID, array('PROCESS_NEXT_POS' => $intProcessNextPos + $intProcessLimit)]);
				Log::getInstance($this->strModuleId)->add(static::getMessage('PROCESS_PHASED_END_STEP', array('#POSITION#' => ($intProcessNextPos + $intProcessLimit))), $intProfileID);
			}
		}

		return Exporter::RESULT_SUCCESS;
	}

	public function stepExport_prepareFields($arFields, $arFieldsInfo, $arRemoteFields=array()) {
		if (!empty($arFields)) {
			foreach ($arFields as $code => $value) {
				// Temporary value
				$arNewValue = array();
				$arValue = !is_array($value) ? array($value) : $value;
				// File
				if ($arFieldsInfo[$code]['type'] == 'product_file' ||
			        ($arFieldsInfo[$code]['type'] == 'product_property' && $arFieldsInfo[$code]['propertyType'] == 'F')) {
					// Add new values
					foreach ($arValue as $path) {
						$name = pathinfo($path, PATHINFO_BASENAME);
						$data = file_get_contents($path);
						$arNewValue[] = array("fileData" => array(
							$name,
							base64_encode($data)
						));
					}
					// Delete old values
					if ($arRemoteFields[$code] && is_array($arRemoteFields[$code])) {
						foreach ($arRemoteFields[$code] as $arRFValue) {
							$arNewValue[] = array(
								"valueId" => $arRFValue['valueId'],
								"value" => array('remove' => 'Y'),
							);
						}
					}
				}
				// List
				elseif ($arFieldsInfo[$code]['type'] == 'product_property' && $arFieldsInfo[$code]['propertyType'] == 'L') {
					if ($arFieldsInfo[$code] && is_array($arFieldsInfo[$code]['values'])) {
						foreach ($arFieldsInfo[$code]['values'] as $arFIValue) {
							if (in_array($arFIValue['VALUE'], $arValue)) {
								$arNewValue[] = $arFIValue['ID'];
							}
						}
					}
				}
				// Other types
				else {
					$arNewValue = $arValue;
				}
				// Returned value
				if (count($arNewValue) == 1) {
					$arNewValue = $arNewValue[0];
				}
				elseif (count($arNewValue) == 0) {
					$arNewValue = '';
				}
				$arFields[$code] = Bitrix24Rest::convEncForPortal($arNewValue);
			}
		}
		return $arFields;
	}

	public function stepExport_findRemoteSection($intProfileID, $strSectionName, $arRemoteFields=array()) {
		$intRemSectionID = false;
		$arParams = [
			'order'  => ['SORT' => 'ASC'],
			'filter' => [
				'NAME' => $strSectionName,
			],
		];
		$arRemoteSections = BitrixRest::executeMethod('crm.productsection.list', $this->strModuleId, $arParams, 
			$intProfileID);
//		Log::getInstance($this->strModuleId)->add('$arRemoteSections ' . print_r($arRemoteSections, true), $intProfileID);
		// Get exist section
		if (!empty($arRemoteSections)) {
			$intRemSectionID = $arRemoteSections[0]['ID'];
		}
		// Add new section
		else {
			$arParams = array();
			$arParams['fields'] = array(
                'NAME' => $strSectionName,
                'SECTION_ID' => 0,
			);
			$intRemSectionID = BitrixRest::executeMethod('crm.productsection.add', $this->strModuleId, $arParams, 
				$intProfileID);
		}
		return $intRemSectionID;
	}


	/**
	 * Ajax actions
	 */

	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		parent::ajaxAction($strAction, $arParams, $arJsonResult);
		#$arProfile = Profile::getProfiles($arParams['PROFILE_ID']);
		switch ($strAction) {
			case 'params_next_pos_reset':
				#$res = Profile::setParam($arParams['PROFILE_ID'], array('PROCESS_NEXT_POS' => 0));
				$res = Helper::call($this->strModuleId, 'Profile', 'setParam', [$arParams['PROFILE_ID'], array('PROCESS_NEXT_POS' => 0)]);
				if ($res) {
					$arJsonResult['result'] = 'ok';
				}
				else {
					$arJsonResult['result'] = 'error';
				}
				break;
		}
	}

}

?>