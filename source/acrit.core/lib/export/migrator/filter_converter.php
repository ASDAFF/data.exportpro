<?
/**
 * Class for migrate filter from old profiles
 */

namespace Acrit\Core\Export\Migrator;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Json;

/**
 *	
 */
class FilterConverter {
	
	protected $intIBlockID;
	protected $arAvailableFields;
	
	public function __construct($strModuleId, $intIBlockID){
		$this->setIBlockID($intIBlockID);
		#$this->arAvailableFields = ProfileIBlock::getAvailableElementFieldsPlain($intIBlockID);
		$this->arAvailableFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$intIBlockID]);
		if(!\Bitrix\Main\Loader::includeModule('iblock')){
			die('No iblock module!');
		}
	}
	
	/**
	 *	Static function for convert filter
	 */
	public static function convertFilter($strModuleId, $arOldFilter, $intIBlockID){
		if(is_array($arOldFilter) && !empty($arOldFilter)){
			$arNewFilter = array();
			$obFilterConverter = new static($strModuleId, $intIBlockID);
			$arNewFilter[] = $obFilterConverter->convertFilterGroupOrItem($arOldFilter);
			unset($obFilterConverter);
			return Json::encode($arNewFilter);
		}
		return false;
	}
	
	/**
	 *	Set IBlock_ID
	 */
	public function setIBlockID($intIBlockID){
		$this->intIBlockID = $intIBlockID;
	}

	/**
	 *	Convert one entity (group || item), recursively
	 */
	public function convertFilterGroupOrItem($arGroupOrItem){ // recursive // CLASS_ID, DATA, CHILDREN
		# Group?
		if(is_array($arGroupOrItem)){
			if($arGroupOrItem['CLASS_ID'] == 'CondGroup'){
				$arNewItem = $this->convertFilterGroup($arGroupOrItem);
				if(is_array($arGroupOrItem['CHILDREN'])) {
					foreach($arGroupOrItem['CHILDREN'] as $key => $arChildren){
						$arNewItem['items'][$key] = call_user_func(__METHOD__, $arChildren);
						if($arNewItem['items'][$key] === false){
							unset($arNewItem['items'][$key]);
						}
					}
				}
			}
			# Item?
			else {
				$arNewItem = $this->convertFilterItem($arGroupOrItem);
			}
			return $arNewItem;
		}
	}
	
	/**
	 *	Convert group, no recursion here
	 */
	protected function convertFilterGroup($arGroup){
		return array(
			'type' => 'group',
			'aggregatorType' => $arGroup['DATA']['All'] == 'AND' ? 'ALL' : 'ANY',
			//'aggregatorValue' => $arGroup['DATA']['True'] == 'True' ? 'Y' : 'N',
			'items' => array(),
		);
	}
	
	/**
	 *	Convert one item
	 */
	protected function convertFilterItem($arItem){
		$bSuccess = false;
		$arClassData = explode(':', $arItem['CLASS_ID']);
		$strClassID = array_shift($arClassData);
		/*
		$arResult = array(
			'type' => 'item',
			'iblockType' => 'main',
			'field' => array(
				'name' => '',
				'value' => '',
			),
			'logic' => array(
				'name' => '',
				'value' => '',
				'hide' => 'N',
			),
			'value' => array(
				'name' => '',
				'value' => '',
			),
		);
		*/
		$arResult = array(
			'type' => 'item',
			'iblockType' => 'main',
		);
		#
		$strField = $this->getMatchField($strClassID, $arClassData);
		if(strlen($strField) && is_array($this->arAvailableFields[$strField])){
			$arField = $this->arAvailableFields[$strField];
			$arField['CODE'] = $strField;
			#
			$arResult['field'] = array(
				'name' => $arField['NAME'],
				'value' => $strField,
			);
			$arLogic = $this->getMatchLogic($arField, $arItem['DATA']);
			if(is_array($arLogic)) {
				$arResult['logic'] = array(
					'name' => $arLogic['NAME'],
					'value' => $arLogic['CODE'],
					'hide' => $arLogic['HIDE_VALUE'] ? 'Y' : 'N',
				);
				$arResult['value'] = array(
					'name' => '',
					'value' => '',
				);
				if(!$arLogic['HIDE_VALUE']) {
					$arValue = $this->getMatchValue($arField, $arLogic, $arItem['DATA']['value']);
					if(!Helper::isEmpty($arValue)){
						$arResult['value'] = array(
							'name' => $arValue['NAME'],
							'value' => $arValue['VALUE'],
						);
					}
				}
				$bSuccess = true;
			}
			#
		}
		if(!$bSuccess){
			$arResult = false;
		}
		return $arResult;
	}
	
	/**
	 *	Get match for old field in new module
	 */
	protected function getMatchField($strClassID, $arClassData){
		$strResult = '';
		$arMatchBase = array(
			'CondIBElement' => 'ID',
			#'CondIBIBlock' => 'IBLOCK_ID',
			#'CondIBSection' => '', ???
			'CondIBCode' => 'CODE',
			'CondIBXmlID' => 'XML_ID',
			'CondIBName' => 'NAME',
			'CondIBActive' => 'ACTIVE',
			'CondIBDateActiveFrom' => 'ACTIVE_FROM',
			'CondIBDateActiveTo' => 'ACTIVE_TO',
			'CondIBSort' => 'SORT',
			'CondIBPreviewText' => 'PREVIEW_TEXT',
			'CondIBDetailText' => 'DETAIL_TEXT',
			'CondIBDateCreate' => 'DATE_CREATE',
			'CondIBCreatedBy' => 'CREATED_BY',
			'CondIBTimestampX' => 'TIMESTAMP_X',
			'CondIBModifiedBy' => 'MODIFIED_BY',
			'CondIBTags' => 'TAGS',
			#
			'CondCatQuantity' => 'CATALOG_QUANTITY',
			'CondCatWeight' => 'CATALOG_WEIGHT',
			'CondCatVatID' => 'CATALOG_VAT_ID',
			'CondCatVatIncluded' => 'CATALOG_VAT_INCLUDED',
			#
			'CondGooglemerchantAvailability' => 'CATALOG_AVAILABLE',
			'CondExportAvailability' => 'CATALOG_AVAILABLE',
			'CondExportproAvailability' => 'CATALOG_AVAILABLE',
			'CondExportproplusAvailability' => 'CATALOG_AVAILABLE',
			#
			#'CondIBDetailText' => 'DETAIL_TEXT',
		);
		if(array_key_exists($strClassID, $arMatchBase)){
			$strResult = $arMatchBase[$strClassID];
		}
		#elseif($strClassID == 'CondExportproplusStore'){
		elseif(in_array($strClassID, ['CondGooglemerchantStore', 'CondExportStore', 'CondExportproStore', 'CondExportproplusStore'])){
			$strResult = 'CATALOG_STORE_AMOUNT_'.$arClassData[0];
		}
		elseif($strClassID == 'CondIBProp'){
			$intIBlockID = IntVal($arClassData[0]);
			$intPropertyID = IntVal($arClassData[1]);
			if($intIBlockID>0 && $intPropertyID>0){
				$resProperty = \CIBlockProperty::getList(array(), array('IBLOCK_ID'=>$intIBlockID, 'ID'=>$intPropertyID));
				if($arProperty = $resProperty->getNext(false, false)){
					$strResult = 'PROPERTY_'.(strlen($arProperty['CODE'])?$arProperty['CODE']:$arProperty['ID']);
				}
			}
		}
		elseif(preg_match('#^CondCatPrice_(\d+)$#', $strClassID, $arMatch)){
			//$strResult = 'PROPERTY_ACRIT_EXP_PRICE_'.$arMatch[1].'_VALUE';
			$strResult = 'CATALOG_PRICE_'.$arMatch[1];
		}
		elseif(preg_match('#^CondCatPrice_(\d+)_WD$#', $strClassID, $arMatch)){
			//$strResult = 'PROPERTY_ACRIT_EXP_PRICE_'.$arMatch[1].'_VALUE';
			$strResult = 'CATALOG_PRICE_'.$arMatch[1];
		}
		elseif(preg_match('#^CondCatPrice_(\d+)_D$#', $strClassID, $arMatch)){
			$strResult = 'PROPERTY_ACRIT_EXP_PRICE_'.$arMatch[1].'_DISCOUNT';
		}
		else{
			#
		}
		return $strResult;
	}
	
	/**
	 *	Get match for old logic in new module
	 */
	protected function getMatchLogic($arField, &$arData){
		$strLogic = &$arData['logic'];
		$mValue = &$arData['value'];
		if($arField['TYPE'].(strlen($arField['USER_TYPE'])?':'.$arField['USER_TYPE']:'') == 'S:_Checkbox'){
			$strLogic = $strLogic == 'Equal' ? 'Checked' : 'NotChecked';
		}
		if($arField['CODE'] == 'ID' && $strLogic == 'Equal' && is_array($mValue)){
			$strLogic = 'InList';
			$mValue = implode(',', $mValue);
		}
		$arMatchBase = array(
			'Equal' => 'EQUAL',
			'Not' => 'NOT_EQUAL',
			'Great' => 'MORE',
			'Less' => 'LESS',
			'EqGr' => 'MORE_OR_EQUAL',
			'EqLs' => 'LESS_OR_EQUAL',
			'Contain' => 'SUBSTRING',
			'NotCont' => 'NOT_SUBSTRING',
			#
			'Checked' => 'CHECKED',
			'NotChecked' => 'NOT_CHECKED',
			#
			'InList' => 'IN_LIST',
		);
		if(strlen($strLogic) && array_key_exists($strLogic, $arMatchBase)) {
			$arLogics = Filter::getLogicAll($arField['TYPE'], $arField['USER_TYPE']);
			$strLogicResult = $arMatchBase[$strLogic];
			$arLogic = $arLogics[$strLogicResult];
			if(is_array($arLogic)){
				$arLogic = array_merge(array('CODE'=>$strLogicResult), $arLogic);
				return $arLogic;
			}
		}
		return false;
	}
	
	/**
	 *	Get match for old value in new module
	 */
	protected function getMatchValue($arField, $arLogic, $mValue){
		$arResult = array(
			'NAME' => is_array($mValue) ? implode(', ', $mValue) : $mValue,
			'VALUE' => $mValue,
		);
		return $arResult;
	}
	
}

?>