<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\DiscountRecalculation;
	
Loc::loadMessages(__FILE__);

# Get iblocks
$arIBlocksByType = Helper::getIBlockList(true, false, true);

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

# Show just catalogs
$bCatalogModule = \Bitrix\Main\Loader::includeModule('catalog');
if(!isset($arProfile['PARAMS']['SHOW_JUST_CATALOGS']) && $bCatalogModule){
	$arProfile['PARAMS']['SHOW_JUST_CATALOGS'] = 'Y';
}
$bShowJustCatalogs = $arProfile['PARAMS']['SHOW_JUST_CATALOGS']=='Y';
if(isset($arPost['show_just_catalogs'])){
	$bShowJustCatalogs = $arPost['show_just_catalogs']=='Y';
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

#
$arProfileIBlocks = array();
$arQuery = [
	'filter' => array(
		'PROFILE_ID' => $intProfileID,
	),
	'select' => array(
		'IBLOCK_ID',
	),
];
#$resProfileIBlocks = ProfileIBlock::getList($arQuery);
$resProfileIBlocks = Helper::call($strModuleId, 'ProfileIBlock', 'getList', [$arQuery]);
while($arProfileIBlock = $resProfileIBlocks->fetch()){
	$arProfileIBlocks[] = $arProfileIBlock['IBLOCK_ID'];
}

# Get generated count info
$arCountInfo = array();
$arQuery = [
	'filter' => array(
		'PROFILE_ID' => $intProfileID,
	),
	'select' => array(
		'IBLOCK_ID',
		'CNT',
	),
	'group' => array(
		'IBLOCK_ID',
	),
	'runtime' => array(
		new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'),
	)
];
#$resItems = ExportData::getList($arQuery);
$resItems = Helper::call($strModuleId, 'ExportData', 'getList', [$arQuery]);
while($arItem = $resItems->fetch()){
	$arCountInfo[$arItem['IBLOCK_ID']] = $arItem['CNT'];
};

?>

<div class="acrit-exp-table-iblocks-preview">
	<table class="adm-list-table">
		<thead>
			<tr class="adm-list-table-header">
				<td class="adm-list-table-cell" style="width:14px;" rowspan="2">
					<div class="adm-list-table-cell-inner"></div>
				</td>
				<td class="adm-list-table-cell" style="width:14px;" rowspan="2">
					<div class="adm-list-table-cell-inner"></div>
				</td>
				<td class="adm-list-table-cell" rowspan="2">
					<div class="adm-list-table-cell-inner" style="text-align:left">
						<?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_HEADER_NAME');?>
					</div>
				</td>
				<td class="adm-list-table-cell" style="width:40px;" colspan="2">
					<div class="adm-list-table-cell-inner">
						<?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_HEADER_ELEMENTS');?>
					</div>
				</td>
				<td class="adm-list-table-cell" style="width:40px;" rowspan="2">
					<div class="adm-list-table-cell-inner">
						<?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_HEADER_GENERATED');?>
					</div>
				</td>
				<td class="adm-list-table-cell" style="width:40px;" rowspan="2">
					<div class="adm-list-table-cell-inner">
						<?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_HEADER_OFFERS');?>
					</div>
				</td>
			</tr>
			<tr class="adm-list-table-header">
				<td class="adm-list-table-cell" style="width:50px">
					<div class="adm-list-table-cell-inner">
						<?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_HEADER_ELEMENTS_COUNT');?>
					</div>
				</td>
				<td class="adm-list-table-cell" style="width:140px">
					<div class="adm-list-table-cell-inner">
						<?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_HEADER_ELEMENTS_SUITABLE');?>
					</div>
				</td>
			</tr>
		</thead>
		<tbody>
			<?foreach($arIBlocksByType as $IBlockTypeID => $arIBlockType):?>
				<?if(!empty($arIBlockType['ITEMS'])):?>
					<tr class="heading">
						<td colspan="7"><?=$arIBlockType['NAME'];?></td>
					</tr>
					<?foreach($arIBlockType['ITEMS'] as $arIBlock):?>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-center">
								<?if(in_array($arIBlock['ID'], $arProfileIBlocks)):?>
									<?
									$arMenu = array();
									/*
									$arMenu[] = array(
										'TEXT'	=> Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_COPY_SETTINGS_TO'),
										'ONCLICK' => 'alert(1);',
										'GLOBAL_ICON' => 'adm-menu-edit',
									);
									*/
									$arMenu[] = array(
										'TEXT'	=> Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_CLEAR_SETTINGS'),
										'ONCLICK' => 'acritExpClearIBlockData('.$arIBlock['ID'].', "'.$arIBlock['NAME'].'", false, function(){AcritExpPopupIBlocksPreview.LoadContent();});',
										'GLOBAL_ICON' => 'adm-menu-delete',
									);
									?>
									<div class="adm-list-table-popup adm-list-table-popup-active"
										 title="<?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_ACTIONS');?>" 
										 onclick="<?=Helper::showMenuOnClick($arMenu);?>"></div>
								<?endif?>
							</td>
							<td class="adm-list-table-cell align-center">
								<?if(in_array($arIBlock['ID'], $arProfileIBlocks)):?>
									<img src="/bitrix/themes/.default/images/lamp/green.gif" alt="" width="14" height="14" />
								<?else:?>
									<img src="/bitrix/themes/.default/images/lamp/grey.gif" alt="" width="14" height="14" />
								<?endif?>
							</td>
							<td class="adm-list-table-cell">
								<?
								$strListMode = $arIBlock['LIST_MODE'];
								$arAvailableListModes = array('S', 'C');
								if(!in_array($strListMode, $arAvailableListModes)){
									$strListMode = \Bitrix\Main\Config\Option::get('iblock', 'combined_list_mode') == 'Y' ? 'C' : 'S';
								}
								if(!in_array($strListMode, $arAvailableListModes)){
									$strListMode = reset($arAvailableListModes);
								}
								?>
								<?if($strListMode=='S'):?>
									<a href="/bitrix/admin/iblock_section_admin.php?IBLOCK_ID=<?=$arIBlock['ID'];?>&type=<?=$arIBlock['IBLOCK_TYPE_ID'];?>&lang=<?=LANGUAGE_ID;?>&find_section_section=0" target="_blank"><?=$arIBlock['NAME'];?></a>
								<?else:?>
									<a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=<?=$arIBlock['ID'];?>&type=<?=$arIBlock['IBLOCK_TYPE_ID'];?>&lang=<?=LANGUAGE_ID;?>&find_section_section=0" target="_blank"><?=$arIBlock['NAME'];?></a>
								<?endif?>
								[<a href="/bitrix/admin/iblock_edit.php?type=<?=$arIBlock['IBLOCK_TYPE_ID'];?>&lang=<?=LANGUAGE_ID;?>&ID=<?=$arIBlock['ID'];?>&admin=Y" target="_blank"><?=$arIBlock['ID'];?></a>]
							</td>
							<td class="adm-list-table-cell align-right">
								<?=$arIBlock['ELEMENT_CNT'];?>
							</td>
							<td class="adm-list-table-cell align-right">
								<?if(in_array($arIBlock['ID'], $arProfileIBlocks)):?>
									<?
									#$arFilter = Profile::getFilter($intProfileID, $arIBlock['ID']);
									$arFilter = Helper::call($strModuleId, 'Profile', 'getFilter', [$intProfileID, $arIBlock['ID']]);
									$intCountElements = \CIBlockElement::getList(array(), $arFilter, array());
									?>
									<?=$intCountElements;?>
									<?if($intCountElements):?>
										<?
										$resExample = \CIBlockElement::getList(array('RAND'=>'ASC'), $arFilter, false, array('nTopCount'=>'1'), array('ID'));
										$arExample = $resExample->GetNext(false,false);
										?>
										<?if(is_array($arExample)):?>
											(<a href="<?=Exporter::getInstance($strModuleId)->getElementPreviewUrl($arExample['ID'], $intProfileID);?>" target="_blank"><?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_EXAMPLE');?></a>)
										<?endif?>
									<?endif?>
								<?endif?>
							</td>
							<td class="adm-list-table-cell align-right">
								<?=IntVal($arCountInfo[$arIBlock['ID']]);?>
							</td>
							<td class="adm-list-table-cell align-center">
								<?if($arIBlock['CATALOG']['OFFERS_IBLOCK_ID']):?>
									<?=Loc::getMessage('MAIN_YES');?>
								<?else:?>
									<?=Loc::getMessage('MAIN_NO');?>
								<?endif?>
							</td>
						</tr>
					<?endforeach?>
				<?endif?>
			<?endforeach?>
		</tbody>
	</table>
	<hr/>
	<p><?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_NOTICE_OFFERS');?></p>
	<?if(DiscountRecalculation::isEnabled()):?>
		<p><?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_NOTICE_DISCOUNTS');?></p>
	<?endif?>
</div>
