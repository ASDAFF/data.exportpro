<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

#$arAvailableFields = ProfileIBlock::getAvailableElementFields($intIBlockID);
$arAvailableFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$intIBlockID]);
if($intIBlockParentID) {
	#$arAvailableParentFields = ProfileIBlock::getAvailableElementFields($intIBlockParentID);
	$arAvailableParentFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$intIBlockParentID]);
}
if($intIBlockOffersID) {
	#$arAvailableOfferFields = ProfileIBlock::getAvailableElementFields($intIBlockOffersID);
	$arAvailableOfferFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$intIBlockOffersID]);
}

?>

<select data-role="select-additional-fields" size="14" multiple="multiple" style="height:100%; width:100%;">
	<optgroup label="<?=Loc::getMessage('ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_IBLOCK_CURRENT');?>">
		<?foreach($arAvailableFields['properties']['ITEMS'] as $arItem):?>
			<?
			$arMore = array(
				$arItem['ID'],
				$arItem['CODE'],
				$arItem['DATA']['PROPERTY_TYPE'].($arItem['DATA']['USER_TYPE']?':'.$arItem['DATA']['USER_TYPE']:''),
			);
			?>
			<option value="<?=$arItem['ID'];?>"><?=$arItem['NAME'];?> [<?=implode(', ', $arMore);?>]</option>
		<?endforeach?>
	</optgroup>
	<?if($intIBlockParentID):?>
		<optgroup label="<?=Loc::getMessage('ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_IBLOCK_PARENT');?>">
			<?foreach($arAvailableParentFields['properties']['ITEMS'] as $arItem):?>
				<?
				$arMore = array(
					$arItem['ID'],
					$arItem['CODE'],
					$arItem['DATA']['PROPERTY_TYPE'].($arItem['DATA']['USER_TYPE']?':'.$arItem['DATA']['USER_TYPE']:''),
				);
				?>
				<option value="PARENT.<?=$arItem['ID'];?>"><?=$arItem['NAME'];?> [<?=implode(', ', $arMore);?>]</option>
			<?endforeach?>
		</optgroup>
	<?endif?>
	<?if($intIBlockOffersID):?>
		<optgroup label="<?=Loc::getMessage('ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_IBLOCK_OFFERS');?>">
			<?foreach($arAvailableOfferFields['properties']['ITEMS'] as $arItem):?>
				<?
				$arMore = array(
					$arItem['ID'],
					$arItem['CODE'],
					$arItem['DATA']['PROPERTY_TYPE'].($arItem['DATA']['USER_TYPE']?':'.$arItem['DATA']['USER_TYPE']:''),
				);
				?>
				<option value="OFFER.<?=$arItem['ID'];?>"><?=$arItem['NAME'];?> [<?=implode(', ', $arMore);?>]</option>
			<?endforeach?>
		</optgroup>
	<?endif?>
</select>
