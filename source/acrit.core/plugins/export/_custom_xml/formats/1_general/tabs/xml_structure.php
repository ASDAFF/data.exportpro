<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

if(is_null($arProfile['PARAMS']['CUSTOM_XML_STRUCTURE_GENERAL'])){
	$arProfile['PARAMS']['CUSTOM_XML_STRUCTURE_GENERAL'] = Helper::convertUtf8(file_get_contents(__DIR__.'/../default_xml/general.xml'));
}
if(is_null($arProfile['PARAMS']['CUSTOM_XML_STRUCTURE_CATEGORY'])){
	$arProfile['PARAMS']['CUSTOM_XML_STRUCTURE_CATEGORY'] = Helper::convertUtf8(file_get_contents(__DIR__.'/../default_xml/category.xml'));
}
if(is_null($arProfile['PARAMS']['CUSTOM_XML_STRUCTURE_CURRENCY'])){
	$arProfile['PARAMS']['CUSTOM_XML_STRUCTURE_CURRENCY'] = Helper::convertUtf8(file_get_contents(__DIR__.'/../default_xml/currency.xml'));
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// XML structure for custom XML
$obTabControl->BeginCustomField('PROFILE[CUSTOM_XML_STRUCTURE]', Loc::getMessage('ACRIT_EXP_XML_STRUCTURE'));
?>
	<tr class="heading">
		<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
	</tr>
	<tr id="tr_CUSTOM_XML_STRUCTURE">
		<td colspan="2">
			<div data-role="xml-structure-wrapper">
				<textarea class="acrit-exp-custom-xml-structure" name="PROFILE[PARAMS][CUSTOM_XML_STRUCTURE_GENERAL]" style="height:400px"><?
					print htmlspecialcharsbx($arProfile['PARAMS']['CUSTOM_XML_STRUCTURE_GENERAL']);
				?></textarea>
				<?=Helper::showNote($obPlugin::getMessage('CHECK_XML_VALID_NOTICE'), true);?>
				<?=Helper::showNote(Loc::getMessage('ACRIT_EXP_XML_STRUCTURE_AVAILABLE_MACROS', array('#MACROS#' => '#ITEMS#, #CATEGORIES#, #CURRENCIES#; #ENCODING#, #SITE_URL#, #DATETIME#')), true);?>
			</div><br/>
			<div>
				<table>
					<tbody>
						<tr>
							<td><?=Loc::getMessage('ACRIT_EXP_FORMAT_DATETIME');?>:</td>
							<td>
								<select name="PROFILE[PARAMS][FORMAT_DATETIME]" data-role="xml-format-date">
									<option value=".datetime"><?=Loc::getMessage('ACRIT_EXP_FORMAT_DATETIME_SITE');?></option>
									<option value=".date"><?=Loc::getMessage('ACRIT_EXP_FORMAT_DATE_SITE');?></option>
									<?foreach($obPlugin->getDateFormats() as $strDateFormat):?>
										<?$bSelected = $strDateFormat == $arProfile['PARAMS']['FORMAT_DATETIME'];?>
										<option value="<?=$strDateFormat;?>"<?if($bSelected):?> selected="selected"<?endif?>><?
											print date($strDateFormat);
										?></option>
									<?endforeach?>
									<?$bOther = $arProfile['PARAMS']['FORMAT_DATETIME'] == '.other';?>
									<option value=".other"<?if($bOther):?> selected="selected"<?endif?>>
										<?=Loc::getMessage('ACRIT_EXP_FORMAT_DATE_OTHER');?>
									</option>
								</select>
								<input type="text" name="PROFILE[PARAMS][FORMAT_DATETIME_OTHER]" size="20"
									value="<?=htmlspecialcharsbx($arProfile['PARAMS']['FORMAT_DATETIME_OTHER']);?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_XML_STRUCTURE_CATEGORY');?></td>
	</tr>
	<tr id="tr_CUSTOM_XML_STRUCTURE_CATEGORY">
		<td colspan="2">
			<div data-role="xml-structure-wrapper">
				<textarea class="acrit-exp-custom-xml-structure" name="PROFILE[PARAMS][CUSTOM_XML_STRUCTURE_CATEGORY]" style="height:50px"><?
					print htmlspecialcharsbx($arProfile['PARAMS']['CUSTOM_XML_STRUCTURE_CATEGORY']);
				?></textarea>
				<?=Helper::showNote($obPlugin::getMessage('CHECK_XML_VALID_NOTICE'), true);?>
				<?=Helper::showNote(Loc::getMessage('ACRIT_EXP_XML_STRUCTURE_AVAILABLE_MACROS', array(
					'#MACROS#' => '#ID#, #PARENT_ID#, #NAME#, #CODE#, #URL#, #EXTERNAL_ID#')
				), true);?>
			</div><br/>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage('ACRIT_EXP_XML_STRUCTURE_CURRENCY');?></td>
	</tr>
	<tr id="tr_CUSTOM_XML_STRUCTURE_CURRENCY">
		<td colspan="2">
			<div data-role="xml-structure-wrapper">
				<textarea class="acrit-exp-custom-xml-structure" name="PROFILE[PARAMS][CUSTOM_XML_STRUCTURE_CURRENCY]" style="height:50px"><?
					print htmlspecialcharsbx($arProfile['PARAMS']['CUSTOM_XML_STRUCTURE_CURRENCY']);
				?></textarea>
				<?=Helper::showNote($obPlugin::getMessage('CHECK_XML_VALID_NOTICE'), true);?>
				<?=Helper::showNote(Loc::getMessage('ACRIT_EXP_XML_STRUCTURE_AVAILABLE_MACROS', array('#MACROS#' => '#CURRENCY#, #RATE#')), true);?>
			</div><br/>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[CUSTOM_XML_STRUCTURE]');

?>