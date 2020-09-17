<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

// Это использовать для всех дополнительных параметров в плагинах, чтобы параметры профиля не пересекались
$strPluginParams = $obPlugin->getPluginParamsInputName();
$arPluginParams = $obPlugin->getPluginParams();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// EXPORT_GIFTS
$obTabControl->BeginCustomField($strPluginParams.'[EXPORT_GIFTS]', $obPlugin::getMessage('EXPORT_GIFTS'));
?>
	<tr id="row_YANDEX_MARKET_EXPORT_GIFTS">
		<td width="40%" valign="top">
			<?=Helper::showHint($obPlugin::getMessage('EXPORT_GIFTS_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML()?>:
		</td>
		<td width="60%">
			<label>
				<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_GIFTS]" value="Y"
					<?if($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_GIFTS']=='Y'):?>checked="checked"<?endif?>/>
			</label>
		</td>
	</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[EXPORT_GIFTS]');

