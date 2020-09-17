BX.ready(function(){
	setTimeout(function(){
		BX.ajax({
			url: '/bitrix/admin/acrit_core_check_updates.php?regular=Y&lang='+phpVars.LANGUAGE_ID,
			method: 'GET',
			dataType: 'json',
			cache: false,
			async: true,
			start: true
		});
	}, 1000);
});
/*
$(document).ready(function(){
	setTimeout(function(){
		// Check updates
		$.ajax({
			url: '/bitrix/admin/acrit_core_check_updates.php',
			type: 'GET',
			data: {
				lang: phpVars.LANGUAGE_ID,
				regular: 'Y'
			},
			datatype: 'json',
			success: function(data, textStatus, jqXHR){
				//
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log('acrit.core update result [check_updates_regular.js]:');
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
			}
		});
	}, 1000);
});
*/