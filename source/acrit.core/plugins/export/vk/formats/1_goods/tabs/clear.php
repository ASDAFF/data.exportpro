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

$obTabControl->AddSection('CLEAR', $obPlugin::getMessage('CLEAR_HEADER'));

// CLEAR ALL ITEMS
$obTabControl->BeginCustomField($strPluginParams.'[CLEAR_ALL]', $obPlugin::getMessage('CLEAR_ALL'));
?>
    <tr id="row_VK_GOODS_CLEAR_ALL">
        <td width="40%" valign="top">
            <?=Helper::showHint($obPlugin::getMessage('CLEAR_ALL_DESC'));?>
            <?=$obTabControl->GetCustomLabelHTML() ?>:
        </td>
        <td width="60%">
            <a href="#" class="adm-btn adm-btn-red" data-role="clear-all-items"><?=$obPlugin::getMessage('CLEAR_ALL_BTN_TITLE');?></a>
        </td>
    </tr>
    <?
$obTabControl->EndCustomField($strPluginParams.'[CLEAR_ALL]');

// CLEAR LOADED ITEMS
$obTabControl->BeginCustomField($strPluginParams.'[CLEAR_LOADED]', $obPlugin::getMessage('CLEAR_LOADED'));
?>
    <tr id="row_VK_GOODS_CLEAR_LOADED">
        <td width="40%" valign="top">
            <?=Helper::showHint($obPlugin::getMessage('CLEAR_LOADED_DESC'));?>
            <?=$obTabControl->GetCustomLabelHTML() ?>:
        </td>
        <td width="60%">
            <a href="#" class="adm-btn" data-role="clear-loaded-items"><?=$obPlugin::getMessage('CLEAR_LOADED_BTN_TITLE');?></a>
        </td>
    </tr>
    <?
$obTabControl->EndCustomField($strPluginParams.'[CLEAR_LOADED]');

// CLEAR ALBUM ITEMS
$obTabControl->BeginCustomField($strPluginParams.'[CLEAR_ALBUM]', $obPlugin::getMessage('CLEAR_ALBUM'));
?>
    <tr id="row_VK_GOODS_CLEAR_ALBUM">
        <td width="40%" valign="top">
            <?=Helper::showHint($obPlugin::getMessage('CLEAR_ALBUM_DESC'));?>
            <?=$obTabControl->GetCustomLabelHTML() ?>:
        </td>
        <td width="60%">
            <select name="albums">
                <option value="">-- <?=$obPlugin::getMessage('CLEAR_ALBUM_SELECT_TITLE');?> --</option>
                <?foreach ($arAlbums as $id => $title):?>
                <option value="<?=$id;?>"><?=$title;?></option>
                <?endforeach;?>
            </select>
            <a href="#" class="adm-btn" data-role="clear-album-items"><?=$obPlugin::getMessage('CLEAR_ALBUM_BTN_TITLE');?></a>
        </td>
    </tr>
    <?
$obTabControl->EndCustomField($strPluginParams.'[CLEAR_ALBUM]');
