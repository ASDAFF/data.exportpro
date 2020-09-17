// Ajax-request general
/**
 *	Examples:
 *	ajaxAction = 'load_structure_iblock';
 *	ajaxAction = ['plugin_ajax_action','get_props_for_additional_fields'];
 */
function acritExpAjax(ajaxAction, data, callbackSuccess, callbackError, post, hideLoader){
	var lang = phpVars.LANGUAGE_ID,//$('#param__lang').val(),
		profileId = $('#param__profile_id').val(),
		currentPlugin = $('#param__plugin').val(),
		currentFormat = $('#param__format').val();
		copy = $('#param__copy').val();
	//
	if(typeof data == 'string' && data.substr(0,1)=='&'){
		data = data.substr(1);
	}
	//
	if(hideLoader!==true) {
		BX.showWait();
	}
	var action = '';
	if($.isArray(ajaxAction)) {
		action = ajaxAction[1];
		ajaxAction = ajaxAction[0];
	}
	if(action.length){
		action = '&action='+action;
	}
	return $.ajax({
		url: location.pathname+'?ID='+profileId+'&lang='+lang+'&ajax_action='+ajaxAction+action
			+'&plugin='+currentPlugin+'&format='+currentFormat+'&copy='+copy,
		type: post==true ? 'POST' : 'GET',
		data: data,
		datatype: 'json',
		success: function(data, textStatus, jqXHR){
			if(typeof callbackSuccess == 'function') {
				jqXHR._ajax_action = ajaxAction;
				callbackSuccess(data, textStatus, jqXHR);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
			if(typeof data.ServerTime == 'string'){
				acritExpRefreshServerTime(data.ServerTime);
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			jqXHR._ajax_action = ajaxAction;
			if(typeof callbackError == 'function') {
				callbackError(jqXHR, textStatus, errorThrown);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		}
	});
}

/**
 *	Set cookie
 */
function acritExpCookie(key, value){
	var params = {path:'/',domain:location.host},
		moduleId = acritExpGetModuleId(),
		profileId = $('#param__profile_id').val(),
		cookieId = 'acrit_'+moduleId+'_'+profileId+'_'+key;
	if(value == undefined){
		return $.cookie(cookieId);
	}
	else {
		return $.cookie(cookieId, value, params);
	}
}

function acritExpGetModuleId(){
	return location.pathname.replace(/\/bitrix\/admin\/acrit_(.*?)_.*?$/g, '$1');
}

function acritExpHandleAjaxError(jqXHR, isError){
	if(isError) {
		if(jqXHR){
			console.log(jqXHR);
			console.log('Response: ' + jqXHR.responseText);
		}
	}
	if(jqXHR.status != 200 && jqXHR.status != 0) {
		console.log('HTTP error: ' + jqXHR.status + ' ' + jqXHR.statusText);
	}
	if(jqXHR.responseText && jqXHR.responseText.indexOf('bx-admin-auth-form')!=-1) {
		alert(BX.message('ACRIT_EXP_AJAX_AUTH_REQUIRED'));
	}
	var exclude = ['export_execute','load_popup_select_field','load_popup_value_settings','load_popup_field_settings',
		'categories_redefinition_show','categories_redefinition_select','save_last_settings_tab','reload_iblocks',
		'load_structure_iblock','iblocks_preview','history_refresh'];
	/*
	if(jqXHR._ajax_action != 'log_refresh' && exclude.indexOf(jqXHR._ajax_action)==-1) {
		acritExpUpdateLog();
	}
	*/
}

function acritExpScrollToTop(){
	$('html,body').animate({
		scrollTop:$('#acrit_exp_form').offset().top - 4
	});
}

function acritExpSelectNextOption(e){
	if(e.button == 1){
		e.preventDefault();
		e.stopPropagation();
		var options = $(this).find('option'),
			newOption = options.filter(':selected').next();
		if(!newOption.length){
			newOption = options.first();
		}
		$(this).val(newOption.val()).trigger('change');
	}
}

function acritExpRefreshServerTime(time){
	$('[data-role="acrit-core-server-time"]').attr('data-date', time).text(time);
	acritCoreCronShowServerTime();
}

function acritExpGetFileUrl(){
	let
		useHttps = $('input[data-role="acrit_exp_use_https"]').prop('checked'),
		domain = $('input[data-role="acrit_exp_domain"]').val(),
		filename = $('input[data-role="export-file-name"]').val(),
		url = (useHttps ? 'https://' : 'http://') + domain + filename;
	if(domain == undefined || !domain.length){
		alert(BX.message('ACRIT_EXP_GET_FILE_URL_NO_DOMAIN'));
	}
	else if(filename == undefined || !filename.length){
		alert(BX.message('ACRIT_EXP_GET_FILE_URL_NO_FILENAME'));
	}
	else{
		prompt(BX.message('ACRIT_EXP_GET_FILE_TITLE'), url);
	}
}

// Module version in nav chain
$(document).ready(function(){
	if(acritExpModuleVersion.length > 0 && acritExpCoreVersion.length > 0) {
		$('a[id^="bx_admin_chain_item_menu_acrit_"]>span').append(' ('+acritExpModuleVersion+' / '+acritExpCoreVersion+')');
	}
});

// Add message container to button bar
$(document).ready(function(){
	$('#acrit_exp_form .adm-detail-content-btns')
		.append('&nbsp; <span data-role="iblock-settings-save-progress"></span>')
		.append('&nbsp; <span data-role="iblock-settings-save-result"></span>');
});

// Main notice
$(document).delegate('a[data-role="main-notice-hide"]', 'click', function(e){
	e.preventDefault();
	// ajax
	acritExpAjax('main_notice_hide', '', function(JsonResult, textStatus, jqXHR){
		if(JsonResult.Success){
			$('div[data-role="main-notice"]').remove();
		}
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, true);
});

// Sites
$(document).delegate('#acrit_exp_form #field_SITE_ID', 'change', function(e){
	var domain = $(this).find('option:selected').data('domain'),
		siteId = $(this).val();
	if(domain!=undefined && domain.length>0) {
		$('.acrit_exp_domain_from_site').val(domain).show();
	}
	else {
		$('.acrit_exp_domain_from_site').val('').hide();
	}
	acritExpChangeProfileName();
	//
	acritExpAjax('cron_get_command', {site_id: siteId}, function(JsonResult, textStatus, jqXHR){
		if(JsonResult){
			$('#acrit-core-cron-command-copy').text(JsonResult.Command);
		}
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, true);
});
$(document).delegate('#acrit_exp_form .profile_domain_buttons input[type=button]', 'click', function(e){
	var domain = $(this).val();
	if(domain!=undefined) {
		$('#tr_DOMAIN input[type=text]').val(domain);
	}
});
$(document).ready(function(){
	$('#acrit_exp_form #field_SITE_ID').trigger('change');
});

// Name
$(document).delegate('input[data-role="profile-name"]', 'change', function(){
	$(this).attr('data-custom-name', 'true');
});
function acritExpChangeProfileName(){
	if(!window.acritExpCanChangeName){
		return;
	}
	var inputName = $('input[data-role="profile-name"]'),
		format = window.acritExpCurrentPlugin,
		site = $('select[data-role="profile-site"]').val();
	if(inputName.attr('data-custom-name') != 'true'){
		var name = format && format.NAME ? format.NAME : inputName.data('default-name');
		name = name.replace(/[\[\]']+/g,'');
		if(site && site.length){
			name = name + ' [' + site + ']';
		}
		// ajax
		acritExpAjax('get_profile_name_index', 'profile_name='+name, function(JsonResult, textStatus, jqXHR){
			if(JsonResult && JsonResult.NAME.length){
				inputName.val(JsonResult.NAME);
			}
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	}
}
$(document).ready(function(){
	setTimeout(function(){
		window.acritExpCanChangeName = true;
	}, 1000);
	$('input[data-role="profile-name"]').bind('textchange', function(){
		var textContent = $('#param__page_title').val();
		if(textContent.length){
			textContent = $('#param__page_title').val() + ' &laquo;' + $.trim($(this).val()) + '&raquo;';
			$('h1#adm-title').contents().first().replaceWith(textContent);
		}
	});
});

// Plugins
function acritExpShowPluginData(initial){
	var currentPluginCode = $('#acrit_exp_form #field_PLUGIN').val(),
		currentFormatCode = $('#acrit_exp_form #field_FORMAT').val(),
		currentPlugin = window.acritExpPlugins[currentPluginCode],
		current = null;
	if (currentPlugin){
		current = currentPlugin;
		if(currentFormatCode && currentFormatCode.length && typeof currentPlugin.FORMATS == 'object' && typeof currentPlugin.FORMATS.length) {
			for(var i in currentPlugin.FORMATS) {
				if(currentPlugin.FORMATS[i].CODE==currentFormatCode){
					current = currentPlugin.FORMATS[i];
				}
			}
		}
	}
	if(current) {
		if(!initial) {
			$('#param__plugin').val(currentPluginCode);
			$('#param__format').val(currentFormatCode);
			acritExpPluginLoadSettings(false);
		}
	}
	window.acritExpCurrentPlugin = current;
	acritExpChangeProfileName();
}
function acritExpPluginLoadSettings(saveValues) {
	var currentValues = '',
		trSettings = $('#acrit_exp_form #tr_PLUGIN_SETTINGS'),
		divSettings = $('#acrit_exp_form #div_PLUGIN_SETTINGS'),
		rowDescription = $('#acrit_exp_form #tr_PLUGIN_DESCRIPTION_HEADING, #acrit_exp_form #tr_PLUGIN_DESCRIPTION'),
		rowExample = $('#acrit_exp_form #tr_PLUGIN_EXAMPLE_HEADING, #acrit_exp_form #tr_PLUGIN_EXAMPLE');
	if(saveValues){
		currentValues = $('#acrit_exp_form #div_PLUGIN_SETTINGS :input').serialize();
	}
	divSettings.html('<table style="width:100%"><tr><td width="40%"></td>'
		+'<td width="60%">'+BX.message('ACRIT_EXP_POPUP_LOADING')+'</td></tr></table>');
	acritExpAjax('load_plugin_settings', currentValues, function(JsonResult, textStatus, jqXHR){
		divSettings.html(JsonResult.HTML).find('input[type=checkbox]').each(function(){
			BX.adminFormTools.modifyCheckbox(this);
		});
		trSettings.hide();
		if(JsonResult.HTML.length){
			trSettings.show();
		}
		if(JsonResult.DESCRIPTION.length){
			rowDescription.show().not('.heading').children('td').html('<div class="plugin-description">'+JsonResult.DESCRIPTION+'</div>');
		}
		if(JsonResult.EXAMPLE.length){
			rowExample.show().not('.heading').children('td').html('<div class="plugin-example">'+JsonResult.EXAMPLE+'</div>');
		}
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, true);
}
function acritExpPluginTemplate(state) {
	if (!state.id) {
		return state.text;
	}
	var html = $('<span class="select2-menuicon"><img src="'+$(state.element).attr('data-icon')+'" /> <span>' + state.text + '</span></span>');
	return html;
};
$(document).delegate('#acrit_exp_form #field_PLUGIN', 'change', function(e, data){
	if(!(data && data.initial)){
		$('.acrit_exp_type_info').hide().not('.heading').children('td').html('');
	}
	if(window.acritExpPlugins == undefined){
		location.reload(); // this can be on immediate after auth in admin section
	}
	var selectFormat = $('#acrit_exp_form #field_FORMAT'),
		currentPlugin = window.acritExpPlugins[$(this).val()],
		rowFormat = $('#acrit_exp_form #tr_PLUGIN_FORMAT'),
		//
		trSettings = $('#acrit_exp_form #tr_PLUGIN_SETTINGS'),
		divSettings = $('#acrit_exp_form #div_PLUGIN_SETTINGS');
	rowFormat.hide();
	selectFormat.html('');
	if(currentPlugin) {
		if(currentPlugin && typeof currentPlugin.FORMATS == 'object') {
			// fill FORMATS
			var formatsCount = 0;
			for(var i in currentPlugin.FORMATS) {
				var format = currentPlugin.FORMATS[i];
				var selected = $('#param__format').val() == format.CODE ? ' selected="selected"' : '';
				selectFormat.append('<option value="'+format.CODE+'"'+selected+'>'+format.NAME+'</option>');
				formatsCount++;
			}
			// show formats SELECT
			if(formatsCount > 1) {
				rowFormat.show();
				window.acritExpFormatSelect2Config = {
					dropdownParent: $('#tr_PLUGIN_FORMAT > td > div').first(),
					dropdownPosition: 'below',
					language: 'ru'
				};
				window.acritExpFormatSelect2Object = $('#field_FORMAT').select2(window.acritExpFormatSelect2Config);
			}
		}
		acritExpShowPluginData(data && data.initial);
	}
	else {
		divSettings.html('');
		trSettings.hide();
	}
});
$(document).delegate('#acrit_exp_form #field_FORMAT', 'change', function(e){
	acritExpShowPluginData();
});
$(document).ready(function(){
	// Select2
	window.acritExpPluginSelect2Config = {
		templateResult: acritExpPluginTemplate,
		templateSelection: acritExpPluginTemplate,
		dropdownParent: $('#tr_PLUGIN > td > div').first(),
		dropdownPosition: 'below',
		language: 'ru'
	};
	window.acritExpPluginSelect2Object = $('#field_PLUGIN').select2(window.acritExpPluginSelect2Config);
	// Refresh select2 on tabs change
	BX.addCustomEvent('OnAdminTabsChange', function(){
		if(window.acritExpPluginSelect2Object){
			window.acritExpPluginSelect2Object.select2(window.acritExpPluginSelect2Config);
		}
		if(window.acritExpFormatSelect2Object){
			window.acritExpFormatSelect2Object.select2(window.acritExpFormatSelect2Config);
		}
		//acritExpIBlocksSelectStylize();
	});
	// Initial trigger
	$('#field_PLUGIN').trigger('change', {initial:true});
	// Disable all profile tabs besides first ['general']
	if($('#param__plugin').length && $('#param__plugin').val()=='') {
		var formName = $('#param__form_name').val();
		for(var i in window[formName].aTabs){
			if(window[formName].aTabs[i].DIV!='general'){
				window[formName].DisableTab(window[formName].aTabs[i].DIV);
			}
		}
	}
});
$(document).delegate('#input_PLUGIN_activate', 'click', function(e){
	if(confirm($(this).data('confirm'))){
		$('#field_PLUGIN').removeAttr('disabled');
		$('#field_FORMAT').removeAttr('disabled');
		$(this).remove();
	}
});

// Structure tab
$(document).delegate('#acrit_exp_form #field_IBLOCK', 'change', function(e, data){
	var fieldIBlock = $(this),
		iblock_id = fieldIBlock.val();
	fieldIBlock.attr('data-loaded', 'N');
	acritExpAjax('load_structure_iblock', 'iblock_id='+iblock_id, function(JsonResult, textStatus, jqXHR){
		var divIBlockContent = $('#acrit_exp_form #field_IBLOCK_content');
		divIBlockContent.html(JsonResult.HTML).find('input[type=checkbox]').each(function(){
			BX.adminFormTools.modifyCheckbox(this);
		});
		var initialTab = divIBlockContent.find('[data-role="iblock-structure-settings-tabs"]').data('initial-tab');
		if(initialTab && initialTab.length) {
			$('#view_tab_' + initialTab).trigger('click');
		}
		fieldIBlock.attr('data-loaded', 'Y');
		$('select[data-role="categories-list"]').trigger('change');
		$('select[data-role="categories-redefinition-mode"]').trigger('change');
		$('select[data-role="categories-redefinition-source"]').trigger('change');
		$('select[data-role="offers-sort--field"]').trigger('change');
		$(document).scrollTop($(document).scrollTop()+1);
		$(document).scrollTop($(document).scrollTop()-1);
		BX.onCustomEvent('onLoadStructureIBlock', [iblock_id]);
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, false);
});
// Select2
function acritExpIBlocksSelectStylize(){
	var select = $('#acrit_exp_form #field_IBLOCK');
	select.select2({
		dropdownAutoWidth: true,
		dropdownPosition: 'below',
		dropdownParent: select.parent(),
		language: 'ru'
	});
}
function acritExpIBlocksReload(data){
	var oldValue = $('#field_IBLOCK').val();
	if(typeof data == 'object' && typeof data.IBlocks == 'string'){
		$('#field_IBLOCK').html(data.IBlocks).val(oldValue);
		acritExpIBlocksSelectStylize();
	}
	else {
		if(typeof data != 'string'){
			data = '';
		}
		acritExpAjax('reload_iblocks', data, function(JsonResult, textStatus, jqXHR){
			$('#field_IBLOCK').html(JsonResult.HTML).val(oldValue);
			acritExpIBlocksSelectStylize();
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, false);
	}
}
$(document).delegate('input[type=checkbox][data-role="show-just-catalogs"]', 'change', function(e){
	acritExpIBlocksReload('show_just_catalogs='+($(this).is(':checked')?'Y':'N'));
});
$(document).ready(function(){
	acritExpIBlocksSelectStylize();
});

// Structure tab - Fields
$(document).delegate('#acrit_exp_form [data-role="iblock-structure"] [data-role="field-type"]', 'mousedown', acritExpSelectNextOption);
$(document).delegate('#acrit_exp_form [data-role="iblock-structure"] [data-role="field-type"]', 'change', function(e){
	e.preventDefault();
	var container = $(this).closest('tr').find('[data-role="field-value-cell"]').first(),
		//iblock_id = $('#acrit_exp_form #field_IBLOCK').val(),
		iblock_id = $(this).closest('div[data-role="iblock-structure"]').attr('data-iblock-id'),
		field = $(this).closest('tr').data('field'),
		type = $(this).val(),
		request = 'iblock_id='+iblock_id+'&iblock_main='+'&field='+field+'&type='+type;
	acritExpAjax('change_field_type', request, function(JsonResult, textStatus, jqXHR){
		container.html(JsonResult.HTML);
	}, function(jqXHR){
		container.html(jqXHR.responseText);
	}, false);
});
$(document).delegate('#acrit_exp_form [data-role="iblock-settings-save"]', 'click', function(e, data){
	var div = $('#field_IBLOCK_content'),
		divSaving = $('span[data-role="iblock-settings-save-progress"]'),
		divResult = $('span[data-role="iblock-settings-save-result"]'),
		formData = $(':input', div).serialize();
	//
	if(divSaving.attr('data-saving') == 'Y'){
		return;
	}
	divSaving.html('');
	divResult.html('');
	//
	divSaving.attr('data-saving', 'Y').html(BX.message('ACRIT_EXP_IBLOCK_SETTINGS_SAVE_PROGRESS'));
	//
	acritExpAjax('iblock_save_data', formData, function(JsonResult, textStatus, jqXHR){
		if(JsonResult.SaveSuccess) {
			divResult.html(BX.message('ACRIT_EXP_IBLOCK_SETTINGS_SAVE_SUCCESS'));
		}
		else {
			divResult.html(BX.message('ACRIT_EXP_IBLOCK_SETTINGS_SAVE_ERROR'));
		}
		if(window.acritExpIblockSettingsSaveTimeout){
			clearTimeout(window.acritExpIblockSettingsSaveTimeout);
		}
		window.acritExpIblockSettingsSaveTimeout = setTimeout(function(){
			divResult.html('');
		},3000);
		$('[data-role="iblock-settings-result"]').html('');
		acritExpIBlocksReload(JsonResult);
		/*
		if(!(typeof data == 'object' && data.hotkey)){
			setTimeout(function(){
				acritExpScrollToTop();
			},250);
		}
		*/
		divSaving.removeAttr('data-saving').html('');
		acritExpHandleIBlocksMultipleNotice(JsonResult.IBlocksMultipleNotice);
	}, function(jqXHR){
		$('[data-role="iblock-settings-result"]').html(jqXHR.responseText);
		divSaving.removeAttr('data-saving').html('');
	}, true);
});
$(document).delegate('#acrit_exp_form [data-role="iblock-settings-clear"]', 'click', function(e){
	e.preventDefault();
	var iblockId = $('#field_IBLOCK').val(),
		iblockName = $('#field_IBLOCK option:selected').attr('data-name');
	if(iblockId){
		acritExpClearIBlockData(iblockId, iblockName, true);
	}
});
function acritExpClearIBlockData(iblockId, iblockName, doScroll, callback){
	if(iblockId > 0) {
		var message = BX.message('ACRIT_EXP_IBLOCK_SETTINGS_CLEAR_CONFIRM');
		message = message.replace(/#ID#/, iblockId);
		message = message.replace(/#NAME#/, iblockName);
		if(confirm(message)) {
			acritExpAjax('iblock_clear_data', '&iblock_id='+iblockId, function(JsonResult, textStatus, jqXHR){
				$('#field_IBLOCK').trigger('change');
				$('[data-role="iblock-settings-result"]').html('');
				acritExpIBlocksReload(JsonResult);
				if(doScroll == true) {
					acritExpScrollToTop();
				}
				if (typeof callback == 'function'){
					callback(iblockId, iblockName, doScroll);
				}
				acritExpHandleIBlocksMultipleNotice(JsonResult.IBlocksMultipleNotice);
			}, function(jqXHR){
				$('[data-role="iblock-settings-result"]').html(jqXHR.responseText);
			}, false);
		}
	}
}
$(document).delegate('#acrit_exp_form [data-role="field--button-params"]', 'click', function(e){
	e.preventDefault();
	var openerLink = $(this),
		iblockId = $(this).closest('[data-role="iblock-structure"]').attr('data-iblock-id'),
		rowField = $(this).closest('tr[data-field]'),
		fieldName = rowField.attr('data-name'),
		fieldCode = rowField.attr('data-field'),
		fieldType = $('select[data-role="field-type"]', rowField).val(),
		inputParams = $('input[data-role="field--params"]', rowField);
	//
	AcritExpPopupFieldSettings.Open(openerLink, inputParams, iblockId, fieldCode, fieldType, fieldName);
});
function acritExpHandleIBlocksMultipleNotice(IBlocksMultipleNotice){
	var div = $('div[data-role="multiple-iblocks-note"]');
	if(div.length){
		if(typeof IBlocksMultipleNotice == 'string' && IBlocksMultipleNotice.length > 0){
			div.html(IBlocksMultipleNotice);
		}
		else{
			div.html('');
		}
	}
}

/**
 *	[field type = SIMPLE]
 */
$(document).delegate('#acrit_exp_form [data-role="field-simple--value-type"]', 'mousedown', acritExpSelectNextOption);
$(document).delegate('#acrit_exp_form [data-role="field-simple--value-type"]', 'change', function(e){
	$(this).closest('tr').attr('data-type', $(this).val());
});
$(document).delegate('#acrit_exp_form [data-role="field-simple--value-add"]', 'click', function(e){
	var table = $(this).closest('.acrit-exp-field-value').children('table'),
		thisRow = $(this).closest('tr'),
		newRow = $('tr', table).first().clone();
	$('select option', newRow).prop('selected', false);
	$('input[type=text],input[type=hidden],textarea', newRow).val('');
	$('textarea', newRow).css('height', '');
	//table.children('tbody').append(newRow);
	newRow.insertAfter(thisRow);
	$('select', newRow).trigger('change');
});
$(document).delegate('#acrit_exp_form [data-role="field-simple--value-clear"]', 'click', function(e){
	e.preventDefault();
	var item = $(this).closest('[data-role="field-simple--value-item"]');
	$('[data-role="field-simple--value-title"]', item).val('');
	$('[data-role="field-simple--value-value"]', item).val('');
});
$(document).delegate('#acrit_exp_form [data-role="field-simple--value-delete"]', 'click', function(e){
	e.preventDefault();
	$(this).closest('tr').remove();
});
$(document).delegate('#acrit_exp_form [data-role="field-simple--value-title"]', 'click', function(e){
	$(this).closest('table').find('[data-role="field-simple--button-select-field"]').trigger('click');
});
$(document).delegate('#acrit_exp_form [data-role="field-simple--button-select-field"]', 'click', function(e){
	e.preventDefault();
	var divIBlock = $(this).closest('[data-role="iblock-structure"]'),
		divSort = $(this).closest('[data-role="sort-field"]'),
		IBlockID = divSort.length ? divSort.attr('data-iblock-id') : divIBlock.attr('data-iblock-id'),
		closestField = $(this).closest('[data-type="FIELD"]'),
		inputValue = closestField.find('[data-role="field-simple--value-value"]'),
		inputTitle = closestField.find('[data-role="field-simple--value-title"]')
		currentValue = inputValue.val(),
		allowIBlockSelect = divSort.length ? 'N' : 'Y';
	AcritExpPopupSelectField.OnSelectField = function(thisPopup){
		var selectedOption = $('select[data-role="field-select-list"]:visible option:selected', thisPopup.DIV),
			selectedOptgroup = selectedOption.closest('optgroup');
		if(selectedOption.length && selectedOptgroup.length) {
			var fieldCode = selectedOption.val(),
				fieldName = selectedOption.text();
			inputValue.val(fieldCode);
			inputTitle.val(fieldName);
		}
	}
	AcritExpPopupSelectField.Open(IBlockID, currentValue, allowIBlockSelect);
});
$(document).delegate('#acrit_exp_form [data-role="field-simple--button-select-const"]', 'click', function(e){
	e.preventDefault();
	var divIBlock = $(this).closest('[data-role="iblock-structure"]'),
		IBlockID = divIBlock.attr('data-iblock-id'),
		inputArea = $(this).closest('table').find('textarea');
	AcritExpPopupSelectField.OnSelectField = function(thisPopup){
		var selectedOption = $('select[data-role="field-select-list"]:visible option:selected', thisPopup.DIV),
			selectedOptgroup = selectedOption.closest('optgroup');
		if(selectedOption.length && selectedOptgroup.length) {
			var group = selectedOptgroup.data('code'),
				field = selectedOption.val(),
				isParent = false,
				isOffer = false;
			if(field.match(/^PARENT\./)){
				field = field.replace(/^PARENT\./, '');
				isParent = true;
				group = 'PARENT.' + group;
			}
			else if(field.match(/^OFFER\./)){
				field = field.replace(/^OFFER\./, '');
				isOffer = true;
				group = 'OFFER.' + group;
			}
			var macro = '{='+group+'.'+field+'}';
			inputArea.insertAtCaret(macro);
		}
	}
	AcritExpPopupSelectField.Open(IBlockID);
});
$(document).delegate('#acrit_exp_form [data-role="field-simple--button-params"]', 'click', function(e){
	e.preventDefault();
	var openerLink = $(this),
		iblockStructure = $(this).closest('[data-role="iblock-structure"]'),
		iblockId = iblockStructure.attr('data-iblock-id'),
		rowField = $(this).closest('tr[data-field]');
		fieldName = rowField.attr('data-name'),
		fieldCode = rowField.attr('data-field'),
		fieldType = $('select[data-role="field-type"]', rowField).val(),
		rowValue = $(this).closest('[data-role="field-simple--value-item"]'),
		valueType = $('select[data-role="field-simple--value-type"]', rowValue).val(),
		inputParams = $('input[data-role="field-simple--value-params"]', rowValue);
	if(!rowField.length || !iblockStructure.length){
		rowSortField = $(this).closest('[data-role="sort-field"]');
		fieldName = rowSortField.attr('data-name');
		fieldCode = rowSortField.attr('data-field');
		fieldType = rowSortField.attr('data-type');
		iblockId = rowSortField.attr('data-iblock-id');
	}
	//
	AcritExpPopupValueSettings.Open(openerLink, inputParams, iblockId, fieldCode, fieldType, fieldName, valueType);
});

/**
 *	[field type = MULTICONDITIONAL]
 */
$(document).delegate('[data-role="field-multicondition-value-add"]', 'click', function(e){
	e.preventDefault();
	var values = $(this).closest('td[data-role="field-value-cell"]').find('div[data-role="field-multicondition-values"]'),
		code = $(this).closest('tr[data-role="field_row"]').attr('data-field'),
		iblock_id = $(this).closest('div[data-role="iblock-structure"]').attr('data-iblock-id'),
		data = 'iblock_id='+iblock_id+'&field_code='+code;
	acritExpAjax('add_multicondition_item', data, function(JsonResult){
		values.append(JsonResult.HTML);
	}, function(jqXHR){
		console.log(jqXHR.responseText);
		$('[data-role="console-results"]').html(jqXHR.responseText);
	}, true, false);
});
$(document).delegate('[data-role="field-multicondition-value-delete"]', 'click', function(e){
	$(this).closest('[data-role="field-multicondition-value"]').remove();
});


/**
 *	Profile IBlock settings subtabs
 */
function acritExpSettingsChangeTab(tab){
	if($('#acrit_exp_form #field_IBLOCK').attr('data-loaded')=='Y' && !window.acritExpInitialSettingsTabClick) {
		var selectedTab = $('#field_IBLOCK_content [data-role="iblock-structure-settings-tabs"] .adm-detail-subtab-active'),
			tab = $('#field_IBLOCK_content [data-role="iblock-structure-settings-tabs"] .adm-detail-subtab-active').attr('ID'),
			iblock_id = $('input[data-role="profile-iblock-id"]').val();
		tab = tab.replace(/^view_tab_/, '');
		if(tab == 'subtab_console') {
			return;
		}
		acritExpAjax('save_last_settings_tab', 'iblock_id='+iblock_id+'&tab='+tab, null, null, false, true);
	}
}

/**
 *	POPUP: select field for field values
 */
var AcritExpPopupSelectField;
$(document).ready(function(){
	AcritExpPopupSelectField = new BX.CDialog({
		ID: 'AcritExpPopupSelectField',
		title: BX.message('ACRIT_EXP_POPUP_SELECT_FIELD_TITLE'),
		content: '',
		resizable: true,
		draggable: true,
		height: 400,
		width: 800
	});
	AcritExpPopupSelectField.OnSelectField = null;
	AcritExpPopupSelectField.Open = function(IBlockID, currentValue, allowIBlockSelect){
		this.IBlockID = IBlockID;
		this.CurrentValue = currentValue;
		this.AllowIBlockSelect = allowIBlockSelect;
		//
		this.Show();
		this.LoadContent();
	}
	AcritExpPopupSelectField.LoadContent = function(){
		var thisPopup = this;
		//
		thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
		// Set popup buttons
		this.SetNavButtons();
		//
		var data = 'iblock_id='+thisPopup.IBlockID+'&current_value='+thisPopup.CurrentValue; // +'&allow_iblock_change='+thisPopup.AllowIBlockSelect
		//
		acritExpAjax('load_popup_select_field', data, function(JsonResult, textStatus, jqXHR){
			//thisPopup.SetContent(JsonResult.HTML);
			$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
			$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
					 '-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
			thisPopup.FilterFields();
			$('input[data-role="field-select-search"]', thisPopup.DIV).bind('textchange', function(){
				thisPopup.FilterFields();
			});
			$('[data-role="field-select-type"]', thisPopup.DIV).bind('change', function(){
				var table = $(this).closest('.acrit-exp-field-select-table'),
					listElement = $('select[data-role="field-select-list"][data-type="element"]', table),
					listOffer = $('select[data-role="field-select-list"][data-type="offer"]', table),
					listParent = $('select[data-role="field-select-list"][data-type="parent"]', table);
				listElement.hide();
				listOffer.hide();
				listParent.hide();
				switch($(this).val()){
					case 'element':
						listElement.show();
						break;
					case 'offer':
						listOffer.show();
						break;
					case 'parent':
						listParent.show();
						break;
				}
				thisPopup.FilterFields();
			}).trigger('change');
			$('select[data-role="field-select-list"]', thisPopup.DIV).dblclick(function(){
				$('.bx-core-adm-dialog-buttons input[type=button]', thisPopup.DIV).first().trigger('click');
			});
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, false);
	}
	AcritExpPopupSelectField.SetNavButtons = function(){
		$(this.PARTS.BUTTONS_CONTAINER).html('');
		this.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_POPUP_SAVE'),
				'className': 'adm-btn-green',
				'id': this.PARAMS.ID + '_btnSave',
				'action': function(){
					if(typeof AcritExpPopupSelectField.OnSelectField == 'function'){
						var thisPopup = this.parentWindow;
						thisPopup.OnSelectField(thisPopup);
						thisPopup.Close();
					}
					else {
						alert('Error! Handler «OnSelectField» is not set.');
					}
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_CANCEL'),
				'id': this.PARAMS.ID + '_btnCancel',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		)
	}
	AcritExpPopupSelectField.FilterFields = function(){
		var fieldText = $('input[data-role="field-select-search"]', this.DIV),
			fieldList = $('select[data-role="field-select-list"]', this.DIV),
			searchText = $.trim(fieldText.val()).toLowerCase();
		if(searchText=='') {
			$('optgroup', fieldList).show();
			$('optgroup option', fieldList).show();
			fieldList.children('option').hide();
		}
		else {
			var found = false;
			fieldList.children('option').show();
			$('optgroup', fieldList).hide();
			$('optgroup option', fieldList).hide().each(function(){
				var search = $(this).val().toLowerCase() + ' ' + $.trim($(this).text()).toLowerCase();
				var matched = search.indexOf(searchText) > -1;
				if(matched){
					$(this).show().closest('optgroup').show();
					found = true;
				}
			});
			if(found) {
				fieldList.children('option').hide();
			}
			else {
				fieldList.find('option:selected').removeAttr('selected');
			}
		}
	}
});


/**
 *	POPUP: Value settings
 */
var AcritExpPopupValueSettings;
$(document).ready(function(){
	AcritExpPopupValueSettings = new BX.CDialog({
		ID: 'AcritExpPopupValueSettings',
		title: '',
		content: '',
		resizable: true,
		draggable: true,
		height: 400,
		width: 800
	});
	AcritExpPopupValueSettings.Open = function(openerLink, inputParams, iblockId, fieldCode, fieldType, fieldName, valueType){
		this.openerLink = openerLink;
		this.inputParams = inputParams;
		this.iblockId = iblockId;
		this.fieldCode = fieldCode;
		this.fieldType = fieldType;
		this.fieldName = fieldName;
		this.valueType = valueType;
		//
		this.Show();
		var popupTitle = BX.message('ACRIT_EXP_POPUP_VALUE_SETTINGS_TITLE') + ' "' + fieldName + '"';
		if(fieldCode.length && fieldCode.substr(0,1) != '_'){
			popupTitle += ' [' + fieldCode + ']';
		}
		this.SetTitle(popupTitle);
		this.LoadContent();
	}
	AcritExpPopupValueSettings.LoadContent = function(){
		var thisPopup = this;
		//
		thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
		// Set popup buttons
		thisPopup.SetNavButtons();
		//
		var code = thisPopup.fieldCode,
			type = thisPopup.fieldType,
			name = encodeURIComponent(thisPopup.fieldName),
			iblockId = thisPopup.iblockId,
			currentParams = thisPopup.inputParams.val().length ? '&'+thisPopup.inputParams.val() : '',
			currentValue = this.openerLink.closest('tr[data-role="field-simple--value-item"]').find('[data-role="field-simple--value-value"]').val(),
			postData = '&iblock_id='+iblockId+'&field_code='+code+'&field_type='+type+'&field_name='+name
				+'&value_type='+thisPopup.valueType+'&current_value='+currentValue+currentParams;
		//
		acritExpAjax('load_popup_value_settings', postData, function(JsonResult, textStatus, jqXHR){
			//thisPopup.SetContent(JsonResult.HTML);
			$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
			$('input[type=checkbox]', thisPopup.PARTS.CONTENT).not('.no-checkbox-styling').each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	}
	AcritExpPopupValueSettings.SetNavButtons = function(){
		$(this.PARTS.BUTTONS_CONTAINER).html('');
		this.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_POPUP_SAVE'),
				'className': 'adm-btn-green',
				'id': this.PARAMS.ID + '_btnSave',
				'action': function(){
					var thisPopup = this.parentWindow,
						noSerializeInputs = $('[data-noserialize="Y"] :input', thisPopup.PARTS.CONTENT_DATA),
						formData = $(':input', thisPopup.PARTS.CONTENT_DATA).not(noSerializeInputs).serialize();
					thisPopup.inputParams.val(formData);
					thisPopup.Close();
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_CANCEL'),
				'id': this.PARAMS.ID + '_btnCancel',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		)
	}
});

/**
 *	[Profile] Field/Value settings
 */
$(document).delegate('table[data-role="value-replaces"] [data-role="replace-add"]', 'click', function(e){
	e.preventDefault();
	var table = $(this).closest('table[data-role="value-replaces"]'),
		body = table.children('tbody'),
		nothingRow = body.children('tr').last(),
		pattern = body.children('tr').first(),
		newRow = pattern.clone();
	newRow.removeAttr('data-noserialize').removeAttr('class').insertBefore(nothingRow);
	newRow.find('input[type=checkbox]').each(function(){
		BX.adminFormTools.modifyCheckbox(this);
		$(this).trigger('change');
	});
});
$(document).delegate('table[data-role="value-replaces"] [data-role="replace-delete"]', 'click', function(e){
	e.preventDefault();
	$(this).closest('tr').remove();
});
$(document).delegate('table[data-role="value-replaces"] [data-role="replace-use-regexp"]', 'change', function(e){
	$(this).parent().find('input[type=hidden]').val($(this).prop('checked')?'Y':'N');
});
$(document).delegate('table[data-role="value-replaces"] [data-role="replace-case-sensitive"]', 'change', function(e){
	$(this).parent().find('input[type=hidden]').val($(this).prop('checked')?'Y':'N');
});
$(document).delegate('table[data-role="value-replaces"] [data-role="replace-use-regexp"]', 'change', function(e){
	var inputModifier = $(this).closest('tr').find('[data-role="replace-regexp-modifier"]');
	if($(this).is(':checked')){
		inputModifier.show();
	}
	else{
		inputModifier.hide();
	}
});


/**
 *	POPUP: Field settings
 */
var AcritExpPopupFieldSettings;
$(document).ready(function(){
	AcritExpPopupFieldSettings = new BX.CDialog({
		ID: 'AcritExpPopupFieldSettings',
		title: '',
		content: '',
		resizable: true,
		draggable: true,
		height: 400,
		width: 800
	});
	AcritExpPopupFieldSettings.Open = function(openerLink, inputParams, iblockId, fieldCode, fieldType, fieldName){
		this.openerLink = openerLink;
		this.inputParams = inputParams;
		this.iblockId = iblockId;
		this.fieldCode = fieldCode;
		this.fieldType = fieldType;
		this.fieldName = fieldName;
		//
		this.Show();
		this.SetTitle(BX.message('ACRIT_EXP_POPUP_FIELD_SETTINGS_TITLE') + ' "' + fieldName + '" [' + fieldCode + ']');
		this.LoadContent();
	}
	AcritExpPopupFieldSettings.LoadContent = function(){
		var thisPopup = this;
		//
		thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
		// Set popup buttons
		thisPopup.SetNavButtons();
		//
		var code = thisPopup.fieldCode,
			type = thisPopup.fieldType,
			name = encodeURIComponent(thisPopup.fieldName);
			iblockId = thisPopup.iblockId,
			currentParams = thisPopup.inputParams.val().length ? '&'+thisPopup.inputParams.val() : '';
			postData = '&iblock_id='+iblockId+'&field_code='+code+'&field_type='+type+'&field_name='+name
				+currentParams;
		//
		acritExpAjax('load_popup_field_settings', postData, function(JsonResult, textStatus, jqXHR){
			$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
			$('input[type=checkbox]', thisPopup.PARTS.CONTENT).not('.no-checkbox-styling').each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	}
	AcritExpPopupFieldSettings.SetNavButtons = function(){
		$(this.PARTS.BUTTONS_CONTAINER).html('');
		this.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_POPUP_SAVE'),
				'className': 'adm-btn-green',
				'id': this.PARAMS.ID + '_btnSave',
				'action': function(){
					var thisPopup = this.parentWindow,
						noSerializeInputs = $('[data-noserialize="Y"] :input', thisPopup.PARTS.CONTENT_DATA),
						formData = $(':input', thisPopup.PARTS.CONTENT_DATA).not(noSerializeInputs).serialize();
					thisPopup.inputParams.val(formData);
					thisPopup.Close();
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_CANCEL'),
				'id': this.PARAMS.ID + '_btnCancel',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		)
	}
});

/**
 *	POPUP: Additional fields
 */
var AcritExpPopupAdditionalFields;
$(document).ready(function(){
	AcritExpPopupAdditionalFields = new BX.CDialog({
		ID: 'AcritExpPopupAdditionalFields',
		title: '',
		content: '',
		resizable: true,
		draggable: true,
		height: 300,
		width: 600
	});
	AcritExpPopupAdditionalFields.Open = function(openerLink, iblockId){
		this.openerLink = openerLink;
		this.iblockId = iblockId;
		//
		this.Show();
		this.SetTitle(BX.message('ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_TITLE'));
		this.LoadContent();
	}
	AcritExpPopupAdditionalFields.LoadContent = function(){
		var thisPopup = this;
		//
		thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
		// Set popup buttons
		thisPopup.SetNavButtons();
		//
		var iblockId = thisPopup.iblockId,
			postData = 'iblock_id='+iblockId;
		//
		acritExpAjax('show_props_for_additional_fields', postData, function(JsonResult, textStatus, jqXHR){
			$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
			$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
					 '-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
			$('input[type=checkbox]', thisPopup.PARTS.CONTENT).not('.no-checkbox-styling').each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
			$('select[data-role="select-additional-fields"]', thisPopup.DIV).dblclick(function(){
				$('.bx-core-adm-dialog-buttons input[type=button]', thisPopup.DIV).first().trigger('click');
			});
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	}
	AcritExpPopupAdditionalFields.SetNavButtons = function(){
		$(this.PARTS.BUTTONS_CONTAINER).html('');
		this.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_POPUP_SAVE'),
				'className': 'adm-btn-green',
				'id': this.PARAMS.ID + '_btnSave',
				'action': function(){
					var thisPopup = this.parentWindow,
						select = $('select[data-role="select-additional-fields"]', thisPopup.PARTS.CONTENT_DATA),
						propsId = $('option:selected', select).get().map(function(a){return $(a).val();}).join(',');
					acritExpAjax('add_additional_fields', 'iblock_id='+thisPopup.iblockId+'&props='+propsId, function(JsonResult, textStatus, jqXHR){
						if(JsonResult.Success){
							$(thisPopup.openerLink).closest('table').children('tbody').append(JsonResult.HTML);
							thisPopup.Close();
						}
						acritExpHandleAjaxError(jqXHR, false);
					}, function(jqXHR){
						acritExpHandleAjaxError(jqXHR, true);
					}, false);
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_CANCEL'),
				'id': this.PARAMS.ID + '_btnCancel',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		)
	}
});

// Add additional field [SINGLE]
$(document).delegate('[data-role="additional-field-add"]', 'click', function(e){
	e.preventDefault();
	var btnAdd = $(this),
		iblockId = btnAdd.closest('[data-role="iblock-structure"]').attr('data-iblock-id');
	acritExpAjax('add_additional_field', 'iblock_id='+iblockId, function(JsonResult, textStatus, jqXHR){
		if(JsonResult.Success){
			btnAdd.closest('table').children('tbody').append(JsonResult.HTML);
		}
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, false);
});
// Delete additional field [SINGLE]
$(document).delegate('[data-role="additional-field-delete"]', 'click', function(e){
	e.preventDefault();
	if(confirm(BX.message('ACRIT_EXP_ADDITIONAL_FIELD_DELETE_CONFIRM'))) {
		var fieldId = $(this).closest('[data-role="field_row"]').attr('data-field-id'),
			data = 'field_id='+fieldId;
		acritExpAjax('delete_additional_field', data, function(JsonResult, textStatus, jqXHR){
			if(JsonResult.Success){
				$('[data-role="field_row"][data-field="'+JsonResult.Field+'"]').remove();
			}
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, false);
	}
});
// Add additional fields [MULTIPLE]
$(document).delegate('[data-role="additional-field-add-multiple"]', 'click', function(e){
	e.preventDefault();
	var iblockId = $(this).closest('[data-role="iblock-structure"]').attr('data-iblock-id');
	AcritExpPopupAdditionalFields.Open(this, iblockId);
});
// Delete all additional fields [MULTIPLE]
$(document).delegate('[data-role="additional-field-delete-all"]', 'click', function(e){
	e.preventDefault();
	if(confirm(BX.message('ACRIT_EXP_ADDITIONAL_FIELDS_DELETE_ALL_CONFIRM'))) {
		var iblockStructure = $(this).closest('[data-role="iblock-structure"]'),
			iblockId = iblockStructure.attr('data-iblock-id'),
			data = 'iblock_id='+iblockId;
		acritExpAjax('delete_additional_fields_all', data, function(JsonResult, textStatus, jqXHR){
			if(JsonResult.Success){
				$('tr[data-field-id][data-field-id!="0"]', iblockStructure).remove();
			}
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, false);
	}
});

/* Price correct */
$(document).delegate('[data-role="price-correct-add"]', 'click', function(e){
	e.preventDefault();
	var newRow = $(this).closest('tr').clone(),
		tbody = $(this).closest('tbody');
	$('input[type=text]', newRow).val('');
	tbody.append(newRow);
});
$(document).delegate('[data-role="price-correct-delete"]', 'click', function(e){
	e.preventDefault();
	var currentRow = $(this).closest('tr');
	if(currentRow.siblings().length>0) {
		currentRow.remove();
	}
	else {
		$('input[type=text]', currentRow).val('');
	}
});

/**
 *	Work with categories
 */
// Select source categories
$(document).delegate('select[data-role="sections-mode"]', 'change', function(e){
	var row = $('#row_CATEGORIES_LIST');
	if($(this).val()=='all'){
		row.hide();
	}
	else {
		row.show();
	}
});
$(document).delegate('select[data-role="categories-list"]', 'change', function(e){
	var data = $('option:selected', this).get().map(function(option){return option.value}).join(',');
	$('input[data-role="categories-id"]').val(data);
});
$(document).delegate('input[data-role="categories-unselect"]', 'click', function(){
	if(confirm($(this).attr('data-confirm'))) {
		$('select[data-role="categories-list"]').val('').trigger('change');
	}
});
$(document).ready(function(){
	$('select[data-role="categories-list"]').trigger('change'); // this also need in load_structure_iblock
});
$(document).ready(function(){
	$('select[data-role="sections-mode"]').trigger('change');
});
// Redefinition
$(document).delegate('input[data-role="categories-redefinition-button"]', 'click', function(e){
	var iblockSettings = $('[data-role="profile-iblock-settings"]'),
		iblockId = $('input[data-role="profile-iblock-id"]').val(),
		categoriesId = $('input[data-role="categories-id"]', iblockSettings).val(),
		categoriesMode = $('select[data-role="categories-redefinition-mode"]', iblockSettings).val(),
		categoriesSource = $('select[data-role="sections-mode"]', iblockSettings).val();
	AcritExpPopupCategoriesRedefinition.Open(this, iblockId, categoriesId, categoriesMode, categoriesSource);
});
$(document).delegate('select[data-role="categories-redefinition-mode"]', 'change', function(){
	var
		trParents = $('#tr_CATEGORIES_EXPORT_PARENTS'),
		trUpdate = $('#tr_CATEGORIES_UPDATE'),
		selectSource = $('select[data-role="categories-redefinition-source"]');
	if($(this).val()==$(this).data('strict-value')){
		trParents.hide();
		if(selectSource.val() != selectSource.attr('data-uf-value')){
			if(selectSource.val() != selectSource.attr('data-custom-value')){
				trUpdate.show();
			}
		}
	}
	else {
		trParents.show();
		trUpdate.hide();
	}
	// If we'are using property as categories, we can't export parent categories, so hide it!
	if(selectSource.val() == selectSource.attr('data-custom-value')){
		trParents.hide();
	}
});
$(document).delegate('select[data-role="categories-redefinition-source"]', 'change', function(){
	var
		trRedefinitions = $('#tr_CATEGORIES_REDEFINITION_BUTTON'),
		trUserFields = $('#tr_CATEGORIES_REDEFINITION_SOURCE_UF'),
		trCustom = $('#tr_CATEGORIES_REDEFINITION_SOURCE_CUSTOM'),
		trButton = $('#tr_CATEGORIES_REDEFINITION_BUTTON').hide(),
		trUpdate = $('#tr_CATEGORIES_UPDATE').hide(),
		selectMode = $('select[data-role="categories-redefinition-mode"]').trigger('change');
		trParents = $('#tr_CATEGORIES_EXPORT_PARENTS');
	if($(this).val() == $(this).data('uf-value')){
		trRedefinitions.hide();
		trUserFields.show();
		trCustom.hide();
	}
	else if($(this).val() == $(this).data('custom-value')){
		trRedefinitions.hide();
		trUserFields.hide();
		trCustom.show();
	}
	else {
		trRedefinitions.show();
		trUserFields.hide();
		trCustom.hide();
		//
		trButton.show();
		if(selectMode.val() != selectMode.attr('data-strict-value')){
			trUpdate.show();
		}
	}
	var rowsCategoryNameCustom = $('tr.adm-list-table-row[data-category-custom-name="Y"]');
	if($(this).val() == $(this).data('custom-value')){
		rowsCategoryNameCustom.show();
	}
	else{
		rowsCategoryNameCustom.hide();
	}
});
$(document).delegate('[data-role="categories-update"]', 'click', function(){
	var thisButton = $(this),
		data = $('#subtab_categories :input').serialize(),
		cellMessage = $('td[data-role="categories-update-error"]').html('');
	thisButton.attr('disabled', 'disabled');
	acritExpAjax('categories_update', data, function(JsonResult, textStatus, jqXHR){
		thisButton.removeAttr('disabled');
		var spanDate = $('[data-role="categories-update-date"] span');
		if(JsonResult.Success && JsonResult.Date){
			spanDate.text(JsonResult.Date);
			setTimeout(function(){
				AcritExpPopupCategoriesRedefinitionSelect.LoadContent();
			}, 100);
			cellMessage.html(BX.message('ACRIT_EXP_UPDATE_CATEGORIES_SUCCESS'));
			setTimeout(function(){
				cellMessage.html('');
			}, 4000);
		}
		else {
			spanDate.text(spanDate.data('empty-value'));
			if(typeof JsonResult.Message == 'string' && JsonResult.Message.length){
				message = JsonResult.Message;
			}
			else{
				message = BX.message('ACRIT_EXP_UPDATE_CATEGORIES_ERROR') + "\n" + jqXHR.responseText;
			}
			cellMessage.html(message);
		}
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		thisButton.removeAttr('disabled');
		acritExpHandleAjaxError(jqXHR, true);
	}, true);
});
$(document).ready(function(){
	$('select[data-role="categories-redefinition-mode"]').trigger('change');
	$('select[data-role="categories-redefinition-source"]').trigger('change');
});

/**
 *	POPUP: Categories redefinition general
 */
var AcritExpPopupCategoriesRedefinition;
$(document).ready(function(){
	AcritExpPopupCategoriesRedefinition = new BX.CDialog({
		ID: 'AcritExpPopupCategoriesRedefinition',
		title: BX.message('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_TITLE'),
		content: '',
		resizable: true,
		draggable: true,
		height: 450,
		width: 990
	});
	AcritExpPopupCategoriesRedefinition.Open = function(openerLink, iblockId, categoriesId, categoriesMode, categoriesSource){
		this.openerLink = openerLink;
		this.iblockId = iblockId;
		this.categoriesId = categoriesId;
		this.categoriesMode = categoriesMode;
		this.categoriesSource = categoriesSource;
		//
		this.Show();
		this.LoadContent();
	}
	AcritExpPopupCategoriesRedefinition.LoadContent = function(){
		var thisPopup = this;
		//
		thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
		// Set popup buttons
		thisPopup.SetNavButtons();
		//
		var iblockId = thisPopup.iblockId,
			categoriesId = thisPopup.categoriesId,
			categoriesMode = thisPopup.categoriesMode,
			categoriesSource = thisPopup.categoriesSource,
			postData = 'iblock_id='+iblockId+'&categories_id='+categoriesId+'&categories_mode='+categoriesMode
				+'&categories_source='+categoriesSource;
		//
		acritExpAjax('categories_redefinition_show', postData, function(JsonResult, textStatus, jqXHR){
			$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
			$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
					 '-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
			$('input[type=checkbox]', thisPopup.PARTS.CONTENT).not('.no-checkbox-styling').each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	}
	AcritExpPopupCategoriesRedefinition.SetNavButtons = function(){
		$(this.PARTS.BUTTONS_CONTAINER).html('');
		this.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_POPUP_SAVE'),
				'className': 'adm-btn-green',
				'id': this.PARAMS.ID + '_btnSave',
				'action': function(){
					var thisPopup = this.parentWindow,
						postData = 'iblock_id='+thisPopup.iblockId+'&'+$(':input', thisPopup.PARTS.CONTENT_DATA).serialize();
					acritExpAjax('categories_redefinition_save', postData, function(JsonResult, textStatus, jqXHR){
						if(JsonResult.Success){
							thisPopup.Close();
						}
						acritExpHandleAjaxError(jqXHR, false);
					}, function(jqXHR){
						acritExpHandleAjaxError(jqXHR, true);
					}, true);
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_ALL'),
				'id': this.PARAMS.ID + '_btnClear',
				'action': function(){
					if(confirm(BX.message('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_CONFIRM'))){
						var thisPopup = this.parentWindow,
							postData = 'iblock_id='+thisPopup.iblockId+'&clear_all=Y';
						acritExpAjax('categories_redefinition_save', postData, function(JsonResult, textStatus, jqXHR){
							if(JsonResult.Success){
								thisPopup.Close();
							}
							acritExpHandleAjaxError(jqXHR, false);
						}, function(jqXHR){
							acritExpHandleAjaxError(jqXHR, true);
						}, true);
					}
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_CANCEL'),
				'id': this.PARAMS.ID + '_btnCancel',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		)
	}
});

/**
 *	POPUP: Categories redefinition select
 */
var AcritExpPopupCategoriesRedefinitionSelect;
$(document).ready(function(){
	AcritExpPopupCategoriesRedefinitionSelect = new BX.CDialog({
		ID: 'AcritExpPopupCategoriesRedefinitionSelect',
		title: BX.message('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_SELECT_TITLE'),
		content: '',
		resizable: true,
		draggable: true,
		height: 300,
		width: 1000
	});
	AcritExpPopupCategoriesRedefinitionSelect.Open = function(openerLink, iblockId, sectionId, currentValue){
		this.openerLink = openerLink;
		this.iblockIdLast = this.iblockId;
		this.iblockId = iblockId;
		this.sectionId = sectionId;
		this.currentValue = currentValue;
		//
		this.Show();
		if(this.iblockIdLast!=this.iblockId) {
			this.LoadContent();
		}
		else {
			var input = $('input[data-role="category-redefinition-search"]', this.DIV).val('');
			this.FilterFields();
			input.focus();
		}
	}
	AcritExpPopupCategoriesRedefinitionSelect.LoadContent = function(){
		var thisPopup = this;
		//
		thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
		// Set popup buttons
		thisPopup.SetNavButtons();
		//
		var iblockId = thisPopup.iblockId,
			sectionId = thisPopup.sectionId,
			currentValue = thisPopup.currentValue,
			postData = 'iblock_id='+iblockId+'&section_id='+sectionId+'&current_value='+encodeURIComponent(currentValue);
		//
		acritExpAjax('categories_redefinition_select', postData, function(JsonResult, textStatus, jqXHR){
			$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
			$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
					 '-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
			$('input[type=checkbox]', thisPopup.PARTS.CONTENT).not('.no-checkbox-styling').each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
			thisPopup.FilterFields();
			$('input[data-role="category-redefinition-search"]', thisPopup.DIV).bind('textchange', function(){
				clearTimeout(window.acritExpCategoryRedefinitionSearchTimeout);
				window.acritExpCategoryRedefinitionSearchTimeout = setTimeout(function(){
					thisPopup.FilterFields();
				}, 250);
			}).focus();
			$('select[data-role="category-redefinition-select"]', thisPopup.DIV).dblclick(function(){
				$('.bx-core-adm-dialog-buttons input[type=button]', thisPopup.DIV).first().trigger('click');
			});
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	}
	AcritExpPopupCategoriesRedefinitionSelect.SetNavButtons = function(){
		$(this.PARTS.BUTTONS_CONTAINER).html('');
		this.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_POPUP_SAVE'),
				'className': 'adm-btn-green',
				'id': this.PARAMS.ID + '_btnSave',
				'action': function(){
					var thisPopup = this.parentWindow,
						value = $('select[data-role="category-redefinition-select"]', thisPopup.DIV).val(),
						thisRow = $(thisPopup.openerLink).closest('tr'),
						nextRow = thisRow.next('tr'),
						thisDepth = thisRow.attr('data-depth'),
						nextDepth = null,
						input = null,
						skipDepth = null;
					$(thisPopup.openerLink).trigger('acrit:categoryselect', {category: value});
					thisRow.find('input[type=text]').val(value).attr('title', value);
					while(true){
						// Stop at end
						if(!nextRow.length){
							break
						}
						// Check depth
						nextDepth = nextRow.attr('data-depth');
						if(nextDepth <= thisDepth){
							break;
						}
						if(nextDepth <= skipDepth){
							skipDepth = null;
						}
						// Set value
						if(skipDepth == null || nextDepth <= skipDepth){
							input = nextRow.find('input[type=text]');
							if(input.length){
								if(input.val().length){
									skipDepth = nextDepth;
								}
								else {
									input.val(value).attr('title', value);
								}
							}
						}
						// Get next row
						nextRow = nextRow.next('tr');
					}
					thisPopup.Close();
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_CANCEL'),
				'id': this.PARAMS.ID + '_btnCancel',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		)
	}
	AcritExpPopupCategoriesRedefinitionSelect.FilterFields = function(){
		var fieldText = $('input[data-role="category-redefinition-search"]', this.DIV),
			fieldList = $('select[data-role="category-redefinition-select"]', this.DIV),
			searchText = $.trim(fieldText.val()).toLowerCase(),
			searchTexts = searchText.replace(/\t\n\r\v/g, ' ').replace(/\s{2,}/g, ' ').split(' ');
		if(searchText=='') {
			$('option', fieldList).show().first().hide();
		}
		else {
			var found = false;
			$('option', fieldList).hide().first().show();
			$('option', fieldList).each(function(){
				var search = $(this).val().toLowerCase() + ' ' + $.trim($(this).text()).toLowerCase();
				var matched = true;
				for(var i in searchTexts){
					if(search.indexOf(searchTexts[i]) == -1){
						matched = false;
						break;
					}
				}
				if(matched){
					$(this).show();
					found = true;
				}
			});
			if(found) {
				$('option', fieldList).first().hide();
			}
			else {
				$('option:selected', fieldList).removeAttr('selected');
			}
		}
	}
});
$(document).delegate('input[data-role="categories-redefinition-text"]', 'click', function(e){
	$(this).closest('tr').find('input[type="button"]').trigger('click');
});
$(document).delegate('a[data-role="categories-redefinition-button-clear"]', 'click', function(e){
	e.preventDefault();
	var item = $(this).closest('tr');
	$('[data-role="categories-redefinition-text"]', item).val('');
});
$(document).delegate('input[data-role="categories-redefinition-button-select"]', 'click', function(e){
	var iblockId = $(this).attr('data-iblock-id'),
		sectionId = $(this).attr('data-section-id'),
		currentValue = $(this).closest('tr').find('input[type=text]').val();
	AcritExpPopupCategoriesRedefinitionSelect.Open(this, iblockId, sectionId, currentValue);
});

/**
 *	POPUP: Export execute
 */
var AcritExpPopupExecute;
$(document).ready(function(){
	AcritExpPopupExecute = new BX.CDialog({
		ID: 'AcritExpPopupExecute',
		title: BX.message('ACRIT_EXP_POPUP_EXECUTE_TITLE'),
		content: '',
		resizable: true,
		draggable: true,
		height: 400,
		width: 500
	});
	AcritExpPopupExecute.Open = function(){
		this.repeatDelay = acritExpExportTimeDelay && acritExpExportTimeDelay>0 ? acritExpExportTimeDelay : 50;
		this.Stopped = false;
		//
		this.Show();
		this.LoadContent();
	}
	AcritExpPopupExecute.LoadContent = function(){
		var thisPopup = this;
		//
		thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
		// Set popup buttons
		thisPopup.SetNavButtons();
		//
		thisPopup.EnableControls();
		//
		acritExpAjax('load_popup_execute', '', function(JsonResult, textStatus, jqXHR){
			$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
			$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
					 '-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
			$('input[type=checkbox]', thisPopup.PARTS.CONTENT).not('.no-checkbox-styling').each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
			var isError = (typeof JsonResult != 'object') || !JsonResult.Success;
			if(isError) {
				thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_EXECUTE_ERROR'));
			}
			acritExpHandleAjaxError(jqXHR, isError);
		}, function(jqXHR){
			thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_EXECUTE_ERROR'));
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	}
	AcritExpPopupExecute.SetNavButtons = function(){
		$(this.PARTS.BUTTONS_CONTAINER).html('');
		this.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_POPUP_EXECUTE_BUTTON_START'),
				'className': 'adm-btn-green',
				'id': 'acrit-exp-popup-execute-button-start',
				'action': function(){
					var thisPopup = this.parentWindow;
					thisPopup.Stopped = false;
					thisPopup.Execute({first:true});
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_EXECUTE_BUTTON_STOP'),
				'id': 'acrit-exp-popup-execute-button-stop',
				'action': function(){
					var thisPopup = this.parentWindow;
					thisPopup.Stop();
					thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_EXECUTE_STOPPED'));
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_CLOSE'),
				'id': 'acrit-exp-popup-execute-button-close',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		)
	}
	AcritExpPopupExecute.DisableControls = function(){
		$('#acrit-exp-popup-execute-button-start', this.DIV).attr('disabled', 'disabled');
		$('#acrit-exp-popup-execute-button-stop', this.DIV).removeAttr('disabled');
	}
	AcritExpPopupExecute.EnableControls = function(){
		$('#acrit-exp-popup-execute-button-start', this.DIV).removeAttr('disabled');
		$('#acrit-exp-popup-execute-button-stop', this.DIV).attr('disabled', 'disabled');
	}
	AcritExpPopupExecute.Execute = function(requestData){
		var thisPopup = this;
		if(!thisPopup.Stopped) {
			if(requestData == undefined){
				requestData = {};
			}
			thisPopup.DisableControls();
			thisPopup.AjaxRequest = acritExpAjax('export_execute', requestData, function(JsonResult, textStatus, jqXHR){
				$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
				$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
					'height': '100%',
					'-webkit-box-sizing': 'border-box',
						 '-moz-box-sizing': 'border-box',
									'box-sizing': 'border-box'
				}).children().css({
					'height': '100%'
				});
				setTimeout(function(){
					if(JsonResult.Repeat){
						requestData.first = false;
						if(!thisPopup.Stopped) {
							thisPopup.Execute(requestData);
						}
					}
					else if(JsonResult.Success) {
						thisPopup.EnableControls();
						acritExpUpdateLogAndHistory();
					}
					else {
						if(JsonResult.ShowError==true) {
							if(JsonResult.HTML) {
								thisPopup.SetContent(JsonResult.HTML);
							}
							else {
								thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_EXECUTE_ERROR'));
							}
						}
						thisPopup.EnableControls();
					}
				}, thisPopup.repeatDelay);
				if(JsonResult.LockedHtml != undefined){
					$('#acrit-exp-lock-notifier').html(JsonResult.LockedHtml);
				}
				acritExpHandleAjaxError(jqXHR, false);
			}, function(jqXHR){
				thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_EXECUTE_ERROR'));
				acritExpHandleAjaxError(jqXHR, true);
				thisPopup.EnableControls();
			}, true);
		}
	}
	AcritExpPopupExecute.Stop = function(){
		this.Stopped = true;
		this.EnableControls();
		if (this.AjaxRequest && this.AjaxRequest.readyState != 4) {
			this.AjaxRequest.abort();
		}
		// Unlock on stop
		acritExpAjax('profile_unlock', '', null, null, false, false);
	}
	BX.addCustomEvent('onWindowClose', function(popupWindow){
		if(popupWindow == AcritExpPopupExecute){
			AcritExpPopupExecute.Stop();
		}
	});
});
$(document).delegate('[data-role="profile-unlock"]', 'click', function(e){
	e.preventDefault();
	var canUnlock = true,
		confirmMessage = $(this).attr('data-confirm');
	if(typeof confirmMessage == 'string' && confirmMessage.length){
		if(!confirm(confirmMessage)){
			canUnlock = false;
		}
	}
	if(canUnlock){
		acritExpAjax('profile_unlock', '', function(JsonResult, textStatus, jqXHR){
			if(JsonResult.Success) {
				if($('#acrit-exp-lock-notifier').length){
					$('#acrit-exp-lock-notifier').html('');
				}
				if(AcritExpPopupExecute.isOpen){
					AcritExpPopupExecute.LoadContent();
				}
			}
			else {
				console.log(JsonResult);
			}
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	}
});

/**
 *	POPUP: iblocks preview
 */
var AcritExpPopupIBlocksPreview;;
$(document).ready(function(){
	AcritExpPopupIBlocksPreview = new BX.CDialog({
		ID: 'AcritExpPopupIBlocksPreview',
		title: BX.message('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_TITLE'),
		content: '',
		resizable: true,
		draggable: true,
		height: 350,
		width: 800
	});
	AcritExpPopupIBlocksPreview.Open = function(showJustCatalogs){
		this.showJustCatalogs = showJustCatalogs;
		this.Show();
		this.LoadContent();
	}
	AcritExpPopupIBlocksPreview.LoadContent = function(){
		var thisPopup = this;
		//
		thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
		// Set popup buttons
		thisPopup.SetNavButtons();
		//
		var postData = 'show_just_catalogs='+(thisPopup.showJustCatalogs?'Y':'N');
		//
		acritExpAjax('iblocks_preview', postData, function(JsonResult, textStatus, jqXHR){
			$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
			$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
					 '-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
			$('input[type=checkbox]', thisPopup.PARTS.CONTENT).not('.no-checkbox-styling').each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	}
	AcritExpPopupIBlocksPreview.SetNavButtons = function(){
		$(this.PARTS.BUTTONS_CONTAINER).html('');
		this.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_POPUP_REFRESH'),
				'className': 'adm-btn-green',
				'id': this.PARAMS.ID + '_btnRefresh',
				'action': function(){
					AcritExpPopupIBlocksPreview.LoadContent();
				}
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_CLOSE'),
				'id': this.PARAMS.ID + '_btnClose',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		)
	}
});
$(document).delegate('input[data-role="preview-iblocks"]', 'click', function(e){
	var showJustCatalogs = $('input[type=checkbox][data-role="show-just-catalogs"]').is(':checked');
	AcritExpPopupIBlocksPreview.Open(showJustCatalogs);
});

/* Currency */
$(document).delegate('#field_CURRENCY_TARGET_CURRENCY', 'change', function(e){
	var tr = $('#tr_CURRENCY_RATES_SOURCE');
	if($(this).val().length){
		tr.show();
	}
	else {
		tr.hide();
	}
});
$(document).ready(function(){
	$('#field_CURRENCY_TARGET_CURRENCY').trigger('change');
});

/* Cron */
function acritExpSetCronStatus(configured){
	var statusDiv = $('div[data-cron-status][data-profile-id]');
	statusDiv.attr('data-cron-status', configured ? 'Y' : 'N');
	if(configured){
		$('[data-role="cron-clear"]').removeAttr('disabled');
	}
	else {
		$('[data-role="cron-clear"]').attr('disabled', 'disabled');
	}
	statusDiv.addClass('acrit-exp-text-blink');
	clearTimeout(window.acritExpTimeoutCronStatus);
	window.acritExpTimeoutCronStatus = setTimeout(function(){
		statusDiv.removeClass('acrit-exp-text-blink');
	}, 1000);
}
function acritExpCronSetup(schedule){
	acritExpAjax('cron_setup', 'cron_action=add&schedule='+encodeURIComponent(schedule), function(JsonResult, textStatus, jqXHR){
		acritExpSetCronStatus(JsonResult.IsConfigured);
		$('[data-role="cron-current-tasks-wrapper"]').html(JsonResult.CronTasksHtml);
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, false);
}
function acritExpCronClear(){
	acritExpAjax('cron_setup', 'cron_action=delete', function(JsonResult, textStatus, jqXHR){
		acritExpSetCronStatus(JsonResult.IsConfigured);
		$('[data-role="cron-current-tasks-wrapper"]').html(JsonResult.CronTasksHtml);
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, false);
}
$(document).delegate('[data-role="cron-example"]', 'click', function(e){
	e.preventDefault();
	var schedule = $(this).data('schedule').split(' '),
		table = $(this).closest('table');
	$('input[name="minute"]').val(schedule[0]);
	$('input[name="hour"]').val(schedule[1]);
	$('input[name="day"]').val(schedule[2]);
	$('input[name="month"]').val(schedule[3]);
	$('input[name="weekday"]').val(schedule[4]);
});
$(document).delegate('[data-role="cron-setup"]', 'click', function(e){
	e.preventDefault();
	var schedule = $('.acrit-core-cron-form-schedule input[type=text]').get().map(function(input){
		var value = $.trim($(input).val());
		if(!value.length){
			value = '*';
		}
		return value;
	}).join(' ');
	acritExpCronSetup(schedule);
});
$(document).delegate('[data-role="cron-clear"]', 'click', function(e){
	e.preventDefault();
	acritExpCronClear();
});
$(document).delegate('[data-role="cron-current-tasks-toggle"]', 'click', function(e){
	e.preventDefault();
	var target = $(this).next('[data-role="cron-current-tasks"]');
	if(!target.is(':animated')){
		target.slideToggle(200);
	}
});
$(document).delegate('[data-role="run-manual"]', 'click', function(e){
	e.preventDefault();
	AcritExpPopupExecute.Open($('#param__profile_id').val());
});
$(document).delegate('[data-role="run-background"]', 'click', function(e){
	e.preventDefault();
	acritExpAjax('run_in_background', null, function(JsonResult, textStatus, jqXHR){
		if(JsonResult.Success && JsonResult.SuccessMessage != undefined){
			if(JsonResult.LockedHtml != undefined){
				$('#acrit-exp-lock-notifier').html(JsonResult.LockedHtml);
			}
			if(JsonResult.SuccessMessage && JsonResult.SuccessMessage.length){
				var span = $('span[data-role="run-background-status"]')
					.addClass('acrit-exp-text-blink').html(JsonResult.SuccessMessage);
				clearTimeout(window.acritExpTimeoutRunInBackground1);
				window.acritExpTimeoutRunInBackground1 = setTimeout(function(){
					span.removeClass('acrit-exp-text-blink');
				}, 1000);
				clearTimeout(window.acritExpTimeoutRunInBackground2);
				window.acritExpTimeoutRunInBackground2 = setTimeout(function(){
					span.fadeOut(200, function(){
						$(this).html('');
					});
				}, 5000);
			}
		}
		else if (!JsonResult.Success && JsonResult.ErrorMessage != undefined){
			alert(JsonResult.ErrorMessage);
		}
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, false);
});
$(document).delegate('input[data-role="cron-one-time"]', 'change', function(e){
	e.preventDefault();
	var checkbox = $(this),
		post = {
			one_time: checkbox.is(':checked') ? 'Y' : 'N'
		};
	clearTimeout(window.acritExpCronOneTimeTimeout);
	checkbox.attr('disabled', 'disabled');
	acritExpAjax('cron_set_one_time', post, function(JsonResult, textStatus, jqXHR){
		if(JsonResult.Success){
			var span = $('span[data-role="cron-one-time-result"]');
			span.html(JsonResult.SuccessMessage).fadeIn(0);
			window.acritExpCronOneTimeTimeout = setTimeout(function(){
				span.fadeOut(200);
			}, 2500);
		}
		checkbox.removeAttr('disabled');
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		checkbox.removeAttr('disabled');
		acritExpHandleAjaxError(jqXHR, true);
	}, true);
});

/* Log and history */
function acritExpHandleLogTextarea(log){
	var textarea = $('textarea[data-role="profile-log"]');
	if(log==true){
		log = textarea.val();
	}
	if(log && log.length){
		textarea.val(log).removeAttr('disabled').css('height','');
	}
	else {
		textarea.val('').attr('disabled', 'disabled').css('height', textarea.data('empty-height'));
	}
}
$(document).delegate('input[data-role="profile-log-clear"]', 'click', function(e){
	acritExpAjax('log_clear', '', function(JsonResult, textStatus, jqXHR){
		acritExpHandleLogTextarea(null);
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleLogTextarea(null);
		acritExpHandleAjaxError(jqXHR, true);
	}, false);
});
$(document).delegate('input[data-role="profile-history-refresh"]', 'click', function(e, params){
	acritExpAjax('history_refresh', params, function(JsonResult, textStatus, jqXHR){
		$('#tr_HISTORY > td').html(JsonResult.HTML);
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, false);
});
function acritExpUpdateLog(){
	$('a[data-role="log-refresh"]').trigger('click');
}
function acritExpUpdateHistory(page, size){
	$('input[data-role="profile-history-refresh"]').trigger('click', {
		page: page,
		size: size
	});
}
function acritExpUpdateLogAndHistory(){
	acritExpUpdateLog();
	acritExpUpdateHistory();
}
$(document).ready(function(){
	acritExpHandleLogTextarea(true);
	var span = $('<span data-role="profile-log-export-file-name-tab" class="acrit-exp-profile-log-filename"></span>')
		.appendTo($('#log .adm-detail-title')),
		fileName = $('[data-role="profile-log-export-file-name-hidden"]').html();
		fileName = fileName != undefined ? fileName : '';
	span.html((fileName.length ? fileName : ''));
});

/* Offers sort (for get first offer of element) */
$(document).delegate('input[data-role="offers-sort--add"]', 'click', function(e){
	var block = $('div[data-role="offers-sort--block"]'),	
		item = block.children().first();
	item.clone().appendTo(block).find('input,select').val('');
});
$(document).delegate('a[data-role="offers-sort--delete"]', 'click', function(e){
	e.preventDefault();
	$(this).closest('[data-role="offers-sort--item"]').remove();
});
$(document).delegate('select[data-role="offers-sort--field"]', 'change', function(e){
	var otherValue = '-',
		otherBlock = $(this).parent().find('[data-role="offers-sort--other"]');
	if($(this).val() == otherValue){
		otherBlock.show().focus();
	}
	else {
		otherBlock.hide();
	}
});
$(document).ready(function(){
	$('select[data-role="offers-sort--field"]').trigger('change');
});
	
/* Additional attributes (in popup 'field_settings') */
$(document).delegate('[data-role="additional-attributes--add"]', 'click', function(e){
	var tbody = $(this).closest('table').children('tbody').first(),
		patternRow = $('tr[data-role="additional-attributes--pattern"]', tbody),
		nothingRow = $('tr[data-role="additional-attributes--nothing"]', tbody),
		newRow = patternRow.clone().insertBefore(nothingRow);
	newRow.removeAttr('data-noserialize class style data-role').find('input').val();
});
$(document).delegate('[data-role="additional-attributes--delete"]', 'click', function(e){
	e.preventDefault();
	$(this).closest('tr').remove();
});

$(document).delegate('form[data-role="popup-form"]', 'submit', function(e){
	e.preventDefault();
	var popup = $(this).closest('.bx-core-adm-dialog');
	if(popup.length){
		$('.bx-core-adm-dialog-buttons input[type=button].adm-btn-green', popup).trigger('click');
	}
});

// Export file name input
$(document).delegate('input[type=text][data-role="export-file-name"]', 'blur', function(){
	var value = $.trim($(this).val());
	if(value.length && value.substr(0, 1) != '/'){
		value = '/' + value;
		$(this).val(value);
	}
});

// Step-by-step option
$(document).delegate('input#checkbox_STEP_BY_STEP', 'change', function(){
	var rowCount = $('tr#row_STEP_BY_STEP_COUNT');
	if($(this).is(':checked')){
		rowCount.show();
	}
	else {
		rowCount.hide();
	}
});
$(document).ready(function(){
	$('input#checkbox_STEP_BY_STEP').trigger('change');
});
$(document).delegate('input[data-role="step-export-reset"]', 'click', function(e){
	acritExpAjax('step_export_reset', '', function(JsonResult, textStatus, jqXHR){
		if(JsonResult.Success){
			$('tr#row_STEP_BY_STEP_INDEX').remove();
		}
		acritExpHandleAjaxError(jqXHR, false);
	}, function(jqXHR){
		acritExpHandleAjaxError(jqXHR, true);
	}, true);
});

/**
 *	Check updates
 *	Check lock
 */
$(document).ready(function(){
	function checkLock(){
		if(!window.lockChecking){
			var btnStart = $('#acrit-exp-popup-execute-button-start');
			if(btnStart.length && btnStart.is('[disabled]')){
				return;
			}
			window.lockChecking = true;
			acritExpAjax('check_lock', '', function(JsonResult){
				var notifier = $('#acrit-exp-lock-notifier');
				if(JsonResult.HTML && JsonResult.HTML.length){
					notifier.html(JsonResult.HTML);
				}
				else{
					notifier.html('');
				}
				window.lockChecking = false;
			}, function(jqXHR){
				console.log(jqXHR.responseText);
				window.lockChecking = false;
			}, true, true);
		}
		else{}
	}
	// Check lock
	if($('#acrit-exp-lock-notifier').length){
		window.lockChecking = false
		checkLock();
		setInterval(checkLock, 15000);
	}
});

/**
 *	Console
 */
$(document).delegate('input[data-role="console-execute"]', 'click', function(e){
	e.preventDefault();
	var iblockId = $('input[data-role="profile-iblock-id"]').val(),
		command = $('textarea[data-role="console-text"]').val(),
		height = $('textarea[data-role="console-text"]').outerHeight(),
		text = $('input[data-role="console-type"]').is(':checked') ? 'Y' : 'N';
		data = 'iblock_id='+iblockId+
						'&command='+command+
						'&height='+height+
						'&text='+text;
	acritExpAjax('console_execute', data, function(JsonResult){
		if(typeof JsonResult.HTML == 'string'){
			$('[data-role="console-results"]').html(JsonResult.HTML);
		}
		else if (typeof JsonResult.AccessDenied == 'string'){
			alert(JsonResult.AccessDenied);
		}
		else {
			alert('Error!');
			console.log(JsonResult);
		}
	}, function(jqXHR){
		console.log(jqXHR.responseText);
		$('[data-role="console-results"]').html(jqXHR.responseText);
	}, true, false);
});
$(document).delegate('input[data-role="console-fast-command"]', 'click', function(e){
	$('textarea[data-role="console-text"]').val($(this).attr('data-command'));
	$('input[data-role="console-type"]').prop('checked', $(this).attr('data-text') == 'Y' ? true : false);
	$('input[data-role="console-execute"]').trigger('click');
});

/**
 *	Check EXPORT_FILE_NAME is unique among all profiles
 */
$(document).delegate('input[type="text"][data-role="export-file-name"]', 'input', function(e){
	var exportFilename = $(this).val().trim();
	clearTimeout(window.acritExpExportFileNameTimeout);
	window.acritExpExportFileNameTimeout = setTimeout(function(){
		var data = {
			export_filename: exportFilename
		};
		acritExpAjax('check_export_filename_unique', data, function(JsonResult){
			if(typeof JsonResult.UniqueMessage == 'string' && JsonResult.UniqueMessage.length){
				alert(JsonResult.UniqueMessage);
			}
		}, null, false, true);
	}, 500);
});

/* Popup settings */
$(document).delegate('.acrit-exp-field-settings select', 'mousedown', acritExpSelectNextOption);

/* Teacher set icon (if has submenu) */
$(document).ready(function(){
	$('.adm-detail-toolbar a[onclick*="acritTeacher"]').attr('id', 'acrit-exp-button-teacher');
});