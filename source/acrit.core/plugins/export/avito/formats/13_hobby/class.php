<?

/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/hobbi_i_otdyh
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\EventManager,
    \Acrit\Core\Helper,
    \Acrit\Core\Xml,
    \Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoHobby extends Avito {

   CONST DATE_UPDATED = '2018-08-27';

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
      return parent::getCode() . '_HOBBY';
   }

   /**
    * Get plugin short name
    */
   public static function getName() {
      return static::getMessage('NAME');
   }

   /* END OF BASE STATIC METHODS */

   public function getDefaultExportFilename() {
      return 'avito_hobby.xml';
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
          'CODE' => 'GOODS_TYPE',
          'DISPLAY_CODE' => 'GoodsType',
          'NAME' => static::getMessage('FIELD_GOODS_TYPE_NAME'),
          'SORT' => 1000,
          'DESCRIPTION' => static::getMessage('FIELD_GOODS_TYPE_DESC'),
      ));
      $arResult[] = new Field(array(
          'CODE' => 'AD_TYPE',
          'DISPLAY_CODE' => 'AdType',
          'NAME' => static::getMessage('FIELD_AD_TYPE_NAME'),
          'SORT' => 1010,
          'DESCRIPTION' => static::getMessage('FIELD_AD_TYPE_DESC'),
          'REQUIRED' => true,
      ));
      $arResult[] = new Field(array(
          'CODE' => 'VEHICLE_TYPE',
          'DISPLAY_CODE' => 'VehicleType',
          'NAME' => static::getMessage('FIELD_VEHICLE_TYPE_NAME'),
          'SORT' => 1020,
          'DESCRIPTION' => static::getMessage('FIELD_VEHICLE_TYPE_DESC'),
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
      #
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
      if (!Helper::isEmpty($arFields['GOODS_TYPE']))
         $arXmlTags['GoodsType'] = Xml::addTag($arFields['GOODS_TYPE']);
      if (!Helper::isEmpty($arFields['AD_TYPE']))
         $arXmlTags['AdType'] = Xml::addTag($arFields['AD_TYPE']);
      if (!Helper::isEmpty($arFields['VEHICLE_TYPE']))
         $arXmlTags['VehicleType'] = Xml::addTag($arFields['VEHICLE_TYPE']);
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