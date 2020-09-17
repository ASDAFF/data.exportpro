if(!window.zakupkiMosRuYmlPluginInitialized){
	//
	/*
	$(document).delegate('input[name="PROFILE[DOMAIN]"], input[name="PROFILE[IS_HTTPS]"]', 'change', function(e){
		var
			opener = $('a[data-role="yandex-turbo-add-feed"]'),
			href = opener.attr('data-href'),
			domain = $('input[name="PROFILE[DOMAIN]"]').val().trim(),
			ssl = $('input[name="PROFILE[IS_HTTPS]"]').is(':checked') ? true : false;
		if(opener.length){
			href = href
				.replace(/#SCHEME#/g, ssl ? 'https' : 'http')
				.replace(/#DOMAIN#/g, domain)
				.replace(/#PORT#/g, ssl ? '443' : '80');
			opener.attr('href', href);
		}
	});
	*/
	//
	window.zakupkiMosRuYmlPluginInitialized = true;
}

$(document).ready(function(){
	$('input[name="PROFILE[DOMAIN]"]').trigger('change');
});
setTimeout(function(){
	$('input[name="PROFILE[DOMAIN]"]').trigger('change');
},500);