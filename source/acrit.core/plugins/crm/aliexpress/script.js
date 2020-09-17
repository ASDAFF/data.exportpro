
$(document).delegate('[data-role="connection-check"]', 'click', function(e) {
	var btn = $(this);
	var token = $('[data-role="connect-cred-token"]').val();
	if (!btn.hasClass('adm-btn-disabled')) {
		data = {
			'token': token,
		};
		btn.addClass('adm-btn-disabled');
		acritExpAjax(['plugin_ajax_action', 'connection_check'], data, function (JsonResult, textStatus, jqXHR) {
			if (JsonResult.result == 'ok') {
				btn.removeClass('adm-btn-disabled');
				if (JsonResult.check == 'success') {
					$('#check_msg').html(JsonResult.message);
				}
				else {
					$('#check_msg').html('<span class="required">' + JsonResult.message + '</span>');
				}
			}
		}, function (jqXHR) {
			console.log(jqXHR);
		}, true);
	}
	return false;
});


$(document).ready(function(){

});
