<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

$bCatalogModule = \Bitrix\Main\Loader::includeModule('catalog');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Block for tags management
$obTabControl->BeginCustomField('PROFILE[IBLOCK_MANAGE]', Loc::getMessage('ACRIT_EXP_IBLOCKS_MANAGE'));
?><tr class="heading" id="tr_IBLOCKS_MANAGE_HEADING">
		<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
	</tr>
	<tr id="tr_IBLOCKS_MANAGE">
		<td colspan="2">
			<div>
				<?if(!$bCopy):?>
					<div class="acrit-exp-button-preview-iblocks">
						<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_TAB_STRUCTURE_IBLOCK_PREVIEW');?>"
							data-role="preview-iblocks" />
					</div>
				<?endif?>
				<div id="field_IBLOCK_title">
					<span><?=Loc::getMessage('ACRIT_EXP_TAB_STRUCTURE_IBLOCK_TITLE');?></span>
					<span style="vertical-align:middle">
						<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_STRUCTURE_IBLOCK_TITLE_HINT'));?>
					</span>
					&nbsp;
				</div>
				<div id="field_IBLOCK_value" style="position:relative;">
					<div class="acrit-exp-select-wrapper">
						<select id="field_IBLOCK"<?if($arProfile['LAST_IBLOCK_ID']):?> data-loaded="Y"<?endif?>>
							<?require __DIR__.'/_structure_iblock_select.php';?>
						</select>
						<a href="#" id="field_IBLOCK_clear" data-role="iblock-settings-clear"
							title="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_BUTTON_CLEAR');?>"></a>
					</div>
					<?if($bCatalogModule):?>
						<div id="field_IBLOCK_just_catalogs">
							<label>
								<input type="hidden" name="PROFILE[PARAMS][SHOW_JUST_CATALOGS]" value="N" />
								<input type="checkbox" name="PROFILE[PARAMS][SHOW_JUST_CATALOGS]" value="Y" data-role="show-just-catalogs"
									<?if($arProfile['PARAMS']['SHOW_JUST_CATALOGS']=='Y'):?>checked="checked"<?endif?>/>
								<?=Loc::getMessage('ACRIT_EXP_TAB_STRUCTURE_IBLOCK_SHOW_JUST_CATALOGS');?>
							</label>
							<span style="margin-left:-4px;vertical-align:middle">
								<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_STRUCTURE_IBLOCK_SHOW_JUST_CATALOGS_HINT'));?>
							</span>
						</div>
					<?endif?>
				</div>
			</div>
			<br/><br/>
			<?if(Helper::getOption($strModuleId, 'show_iblock_multiple_notice') == 'Y'):?>
				<div data-role="multiple-iblocks-note"><?=$strIBlocksMultipleNotice;?></div>
			<?endif?>
			<?if($bCatalogModule):?><br/><?endif?>
			<div id="field_IBLOCK_content">
				<?/**/?>
				<?if($arProfile['LAST_IBLOCK_ID']):?>
					<?
					$intIBlockID = $arProfile['LAST_IBLOCK_ID'];
					$arCatalog = Helper::getCatalogArray($intIBlockID);
					if($arCatalog['OFFERS_IBLOCK_ID']){
						$intIBlockOffersID = $arCatalog['OFFERS_IBLOCK_ID'];
					}
					require __DIR__.'/_structure_iblock_all.php';
					?>
					<?if(strlen($arProfile['LAST_SETTINGS_TAB'])):?>
						<script>
							var initialTab = $('#view_tab_<?=$arProfile['LAST_SETTINGS_TAB'];?>');
							if(initialTab.length) {
								window.acritExpInitialSettingsTabClick = true;
								initialTab.trigger('click');
								window.acritExpInitialSettingsTabClick = false;
							}
						</script>
					<?endif?>
				<?endif?>
				<?/**/?>
			</div>
		</td>
	</tr><?
$obTabControl->EndCustomField('PROFILE[IBLOCK_MANAGE]');

?>