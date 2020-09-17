<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

$arDefaultElementFields = $obPlugin->getDefaultFields();
$arDefaultOfferFields = $obPlugin->getDefaultFields(true);
if(is_null($arProfile['PARAMS']['JSON_ELEMENT_FIELDS'])){
	$arProfile['PARAMS']['JSON_ELEMENT_FIELDS'] = $arDefaultElementFields;
}
if(is_null($arProfile['PARAMS']['JSON_OFFER_FIELDS'])){
	$arProfile['PARAMS']['JSON_OFFER_FIELDS'] = $arDefaultOfferFields;
}

$arCurrentEncodeOptions = $arProfile['PARAMS']['JSON_ENCODE_OPTIONS'];
if(!is_array($arCurrentEncodeOptions)){
	$arCurrentEncodeOptions = $obPlugin->getDefaultEncodeOptions();
}
$arEncodeOptions = $obPlugin->getSupportedEncodeOptions();

// JSON structure
$obTabControl->BeginCustomField('PROFILE[JSON_STRUCTURE]', Helper::getMessage('ACRIT_EXP_JSON_STRUCTURE'));
?>
	<tr class="heading">
		<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
	</tr>
	<tr id="tr_JSON_ELEMENT_FIELDS">
		<td width="40%" style="padding-top:10px; vertical-align:top;">
			<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_JSON_STRUCTURE_GENERAL_HINT'));?>
			<label for="acrit-exp-json-add-header"><?=Helper::getMessage('ACRIT_EXP_JSON_STRUCTURE_GENERAL');?>:</label>
		</td>
		<td width="60%">
			<div data-role="json-structure-wrapper">
				<?
					$strStructure = $arProfile['PARAMS']['JSON_STRUCTURE'];
					if(!strlen($strStructure)){
						$strStructure = Helper::getMessage('ACRIT_EXP_JSON_STRUCTURE_EXAMPLE');
					}
				?>
				<textarea class="acrit-exp-custom-json-structure" name="PROFILE[PARAMS][JSON_STRUCTURE]"
					placeholder="<?=Helper::getMessage('ACRIT_EXP_JSON_STRUCTURE_PLACEHOLDER');?>" spellcheck="false"><?
					print htmlspecialcharsbx($strStructure);
				?></textarea>
			</div>
		</td>
	</tr>
	<tr id="tr_JSON_STRUCTURE_NOTICE">
		<td width="40%"></td>
		<td width="60%">
			<?=Helper::showNote(Helper::getMessage('ACRIT_EXP_JSON_STRUCTURE_NOTICE'), true);?>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[JSON_STRUCTURE]');
?>

<?// JSON fields
$obTabControl->BeginCustomField('PROFILE[JSON_FIELDS]', Helper::getMessage('ACRIT_EXP_JSON_FIELDS'));
?>
	<tr class="heading">
		<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
	</tr>
	<tr id="tr_JSON_ELEMENT_FIELDS">
		<td width="40%" style="padding-top:10px; vertical-align:top;">
			<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_JSON_ELEMENT_FIELDS_HINT'));?>
			<label for="acrit-exp-json-add-header"><?=Helper::getMessage('ACRIT_EXP_JSON_ELEMENT_FIELDS');?>:</label>
		</td>
		<td width="60%">
			<div data-role="json-fields-wrapper">
				<textarea class="acrit-exp-custom-json-fields" name="PROFILE[PARAMS][JSON_ELEMENT_FIELDS]"
					placeholder="<?=Helper::getMessage('ACRIT_EXP_JSON_FIELD_PLACEHOLDER', [
						'#EXAMPLE#' => implode(PHP_EOL, $arDefaultElementFields),
					]);?>" spellcheck="false"><?
					print htmlspecialcharsbx($arProfile['PARAMS']['JSON_ELEMENT_FIELDS']);
				?></textarea>
			</div>
		</td>
	</tr>
	<tr id="tr_JSON_OFFER_FIELDS">
		<td width="40%" style="padding-top:10px; vertical-align:top;">
			<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_JSON_OFFER_FIELDS_HINT'));?>
			<label for="acrit-exp-json-add-header"><?=Helper::getMessage('ACRIT_EXP_JSON_OFFER_FIELDS');?>:<br/>
			<?=Helper::getMessage('ACRIT_EXP_JSON_OFFER_FIELDS_NOTICE');?>
			</label>
		</td>
		<td width="60%">
			<div data-role="json-fields-wrapper">
				<textarea class="acrit-exp-custom-json-fields" name="PROFILE[PARAMS][JSON_OFFER_FIELDS]"
					placeholder="<?=Helper::getMessage('ACRIT_EXP_JSON_FIELD_PLACEHOLDER', [
						'#EXAMPLE#' => implode(PHP_EOL, $arDefaultOfferFields),
					]);?>" spellcheck="false"><?
					print htmlspecialcharsbx($arProfile['PARAMS']['JSON_OFFER_FIELDS']);
				?></textarea>
			</div>
		</td>
	</tr>
	<tr id="tr_JSON_FIELDS_NOTICE">
		<td width="40%"></td>
		<td width="60%">
			<?=Helper::showNote(Helper::getMessage('ACRIT_EXP_JSON_FIELDS_NOTICE'), true);?>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[JSON_FIELDS]');
?>

<?// JSON settings
$obTabControl->BeginCustomField('PROFILE[JSON_SETTINGS]', Helper::getMessage('ACRIT_EXP_JSON_SETTINGS'));
?>
	<tr class="heading">
		<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
	</tr>
	<tr id="tr_JSON_ADD_UTM">
		<td width="40%">
			<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_JSON_ADD_UTM_HINT'));?>
			<label for="acrit-exp-json-add-utm"><?=Helper::getMessage('ACRIT_EXP_JSON_ADD_UTM');?>:</label>
		</td>
		<td width="60%">
			<input type="hidden" value="N" name="PROFILE[PARAMS][JSON_ADD_UTM]" />
			<input type="checkbox" value="Y" name="PROFILE[PARAMS][JSON_ADD_UTM]" id="acrit-exp-json-add-utm"
				<?if($arProfile['PARAMS']['JSON_ADD_UTM']=='Y'):?>checked="checked"<?endif?> 
				data-role="custom-json-add-utm" />
		</td>
	</tr>
	<tr id="tr_JSON_UTM_FIELD" style="display:none;">
		<td width="40%">
			<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_JSON_UTM_FIELD_HINT'));?>
			<label for="acrit-exp-json-offers-preprocess-field"><?=Helper::getMessage('ACRIT_EXP_JSON_UTM_FIELD');?>:</label>
		</td>
		<td width="60%">
			<input type="text" name="PROFILE[PARAMS][JSON_UTM_FIELD]"
				value="<?=htmlspecialcharsbx($arProfile['PARAMS']['JSON_UTM_FIELD']);?>"
				id="acrit-exp-json-utm-field" />
		</td>
	</tr>
	<tr id="tr_OFFERS_PREPROCESS">
		<td width="40%">
			<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_JSON_OFFERS_PREPROCESS_HINT'));?>
			<label for="acrit-exp-json-offers-preprocess"><?=Helper::getMessage('ACRIT_EXP_JSON_OFFERS_PREPROCESS');?>:</label>
		</td>
		<td width="60%">
			<input type="hidden" value="N" name="PROFILE[PARAMS][JSON_OFFERS_PREPROCESS]" />
			<input type="checkbox" value="Y" name="PROFILE[PARAMS][JSON_OFFERS_PREPROCESS]"
				<?if($arProfile['PARAMS']['JSON_OFFERS_PREPROCESS']=='Y'):?>checked="checked"<?endif?>
				id="acrit-exp-json-offers-preprocess" data-role="custom-json-offers-preprocess" />
		</td>
	</tr>
	<tr id="tr_OFFERS_PREPROCESS_FIELD" style="display:none;">
		<td width="40%">
			<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_JSON_OFFERS_PREPROCESS_FIELD_HINT'));?>
			<label for="acrit-exp-json-offers-preprocess-field"><?=Helper::getMessage('ACRIT_EXP_JSON_OFFERS_PREPROCESS_FIELD');?>:</label>
		</td>
		<td width="60%">
			<input type="text" name="PROFILE[PARAMS][JSON_OFFERS_PREPROCESS_FIELD]"
				value="<?=htmlspecialcharsbx($arProfile['PARAMS']['JSON_OFFERS_PREPROCESS_FIELD']);?>"
				id="acrit-exp-json-offers-preprocess-field" />
		</td>
	</tr>
	<tr id="tr_TRANSFORM_FIELDS">
		<td width="40%">
			<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_JSON_TRANSFORM_FIELDS_HINT'));?>
			<label for="acrit-exp-json-transform-fields"><?=Helper::getMessage('ACRIT_EXP_JSON_TRANSFORM_FIELDS');?>:</label>
		</td>
		<td width="60%">
			<input type="text" name="PROFILE[PARAMS][JSON_TRANSFORM_FIELDS]" size="50"
				value="<?=htmlspecialcharsbx($arProfile['PARAMS']['JSON_TRANSFORM_FIELDS']);?>"
				id="acrit-exp-json-transform-fields" style="max-width:96%;" />
		</td>
	</tr>
	<tr id="tr_ENCODE_OPTIONS">
		<td width="40%">
			<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_JSON_ENCODE_OPTIONS_HINT'));?>
			<label for="acrit-exp-json-encode-options"><?=Helper::getMessage('ACRIT_EXP_JSON_ENCODE_OPTIONS');?>:</label>
		</td>
		<td width="60%" class="acrit-exp-custom-json-encode-options">
			<?foreach($obPlugin->getSupportedEncodeOptions() as $strOption):?>
				<div class="acrit-exp-custom-json-encode-option">
					<label>
						<input type="checkbox" name="PROFILE[PARAMS][JSON_ENCODE_OPTIONS][]" value="<?=$strOption;?>"
							<?if(in_array($strOption, $arCurrentEncodeOptions)):?> checked="checked"<?endif?>/>
						<span><?=Helper::getMessage('ACRIT_EXP_JSON_ENCODE_OPTION_'.$strOption)?>
							<code>[<?=$strOption;?>]</code></span>
					</label>
				</div>
			<?endforeach?>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[JSON_SETTINGS]');
?>