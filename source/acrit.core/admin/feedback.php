<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

// Core (part 1)
$strCoreId = 'acrit.core';
define('ADMIN_MODULE_NAME', $strCoreId);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
\Bitrix\Main\Loader::includeModule($strCoreId);
IncludeModuleLangFile(__FILE__);

// Stop buffering
#Helper::obRestart();
#Helper::obStop();

// Arguments
$arGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray();
$arPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->toArray();
if(!Helper::isUtf()){
	$arGet = Helper::convertEncoding($arGet, 'UTF-8', 'CP1251');
	$arPost = Helper::convertEncoding($arPost, 'UTF-8', 'CP1251');
}
$strAction = $arGet['action'];
$strModuleId = $arPost['module'];

if($strAction == 'feedback_send'){
	$strEmailAdmin = $arPost['email_admin'];
	$strSubject = $arPost['subject'];
	$strProblem = $arPost['problem'];
	$strName = $arPost['name'];
	$strEmailUser = $arPost['email_user'];
	$strTech = $arPost['tech'];
	$strUrl = $arPost['url'];
	$obDate = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
	#
	$strMessage = $strProblem."\r\n".
								"\r\n\r\n".
								$strTech."\r\n".
								'URL: '.$strUrl."\r\n".
								"\r\n".
								$obDate->format('Y-m-d H:i:s');
	$arEmail = [
		'TO' => $strEmailAdmin,
		'SUBJECT' => $strSubject,
		'BODY' => $strMessage,
		'HEADER' => [
			'From' => sprintf('%s <%s>', $strName, $strEmailUser),
		],
		'CHARSET' => defined('BX_UTF') && BX_UTF === true ? 'UTF-8' : 'windows-1251',
		'CONTENT_TYPE' => 'text',
	];
	$bSuccess = \Bitrix\Main\Mail\Mail::send($arEmail);
	print $bSuccess ? 'Y' : 'N';
}

die();

?>