<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

Loc::loadMessages(\Bitrix\Main\Loader::getDocumentRoot().'/bitrix/modules/'.$strModuleId
	.'/admin/new/include/tabs/structure.php');

if(!is_object($obPlugin)) {
	print Helper::showError(Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_PLUGIN_NOT_SELECTED'));
	return;
}

# Show IBlock header
$strIBlockName = '';
if($intIBlockID > 0){
	$resIBlock = \CIBlock::getList(array(), array('ID' => $intIBlockID));
	if($arIBlock = $resIBlock->getNext(false,false)){
		$strIBlockName = $arIBlock['NAME'];
	}
}
print Helper::showHeading(Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_TITLE', array(
	'#IBLOCK_ID#' => $intIBlockID,
	'#IBLOCK_NAME#' => $strIBlockName,
)));

#print ProfileIBlock::checkIBlock($intIBlockID);
print Helper::call($strModuleId, 'ProfileIBlock', 'checkIBlock', [$intIBlockID]);

# Get all value types
/*
$obFieldTmp = new Field([]);
$obFieldTmp->setModuleId($strModuleId);
$arValueTypes = $obFieldTmp->getValueTypes();
unset($obFieldTmp);
*/
$arValueTypes = Field::getValueTypesStatic($strModuleId);

# Get saved iblock data
#$arSavedIBlock = ProfileIBlock::loadIBlockData($intProfileID, $intIBlockID);
$arSavedIBlock = Helper::call($strModuleId, 'ProfileIBlock', 'loadIBlockData', [$intProfileID, $intIBlockID]);
$arIBlockParams = $arSavedIBlock['PARAMS'];
if($intIBlockOffersID){
	#$arIBlockOffersParams = ProfileIBlock::loadIBlockData($intProfileID, $intIBlockOffersID);
	$arIBlockOffersParams = Helper::call($strModuleId, 'ProfileIBlock', 'loadIBlockData', [$intProfileID, $intIBlockOffersID]);
}

?>

<div data-role="profile-iblock-settings">
	<input type="hidden" data-role="profile-iblock-id" value="<?=$intIBlockID?>" />
	<input type="hidden" data-role="profile-offers-iblock-id" value="<?=$intIBlockOffersID?>" />
	<div data-role="iblock-structure-settings-tabs" data-initial-tab="<?=$arProfile['LAST_SETTINGS_TAB'];?>">
		<?
			# Additional sub tabs
			$arAdditionalSubTabs = $obPlugin->getAdditionalSubTabs($intProfileID, $intIBlockID);
			if(!is_array($arAdditionalSubTabs)){
				$arAdditionalSubTabs = array();
			}
			foreach($arAdditionalSubTabs as $key => $arTab){
				if(is_numeric($key)){
					if(!strlen($arTab['DIV']) || !strlen($arTab['TAB']) || !strlen($arTab['FILE']) || !is_file($arTab['FILE'])){
						unset($arAdditionalSubTabs[$key]);
						continue;
					}
				}
				else{
					if(!strlen($arTab['FILE']) || !is_file($arTab['FILE'])){
						unset($arAdditionalSubTabs[$key]);
						continue;
					}
				}
				if(strpos($arTab['DIV'], 'subtab_') !== 0) {
					$arTab['DIV'] = 'subtab_'.$arTab['DIV'];
				}
				$arAdditionalSubTabs[$key]['ONSELECT'] = 'acritExpSettingsChangeTab(this);';
				$arAdditionalSubTabs[$key]['SORT'] = is_numeric($arTab['SORT']) ? $arTab['SORT'] : 100;
			}
			# Prepare additional tabs
			$arSubTabs = array();
			$arSubTabs['product'] = array(
				'DIV' => 'fields_product',
				'TAB' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_FIELDS_PRODUCT'),
				'TITLE' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_FIELDS_PRODUCT_DESC'),
				'ONSELECT' => 'acritExpSettingsChangeTab(this);',
				'SORT' => 10,
				'FILE' => __DIR__.'/_structure_iblock_main.php',
			);
			if($intIBlockOffersID) {
				$arSubTabs['offer'] = array(
					'DIV' => 'fields_offer',
					'TAB' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_FIELDS_OFFER'),
					'TITLE' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_FIELDS_OFFER_DESC'),
					'ONSELECT' => 'acritExpSettingsChangeTab(this);',
					'SORT' => 20,
					'FILE' => __DIR__.'/_structure_iblock_offers.php',
				);
			}
			$arSubTabs['general'] = array(
				'DIV' => 'subtab_general',
				'TAB' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_GENERAL'),
				'TITLE' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_GENERAL_DESC'),
				'ONSELECT' => 'acritExpSettingsChangeTab(this);',
				'SORT' => 30,
				'FILE' => __DIR__.'/../subtabs/general.php',
			);
			$arSubTabs['categories'] = array(
				'DIV' => 'subtab_categories',
				'TAB' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_CATEGORIES'),
				'TITLE' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_CATEGORIES_DESC'),
				'ONSELECT' => 'acritExpSettingsChangeTab(this);',
				'SORT' => 40,
				'FILE' => __DIR__.'/../subtabs/categories.php',
			);
			$arSubTabs['filter'] = array(
				'DIV' => 'subtab_filter',
				'TAB' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_FILTER'),
				'TITLE' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_FILTER_DESC'),
				'ONSELECT' => 'acritExpSettingsChangeTab(this);',
				'SORT' => 50,
				'FILE' => __DIR__.'/../subtabs/filter.php',
			);
			if($intIBlockOffersID) {
				$arSubTabs['offers'] = array(
					'DIV' => 'subtab_offers',
					'TAB' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_OFFERS'),
					'TITLE' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_OFFERS_DESC'),
					'ONSELECT' => 'acritExpSettingsChangeTab(this);',
					'SORT' => 60,
					'FILE' => __DIR__.'/../subtabs/offers.php',
				);
			}
			$arSubTabs['console'] = array(
				'DIV' => 'subtab_console',
				'TAB' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_CONSOLE'),
				'TITLE' => GetMessage('ACRIT_EXP_STRUCTURE_IBLOCK_SUBTAB_CONSOLE_DESC'),
				'ONSELECT' => 'acritExpSettingsChangeTab(this);',
				'SORT' => 1000000,
				'FILE' => __DIR__.'/../subtabs/console.php',
				'CONSOLE' => true,
			);
			foreach($arAdditionalSubTabs as $key => $arTab){
				if(is_array($arSubTabs[$key])){
					$arSubTabs[$key]['FILE2'] = $arTab['FILE'];
				}
				else{
					$arSubTabs[] = $arTab;
				}
			}
			usort($arSubTabs, 'Acrit\Core\Helper::sortBySort');
			// Display tabs
			$obSubTabControl = new \CAdminViewTabControl('ProfileIBlockSubTabs', $arSubTabs);
			$obSubTabControl->Begin();
			foreach($arSubTabs as $arSubTab){
				$obSubTabControl->BeginNextTab();
				require $arSubTab['FILE'];
				if(strlen($arSubTab['FILE2']) && is_file($arSubTab['FILE2'])){
					require $arSubTab['FILE2'];
				}
			}
			// End
			$obSubTabControl->End();
		?>
	</div>
	<br/>

	<?if(!$bCopy):?>
		<div>
			<input type="button" class="adm-btn-green" value="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_BUTTON_SAVE');?>" data-role="iblock-settings-save" />
			<span data-role="iblock-settings-save-progress"></span>
			<span data-role="iblock-settings-save-result"></span>
			<input type="button" class="adm-btn" value="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_BUTTON_CLEAR');?>" data-role="iblock-settings-clear" style="float:right;" />
		</div>
	<?endif?>

	<div data-role="iblock-settings-result"></div>
</div>
