$.fn.acritFilter = function(options){
	
	// Vars
	var divContainer = $(this),
		ID = divContainer.attr('id');
	
	// 1. PREPARE
	
	// Defaults
	var defaults = {
		inputName: 'filter',
		confirmDeleteItem: true,
		confirmDeleteGroup: true,
		allFields: {
			'NAME': {
				'name': 'Name',
				'type': 'S',
			},
			'CODE': {
				'name': 'Code',
				'type': 'S',
			},
			'ACTIVE': {
				'name': 'Active',
				'type': 'C',
			}
		},
		formData: {},
		tpl:{
			item:
				'<div data-role="item">' +
					'<span data-role="name"></span> ' +
					'<span data-role="field">' +
						'<a href="#" data-role="select-field" data-acrit-modal="test1">' + options.lang.selectField + '</a>' +
					'</span> ' +
					'<span data-role="type"></span> ' +
					'<span data-role="logic">' +
						'<a href="#" data-role="select-logic" data-acrit-modal="test2">' + options.lang.selectLogic + '</a>' +
					'</span> ' +
					'<span data-role="value">' +
						'<a href="#" data-role="select-value" data-acrit-modal="test3">' + options.lang.selectValue + '</a>' +
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
						'<select data-role="group-aggregator-value">' +
							'<option value="Y">' + options.lang.aggregatorY + '</option>' +
							'<option value="N">' + options.lang.aggregatorN + '</option>' +
						'</select>' +
					'</div>' +
					'<div data-role="group-items">' +
						'' +
					'</div>' +
					'<div data-role="group-controls">' +
						'<a href="#" data-role="add-item">' + options.lang.addItem + '</a> ' +
						'<a href="#" data-role="add-group">' + options.lang.addGroup + '</a> ' +
						'<a href="#" data-role="delete-group">&times;</a>' +
					'</div>' +
				'</div>'
		}
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
		var inputJson = divContainer.find('[data-role="json-result"]'),
			jsonItems = divContainer.searchGroupsForJson()[0];
		inputJson.val(JSON.stringify(jsonItems));
	}
	divContainer.bind('acrit:buildJsonResult', function(){ // like public method
		divContainer.buildJsonResult();
	});
	
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
	var jsonResult = $('<input>').attr({
		'type': 'hidden',
		'name': options.inputName,
		'value': '',
		'data-role': 'json-result'
	});
	divContainer.append(jsonResult);
	
	// 3.3 Create root div group
	divContainer.addGroup();
	
	// 4. EVENT HANDLERS
	
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
	
	// Select field
	//$(document).delegate('#' + ID + ' [data-role="select-field"]', 'click', function(e){
	//	e.preventDefault();
	//	
	//});
	
	// return this
	return this;
};
