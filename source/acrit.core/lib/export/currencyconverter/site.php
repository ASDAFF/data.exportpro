<?
/**
 * Currency converter class
 */

namespace Acrit\Core\Export\CurrencyConverter;

use
	\Bitrix\Main\Localization\Loc;

/**
 * Rates from currency
 */
abstract class Site extends Base {
	
	/**
	 *	Get name of converter
	 */
	public static function getName(){
		return Loc::getMessage('ACRIT_EXP_CURRENCYCONVERTER_SITE_NAME');
	}
	
	/**
	 *	Get code of converter
	 */
	public static function getCode(){
		return 'SITE';
	}
	
	/**
	 *	Get sort index
	 */
	public static function getSort(){
		return 10;
	}
	
	/**
	 *	Convert currency
	 */
	public static function convert($fPrice, $strFrom, $strTo){
		$fFactor = static::getFactor($strFrom, $strTo);
		if(is_float($fFactor)) {
			return $fPrice * $fFactor;
		}
		return false;
	}
	
	/**
	 *	Get currency convert factor
	 */
	public static function getFactor($strFrom, $strTo){
		$mResult = false;
		if(\Bitrix\Main\Loader::includeModule('currency')){
			$fFactor = \CCurrencyRates::GetConvertFactor($strFrom, $strTo);
			if(is_float($fFactor)){
				$mResult = $fFactor;
			}
		}
		return $mResult;
	}
	
}
