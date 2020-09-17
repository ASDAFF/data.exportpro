<?

/**
 * Acrit Core: youla.ru plugin
 * @documentation https://docs.google.com/document/d/1flyFODQ1UGy6pKh5jwi0-yuNz2SzqkeZsNwEvD1zbmU/edit#
 */

namespace Acrit\Core\Export\Plugins;

class YoulaRuYml extends YoulaRu {

   const DATE_UPDATED = '2019-10-28';

   protected static $bSubclass = true;

   # General
   protected $strDefaultFilename = 'youla_ru_yml.xml';
   protected $arSupportedFormats = ['XML'];
   protected $arSupportedEncoding = [self::UTF8];
   protected $strFileExt = 'xml';
   protected $arSupportedCurrencies = ['RUB', 'UAH', 'BYR', 'KZT', 'EUR', 'USD'];

   # Basic settings
   protected $bAdditionalFields = false;
   protected $bCategoriesExport = true;
   protected $bCategoriesUpdate = false;
   protected $bCurrenciesExport = false;
   protected $bCategoriesList = true;
   protected $bHideCategoriesUpdateButton = true;
   protected $bCategoriesStrict = true;


   # XML settings
   protected $strXmlItemElement = 'offer';
   protected $intXmlDepthItems = 3;

   # Other export settings
   protected $bZip = true;

   /**
    * 	Get available fields for current plugin
    */
   public function getUniversalFields($intProfileID, $intIBlockID) {
      $arResult = [];
      $arResult['HEADER_GENERAL'] = [];
      $arResult['@id'] = ['FIELD' => 'ID'];

      $arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];


      $arResult['address'] = ['CONST' => ''];
      $arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
      $arResult['phone'] = ['CONST' => ''];
      $arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y']];
      $arResult['managerName'] = ['CONST' => ''];
      $arResult['picture'] = ['FIELD' => 'DETAIL_PICTURE', 'MULTIPLE' => true, 'MAX_COUNT' => 10];
      $arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true];
      $arResult['HEADER_ADITIONAL'] = [];


      self::makeReplaceStructure($arResult);

      #
      #
      return $arResult;
   }

   protected function onUpBeforeProcessElement(&$arResult, &$arElement, &$arFields, &$arElementSections, $intMainIBlockId) {
      $elementSection = reset($arElementSections);

      $categoryName = $this->getCategoryRedefinitionName($elementSection);
      $categoryId = $this->getCategoriesIdByName($categoryName, $intProfileID);
      $parentCategoryId = $this->getCategoriesParentIdById($categoryId, $intProfileID);

      $arFields['youlaCategoryId'] = $parentCategoryId;
      $arFields['youlaSubcategoryId'] = $categoryId;
   }

   static protected function makeReplaceStructure(&$arResult) {
      require __DIR__ . '/../../include/replace_structure.php';

      foreach ($arReplacedFields as $fieldName => $arReplace) {
         $allowedValues = '';
         foreach ($arReplace as $id => $name) {
            $allowedValues .= $id . ' - ' . $name . '<br>';
         }
         $arResult[$fieldName] = ['ALLOWED_VALUES' => $allowedValues, 'PARAMS' =>
             ['REPLACE' => ['from' => array_values($arReplace), 'to' => array_keys($arReplace),]
         ]];
      }
   }

   /**
    * 	Build main xml structure
    */
   protected function onUpGetXmlStructure(&$strXml) {
      # Build xml
      $strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>' . static::EOL;
      $strXml .= '<yml_catalog date="#XML_GENERATION_DATE#">' . static::EOL;
      $strXml .= '	<shop>' . static::EOL;
      $strXml .= '		<offers>' . static::EOL;
      $strXml .= '			#XML_ITEMS#' . static::EOL;
      $strXml .= '		</offers>' . static::EOL;
      $strXml .= '	</shop>' . static::EOL;
      $strXml .= '</yml_catalog>' . static::EOL;
      # Replace macros
      $arReplace = [
          '#XML_GENERATION_DATE#' => date('c'),
          '#XML_ENCODING#' => $this->arParams['ENCODING'],
      ];
      $strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
   }

}

?>