<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper;

if(!strlen($strEmailAdmin) || !check_email($strEmailAdmin)){
	$strEmailAdmin = reset(explode(',', \Bitrix\Main\Config\Option::get('main', 'email_from')));
}

$arTech = [
	'MODULE_CODE' => $strModuleId,
	'MODULE_VERSION' => Helper::getModuleVersion($strModuleId),
	'BITRIX_VERSION' => SM_VERSION,
	'BITRIX_VERSION_DATE' => SM_VERSION_DATE,
	'LICENCE_HASH' => md5('BITRIX'.LICENSE_KEY.'LICENCE'),
	'SITE_CHARSET' => SITE_CHARSET,
	'FORMAT_DATE' => FORMAT_DATE,
	'FORMAT_DATETIME' => FORMAT_DATETIME,
];
$arTechTmp = [];
foreach($arTech as $key => $value){
	$arTechTmp[] = Helper::getMessage('ACRIT_CORE_FEEDBACK_TECH_'.$key).': '.$value;
}
$strTech = implode("\r\n", $arTechTmp);

?>
<div id="acrit-core-feedback-form">
	<input type="hidden" value="<?=$strModuleId;?>" id="acrit-core-feedback-form-module" />
	<input type="hidden" value="<?=$strEmailAdmin;?>" id="acrit-core-feedback-form-email-admin" />
	<input type="hidden" value="<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_SUBJECT', [
		'#MODULE_ID#' => $strModuleId,
	]);?>" id="acrit-core-feedback-form-subject" />
	<table>
		<tbody>
			<tr>
				<td>
					<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_PROBLEM');?>:
				</td>
				<td>
					<textarea cols="50" rows="5"
						id="acrit-core-feedback-form-problem"
						placeholder="<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_PROBLEM_PLACEHOLDER');?>"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_NAME');?>:
				</td>
				<td>
					<input type="text" size="50" maxlength="250" value="<?=$GLOBALS['USER']->getFullName();?>"
						id="acrit-core-feedback-form-name"
						placeholder="<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_NAME_PLACEHOLDER');?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_EMAIL');?>:
				</td>
				<td>
					<input type="text" size="50" maxlength="250" value="<?=$GLOBALS['USER']->getEmail();?>"
						id="acrit-core-feedback-form-email-user"
						placeholder="<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_EMAIL_PLACEHOLDER');?>" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div id="acrit-core-feedback-form-agree">
						<input type="checkbox" id="acrit-core-feedback-form-agree-checkbox" checked="checked" />
						<label for="acrit-core-feedback-form-agree-checkbox">
							<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_AGREE');?>
						</label>
					</div>
					<table id="acrit-core-feedback-form-buttons">
						<tbody>
							<tr>
								<td>
									<a href="javascript:void(0);" class="adm-btn adm-btn-green" id="acrit-core-feedback-form-submit"
										data-success="<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_SUCCESS');?>"
										data-error="<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_ERROR');?>"
										data-agree="<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_MUST_AGREE');?>"
										onclick="return acritCoreFeedbackFormSubmit();">
										<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_SUBMIT');?>
									</a>
								</td>
								<td>
									<a href="javascript:void(0);" id="acrit-core-feedback-form-transmit"
										onclick="return acritCoreFeedbackFormTransmit();">
										<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_TRANSMIT');?>
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr id="acrit-core-feedback-form-tech">
				<td>
					<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_TECH');?>:
				</td>
				<td>
					<textarea cols="50" rows="<?=count($arTechTmp);?>" readonly="readonly"
						id="acrit-core-feedback-form-tech-data"
						placeholder="<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_TECH_PLACEHOLDER');?>"><?=$strTech;?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<style>
	#acrit-core-feedback-form {
		max-width:600px;
	}
	#acrit-core-feedback-form table {
		width:100%;
	}
	#acrit-core-feedback-form table td {
		vertical-align:top;
	}
	#acrit-core-feedback-form table td:first-child {
		padding-right:5px;
		padding-top:5px;
		text-align:right;
		width:35%;
	}
	#acrit-core-feedback-form table td[colspan] {
		width:auto;
	}
	#acrit-core-feedback-form table td textarea {
		max-height:500px;
		min-height:100px;
		resize:vertical;
		width:100%;
		-webkit-box-sizing:border-box;
		   -moz-box-sizing:border-box;
            box-sizing:border-box;
	}
	#acrit-core-feedback-form table td textarea[readonly] {
		background:#eee;
		height:auto;
		max-height:none;
		min-height:0;
		opacity:1!important;
		resize:none;
	}
	#acrit-core-feedback-form table td input[type=text] {
		width:100%;
		-webkit-box-sizing:border-box;
		   -moz-box-sizing:border-box;
            box-sizing:border-box;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-agree {
		padding:10px 0;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-agree > * {
		vertical-align:middle;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-buttons {
		width:100%;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-buttons td {
		vertical-align:middle;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-buttons td:first-child{
		text-align:left;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-buttons td:last-child{
		text-align:right;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-submit[disabled] {
		opacity:0.4;
		pointer-events:none!important;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-transmit {
		border-bottom:1px dashed #2675d7;
		color:#2675d7;
		text-decoration:none;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-transmit:hover {
		border-bottom:0;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-transmit[disabled] {
		opacity:0.4;
		pointer-events:none!important;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-tech {
		display:none;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-tech.visible {
		display:table-row;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-tech td {
		padding-bottom:20px;
		padding-top:20px;
	}
	#acrit-core-feedback-form #acrit-core-feedback-form-tech-data[readonly][disabled]{
		opacity:0.4!important;
	}
</style>
<script src="/bitrix/js/acrit.core/feedback-form.js?<?=microtime(true);?>"></script>
