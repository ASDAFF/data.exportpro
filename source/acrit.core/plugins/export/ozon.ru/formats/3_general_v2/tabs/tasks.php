<?

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\EventManager,
    \Acrit\Core\Helper,
    \Acrit\Core\HttpRequest,
    \Acrit\Core\Export\Plugin,
    \Acrit\Core\Export\Field\Field,
    \Acrit\Core\Export\Exporter,
    \Acrit\Core\Export\ProfileTable as Profile,
    \Acrit\Core\Export\ProfileIBlockTable as ProfileIBlock,
    \Acrit\Core\Export\Filter,
    \Acrit\Core\Export\ExportDataTable as ExportData,
    \Acrit\Core\Log,
    \Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition;

Loc::loadMessages(__FILE__);

// Это использовать для всех дополнительных параметров в плагинах, чтобы параметры профиля не пересекались
$strPluginParams = $obPlugin->getPluginParamsInputName();
$arPluginParams = $obPlugin->getPluginParams();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 
$obTabControl->BeginCustomField($strPluginParams . '[EXPORT_PROMOCODES]', $obPlugin::getMessage('EXPORT_PROMOCODES'));

$intProfileID = $_GET['ID'];
?>
<tr>
   <td valign="top" style="text-align: center;" colspan="2">
      <input data-role="update_items_status" type="button" value="<?= $obPlugin::getMessage('RELOAD_STATUS') ?>"/>
   </td>
</tr>
<tr>
   <td valign="top" colspan="2" class="ozone-status-table" >

      <?
      //Helper::call($strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
      //OzonRuGeneral::ozonGetItems()
      //$arProfile['PARAMS']['FIRST_EXPORT_SYNC_ITEMS_DONE'] = OzonRuGeneral::ozonSyncItemsOnFirstExport($intProfileID);
      //$obOzonRuGeneral = new OzonRuGeneral($strModuleId);
      ?>

      <?= $obPlugin->ozoneItemsStatusTable($intProfileID, $strModuleId); ?>

   </td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams . '[EXPORT_PROMOCODES]');
