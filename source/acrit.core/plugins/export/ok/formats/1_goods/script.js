/**
 * Clear tab
 */

$(document).delegate('a[data-role="clear-all-items"]', 'click', function(e) {
	var btn = $(this);
	if (!btn.hasClass('adm-btn-disabled')) {
		if (confirm(BX.message('CLEAR_ALERT'))) {
			btn.removeClass('adm-btn-red').addClass('adm-btn-disabled');
			acritExpAjax(['plugin_ajax_action', 'items_clear_all_get_list'], {}, function (JsonResult, textStatus, jqXHR) {
				//console.log(JsonResult);
				if (JsonResult.result == 'ok') {
					if (JsonResult.list.length > 0) {
						okGoodsClearDeleteList(btn, JsonResult.list, 0);
					}
					else {
						btn.removeClass('adm-btn-disabled').addClass('adm-btn-red');
					}
				}
			}, function (jqXHR) {
				console.log(jqXHR);
			}, true);
		}
	}
	return false;
});

$(document).delegate('a[data-role="clear-loaded-items"]', 'click', function(e) {
	var btn = $(this);
	if (!btn.hasClass('adm-btn-disabled')) {
		if (confirm(BX.message('CLEAR_ALERT'))) {
			btn.addClass('adm-btn-disabled');
			acritExpAjax(['plugin_ajax_action', 'items_clear_loaded_get_list'], {}, function (JsonResult, textStatus, jqXHR) {
				//console.log(JsonResult);
				if (JsonResult.result == 'ok') {
					if (JsonResult.list.length > 0) {
						okGoodsClearDeleteList(btn, JsonResult.list, 0);
					}
					else {
						btn.removeClass('adm-btn-disabled');
					}
				}
			}, function (jqXHR) {
				console.log(jqXHR);
			}, true);
		}
	}
	return false;
});

$(document).delegate('a[data-role="clear-album-items"]', 'click', function(e) {
	var btn = $(this);
	var album_id = $('#row_OK_GOODS_CLEAR_ALBUM [name="albums"]').val();
	if (!btn.hasClass('adm-btn-disabled')) {
		if (confirm(BX.message('CLEAR_ALERT'))) {
			data = {
				'id': album_id,
			};
			btn.addClass('adm-btn-disabled');
			acritExpAjax(['plugin_ajax_action', 'items_clear_album_get_list'], data, function (JsonResult, textStatus, jqXHR) {
				//console.log(JsonResult);
				if (JsonResult.result == 'ok') {
					if (JsonResult.list.length > 0) {
						okGoodsClearDeleteList(btn, JsonResult.list, 0);
					}
					else {
						btn.removeClass('adm-btn-disabled');
					}
				}
			}, function (jqXHR) {
				console.log(jqXHR);
			}, true);
		}
	}
	return false;
});

function okGoodsClearDeleteList(btn, list, step) {
	acritExpAjax(['plugin_ajax_action', 'items_clear_delete'], {list: list, step: step}, function (JsonResult, textStatus, jqXHR) {
		//console.log(JsonResult);
		if (JsonResult.result == 'ok') {
			if (JsonResult.not_empty) {
				okGoodsClearDeleteList(btn, list, step + 1);
			}
			else {
				btn.removeClass('adm-btn-disabled');
				if (btn.data('role') == 'clear-all-items') {
					btn.addClass('adm-btn-red');
				}
			}
		}
	}, function (jqXHR) {
		console.log(jqXHR);
	}, true);
}


/**
 * Albums tab
 */

$(document).delegate('input.acrit-exp-tab-albums-table-album_name', 'change', function(e) {
	okGoodsAlbumsUpdate();
	return false;
});

$(document).delegate('input.acrit-exp-tab-albums-table-album_id', 'change', function(e) {
	okGoodsAlbumsUpdate();
	return false;
});

$(document).delegate('input.acrit-exp-tab-albums-table-iblock_id', 'change', function(e) {
	okGoodsAlbumsUpdate();
	return false;
});

function okGoodsAlbumsGetList() {
	acritExpAjax(['plugin_ajax_action', 'albums_sections_list'], {}, function (JsonResult, textStatus, jqXHR) {
//			console.log(JsonResult);
			if (JsonResult.result == 'ok') {
				$('.acrit-exp-tab-albums-table tbody').empty();
				$.each(JsonResult.list, function(k, item) {
					var html_row = '' +
						'<tr class="adm-list-table-row" data-role="field_row" data-section_id="' + item.section_id + '">\n' +
						'    <td class="adm-list-table-cell">' + item.section_name + ' [' + item.section_id + ']</td>\n' +
						'    <td class="adm-list-table-cell acrit-exp-tab-albums-table-input-cell">\n' +
						'        <div class="acrit-exp-tab-albums-table-input-wrap">\n' +
						'            <input type="text" name="PROFILE[PARAMS][ALBUMS_REDEF][' + item.section_id + '][album_name]" class="acrit-exp-tab-albums-table-album_name" value="' + item.album_name + '" />\n' +
						'            <input type="hidden" name="PROFILE[PARAMS][ALBUMS_REDEF][' + item.section_id + '][iblock_id]" class="acrit-exp-tab-albums-table-iblock_id" value="' + item.iblock_id + '" />\n' +
						'            <input type="hidden" name="PROFILE[PARAMS][ALBUMS_REDEF][' + item.section_id + '][section_id]" class="acrit-exp-tab-albums-table-section_id" value="' + item.section_id + '" />\n' +
						'        </div>\n' +
						'    </td>\n' +
						'    <td class="adm-list-table-cell acrit-exp-tab-albums-table-input-cell">\n' +
						'        <div class="acrit-exp-tab-albums-table-input-wrap">\n' +
						'            <input type="text" name="PROFILE[PARAMS][ALBUMS_REDEF][' + item.section_id + '][album_id]" class="acrit-exp-tab-albums-table-album_id" value="' + item.album_id + '" />\n' +
						'        </div>\n' +
						'    </td>\n' +
						//'    <td class="adm-list-table-cell"><a href="#" class="acrit-exp-tab-albums-table-down">Поднять</a> / <a href="#" class="acrit-exp-tab-albums-table-up">Опустить</a></td>\n' +
						'</tr>';
					$('.acrit-exp-tab-albums-table tbody').append(html_row);
				});
			}
		}, function (jqXHR) {
			console.log(jqXHR);
		}, true
	);
}

function okGoodsAlbumsUpdate() {
	var table = {};
	$('.acrit-exp-tab-albums-table .adm-list-table-row').each(function (index) {
		var iblock_id = $(this).find('.acrit-exp-tab-albums-table-iblock_id').val();
		var section_id = $(this).find('.acrit-exp-tab-albums-table-section_id').val();
		var album_name = $(this).find('.acrit-exp-tab-albums-table-album_name').val();
		var album_id = $(this).find('.acrit-exp-tab-albums-table-album_id').val();
		table[section_id] = {'iblock_id': iblock_id, 'section_id': section_id, 'album_name': album_name, 'album_id': album_id}
	});
	acritExpAjax(['plugin_ajax_action', 'albums_sections_update'], {'table': table}, function (JsonResult, textStatus, jqXHR) {
			//console.log(JsonResult);
			if (JsonResult.result == 'ok') {
				okGoodsAlbumsGetList();
			}
		}, function (jqXHR) {
			console.log(jqXHR);
		}, true
	);
}

$(document).ready(function(){
	okGoodsAlbumsGetList();
});
