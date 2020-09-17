<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Log;

// Core (part 1)
$strCoreId = 'acrit.core';
define('ADMIN_MODULE_NAME', $strCoreId);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
\Bitrix\Main\Loader::includeModule($strCoreId);
IncludeModuleLangFile(__FILE__);
\CJSCore::Init('jquery2');

// Arguments
$arGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray();
$strAjaxAction = $arGet['action'];
$strModuleID = $arGet['module'];
$intProfileID = $arGet['profile'];

// AJAX actions
if(strlen($strAjaxAction)){
	Json::setHttpHeader();
	ini_set('display_errors', 0);
	error_reporting(~E_ALL);
	$arJsonResult = [
		'Success' => false,
	];
	$APPLICATION->RestartBuffer();
	switch($strAjaxAction){
		case 'refresh':
		case 'clear':
			if($strAjaxAction == 'clear'){
				Log::getInstance($strModuleID)->deleteLog($intProfileID);
			}
			$arJsonResult['ID'] = $intProfileID;
			$arJsonResult['Success'] = true;
			$arJsonResult['Log'] = '';
			$arJsonResult['LogFilename'] = Log::getInstance($strModuleID)->getLogFilename($intProfileID, true);
			$strLogPreview = Log::getInstance($strModuleID)->getLogPreview($intProfileID);
			if(strlen($strLogPreview)){
				$arJsonResult['Log'] = $strLogPreview;
			}
			$arJsonResult['LogSize'] = Log::getInstance($strModuleID)->getLogSize($intProfileID, true);
			$arJsonResult['MaxSize'] = Log::getInstance($strModuleID)->getMaxSize(true, true);
			break;
	}
	ini_set('display_errors', 0); // Against 'Warning:  A non-numeric value encountered in /home/bitrix/www/bitrix/modules/perfmon/classes/general/keeper.php on line 321'
	Json::printEncoded($arJsonResult);
	die();
}

// Profile?
$bProfile = false;
if($intProfileID && $intProfileID > 0) {
	$bProfile = true;
}

// Download?
if($arGet[Log::DOWNLOAD_PARAM] == Log::DOWNLOAD_PARAM_Y){
	Log::getInstance($strModuleID)->downloadLog($intProfileID);
	die();
}

if($bProfile) {
	$APPLICATION->SetTitle(Helper::getMessage('ACRIT_EXP_PAGE_TITLE_PROFILE'));
}
else {
	$APPLICATION->SetTitle(Helper::getMessage('ACRIT_EXP_PAGE_TITLE_MODULE'));
}
$APPLICATION->RestartBuffer();
Log::getInstance($strModuleID)->showLog($intProfileID, true);
die();

?>