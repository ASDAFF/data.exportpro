(function($) {
	
	/* Filter */
	$.fn.acritFilter = function(options){
		
		// Vars
		var divContainer = $(this),
			ID = divContainer.attr('id');
		
		// 1. PREPARE
		
		// Defaults
		var defaults = {
			inputName: 'filter',
			confirmDeleteItem: false,
			confirmDeleteGroup: false,
			formData: {},
			tpl:{
				item:
					'<div data-role="item">' +
						'<span data-role="name"></span> ' +
						'<span data-role="field">' +
							'<a href="#" data-role="select-field" data-entity="field" data-default="' + options.lang.selectField + '">' + options.lang.selectField + '</a>' +
						'</span> ' +
						'<span data-role="type"></span> ' +
						'<span data-role="logic">' +
							'<a href="#" data-role="select-logic" data-entity="logic" data-default="' + options.lang.selectLogic + '">' + options.lang.selectLogic + '</a>' +
						'</span> ' +
						'<span data-role="value">' +
							'<a href="#" data-role="select-value" data-entity="value" data-default="' + options.lang.selectValue + '">' + options.lang.selectValue + '</a>' +
						'</span> ' +
						'<span data-role="additional"></span>' +
						'<a href="#" data-role="delete-item">&times;</a>' +
					'</div>',
				group:
					'<div data-role="group">' +
						'<div data-role="group-logic">' +
							'<select data-role="group-aggregator-type">' +
								'<option value="ALL">' + options.lang.aggregatorAll + '</option>' +
								'<option value="ANY">' + options.lang.aggregatorAny + '</option>' +
							'</select> ' +
							//'<select data-role="group-aggregator-value">' +
							//	'<option value="Y">' + options.lang.aggregatorY + '</option>' +
							//	'<option value="N">' + options.lang.aggregatorN + '</option>' +
							//'</select>' +
						'</div>' +
						'<div data-role="group-controls">' +
							'<a href="#" data-role="add-item">' + options.lang.addItem + '</a> ' +
							'<a href="#" data-role="add-group">' + options.lang.addGroup + '</a> ' +
							'<a href="#" data-role="delete-group">&times;</a>' +
						'</div>' +
						'<div data-role="group-items">' +
							'' +
						'</div>' +
					'</div>'
			},
			field: null,
			callbackClickEntity: null
		};
		
		// Options
		options = $.extend({}, defaults, options);
		
		// Correct input data
		if(ID == undefined || !ID.length) {
			ID = 'filter_' + $.trim(Math.random()).substr(2);
			divContainer.attr('id', ID);
		}
		
		// 2. METHODS
		
		// Set content for input[type=hidden]
		divContainer.buildJsonResult = function(){
			var //inputJson = divContainer.find('[data-role="json-result"]'),
				jsonItems = divContainer.compileJson(),
				jsonText = JSON.stringify(jsonItems);
			//inputJson.val(jsonText);
			if(options.field && $(options.field).length){
				$(options.field).val(jsonText);
			}
		}
		divContainer.bind('acrit:buildJsonResult', function(){ // like public method
			divContainer.buildJsonResult();
		});
		
		divContainer.compileJson = function(result, parentObject){
			var initial = result == undefined;
			result = initial ? [] : result;
			parentObject = initial ? divContainer : parentObject;
			parentObject.children('[data-role="group"],[data-role="item"]').each(function(){
				var isGroup = $(this).is('[data-role="group"]'),
					isItem = $(this).is('[data-role="item"]');
				if(isGroup){
					var groupLogic = $(this).children('[data-role="group-logic"]');
					result.push({
						'type': 'group',
						'aggregatorType': groupLogic.children('[data-role="group-aggregator-type"]').val(),
						//'aggregatorValue': groupLogic.children('[data-role="group-aggregator-value"]').val(),
						'items': []
					});
					divContainer.compileJson(result[result.length-1]['items'], $(this).children('[data-role="group-items"]'));
				}
				else if(isItem){
					var selectField = $(this).find('[data-role="select-field"]'),
						selectLogic = $(this).find('[data-role="select-logic"]'),
						selectValue = $(this).find('[data-role="select-value"]');
					var subJson = {
						'type': 'item',
						'iblockType': selectField.closest('[data-role="item"]').attr('data-iblock-type'),
						'field': {
							'name': selectField.attr('data-name'),
							'value': selectField.attr('data-code'),
						},
						'logic': {
							'name': selectLogic.attr('data-name'),
							'value': selectLogic.attr('data-code'),
							'hide': selectLogic.attr('data-hide-value')=='Y' ? 'Y' : 'N',
						},
						'value': {
							'name': selectValue.attr('data-name'),
							'value': selectValue.attr('data-code'),
						}
					};
					result.push(subJson);
				}
			});
			//if(initial) {
			//	result = result[0];
			//}
			return result;
		};
		
		//
		divContainer.loadFromField = function(){
			var loaded = false;
			if(options.field && $(options.field).length){
				var field = $(options.field),
					jsonText = field.val();
				if(jsonText.length) {
					try {
						json = JSON.parse(jsonText);
						if(typeof json == 'object'){
							divContainer.build(json);
							loaded = true;
						}
					}
					catch (e) {
						console.log('Error while parsing json:');
						console.log(e);
					}
				}
			}
			if(!loaded){
				// Create root div group
				divContainer.addGroup();
			}
		};
		
		//
		divContainer.build = function(json, parentObject){
			if(typeof json == 'object'){
				for(var i in json) {
					if(json[i].type=='group') {
						group = divContainer.addGroup(parentObject, json[i]);
						divContainer.build(json[i].items, group.children('[data-role="group-items"]'));
					}
					else if(json[i].type=='item'){
						item = divContainer.addItem(parentObject, json[i]);
					}
				}
			}
		}
		
		// Add item
		// fields = {"type":"item","field":{"name":"!!!Наши предложения [291, OUR_OFFERS, L]","value":"PROPERTY_OUR_OFFERS"},"logic":{"name":"в списке","value":"IN_LIST"},"value":{"name":"Спецпредложение, Лидер продаж, Новинка","value":"53#|#54#|#55"}}
		divContainer.addItem = function(parentObject, fields){
			var newItem = $(options.tpl.item);
			if(parentObject == undefined) {
				parentObject = this;
			}
			parentObject.append(newItem);
			if(typeof fields == 'object') {
				divContainer.loadItemData(newItem, fields);
			}
			divContainer.buildJsonResult();
			return newItem;
		}
		
		// Load item
		divContainer.loadItemData = function(item, fields){
			var selectField = $('[data-role="select-field"]', item),
				selectLogic = $('[data-role="select-logic"]', item),
				selectValue = $('[data-role="select-value"]', item);
			fields.field.name = fields.field.name && (fields.field.name.length) ? fields.field.name : selectField.attr('data-default');
			fields.logic.name = fields.logic.name && (fields.logic.name.length) ? fields.logic.name : selectLogic.attr('data-default');
			fields.value.name = (fields.value.name != undefined) && (fields.value.name.length || (typeof fields.value.name == 'number')) ? fields.value.name : selectValue.attr('data-default');
			item.attr('data-iblock-type', fields.iblockType);
			selectField
				.text(fields.field.name)
				.attr('data-name', fields.field.name)
				.attr('data-code', fields.field.value);
			selectLogic
				.text(fields.logic.name)
				.attr('data-name', fields.logic.name)
				.attr('data-code', fields.logic.value);
			selectValue
				.text(fields.value.name)
				.attr('data-name', fields.value.name)
				.attr('data-code', fields.value.value);
			if(fields.logic.hide=='Y'){
				selectLogic.attr('data-hide-value', 'Y');
				selectValue.parent().hide();
			}
		}
		
		// Add group
		// fields = "{"type":"group","aggregatorType":"ALL","aggregatorValue":"Y","items":[...]}"
		divContainer.addGroup = function(parentObject, fields){
			var newGroup = $(options.tpl.group);
			if(!parentObject) {
				parentObject = this;
				newGroup.find('[data-role="delete-group"]').remove();
			}
			parentObject.append(newGroup);
			if(typeof fields == 'object') {
				divContainer.loadGroupData(newGroup, fields);
			}
			divContainer.buildJsonResult();
			return newGroup;
		}
		
		// Load groupdata
		divContainer.loadGroupData = function(group, fields){
			var groupLogic = group.children('[data-role="group-logic"]');
			groupLogic.children('[data-role="group-aggregator-type"]').val(fields.aggregatorType);
			//groupLogic.children('[data-role="group-aggregator-value"]').val(fields.aggregatorValue);
		}
		
		// Delete item
		divContainer.deleteItem = function(item){
			if(!(options.confirmDeleteItem && !confirm(options.lang.deleteItemConfirm))) {
				item.remove();
				divContainer.buildJsonResult();
			}
		}
		
		// Delete group
		divContainer.deleteGroup = function(group){
			if(!(options.confirmDeleteGroup && !confirm(options.lang.deleteGroupConfirm))) {
				if(!group.parent().is(divContainer)) {
					group.remove();
					divContainer.buildJsonResult();
				}
			}
		}
		
		// 3. INITIAL ACTIONS
		divContainer.html('');
		
		// 3.1 Create input[type=hidden] with config
		var jsonParams = $('<input>').attr({
			'type': 'hidden',
			'value': JSON.stringify(options.formData),
			'data-role': 'params'
		});
		divContainer.append(jsonParams);
		
		// 3.2 Create input[type=hidden] for result json
		/*
		var jsonResult = $('<input>').attr({
			'type': 'hidden',
			'name': options.inputName,
			'value': '',
			'data-role': 'json-result'
		});
		divContainer.append(jsonResult);
		*/
		
		// Load (or initial action)
		divContainer.loadFromField();
		
		// 4. EVENT HANDLERS
		
		// Change aggregator type
		$(document).delegate('#' + ID + ' [data-role="group-aggregator-type"]', 'change', function(e){
			e.preventDefault();
			divContainer.buildJsonResult();
		});
		
		// Change aggregator value
		/*
		$(document).delegate('#' + ID + ' [data-role="group-aggregator-value"]', 'change', function(e){
			e.preventDefault();
			divContainer.buildJsonResult();
		});
		*/
		
		// Add item
		$(document).delegate('#' + ID + ' [data-role="add-item"]', 'click', function(e){
			e.preventDefault();
			var parentObject = $(this).closest('[data-role="group"]').children('[data-role="group-items"]');
			divContainer.addItem(parentObject);
		});
		
		// Add group
		$(document).delegate('#' + ID + ' [data-role="add-group"]', 'click', function(e){
			e.preventDefault();
			var parentObject = $(this).closest('[data-role="group"]').children('[data-role="group-items"]');
			divContainer.addGroup(parentObject);
		});
		
		// Delete item
		$(document).delegate('#' + ID + ' [data-role="delete-item"]', 'click', function(e){
			e.preventDefault();
			var item = $(this).closest('[data-role="item"]');
			divContainer.deleteItem(item);
		});
		
		// Delete group
		$(document).delegate('#' + ID + ' [data-role="delete-group"]', 'click', function(e){
			e.preventDefault();
			var group = $(this).closest('[data-role="group"]');
			divContainer.deleteGroup(group);
		});
		
		// Click on entity
		$(document).delegate('#' + ID + ' a[data-entity]', 'click', function(e){
			e.preventDefault();
			if(typeof options.callbackClickEntity == 'function') {
				options.callbackClickEntity(e, this, options);
			}
		});
		
		// Select field
		//$(document).delegate('#' + ID + ' [data-role="select-field"]', 'click', function(e){
		//	e.preventDefault();
		//	
		//});
		
		// return this
		return this;
	};
	
})(jQuery);

// Custom function
function AcritExpConditionsPopupCallbackClickEntity(e, sender, options){
	e.preventDefault();
	//
	var divFilter = $(sender).closest('[data-role="filter"]'),
		iblockId = divFilter.attr('data-iblock-id'),
		iblockType = $(sender).closest('[data-role="item"]').attr('data-iblock-type')=='offers' ? 'offers' : 'main',
		entity = $(sender).attr('data-entity'),
		item = $(sender).closest('[data-role="item"]'),
		currentField = $('[data-entity="field"]', item).attr('data-code'),
		currentLogic = $('[data-entity="logic"]', item).attr('data-code'),
		currentValue = $('[data-entity="value"]', item).attr('data-code'),
		currentValueTitle = $('[data-entity="value"]', item).attr('data-name');
	currentField = currentField == undefined ? '' : currentField;
	currentLogic = currentLogic == undefined ? '' : currentLogic;
	currentValue = currentValue == undefined ? '' : currentValue;
	currentValueTitle = currentValueTitle == undefined ? '' : currentValueTitle;
	AcritExpConditionsPopup.SetCurrentFilter(divFilter);
	AcritExpConditionsPopup.OnSelectField = function(thisPopup, openerLink, selectedOption, inputValue, iblockType){
		if(iblockType == undefined) {
			iblockType = 'main';
		}
		if(selectedOption && selectedOption.length) {
			var optionName = selectedOption.attr('data-name') != undefined ? selectedOption.attr('data-name') : selectedOption.text();
			$(openerLink)
				.text(optionName)
				.attr('data-name', optionName)
				.attr('data-code', selectedOption.val());
			if(entity=='logic'){
				var isValueHidden = selectedOption.attr('data-hide-value')=='Y';
				if(isValueHidden) {
					$('[data-role="value"]', item).hide();
					$(openerLink).attr('data-hide-value', 'Y');
				}
				else {
					$('[data-role="value"]', item).show();
					$(openerLink).attr('data-hide-value', 'N');
				}
			}
			else if (entity=='field'){
				// set iblock type
				$(openerLink).closest('[data-role="item"]').attr('data-iblock-type', iblockType);
				// clear logic
				var logicLink = $('[data-role="select-logic"]', item);
				logicLink.attr('data-name','').attr('data-code','').attr('data-hide-value','').text(logicLink.attr('data-default'));
				if(selectedOption.length && selectedOption.attr('data-logic-code') != undefined){
					logicLink.attr('data-code', selectedOption.attr('data-logic-code'));
					logicLink.attr('data-name', selectedOption.attr('data-logic-name'));
					logicLink.attr('data-hide-value', selectedOption.attr('data-logic-hide-value'));
					logicLink.text(selectedOption.attr('data-logic-name'));
				}
				// clear value
				var valueLink = $('[data-role="select-value"]', item);
				valueLink.attr('data-name','').attr('data-code','').text(valueLink.attr('data-default'));
				if(selectedOption.length && selectedOption.attr('data-logic-hide-value') == 'Y'){
					valueLink.hide();
				}
				else{
					valueLink.show();
				}
			}
		}
		else if (inputValue && inputValue.length){
			if (inputValue.prop('tagName').toUpperCase()=='SELECT') {
				var select = $('.acrit-exp-field-select-list', AcritExpConditionsPopup.DIV);
					items = $('option:selected', select).not('[value=""]'),
					text = items.get().map(function(option){return $(option).text()}).join(', '),
					value = items.get().map(function(option){return $(option).val()})
						.join(BX.message('ACRIT_EXP_CONDITIONS_VALUE_SEPARATOR')),
					justId = select.attr('data-just-id') == 'Y';
				if(justId){
					text = items.get().map(function(option){return $(option).val()}).join(', ');
				}
				if(text.length) {
					$(openerLink)
						.text(text)
						.attr('data-name', text)
						.attr('data-code', value);
				}
			}
			else if (inputValue.prop('tagName').toUpperCase()=='INPUT') {
				if(inputValue.prop('type').toUpperCase()=='TEXT' || inputValue.prop('type').toUpperCase()=='HIDDEN'){
					var value,
						text;
					if(inputValue.length == 1){
						value = inputValue.val();
						text = inputValue.attr('data-text');
						if(text == undefined || !text.length){
							text = value;
						}
					}
					else if (inputValue.length > 1){
						value = inputValue.get().map(function(input){return $(input).val()})
							.join(BX.message('ACRIT_EXP_CONDITIONS_VALUE_SEPARATOR'));
						text = inputValue.get().map(function(input){return $(input).val()})
							.join(', ');
					}
					$(openerLink)
						.text(text)
						.attr('data-name', text)
						.attr('data-code', value);
				}
			}
		}
		// rebuild
		thisPopup.divFilter.trigger('acrit:buildJsonResult');
	}
	AcritExpConditionsPopup.Open(sender, iblockId, iblockType, entity, currentField, currentLogic, currentValue, currentValueTitle);
}

/**
 *	POPUP: select field
 */
var AcritExpConditionsPopup;
AcritExpConditionsPopup = new BX.CDialog({
	ID: 'AcritExpConditionsPopup',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 400,
	width: 800
});
AcritExpConditionsPopup.OnSelectField = null;
AcritExpConditionsPopup.SetCurrentFilter = function(divFilter){
	this.divFilter = divFilter;
}
AcritExpConditionsPopup.Open = function(sender, iblockId, iblockType, entity, currentField, currentLogic, currentValue, currentValueTitle){
	this.openerLink = sender;
	this.iblockId = iblockId;
	this.iblockType = iblockType; // 'main' || 'offers'
	this.entity = entity;
	this.currentField = currentField;
	this.currentLogic = currentLogic;
	this.currentValue = currentValue;
	this.currentValueTitle = currentValueTitle;
	//
	this.SetTitle(BX.message('ACRIT_EXP_CONDITIONS_POPUP_SELECT_'+entity.toUpperCase()));
	this.SetContent(BX.message('ACRIT_EXP_CONDITIONS_POPUP_LOADING'));
	this.SetNavButtons(true);
	this.Show();
	this.LoadContent();
}
AcritExpConditionsPopup.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
AcritExpConditionsPopup.FilterFields = function(){
	var fieldText = $('[data-role="entity-select-search"]:visible', this.DIV),
		fieldList = $('[data-role="entity-select-item"]:visible', this.DIV),
		searchText = $.trim(fieldText.val()).toLowerCase()
		grouped = $('optgroup', fieldList).length > 0;
	if(searchText=='') {
		if(grouped) {
			$('optgroup', fieldList).show();
		}
		$('option', fieldList).not('[data-role="entity-select-item-not-found"]').show();
		fieldList.children('option').filter('[data-role="entity-select-item-not-found"]').hide();
	}
	else {
		var found = false;
		fieldList.children('option').not('[data-role="entity-select-item-not-found"]').show();
		if(grouped) {
			$('optgroup', fieldList).hide();
		}
		$('option', fieldList).not('[data-role="entity-select-item-not-found"]').hide().each(function(){
			var search = $(this).val().toLowerCase() + ' ' + $.trim($(this).text()).toLowerCase();
			var matched = search.indexOf(searchText) > -1;
			if(matched){
				$(this).show();
				if(grouped){
					$(this).closest('optgroup').show();
				}
				found = true;
			}
		});
		if(found) {
			fieldList.children('option').filter('[data-role="entity-select-item-not-found"]').hide();
		}
		else {
			fieldList.find('option:selected').removeAttr('selected');
		}
	}
}
AcritExpConditionsPopup.LoadContent = function(){
	var thisPopup = this,
		ajaxData = 'iblock_id='+this.iblockId+'&iblock_type='+this.iblockType+'&entity='+this.entity
			+'&current_field='+this.currentField+'&current_logic='+this.currentLogic
			+'&current_value='+encodeURIComponent(this.currentValue)
			+'&current_value_title='+encodeURIComponent(this.currentValueTitle);
	//
	acritExpAjax('load_popup_conditions', ajaxData, function(JsonResult){
		$(thisPopup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(JsonResult.HTML);
		var isListWithSearch = $('[data-role="entity-select-item"]', thisPopup.DIV).length>0;
		if(isListWithSearch){
			$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
					 '-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
		}
		thisPopup.FilterFields();
		$('[data-role="entity-select-search"]', thisPopup.DIV).bind('textchange', function(){
			thisPopup.FilterFields();
		});
		$('[data-role="entity-select-item"],[data-role="entity-select-value"],[data-role="entity-select-value-pseudo"]', thisPopup.DIV).dblclick(function(){
			if($(this).is('select') && $('option:selected', this).length) {
				$('#acrit_exp_conditions_save').trigger('click');
			}
		}).keydown(function(e){
			if(e.keyCode==13) {
				$('#acrit_exp_conditions_save').trigger('click');
			}
		});
		if($('[data-role="allow-save"]', thisPopup.DIV).length) {
			thisPopup.SetNavButtons();
		}
		$('[data-role="entity-select-type"]', thisPopup.DIV).trigger('change');
		$('[data-role="entity-select-item"],[data-role="entity-select-value"]', thisPopup.DIV).first().focus();
	}, function(jqXHR){
		//$('#acrit_exp_form #field_IBLOCK_content').html(jqXHR.responseText);
	}, true);
}
AcritExpConditionsPopup.SetNavButtons = function(empty){
	$(AcritExpConditionsPopup.PARTS.BUTTONS_CONTAINER).html('');
	if(!empty) {
		AcritExpConditionsPopup.SetButtons(
			[{
				'name': BX.message('ACRIT_EXP_CONDITIONS_POPUP_SAVE'),
				'title': '',
				'id': 'acrit_exp_conditions_save',
				'className': 'adm-btn-green',
				'action': function(){
					var selected = false;
					if(typeof AcritExpConditionsPopup.OnSelectField == 'function'){
						var thisPopup = this.parentWindow,
							selectedOption = $('[data-role="entity-select-item"]:visible option:selected', thisPopup.DIV),
							inputValue = $('[data-role="entity-select-value"]:visible', thisPopup.DIV),
							inputHidden = $('[data-role="entity-select-value-hidden"]', thisPopup.DIV),
							iblockType = $('[data-role="entity-select-type"],[data-role="entity-select-type-hidden"]', thisPopup.DIV)
								.first().val();
						if(selectedOption.length) {
							thisPopup.OnSelectField(thisPopup, thisPopup.openerLink, selectedOption, null, iblockType);
							selected = true;
						}
						else if (inputHidden.length){
							thisPopup.OnSelectField(thisPopup, thisPopup.openerLink, null, inputHidden, iblockType);
							selected = true;
						}
						else if (inputValue.length){
							thisPopup.OnSelectField(thisPopup, thisPopup.openerLink, null, inputValue, iblockType);
							selected = true;
						}
						if(selected){
							thisPopup.Close();
						}
					}
				}
			}, {
				'name': BX.message('ACRIT_EXP_CONDITIONS_POPUP_CANCEL'),
				'title': '',
				'id': 'acrit_exp_conditions_cancel',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		);
	}
}

/**
 *	Field select for filter
 */
$(document).delegate('.acrit-exp-field-select-table select[data-role="entity-select-type"]', 'change', function(){
	var table = $(this).closest('.acrit-exp-field-select-table'),
		listMain = $('[data-role="entity-select-item"][data-type="main"]', table),
		listOffers = $('[data-role="entity-select-item"][data-type="offers"]', table);
	listMain.hide();
	listOffers.hide();
	if($(this).val()=='offers'){
		listOffers.show();
	}
	else {
		listMain.show();
	}
	$('[data-role="entity-select-search"]', table).trigger('textchange');
});

/**
 *	Select elements ID from list
 */
$(document).delegate('[data-role="select-element-id"]', 'click', function(e){
	e.preventDefault();
	var table = $('[data-role="table-select-elements-id"]'),
		index = parseInt(table.attr('data-index')) + 1,
		newRow = $('tbody > tr', table).first().clone(),
		inputText = $('input[type=text]', newRow).val(''),
		inputButton = $('input[type=button]', newRow),
		textSpan = $('span', newRow).text('');
	inputText.attr('id', inputText.attr('id').replace(/\[n0\]/, '[n'+index+']'));
	inputButton.attr('onclick', inputButton.attr('onclick').replace(/&k=n0/, '&k=n'+index));
	textSpan.attr('id', textSpan.attr('id').replace(/_n0/, '_n'+index));
	newRow.appendTo($('tbody', table));
	table.attr('data-index', index);
});
$(document).delegate('[data-role="table-select-elements-id"] input[type=text]', 'change', function(e){
	var data = [];
	$('[data-role="table-select-elements-id"] input[type=text]').each(function(){
		if($.trim($(this).val()) != ''){
			data.push($.trim($(this).val()));
		}
	});
	$('[data-role="entity-select-value-hidden"]').val(data.join(','));
});

/**
 *	Select elements ID from list
 */
$(document).delegate('input[data-role="entity-select-value-multiple-add"]', 'click', function(e){
	var table = $('table[data-role="entity-select-value-multiple"]'),
		tbody = table.children('tbody'),
		row = tbody.children('tr').first(),
		newRow = row.clone().appendTo(tbody);
	newRow.find('input[type=text]').val('').keydown(function(e){
		if(e.keyCode==13) {
			$('#acrit_exp_conditions_save').trigger('click');
		}
	});
});
$(document).delegate('a[data-role="entity-select-value-multiple-delete"]', 'click', function(e){
	e.preventDefault();
	$(this).closest('tr').remove();
});