if(!window.yandexTurboRssPluginInitialized){
	//
	$(document).delegate('#row_YANDEX_TURBO_SHOW_BUTTON input[type=checkbox]', 'change', function(e){
		if($(this).is(':checked')){
			$('.row_YANDEX_TURBO_BUTTON_SETTINGS').show();
		}
		else {
			$('.row_YANDEX_TURBO_BUTTON_SETTINGS').hide();
		}
	});
	//
	$(document).delegate('input[type=button][data-role="yandex-turbo-select-all"]', 'click', function(e){
		var structure = $('div[data-role="yandex-turbo-structure"]');
		$('input[type=checkbox]', structure).prop('checked', true);
	});
	$(document).delegate('input[type=button][data-role="yandex-turbo-select-nothing"]', 'click', function(e){
		var structure = $('div[data-role="yandex-turbo-structure"]');
		$('input[type=checkbox]', structure).prop('checked', false);
	});
	$(document).delegate('input[type=button][data-role="yandex-turbo-select-root"]', 'click', function(e){
		var structure = $('div[data-role="yandex-turbo-structure"]');
		$('input[type=checkbox]', structure).prop('checked', true);
		$('ul ul ul input[type=checkbox]', structure).prop('checked', false);
	});
	//
	$(document).delegate('.row_YANDEX_TURBO_SHARE table input[type=checkbox]', 'change', function(e){
		var input = $(this).closest('tr').find('input[type=text]');
		if($(this).is(':checked')){
			input.css('visibility', 'visible');
		}
		else {
			input.css('visibility', 'hidden');
		}
	})
	//
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
	//
	window.yandexTurboRssPluginInitialized = true;
}

$(document).ready(function(){
	$('#row_YANDEX_TURBO_SHOW_BUTTON input[type=checkbox]').trigger('change');
	$('.row_YANDEX_TURBO_SHARE table input[type=checkbox]').trigger('change');
});

setTimeout(function(){
	$('#row_YANDEX_TURBO_SHOW_BUTTON input[type=checkbox]').trigger('change');
	$('.row_YANDEX_TURBO_SHARE table input[type=checkbox]').trigger('change');
},500);


$(document).ready(function(){
	$('input[name="PROFILE[DOMAIN]"]').trigger('change');
});
setTimeout(function(){
	$('input[name="PROFILE[DOMAIN]"]').trigger('change');
},500);