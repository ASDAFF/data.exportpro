function acritExpAjax(ajaxAction, data, callbackSuccess, callbackError, post, hideLoader){
	var lang = phpVars.LANGUAGE_ID,
		mid = location.search.match(/mid=([a-z0-9-_\.]+)/)[1];
	//
	if(typeof data == 'string' && data.substr(0,1)=='&'){
		data = data.substr(1);
	}
	//
	if(hideLoader!==true) {
		BX.showWait();
	}
	var action = '';
	if($.isArray(ajaxAction)) {
		action = ajaxAction[1];
		ajaxAction = ajaxAction[0];
	}
	if(action.length){
		action = '&action='+action;
	}
	return $.ajax({
		url: location.pathname+'?mid='+mid+'&lang='+lang+'&ajax_action='+ajaxAction+action,
		type: post==true ? 'POST' : 'GET',
		data: data,
		datatype: 'json',
		success: function(data, textStatus, jqXHR){
			if(typeof callbackSuccess == 'function') {
				callbackSuccess(data, textStatus, jqXHR);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			if(typeof callbackError == 'function') {
				callbackError(jqXHR, textStatus, errorThrown);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		}
	});
}

/* Log */
function acritExpOptionsHandleLogTextarea(log){
	var textarea = $('textarea[data-role="module-log"]');
	if(log==true){
		log = textarea.val();
	}
	if(log && log.length){
		textarea.val(log).removeAttr('disabled').css('height','');
	}
	else {
		textarea.val('').attr('disabled', 'disabled').css('height', textarea.data('empty-height'));
	}
}
$(document).delegate('input[data-role="module-log-refresh"]', 'click', function(e){
	acritExpAjax('log_refresh', '', function(JsonResult, textStatus, jqXHR){
		if(JsonResult.Success){
			acritExpOptionsHandleLogTextarea(JsonResult.Log);
		}
		else {
			acritExpOptionsHandleLogTextarea(null);
		}
		if(JsonResult.LogSize != undefined){
			$('[data-role="log-full-size"]').html(JsonResult.LogSize);
		}
	}, function(jqXHR){
		acritExpOptionsHandleLogTextarea(null);
	}, false);
});
$(document).delegate('input[data-role="module-log-clear"]', 'click', function(e){
	acritExpAjax('log_clear', '', function(JsonResult, textStatus, jqXHR){
		acritExpOptionsHandleLogTextarea(null);
	}, function(jqXHR){
		acritExpOptionsHandleLogTextarea(null);
	}, false);
});
$(document).delegate('input[data-role="check-php-path"]', 'click', function(e){
	var phpPath = $('#acrit_exportproplus_option_php_path').val();
	if(phpPath.length) {
		acritExpAjax('check_php_path', 'php_path='+encodeURIComponent(phpPath), function(JsonResult, textStatus, jqXHR){
			if(JsonResult.Success){
				alert(JsonResult.Message);
			}
			else{
				alert(JsonResult.Message);
			}
		}, function(jqXHR){
			alert('Error!');
		}, false);
	}
});
$(document).delegate('tr#acrit_exp_option_multithreaded input[type=checkbox]', 'change', function(e){
	$('tr#acrit_exp_option_threads').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
	$('tr#acrit_exp_option_elements_per_thread_cron').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
	$('tr#acrit_exp_option_elements_per_thread_manual').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
});
$(document).delegate('tr#acrit_exp_option_discount_recalculation_enabled input[type=checkbox]', 'change', function(e){
	$('tr#acrit_exp_option_discount_recalculation_calc_value').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
	$('tr#acrit_exp_option_discount_recalculation_calc_discount').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
	$('tr#acrit_exp_option_discount_recalculation_calc_percent').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
	$('tr#acrit_exp_option_discount_recalculation_prices').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
});
$(document).ready(function(){
	acritExpOptionsHandleLogTextarea(true);
	$('tr#acrit_exp_option_multithreaded input[type=checkbox]').trigger('change');
	$('tr#acrit_exp_option_discount_recalculation_enabled input[type=checkbox]').trigger('change');
});