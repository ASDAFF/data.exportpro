if (!window.bitrix24PluginInitialized) {
	//
	window.bitrix24PluginInitialized = true;

	// // Reset PROCESS_NEXT_POS param
	// $(document).delegate('#test_rest_btn', 'click', function(e) {
	// 	var btn = $(this);
	// 	if (!btn.hasClass('adm-btn-disabled')) {
	// 		acritExpAjax(['plugin_ajax_action', 'rest_test'], {}, function (JsonResult, textStatus, jqXHR) {
	// 				console.log(JsonResult);
	// 				btn.removeClass('adm-btn-disabled');
	// 			}, function (jqXHR) {
	// 				console.log(jqXHR);
	// 			}, true
	// 		);
	// 	}
	// 	return false;
	// });

	// Reset PROCESS_NEXT_POS param
	$(document).delegate('#acrit_exp_plugin_vk_process_next_pos_reset', 'click', function(e) {
		var btn = $(this);
		if (!btn.hasClass('adm-btn-disabled')) {
			if (confirm(BX.message('SETTINGS_PROCESS_NEXT_POS_RESET_ALERT'))) {
				data = {};
				btn.addClass('adm-btn-disabled');
				acritExpAjax(['plugin_ajax_action', 'params_next_pos_reset'], data, function (JsonResult, textStatus, jqXHR) {
						//console.log(JsonResult);
						btn.removeClass('adm-btn-disabled');
						if (JsonResult.result == 'ok') {
							$('#acrit_exp_plugin_vk_process_next_pos_view').text('0');
						}
					}, function (jqXHR) {
						console.log(jqXHR);
					}, true
				);
			}
		}
		return false;
	});
}
