// Ajax-request general
/**
 *	Examples:
 *	ajaxAction = 'load_structure_iblock';
 *	ajaxAction = ['plugin_ajax_action','get_props_for_additional_fields'];
 */
function acritExpAjax(ajaxAction, data, callbackSuccess, callbackError, post, hideLoader){
	var lang = phpVars.LANGUAGE_ID;
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
		url: location.pathname+'?lang='+lang+'&ajax_action='+ajaxAction+action,
		type: post==true ? 'POST' : 'GET',
		data: data,
		datatype: 'json',
		success: function(data, textStatus, jqXHR){
			if(typeof callbackSuccess == 'function') {
				callbackSuccess(data, textStatus, jqXHR);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			if(typeof callbackError == 'function') {
				callbackError(jqXHR, textStatus, errorThrown);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		}
	});
}

// Module version in nav chain
$(document).ready(function(){
	if(acritExpModuleVersion.length > 0 && acritExpCoreVersion.length > 0) {
		$('a[id^="bx_admin_chain_item_menu_acrit_"]>span').append(' ('+acritExpModuleVersion+' / '+acritExpCoreVersion+')');
	}
});

function acritExpDoBackup(formId){
	var form = $('#form_'+formId);
	var ID = [];
	$('td.adm-list-table-checkbox > input[type=checkbox]', form).each(function(){
		if($(this).is(':checked')) {
			ID.push($(this).val());
		}
	});
	url = location.href.substr(0, location.href.length - location.hash.length);
	location.href = url + '&backup=' + (ID.length ? ID.join(',') : 'all');
}

/**
 *	POPUP: Backup/restore
 */
var AcritExpPopupRestore = new BX.CDialog({
	ID: 'AcritExpPopupRestore',
	title: BX.message('ACRIT_EXP_POPUP_RESTORE_TITLE'),
	content: '',
	resizable: true,
	draggable: true,
	height: 290,
	width: 650
});
AcritExpPopupRestore.Open = function(){
	this.Show();
	this.LoadContent();
}
AcritExpPopupRestore.LoadContent = function(){
	var thisPopup = this;
	//
	thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
	//
	thisPopup.DisableControls();
	acritExpAjax('load_popup_backup_restore', '', function(JsonResult){
		// Set popup buttons
		thisPopup.SetNavButtons();
		//
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
		thisPopup.EnableControls();
	}, function(jqXHR){
		console.log(jqXHR.responseText);
		thisPopup.EnableControls();
	}, true);
}
AcritExpPopupRestore.SetNavButtons = function(){
	$(this.PARTS.BUTTONS_CONTAINER).html('');
	this.SetButtons(
		[{
			'name': BX.message('ACRIT_EXP_POPUP_RESTORE_SAVE'),
			'className': 'adm-btn-green',
			'id': 'acrit-exp-popup-backup-restore-start',
			'action': function(){
				var thisPopup = this.parentWindow,
					form = $('form', thisPopup.DIV);
				if($('input[type=file]', form).val()==''){
					alert(BX.message('ACRIT_EXP_POPUP_RESTORE_NO_FILE'));
				}
				else {
					form.submit();
				}
			}
		}, {
			'name': BX.message('ACRIT_EXP_POPUP_RESTORE_CLOSE'),
			'action': function(){
				this.parentWindow.Close();
			}
		}]
	)
}
AcritExpPopupRestore.DisableControls = function(){
	$('#acrit-exp-popup-backup-restore-start', this.DIV).attr('disabled', 'disabled');
	$('input[type=text], input[type=file], input[type=button], select', this.DIV).attr('disabled', 'disabled');
}
AcritExpPopupRestore.EnableControls = function(){
	$('#acrit-exp-popup-backup-restore-start', this.DIV).removeAttr('disabled');
	$('input[type=text], input[type=file], input[type=button], select', this.DIV).removeAttr('disabled');
}

/**
 *	POPUP: Wizard quick start
 */
var AcritExpPopupWizardQuickStart = new BX.CDialog({
	ID: 'AcritExpPopupWizardQuickStart',
	title: BX.message('ACRIT_EXP_POPUP_WIZARD_QUICK_START_TITLE'),
	content: '',
	resizable: true,
	draggable: true,
	height: 360,
	width: 640
});
AcritExpPopupWizardQuickStart.Open = function(){
	this.Show();
	this.SetNavButtons(false);
	this.LoadContent();
}
AcritExpPopupWizardQuickStart.LoadContent = function(){
	var thisPopup = this;
	//
	thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
	//
	thisPopup.DisableControls();
	acritExpAjax('load_popup_wizard_quick_start', '', function(JsonResult){
		// Set popup buttons
		thisPopup.SetNavButtons();
		//
		$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
		$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
			'height': '100%',
			'-webkit-box-sizing': 'border-box',
			   '-moz-box-sizing': 'border-box',
			        'box-sizing': 'border-box'
		}).children().css({
			'height': '100%',
			'position': 'relative',
		});
		$('input[type=checkbox]', thisPopup.PARTS.CONTENT).not('.no-checkbox-styling').each(function(){
			BX.adminFormTools.modifyCheckbox(this);
		});
		$('.acrit_exp_wizard_quick_start_steps').children('[data-step="1"]').show();
		thisPopup.EnableControls();
		//
		var divSteps = $('.acrit_exp_wizard_quick_start_steps');
		//divSteps.attr('data-step-index', '1').attr('data-step-count', divSteps.children().length);
	}, function(jqXHR){
		console.log(jqXHR.responseText);
		thisPopup.EnableControls();
	}, true);
}
AcritExpPopupWizardQuickStart.SetNavButtons = function(visible){
	$(this.PARTS.BUTTONS_CONTAINER).html('');
	if(visible !== false){
		this.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_POPUP_WIZARD_QUICK_START_PREV'),
				'className': 'adm-btn',
				'id': 'acrit-exp-popup-wizard-quick-start-prev',
				'action': AcritExpPopupWizardQuickStart.PrevNextClick
			}, {
				'name': BX.message('ACRIT_EXP_POPUP_WIZARD_QUICK_START_NEXT'),
				'className': 'adm-btn-green',
				'id': 'acrit-exp-popup-wizard-quick-start-next',
				'action': AcritExpPopupWizardQuickStart.PrevNextClick
			}]
		)
		$('#acrit-exp-popup-wizard-quick-start-prev').attr('disabled', 'disabled');
		$(this.PARTS.BUTTONS_CONTAINER).append(' &nbsp; &nbsp; <span data-role="acrit_exp_wizard_message"></span>')
	}
}
AcritExpPopupWizardQuickStart.DisableControls = function(){
	$('#acrit-exp-popup-backup-restore-start', this.PARTS.CONTENT_DATA).attr('disabled', 'disabled');
	$('input[type=text], input[type=file], input[type=button], select', this.PARTS.CONTENT_DATA)
		.attr('disabled', 'disabled');
}
AcritExpPopupWizardQuickStart.EnableControls = function(){
	$('#acrit-exp-popup-backup-restore-start', this.PARTS.CONTENT_DATA).removeAttr('disabled');
	$('input[type=text], input[type=file], input[type=button], select', this.PARTS.CONTENT_DATA)
		.removeAttr('disabled');
}
AcritExpPopupWizardQuickStart.PrevNextClick = function(){
	let
		divSteps = $('.acrit_exp_wizard_quick_start_steps'),
		curStep = !isNaN(parseInt(divSteps.attr('data-step'))) ? parseInt(divSteps.attr('data-step')) : 1,
		isPrev = !!$(this).attr('id').match(/prev$/),
		isNext = !!$(this).attr('id').match(/next$/),
		step = isPrev ? curStep - 1 : curStep + 1,
		divCurStep = divSteps.children().filter('[data-step='+curStep+']'),
		divNextStep = divSteps.children().filter('[data-step='+step+']'),
		lastStep = parseInt(divSteps.children('[data-step]').last().attr('data-step')),
		isLastStep = step == lastStep,
		isFinish = step > lastStep,
		btnPrev = $('#acrit-exp-popup-wizard-quick-start-prev'),
		btnNext = $('#acrit-exp-popup-wizard-quick-start-next'),
		callbackOut = divCurStep.attr('data-callback-out'),
		callbackIn = divNextStep.attr('data-callback-in'),
		spanMessage = $('span[data-role="acrit_exp_wizard_message"]', this.parentWindow.PARTS.BUTTONS_CONTAINER),
		checkAll = $('input[data-role="acrit_exp_wizard_quick_start_select_all"]'),
		wizardControls = $('.acrit_exp_wizard_quick_start_steps :input', this.parentWindow.PARTS.BUTTONS_CONTAINER)
			.add(btnPrev).add(btnNext);
	if(isNext && typeof window[callbackOut] == 'function'){
		let callbackResult = window[callbackOut](divCurStep, spanMessage);
		if(callbackResult === false){
			return false;
		}
	}
	if(typeof window[callbackIn] == 'function'){
		let callbackResult = window[callbackIn](divNextStep, spanMessage);
		if(callbackResult === false){
			return false;
		}
	}
	checkAll.prop('checked', false);
	spanMessage.text('');
	if(divNextStep.length){
		divSteps.attr('data-step', step);
		divNextStep.show().siblings().hide();
	}
	if(step == 1){
		btnPrev.attr('disabled', 'disabled');
	}
	else{
		btnPrev.removeAttr('disabled');
	}
	if(isFinish){
		wizardControls.attr('disabled', 'disabled');
		let data = $('.acrit_exp_wizard_quick_start_steps :input').serialize();
		acritExpAjax('wizard_quick_start_process', data, function(JsonResult){
			wizardControls.removeAttr('disabled');
			if(JsonResult.Success){
				alert(JsonResult.SuccessMessage);
				AcritExpPopupWizardQuickStart.Close();
				AcritExpProfiles.GetAdminList(''); //location.reload();
			}
			else{
				alert('Error.');
			}
		}, function(jqXHR){
			wizardControls.removeAttr('disabled');
			alert('Error.');
			console.log(jqXHR.responseText);
		}, true);
	}
	else if(isLastStep || isFinish){
		btnNext.val(BX.message('ACRIT_EXP_POPUP_WIZARD_QUICK_START_FINISH'));
	}
	else{
		btnNext.val(BX.message('ACRIT_EXP_POPUP_WIZARD_QUICK_START_NEXT'));
	}
}
function acrit_exp_wizard_callback_in_plugins(divStep, spanMessage){
	setTimeout(function(){
		$('input[data-role="acrit_exp_wizard_quick_start_plugin_checkbox"]', divStep).first().trigger('change');
	}, 10);
}
function acrit_exp_wizard_callback_out_plugins(divStep, spanMessage){
	let checkboxes = $('input[data-role="acrit_exp_wizard_quick_start_plugin_checkbox"]:checked', divStep);
	if(!checkboxes.length){
		spanMessage.text(BX.message('ACRIT_EXP_POPUP_WIZARD_QUICK_START_NO_PLUGIN'));
		return false;
	}
}
function acrit_exp_wizard_callback_out_iblocks(divStep, spanMessage){
	let checkboxes = $('input[data-role="acrit_exp_wizard_quick_start_iblock_checkbox"]:checked', divStep);
	if(!checkboxes.length){
		spanMessage.text(BX.message('ACRIT_EXP_POPUP_WIZARD_QUICK_START_NO_PLUGIN'));
		return false;
	}
}
function acrit_exp_wizard_callback_in_iblocks(divStep, spanMessage){
	setTimeout(function(){
		$('input[data-role="acrit_exp_wizard_quick_start_iblock_checkbox"]', divStep).first().trigger('change');
	}, 10);
}
function acrit_exp_wizard_callback_out_confirm(divStep, spanMessage){
	let
		site = $('select[data-role="acrit_exp_wizard_quick_start_site"]', divStep).val(),
		domain = $('input[data-role="acrit_exp_wizard_quick_start_domain"]', divStep).val(),
		https = $('input[data-role="acrit_exp_wizard_quick_start_https"]', divStep).prop('checked') ? 'Y' : 'N',
		offersMode = $('select[data-role="acrit_exp_wizard_quick_start_offers_mode"]', divStep).val(),
		run = $('input[data-role="acrit_exp_wizard_quick_start_run"]', divStep).prop('checked') ? 'Y' : 'N';
}
function acrit_exp_wizard_callback_in_confirm(divStep, spanMessage){
	
}

/**
 *	Select file to <input type="file" />
 */
$(document).delegate('table.acrit-exp-backup-restore td .file_wrapper input[type=file]', 'change', function(e){
	var fileName = this.value.match(/[^\/\\]+$/),
		textInput = $(this).parent().find('input[type=text]');
	if(fileName==null) {
		textInput.val('');
		this.value = '';
	}
	else {
		var fileExt = this.value.split('.').slice(-1)[0].toLowerCase();
		if(fileExt=='zip') {
			var fileSize = this.files[0].size,
				sizeUnit = ["B", "KB", "MB"],
				index = 0;
			while(fileSize >= 1024 && index < 2) {
				fileSize /= 1024;
				index++;
			}
			fileSize = Math.round(fileSize * 100) / 100 + sizeUnit[index];
			fileName = fileName[0];
			textInput.val(fileName+' ('+fileSize+')');
		}
		else {
			textInput.val('');
			this.value = '';
			alert(BX.message('ACRIT_EXP_POPUP_RESTORE_WRONG_FILE'));
		}
	}
});

/**
 *	Delete all
 */
$(document).delegate('input[data-role="profiles-delete-all"]', 'click', function(e){
	var thisButton = this;
	$('[data-role="restore-status"]').html('');
	setTimeout(function(){
		if(confirm($(thisButton).data('confirm'))){
			acritExpAjax('profiles_delete_all', '', function(JsonResult){
				$('[data-role="restore-status"]').html(JsonResult.HTML);
				if(AcritExpProfiles){
					AcritExpProfiles.GetAdminList('');
				}
			}, function(jqXHR){
				console.log(jqXHR.responseText);
			}, true);
		}
	}, 10);
});

/**
 *	Show warning on select mode EXACT
 */
$(document).delegate('select[data-role="backup-restore-mode"]', 'change', function(){
	var warningBlock = $('[data-role="backup-restore-exact-warning"]');
	if($(this).val() == 'exact'){
		warningBlock.show();
	}
	else{
		warningBlock.hide();
	}
});

/**
 *	Handler form suubmit and load iframe
 */
$(document).delegate('#acrit-exp-form-backup-restore', 'submit', function(e){
	$('[data-role="restore-status"]').html('');
	setTimeout(function(){
		AcritExpPopupRestore.DisableControls();
	}, 10);
});
function acritExpRestoreIFrameLoaded(iframe){
	var response = $(iframe).contents().find('body').text();
	if(response.length) {
		var JsonResult = BX.parseJSON(response);
		console.log(JsonResult);
		if(typeof JsonResult == 'object' && JsonResult.HTML) {
			$('[data-role="restore-status"]').html(JsonResult.HTML);
		}
		else {
			alert(BX.message('ACRIT_EXP_POPUP_RESTORE_RESTORE_ERROR'));
		}
		AcritExpPopupRestore.EnableControls();
		if(AcritExpProfiles){
			AcritExpProfiles.GetAdminList('');
		}
	}
}


/**
 *	Wizard: Quick start
 */
/* Plugins */
$(document).delegate('input[data-role="acrit_exp_wizard_quick_start_plugin_checkbox"]', 'change', function(){
	let
		checkboxesAll = $('input[data-role="acrit_exp_wizard_quick_start_plugin_checkbox"]'),
		checkboxesVisible = $('label:visible > input[data-role="acrit_exp_wizard_quick_start_plugin_checkbox"]'),
		countAll = checkboxesAll.length,
		countCheckedAll = checkboxesAll.filter(':checked').length,
		countVisible = checkboxesVisible.length,
		countCheckedVisible = checkboxesVisible.filter(':checked').length,
		checkboxSelectAll = $('input[data-role="acrit_exp_wizard_quick_start_select_all"]'),
		span = $('span[data-role="acrit_exp_wizard_quick_start_selected"]');
	span.text(countCheckedAll);
	if(countVisible > 0 && countCheckedVisible == countVisible){
		checkboxSelectAll.prop('checked', true);
	}
	else {
		checkboxSelectAll.prop('checked', false);
	}
});
$(document).delegate('input[data-role="acrit_exp_wizard_quick_start_select_all"]', 'change', function(e){
	let checked = $(this).prop('checked');
	$('label:visible > input[data-role="acrit_exp_wizard_quick_start_plugin_checkbox"]')
		.prop('checked', checked).last().trigger('change');
});
$(document).delegate('input[data-role="acrit_exp_wizard_quick_start_plugins_filter"]', 'input', function(e){
	let
		text = $(this).val().trim().toLowerCase(),
		plugins = $('div[data-role="acrit_exp_wizard_quick_start_plugin"]'),
		formats = $('div[data-role="acrit_exp_wizard_quick_start_format"]'),
		divNothing = $('div[data-role="acrit_exp_wizard_quick_start_plugins_nothing_found"]');
	if(text.length){
		plugins.each(function(){
			let
				found = false,
				filtered;
			$('div[data-role="acrit_exp_wizard_quick_start_format"]', this).each(function(){
				filtered = $(this).attr('data-filter').toLowerCase().indexOf(text) != -1;
				$(this).toggle(filtered);
				found = found || filtered;
			});
			if(!found){
				filtered = $(this).attr('data-filter').toLowerCase().indexOf(text) != -1;
			}
			$(this).toggle(found);
		});
	}
	else{
		plugins.show();
		formats.show();
	}
	$('input[data-role="acrit_exp_wizard_quick_start_plugin_checkbox"]').first().trigger('change');
	if(plugins.filter(':visible').length){
		divNothing.hide();
	}
	else{
		divNothing.show();
	}
});
/* IBlocks */
$(document).delegate('input[data-role="acrit_exp_wizard_quick_start_iblock_checkbox"]', 'change', function(){
	let
		checkboxesAll = $('input[data-role="acrit_exp_wizard_quick_start_iblock_checkbox"]'),
		checkboxesVisible = $('label:visible > input[data-role="acrit_exp_wizard_quick_start_iblock_checkbox"]'),
		countAll = checkboxesAll.length,
		countCheckedAll = checkboxesAll.filter(':checked').length,
		countVisible = checkboxesVisible.length,
		countCheckedVisible = checkboxesVisible.filter(':checked').length,
		checkboxSelectAll = $('input[data-role="acrit_exp_wizard_quick_start_select_all"]'),
		span = $('span[data-role="acrit_exp_wizard_quick_start_selected"]');
	span.text(countCheckedAll);
	if(countVisible > 0 && countCheckedVisible == countVisible){
		checkboxSelectAll.prop('checked', true);
	}
	else {
		checkboxSelectAll.prop('checked', false);
	}
});
$(document).delegate('input[data-role="acrit_exp_wizard_quick_start_select_all"]', 'change', function(e){
	let checked = $(this).prop('checked');
	$('label:visible > input[data-role="acrit_exp_wizard_quick_start_iblock_checkbox"]')
		.prop('checked', checked).last().trigger('change');
});
$(document).delegate('input[data-role="acrit_exp_wizard_quick_start_iblocks_filter"]', 'input', function(e){
	let
		text = $(this).val().trim().toLowerCase(),
		types = $('div[data-role="acrit_exp_wizard_quick_start_iblock_type"]'),
		iblocks = $('div[data-role="acrit_exp_wizard_quick_start_iblock"]'),
		divNothing = $('div[data-role="acrit_exp_wizard_quick_start_iblocks_nothing_found"]');
	if(text.length){
		types.each(function(){
			let
				found = false,
				filtered;
			$('div[data-role="acrit_exp_wizard_quick_start_iblock"]', this).each(function(){
				filtered = $(this).attr('data-filter').toLowerCase().indexOf(text) != -1;
				$(this).toggle(filtered);
				found = found || filtered;
			});
			if(!found){
				filtered = $(this).attr('data-filter').toLowerCase().indexOf(text) != -1;
			}
			$(this).toggle(found);
		});
	}
	else{
		types.show();
		iblocks.show();
	}
	$('input[data-role="acrit_exp_wizard_quick_start_iblock_checkbox"]').first().trigger('change');
	if(types.filter(':visible').length){
		divNothing.hide();
	}
	else{
		divNothing.show();
	}
});
