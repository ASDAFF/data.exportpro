if(!window.yandexMarketPluginInitialized){
	$(document).delegate('#row_YANDEX_MARKET_EXPORT_GIFTS input[type=checkbox]', 'click', function(e){
		var rowsAll = $('tr.heading[data-header="header_gifts"], tr.adm-list-table-row[data-field="GIFTS_ID"],'
			+'tr.adm-list-table-row[data-field="GIFTS_DESCRIPTION"], tr.adm-list-table-row[data-field="GIFTS_URL"]');
		if($(this).is(':checked')){
			rowsAll.show();
		}
		else{
			rowsAll.hide();
		}
	});
	window.yandexMarketPluginInitialized = true;
}

$(document).ready(function(){
	$('#row_YANDEX_MARKET_EXPORT_GIFTS input[type=checkbox]').trigger('click');
});

setTimeout(function(){
	$('#row_YANDEX_MARKET_EXPORT_GIFTS input[type=checkbox]').trigger('click');
},500);