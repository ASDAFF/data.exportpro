<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class LeadingZero extends SettingsBase {
	
	public static function getCode(){
		return 'LEADING_ZERO';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 1400;
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
	
	private static function getDefaultValue(){
		return 9;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		$intCount = $arParams[static::getCode().'_count'];
		if(!(is_numeric($intCount) && $intCount > 0 && IntVal($intCount) == $intCount)){
			$intCount = static::getDefaultValue();
		}
		?>
		<input type="checkbox" name="<?=static::getCode();?>" value="Y" id="<?=static::getInputID();?>"
			<?if($arParams[static::getCode()]=='Y'):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		&nbsp;
		<span id="<?=static::getInputID();?>_span">	
			<span><?=static::getMessage('LEADING_ZERO_COUNT');?></span> &nbsp;
			<input type="text" name="<?=static::getCode();?>_count" value="<?=htmlspecialcharsbx($intCount);?>"
				size="5" maxlength="2" style="font-family:'Courier New','Courier',monospace;" />
			<?=Helper::ShowHint(static::getMessage('LEADING_ZERO_COUNT_HINT'));?>
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
				$intCount = $arParams[static::getCode().'_count'];
				if(is_numeric($strValue) && $strValue > 0){
					if(is_numeric($intCount) && $intCount > 0 && IntVal($intCount) == $intCount){
						$strValue = sprintf('%0'.$intCount.'d', $strValue);
					}
				}
			});
			#
		}
	}
	
}
