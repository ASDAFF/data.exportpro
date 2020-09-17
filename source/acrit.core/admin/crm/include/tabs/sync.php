<?
namespace Acrit\Core\Crm;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Asset::getInstance()->addString('<style>
.run-disabled { pointer-events: none; cursor: default; color: #888; }
#tr_sync_man_run a.adm-btn.adm-btn-save { margin-left: 0; }
</style>');

$obTabControl->AddSection('HEADING_SYNC_MAN', Loc::getMessage('ACRIT_CRM_TAB_SYNC_HEADING'));
$obTabControl->BeginCustomField('PROFILE[SYNC][man]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_TITLE'));
?>
	<tr id="tr_sync_man_params">
		<td>
			<label for="field_sync_man_params"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
            <select name="PROFILE[SYNC][man][period]" id="field_sync_man_period">
                <option value=""<?=$arProfile['SYNC']['man']['period']==''?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_ALL');?></option>
                <option value="3m"<?=$arProfile['SYNC']['man']['period']=='3m'?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_3M');?></option>
                <option value="1m"<?=$arProfile['SYNC']['man']['period']=='1m'?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_1M');?></option>
                <option value="1w"<?=$arProfile['SYNC']['man']['period']=='1w'?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_1W');?></option>
                <option value="1d"<?=$arProfile['SYNC']['man']['period']=='1d'?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_1D');?></option>
            </select>
            <p>
                <input type="checkbox" name="PROFILE[SYNC][man][only_new]" value="y"<?=$arProfile['SYNC']['man']['only_new']=='y'?' checked':'';?> id="field_sync_man_only_new" />
                <label for="field_sync_man_only_new"><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_ONLY_NEW');?></label>
            </p>
		</td>
	</tr>
	<?
$obTabControl->EndCustomField('PROFILE[SYNC][man]');
$obTabControl->BeginCustomField('PROFILE[SYNC_MAN_RUN]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_RUN_TITLE'));
?>
	<tr id="tr_sync_man_run">
		<td>
			<label for="field_sync_man_run"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
            <a href="#" class="adm-btn adm-btn-save" id="man_sync_start" style="margin-bottom: 4px;"><?=GetMessage("ACRIT_EXP_RUNNOW_START")?></a>
            <a href="#" class="adm-btn adm-btn-disabled" id="man_sync_stop" style="margin-bottom: 4px;"><?=GetMessage("ACRIT_EXP_RUNNOW_STOP")?></a>
            <div id="start_export_progress">
                <div class="adm-info-message-wrap adm-info-message-gray">
                    <div class="adm-info-message">
                        <div class="adm-progress-bar-outer" style="width: 500px;">
                            <div class="adm-progress-bar-inner" style="width: 400px;">
                                <div class="adm-progress-bar-inner-text" style="width: 500px;">10%</div>
                            </div><span class="adm-progress-bar-outer-text">10%</span>
                        </div>
                        <div class="adm-info-message-buttons"></div>
                    </div>
                </div>
            </div>
            <div class="start-import-result" id="man_sync_result" style="display:none;">
                <div class="start-import-result-all"><?=GetMessage("ACRIT_EXP_VSEGO_OBRABOTANO")?><span>0</span></div>
                <div class="start-import-result-good"><?=GetMessage("ACRIT_EXP_USPESNO_IMPORTIROVAN")?><span>0</span></div>
                <div class="start-import-result-skip"><?=GetMessage("ACRIT_EXP_PROPUSENO")?><span>0</span></div>
                <div class="start-import-result-bad"><?=GetMessage("ACRIT_EXP_S_OSIBKAMI")?><span>0</span></div>
            </div>
		</td>
	</tr>
	<?
$obTabControl->EndCustomField('PROFILE[SYNC_MAN_RUN]');

$obTabControl->AddSection('HEADING_SYNC_ADD', Loc::getMessage('ACRIT_CRM_TAB_SYNC_ADD_TITLE'));
$obTabControl->BeginCustomField('PROFILE[SYNC][add][period]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_ADD_PERIOD'));
?>
	<tr id="tr_sync_add_period">
		<td>
			<label for="field_sync_add_period"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
			<input type="text" name="PROFILE[SYNC][add][period]" id="field_sync_add_period" value="<?=$arProfile['SYNC']['add']['period']?$arProfile['SYNC']['add']['period']:'5';?>" />
		</td>
	</tr>
	<?
$obTabControl->EndCustomField('PROFILE[SYNC][add][period]');

?>