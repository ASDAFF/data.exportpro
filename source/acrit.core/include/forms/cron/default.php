<?
/**
 *	Required VARIABLES:
			MODULE_ID (string, 'acrit.processing')
			PROFILE_ID (integer/string, 123 or 'import_modules')
			CLI_FILE (string, 'run.php')
			CALLBACK_SETUP (string, 'func_cron_setup')
			CALLBACK_CLEAR (string, 'func_cron_clear')
 *	Additional VARIABLES:
			INLINE_JS (boolean)
			SHOW_TASKS (boolean)
 */
namespace Acrit\Processing;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Cli,
	\Acrit\Core\Log;

# Params
$strModuleId = $arVariables['MODULE_ID'];
$strProfileId = $arVariables['PROFILE_ID'];
$strCliFile = $arVariables['CLI_FILE'];
$strCallbackSetup = !!$arVariables['CALLBACK_SETUP'];
$strCallbackClear = !!$arVariables['CALLBACK_CLEAR'];
$bInlineJs = !!$arVariables['INLINE_JS'];
$bShowTasks = !!$arVariables['SHOW_TASKS'];

# Check required fields
if(!strlen($strModuleId)){
	print 'No module id specified.';
	return;
}
if(!strlen($strProfileId)){
	print 'No profile id specified.';
	return;
}
if(!strlen($strCliFile)){
	print 'No cli file specified.';
	return;
}

# Lang
$strLang = 'ACRIT_CORE_';
Helper::loadMessages(__FILE__);

# JS
$arJs = [
	'/bitrix/js/'.ACRIT_CORE.'/helper.js',
	'/bitrix/js/'.ACRIT_CORE.'/moment.min.js',
	'/bitrix/js/'.ACRIT_CORE.'/cron.js',
	'/bitrix/js/'.ACRIT_CORE.'/copy_to_clipboard.js',
];
if(!$bInlineJs){
	foreach($arJs as $strJs){
		\Bitrix\Main\Page\Asset::getInstance()->addJs($strJs);
	}
}

# Get cli data
$arCli = Cli::getFullCommand($strModuleId, $strCliFile, $strProfileId, Log::getInstance($strModuleId)->getLogFilename($strProfileId));
$bCanAutoSetCrontab = $arCli['CAN_AUTO_SET'];
$bAlreadyInstalled = $arCli['ALREADY_INSTALLED'];
$strCommandFull = $arCli['COMMAND'];
$strCommandFullNoOutput = $arCli['COMMAND_NO_OUTPUT'];
$arSchedule = $arCli['SCHEDULE'];

?>
<div>
	<?if($bInlineJs):?>
		<?foreach($arJs as $strJs):?>
			<script src="<?=$strJs;?>?<?=filemtime(Helper::root().$strJs);?>"></script>
		<?endforeach?>
	<?endif?>
	<input type="hidden" data-role="acrt-core-cron-module-id" value="<?=$strModuleId;?>" />
	<input type="hidden" data-role="acrt-core-cron-profile-id" value="<?=$strProfileId;?>" />
	<input type="hidden" data-role="acrt-core-cron-cli-file" value="<?=$strCliFile;?>" />
	<input type="hidden" data-role="acrt-core-cron-show-tasks" value="<?=($bShowTasks ? 'Y' : 'N');?>" />
	<table>
		<tbody>
			<?if($bCanAutoSetCrontab):?>
				<tr id="tr_CRON_STATUS">
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::showHint(Helper::getMessage($strLang.'CRON_STATUS_HINT'));?>
						<label><?=Helper::getMessage($strLang.'CRON_STATUS');?>:</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<div class="acrit-core-cron-status">
							<div data-cron-status="<?=($bAlreadyInstalled?'Y':'N');?>" data-profile-id="<?=$intProfileID;?>">
								<span data-status="Y"><?=Helper::getMessage($strLang.'CRON_STATUS_Y');?></span>
								<span data-status="N"><?=Helper::getMessage($strLang.'CRON_STATUS_N');?></span>
							</div>
						</div>
					</td>
				</tr>
				<tr id="tr_CRON_SERVER_TIME">
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::showHint(Helper::getMessage($strLang.'CRON_SERVER_TIME_HINT'));?>
						<label><?=Helper::getMessage($strLang.'CRON_SERVER_TIME');?>:</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?
						$strDate = date('r');
						?>
						<span id="acrit-core-server-time" data-role="acrit-core-server-time" data-date="<?=$strDate;?>"
							data-days="<?=Helper::getMessage($strLang.'CRON_SERVER_TIME_DAYS');?>"
							data-months="<?=Helper::getMessage($strLang.'CRON_SERVER_TIME_MONTHS');?>">---</span>
					</td>
				</tr>
				<tr id="tr_CRON_SCHEDULE">
					<td class="adm-detail-content-cell-l" width="40%">
						<?=Helper::showHint(Helper::getMessage($strLang.'CRON_SCHEDULE_HINT'));?>
						<label><?=Helper::getMessage($strLang.'CRON_SCHEDULE');?>:</label>
					</td>
					<td class="adm-detail-content-cell-r" width="60%">
						<table class="acrit-core-cron-form-schedule" data-role="acrit-core-cron-schedule">
							<tbody>
								<tr>
									<td><?=Helper::getMessage($strLang.'CRON_SCHEDULE_MINUTE');?></td>
									<td><?=Helper::getMessage($strLang.'CRON_SCHEDULE_HOUR');?></td>
									<td><?=Helper::getMessage($strLang.'CRON_SCHEDULE_DAY');?></td>
									<td><?=Helper::getMessage($strLang.'CRON_SCHEDULE_MONTH');?></td>
									<td><?=Helper::getMessage($strLang.'CRON_SCHEDULE_WEEKDAY');?></td>
								</tr>
								<tr>
									<td><input type="text" maxlength="50" placeholder="*" name="minute" value="<?=$arSchedule[0];?>" /></td>
									<td><input type="text" maxlength="50" placeholder="*" name="hour" value="<?=$arSchedule[1];?>" /></td>
									<td><input type="text" maxlength="50" placeholder="*" name="day" value="<?=$arSchedule[2];?>" /></td>
									<td><input type="text" maxlength="50" placeholder="*" name="month" value="<?=$arSchedule[3];?>" /></td>
									<td><input type="text" maxlength="50" placeholder="*" name="weekday" value="<?=$arSchedule[4];?>" /></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr id="tr_CRON_SCHEDULE_EXAMPLES">
					<td class="adm-detail-content-cell-l" width="40%"></td>
					<td class="adm-detail-content-cell-r" width="60%">
						<span><?=Helper::getMessage($strLang.'CRON_SCHEDULE_FAST');?>:</span>
						<?
						$arExamples = array(
							'* * * * *' => Helper::getMessage($strLang.'CRON_SCHEDULE_FAST_MINUTE'),
							'*/5 * * * *' => Helper::getMessage($strLang.'CRON_SCHEDULE_FAST_5_MINUTES'),
							'*/30 * * * *' => Helper::getMessage($strLang.'CRON_SCHEDULE_FAST_HOURLY_HALF'),
							'0 * * * *' => Helper::getMessage($strLang.'CRON_SCHEDULE_FAST_HOURLY'),
							'0 */4 * * *' => Helper::getMessage($strLang.'CRON_SCHEDULE_FAST_HOURLY_4'),
							'0 8 * * *' => Helper::getMessage($strLang.'CRON_SCHEDULE_FAST_DAILY'),
							'0 9,12,16 * * *' => Helper::getMessage($strLang.'CRON_SCHEDULE_FAST_9_12_16'),
							'0 8 * * 7' => Helper::getMessage($strLang.'CRON_SCHEDULE_FAST_SUNDAY'),
						);
						?>
						<?foreach($arExamples as $strSchedule => $strName):?>
							<a href="#" data-role="acrit-core-cron-example" data-schedule="<?=$strSchedule;?>"><?=$strName;?></a><?
								if($strName === end($arExamples)){
									print '.';
								}
								else {
									print ', ';
								}
							?>
						<?endforeach?>
					</td>
				</tr>
				<tr id="tr_CRON_SCHEDULE_BUTTONS">
					<td class="adm-detail-content-cell-l" width="40%"></td>
					<td class="adm-detail-content-cell-r" width="60%">
						<input type="button" value="<?=Helper::getMessage($strLang.'CRON_BUTTON_SETUP');?>" class="adm-btn-green"
							data-role="acrit-core-cron-setup" data-callback="<?=$strCallbackSetup;?>" />
						<input type="button" value="<?=Helper::getMessage($strLang.'CRON_BUTTON_CLEAR');?>" class="adm-btn"
							data-role="acrit-core-cron-clear" data-callback="<?=$strCallbackClear;?>"
							<?if(!$bAlreadyInstalled):?> disabled="disabled"<?endif?> />
					</td>
				</tr>
				<?if($bShowTasks):?>
					<tr id="tr_CRON_TASKS">
						<td class="adm-detail-content-cell-l" style="vertical-align:top">
							<?=Helper::showHint(Helper::getMessage($strLang.'CRON_TASKS_HINT'));?>
							<label><?=Helper::getMessage($strLang.'CRON_TASKS');?>:</label>
						</td>
						<td class="adm-detail-content-cell-r" data-role="cron-current-tasks-wrapper">
							<?=Helper::getHtmlObject(ACRIT_CORE, null, 'forms/cron', 'tasks', [
								'MODULE_ID' => ACRIT_PROCESSING,
								'PROFILE_ID' => $strProfileId,
								'CLI_FILE' => $strCliFile,
							])?>
						</td>
					</tr>
				<?endif?>
			<?else:?>
				<tr id="tr_CRON_CANNOT_AUTOSET">
					<td colspan="2">
						<div style="color:red"><?=Helper::getMessage($strLang.'CRON_CANNOT_AUTOSET');?></div>
					</td>
				</tr>
			<?endif?>
			<tr class="heading">
				<td colspan="2"><?=Helper::getMessage($strLang.'CRON_COMMAND');?>
					<?=Helper::showHint(Helper::getMessage($strLang.'CRON_COMMAND_HINT', [
						'#MODULE_ID#' => $strModuleId,
					]));?>
				</td>
			</tr>
			<tr id="tr_CRON_COMMAND">
				<td class="adm-detail-content-cell-r" colspan="2">
					<?if($strCommandFull != $strCommandFullNoOutput):?>
						<div class="acrit-core-cron-form-command-title"><?=Helper::getMessage($strLang.'CRON_TITLE_WITH_OUTPUT');?></div>
					<?endif?>
					<div class="acrit-core-cron-form-command">
						<code class="acrit-core-cron-command-copy"><?=$strCommandFull;?></code>
						<a href="javascript:void(0)" class="acrit-core-cron-form-command-copy acrit-inline-link"
							data-role="acrit-core-cron-command-copy" data-message="<?=Helper::getMessage($strLang.'CRON_COMMAND_COPY_SUCCESS');?>">
							<?=Helper::getMessage($strLang.'CRON_COMMAND_COPY');?>
						</a>
						<span></span>
					</div>
					<br/>
					<?if($strCommandFull != $strCommandFullNoOutput):?>
						<div class="acrit-core-cron-form-command-title"><?=Helper::getMessage($strLang.'CRON_TITLE_NO_OUTPUT');?></div>
						<div class="acrit-core-cron-form-command">
							<code class="acrit-core-cron-command-copy"><?=$strCommandFullNoOutput;?></code>
							<a href="javascript:void(0)" class="acrit-core-cron-form-command-copy acrit-inline-link"
								data-role="acrit-core-cron-command-copy" data-message="<?=Helper::getMessage($strLang.'CRON_COMMAND_COPY_SUCCESS');?>">
								<?=Helper::getMessage($strLang.'CRON_COMMAND_COPY');?>
							</a>
							<span></span>
						</div>
						<br/>
					<?endif?>
					<div style="margin-top:4px;"><?=Helper::getMessage($strLang.'CRON_COMMAND_WARNING');?></div>
					<?if(isset($_SERVER['BITRIX_VA_VER'])):?>
						<div style="margin-top:4px;"><?=Helper::getMessage($strLang.'CRON_LINK_ARTICLE_BITRIX_ENV');?></div>
					<?endif?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
