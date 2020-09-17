<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

// Это использовать для всех дополнительных параметров в плагинах, чтобы параметры профиля не пересекались
$strPluginParams = $obPlugin->getPluginParamsInputName();
$arPluginParams = $obPlugin->getPluginParams();

#
$intProfileID = $arProfile['ID'];
$strVkGroupId = strval($arProfile['PARAMS']['GROUP_ID']);
$strVkOwnerId = intval('-' . $strVkGroupId);
$arAlbums = $obPlugin->getGroupAlbums($strVkOwnerId, $intProfileID);
$arAlbums = !empty($arAlbums) ? array_flip($arAlbums) : $arAlbums;
?>
<script>
    BX.message({'CLEAR_ALERT': '<?=$obPlugin::getMessage('CLEAR_ALERT');?>'});
</script>
<?
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('ALBUMS', $obPlugin::getMessage('ALBUMS_HEADER'));

// ALBUMS TABLE
$obTabControl->BeginCustomField($strPluginParams.'[ALBUMS_TABLE]', $obPlugin::getMessage('ALBUMS_TABLE'));
?>
    <tr>
        <td width="100%" valign="top" colspan="2">
            <div class="adm-info-message-wrap">
                <div class="adm-info-message"><?=$obPlugin::getMessage('ALBUMS_TABLE_NOTE');?></div>
            </div>
        </td>
    </tr>
    <tr id="row_VK_GOODS_ALBUMS_TABLE">
        <td width="100%" valign="top" colspan="2">
            <table class="adm-list-table acrit-exp-tab-albums-table">
                <thead>
                    <tr class="adm-list-table-header">
                        <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=$obPlugin::getMessage('ALBUMS_TABLE_H1');?></div></td>
                        <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=$obPlugin::getMessage('ALBUMS_TABLE_H2');?></div></td>
                        <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=$obPlugin::getMessage('ALBUMS_TABLE_H3');?></div></td>
                        <?/*<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">Порядок</div></td>*/?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField($strPluginParams.'[ALBUMS_TABLE]');
