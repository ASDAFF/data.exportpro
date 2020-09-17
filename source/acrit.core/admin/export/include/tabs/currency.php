<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\CurrencyConverter\Base as CurrencyConverterBase;

Loc::loadMessages(__FILE__);

$arCurrencyAll = Helper::getCurrencyList();
$arPluginCurrency = array();
if(is_object($obPlugin)) {
	$arPluginCurrency = $obPlugin->getSupportedCurrencies();
}
$arPriceAll = Helper::getPriceList();
$arConverters = CurrencyConverterBase::getConverterList();

$arProfileParams = $arProfile['PARAMS'];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Convert to currency
$obTabControl->BeginCustomField('PROFILE[PARAMS][CURRENCY][TARGET_CURRENCY]', Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_TARGET_CURRENCY'));
?>
	<tr id="tr_CURRENCY_TARGET_CURRENCY">
		<td width="40%" class="adm-detail-content-cell-l">
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_TARGET_CURRENCY_HINT'));?>
			<label for="field_CURRENCY_TARGET_CURRENCY"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td width="60%" class="adm-detail-content-cell-r acrit-exp-select-wrapper">
			<select name="PROFILE[PARAMS][CURRENCY][TARGET_CURRENCY]" id="field_CURRENCY_TARGET_CURRENCY">
				<option value="">
					<?=Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_CURRENCY_CONVERT_NO');?>
				</option>
				<?foreach($arCurrencyAll as $strCurrency => $arCurrency):?>
					<?if(in_array($strCurrency, $arPluginCurrency)):?>
						<option value="<?=$strCurrency;?>"
							<?if($arProfileParams['CURRENCY']['TARGET_CURRENCY']==$strCurrency):?> selected="selected"<?endif?>
						>
							[<?=$strCurrency;?>] <?=$arCurrency['FULL_NAME'];?>
						</option>
					<?endif?>
				<?endforeach?>
			</select>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[PARAMS][CURRENCY][TARGET_CURRENCY]');

// Rates source
$obTabControl->BeginCustomField('PROFILE[PARAMS][CURRENCY][RATES_SOURCE]', Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_CURRENCY_RATES_SOURCE'));
?>
	<tr id="tr_CURRENCY_RATES_SOURCE">
		<td>
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_CURRENCY_RATES_SOURCE_HINT'));?>
			<label for="field_CURRENCY_RATES_SOURCE"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td class="acrit-exp-select-wrapper">
			<select name="PROFILE[PARAMS][CURRENCY][RATES_SOURCE]" id="field_CURRENCY_RATES_SOURCE">
				<?foreach($arConverters as $strConverter => $arConverter):?>
					<option value="<?=$strConverter;?>"<?if($arProfileParams['CURRENCY']['RATES_SOURCE']==$strConverter):?> selected="selected"<?endif?>>
						<?=$arConverter['NAME'];?>
					</option>
				<?endforeach?>
			</select>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[PARAMS][CURRENCY][RATES_SOURCE]');

/*
// Price correct
$obTabControl->BeginCustomField('PROFILE[PARAMS][PRICE_CORRECT]', Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_PRICE_CORRECT'));
?>
	<tr class="heading"><td colspan="2"><?=$obTabControl->GetCustomLabelHTML();?></td></tr>
	<?foreach($arPriceAll as $arPrice):?>
		<?
		$arPriceCorrect = $arProfile['PARAMS']['PRICE_CORRECT'][$arPrice['ID']];
		if(!is_array($arPriceCorrect)){
			$arPriceCorrect = array();
		}
		if(!is_array($arPriceCorrect['VALUE']) || !is_array($arPriceCorrect['FROM']) || !is_array($arPriceCorrect['TO'])){
			$arPriceCorrect['VALUE'] = array();
			$arPriceCorrect['FROM'] = array();
			$arPriceCorrect['TO'] = array();
		}
		?>
		<tr>
			<td width="40%" style="padding-top:12px;vertical-align:top;">
				<?=$arPrice['NAME_LANG'];?> [<?=$arPrice['ID'];?>, <?=$arPrice['NAME'];?>]:
			</td>
			<td width="60%">
				<table class="acrit-exp-table-price-correct">
					<tbody>
						<?$bFirst=true;?>
						<?foreach(array_merge(array(''),$arPriceCorrect['VALUE']) as $key => $strValue):?>
							<?
							$strValue = $arPriceCorrect['VALUE'][$key];
							$strFrom = $arPriceCorrect['FROM'][$key];
							$strTo = $arPriceCorrect['TO'][$key];
							if(!$bFirst && !strlen($strValue) && !strlen($strFrom) && !strlen($strTo)){
								continue;
							}
							?>
							<tr>
								<td class="acrit-exp-table-price-correct-value">
									<input type="text" size="6" placeholder="+10%" value="<?=$strValue;?>"
										name="PROFILE[PARAMS][PRICE_CORRECT][<?=$arPrice['ID'];?>][VALUE][]" />
								</td>
								<td class="acrit-exp-table-price-correct-text-2">
									<?=Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_PRICE_CORRECT_TEXT_2');?>
								</td>
								<td class="acrit-exp-table-price-correct-from">
									<input type="text" size="6" placeholder="0" value="<?=$strFrom;?>"
										name="PROFILE[PARAMS][PRICE_CORRECT][<?=$arPrice['ID'];?>][FROM][]" />
								</td>
								<td class="acrit-exp-table-price-correct-text-3">
									<?=Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_PRICE_CORRECT_TEXT_3');?>
								</td>
								<td class="acrit-exp-table-price-correct-to">
									<input type="text" size="6" placeholder="1000" value="<?=$strTo;?>"
										name="PROFILE[PARAMS][PRICE_CORRECT][<?=$arPrice['ID'];?>][TO][]" />
								</td>
								<td class="acrit-exp-table-price-correct-add">
									<input type="button" value="+" title="<?=Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_PRICE_CORRECT_ADD');?>"
										data-role="price-correct-add" />
								</td>
								<td class="acrit-exp-table-price-correct-delete">
									<a href="#" title="<?=Loc::getMessage('ACRIT_EXP_TAB_CURRENCIES_PRICE_CORRECT_DELETE');?>"
										data-role="price-correct-delete"
									>&times;</a>
								</td>
							</tr>
							<?
							$bFirst = false;
							?>
						<?endforeach?>
					</tbody>
				</table>
			</td>
		</tr>
	<?endforeach?>
<?
$obTabControl->EndCustomField('PROFILE[PARAMS][PRICE_CORRECT]');
*/
?>