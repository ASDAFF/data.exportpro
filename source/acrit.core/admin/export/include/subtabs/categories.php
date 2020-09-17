<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition;

Loc::loadMessages(__FILE__);

$intMaxDepth = IntVal(Helper::getOption($ModuleID, 'categories_depth'));
$intMaxDepth = $intMaxDepth>=0 && $intMaxDepth<=9 ? $intMaxDepth : 0;
$arCategoriesAll = Helper::getIBlockSections($intIBlockID, $intMaxDepth);

$arCategoriesSaved = '';
$arSectionsID = explode(',', $arSavedIBlock['SECTIONS_ID']);
Helper::arrayRemoveEmptyValues($arSectionsID);

?>
<table class="adm-list-table">
	<tbody>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_SECTIONS_MODE_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_TAB_SECTIONS_MODE');?>:
			</td>
			<td width="60%">
				<?
				$arOptions = array(
					'selected' => Loc::getMessage('ACRIT_EXP_TAB_SECTIONS_MODE_SELECTED'),
					'selected_with_subsections' => Loc::getMessage('ACRIT_EXP_TAB_SECTIONS_MODE_SELECTED_WITH_SEBSECTIONS'),
					'all' => Loc::getMessage('ACRIT_EXP_TAB_SECTIONS_MODE_ALL'),
				);
				if(!array_key_exists($arSavedIBlock['SECTIONS_MODE'], $arOptions)){
					$arSavedIBlock['SECTIONS_MODE'] = 'all';
				}
				$arOptions = array(
					'REFERENCE' => array_values($arOptions),
					'REFERENCE_ID' => array_keys($arOptions),
				);
				print SelectBoxFromArray('iblock_sections_mode['.$intIBlockID.']', $arOptions, 
					$arSavedIBlock['SECTIONS_MODE'], '', 'data-role="sections-mode"');
				?>
				<script>
				$('select[data-role="sections-mode"]').trigger('change');
				</script>
			</td>
		</tr>
		<tr id="row_CATEGORIES_LIST">
			<td width="40%" valign="top">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_LIST_HINT', [
					'#LANGUAGE_ID#' => LANGUAGE_ID,
					'#MODULE_ID#' => $strModuleId,
				]));?>
				<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_LIST');?>:
			</td>
			<td width="60%">
				<div>
					<input type="hidden" name="iblock_sections_id[<?=$intIBlockID;?>]" value="" data-role="categories-id" />
					<select class="acrit-exp-categories" multiple="multiple" size="12" data-role="categories-list">
						<?foreach($arCategoriesAll as $arCategory):?>
							<?$bSelected = in_array($arCategory['ID'], $arSectionsID);?>
							<option value="<?=$arCategory['ID'];?>"<?if($bSelected):?>selected="selected"<?endif?>><?
								print str_repeat('.&nbsp;&nbsp;', $arCategory['DEPTH_LEVEL']-1);
								print $arCategory['NAME'];
								print ' ['.$arCategory['ID'].']';
							?></option>
						<?endforeach?>
					</select>
				</div>
				<div style="padding-top:4px;">
					<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_LIST_UNSELECT');?>"
						data-confirm="<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_LIST_UNSELECT_CONFIRM');?>"
						data-role="categories-unselect" />
				</div>
			</td>
		</tr>
		<?if(is_object($obPlugin) && $obPlugin->areCategoriesExport()):?>
			<tr id="tr_CATEGORIES_REDEFINITION_MODE">
				<td>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MODE_HINT'));?>
					<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MODE');?>
				</td>
				<td>
					<?
					$bIsCategoryStrict = false;
					$bHasCategoryList = false;
					if(is_object($obPlugin)) {
						$bIsCategoryStrict = $obPlugin->isCategoryStrict();
						$bHasCategoryList = $obPlugin->hasCategoryList();
					}
					$arOptions = array();
					if($bIsCategoryStrict) {
						$arOptions[CategoryRedefinition::MODE_STRICT] = Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MODE_STRICT');
					}
					else {
						$arOptions[CategoryRedefinition::MODE_CUSTOM] = Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MODE_CUSTOM');
						if($bHasCategoryList){
							$arOptions[CategoryRedefinition::MODE_STRICT] = Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MODE_STRICT');
						}
					}
					$bDisabled = count($arOptions) <= 1;
					if($bDisabled){
						$strDefaultOption = key($arOptions);
					}
					$arOptions = array(
						'REFERENCE' => array_values($arOptions),
						'REFERENCE_ID' => array_keys($arOptions),
					);
					print SelectBoxFromArray('PROFILE[PARAMS][CATEGORIES_REDEFINITION_MODE]', $arOptions, 
						$arProfile['PARAMS']['CATEGORIES_REDEFINITION_MODE'], '',
						'data-role="categories-redefinition-mode" '
						.'data-strict-value="'.CategoryRedefinition::MODE_STRICT.'"'
						.($bDisabled ? ' disabled="disabled"' : '')
					);
					?>
					<?if($bDisabled):?>
						<input type="hidden" name="PROFILE[PARAMS][CATEGORIES_REDEFINITION_MODE]" value="<?=$strDefaultOption;?>" />
					<?endif?>
				</td>
			</tr>
			<tr id="tr_CATEGORIES_REDEFINITION_SOURCE">
				<td>
					<?$bCategoryCustom = $obPlugin->isCategoryCustomName();?>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_HINT'.($bCategoryCustom ? '_CUSTOM' : '')));?>
					<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE');?>
				</td>
				<td>
					<?
					$bIsCategoryStrict = false;
					$bHasCategoryList = false;
					if(is_object($obPlugin)) {
						$bIsCategoryStrict = $obPlugin->isCategoryStrict();
						$bHasCategoryList = $obPlugin->hasCategoryList();
					}
					$arOptions = [
						CategoryRedefinition::SOURCE_REDEFINITIONS 
							=> Helper::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_REDEFINITIONS'),
						CategoryRedefinition::SOURCE_USER_FIELDS 
							=> Helper::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_USER_FIELDS'),
					];
					if($bCategoryCustom){
						$arOptions[CategoryRedefinition::SOURCE_CUSTOM] =
							Helper::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_CUSTOM');
					}
					$arOptions = [
						'REFERENCE' => array_values($arOptions),
						'REFERENCE_ID' => array_keys($arOptions),
					];
					print SelectBoxFromArray('iblockparams['.$intIBlockID.'][CATEGORIES_REDEFINITION_SOURCE]', $arOptions, 
						$arIBlockParams['CATEGORIES_REDEFINITION_SOURCE'], '',
						'data-role="categories-redefinition-source" '
						.'data-uf-value="'.CategoryRedefinition::SOURCE_USER_FIELDS.'"'
						.'data-custom-value="'.CategoryRedefinition::SOURCE_CUSTOM.'"'
					);
					?>
				</td>
			</tr>
			<tr id="tr_CATEGORIES_REDEFINITION_SOURCE_UF" style="display:none;">
				<td>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_UF_HINT'));?>
					<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_UF');?>
				</td>
				<td>
					<div class="acrit-exp-select-wrapper">
						<?
						$arUserFields = Helper::call($strModuleId, 'CategoryRedefinition', 'getSectionUserFields', 
							[$intProfileID, $intIBlockID]);
						$arUserFields = array_merge([
							'' => Helper::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_UF_EMPTY'),
						], $arUserFields);
						$arOptions = [
							'REFERENCE' => array_values($arUserFields),
							'REFERENCE_ID' => array_keys($arUserFields),
						];
						print SelectBoxFromArray('iblockparams['.$intIBlockID.'][CATEGORIES_REDEFINITION_SOURCE_UF]', $arOptions, 
							$arIBlockParams['CATEGORIES_REDEFINITION_SOURCE_UF'], '',
							'data-role="categories-redefinition-source-uf" '
						);
						?>
					</div>
				</td>
			</tr>
			<tr id="tr_CATEGORIES_REDEFINITION_BUTTON">
				<td>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MANAGE_HINT'));?>
					<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MANAGE');?>
				</td>
				<td>
					<?if(is_object($obPlugin) && $obPlugin->areCategoriesExport()):?>
						<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MANAGE_BUTTON');?>"
							data-role="categories-redefinition-button"/>
					<?endif?>
				</td>
			</tr>
			<tr id="tr_CATEGORIES_EXPORT_PARENTS">
				<td>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_EXPORT_PARENTS_HINT'));?>
					<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_EXPORT_PARENTS');?>
				</td>
				<td>
					<input type="hidden" name="PROFILE[PARAMS][CATEGORIES_EXPORT_PARENTS]" value="N" />
					<input type="checkbox" name="PROFILE[PARAMS][CATEGORIES_EXPORT_PARENTS]" value="Y" 
						<?if($arProfile['PARAMS']['CATEGORIES_EXPORT_PARENTS']=='Y'):?>checked="checked"<?endif?>
					/>
				</td>
			</tr>
			<?if(is_object($obPlugin) && $obPlugin->hasCategoryList() && !$obPlugin->hideCategoriesUpdateButton() && ($obPlugin->areCategoriesExport() || $obPlugin->areCategoriesUpdate())):?>
				<tr id="tr_CATEGORIES_UPDATE" style="display:none">
					<td>
						<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_UPDATE_HINT'));?>
						<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_UPDATE');?>
					</td>
					<td>
						<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_UPDATE_BUTTON');?>"
							data-role="categories-update" />
						&nbsp;
						<span data-role="categories-update-date">
							<?=Loc::getMessage('ACRIT_EXP_TAB_CATEGORIES_UPDATE_DATE');?>
							<span data-empty-value="---">
								<?$fTime = $obPlugin->getCategoriesDate();?>
								<?if($fTime):?>
									<?=Helper::formatUnixDatetime($obPlugin->getCategoriesDate());?>
								<?else:?>
									---
								<?endif?>
							</span>
						</span>
					</td>
				</tr>
				<tr id="tr_CATEGORIES_UPDATE_MESSAGE">
					<td style="padding:0;"></td>
					<td data-role="categories-update-error" style="padding:0;">
						<style>
							td[data-role="categories-update-error"] > span {display:inline-block; padding:4px 10px 16px;}
						</style>
					</td>
				</tr>
			<?endif?>
			<?if(strlen($strHtml = $obPlugin->categoriesCustomActions($intIBlockID, $arIBlockParams))):?>
				<?=$strHtml;?>
			<?endif?>
		<?endif?>
	</tbody>
</table>