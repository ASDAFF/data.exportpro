<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Debug;

// Core (part 1)
define('ADMIN_MODULE_NAME', htmlspecialchars($_GET['module']));
define('ACRIT_CORE_EXPORT_PREVIEW', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
IncludeModuleLangFile(__FILE__);
$arGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray();
$strModuleId = $arGet['module'];
$strModuleCode = preg_replace('#^(.*?)\.(.*?)$#', '$2', $strModuleId);
$strModuleUnderscore = preg_replace('#^(.*?)\.(.*?)$#', '$1_$2', $strModuleId);

// Check rights
if($APPLICATION->GetGroupRight($strModuleId) == 'D'){
	$APPLICATION->authForm(Loc::getMessage('ACCESS_DENIED'));
}

// Core
if(!\Bitrix\Main\Loader::includeModule('acrit.core')){
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	?><div id="acrit-exp-core-notifier"><?
		print '<div style="margin-top:15px;"></div>';
		print \CAdminMessage::ShowMessage(array(
			'MESSAGE' => Loc::getMessage('ACRIT_EXP_CORE_NOTICE'),
			'HTML' => true,
		));
	?></div><?
	die();
}

// Module
\Bitrix\Main\Loader::includeModule($strModuleId);

// Debug
Debug::setModuleId($strModuleId);

$bSkipIncludeProlog = true;
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ACRIT_CORE.'/install/demo.php');

$arPluginsPlain = Exporter::getInstance($strModuleId)->findPlugins(false);

// Check database
Helper::checkDatabase($strModuleId);

// Profile ID
$intProfileID = $arGet['profile_id'];
$intProfileID = is_numeric($intProfileID) && $intProfileID > 0 ? $intProfileID : false;
$bInitial = $arGet['initial'] == 'Y';

$bActiveProfilesFound = false;
$arProfiles = Helper::call($strModuleId, 'Profile', 'getProfiles', [[], [], false, false, ['ID', 'ACTIVE', 'NAME']]);
if(!is_array($arProfiles)){
	$arProfiles = [];
}
foreach($arProfiles as $arProfile){
	if($arProfile['ACTIVE']=='Y'){
		$bActiveProfilesFound = true;
	}
}
if(!$bActiveProfilesFound){
	print Helper::showNote(Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_NO_ACTIVE_PROFILES', [
		'#MODULE_CODE#' => $strModuleCode,
	]));
	return;
}

// Process
$intIBlockID = false;
if($bActiveProfilesFound){
	$intElementID = $arGet['ID'];
	if($intElementID) {
		$intIBlockID = Helper::getElementIBlockID($intElementID);
		if($intIBlockID) {
			$arProcessResult = Exporter::processElement($intElementID, $intIBlockID, $intProfileID, 
				Exporter::PROCESS_MODE_PREVIEW, $strModuleId);
			$arProcessResult = $arProcessResult[$strModuleId]['RESULT'];
		}
	}
}
$arProcessResult = is_array($arProcessResult) ? $arProcessResult : array();
?>

<style>
.acrit-exp-preview > table > tbody > tr > td > hr:last-child {
	display:none;
}
</style>

<div class="acrit-exp-preview">
	<?if(is_array($arProcessResult) && is_array($arProcessResult['PROFILES'])):?>
		<table class="adm-list-table">
			<thead>
				<tr class="adm-list-table-header">
					<th class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner">
						</div>
					</th>
					<th class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner">
							<?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_ID');?>
						</div>
					</th>
					<th class="adm-list-table-cell">
						<div class="adm-list-table-cell-inner">
							<?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_PROFILE');?>
						</div>
					</th>
					<th class="adm-list-table-cell" style="width:80px;">
						<div class="adm-list-table-cell-inner">
							<?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_TIME');?>
						</div>
					</th>
				</tr>
			</thead>
			<tbody>
				<?foreach($arProcessResult['PROFILES'] as $arProfile):?>
					<?
					$arPlugin = Exporter::getInstance($strModuleId)->getPluginInfo($arProfile['FORMAT']);
					$bFiltered = Helper::call($strModuleId, 'Profile', 'isItemFiltered', [$arProfile['ID'], $intIBlockID, $intElementID]);
					?>
					<tr class="adm-list-table-row">
						<td class="adm-list-table-cell">
							<?if(is_array($arProfile['_PREVIEW'])):?>
								<?if($bFiltered):?>
									<img src="/bitrix/themes/.default/images/lamp/green.gif" alt="" width="14" height="14" />
								<?else:?>
									<img src="/bitrix/themes/.default/images/lamp/yellow.gif" alt="" width="14" height="14" />
								<?endif?>
							<?else:?>
								<img src="/bitrix/themes/.default/images/lamp/grey.gif" alt="" width="14" height="14" />
							<?endif?>
						</td>
						<td class="adm-list-table-cell">
							<?=$arProfile['ID'];?>
						</td>
						<td class="adm-list-table-cell">
							<div class="acrit-exp-preview-profile-title">
								<?if(strlen($arPlugin['ICON_BASE64'])):?>
									<img src="<?=$arPlugin['ICON_BASE64'];?>" alt="" />
								<?endif?>
								<?if(is_array($arProfile['_PREVIEW'])):?>
									<a href="/bitrix/admin/<?=$strModuleUnderscore;?>_new_edit.php?ID=<?=$arProfile['ID'];?>&lang=<?=LANGUAGE_ID;?>"
										target="_blank"><?=$arProfile['NAME'];?></a>
								<?else:?>
									<?=$arProfile['NAME'];?>
								<?endif?>
								<span>[<?=$arPlugin['NAME'];?>]</span>
							</div>
							<?if(is_array($arProfile['_PREVIEW'])):?>
								<?foreach($arProfile['_PREVIEW'] as $arDataItem):?>
									<?print Exporter::displayPreviewResult($arDataItem);?>
								<?endforeach?>
							<?endif?>
							<?if(!is_array($arPlugin)):?>
								<?=Helper::showError(Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_FORMAT_NOT_FOUND', array(
									'#MODULE_CODE#' => $strModuleCode,
									'#PROFILE_ID#' => $arProfile['ID'],
								)));?>
							<?endif?>
						</td>
						<td class="adm-list-table-cell align-right">
							<?if(is_array($arProfile['_PREVIEW'])):?>
								<?
								$fTime = 0;
								if(is_array($arProfile['_PREVIEW'])){
									foreach($arProfile['_PREVIEW'] as $arDataItem){
										$fTime += $arDataItem['TIME'];
									}
								}
								?>
								<?=number_format($fTime, 4, '.', '');?>
							<?endif?>
						</td>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
	<?endif?>
</div>
<?if(isset($arProcessResult['_TIME_FULL']) || isset($arProcessResult['_TIME_GET_DATA'])):?>
	<div class="acrit-exp-preview-time-full">
		<?if(isset($arProcessResult['_TIME_GET_DATA'])):?>
			<?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_TIME_GET_DATA', array(
				'#TIME_GET_DATA#' => number_format($arProcessResult['_TIME_GET_DATA'], 4, '.', ''),
			));?><br/>
		<?endif?>
		<?if(isset($arProcessResult['_TIME_FULL'])):?>
			<?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_TIME_FULL', array(
				'#TIME_FULL#' => number_format($arProcessResult['_TIME_FULL'], 4, '.', ''),
			));?><br/>
		<?endif?>
	</div>
<?endif?>

<?// Legend ?>
<div>
	<div><b><?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_LEGEND_TITLE');?></b></div>
	<table>
		<tbody>
			<tr>
				<td><img src="/bitrix/themes/.default/images/lamp/grey.gif" alt="" width="14" height="14" /></td>
				<td><?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_LEGEND_GRAY');?></td>
			</tr>
			<tr>
				<td><img src="/bitrix/themes/.default/images/lamp/yellow.gif" alt="" width="14" height="14" /></td>
				<td><?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_LEGEND_YELLOW');?></td>
			</tr>
			<tr>
				<td><img src="/bitrix/themes/.default/images/lamp/green.gif" alt="" width="14" height="14" /></td>
				<td><?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_LEGEND_GREEN');?></td>
			</tr>
		</tbody>
	</table>
</div>
<br/>

<div class="acrit-exp-preview-select" style="display:none">
	<span class="acrit-exp-select-wrapper" style="float:right;margin:3px 0 0 3px;">
		<select style="padding-right:60px;">
			<option value=""><?=Loc::getMessage('ACRIT_EXP_EXPORT_PREVIEW_PROFILE_ALL');?></option>
			<?foreach($arProfiles as $arProfile):?>
				<?if($arProfile['ACTIVE'] == 'Y'):?>
					<option value="<?=$arProfile['ID'];?>" data-icon="<?=$arPluginsPlain[$arProfile['PLUGIN']]['ICON_BASE64'];?>"
						<?if($arProfile['ID'] == $intProfileID):?>selected="selected"<?endif?>>
						<?=$arProfile['NAME'];?> [<?=$arProfile['ID'];?>]
					</option>
				<?endif?>
			<?endforeach?>
		</select>
	</span>
</div>

<script src="/bitrix/js/<?=ACRIT_CORE;?>/highlightjs/highlight.pack.js"></script>
<script>
$('.acrit-exp-preview pre code').each(function(i, block) {
	highlighElement(block);
});
</script>

<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin_after.php');
?>