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
$strPageTitle .= ' &laquo;'.Helper::getModuleName($strModuleId).'&raquo; ('.$strModuleId.')';
$APPLICATION->SetTitle($strPageTitle);

// Tab control
$arTabs = [
	[
		'DIV' => 'documentation',
		'TAB' => Helper::getMessage('ACRIT_EXP_TAB_DOCUMENTATION_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_EXP_TAB_DOCUMENTATION_DESC'),
	],
	[
		'DIV' => 'video',
		'TAB' => Helper::getMessage('ACRIT_EXP_TAB_VIDEO_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_EXP_TAB_VIDEO_DESC'),
	],
	[
		'DIV' => 'ask',
		'TAB' => Helper::getMessage('ACRIT_EXP_TAB_ASK_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_EXP_TAB_ASK_DESC'),
	]
];

?><div id="acrit_exp_support"><?

// Start TabControl (via CAdminForm, not CAdminTabControl)
$obTabControl = new \CAdminForm('AcritExpSupport', $arTabs);
$obTabControl->Begin(array(
	'FORM_ACTION' => $APPLICATION->GetCurPageParam('', array()),
));

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 1. Documentation
$obTabControl->BeginNextFormTab();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//
$obTabControl->BeginCustomField('FAQ', Helper::getMessage('ACRIT_EXP_FAQ'));
$strUrl = 'http://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/';
?>
<tr class="heading"><td><?=$obTabControl->GetCustomLabelHTML()?></td></tr>
<tr>
	<td style="text-align:center;">
		<div><a href="<?=$strUrl;?>" target="_blank"><?=$strUrl;?></a></div><br/>
	</td>
</tr>
<?
$obTabControl->EndCustomField('FAQ');

//
$obTabControl->BeginCustomField('REQUIREMENTS_1', Helper::getMessage('ACRIT_EXP_REQUIREMENTS_1'));
$strUrl = 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/test-your-environment-before-configuring-the-module-acrit-export/';
?>
<tr class="heading"><td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td></tr>
<tr>
	<td style="text-align:center;">
		<div><a href="<?=$strUrl;?>" target="_blank"><?=$strUrl;?></a></div><br/>
	</td>
</tr>
<?
$obTabControl->EndCustomField('REQUIREMENTS_1');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 2. Video
$obTabControl->BeginNextFormTab();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//
$obTabControl->BeginCustomField('VIDEO', Helper::getMessage('ACRIT_EXP_VIDEO_FIELD'));
?>
<tr class="heading"><td><?=$obTabControl->GetCustomLabelHTML()?></td></tr>
<tr>
	<td style="text-align:center;">
		<div><iframe width="800" height="500" src="https://www.youtube.com/embed/ene4qDMdn6A?list=PLnH5qqS_5Wnzw10GhPty9XgZSluYlFa4y" frameborder="0" allowfullscreen></iframe><br/>
	</td>
</tr>
<?
$obTabControl->EndCustomField('VIDEO');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 3. Ask
$obTabControl->BeginNextFormTab();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//
$obTabControl->BeginCustomField('REQUIREMENTS_2', Helper::getMessage('ACRIT_EXP_REQUIREMENTS_2'));
$strUrl = 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/test-your-environment-before-configuring-the-module-acrit-export/';
?>
<tr class="heading"><td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td></tr>
<tr>
	<td colspan="2">
		<div><?=Helper::getMessage('ACRIT_EXP_REQUIREMENTS_TEXT', ['#URL#' => $strUrl]);?></div><br/>
	</td>
</tr>
<?
$obTabControl->EndCustomField('REQUIREMENTS_2');

//
$obTabControl->BeginCustomField('ASK_FORM', Helper::getMessage('ACRIT_EXP_ASK_FORM'));
?>
<tr class="heading"><td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td></tr>
<tr>
	<td width="40%" class="adm-detail-content-cell-l" style="padding-top:10px; vertical-align:top;">
		<?=Helper::getMessage('ACRIT_EXP_ASK_FORM_EMAIL');?>
	</td>
	<td width="60%" class="adm-detail-content-cell-r">
		<div style="margin-bottom:6px;">
			<input type="email" style="width:96%;" data-role="ticket-email"
				value="<?=\Bitrix\Main\Config\Option::get('main', 'email_from');?>"
				data-error="<?=Helper::getMessage('ACRIT_EXP_ASK_FORM_ERROR_EMPTY_EMAIL');?>"
				data-incorrect="<?=Helper::getMessage('ACRIT_EXP_ASK_FORM_ERROR_WRONG_EMAIL');?>" />
		</div>
	</td>
</tr>
<tr>
	<td width="40%" class="adm-detail-content-cell-l" style="padding-top:10px; vertical-align:top;">
		<?=Helper::getMessage('ACRIT_EXP_ASK_FORM_SUBJECT');?>
	</td>
	<td width="60%" class="adm-detail-content-cell-r">
		<div style="margin-bottom:6px;">
			<input type="email" style="width:96%;" data-role="ticket-subject" value="<?=Helper::getMessage(
				'ACRIT_EXP_ASK_FORM_SUBJECT_DEFAULT', ['#SITE_NAME#' => Helper::getCurrentDomain()]);?>"
				data-error="<?=Helper::getMessage('ACRIT_EXP_ASK_FORM_ERROR_EMPTY_SUBJECT');?>" />
		</div>
	</td>
</tr>
<tr>
	<td width="40%" class="adm-detail-content-cell-l" style="padding-top:10px; vertical-align:top;">
		<?=Helper::getMessage('ACRIT_EXP_ASK_FORM_MESSAGE');?>
	</td>
	<td width="60%" class="adm-detail-content-cell-r">
		<div style="margin-bottom:6px;">
			<textarea cols="70" rows="10" style="resize:vertical; width:96%;" data-role="ticket-message"
				data-error="<?=Helper::getMessage('ACRIT_EXP_ASK_FORM_ERROR_EMPTY');?>"></textarea>
		</div>
	</td>
</tr>
<tr>
	<td width="40%" class="adm-detail-content-cell-l"></td>
	<td width="60%" class="adm-detail-content-cell-r">
		<div>
			<input type="button" value="<?=Helper::getMessage('ACRIT_EXP_ASK_FORM_BUTTON');?>" data-role="ticket-send" />
		</div>
	</td>
</tr>
<?
$obTabControl->EndCustomField('ASK_FORM');

//
$obTabControl->BeginCustomField('CONTACTS', Helper::getMessage('ACRIT_EXP_ASK_CONTACTS_TITLE'));
?>
<tr class="heading"><td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td></tr>
<tr>
	<td colspan="2">
		<fieldset title="<?=$obTabControl->GetCustomLabelHTML()?>">
			<legend><?=$obTabControl->GetCustomLabelHTML()?></legend>
			<?=Helper::getMessage('ACRIT_EXP_ASK_CONTACTS_TEXT');?>
		</fieldset>
	</td>
</tr>
<?
$obTabControl->EndCustomField('CONTACTS');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$obTabControl->Show();

?></div><?

?>
<div style="display:none">
	<form action="https://www.acrit-studio.ru/support/?show_wizard=Y" method="post" id="form-ticket" target="_blank" accept-charset="UTF-8">
		<input type="hidden" name="send_ticket_from_module" value="Y" />
		<input type="hidden" name="ticket_email" value="" />
		<input type="hidden" name="ticket_title" value="" />
		<input type="hidden" name="ticket_text" value="" />
		<input type="hidden" name="module_id" value="<?=$strModuleId;?>" />
		<input type="hidden" name="module_version" value="<?=Helper::getModuleVersion($strModuleId);?>" />
		<input type="hidden" name="core_version" value="<?=Helper::getModuleVersion($strCoreId);?>" />
		<input type="hidden" name="bitrix_version" value="<?=SM_VERSION.' ('.SM_VERSION_DATE.')';?>" />
		<input type="hidden" name="site_encoding" value="<?=SITE_CHARSET;?>" />
		<input type="hidden" name="site_domain" value="<?=Helper::getCurrentDomain();?>" />
	</form>
	<script>
	$('input[type=button][data-role="ticket-send"]').click(function(e){
		e.preventDefault();
		function validateEmail(email) {
			var pattern  = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@(([[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return pattern .test(email);
		}
		var form = $('#form-ticket'),
			inputEmail = $('input[data-role="ticket-email"]'),
			inputSubject = $('input[data-role="ticket-subject"]'),
			inputMessage = $('textarea[data-role="ticket-message"]'),
			textEmail = $.trim(inputEmail.val());
			textSubject = $.trim(inputSubject.val());
			textMessage = $.trim(inputMessage.val());
		if(!textEmail.length){
			alert(inputEmail.attr('data-error'));
			return;
		}
		if(!validateEmail(textEmail)){
			alert(inputEmail.attr('data-incorrect'));
			return;
		}
		if(!textSubject.length){
			alert(inputSubject.attr('data-error'));
			return;
		}
		if(!textMessage.length){
			alert(inputMessage.attr('data-error'));
			return;
		}
		textMessage = [
			textMessage,
			'\n\n',
			'<?=Helper::getMessage('ACRIT_EXP_ASK_MODULE_ID');?>: ' + $('input[name="module_id"]', form).val(),
			'\n',
			'<?=Helper::getMessage('ACRIT_EXP_ASK_MODULE_VERSION');?>: ' + $('input[name="module_version"]', form).val() 
				+ ' / ' + $('input[name="core_version"]', form).val(),
			'\n',
			'<?=Helper::getMessage('ACRIT_EXP_ASK_BITRIX_VERSION');?>: ' + $('input[name="bitrix_version"]', form).val(),
			'\n',
			'<?=Helper::getMessage('ACRIT_EXP_ASK_SITE_ENCODING');?>: ' + $('input[name="site_encoding"]', form).val(),
			'\n',
			'<?=Helper::getMessage('ACRIT_EXP_ASK_SITE_DOMAIN');?>: ' + $('input[name="site_domain"]', form).val(),
			'\n'
		];
		$('input[name="ticket_email"]', form).val(textEmail);
		$('input[name="ticket_title"]', form).val(textSubject);
		$('input[name="ticket_text"]', form).val(textMessage.join(''));
		form.submit();
	});
	</script>
</div>
<?

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>