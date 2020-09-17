<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class SettingsReplace extends SettingsBase {
	
	public static function getCode(){
		return 'REPLACE';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 1000;
	}
	
	public static function getGroup(){
		return array(
			'CODE' => 'REPLACES',
			'NAME' => static::getMessage('GROUP'),
			'HINT' => static::getHint(),
		);
	}
	
	public static function isForFields(){
		return true;
	}
	
	public static function isForValues(){
		return true;
	}
	
	public static function isFullWidth(){
		return true;
	}
	
	public static function isShown($obField, $arParams){
		return true;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		$arReplace = $arParams[static::getCode()];
		$bRegExp = end(\Acrit\Core\Export\Exporter::getExportModules(true)) == $obField->getModuleId();
		if(!is_array($arReplace) || !is_array($arReplace['from']) || !is_array($arReplace['to'])){
			$arReplace = array('from'=>array(),'to'=>array());
		}
		$arReplaceTmp = array();
		foreach($arReplace['from'] as $key => $value){
			$arReplaceTmp[] = array(
				'FROM' => $arReplace['from'][$key],
				'TO' => $arReplace['to'][$key],
				'USE_REGEXP' => is_array($arReplace['use_regexp']) ? $arReplace['use_regexp'][$key] : '',
				'CASE_SENSITIVE' => is_array($arReplace['case_sensitive']) ? $arReplace['case_sensitive'][$key] : '',
				'MODIFIER' => is_array($arReplace['modifier']) ? $arReplace['modifier'][$key] : '',
			);
		}
		array_unshift($arReplaceTmp, true);
		$strUniqID = md5(uniqid().time());
		?>
			<table class="acrit-exp-field-settings-replace" data-role="value-replaces"
				id="acrit-exp-field-settings-replace-<?=$strUniqID;?>" >
				<tfoot>
					<td colspan="<?=($bRegExp?'5':'4');?>">
						<input type="button" value="<?=static::getMessage('ADD');?>" data-role="replace-add" />
					</td>
				</tfoot>
				<tbody>
					<?foreach($arReplaceTmp as $arReplaceItem):?>
						<?
						$bIsHidden = !is_array($arReplaceItem);
						if($bIsHidden){
							$arReplaceItem = array();
						}
						?>
						<tr <?if($bIsHidden):?> class="acrit-exp-field-settings-replace-row-hidden" data-noserialize="Y"<?endif?>>
							<td class="cell-from">
								<input type="text" size="10" name="<?=static::getCode();?>[from][]" value="<?=htmlspecialcharsbx($arReplaceItem['FROM']);?>" placeholder="<?=static::getMessage('FROM');?>" data-role="replace-from" />
							</td>
							<td class="cell-to">
								<input type="text" size="10" name="<?=static::getCode();?>[to][]" value="<?=htmlspecialcharsbx($arReplaceItem['TO']);?>" placeholder="<?=static::getMessage('TO');?>" data-role="replace-to" />
							</td>
							<?if($bRegExp):?>
								<td class="cell-regexp" style="white-space:nowrap">
									<label class="checkbox" title="<?=static::getMessage('USE_REGEXP_HINT');?>">
										<input type="hidden" name="<?=static::getCode();?>[use_regexp][]" value="<?=$arReplaceItem['USE_REGEXP'];?>" />
										<input type="checkbox" data-role="replace-use-regexp"<?if($bIsHidden):?> class="no-checkbox-styling"<?endif?><?if($arReplaceItem['USE_REGEXP']=='Y'):?> checked="checked"<?endif?> />
										<?=static::getMessage('USE_REGEXP');?>
									</label>
									<input type="text" size="3" maxlength="50" style="width:60px" data-role="replace-regexp-modifier" 
										name="<?=static::getCode();?>[modifier][]" value="<?=$arReplaceItem['MODIFIER'];?>"
										placeholder="<?=static::getMessage('MODIFIER');?>"
										title="<?=static::getMessage('MODIFIER_HINT');?>" />
								</td>
							<?endif?>
							<td class="cell-casesensitive">
								<label class="checkbox" title="<?=static::getMessage('CASE_SENSITIVE_HINT');?>">
									<input type="hidden" name="<?=static::getCode();?>[case_sensitive][]" value="<?=$arReplaceItem['CASE_SENSITIVE'];?>" />
									<input type="checkbox" data-role="replace-case-sensitive"<?if($bIsHidden):?> class="no-checkbox-styling"<?endif?><?if($arReplaceItem['CASE_SENSITIVE']=='Y'):?> checked="checked"<?endif?> />
									<?=static::getMessage('CASE_SENSITIVE');?>
								</label>
							</td>
							<td class="cell-delete">
								<a href="#" data-role="replace-delete" title="<?=static::getMessage('DELETE_HINT');?>">&times;</a>
							</td>
						</tr>
					<?endforeach?>
					<tr class="acrit-exp-field-settings-replace-row-nothing">
						<td colspan="<?=($bRegExp?'5':'4');?>"><?=static::getMessage('NOTHING');?></td>
					</tr>
				</tbody>
			</table>
			<?if($bRegExp):?>
				<script>
				$('#acrit-exp-field-settings-replace-<?=$strUniqID;?> tr:visible [data-role="replace-use-regexp"]').each(function(){
					$(this).trigger('change');
				});
				</script>
			<?endif?>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		#
		if(is_array($arParams[static::getCode()]) && !empty($arParams[static::getCode()]['from'])){
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				$mParamValue = $arParams[static::getCode()];
				foreach($mParamValue['from'] as $key => $value){
					$bCaseSensitive = $mParamValue['case_sensitive'][$key]=='Y';
					if($mParamValue['use_regexp'][$key]=='Y'){
						$strModifier = trim($mParamValue['modifier'][$key]);
						if($bCaseSensitive && stripos($strModifier, 'i') === false){
							$strModifier = 'i'.$strModifier;
						}
						$strPattern = $mParamValue['from'][$key];
						$strPattern = str_replace('#', '\#', $strPattern);
						$strValue = preg_replace('#'.$strPattern.'#'.$strModifier, $mParamValue['to'][$key], $strValue);
					}
					else {
						if($bCaseSensitive) {
							$strValue = str_replace($mParamValue['from'][$key], $mParamValue['to'][$key], $strValue);
						}
						else {
							$strValue = str_ireplace($mParamValue['from'][$key], $mParamValue['to'][$key], $strValue);
						}
					}
				}
			});
		}
		#
	}
	
}
