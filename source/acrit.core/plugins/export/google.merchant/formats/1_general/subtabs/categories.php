<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

/*
$intMaxDepth = IntVal(Helper::getOption('categories_depth'));
$intMaxDepth = $intMaxDepth>=0 && $intMaxDepth<=9 ? $intMaxDepth : 0;
$arCategoriesAll = Helper::getIBlockSections($intIBlockID, $intMaxDepth);

$arCategoriesSaved = '';
$arSectionsID = explode(',', $arSavedIBlock['SECTIONS_ID']);
Helper::arrayRemoveEmptyValues($arSectionsID);
*/

?>
<table class="adm-list-table" style="display:none;">
	<tbody>
		<tr id="tr_GOOGLE_CATEGORIES_MODE">
			<td width="40%">
				<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_GOOGLE_CATEGORIES_MODE_HINT'));?>
				<?=Loc::getMessage('ACRIT_EXP_TAB_GOOGLE_CATEGORIES_MODE');?>:
			</td>
			<td>
				<?
				$arOptions = array(
					'russian' => Loc::getMessage('ACRIT_EXP_TAB_GOOGLE_CATEGORIES_MODE_RUSSIAN'),
					'english' => Loc::getMessage('ACRIT_EXP_TAB_GOOGLE_CATEGORIES_MODE_ENGLISH'),
					'numeric' => Loc::getMessage('ACRIT_EXP_TAB_GOOGLE_CATEGORIES_MODE_NUMERIC'),
				);
				$arOptions = array(
					'REFERENCE' => array_values($arOptions),
					'REFERENCE_ID' => array_keys($arOptions),
				);
				print SelectBoxFromArray('PROFILE[PARAMS][GOOGLE_CATEGORIES_MODE]', $arOptions, 
					$arProfile['PARAMS']['GOOGLE_CATEGORIES_MODE'], '', 'data-role="googe-categories-mode" ', ''
				);
				?>
			</td>
		</tr>
	</tbody>
</table>
<script>
$('#tr_GOOGLE_CATEGORIES_MODE').insertAfter($('#tr_CATEGORIES_REDEFINITION_MODE'));
</script>