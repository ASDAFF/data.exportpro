<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

if(!Helper::isUtf()) {
	$arPost = Helper::convertEncoding($arPost);
}

$strFieldCode = $arPost['field_code'];
$strFieldType = $arPost['field_type'];
$strFieldName = $arPost['field_name'];

print Helper::showNote(Loc::getMessage('ACRIT_EXP_POPUP_VALUE_SETTINGS_NOTICE'), true).'<br/>';

?>

<form action="<?=POST_FORM_ACTION_URI;?>" method="post" data-role="popup-form">
<?
if(strlen($strFieldType)) {
	$arFieldsAll = Field::getValueTypesStatic($strModuleId);
	$arField = $arFieldsAll[$strFieldType];
	if(is_array($arField) && strlen($arField['CLASS'])){
		$arCurrentParams = $arPost;
		$arPluginFields = $obPlugin->getFields($intProfileID, $intIBlockID);
		#$arPluginFields[] = Profile::getFieldSortElement($intProfileID, $intIBlockID);
		$arPluginFields[] = Helper::call($strModuleId, 'Profile', 'getFieldSortElement', [$intProfileID, $intIBlockID]);
		#$arPluginFields[] = Profile::getFieldSortOffer($intProfileID, $intIBlockOfferID);
		$arPluginFields[] = Helper::call($strModuleId, 'Profile', 'getFieldSortOffer', [$intProfileID, $intIBlockOfferID]);
		foreach($arPluginFields as $obField){
			$obField->setModuleId($strModuleId);
			$obField->setPlugin($obPlugin);
			if($obField->getCode()==$strFieldCode){
				print $arField['CLASS']::showValueSettings($obField, $strFieldCode, $strFieldName, $arPost);
			}
		}
	}
}
?>
<div style="display:none"><input type="submit" value="" /></div>
</form>
