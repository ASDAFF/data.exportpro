<?
namespace Acrit\Core;

use 
	\Acrit\Core\Update,
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

$bCheckCore = false;
$arUpdates = Update::checkModuleUpdates($strModuleId, $intDateTo, $strVersion, $bCheckCore);
#
/*
if(isset($arUpdates[ACRIT_CORE])){
	$arUpdatesCore = $arUpdates[ACRIT_CORE];
	unset($arUpdates[ACRIT_CORE]);
	$arUpdates[ACRIT_CORE] = $arUpdatesCore;
	unset($arUpdatesCore);
}
*/
#
$arUpdatesModule = &$arUpdates[$strModuleId];
#$arUpdatesCore = &$arUpdates[ACRIT_CORE];
#
$arUpdatesModule = is_array($arUpdatesModule) ? $arUpdatesModule : [];
#$arUpdatesCore = is_array($arUpdatesCore) ? $arUpdatesCore : [];
#
$intMaxDisplayUpdates = 10;
#
if(is_numeric($intDateTo) && $intDateTo>0 && $intDateTo<=time()){
	$strMessage = Helper::getMessage('ACRIT_CORE_UPDATE_NOTIFIER_RENEW_LICENSE', array(
		'#DATE#' => date(\CDatabase::DateFormatToPHP(FORMAT_DATE), $intDateTo),
		'#LINK#' => Helper::getRenewUrl($strModuleId),
	));
	print Helper::showNote($strMessage, true);
}
elseif(!empty($arUpdatesModule)/* || !empty($arUpdatesCore)*/){
	$arUpdatesModule = array_slice(array_reverse($arUpdatesModule), 0, $intMaxDisplayUpdates);
	#$arUpdatesCore = array_slice(array_reverse($arUpdatesCore), 0, $intMaxDisplayUpdates);
	$intUpdatesCount = count($arUpdatesModule)/* + count($arUpdatesCore)*/;
	foreach($arUpdates as $strUpdateModuleId => $arUpdatesItem){
		if(empty($arUpdatesItem)){
			unset($arUpdates[$strUpdateModuleId]);
		}
	}
	ob_start();
	?>
	<div id="acrit-core-update-notifier-details-block">
		<ul>
			<?foreach($arUpdates as $strUpdateModuleId => $arUpdatesItem):?>
				<?if(!empty($arUpdatesItem)):?>
					<?if(count($arUpdates) > 1):?>
						<li>
						<div><b><?=Helper::getModuleName($strUpdateModuleId);?> [<?=$strUpdateModuleId;?>, <?=Helper::getModuleVersion($strUpdateModuleId);?>]</b></div>
						<br/>
						<ul>
					<?endif?>
						<?foreach($arUpdatesItem as $strVersion => $strDescription):?>
							<li><div><b><?=$strVersion;?></b>.<br/><?=$strDescription;?></div><br/></li>
						<?endforeach?>
					<?if(count($arUpdates) > 1):?>
						</ul>
					</li>
					<?endif?>
				<?endif?>
			<?endforeach?>
		</ul>
		<a href="/bitrix/admin/update_system_partner.php?lang=<?=LANGUAGE_ID?>&addmodule=<?=implode(',', array_keys($arUpdates));?>" 
			target="_blank" class="adm-btn adm-btn-green">
			<?=Helper::getMessage('ACRIT_CORE_UPDATE_NOTIFIER_UPDATE');?>
		</a>
	</div>
	<div style="display:none!important;">
		<style>
		#acrit-core-update-notifier-details-toggle{
			border-bottom:1px dashed #2675d7;
			color:#2675d7;
			text-decoration:none;
		}
		#acrit-core-update-notifier-details-toggle:hover{
			border-bottom:0;
		}
		#acrit-core-update-notifier-details-block{
			display:none;
		}
		#acrit-core-update-notifier-details-block ul{
			list-style:square;
			margin-bottom:4px;
			margin-left:0;
			padding-left:18px;
		}
		#acrit-core-update-notifier-details-block ul{
			list-style:disc;
		}
		</style>
		<script>
		$('#acrit-core-update-notifier-details-toggle').bind('click', function(e){
			e.preventDefault();
			$('#acrit-core-update-notifier-details-block').toggle();
		});
		</script>
	</div>
	<?
	$strDetails = ob_get_clean();
	print Helper::showSuccess(Helper::getMessage('ACRIT_CORE_UPDATE_NOTIFIER_AVAILABLE', array(
		'#COUNT#' => $intUpdatesCount,
	)), $strDetails);
}

?>