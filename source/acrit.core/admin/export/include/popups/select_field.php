<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

# Fields for element of current iblock
#$arAvailableElementFields = ProfileIBlock::getAvailableElementFields($intIBlockID);
$arAvailableElementFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$intIBlockID]);

# Get current iblock info
$arCatalog = Helper::getCatalogArray($intIBlockID);

$bIBlockIsParent = $arCatalog['OFFERS_IBLOCK_ID']>0; // Это родительский инфоблок! У него есть предложения!
$bIBlockIsOffers = $arCatalog['PRODUCT_IBLOCK_ID']>0; // Это инфоблок предложений, у него есть родительский инфоблок!

# If this iblock has offers iblock
if($bIBlockIsParent) {
	#$arAvailableOfferFields = ProfileIBlock::getAvailableElementFields($arCatalog['OFFERS_IBLOCK_ID']);
	$arAvailableOfferFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$arCatalog['OFFERS_IBLOCK_ID']]);
}

# If this offers-iblock has parent iblock
if($bIBlockIsOffers){
	#$arAvailableParentFields = ProfileIBlock::getAvailableElementFields($arCatalog['PRODUCT_IBLOCK_ID']);
	$arAvailableParentFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$arCatalog['PRODUCT_IBLOCK_ID']]);
}

# Get current value
$strCurrentValue = $arGet['current_value'];
$bIsOfferValue = preg_match('#^OFFER\.#', $strCurrentValue);
$bIsParentValue = preg_match('#^PARENT\.#', $strCurrentValue);

# 
$bIBlockChangeAllowed = $arGet['allow_iblock_change']!='N';

$intColspan = 1;

?>
<?if($intIBlockID):?>
	<table class="acrit-exp-field-select-table">
		<tbody>
			<tr>
				<?if(($bIBlockIsParent || $bIBlockIsOffers) && $bIBlockChangeAllowed):?>
					<?$intColspan=2;?>
					<td>
						<select class="acrit-exp-field-select-type" data-role="field-select-type">
							<?if($bIBlockIsParent):?>
								<option value="element" data-type="element">
									<?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_TYPE_PRODUCT');?>
								</option>
								<option value="offer" data-type="offer"<?if($bIsOfferValue):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_TYPE_OFFER');?>
								</option>
							<?elseif($bIBlockIsOffers):?>
								<option value="element" data-type="element">
									<?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_TYPE_OFFER');?>
								</option>
								<option value="parent" data-type="parent"<?if($bIsParentValue):?> selected="selected"<?endif?>>
									<?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_TYPE_PRODUCT');?>
								</option>
							<?endif?>
						</select>
					</td>
				<?endif?>
				<td>
					<input type="text" value="" class="acrit-exp-field-select-text" data-role="field-select-search"
						placeholder="<?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_PLACEHOLDER');?>"/>
				</td>
			</tr>
			<tr>
				<td colspan="<?=$intColspan;?>">
					<select class="acrit-exp-field-select-list" size="10" data-role="field-select-list" data-type="element"<?if($bIsParentIBlock):?> style="display:none"<?endif?>>
						<option value="" disabled="disabled"><?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_NOT_FOUND');?></option>
						<?foreach($arAvailableElementFields as $strGroup => $arGroup):?>
							<?if(is_array($arGroup['ITEMS']) && !empty($arGroup['ITEMS'])):?>
								<optgroup label="<?=$arGroup['NAME'];?>" data-code="<?=$strGroup;?>">
									<?foreach($arGroup['ITEMS'] as $strItem => $arItem):?>
										<?
										$strItemCode = strlen($arGroup['PREFIX']) ? $arGroup['PREFIX'].$strItem : $strItem;
										$arItem['NAME_PREFIX'] = $arGroup['NAME_PREFIX'];
										?>
										<option value="<?=$strItemCode;?>" <?if($strItemCode==$strCurrentValue):?> selected="selected"<?endif?>><?
											#print ProfileIBlock::displayAvailableItemName($arItem);
											print Helper::call($strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arItem]);
										?></option>
									<?endforeach?>
								</optgroup>
							<?endif?>
						<?endforeach?>
					</select>
					<?if($bIBlockIsParent && $bIBlockChangeAllowed):?>
						<select class="acrit-exp-field-select-list" size="10" data-role="field-select-list" data-type="offer"<?if(!$bIsParentIBlock):?> style="display:none"<?endif?>>
							<option value="" disabled="disabled"><?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_NOT_FOUND');?></option>
							<?foreach($arAvailableOfferFields as $strGroup => $arGroup):?>
								<?if(is_array($arGroup['ITEMS']) && !empty($arGroup['ITEMS'])):?>
									<optgroup label="<?=$arGroup['NAME'];?>" data-code="<?=$strGroup;?>">
										<?foreach($arGroup['ITEMS'] as $strItem => $arItem):?>
											<?
											$strItemCode = strlen($arGroup['PREFIX']) ? $arGroup['PREFIX'].$strItem : $strItem;
											$strItemCode = 'OFFER.'.$strItemCode;
											$arItem['NAME_PREFIX'] = $arGroup['NAME_PREFIX'];
											?>
											<option value="<?=$strItemCode;?>" <?if($strItemCode==$strCurrentValue):?> selected="selected"<?endif?>><?
												#print ProfileIBlock::displayAvailableItemName($arItem, false, true);
												print Helper::call($strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arItem, false, true]);
											?></option>
										<?endforeach?>
									</optgroup>
								<?endif?>
							<?endforeach?>
						</select>
					<?endif?>
					<?if($bIBlockIsOffers && $bIBlockChangeAllowed):?>
						<select class="acrit-exp-field-select-list" size="10" data-role="field-select-list" data-type="parent"<?if(!$bIsParentIBlock):?> style="display:none"<?endif?>>
							<option value="" disabled="disabled"><?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_NOT_FOUND');?></option>
							<?foreach($arAvailableParentFields as $strGroup => $arGroup):?>
								<?if(is_array($arGroup['ITEMS']) && !empty($arGroup['ITEMS'])):?>
									<optgroup label="<?=$arGroup['NAME'];?>" data-code="<?=$strGroup;?>">
										<?foreach($arGroup['ITEMS'] as $strItem => $arItem):?>
											<?
											$strItemCode = strlen($arGroup['PREFIX']) ? $arGroup['PREFIX'].$strItem : $strItem;
											$strItemCode = 'PARENT.'.$strItemCode;
											$arItem['NAME_PREFIX'] = $arGroup['NAME_PREFIX'];
											?>
											<option value="<?=$strItemCode;?>" <?if($strItemCode==$strCurrentValue):?> selected="selected"<?endif?>><?
												#print ProfileIBlock::displayAvailableItemName($arItem, true, false);
												print Helper::call($strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arItem, true, false]);
											?></option>
										<?endforeach?>
									</optgroup>
								<?endif?>
							<?endforeach?>
						</select>
					<?endif?>
				</td>
			</tr>
		</tbody>
	</table>
<?else:?>
	<p><?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_NO_IBLOCK_SELECTED');?></p>
<?endif?>
