// Run export [Alt+R]
$.alt('R', function() {
	if(AcritExpPopupExecute.isOpen){
		var buttonStart = $('#acrit-exp-popup-execute-button-start'),
			buttonUnlock = $('[data-role="profile-unlock"]', AcritExpPopupExecute.PARTS.CONTENT_DATA);
		if(buttonUnlock.length){
			buttonUnlock.trigger('click');
		}
		else{
			if(!buttonStart.is('[disabled]')){
				buttonStart.trigger('click');
			}
		}
	}
	else{
		AcritExpPopupExecute.Open();
	}
	return false;
});
// Stop export [Alt+H] // Halt
$.alt('H', function() {
	if(AcritExpPopupExecute.isOpen){
		var button = $('#acrit-exp-popup-execute-button-stop');
		if(!button.is('[disabled]')){
			button.trigger('click');
		}
	}
	return false;
});
// Open fields for MAIN iblock [Alt+1]
$.alt('1', function() {
	$('#tab_cont_structure').trigger('click');
	$('#view_tab_fields_product').trigger('click');
	return false;
});
// Open fields for OFFERS iblock [Alt+2]
$.alt('2', function() {
	$('#tab_cont_structure').trigger('click');
	$('#view_tab_fields_offer').trigger('click');
	return false;
});
// Open subtab: general params
$.alt('3', function() {
	$('#tab_cont_structure').trigger('click');
	$('#view_tab_subtab_general').trigger('click');
	return false;
});
// Open subtab: categories
$.alt('4', function() {
	$('#tab_cont_structure').trigger('click');
	$('#view_tab_subtab_categories').trigger('click');
	return false;
});
// Open subtab: filter
$.alt('5', function() {
	$('#tab_cont_structure').trigger('click');
	$('#view_tab_subtab_filter').trigger('click');
	return false;
});
// Open subtab: offer settings
$.alt('6', function() {
	$('#tab_cont_structure').trigger('click');
	$('#view_tab_subtab_offers').trigger('click');
	return false;
});
// Open console [Alt+C]
$.alt('C', function() {
	$('#tab_cont_structure').trigger('click');
	$('#view_tab_subtab_console').css('display','inline-block').trigger('click');
	return false;
});
// Execute console [Alt+X]
$.alt('X', function() {
	if($('#tab_cont_structure').is('.adm-detail-tab-active') && $('#view_tab_subtab_console').is('.adm-detail-subtab-active')){
		$('[data-role="console-execute"]').trigger('click');
	}
	return false;
});
// Clear profile export data [Alt+T]
$.alt('T', function() {
	if(confirm(BX.message('ACRIT_EXP_AJAX_CONFIRM_CLEAR_EXPORT_DATA'))){
		acritExpAjax('clear_profile_export_data', '', null, null, false, false);
	}
	return false;
});
// Open/refresh preview popup
$.alt('P', function() {
	if(AcritExpPopupIBlocksPreview.isOpen){
		var button = $('#AcritExpPopupIBlocksPreview_btnRefresh');
		if(!button.is('[disabled]')){
			button.trigger('click');
		}
	}
	else{
		$('input[data-role="preview-iblocks"]').trigger('click');
	}
	return false;
});
// Open export file / social page
$.alt('O', function() {
	var opener = $('.acrit-exp-progress a.acrit-exp-file-open-link[href]').first();
	if(!opener.length){
		opener = $('a.acrit-exp-file-open-link[href]').first();
	}
	if(opener.length && opener.attr('href') != undefined) {
		window.open(opener.attr('href'));
	}
	return false;
});
// Open first's element preview
$.alt('F', function() {
	var iblockId = $('input[data-role="profile-iblock-id"]').val();
	if(window.acritExpFindFirstElement){
		window.acritExpFindFirstElement.abort();
	}
	window.acritExpFindFirstElement = acritExpAjax('find_first_element', 'iblock_id='+iblockId, function(data, textStatus, jqXHR){
		if(typeof data.FirstElement == 'object'){
			window.open(data.FirstElement['_URL']);
		}
		else if(typeof data.ErrorMessage == 'string'){
			alert(data.ErrorMessage);
		}
	}, null, false, false);
	return false;
});
// Save iblock settings
$.alt('S', function() {
	$('[data-role="iblock-settings-save"]').trigger('click', {hotkey:true});
	return false;
});
// Open tab 'General'
$.alt('G', function() {
	$('#tab_cont_general').trigger('click');
	return false;
});
// Open tab 'Cron'
$.alt('A', function() {
	$('#tab_cont_cron').trigger('click');
	return false;
});
// Open tab 'Log and History'
$.alt('L', function() {
	$('#tab_cont_log').trigger('click');
	$('[data-role="log-refresh"]').trigger('click');
	$('[data-role="profile-history-refresh"]').trigger('click');
	return false;
});
// Backup current profile
$.alt('B', function() {
	window.open(acritExpProfileBackupUrl);
	return false;
});