<?php
define('ACRIT_EXP_CRON', true);
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS',true);
ini_set('display_errors', 0);
error_reporting(0);

# Arguments
$arArguments = require(__DIR__.'/../../include/cli/parse_arguments.php');
if(strlen($arArguments['site'])){
	define('SITE_ID', $arArguments['site']);
}

# Get real document root
$_SERVER['DOCUMENT_ROOT'] = $DOCUMENT_ROOT = realpath(__DIR__.'/../../../../../');
if(preg_match('#^(.*?)/(bitrix|local)/modules/#', reset($argv), $arMatch)){
	$_SERVER['DOCUMENT_ROOT'] = $DOCUMENT_ROOT = $arMatch[1];
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/interface/init_admin.php');
ini_set('display_errors', 0);
error_reporting(0);
set_time_limit(0);
ignore_user_abort(true);

$strCoreId = 'acrit.core';
if(\Bitrix\Main\Loader::includeModule($strCoreId)) {
	$strModuleId = $arArguments['module'];
	if(strlen($strModuleId)) {
		if(\Bitrix\Main\Loader::includeModule($strModuleId)){
			\Acrit\Core\Export\Exporter::getInstance($strModuleId)->runThread();
		}
		else{
			print 'Module '.$strModuleId.' is not installed!';
		}
	}
	else{
		print 'Empty \'module\' value in command';
	}
}
else{
	print 'Module '.$strCoreId.' is not installed.';
}

?>