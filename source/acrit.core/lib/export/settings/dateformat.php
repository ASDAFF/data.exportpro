<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class DateFormat extends SettingsBase {
	
	public static function getCode(){
		return 'DATEFORMAT';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 1300;
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
	
	private static function getInputFrom(){
		return static::getCode().'_from';
	}
	
	private static function getInputTo(){
		return static::getCode().'_to';
	}
	
	private static function getInputKeep(){
		return static::getCode().'_keep_wrong';
	}
	
	private static function getChange(){
		return static::getCode().'_do_change';
	}
	
	private static function getChangeValue(){
		return static::getCode().'_change_value';
	}
	
	private static function getChangeType(){
		return static::getCode().'_change_type';
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		$strInputFrom = static::getInputFrom();
		$strInputTo = static::getInputTo();
		$strInputKeep = static::getInputKeep();
		#
		$strInputChange = static::getChange();
		$strInputChangeValue = static::getChangeValue();
		$strInputChangeType = static::getChangeType();
		#
		if(!strlen($arParams[$strInputFrom])){
			$arParams[$strInputFrom] = \CDatabase::DateFormatToPHP(FORMAT_DATETIME);
		}
		?>
		<input type="checkbox" name="<?=static::getCode();?>" value="Y" id="<?=static::getInputID();?>"
			<?if($arParams[static::getCode()]=='Y'):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		&nbsp;
		<span id="<?=static::getInputID();?>_dates">
			<input type="text" name="<?=$strInputFrom;?>" value="<?=$arParams[$strInputFrom];?>"
				size="15" style="font-family:'Courier New','Courier',monospace;" />
			<?=static::getMessage('TEXT');?>
			<input type="text" name="<?=$strInputTo;?>" value="<?=$arParams[$strInputTo];?>"
				size="15" style="font-family:'Courier New','Courier',monospace;" />
			&nbsp;
			<label>
				<input type="checkbox" name="<?=$strInputKeep;?>" value="Y"
					<?if($arParams[$strInputKeep]):?> checked="checked"<?endif?> />
				<?=static::getMessage('KEEP');?>
			</label>
			<?=Helper::ShowHint(static::getMessage('KEEP_HINT'));?>
		</span>
		<span id="<?=static::getInputID();?>_change">
			<span style="display:block; height:0; margin-bottom:2px;"></span>
			<input type="checkbox" name="<?=$strInputChange;?>" value="Y" id="<?=$strInputChange;?>"
				<?if($arParams[$strInputChange]=='Y'):?> checked="checked"<?endif?> />
			<?=Helper::ShowHint(static::getMessage('CHANGE_HINT'));?>
			<label for="<?=$strInputChange;?>"><?=static::getMessage('CHANGE');?></label>
			&nbsp;
			<input type="text" name="<?=$strInputChangeValue;?>" value="<?=IntVal($arParams[$strInputChangeValue]);?>"
				size="8" style="font-family:'Courier New','Courier',monospace;" />
			<select name="<?=$strInputChangeType;?>">
				<option value="D"<?if($arParams[$strInputChangeType]=='D'):?> selected="selected"<?endif?>>
					<?=static::getMessage('CHANGE_DAYS');?>
				</option>
				<option value="H"<?if($arParams[$strInputChangeType]=='H'):?> selected="selected"<?endif?>>
					<?=static::getMessage('CHANGE_HOURS');?>
				</option>
				<option value="M"<?if($arParams[$strInputChangeType]=='M'):?> selected="selected"<?endif?>>
					<?=static::getMessage('CHANGE_MINUTES');?>
				</option>
				<option value="S"<?if($arParams[$strInputChangeType]=='S'):?> selected="selected"<?endif?>>
					<?=static::getMessage('CHANGE_SECONDS');?>
				</option>
			</select>
		</span>
		<input type="text" style="padding-left:0; padding-right:0; visibility:hidden; width:0;" />
		<script>
		$('#<?=static::getInputID();?>').bind('change', function(){
			var dates = $('#<?=static::getInputID();?>_dates');
			var change = $('#<?=static::getInputID();?>_change');
			if($(this).is(':checked')){
				dates.show();
				change.show();
			}
			else{
				dates.hide();
				change.hide();
			}
		}).trigger('change');
		</script>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		if($arParams[static::getCode()]=='Y'){
			#
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				$strInputFrom = static::getInputFrom();
				$strInputTo = static::getInputTo();
				$strInputKeep = static::getInputKeep();
				if(!strlen($arParams[$strInputFrom])){
					$arParams[$strInputFrom] = \CDatabase::dateFormatToPHP(FORMAT_DATETIME);
				}
				if(strlen($arParams[$strInputFrom]) && strlen($arParams[$strInputTo])){
					$obDateTime = \DateTime::createFromFormat($arParams[$strInputFrom], $strValue);
					if($obDateTime === false) {
						if($arParams[$strInputKeep] != 'Y'){
							$strValue = null;
						}
					}
					else {
						static::changeDate($strValue, $obDateTime, $arParams, $obField);
						$strValue = $obDateTime->format($arParams[$strInputTo]);
					}
					unset($obDateTime);
				}
			});
			#
		}
	}
	
	protected static function changeDate(&$strValue, $obDateTime, $arParams, $obField){
		$strInputChange = static::getChange();
		$strInputChangeValue = static::getChangeValue();
		$strInputChangeType = static::getChangeType();
		#
		if($arParams[$strInputChange] == 'Y'){
			try {
				$strDateIntervalValue = IntVal($arParams[$strInputChangeValue]);
				$strDateIntervalType = $arParams[$strInputChangeType];
				if(!strlen($strDateIntervalType)){
					$strDateIntervalType = 'D';
				}
				$strDateIntervalTime = in_array($strDateIntervalType, array('H', 'M', 'S')) ? 'T' : '';
				$strInterval = 'P'.$strDateIntervalTime.abs($strDateIntervalValue).$strDateIntervalType;
				if($strDateIntervalValue > 0){
					$obDateTime->add(new \DateInterval($strInterval));
				}
				elseif($strDateIntervalValue < 0){
					$obDateTime->sub(new \DateInterval($strInterval));
				}
			} catch(\Exception $e) {
				$strLogMessage = static::getMessage('CHANGE_LOG_MESSAGE', array(
					'#MESSAGE#' => $e->getMessage(),
					'#FIELD#' => (is_object($obField) ? $obField->getCode() : (strlen($arParams['field_code']) ? $arParams['field_code'] : '?')),
					'#VALUE#' => $strValue,
				));
				if(is_object($obField) && strlen($obField->getModuleId())){
					Log::getInstance($obField->getModuleId())->add($strLogMessage, (is_object($obField) ? $obField->getProfileID() : false), false);
				}
			}
		}
	}
	
}
