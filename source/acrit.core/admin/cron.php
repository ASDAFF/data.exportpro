<?
namespace Acrit\Core;

use
	\Acrit\Core\Cli,
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Log;

# Core (part 1)
$strCoreId = 'acrit.core';
define('ADMIN_MODULE_NAME', $strCoreId);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
\Bitrix\Main\Loader::includeModule($strCoreId);
Helper::loadMessages(__FILE__);

# JSON start
Json::setHttpHeader();
$arJsonResult = Json::prepare();
$arJsonResult['Success'] = false;

# Arguments
$arGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray();
$strModuleId = $arGet['module_id'];
$strProfileId = $arGet['profile_id'];
$strCliFile = $arGet['cli_file'];
$strSchedule = $arGet['schedule'];
$strAjaxAction = $arGet['ajax_action'];
$bShowTasks = $arGet['show_tasks'] == 'Y';

#AJAX actions
if(\Bitrix\Main\Loader::includeModule($strModuleId)) {
	$strRight = $APPLICATION->getGroupRight($strModuleId);
	if($strRight != 'D'){
		$arCli = Cli::getFullCommand($strModuleId, $strCliFile, $strProfileId, Log::getInstance($strModuleId)->getLogFilename($strProfileId));
		switch($strAjaxAction){
			case 'setup':
				if($strRight == 'W'){
					Cli::deleteCronTask($strModuleId, $arCli['COMMAND_SHORT']);
					$bResult = Cli::addCronTask($strModuleId, $arCli['COMMAND'], $strSchedule);
					if($bResult){
						Log::getInstance($strModuleId)->add(Helper::getMessage('ACRIT_EXP_AJAX_CRON_SETUP_SUCCESS', array(
							'#COMMAND#' => $arCli['COMMAND'],
						)), $intProfileID);
					}
					else{
						Log::getInstance($strModuleId)->add(Helper::getMessage('ACRIT_EXP_AJAX_CRON_SETUP_ERROR', array(
							'#COMMAND#' => $arCli['COMMAND'],
						)), $intProfileID);
					}
					$arJsonResult['Success'] = true;
				}
				$arJsonResult['IsConfigured'] = Cli::isCronTaskConfigured($strModuleId, $arCli['COMMAND'], $strSchedule);
				#
				if($bShowTasks) {
					$arJsonResult['CurrentTasks'] = Helper::getHtmlObject(ACRIT_CORE, null, 'forms/cron', 'tasks', [
						'MODULE_ID' => ACRIT_PROCESSING,
						'PROFILE_ID' => 'import_clients',
						'CLI_FILE' => 'run.php',
					]);
				}
				break;
			case 'clear':
				if($strRight == 'W'){
					$arJsonResult['Success'] = !!Cli::deleteCronTask($strModuleId, $arCli['COMMAND_SHORT']);
					$arJsonResult['IsConfigured'] = Cli::isCronTaskConfigured($strModuleId, $arCli['COMMAND'], $strSchedule);
				}
				#
				if($bShowTasks) {
					$arJsonResult['CurrentTasks'] = Helper::getHtmlObject(ACRIT_CORE, null, 'forms/cron', 'tasks', [
						'MODULE_ID' => ACRIT_PROCESSING,
						'PROFILE_ID' => 'import_clients',
						'CLI_FILE' => 'run.php',
					]);
				}
				break;
		}
	}
}

# JSON finish
Json::output($arJsonResult);
die();

?>