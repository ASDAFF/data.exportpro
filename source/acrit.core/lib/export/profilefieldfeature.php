<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\ProfileIBlockTable as ProfileIBlock,
	\Acrit\Core\Export\ProfileValueTable as ProfileValue,
	\Acrit\Core\Export\Field\Valuebase;

Loc::loadMessages(__FILE__);

/**
 * Class ProfileFieldFeature
 * @package Acrit\Core\Export
 */

class ProfileFieldFeature {
	
	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!
	
	const PRODUCT = 'ELEMENT';
	const OFFER = 'OFFER';
	
	static $arCacheFieldFeatures = array();
	
	/**
	 *	Get usage field types in profile iblock
	 *	$intIBlockID - just main IBlock (NOT offers iblock!)
	 */
	public static function getIBlockFeatures($intProfileID, $intIBlockID){
		if(!$intProfileID || !$intIBlockID){
			return array();
		}
		$arResult = &static::$arCacheFieldFeatures[static::MODULE_ID][$intProfileID.'_'.$intIBlockID];
		if(isset($arResult)){
			return $arResult;
		}
		$arResult = array();
		#
		$arCatalog = Helper::getCatalogArray($intIBlockID);
		$arFields = static::getFieldsForIBlock($intProfileID, $intIBlockID, $arCatalog['OFFERS_IBLOCK_ID']);
		#
		$arAvailableFields = static::getIBlockAvailableFields($intProfileID, $intIBlockID, $arCatalog['OFFERS_IBLOCK_ID']);
		#
		$arGroups = array();
		foreach($arAvailableFields as $strType => $arAvailableTypeFields){
			if(is_array($arFields[$strType])){
				foreach($arFields[$strType] as $strField){
					if(is_array($arAvailableTypeFields[$strField])){
						$arGroups[$strType][$arAvailableTypeFields[$strField]['GROUP']][] = $strField;
					}
				}
			}
		}
		#
		$strPrefix = 'PROPERTY_';
		foreach($arGroups as $type => $arType){
			if(is_array($arType['PROPERTY'])){
				$arGroups[$type]['PROPERTY_ID'] = array();
				$arPropsCode = array();
				foreach($arType['PROPERTY'] as $strProp){
					$strProp = reset(explode(ValueBase::SUBFIELD_OPERATOR, $strProp));
					$arPropsCode[] = substr($strProp, strlen($strPrefix));
				}
				$arPropsCode = array_unique($arPropsCode);
				if(!empty($arPropsCode)){
					$intPropIBlockID = $intIBlockID;
					if($type == static::OFFER && is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID'] > 0){
						$intPropIBlockID = $arCatalog['OFFERS_IBLOCK_ID'];
					}
					$arProps = Helper::getIBlockPropsIdByCode($intPropIBlockID, $arPropsCode);
					$arGroups[$type]['PROPERTY_ID'] = array_keys($arProps);
				}
			}
		}
		#
		$strPrefix = 'SECTION__';
		foreach($arGroups as $type => $arType){
			if(is_array($arType['SECTION'])){
				foreach($arType['SECTION'] as $key => $strField){
					$arGroups[$type]['SECTION'][$key] = substr($strField, strlen($strPrefix));
				}
			}
		}
		#
		$strPrefix = 'IBLOCK__';
		foreach($arGroups as $type => $arType){
			if(is_array($arType['IBLOCK'])){
				foreach($arType['IBLOCK'] as $key => $strField){
					$arGroups[$type]['IBLOCK'][$key] = substr($strField, strlen($strPrefix));
				}
			}
		}
		#
		foreach($arAvailableFields as $strType => $arAvailableTypeFields){
			$strBarcode = 'CATALOG_BARCODE';
			if(is_array($arGroups[$strType]['CATALOG'])){
				$key = array_search($strBarcode, $arGroups[$strType]['CATALOG']);
				if($key !== false) {
					$arGroups[$strType]['BARCODE'] = array($strBarcode);
					unset($arGroups[$strType]['CATALOG'][$key]);
				}
			}
		}
		#
		$arResult = array(
			'FIELDS' => $arFields,
			'GROUPS' => $arGroups,
		);
		return $arResult;
	}
	
	/**
	 *	Get all fields for IBlock
	 */
	protected static function getFieldsForIBlock($intProfileID, $intIBlockID, $intOffersIBlock){
		if($intOffersIBlock){
			$arResult = array_merge_recursive(
				static::getFieldsForSingleIBlock($intProfileID, $intIBlockID, false), 
				static::getFieldsForSingleIBlock($intProfileID, $intOffersIBlock, true)
			);
		}
		else{
			$arResult = static::getFieldsForSingleIBlock($intProfileID, $intIBlockID, false);
		}
		foreach($arResult as $key => $arItems){
			$arResult[$key] = array_unique($arItems);
		}
		return $arResult;
	}
	
	/**
	 *	Get fields for one IBlock
	 */
	protected static function getFieldsForSingleIBlock($intProfileID, $intIBlockID, $bOffersIBlock){
		$arResult = array(
			static::PRODUCT => array(),
			static::OFFER => array(),
		);
		$arQuery = [
			'filter' => array('PROFILE_ID' => $intProfileID, 'IBLOCK_ID' => $intIBlockID),
			'select' => array('TYPE', 'VALUE', 'CONST'),
		];
		$resFields = Helper::call(static::MODULE_ID, 'ProfileValue', 'getList', [$arQuery]);
		while($arField = $resFields->fetch()){
			switch($arField['TYPE']){
				case 'FIELD':
					if(strlen($arField['VALUE'])){
						static::addValue($arResult, $arField['VALUE'], $bOffersIBlock);
					}
					break;
				case 'CONST':
					if(strlen($arField['CONST'])){
						if(preg_match_all(Valuebase::CONST_VALUES_SEARCH_PATTERN, $arField['CONST'], $arMatches)){
							foreach($arMatches[0] as $strMatch){
								static::addValue($arResult, trim($strMatch, '{=}'), $bOffersIBlock);
							}
						}
					}
					break;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Add value to collection
	 */
	protected static function addValue(&$arResult, $strValue, $bOffersIBlock){
		$arValue = explode('.', $strValue);
		$strKey = $bOffersIBlock ? static::OFFER : static::PRODUCT;
		if(count($arValue) == 1){
			$arResult[$strKey][] = end($arValue);
		}
		else{
			if($arValue[0] == 'OFFER'){
				$strKey = static::OFFER;
			}
			elseif($arValue[0] == 'PARENT'){
				$strKey = static::PRODUCT;
			}
			$arResult[$strKey][] = end($arValue);
		}
	}
	
	/**
	 *	Get available fields
	 */
	protected static function getIBlockAvailableFields($intProfileID, $intIBlockID, $intOffersIBlock){
		$arResult = array();
		$arResult[static::PRODUCT] = ProfileIBlock::getAvailableElementFieldsPlain($intIBlockID);
		if($intOffersIBlock){
			$arResult[static::OFFER] = ProfileIBlock::getAvailableElementFieldsPlain($intOffersIBlock);
		}
		return $arResult;
	}
	
}

?>