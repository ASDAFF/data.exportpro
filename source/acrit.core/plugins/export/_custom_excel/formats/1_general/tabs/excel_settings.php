<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

if(is_null($arProfile['PARAMS']['CUSTOM_EXCEL']['FIELDS'])){
	$arProfile['PARAMS']['CUSTOM_EXCEL']['FIELDS'] = $obPlugin->getDefaultFields();
}

$obPlugin->prepareSheetTitle($arProfile['PARAMS']['CUSTOM_EXCEL']['SHEET_TITLE']);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// EXCEL structure for custom EXCEL
$obTabControl->BeginCustomField('PROFILE[CUSTOM_EXCEL_STRUCTURE]', Loc::getMessage('ACRIT_EXP_EXCEL_FIELD'));
?>
	<?if($obPlugin->areEditableColumns()):?>
		<tr class="heading">
			<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
		</tr>
		<tr id="tr_CUSTOM_EXCEL_FIELDS">
			<td colspan="2">
				<div data-role="excel-structure-wrapper">
					<textarea class="acrit-exp-custom-excel-structure" name="PROFILE[PARAMS][CUSTOM_EXCEL][FIELDS]"
						placeholder="<?=Loc::getMessage('ACRIT_EXP_EXCEL_FIELD_PLACEHOLDER');?>" style="height:200px"><?
						print htmlspecialcharsbx($arProfile['PARAMS']['CUSTOM_EXCEL']['FIELDS']);
					?></textarea>
				</div>
				<?=Helper::showNote(Loc::getMessage('ACRIT_EXP_EXCEL_FIELDS_NOTICE'));?>
			</td>
		</tr>
	<?endif?>
	<?if($obPlugin->areAdditionalSettingsAvailable()):?>
		<tr class="heading">
			<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_EXCEL_ADDITIONAL_SETTINGS');?></td>
		</tr>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_EXCEL_MULTISHEET_HINT'));?>
				<label for="acrit-exp-excel-multisheet"><?=Loc::getMessage('ACRIT_EXP_EXCEL_MULTISHEET');?>:</label>
			</td>
			<td width="60%">
				<input type="hidden" value="N" name="PROFILE[PARAMS][CUSTOM_EXCEL][MULTISHEET]" />
				<input type="checkbox" value="Y" name="PROFILE[PARAMS][CUSTOM_EXCEL][MULTISHEET]" id="acrit-exp-excel-multisheet"
					<?if($arProfile['PARAMS']['CUSTOM_EXCEL']['MULTISHEET']=='Y'):?>checked="checked"<?endif?> 
					data-role="custom-excel-multisheet" />
			</td>
		</tr>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_EXCEL_SHEET_TITLE_HINT'));?>
				<label for="acrit-exp-excel-sheet-title"><?=Loc::getMessage('ACRIT_EXP_EXCEL_SHEET_TITLE');?>:</label>
			</td>
			<td width="60%">
				<input type="text" name="PROFILE[PARAMS][CUSTOM_EXCEL][SHEET_TITLE]" id="acrit-exp-excel-sheet-title"
					value="<?=$arProfile['PARAMS']['CUSTOM_EXCEL']['SHEET_TITLE'];?>" data-role="custom-excel-sheet-title"
					size="32" maxlength="31" />
			</td>
		</tr>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_EXCEL_ADD_HEADER_HINT'));?>
				<label for="acrit-exp-excel-add-header"><?=Loc::getMessage('ACRIT_EXP_EXCEL_ADD_HEADER');?>:</label>
			</td>
			<td width="60%">
				<input type="hidden" value="N" name="PROFILE[PARAMS][CUSTOM_EXCEL][ADD_HEADER]" />
				<input type="checkbox" value="Y" name="PROFILE[PARAMS][CUSTOM_EXCEL][ADD_HEADER]" id="acrit-exp-excel-add-header"
					<?if($arProfile['PARAMS']['CUSTOM_EXCEL']['ADD_HEADER']!='N'):?>checked="checked"<?endif?> 
					data-role="custom-excel-add-header" />
			</td>
		</tr>
		<tr style="display:none">
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_EXCEL_FREEZE_HEADER_HINT'));?>
				<label for="acrit-exp-excel-freeze-header"><?=Loc::getMessage('ACRIT_EXP_EXCEL_FREEZE_HEADER');?>:</label>
			</td>
			<td width="60%">
				<input type="hidden" value="N" name="PROFILE[PARAMS][CUSTOM_EXCEL][FREEZE_HEADER]" />
				<input type="checkbox" value="Y" name="PROFILE[PARAMS][CUSTOM_EXCEL][FREEZE_HEADER]" id="acrit-exp-excel-freeze-header"
					<?if($arProfile['PARAMS']['CUSTOM_EXCEL']['FREEZE_HEADER']=='Y'):?>checked="checked"<?endif?> 
					data-role="custom-excel-freeze-header" />
			</td>
		</tr>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_EXCEL_ADD_UTM_HINT'));?>
				<label for="acrit-exp-excel-add-utm"><?=Loc::getMessage('ACRIT_EXP_EXCEL_ADD_UTM');?>:</label>
			</td>
			<td width="60%">
				<input type="hidden" value="N" name="PROFILE[PARAMS][CUSTOM_EXCEL][ADD_UTM]" />
				<input type="checkbox" value="Y" name="PROFILE[PARAMS][CUSTOM_EXCEL][ADD_UTM]" id="acrit-exp-excel-add-utm"
					<?if($arProfile['PARAMS']['CUSTOM_EXCEL']['ADD_UTM']=='Y'):?>checked="checked"<?endif?> 
					data-role="custom-excel-utm-toggle" />
			</td>
		</tr>
	<?endif?>
<?
$obTabControl->EndCustomField('PROFILE[CUSTOM_EXCEL_STRUCTURE]');

?>