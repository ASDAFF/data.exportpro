if(!window.customXmlPluginInitialized){
	//
	jQuery.fn.extend({insertAtCaret:function(a){return this.each(function(b){if(document.selection)this.focus(),sel=document.selection.createRange(),sel.text=a,this.focus();else if(this.selectionStart||"0"==this.selectionStart){b=this.selectionStart;var c=this.selectionEnd,d=this.scrollTop;this.value=this.value.substring(0,b)+a+this.value.substring(c,this.value.length);this.focus();this.selectionStart=b+a.length;this.selectionEnd=b+a.length;this.scrollTop=d}else this.value+=a,this.focus()})}});
	// Check XML valid link
	$(document).delegate('a[data-role="custom-xml-check-valid"]', 'click', function(e){
		e.preventDefault();
		var xml = $(this).closest('[data-role="xml-structure-wrapper"]').find('textarea').val();
		acritExpAjax(['plugin_ajax_action','check_xml_valid'], 'xml='+xml, function(JsonResult, textStatus, jqXHR){
			if(JsonResult.Message!=undefined && JsonResult.Message.length){
				alert(JsonResult.Message);
			}
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, true);
	});
	// XML macros
	$(document).delegate('a[data-role="xml-macro-link"]', 'click', function(e){
		e.preventDefault();
		var macro = $(this).attr('data-macro'),
			textarea = $(this).closest('[data-role="xml-structure-wrapper"]').find('textarea').first();
		textarea.insertAtCaret(macro);
	});
	// Date format
	$(document).delegate('select[data-role="xml-format-date"]', 'change', function(e){
		var input = $(this).next('input');
		input.hide();
		if($(this).val() == '.other'){
			input.show();
		}
	});
	//
	window.customXmlPluginInitialized = true;
}

$(document).ready(function(){
	$('[data-role="xml-structure-wrapper"]').each(function(){
		$('.adm-info-message', this).each(function(){
			var html = $(this).html();
			html = html.replace(/(#.*?#)/g, '<a href="#" class="acrit-exp-custom-xml-macro-link" data-role="xml-macro-link" data-macro="$1">$1</a>');
			$(this).html(html);
		});
	});
	$('select[data-role="xml-format-date"]').trigger('change');
});

