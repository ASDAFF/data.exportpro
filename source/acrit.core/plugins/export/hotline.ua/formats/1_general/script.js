if (!window.hotlinePluginInitialized) {
	$(document).on('click', '.hotline-config-deliveries-add-new', function (e) {

		e.preventDefault();

		var body = $('.hotline-config-deliveries-table'),
						cleanRow = $('.hotline-config-deliveries-new-row'),
						tempName = '';
		var rowNumber = parseInt($('.hotline-config-deliveries-count').val());
		rowNumber++;

		var newRow = cleanRow.clone();
		newRow.show().insertBefore('.hotline-config-deliveries-new-row').removeClass('hotline-config-deliveries-new-row');
		newRow.find('input').each(function () {
			tempName = $(this).attr('name');
			tempName = tempName.replace('#', rowNumber)
			$(this).attr('name', tempName);
		})
		$('.hotline-config-deliveries-count').val(rowNumber);
	});
	window.hotlinePluginInitialized = true;
}