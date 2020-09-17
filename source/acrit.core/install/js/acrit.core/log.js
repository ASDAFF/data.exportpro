window.acritCoreLogAjaxQuery = null;
function acritCoreLogAjax(ajaxAction, moduleId, profileId, callbackSuccess, callbackError){
	BX.showWait();
	if(window.acritCoreLogAjaxQuery){
		window.acritCoreLogAjaxQuery.abort();
	}
	window.acritCoreLogAjaxQuery = $.ajax({
		url: '/bitrix/admin/acrit_core_log.php?action='+ajaxAction+'&module='+moduleId+'&profile='+profileId,
		type: 'POST',
		data: {},
		datatype: 'json',
		success: function(jsonResult, textStatus, jqXHR){
			if(typeof callbackSuccess == 'function') {
				jqXHR._ajax_action = ajaxAction;
				callbackSuccess(jsonResult, textStatus, jqXHR);
			}
			BX.closeWait();
		},
		error: function(jqXHR, textStatus, errorThrown){
			if(!(jqXHR && jqXHR.statusText == 'abort')){
				jqXHR._ajax_action = ajaxAction;
				if(typeof callbackError == 'function') {
					callbackError(jqXHR, textStatus, errorThrown);
				}
			}
			BX.closeWait();
		}
	});
}
$(document).delegate('[data-role="log-wrapper"] [data-role^="log-"][data-ajax="Y"]', 'click', function(e){
	e.preventDefault();
	var action = $(this).attr('data-role').replace(/^log-(.*?)/g, '$1'),
		confirmText = $(this).attr('data-confirm'),
		wrapper = $(this).closest('[data-role="log-wrapper"]'),
		moduleId = wrapper.attr('data-module-id'),
		profileId = wrapper.attr('data-profile-id'),
		spanFullSize = $('[data-role="log-full-size"]', wrapper),
		spanMaxSize = $('[data-role="log-max-size"]', wrapper),
		textareaLog = $('textarea[data-role="log-content"]', wrapper),
		maySend = true;
	if(typeof confirmText == 'string' && !confirm(confirmText)){
		maySend = false;
	}
	if(maySend) {
		acritCoreLogAjax(action, moduleId, profileId, function(jsonResult, textStatus, jqXHR){
			if(jsonResult.Success){
				if(jsonResult.LogSize != undefined){
					spanFullSize.html(jsonResult.LogSize);
				}
				if(jsonResult.MaxSize != undefined){
					spanMaxSize.html(jsonResult.MaxSize);
				}
				if(typeof jsonResult.Log == 'string'){
					textareaLog.val(jsonResult.Log);
					textareaLog.trigger('_scroll_down_');
				}
			}
		}, function(jqXHR, textStatus, errorThrown){
			alert('Error!');
			console.log('Response: ' + jqXHR.responseText);
			console.log(jqXHR);
		});
	}
});
$(document).delegate('#tab_cont_log', 'click', function(){
	$('textarea[data-role="log-content"]').trigger('_scroll_down_');
});
$(document).ready(function(){
	$('textarea[data-role="log-content"]').bind('_scroll_down_', function(){
		$(this).scrollTop(1000000);
	}).trigger('_scroll_down_');
});
