<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Cli;

$strLang = 'ACRIT_CORE_';
Helper::loadMessages(__FILE__);

$strModuleId = $arVariables['MODULE_ID'];
$strProfileId = $arVariables['PROFILE_ID'];

$arCurrentTasksAll = Cli::getCronTasks($strModuleId);
$arCurrentTasksMatch = [];
foreach($arCurrentTasksAll as $key => $arTask){
	$arTask['COMMAND_FULL'];
	if(preg_match('#profile=([A-z0-9-_]+)#i', $arTask['COMMAND_FULL'], $arMatch)){
		if($arMatch[1] == 'all'){
			$arCurrentTasksMatch[] = $arTask;
		}
		else{
			$arId = explode(',', $arMatch[1]);
			if(in_array($strProfileId, $arId)){
				$arCurrentTasksMatch[] = $arTask;
			}
		}
	}
}

?>
<?if(!empty($arCurrentTasksMatch)):?>
	<div>
		<div>
			<a href="javascript:void(0)" data-role="acrit-core-cron-current-tasks-toggle" class="acrit-inline-link">
				<?=Helper::getMessage($strLang.'CRON_TASKS_TOGGLE');?>
			</a>
			(<?=count($arCurrentTasksMatch);?>)
		</div>
		<div data-role="acrit-core-cron-current-tasks" style="display:none;">
			<?foreach($arCurrentTasksMatch as $arTask):?>
				<div class="acrit-core-cron-form-command" style="margin-top:4px;">
					<code><?=$arTask['COMMAND_FULL'];?></code>
				</div>
			<?endforeach?>
		</div>
		<?if(count($arCurrentTasksMatch) > 1):?>
			<div style="margin-top:8px;">
				<?=Helper::showNote(Helper::getMessage($strLang.'CRON_TASKS_MULTIPLE_NOTICE'), true);?>
			</div>
		<?endif?>
	</div>
<?else:?>
	<?=Helper::getMessage($strLang.'CRON_TASKS_NO');?>
<?endif?>
