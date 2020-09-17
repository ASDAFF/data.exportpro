if(!window.customCsvGeneralPluginInitialized){
	//
	$(document).delegate('input[data-role="custom-csv-utm-toggle"]', 'change', function(e){
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
	//
	function customCsvGeneralTriggers(){
		$('input[data-role="custom-csv-utm-toggle"]').trigger('change');
	}
	//
	window.customCsvGeneralPluginInitialized = true;
}

// On load
setTimeout(function(){
	customCsvGeneralTriggers();
}, 500);
$(document).ready(function(){
	customCsvGeneralTriggers();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	customCsvGeneralTriggers();
});
