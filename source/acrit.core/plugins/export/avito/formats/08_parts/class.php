<?

/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/zapchasti_i_aksessuary
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\EventManager,
    \Acrit\Core\Helper,
    \Acrit\Core\Xml,
    \Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoParts extends Avito {

   CONST DATE_UPDATED = '2019-02-20';

   protected static $bSubclass = true;

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
      return parent::getCode() . '_PARTS';
   }

   /**
    * Get plugin short name
    */
   public static function getName() {
      return static::getMessage('NAME');
   }

   /* END OF BASE STATIC METHODS */

   public function getDefaultExportFilename() {
      return 'avito_parts.xml';
   }

   /**
    * 	Get adailable fields for current plugin
    */
   public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
      $arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
      #
      $arResult[] = new Field(array(
          'CODE' => 'CONDITION',
          'DISPLAY_CODE' => 'Condition',
          'NAME' => static::getMessage('FIELD_CONDITION_NAME'),
          'SORT' => 360,
          'DESCRIPTION' => static::getMessage('FIELD_CONDITION_DESC'),
          'REQUIRED' => true,
      ));
      $arResult[] = new Field(array(
          'CODE' => 'OEM',
          'DISPLAY_CODE' => 'OEM',
          'NAME' => static::getMessage('FIELD_OEM_NAME'),
          'SORT' => 362,
          'DESCRIPTION' => static::getMessage('FIELD_OEM_DESC'),
          'REQUIRED' => false,
      ));
      $this->modifyField($arResult, 'CATEGORY', array(
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'CONST',
                  'CONST' => static::getMessage('FIELD_CATEGORY_DEFAULT'),
              ),
          ),
      ));

      $arResult[] = new Field(array(
          'CODE' => 'TYPE_ID',
          'DISPLAY_CODE' => 'TypeId',
          'NAME' => static::getMessage('FIELD_TYPE_ID_NAME'),
          'SORT' => 1000,
          'DESCRIPTION' => static::getMessage('FIELD_TYPE_ID_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'AD_TYPE',
          'DISPLAY_CODE' => 'AdType',
          'NAME' => static::getMessage('FIELD_AD_TYPE_NAME'),
          'SORT' => 1010,
          'DESCRIPTION' => static::getMessage('FIELD_AD_TYPE_DESC'),
      ));
      if ($bAdmin) {
         $arResult[] = new Field(array(
             'SORT' => 1020,
             'NAME' => static::getMessage('HEADER_TIRES'),
             'IS_HEADER' => true,
         ));
      }
      $arResult[] = new Field(array(
          'CODE' => 'RIM_DIAMETER',
          'DISPLAY_CODE' => 'RimDiameter',
          'NAME' => static::getMessage('FIELD_RIM_DIAMETER_NAME'),
          'SORT' => 1030,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_DIAMETER_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'TIRE_TYPE',
          'DISPLAY_CODE' => 'TireType',
          'NAME' => static::getMessage('FIELD_TIRE_TYPE_NAME'),
          'SORT' => 1040,
          'DESCRIPTION' => static::getMessage('FIELD_TIRE_TYPE_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'WHEEL_AXLE',
          'DISPLAY_CODE' => 'WheelAxle',
          'NAME' => static::getMessage('FIELD_WHEEL_AXLE_NAME'),
          'SORT' => 1050,
          'DESCRIPTION' => static::getMessage('FIELD_WHEEL_AXLE_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_TYPE',
          'DISPLAY_CODE' => 'RimType',
          'NAME' => static::getMessage('FIELD_RIM_TYPE_NAME'),
          'SORT' => 1060,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_TYPE_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'TIRE_SECTION_WIDTH',
          'DISPLAY_CODE' => 'TireSectionWidth',
          'NAME' => static::getMessage('FIELD_TIRE_SECTION_WIDTH_NAME'),
          'SORT' => 1070,
          'DESCRIPTION' => static::getMessage('FIELD_TIRE_SECTION_WIDTH_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'TIRE_ASPECT_RATIO',
          'DISPLAY_CODE' => 'TireAspectRatio',
          'NAME' => static::getMessage('FIELD_TIRE_ASPECT_RATIO_NAME'),
          'SORT' => 1080,
          'DESCRIPTION' => static::getMessage('FIELD_TIRE_ASPECT_RATIO_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_WIDTH',
          'DISPLAY_CODE' => 'RimWidth',
          'NAME' => static::getMessage('FIELD_RIM_WIDTH_NAME'),
          'SORT' => 1090,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_WIDTH_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_BOLTS',
          'DISPLAY_CODE' => 'RimBolts',
          'NAME' => static::getMessage('FIELD_RIM_BOLTS_NAME'),
          'SORT' => 1100,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_BOLTS_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_BOLTS_DIAMETER',
          'DISPLAY_CODE' => 'RimBoltsDiameter',
          'NAME' => static::getMessage('FIELD_RIM_BOLTS_DIAMETER_NAME'),
          'SORT' => 1110,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_BOLTS_DIAMETER_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'RIM_OFFSET',
          'DISPLAY_CODE' => 'RimOffset',
          'NAME' => static::getMessage('FIELD_RIM_OFFSET_NAME'),
          'SORT' => 1120,
          'DESCRIPTION' => static::getMessage('FIELD_RIM_OFFSET_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'BRAND',
          'DISPLAY_CODE' => 'Brand',
          'NAME' => static::getMessage('FIELD_BRAND_NAME'),
          'SORT' => 1130,
          'DESCRIPTION' => static::getMessage('FIELD_BRAND_DESC'),
      ));
      #
      $this->sortFields($arResult);
      return $arResult;
   }

   /**
    * 	Process single element (generate XML)
    * 	@return array
    */
   public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
      $intProfileID = $arProfile['ID'];
      $intElementID = $arElement['ID'];
      # Build XML
      $arXmlTags = array(
          'Id' => array('#' => $arFields['ID']),
      );
      if (!Helper::isEmpty($arFields['DATE_BEGIN']))
         $arXmlTags['DateBegin'] = Xml::addTag($arFields['DATE_BEGIN']);
      if (!Helper::isEmpty($arFields['DATE_END']))
         $arXmlTags['DateEnd'] = Xml::addTag($arFields['DATE_END']);
      if (!Helper::isEmpty($arFields['LISTING_FEE']))
         $arXmlTags['ListingFee'] = Xml::addTag($arFields['LISTING_FEE']);
      if (!Helper::isEmpty($arFields['AD_STATUS']))
         $arXmlTags['AdStatus'] = Xml::addTag($arFields['AD_STATUS']);
      if (!Helper::isEmpty($arFields['AVITO_ID']))
         $arXmlTags['AvitoId'] = Xml::addTag($arFields['AVITO_ID']);
      #
      if (!Helper::isEmpty($arFields['ALLOW_EMAIL']))
         $arXmlTags['AllowEmail'] = Xml::addTag($arFields['ALLOW_EMAIL']);
      if (!Helper::isEmpty($arFields['MANAGER_NAME']))
         $arXmlTags['ManagerName'] = Xml::addTag($arFields['MANAGER_NAME']);
      if (!Helper::isEmpty($arFields['CONTACT_PHONE']))
         $arXmlTags['ContactPhone'] = Xml::addTag($arFields['CONTACT_PHONE']);
      #
      if (!Helper::isEmpty($arFields['DESCRIPTION']))
         $arXmlTags['Description'] = Xml::addTag($arFields['DESCRIPTION']);
      if (!Helper::isEmpty($arFields['IMAGES']))
         $arXmlTags['Images'] = $this->getXmlTag_Images($arFields['IMAGES']);
      if (!Helper::isEmpty($arFields['VIDEO_URL']))
         $arXmlTags['VideoURL'] = Xml::addTag($arFields['VIDEO_URL']);
      if (!Helper::isEmpty($arFields['TITLE']))
         $arXmlTags['Title'] = Xml::addTag($arFields['TITLE']);
      if (!Helper::isEmpty($arFields['PRICE']))
         $arXmlTags['Price'] = Xml::addTag($arFields['PRICE']);
      if (!Helper::isEmpty($arFields['CONDITION']))
         $arXmlTags['Condition'] = Xml::addTag($arFields['CONDITION']);
      if (!Helper::isEmpty($arFields['OEM']))
         $arXmlTags['OEM'] = Xml::addTag($arFields['OEM']);
      #
			if(!Helper::isEmpty($arFields['LATITUDE']) || !Helper::isEmpty($arFields['LONGITUDE'])) {
				$arXmlTags['Latitude'] = Xml::addTag($arFields['LATITUDE']);
				$arXmlTags['Longitude'] = Xml::addTag($arFields['LONGITUDE']);
			}
      if (!Helper::isEmpty($arFields['ADDRESS']))
         $arXmlTags['Address'] = Xml::addTag($arFields['ADDRESS']);
      if (!Helper::isEmpty($arFields['REGION']))
         $arXmlTags['Region'] = Xml::addTag($arFields['REGION']);
      if (!Helper::isEmpty($arFields['CITY']))
         $arXmlTags['City'] = Xml::addTag($arFields['CITY']);
      if (!Helper::isEmpty($arFields['SUBWAY']))
         $arXmlTags['Subway'] = Xml::addTag($arFields['SUBWAY']);
      if (!Helper::isEmpty($arFields['DISTRICT']))
         $arXmlTags['District'] = Xml::addTag($arFields['DISTRICT']);
      #
      if (!Helper::isEmpty($arFields['CATEGORY']))
         $arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
      if (!Helper::isEmpty($arFields['TYPE_ID']))
         $arXmlTags['TypeId'] = Xml::addTag($arFields['TYPE_ID']);
      if (!Helper::isEmpty($arFields['AD_TYPE']))
         $arXmlTags['AdType'] = Xml::addTag($arFields['AD_TYPE']);
      #
      if (!Helper::isEmpty($arFields['RIM_DIAMETER']))
         $arXmlTags['RimDiameter'] = Xml::addTag($arFields['RIM_DIAMETER']);
      if (!Helper::isEmpty($arFields['TIRE_TYPE']))
         $arXmlTags['TireType'] = Xml::addTag($arFields['TIRE_TYPE']);
      if (!Helper::isEmpty($arFields['WHEEL_AXLE']))
         $arXmlTags['WheelAxle'] = Xml::addTag($arFields['WHEEL_AXLE']);
      if (!Helper::isEmpty($arFields['RIM_TYPE']))
         $arXmlTags['RimType'] = Xml::addTag($arFields['RIM_TYPE']);
      if (!Helper::isEmpty($arFields['TIRE_SECTION_WIDTH']))
         $arXmlTags['TireSectionWidth'] = Xml::addTag($arFields['TIRE_SECTION_WIDTH']);
      if (!Helper::isEmpty($arFields['TIRE_ASPECT_RATIO']))
         $arXmlTags['TireAspectRatio'] = Xml::addTag($arFields['TIRE_ASPECT_RATIO']);
      if (!Helper::isEmpty($arFields['RIM_WIDTH']))
         $arXmlTags['RimWidth'] = Xml::addTag($arFields['RIM_WIDTH']);
      if (!Helper::isEmpty($arFields['RIM_BOLTS']))
         $arXmlTags['RimBolts'] = Xml::addTag($arFields['RIM_BOLTS']);
      if (!Helper::isEmpty($arFields['RIM_BOLTS_DIAMETER']))
         $arXmlTags['RimBoltsDiameter'] = Xml::addTag($arFields['RIM_BOLTS_DIAMETER']);
      if (!Helper::isEmpty($arFields['RIM_OFFSET']))
         $arXmlTags['RimOffset'] = Xml::addTag($arFields['RIM_OFFSET']);
      if (!Helper::isEmpty($arFields['BRAND']))
         $arXmlTags['Brand'] = Xml::addTag($arFields['BRAND']);
      # build XML
      $arXml = array(
          'Ad' => array(
              '#' => $arXmlTags,
          ),
      );
      foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoXml') as $arHandler) {
         ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
      }
      $strXml = Xml::arrayToXml($arXml);
      # build result
      $arResult = array(
          'TYPE' => 'XML',
          'DATA' => $strXml,
          'CURRENCY' => '',
          'SECTION_ID' => static::getElement_SectionID($intProfileID, $arElement),
          'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
          'DATA_MORE' => array(),
      );
      foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoResult') as $arHandler) {
         ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
      }
      # after..
      unset($intProfileID, $intElementID, $arXmlTags, $arXml);
      return $arResult;
   }

}

?>