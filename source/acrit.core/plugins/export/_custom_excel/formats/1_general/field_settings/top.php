<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$strPluginClass = get_class($this);

$bIsAdditionalField = Helper::call($this->strModuleId, 'AdditionalField', 'getIdFromCode', [$strFieldCode]) > 0;

$strId = 'input_'.MD5(__FILE__);

?>
<div>
	<table class="acrit-exp-field-settings">
		<tbody>
			<tr class="heading">
				<td colspan="2"><?=static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_GROUP_TOP');?></td>
			</tr>
			<tr>
				<td>
					<label for="<?=$strId;?>_1"><?=static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_NAME');?>:</label>
				</td>
				<td>
					<input type="text" name="_CUSTOM_EXCEL_NAME" value="<?=$arParams['_CUSTOM_EXCEL_NAME'];?>" size="30"
						id="<?=$strId;?>_1" />
					<?=Helper::showHint(static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_NAME_HINT'));?>
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?=$strId;?>_2"><?=static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_ROLE');?>:</label>
				</td>
				<td>
					<?
					$arRoles = array(
						'' => static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_ROLE_DEFAULT'),
						$strPluginClass::ROLE_URL => static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_ROLE_URL'),
					);
					$arRoles = array(
						'REFERENCE' => array_values($arRoles),
						'REFERENCE_ID' => array_keys($arRoles),
					);
					print SelectBoxFromArray('_CUSTOM_EXCEL_ROLE', $arRoles, $arParams['_CUSTOM_EXCEL_ROLE'], '', 'id="'.$strId.'_2"');
					?>
					<?=Helper::showHint(static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_ROLE_HINT'));?>
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?=$strId;?>_3"><?=static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_REQUIRED');?>:</label>
				</td>
				<td>
					<input type="checkbox" name="_CUSTOM_EXCEL_REQUIRED" value="Y"
						<?if($arParams['_CUSTOM_EXCEL_REQUIRED']=='Y'):?>checked="checked"<?endif?> id="<?=$strId;?>_3" />
					<?=Helper::showHint(static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_REQUIRED_HINT'));?>
				</td>
			</tr>
			<tr class="heading">
				<td colspan="2"><?=static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_GROUP_BOTTOM');?></td>
			</tr>
			<tr>
				<td>
					<label for="<?=$strId;?>_4">
						<?=static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_WIDTH');?>:</label>
				</td>
				<td>
					<input type="text" name="_CUSTOM_EXCEL_WIDTH" value="<?=$arParams['_CUSTOM_EXCEL_WIDTH'];?>" size="10" 
						maxlength="4" id="<?=$strId;?>_4" />
					<?=Helper::showHint(static::getMessage('ACRIT_EXP_CUSTOM_EXCEL_FIELD_SETTINGS_WIDTH_HINT'));?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<br/>