<?
namespace Acrit\Core\Export;

use
	\Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

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

// Check rights
if($APPLICATION->GetGroupRight($strModuleId) == 'D'){
	$APPLICATION->authForm(Loc::getMessage('ACCESS_DENIED'));
}

// Input data
$obGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList();
$arGet = $obGet->toArray();
$obPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList();
$arPost = $obPost->toArray();

// Demo
acritShowDemoExpired($strModuleId);

// Page title
$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE_SUPPORT');

// Core notice
if(!\Bitrix\Main\Loader::includeModule($strCoreId)){
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	?><div id="acrit-exp-core-notifier"><?
		print '<div style="margin-top:15px;"></div>';
		print \CAdminMessage::ShowMessage(array(
			'MESSAGE' => \Bitrix\Main\Localization\Loc::getMessage('ACRIT_EXP_CORE_NOTICE', [
				'#CORE_ID#' => $strCoreId,
				'#LANG#' => LANGUAGE_ID,
			]),
			'HTML' => true,
		));
	?></div><?
	$APPLICATION->SetTitle($strPageTitle);
	die();
}

// Module
\Bitrix\Main\Loader::includeModule($strModuleId);

// Core (part 2, visual)
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

// Demo
acritShowDemoNotice($strModuleId);

// Set page title
$APPLICATION->SetTitle($strPageTitle);

// Tab control
$arTabs = [
	[
		'DIV' => 'idea',
		'TAB' => Helper::getMessage('ACRIT_EXP_TAB_IDEA_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_EXP_TAB_IDEA_DESC'),
	]
];

?><div id="acrit_exp_support"><?

// Start TabControl (via CAdminForm, not CAdminTabControl)
$obTabControl = new \CAdminForm('AcritExpSupport', $arTabs);
$obTabControl->Begin(array(
	'FORM_ACTION' => $APPLICATION->GetCurPageParam('', array()),
));

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 1. Idea
$obTabControl->BeginNextFormTab();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//
$obTabControl->BeginCustomField('IDEA', Helper::getMessage('ACRIT_EXP_IDEA'));
$strUrl = 'https://www.acrit-studio.ru/services/idea/';
?>
<tr class="heading"><td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td></tr>
<tr>
	<td colspan="2">
		<div><?=Helper::getMessage('ACRIT_EXP_IDEA_TEXT', ['#URL#' => $strUrl]);?></div><br/>
	</td>
</tr>
<?
$obTabControl->EndCustomField('IDEA');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$obTabControl->Show();

?></div><?

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>