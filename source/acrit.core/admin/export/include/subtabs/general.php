<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\Field\ValueBase;

Loc::loadMessages(__FILE__);

$arSavedValue = Helper::call($strModuleId, 'ProfileValue', 'loadFieldValuesAll', [$intProfileID, $intIBlockID, Profile::FIELD_SORT_ELEMENT]);
$obSortFieldElements = Helper::call($strModuleId, 'Profile', 'getFieldSortElement', [$intProfileID, $intIBlockID]);
$obSortFieldElements->setValue($arSavedValue);


$arSavedValue = Helper::call($strModuleId, 'ProfileValue', 'loadFieldValuesAll', [$intProfileID, $intIBlockOffersID, Profile::FIELD_SORT_OFFER]);
$obSortFieldOffers = Helper::call($strModuleId, 'Profile', 'getFieldSortOffer', [$intProfileID, $intIBlockOffersID]);
$obSortFieldOffers->setValue($arSavedValue);

?>

<table class="adm-list-table">
	<tbody>
		<tr class="heading"><td colspan="2"><?=Loc::getMessage('ACRIT_EXP_TAB_GENERAL_HEADER_SORT');?></td></tr>
		<tr>
			<td width="40%" style="padding-top:11px; vertical-align:top;">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_DATA_ELEMENTS_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_DATA_ELEMENTS');?>:
			</td>
			<td width="60%">
				<div data-role="sort-field" data-iblock-id="<?=$intIBlockID;?>"  data-name="<?=$obSortFieldElements->getName();?>" data-field="<?=$obSortFieldElements->getCode();?>" data-type="FIELD">
					<input type="hidden" value="FIELD"
						name="<?=ValueBase::INPUTNAME_DEFAULT;?>[<?=$intIBlockID;?>][<?=$obSortFieldElements->getCode();?>][field_type]" />
					<?=$obSortFieldElements->displayField();?>
				</div>
			</td>
		</tr>
		<?if($intIBlockOffersID):?>
			<tr>
				<td width="40%" style="padding-top:11px; vertical-align:top;">
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_DATA_OFFERS_HINT'));?>
					<?=Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_DATA_OFFERS');?>:
				</td>
				<td width="60%">
					<div data-role="sort-field" data-iblock-id="<?=$intIBlockOffersID;?>" data-name="<?=$obSortFieldOffers->getName();?>" data-field="<?=$obSortFieldOffers->getCode();?>" data-type="FIELD">
						<input type="hidden" value="FIELD"
							name="<?=ValueBase::INPUTNAME_DEFAULT;?>[<?=$intIBlockOffersID;?>][<?=$obSortFieldOffers->getCode();?>][field_type]" />
						<?=$obSortFieldOffers->displayField();?>
					</div>
				</td>
			</tr>
		<?endif?>
		<tr>
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_ORDER_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_ORDER');?>:
			</td>
			<td width="60%">
				<div>
					<select name="PROFILE[PARAMS][SORT_ORDER]" data-role="iblock-sort-field">
						<option value="ASC"<?if($arProfile['PARAMS']['SORT_ORDER']=='ASC'):?> selected="selected"<?endif?>>
							<?=Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_ORDER_ASC');?>
						</option>
						<option value="DESC"<?if($arProfile['PARAMS']['SORT_ORDER']=='DESC'):?> selected="selected"<?endif?>>
							<?=Loc::getMessage('ACRIT_EXP_TAB_GENERAL_SORT_ORDER_DESC');?>
						</option>
					</select>
				</div>
			</td>
		</tr>
	</tbody>
</table>


