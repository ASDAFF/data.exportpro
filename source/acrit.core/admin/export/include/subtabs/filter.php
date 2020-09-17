<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Filter;

Loc::loadMessages(__FILE__);

if(is_null($arSavedIBlock['FILTER'])){
	$arSavedIBlock['FILTER'] = Filter::getConditionsJson($strModuleId, $intIBlockID, array(
		'FIELD' => 'ACTIVE',
		'LOGIC' => 'CHECKED',
	));
}

$obSortFieldElements = Helper::call($strModuleId, 'Profile', 'getFieldSortElement', [$intProfileID, $intIBlockID]);
$obSortFieldElements->setValue($arSavedValue);

// Show filter
$obFilter = new Filter($strModuleId, $intIBlockID);
$obFilter->setInputName('iblockfilter['.$intIBlockID.']');
$obFilter->setJson($arSavedIBlock['FILTER']);
print $obFilter->show();
$obFilter->buildFilter();
unset($obFilter);
?>

<br/>
<table class="adm-list-table">
	<tbody>
		<tr class="heading"><td colspan="2"><?=Loc::getMessage('ACRIT_EXP_TAB_FILTER_SETTINGS');?></td></tr>
		<tr>
			<td width="40%" style="padding-top:11px; vertical-align:top;">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_FILTER_INCLUDE_SUBSECTIONS_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_TAB_FILTER_INCLUDE_SUBSECTIONS');?>:
			</td>
			<td width="60%">
				<div data-role="sort-field" data-iblock-id="<?=$intIBlockID;?>"  data-name="<?=$obSortFieldElements->getName();?>" data-field="<?=$obSortFieldElements->getCode();?>" data-type="FIELD">
					<?
					$arIncludeSubsections = array(
						'N' => Loc::getMessage('ACRIT_EXP_TAB_FILTER_INCLUDE_SUBSECTIONS_N'),
						'Y' => Loc::getMessage('ACRIT_EXP_TAB_FILTER_INCLUDE_SUBSECTIONS_Y'),
					);
					$arIncludeSubsections = array(
						'reference' => array_values($arIncludeSubsections),
						'reference_id' => array_keys($arIncludeSubsections),
					);
					print SelectBoxFromArray('iblockparams['.$intIBlockID.'][FILTER_INCLUDE_SUBSECTIONS]', $arIncludeSubsections,
						$arIBlockParams['FILTER_INCLUDE_SUBSECTIONS'], false, '');
					?>
				</div>
			</td>
		</tr>
	</tbody>
</table>