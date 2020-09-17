<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Cli;

Loc::loadMessages(__FILE__);

$arCurrentTasksAll = Cli::getCronTasks($strModuleId);
$arCurrentTasksMatch = [];
foreach($arCurrentTasksAll as $key => $arTask){
	$arTask['COMMAND_FULL'];
	if(preg_match('#profile=(all|[\d,]+)#i', $arTask['COMMAND_FULL'], $arMatch)){
		if($arMatch[1] == 'all'){
			$arCurrentTasksMatch[] = $arTask;
		}
		else{
			$arId = explode(',', $arMatch[1]);
			if(in_array($intProfileID, $arId)){
				$arCurrentTasksMatch[] = $arTask;
			}
		}
	}
}

?>
<?if(!empty($arCurrentTasksMatch)):?>
	<div>
		<a href="javascript:void(0)" data-role="cron-current-tasks-toggle" class="acrit-inline-link">
			<?=Loc::getMessage('ACRIT_EXP_CRON_TASKS_TOGGLE');?>
		</a>
		(<?=count($arCurrentTasksMatch);?>)
		<div data-role="cron-current-tasks" style="display:none;">
			<?foreach($arCurrentTasksMatch as $arTask):?>
				<div class="acrit-exp-cron-form-command" style="margin-top:4px;">
					<code><?=$arTask['COMMAND_FULL'];?></code>
				</div>
			<?endforeach?>
		</div>
		<?if(count($arCurrentTasksMatch) > 1):?>
			<div style="margin-top:8px;">
				<?=Helper::showNote(Loc::getMessage('ACRIT_EXP_CRON_TASKS_MULTIPLE_NOTICE'), true);?>
			</div>
		<?endif?>
	</div>
<?else:?>
	<?=Loc::getMessage('ACRIT_EXP_CRON_TASKS_NO');?>
<?endif?>
