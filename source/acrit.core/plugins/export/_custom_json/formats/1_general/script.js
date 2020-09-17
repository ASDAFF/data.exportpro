if(!window.customJsonGeneralPluginInitialized){
	//
	$(document).delegate('input[data-role="custom-json-add-utm"]', 'change', function(e){
		var rowField = $('#tr_JSON_UTM_FIELD'),
			rowsAll = $('tr.heading[data-header="HEADER_UTM"]')
			.add('tr.adm-list-table-row[data-field="?utm_source"]')
			.add('tr.adm-list-table-row[data-field="?utm_medium"]')
			.add('tr.adm-list-table-row[data-field="?utm_campaign"]')
			.add('tr.adm-list-table-row[data-field="?utm_content"]')
			.add('tr.adm-list-table-row[data-field="?utm_term"]');
		if ($(this).is(':checked')) {
			rowsAll.show();
			rowField.show();
		} else {
			rowsAll.hide();
			rowField.hide();
		}
	});
	$(document).delegate('input[data-role="custom-json-offers-preprocess"]', 'change', function(e){
		var rowField = $('#tr_OFFERS_PREPROCESS_FIELD');
		if ($(this).is(':checked')) {
			rowField.show();
		} else {
			rowField.hide();
		}
	});
	//
	function customCsvGeneralTriggers(){
		$('input[data-role="custom-json-add-utm"]').trigger('change');
		$('input[data-role="custom-json-offers-preprocess"]').trigger('change');
	}
	//
	window.customJsonGeneralPluginInitialized = true;
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
