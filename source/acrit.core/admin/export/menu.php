<?
namespace Acrit\Core\Export;

use
	\Bitrix\Main\Localization\Loc;
	

Loc::loadMessages(__FILE__);

$bOldCoreDisabled = \Bitrix\Main\Config\Option::get($strModuleId, 'disable_old_core') == 'Y';

if($APPLICATION->GetGroupRight($strModuleId) != 'D'){
	$strModuleCode = str_replace('.', '_', $strModuleId);
	$strModuleName = $strModuleId;
	$strModuleIndexFile = realpath(__DIR__.'/../../..').'/'.$strModuleId.'/install/index.php';
	if(is_file($strModuleIndexFile)){
		require_once $strModuleIndexFile;
		$obModule = new $strModuleCode();
		$strModuleName = $obModule->MODULE_NAME;
		unset($obModule);
	}
	#
	$arSubmenu = [];
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_NEW_TITLE').(!$bOldCoreDisabled?Loc::getMessage('ACRIT_EXP_MENU_NEW_TITLE_'):''),
		'url' => $strModuleCode.'_new_list.php?lang='.LANGUAGE_ID,
		'more_url' => array($strModuleCode.'_new_list.php', $strModuleCode.'_new_edit.php', $strModuleCode.'_new_migrator.php'),
	);
	if(!$bOldCoreDisabled){
		$arSubmenu[] = array(
			'text' => Loc::getMessage('ACRIT_EXP_MENU_TITLE'),
			'url' => $strModuleCode.'_list.php?lang='.LANGUAGE_ID,
			'more_url' => array($strModuleCode.'_edit.php'),
		);
		$arSubmenu[] = array(
			'text' => Loc::getMessage('ACRIT_EXP_MENU_PROFILE_EXPORT'),
			'url' => $strModuleCode.'_export.php',
			'more_url' => array($strModuleCode.'_export.php'),
		);
	}
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_NEW_SETTINGS'),
		'url' => sprintf('/bitrix/admin/settings.php?mid=%s&lang=%s', $strModuleId, LANGUAGE_ID),
		'more_url' => [],
	);
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_CRM_TITLE'),
		'url' => $strModuleCode.'_crm_list.php?lang='.LANGUAGE_ID,
		'more_url' => array($strModuleCode.'_crm_list.php', $strModuleCode.'_crm_edit.php'),
	);
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_NEW_SUPPORT'),
		'url' => $strModuleCode.'_new_support.php?lang='.LANGUAGE_ID,
		'more_url' => array($strModuleCode.'_new_support.php'),
	);
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_NEW_IDEA'),
		'url' => $strModuleCode.'_new_idea.php?lang='.LANGUAGE_ID,
		'more_url' => array($strModuleCode.'_new_idea.php'),
	);
	$intSort = 10;
	$arModulesAll = [
		'acrit.googlemerchant',
		'acrit.export',
		'acrit.exportpro',
		'acrit.exportproplus',
	];
	$key = array_search($strModuleId, $arModulesAll);
	if(is_numeric($key)){
		$intSort = ($key + 1) * 10;
	}
	$aMenu = array(
		'parent_menu' => 'global_menu_acrit',
		'section' => $strModuleName,
		'sort' => $intSort,
		'text' => $strModuleName,
		'title' => Loc::getMessage('ACRIT_EXP_MENU_TEXT'),
		'url' => '',
		'icon' => 'acrit_exp_menu_icon',
		'page_icon' => '',
		'items_id' => 'menu_'.$strModuleCode,
		'items' => $arSubmenu,
	);
	return $aMenu;
}
return false;
?>