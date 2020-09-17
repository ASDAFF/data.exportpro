<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Json;

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
$strCurrentValue = $arPost['current_value'];
$strCurrentValueTitle = $arPost['current_value_title'];
if(!Helper::isUtf()){
	$strCurrentValue = Helper::convertEncoding($strCurrentValue, 'UTF-8', 'CP1251');
	$strCurrentValueTitle = Helper::convertEncoding($strCurrentValueTitle, 'UTF-8', 'CP1251');
}
$arCurrentValue = explode(Filter::VALUE_SEPARATOR, $strCurrentValue);
foreach($arCurrentValue as $key => $value){
	if(is_string($value) && !strlen($value)) {
		unset($arCurrentValue[$key]);
	}
}

$arCurrentField = $arAvailableElementFields[$strCurrentField];
$arCurrentLogic = null;
if(is_array($arCurrentField)) {
	$strType = $arCurrentField['TYPE'];
	$strUserType = $arCurrentField['USER_TYPE'];
	if(strlen($strType) && strlen($strCurrentLogic)){
		$arLogicAll = Filter::getLogicAll($strType, $strUserType);
		$arCurrentLogic = $arLogicAll[$strCurrentLogic];
	}
}

// Text for value search
$strValueSearch = $arPost['q']; //htmlspecialcharsbx($arPost['q']);
if(!Helper::isUtf()){
	$strValueSearch = Helper::convertEncoding($strValueSearch, 'UTF-8', 'CP1251');
}
$strValueSearch = Helper::forSql($strValueSearch);

# Helpers for select items via AJAX
$bAjaxSelectItems = true;
$strCustomAction = $arGet['custom_action'];
$intResultsPerPage = 30;
$intPage = IntVal($arPost['page']) == 0 ? 1 : IntVal($arPost['page']);
$intIndex = 0;
$intIndexMin = ($intPage - 1) * $intResultsPerPage + 1;
$intIndexMax = ($intPage) * $intResultsPerPage;
$arJsonItems = array();
$intCount = 0;

// Load enums for PROPERTY_TYPE = L
if($strCustomAction=='load_items_l'){
	$intIBlockItemsID = IntVal($arCurrentField['DATA']['IBLOCK_ID']);
	if($intIBlockItemsID>0) {
		$arFilter = array(
			'PROPERTY_ID' => $arCurrentField['DATA']['ID'],
		);
		if(strlen($strValueSearch)){
			$arFilter[] = array(
				'LOGIC' => 'OR',
				array('ID' => $strValueSearch),
				array('%VALUE' => $strValueSearch),
			);
		}
		$resProps = \Bitrix\Iblock\PropertyEnumerationTable::getList(array(
			'filter' => $arFilter,
			'order' => array('VALUE' => 'ASC'),
		));
		while($arItem = $resProps->fetch()){
			$intIndex++;
			if($intIndex >= $intIndexMin && $intIndex <= $intIndexMax){
				$arJsonItems[] = array(
					'id' => IntVal($arItem['ID']),
					'text' => $arItem['VALUE'].' ['.$arItem['ID'].']',
				);
			}
		}
		$intCount = $intIndex;
	}
}
// Load enums for PROPERTY_TYPE = E
elseif($strCustomAction=='load_items_e'){
	$intIBlockItemsID = IntVal($arCurrentField['DATA']['LINK_IBLOCK_ID']);
	if($intIBlockItemsID>0) {
		$arFilter = array(
			'IBLOCK_ID' => $intIBlockItemsID,
		);
		if(strlen($strValueSearch)){
			$arFilterTmp = array(
				'LOGIC' => 'OR',
				'%NAME' => $strValueSearch,
				'%CODE' => $strValueSearch,
			);
			if(is_numeric($strValueSearch)){
				$arFilterTmp['ID'] = $strValueSearch;
			}
			$arFilter[] = $arFilterTmp;
		}
		$arNavParams = array(
			'iNumPage' => $intPage,
			'nPageSize' => $intResultsPerPage,
		);
		$resItems = \CIBlockElement::GetList(array(), $arFilter, false, $arNavParams, array('ID', 'NAME'));
		while($arItem = $resItems->GetNext()){
			$arJsonItems[] = array(
				'id' => IntVal($arItem['ID']),
				'text' => $arItem['~NAME'].' ['.$arItem['ID'].']',
			);
		}
		$intCount = IntVal($resItems->NavRecordCount);
	}
}
elseif($strCustomAction=='load_items_g'){
	$intIBlockItemsID = IntVal($arCurrentField['DATA']['LINK_IBLOCK_ID']);
	if($intIBlockItemsID>0) {
		$resSections = Filter::searchSectionsByText($intIBlockItemsID, $strValueSearch);
		while($arSection = $resSections->GetNext()){
			$intIndex++;
			if($intIndex >= $intIndexMin && $intIndex <= $intIndexMax){
				$arJsonItems[] = array(
					'id' => IntVal($arSection['ID']),
					'text' => $arSection['~NAME'].' ['.$arSection['ID'].']',
				);
			}
		}
		$intCount = $intIndex;
	}
}
elseif($strCustomAction=='load_sections'){
	$resSections = Filter::searchSectionsByText($intIBlockID, $strValueSearch);
	while($arSection = $resSections->GetNext()){
		$intIndex++;
		if($intIndex >= $intIndexMin && $intIndex <= $intIndexMax){
			$arJsonItems[] = array(
				'id' => IntVal($arSection['ID']),
				'text' => $arSection['~NAME'].' ['.$arSection['ID'].']',
			);
		}
	}
	$intCount = $intIndex;
}
// Load enums for PROPERTY_TYPE = S:directory
elseif($strCustomAction=='load_items_s_directory'){
	$strHlTableName = $arCurrentField['DATA']['USER_TYPE_SETTINGS']['TABLE_NAME'];
	if(strlen($strHlTableName) && \Bitrix\Main\Loader::includeModule('highloadblock')) {
		$arFilter = array();
		if(strlen($strValueSearch)){
			$arFilter[] = array(
				'LOGIC' => 'OR',
				'ID' => $strValueSearch,
				'%UF_NAME' => $strValueSearch,
				'%UF_XML_ID' => $strValueSearch,
			);
		}
		$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array(
			'filter' => array('TABLE_NAME'=>$strHlTableName))
		)->fetch();
		$obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
		$strEntityDataClass = $obEntity->getDataClass();
		$resData = $strEntityDataClass::GetList(array(
			'filter' => $arFilter,
			'select' => array('ID','UF_NAME','UF_XML_ID'),
			'order' => array('ID' => 'ASC'),
			'limit' => $intResultsPerPage,
			'offset' => ($intPage-1)*$intResultsPerPage
		));
		while($arItem = $resData->fetch()) {
			$arJsonItems[] = array(
				'id' => $arItem['UF_XML_ID'],
				'text' => $arItem['UF_NAME'],
			);
		}
		$intCount = IntVal($strEntityDataClass::getCount());
	}
}
// Load enums for PROPERTY_TYPE = N:_ID_LIST
elseif($strCustomAction=='load_items_n_id_list'){
	if($intIBlockID>0) {
		$arFilter = array(
			'IBLOCK_ID' => $intFieldIBlockID,
		);
		if(strlen($strValueSearch)){
			$arFilterTmp = array(
				'LOGIC' => 'OR',
				'%NAME' => $strValueSearch,
				'%CODE' => $strValueSearch,
			);
			if(is_numeric($strValueSearch)){
				$arFilterTmp['ID'] = $strValueSearch;
			}
			$arFilter[] = $arFilterTmp;
		}
		$arNavParams = array(
			'iNumPage' => $intPage,
			'nPageSize' => $intResultsPerPage,
		);
		$resItems = \CIBlockElement::GetList(array(), $arFilter, false, $arNavParams, array('ID', 'NAME'));
		while($arItem = $resItems->GetNext()){
			$arJsonItems[] = array(
				'id' => IntVal($arItem['ID']),
				'text' => $arItem['~NAME'].' ['.$arItem['ID'].']',
			);
		}
		$intCount = IntVal($resItems->NavRecordCount);
	}
}
else {
	$bAjaxSelectItems = false;
}
if($bAjaxSelectItems){
	$arJsonItems = array(
		'incomplete_results' => false,
		'items' => $arJsonItems,
		'total_count' => $intCount,
	);
	$GLOBALS['APPLICATION']->RestartBuffer();
	print Json::encode($arJsonItems);
	die();
}

?>

<?if(strlen($strType)):?>
	<?if(is_array($arCurrentLogic)):?>
		<input type="hidden" data-role="allow-save" />
		<div class="acrit-exp-field-select-wrapper acrit-exp-select-wrapper" id="acrit-exp-field-select-wrapper" style="padding-top:0;">
			<?$bMultiple = in_array($strCurrentLogic, array('IN_LIST', 'NOT_IN_LIST'));?>
			<?// TYPE = L ?>
			<?if($strType=='L' && $arCurrentField['IS_PROPERTY']):?>
				<?$arItems = Filter::getPropertyItems_L($arCurrentValue, $arCurrentField);?>
				<select name="value" class="acrit-exp-field-select-list" id="acrit-exp-field-select-l" 
					data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
					<?foreach($arItems as $strID => $strName):?>
						<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
					<?endforeach?>
				</select>
				<?
				$arSelect2 = array(
					'SELECT_ID' => 'acrit-exp-field-select-l',
					'CUSTOM_ACTION' => 'load_items_l',
				);
				?>
			<?// TYPE = E ?>
			<?elseif($strType=='E' && $arCurrentField['IS_PROPERTY']):?>
				<?$arItems = Filter::getPropertyItems_E($arCurrentValue, $arCurrentField);?>
				<select name="value" class="acrit-exp-field-select-list" id="acrit-exp-field-select-e" 
					data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
					<?foreach($arItems as $strID => $strName):?>
						<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
					<?endforeach?>
				</select>
				<?
				$arSelect2 = array(
					'SELECT_ID' => 'acrit-exp-field-select-e',
					'CUSTOM_ACTION' => 'load_items_e',
				);
				?>
			<?// TYPE = G ?>
			<?elseif($strType=='G' && $arCurrentField['IS_PROPERTY']):?>
				<?$arItems = Filter::getPropertyItems_G($arCurrentValue, $arCurrentField);?>
				<select name="value" class="acrit-exp-field-select-list" id="acrit-exp-field-select-g" 
					data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
					<?foreach($arItems as $strID => $strName):?>
						<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
					<?endforeach?>
				</select>
				<?
				$arSelect2 = array(
					'SELECT_ID' => 'acrit-exp-field-select-g',
					'CUSTOM_ACTION' => 'load_items_g',
				);
				?>
			<?// TYPE = S:directory ?>
			<?elseif($strType=='S' && $arCurrentField['IS_PROPERTY'] && $strUserType=='directory'):?>
				<?$arItems = Filter::getPropertyItems_S_directory($arCurrentValue, $arCurrentField);?>
				<select name="value" class="acrit-exp-field-select-list" id="acrit-exp-field-select-s-directory" 
					data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
					<?foreach($arItems as $strID => $strName):?>
						<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
					<?endforeach?>
				</select>
				<?
				$arSelect2 = array(
					'SELECT_ID' => 'acrit-exp-field-select-s-directory',
					'CUSTOM_ACTION' => 'load_items_s_directory',
				);
				?>
			<?// TYPE = S:_Currency ?>
			<?elseif($strType=='S' && $strUserType=='_Currency'):?>
				<?$arCurrencies = Helper::getCurrencyList();?>
				<select name="value" class="acrit-exp-field-select-list" id="acrit-exp-field-select-currency" 
					data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
					<?foreach($arCurrencies as $strCurrency => $arCurrency):?>
						<option value="<?=$strCurrency;?>"<?if(in_array($strCurrency, $arCurrentValue)):?> selected="selected"<?endif?>>[<?=$strCurrency;?>] <?=$arCurrency['FULL_NAME'];?></option>
					<?endforeach?>
				</select>
				<?
				$arSelect2 = array(
					'SELECT_ID' => 'acrit-exp-field-select-currency',
				);
				?>
			<?// TYPE = N:_ID_LIST ?>
			<?elseif($strType=='N' && $strUserType=='_ID_LIST'):?>
				<?$arItems = Filter::getPropertyItems_E($arCurrentValue, array());?>
				<select name="value" class="acrit-exp-field-select-list" id="acrit-exp-field-select-n-id-list" data-just-id="Y"
					data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
					<?foreach($arItems as $strID => $strName):?>
						<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
					<?endforeach?>
				</select>
				<?
				$arSelect2 = array(
					'SELECT_ID' => 'acrit-exp-field-select-n-id-list',
					'CUSTOM_ACTION' => 'load_items_n_id_list',
				);
				?>
			<?// TYPE = N:_SectionId ?>
			<?elseif($strType=='N' && $strUserType=='_SectionId'):?>
				<?$bMultiple = in_array($strCurrentLogic, array('IN_LIST', 'NOT_IN_LIST'));?>
				<?$arItems = Filter::getPropertyItems_G($arCurrentValue, $arCurrentField);?>
				<select name="value" class="acrit-exp-field-select-list" id="acrit-exp-field-select-g" 
					data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
					<?foreach($arItems as $strID => $strName):?>
						<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
					<?endforeach?>
				</select>
				<?
				$arSelect2 = array(
					'SELECT_ID' => 'acrit-exp-field-select-g',
					'CUSTOM_ACTION' => 'load_sections',
				);
				?>
				<?/*
				<?$arItems = Filter::getPropertyItems_E($arCurrentValue, array());?>
				<select name="value" class="acrit-exp-field-select-list" id="acrit-exp-field-select-n-id-list" data-just-id="Y"
					data-role="entity-select-value"<?if($bMultiple):?> multiple="multiple"<?endif?>>
					<?foreach($arItems as $strID => $strName):?>
						<option value="<?=$strID;?>" selected="selected"><?=$strName;?></option>
					<?endforeach?>
				</select>
				<?
				$arSelect2 = array(
					'SELECT_ID' => 'acrit-exp-field-select-n-id-list',
					'CUSTOM_ACTION' => 'load_items_n_id_list',
				);
				?>
				*/?>
			<?// TYPE = S, N ?>
			<?else:?>
				<?if($strType=='S' && ($strUserType=='DateTime' || $strUserType=='Date')):?>
					<?if(in_array($strCurrentLogic, ['FOR_THE_LAST', 'NOT_FOR_THE_LAST'])):?>
						<?
						$strDatetimeType = 'd';
						if($arDatetimeValue = Filter::parseDatetimeValue($strCurrentValue, $strCurrentField, true)){
							$strCurrentValue = $arDatetimeValue[1];
							$strDatetimeType = $arDatetimeValue[2];
						}
						$arDatetimeFilterValues = Filter::getDatetimeFilterValues($strUserType=='DateTime');
						?>
						<div class="acrit-exp-select-wrapper">
							<input type="hidden" name="value" value="<?=$strCurrentValue;?>" data-role="entity-select-value-hidden"/><br/>
							<input type="text" value="<?=$strCurrentValue;?>" data-role="datetime-for-the-last"/>
							<select data-role="datetime-for-the-last">
								<?foreach($arDatetimeFilterValues as $strDatetimeKey => $strDatetimeValue):?>
									<option value="<?=$strDatetimeKey;?>"
										<?if($strDatetimeType == $strDatetimeKey):?> selected="selected"<?endif?>
										><?=$strDatetimeValue;?></option>
								<?endforeach?>
							</select>
						</div>
						<script>
							$('[data-role="datetime-for-the-last"]').change(function(e){
								var input = $('input[data-role="datetime-for-the-last"]'),
									value = input.val(),
									select = $('select[data-role="datetime-for-the-last"]'),
									type = select.val(),
									inputResult = $(this).closest('div').find('input[data-role="entity-select-value-hidden"]');
								value = parseInt(value);
								if(isNaN(value) || value<0){
									value = 0;
								}
								var text = value + ' ' + $('option:selected', select).text();
								inputResult.val(value + type).attr('data-text', text);
							}).keydown(function(e){
								if(e.keyCode==13) {
									$(this).trigger('change');
									$('#acrit_exp_conditions_save').trigger('click');
								}
							}).trigger('change').filter('input[type="text"]').focus();
						</script>
					<?else:?>
						<div id="acrit-ext-field-select-date">
							<?=\CAdminCalendar::CalendarDate('value', $strCurrentValue, 15, $strUserType=='DateTime'?true:false);?>
						</div>
					<?endif?>
					<script>
					$('#acrit-ext-field-select-date input[type=text]').attr('data-role', 'entity-select-value');
					</script>
				<?elseif($bMultiple):?>
					<?
					if(!is_array($arCurrentValue) || empty($arCurrentValue)){
						$arCurrentValue = array(
							'',
						);
					}
					?>
					<div>
						<table class="acrit-exp-field-select-text-multiple" data-role="entity-select-value-multiple">
							<tbody>
								<?foreach($arCurrentValue as $strValue):?>
									<tr>
										<td>
											<input type="text" name="value" class="acrit-exp-field-select-text" data-role="entity-select-value"
												value="<?=htmlspecialcharsbx($strValue);?>"
												placeholder="<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_PLACEHOLDER_TEXT');?>"/>
										</td>
										<td>
											<a href="javascript:void(0)" title="Delete" data-role="entity-select-value-multiple-delete">&times;</a>
										</td>
									</tr>
								<?endforeach?>
							</tbody>
						</table>
					</div>
					<div>
						<input type="button" data-role="entity-select-value-multiple-add"
							value="<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_TEXT_ADD');?>" />
					</div>
				<?else:?>
					<input type="text" name="value" class="acrit-exp-field-select-text" data-role="entity-select-value"
						value="<?=htmlspecialcharsbx($strCurrentValue);?>"
						placeholder="<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_PLACEHOLDER_TEXT');?>"/>
				<?endif?>
			<?endif?>
			<?if(is_array($arSelect2)):?>
				<script>
				$('#<?=$arSelect2['SELECT_ID'];?>').select2({
					<?if(strlen($arSelect2['CUSTOM_ACTION'])):?>
						ajax: {
							url: '<?=$APPLICATION->GetCurPageParam('custom_action='.$arSelect2['CUSTOM_ACTION'],array('custom_action'));?>',
							type: 'post',
							dataType: 'json',
							data: function (params) {
								var query = $.extend({}, <?=Json::encode($arPost);?>, {
									q: params.term,
									page: params.page
								});
								return query;
							},
							processResults: function(data, params) {
								params.page = params.page || 1;
								return {
									results: data.items,
									pagination: {
										more: (params.page * <?=$intResultsPerPage;?>) < data.total_count
									}
								};
							},
							cache: false
						},
					<?endif?>
					dropdownParent: $('#acrit-exp-field-select-wrapper').closest('.bx-core-adm-dialog-content'),
					dropdownPosition: 'below',
					placeholder: '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_PLACEHOLDER_LIST');?>',
					language: '<?=LANGUAGE_ID;?>'
				}).bind('select2:select', function (e) {
					setTimeout(function(){
						$('.select2-search__field').focus();
					}, 10);
				}).next().find('.select2-search__field').each(function(){
					$(this).on('keydown', function(e){
						if(e.keyCode==27) {
							e.preventDefault();
						}
					});
				});
				</script>
			<?endif?>
		</div>
		<input type="hidden" data-role="filter-title" value="" />
		<input type="hidden" data-role="filter-value" value="" />
	<?else:?>
		<p><?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_NO_LOGIC');?></p>
	<?endif?>
<?else:?>
	<p><?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_FIELD_NO_FIELD');?></p>
<?endif?>
