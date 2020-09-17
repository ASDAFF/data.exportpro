$(document).ready(function(){
	setTimeout(function(){
		// Check updates
		var div = $('#acrit-module-update-notifier');
		if(div.length){
			var module = (location.pathname + location.search).match(/acrit[._]{1}[a-z]+/)[0].replace(/_/, '.');
			$.ajax({
				url: '/bitrix/admin/acrit_core_check_updates.php',
				type: 'GET',
				data: {
					lang: phpVars.LANGUAGE_ID,
					module: module
				},
				datatype: 'json',
				success: function(data, textStatus, jqXHR){
					div.html(data.HTML).show();
				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log(jqXHR);
					console.log(textStatus);
					console.log(errorThrown);
				}
			});
		}
	}, 1000);
});