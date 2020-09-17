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
$obTabControl->BeginCustomField($strPluginParams . '[LOAD_ATTR]', $obPlugin::getMessage('LOAD_ATTR'));
$intProfileID = $_GET['ID'];
if (!$arProfile['PARAMS']['OZON_LOAD_ATTR_STEP_SIZE'])
   $arProfile['PARAMS']['OZON_LOAD_ATTR_STEP_SIZE'] = 59;

$importParams = unserialize(\Bitrix\Main\Config\Option::get($strModuleId, 'OZON_LOAD_ATTR_' . $intProfileID));
?>
<tr>
   <td valign="top" style="text-align: center;" colspan="2">
      <?= $obPlugin::getMessage('OZON_LOAD_ATTR_TEXT'); ?>
   </td>
</tr>
<tr>
   <td valign="top" style="text-align: center;" colspan="2">
      <?= $obPlugin::getMessage('OZON_LOAD_ATTR_STEP_SIZE'); ?> <input  type="text" name="PROFILE[PARAMS][OZON_LOAD_ATTR_STEP_SIZE]"  value="<?= $arProfile['PARAMS']['OZON_LOAD_ATTR_STEP_SIZE'] ?>" /><br/><br/>
   </td>
</tr>
<tr><? $buttonTextLabel = ($importParams['time_first_run'] != '') ? $obPlugin::getMessage('LOAD_ATTR_CONTINUE') : $obPlugin::getMessage('LOAD_ATTR_START'); ?>
   <td valign="top" style="text-align: center;" colspan="2">
      <a href="javascript:void(0)" class="adm-btn run_load_attributes" title=""><?= $buttonTextLabel; ?></a>

   </td>
</tr>
<tr>
   <td valign="top" colspan="2" class="res_load_attributes" >



   </td>
</tr>
<?
$obTabControl->EndCustomField($strPluginParams . '[LOAD_ATTR]');
