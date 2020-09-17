<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

# All fields for plugin
$arPluginFields = $obPlugin->getFields($intProfileID, $intThisIBlockID, true);
foreach($arPluginFields as $key => $obPluginField){
	$arPluginFields[$key]->setModuleId($strModuleId);
}

# All fields that are saved
#$arSavedFields = ProfileField::loadSavedFields($intProfileID, $intThisIBlockID);
$arSavedFields = Helper::call($strModuleId, 'ProfileField', 'loadSavedFields', [$intProfileID, $intThisIBlockID]);

# Get saved values
#$arSavedValues = ProfileValue::loadFieldValuesAll($intProfileID, $intThisIBlockID);
$arSavedValues = Helper::call($strModuleId, 'ProfileValue', 'loadFieldValuesAll', [$intProfileID, $intThisIBlockID]);

?>

<?if($intThisIBlockID):?>
	<input type="hidden" name="iblock_id[]" value="<?=$intThisIBlockID;?>" />
	<div data-role="iblock-structure" data-iblock-id="<?=$intThisIBlockID;?>">
		<div>
			<table class="adm-list-table acrit-exp-fields-table">
				<thead>
					<tr class="adm-list-table-header">
						<td class="adm-list-table-cell acrit-exp-fields-table-code">
							<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
								<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_COL_FIELD');?>
							</div>
						</td>
						<td class="adm-list-table-cell acrit-exp-fields-table-type">
							<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
								<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_COL_TYPE');?>
							</div>
						</td>
						<td class="adm-list-table-cell acrit-exp-fields-table-value">
							<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
								<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_COL_VALUE');?>
							</div>
						</td>
						<td class="adm-list-table-cell acrit-exp-fields-table-settings">
							<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
								<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_COL_PARAMS');?>
							</div>
						</td>
					</tr>
				</thead>
				<?if($obPlugin->areAdditionalFieldsSupported()):?>
					<tfoot>
						<tr>
							<td colspan="4">
								<?if(!$bCopy):?>
									<div class="acrit-exp-fields-table-settings-tfoot-left">
										<input type="button" data-role="additional-field-add"
											value="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_ADDITIONAL_FIELD_ADD');?>" 
										/>
										&nbsp;
										<input type="button" data-role="additional-field-add-multiple"
											value="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_ADDITIONAL_FIELD_ADD_MULTIPLE');?>" 
										/>
									</div>
									<div class="acrit-exp-fields-table-settings-tfoot-right">
										<input type="button" data-role="additional-field-delete-all"
											value="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_ADDITIONAL_FIELD_DELETE_ALL');?>" 
										/>
									</div>
								<?else:?>
									<?
									$bAdditionalFieldsExists = false;
									$arQuery = [
										'filter' => array(
											'PROFILE_ID' => $intProfileID,
											'IBLOCK_ID' => $intThisIBlockID,
										),
										'limit' => '1',
									];
									#$resFields = AdditionalFieldTable::getList($arQuery);
									$resFields = Helper::call($strModuleId, 'AdditionalField', 'getList', [$arQuery]);
									if($arField = $resFields->fetch()){
										$bAdditionalFieldsExists = true;
									}
									if($bAdditionalFieldsExists){
										print Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_ADDITIONAL_FIELD_COPY_NOTICE_IF_EXISTS');
									}
									else{
										print Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_ADDITIONAL_FIELD_COPY_NOTICE'); 
									}
									?>
								<?endif?>
							</td>
						</tr>
					</tfoot>
				<?endif?>
				<tbody>
					<?foreach($arPluginFields as $obPluginField):?>
						<?
						$strFieldCode = $obPluginField->getCode();
						$obPluginField->setProfileID($intProfileID);
						$obPluginField->setIBlockID($intThisIBlockID);
						$obPluginField->setValue($arSavedValues[$strFieldCode]);
						$obPluginField->setType($arSavedFields[$strFieldCode]['TYPE']);
						$obPluginField->setConditions($arSavedFields[$strFieldCode]['CONDITIONS']);
						$obPluginField->setParams($arSavedFields[$strFieldCode]['PARAMS']);
						$obPluginField->setCopyProfileMode($bCopy);
						print $obPluginField->displayRow();
						?>
					<?endforeach?>
				</tbody>
			</table>
		</div>
		<hr/>
		<br/>
	</div>
<?else:?>
	<p><?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_EMPTY');?></p>
<?endif?>
