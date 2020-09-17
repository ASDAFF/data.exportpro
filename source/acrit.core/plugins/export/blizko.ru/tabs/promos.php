<?

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Acrit\Core\Helper,
		\Acrit\Core\Export\Plugins\YandexMarket;

Loc::loadMessages(__FILE__);

// Это использовать для всех дополнительных параметров в плагинах, чтобы параметры профиля не пересекались
$strPluginParams = $obPlugin->getPluginParamsInputName();
$arPluginParams = $obPlugin->getPluginParams();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// EXPORT PROMOCODES
$obTabControl->BeginCustomField($strPluginParams . '[EXPORT_PROMOCODES]', $obPlugin::getMessage('EXPORT_PROMOCODES'));
?>
<tr>
	<td colspan="2" valign="top" style="text-align: center;">
		<?= $obPlugin::getMessage('EXPORT') ?>
	</td>
</tr>
<tr id="row_YANDEX_MARKET_EXPORT_PROMOCODES">
	<td width="40%" valign="top">
		<?= Helper::showHint($obPlugin::getMessage('EXPORT_PROMOCODES_DESC')); ?>
		<?= $obTabControl->GetCustomLabelHTML() ?>:
	</td>
	<td width="60%">
		<label>
			<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_PROMOCODES]" value="Y"
						 <? if ($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES'] == 'Y'): ?>checked="checked"<? endif ?>/>
		</label>
	</td>
</tr><? $display = ($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES'] == 'Y') ? 'block' : 'none'; ?>
<tr id="row_YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS" style="display:<?= $display ?>;">
	<td width="40%" valign="top">
		<?= Helper::showHint($obPlugin::getMessage('EXPORT_PROMOCODES_RULES_DESC')); ?>
		<?= YandexMarket::getMessage('EXPORT_PROMOCODES_RULES'); ?>
	</td>
	<td width="60%">
		<label>
			<? /* ?><input class="YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS" type="text" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS]" value="" /><?/* */ ?>
			<select multiple="true" type="text" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS][]" >
				<?
				\Bitrix\Main\Loader::includeModule('sale');

				$discountIterator = \Bitrix\Sale\Internals\DiscountTable::getList(array(
										'select' => array('ID', 'NAME'),
				));
				while ($discount = $discountIterator->fetch()) {
					$selected = (in_array($discount['ID'], $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS'])) ? 'selected="selected"' : '';
					?><option <?= $selected ?> value="<?= $discount['ID'] ?>"><?= $discount['NAME'] ?></option><?
				}
				?>

			</select>
		</label>
	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams . '[EXPORT_PROMOCODES]');
// EXPORT SPECIAL PRICE
$obTabControl->BeginCustomField($strPluginParams . '[EXPORT_SPECIAL_PRICE]', $obPlugin::getMessage('EXPORT_SPECIAL_PRICE'));
?>
<tr id="row_YANDEX_MARKET_EXPORT_SPECIAL_PRICE">
	<td width="40%" valign="top">
		<?= Helper::showHint($obPlugin::getMessage('EXPORT_SPECIAL_PRICE_DESC')); ?>
		<?= $obTabControl->GetCustomLabelHTML() ?>:
	</td>
	<td width="60%">
		<label>
			<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_SPECIAL_PRICE]" value="Y"
						 <? if ($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE'] == 'Y'): ?>checked="checked"<? endif ?>/>
		</label>
	</td>
</tr>
<? $display = ($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE'] == 'Y') ? 'block' : 'none'; ?>
<tr id="row_YANDEX_MARKET_EXPORT_SPECIAL_PRICE_OPTIONS"  style="display: <?= $display ?>;">
	<td width="100%" colspan="2">
		<table style="margin: 0 auto;">
			<tr >
				<td width="40%" valign="top">
					<?= Helper::showHint($obPlugin::getMessage('EXPORT_ACTION_ID_DESC')); ?>
					<?= $obPlugin::getMessage('EXPORT_ACTION_ID') ?>
				</td>
				<td width="60%">
					<label>
						<input type="text" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_SPECIAL_PRICE_ACTION_ID]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_ACTION_ID'] ?>" />
					</label>
				</td>
			</tr>
			<tr >
				<td width="40%" valign="top">
					<?= $obPlugin::getMessage('EXPORT_DATE_START') ?>
				</td>
				<td width="60%">
					<label>
						<input type="date" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_SPECIAL_PRICE_DATE_START]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_DATE_START'] ?>" />
					</label>
				</td>
			</tr>
			<tr >
				<td width="40%" valign="top">
					<?= $obPlugin::getMessage('EXPORT_DATE_END') ?>
				</td>
				<td width="60%">
					<label>
						<input type="date" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_SPECIAL_PRICE_DATE_END]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_DATE_END'] ?>" />
					</label>
				</td>
			</tr>
			<tr >
				<td width="40%" valign="top">
					<?= $obPlugin::getMessage('EXPORT_DESCRIPTION') ?>
				</td>
				<td width="60%">
					<label>
						<input type="text" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_SPECIAL_PRICE_DESCRIPTION]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_DESCRIPTION'] ?>" />
					</label>
				</td>
			</tr>
			<tr >
				<td width="40%" valign="top">
					<?= $obPlugin::getMessage('EXPORT_URL') ?>
				</td>
				<td width="60%">
					<label>
						<input type="text" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_SPECIAL_PRICE_URL]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_URL'] ?>" />
					</label>
				</td>
			</tr>

	</td></tr></table>	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams . '[EXPORT_SPECIAL_PRICE]');
// EXPORT N_PLUS_M
$obTabControl->BeginCustomField($strPluginParams . '[EXPORT_N_PLUS_M]', $obPlugin::getMessage('EXPORT_N_PLUS_M'));
?>
<tr id="row_YANDEX_MARKET_EXPORT_N_PLUS_M">
	<td width="40%" valign="top">
		<?= Helper::showHint($obPlugin::getMessage('EXPORT_N_PLUS_M_DESC')); ?>
		<?= $obTabControl->GetCustomLabelHTML() ?>:
	</td>
	<td width="60%">
		<label>
			<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_N_PLUS_M]" value="Y"
						 <? if ($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M'] == 'Y'): ?>checked="checked"<? endif ?>/>
		</label>
	</td>
</tr>
<? $display = ($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M'] == 'Y') ? 'block' : 'none'; ?>
<tr id="row_YANDEX_MARKET_EXPORT_N_PLUS_M_OPTIONS"  style="display: <?= $display ?>;">
	<td width="100%" colspan="2">
		<table style="margin: 0 auto;">
			<tr >
				<td width="40%" valign="top">
					<?= Helper::showHint($obPlugin::getMessage('EXPORT_ACTION_ID_DESC')); ?>
					<?= $obPlugin::getMessage('EXPORT_ACTION_ID') ?>
				</td>
				<td width="60%">
					<label>
						<input type="text" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_N_PLUS_M_ACTION_ID]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_ACTION_ID'] ?>" />
					</label>
				</td>
			</tr>
			<tr >
				<td width="40%" valign="top">
					<?= $obPlugin::getMessage('EXPORT_DATE_START') ?>
				</td>
				<td width="60%">
					<label>
						<input type="date" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_N_PLUS_M_DATE_START]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_DATE_START'] ?>" />
					</label>
				</td>
			</tr>
			<tr >
				<td width="40%" valign="top">
					<?= $obPlugin::getMessage('EXPORT_DATE_END') ?>
				</td>
				<td width="60%">
					<label>
						<input type="date" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_N_PLUS_M_DATE_END]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_DATE_END'] ?>" />
					</label>
				</td>
			</tr>
			<tr >
				<td width="40%" valign="top">
					<?= $obPlugin::getMessage('EXPORT_DESCRIPTION') ?>
				</td>
				<td width="60%">
					<label>
						<input type="text" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_N_PLUS_M_DESCRIPTION]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_DESCRIPTION'] ?>" />
					</label>
				</td>
			</tr>
			<tr >
				<td width="40%" valign="top">
					<?= $obPlugin::getMessage('EXPORT_URL') ?>
				</td>
				<td width="60%">
					<label>
						<input type="text" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_N_PLUS_M_URL]" value="<?= $arProfile['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_URL'] ?>" />
					</label>
				</td>
			</tr>

	</td></tr></table>	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams . '[EXPORT_N_PLUS_M]');
// EXPORT_GIFTS
$obTabControl->BeginCustomField($strPluginParams . '[EXPORT_GIFTS]', $obPlugin::getMessage('EXPORT_GIFTS'));
?>

<tr id="row_YANDEX_MARKET_EXPORT_GIFTS">
	<td width="40%" valign="top">
		<?= Helper::showHint($obPlugin::getMessage('EXPORT_GIFTS_DESC')); ?>
		<?= $obTabControl->GetCustomLabelHTML() ?>:
	</td>
	<td width="60%">
		<label>
			<input type="checkbox" name="PROFILE[PARAMS][YANDEX_MARKET_EXPORT_GIFTS]" value="Y"
						 <? if ($arProfile['PARAMS']['YANDEX_MARKET_EXPORT_GIFTS'] == 'Y'): ?>checked="checked"<? endif ?>/>
		</label>
	</td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams . '[EXPORT_GIFTS]');
