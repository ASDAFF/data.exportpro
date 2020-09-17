<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(\Bitrix\Main\Loader::getDocumentRoot().'/bitrix/modules/'.$strCoreId
	.'/admin/export/include/tabs/structure.php');

$bShowCount = true; # ToDo: вынести в опции

# Get iblocks
$arIBlocksByType = Helper::getIBlockList(true, false, $bShowCount);

# Remove iblock-offers
foreach($arIBlocksByType as $IBlockTypeID => $arIBlockType) {
	if(!empty($arIBlockType['ITEMS'])) {
		foreach($arIBlockType['ITEMS'] as $IBlockKey => $arIBlock) {
			if(is_array($arIBlock['CATALOG']) && $arIBlock['CATALOG']['PRODUCT_IBLOCK_ID']>0){
				unset($arIBlocksByType[$IBlockTypeID]['ITEMS'][$IBlockKey]);
			}
		}
	}
}

#
$arProfileIBlocks = array();
$arQuery = [
	'filter' => [
		'PROFILE_ID' => $intProfileID,
	],
	'select' => [
		'IBLOCK_ID',
	],
];
#$resProfileIBlocks = ProfileIBlock::getList($arQuery);
$resProfileIBlocks = Helper::call($strModuleId, 'ProfileIBlock', 'getList', [$arQuery]);
while($arProfileIBlock = $resProfileIBlocks->fetch()){
	$arProfileIBlocks[] = $arProfileIBlock['IBLOCK_ID'];
}

# Show just catalogs
$bCatalogModule = \Bitrix\Main\Loader::includeModule('catalog');
if(!isset($arProfile['PARAMS']['SHOW_JUST_CATALOGS']) && $bCatalogModule){
	$arProfile['PARAMS']['SHOW_JUST_CATALOGS'] = 'Y';
}
$bShowJustCatalogs = $arProfile['PARAMS']['SHOW_JUST_CATALOGS']=='Y';
if(isset($arGet['show_just_catalogs'])){
	$bShowJustCatalogs = $arGet['show_just_catalogs']=='Y';
}
if($bShowJustCatalogs){
	foreach($arIBlocksByType as $IBlockTypeID => $arIBlockType){
		foreach($arIBlockType['ITEMS'] as $key => $arIBlock){
			if(!isset($arIBlock['CATALOG'])){
				unset($arIBlockType['ITEMS'][$key]);
			}
		}
		if(!empty($arIBlockType['ITEMS'])){
			$arIBlocksByType[$IBlockTypeID] = $arIBlockType;
		}
		else {
			unset($arIBlocksByType[$IBlockTypeID]);
		}
	}
}

$arCustomizedIBlocksId = [];

?>
<option value=""><?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SELECT_EMPTY');?></option>
<?foreach($arIBlocksByType as $IBlockTypeID => $arIBlockType):?>
	<?if(!empty($arIBlockType['ITEMS'])):?>
		<optgroup label="<?=$arIBlockType['NAME'];?>">
			<?foreach($arIBlockType['ITEMS'] as $arIBlock):?>
				<option value="<?=$arIBlock['ID'];?>" data-name="<?=htmlspecialcharsbx($arIBlock['NAME']);?>"<?if($arProfile['LAST_IBLOCK_ID']==$arIBlock['ID']):?> selected="selected"<?endif?>><?
					if(in_array($arIBlock['ID'], $arProfileIBlocks)){
						print '* ';
						$arCustomizedIBlocksId[IntVal($arIBlock['ID'])] = [
							'NAME' => $arIBlock['NAME'],
							'TYPE' => $IBlockTypeID,
						];
					}
					print $arIBlock['NAME'];
					print ' ['.$arIBlock['ID'].']';
					if($bShowCount){
						print ' '.Loc::getMessage('ACRIT_EXP_TAB_STRUCTURE_IBLOCK_ELEMENT_COUNT', array('#COUNT#'=>IntVal($arIBlock['ELEMENT_CNT'])));
					}
					if($arIBlock['CATALOG']['OFFERS_IBLOCK_ID']){
						print Loc::getMessage('ACRIT_EXP_TAB_STRUCTURE_IBLOCK_WITH_OFFERS');
						print ' ['.$arIBlock['CATALOG']['OFFERS_IBLOCK_ID'].']';
					}
				?></option>
			<?endforeach?>
		</optgroup>
	<?endif?>
<?endforeach?>

<?
$strIBlocksMultipleNotice = '';
if(Helper::getOption($strModuleId, 'show_iblock_multiple_notice') == 'Y' && count($arCustomizedIBlocksId) > 1){
	$strIBlocksMultipleNotice = Helper::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SELECT_MULTIPLE_NOTICE', [
		'#COUNT#' => count($arCustomizedIBlocksId),
		'#LANGUAGE_ID#' => LANGUAGE_ID,
		'#MODULE_ID#' => $strModuleId,
	]);
	$strIBlocksMultipleNotice = Helper::showNote($strIBlocksMultipleNotice, true, false, true);
}
?>