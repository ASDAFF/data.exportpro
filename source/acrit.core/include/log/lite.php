<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

$strLogPreview = Log::getInstance($strModuleId)->getLogPreview($intProfileId);

$strLogFilenameRel = Log::getInstance($strModuleId)->getLogFilename($intProfileId, true);

?>
<div class="acrit-core-log-preview-wrapper" data-role="log-wrapper"
	data-module-id="<?=$strModuleId;?>" data-profile-id="<?=$intProfileId;?>">
	<div class="acrit-core-log-control">
		<div class="acrit-core-log-control-left">
			<a href="javascript:void(0);" data-role="log-refresh" data-ajax="Y" class="adm-btn">
				<?=Helper::getMessage('ACRIT_CORE_LOG_REFRESH');?>
			</a>
		</div>
		<div class="acrit-core-log-control-right">
			<a href="<?=Log::getInstance($strModuleId)->getLogUrl($intProfileId, false);?>" data-role="log-open" 
				class="adm-btn" target="_blank">
				<?=Helper::getMessage('ACRIT_CORE_LOG_OPEN');?>
			</a>
			&nbsp;
			<a href="<?=Log::getInstance($strModuleId)->getLogUrl($intProfileId, true);?>" data-role="log-download"
				class="adm-btn" target="_blank" title="<?=$strLogFilenameRel;?>">
				<?=Helper::getMessage('ACRIT_CORE_LOG_DOWNLOAD');?>
			</a>
			&nbsp;
			<a href="javascript:void(0);" data-role="log-clear" data-ajax="Y" class="adm-btn" 
				data-confirm="<?=Helper::getMessage('ACRIT_CORE_LOG_CLEAR_CONFIRM');?>">
				<?=Helper::getMessage('ACRIT_CORE_LOG_CLEAR');?>
			</a>
		</div>
	</div>
	<div>
		<textarea class="acrit-core-log" data-role="log-content" data-empty-height="28" readonly="readonly"
			placeholder="<?=Helper::getMessage('ACRIT_CORE_LOG_EMPTY_PLACEHOLDER')?>"
		><?=$strLogPreview;?></textarea>
	</div>
	<div class="acrit-core-log-size-notice">
		<?=Helper::getMessage('ACRIT_CORE_LOG_SIZE_NOTICE', array(
			'#MAX_SIZE#' => Log::getInstance($strModuleId)->getMaxSize(true, true),
			'#LOG_SIZE#' => Log::getInstance($strModuleId)->getLogSize($intProfileId, true),
		))?>
	</div>
</div>