<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$arTypes = $this->getJsonFieldTypes();

?>
<div>
	<table class="acrit-exp-field-settings">
		<tbody>
			<tr class="heading">
				<td colspan="2"><?=Helper::getMessage('ACRIT_EXP_CUSTOM_JSON_FIELD_SETTINGS_GROUP_TOP');?></td>
			</tr>
			<tr>
				<td>
					<label><?=Helper::getMessage('ACRIT_EXP_CUSTOM_JSON_FIELD_TYPE');?>:</label>
				</td>
				<td>
					<select name="JSON_FIELD_TYPE">
						<option value=""><?=Helper::getMessage('ACRIT_EXP_CUSTOM_JSON_FIELD_TYPE_SAVE');?></option>
						<?foreach($arTypes as $strType => $arType):?>
							<option value="<?=$strType;?>"
								<?if($strType == $arParams['JSON_FIELD_TYPE']):?> selected="selected"<?endif?>
								><?=$arType['NAME'];?></option>
						<?endforeach?>
					</select>
					<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_CUSTOM_JSON_FIELD_TYPE_HINT'));?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<br/>