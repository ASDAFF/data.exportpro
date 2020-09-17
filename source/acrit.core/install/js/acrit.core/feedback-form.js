function acritCoreFeedbackFormDisableControls(disabled){
	var controls = [
		'acrit-core-feedback-form-problem',
		'acrit-core-feedback-form-name',
		'acrit-core-feedback-form-email-user',
		'acrit-core-feedback-form-agree-checkbox',
		'acrit-core-feedback-form-submit',
		'acrit-core-feedback-form-transmit',
		'acrit-core-feedback-form-tech-data',
	];
	for(var i in controls){
		if(disabled){
			BX(controls[i]).setAttribute('disabled', 'disabled');
		}
		else{
			BX(controls[i]).removeAttribute('disabled');
		}
	}
}
function acritCoreFeedbackFormSubmit(){
	if(!BX('acrit-core-feedback-form-agree-checkbox').checked){
		alert(BX('acrit-core-feedback-form-submit').getAttribute('data-agree'));
		return false;
	}
	acritCoreFeedbackFormDisableControls(true);
	var ajax = BX.ajax.post(
		'/bitrix/admin/acrit_core_feedback.php?lang=ru&action=feedback_send',
		{
			module: BX('acrit-core-feedback-form-module').value,
			email_admin: BX('acrit-core-feedback-form-email-admin').value,
			subject: BX('acrit-core-feedback-form-subject').value,
			problem: BX('acrit-core-feedback-form-problem').value,
			name: BX('acrit-core-feedback-form-name').value,
			email_user: BX('acrit-core-feedback-form-email-user').value,
			tech: BX('acrit-core-feedback-form-tech-data').value,
			url: location.href
		},
		function(HTML){
			acritCoreFeedbackFormDisableControls(false);
			if(HTML == 'Y'){
				alert(BX('acrit-core-feedback-form-submit').getAttribute('data-success'));
			}
			else{
				alert(BX('acrit-core-feedback-form-submit').getAttribute('data-error'));
			}
		}
	);
	return false;
}
function acritCoreFeedbackFormTransmit(){
	BX.toggleClass(BX('acrit-core-feedback-form-tech'), 'visible');
	return false;
}
BX.adminFormTools.modifyCheckbox(BX('acrit-core-feedback-form-agree-checkbox'));