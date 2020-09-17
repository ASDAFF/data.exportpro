<?
/**
 * Acrit Core: Bitrix plugin
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Log,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\Plugins\BitrixRest as BitrixRest,
	\Acrit\Core\Json;

Loc::loadMessages(__FILE__);

class BitrixIblock extends Bitrix {
	
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
		return parent::getCode().'_IBLOCK';
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

		#$arProfile = Profile::getProfiles($intProfileID);

		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);

		if ($this->arProfile['PARAMS']['IBLOCK_ID']) {
			#$obClass = new \ReflectionClass('BitrixRest');
			#var_dump($obClass->getFileName());
			#die();
			$arFList = BitrixRest::executeMethod($this->strModuleId, 'acrit_iblock.fields.get', ['ENTITY' => $this->arProfile['PARAMS']['IBLOCK_ID']], $intProfileID);
			// Additional fields
			if (is_array($arFList) && !empty($arFList)) {
				$arFList['FIELDS_INFO']['IBLOCK_SECTION_NAME']         = $arFList['IBLOCK_SECTION_ID'];
				$arFList['FIELDS_INFO']['IBLOCK_SECTION_NAME']['NAME'] = static::getMessage('GET_FIELDS_SECTION_NAME');
			}
		}

		if (!empty($arFList)) {
			$i = 0;
			foreach ($arFList as $fsect_code => $arFSection) {
				foreach ($arFSection as $code => $arItem) {
					$field_id = $fsect_code . '_' . $code;
					$field_code = $code . ' (' . $fsect_code . ')';
					$field_name = $arItem['NAME'];
					if ($fsect_code == 'PRICES_INFO') {
						$field_id = $fsect_code . '_' . $arItem['ID'];
					}
					switch ($code) {
						// Hidden fields
						case 'ID':
						case 'XML_ID':
						case 'DATE_CREATE':
						case 'TIMESTAMP_X':
						case 'IBLOCK_TYPE_ID':
						case 'IBLOCK_CODE':
						case 'IBLOCK_NAME':
						case 'IBLOCK_ID':
						case 'IBLOCK_SECTION_ID':
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
								case 'CODE':
									$arDefault = array(
										array(
											'TYPE' => 'FIELD',
											'VALUE' => 'CODE',
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
								case 'PREVIEW_TEXT':
									$arDefault = array(
										array(
											'TYPE' => 'FIELD',
											'VALUE' => 'PREVIEW_TEXT',
											'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
										),
									);
									break;
								case 'DETAIL_TEXT':
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
								case 'IBLOCK_SECTION_ID':
								case 'IBLOCK_SECTION_NAME':
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
								'CODE' => $field_id,
								'DISPLAY_CODE' => $field_code,
								'NAME' => $field_name,
								'SORT' => $i,
								'DESCRIPTION' => '',
								'REQUIRED' => ($arItem['IS_REQUIRED'] == 'Y' || in_array('REQ', $arItem['ATTRIBUTES'])),
								'MULTIPLE' => ($arItem['MULTIPLE'] == 'Y'),
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
		}

		#
		$this->sortFields($arResult);
		return $arResult;
	}

	/**
	 *	TODO: Process single element
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
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBitrixGoodsJson') as $arHandler) {
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
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBitrixGoodsResult') as $arHandler) {
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

		// Fields data
		$arFieldsInfo = array();
		if ($arData['PROFILE']['PARAMS']['IBLOCK_ID']) {
			$arFList = BitrixRest::executeMethod($this->strModuleId, 'acrit_iblock.fields.get', ['ENTITY' => $arData['PROFILE']['PARAMS']['IBLOCK_ID']], $intProfileID);
			// Additional fields
			if (is_array($arFList) && !empty($arFList)) {
				$arFList['FIELDS_INFO']['IBLOCK_SECTION_NAME']         = $arFList['IBLOCK_SECTION_ID'];
				$arFList['FIELDS_INFO']['IBLOCK_SECTION_NAME']['NAME'] = static::getMessage('GET_FIELDS_SECTION_NAME');
			}
		}
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
		#$intExportCount = ExportData::getCount($arQuery);
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
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'!TYPE' => ExportData::TYPE_DUMMY,
				),
				'order'  => array(
					'SORT' => $strSortOrder,
				),
				'select' => array(
					'IBLOCK_ID',
					'ELEMENT_ID',
					'SECTION_ID',
					'TYPE',
					'DATA',
				),
				'limit' => $intLimit,
				'offset' => $intOffset * $intLimit,
			];
			#$resItems = ExportData::getList($arQuery);
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$intExportedCount = 0;
			// Export item
			while ($arItem = $resItems->fetch()) {
				$arItemData = Json::decode($arItem['DATA']);
//				Log::getInstance($this->strModuleId)->add('$arItemData: ' . print_r($arItemData, true), $intProfileID);
				if ($arItemData['XML_ID']) {
					// Try to find item
					$arParams = [
						'ENTITY' => $arData['PROFILE']['PARAMS']['IBLOCK_ID'],
						'FILTER' => [
							'NAME' => $arItemData['FIELDS_INFO_NAME'],
						],
					];
					$arRemoteList = BitrixRest::executeMethod($this->strModuleId, 'acrit_iblock.item.get', $arParams, $intProfileID);
//					Log::getInstance($this->strModuleId)->add('$arRemoteList: ' . print_r($arRemoteList, true), $intProfileID);
					$intRemoteItemID = $arRemoteList[0]['ID'];
					if ($intRemoteItemID) {
						$arRemoteItem = $arRemoteList[0];
						$arFields = $this->stepExport_prepareFields($arItemData, $arFieldsInfo, $arRemoteItem);
						$arFields = $this->stepExport_convertFieldsList($arFields);
						$arFields['ID'] = $intRemoteItemID;
						$arFields['ENTITY'] = $arData['PROFILE']['PARAMS']['IBLOCK_ID'];
						if ($arFields['IBLOCK_SECTION_NAME']) {
							$arFields['IBLOCK_SECTION_ID'] = $this->stepExport_findRemoteSection($intProfileID, $arFields['IBLOCK_SECTION_NAME'], $arRemoteItem);
						}
//						Log::getInstance($this->strModuleId)->add('$arFields: ' . print_r($arFields, true), $intProfileID);
						$res = BitrixRest::executeMethod($this->strModuleId, 'acrit_iblock.item.update', $arFields, $intProfileID, false);
						if ($res['error'] == 'ERROR_CORE') {
//							Log::getInstance($this->strModuleId)->add('acrit_iblock.item.update: ' . $res['error_description'], $intProfileID);
						}
					}
					else {
						$arFields = $this->stepExport_prepareFields($arItemData, $arFieldsInfo);
						$arFields = $this->stepExport_convertFieldsList($arFields);
						$arFields['ENTITY'] = $arData['PROFILE']['PARAMS']['IBLOCK_ID'];
						if ($arFields['IBLOCK_SECTION_NAME']) {
							$arFields['IBLOCK_SECTION_ID'] = $this->stepExport_findRemoteSection($intProfileID, $arFields['IBLOCK_SECTION_NAME']);
						}
//						Log::getInstance($this->strModuleId)->add('$arFields: ' . print_r($arFields, true), $intProfileID);
						$res = BitrixRest::executeMethod($this->strModuleId, 'acrit_iblock.item.add', $arFields, $intProfileID, false);
						if ($res['error'] == 'ERROR_CORE') {
//							Log::getInstance($this->strModuleId)->add('acrit_iblock.item.add: ' . $res['error_description'], $intProfileID);
						}
					}
				}
				$intIndex++;
			}
			// Count result
			$arData['SESSION']['EXPORT']['INDEX'] += $intExportedCount;
			$intOffset++;
		}

		return Exporter::RESULT_SUCCESS;
	}

	public function stepExport_convertFieldsList($arFields) {
		$arSendFields = array();
		foreach ($arFields as $k => $value) {
			if (strpos($k, 'FIELDS_INFO') !== false) {
				$code = str_replace('FIELDS_INFO_', '', $k);
				$arSendFields[$code] = $value;
			}
			elseif (strpos($k, 'PROPS_INFO') !== false) {
				$code = str_replace('PROPS_INFO_', '', $k);
				$arSendFields['PROPERTY_VALUES'][$code] = $value;
			}
			elseif (strpos($k, 'STORES_INFO') !== false) {
				if ($value) {
					$id = str_replace('STORES_INFO_', '', $k);
					$arSendFields['STORE_VALUES'][] = ['ID' => $id, 'AMOUNT' => $value];
				}
			}
			elseif (strpos($k, 'PRICES_INFO') !== false) {
				if ($value) {
					$id = str_replace('PRICES_INFO_', '', $k);
					$arSendFields['PRICE_VALUES'][] = ['ID' => $id, 'VALUE' => $value, 'CURRENCY' => 'RUB'];
				}
			}
		}
		return $arSendFields;
	}

	public function stepExport_convertFieldsInfo($arList) {
		$arListFlat = array();
		foreach ($arList as $k => $arItem) {
			foreach ($arItem as $k2 => $arItem2) {
				$arListFlat[$k.'_'.$k2] = $arItem2;
			}
		}
		return $arListFlat;
	}

	public function stepExport_prepareFields($arFields, $arFieldsInfo, $arRemoteFields=array()) {
		$arFieldsInfoFlat = $this->stepExport_convertFieldsInfo($arFieldsInfo);
		if (!empty($arFields)) {
			foreach ($arFields as $code => $value) {
				// Temporary value
				$arNewValue = array();
				$arValue = !is_array($value) ? array($value) : $value;
				// File
				if ($arFieldsInfoFlat[$code]['TYPE'] == 'product_file') {
					// Add new values
					foreach ($arValue as $path) {
						$name = pathinfo($path, PATHINFO_BASENAME);
						$data = file_get_contents($path);
						$arNewValue[] = array(
							"filename" => $name,
							"filecontent" => base64_encode($data)
						);
					}
				}
				elseif ($arFieldsInfoFlat[$code]['PROPERTY_TYPE'] == 'F') {
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
				elseif ($arFieldsInfoFlat[$code]['type'] == 'product_property' && $arFieldsInfoFlat[$code]['propertyType'] == 'L') {
					if ($arFieldsInfoFlat[$code] && is_array($arFieldsInfoFlat[$code]['values'])) {
						foreach ($arFieldsInfoFlat[$code]['values'] as $arFIValue) {
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
				$arFields[$code] = BitrixRest::convEncForPortal($arNewValue);
			}
		}
		return $arFields;
	}

	public function stepExport_findRemoteSection($intProfileID, $strSectionName, $arRemoteFields=array()) {
		$intRemSectionID = false;
		#$arProfile = Profile::getProfiles($intProfileID);
		$arParams = [
			'ENTITY' => $this->arProfile['PARAMS']['IBLOCK_ID'],
			'FILTER' => [
				'NAME' => $strSectionName,
			],
		];
		$arRemoteSections = BitrixRest::executeMethod($this->strModuleId, 'acrit_iblock.section.get', $arParams, $intProfileID);
//		Log::getInstance($this->strModuleId)->add('$arRemoteSections ' . print_r($arRemoteSections, true), $intProfileID);
		// Get exist section
		if (!empty($arRemoteSections)) {
			$intRemSectionID = $arRemoteSections[0]['ID'];
		}
		// Add new section
		else {
			//$code = $strSectionName;
			$arParams = array(
				'ENTITY' => $this->arProfile['PARAMS']['IBLOCK_ID'],
                'ACTIVE' => 'Y',
				'IBLOCK_ID' => $this->arProfile['PARAMS']['IBLOCK_ID'],
				//'CODE' => $code,
				'NAME' => $strSectionName,
			);
			$intRemSectionID = BitrixRest::executeMethod($this->strModuleId, 'acrit_iblock.section.add', $arParams, $intProfileID);
//			Log::getInstance($this->strModuleId)->add('$intRemSectionID ' . print_r($intRemSectionID, true), $intProfileID);
		}
		return $intRemSectionID;
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

}

?>