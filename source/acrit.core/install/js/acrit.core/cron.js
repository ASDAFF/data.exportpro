// *********************************************************************************************************************
// AJAX
// *********************************************************************************************************************
function acritCoreCronAjax(ajaxAction, post, callbackSuccess, callbackError, hideLoader){
	var lang = phpVars.LANGUAGE_ID,
		moduleId = $('input[data-role="acrt-core-cron-module-id"]').first().val(),
		profileId = $('input[data-role="acrt-core-cron-profile-id"]').first().val(),
		cliFile = $('input[data-role="acrt-core-cron-cli-file"]').first().val(),
		schedule = $('[data-role="acrit-core-cron-schedule"]').first().find('input[type=text]').get().map(function(input){
			var value = $.trim($(input).val());
			if(!value.length){
				value = '*';
			}
			return value;
		}).join(' '),
		show_tasks = $('input[data-role="acrt-core-cron-show-tasks"]').first().val() == 'Y' ? 'Y' : 'N';
	//
	if(typeof post != 'object'){
		post = {};
	}
	if(hideLoader!==true) {
		BX.showWait();
	}
	return $.ajax({
		url: acritCoreHttpBuildQuery('/bitrix/admin/acrit_core_cron.php', {
			module_id: moduleId,
			profile_id: profileId,
			cli_file: cliFile,
			schedule: schedule,
			show_tasks: show_tasks,
			ajax_action: ajaxAction,
			lang: lang
		}),
		type: 'POST',
		data: post,
		datatype: 'json',
		success: function(arJson, textStatus, jqXHR){
			if(typeof callbackSuccess == 'function') {
				callbackSuccess(jqXHR, textStatus, arJson);
			}
			if(arJson.DebugMessage){
				console.log(arJson.DebugMessage);
				alert(arJson.DebugMessage);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			if(jqXHR.statusText != 'abort') {
				console.error(errorThrown);
				console.error(textStatus);
				console.error(jqXHR);
				if(typeof callbackError == 'function') {
					callbackError(jqXHR, textStatus, errorThrown);
				}
				else{
					acritCorePopupError.Open(jqXHR);
				}
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		}
	});
}

// Status
function acritCoreCronSetStatus(configured){
	var statusDiv = $('div[data-cron-status][data-profile-id]');
	statusDiv.attr('data-cron-status', configured ? 'Y' : 'N');
	if(configured){
		$('[data-role="acrit-core-cron-clear"]').removeAttr('disabled');
	}
	else {
		$('[data-role="acrit-core-cron-clear"]').attr('disabled', 'disabled');
	}
	statusDiv.addClass('acrit-core-text-blink');
	clearTimeout(window.acritExpTimeoutCronStatus);
	window.acritExpTimeoutCronStatus = setTimeout(function(){
		statusDiv.removeClass('acrit-core-text-blink');
	}, 1000);
}

// Current tasks
function acritCoreCronSetCurrentTasks(html){
	$('[data-role="cron-current-tasks-wrapper"]').html(html);
}

// Examples
$(document).delegate('[data-role="acrit-core-cron-example"]', 'click', function(e){
	e.preventDefault();
	var schedule = $(this).data('schedule').split(' '),
		table = $(this).closest('table');
	$('input[name="minute"]').val(schedule[0]);
	$('input[name="hour"]').val(schedule[1]);
	$('input[name="day"]').val(schedule[2]);
	$('input[name="month"]').val(schedule[3]);
	$('input[name="weekday"]').val(schedule[4]);
});

// Current tasks
$(document).delegate('a[data-role="acrit-core-cron-current-tasks-toggle"]', 'click', function(e){
	e.preventDefault();
	var target = $(this).parent().parent().find('[data-role="acrit-core-cron-current-tasks"]');
	if(!target.is(':animated')){
		target.slideToggle(200);
	}
});

// Time
function acritCoreCronShowServerTime(){
	$('[data-role="acrit-core-server-time"]').each(function(){
		var block = $(this),
			formatRFC2822 = "ddd, DD MMM YYYY HH:mm:ss ZZ",
			offset,
			timeEn,
			timeRu,
			daysEn = moment.weekdaysShort(),
			daysRu = block.attr('data-days').split(','),
			monthsEn = moment.monthsShort(),
			monthsRu = block.attr('data-months').split(',');
		if(block.is(':visible')){
			timeEn = block.attr('data-date');
			offset = moment.parseZone(timeEn).utcOffset();
			timeEn = moment(timeEn).utcOffset(offset).locale('en').add(1,'second').format(formatRFC2822);
			timeRu = timeEn;
			for(var i in daysEn){
				timeRu = timeRu.replace(daysEn[i], daysRu[i]);
			}
			for(var i in monthsEn){
				timeRu = timeRu.replace(monthsEn[i], monthsRu[i]);
			}
			block.attr('data-date', timeEn).text(timeRu);
		}
	});
}
$(document).ready(function(){
	window.acritCoreServerTimeClock = setInterval(function(){
		acritCoreCronShowServerTime();
	}, 1000);
	acritCoreCronShowServerTime();
});

// Copy
$(document).delegate('a[data-role="acrit-core-cron-command-copy"]', 'click', function(e){
	e.preventDefault();
	var message = $(this).attr('data-message'),
		span = $(this).next('span'),
		command = $(this).prev(),
		timeout;
	acritCoreCopyToClipboard(command.get(0), function(){
		if(message.length) {
			span.html(message).show().addClass('acrit-core-text-blink');
			clearTimeout(timeout);
			timeout = setTimeout(function(){
				span.removeClass('acrit-core-text-blink');
				timeout = setTimeout(function(){
					span.fadeOut(200, function(){
						$(this).html('');
					});
				}, 3000);
			}, 1000);
		}
	});
});

// Setup
$(document).delegate('input[data-role="acrit-core-cron-setup"]', 'click', function(e){
	e.preventDefault();
	acritCoreCronAjax('setup', null, function(jqXHR, textStatus, arJson){
		acritCoreCronSetStatus(arJson.IsConfigured);
		acritCoreCronSetCurrentTasks(arJson.CurrentTasks);
	});
});

// Clear
$(document).delegate('input[data-role="acrit-core-cron-clear"]', 'click', function(e){
	e.preventDefault();
	acritCoreCronAjax('clear', null, function(jqXHR, textStatus, arJson){
		acritCoreCronSetStatus(arJson.IsConfigured);
		acritCoreCronSetCurrentTasks(arJson.CurrentTasks);
	});
});

// More info if cannot autoset
$(document).delegate('a[data-role="acrit-core-cron-more-toggle"]', 'click', function(e){
	e.preventDefault();
	var div = $(this).closest('tr').next().find('div').first();
	if(!div.is(':animated')){
		div.slideToggle();
	}
});
