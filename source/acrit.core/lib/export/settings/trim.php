<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class SettingsTrim extends SettingsBase {
	
	public static function getCode(){
		return 'TRIM';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 100;
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
		&nbsp;
		<span id="<?=static::getInputID();?>_span">
			<?
			$arOptions = array(
				'trim' => static::getMessage('TYPE_TRIM'),
				'ltrim' => static::getMessage('TYPE_LTRIM'),
				'rtrim' => static::getMessage('TYPE_RTRIM'),
			);
			$arOptions = array(
				'REFERENCE' => array_values($arOptions),
				'REFERENCE_ID' => array_keys($arOptions),
			);
			print SelectBoxFromArray(static::getCode().'_type', $arOptions, 
				$arParams[static::getCode().'_type'], '', 'id="'.static::getInputID().'_type"');
			?>
			<input type="text" maxlength="10" name="<?=static::getCode();?>_chars"
				value="<?=$arParams[static::getCode().'_chars'];?>" id="<?=static::getInputID();?>_chars" size="10" 
				placeholder="<?=static::getMessage('CHARS');?>" />
		</span>
		<script>
		$('#<?=static::getInputID();?>').bind('change', function(){
			var subSettings = $('#<?=static::getInputID();?>_span');
			if($(this).is(':checked')){
				subSettings.show();
			}
			else{
				subSettings.hide();
			}
		}).trigger('change');
		</script>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		if($arParams[static::getCode()] == 'Y'){
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				$strCharList = $arParams[static::getCode().'_chars'];
				if(strlen($strCharList)){
					$arCharReplace = ['\n' => "\n", '\r' => "\r", '\s' => "\s", '\t' => "\t", '\v' => "\v"];
					$strCharList = str_replace(array_keys($arCharReplace), array_values($arCharReplace), $strCharList);
				}
				else{
					$strCharList = " \t\n\r\0\x0B";
				}
				switch($arParams[static::getCode().'_type']){
					case 'trim':
						$strValue = trim($strValue, $strCharList);
						break;
					case 'ltrim':
						$strValue = ltrim($strValue, $strCharList);
						break;
					case 'rtrim':
						$strValue = rtrim($strValue, $strCharList);
						break;
				}
			});
		}
	}
	
}
