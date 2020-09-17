<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Cli,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

$arCli = Cli::getFullCommand($strModuleId, 'export.php', $intProfileID, Log::getInstance($strModuleId)->getLogFilename($intProfileID));
$bCanAutoSetCrontab = $arCli['CAN_AUTO_SET'];
$bAlreadyInstalled = $arCli['ALREADY_INSTALLED'];
$strCommandFull = $arCli['COMMAND'];
$arSchedule = $arCli['SCHEDULE'];

#
#$arCurrentTasks = Cli::getCronTasks($strCommand);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->BeginCustomField('PROFILE[RUN_MANUAL]', Loc::getMessage('ACRIT_EXP_RUN_MANUAL'));
?>
	<tr class="heading">
		<td colspan="2">
			<?=$obTabControl->GetCustomLabelHTML()?>
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_RUN_MANUAL_HINT'))?>
		</td>
	</tr>
	<tr id="tr_RUN_MANUAL">
		<td width="40%" class="adm-detail-content-cell-l"></td>
		<td width="60%" class="adm-detail-content-cell-r">
			<div>
				<input type="button" class="adm-btn-green" value="<?=Loc::getMessage('ACRIT_EXP_RUN_MANUAL_BUTTON');?>"
					data-role="run-manual" />
			</div>
			<br/>
			<div>
				<input type="button" class="adm-btn-green" value="<?=Loc::getMessage('ACRIT_EXP_RUN_BACKGROUND_BUTTON');?>"
					data-role="run-background" />
				&nbsp;&nbsp;
				<span data-role="run-background-status" style="color:green;font-weight:bold;"></span>
			</div>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[RUN_MANUAL]');

$obTabControl->AddSection('RUN_CRON', GetMessage('ACRIT_EXP_RUN_AUTO'));

// Cron: server time
$obTabControl->BeginCustomField('PROFILE[CRON_SERVER_TIME]', Loc::getMessage('ACRIT_EXP_CRON_SERVER_TIME'));
?>
	<tr id="tr_CRON_SERVER_TIME">
		<td width="40%" class="adm-detail-content-cell-l">
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CRON_SERVER_TIME_HINT'));?>
			<label><?=$obTabControl->GetCustomLabelHTML()?>:<label>
		</td>
		<td width="60%" class="adm-detail-content-cell-r">
			<?
			$strDate = date('r');
			?>
			<span id="acrit-exp-server-time" data-role="acrit-core-server-time" data-date="<?=$strDate;?>"
				data-days="<?=Loc::getMessage('ACRIT_EXP_CRON_SERVER_TIME_DAYS');?>"
				data-months="<?=Loc::getMessage('ACRIT_EXP_CRON_SERVER_TIME_MONTHS');?>">---</span>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[CRON_SERVER_TIME]');

// Cron: status
$obTabControl->BeginCustomField('PROFILE[CRON_STATUS]', Loc::getMessage('ACRIT_EXP_CRON_STATUS'));
?>
	<tr id="tr_CRON_STATUS">
		<td width="40%" class="adm-detail-content-cell-l">
			<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CRON_STATUS_HINT'));?>
			<label><?=$obTabControl->GetCustomLabelHTML()?>:<label>
		</td>
		<td width="60%" class="adm-detail-content-cell-r">
			<?if($bCanAutoSetCrontab):?>
				<div class="acrit-core-cron-status">
					<div data-cron-status="<?=($bAlreadyInstalled?'Y':'N');?>" data-profile-id="<?=$intProfileID;?>">
						<span data-status="Y"><?=Loc::getMessage('ACRIT_EXP_CRON_STATUS_Y');?></span>
						<span data-status="N"><?=Loc::getMessage('ACRIT_EXP_CRON_STATUS_N');?></span>
					</div>
				</div>
			<?else:?>
				<div class="acrit-core-cron-cannot-autoset" style="color:red">
					<?=Loc::getMessage('ACRIT_EXP_CRON_CANNOT_AUTOSET');?>
				</div>
			<?endif?>
		</td>
	</tr>
	<?if(!$bCanAutoSetCrontab):?>
		<tr id="tr_CRON_STATUS_CANNOT_AUTOSET">
			<td width="40%" class="adm-detail-content-cell-l"></td>
			<td width="60%" class="adm-detail-content-cell-r">
				<div>
					<a href="#" data-role="acrit-core-cron-more-toggle" class="acrit-inline-link">
						<?=Loc::getMessage('ACRIT_EXP_CRON_CANNOT_AUTOSET_TOGGLE');?>
					</a>
				</div>
			</td>
		</tr>
		<tr id="tr_CRON_STATUS_CANNOT_AUTOSET_INFO">
			<td class="adm-detail-content-cell-r" colspan="2">
				<div style="display:none;">
					<?=Helper::showHeading(Loc::getMessage('ACRIT_EXP_CRON_CANNOT_AUTOSET_HEADER'));?>
					<?=Loc::getMessage('ACRIT_EXP_CRON_CANNOT_AUTOSET_MORE');?>
				</div>
			</td>
		</tr>
	<?endif?>
<?
$obTabControl->EndCustomField('PROFILE[CRON_STATUS]');

// Cron: schedule
if($bCanAutoSetCrontab){
	#
	$obTabControl->BeginCustomField('PROFILE[CRON_TASKS]', Loc::getMessage('ACRIT_EXP_CRON_TASKS'));
	?>
		<tr id="tr_CRON_TASKS">
			<td class="adm-detail-content-cell-l" style="vertical-align:top">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CRON_TASKS_HINT'));?>
				<label><?=$obTabControl->GetCustomLabelHTML()?>:</label>
			</td>
			<td class="adm-detail-content-cell-r" data-role="cron-current-tasks-wrapper">
				<?require __DIR__.'/_cron_tasks.php';?>
			</td>
		</tr>
	<?
	$obTabControl->EndCustomField('PROFILE[CRON_TASKS]');
	#
	$obTabControl->BeginCustomField('PROFILE[CRON_SCHEDULE]', Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE'));
	?>
		<tr id="tr_CRON_SCHEDULE">
			<td class="adm-detail-content-cell-l" width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_HINT'));?>
				<label for="field_CURRENCY_TARGET_CURRENCY"><?=$obTabControl->GetCustomLabelHTML()?>:<label>
			</td>
			<td class="adm-detail-content-cell-r" width="60%">
				<div data-role="cron_setup_time">
					<table class="acrit-core-cron-form-schedule">
						<tbody>
							<tr>
								<td><?=Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_MINUTE');?></td>
								<td><?=Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_HOUR');?></td>
								<td><?=Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_DAY');?></td>
								<td><?=Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_MONTH');?></td>
								<td><?=Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_WEEKDAY');?></td>
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
				</div>
			</td>
		</tr>
		<tr id="tr_CRON_SCHEDULE_EXAMPLES">
			<td class="adm-detail-content-cell-l" width="40%"></td>
			<td class="adm-detail-content-cell-r" width="60%">
				<span><?=Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_FAST');?>:</span>
				<?
				$arExamples = array(
					'* * * * *' => Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_FAST_MINUTE'),
					'*/5 * * * *' => Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_FAST_5_MINUTES'),
					'*/30 * * * *' => Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_FAST_HOURLY_HALF'),
					'0 * * * *' => Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_FAST_HOURLY'),
					'0 */4 * * *' => Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_FAST_HOURLY_4'),
					'0 8 * * *' => Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_FAST_DAILY'),
					'0 9,12,16 * * *' => Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_FAST_9_12_16'),
					'0 8 * * 7' => Loc::getMessage('ACRIT_EXP_CRON_SCHEDULE_FAST_SUNDAY'),
				);
				?>
				<?foreach($arExamples as $strSchedule => $strName):?>
					<a href="#" data-role="cron-example" data-schedule="<?=$strSchedule;?>"><?=$strName;?></a><?
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
				<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_CRON_BUTTON_SETUP');?>" class="adm-btn-green"
					data-role="cron-setup" />
				<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_CRON_BUTTON_CLEAR');?>" class="adm-btn"
					data-role="cron-clear"<?if(!$bAlreadyInstalled):?> disabled="disabled"<?endif?> />
			</td>
		</tr>
	<?
	$obTabControl->EndCustomField('PROFILE[CRON_SCHEDULE]');
	#
	$obTabControl->BeginCustomField('PROFILE[CRON_ONE_TIME]', Loc::getMessage('ACRIT_EXP_CRON_ONE_TIME'));
	?>
		<tr id="tr_CRON_ONE_TIME">
			<td class="adm-detail-content-cell-l">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CRON_ONE_TIME_HINT'));?>
				<label for="acrit-core-cron-one-time">
					<?=$obTabControl->GetCustomLabelHTML()?>:
				</label>
			</td>
			<td class="adm-detail-content-cell-r">
				<input type="checkbox" value="Y" data-role="cron-one-time" id="acrit-core-cron-one-time"
					<?if($arProfile['ONE_TIME'] == 'Y'):?> checked="checked"<?endif?> />
				&nbsp;
				<span data-role="cron-one-time-result"></span>
			</td>
		</tr>
	<?
	$obTabControl->EndCustomField('PROFILE[CRON_ONE_TIME]');
}

// Cron: command
$obTabControl->BeginCustomField('PROFILE[CRON_COMMAND]', Loc::getMessage('ACRIT_EXP_CRON_COMMAND'));
?>
	<tr class="heading"><td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?> <?=Helper::showHint(Loc::getMessage('ACRIT_EXP_CRON_COMMAND_HINT', [
		'#MODULE_ID#' => $strModuleId,
	]));?></td></tr>
	<tr id="tr_CRON_COMMAND">
		<td class="adm-detail-content-cell-r" colspan="2">
			<div class="acrit-core-cron-form-command">
				<code id="acrit-core-cron-command-copy"><?=$strCommandFull;?></code>
				<a href="javascript:void(0)" class="acrit-core-cron-form-command-copy acrit-inline-link"
					data-role="acrit-core-cron-command-copy" data-message="<?=Loc::getMessage('ACRIT_EXP_CRON_COMMAND_COPY_SUCCESS');?>">
					<?=Loc::getMessage('ACRIT_EXP_CRON_COMMAND_COPY');?>
				</a>
				<span></span>
			</div>
			<div style="margin-top:4px;"><?=Loc::getMessage('ACRIT_EXP_CRON_COMMAND_WARNING');?></div>
			<?if(isset($_SERVER['BITRIX_VA_VER'])):?>
				<div style="margin-top:4px;"><?=Loc::getMessage('ACRIT_EXP_CRON_LINK_ARTICLE_BITRIX_ENV');?></div>
			<?endif?>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[CRON_COMMAND]');
?>