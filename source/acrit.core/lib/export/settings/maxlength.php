<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class SettingsMaxlength extends SettingsBase {
	
	public static function getCode(){
		return 'MAXLENGTH';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 200;
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
			<?if($arParams[static::getCode()]=='Y' || is_numeric($arParams[static::getCode()])):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		&nbsp;
		<span id="<?=static::getInputID();?>_span">
			<?
			// Support for old case
			if(is_numeric($arParams[static::getCode()])){
				$arParams[static::getCode().'_value'] = $arParams[static::getCode()];
				$arParams[static::getCode().'_dots'] = 'Y';
			}
			?>
			<input type="text" maxlength="10" name="<?=static::getCode();?>_value"
				value="<?=$arParams[static::getCode().'_value'];?>" id="<?=static::getInputID();?>_value" size="5" />
			<?/**/?>
			&nbsp;&nbsp;
			<label>
				<input type="checkbox" name="<?=static::getCode();?>_dots" value="Y" id="<?=static::getInputID();?>_dots"
					<?if($arParams[static::getCode().'_dots']!=''):?> checked="checked"<?endif?> />
				<?=static::getMessage('DOTS_NAME');?>
			</label>
			<?=Helper::ShowHint(static::getMessage('DOTS_DESC'));?>
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
		$('#<?=static::getInputID();?>_round_type').bind('change', function(){
			if($(this).val()=='rules'){
				$('#<?=static::getInputID();?>_round_precision').hide();
			}
			else{
				$('#<?=static::getInputID();?>_round_precision').show();
			}
		}).trigger('change');
		</script>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		// New case
		if($arParams[static::getCode()] == 'Y'){
			if(is_numeric($arParams[static::getCode().'_value']) && $arParams[static::getCode().'_value'] > 0){
				#
				static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
					if(strlen($strValue) > $arParams[static::getCode().'_value']){
						$strDots = '';
						if(strlen($arParams[static::getCode().'_dots'])){
							$strDots = '...';
						}
						$strValue = substr($strValue, 0, $arParams[static::getCode().'_value'] - strlen($strDots)).$strDots;
					}
				});
				#
			}
		}
		// Old case
		elseif(is_numeric($arParams[static::getCode()]) && $arParams[static::getCode()] > 0){
			#
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				if(strlen($strValue) > $arParams[static::getCode()]){
					$strValue = substr($strValue, 0, $arParams[static::getCode()] - 3).'...';
				}
			});
			#
		}
	}
	
}
