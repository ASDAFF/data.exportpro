<?
namespace Acrit\Core\Crm;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('HEADING_CONTACTS', Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_HEADING'));

// Block for tags management
$obTabControl->BeginCustomField('PROFILE[CONTACTS][table_compare]', Loc::getMessage('ACRIT_CRM_CONTACTS_TBLCMPR'));
$store_fields = $obPlugin->getContactFields();
$crm_fields = CrmPortal::getContactFields();
?>
    <tr id="tr_contacts_table_compare">
        <td>
            <table class="adm-list-table" id="acrit_imp_agents_list">
                <thead>
                <tr class="adm-list-table-header">
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_DEAL');?></div></td>
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_ORDER');?></div></td>
                </tr>
                </thead>
                <tbody>
                <?foreach ($crm_fields as $k => $crm_field):?>
                <tr class="adm-list-table-row action-add-row" id="acrit_imp_agents_add">
                    <td class="adm-list-table-cell"><?=$crm_field['name'];?> (<?=$k;?>)</td>
                    <td class="adm-list-table-cell">
                        <select class="custom-select" name="PROFILE[CONTACTS][table_compare][<?=$k;?>]">
                            <option value=""><?=Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_NO');?></option>
                            <?foreach ($store_fields as $fields_group):?>
                            <optgroup label="<?=$fields_group['title'];?>">
	                            <?foreach ($fields_group['items'] as $field):?>
                                <option value="<?=$field['id'];?>"<?=$arProfile['CONTACTS']['table_compare'][$k]==$field['id']?' selected':'';?>><?=$field['name'];?></option>
	                            <?endforeach;?>
                            </optgroup>
                            <?endforeach;?>
                        </select>
                    </td>
                </tr>
                <?endforeach;?>
                </tbody>
            </table>
        </td>
    </tr>
<?
$obTabControl->EndCustomField('PROFILE[CONTACTS][table_compare]');

?>