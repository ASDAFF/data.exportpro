<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

$arCategories = $obPlugin->getCategoriesList($intProfileID);

?>

<table class="acrit-exp-table-category-redefinition-select-value">
	<tbody>
		<tr>
			<td>
				<input type="text" value="" data-role="category-redefinition-search"
					placeholder="<?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_SELECT_TEXT_PLACEHOLDER');?>" />
			</td>
		</tr>
		<tr>
			<td>
				<select size="10" data-role="category-redefinition-select">
					<option value="" disabled="disabled"><?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_SELECT_NOT_FOUND');?></option>
					<?foreach($arCategories as $strCategoryName):?>
						<option value="<?=$strCategoryName;?>"><?=$strCategoryName;?></option>
					<?endforeach?>
				</select>
			</td>
		</tr>
	</tbody>
</table>




