var AcritPopupHint;
$(document).ready(function(){
	AcritPopupHint = new BX.CDialog({
		ID: 'AcritPopupHint',
		title: '',
		content: '',
		resizable: true,
		draggable: true,
		height: 400,
		width: 800
	});
	AcritPopupHint.Open = function(title, content){
		this.SetTitle(title);
		this.SetContent(content);
		this.SetAutoSize();
		this.InitFilter();
		this.Show();
	}
	AcritPopupHint.SetAutoSize = function(){
		$('.bx-core-adm-dialog-content-wrap-inner', this.DIV).css({
			'height': '100%',
			'-webkit-box-sizing': 'border-box',
				 '-moz-box-sizing': 'border-box',
							'box-sizing': 'border-box'
		}).children().css({
			'height': '100%'
		});
	}
	AcritPopupHint.InitFilter = function(){
		var
			div = $('div[data-role="acrit-exp-field-popup-hint"]', this.DIV),
			input = $('input[data-role="acrit-exp-field-popup-hint-search"]', this.DIV),
			groups = $('ul[data-role="acrit-exp-field-popup-hint-groups"]', this.DIV).children('li');
		input.bind('input', function(e){
			var
				query = $(this).val().toLowerCase().trim(),
				emptyQuery = !query.length;
			groups.each(function(){
				var
					title = $(this).children('[data-role="acrit-exp-field-popup-hint-group"]').text().trim(),
					items = $(this).hide().children('ul').children('li').hide(),
					groupVisible = false;
				if(emptyQuery){
					$(this).show();
					items.show();
				}
				else{
					if(title.toLowerCase().indexOf(query) != -1){
						groupVisible = true;
						items.show();
					}
					else{
						items.each(function(){
							if($(this).text().toLowerCase().indexOf(query) != -1){
								$(this).show();
								groupVisible = true;
							}
						});
					}
					if(groupVisible){
						$(this).show();
					}
				}
			});
		});
	}
	AcritPopupHint.SetHtml = function(html){
		$('.bx-core-adm-dialog-content-wrap-inner', this.PARTS.CONTENT_DATA).first().html(html);
	}
});