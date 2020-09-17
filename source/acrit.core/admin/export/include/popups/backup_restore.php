<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Backup;

Loc::loadMessages(__FILE__);

$strFormAction = $APPLICATION->GetCurPageParam('ajax_action=backup_restore', array('ajax_action'));

?>

<style>
table.acrit-exp-backup-restore {
	width:100%;
}
table.acrit-exp-backup-restore td:first-child{
	padding-right:6px;
	text-align:right;
	width:35%;
}
table.acrit-exp-backup-restore td:last-child{
	width:65%;
}
table.acrit-exp-backup-restore td:only-child{
	text-align:left;
	width:auto;
}
table.acrit-exp-backup-restore td .file_wrapper {
	overflow:hidden;
	position:relative;
}
table.acrit-exp-backup-restore td .file_wrapper input[type=file]{
	cursor:text;
	height:10000px;
	left:-1000px;
	opacity:0;
	position:absolute;
	top:-1000px;
	width:10000px;
}
table.acrit-exp-backup-restore td .file_wrapper input[type=text]{
	width:100%;
	-webkit-box-sizing:border-box;
	   -moz-box-sizing:border-box;
	        box-sizing:border-box;
}
</style>
<div style="display:none;">
	<iframe id="iframe_backup_restore" name="iframe_backup_restore" onload="acritExpRestoreIFrameLoaded(this)"></iframe>
</div>
<form action="<?=$strFormAction;?>" method="post" enctype="multipart/form-data" target="iframe_backup_restore" id="acrit-exp-form-backup-restore">
	<table class="acrit-exp-backup-restore">
		<tbody>
			<tr>
				<td>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_FILE_HINT'));?>
					<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_FILE');?>:
				</td>
				<td>
					<div class="file_wrapper">
						<input type="file" name="backup" />
						<input type="text" placeholder="<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_FILE_PLACEHOLDER');?>" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_MODE_HINT'));?>
					<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_MODE');?>:
				</td>
				<td>
					<?
					$arModes = Backup::getModes();
					$arModes = array(
						'reference' => array_values($arModes),
						'reference_id' => array_keys($arModes),
					);
					print SelectBoxFromArray('mode', $arModes,'', false, 'data-role="backup-restore-mode"');
					?>
					&nbsp;
					<span data-role="backup-restore-exact-warning" style="display:none">
						<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_MODE_WARNING');?>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_DELETE_ALL_HINT'));?>
					<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_DELETE_ALL');?>:
				</td>
				<td>
					<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_DELETE_ALL_BUTTON');?>"
						data-role="profiles-delete-all"
						data-confirm="<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_DELETE_ALL_CONFIRM');?>" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div data-role="restore-status"></div>
				</td>
			</tr>
		</tbody>
	</table>
</form>
