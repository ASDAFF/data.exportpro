<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter;

Loc::loadMessages(__FILE__);

$arSteps = static::getSteps($intProfileID);

#$arCurrentProfile = Profile::getProfiles($strModuleId, $intProfileID);
$arCurrentProfile = Helper::call($strModuleId, 'Profile', 'getProfiles', [$intProfileID]);

$strCurrentStep = $arSession['STEP'];
foreach($arSteps as $strStep => $arStep){
	if($arSession['FINISHED']) {
		$arSteps[$strStep]['STATUS'] = 'DONE';
	}
	else {
		$arSteps[$strStep]['STATUS'] = 'WAITING';
	}
}
if(strlen($strCurrentStep) && !$arSession['FINISHED']){
	foreach($arSteps as $strStep => $arStep){
		if($strStep != $strCurrentStep){
			$arSteps[$strStep]['STATUS'] = 'DONE';
		}
		elseif ($strStep == $strCurrentStep){
			$arSteps[$strStep]['STATUS'] = 'CURRENT';
			break;
		}
	}
}

?>

<?if($arCurrentProfile['ACTIVE']!='Y'):?>
	<?=Helper::showError(Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_PROGRESS_PROFILE_IS_NOT_ACTIVE'));?>
<?elseif(empty($arCurrentProfile['IBLOCKS'])):?>
	<?=Helper::showError(Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_PROGRESS_PROFILE_NO_SETUP_IBLOCKS'));?>
<?else:?>
	<div class="acrit-exp-progress">
		<?if(\Bitrix\Main\Loader::includeSharewareModule($strModuleId) === MODULE_DEMO):?>
			<?=Helper::showNote(Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_PROGRESS_DEMO_MODE', [
				'#MODULE_ID#' => $strModuleId,
			]), true);?>
			<br/>
		<?endif?>
		<div class="acrit-exp-progress-steps">
			<ul>
				<?foreach($arSteps as $strStep => $arStep):?>
					<li data-step="<?=$strStep;?>">
						<span class="acrit-exp-progress-item-name">
							<?=$arStep['NAME'];?>
						</span>
						<span class="acrit-exp-progress-item-data">
							<?if($strStep==$strCurrentStep && isset($arSession[$strCurrentStep]['PERCENT'])):?>
								<span class="text-percent">(<?=number_format($arSession[$strCurrentStep]['PERCENT'], 1, '.', '');?>%)</span>
							<?endif?>
							<?if($arStep['STATUS']=='DONE'):?>
								<span class="icon-success"></span>
							<?elseif($arStep['STATUS']=='CURRENT'):?>
								<span class="icon-current"></span>
							<?elseif($arStep['STATUS']=='WAITING'):?>
								<span class="icon-waiting"></span>
							<?elseif($arStep['STATUS']=='ERROR'):?>
								<span class="icon-error">ERROR</span>
							<?endif?>
						</span>
					</li>
				<?endforeach?>
			</ul>
		</div>
		<?if($arSession['FINISHED']):?>
			<?$strResult = $obPlugin->showResults($arSession);?>
			<?if(strlen($strResult)):?>
				<div class="acrit-exp-progress-report">
					<?=$strResult;?>
				</div>
			<?endif?>
		<?endif?>
	</div>
<?endif?>

