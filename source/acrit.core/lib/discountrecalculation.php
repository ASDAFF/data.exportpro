<?
/**
 * Acrit core
 * @package acrit.core
 * @copyright 2018 Acrit
 */
namespace Acrit\Core;

use \Bitrix\Main\Localization\Loc;

/**
 * Discount Recalculation
 */
class DiscountRecalculation {
	
	const MAX_STEP_TIME = 5;
	protected static $intStartTime;
	protected static $bEnabled;
	
	/**
	 *	Is discount recalculation enabled?
	 */
	public static function isEnabled($bForce=false){
		if(static::$bEnabled === null || $bForce) {
			static::$bEnabled = \Bitrix\Main\Config\Option::get(ACRIT_CORE, 
				'discount_recalculation_enabled') == 'Y';
		}
		return static::$bEnabled;
	}
	
	/**
	 *	Get current prices ID
	 */
	public static function getCurrentPricesID(){
		return array_filter(explode(',', Helper::getOption(ACRIT_CORE, 'discount_recalculation_prices')));
	}

	/**
	 *	Auto create properties for all iblocks
	 */
	public static function checkProperties($intIBlockID=null){
		if(\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('catalog')){
			$intSort = 10000;
			$intSortIncrement = 10;
			$arProperties = array();
			#$intSort += 2 * $intSortIncrement;
			$arPriceList = Helper::getPriceList(array('ID'=>'ASC'));
			$arCurrentPrices = static::getCurrentPricesID();
			foreach($arPriceList as $arPrice){
				if(!in_array($arPrice['ID'], $arCurrentPrices)) {
					continue;
				}
				if(Helper::getOption(ACRIT_CORE, 'discount_recalculation_calc_value') == 'Y') {
					$intSort += $intSortIncrement;
					$arProperties[] = array(
						'NAME' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_PRICE_VALUE_NAME', 
							array('#PRICE#'=>$arPrice['NAME_LANG'])),
						'HINT' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_HINT'),
						'CODE' => 'ACRIT_EXP_PRICE_'.$arPrice['ID'].'_VALUE',
						'ACTIVE' => 'Y',
						'SORT' => $intSort,
						'PROPERTY_TYPE' => 'N',
						'COL_COUNT' => '32',
					);
				}
				if(Helper::getOption(ACRIT_CORE, 'discount_recalculation_calc_discount') == 'Y') {
					$intSort += $intSortIncrement;
					$arProperties[] = array(
						'NAME' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_PRICE_DISCOUNT_NAME', 
							array('#PRICE#'=>$arPrice['NAME_LANG'])),
						'HINT' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_HINT'),
						'CODE' => 'ACRIT_EXP_PRICE_'.$arPrice['ID'].'_DISCOUNT',
						'ACTIVE' => 'Y',
						'SORT' => $intSort,
						'PROPERTY_TYPE' => 'N',
						'COL_COUNT' => '32',
					);
				}
				if(Helper::getOption(ACRIT_CORE, 'discount_recalculation_calc_percent') == 'Y') {
					$intSort += $intSortIncrement;
					$arProperties[] = array(
						'NAME' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_PRICE_PERCENT_NAME', 
							array('#PRICE#'=>$arPrice['NAME_LANG'])),
						'HINT' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_HINT'),
						'CODE' => 'ACRIT_EXP_PRICE_'.$arPrice['ID'].'_PERCENT',
						'ACTIVE' => 'Y',
						'SORT' => $intSort,
						'PROPERTY_TYPE' => 'N',
						'COL_COUNT' => '32',
					);
				}
				if(Helper::getOption(ACRIT_CORE, 'discount_recalculation_calc_dates') == 'Y') {
					$intSort += $intSortIncrement;
					$arProperties[] = array(
						'NAME' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_DISCOUNT_ACTIVE_FROM_NAME', 
							array('#PRICE#'=>$arPrice['NAME_LANG'])),
						'HINT' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_HINT'),
						'CODE' => 'ACRIT_EXP_PRICE_'.$arPrice['ID'].'_ACTIVE_FROM',
						'ACTIVE' => 'Y',
						'SORT' => $intSort,
						'PROPERTY_TYPE' => 'S',
						'USER_TYPE' => 'DateTime',
					);
					$intSort += $intSortIncrement;
					$arProperties[] = array(
						'NAME' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_DISCOUNT_ACTIVE_TO_NAME', 
							array('#PRICE#'=>$arPrice['NAME_LANG'])),
						'HINT' => Loc::getMessage('ACRIT_EXP_DISCOUNT_RECALCULATION_PROP_HINT'),
						'CODE' => 'ACRIT_EXP_PRICE_'.$arPrice['ID'].'_ACTIVE_TO',
						'ACTIVE' => 'Y',
						'SORT' => $intSort,
						'PROPERTY_TYPE' => 'S',
						'USER_TYPE' => 'DateTime',
					);
				}
			}
			# Ищем настроенные в модуле инфоблоки
			$bIBlockID = is_numeric($intIBlockID) && $intIBlockID > 0;
			if($bIBlockID){
				$arProfileIBlockID = array($intIBlockID);
				$arCatalog = Helper::getCatalogArray($intIBlockID);
				if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID'] > 0) {
					$arProfileIBlockID[] = $arCatalog['OFFERS_IBLOCK_ID'];
				}
			}
			else{
				$arProfileIBlockID = array_filter(explode(',', Helper::getOption(ACRIT_CORE, 
					'discount_recalculation_iblocks')));
			}
			# Ищем все торговые каталоги и для всех будем обновлять свойства
			$obIBlockProperty = new \CIBlockProperty;
			$resCatalogs = \CCatalog::GetList([], [], false, false, ['IBLOCK_ID']);
			while($arCatalog = $resCatalogs->getNext(false, false)){
				if($bIBlockID && !in_array($arCatalog['IBLOCK_ID'], $arProfileIBlockID)){
					continue;
				}
				$arExistProps = array();
				$resProp = \CIBlockProperty::GetList(array(), array(
					'IBLOCK_ID' => $arCatalog['IBLOCK_ID'],
					'CODE' => 'ACRIT_EXP_PRICE_%',
				));
				while($arProp = $resProp->getNext(false,false)){
					$arExistProps[$arProp['CODE']] = $arProp;
				}
				if(static::isEnabled(true) && in_array($arCatalog['IBLOCK_ID'], $arProfileIBlockID)){
					foreach($arProperties as $key => $arProperty){
						$arProperty['IBLOCK_ID'] = $arCatalog['IBLOCK_ID'];
						if(is_array($arExistProps[$arProperty['CODE']])) {
							$bPropertySuccess = $obIBlockProperty->Update($arExistProps[$arProperty['CODE']]['ID'], $arProperty);
						}
						else {
							$bPropertySuccess = $obIBlockProperty->Add($arProperty);
						}
						if($bPropertySuccess){
							unset($arExistProps[$arProperty['CODE']]);
						}
					}
				}
				foreach($arExistProps as $arProp){
					\CIBlockProperty::Delete($arProp['ID']);
				}
			}
			unset($obIBlockProperty, $arProperty, $arExistProps);
		}
	}
	
	/**
	 *	Handle discount add, update, or delete
	 */
	public static function handleDiscountAction(){
		//
	}
	
	/**
	 *	Handle on / off this functional
	 */
	public static function handleOnOff(){
		//
	}
	
	/**
	 *	Handle save in options.php
	 */
	public static function handleSaveOptions(){
		if(static::isEnabled()){
			static::checkProperties();
		}
	}
	
	/**
	 *	Start time counter
	 */
	protected static function startTime(){
		static::$intStartTime = time();
	}
	
	/**
	 *	Check time is not over
	 */
	protected static function haveTime(){
		return time() - static::$intStartTime <= static::MAX_STEP_TIME;
	}
	
	/**
	 *	Process single element
	 */
	public static function processElement($intElementID, $arPrices=null, $strSiteID=null){
		if(!static::isEnabled()){
			return;
		}
		if($arPrices === null){
			$arPrices = Helper::getPriceList(array('ID'=>'ASC'));
		}
		#
		$arSelect = array(
			'ID',
			'IBLOCK_ID',
		);
		if($strSiteID === null){
			$arSelect[] = 'LID';
		}
		foreach($arPrices as $arPrice){
			$arSelect[] = 'CATALOG_GROUP_'.$arPrice['ID'];
		}
		$resElement = \CIBlockElement::getList(array(), array('ID' => $intElementID), false, false, $arSelect);
		if($arElement = $resElement->getNext(false, false)){
			if($strSiteID === null){
				$strSiteID = $arElement['LID'];
			}
			$arPropValues = array();
			$arCurrentPrices = static::getCurrentPricesID();
			foreach($arPrices as $arPrice){
				$intPriceID = $arPrice['ID'];
				if(!in_array($intPriceID, $arCurrentPrices)) {
					continue;
				}
				$arPrice = array(
					'ID' => $arElement['CATALOG_PRICE_ID_'.$intPriceID],
					'PRICE' => $arElement['CATALOG_PRICE_'.$intPriceID],
					'CURRENCY' => $arElement['CATALOG_CURRENCY_'.$intPriceID],
					'CATALOG_GROUP_ID' => $intPriceID,
				);
				$mPriceDiscount = null;
				$mDiscountValue = null;
				$mDiscountPercent = null;
				$mDiscountActiveFrom = null;
				$mDiscountActiveTo = null;
				if(strlen($intPriceID) && $arPrice['PRICE'] > 0 && strlen($arPrice['CURRENCY'])){
					$arOptimalPrice = \CCatalogProduct::GetOptimalPrice($intElementID, 1, array(), 'N', array($arPrice), 
						$strSiteID);
					if(is_array($arOptimalPrice['RESULT_PRICE'])){
						$mPriceDiscount = $arOptimalPrice['RESULT_PRICE']['UNROUND_DISCOUNT_PRICE'];
						$mDiscountValue = $arPrice['PRICE'] - $arOptimalPrice['RESULT_PRICE']['UNROUND_DISCOUNT_PRICE'];
						$mDiscountPercent = round($mDiscountValue * 100 / $arPrice['PRICE']);
						if(is_array($arOptimalPrice['DISCOUNT'])){
							if(strlen($arOptimalPrice['DISCOUNT']['ACTIVE_FROM'])){
								$mDiscountActiveFrom = $arOptimalPrice['DISCOUNT']['ACTIVE_FROM'];
							}
							if(strlen($arOptimalPrice['DISCOUNT']['ACTIVE_TO'])){
								$mDiscountActiveTo = $arOptimalPrice['DISCOUNT']['ACTIVE_TO'];
							}
						}
					}
				}
				if(Helper::getOption(ACRIT_CORE, 'discount_recalculation_calc_value') == 'Y') {
					$arPropValues['ACRIT_EXP_PRICE_'.$intPriceID.'_VALUE'] = $mPriceDiscount;
				}
				if(Helper::getOption(ACRIT_CORE, 'discount_recalculation_calc_discount') == 'Y') {
					$arPropValues['ACRIT_EXP_PRICE_'.$intPriceID.'_DISCOUNT'] = $mDiscountValue;
				}
				if(Helper::getOption(ACRIT_CORE, 'discount_recalculation_calc_percent') == 'Y') {
					$arPropValues['ACRIT_EXP_PRICE_'.$intPriceID.'_PERCENT'] = $mDiscountPercent;
				}
				if(Helper::getOption(ACRIT_CORE, 'discount_recalculation_calc_dates') == 'Y') {
					$arPropValues['ACRIT_EXP_PRICE_'.$intPriceID.'_ACTIVE_FROM'] = $mDiscountActiveFrom;
					$arPropValues['ACRIT_EXP_PRICE_'.$intPriceID.'_ACTIVE_TO'] = $mDiscountActiveTo;
				}
			}
			\CIBlockElement::SetPropertyValuesEx($intElementID, $arElement['IBLOCK_ID'], $arPropValues);
		}
	}
	
}
