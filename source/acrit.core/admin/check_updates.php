<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Update,
	\Acrit\Core\Json;

// Core (part 1)
$strCoreId = 'acrit.core';
define('ADMIN_MODULE_NAME', $strCoreId);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
\Bitrix\Main\Loader::includeModule($strCoreId);
set_time_limit(intVal(Helper::getOption($strCoreId, 'updates_timeout')));
IncludeModuleLangFile(__FILE__);
\CJSCore::init('jquery2');

// Start JSON
$arJsonResult = Json::prepare();

// Check updates
$strModuleId = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->get('module');
$strRegular = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->get('regular');
if(strlen($strModuleId)){
	// Updates for selected module
	ob_start();
	require __DIR__.'/../include/update_notifier/update_notifier.php';
	$arJsonResult['Success'] = true;
	$arJsonResult['HTML'] = trim(ob_get_clean());
}
elseif($strRegular == 'Y'){
	// Updates for all modules
	Update::checkUpdates();
	$arJsonResult['Success'] = true;
}

// Finish JSON
Json::output($arJsonResult);
die();

?>