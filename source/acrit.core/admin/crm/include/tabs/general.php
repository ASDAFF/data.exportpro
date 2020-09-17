<?
namespace Acrit\Core\Crm;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

if(!$intProfileID){
	$arProfile['NAME'] = Loc::getMessage('ACRIT_EXP_FIELD_NAME_DEFAULT');
	if(!empty($arSites)){
		$arProfile['NAME'] .= ' ['.key($arSites).']';
	}
}

// Active
$obTabControl->BeginCustomField('PROFILE[ACTIVE]', Loc::getMessage('ACRIT_EXP_FIELD_ACTIVE'));
?>
	<tr id="tr_ACTIVE">
		<td>
			<label for="field_ACTIVE"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
			<input type="hidden" name="PROFILE[ACTIVE]" id="field_ACTIVE" value="N" />
			<input type="checkbox" name="PROFILE[ACTIVE]" value="Y"<?if($arProfile['ACTIVE']!='N'):?> checked="checked"<?endif?>>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[ACTIVE]');

// Name
$obTabControl->BeginCustomField('PROFILE[NAME]', Loc::getMessage('ACRIT_EXP_FIELD_NAME'));
?>
	<tr id="tr_NAME">
		<td>
			<label for="field_NAME"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
			<input type="text" name="PROFILE[NAME]" size="50" maxlength="255" data-role="profile-name"
				data-default-name="<?=Loc::getMessage('ACRIT_EXP_FIELD_NAME_DEFAULT');?>"
				<?if($intProfileID):?>data-custom-name="true"<?endif?>
				value="<?=htmlspecialcharsbx($arProfile['NAME']);?>" />
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[NAME]');

// Description
$obTabControl->BeginCustomField('PROFILE[DESCRIPTION]', Loc::getMessage('ACRIT_EXP_FIELD_DESCRIPTION'));
?>
	<tr id="tr_DESCRIPTION">
		<td>
			<label for="field_DESCRIPTION"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
			<textarea name="PROFILE[DESCRIPTION]" id="field_DESCRIPTION" class="acrit-exp-profile-description" 
				style="min-height:48px;resize:vertical;width:80%;"
				cols="51" rows="3"><?=$arProfile['DESCRIPTION']?></textarea>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[DESCRIPTION]');

// Sort
$obTabControl->AddEditField('PROFILE[SORT]', Loc::getMessage('ACRIT_EXP_FIELD_SORT'), false, array('size'=>10, 'maxlength'=>10), 
	$arProfile['SORT']);

// SECTION: Plugin
$obTabControl->AddSection('HEADING_PLUGIN', GetMessage('ACRIT_EXP_HEADING_PLUGIN'));

// Plugin
$obTabControl->BeginCustomField('PROFILE[PLUGIN]', Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN'), true);
?>
	<tr id="tr_PLUGIN">
		<td>
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_HINT'));?>
			<label for="field_PLUGIN"><?=$obTabControl->GetCustomLabelHTML()?></label>
		</td>
		<td>
			<?
			$arPluginsGrouped = array();
			foreach($arPluginTypes as $strPluginType => $strPluginName) {
				$arPluginsGrouped[$strPluginType] = array(
					'NAME' => $strPluginName,
					'ITEMS' => array(),
				);
				foreach($arPlugins as $arPlugin) {
					if($arPlugin['TYPE']==$strPluginType) {
						$arPluginsGrouped[$arPlugin['TYPE']]['ITEMS'][] = $arPlugin;
					}
				}
			}
			$intPluginsCount = 0;
			$strPluginCode = null;
			foreach($arPluginsGrouped as $strGroupCode => $arGroup) {
				if(empty($arGroup['ITEMS']) || !is_array($arGroup['ITEMS'])){
					unset($arPluginsGrouped[$strGroupCode]);
				}
				else{
					$intPluginsCount += count($arGroup['ITEMS']);
					foreach($arGroup['ITEMS'] as $arPlugin){
						$strPluginCode = $arPlugin['CODE'];
					}
				}
			}
			$bShowGroups = count($arPluginsGrouped) > 1;
			$bSelectDisabled = $intProfileID && is_object($obPlugin);
			$bSingle = $intPluginsCount === 1;
			?>
			<div style="position:relative;">
				<select name="PROFILE[PLUGIN]" id="field_PLUGIN"<?if($bSelectDisabled):?> disabled="disabled"<?endif?>>
					<?if(!$bSingle):?>
						<option value=""><?=Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_EMPTY');?></option>
					<?endif?>
					<?foreach($arPluginsGrouped as $strGroupCode => $arGroup):?>
						<?if(!empty($arGroup['ITEMS'])):?>
							<?if($bShowGroups):?>
							<optgroup label="<?=$arGroup['NAME'];?>">
							<?endif?>
								<?foreach($arGroup['ITEMS'] as $arPlugin):?>
									<option value="<?=$arPlugin['CODE'];?>" data-directory="<?=$arPlugin['DIRECTORY'];?>"
										<?if($arPlugin['CODE']==$arProfile['PLUGIN']):?> selected="selected"<?endif?>
										<?if($arPlugin['ICON_BASE64']):?>data-icon="<?=$arPlugin['ICON_BASE64'];?>"<?endif?>
									>
										<?=$arPlugin['NAME'];?>
									</option>
								<?endforeach?>
							<?if($bShowGroups):?>
							</optgroup>
							<?endif?>
						<?endif?>
					<?endforeach?>
				</select>
				<?if(strlen($arProfile['PLUGIN']) && !$bSingle):?>
					<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_ACTIVATE');?>"
						id="input_PLUGIN_activate" data-confirm="<?=Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_ACTIVATE_CONFIRM');?>" />
				<?endif?>
				<?if($bSingle):?>
					<script>
					$(document).ready(function(){
						$('#field_PLUGIN').trigger('change');
					});
					</script>
				<?endif?>
			</div>
		</td>
	</tr>
	<tr id="tr_PLUGIN_SETTINGS"<?if(!is_object($obPlugin)):?> style="display:none"<?endif?>>
		<td colspan="2">
			<div id="div_PLUGIN_SETTINGS">
				<?if(is_object($obPlugin)):?>
					<?=$obPlugin->includeCss();?>
					<?=$obPlugin->includeJs();?>
					<?=$obPlugin->showSettings();?>
				<?endif?>
			</div>
		</td>
	</tr>
	<?if(!$intProfileID):?>
    <tr id="tr_PLUGIN_NEED_SAVE">
        <td></td>
        <td><?=Helper::showNote(Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_NEED_SAVE'));?></td>
    </tr>
	<?endif?>
	<?if(is_object($obPlugin)):?>
		<tr id="tr_PLUGIN_DESCRIPTION_HEADING" class="acrit_exp_type_info heading">
			<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_DESCRIPTION');?></td>
		</tr>
		<tr id="tr_PLUGIN_DESCRIPTION" class="acrit_exp_type_info">
			<td colspan="2">
				<?=$obPlugin::getDescription();?>
			</td>
		</tr>
	<?else:?>
		<tr id="tr_PLUGIN_DESCRIPTION_HEADING" class="acrit_exp_type_info heading" style="display:none">
			<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_DESCRIPTION');?></td>
		</tr>
		<tr id="tr_PLUGIN_DESCRIPTION" class="acrit_exp_type_info" style="display:none"><td colspan="2"></td></tr>
	<?endif?>
<?
$obTabControl->EndCustomField('PROFILE[PLUGIN]');

if ($obPlugin):

// SECTION: Synchronization parameters
$obTabControl->AddSection('HEADING_SYNC_PARAMS', Loc::getMessage('ACRIT_CRM_GENERAL_SYNC_PARAMS'));

// Direction
$obTabControl->BeginCustomField('PROFILE[CONNECT_CRED][direction]', Loc::getMessage('ACRIT_CRM_GENERAL_DIRECTION'));
$directions = $obPlugin->getDirections();
?>
    <tr id="tr_DIRECTION">
        <td>
            <label for="field_CONNECT_CRED_DIRECTION"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_CRED][direction]" id="field_CONNECT_CRED_DIRECTION">
                <?foreach ($directions as $direction):?>
                <option value="<?=$direction['id'];?>>"<?=$arProfile['CONNECT_CRED']['direction'] == $direction['id']?' selected':'';?>><?=$direction['name'];?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_CRED][direction]');

// Start date
$obTabControl->BeginCustomField('PROFILE[CONNECT_CRED][start_date]', Loc::getMessage('ACRIT_CRM_GENERAL_START_DATE'));
?>
    <tr id="tr_START_DATE">
        <td>
            <label for="field_START_DATE"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="text" size="12" maxlength="12" value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['start_date']);?>" name="PROFILE[CONNECT_CRED][start_date]" id="field_START_DATE" class="typeinput" placeholder="<?=GetMessage("SPRODUCTION_CRMSTATUS_DD_MM_GGGG")?>">
	        <?=Calendar('PROFILE[CONNECT_CRED][start_date]', "PROFILE")?>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_CRED][start_date]');

endif;
