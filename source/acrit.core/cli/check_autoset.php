<?php
define('ACRIT_EXP_CRON', true);
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS',true); 
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../../../../');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/interface/init_admin.php');
set_time_limit(0);
ignore_user_abort(true);
$strModuleID = 'acrit.core';
if(\Bitrix\Main\Loader::includeModule($strModuleID)) {
	// Nothing! :)
}
?>