<?
/**
 * Currency converter class
 */

namespace Acrit\Core\Export\CurrencyConverter;

use
	\Bitrix\Main\Localization\Loc,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Xml;

/**
 * Centrobank
 */
abstract class Cbrf extends Base {
	
	static $arCacheData;
	static $arCacheFactor;
	
	/**
	 *	Get name of converter
	 */
	public static function getName(){
		return Loc::getMessage('ACRIT_EXP_CURRENCYCONVERTER_CBRF_NAME');
	}
	
	/**
	 *	Get code of converter
	 */
	public static function getCode(){
		return 'CBRF';
	}
	
	/**
	 *	Get sort index
	 */
	public static function getSort(){
		return 20;
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
		$arCache = &static::$arCacheFactor;
		#
		$mResult= false;
		if(is_array($arCache) && isset($arCache[$strFrom][$strTo])){
			return $arCache[$strFrom][$strTo];
		}
		$arCurrencyData = static::getData();
		$strFrom = in_array($strFrom, array('RUR', 'RUB')) ? 'RUB' : $strFrom;
		$strTo = in_array($strTo, array('RUR', 'RUB')) ? 'RUB' : $strTo;
		if($strFrom=='RUB'){
			if(isset($arCurrencyData[$strTo])){
				$mResult = FloatVal($arCurrencyData[$strTo]['N'])/(FloatVal($arCurrencyData[$strTo]['V']));
			}
		}
		elseif ($strTo=='RUB'){
			if(isset($arCurrencyData[$strFrom])){
				$mResult = FloatVal($arCurrencyData[$strFrom]['V']/$arCurrencyData[$strFrom]['N']);
			}
		}
		else {
			if(isset($arCurrencyData[$strFrom]) && isset($arCurrencyData[$strTo])){
				$x1 = FloatVal($arCurrencyData[$strFrom]['V'])/FloatVal($arCurrencyData[$strFrom]['N']);
				$x2 = FloatVal($arCurrencyData[$strTo]['V'])/FloatVal($arCurrencyData[$strTo]['N']);
				$mResult = $x1/$x2;
			}
		}
		if(is_float($mResult)) {
			$arCache[$strFrom][$strTo] = $mResult;
		}
		return $mResult;
	}
	
	/**
	 *	Update values from remote server
	 */
	public static function getData(){
		$arResult = &static::$arCacheData;
		if(is_array($arResult)){
			return $arResult;
		}
		$strXmlUrl = 'http://www.cbr.ru/scripts/XML_daily.asp';
		$strContent = HttpRequest::get($strXmlUrl, array('TIMEOUT'=>5));
		$arXml = Xml::xmlToArray($strContent);
		if(is_array($arXml['ValCurs']['#']['Valute'])){
			#$arResult['DATE'] = $arXml['ValCurs']['@']['Date'];
			foreach($arXml['ValCurs']['#']['Valute'] as $arCurrency){
				$strCurrencyCode = $arCurrency['#']['CharCode'][0]['#'];
				$strCurrencyNominal = IntVal($arCurrency['#']['Nominal'][0]['#']);
				$strCurrencyValue = str_replace(',', '.', $arCurrency['#']['Value'][0]['#']);
				$arResult[$strCurrencyCode] = array(
					'V' => $strCurrencyValue,
					'N' => $strCurrencyNominal,
				);
			}
		}
		unset($strContent, $arXml);
		return $arResult;
	}
	
}
