<?

/**
 * Acrit Core: TiuRu plugin
 * @package acrit.core
 * @copyright 2019 Acrit
 */

namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper,
    \Acrit\Core\Xml,
    \Acrit\Core\HttpRequest,
    \Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter, 
    \PhpOffice\PhpSpreadsheet\Spreadsheet,
    \PhpOffice\PhpSpreadsheet\Writer\Xlsx,
    \PhpOffice\PhpSpreadsheet\IOFactory,
    \PhpOffice\PhpSpreadsheet\Cell\Coordinate;

Helper::loadMessages(__FILE__);

require_once realpath(__DIR__ . '/../../../yandex.market/class.php');
require_once realpath(__DIR__ . '/../../../yandex.market/formats/1_simple/class.php');

class TiuRuSimple extends YandexMarketSimple {

   CONST DATE_UPDATED = '2019-10-01';
   CONST CATEGORIES_XLS_URL = 'https://my.tiu.ru/cabinet/export_categories/xls';

   protected $bShopName = true;
   protected $bDelivery = true;
   protected $bEnableAutoDiscounts = false;
   protected $bPlatform = true;
   protected $bZip = false;
   protected $bPromoGift = false;
   protected $bPromoSpecialPrice = false;
   protected $bPromoCode = false;
   protected $bPromoNM = false;

   /**
    * Base constructor
    */
   public function __construct($strModuleId) {
      parent::__construct($strModuleId);
   }

   /* START OF BASE STATIC METHODS */

   /**
    * Get plugin unique code ([A-Z_]+)
    */
   public static function getCode() {
      return 'TIU_RU_SIMPLE';
   }

   /**
    * Get plugin short name
    */
   public static function getName() {
      return static::getMessage('NAME');
   }

   /**
    * 	Is it subclass?
    */
   public static function isSubclass() {
      return true;
   }

   /* END OF BASE STATIC METHODS */

   public function getDefaultExportFilename() {
      return 'tiu_ru_simple.xml';
   }

   /**
    * 	Get custom tabs for profile edit
    */
   public function getAdditionalTabs($intProfileID) {
      return array();
   }

   /**
    * 	Get adailable fields for current plugin
    */
   public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
      $arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
      #
      $arResult[] = new Field(array(
          'CODE' => 'SELLING_TYPE',
          'DISPLAY_CODE' => 'selling_type',
          'NAME' => static::getMessage('FIELD_SELLING_TYPE_NAME'),
          'SORT' => 100,
          'DESCRIPTION' => static::getMessage('FIELD_SELLING_TYPE_DESC'),
          'REQUIRED' => false,
          'MULTIPLE' => false,
      ));
      $arResult[] = new Field(array(
          'CODE' => 'PRICES_VALUE',
          'DISPLAY_CODE' => 'prices_value',
          'NAME' => static::getMessage('FIELD_PRICES_VALUE_NAME'),
          'SORT' => 200,
          'DESCRIPTION' => static::getMessage('FIELD_PRICES_VALUE_DESC'),
          'REQUIRED' => false,
          'MULTIPLE' => true,
          'PARAMS' => array(
              'MULTIPLE' => 'multiple',
          ),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'PRICES_QUANTITY',
          'DISPLAY_CODE' => 'prices_quantity',
          'NAME' => static::getMessage('FIELD_PRICES_QUANTITY_NAME'),
          'SORT' => 300,
          'DESCRIPTION' => static::getMessage('FIELD_PRICES_QUANTITY_DESC'),
          'REQUIRED' => false,
          'MULTIPLE' => true,
          'PARAMS' => array(
              'MULTIPLE' => 'multiple',
          ),
      ));

      $arResult[] = new Field(array(
          'CODE' => 'DISCOUNT',
          'DISPLAY_CODE' => 'discount',
          'NAME' => static::getMessage('FIELD_DISCOUNT_NAME'),
          'SORT' => 400,
          'DESCRIPTION' => static::getMessage('FIELD_DISCOUNT_DESC'),
          'REQUIRED' => false,
          'MULTIPLE' => false,
      ));
      $arResult[] = new Field(array(
          'CODE' => 'QUANTITY_IN_STOCK',
          'DISPLAY_CODE' => 'quantity_in_stock',
          'NAME' => static::getMessage('FIELD_QUANTITY_IN_STOCK_NAME'),
          'SORT' => 500,
          'DESCRIPTION' => static::getMessage('FIELD_QUANTITY_IN_STOCK_DESC'),
          'REQUIRED' => false,
          'MULTIPLE' => false,
          'DEFAULT_CONDITIONS' => Filter::getConditionsJson($this->strModuleId, $intIBlockID, array(
              array(
                  'FIELD' => 'CATALOG_QUANTITY',
                  'LOGIC' => 'MORE',
                  'VALUE' => '0',
              ),
          )),
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'CONST',
                  'CONST' => 'true',
                  'SUFFIX' => 'Y',
              ),
              array(
                  'TYPE' => 'CONST',
                  'CONST' => 'false',
                  'SUFFIX' => 'N',
              ),
          ),
      ));


      $this->sortFields($arResult);
      return $arResult;
   }

   protected function getXmlAttr($intProfileID, $arFields, $strType = false) {
      $arResult = parent::getXmlAttr($intProfileID, $arFields, $strType);
      if (!Helper::isEmpty($arFields['SELLING_TYPE'])) {
         $arResult['selling_type'] = $arFields['SELLING_TYPE'];
      }
      return $arResult;
   }

   protected function onProcessElement(&$arProfile, &$intIBlockID, &$arElement, &$arFields, &$mData) {
      if (!Helper::isEmpty($arFields['DISCOUNT']))
         $mData['offer']['#']['discount'] = Xml::addTag($arFields['DISCOUNT']);
      if (!Helper::isEmpty($arFields['PRICES_VALUE']))
         $mData['offer']['#']['prices'] = $this->getXmlTag_Prices($intProfileID, $arFields);
      if (!Helper::isEmpty($arFields['QUANTITY_IN_STOCK']))
         $mData['offer']['#']['quantity_in_stock'] = Xml::addTag($arFields['QUANTITY_IN_STOCK']);
   }

   protected function getXmlTag_Prices($intProfileID, $arFields) {
      $mValue = $arFields['PRICES_VALUE'];
      $mQuantity = $arFields['PRICES_QUANTITY'];

      #
      $mValue = is_array($mValue) ? $mValue : (!Helper::isEmpty($mValue) ? array($mValue) : array());
      $mQuantity = is_array($mQuantity) ? $mQuantity : (!Helper::isEmpty($mQuantity) ? array($mQuantity) : array());

      #
      $arPrice = array();
      foreach ($mValue as $key => $value) {
         $arPrice[] = array(
             '#' => array(
                 'value' => ['#' => $mValue[$key]],
                 'quantity' => ['#' => $mQuantity[$key]],
             ),
         );
      }
      if (!empty($arPrice)) {
         return array(
             array(
                 '#' => array(
                     'price' => $arPrice,
                 )
             ),
         );
      }
      return '';
   }

   /**
    * 	Update categories from server
    */
   public function updateCategories($intProfileID) {
      $bSuccess = false;
      file_get_contents($filename);
      //$strFileContent = file_get_contents(static::CATEGORIES_XLS_URL);

      $strFileContent = HttpRequest::get(static::CATEGORIES_XLS_URL, array('TIMEOUT' => 5));
      if (strlen($strFileContent)) {

         #$strTmpDir = Profile::getTmpDir($intProfileID);
         $strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
         $strTmpFile = $strTmpDir . '/' . pathinfo(static::CATEGORIES_XLS_URL, PATHINFO_BASENAME);
         if (file_put_contents($strTmpFile, $strFileContent)) {

            Helper::includePhpSpreadSheet();
            $obExcel = IOFactory::load($strTmpFile);
            $cells = $obExcel->getActiveSheet()->getCellCollection();

            $intRowCount = $cells->getHighestRow();

            #
            $strCategories = '';
            for ($intLine = 2; $intLine <= $intRowCount; $intLine++) {
               $strCategories .= $cells->get('F' . $intLine)->getValue() . ' - ' . $cells->get('A' . $intLine)->getValue();
               if ($cells->get('B' . $intLine)->getValue())
                  $strCategories .= ' / ' . $cells->get('B' . $intLine)->getValue();
               if ($cells->get('C' . $intLine)->getValue())
                  $strCategories .= ' / ' . $cells->get('C' . $intLine)->getValue();
               if ($cells->get('D' . $intLine)->getValue())
                  $strCategories .= ' / ' . $cells->get('D' . $intLine)->getValue();

               $strCategories .= "\n";
            }
            unset($obExcelData);
            $strCategories = trim($strCategories);
            if (strlen($strCategories)) {
               $strCategories = Helper::convertEncoding($strCategories, 'UTF-8', 'CP1251');
               $strFileName = $this->getCategoriesCacheFile();
               if (is_file($strFileName)) {
                  unlink($strFileName);
               }
               if (file_put_contents($strFileName, $strCategories)) {
                  $bSuccess = true;
               } else {
                  Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES', array('#FILE#' => $strFileName)), $intProfileID);
               }
            } else {
               Log::getInstance($this->strModuleId)->add(static::getMessage('CATEGORIES_ARE_EMPTY', array('#URL#' => static::CATEGORIES_XLS_URL)), $intProfileID);
            }
            @unlink($strTmpFile);
            unset($strCategories, $strFileContent);
         } else {
            Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES_TMP', array('#FILE#' => $strTmpFile)), $intProfileID);
         }
      } else {
         Log::getInstance($this->strModuleId)->add(static::getMessage('CATEGORIES_EMPTY_ANSWER', array('#URL#' => static::CATEGORIES_XLS_URL)), $intProfileID);
      }
      return $bSuccess;
   }

   protected function onGetCategoryTag(&$arCategoryTag, $intCategoryId, $arCategory, $intMode) {
      $id = false;
      if (strpos($arCategory['NAME'], ' - ') !== false) {
         $id = intval(substr($arCategory['NAME'], 0, strpos($arCategory['NAME'], ' - ')));
         $arCategoryTag['@']['portal_id'] = $id;
         $arCategoryTag['#'] = trim(substr($arCategory['NAME'], strpos($arCategory['NAME'], ' - ') + 3));
      }
   }

}

?>