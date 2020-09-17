/**
 *	Hot keys
 */
$.alt = function(Key, Callback) {
	$(document).keydown(function(E) {
		if(E.altKey && E.keyCode == Key.charCodeAt(0)) {
			return Callback.apply(this)
		}
	});
};