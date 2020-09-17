<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

$arNavParams = array(
	'page' => is_numeric($arGet['page']) ? $arGet['page'] : 1,
	'size' => is_numeric($arGet['size']) ? $arGet['size'] : 10,
);

$arProfileHistoryAll = array();
$arHistoryFilter = array(
	'PROFILE_ID' => $intProfileID,
);
$arQuery = [
	'filter' => $arHistoryFilter,
	'select' => array(
		'*',
	),
	'order' => array(
		'ID' => 'DESC',
	),
	'limit' => $arNavParams['size'],
	'offset' => ($arNavParams['page'] - 1) * $arNavParams['size']
];
#$resProfileHistory = History::getList($arQuery);
$resProfileHistory = Helper::call($strModuleId, 'History', 'getList', [$arQuery]);
while($arHistory = $resProfileHistory->fetch()){
	$arHistory['DATE_START'] = is_object($arHistory['DATE_START']) ? $arHistory['DATE_START']->toString() : '';
	$arHistory['DATE_END'] = is_object($arHistory['DATE_END']) ? $arHistory['DATE_END']->toString() : '';
	$arProfileHistoryAll[] = $arHistory;
}

# Nav object
$obNav = new \Bitrix\Main\UI\AdminPageNavigation('acrit-exp-nav-history');
$arQuery = [
	'filter' => $arHistoryFilter,
];
#$resProfileHistory = History::getList($arQuery);
$resProfileHistory = Helper::call($strModuleId, 'History', 'getList', [$arQuery]);
$obNav->setRecordCount($resProfileHistory->getSelectedRowsCount());
unset($resProfileHistory);
$obNav->setCurrentPage($arNavParams['page']);
$obNav->setPageSize($arNavParams['size']);

# Get users
$arUsers = array();
$arUsersID = array();
foreach($arProfileHistoryAll as $arHistory){
	if($arHistory['USER_ID']){
		$arUsersID[] = $arHistory['USER_ID'];
	}
}
$arUsersID = array_unique($arUsersID);
$arFilter = array(
	'ID' => implode('|', $arUsersID),
);
$arSelect = array(
	'ID',
	'NAME',
	'LAST_NAME',
	'LOGIN'
);
$resUsers = \CUser::getList($by='ID', $order='ASC', $arFilter, array('FIELDS'=>$arSelect));
while($arUser = $resUsers->getNext()){
	$arUser['FULL_NAME'] = trim($arUser['NAME'].' '.$arUser['LAST_NAME']);
	if(!strlen($arUser['FULL_NAME'])){
		$arUser['FULL_NAME'] = $arUser['LOGIN'];
	}
	$arUser['FULL_NAME'] = '[<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arUser['ID'].'" target="_blank">'.$arUser['ID'].'</a>] '.$arUser['FULL_NAME'];
	$arUsers[$arUser['ID']] = $arUser;
}

?>
<div data-role="profile-history-wrapper">
	<div>
		<input type="button" data-role="profile-history-refresh"
			value="<?=Loc::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_REFRESH');?>" />
	</div>
	<br/>
	<?if(!empty($arProfileHistoryAll)):?>
		<table class="adm-list-table acrit-exp-table-history">
			<thead>
				<tr class="adm-list-table-header">
					<td class="adm-list-table-cell" rowspan="2">
						<div class="adm-list-table-cell-inner">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_DATE_START');?>
						</div>
					</td>
					<td class="adm-list-table-cell" rowspan="2">
						<div class="adm-list-table-cell-inner">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_DATE_END');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:40px;" rowspan="2">
						<div class="adm-list-table-cell-inner" style="text-align:center">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_ELEMENTS_COUNT');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:120px;" colspan="2">
						<div class="adm-list-table-cell-inner" style="text-align:center">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_SUCCESS');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:120px;" colspan="2">
						<div class="adm-list-table-cell-inner" style="text-align:center">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_ERRORS');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:80px;" rowspan="2">
						<div class="adm-list-table-cell-inner">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_TIME');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:100px;" rowspan="2">
						<div class="adm-list-table-cell-inner">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_TYPE');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:100px;" rowspan="2">
						<div class="adm-list-table-cell-inner">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_USER_ID');?>
						</div>
					</td>
					<?/*
					<td class="adm-list-table-cell" style="width:100px;" rowspan="2">
						<div class="adm-list-table-cell-inner">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_IP');?>
						</div>
					</td>
					*/?>
				</tr>
				<tr class="adm-list-table-header">
					<td class="adm-list-table-cell" style="width:60px;">
						<div class="adm-list-table-cell-inner align-center">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_ELEMENTS');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:60px;">
						<div class="adm-list-table-cell-inner align-center">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_OFFERS');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:60px;">
						<div class="adm-list-table-cell-inner align-center">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_ELEMENTS');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:60px;">
						<div class="adm-list-table-cell-inner align-center">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_OFFERS');?>
						</div>
					</td>
				</tr>
			</thead>
			<tbody>
				<?foreach($arProfileHistoryAll as $arProfileHistory):?>
					<tr class="adm-list-table-row" data-stopped="<?=$arProfileHistory['STOPPED'];?>">
						<td class="adm-list-table-cell" style="white-space:nowrap;">
							<?=$arProfileHistory['DATE_START'];?>
							<?
							if(!is_null($arProfileHistory['MULTITHREADING'])){
								$strCommand = $arProfileHistory['COMMAND'];
								$bMultithreading = $arProfileHistory['MULTITHREADING'] == 'Y';
								$intThreads = is_numeric($arProfileHistory['THREADS']) ? $arProfileHistory['THREADS'] : '---';
								$intElementsPerThread = is_numeric($arProfileHistory['ELEMENTS_PER_THREAD']) ? $arProfileHistory['ELEMENTS_PER_THREAD'] : '---';
								$strHint = Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_'.($arProfileHistory['AUTO'] == 'Y' ? 'CRON' : 'MANUAL').'_HINT', array(
									'#COMMAND#' => strlen($strCommand) ? '<pre style="white-space:normal; word-break:break-all;">'.htmlspecialcharsbx($strCommand).'</pre>' : '???',
									'#MULTITHREADING#' => Loc::getMessage('MAIN_'.($bMultithreading ? 'YES' : 'NO')),
									'#THREADS#' => $intThreads,
									'#ELEMENTS_PER_THREAD#' => $intElementsPerThread,
									'#PID#' => is_numeric($arProfileHistory['PID']) ? $arProfileHistory['PID'] : '???',
									'#VERSION#' => is_numeric($arProfileHistory['PID']) ? $arProfileHistory['VERSION'] : '???',
								));
								print Helper::showHint($strHint);
							}
							?>
						</td>
						<td class="adm-list-table-cell" style="white-space:nowrap;">
							<?=$arProfileHistory['DATE_END'];?>
						</td>
						<td class="adm-list-table-cell align-center">
							<?=$arProfileHistory['ELEMENTS_COUNT'];?>
						</td>
						<td class="adm-list-table-cell align-center">
							<?=$arProfileHistory['ELEMENTS_Y'];?>
						</td>
						<td class="adm-list-table-cell align-center">
							<?=$arProfileHistory['OFFERS_Y'];?>
						</td>
						<td class="adm-list-table-cell align-center">
							<?=$arProfileHistory['ELEMENTS_N'];?>
						</td>
						<td class="adm-list-table-cell align-center">
							<?=$arProfileHistory['OFFERS_N'];?>
						</td>
						<td class="adm-list-table-cell align-right">
							<?=Helper::formatElapsedTime($arProfileHistory['TIME_TOTAL']);?>
						</td>
						<td class="adm-list-table-cell">
							<?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_AUTO_'.($arProfileHistory['AUTO']=='Y'?'Y':'N'));?>
						</td>
						<td class="adm-list-table-cell">
							<?if($arProfileHistory['USER_ID']):?>
								<?=$arUsers[$arProfileHistory['USER_ID']]['FULL_NAME'];?>
							<?endif?>
						</td>
						<?/*
						<td class="adm-list-table-cell align-right">
							<?=$arProfileHistory['IP'];?>
						</td>
						*/?>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
		<?/**/?>
		<script>
		AcritExpHistoryTable = {
			GetAdminList: function(url){
				if(params = url.match(/page-(\d+)-size-(\d+)/)){
					acritExpUpdateHistory(params[1], params[2]);
				}
			}
		}
		</script>
		<style>
		#tr_HISTORY .adm-nav-pages-number-block {
			display:none!important;
		}
		</style>
		<?
		$_REQUEST['admin_history'] = '';
		$APPLICATION->IncludeComponent(
			"bitrix:main.pagenavigation",
			"admin",
			array(
				"SEF_MODE" => "N",
				"NAV_OBJECT" => $obNav,
				"TITLE" => "",
				"PAGE_WINDOW" => 10,
				"SHOW_ALWAYS" => "Y",
				"TABLE_ID" => "AcritExpHistoryTable",
			),
			false,
			array(
				"HIDE_ICONS" => "Y",
			)
		);
		unset($_REQUEST['admin_history']);
		?>
		<?/**/?>
		<div style="clear:both"></div>
		<?/*<p><?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_LIMIT', array('#LIMIT#' => $intHistoryLimit));?></p>*/?>
	</div>
<?else:?>
	<p><?=Loc::getMessage('ACRIT_EXP_PROFILE_HISTORY_EMPTY');?></p>
<?endif?>