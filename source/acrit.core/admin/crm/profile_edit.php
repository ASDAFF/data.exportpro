<?
namespace Acrit\Core\Crm;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\PluginManager,
	\Acrit\Core\Crm\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Field\ValueBase,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Cli,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\DiscountRecalculation,
	\Acrit\Core\Export\Debug;

// Core (part 1)
$strCoreId = 'acrit.core';
$strModuleId = $ModuleID = preg_replace('#^.*?/([a-z0-9]+)_([a-z0-9]+).*?$#', '$1.$2', $_SERVER['REQUEST_URI']);
$strModuleCode = preg_replace('#^(.*?)\.(.*?)$#', '$2', $strModuleId);
$strModuleUnderscore = preg_replace('#^(.*?)\.(.*?)$#', '$1_$2', $strModuleId);
define('ADMIN_MODULE_NAME', $strModuleId);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strModuleId.'/prolog.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strCoreId.'/install/demo.php');
IncludeModuleLangFile(__FILE__);
\CJSCore::Init(array('jquery','jquery2'));
$strModuleCodeLower = toLower($strModuleCode);

// Check rights
$strRight = $APPLICATION->getGroupRight($strModuleId);
if($strRight < 'R'){
	$APPLICATION->authForm(Loc::getMessage('ACCESS_DENIED'));
}

// Input data
$obGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList();
$arGet = $obGet->toArray();
$obPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList();
$arPost = $obPost->toArray();

// Demo
acritShowDemoExpired($strModuleId);

// Core notice
if(!\Bitrix\Main\Loader::includeModule($strCoreId)){
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	?><div id="acrit-exp-core-notifier"><?
		print '<div style="margin-top:15px;"></div>';
		print \CAdminMessage::ShowMessage(array(
			'MESSAGE' => Loc::getMessage('ACRIT_EXP_CORE_NOTICE', [
				'#CORE_ID#' => $strCoreId,
				'#LANG#' => LANGUAGE_ID,
			]),
			'HTML' => true,
		));
	?></div><?
	$APPLICATION->SetTitle(Loc::getMessage('ACRIT_EXP_PAGE_TITLE_DEFAULT'));
	die();
}

// Module
\Bitrix\Main\Loader::includeModule($strModuleId);

// Module ID for integration
Controller::setModuleId($strModuleId);

// Debug
Debug::setModuleId($strModuleId);

// Page title
$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE_ADD');

// CSS
$APPLICATION->setAdditionalCss('/bitrix/js/'.ACRIT_CORE.'/jquery.select2/dist/css/select2.css');
$APPLICATION->setAdditionalCss('/bitrix/js/'.ACRIT_CORE.'/filter/style.css');

// Get helper data
$arSites = Helper::getSitesList();
$arPlugins = Exporter::getInstance($strModuleId)->findPlugins();
$arPluginsPlain = Exporter::getInstance($strModuleId)->findPlugins(false);
$arPluginTypes = array(
	Plugin::TYPE_NATIVE => Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_NATIVE'),
	Plugin::TYPE_CUSTOM => Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_CUSTOM'),
);

// Core (part 2, visual)
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

// Demo
acritShowDemoNotice($strModuleId);

// Get delay time
$fDelayTime = FloatVal(Helper::getOption($strModuleId, 'time_delay'));
if ($fDelayTime <= 0){
	$fDelayTime = 0.05;
}
$fDelayTime *= 1000;

// Text definitions for popup
ob_start();
?><script>
var acritExpExportTimeDelay = <?=$fDelayTime?>;
var acritExpModuleVersion = '<?=Helper::getModuleVersion($strModuleId);?>';
var acritExpCoreVersion = '<?=Helper::getModuleVersion($strCoreId);?>';
BX.message({
	// General
	ACRIT_EXP_POPUP_LOADING: '<?=Loc::getMessage('ACRIT_EXP_POPUP_LOADING');?>',
	ACRIT_EXP_POPUP_SAVE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_SAVE');?>',
	ACRIT_EXP_POPUP_CLOSE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CLOSE');?>',
	ACRIT_EXP_POPUP_CANCEL: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CANCEL');?>',
	ACRIT_EXP_POPUP_REFRESH: '<?=Loc::getMessage('ACRIT_EXP_POPUP_REFRESH');?>',
	//
	ACRIT_EXP_IBLOCK_SETTINGS_SAVE_PROGRESS: '<?=Loc::getMessage('ACRIT_EXP_IBLOCK_SETTINGS_SAVE_PROGRESS');?>',
	ACRIT_EXP_IBLOCK_SETTINGS_SAVE_SUCCESS: '<?=Loc::getMessage('ACRIT_EXP_IBLOCK_SETTINGS_SAVE_SUCCESS');?>',
	ACRIT_EXP_IBLOCK_SETTINGS_SAVE_ERROR: '<?=Loc::getMessage('ACRIT_EXP_IBLOCK_SETTINGS_SAVE_ERROR');?>',
	ACRIT_EXP_IBLOCK_SETTINGS_CLEAR_CONFIRM: '<?=Loc::getMessage('ACRIT_EXP_IBLOCK_SETTINGS_CLEAR_CONFIRM');?>',
	// Popup: SelectField
	ACRIT_EXP_POPUP_SELECT_FIELD_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_TITLE');?>',
	// Popup: ValueSettings
	ACRIT_EXP_POPUP_VALUE_SETTINGS_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_VALUE_SETTINGS_TITLE');?>',
	// Popup: FieldSettings
	ACRIT_EXP_POPUP_FIELD_SETTINGS_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_FIELD_SETTINGS_TITLE');?>',
	// Popup: AdditionalFields
	ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_TITLE');?>',
	// Popup: CategoriesRedefinition
	ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_TITLE');?>',
	ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_ALL: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_ALL');?>',
	ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_CONFIRM: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_CONFIRM');?>',
	// Popup: Execute
	ACRIT_EXP_POPUP_EXECUTE_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_TITLE');?>',
	ACRIT_EXP_POPUP_EXECUTE_BUTTON_START: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_BUTTON_START');?>',
	ACRIT_EXP_POPUP_EXECUTE_BUTTON_STOP: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_BUTTON_STOP');?>',
	ACRIT_EXP_POPUP_EXECUTE_STOPPED: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_STOPPED');?>',
	ACRIT_EXP_POPUP_EXECUTE_ERROR: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_ERROR');?>',
	// Popup: IBlocks preview
	ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_TITLE');?>',
	// 
	ACRIT_EXP_ADDITIONAL_FIELD_DELETE_CONFIRM: '<?=Loc::getMessage('ACRIT_EXP_ADDITIONAL_FIELD_DELETE_CONFIRM');?>',
	ACRIT_EXP_ADDITIONAL_FIELDS_DELETE_ALL_CONFIRM: '<?=Loc::getMessage('ACRIT_EXP_ADDITIONAL_FIELDS_DELETE_ALL_CONFIRM');?>',
	// 
	ACRIT_EXP_UPDATE_CATEGORIES_SUCCESS: '<?=Loc::getMessage('ACRIT_EXP_UPDATE_CATEGORIES_SUCCESS');?>',
	ACRIT_EXP_UPDATE_CATEGORIES_ERROR: '<?=Loc::getMessage('ACRIT_EXP_UPDATE_CATEGORIES_ERROR');?>',
	//
	ACRIT_EXP_POPUP_CRON_ERROR: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CRON_ERROR');?>',
	ACRIT_EXP_AJAX_AUTH_REQUIRED: '<?=Loc::getMessage('ACRIT_EXP_AJAX_AUTH_REQUIRED');?>',
	ACRIT_EXP_AJAX_CONFIRM_CLEAR_EXPORT_DATA: '<?=Loc::getMessage('ACRIT_EXP_AJAX_CONFIRM_CLEAR_EXPORT_DATA');?>'
});
</script><?
\Bitrix\Main\Page\Asset::GetInstance()->AddString(ob_get_clean(), true, \Bitrix\Main\Page\AssetLocation::AFTER_CSS);

// JS lang
$strSelect2LangFile = Helper::isUtf() ? 'ru_utf8.js' : 'ru_cp1251.js';

// JS
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.cookie.min.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.textchange.min.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.insertatcaret.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.acrit.tabs.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.acrit.filter.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.acrit.popup.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.acrit.hotkey.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/filter/script.js');
\Bitrix\Main\Page\Asset::GetInstance()->addJs('/bitrix/js/'.ACRIT_CORE.'/jquery.select2/dist/js/select2.js');
\Bitrix\Main\Page\Asset::GetInstance()->addJs('/bitrix/js/'.ACRIT_CORE.'/jquery.select2/'.$strSelect2LangFile);
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/highlightjs/highlight.pack.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/moment.min.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/copy_to_clipboard.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/cron.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/crm/profile_edit.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/export/profile_edit.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/export/profile_edit.hotkeys.js');
Filter::addJs();

// Get current profile
$intProfileID = IntVal($arGet['ID']);
if ($intProfileID > 0) {
	$arQuery = [
		'filter' => [
			'ID' => $intProfileID,
		],
		'limit' => 1,
	];
	if(Helper::call($strModuleId, 'CrmProfiles', 'getList', [$arQuery])->getSelectedRowsCount() == 0){
		LocalRedirect('/bitrix/admin/acrit_'.$strModuleCodeLower.'_crm_list.php?lang='.LANGUAGE_ID);
	}
	$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE_EDIT', array('#ID#' => $intProfileID));
}
$strAdminFormName = 'AcritExpProfile';
$strTabParam = $strAdminFormName.'_active_tab';

if ($intProfileID) {
	Controller::setProfile($intProfileID);
}

//// TODO: Backup
//if($arGet['backup'] == 'Y' && $intProfileID > 0){
//	$bBackupSuccess = false;
//	#$strTmpFile = Backup::createBackupFile($intProfileID);
//	$strTmpFile = Helper::call($strModuleId, 'Backup', 'createBackupFile', [$intProfileID]);
//	if(strlen($strTmpFile) && is_file($strTmpFile)) {
//		#$strZipFile = Backup::fileToZip($strTmpFile);
//		$strZipFile = Helper::call($strModuleId, 'Backup', 'fileToZip', [$strTmpFile]);
//		if(is_file($strZipFile)){
//			Helper::obRestart();
//			#$bBackupSuccess = Backup::downloadFile($strZipFile);
//			$bBackupSuccess = Helper::call($strModuleId, 'Backup', 'downloadFile', [$strZipFile]);
//			@unlink($strTmpFile);
//			@unlink($strZipFile);
//			if($bBackupSuccess){
//				die();
//			}
//		}
//	}
//	if(!$bBackupSuccess) {
//		LocalRedirect($APPLICATION->getCurPageParam('', array('backup')));
//	}
//}

//// TODO: Copy mode?
//$bCopy = $arGet['copy'] == 'Y';
//if($bCopy){
//	$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE_COPY', array('#ID#' => $intProfileID));
//}

// Set page title
$APPLICATION->SetTitle($strPageTitle);

// Deleting current profile?
if($arGet['delete'] == 'Y'){
	Helper::call($strModuleId, 'CrmProfiles', 'delete', [$intProfileID]);
	LocalRedirect('/bitrix/admin/acrit_'.$strModuleCodeLower.'_crm_list.php?lang='.LANGUAGE_ID);
}

// Context menu
$arMenu = array();
$arMenu[] = array(
	'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_LIST'),
	'LINK' => 'acrit_'.$strModuleCodeLower.'_crm_list.php?lang='.LANGUAGE_ID,
	'ICON' => 'btn_list',
);
if($intProfileID) {
	$arActionsMenu = array();
	$arActionsMenu[] = array(
		'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_ADD'),
		'LINK' => 'acrit_'.$strModuleCodeLower.'_crm_edit.php?lang='.LANGUAGE_ID,
		'ICON' => 'edit',
	);
	if(!$bCopy){
//		$arActionsMenu[] = array(
//			'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_COPY'),
//			'LINK' => 'acrit_'.$strModuleCodeLower.'_crm_edit.php?ID='.$intProfileID.'&copy=Y&lang='.LANGUAGE_ID,
//			'ICON' => 'copy',
//		);
		$strDeleteUrl = 'acrit_'.$strModuleCodeLower.'_crm_edit.php?ID='.$intProfileID.'&delete=Y&lang='.LANGUAGE_ID;
		$arActionsMenu[] = array(
			'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_DELETE'),
			'ICON' => 'delete',
			'ACTION' => "if(confirm('".Loc::GetMessage("ACRIT_EXP_MENU_DELETE_CONFIRM")."')){window.location='".$strDeleteUrl."';}",
		);
//		$arActionsMenu[] = array(
//			'SEPARATOR' => true,
//		);
//		$strBackupUrl = 'acrit_'.$strModuleCodeLower.'_crm_edit.php?ID='.$intProfileID.'&backup=Y&lang='.LANGUAGE_ID;
//		$APPLICATION->addHeadString('<script>var acritExpProfileBackupUrl = "'.$strBackupUrl.'";</script>');
//		$arActionsMenu[] = array(
//			'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_BACKUP'),
//			'ICON' => 'pack',
//			'LINK' => $strBackupUrl,
//		);
	}
	$arMenu[] = array(
		'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_ACTIONS'),
		'ICON' => 'btn_new',
		'MENU' => $arActionsMenu,
	);
	if(!$bCopy){
		$arMenu[] = array(
			'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_RUN'),
			'ICON' => 'acrit-exp-button-run',
			'ONCLICK' => 'AcritExpPopupExecute.Open();',
		);
	}
}
$context = new \CAdminContextMenu($arMenu);
$context->Show();

// Get helper data
/*
$arSites = Helper::getSitesList();
$arPlugins = Exporter::getInstance($strModuleId)->findPlugins();
$arPluginsPlain = Exporter::getInstance($strModuleId)->findPlugins(false);
$arPluginTypes = array(
	Plugin::TYPE_NATIVE => Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_NATIVE'),
	Plugin::TYPE_CUSTOM => Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_CUSTOM'),
);
*/

// Add plugins js-array to page content
$arPluginsPrint = $arPlugins;
foreach($arPluginsPrint as &$arPlugin){
	unset($arPlugin['DESCRIPTION'], $arPlugin['EXAMPLE'], $arPlugin['ICON'], $arPlugin['ICON_BASE64']);
	foreach($arPlugin['FORMATS'] as &$arFormat){
		unset($arFormat['DESCRIPTION'], $arFormat['EXAMPLE'], $arFormat['ICON'], $arFormat['ICON_BASE64']);
	}
}
unset($arPlugin, $arFormat);
ob_start();
?><script>
window.acritExpPlugins = <?=\CUtil::PhpToJSObject($arPluginsPrint);?>;
</script><?
\Bitrix\Main\Page\Asset::GetInstance()->AddString(ob_get_clean(), true, \Bitrix\Main\Page\AssetLocation::AFTER_CSS);
unset($arPluginsPrint);

// Current
$arProfilePlugin = false;
$strPluginClass = null;
$obPlugin = null; // plugin || format

// TODO: Get current data
$arProfile = array();
if ($intProfileID) {
	// Get from db
	#$arProfile = Profiles::getProfiles($intProfileID);
	$arProfile = Helper::call($strModuleId, 'CrmProfiles', 'getProfiles', [$intProfileID]);
//	echo '<pre>'; print_r($arProfile); echo '</pre>';
	// Get plugin info
	if (strlen($arProfile['PLUGIN'])) {
		$arProfilePlugin = Exporter::getInstance($strModuleId)->getPluginInfo($arProfile['PLUGIN']);
//		echo '<pre>'; print_r($arProfilePlugin); echo '</pre>';
		if(is_array($arProfilePlugin)){
			$strPluginClass = $arProfilePlugin['CLASS'];
		}
		else {
			print Helper::showError(Loc::getMessage('ACRIT_EXP_ERROR_FORMAT_NOT_FOUND_TITLE'),
				Loc::getMessage('ACRIT_EXP_ERROR_FORMAT_NOT_FOUND_DETAILS', array(
					'#FORMAT#' => $arProfile['FORMAT'],
				)));
		}
	}
	$APPLICATION->SetTitle($APPLICATION->GetTitle().' &laquo;'.$arProfile['NAME'].'&raquo;');
}
if (strlen($strPluginClass) && class_exists($strPluginClass)) {
	$obPlugin = new $strPluginClass($strModuleId);
}

// Check / get default data
$arProfile['ACTIVE'] = in_array($arProfile['ACTIVE'],array('Y','N')) ? $arProfile['ACTIVE'] : 'Y';
$arProfile['SORT'] = is_numeric($arProfile['SORT']) && $arProfile['SORT']>0 ? $arProfile['SORT'] : 100;

// Set array of profile
if(is_object($obPlugin)) {
	$obPlugin->setProfileArray($arProfile);
}

// Save form on POST
$bSave = !!strlen($arPost['save']);
$bApply = !!strlen($arPost['apply']);
$bCancel = !!strlen($arPost['cancel']);
if(($bSave || $bApply) && $strRight == 'W'){
    // TODO
	$arProfileFields = $arPost['PROFILE'];
//	if(is_object($obPlugin)) {
//		$obPlugin->setProfileArray($arProfileFields);
//	}
	foreach ($arProfileFields as $k => $value) {
        if (in_array($k, ['PARAMS', 'CONNECT_CRED', 'CONNECT_DATA', 'OPTIONS', 'STAGES', 'FIELDS', 'CONTACTS', 'PRODUCTS', 'OTHER', 'SYNC'])) {
	        $arProfileFields[$k] = serialize($value);
        }
    }
	$bCopySuccess = false;
	if($intProfileID && $bCopy) {
		#$intNewProfileID = Profiles::copyProfile($intProfileID);
		$intNewProfileID = Helper::call($strModuleId, 'CrmProfiles', 'copyProfile', [$intProfileID]);
		if ($intNewProfileID) {
			$intProfileID = $intNewProfileID;
			#$obResult = Profiles::update($intProfileID, $arProfileFields);
			$obResult = Helper::call($strModuleId, 'CrmProfiles', 'update', [$intProfileID, $arProfileFields]);
			$bCopySuccess = true;
			$strTabParam = $strAdminFormName.'_active_tab';
		}
	}
	elseif($intProfileID && !$bCopy) {
		#$obResult = Profiles::update($intProfileID, $arProfileFields);
		$obResult = Helper::call($strModuleId, 'CrmProfiles', 'update', [$intProfileID, $arProfileFields]);
	}
	else {
		$arProfileFields['DATE_CREATED'] = new \Bitrix\Main\Type\DateTime();
		#$obResult = Profiles::add($arProfileFields);
		$obResult = Helper::call($strModuleId, 'CrmProfiles', 'add', [$arProfileFields]);
		$intProfileID = $obResult->getID();
	}
	if($bCopySuccess || $obResult->isSuccess()) {
	    // Add agent
        if ($intProfileID) {
	        \Acrit\Core\Crm\PeriodSync::setModuleId($strModuleId);
	        \Acrit\Core\Crm\PeriodSync::set($intProfileID);
        }
		// Redirect
		if($bApply) {
			$arClearGetParams = array(
				'ID',
				'copy',
				$strTabParam,
			);
			$strTab = strlen($arPost[$strTabParam]) ? '&'.$strTabParam.'='.$arPost[$strTabParam] : '';
			$strUrl = $APPLICATION->getCurPageParam('ID='.$intProfileID.$strTab, $arClearGetParams);
		}
		else {
			$strUrl = '/bitrix/admin/acrit_'.$strModuleCodeLower.'_crm_list.php?lang='.LANGUAGE_ID;
		}
		LocalRedirect($strUrl);
	}
	else {
		$arErrors = $obResult->getErrorMessages();
		print Helper::showError(is_array($arErrors) ? implode('<br/>', $arErrors) : $arErrors);
		$arProfile = $arPost['PROFILE'];
	}
}

// Ajax actions
$strAjaxAction = $arGet['ajax_action'];
if(strlen($strAjaxAction)){
	header('Content-Type: application/json; charset='.(Helper::isUtf()?'utf-8':'windows-1251'));
	ini_set('display_`errors',0);
	error_reporting(~E_ALL);
	$arJsonResult = array();
	$APPLICATION->RestartBuffer();
	switch($strAjaxAction){
        case 'man_sync_run':
	        require 'include/actions/man_sync_run.php';
            break;
        case 'man_sync_count':
	        require 'include/actions/man_sync_count.php';
            break;
		/*// get_plugin_info
		case 'get_plugin_info':
			$strAjaxPlugin = $arGet['plugin'];
			$strAjaxFormat = $arGet['format'];
			if(strlen($strAjaxPlugin)) {
				$arJsonResult['PLUGIN'] = false;
				if(is_array($arPlugins[$strAjaxPlugin])){
					$arJsonResult['PLUGIN'] = $arPlugins[$strAjaxPlugin];
					if(strlen($strAjaxFormat)) {
						$arJsonResult['FORMAT'] = false;
						if(is_array($arPlugins[$strAjaxPlugin]['FORMATS'][$strAjaxFormat])) {
							$arJsonResult['FORMAT'] = $arPlugins[$strAjaxPlugin]['FORMATS'][$strAjaxFormat];
						}
					}
				}
			}
			$arJsonResult['PLUGIN_CODE'] = $strAjaxPlugin;
			$arJsonResult['FORMAT_CODE'] = $strAjaxFormat;
			break;
		// save last settings subtab
		case 'save_last_settings_tab':
			#Profiles::update($intProfileID, array(
			#	'LAST_SETTINGS_TAB' => $arGet['tab'],
			#));
			if($strRight == 'W'){
				Helper::call($strModuleId, 'CrmProfiles', 'update', [$intProfileID, ['LAST_SETTINGS_TAB' => $arGet['tab']]]);
			}
			break;
*/
		// custom plugin actions
		case 'plugin_ajax_action':
			$strAction = $arGet['action'];
			if(is_object($obPlugin)) {
				$obPlugin->ajaxAction($strAction, array(
					'PROFILE_ID' => $intProfileID,
					'IBLOCK_ID' => $intIBlockID,
					'IBLOCK_OFFERS_ID' => $intIBlockOffersID,
					'GET' => $arGet,
					'POST' => $arPost,
					'PLUGINS' => $arPlugins,
				), $arJsonResult);
			}
			break;
		// Load plugin settings
		case 'load_plugin_settings':
			$arJsonResult['HTML'] = '';
			$arJsonResult['DESCRIPTION'] = '';
			$arJsonResult['EXAMPLE'] = '';
			$arPlugin = Exporter::getInstance($strModuleId)->getPluginInfo($arGet['format']);
			if(is_array($arPlugin) && is_array($arPluginsPlain[$arPlugin['CODE']])){
				$obPlugin = new $arPluginsPlain[$arPlugin['CODE']]['CLASS']($strModuleId);
				# Auto set default filename
				$arSavedPlugin = Exporter::getInstance($strModuleId)->getPluginInfo($arProfile['FORMAT']);
				if($arPlugin!=$arSavedPlugin && method_exists($obPlugin, 'getDefaultExportFilename')){
					$strExportFilenameDefault = $obPlugin->getDefaultExportFilename();
					if(strlen($strExportFilenameDefault)){
						$strDirectory = $obPlugin->getDefaultDirectory();
					}
					$strExportFilename = $strDirectory.'/'.$strExportFilenameDefault;
					$intFilenameIndex = 0;
					while(true){
						$intFilenameIndex++;
						$strExportFilename = Helper::getFileNameWithIndex($strExportFilename, $intFilenameIndex);
						if(!is_file($_SERVER['DOCUMENT_ROOT'].$strExportFilename)){
							break;
						}
					}
					$arProfile['PARAMS']['EXPORT_FILE_NAME'] = $strExportFilename;
				}
				#
				$obPlugin->setProfileArray($arProfile);
				if(is_array($arPost['PROFILE'])){
					if(isset($arPost['PROFILE']['EXPORT_FILE_NAME']) && !strlen($arPost['PROFILE']['EXPORT_FILE_NAME'])) {
						unset($arPost['PROFILE']['EXPORT_FILE_NAME']);
					}
					$obPlugin->setProfileArray(array_merge($arProfile, $arPost['PROFILE']));
				}
			}
			if(is_object($obPlugin)) {
				$arJsonResult['HTML'] =
					$obPlugin->includeCss().
					$obPlugin->includeJs().
					$obPlugin->showSettings();
				$arJsonResult['DESCRIPTION'] = $obPlugin::getDescription();
				$arJsonResult['EXAMPLE'] = $obPlugin::getExample();
			}
			break;
/*
		// Unlock profile
		case 'profile_unlock':
			$arJsonResult['HTML'] = '';
			$arJsonResult['Success'] = false;
			#$obResult = Profiles::unlock($intProfileID);
			$obResult = Helper::call($strModuleId, 'CrmProfiles', 'unlock', [$intProfileID]);
			#Profiles::clearSession($intProfileID);
			Helper::call($strModuleId, 'CrmProfiles', 'clearSession', [$intProfileID]);
			if($obResult->isSuccess()){
				$arJsonResult['Success'] = true;
				$arSession = unserialize($arProfile['SESSION']);
				$arSession = is_array($arSession) ? $arSession : array();
				if($arSession['HISTORY_ID'] > 0){
					#$obResult = History::update($arSession['HISTORY_ID'], array('STOPPED' => 'Y'));
					$obResult = Helper::call($strModuleId, 'History', 'update', [$arSession['HISTORY_ID'], array('STOPPED' => 'Y')]);
					$arJsonResult['Success'] = $obResult->isSuccess();
				}
			}
			break;
		// Log: refresh
		case 'log_refresh':
			$arJsonResult['Success'] = false;
			// Profile log
			$strLogPreview = Log::getInstance($strModuleId)->getLogPreview($intProfileID);
			if(strlen($strLogPreview)){
				$arJsonResult['Success'] = true;
				$arJsonResult['Log'] = $strLogPreview;
			}
			$arJsonResult['LogSize'] = Log::getInstance($strModuleId)->getLogSize($intProfileID, true);
			if(is_object($obPlugin)){
				$arJsonResult['ExportFilename'] = $obPlugin->showFileOpenLink(false, true);
			}
			break;
		// Log: clear
		case 'log_clear':
			$arJsonResult['Success'] = true;
			Log::getInstance($strModuleId)->deleteLog($intProfileID);
			break;
		// History refresh
		case 'history_refresh':
			ob_start();
			require __DIR__.'/include/tabs/_log_history.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Get profile name index
		case 'get_profile_name_index':
			$strName = $arPost['profile_name'];
			if(!Helper::isUtf()){
				$strName = Helper::convertEncoding($strName, 'UTF-8', 'CP1251');
			}
			$strName = preg_replace('#\s?\(\d+\)$#', '', $strName);
			$arExistNames = array();
			#foreach(Profiles::getProfiles() as $arProfile){
			$arProfiles = Helper::call($strModuleId, 'CrmProfiles', 'getProfiles');
			foreach($arProfiles as $arProfile){
				$arExistNames[] = $arProfile['NAME'];
			}
			$intNameIndex = 0;
			while(true){
				$intNameIndex++;
				$strNameNew = $strName.' ('.$intNameIndex.')';
				if(!in_array($strNameNew, $arExistNames)){
					break;
				}
			}
			$arJsonResult['NAME'] = $strNameNew;
			break;
		// Hide main notice
		case 'main_notice_hide':
			$arJsonResult['Success'] = true;
			\CUserOptions::SetOption($strModuleId, 'main_notice_hidden', 'Y');
			break;
		// Check lock
		case 'check_lock':
			ob_start();
			#if(Profiles::isLocked($arProfile)){
			if(Helper::call($strModuleId, 'CrmProfiles', 'isLocked', [$arProfile])){
				print '<div style="margin-top:15px;"></div>';
				print Helper::showNote(Loc::getMessage('ACRIT_EXP_LOCK_NOTICE', array(
					'#DATE#' => is_object($arProfile['DATE_LOCKED']) ? $arProfile['DATE_LOCKED']->toString() : '???',
				)), true).'<br/>';
			}
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Execute console command
		case 'console_execute':
			$strConsoleCommand = $arPost['command'];
			$strConsoleHeight = $arPost['height'];
			$strConsoleText = $arPost['text'];
			\CUserOptions::SetOption($strModuleId, 'console_command', base64_encode($strConsoleCommand));
			\CUserOptions::SetOption($strModuleId, 'console_height', $strConsoleHeight);
			\CUserOptions::SetOption($strModuleId, 'console_text', $strConsoleText);
			$arJsonResult['HTML'] = false;
			if($USER->isAdmin()){
				$fTime = microtime(true);
				$arClasses = array(
					'\Bitrix\Main\Localization\Loc',
					'\Bitrix\Main\EventManager',
					'\Bitrix\Main\Application',
					'\Bitrix\Main\Config\Option',
				);
				$arCoreAutoload = &$GLOBALS['ACRIT_CORE_AUTOLOAD_CLASSES'];
				#$arModuleAutoload = &$GLOBALS['ACRIT_'.toUpper($strModuleCode).'_AUTOLOAD_CLASSES'];
				foreach($arCoreAutoload as $strClass => $strClassDir){
					$arClass = explode('\\', $strClass);
					$strClassBasename = array_pop($arClass); 
					$strAs = '';
					if(substr($strClassBasename, -5) === 'Table'){
						$strAs = substr($strClassBasename, 0, -5);
					}
					elseif($strClassBasename === 'Base'){
						$strAs = end($arClass).$strClassBasename;
					}
					$arClass[] = $strClassBasename;
					$strClass = '\\'.implode('\\', $arClass);
					$arClasses[] = $strClass.(strlen($strAs) ? ' as '.$strAs : '');
				}
				$arModules = array('iblock', 'catalog', 'sale', 'currency');
				foreach($arModules as $strModule){
					\Bitrix\Main\Loader::includeModule($strModule);
				}
				$strCommand = 'use '.implode(', '.PHP_EOL."    ", $arClasses).';'.PHP_EOL;
				$arGlobals = array(
					'$DB',
					'$DBType',
					'$DBHost',
					'$DBLogin',
					'$DBPassword',
					'$DBName',
					'$DBDebug',
					'$DBDebugToFile',
					'$USER',
					'$APPLICATION',
					'$intProfileID',
					'$intIBlockID',
					'$intIBlockOffersID',
					'$intIBlockParentID',
					'$arCatalog',
					'$arSites',
					'$arPlugins',
					'$arPluginsPlain',
					'$arPluginTypes',
					'$arProfilePlugin',
					'$strPluginClass',
					'$arGet',
					'$arPost',
					'$arProfile',
				);
				$strCommand .= 'global '.implode(', ', $arGlobals).';'.PHP_EOL;
				$strCommand .= $strConsoleCommand;
				Debug::$arData['PROFILE_ID'] = $intProfileID;
				Debug::$arData['IBLOCK_ID'] = $intIBlockID;
				Debug::$arData['OFFERS_IBLOCK_ID'] = $intIBlockOffersID;
				Debug::$arData['PLUGIN'] = $obPlugin;
				Debug::$arData['CATALOG'] = $arCatalog;
				ini_set('display_errors',1);
				error_reporting(E_ALL^E_NOTICE^E_STRICT^E_DEPRECATED);
				$strContent = $obPlugin->executeConsole($strCommand);
				if($strConsoleText=='Y'){
					ob_start();
					Helper::P($strContent);
					$strContent = ob_get_clean();
				}
				if(trim($strContent) == ''){
					$strContent = '<br/>';
				}
				$strContent = '<hr/>'.$strContent.'<hr/>';
				$strContent .= Loc::getMessage('ACRIT_EXP_AJAX_CONSOLE_TIME', array(
					'#TIME#' => number_format(microtime(true)-$fTime, 6, '.', ''),
				));
				$arJsonResult['HTML'] = $strContent;
			}
			else{
				$arJsonResult['AccessDenied'] = Loc::getMessage('ACRIT_EXP_ERROR_CONSOLE_ACCESS_DENIED');
			}
			break;*/
	}
	print Json::encode($arJsonResult);
	ini_set('display_errors', 0); // Against 'Warning:  A non-numeric value encountered in /home/bitrix/www/bitrix/modules/perfmon/classes/general/keeper.php on line 321'
	die();
}

// Check database
Helper::checkDatabase($strModuleId);

// Tab control
$arTabs = array();
$strTabsDir = '/include/tabs';
$arTabs[] = array(
	'DIV' => 'general',
	'TAB' => Loc::getMessage('ACRIT_EXP_TAB_GENERAL_NAME'),
	'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_GENERAL_DESC'),
	'SORT' => 1,
	'FILE' => __DIR__.$strTabsDir.'/general.php',
);
if($intProfileID){
	$arTabs[] = array(
		'DIV' => 'basic',
		'TAB' => Loc::getMessage('ACRIT_EXP_TAB_BASIC_NAME'),
		'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_BASIC_DESC'),
		'SORT' => 5,
		'FILE' => __DIR__.$strTabsDir.'/basic.php',
	);
	$arTabs[] = array(
		'DIV' => 'contacts',
		'TAB' => Loc::getMessage('ACRIT_EXP_TAB_CONTACTS_NAME'),
		'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_CONTACTS_DESC'),
		'SORT' => 10,
		'FILE' => __DIR__.$strTabsDir.'/contacts.php',
	);
	$arTabs[] = array(
		'DIV' => 'stages',
		'TAB' => Loc::getMessage('ACRIT_EXP_TAB_STAGES_NAME'),
		'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_STAGES_DESC'),
		'SORT' => 30,
		'FILE' => __DIR__.$strTabsDir.'/stages.php',
	);
	$arTabs[] = array(
		'DIV' => 'fields',
		'TAB' => Loc::getMessage('ACRIT_EXP_TAB_FIELDS_NAME'),
		'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_FIELDS_DESC'),
		'SORT' => 40,
		'FILE' => __DIR__.$strTabsDir.'/fields.php',
	);
//	$arTabs[] = array(
//		'DIV' => 'products',
//		'TAB' => Loc::getMessage('ACRIT_EXP_TAB_PRODUCTS_NAME'),
//		'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_PRODUCTS_DESC'),
//		'SORT' => 50,
//		'FILE' => __DIR__.$strTabsDir.'/products.php',
//	);
	if(!$bCopy){
		$arTabs[] = array(
			'DIV' => 'sync',
			'TAB' => Loc::getMessage('ACRIT_EXP_TAB_SYNC_NAME'),
			'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_SYNC_DESC'),
			'SORT' => 60,
			'FILE' => __DIR__.$strTabsDir.'/sync.php',
		);
//		$arTabs[] = array(
//			'DIV' => 'cron',
//			'TAB' => Loc::getMessage('ACRIT_EXP_TAB_CRON_NAME'),
//			'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_CRON_DESC'),
//			'SORT' => 30,
//			'FILE' => __DIR__.$strTabsDir.'/cron.php',
//		);
//		$arTabs[] = array(
//			'DIV' => 'log',
//			'TAB' => Loc::getMessage('ACRIT_EXP_TAB_LOG_NAME'),
//			'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_LOG_DESC'),
//			'SORT' => 40,
//			'FILE' => __DIR__.$strTabsDir.'/log.php',
//		);
	}
}

// Get system tabs codes
$arSystemTabs = array();
foreach($arTabs as $key => $arTab) {
	$arSystemTabs[] = $arTab['DIV'];
}

// Custom tabs
$arProfileTabs = array();
$intTabSortMinimal = 2;
$intTabSortDefault = 100;
if(is_object($obPlugin)) {
	$arProfileTabs = $obPlugin->getAdditionalTabs($intProfileID);
	if(!is_array($arProfileTabs)){
		$arProfileTabs = array();
	}
	foreach($arProfileTabs as $key => $arTab){
		if(!strlen($arTab['DIV']) || !strlen($arTab['TAB']) || !strlen($arTab['FILE']) || !is_file($arTab['FILE'])){
			unset($arProfileTabs[$key]);
		}
	}
	// Set tab sort by default
	foreach($arProfileTabs as $key => $arProfileTab) {
		$arProfileTab['SORT'] = IntVal($arProfileTab['SORT']);
		$arProfileTab['SORT'] = $arProfileTab['SORT']>=$intTabSortMinimal ? $arProfileTab['SORT'] : $intTabSortDefault;
		$arProfileTabs[$key] = $arProfileTab;
	}
	// Search files for additional tabs
	foreach($arProfileTabs as $key => $arProfileTab) {
		$bTabFileFound = false;
		if(strlen($arProfileTab['FILE'])) {
			if(is_file($arProfileTab['FILE']) && filesize($arProfileTab['FILE'])) {
				$bTabFileFound = true;
				$arProfileTabs[$key]['FILE'] = $arProfileTab['FILE'];
			}
		}
		if(!$bTabFileFound) {
			unset($arProfileTabs[$key]);
		}
	}
	if(is_array($arProfileTabs)) {
		$arTabs = array_merge($arTabs,$arProfileTabs);
	}
}
// Get custom tabs (ToDo: check this work properly)
foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers($strModuleId, 'OnGetAdditionalTabs') as $arHandler) {
	ExecuteModuleEventEx($arHandler, array(&$arTabs));
}
# Set tab sort by default (DEFAULT=100)
foreach($arTabs as $key => $arTab) {
	if(!in_array($arTab['DIV'], $arSystemTabs)){
		$arTab['SORT'] = IntVal($arTab['SORT']);
		$arTab['SORT'] = $arTab['SORT']>=$intTabSortMinimal ? $arTab['SORT'] : $intTabSortDefault;
		$arTabs[$key] = $arTab;
	}
}
usort($arTabs, '\Acrit\Core\Helper::sortBySort');

# Lock notifier
if(Helper::getOption($strModuleId, 'check_lock') == 'Y'){
	?><div id="acrit-exp-lock-notifier"><?
		#if(Profiles::isLocked($arProfile)){
		if(Helper::call($strModuleId, 'CrmProfiles', 'isLocked', [$arProfile])){
			print '<div style="margin-top:15px;"></div>';
			print Helper::showNote(Loc::getMessage('ACRIT_EXP_LOCK_NOTICE', array(
				'#DATE#' => is_object($arProfile['DATE_LOCKED']) ? $arProfile['DATE_LOCKED']->toString() : '???',
			)), true).'<br/>';
		}
	?></div><?
}

# Update notifier
\Acrit\Core\Update::display();

# XDebug notifier
Helper::call($strModuleId, 'CrmProfiles', 'checkXDebug');

# Check export file name unique
if(!$bCopy && is_array($arProfile) && is_object($obPlugin)){
	$arCheckResults = $obPlugin->checkData();
	$arPluginErrors = [];
	if(is_array($arCheckResults)){
		foreach($arCheckResults as $arCheckResult){
			if(!is_array($arCheckResult)){
				$arCheckResult = ['MESSAGE' => $arCheckResult];
			}
			$bError = !!$arCheckResult['IS_ERROR'];
			$strErrorTitle = $arCheckResult['TITLE'];
			$strErrorMessage = $arCheckResult['MESSAGE'];
			if($bError){
				$arPluginErrors[] = Helper::showError($strErrorTitle, $strErrorMessage);
			}
			else{
				$arPluginErrors[] = Helper::showNote((strlen($strErrorTitle)?'<b>'.$strErrorTitle.'</b><br/>':'').$strErrorMessage, true, false, true);
			}
		}
	}
	print implode('', $arPluginErrors);
}

?><div id="acrit_exp_form"><?

// Show plugin messages
if(is_object($obPlugin)){
	ob_start();
	$obPlugin->showMessages();
	$strHtml = trim(ob_get_clean());
	if(strlen($strHtml)){
		print '<div id="acrit-exp-plugin-messages">'.$strHtml.'</div>';
	}
}

$bShowMainNotice = \CUserOptions::GetOption($strModuleId, 'main_notice_hidden') != 'Y';

// Start TabControl (via CAdminForm, not CAdminTabControl)
$obTabControl = new \CAdminForm($strAdminFormName, $arTabs);
$obTabControl->Begin(array(
	'FORM_ACTION' => $APPLICATION->GetCurPageParam('', []),
));
$obTabControl->BeginPrologContent();
// Begin form parameters for JS
?>
<?if($bShowMainNotice):?>
<div data-role="main-notice"><?=Helper::showNote(Loc::getMessage('ACRIT_EXP_MAIN_NOTICE_FOR_HINTS'), true);?></div>
<?endif?>
<div data-role="settings-notice"><?=Helper::showNote(Loc::getMessage('ACRIT_CRM_SETTINGS_LINK'), true);?></div>
<input type="hidden" id="param__profile_id" value="<?=$intProfileID;?>" />
<input type="hidden" id="param__form_name" value="<?=$strAdminFormName;?>" />
<input type="hidden" id="param__plugin" value="<?=$arProfile['PLUGIN'];?>" />
<input type="hidden" id="param__format" value="<?=$arProfile['FORMAT'];?>" />
<input type="hidden" id="param__copy" value="<?=($bCopy?'Y':'N');?>" />
<input type="hidden" id="param__page_title" value="<?=($bCopy?'':$strPageTitle);?>" />
<?
// End form parameters for JS
$obTabControl->EndPrologContent();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// All tabs
foreach($arTabs as $arTab){
	$obTabControl->BeginNextFormTab();
	require $arTab['FILE'];
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$obTabControl->Buttons(array(
	'disabled' => false,
	'back_url' => 'acrit_'.$strModuleCodeLower.'_crm_list.php?lang='.LANGUAGE_ID,
));
$obTabControl->Show();

?></div><?

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>