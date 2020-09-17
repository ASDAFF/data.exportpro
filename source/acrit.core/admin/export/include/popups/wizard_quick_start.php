<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter;

Helper::loadMessages(__FILE__);
Helper::loadMessages(realpath(__DIR__.'/../subtabs/offers.php'));
$strLang = 'ACRIT_EXP_POPUP_WIZARD_';

$arIBlocks = Helper::getIBlockList(true, false, false, true);
foreach($arIBlocks as $strIBlockType => $arIBlockType){
	foreach($arIBlockType['ITEMS'] as $intIBLockId => $arIBlock){
		if($arIBlock['CATALOG'] && $arIBlock['CATALOG']['PRODUCT_IBLOCK_ID']){
			unset($arIBlockType['ITEMS'][$intIBLockId]);
		}
	}
	if(empty($arIBlockType['ITEMS'])){
		unset($arIBlocks[$strIBlockType]);
	}
}

$arSites = Helper::getSitesList();

if($bSaveWizardQuickStart){
	set_time_limit(0);
	$bSuccess = false;
	$strSiteId = $arPost['site'];
	$arIBlockFields = [];
	foreach($arPost['iblocks'] as $intIBlockId){
		$arIBlockFields[$intIBlockId] = ProfileIBlockTable::getAvailableElementFieldsPlain($intIBlockId);
	}
	$arExistNames = [];
	$arExistFilenames = [];
	$arProfiles = Helper::call($strModuleId, 'Profile', 'getProfiles', [[], [], false, false, ['ID', 'NAME', 'PARAMS']]);
	foreach($arProfiles as $arProfile){
		$arExistNames[] = $arProfile['NAME'];
		if(isset($arProfile['PARAMS']['EXPORT_FILE_NAME'])){
			$strFilename = Helper::path(trim($arProfile['PARAMS']['EXPORT_FILE_NAME']));
			if(strlen($strFilename) && substr($strFilename, 0, 1) == '/' && substr($strFilename, 1, 2) != '/'){
				$arExistFilenames[] = $strFilename;
			}
		}
	}
	$arNewProfilesId = [];
	\Bitrix\Main\Application::getConnection()->startTransaction();
	foreach($arPost['formats'] as $strFormat){
		if($arPluginsPlain[$strFormat]['CLASS']){
			$intProfileId = false;
			$strPlugin = null;
			foreach($arPlugins as $arPlugin){
				foreach($arPlugin['FORMATS'] as $arFormat){
					if($arFormat['CODE'] == $strFormat){
						# Get profile name with index
						$strName = $arPluginsPlain[$strFormat]['NAME'];
						$intNameIndex = 0;
						while(true){
							$intNameIndex++;
							$strNameNew = $strName.($intNameIndex > 1 ? ' ('.$intNameIndex.')' : '');
							if(!in_array($strNameNew, $arExistNames)){
								break;
							}
						}
						# Create profile
						$arProfile = [
							'ACTIVE' => 'Y',
							'NAME' => $strNameNew,
							'SORT' => '100',
							'SITE_ID' => $strSiteId,
							'DOMAIN' => $arPost['domain'],
							'IS_HTTPS' => $arPost['https'] == 'Y' ? 'Y' : 'N',
							'PLUGIN' => $arPlugin['CODE'],
							'FORMAT' => $strFormat,
							'DATE_CREATED' => new \Bitrix\Main\Type\Datetime,
							'AUTO_GENERATE' => 'N',
							'LOCKED' => 'N',
							'ONE_TIME' => 'N',
							'PARAMS' => [],
							'LAST_IBLOCK_ID' => reset($arPost['iblocks']),
						];
						$obPlugin = new $arPluginsPlain[$strFormat]['CLASS']($strModuleId);
						$strExportFileName = $obPlugin->getDefaultExportFilename();
						if(strlen($strExportFileName)){
							$intIndex = 1;
							$strExportFileName = sprintf('/upload/%s/%s', $strModuleId, $strExportFileName);
							while(true){
								$strExportFileNameTest = $strExportFileName;
								if($intIndex > 1){
									$strExportFileNameTest = preg_replace('#\.([a-z0-9]+)$#', '_'.$intIndex.'.$1', $strExportFileName);
								}
								if(!in_array($strExportFileNameTest, $arExistFilenames)){
									$strExportFileName = $strExportFileNameTest;
									$arExistFilenames[] = $strExportFileName;
									break;
								}
								$intIndex++;
							}
							if(strlen($strExportFileName)){
								$arProfile['PARAMS']['EXPORT_FILE_NAME'] = $strExportFileName;
							}
						}
						$arProfile['PARAMS'] = serialize($arProfile['PARAMS']);
						$obResult = Helper::call($strModuleId, 'Profile', 'add', [$arProfile]);
						$intProfileId = $obResult->getId();
						if($intProfileId) {
							$bSuccess = true;
							$arExistNames[] = $arProfile['NAME'];
							# Prepare save iblocks
							$arIBlocks = [];
							foreach($arPost['iblocks'] as $intIBlockId){
								$arCatalog = Helper::getCatalogArray($intIBlockId);
								$arIBlocks[] = $intIBlockId;
								if($arCatalog['OFFERS_IBLOCK_ID']){
									$arIBlocks[] = $arCatalog['OFFERS_IBLOCK_ID'];
								}
							}
							$bMain = true;
							# Save iblocks
							foreach($arIBlocks as $intIBlockId){
								$arIBlock = [
									'SECTIONS_ID' => '',
									'SECTIONS_MODE' => 'all',
									'FILTER' => '',
									'PARAMS' => [
										'OFFERS_MODE' => $arPost['offers_mode'],
									],
									'FIELDS' => [],
								];
								if(!$bMain){
									unset($arIBlock['SECTIONS_ID'], $arIBlock['SECTIONS_MODE'], $arIBlock['PARAMS']);
								}
								$arFields = $obPlugin->getFields($intProfileId, $intIBlockId, false);
								foreach($arFields as $obField){
									$strField = $obField->getCode();
									$arField = $obField->getInitialParams();
									$arTypes = ['FIELD', 'CONDITION', 'MULTICONDITION'];
									$strFieldType = in_array($arField['DEFAULT_TYPE'], $arTypes) ? $arField['DEFAULT_TYPE'] : 'FIELD';
									$arSaveField = [
										'field_type' => $strFieldType,
										'field_params' => $arField['PARAMS'] ? http_build_query($arField['PARAMS']) : '',
										#'field_conditions' => [],
										'type' => [],
										'value' => [],
										'const' => [],
										'title' => [],
										'params' => [],
									];
									$arDefaultValues = $arField['DEFAULT_VALUE'];
									if(!$bMain && $arField['DEFAULT_VALUE_OFFERS']){
										$arDefaultValues = $arField['DEFAULT_VALUE_OFFERS'];
									}
									if(is_array($arDefaultValues)){
										foreach($arDefaultValues as $arValue){
											$strValueType = $arValue['TYPE'];
											$strValue = $strValueType == 'FIELD' ? $arValue['VALUE'] : '';
											$strConst = $strValueType == 'CONST' ? $arValue['CONST'] : '';
											$arParams = http_build_query($arValue['PARAMS'] ? $arValue['PARAMS'] : []);
											if($strFieldType == 'FIELD'){
												$arSaveField['type'][0][] = $strValueType;
												$arSaveField['value'][0][] = $strValue;
												$arSaveField['const'][0][] = $strConst;
												$arSaveField['title'][0][] = $strValue ? $arIBlockFields[$intIBlockId][$strValue]['NAME'] : '';
												$arSaveField['params'][0][] = $arParams;
											}
										}
									}
									$arIBlock['FIELDS'][$strField] = $arSaveField;
								}
								$arArguments = [$intProfileId, $intIBlockId, $arPluginsPlain[$strFormat]['CLASS'], $arIBlock];
								Helper::call($strModuleId, 'Profile', 'updateIBlockSettings', $arArguments);
								$bMain = false;
							}
							$arNewProfilesId[] = $intProfileId;
						}
						unset($obPlugin);
					}
				}
			}
		}
	}
	\Bitrix\Main\Application::getConnection()->commitTransaction();
	Helper::call($strModuleId, 'Profile', 'clearProfilesCache');
	if($arPost['run'] == 'Y' && !empty($arNewProfilesId)){
		Exporter::run($strModuleId, $arNewProfilesId);
	}
	$arJsonResult['Success'] = $bSuccess;
	if($bSuccess){
		$arJsonResult['SuccessMessage'] = Helper::getMessage($strLang.'SUCCESS_MESSAGE');
	}
	return;
}

?>
<div class="acrit_exp_wizard_quick_start_steps">

	<?# Plugins ?>
	<div class="acrit_exp_wizard_quick_start_step" data-step="1" 
		data-callback-in="acrit_exp_wizard_callback_in_plugins"
		data-callback-out="acrit_exp_wizard_callback_out_plugins">
		<div class="acrit_exp_wizard_quick_start_step_header"><?=Helper::getMessage($strLang.'PLUGINS_HEADER');?></div>
		<div class="acrit_exp_wizard_quick_start_filter">
			<input type="text" placeholder="<?=Helper::getMessage($strLang.'PLUGINS_FILTER_PLACEHOLDER');?>"
				data-role="acrit_exp_wizard_quick_start_plugins_filter" />
		</div>
		<div class="acrit_exp_wizard_plugins_wrapper">
			<div class="acrit_exp_wizard_plugins" data-role="acrit_exp_wizard_quick_start_plugins">
				<?foreach($arPlugins as $strPlugin => $arPlugin):?>
					<div class="acrit_exp_wizard_plugin" data-code="<?=$arPlugin['CODE']?>"
						data-filter="<?=htmlspecialcharsbx($arPlugin['NAME']);?> [<?=$arPlugin['CODE']?>]"
						data-role="acrit_exp_wizard_quick_start_plugin">
						<img class="acrit_exp_wizard_plugin_icon" src="<?=$arPlugin['ICON_BASE64']?>"
							title="<?=htmlspecialcharsbx($arPlugin['NAME']);?>"></img>
						<div class="acrit_exp_wizard_formats">
							<?foreach($arPlugin['FORMATS'] as $strFormat => $arFormat):?>
								<div class="acrit_exp_wizard_format" data-code="<?=$arFormat['CODE']?>"
									data-filter="<?=htmlspecialcharsbx($arFormat['NAME']);?> [<?=$arFormat['CODE']?>]"
									data-role="acrit_exp_wizard_quick_start_format">
									<div class="acrit_exp_wizard_format_name">
										<label>
											<input type="checkbox" name="formats[]" value="<?=$arFormat['CODE']?>"
												data-role="acrit_exp_wizard_quick_start_plugin_checkbox" />
											<?=$arFormat['NAME'];?>
										</label>
									</div>
								</div>
							<?endforeach?>
						</div>
					</div>
				<?endforeach?>
				<div class="acrit_exp_wizard_nothing_found" data-role="acrit_exp_wizard_quick_start_plugins_nothing_found">
					<?=Helper::getMessage($strLang.'NOTHING_FOUND');?>
				</div>
			</div>
		</div>
		<div class="acrit_exp_wizard_quick_start_controls">
			<div class="acrit_exp_wizard_quick_start_controls_status">
				<?=Helper::getMessage($strLang.'SELECTED');?>: <span data-role="acrit_exp_wizard_quick_start_selected">0</span>
			</div>
			<label>
				<input type="checkbox" data-role="acrit_exp_wizard_quick_start_select_all" />
				<?=Helper::getMessage($strLang.'SELECT_ALL');?>
			</label>
		</div>
	</div>
	
	<?# IBlocks ?>
	<div class="acrit_exp_wizard_quick_start_step" data-step="2" 
		data-callback-in="acrit_exp_wizard_callback_in_iblocks"
		data-callback-out="acrit_exp_wizard_callback_out_iblocks">
		<div class="acrit_exp_wizard_quick_start_step_header"><?=Helper::getMessage($strLang.'IBLOCKS_HEADER');?></div>
		<div class="acrit_exp_wizard_quick_start_filter">
			<input type="text" placeholder="<?=Helper::getMessage($strLang.'IBLOCKS_FILTER_PLACEHOLDER');?>"
				data-role="acrit_exp_wizard_quick_start_iblocks_filter" />
		</div>
		<div class="acrit_exp_wizard_iblock_types_wrapper">
			<div class="acrit_exp_wizard_iblock_types" data-role="acrit_exp_wizard_quick_start_iblocks">
				<?foreach($arIBlocks as $strIBlockType => $arIBlockType):?>
					<?if(!empty($arIBlockType['ITEMS'])):?>
						<div class="acrit_exp_wizard_iblock_type" data-code="<?=$arIBlockType['CODE']?>"
							data-filter="<?=htmlspecialcharsbx($arIBlockType['NAME']);?> [<?=$strIBlockType?>]"
							data-role="acrit_exp_wizard_quick_start_iblock_type">
							<div class="acrit_exp_wizard_iblock_icon" title="<?=htmlspecialcharsbx($arIBlockType['NAME']);?>"></div>
							<div class="acrit_exp_wizard_iblocks">
								<?foreach($arIBlockType['ITEMS'] as $intIBLockId => $arIBlock):?>
									<?
									$bCatalog = !empty($arIBlock['CATALOG']);
									$strTitle = sprintf('%s [%d, %s]', $arIBlock['NAME'], $arIBlock['ID'], $arIBlock['CODE']);
									if($bCatalog){
										$strTitle .= sprintf(' (%s)', toLower(Helper::getMessage($strLang.'IBLOCKS_CATALOG')));
									}
									?>
									<div class="acrit_exp_wizard_iblock <?if($bCatalog):?> acrit_exp_wizard_iblock_catalog<?endif?>" 
										data-code="<?=$arIBlock['CODE']?>" data-filter="<?=htmlspecialcharsbx($strTitle)?>"
										data-role="acrit_exp_wizard_quick_start_iblock">
										<div class="acrit_exp_wizard_iblock_name" title="<?=htmlspecialcharsbx($strTitle);?>">
											<label>
												<input type="checkbox" name="iblocks[]" value="<?=$arIBlock['ID']?>"
													data-role="acrit_exp_wizard_quick_start_iblock_checkbox"
													<?if($bCatalog):?>checked="checked"<?endif?> />
												<?=$arIBlock['NAME'];?> [<?=$arIBlock['ID'];?>, <?=$arIBlock['CODE'];?>]
											</label>
										</div>
									</div>
								<?endforeach?>
							</div>
						</div>
					<?endif?>
				<?endforeach?>
				<div class="acrit_exp_wizard_nothing_found" data-role="acrit_exp_wizard_quick_start_iblocks_nothing_found">
					<?=Helper::getMessage($strLang.'NOTHING_FOUND');?>
				</div>
			</div>
		</div>
		<div class="acrit_exp_wizard_quick_start_controls">
			<div class="acrit_exp_wizard_quick_start_controls_status">
				<?=Helper::getMessage($strLang.'SELECTED');?>: <span data-role="acrit_exp_wizard_quick_start_selected">0</span>
			</div>
			<label>
				<input type="checkbox" data-role="acrit_exp_wizard_quick_start_select_all" />
				<?=Helper::getMessage($strLang.'SELECT_ALL');?>
			</label>
		</div>
	</div>
	
	<?# Confirm ?>
	<div class="acrit_exp_wizard_quick_start_step" data-step="3"
		data-callback-in="acrit_exp_wizard_callback_in_confirm"
		data-callback-out="acrit_exp_wizard_callback_out_confirm">
		<table class="adm-detail-content-table edit-table acrit_exp_wizard_quick_start_confirm_table">
			<tbody>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::getMessage($strLang.'SITE');?>:
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<select name="site" data-role="acrit_exp_wizard_quick_start_site">
							<?foreach($arSites as $arSite):?>
								<option value="<?=$arSite['ID'];?>" data-domain="<?=$arSite['SERVER_NAME'];?>"
									<?if($arProfile['SITE_ID']==$arSite['ID']):?> selected="selected"<?endif?>>
									[<?=$arSite['ID'];?>]
									<?=$arSite['NAME'];?>
									<?if(strlen($arSite['SERVER_NAME'])):?>
										[<?=$arSite['SERVER_NAME'];?>]
									<?endif?>
								</option>
							<?endforeach?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::getMessage($strLang.'DOMAIN');?>:
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="domain" value="<?=htmlspecialcharsbx(Helper::getCurrentHost())?>"
							data-role="acrit_exp_wizard_quick_start_domain" />
						&nbsp;
						<span class="acrit_exp_wizard_quick_start_https">
							<label>
								<input type="checkbox" name="https" value="Y"<?if(Helper::isHttps()):?> checked="checked"<?endif?>
									data-role="acrit_exp_wizard_quick_start_https" />
								<?=Helper::getMessage($strLang.'HTTPS');?>
							</label>
						</span>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::getMessage($strLang.'OFFERS_MODE');?>:
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?
						$arOptions = array(
							'only' => Helper::getMessage('ACRIT_EXP_TAB_OFFERS_MODE_ONLY'),
							'all' => Helper::getMessage('ACRIT_EXP_TAB_OFFERS_MODE_ALL'),
							'none' => Helper::getMessage('ACRIT_EXP_TAB_OFFERS_MODE_NONE'),
							'offers' => Helper::getMessage('ACRIT_EXP_TAB_OFFERS_MODE_OFFERS'),
						);
						$arOptions = [
							'REFERENCE' => array_values($arOptions),
							'REFERENCE_ID' => array_keys($arOptions),
						];
						print SelectBoxFromArray('offers_mode', $arOptions, '', '', 
							'data-role="acrit_exp_wizard_quick_start_offers_mode"');
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<?=Helper::showNote(Helper::getMessage($strLang.'FINISH_NOTE'));?>
		<div>
			<label>
				<input type="checkbox" name="run" value="Y" data-role="acrit_exp_wizard_quick_start_run" />
				<?=Helper::getMessage($strLang.'RUN');?>
			</label>
		</div>
	</div>
	
</div>
