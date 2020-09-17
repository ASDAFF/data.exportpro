<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

// Это использовать для всех дополнительных параметров в плагинах, чтобы параметры профиля не пересекались
$strPluginParams = $obPlugin->getPluginParamsInputName();
$arPluginParams = $obPlugin->getPluginParams();

#
$bCanExportPromocodes = false;
$arDiscounts = array();
if(\Bitrix\Main\Loader::includeModule('sale')){
	$bCanExportPromocodes = true;
	$discountIterator = \Bitrix\Sale\Internals\DiscountTable::getList(array(
		'select' => array('ID', 'NAME'),
	));
	while ($discount = $discountIterator->fetch()) {
		$arDiscounts[$discount['ID']] = $discount;
	}
}
else {
	$arProfile['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES'] = 'N';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('PROMOS', $obPlugin::getMessage('PROMOS_HEADER'));

// EXPORT PROMOCODES
$obTabControl->BeginCustomField($strPluginParams.'[EXPORT_PROMOCODES]', $obPlugin::getMessage('EXPORT_PROMOCODES'));
?>
<tr id="row_YANDEX_MARKET_EXPORT_PROMOCODES">
	<td width="40%" valign="top">
		<label for="acrit_exp_yandex_market_export_promocodes">
			<?=Helper::showHint($obPlugin::getMessage('EXPORT_PROMOCODES_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML() ?>:
		</label>
	</td>
	<td width="60%">
		<label>
			<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_PROMOCODES]" value="Y"
				id="acrit_exp_yandex_market_export_promocodes"
				<?if($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES'] == 'Y'): ?>checked="checked"<?endif?>
				<?if(!$bCanExportPromocodes):?>disabled="disabled"<?endif?>/>
			<?if(!$bCanExportPromocodes):?>
				<?=$obPlugin::getMessage('EXPORT_PROMOCODES_NO_SALE');?>
			<?endif?>
		</label>
	</td>
</tr>
<?if($bCanExportPromocodes):?>
	<tr id="row_YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS">
		<td width="40%" valign="top">
			<label for="acrit_exp_yandex_market_export_promocodes_fields">
				<?=Helper::showHint($obPlugin::getMessage('EXPORT_PROMOCODES_RULES_DESC'));?>
				<?=$obPlugin::getMessage('EXPORT_PROMOCODES_RULES');?>
			</label>
		</td>
		<td width="60%">
			<label>
				<select name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS][]" multiple="multiple"
					id="acrit_exp_yandex_market_export_promocodes_fields">
					<?foreach($arDiscounts as $arDiscount):?>
						<?$bSelected = is_array($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS']) && 
							in_array($arDiscount['ID'], $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS']);?>
						<option value="<?=$arDiscount['ID'] ?>"
							<?if($bSelected):?>selected="selected"<?endif?>><?=$arDiscount['NAME']?> [<?=$arDiscount['ID'];?>]</option>
					<?endforeach?>
				</select>
			</label>
		</td>
	</tr>
<?endif?>
<?
$obTabControl->EndCustomField($strPluginParams.'[EXPORT_PROMOCODES]');

// EXPORT SPECIAL PRICE
$obTabControl->BeginCustomField($strPluginParams.'[EXPORT_SPECIAL_PRICE]', $obPlugin::getMessage('EXPORT_SPECIAL_PRICE'));
?>
<tr id="row_YANDEX_MARKET_EXPORT_SPECIAL_PRICE">
	<td width="40%" valign="top">
		<label for="acrit_exp_yandex_market_export_special_price">
			<?=Helper::showHint($obPlugin::getMessage('EXPORT_SPECIAL_PRICE_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML() ?>:
		</label>
	</td>
	<td width="60%">
		<label>
			<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_SPECIAL_PRICE]" value="Y"
				id="acrit_exp_yandex_market_export_special_price"
				<?if($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE'] == 'Y'): ?>checked="checked"<?endif?>/>
		</label>
	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[EXPORT_SPECIAL_PRICE]');

// EXPORT PROMOCARD
$obTabControl->BeginCustomField($strPluginParams.'[EXPORT_PROMOCARD]', $obPlugin::getMessage('EXPORT_PROMOCARD'));
?>
<tr id="row_YANDEX_MARKET_EXPORT_PROMOCARD">
	<td width="40%" valign="top">
		<label for="acrit_exp_yandex_market_export_promocard">
			<?=Helper::showHint($obPlugin::getMessage('EXPORT_PROMOCARD_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML() ?>:
		</label>
	</td>
	<td width="60%">
		<label>
			<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_PROMOCARD]" value="Y"
				id="acrit_exp_yandex_market_export_promocard"
				<?if($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCARD'] == 'Y'): ?>checked="checked"<?endif?>/>
		</label>
	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[EXPORT_PROMOCARD]');

// EXPORT N_PLUS_M
$obTabControl->BeginCustomField($strPluginParams.'[EXPORT_N_PLUS_M]', $obPlugin::getMessage('EXPORT_N_PLUS_M'));
?>
<tr id="row_YANDEX_MARKET_EXPORT_N_PLUS_M">
	<td width="40%" valign="top">
		<label for="acrit_exp_yandex_market_export_n_plus_m">
			<?=Helper::showHint($obPlugin::getMessage('EXPORT_N_PLUS_M_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML() ?>:
		</label>
	</td>
	<td width="60%">
		<label>
			<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_N_PLUS_M]" value="Y"
				id="acrit_exp_yandex_market_export_n_plus_m"
				<?if($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M'] == 'Y'): ?>checked="checked"<?endif?>/>
		</label>
	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[EXPORT_N_PLUS_M]');

// EXPORT_GIFTS
$obTabControl->BeginCustomField($strPluginParams.'[EXPORT_GIFTS]', $obPlugin::getMessage('EXPORT_GIFTS'));
?>
<tr id="row_YANDEX_MARKET_EXPORT_GIFTS">
	<td width="40%" valign="top">
		<label for="acrit_exp_yandex_market_export_gifts">
			<?=Helper::showHint($obPlugin::getMessage('EXPORT_GIFTS_DESC'));?>
			<?=$obTabControl->GetCustomLabelHTML() ?>:
		</label>
	</td>
	<td width="60%">
		<label>
			<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_GIFTS]" value="Y"
				id="acrit_exp_yandex_market_export_gifts"
				<?if($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_GIFTS'] == 'Y'): ?>checked="checked"<?endif?>/>
		</label>
	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[EXPORT_GIFTS]');

// EXPORT_PROMOS_HELP_URL
$obTabControl->BeginCustomField($strPluginParams.'[EXPORT_PROMOS_HELP_URL]', $obPlugin::getMessage('EXPORT_PROMOS_HELP_URL'));
?>
<tr id="row_YANDEX_MARKET_EXPORT_PROMOS_NOTICE">
	<td width="40%" valign="top"></td>
	<td width="60%">
		<?=Helper::showNote($obPlugin::getMessage('EXPORT_PROMOS_HELP_URL'), true);?>
	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[EXPORT_PROMOS_HELP_URL]');

// EXPORT_PROMOS_NOTICE
$obTabControl->BeginCustomField($strPluginParams.'[EXPORT_PROMOS_NOTICE]', $obPlugin::getMessage('EXPORT_PROMOS_NOTICE'));
?>
<tr id="row_YANDEX_MARKET_EXPORT_PROMOS_NOTICE">
	<td width="40%" valign="top"></td>
	<td width="60%">
		<?=Helper::showNote($obPlugin::getMessage('EXPORT_PROMOS_NOTICE'), true);?>
	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[EXPORT_PROMOS_NOTICE]');
