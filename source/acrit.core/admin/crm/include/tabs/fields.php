<?
namespace Acrit\Core\Crm;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('HEADING_FIELDS_TBLCMPR', Loc::getMessage('ACRIT_CRM_TAB_FIELDS_HEADING'));

// Block for tags management
$obTabControl->BeginCustomField('PROFILE[FIELDS][table_compare]', Loc::getMessage('ACRIT_CRM_FIELDS_TBLCMPR'));
$store_fields = $obPlugin->getFields();
$crm_fields = CrmPortal::getFields();
?>
    <tr id="tr_fields_table_compare">
        <td>
            <table class="adm-list-table" id="acrit_imp_agents_list">
                <thead>
                <tr class="adm-list-table-header">
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_FIELDS_ORDER');?></div></td>
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_FIELDS_DEAL');?></div></td>
                </tr>
                </thead>
                <tbody>
                    <?foreach ($store_fields as $field):?>
                    <tr class="adm-list-table-row action-add-row" id="acrit_imp_agents_add">
                        <td class="adm-list-table-cell"><?=$field['name'];?></td>
                        <td class="adm-list-table-cell">
                            <select class="custom-select" name="PROFILE[FIELDS][table_compare][<?=$field['id'];?>][value]">">
                                <option value=""><?=Loc::getMessage('ACRIT_CRM_TAB_FIELDS_NO');?></option>
	                            <?foreach ($crm_fields as $crm_field):?>
                                <option value="<?=$crm_field['id'];?>"<?=$arProfile['FIELDS']['table_compare'][$field['id']]['value']==$crm_field['id']?' selected':'';?>><?=$crm_field['name'];?> (<?=$crm_field['id'];?>)</option>
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
$obTabControl->EndCustomField('PROFILE[FIELDS]');

?>