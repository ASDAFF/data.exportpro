<?
/**
 * Base class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper;
	
Loc::loadMessages(__FILE__);

/**
 * Base interface for settings
 */
abstract class SettingsBase {
	
	static $strPrefix = '';
	static $arCachedSettings = array();
	
	public static function getMessage($strMessage, $arReplace=array()){
		$strCode = get_called_class()==__CLASS__ ? 'BASE' : static::getCode();
		$strResult = Loc::getMessage('ACRIT_EXP_SETTINGS_'.$strCode.'_'.$strMessage, $arReplace);
		return $strResult;
	}
	
	abstract public static function getName();
	
	abstract public static function getCode();
	
	abstract public static function getHint();
	
	abstract public static function getSort();
	
	abstract public static function getGroup();
	
	abstract public static function isForFields();
	
	abstract public static function isForValues();
	
	/**
	 *	Get predefined groups
	 */
	public static function getGroups(){
		return array(
			'GENERAL' => static::getMessage('GROUP_GENERAL'),
			'ADDITIONAL' => static::getMessage('GROUP_ADDITIONAL'),
		);
	}
	
	/**
	 *	Set type for prefix
	 */
	public static function setPrefix($strPrefix){
		static::$strPrefix = strlen($strPrefix) ? toLower($strPrefix).'_' : '';
	}
	
	/**
	 *	Get ID for input (id="...")
	 */
	public static function getInputID(){
		return 'acrit_exp_settings_'.(static::$strPrefix).ToLower(static::getCode());
	}
	
	/**
	 *	Get settings HTML
	 */
	abstract public static function showSettings($strFieldCode, $obField, $arParams);
	
	/**
	 *	Process value with saved settings
	 */
	abstract public static function process(&$mValue, $arParams, $obField=null);
	
	/**
	 *	
	 */
	protected static function processMultipleValue(&$mValue, &$arParams, &$obField, $mCallback){
		if(is_array($mValue)) {
			foreach($mValue as &$strValue){
				call_user_func_array($mCallback, [&$strValue, $arParams, $obField]);
			}
		} else {
			call_user_func_array($mCallback, [&$mValue, $arParams, $obField]);
		}
	}
	
	/**
	 *	Show (or not) current settings
	 *	$arParams содержит iblock_id, field_code, field_type, field_name, value_type, и сам набор параметров.
	 */
	public static function isShown($obField, $arParams){
		return true;
	}
	
	/**
	 *	Is full width?
	 */
	public static function isFullWidth(){
		return false;
	}
	
	/**
	 *	Get all available settings
	 */
	public static function getListAll($arParams=false, $obField=null){
		$arResult = &static::$arCachedSettings;
		#
		$arParams = is_array($arParams) ? $arParams : array();
		#
		if(!is_array($arResult) || empty($arResult)) {
			$arResult = static::getGroups();
			foreach($arResult as $strGroupCode => $strGroupName){
				$arResult[$strGroupCode] = array(
					'NAME' => $strGroupName,
					'ITEMS' => array(),
				);
			}
			$resHandle = opendir(__DIR__);
			while ($strFile = readdir($resHandle)) {
				if($strFile != '.' && $strFile != '..') {
					$strFullFilename = __DIR__.DIRECTORY_SEPARATOR.$strFile;
					if(ToUpper(pathinfo($strFile, PATHINFO_EXTENSION))=='PHP') {
						require_once($strFullFilename);
					}
				}
			}
			closedir($resHandle);
			// You can add your custom settings
			if(is_object($obField) && strlen($obField->getModuleId())){
				foreach (EventManager::getInstance()->findEventHandlers($obField->getModuleId(), 'OnGetAllSettings') as $arHandler) {
					ExecuteModuleEventEx($arHandler, array($arParams));
				}
			}
			//
			foreach(get_declared_classes() as $strClass){
				if(is_subclass_of($strClass, __CLASS__)) {
					$strCode = $strClass::getCode();
					$arItem = array(
						'NAME' => $strClass::getName(),
						'HINT' => $strClass::getHint(),
						'SORT' => $strClass::getSort(),
						'GROUP' => $strClass::getGroup(),
						'CLASS' => $strClass,
						'FULL_WIDTH' => $strClass::isFullWidth(),
						'FOR_FIELDS' => $strClass::isForFields(),
						'FOR_VALUES' => $strClass::isForValues(),
					);
					if(!is_array($arResult[$arItem['GROUP']['CODE']])){
						$arResult[$arItem['GROUP']['CODE']] = array(
							'NAME' => $arItem['GROUP']['NAME'],
							'HINT' => $arItem['GROUP']['HINT'],
							'ITEMS' => array(),
						);
					}
					$arResult[$arItem['GROUP']['CODE']]['ITEMS'][$strCode] = $arItem;
				}
			}
			// You can modify settings list
			foreach (EventManager::getInstance()->findEventHandlers($obField->getModuleId(), 'OnAfterGetAllSettings') as $arHandler) {
				ExecuteModuleEventEx($arHandler, array(&$arResult, $arParams));
			}
			//
			foreach($arResult as $strGroupCode => $strGroupName){
				uasort($arResult[$strGroupCode]['ITEMS'], '\Acrit\Core\Helper::sortBySort');
			}
		}
		#
		return $arResult;
	}
	
	/**
	 *	strType = FIELDS || VALUES
	 */
	protected static function getListForType($strType, $obField, $arParams=false){
		$arResult = static::getListAll($arParams, $obField);
		foreach($arResult as $strGroupCode => $arGroup){
			foreach($arGroup['ITEMS'] as $key => $arItem){
				$arResult[$strGroupCode]['ITEMS'][$key]['CLASS']::setPrefix($strType);
				if(!$arItem['FOR_'.$strType]) {
					unset($arResult[$strGroupCode]['ITEMS'][$key]);
				}
			}
			if(empty($arResult[$strGroupCode]['ITEMS'])){
				unset($arResult[$strGroupCode]);
			}
		}
		
		static::removeNotShownSettings($arResult, $obField, $arParams);
		return $arResult;
	}
	
	public static function getListForFields($obField, $arParams=false){
		return static::getListForType('FIELDS', $obField, $arParams);
	}
	
	public static function getListForValues($obField, $arParams=false){
		return static::getListForType('VALUES', $obField, $arParams);
	}
	
	public static function applySettings(&$mValue, $obField, $arSettings, $arParams){
		$arParams = is_array($arParams) ? $arParams : array();
		foreach($arSettings as $strGroupCode => $arGroup){
			if(is_array($arGroup['ITEMS'])){
				foreach($arGroup['ITEMS'] as $strSettingsName => $arSettingsItem){
					$arSettingsItem['CLASS']::process($mValue, $arParams, $obField);
				}
			}
		}
	}
	
	public static function applySettingsForField(&$mValue, $obField, $arParams){
		$arSettings = static::getListForFields($obField, $arParams);
		static::applySettings($mValue, $obField, $arSettings, $arParams);
	}
	
	public static function applySettingsForValue(&$mValue, $obField, $arParams){
		$arSettings = static::getListForValues($obField, $arParams);
		static::applySettings($mValue, $obField, $arSettings, $arParams);
	}
	
	public static function removeNotShownSettings(&$arSettings, $obField, $arParams){
		foreach($arSettings as $strGroupCode => $arGroup){
			if(is_array($arGroup['ITEMS'])){
				foreach($arGroup['ITEMS'] as $strSettingsName => $arSettingsItem){
					if(!$arSettingsItem['CLASS']::isShown($obField, $arParams)){
						unset($arSettings[$strGroupCode]['ITEMS'][$strSettingsName]);
					}
				}
			}
		}
		foreach($arSettings as $strGroupCode => $arGroup){
			if(empty($arGroup['ITEMS'])){
				unset($arSettings[$strGroupCode]);
			}
		}
	}

	
}
