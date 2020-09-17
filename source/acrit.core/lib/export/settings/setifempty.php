<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

Helper::loadMessages(__FILE__);

class SettingsSetIfEmpty extends SettingsBase {
	
	public static function getCode(){
		return 'SETIFEMPTY';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 2000;
	}
	
	public static function getGroup(){
		return array(
			'CODE' => 'ADDITIONAL',
		);
	}
	
	public static function isForFields(){
		return true;
	}
	
	public static function isForValues(){
		return true;
	}
	
	public static function isShown($obField, $arParams){
		if(in_array($obField->getModuleId(), array_slice(\Acrit\Core\Export\Exporter::getInstance($obField->getModuleId())->getExportModules(true), -2))){
			return true;
		}
		return false;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		$strValue = $arParams[static::getCode().'_value'];
		?>
		<input type="checkbox" name="<?=static::getCode();?>" value="Y" id="<?=static::getInputID();?>"
			<?if($arParams[static::getCode()]=='Y'):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		&nbsp;
		<span id="<?=static::getInputID();?>_span">
			<input type="text" name="<?=static::getCode();?>_value" value="<?=htmlspecialcharsbx($strValue);?>" size="30" 
				maxlength="255" />
		</span>
		<script>
		$('#<?=static::getInputID();?>').bind('change', function(){
			var eval = $('#<?=static::getInputID();?>_span');
			if($(this).is(':checked')){
				eval.show();
			}
			else{
				eval.hide();
			}
		}).trigger('change');
		</script>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		if($arParams[static::getCode()] == 'Y') {
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				$strReplaceValue = $arParams[static::getCode().'_value'];
				if(!strlen($strValue)){
					$strValue = $strReplaceValue;
				}
			});
		}
	}
	
}
