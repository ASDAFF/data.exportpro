if (!window.acritExpGoodsJsonInitialized) {
	window.acritExpGoodsJsonInitialized = true;
	
	$(document).delegate('select[data-role="acrit-exp-goods-json-storage-switcher"]', 'change', function(e){
		var rowInternal = $('table.acrit-exp-goods-storage-directory-internal').closest('tr').hide(),
			rowExternal = $('table.acrit-exp-goods-storage-directory-external').closest('tr').hide();
		if($(this).val() == 'external'){
			rowExternal.show();
		}
		else{
			rowInternal.show();
		}
	});
	
	function acritExpGoodsJsonTriggers(){
		$('select[data-role="acrit-exp-goods-json-storage-switcher"]').trigger('change');
	}
	
}

// On load
setTimeout(function(){
	acritExpGoodsJsonTriggers();
}, 500);
$(document).ready(function(){
	acritExpGoodsJsonTriggers();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpGoodsJsonTriggers();
});
