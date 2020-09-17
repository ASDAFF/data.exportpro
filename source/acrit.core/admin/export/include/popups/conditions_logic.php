<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Filter;

Loc::loadMessages(__FILE__);

$strIBlockType = $arPost['iblock_type']=='offers' ? 'offers' : 'main';

$intFieldIBlockID = $intIBlockID;
if($strIBlockType=='offers') {
	$intFieldIBlockID = $intIBlockOffersID;
}
#$arAvailableElementFields = ProfileIBlock::getAvailableElementFieldsPlain($intFieldIBlockID);
$arAvailableElementFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$intFieldIBlockID]);

$strCurrentField = $arPost['current_field'];
$strCurrentLogic = $arPost['current_logic'];

$strType = '';
$arCurrentField = $arAvailableElementFields[$strCurrentField];
$arLogicAll = array();
if(is_array($arCurrentField)) {
	$strType = $arCurrentField['TYPE'];
	$strUserType = $arCurrentField['USER_TYPE'];
	if(strlen($strType)){
		$arLogicAll = Filter::getLogicAll($strType, $strUserType);
	}
}

?>

<?if(strlen($strType)):?>
	<input type="hidden" data-role="allow-save" />
	<table class="acrit-exp-field-select-table">
		<tbody>
			<tr>
				<td>
					<input type="text" value="" class="acrit-exp-field-select-text" data-role="entity-select-search"
						placeholder="<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_TEXT_PLACEHOLDER');?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<select class="acrit-exp-field-select-list" size="10" data-role="entity-select-item">
						<option value="" disabled="disabled" data-role="entity-select-item-not-found">
							<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_NOT_FOUND');?>
						</option>
						<?foreach($arLogicAll as $strLogic => $arLogic):?>
							<option value="<?=$strLogic;?>" <?if($strLogic==$strCurrentLogic):?> selected="selected"<?endif?>
								<?if($arLogic['HIDE_VALUE']):?>data-hide-value="Y"<?endif?>
								><?
								print $arLogic['NAME'];
							?></option>
						<?endforeach?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
<?else:?>
	<p><?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_NO_FIELD');?></p>
<?endif?>
