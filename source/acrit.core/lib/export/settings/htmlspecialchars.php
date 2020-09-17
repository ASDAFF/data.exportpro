<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class SettingsHtmlspecialchars extends SettingsBase {
	
	public static function getCode(){
		return 'HTMLSPECIALCHARS';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 600;
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
	
	public static function isShown($obField, $arParams){
		return true;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		$arOptions = array(
			'escape' => static::getMessage('ESCAPE'),
			'cut' => static::getMessage('CUT'),
			'cut_with_quotes' => static::getMessage('CUT_WITH_QUOTES'),
		);
		if(isset($arParams['field_type']) && !isset($arParams['value_type']) && $obField->isSupportCData()){
			$arOptions['cdata'] = static::getMessage('CDATA');
		}
		else {
			$arOptions['skip'] = static::getMessage('SKIP');
		}
		$arOptions = array(
			'REFERENCE' => array_values($arOptions),
			'REFERENCE_ID' => array_keys($arOptions),
		);
		print SelectBoxFromArray(static::getCode(), $arOptions, $arParams[static::getCode()], '', 
			'id="'.static::getInputID().'"');
		print Helper::ShowHint(static::getHint());
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
			switch($arParams[static::getCode()]){
				case 'skip':
					// Nothing
					break;
				case 'cut':
					$arSpecialChars = array('<', '>', '&');
					$strValue = str_replace($arSpecialChars, '', $strValue);
					break;
				case 'cut_with_quotes':
					$arSpecialChars = array('<', '>', '&', '"', '\'');
					$strValue = str_replace($arSpecialChars, '', $strValue);
					break;
				case 'cdata':
					$strValue = '<![CDATA['.$strValue.']]>';
					break;
				default: // escape
					$strValue = htmlspecialchars($strValue, ENT_HTML5 | ENT_QUOTES , Helper::isUtf() ? 'UTF-8' : 'CP1251', false);
					break;
			}
		});
	}
	
}
