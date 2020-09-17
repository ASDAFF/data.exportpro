<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\ValueBase;

Loc::loadMessages(__FILE__);

$arCatalog = Helper::getCatalogArray($intIBlockID);

?>

<table class="adm-list-table">
	<tbody>
		<?if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID']):?>
			<tr>
				<td width="40%">
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_XML_SETTINGS_OFFERS_PREPROCESS_HINT'));?>
					<?=Loc::getMessage('ACRIT_EXP_XML_SETTINGS_OFFERS_PREPROCESS');?>:
				</td>
				<td width="60%">
					<input type="checkbox" value="Y" name="PROFILE[PARAMS][OFFERS_PREPROCESS]"
						<?if($arProfile['PARAMS']['OFFERS_PREPROCESS']=='Y'):?>checked="checked"<?endif?> />
				</td>
			</tr>
		<?endif?>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_XML_SETTINGS_ADD_UMT_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_XML_SETTINGS_ADD_UMT');?>:
			</td>
			<td width="60%">
				<input type="checkbox" value="Y" name="iblockparams[<?=$intIBlockID;?>][XML_ADD_UTM]"
					<?if($arIBlockParams['XML_ADD_UTM']=='Y'):?>checked="checked"<?endif?> />
			</td>
		</tr>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_XML_SETTINGS_ALL_CATEGORIES_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_XML_SETTINGS_ALL_CATEGORIES');?>:
			</td>
			<td width="60%">
				<input type="checkbox" value="Y" name="iblockparams[<?=$intIBlockID;?>][XML_ALL_CATEGORIES]"
					<?if($arIBlockParams['XML_ALL_CATEGORIES']=='Y'):?>checked="checked"<?endif?> />
			</td>
		</tr>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_XML_SETTINGS_DELETE_MODE_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_XML_SETTINGS_DELETE_MODE');?>:
			</td>
			<td width="60%">
				<?
				$arDeleteMode = array(
					$strPluginClass::DELETE_MODE_NO => Loc::getMessage('ACRIT_EXP_XML_SETTINGS_DELETE_MODE_NO'),
					$strPluginClass::DELETE_MODE_SIMPLE => Loc::getMessage('ACRIT_EXP_XML_SETTINGS_DELETE_MODE_SIMPLE'),
					$strPluginClass::DELETE_MODE_ATTR => Loc::getMessage('ACRIT_EXP_XML_SETTINGS_DELETE_MODE_ATTR'),
				);
				$arDeleteMode = array(
					'REFERENCE' => array_values($arDeleteMode),
					'REFERENCE_ID' => array_keys($arDeleteMode),
				);
				print SelectBoxFromArray('iblockparams['.$intIBlockID.'][XML_DELETE_MODE]', $arDeleteMode,
					$arIBlockParams['XML_DELETE_MODE'], '', '');
				?>
			</td>
		</tr>
	</tbody>
</table>


