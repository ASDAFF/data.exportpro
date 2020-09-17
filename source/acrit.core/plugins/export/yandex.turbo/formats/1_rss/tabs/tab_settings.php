<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

// Это использовать для всех дополнительных параметров в плагинах, чтобы параметры профиля не пересекались
$strPluginParams = 'PROFILE[PARAMS][_PLUGINS]['.$obPlugin::getCode().']';
$arPluginParams = $arProfile['PARAMS']['_PLUGINS'][$obPlugin::getCode()];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// COUNT_PER_FILE
$obTabControl->BeginCustomField($strPluginParams.'[COUNT_PER_FILE]', $obPlugin::getMessage('COUNT_PER_FILE'));
?>
	<tr>
		<td width="40%">
			<?=Helper::showHint($obPlugin::getMessage('COUNT_PER_FILE_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML()?>:
		</td>
		<td width="60%">
			<input type="text" name="<?=$strPluginParams;?>[COUNT_PER_FILE]"
				value="<?=$obPlugin::getCountPerPage($arPluginParams['COUNT_PER_FILE']);?>" maxlength="5" />
		</td>
	</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[COUNT_PER_FILE]');

// BUTTON
$obTabControl->BeginCustomField($strPluginParams.'[SHOW_BUTTON]', $obPlugin::getMessage('SHOW_BUTTON'));
?>
	<tr id="row_YANDEX_TURBO_SHOW_BUTTON">
		<td width="40%">
			<?=Helper::showHint($obPlugin::getMessage('SHOW_BUTTON_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML()?>:
		</td>
		<td width="60%">
			<input type="hidden" name="<?=$strPluginParams;?>[SHOW_BUTTON]" value="N" />
			<input type="checkbox" name="<?=$strPluginParams;?>[SHOW_BUTTON]" value="Y" 
				<?if($arPluginParams['SHOW_BUTTON']=='Y'):?>checked="checked"<?endif?> />
		</td>
	</tr>
	<tr class="row_YANDEX_TURBO_BUTTON_SETTINGS" style="display:none">
		<td>
			<?=Helper::showHint($obPlugin::getMessage('BUTTON_ACTION_DESC'));?>
			<?=$obPlugin::getMessage('BUTTON_ACTION');?>:
		</td>
		<td>
			<input type="text" name="<?=$strPluginParams;?>[BUTTON_ACTION]" size="53"
				value="<?=$arPluginParams['BUTTON_ACTION'];?>" maxlength="255" />
		</td>
	</tr>
	<tr class="row_YANDEX_TURBO_BUTTON_SETTINGS" style="display:none">
		<td>
			<?=Helper::showHint($obPlugin::getMessage('BUTTON_BACKGROUND_COLOR_DESC'));?>
			<?=$obPlugin::getMessage('BUTTON_BACKGROUND_COLOR');?>:
		</td>
		<td>
			<input type="text" name="<?=$strPluginParams;?>[BUTTON_BACKGROUND_COLOR]"
				value="<?=$arPluginParams['BUTTON_BACKGROUND_COLOR'];?>" maxlength="50" />
		</td>
	</tr>
	<tr class="row_YANDEX_TURBO_BUTTON_SETTINGS" style="display:none">
		<td>
			<?=Helper::showHint($obPlugin::getMessage('BUTTON_COLOR_DESC'));?>
			<?=$obPlugin::getMessage('BUTTON_COLOR');?>:
		</td>
		<td>
			<input type="text" name="<?=$strPluginParams;?>[BUTTON_COLOR]"
				value="<?=$arPluginParams['BUTTON_COLOR'];?>" maxlength="50" />
		</td>
	</tr>
	<tr class="row_YANDEX_TURBO_BUTTON_SETTINGS" style="display:none">
		<td>
			<?=Helper::showHint($obPlugin::getMessage('BUTTON_TEXT_DESC'));?>
			<?=$obPlugin::getMessage('BUTTON_TEXT');?>:
		</td>
		<td>
			<input type="text" name="<?=$strPluginParams;?>[BUTTON_TEXT]"
				value="<?=$arPluginParams['BUTTON_TEXT'];?>" maxlength="255" />
		</td>
	</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[SHOW_BUTTON]');

// SHARE
$obTabControl->BeginCustomField($strPluginParams.'[SHARE]', $obPlugin::getMessage('SHARE'));
$arPluginParams['SHARE'] = is_array($arPluginParams['SHARE']) ? $arPluginParams['SHARE'] : array();
?>
	<tr class="row_YANDEX_TURBO_SHARE">
		<td width="40%" valign="top">
			<?=Helper::showHint($obPlugin::getMessage('SHARE_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML()?>:
		</td>
		<td width="60%">
			<table>
				<tbody>
					<?foreach($obPlugin::getSharesAll() as $key => $arShare):?>
						<tr>
							<td>
								<label>
									<input type="checkbox" name="<?=$strPluginParams;?>[SHARE][]" value="<?=$key;?>" 
										<?if(in_array($key, $arPluginParams['SHARE'])):?>checked="checked"<?endif?> />
									<?=$arShare['NAME'];?>
								</label>
							</td>
							<td>
								<input type="text" name="<?=$strPluginParams;?>[SHARE_URL][<?=$key;?>]" size="40"
									value="<?=htmlspecialcharsbx($arPluginParams['SHARE_URL'][$key]);?>"
									style="height:22px; visibility:hidden" />
							</td>
						</tr>
					<?endforeach?>
				</tbody>
			</table>
		</td>
	</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[SHARE]');
?>
