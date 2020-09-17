<?
/**
 * Acrit Core: Avito base plugin
 * @documentation http://autoload.avito.ru/format/
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper,
    \Acrit\Core\Export\Exporter,
    \Acrit\Core\Export\Plugin,
    \Acrit\Core\Export\Field\Field,
    \Acrit\Core\Export\ExportDataTable as ExportData,
    \Acrit\Core\HttpRequest,
    \Acrit\Core\Export\Filter,
    \Acrit\Core\Export\CurrencyConverter\Base as CurrencyConverterBase,
    \Acrit\Core\Log,
    \Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class Avito extends Plugin {

   CONST DATE_UPDATED = '2018-12-10';
   CONST CATEGORIES_PARSE_URL = 'https://www.avito.ru/map';
   CONST CATEGORIES_PARSE_NODE = '';
   CONST CATEGORIES_FILENAME = 'categories.txt';

   protected $strFileExt;

   /**
    * Base constructor.
    */
   public function __construct($strModuleId) {
      parent::__construct($strModuleId);
   }

   /* START OF BASE STATIC METHODS */

   /**
    * Get plugin unique code ([A-Z_]+)
    */
   public static function getCode() {
      return 'AVITO';
   }

   /**
    * Get plugin short name
    */
   public static function getName() {
      return static::getMessage('NAME');
   }

   /**
    * 	Get list of supported currencies
    */
   public function getSupportedCurrencies() {
      return array('RUB');
   }

   /**
    * 	Are categories export?
    */
   public function areCategoriesExport() {
      return true;
   }

   /**
    * 	Is plugin works just with own categories of products
    */
   public function isCategoryStrict() {
      return false;
   }

   /**
    * 	Is plugin has own categories (it is optional)
    */
   public function hasCategoryList() {
      return false;
   }

   /**
    * 	Get all categories (/a/b/c, /d/e/f, ...)
    */
   public function getCategoriesList($intProfileID) {
      return false;
   }

   /**
    *
    */
   public function isStepByStepMode() {
      return true;
   }

   /* END OF BASE STATIC METHODS */

   public function getDefaultExportFilename() {
      return 'avito.xml';
   }

   /**
    * 	Set available extension
    */
   protected function setAvailableExtension($strExtension) {
      $this->strFileExt = $strExtension;
   }

   /**
    * 	Show plugin settings
    */
   public function showSettings() {
      $this->setAvailableExtension('xml');
      return $this->showDefaultSettings();
   }

   /**
    * 	Show plugin default settings
    */
   protected function showDefaultSettings() {
      ob_start();
      ?>
      <table class="acrit-exp-plugin-settings" style="width:100%;">
         <tbody>
            <tr>
               <td width="40%" class="adm-detail-content-cell-l">
                  <?= Helper::ShowHint(static::getMessage('SETTINGS_FILE_HINT')); ?>
                  <b><?= static::getMessage('SETTINGS_FILE'); ?>:</b>
               </td>
               <td width="60%" class="adm-detail-content-cell-r">
                  <?
                  \CAdminFileDialog::ShowScript(Array(
                      'event' => 'AcritExpPluginXmlFilenameSelect',
                      'arResultDest' => array('FUNCTION_NAME' => 'acrit_exp_plugin_xml_filename_select'),
                      'arPath' => array(),
                      'select' => 'F',
                      'operation' => 'S',
                      'showUploadTab' => true,
                      'showAddToMenuTab' => false,
                      'fileFilter' => $this->strFileExt,
                      'allowAllFiles' => true,
                      'saveConfig' => true,
                  ));
                  ?>
                  <script>
                     function acrit_exp_plugin_xml_filename_select(File, Path, Site) {
                        var FilePath = Path + '/' + File;
                        $('#acrit_exp_plugin_xml_filename').val(FilePath);
                     }
                  </script>
                  <table class="acrit-exp-plugin-settings-fileselect">
                     <tbody>
                        <tr>
                           <td><input type="text" name="PROFILE[PARAMS][EXPORT_FILE_NAME]"
                                      id="acrit_exp_plugin_xml_filename" data-role="export-file-name"
                                      value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['EXPORT_FILE_NAME']); ?>" size="40"
                                      placeholder="<?= static::getMessage('SETTINGS_FILE_PLACEHOLDER'); ?>" /></td>
                           <td><input type="button" value="..." onclick="AcritExpPluginXmlFilenameSelect()" /></td>
                           <td>
                              &nbsp;
                              <?= $this->showFileOpenLink(); ?>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
      <?= $this->showStepByStepSettings(); ?>
      <?
      return ob_get_clean();
   }

   /**
    * 	Get adailable fields for current plugin
    */
   public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
      $arResult = array();
      if ($bAdmin) {
         $arResult[] = new Field(array(
             'SORT' => 99,
             'NAME' => static::getMessage('HEADER_GENERAL'),
             'IS_HEADER' => true,
         ));
      }
      $arResult[] = new Field(array(
          'CODE' => 'ID',
          'DISPLAY_CODE' => 'Id',
          'NAME' => static::getMessage('FIELD_ID_NAME'),
          'SORT' => 100,
          'DESCRIPTION' => static::getMessage('FIELD_ID_DESC'),
          'REQUIRED' => true,
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'ID',
              ),
          ),
          'PARAMS' => array(
              'MAXLENGTH' => '100',
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'DATE_BEGIN',
          'DISPLAY_CODE' => 'DateBegin',
          'NAME' => static::getMessage('FIELD_DATE_BEGIN_NAME'),
          'SORT' => 110,
          'DESCRIPTION' => static::getMessage('FIELD_DATE_BEGIN_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'ACTIVE_FROM',
                  'PARAMS' => array(
                      'DATEFORMAT' => 'Y',
                      'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
                      'DATEFORMAT_to' => 'Y-m-d\TH:i:sP',
                  ),
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'DATE_END',
          'DISPLAY_CODE' => 'DateEnd',
          'NAME' => static::getMessage('FIELD_DATE_END_NAME'),
          'SORT' => 120,
          'DESCRIPTION' => static::getMessage('FIELD_DATE_END_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'ACTIVE_TO',
                  'PARAMS' => array(
                      'DATEFORMAT' => 'Y',
                      'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
                      'DATEFORMAT_to' => 'Y-m-d\TH:i:sP',
                  ),
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'LISTING_FEE',
          'DISPLAY_CODE' => 'ListingFee',
          'NAME' => static::getMessage('FIELD_LISTING_FEE_NAME'),
          'SORT' => 130,
          'DESCRIPTION' => static::getMessage('FIELD_LISTING_FEE_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'CONST',
                  'CONST' => 'Package',
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'AD_STATUS',
          'DISPLAY_CODE' => 'AdStatus',
          'NAME' => static::getMessage('FIELD_AD_STATUS_NAME'),
          'SORT' => 140,
          'DESCRIPTION' => static::getMessage('FIELD_AD_STATUS_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'CONST',
                  'CONST' => 'Free',
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'AVITO_ID',
          'DISPLAY_CODE' => 'AvitoId',
          'NAME' => static::getMessage('FIELD_AVITO_ID_NAME'),
          'SORT' => 150,
          'DESCRIPTION' => static::getMessage('FIELD_AVITO_ID_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'ALLOW_EMAIL',
          'DISPLAY_CODE' => 'AllowEmail',
          'NAME' => static::getMessage('FIELD_ALLOW_EMAIL_NAME'),
          'SORT' => 200,
          'DESCRIPTION' => static::getMessage('FIELD_ALLOW_EMAIL_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'CONST',
                  'CONST' => static::getMessage('FIELD_ALLOW_EMAIL_DEFAULT'),
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'MANAGER_NAME',
          'DISPLAY_CODE' => 'ManagerName',
          'NAME' => static::getMessage('FIELD_MANAGER_NAME_NAME'),
          'SORT' => 210,
          'DESCRIPTION' => static::getMessage('FIELD_MANAGER_NAME_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'CONST',
                  'CONST' => '',
              ),
          ),
          'PARAMS' => array(
              'MAXLENGTH' => '100',
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'CONTACT_PHONE',
          'DISPLAY_CODE' => 'ContactPhone',
          'NAME' => static::getMessage('FIELD_CONTACT_PHONE_NAME'),
          'SORT' => 220,
          'DESCRIPTION' => static::getMessage('FIELD_CONTACT_PHONE_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'CONST',
                  'CONST' => '',
              ),
          ),
      ));
      #
      $arResult[] = new Field(array(
          'CODE' => 'DESCRIPTION',
          'DISPLAY_CODE' => 'Description',
          'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
          'SORT' => 300,
          'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
          'REQUIRED' => true,
          'CDATA' => true,
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'DETAIL_TEXT',
                  'PARAMS' => array(
                      'HTML2TEXT' => 'Y',
                  ),
              ),
          ),
          'PARAMS' => array(
              'MAXLENGTH' => '3000',
              'HTMLSPECIALCHARS' => 'escape',
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'IMAGES',
          'DISPLAY_CODE' => 'Images',
          'NAME' => static::getMessage('FIELD_IMAGES_NAME'),
          'SORT' => 320,
          'DESCRIPTION' => static::getMessage('FIELD_IMAGES_DESC'),
          'MULTIPLE' => true,
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'DETAIL_PICTURE',
              ),
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'PROPERTY_MORE_PHOTO',
                  'PARAMS' => array(
                      'MULTIPLE' => 'multiple',
                  ),
              ),
          ),
          'PARAMS' => array(
              'MULTIPLE' => 'multiple',
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'VIDEO_URL',
          'DISPLAY_CODE' => 'VideoURL',
          'NAME' => static::getMessage('FIELD_VIDEO_URL_NAME'),
          'SORT' => 330,
          'DESCRIPTION' => static::getMessage('FIELD_VIDEO_URL_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'PROPERTY_YOUTUBE',
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'TITLE',
          'DISPLAY_CODE' => 'Title',
          'NAME' => static::getMessage('FIELD_TITLE_NAME'),
          'SORT' => 340,
          'DESCRIPTION' => static::getMessage('FIELD_TITLE_DESC'),
          'REQUIRED' => true,
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'NAME',
              ),
          ),
          'PARAMS' => array(
              'HTMLSPECIALCHARS' => 'escape',
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'PRICE',
          'DISPLAY_CODE' => 'Price',
          'NAME' => static::getMessage('FIELD_PRICE_NAME'),
          'SORT' => 350,
          'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'CATALOG_PRICE_1__WITH_DISCOUNT',
              ),
          ),
          'IS_PRICE' => true,
      ));
      #
      if ($bAdmin) {
         $arResult[] = new Field(array(
             'SORT' => 499,
             'NAME' => static::getMessage('HEADER_LOCATION'),
             'IS_HEADER' => true,
         ));
      }
      $arResult[] = new Field(array(
          'CODE' => 'LATITUDE',
          'DISPLAY_CODE' => 'Latitude',
          'NAME' => static::getMessage('FIELD_LATITUDE_NAME'),
          'SORT' => 505,
          'DESCRIPTION' => static::getMessage('FIELD_LATITUDE_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'PROPERTY_LATITUDE',
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'LONGITUDE',
          'DISPLAY_CODE' => 'Longitude',
          'NAME' => static::getMessage('FIELD_LONGITUDE_NAME'),
          'SORT' => 506,
          'DESCRIPTION' => static::getMessage('FIELD_LONGITUDE_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'PROPERTY_LONGITUDE',
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'ADDRESS',
          'DISPLAY_CODE' => 'Address',
          'NAME' => static::getMessage('FIELD_ADDRESS_NAME'),
          'SORT' => 500,
          'DESCRIPTION' => static::getMessage('FIELD_ADDRESS_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'PROPERTY_ADDRESS',
              ),
          ),
          'PARAMS' => array(
              'MAXLENGTH' => '256',
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'REGION',
          'DISPLAY_CODE' => 'Region',
          'NAME' => static::getMessage('FIELD_REGION_NAME'),
          'SORT' => 510,
          'DESCRIPTION' => static::getMessage('FIELD_REGION_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'PROPERTY_REGION',
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'CITY',
          'DISPLAY_CODE' => 'City',
          'NAME' => static::getMessage('FIELD_CITY_NAME'),
          'SORT' => 520,
          'DESCRIPTION' => static::getMessage('FIELD_CITY_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'PROPERTY_CITY',
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'SUBWAY',
          'DISPLAY_CODE' => 'Subway',
          'NAME' => static::getMessage('FIELD_SUBWAY_NAME'),
          'SORT' => 530,
          'DESCRIPTION' => static::getMessage('FIELD_SUBWAY_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'PROPERTY_CITY',
              ),
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'DISTRICT',
          'DISPLAY_CODE' => 'District',
          'NAME' => static::getMessage('FIELD_DISTRICT_NAME'),
          'SORT' => 540,
          'DESCRIPTION' => static::getMessage('FIELD_DISTRICT_DESC'),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'PROPERTY_DISTRICT',
              ),
          ),
      ));
      #
      if ($bAdmin) {
         $arResult[] = new Field(array(
             'SORT' => 899,
             'NAME' => static::getMessage('HEADER_CHARACTERISTICS'),
             'IS_HEADER' => true,
         ));
      }
      $arResult[] = new Field(array(
          'CODE' => 'CATEGORY',
          'DISPLAY_CODE' => 'Category',
          'NAME' => static::getMessage('FIELD_CATEGORY_NAME'),
          'SORT' => 900,
          'DESCRIPTION' => static::getMessage('FIELD_CATEGORY_DESC'),
          'REQUIRED' => true,
      ));

      # More fields are in each format (see getFields)
      return $arResult;
   }

   /**
    * 	Process single element
    * 	@return array
    */
   public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
      // basically [in this class] do nothing, all business logic are in each format
   }

   /**
    * 	Show results
    */
   public function showResults($arSession) {
      ob_start();
      $intTime = $arSession['TIME_FINISHED'] - $arSession['TIME_START'];
      if ($intTime <= 0) {
         $intTime = 1;
      }
      ?>
      <div><?= static::getMessage('RESULT_GENERATED'); ?>: <?= IntVal($arSession['GENERATE']['INDEX']); ?></div>
      <div><?= static::getMessage('RESULT_EXPORTED'); ?>: <?= IntVal($arSession['EXPORT']['INDEX']); ?></div>
      <div><?= static::getMessage('RESULT_ELAPSED_TIME'); ?>: <?= Helper::formatElapsedTime($intTime); ?></div>
      <div><?= static::getMessage('RESULT_DATETIME'); ?>: <?= (new \Bitrix\Main\Type\DateTime())->toString(); ?></div>
      <? if ($this->arProfile['PARAMS']['STEP_BY_STEP'] == 'Y' && $arSession['EXPORT']['STEP_INDEX'] > 0): ?>
         <div><?= static::getMessage('RESULT_STEP'); ?>: <?= $arSession['EXPORT']['STEP_INDEX']; ?></div>
      <? endif ?>
      <?= $this->showFileOpenLink(); ?>
      <?
      return Helper::showSuccess(ob_get_clean());
   }

   /* START OF BASE METHODS FOR XML SUBCLASSES */

   /**
    * 	Get steps
    */
   public function getSteps() {
      $arResult = array();
      $arResult['CHECK'] = array(
          'NAME' => static::getMessage('ACRIT_EXP_EXPORTER_STEP_CHECK'),
          'SORT' => 10,
          #'FUNC' => __CLASS__.'::stepCheck',
          'FUNC' => array($this, 'stepCheck'),
      );
      $arResult['EXPORT'] = array(
          'NAME' => static::getMessage('STEP_EXPORT'),
          'SORT' => 100,
          #'FUNC' => __CLASS__.'::stepExport',
          'FUNC' => array($this, 'stepExport'),
      );
      return $arResult;
   }

   /**
    * 	Step: Check input params and data
    */
   public function stepCheck($intProfileID, $arData) {
      $strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
      if (!strlen($strExportFilename)) {
         Log::getInstance($this->strModuleId)->add(static::getMessage('NO_EXPORT_FILE_SPECIFIED'), $intProfileID);
         print static::getMessage('NO_EXPORT_FILE_SPECIFIED');
         return Exporter::RESULT_ERROR;
      }
      return Exporter::RESULT_SUCCESS;
   }

   /**
    * 	Step: Export
    */
   public function stepExport($intProfileID, $arData) {
      $arSession = &$arData['SESSION']['EXPORT'];
      $bIsCron = $arData['IS_CRON'];
      #
      $strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
      #
      if (!isset($arSession['XML_FILE'])) {
         #$strTmpDir = Profile::getTmpDir($intProfileID);
         $strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
         $strTmpFile = pathinfo($strExportFilename, PATHINFO_BASENAME) . '.tmp';
         $arSession['XML_FILE_URL'] = $strExportFilename;
         $arSession['XML_FILE'] = $_SERVER['DOCUMENT_ROOT'] . $strExportFilename;
         $arSession['XML_FILE_TMP'] = $strTmpDir . '/' . $strTmpFile;
         #
         if (is_file($arSession['XML_FILE_TMP'])) {
            unlink($arSession['XML_FILE_TMP']);
         }
         touch($arSession['XML_FILE_TMP']);
         unset($strTmpDir, $strTmpFile);
      }

      # SubStep1 [header]
      if (!isset($arSession['XML_HEADER_WROTE'])) {
         $this->stepExport_writeXmlHeader($intProfileID, $arData);
         $arSession['XML_HEADER_WROTE'] = true;
      }

      # SubStep2 [each <offer>]
      if (!isset($arSession['XML_OFFERS_WROTE'])) {
         $this->stepExport_writeXmlOffers($intProfileID, $arData);
         $arSession['XML_OFFERS_WROTE'] = true;
      }

      # SubStep3 [footer]
      if (!isset($arSession['XML_FOOTER_WROTE'])) {
         $this->stepExport_writeXmlFooter($intProfileID, $arData);
         $arSession['XML_FOOTER_WROTE'] = true;
      }

      # SubStep4 [tmp => real]
      if (is_file($arSession['XML_FILE'])) {
         unlink($arSession['XML_FILE']);
      }
      if (!Helper::createDirectoriesForFile($arSession['XML_FILE'])) {
         $strMessage = Loc::getMessage('ACRIT_EXP_ERROR_CREATE_DIRECORY', array(
                     '#DIR#' => Helper::getDirectoryForFile($arSession['XML_FILE']),
         ));
         Log::getInstance($this->strModuleId)->add($strMessage);
         print Helper::showError($strMessage);
         return Exporter::RESULT_ERROR;
      }
      if (is_file($arSession['XML_FILE'])) {
         @unlink($arSession['XML_FILE']);
      }
      if (!@rename($arSession['XML_FILE_TMP'], $arSession['XML_FILE'])) {
         @unlink($arSession['XML_FILE_TMP']);
         $strMessage = Loc::getMessage('ACRIT_EXP_FILE_NO_PERMISSIONS', array(
                     '#FILE#' => $arSession['XML_FILE'],
         ));
         Log::getInstance($this->strModuleId)->add($strMessage);
         print Helper::showError($strMessage);
         return Exporter::RESULT_ERROR;
      }

      # SubStep8
      $arSession['EXPORT_FILE_SIZE_XML'] = filesize($arSession['XML_FILE']);

      #
      return Exporter::RESULT_SUCCESS;
   }

   /**
    * 	Step: Export, write header
    */
   protected function stepExport_writeXmlHeader($intProfileID, $arData) {
      $strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
      #
      $strDate = new \Bitrix\Main\Type\DateTime(null, 'Y-m-d H:i');
      $strXml = '';
      $strXml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
      $strXml .= '<Ads formatVersion="3" target="Avito.ru">' . "\n";
      file_put_contents($strFile, $strXml, FILE_APPEND);
   }

   /**
    * 	Step: Export, write offers
    * 	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
    */
   protected function stepExport_writeXmlOffers($intProfileID, $arData) {
      $strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
      # For step export
      $bStepExport = $arData['PROFILE']['PARAMS']['STEP_BY_STEP'] == 'Y';
      $intCountPerStep = IntVal($arData['PROFILE']['PARAMS']['STEP_BY_STEP_COUNT']);
      if ($intCountPerStep < 1) {
         $bStepExport = false;
      }
      $arLastExportedItem = unserialize($arData['PROFILE']['LAST_EXPORTED_ITEM']);
      if (!is_array($arLastExportedItem)) {
         $arLastExportedItem = array();
      }
      $intExportedByStep = 0;
      $bStepExportFoundFirstItem = false;
      #
      $intOffset = 0;
      $intLimit = 5000;
      $strSortOrder = ToUpper($arData['PROFILE']['PARAMS']['SORT_ORDER']);
      if (!in_array($strSortOrder, array('ASC', 'DESC'))) {
         $strSortOrder = 'ASC';
      }
      while (true) {
         $arFilter = array(
             'PROFILE_ID' => $intProfileID,
             '!TYPE' => ExportData::TYPE_DUMMY,
         );
         if ($bStepExport && isset($arLastExportedItem['SORT']) && isset($arLastExportedItem['ID'])) { # For step export: we are not needing for past sort values
            $arFilter['>=SORT'] = $arLastExportedItem['SORT'];
         }
         $arOrder = array(
             'SORT' => $strSortOrder,
             'ID' => 'ASC',
         );
         $arSelect = array(
             'IBLOCK_ID',
             'ELEMENT_ID',
             'SECTION_ID',
             'TYPE',
             'DATA',
             # For step export
             'SORT',
             'ID'
         );
         $arQuery = [
             'filter' => $arFilter,
             'order' => $arOrder,
             'select' => $arSelect,
             'limit' => $intLimit,
             'offset' => $intOffset * $intLimit,
         ];
         #$resItems = ExportData::getList($arQuery);
         $resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
         $strXml = '';
         $intCount = 0;
         $bBreakedByStepCount = false;
         while ($arItem = $resItems->fetch()) {
            $intCount++;
            if ($bStepExport && isset($arLastExportedItem['SORT']) && isset($arLastExportedItem['ID'])) { # For step export
               if (!$bStepExportFoundFirstItem) {
                  $bStepExportFoundFirstItem = $arItem['ID'] > $arLastExportedItem['ID'];
               }
               if (!$bStepExportFoundFirstItem) {
                  continue;
               }
            }
            $arItem['DATA'] = rtrim(Xml::addOffset($arItem['DATA'], 1)) . "\n";
            $strXml .= $arItem['DATA'];
            $intExportedByStep++;
            if ($intExportedByStep == $intCountPerStep) {  # For step export
               $intStepIndex = IntVal($arLastExportedItem['STEP']) + 1;
               $this->setLastExportedItem($arData['PROFILE']['ID'], array(
                   'SORT' => $arItem['SORT'],
                   'ID' => $arItem['ID'],
                       ), $intStepIndex);
               $arData['SESSION']['EXPORT']['STEP_INDEX'] = $intStepIndex;
               $bBreakedByStepCount = true;
               break;
            }
         }
         if (!Helper::isUtf()) {
            $strXml = Helper::convertEncoding($strXml, 'CP1251', 'UTF-8');
         }
         $arData['SESSION']['EXPORT']['INDEX'] += $intExportedByStep;
         file_put_contents($strFile, $strXml, FILE_APPEND);
         if ($bBreakedByStepCount) { # For step export
            break;
         }
         # Finishing..
         if ($intCount < $intLimit) {
            if ($bStepExport) { # For step export, todo: wrap to method
               $this->setLastExportedItem($arData['PROFILE']['ID'], false);
            }
            break;
         }
         $intOffset++;
      }
   }

   /**
    * 	Step: Export, write offers footer
    */
   protected function stepExport_writeXmlOffersFooter($intProfileID, $arData) {
      $strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
      #
      $strXml = '';
      $strXml .= "\t\t" . '</offers>' . "\n";
      file_put_contents($strFile, $strXml, FILE_APPEND);
   }

   /**
    * 	Step: Export, write footer
    */
   protected function stepExport_writeXmlFooter($intProfileID, $arData) {
      $strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
      #
      $strXml = '';
      $strXml .= '</Ads>' . "\n";
      file_put_contents($strFile, $strXml, FILE_APPEND);
   }

   /* HELPERS FOR SIMILAR XML-TYPES */

   /**
    * 	Get XML tag: <picture>
    */
   protected function getXmlTag_Images($arImages) {
      return Xml::addTagWithSubtags($arImages, 'Image', function($arValues, $arParams) {
                 if (!is_array($arValues)) {
                    $arValues = array($arValues);
                 }
                 $arResult = array();
                 foreach ($arValues as $strValue) {
                    $arResult[] = array(
                        '@' => array('url' => $strValue),
                    );
                 }
                 return $arResult;
              }, array());
   }

   /* END OF BASE METHODS FOR XML SUBCLASSES */
}
?>