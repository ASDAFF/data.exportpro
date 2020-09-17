<?
/**
 * Class for settings of fields and values
 * This settings is used in core, we cannot control its behaviour here
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class evenEmpty extends SettingsBase {
	
	public static function getCode(){
		return 'EVEN_EMPTY';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 0;
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
	
	public static function isShown($obField, $arParams, $arValue=null){
		if($arParams['value_type']!='FIELD' && is_subclass_of($obField->getPlugin(), '\Acrit\Core\Export\UniversalPlugin')){
			return true;
		}
		return false;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		?>
		<input type="checkbox" name="<?=static::getCode();?>" value="Y" id="<?=static::getInputID();?>"
			<?if($arParams[static::getCode()]=='Y'):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		// nothing, logic in each plugin.php
	}
	
}
