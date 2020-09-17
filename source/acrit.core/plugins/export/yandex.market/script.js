if (!window.yandexMarketPluginInitialized) {
	
	$(document).delegate('#row_YANDEX_MARKET_EXPORT_PROMOCODES input[type=checkbox]', 'change', function(e) {
		var rowsAllPromocodes = $('#row_YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS');
		if ($(this).is(':checked')) {
			rowsAllPromocodes.show();
		} else {
			rowsAllPromocodes.hide();
		}
	});
	
	$(document).delegate('#row_YANDEX_MARKET_EXPORT_SPECIAL_PRICE input[type=checkbox]', 'change', function(e) {
		var rowsAll = $('tr.heading[data-header="HEADER_PROMOS_SPECIAL_PRICE"]')
			.add('tr.adm-list-table-row[data-field="SPECIAL_PRICE_START"]')
			.add('tr.adm-list-table-row[data-field="SPECIAL_PRICE_END"]')
			.add('tr.adm-list-table-row[data-field="SPECIAL_PRICE_DESCRIPTION"]')
			.add('tr.adm-list-table-row[data-field="SPECIAL_PRICE_URL"]');
			//.add('tr.adm-list-table-row[data-field="SPECIAL_PRICE_DISCOUNT_PRICE"]')
			//.add('tr.adm-list-table-row[data-field="SPECIAL_PRICE_CURRECNY"]');
		if ($(this).is(':checked')) {
			rowsAll.show();
		} else {
			rowsAll.hide();
		}
	});
	
	$(document).delegate('#row_YANDEX_MARKET_EXPORT_PROMOCARD input[type=checkbox]', 'change', function(e) {
		var rowsAll = $('tr.heading[data-header="HEADER_PROMOS_PROMOCARD"]')
			.add('tr.adm-list-table-row[data-field="PROMOCARD_START"]')
			.add('tr.adm-list-table-row[data-field="PROMOCARD_END"]')
			.add('tr.adm-list-table-row[data-field="PROMOCARD_DESCRIPTION"]')
			.add('tr.adm-list-table-row[data-field="PROMOCARD_URL"]')
			.add('tr.adm-list-table-row[data-field="PROMOCARD_PRICE"]')
			.add('tr.adm-list-table-row[data-field="PROMOCARD_CURRECNY"]');
		if ($(this).is(':checked')) {
			rowsAll.show();
		} else {
			rowsAll.hide();
		}
	});
	
	$(document).delegate('#row_YANDEX_MARKET_EXPORT_N_PLUS_M input[type=checkbox]', 'change', function(e) {
		var rowsAll = $('tr.heading[data-header="HEADER_PROMOS_N_PLUS_M"]')
			.add('tr.adm-list-table-row[data-field="N_PLUS_M_START"]')
			.add('tr.adm-list-table-row[data-field="N_PLUS_M_END"]')
			.add('tr.adm-list-table-row[data-field="N_PLUS_M_DESCRIPTION"]')
			.add('tr.adm-list-table-row[data-field="N_PLUS_M_URL"]')
			.add('tr.adm-list-table-row[data-field="N_PLUS_M_REQUIRED_QUANTITY"]')
			.add('tr.adm-list-table-row[data-field="N_PLUS_M_FREE_QUANTITY"]');
		if ($(this).is(':checked')) {
			rowsAll.show();
		} else {
			rowsAll.hide();
		}
	});
	
	$(document).delegate('#row_YANDEX_MARKET_EXPORT_GIFTS input[type=checkbox]', 'change', function(e) {
		var rowsAll = $('tr.heading[data-header="HEADER_PROMOS_GIFTS"]')
			.add('tr.adm-list-table-row[data-field="GIFTS_ID"]')
			.add('tr.adm-list-table-row[data-field="GIFTS_DESCRIPTION"]')
			.add('tr.adm-list-table-row[data-field="GIFTS_URL"]');
		if ($(this).is(':checked')) {
			rowsAll.show();
		} else {
			rowsAll.hide();
		}
	});
	
	function acritExpYandexMarketTriggers(){
		$('#row_YANDEX_MARKET_EXPORT_PROMOCODES input[type=checkbox]').trigger('change');
		$('#row_YANDEX_MARKET_EXPORT_SPECIAL_PRICE input[type=checkbox]').trigger('change');
		$('#row_YANDEX_MARKET_EXPORT_PROMOCARD input[type=checkbox]').trigger('change');
		$('#row_YANDEX_MARKET_EXPORT_N_PLUS_M input[type=checkbox]').trigger('change');
		$('#row_YANDEX_MARKET_EXPORT_GIFTS input[type=checkbox]').trigger('change');
	}

	window.yandexMarketPluginInitialized = true;
}

// On load
setTimeout(function(){
	acritExpYandexMarketTriggers();
}, 500);
$(document).ready(function(){
	acritExpYandexMarketTriggers();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpYandexMarketTriggers();
});
