<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

$strIBlockType = $arPost['iblock_type']=='offers' ? 'offers' : 'main';

$strCurrentField = $arPost['current_field'];

# Get all fields for main iblock
#$arAvailableElementFields = ProfileIBlock::getAvailableElementFields($intIBlockID);
$arAvailableElementFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$intIBlockID]);
foreach($arAvailableElementFields as $strGroup => $arGroup){
	if(is_array($arGroup['ITEMS'])) {
		foreach($arGroup['ITEMS'] as $key => $arItem){
			if($arItem['FILTRABLE']===false){
				unset($arAvailableElementFields[$strGroup]['ITEMS'][$key]);
			}
		}
	}
	if(empty($arGroup['ITEMS'])){
		unset($arAvailableElementFields[$strGroup]);
	}
}

# Get all fields for offers iblock
if($intIBlockOffersID){
	#$arAvailableOfferFields = ProfileIBlock::getAvailableElementFields($intIBlockOffersID);
	$arAvailableOfferFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$intIBlockOffersID]);
	foreach($arAvailableOfferFields as $strGroup => $arGroup){
		if(is_array($arGroup['ITEMS'])) {
			foreach($arGroup['ITEMS'] as $key => $arItem){
				if($arItem['FILTRABLE']===false){
					unset($arAvailableOfferFields[$strGroup]['ITEMS'][$key]);
				}
			}
		}
		if(empty($arGroup['ITEMS'])){
			unset($arAvailableOfferFields[$strGroup]);
		}
	}
}



?>

<input type="hidden" data-role="allow-save" />
<?if(!$intIBlockOffersID):?>
	<input type="hidden" data-role="entity-select-type-hidden" value="main" />
<?endif?>
<table class="acrit-exp-field-select-table">
	<tbody>
		<tr>
			<?if($intIBlockOffersID):?>
				<td>
					<select class="acrit-exp-field-select-type" data-role="entity-select-type">
						<option value="main"<?if($strIBlockType=='main'):?> selected="selected"<?endif?>>
							<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_TYPE_MAIN');?>
						</option>
						<option value="offers"<?if($strIBlockType=='offers'):?> selected="selected"<?endif?>>
							<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_TYPE_OFFERS');?>
						</option>
					</select>
				</td>
			<?endif?>
			<td>
				<input type="text" value="" class="acrit-exp-field-select-text" data-role="entity-select-search" placeholder="<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_TEXT_PLACEHOLDER');?>"/>
			</td>
		</tr>
		<tr>
			<td<?if($intIBlockOffersID):?> colspan="2"<?endif?>>
				<select class="acrit-exp-field-select-list" size="10" data-role="entity-select-item" data-type="main">
					<option value="" disabled="disabled" data-role="entity-select-item-not-found" style="display:none">
						<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_NOT_FOUND');?>
					</option>
					<?foreach($arAvailableElementFields as $strGroup => $arGroup):?>
						<?if(is_array($arGroup['ITEMS']) && !empty($arGroup['ITEMS'])):?>
							<optgroup label="<?=$arGroup['NAME'];?>" data-code="<?=$strGroup;?>">
								<?foreach($arGroup['ITEMS'] as $strItem => $arItem):?>
									<?
									$strItemCode = strlen($arGroup['PREFIX']) ? $arGroup['PREFIX'].$strItem : $strItem;
									if(in_array($strItemCode, ['DETAIL_PAGE_URL'])){
										continue;
									}
									$arItem['NAME_PREFIX'] = $arGroup['NAME_PREFIX'];
									$arLogic = Filter::getLogicAll($arItem['TYPE'], $arItem['USER_TYPE']);
									$arLogic = array_merge(array(
										'CODE' => key($arLogic),
									), reset($arLogic));
									?>
									<option value="<?=$strItemCode;?>" data-name="<?=htmlspecialcharsbx($arItem['NAME']);?>" 
										<?if($strItemCode==$strCurrentField):?> selected="selected"<?endif?>
										data-logic-code="<?=$arLogic['CODE'];?>"
										data-logic-name="<?=$arLogic['NAME'];?>"
										data-logic-hide-value="<?=($arLogic['HIDE_VALUE'] ? 'Y' : 'N');?>"
									><?
										#print ProfileIBlock::displayAvailableItemName($arItem);
										print Helper::call($strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arItem]);
									?></option>
								<?endforeach?>
							</optgroup>
						<?endif?>
					<?endforeach?>
				</select>
				<?if($intIBlockOffersID):?>
					<select class="acrit-exp-field-select-list" size="10" data-role="entity-select-item" data-type="offers" style="display:none">
						<option value="" disabled="disabled" data-role="entity-select-item-not-found" style="display:none">
							<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_NOT_FOUND');?>
						</option>
						<?foreach($arAvailableOfferFields as $strGroup => $arGroup):?>
							<?if(is_array($arGroup['ITEMS']) && !empty($arGroup['ITEMS'])):?>
								<optgroup label="<?=$arGroup['NAME'];?>" data-code="<?=$strGroup;?>">
									<?foreach($arGroup['ITEMS'] as $strItem => $arItem):?>
										<?
										$strItemCode = strlen($arGroup['PREFIX']) ? $arGroup['PREFIX'].$strItem : $strItem;
										$arItem['NAME_PREFIX'] = $arGroup['NAME_PREFIX'];
										$arLogic = Filter::getLogicAll($arItem['TYPE'], $arItem['USER_TYPE']);
										$arLogic = array_merge(array(
											'CODE' => key($arLogic),
										), reset($arLogic));
										?>
										<option value="<?=$strItemCode;?>" data-name="<?=htmlspecialcharsbx($arItem['NAME']);?>" 
											<?if($strItemCode==$strCurrentField):?> selected="selected"<?endif?>
											data-logic-code="<?=$arLogic['CODE'];?>"
											data-logic-name="<?=$arLogic['NAME'];?>"
											data-logic-hide-value="<?=($arLogic['HIDE_VALUE'] ? 'Y' : 'N');?>"
										><?
											#print ProfileIBlock::displayAvailableItemName($arItem, false, true);
											print Helper::call($strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arItem, false, true]);
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
