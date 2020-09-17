<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strPluginClass = get_class($this);

$bIsAdditionalField = Helper::call($this->strModuleId, 'AdditionalField', 'getIdFromCode', [$strFieldCode]) > 0;

?>
<div>
	<table class="acrit-exp-field-settings">
		<tbody>
			<tr class="heading">
				<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_GROUP_TOP');?></td>
			</tr>
			<tr>
				<td>
					<label><?=Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_NAME');?>:</label>
				</td>
				<td>
					<input type="text" name="_CUSTOM_CSV_NAME" value="<?=$arParams['_CUSTOM_CSV_NAME'];?>" size="30" />
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_NAME_HINT'));?>
				</td>
			</tr>
			<tr>
				<td>
					<label><?=Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_ROLE');?>:</label>
				</td>
				<td>
					<?
					$arRoles = array(
						'' => Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_ROLE_DEFAULT'),
						$strPluginClass::ROLE_URL => Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_ROLE_URL'),
					);
					$arRoles = array(
						'REFERENCE' => array_values($arRoles),
						'REFERENCE_ID' => array_keys($arRoles),
					);
					print SelectBoxFromArray('_CUSTOM_CSV_ROLE', $arRoles, $arParams['_CUSTOM_CSV_ROLE'], '', '');
					?>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_ROLE_HINT'));?>
				</td>
			</tr>
			<tr>
				<td>
					<label><?=Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_REQUIRED');?>:</label>
				</td>
				<td>
					<input type="checkbox" name="_CUSTOM_CSV_REQUIRED" value="Y"
						<?if($arParams['_CUSTOM_CSV_REQUIRED']=='Y'):?>checked="checked"<?endif?> />
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_REQUIRED_HINT'));?>
				</td>
			</tr>
		</tbody>
	</table>
	<?=Helper::showNote(Loc::getMessage('ACRIT_EXP_CUSTOM_CSV_FIELD_SETTINGS_SAVE_NOTICE'), false, true);?>
</div>
<br/>