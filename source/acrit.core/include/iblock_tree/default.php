<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper;

# Get full iblocks tree
$arIBlocks = Helper::getIBlockList(true, false, false, false);

# Parameters
$strCode = $arVariables['CODE'];
$bMultiple = !!$arVariables['MULTIPLE'];
$arCurrentIBlocks = array_filter(explode(',', $arVariables['VALUE']));
$intSize = is_numeric($arVariables['SIZE']) && $arVariables['SIZE'] > 0 ? $arVariables['SIZE'] : ($bMultiple ? 8 : 1);
$strMinWidth = $arVariables['MIN_WIDTH'];
if(is_numeric($strMinWidth)){
	$strMinWidth .= 'px';
}
$bJustCatalogs = !!$arVariables['JUST_CATALOGS'];
$bHideEmpty = !$arVariables['SHOW_EMPTY'];

# Just catalogs
if($bJustCatalogs){
	if(\Bitrix\Main\Loader::includeModule('catalog')){
		$arCatalogIBlocksId = [];
		$resCatalogs = \CCatalog::GetList([], [], false, false, ['IBLOCK_ID']);
		while($arCatalog = $resCatalogs->getNext(false, false)){
			$arCatalogIBlocksId[] = $arCatalog['IBLOCK_ID'];
		}
		foreach($arIBlocks as $strIBlockType => $arIBlockType){
			foreach($arIBlockType['ITEMS'] as $key => $arIBlock){
				if(!in_array($arIBlock['ID'], $arCatalogIBlocksId)){
					unset($arIBlocks[$strIBlockType]['ITEMS'][$key]);
				}
			}
		}
	}
}

# Remove empty
if($bHideEmpty){
	foreach($arIBlocks as $strIBlockType => $arIBlockType){
		if(empty($arIBlockType['ITEMS'])){
			unset($arIBlocks[$strIBlockType]);
		}
	}
}

?>
<select name="<?=$strCode;?><?if($bMultiple):?>[]<?endif?>"<?if($bMultiple):?> multiple="multiple"<?endif?> size="<?=$intSize;?>"<?if(strlen($strMinWidth)):?> style="min-width:<?=$strMinWidth;?>;"<?endif?>>
	<?foreach($arIBlocks as $strIBlockType => $arIBlockType):?>
		<optgroup label="<?=$arIBlockType['NAME'];?> [<?=$strIBlockType;?>]">
			<?foreach($arIBlockType['ITEMS'] as $arIBlock):?>
				<option value="<?=$arIBlock['ID'];?>"<?if(in_array($arIBlock['ID'], $arCurrentIBlocks)):?> selected="selected"<?endif?>><?=$arIBlock['NAME'];?>, [<?=$arIBlock['ID'];?>, <?=$arIBlock['CODE'];?>]</option>
			<?endforeach?>
		</optgroup>
	<?endforeach?>
</select>