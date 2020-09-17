<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class SettingsCase extends SettingsBase {
	
	public static function getCode(){
		return 'CASE';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 800;
	}
	
	public static function getGroup(){
		return array(
			'CODE' => 'GENERAL',
		);
	}
	
	public static function isForFields(){
		return true;
	}
	
	public static function isForValues(){
		return true;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		$arOptions = array(
			'skip' => static::getMessage('SKIP'),
			'lower' => static::getMessage('LOWER'),
			'upper' => static::getMessage('UPPER'),
			'ucfirst' => static::getMessage('UCFIFRST'),
			'ucwords' => static::getMessage('UCWORDS'),
		);
		$arOptions = array(
			'REFERENCE' => array_values($arOptions),
			'REFERENCE_ID' => array_keys($arOptions),
		);
		print SelectBoxFromArray(static::getCode(), $arOptions, $arParams[static::getCode()], '', 
			'id="'.static::getInputID().'"');
		print Helper::ShowHint(static::getHint());
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		if(strlen($arParams[static::getCode()])){
			#
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				$mParamValue = $arParams[static::getCode()];
				if($mParamValue == 'lower'){
					$strValue = toLower($strValue);
				}
				elseif($mParamValue == 'upper'){
					$strValue = toUpper($strValue);
				}
				elseif($mParamValue == 'ucfirst'){
					$strValue = toUpper(substr($strValue, 0, 1)).substr($strValue, 1);
				}
				elseif($mParamValue == 'ucwords'){
					if(function_exists('mb_convert_case')){
						$strValue = mb_convert_case($strValue, MB_CASE_TITLE, Helper::isUtf() ? 'UTF-8' : 'CP1251');
					}
					else{
						$strValue = ucwords($strValue);
					}
				}
			});
			#
		}
	}
	
}
