<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper;

$arGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray();

$strModuleId = $arGet['module'];
if(!strlen($strModuleId)){
	$strModuleId = ACRIT_CORE;
}

$strLogFilename = Log::getInstance($strModuleId)->getLogFilename($intProfileId);
$strLogFilenameRel = Log::getInstance($strModuleId)->getLogFilename($intProfileId, true);

$strDownloadUrl = http_build_query([
	Log::DOWNLOAD_PARAM => Log::DOWNLOAD_PARAM_Y,
]);

$strCss = '/bitrix/themes/.default/'.ACRIT_CORE.'.css';
$strCssHref = $strCss.'?'.filemtime(Helper::root().$strCss);

if(is_file($strLogFilename)){
	$strDatetime = date(\CDatabase::dateFormatToPhp(FORMAT_DATETIME), filemtime($strLogFilename));
}


$strTitle = Helper::getMessage('ACRIT_CORE_LOG_TITLE', array(
	'#WHAT#' => Helper::getMessage('ACRIT_CORE_LOG_WHAT_'.($intProfileId ? 'PROFILE' : 'MODULE'), array(
		'#MODULE_ID#' => $strModuleId,
		'#PROFILE_ID#' => $intProfileId,
	)),
));

?>
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID;?>">
<head>
	<meta charset="<?=(Helper::isUtf()?'utf-8':'windows-1251');?>">
	<title><?=$strTitle;?></title>
	<link rel="stylesheet" type="text/css" href="<?=$strCssHref;?>" />
	<script src="<?=\CJSCore::getExtInfo('jquery2')['js'];?>"></script>
</head>
<body class="acrit-core-log-detail-wrapper">
	<div class="acrit-core-log-panel">
		<span class="acrit-core-log-size-notice">
			<?=Helper::getMessage('ACRIT_CORE_LOG_SIZE_NOTICE', array(
				'#MAX_SIZE#' => Log::getInstance($strModuleId)->getMaxSize(false, true),
			));?>
		</span>
		<a class="acrit-core-log-download"
			href="<?=$GLOBALS['APPLICATION']->getCurPageParam($strDownloadUrl, [Log::DOWNLOAD_PARAM]);?>"
			title="<?=$strLogFilenameRel;?>" >
			<?=Helper::getMessage('ACRIT_CORE_LOG_DOWNLOAD', array(
				'#WHAT#' => Helper::getMessage('ACRIT_CORE_LOG_WHAT_'.($intProfileId ? 'PROFILE' : 'MODULE'), array(
					'#MODULE_ID#' => $strModuleId,
					'#PROFILE_ID#' => $intProfileId,
				)),
				'#FILESIZE#' => Log::getInstance($strModuleId)->getLogSize($intProfileId, true),
			));?></a>
		<?if(is_file($strLogFilename)):?>
			<span class="acrit-core-log-date">
				<?=Helper::getMessage('ACRIT_CORE_LOG_DATETIME', array(
					'#DATETIME#' => $strDatetime,
				));?>
			</span>
		<?endif?>
	</div>
	<div class="acrit-core-log">
		<div class="acrit-core-log-content">
			<?if(strlen($strLogFilename) && is_file($strLogFilename) && filesize($strLogFilename)):?>
				<pre><?=Log::getInstance($strModuleId)->getLogDetail($intProfileId);?></pre>
			<?else:?>
				<p><?=Helper::getMessage('ACRIT_CORE_LOG_EMPTY');?></p>
			<?endif?>
		</div>
	</div>
	<script>
	function acritExpFullLogScrollToEnd(){
		$('html,body').scrollTop($(document).height()+100000);
	}
	$(document).ready(function(){
		setTimeout(function(){
			acritExpFullLogScrollToEnd();
		}, 100);
	});
	$(window).load(function(){
		acritExpFullLogScrollToEnd();
	});
	</script>
</body>
</html>