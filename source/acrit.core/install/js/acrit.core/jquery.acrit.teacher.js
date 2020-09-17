/*
jQuery.acritTeacher = function(name, input){
	let
		type = typeof input,
		mainArguments = arguments;
	
	if(jQuery.acritTeacher.data == undefined){
		jQuery.acritTeacher.data = {};
	}
	
	if(typeof jQuery.acritTeacher.data == undefined){
		
	}
	
	switch(type){
		case 'string': return executeMethod(name, input);
		case 'object': return saveInputData(name, input);
	}
	
	function executeMethod(name, method){
		switch(method){
			case 'start':
				console.log(mainArguments);
				break;
		}
	}
	
	function saveInputData(name, input){
		jQuery.acritTeacher.data.name = input;
	}
	
}
*/

/*
jQuery.acritTeacher({
	data: {} // json || function || ajax
});
*/

$.fn.acritTeacher = function(options){
	
	let
		divContainer = $(this),
		defaults = {
			testOption: true
		};
	
	if(this.acritTeacherOverlay == undefined){
		this.acritTeacherOverlay = $('<div [data-role="acrit_teacher_overlay"] />').appendTo($('body'));
	}
	
	// Options
	options = $.extend({}, defaults, options);
	
	// 1. METHODS
	
	// Set content for input[type=hidden]
	divContainer.buildJsonResult = function(){
		var inputJson = divContainer.find('[data-role="json-result"]'),
			jsonItems = divContainer.searchGroupsForJson()[0];
		inputJson.val(JSON.stringify(jsonItems));
	}
	
	//
	divContainer.bind('acrit:buildJsonResult', function(){
		divContainer.buildJsonResult();
	});
	
	//
	divContainer.searchGroupsForJson = function(parentObject){
		var result = [];
		if(!parentObject) {
			parentObject = $(this);
		}
		var groups = parentObject.find('[data-role="group"]').first().parent().children('[data-role="group"]');
		groups.each(function(){
			var group = $(this);
			var jsonItem = {
				'Name': 'group_' + $.trim(Math.random()).substr(2),
				'AggregatorType': group.find('[data-role="group-aggregator-type"]')
					.not(group.find('[data-role="group-items"] [data-role="group-aggregator-type"]')).val(),
				'AggregatorValue': group.find('[data-role="group-aggregator-value"]')
					.not(group.find('[data-role="group-items"] [data-role="group-aggregator-value"]')).val(),
				'Groups': divContainer.searchGroupsForJson(group),
				'Fields': divContainer.searchFieldsForJson(group)
			};
			result.push(jsonItem);
		});
		return result;
	}
	
	//
	divContainer.searchFieldsForJson = function(parentObject){
		var result = [];
		if(!parentObject) {
			parentObject = $(this);
		}
		var fields = parentObject.children('[data-role="group-items"]').children('[data-role="item"]');
		fields.each(function(){
			var field = $(this);
			var jsonItem = {
				'FieldName': field.find('[data-role="select-field"]').attr('data-name'),
				'FieldCode': field.find('[data-role="select-field"]').attr('data-code'),
				'LogicName': field.find('[data-role="select-logic"]').attr('data-name'),
				'LogicCode': field.find('[data-role="select-logic"]').attr('data-code'),
				'Value': {},
				'ValueX': {}
			};
			if(jsonItem.FieldCode != undefined && jsonItem.LogicCode != undefined) {
				result.push(jsonItem);
			}
		});
		return result;
	}
	
	// Add item
	divContainer.addItem = function(parentObject){
		var newItem = $(options.tpl.item);
		if(parentObject == undefined) {
			parentObject = this;
		}
		parentObject.append(newItem);
		divContainer.buildJsonResult();
		return newItem;
	}
	
	// Add group
	divContainer.addGroup = function(parentObject){
		var newGroup = $(options.tpl.group);
		if(!parentObject) {
			parentObject = this;
			newGroup.find('[data-role="delete-group"]').remove();
		}
		parentObject.append(newGroup);
		divContainer.buildJsonResult();
		return newGroup;
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
	
	// 2. EVENT HANDLERS
	
	// Add item
	
	// 3. INITIAL ACTIONS
	
	// return this
	return this;
};

$(document).ready(function(){
	$('#adm-workarea').acritTeacher();
});

