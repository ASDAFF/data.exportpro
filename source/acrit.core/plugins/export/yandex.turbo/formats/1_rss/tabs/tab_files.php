<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

// Это использовать для всех дополнительных параметров в плагинах, чтобы параметры профиля не пересекались
$strPluginParams = $obPlugin->getPluginParamsInputName();
$arPluginParams = $obPlugin->getPluginParams();

$arStructure = $obPlugin->getDirStructure($arProfile['SITE_ID']);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// DIRS_STRUCTURE
$obTabControl->BeginCustomField($strPluginParams.'[DIRS_STRUCTURE]', $obPlugin::getMessage('DIRS_STRUCTURE'));
?>
	<tr>
		<td width="40%" style="padding-top:5px;vertical-align:top;">
			<?=Helper::showHint($obPlugin::getMessage('DIRS_STRUCTURE_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML()?>:
		</td>
		<td width="60%">
			<div class="acrit-exp-yandex-turbo-structure" data-role="yandex-turbo-structure">
				<?=$obPlugin->displayDirsStructure($arStructure);?>
			</div>
			<br/>
			<div>
				<input type="button" data-role="yandex-turbo-select-all"
					value="<?=$obPlugin::getMessage('DIRS_SELECT_ALL');?>" />
				<input type="button" data-role="yandex-turbo-select-nothing"
					value="<?=$obPlugin::getMessage('DIRS_SELECT_NOTHING');?>" />
				<input type="button" data-role="yandex-turbo-select-root"
					value="<?=$obPlugin::getMessage('DIRS_SELECT_ROOT');?>" />
			</div>
		</td>
	</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[DIRS_STRUCTURE]');

// CUSTOM_PARAM
$obTabControl->BeginCustomField($strPluginParams.'[HTTP_CUSTOM_PARAM]', $obPlugin::getMessage('HTTP_CUSTOM_PARAM'));
?>
	<tr>
		<td width="40%">
			<?=Helper::showHint($obPlugin::getMessage('HTTP_CUSTOM_PARAM_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML()?>:
		</td>
		<td width="60%">
			<input type="text" name="<?=$strPluginParams;?>[HTTP_CUSTOM_PARAM]" size="40"
				value="<?=$arPluginParams['HTTP_CUSTOM_PARAM'];?>"
				placeholder="<?=$arPluginParams['HTTP_CUSTOM_PARAM_PLACEHOLDER'];?>" />
		</td>
	</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[HTTP_CUSTOM_PARAM]');
