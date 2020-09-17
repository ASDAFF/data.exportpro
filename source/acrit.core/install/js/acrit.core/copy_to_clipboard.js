function acritCoreCopyToClipboard(elementId, callback) {
	elementId = (typeof elementId == 'object' ? elementId : document.getElementById(elementId));
	if(document.selection) {
		document.selection.empty();
		var range = document.body.createTextRange();
		range.moveToElementText(elementId);
		range.select().createTextRange();
		document.execCommand('Copy');
		document.selection.empty();
	} else if(window.getSelection) {
		window.getSelection().removeAllRanges();
		var range = document.createRange();
		range.selectNode(elementId);
		window.getSelection().addRange(range);
		document.execCommand('Copy');
		window.getSelection().removeAllRanges();
		if(typeof callback == 'function'){
			callback.call();
		}
	}
}