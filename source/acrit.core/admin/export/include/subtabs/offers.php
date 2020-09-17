<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\ValueBase,
	\Acrit\Core\Export\Filter;

Loc::loadMessages(__FILE__);

# Get prices for sort
$arPrices = Helper::getPriceList(array('SORT'=>'ASC','ID'=>'ASC'));

$arSort2Params = $arProfile['IBLOCKS'][$intIBlockID]['PARAMS']['OFFER_SORT2'];
if(!is_array($arSort2Params)){
	$arSort2Params = array();
}
if(empty($arSort2Params)){
	$arSort2Params = array(
		'FIELD' => array('ID'),
		'OTHER' => array(''),
		'ORDER' => array('DESC'),
	);
}

if(is_null($arIBlockOffersParams['FILTER'])){
	$arIBlockOffersParams['FILTER'] = Filter::getConditionsJson($strModuleId, $intIBlockOffersID, array(
		'FIELD' => 'ACTIVE',
		'LOGIC' => 'CHECKED',
	));
}

?>
<table class="adm-list-table">
	<tbody>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_OFFERS_MODE_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_MODE');?>:
			</td>
			<td width="60%">
				<?
				$arOptions = array(
					'only' => Loc::getMessage('ACRIT_EXP_TAB_OFFERS_MODE_ONLY'),
					'all' => Loc::getMessage('ACRIT_EXP_TAB_OFFERS_MODE_ALL'),
					'none' => Loc::getMessage('ACRIT_EXP_TAB_OFFERS_MODE_NONE'),
					'offers' => Loc::getMessage('ACRIT_EXP_TAB_OFFERS_MODE_OFFERS'),
				);
				$arOptions = array(
					'REFERENCE' => array_values($arOptions),
					'REFERENCE_ID' => array_keys($arOptions),
				);
				print SelectBoxFromArray('iblockparams['.$intIBlockID.'][OFFERS_MODE]', $arOptions, 
					$arIBlockParams['OFFERS_MODE'], '', '');
				?>
			</td>
		</tr>
		<tr class="heading"><td colspan="2"><?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_HEADER_FILTER');?></td></tr>
		<tr>
			<td colspan="2">
				<?
				$obFilter = new Filter($strModuleId, $intIBlockOffersID);
				$obFilter->setInputName('iblockfilter['.$intIBlockOffersID.']');
				$obFilter->setJson($arIBlockOffersParams['FILTER']);
				print $obFilter->show();
				$obFilter->buildFilter();
				unset($obFilter);
				?>
			</td>
		</tr>
		<tr class="heading"><td colspan="2"><?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_HEADER_SORT2');?></td></tr>
		<tr>
			<td width="40%" style="padding-top:11px;vertical-align:top;">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_OFFERS_HEADER_SORT2_FIELD_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_HEADER_SORT2_FIELD');?>:
			</td>
			<td width="60%">
				<div class="acrit-exp-offers-sort-block" data-role="offers-sort--block">
					<?foreach($arSort2Params['FIELD'] as $key => $strField):?>
						<?
						$strOther = $arSort2Params['OTHER'][$key];
						$strOrder = $arSort2Params['ORDER'][$key];
						?>
						<div class="acrit-exp-offers-sort-item" data-role="offers-sort--item">
							<select name="iblockparams[<?=$intIBlockID;?>][OFFER_SORT2][FIELD][]" data-role="offers-sort--field">
								<option value="-"<?if($strField=='-'):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_SORT2_OTHER');?></option>
								<option value="ID"<?if($strField=='ID'):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_SORT2_ID');?>
								</option>
								<option value="NAME"<?if($strField=='NAME'):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_SORT2_NAME');?>
								</option>
								<option value="ACTIVE_FROM"<?if($strField=='ACTIVE_FROM'):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_SORT2_ACTIVE_FROM');?>
								</option>
								<option value="SORT"<?if($strField=='SORT'):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_SORT2_SORT');?>
								</option>
								<option value="TIMESTAMP_X"<?if($strField=='TIMESTAMP_X'):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_SORT2_TIMESTAMP_X');?>
								</option>
								<?if(is_array($arPrices) && !empty($arPrices)):?>
									<optgroup label="<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_SORT2_PRICES');?>">
										<?foreach($arPrices as $arPrice):?>
											<?$strPrice = 'CATALOG_PRICE_'.$arPrice['ID'];?>
											<option value="<?=$strPrice?>"<?if($strField==$strPrice):?> selected="selected"<?endif?>>
												<?=$arPrice['NAME_LANG'];?> [<?=$arPrice['NAME'];?>, <?=$arPrice['ID'];?>]
											</option>
										<?endforeach?>
									</optgroup>
								<?endif?>
							</select>
							<input type="text" name="iblockparams[<?=$intIBlockID;?>][OFFER_SORT2][OTHER][]" data-role="offers-sort--other"
								value="<?=$strOther;?>" size="25" />
							<select name="iblockparams[<?=$intIBlockID;?>][OFFER_SORT2][ORDER][]" data-role="offers-sort--order">
								<option value="ASC"<?if($strOrder=='ASC'):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_ORDER_ASC');?>
								</option>
								<option value="DESC"<?if($strOrder=='DESC'):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_ORDER_DESC');?>
								</option>
							</select>
							<a href="#" class="acrit-exp-offers-sort-delete" data-role="offers-sort--delete">&times;</a>
						</div>
					<?endforeach?>
				</div>
				<input type="button" class="acrit-exp-offers-sort-add" data-role="offers-sort--add"
					value="<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_SORT2_ADD');?>" />
			</td>
		</tr>
		<tr class="heading"><td colspan="2"><?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_ADDITIONAL_PARAMETERS');?></td></tr>
		<tr>
			<td width="40%" style="padding-top:11px; vertical-align:top;">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_OFFERS_COUNT_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_TAB_OFFERS_COUNT');?>:
			</td>
			<td width="60%">
				<?
				$intOffersMaxCount = IntVal($arProfile['IBLOCKS'][$intIBlockID]['PARAMS']['OFFERS_MAX_COUNT']);
				if($intOffersMaxCount<=0){
					$intOffersMaxCount = '';
				}
				?>
				<input type="text" name="iblockparams[<?=$intIBlockID;?>][OFFERS_MAX_COUNT]"
					value="<?=$intOffersMaxCount;?>" size="10" />
			</td>
		</tr>
	</tbody>
</table>