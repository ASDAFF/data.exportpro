<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class SettingsEntityDecode extends SettingsBase {
	
	public static function getCode(){
		return 'ENTITY_DECODE';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 500;
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
			<label>
				<input type="checkbox" name="<?=static::getCode();?>_remove_amp" value="Y"
					<?if($arParams[static::getCode().'_remove_amp'] == 'Y'):?>checked="checked"<?endif?> />
				<?=static::getMessage('REMOVE_AMPERSANDS');?>
			</label>
			<?=Helper::ShowHint(static::getMessage('REMOVE_AMPERSANDS_HINT'));?>
		</span>
		<script>
		$('#<?=static::getInputID();?>').bind('change', function(){
			var span = $('#<?=static::getInputID();?>_span');
			if($(this).is(':checked')){
				span.show();
			}
			else{
				span.hide();
			}
		}).trigger('change');
		</script>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		if($arParams[static::getCode()] == 'Y'){
			#
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				$strValue = html_entity_decode($strValue, ENT_QUOTES | ENT_HTML5, (Helper::isUtf() ? 'UTF-8' : 'CP1251'));
				if($arParams[static::getCode().'_remove_amp'] == 'Y'){
					$strValue = str_replace('&', '', $strValue);
				}
			});
			#
		}
	}
	
}
