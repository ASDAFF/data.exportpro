<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Json,
	\Acrit\Core\Cli;
	

// Core (part 1)
$strCoreId = 'acrit.core';
$strModuleId = $ModuleID = preg_replace('#^.*?/([a-z0-9]+)_([a-z0-9]+).*?$#', '$1.$2', $_SERVER['REQUEST_URI']);
$strModuleCode = preg_replace('#^(.*?)\.(.*?)$#', '$2', $strModuleId);
$strModuleUnderscore = preg_replace('#^(.*?)\.(.*?)$#', '$1_$2', $strModuleId);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strModuleId.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strCoreId.'/install/demo.php');
Loc::LoadMessages(__FILE__);
\CJSCore::Init('jquery2');
$strModuleCodeLower = toLower($strModuleCode);

// Check rights
$strRight = $APPLICATION->getGroupRight($strModuleId);
if($strRight < 'R'){
	$APPLICATION->authForm(Loc::getMessage('ACCESS_DENIED'));
}

// Input data
$arGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray();
$arPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->toArray();

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
$arPlugins = Exporter::getInstance($strModuleId)->findPlugins(true);
$arPluginsPlain = Exporter::getInstance($strModuleId)->findPlugins(false);
$APPLICATION->SetTitle(Loc::getMessage('ACRIT_EXP_PAGE_TITLE'));
$intLockTime = IntVal(Helper::getOption($strModuleId, 'lock_time'));
$intLockTime = $intLockTime > 0 ? $intLockTime * 60 : 0;

// Backup
$strErrorSessionKey = 'ACRIT_EXP_BACKUP_ERROR_CODE';
if(strlen($arGet['backup'])){
	$strErrorCode = null;
	$strErrorData = null;
	$bBackupSuccess = false;
	#Backup::setModuleId($strModuleId);
	#$strTmpFile = Backup::createBackupFile($arGet['backup']);
	$strTmpFile = Helper::call($strModuleId, 'Backup', 'createBackupFile', [$arGet['backup']]);
	if(strlen($strTmpFile) && is_file($strTmpFile)) {
		#$strZipFile = Backup::fileToZip($strTmpFile);
		$strZipFile = Helper::call($strModuleId, 'Backup', 'fileToZip', [$strTmpFile]);
		if(is_file($strZipFile)){
			Helper::obRestart();
			#$bBackupSuccess = Backup::downloadFile($strZipFile);
			$bBackupSuccess = Helper::call($strModuleId, 'Backup', 'downloadFile', [$strZipFile]);
			@unlink($strTmpFile);
			@unlink($strZipFile);
			if($bBackupSuccess){
				die();
			}
		}
		else{
			if(is_file($strZipFile) && !is_writeable($strZipFile)){
				$strErrorCode = 'FILE_IS_NOT_WRITEABLE';
				$strErrorData = $strZipFile;
			}
			else{
				$strDir = pathinfo($strZipFile, PATHINFO_DIRNAME);
				if(!is_writeable($strDir)){
					$strErrorCode = 'DIR_IS_NOT_WRITEABLE';
					$strErrorData = $strDir;
				}
			}
		}
	}
	else{
		if(is_file($strTmpFile) && !is_writeable($strTmpFile)){
			$strErrorCode = 'FILE_IS_NOT_WRITEABLE';
			$strErrorData = $strTmpFile;
		}
		else{
			$strTmpDir = pathinfo($strTmpFile, PATHINFO_DIRNAME);
			if(!is_writeable($strTmpDir)){
				$strErrorCode = 'DIR_IS_NOT_WRITEABLE';
				$strErrorData = $strTmpDir;
			}
		}
	}
	if(!$bBackupSuccess) {
		if(strlen($strErrorCode)){
			$_SESSION[$strErrorSessionKey] = array(
				'TITLE' => Loc::getMessage('ACRIT_EXP_POPUP_BACKUP_ERROR'),
				'DESCR' => Loc::getMessage('ACRIT_EXP_POPUP_BACKUP_ERROR_'.$strErrorCode, [
					'#DATA#' => $strErrorData,
				]),
			);
		}
		LocalRedirect($APPLICATION->getCurPageParam('', ['backup']));
	}
}

// Start table
$sTableID = 'AcritExpProfiles';
$oSort = new \CAdminSorting($sTableID, 'SORT', 'ASC');
$lAdmin = new \CAdminList($sTableID, $oSort);

// Filter
function CheckFilter($lAdmin, $arFilterFields) {
	foreach ($arFilterFields as $f) {
		global $f;
	}
	return count($lAdmin->arFilterErrors)==0;
}
$arFilterFields = Array(
	'find_ID',
	'find_ACTIVE',
	'find_LOCKED',
	'find_NAME',
	'find_FORMAT',
	'find_AUTO_GENERATE',
	'find_SITE_ID',
	'find_DOMAIN',
	'find_IS_HTTPS',
	'find_DATE_CREATED',
	'find_DATE_MODIFIED',
);
$lAdmin->InitFilter($arFilterFields);
if (CheckFilter($lAdmin, $arFilterFields)) {
	$arFilter = array();
	if(!empty($find_ID))
		$arFilter['ID'] = $find_ID;
	if(in_array($find_ACTIVE, array('Y', 'N')))
		$arFilter['ACTIVE'] = $find_ACTIVE;
	if(in_array($find_LOCKED, array('Y', 'N'))) {
		$obNow = new \DateTime();
		$strDate = $obNow->modify('-'.$intLockTime.' second')->format(\CDatabase::dateFormatToPHP(FORMAT_DATETIME));
		unset($obNow);
		if($find_LOCKED == 'Y') {
			$arFilter['LOCKED'] = 'Y';
			if($intLockTime > 0){
				$arFilter['>DATE_LOCKED'] = $strDate;
			}
		}
		elseif($find_LOCKED == 'N') {
			$arFilter = array(
				'LOGIC' => 'OR',
				array('LOCKED' => 'N'),
			);
			if($intLockTime > 0){
				$arFilter[] = array('<DATE_LOCKED' => $strDate);
			}
		}
	}
	if(!empty($find_NAME))
		$arFilter['%NAME'] = $find_NAME;
	if(!empty($find_FORMAT)) {
		$arFormat = explode('.', $find_FORMAT);
		if(count($arFormat)==2) {
			$arFilter['PLUGIN'] = $arFormat[0];
			$arFilter['FORMAT'] = $arFormat[1];
		}
		elseif(count($arFormat)==1) {
			$arFilter['PLUGIN'] = $find_FORMAT;
		}
	}
	if(in_array($find_AUTO_GENERATE, array('Y', 'N')))
		$arFilter['AUTO_GENERATE'] = $find_AUTO_GENERATE;
	if(strlen($find_SITE_ID))
		$arFilter['SITE_ID'] = $find_SITE_ID;
	//
	if(!empty($find_DATE_CREATED_from))
		$arFilter['>=DATE_CREATED'] = $find_DATE_CREATED_from;
	if(!empty($find_DATE_CREATED_to))
		$arFilter['<=DATE_CREATED'] = $find_DATE_CREATED_to;
	//
	if(!empty($find_DATE_MODIFIED_from))
		$arFilter['>=DATE_MODIFIED'] = $find_DATE_MODIFIED_from;
	if(!empty($find_DATE_MODIFIED_to))
		$arFilter['<=DATE_MODIFIED'] = $find_DATE_MODIFIED_to;;
}

// Processing with actions
if($lAdmin->EditAction() && $strRight == 'W') {
	@set_time_limit(0);
	foreach($FIELDS as $ID => $arFields) {
		if(!$lAdmin->IsUpdated($ID)) {
			continue;
		}
		$DB->StartTransaction();
		$ID = IntVal($ID);
		$resProfile = Helper::call($strModuleId, 'Profile', 'getList', [[
			'filter' => array(
				'ID' => $ID,
			),
		]]);
		if($arProfile = $resProfile->fetch()){
			if(!Helper::call($strModuleId, 'Profile', 'update', [$ID, $arFields])) {
				$lAdmin->AddGroupError(GetMessage('rub_save_error'), $ID);
				$DB->Rollback();
			}
		}
		else {
			$lAdmin->AddGroupError(GetMessage('rub_save_error').' '.GetMessage('rub_no_rubric'), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}
if($lAdmin->GroupAction() && $strRight == 'W') {
	if(is_array($_REQUEST['ID'])){
		$arID = [];
		foreach($_REQUEST['ID'] as $intID){
			$arID[] = IntVal($intID);
		}
	}
	else{
		$arID = [IntVal($_REQUEST['ID'])];
	}
	$strGroupAction = $_REQUEST['action'];
	$strGroupTarget = $_REQUEST['action_target'];
	@set_time_limit(0);
  if($strGroupTarget == 'selected') {
		$resProfiles = Helper::call($strModuleId, 'Profile', 'getList', [[
			'filter' => $arFilter,
			'select' => array(
				'ID',
			),
		]]);
    while($arProfile = $resProfiles->fetch()) {
			$arID[] = $arProfile['ID'];
		}
		$arID = array_unique($arID);
		unset($resProfiles, $arProfile);
  }
	
  foreach($arID as $ID) {
    $ID = IntVal($ID);
    if($ID <= 0) {
			continue;
		}
		$resProfile = Helper::call($strModuleId, 'Profile', 'getList', [[
			'filter' => array(
				'ID' => $ID,
			),
			'select' => array(
				'ID',
				'NAME',
			),
			'limit' => 1,
		]]);
		if($arProfile = $resProfile->fetch()){
			$DB->StartTransaction();
			$bSuccess = false;
			$strError = '';
			switch($strGroupAction) {
				case 'delete':
					$obResult = Helper::call($strModuleId, 'Profile', 'delete', [$ID]);
					$bSuccess = $obResult->isSuccess();
					if(!$bSuccess){
						$strError = Loc::getMessage('ACRIT_EXP_GROUP_ERROR_DELETE', array('#ID#' => $ID));
					}
					break;
				case 'activate':
				case 'deactivate':
					$arProfileFields = array(
						'ACTIVE' => $strGroupAction == 'activate' ? 'Y' : 'N',
					);
					$obResult = Helper::call($strModuleId, 'Profile', 'update', [$ID, $arProfileFields]);
					$bSuccess = $obResult->isSuccess();
					if(!$bSuccess) {
						$strError = Loc::getMessage('ACRIT_EXP_GROUP_ERROR_UPDATE', array('#ID#' => $ID));
					}
					break;
				case 'unlock':
					$obResult = Helper::call($strModuleId, 'Profile', 'unlock', [$ID]);
					$bSuccess = $obResult->isSuccess();
					if(!$bSuccess) {
						$strError = Loc::getMessage('ACRIT_EXP_GROUP_ERROR_UNLOCK', array('#ID#' => $ID));
					}
					break;
				case 'uncron':
					$bSuccess = Cli::deleteProfileCron($strModuleId, $ID, 'export.php');
					if(!$bSuccess) {
						$strError = Loc::getMessage('ACRIT_EXP_GROUP_ERROR_UNCRON', array('#ID#' => $ID));
					}
					break;
			}
			if($bSuccess){
				$DB->Commit();
			}
			else {
				$DB->Rollback();
				if(strlen($strError)){
					$lAdmin->AddGroupError($strError);
				}
			}
		}
		else {
			$lAdmin->AddGroupError(Loc::getMessage('ACRIT_EXP_GROUP_ERROR_NOT_FOUND', array('#ID#' => $ID)));
		}
		unset($resProfile, $arProfile, $arProfileFields);
  }
}

// Ajax actions
$strAjaxAction = $arGet['ajax_action'];
if(strlen($strAjaxAction)){
	header('Content-Type: application/json; charset='.(Helper::isUtf()?'utf-8':'windows-1251'));
	ini_set('display_errors',0);
	error_reporting(~E_ALL);
	$arJsonResult = array();
	$APPLICATION->RestartBuffer();
	switch($strAjaxAction){
		// Load popup restore
		case 'load_popup_backup_restore':
			ob_start();
			require __DIR__.'/include/popups/backup_restore.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Load popup wizard quick start
		case 'load_popup_wizard_quick_start':
			ob_start();
			require __DIR__.'/include/popups/wizard_quick_start.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Save popup wizard quick start
		case 'wizard_quick_start_process':
			$bSaveWizardQuickStart = true;
			ob_start();
			require __DIR__.'/include/popups/wizard_quick_start.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Do restore!
		case 'backup_restore':
			$bSuccess = false;
			if($strRight == 'W'){
				$arFile = $_FILES['backup'];
				#$bSuccess = Backup::restoreFromBackupFile($arFile['tmp_name'], $arPost['mode']);
				$bSuccess = Helper::call($strModuleId, 'Backup', 'restoreFromBackupFile', [$arFile['tmp_name'], $arPost['mode']]);
			}
			if($bSuccess) {
				$arJsonResult['HTML'] = Helper::showSuccess(Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_SUCCESS'));
			}
			else {
				$arJsonResult['HTML'] = Helper::showError(Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_ERROR'));
			}
			break;
		// Delete all profiles data
		case 'profiles_delete_all':
			$arJsonResult['Success'] = false;
			if($strRight == 'W'){
				#Backup::deleteProfilesDataAll();
				Helper::call($strModuleId, 'Backup', 'deleteProfilesDataAll');
				$arJsonResult['Success'] = true;
				$arJsonResult['HTML'] = Helper::showSuccess(Loc::getMessage('ACRIT_EXP_BACKUP_DELETED_ALL'));
			}
			break;
	}
	print Json::encode($arJsonResult);
	ini_set('display_errors', 0); // Against 'Warning:  A non-numeric value encountered in /home/bitrix/www/bitrix/modules/perfmon/classes/general/keeper.php on line 321'
	die();
}

// Check database
Helper::checkDatabase($strModuleId);

// Get items list
$tProfile = $strModuleUnderscore.'_profile'; // such in ORM! Not the real table name
$tHistory = Helper::call($strModuleId, 'History', 'getTableName');
$arQuery = array(
	'select' => array(
		'*',
		new \Bitrix\Main\Entity\ExpressionField(
			'DATE_START',
			"(SELECT `{$tHistory}`.`DATE_START` FROM `{$tHistory}` WHERE `{$tProfile}`.`ID`=`{$tHistory}`.`PROFILE_ID` ORDER BY `ID` DESC LIMIT 1)",
			'ID'
		),
		new \Bitrix\Main\Entity\ExpressionField(
			'DATE_END',
			"(SELECT `{$tHistory}`.`DATE_END` FROM `{$tHistory}` WHERE `{$tProfile}`.`ID`=`{$tHistory}`.`PROFILE_ID` ORDER BY `ID` DESC LIMIT 1)",
			'ID'
		),
		new \Bitrix\Main\Entity\ExpressionField(
			'TIME_TOTAL',
			"(SELECT `{$tHistory}`.`TIME_TOTAL` FROM `{$tHistory}` WHERE `{$tProfile}`.`ID`=`{$tHistory}`.`PROFILE_ID` ORDER BY `ID` DESC LIMIT 1)",
			'ID'
		),
		new \Bitrix\Main\Entity\ExpressionField(
			'TIME_GENERATED',
			"(SELECT `{$tHistory}`.`TIME_GENERATED` FROM `{$tHistory}` WHERE `{$tProfile}`.`ID`=`{$tHistory}`.`PROFILE_ID` ORDER BY `ID` DESC LIMIT 1)",
			'ID'
		),
		new \Bitrix\Main\Entity\ExpressionField(
			'COUNT_SUCCESS',
			"(SELECT `{$tHistory}`.`ELEMENTS_Y` + `{$tHistory}`.`OFFERS_Y` FROM `{$tHistory}` WHERE `{$tProfile}`.`ID`=`{$tHistory}`.`PROFILE_ID` ORDER BY `ID` DESC LIMIT 1)",
			'ID'
		),
		new \Bitrix\Main\Entity\ExpressionField(
			'COUNT_ERROR',
			"(SELECT `{$tHistory}`.`ELEMENTS_N` + `{$tHistory}`.`OFFERS_N` FROM `{$tHistory}` WHERE `{$tProfile}`.`ID`=`{$tHistory}`.`PROFILE_ID` ORDER BY `ID` DESC LIMIT 1)",
			'ID'
		),
	),
	'order' => array($by => $order),
	'filter' => $arFilter,
);
$resData = Helper::call($strModuleId, 'Profile', 'getList', [$arQuery]);
$resData = new \CAdminResult($resData, $sTableID);
$resData->NavStart();
$lAdmin->NavText($resData->GetNavPrint(''));
$intProfilesCount = IntVal($resData->NavRecordCount);

//
$bCrontabCanAutoSet = Cli::canAutoSet();

// Add headers
$lAdmin->AddHeaders(array(
  array(
	  'id' => 'ID',
    'content' => getMessage('ACRIT_EXP_HEADER_ID'),
    'sort' => 'ID',
    'align' => 'right',
    'default' => true,
  ),
  array(
	  'id' => 'LOCKED',
    'content' => getMessage('ACRIT_EXP_HEADER_LOCKED'),
    #'sort' => 'LOCKED',
		'align' => 'left',
    'default' => true,
  ),
  array(
	  'id' => 'ACTIVE',
    'content' => getMessage('ACRIT_EXP_HEADER_ACTIVE'),
    'sort' => 'ACTIVE',
    'align' => 'center',
    'default' => true,
  ),
  array(
	  'id' => 'SORT',
    'content' => getMessage('ACRIT_EXP_HEADER_SORT'),
    'sort' => 'SORT',
		'align' => 'right',
    'default' => true,
  ),
  array(
	  'id' => 'NAME',
    'content' => getMessage('ACRIT_EXP_HEADER_NAME'),
    'sort' => 'NAME',
    'align' => 'left',
    'default' => true,
  ),
  array(
	  'id' => 'DESCRIPTION',
    'content' => getMessage('ACRIT_EXP_HEADER_DESCRIPTION'),
    'sort' => 'DESCRIPTION',
    'align' => 'left',
    'default' => false,
  ),
  array(
	  'id' => 'FORMAT',
    'content' => getMessage('ACRIT_EXP_HEADER_FORMAT'),
    'sort' => 'FORMAT',
		'align' => 'left',
    'default' => true,
  ),
  array(
	  'id' => 'EXPORT_FILE_NAME',
    'content' => getMessage('ACRIT_EXP_HEADER_EXPORT_FILE_NAME'),
		'align' => 'left',
    'default' => true,
  ),
  array(
	  'id' => 'SITE_ID',
    'content' => getMessage('ACRIT_EXP_HEADER_SITE_ID'),
    'sort' => 'SITE_ID',
		'align' => 'left',
    'default' => false,
  ),
  array(
	  'id' => 'DOMAIN',
    'content' => getMessage('ACRIT_EXP_HEADER_DOMAIN'),
    'sort' => 'DOMAIN',
		'align' => 'left',
    'default' => false,
  ),
  array(
	  'id' => 'IS_HTTPS',
    'content' => getMessage('ACRIT_EXP_HEADER_IS_HTTPS'),
    'sort' => 'IS_HTTPS',
		'align' => 'center',
    'default' => false,
  ),
  array(
	  'id' => 'AUTO_GENERATE',
    'content' => getMessage('ACRIT_EXP_HEADER_AUTO_GENERATE'),
    'sort' => 'AUTO_GENERATE',
		'align' => 'center',
    'default' => false,
  ),
  array(
	  'id' => 'AUTO_CRON',
    'content' => getMessage('ACRIT_EXP_HEADER_AUTO_CRON'),
		'align' => 'center',
    'default' => $bCrontabCanAutoSet,
  ),
  array(
	  'id' => 'DATE_CREATED',
    'content' => getMessage('ACRIT_EXP_HEADER_DATE_CREATED'),
    'sort' => 'DATE_CREATED',
		'align' => 'left',
    'default' => false,
  ),
  array(
	  'id' => 'DATE_MODIFIED',
    'content' => getMessage('ACRIT_EXP_HEADER_DATE_MODIFIED'),
    'sort' => 'DATE_MODIFIED',
		'align' => 'left',
    'default' => true,
  ),
  array(
	  'id' => 'DATE_START', // Dynamic column
    'content' => getMessage('ACRIT_EXP_HEADER_DATE_START'),
    'sort' => 'DATE_START',
		'align' => 'left',
    'default' => true,
  ),
  array(
	  'id' => 'DATE_END', // Dynamic column
    'content' => getMessage('ACRIT_EXP_HEADER_DATE_END'),
    'sort' => 'DATE_END',
		'align' => 'left',
    'default' => true,
  ),
  array(
	  'id' => 'DATE_LOCKED',
    'content' => getMessage('ACRIT_EXP_HEADER_DATE_LOCKED'),
    'sort' => 'DATE_LOCKED',
		'align' => 'left',
    'default' => false,
  ),
  array(
	  'id' => 'TIME_GENERATED', // Dynamic column
    'content' => getMessage('ACRIT_EXP_HEADER_TIME_GENERATED'),
    'sort' => 'TIME_GENERATED',
		'align' => 'left',
    'default' => false,
  ),
  array(
	  'id' => 'TIME_TOTAL', // Dynamic column
    'content' => getMessage('ACRIT_EXP_HEADER_TIME_TOTAL'),
    'sort' => 'TIME_TOTAL',
		'align' => 'left',
    'default' => true,
  ),
  array(
	  'id' => 'COUNT_SUCCESS', // Dynamic column
    'content' => getMessage('ACRIT_EXP_HEADER_COUNT_SUCCESS'),
    'sort' => 'COUNT_SUCCESS',
		'align' => 'left',
    'default' => true,
  ),
  array(
	  'id' => 'COUNT_ERROR', // Dynamic column
    'content' => getMessage('ACRIT_EXP_HEADER_COUNT_ERROR'),
    'sort' => 'COUNT_ERROR',
		'align' => 'left',
    'default' => true,
  ),
));

// Build items list
while ($arRow = $resData->NavNext(true, 'f_')) {
	$arRow['PARAMS'] = unserialize($arRow['PARAMS']);
	$arPlugin = Exporter::getInstance($strModuleId)->getPluginInfo($f_FORMAT);
	$obPlugin = null;
	if(is_array($arPlugin)) {
		$obPlugin = new $arPlugin['CLASS']($strModuleId);
		$obPlugin->setProfileArray($arRow);
	}
	//
  $obRow = &$lAdmin->AddRow($f_ID, $arRow);
	//
	$bIsOnCron = Cli::isProfileOnCron($strModuleId, $f_ID, 'export.php', null, true);
	// ID
	$obRow->AddViewField('ID', '<a href="'.$strModuleUnderscore.'_new_edit.php?ID='.$f_ID.'&lang='.LANGUAGE_ID.'">'.$f_ID.'</a>');
	// LOCKED
	$bLocked = Helper::call($strModuleId, 'Profile', 'isLocked', [$f_ID]);
	$obRow->AddViewField('LOCKED', '<img src="/bitrix/themes/.default/images/lamp/'.($bLocked?'red':'green').'.gif" width="14" height="14" alt="" />');
	// ACTIVE
	$obRow->AddCheckField('ACTIVE', $f_ACTIVE);
  // NAME
  $obRow->AddInputField('NAME', array('SIZE' => '20'));
  $obRow->AddViewField('NAME', '<a href="'.$strModuleUnderscore.'_new_edit.php?ID='.$f_ID.'&lang='.LANGUAGE_ID.'">'.$f_NAME.'</a>');
	// DESCRIPTION
	$sHTML = '<textarea rows="4" cols="40" name="FIELDS['.$f_ID.'][DESCRIPTION]">'.htmlspecialchars($f_DESCRIPTION).'</textarea>';
	$obRow->AddEditField('DESCRIPTION', $sHTML);
	$obRow->AddViewField('DESCRIPTION', $f_DESCRIPTION);
  // SORT
  $obRow->AddInputField('SORT', array('SIZE' => 5));
  // SITE_ID
	$strSiteID = '<a href="/bitrix/admin/site_edit.php?lang='.LANGUAGE_ID.'&LID='.$f_SITE_ID.'" target="_blank">'.$f_SITE_ID.'</a>';
  $obRow->AddViewField('SITE_ID', $strSiteID);
  // DOMAIN
  $obRow->AddViewField('DOMAIN', $f_DOMAIN);
  // IS_HTTPS
  $obRow->AddCheckField('IS_HTTPS', $f_IS_HTTPS);
	// FORMAT
	$strIcon = '';
	$strName = '';
	if(is_array($arPlugin)) {
		if(is_array($arPlugin)) {
			$strIcon = $arPlugin['ICON_BASE64'];
			$strName = $arPlugin['NAME'];
			if(!strlen($strIcon) && $arPlugin['IS_SUBCLASS'] && strlen($arPlugin['PARENT'])){
				$strIcon = $arPluginsPlain[$arPlugin['PARENT']]['ICON_BASE64'];
			}
		}
	}
	$strFormat = '<span class="acrit-exp-profiles-list-plugin">';
	if(strlen($strIcon)){
		$strFormat .= '<img src="'.$strIcon.'" alt="" >';
	}
	if(strlen($strName)){
		$strFormat .= '<span>'.$strName.'</span>';
	}
	$strFormat .= '</span>';
	$obRow->AddViewField('FORMAT', $strFormat);
	// EXPORT_FILE_NAME
	$obRow->AddViewField('EXPORT_FILE_NAME', is_object($obPlugin) ? $obPlugin->showFileOpenLink(false, true) : '');
	// AUTO_GENERATE
	$obRow->AddCheckField('AUTO_GENERATE', $f_AUTO_GENERATE);
	// AUTO_CRON
	$strMessage = Loc::getMessage('MAIN_'.($bIsOnCron?'YES':'NO'));
	$strColor = $bIsOnCron ? 'green' : 'initial';
	$strFontWeight = $bIsOnCron ? 'bold' : 'normal';
	$obRow->AddViewField('AUTO_CRON', '<span style="color:'.$strColor.';font-weight:'.$strFontWeight.'">'.$strMessage.'</span>');
	// DATE_CREATED
	$obRow->AddViewField('DATE_CREATED', $f_DATE_CREATED);
	// DATE_MODIFIED
	$obRow->AddViewField('DATE_MODIFIED', $f_DATE_MODIFIED);
	// DATE_START
	$obRow->AddViewField('DATE_START', $f_DATE_START);
	// TIME_GENERATED
	$obRow->AddViewField('TIME_GENERATED', Helper::formatElapsedTime($f_TIME_GENERATED));
	// TIME_TOTAL
	$obRow->AddViewField('TIME_TOTAL', Helper::formatElapsedTime($f_TIME_TOTAL));
	// COUNT_SUCCESS
	$obRow->AddViewField('COUNT_SUCCESS', $f_COUNT_SUCCESS);
	// COUNT_ERROR
	$obRow->AddViewField('COUNT_ERROR', $f_COUNT_ERROR);
	// Build context menu
  $arActions = array();
  $arActions[] = array(
    'ICON' => 'edit',
    'DEFAULT' => true,
    'TEXT' => getMessage('ACRIT_EXP_CONTEXT_PROFILE_EDIT'),
    'ACTION' => $lAdmin->ActionRedirect($strModuleUnderscore.'_new_edit.php?ID='.$f_ID.'&lang='.LANGUAGE_ID),
  );
  $arActions[] = array(
    'ICON' => 'copy',
    'DEFAULT' => false,
    'TEXT' => getMessage('ACRIT_EXP_CONTEXT_PROFILE_COPY'),
    'ACTION' => $lAdmin->ActionRedirect($strModuleUnderscore.'_new_edit.php?ID='.$f_ID.'&copy=Y&lang='.LANGUAGE_ID),
  );
	$arActions[] = array(
		'ICON' => 'delete',
		'DEFAULT' => false,
		'TEXT' => getMessage('ACRIT_EXP_CONTEXT_PROFILE_DELETE'),
		'ACTION' => "if(confirm('".sprintf(getMessage('ACRIT_EXP_CONTEXT_PROFILE_DELETE_CONFIRM'), $f_NAME)."')&&'u254'=='u254') ".$lAdmin->ActionDoGroup($f_ID, 'delete'),
	);
	$arActions[] = array('SEPARATOR' => true);
	$arActions[] = array(
		'ICON' => 'pack',
		'DEFAULT' => false,
		'TEXT' => getMessage('ACRIT_EXP_CONTEXT_PROFILE_BACKUP'),
		'ACTION' => $lAdmin->ActionRedirect($strModuleUnderscore.'_new_edit.php?ID='.$f_ID.'&backup=Y&lang='.LANGUAGE_ID),
	);
	$arActions[] = array('SEPARATOR' => true);
	if($f_ACTIVE=='N') {
		$arActions[] = array(
			'ICON' => 'activate',
			'DEFAULT' => false,
			'TEXT' => getMessage('ACRIT_EXP_CONTEXT_PROFILE_ACTIVATE'),
			'ACTION' => $lAdmin->ActionDoGroup($f_ID, 'activate'),
		);
	}
	else {
		$arActions[] = array(
			'ICON' => 'deactivate',
			'DEFAULT' => false,
			'TEXT' => getMessage('ACRIT_EXP_CONTEXT_PROFILE_DEACTIVATE'),
			'ACTION' => $lAdmin->ActionDoGroup($f_ID, 'deactivate'),
		);
	}
	if($f_LOCKED=='Y') {
		$arActions[] = array('SEPARATOR' => true);
		$arActions[] = array(
			'ICON' => 'unlock',
			'DEFAULT' => false,
			'TEXT' => getMessage('ACRIT_EXP_CONTEXT_PROFILE_UNLOCK'),
			'ACTION' => $lAdmin->ActionDoGroup($f_ID, 'unlock'),
		);
	}
	if($bIsOnCron && $bCrontabCanAutoSet){
		$arActions[] = array('SEPARATOR' => true);
		$arActions[] = array(
			'ICON' => 'uncron',
			'DEFAULT' => false,
			'TEXT' => getMessage('ACRIT_EXP_CONTEXT_PROFILE_REMOVE_CRONTAB'),
			'ACTION' => $lAdmin->ActionDoGroup($f_ID, 'uncron'),
		);
	}
  $obRow->AddActions($arActions);
	#
	unset($arPlugin, $obPlugin);
}

// List Footer
$lAdmin->AddFooter(
  array(
    array('title' => GetMessage('MAIN_ADMIN_LIST_SELECTED'), 'value' => $resData->SelectedRowsCount()),
    array('title' => GetMessage('MAIN_ADMIN_LIST_CHECKED'), 'value' => '0', 'counter' => true),
  )
);
$arGroupActions = array(
  'delete' => GetMessage('MAIN_ADMIN_LIST_DELETE'),
  'activate' => GetMessage('MAIN_ADMIN_LIST_ACTIVATE'),
  'deactivate' => GetMessage('MAIN_ADMIN_LIST_DEACTIVATE'),
  'unlock' => getMessage('ACRIT_EXP_GROUP_UNLOCK'),
);
if($bCrontabCanAutoSet){
	$arGroupActions['uncron'] = getMessage('ACRIT_EXP_GROUP_UNCRON');
}
$lAdmin->AddGroupActionTable($arGroupActions);

// Context menu
$arBackupSubmenu = array();
if($intProfilesCount) {
	$arBackupSubmenu[] = array(
		'TEXT' => getMessage('ACRIT_EXP_TOOLBAR_BACKUP_CREATE'),
		'ICON' => 'pack',
		'ACTION' => 'acritExpDoBackup("'.$sTableID.'");',
	);
}
$arBackupSubmenu[] = array(
	'TEXT' => getMessage('ACRIT_EXP_TOOLBAR_BACKUP_RESTORE'),
	'ICON' => 'unpack',
	'ACTION' => 'AcritExpPopupRestore.Open();',
);
$arContext = array(
  array(
    'TEXT' => getMessage('ACRIT_EXP_TOOLBAR_ADD'),
    'LINK' => $strModuleUnderscore.'_new_edit.php?lang='.LANGUAGE_ID,
    'ICON' => 'btn_new',
  ),
  array(
    'TEXT' => getMessage('ACRIT_EXP_TOOLBAR_BACKUP'),
    'ICON' => 'btn_backup',
		'MENU' => $arBackupSubmenu,
  ),
  array(
		'TEXT' => getMessage('ACRIT_EXP_TOOLBAR_WIZARD_QUICK_START'),
		'ICON' => 'btn_wizard',
		'ONCLICK' => 'AcritExpPopupWizardQuickStart.Open();',
  ),
);
$lAdmin->AddAdminContextMenu($arContext);

// Start output
$lAdmin->CheckListMode();

// Core (part 2)
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

// Demo
acritShowDemoNotice($strModuleId);

// Text definitions for popup
ob_start();
?><script>
var acritExpModuleVersion = '<?=Helper::getModuleVersion($strModuleId);?>';
var acritExpCoreVersion = '<?=Helper::getModuleVersion($strCoreId);?>';
BX.message({
	// General
	ACRIT_EXP_POPUP_LOADING: '<?=Loc::getMessage('ACRIT_EXP_POPUP_LOADING');?>',
	// Popup: backup/restore
	ACRIT_EXP_POPUP_RESTORE_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_TITLE');?>',
	ACRIT_EXP_POPUP_RESTORE_SAVE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_SAVE');?>',
	ACRIT_EXP_POPUP_RESTORE_CLOSE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_CLOSE');?>',
	ACRIT_EXP_POPUP_RESTORE_WRONG_FILE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_WRONG_FILE');?>',
	ACRIT_EXP_POPUP_RESTORE_NO_FILE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_NO_FILE');?>',
	ACRIT_EXP_POPUP_RESTORE_RESTORE_ERROR: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_RESTORE_ERROR');?>',
	// Popup: wizard quick start
	ACRIT_EXP_POPUP_WIZARD_QUICK_START_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_WIZARD_QUICK_START_TITLE');?>',
	ACRIT_EXP_POPUP_WIZARD_QUICK_START_NEXT: '<?=Loc::getMessage('ACRIT_EXP_POPUP_WIZARD_QUICK_START_NEXT');?>',
	ACRIT_EXP_POPUP_WIZARD_QUICK_START_PREV: '<?=Loc::getMessage('ACRIT_EXP_POPUP_WIZARD_QUICK_START_PREV');?>',
	ACRIT_EXP_POPUP_WIZARD_QUICK_START_SUBMIT: '<?=Loc::getMessage('ACRIT_EXP_POPUP_WIZARD_QUICK_START_SUBMIT');?>',
	ACRIT_EXP_POPUP_WIZARD_QUICK_START_CLOSE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_WIZARD_QUICK_START_CLOSE');?>',
	ACRIT_EXP_POPUP_WIZARD_QUICK_START_FINISH: '<?=Loc::getMessage('ACRIT_EXP_POPUP_WIZARD_QUICK_START_FINISH');?>',
	ACRIT_EXP_POPUP_WIZARD_QUICK_START_NO_PLUGIN: '<?=Loc::getMessage('ACRIT_EXP_POPUP_WIZARD_QUICK_START_NO_PLUGIN');?>',
	ACRIT_EXP_POPUP_WIZARD_QUICK_START_NO_IBLOCK: '<?=Loc::getMessage('ACRIT_EXP_POPUP_WIZARD_QUICK_START_NO_IBLOCK');?>'
});
</script><?
$strJs = ob_get_clean();
\Bitrix\Main\Page\Asset::GetInstance()->AddString($strJs, true, \Bitrix\Main\Page\AssetLocation::AFTER_CSS);

// JS
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.$strCoreId.'/jquery.acrit.hotkey.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.$strCoreId.'/export/profile_list.js');

// Output filter
$oFilter = new \CAdminFilter(
  $sTableID.'_filter',
  array(
		getMessage('ACRIT_EXP_FILTER_ACTIVE'),
		getMessage('ACRIT_EXP_FILTER_LOCKED'),
		getMessage('ACRIT_EXP_FILTER_NAME'),
		getMessage('ACRIT_EXP_FILTER_FORMAT'),
		getMessage('ACRIT_EXP_FILTER_AUTO_GENERATE'),
		getMessage('ACRIT_EXP_FILTER_SITE_ID'),
		getMessage('ACRIT_EXP_FILTER_DATE_CREATED'),
		getMessage('ACRIT_EXP_FILTER_DATE_MODIFIED'),
  )
);

// Filename conflicts notifier
$arFilenames = [];
$arProfilesAll = Helper::call($strModuleId, 'Profile', 'getProfiles', [[], [], false, false, ['ID', 'PARAMS']]);
if(is_array($arProfilesAll)){
	foreach($arProfilesAll as $arProfile){
		if(isset($arProfile['PARAMS']['EXPORT_FILE_NAME'])){
			$strFilename = Helper::path(trim($arProfile['PARAMS']['EXPORT_FILE_NAME']));
			if(strlen($strFilename) && substr($strFilename, 0, 1) == '/' && substr($strFilename, 1, 2) != '/'){
				if(isset($arFilenames[$strFilename])){
					$arFilenames[$strFilename] = array_merge($arFilenames[$strFilename], [$arProfile['ID']]);
				}
				else{
					$arFilenames[$strFilename] = [$arProfile['ID']];
				}
			}
		}
	}
}
foreach($arFilenames as $strFilename => $arProfilesId){
	if(count($arProfilesId) == 1) {
		unset($arFilenames[$strFilename]);
	}
}
if(!empty($arFilenames)){
	$strHtml = '<ul>';
	foreach($arFilenames as $strFilename => $arProfilesId){
		foreach($arProfilesId as $key => $intProfileId){
			$arProfilesId[$key] = sprintf('<a href="/bitrix/admin/acrit_%s_new_edit.php?ID=%d&lang=%s" target="_blank">%d</a>',
				$strModuleCodeLower, $intProfileId, LANGUAGE_ID, $intProfileId);
		}
		/*
		if(is_file(Helper::root().$strFilename)){
			$strFilename = sprintf('<a href="%1$s" target="_blank">%1$s</a>', $strFilename);
		}
		*/
		$strHtml .= Helper::getMessage('ACRIT_EXP_FILENAME_CONFLICTS_ITEM', [
			'#FILENAME#' => $strFilename,
			'#PROFILES#' => implode(', ', $arProfilesId),
		]);
	}
	$strHtml .= '<ul>';
	print Helper::showError(Helper::getMessage('ACRIT_EXP_FILENAME_CONFLICTS_TITLE'), $strHtml);
}
?>

<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
	<?$oFilter->Begin();?>
	<tr>
		<td><b><?=getMessage('ACRIT_EXP_FILTER_ID')?>:</b></td>
		<td>
			<input type="text" size="25" name="find_ID" value="<?=htmlspecialcharsbx($find_ID);?>" />
		</td>
	</tr>
	<tr>
		<td><?=getMessage('ACRIT_EXP_FILTER_ACTIVE')?>:</td>
		<td>
			<?
			$arActiveValues = array(
				'reference' => array(
					Loc::getMessage('MAIN_YES'),
					Loc::getMessage('MAIN_NO'),
				),
				'reference_id' => array('Y', 'N'),
			);
			print SelectBoxFromArray('find_ACTIVE', $arActiveValues, $find_ACTIVE, Loc::getMessage('MAIN_ALL'), '');
			?>
		</td>
	</tr>
	<tr>
		<td><?=getMessage('ACRIT_EXP_FILTER_LOCKED')?>:</td>
		<td>
			<?
			$arActiveValues = array(
				'reference' => array(
					Loc::getMessage('MAIN_YES'),
					Loc::getMessage('MAIN_NO'),
				),
				'reference_id' => array('Y', 'N'),
			);
			print SelectBoxFromArray('find_LOCKED', $arActiveValues, $find_LOCKED, Loc::getMessage('MAIN_ALL'), '');
			?>
		</td>
	</tr>
	<tr>
		<td><?=getMessage('ACRIT_EXP_FILTER_NAME')?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_NAME" value="<?=htmlspecialcharsbx($find_NAME);?>" /></td>
	</tr>
	<tr>
		<td><?=getMessage('ACRIT_EXP_FILTER_FORMAT')?>:</td>
		<td>
			<select name="find_FORMAT" id="find_FORMAT" class="adm-select">
				<?
				foreach(Exporter::getInstance($strModuleId)->findPlugins(true) as $strPlugin => $arPlugin){
					$strValue = $arPlugin['CODE'].'.%';
					$bSel = ($find_FORMAT == $strValue ? true : false);
					?>
					<option value="<?=$strValue;?>"<?if($bSel):?> selected="selected"<?endif?>>
						<?=$arPlugin['NAME'];?>
					</option>
					<?
					if(is_array($arPlugin['FORMATS']) && count($arPlugin['FORMATS']) > 1){
						foreach($arPlugin['FORMATS'] as $arFormat){
							$strValue = $arPlugin['CODE'].'.'.$arFormat['CODE'];
							$bSel = ($find_FORMAT == $strValue ? true : false);
							?>
							<option value="<?=$strValue;?>"<?if($bSel):?> selected="selected"<?endif?>>
								. . <?=$arFormat['NAME'];?>
							</option>
							<?
						}
					}
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?=getMessage('ACRIT_EXP_FILTER_AUTO_GENERATE')?>:</td>
		<td>
			<?
			$arAutoGenerateValues = array(
				'reference' => array(
					Loc::getMessage('MAIN_YES'),
					Loc::getMessage('MAIN_NO'),
				),
				'reference_id' => array('Y', 'N'),
			);
			print SelectBoxFromArray('find_AUTO_GENERATE', $arAutoGenerateValues, $find_AUTO_GENERATE, Loc::getMessage('MAIN_ALL'), '');
			?>
		</td>
	</tr>
	<tr>
		<td><?=getMessage('ACRIT_EXP_FILTER_SITE_ID')?>:</td>
		<td>
			<?
			$arSites = Helper::getSitesList();
			?>
			<select name="find_SITE_ID">
				<option value=""><?=GetMessage('MAIN_ALL');?></option>
				<?foreach ($arSites as $arSite):?>
					<option value="<?=$arSite['LID']?>"<?if($find_SITE_ID==$arSite['LID']):?> selected="selected"<?endif?>>
						[<?=$arSite['ID']?>] <?=$arSite['NAME']?>
					</option>
				<?endforeach?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?=getMessage('ACRIT_EXP_FILTER_DATE_CREATED')?>:</td>
		<td><?=CalendarPeriod('find_DATE_CREATED_from', htmlspecialcharsbx($find_DATE_CREATED_from), 'find_DATE_CREATED_to', htmlspecialcharsbx($find_DATE_CREATED_to), 'find_form', 'Y')?></td>
	</tr>
	<tr>
		<td><?=getMessage('ACRIT_EXP_FILTER_DATE_MODIFIED')?>:</td>
		<td><?=CalendarPeriod('find_DATE_MODIFIED_from', htmlspecialcharsbx($find_DATE_MODIFIED_from), 'find_DATE_MODIFIED_to', htmlspecialcharsbx($find_DATE_MODIFIED_to), 'find_form', 'Y')?></td>
	</tr>
	<?$oFilter->Buttons(array('table_id'=>$sTableID,'url'=>$APPLICATION->GetCurPage(),'form'=>'find_form'));?>
	<?$oFilter->End();?>
</form>

<?// Output ?>

<?
# Update notifier
\Acrit\Core\Update::display();
# XDebug notifier
Helper::call($strModuleId, 'Profile', 'checkXDebug');
# Error
if(is_array($_SESSION[$strErrorSessionKey])){
	print Helper::showError($_SESSION[$strErrorSessionKey]['TITLE'], $_SESSION[$strErrorSessionKey]['DESCR']);
	unset($_SESSION[$strErrorSessionKey]);
}
?>
<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>