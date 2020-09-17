<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application,
	Acrit\Core\Helper,
	Acrit\Core\Crm\Rest,
	Acrit\Core\Crm\CrmPortal,
	Acrit\Core\Crm\Controller,
	Acrit\Core\Crm\Settings;

CModule::IncludeModule("acrit.core");

Controller::setModuleId('acrit.exportproplus');

if (!$USER->IsAdmin()) {
	die();
}

$arRes = Rest::restToken($_REQUEST['code']);

// Add placements and event handlers
//$sync_active = Settings::get('active');
if (Controller::checkConnection() && $sync_active) {
//	Portal::regCrmHandlers();
//	Helper::setPortalPlacements();
}

if (!$arRes['error']) {
    LocalRedirect('/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid='.Controller::$MODULE_ID.'&acrit_exportproplus_tab_control_active_tab=crm');
}
else {
    echo 'Authorization error: ' . $arRes['error'];
}
