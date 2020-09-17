if(!window.customExcelGeneralPluginInitialized){
	//
	$(document).delegate('input[data-role="custom-excel-utm-toggle"]', 'change', function(e){
		var rowsAll = $('tr.heading[data-header="_UTM_FIELDS"]')
			.add('tr.adm-list-table-row[data-field="UTM_SOURCE"]')
			.add('tr.adm-list-table-row[data-field="UTM_MEDIUM"]')
			.add('tr.adm-list-table-row[data-field="UTM_CAMPAIGN"]')
			.add('tr.adm-list-table-row[data-field="UTM_CONTENT"]')
			.add('tr.adm-list-table-row[data-field="UTM_TERM"]')
		if ($(this).is(':checked')) {
			rowsAll.show();
		} else {
			rowsAll.hide();
		}
	});
	$(document).delegate('input[data-role="custom-excel-add-header"]', 'change', function(e){
		var freezeRow = $('input[data-role="custom-excel-freeze-header"]').closest('tr');
		if($(this).is(':checked')){
			freezeRow.show();
		}
		else{
			freezeRow.hide();
		}
	});
	$(document).delegate('input[data-role="custom-excel-multisheet"]', 'change', function(e){
		var sheetTitleRow = $('input[data-role="custom-excel-sheet-title"]').closest('tr');
		if($(this).is(':checked')){
			sheetTitleRow.hide();
		}
		else{
			sheetTitleRow.show();
		}
	});
	$(document).delegate('select[data-role="excel-general-format"]', 'change', function(e){
		var inputFilename = $('input[data-role="export-file-name"]'),
			filename = inputFilename.val(),
			format = $(this).val(),
			rowNoXmlWriter = $('#tr_FORMAT_NO_XML_WRITER');
		filename = filename.replace(/\.(xlsx|xls|ods|pdf)$/i, '.' + $(this).find('option:selected').val().toLowerCase());
		if(filename.length){
			inputFilename.val(filename);
		}
		if(format == 'XLSX' || format == 'ODS') {
			rowNoXmlWriter.show();
		}
		else{
			rowNoXmlWriter.hide();
		}
	});
	//
	function customExcelGeneralTriggers(){
		$('input[data-role="custom-excel-utm-toggle"]').trigger('change');
		$('input[data-role="custom-excel-add-header"]').trigger('change');
		$('input[data-role="custom-excel-multisheet"]').trigger('change');
		$('select[data-role="excel-general-format"]').trigger('change');
	}
	//
	window.customExcelGeneralPluginInitialized = true;
}

// On load
setTimeout(function(){
	customExcelGeneralTriggers();
}, 500);
$(document).ready(function(){
	customExcelGeneralTriggers();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	customExcelGeneralTriggers();
});
