<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strPluginClass = get_class($this);

#$bIsAdditionalField = AdditionalField::getIdFromCode($strFieldCode) > 0;
$bIsAdditionalField = Helper::call($this->strModuleId, 'AdditionalField', 'getIdFromCode', [$strFieldCode]) > 0;

?>
<div>
	<table class="acrit-exp-field-settings">
		<tbody>
			<tr class="heading">
				<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_GROUP_TOP');?></td>
			</tr>
			<tr>
				<td>
					<label><?=Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_NAME');?>:</label>
				</td>
				<td>
					<input type="text" name="_CUSTOM_XML_NAME" value="<?=$arParams['_CUSTOM_XML_NAME'];?>" size="30" />
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_NAME_HINT'));?>
				</td>
			</tr>
			<tr>
				<td>
					<label><?=Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_ROLE');?>:</label>
				</td>
				<td>
					<?
					$arRoles = array(
						'' => Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_ROLE_DEFAULT'),
						$strPluginClass::ROLE_URL => Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_ROLE_URL'),
						$strPluginClass::ROLE_PICTURE => Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_ROLE_PICTURE'),
						$strPluginClass::ROLE_CATEGORY => Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_ROLE_CATEGORY'),
					);
					if(!$bIsAdditionalField){
						$arRoles[$strPluginClass::ROLE_CURRENCY] = Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_ROLE_CURRENCY');
					}
					$arRoles = array(
						'REFERENCE' => array_values($arRoles),
						'REFERENCE_ID' => array_keys($arRoles),
					);
					print SelectBoxFromArray('_CUSTOM_XML_ROLE', $arRoles, $arParams['_CUSTOM_XML_ROLE'], '', '');
					?>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_ROLE_HINT'));?>
				</td>
			</tr>
			<tr>
				<td>
					<label><?=Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_REQUIRED');?>:</label>
				</td>
				<td>
					<input type="checkbox" name="_CUSTOM_XML_REQUIRED" value="Y"
						<?if($arParams['_CUSTOM_XML_REQUIRED']=='Y'):?>checked="checked"<?endif?> />
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_REQUIRED_HINT'));?>
				</td>
			</tr>
			<tr>
				<td>
					<label><?=Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_MULTIPLE');?>:</label>
				</td>
				<td>
					<input type="checkbox" name="_CUSTOM_XML_MULTIPLE" value="Y"
						<?if($arParams['_CUSTOM_XML_MULTIPLE']=='Y'):?>checked="checked"<?endif?> />
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_MULTIPLE_HINT'));?>
				</td>
			</tr>
			<tr>
				<td>
					<label><?=Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_CDATA');?>:</label>
				</td>
				<td>
					<input type="checkbox" name="_CUSTOM_XML_CDATA" value="Y"
						<?if($arParams['_CUSTOM_XML_CDATA']=='Y'):?>checked="checked"<?endif?> />
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_CDATA_HINT'));?>
				</td>
			</tr>
			<tr>
				<td>
					<label><?=Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_DELETE_MODE');?>:</label>
				</td>
				<td>
					<?
					$arDeleteMode = array(
						$strPluginClass::DELETE_MODE_NO => Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_DELETE_MODE_NO'),
						$strPluginClass::DELETE_MODE_SIMPLE => Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_DELETE_MODE_SIMPLE'),
						$strPluginClass::DELETE_MODE_ATTR => Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_DELETE_MODE_ATTR'),
					);
					$arDeleteMode = array(
						'REFERENCE' => array_values($arDeleteMode),
						'REFERENCE_ID' => array_keys($arDeleteMode),
					);
					print SelectBoxFromArray('_CUSTOM_XML_DELETE_MODE', $arDeleteMode, $arParams['_CUSTOM_XML_DELETE_MODE'], '', '');
					?>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_DELETE_MODE_HINT'));?>
				</td>
			</tr>
		</tbody>
	</table>
	<?=Helper::showNote(Loc::getMessage('ACRIT_EXP_CUSTOM_XML_FIELD_SETTINGS_SAVE_NOTICE'), false, true);?>
</div>
<br/>