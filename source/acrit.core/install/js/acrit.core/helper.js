// http_build_query
function acritCoreHttpBuildQuery(url, params){
	var query = Object.keys(params)
   .map(function(k) {return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]);})
    .join('&');
	return url + (query.length ? (url.indexOf('?') == -1 ? '?' : '&') + query : '');
}

// POPUP: error text
var acritCorePopupError;
acritCorePopupError = new BX.CDialog({
	ID: 'acritCorePopupError',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 300,
	width: 800
});
acritCorePopupError.Open = function(error){
	this.SetTitle(BX.message('ACRIT_PROCESSING_POPUP_ERROR'));
	this.SetNavButtons();
	this.Show();
	this.LoadContent(error);
}
acritCorePopupError.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
acritCorePopupError.LoadContent = function(error){
	if(typeof error == 'object'){
		var jqXHR = error;
		error = jqXHR.responseText.replace(/<pre>/g, '<pre class="acritProcessing-error-text">');
		if(!error.length){
			error = '<pre class="acritProcessing-error-text">&lt;Empty response&gt;'+"\n\n"+'Status text: '+jqXHR.statusText+"\n\n"+jqXHR.getAllResponseHeaders()+'</pre>'
		}
	}
	this.SetContent(error);
}
acritCorePopupError.SetNavButtons = function(){
	var container = $(this.PARTS.BUTTONS_CONTAINER).html('');
	this.SetButtons(
		[{
			'name': BX.message('ACRIT_PROCESSING_POPUP_CLOSE'),
			'id': 'acritProcessing_profile_preview_cancel',
			'className': 'acritProcessing-button-right',
			'action': function(){
				this.parentWindow.Close();
			}
		}]
	);
	container.append('<div style="clear:both"/>');
}






