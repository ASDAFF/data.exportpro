// Refresh preview
$.alt('R', function() {
	if(AcritExpPopupPreview.isOpen){
		$('#acrit-exp-preview-refresh').trigger('click');
	}
	else{
		if($('#acrit-exp-element-preview-button').length){
			$('#acrit-exp-element-preview-button').trigger('click');
		}
		else{
			var dropdown = $('a[onclick*="AcritExpPopupPreview.Open"]')[0];
			if(dropdown.OPENER){
				dropdown.OPENER.Open();
			}
			else{
				$(dropdown).trigger('click');
			}
		}
	}
});
//
function acritExpPreviewProfileSelectTemplate(state) {
	if (!state.id) {
		return state.text;
	}
	var html = $('<span class="select2-menuicon"><img src="'+$(state.element).attr('data-icon')+'" /> <span>' + state.text + '</span></span>');
	return html;
};
//
var AcritExpPopupPreview = new BX.CDialog({
	ID: 'AcritExpPopupPreview',
	resizable: true,
	draggable: true,
	height: $(window).height() - 123 - 60,
	width: $(window).width() - 26 - 60,
});
AcritExpPopupPreview.Open = function(strModuleId, elementId, profileId){
	this.strModuleId = strModuleId;
	this.elementId = elementId;
	// Positioning
	if(this.bExpanded){
		this.__expand();
	}
	var thisPopup = this;
	setTimeout(function(){
		var divPopup = $(thisPopup.DIV),
			divContent = $(thisPopup.PARTS.CONTENT_DATA),
			widthDelta = divPopup.width() - divContent.width(),
			heightDelta = divPopup.height() - divContent.height(),
			margin = 15,
			newWidth = $(window).width() - widthDelta - margin,
			newHeight = $(window).height() - heightDelta - margin;
		divContent.width(newWidth).height(newHeight);
		divPopup.css({left: ($(window).width() - divPopup.width()) / 2, top: ($(window).height() - divPopup.height() + margin) / 2});
	}, 1);
	//
	// $('select', this.PARTS.BUTTONS_CONTAINER).val('').select2(window.acritExpProfileSelectConfig);
	//
	this.SetTitle(BX.message('ACRIT_EXP_EVENT_HANDLER_PREVIEW_TITLE') + ' (' + this.strModuleId + ')');
	this.Show();
	this.LoadContent(strModuleId, false, profileId, true);
}
AcritExpPopupPreview.LoadContent = function(strModuleId, saveContent, profileId, initial){
	var thisPopup = this;
	if(!saveContent) {
		thisPopup.SetContent(BX.message('ACRIT_EXP_EVENT_HANDLER_PREVIEW_LOADING'));
	}
	thisPopup.profileId = profileId;
	// Set popup buttons
	thisPopup.SetNavButtons();
	//
	/*
	if(!profileId && window.acritExpPreviewProfileId && window.acritExpPreviewProfileId[thisPopup.strModuleId]){
		profileId = window.acritExpPreviewProfileId[thisPopup.strModuleId];
	}
	*/
	//
	BX.showWait();
	$.ajax({
		url: '/bitrix/admin/acrit_core_export_preview.php?module='+strModuleId+'&ID='+this.elementId+'&lang='+phpVars.LANGUAGE_ID,
		type: 'GET',
		data: {
			'profile_id': profileId > 0 ? profileId : '',
			'initial': initial ? 'Y' : 'N'
		},
		success: function(HTML){
			$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(HTML);
			$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
					 '-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
			$('input[type=checkbox]', thisPopup.PARTS.CONTENT).not('.no-checkbox-styling').each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
			// Append profile selector
			thisPopup.SetProfileSelect(initial);
			//
			BX.closeWait();
		},
		error: function(jqXHR, textStatus, errorThrown){
			BX.closeWait();
		}
	});
}
AcritExpPopupPreview.SetNavButtons = function(){
	$('input[type=button]', this.PARTS.BUTTONS_CONTAINER).remove();
	this.SetButtons(
		[{
			'name': BX.message('ACRIT_EXP_EVENT_HANDLER_PREVIEW_REFRESH'),
			'className': 'adm-btn-green',
			'id': 'acrit-exp-preview-refresh',
			'action': function(){
				var thisPopup = this.parentWindow,
					selectProfile = $('select', thisPopup.PARTS.BUTTONS_CONTAINER),
					profileId = null;
				if(selectProfile.length){
					profileId = selectProfile.val();
				}
				else if(thisPopup.profileId > 0){
					profileId = thisPopup.profileId;
				}
				thisPopup.LoadContent(thisPopup.strModuleId, true, profileId);
			}
		}, {
			'name': BX.message('ACRIT_EXP_EVENT_HANDLER_PREVIEW_CLOSE'),
			'id': 'acrit-exp-preview-close',
			'action': function(){
				this.parentWindow.Close();
			}
		}]
	)
}
AcritExpPopupPreview.SetProfileSelect = function(initial){
	var thisPopup = this,
		container = $(this.PARTS.BUTTONS_CONTAINER),
		selectWrapper = $('.acrit-exp-preview-select', this.PARTS.CONTENT_DATA);
	$('select, span', this.PARTS.BUTTONS_CONTAINER).remove();
	container.append(selectWrapper.html());
	//
	window.acritExpProfileSelectConfig = {
		templateResult: acritExpPreviewProfileSelectTemplate,
		templateSelection: acritExpPreviewProfileSelectTemplate,
		//dropdownParent: $('.acrit-exp-preview').first(),
		dropdownPosition: 'above',
		language: phpVars.LANGUAGE_ID
	}
	//
	var select = $('select', container).bind('change', function(e){
		var profileId = $(this).val();
		//thisPopup.profileId = profileId;
		thisPopup.LoadContent(thisPopup.strModuleId, true, $(this).val());
	});
	select.select2($.extend(window.acritExpProfileSelectConfig, {
		dropdownParent: select.parent()
	}));
	if(initial){
		select.parent().addClass('acrit-exp-text-blink');
		setTimeout(function(){
			select.parent().removeClass('acrit-exp-text-blink');
		}, 750);
	}
}