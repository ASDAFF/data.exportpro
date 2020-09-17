<?
namespace Acrit\Core\Export;

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
			<input type="text" name="PROFILE[NAME]" size="50" maxlength="255" data-role="profile-name" spellcheck="false"
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

// SECTION: SYSTEM
$obTabControl->AddSection('HEADING_SYSTEM', GetMessage('ACRIT_EXP_HEADING_SYSTEM'));

// Site ID
$obTabControl->BeginCustomField('PROFILE[SITE_ID]', Loc::getMessage('ACRIT_EXP_FIELD_SITE_ID'), true);
?>
	<tr id="tr_SITE_ID">
		<td>
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_FIELD_SITE_ID_HINT'));?>
			<label for="field_SITE_ID"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td class="acrit-exp-select-wrapper">
			<select name="PROFILE[SITE_ID]" id="field_SITE_ID" data-role="profile-site">
				<?foreach($arSites as $arSite):?>
					<option value="<?=$arSite['ID'];?>" data-domain="<?=$arSite['SERVER_NAME'];?>"
						<?if($arProfile['SITE_ID']==$arSite['ID']):?> selected="selected"<?endif?>>
						<?=$arSite['NAME'];?>
						<?if(strlen($arSite['SERVER_NAME'])):?>
							[<?=$arSite['SERVER_NAME'];?>]
						<?endif?>
					</option>
				<?endforeach?>
			</select>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[SITE_ID]');

// Domain
$obTabControl->BeginCustomField('PROFILE[DOMAIN]', Loc::getMessage('ACRIT_EXP_FIELD_DOMAIN'), true);
?>
	<tr id="tr_DOMAIN">
		<td>
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_FIELD_DOMAIN_HINT'));?>
			<?=$obTabControl->GetCustomLabelHTML()?>
		</td>
		<td>
			<input type="text" name="PROFILE[DOMAIN]" value="<?=$arProfile['DOMAIN']?>" size="30" maxlength="255"
				data-role="acrit_exp_domain"
				spellcheck="false" />
			<?/*
			<div class="profile_domain_buttons" style="display:inline-block">
				<input type="button" value="" class="acrit_exp_domain_button acrit_exp_domain_from_site" style="display:none" />
			</div>
			*/?>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[DOMAIN]');

// Is HTTPS?
$obTabControl->BeginCustomField('PROFILE[IS_HTTPS]', Loc::getMessage('ACRIT_EXP_FIELD_IS_HTTPS'));
?>
	<tr id="tr_IS_HTTPS">
		<td>
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_FIELD_IS_HTTPS_HINT'));?>
		</td>
		<td>
			<input type="hidden" name="PROFILE[IS_HTTPS]" value="N" />
			<label for="field_IS_HTTPS">
				<input type="checkbox" name="PROFILE[IS_HTTPS]" id="field_IS_HTTPS" value="Y"
					data-role="acrit_exp_use_https"
					<?if($arProfile['IS_HTTPS']!='N'):?> checked="checked"<?endif?>>
				<?=$obTabControl->GetCustomLabelHTML()?>
			</label>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[IS_HTTPS]');

// AutoGenerate?
$obTabControl->BeginCustomField('PROFILE[AUTO_GENERATE]', Loc::getMessage('ACRIT_EXP_FIELD_AUTO_GENERATE'));
?>
	<tr id="tr_AUTO_GENERATE">
		<td>
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_FIELD_AUTO_GENERATE_HINT'));?>
		</td>
		<td>
			<input type="hidden" name="PROFILE[AUTO_GENERATE]" value="N" />
			<label for="field_AUTO_GENERATE">
				<input type="checkbox" name="PROFILE[AUTO_GENERATE]" id="field_AUTO_GENERATE" value="Y"<?if($arProfile['AUTO_GENERATE']=='Y'):?> checked="checked"<?endif?>>
				<?=$obTabControl->GetCustomLabelHTML()?>
			</label>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[AUTO_GENERATE]');

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
			if($bSingle && strlen($strPluginCode)){
				$arProfile['PLUGIN'] = $strPluginCode;
				if(!$obPlugin){
					foreach($arPluginsGrouped as $strGroupCode => $arGroup) {
						foreach($arGroup['ITEMS'] as $arPlugin){
							$obPlugin = new $arPlugin['FORMATS'][0]['CLASS']($strModuleId);
						}
					}
				}
			}
			?>
			<div class="acrit-exp-select-wrapper" style="position:relative;">
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
	<tr id="tr_PLUGIN_FORMAT"<?if(!is_array($arProfilePlugin['FORMATS'])):?> style="display:none"<?endif?>>
		<td>
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_FIELD_FORMAT_HINT'));?>
			<label for="field_PLUGIN_FORMAT"><span class="adm-required-field"><?=Loc::getMessage('ACRIT_EXP_FIELD_FORMAT');?></span></label>
		</td>
		<td class="acrit-exp-select-wrapper">
			<div class="acrit-exp-select-wrapper" style="position:relative;">
				<select name="PROFILE[FORMAT]" id="field_FORMAT"<?if($bSelectDisabled):?> disabled="disabled"<?endif?>>
					<?if(is_array($arProfilePlugin['FORMATS'])):?>
						<?foreach($arProfilePlugin['FORMATS'] as $arFormat):?>
							<option value="<?=$arFormat['CODE'];?>"
								<?if($arFormat['CODE']==$arProfile['FORMAT']):?> selected="selected"<?endif?>
							><?=$arFormat['NAME'];?></option>
						<?endforeach?>
					<?endif?>
				</select>
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
		<?
		$strDescription = trim($obPlugin::getDescription());
		$strExample = trim($obPlugin::getExample());
		?>
		<?if(strlen($strDescription)):?>
			<tr id="tr_PLUGIN_DESCRIPTION_HEADING" class="acrit_exp_type_info heading">
				<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_DESCRIPTION');?></td>
			</tr>
			<tr id="tr_PLUGIN_DESCRIPTION" class="acrit_exp_type_info">
				<td colspan="2">
					<?=$strDescription;?>
				</td>
			</tr>
		<?endif?>
		<?if(strlen($strExample)):?>
			<tr id="tr_PLUGIN_EXAMPLE_HEADING" class="acrit_exp_type_info heading">
				<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_EXAMPLE');?></td>
			</tr>
			<tr id="tr_PLUGIN_EXAMPLE" class="acrit_exp_type_info">
				<td colspan="2">
					<?=$strExample;?>
				</td>
			</tr>
		<?endif?>
	<?else:?>
		<tr id="tr_PLUGIN_DESCRIPTION_HEADING" class="acrit_exp_type_info heading" style="display:none">
			<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_DESCRIPTION');?></td>
		</tr>
		<tr id="tr_PLUGIN_DESCRIPTION" class="acrit_exp_type_info" style="display:none"><td colspan="2"></td></tr>
		<tr id="tr_PLUGIN_EXAMPLE_HEADING" class="acrit_exp_type_info heading" style="display:none">
			<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_EXAMPLE');?></td>
		</tr>
		<tr id="tr_PLUGIN_EXAMPLE" class="acrit_exp_type_info" style="display:none"><td colspan="2"></td></tr>
	<?endif?>
<?
$obTabControl->EndCustomField('PROFILE[PLUGIN]');
?>