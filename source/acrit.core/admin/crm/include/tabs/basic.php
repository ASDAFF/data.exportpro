<?
namespace Acrit\Core\Crm;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('HEADING_BASIC', Loc::getMessage('ACRIT_CRM_TAB_BASIC_HEADING'));

// Code
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][source_id]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_SOURCE_ID'));
?>
    <tr id="tr_connect_data_source_id">
        <td>
            <label for="field_connect_data_source_id"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="text" name="PROFILE[CONNECT_DATA][source_id]" size="50" maxlength="255" data-role="profile-name"
                   data-default-name="<?=Loc::getMessage('ACRIT_EXP_FIELD_CODE_DEFAULT');?>"
			       <?if($intProfileID):?>data-custom-name="true"<?endif?>
                   value="<?=htmlspecialcharsbx($arProfile['CONNECT_DATA']['source_id']);?>" />
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][source_id]');

// Prefix
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][prefix]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_PREFIX'));
?>
    <tr id="tr_connect_data_prefix">
        <td>
            <label for="field_connect_data_prefix"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="text" name="PROFILE[CONNECT_DATA][prefix]" size="50" maxlength="255" data-role="profile-name"
                   data-default-name="<?=Loc::getMessage('ACRIT_EXP_FIELD_PREFIX_DEFAULT');?>"
			       <?if($intProfileID):?>data-custom-name="true"<?endif?>
                   value="<?=htmlspecialcharsbx($arProfile['CONNECT_DATA']['prefix']);?>" />
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][prefix]');

// Prefix
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][category]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_CATEGORY'));
$list = CrmPortal::getDirections();
?>
    <tr id="tr_connect_data_category">
        <td>
            <label for="field_connect_data_category"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][category]">
                <?foreach ($list as $id => $name):?>
                <option value="<?=$id;?>"<?=$arProfile['CONNECT_DATA']['category']==$id?' selected':'';?>><?=$name;?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][category]');

// Prefix
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][responsible]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_RESPONSIBLE'));
$list = CrmPortal::getUsers();
?>
    <tr id="tr_connect_data_responsible">
        <td>
            <label for="field_connect_data_responsible"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][responsible]">
	            <?foreach ($list as $id => $name):?>
                <option value="<?=$id;?>"<?=$arProfile['CONNECT_DATA']['responsible']==$id?' selected':'';?>><?=$name;?></option>
	            <?endforeach;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][responsible]');
