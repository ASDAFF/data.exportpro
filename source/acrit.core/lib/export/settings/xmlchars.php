<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class XmlChars extends SettingsBase {
	
	public static function getCode(){
		return 'XMLCHARS';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 400;
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
		?>
		<input type="checkbox" name="<?=static::getCode();?>" value="Y" id="<?=static::getInputID();?>"
			<?if($arParams[static::getCode()]=='Y'):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		if($arParams[static::getCode()] == 'Y'){
			#
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				$strValue = static::removeForbiddenAsciiCharacters($strValue);
			});
			#
		}
	}
	
	protected static function removeForbiddenAsciiCharacters($strText){
		$arExclude = array(
			'9' => "\t",
			'10' => "\n",
			'13' => "\r",
			'32' => " ",
		);
		for($i=1; $i<=32; $i++){
			if(array_key_exists($i, $arExclude)){
				continue;
			}
			$strText = str_replace(chr($i), '', $strText);
		}
		return $strText;
	}
	
}
