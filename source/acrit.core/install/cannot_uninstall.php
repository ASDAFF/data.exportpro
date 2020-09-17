<?
IncludeModuleLangFile(__FILE__);
if(is_array($_SESSION['MP_MOD_DELETED']) && $_SESSION['MP_MOD_DELETED']['ID'] == ACRIT_CORE){
	unset($_SESSION['MP_MOD_DELETED']);
}
?>
<div>
	<?=\Acrit\Core\Helper::showError(getMessage('ACRIT_CORE_CANNOT_UNINSTALL_TITLE'), 
		getMessage('ACRIT_CORE_CANNOT_UNINSTALL_DESC', [
			'#MODULES#' => implode(', ', $GLOBALS['ACRIT_MODULE_OTHER_MODULES']),
		]));?>
</div>
<form action="/bitrix/admin/partner_modules.php" method="get">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID;?>" />
	<input type="submit" value="<?=getMessage('MOD_BACK');?>" />
</form>