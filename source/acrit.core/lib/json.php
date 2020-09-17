<?
/**
 * Class to work with JSON
 */

namespace Acrit\Core;

use
	\Acrit\Core\Helper;

class Json extends \Bitrix\Main\Web\Json {
	
	static protected $intTabSize = 4; // space count for one tab in formatted output
	
	/**
	 *	Helper for add value
	 */
	public static function addValue($mValue){
		$arResult = array();
		if(is_array($mValue)){
			foreach($mValue as $strValueItem){
				$arResult[] = $strValueItem;
			}
		}
		else {
			$arResult = $mValue;
		}
		return $arResult;
	}
	
	/**
	 *	Set http header for JSON file
	 */
	public static function setHttpHeader(){
		header('Content-Type: application/json; charset='.(Helper::isUtf()?'utf-8':'windows-1251'));
	}
	
	/**
	 *	Set no display errors
	 *	Against 'Warning:  A non-numeric value encountered in /home/bitrix/www/bitrix/modules/perfmon/classes/general/keeper.php on line 321'
	 */
	public static function disableErrors(){
		ini_set('display_errors', 0);
		error_reporting(~E_ALL);
	}
	
	/**
	 *	Print JSON to page
	 */
	public static function printEncoded($arJson, $intOptions=0){
		if($intOptions === 0 && checkVersion(PHP_VERSION, '7.2.0')){
			$intOptions = JSON_INVALID_UTF8_IGNORE;
		}
		print static::encode($arJson, $intOptions);
	}
	
	/**
	 *	
	 */
	public static function prepare($arJson=[]){
		static::setHttpHeader();
		static::disableErrors();
		Helper::obRestart();
		return $arJson;
	}
	
	/**
	 *	
	 */
	public static function output($arJsonResult, $arOptions=0){
		Helper::obRestart();
		static::printEncoded($arJsonResult, $arOptions);
		static::disableErrors();
	}
	
	/**
	 *	
	 */
	public static function getTabSize(){
		return static::$intTabSize;
	}
	
	/**
	 *	
	 */
	public static function replaceSpaces(&$strJson){
		$strJson = preg_replace_callback("#^[\t]?([ ]*)(.*?)$#m", function($arMatch){
			$intTabCount = floor(strlen($arMatch[1]) / static::$intTabSize);
			return str_repeat("\t",  $intTabCount).$arMatch[2];
		}, $strJson);
	}
	
	/**
	 *	
	 */
	public static function addIndent(&$strJson, $intAmount=1){
		$strOffset = str_repeat("\t", $intAmount);
		$strJson = preg_replace('#^(.*?)$#m', $strOffset.'$1', $strJson);
	}

}
?>