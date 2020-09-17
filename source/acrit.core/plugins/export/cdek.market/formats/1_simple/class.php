<?
/**
 * Acrit Core: Cdek.Market plugin
 * @documentation https://docs.cdek.market/prodavcam/instrukcii/import-tovarov.html
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class CdekMarketSimple extends CdekMarket {
	
	CONST DATE_UPDATED = '2019-09-15';
    CONST CATEGORIES_FILENAME = 'categories.txt';
	//protected static $bSubclass = true;
	
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
		return parent::getCode().'_SIMPLE';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}

    /**
     *	Is it subclass?
     */
    public static function isSubclass(){
        return true;
    }

    public function getDefaultExportFilename(){
        return 'sdek_market.xml';
    }
	
	/* END OF BASE STATIC METHODS */

    /**
     *	Are categories export?
     */
    public function areCategoriesExport(){
        return true;
    }

    /**
     *	Is plugin works just with own categories of products
     */
    public function isCategoryStrict(){
        return true;
    }

    /**
     *	Is plugin has own categories (it is optional)
     */
    public function hasCategoryList(){
        return true;
    }


    /**
     *	Update categories from server
     */
    public function updateCategories($intProfileID){
        $strFileName = $this->getCategoriesCacheFile();
        return (is_file($strFileName) && filesize($strFileName));
    }

    /**
     *	Get categories date update
     */
    public function getCategoriesDate(){
        $strFileName = $this->getCategoriesCacheFile();
        return is_file($strFileName) ? filemtime($strFileName) : false;
    }

    /**
     *	Get categories list
     */
    public function getCategoriesList($intProfileID){
        $strFileName = $this->getCategoriesCacheFile();
        if(!is_file($strFileName) || !filesize($strFileName)) {
            $this->updateCategories($intProfileID);
        }
        if(is_file($strFileName) && filesize($strFileName)) {
            $strContents = file_get_contents($strFileName);
            if(!Helper::isUtf()){
                $strContents = Helper::convertEncoding($strContents, 'UTF-8', 'CP1251');
            }
            return explode("\n", $strContents);
        }
        return false;
    }

    /**
     *	Get filename for categories cache
     */
    protected function getCategoriesCacheFile(){
        $strCacheDir = __DIR__.'/cache';
        if(!is_dir($strCacheDir)){
            mkdir($strCacheDir, BX_DIR_PERMISSIONS, true);
        }
        return $strCacheDir.'/'.static::CATEGORIES_FILENAME;
    }

    /**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
        $arResult = array();
        $arResult[] = new Field(array(
            'CODE' => 'PRODUCT_CODE',
            'DISPLAY_CODE' => 'Product_code',
            'NAME' => static::getMessage('FIELD_PRODUCT_CODE_NAME'),
            'SORT' => 100,
            'DESCRIPTION' => static::getMessage('FIELD_PRODUCT_CODE_NAME'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'ID',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'PRODUCT_ID',
            'DISPLAY_CODE' => 'product_id',
            'NAME' => static::getMessage('FIELD_ID_NAME'),
            'SORT' => 300,
            'DESCRIPTION' => static::getMessage('FIELD_ID_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'ID',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LIST_PRICE',
            'DISPLAY_CODE' => 'List_price',
            'NAME' => static::getMessage('FIELD_LIST_PRICE_NAME'),
            'SORT' => 400,
            'DESCRIPTION' => static::getMessage('FIELD_LIST_PRICE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_RECOMMENDED_PRICE',
                ),
            ),
            'IS_PRICE' => true,
        ));
        $arResult[] = new Field(array(
            'CODE' => 'PRICE',
            'DISPLAY_CODE' => 'Price',
            'NAME' => static::getMessage('FIELD_PRICE_NAME'),
            'SORT' => 500,
            'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_PRICE',
                ),
            ),
            'IS_PRICE' => true,
        ));
        $arResult[] = new Field(array(
            'CODE' => 'QUANTITY',
            'DISPLAY_CODE' => 'Quantity',
            'NAME' => static::getMessage('FIELD_QUANTITY_NAME'),
            'SORT' => 600,
            'DESCRIPTION' => static::getMessage('FIELD_QUANTITY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_QUANTITY',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'WEIGHT',
            'DISPLAY_CODE' => 'weight',
            'NAME' => static::getMessage('FIELD_WEIGHT_NAME'),
            'SORT' => 700,
            'DESCRIPTION' => static::getMessage('FIELD_WEIGHT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_WEIGHT',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'MIN_QUANTITY',
            'DISPLAY_CODE' => 'min_quantity',
            'NAME' => static::getMessage('FIELD_MIN_QUANTITY_NAME'),
            'SORT' => 800,
            'DESCRIPTION' => static::getMessage('FIELD_MIN_QUANTITY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_MIN_QUANTITY',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'MAX_QUANTITY',
            'DISPLAY_CODE' => 'max_quantity',
            'NAME' => static::getMessage('FIELD_MAX_QUANTITY_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_MAX_QUANTITY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_MAX_QUANTITY',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'SHIPPING_FREIGHRT',
            'DISPLAY_CODE' => 'shipping_freight',
            'NAME' => static::getMessage('FIELD_SHIPPING_FREIGHRT_NAME'),
            'SORT' => 1000,
            'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_FREIGHRT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'CONST',
                    'CONST' => 'true',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'DATE_ADDED',
            'DISPLAY_CODE' => 'Date_added',
            'NAME' => static::getMessage('FIELD_DATE_ADDED_NAME'),
            'SORT' => 1100,
            'DESCRIPTION' => static::getMessage('FIELD_DATE_ADDED_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => (new \Bitrix\Main\Type\DateTime())->format('Y-m-d H:i'),
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'DOWNLOADABLE',
            'DISPLAY_CODE' => 'downloadable',
            'NAME' => static::getMessage('FIELD_DOWNLOADABLE_NAME'),
            'SORT' => 1200,
            'DESCRIPTION' => static::getMessage('FIELD_DOWNLOADABLE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'CONST',
                    'CONST' => 'false',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'FILES',
            'DISPLAY_CODE' => 'Files',
            'NAME' => static::getMessage('FIELD_FILES_NAME'),
            'SORT' => 1300,
            'DESCRIPTION' => static::getMessage('FIELD_FILES_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_FILES',
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_MORE_PHOTO',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'THUMBNAIL',
            'DISPLAY_CODE' => 'Thumbnail',
            'NAME' => static::getMessage('FIELD_THUMBNAIL_NAME'),
            'SORT' => 1400,
            'DESCRIPTION' => static::getMessage('FIELD_THUMBNAIL_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_THUMBNAIL',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'DETAILED_IMAGE',
            'DISPLAY_CODE' => 'Detailed_image',
            'NAME' => static::getMessage('FIELD_DETAILED_IMAGE_NAME'),
            'SORT' => 1500,
            'DESCRIPTION' => static::getMessage('FIELD_DETAILED_IMAGE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'DETAIL_PICTURE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'PRODUCT_NAME',
            'DISPLAY_CODE' => 'Product_name',
            'NAME' => static::getMessage('FIELD_PRODUCT_NAME_NAME'),
            'SORT' => 1600,
            'DESCRIPTION' => static::getMessage('FIELD_PRODUCT_NAME_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'NAME',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'DESCRIPTION',
            'DISPLAY_CODE' => 'Description',
            'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
            'SORT' => 1700,
            'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'CDATA' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'DETAIL_TEXT',
                    'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
                ),
            ),
            'PARAMS' => array('HTMLSPECIALCHARS' => 'cdata'),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'SHORT_DESCRIPTION',
            'DISPLAY_CODE' => 'Short_description',
            'NAME' => static::getMessage('FIELD_SHORT_DESCRIPTION_NAME'),
            'SORT' => 1800,
            'DESCRIPTION' => static::getMessage('FIELD_SHORT_DESCRIPTION_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'CDATA' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PREVIEW_TEXT',
                    'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
                ),
            ),
            'PARAMS' => array('HTMLSPECIALCHARS' => 'cdata'),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'META_KEYWORDS',
            'DISPLAY_CODE' => 'Meta_keywords',
            'NAME' => static::getMessage('FIELD_META_KEYWORDS_NAME'),
            'SORT' => 1900,
            'DESCRIPTION' => static::getMessage('FIELD_META_KEYWORDS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'META_KEYWORDS',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'META_DESCRIPTION',
            'DISPLAY_CODE' => 'Meta_description',
            'NAME' => static::getMessage('FIELD_META_DESCRIPTION_NAME'),
            'SORT' => 2000,
            'DESCRIPTION' => static::getMessage('FIELD_META_DESCRIPTION_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'META_DESCRIPTION',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'SEARCH_WORDS',
            'DISPLAY_CODE' => 'Search_words',
            'NAME' => static::getMessage('FIELD_SEARCH_WORDS_NAME'),
            'SORT' => 2100,
            'DESCRIPTION' => static::getMessage('FIELD_SEARCH_WORDS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_SEARCH_WORDS',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'PAGE_TITLE',
            'DISPLAY_CODE' => 'Page_title',
            'NAME' => static::getMessage('FIELD_PAGE_TITLE_NAME'),
            'SORT' => 2200,
            'DESCRIPTION' => static::getMessage('FIELD_PAGE_TITLE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_PAGE_TITLE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'PROMO_TEXT',
            'DISPLAY_CODE' => 'Promo_text',
            'NAME' => static::getMessage('FIELD_PROMO_TEXT_NAME'),
            'SORT' => 2300,
            'DESCRIPTION' => static::getMessage('FIELD_PROMO_TEXT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_PROMO_TEXT',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'TAXES',
            'DISPLAY_CODE' => 'Taxes',
            'NAME' => static::getMessage('FIELD_TAXES_NAME'),
            'SORT' => 2400,
            'DESCRIPTION' => static::getMessage('FIELD_TAXES_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_VAT_VALUE',
                    'PARAMS' => array(
                        'REPLACE' => array(
                            'from' => array('-', '18%', '10%', '0%'),
                            'to'   => array('NO_VAT', 'VAT_18', 'VAT_10', 'VAT_0'),
                            'use_regexp' => array('', '', '', ''),
                            'case_sensitive' => array('', '', '', ''),
                        ),
                    ),
                ),
            ),
        ));
        // todo
        $arResult[] = new Field(array(
            'CODE' => 'FEATURES',
            'DISPLAY_CODE' => 'Features',
            'NAME' => static::getMessage('FIELD_FEATURES_NAME'),
            'SORT' => 2500,
            'DESCRIPTION' => static::getMessage('FIELD_FEATURES_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_FEATURES',
                ),
            ),
        ));
        // todo
        $arResult[] = new Field(array(
            'CODE' => 'OPTIONS',
            'DISPLAY_CODE' => 'Options',
            'NAME' => static::getMessage('FIELD_OPTIONS_NAME'),
            'SORT' => 2600,
            'DESCRIPTION' => static::getMessage('FIELD_OPTIONS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_OPTIONS',
                ),
            ),
        ));

        $arResult[] = new Field(array(
            'CODE' => 'ITEMS_IN_BOX',
            'DISPLAY_CODE' => 'Items_in_box',
            'NAME' => static::getMessage('FIELD_ITEMS_IN_BOX_NAME'),
            'SORT' => 2700,
            'DESCRIPTION' => static::getMessage('FIELD_ITEMS_IN_BOX_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_ITEMS_IN_BOX',
                ),
            ),
        ));
        //todo
        $arResult[] = new Field(array(
            'CODE' => 'BOX_SIZE',
            'DISPLAY_CODE' => 'Box_size',
            'NAME' => static::getMessage('FIELD_BOX_SIZE_NAME'),
            'SORT' => 2800,
            'DESCRIPTION' => static::getMessage('FIELD_BOX_SIZE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_DIMENSIONS', # ToDo: add this to available fields! + settings support (1x2x3, or 1cm x 2cm x 3cm, or ..)
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'USERGROUP_ID',
            'DISPLAY_CODE' => 'Usergroup_ids',
            'NAME' => static::getMessage('FIELD_USERGROUP_ID_NAME'),
            'SORT' => 2900,
            'DESCRIPTION' => static::getMessage('FIELD_USERGROUP_ID_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'ID',
                ),
            ),
            'DEFAULT_VALUE_OFFERS' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PARENT.ID',
                    'PARAMS' => array('RAW' => 'Y'),
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'VENDOR',
            'DISPLAY_CODE' => 'Vendor',
            'NAME' => static::getMessage('FIELD_VENDOR_NAME'),
            'SORT' => 3000,
            'DESCRIPTION' => static::getMessage('FIELD_VENDOR_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BRAND',
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_MANUFACTURER',
                ),
            ),
        ));

		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]);
		foreach($arAdditionalFields as $arAdditionalField){
			$arDefaultValue = null;
			if(strlen($arAdditionalField['DEFAULT_FIELD'])){
				$arDefaultValue = array();
				$arDefaultValue[] = array(
					'TYPE' => 'FIELD',
					'VALUE' => $arAdditionalField['DEFAULT_FIELD'],
				);
			}
			$arResult[] = new Field(array(
				'ID' => IntVal($arAdditionalField['ID']),
				#'CODE' => AdditionalField::getFieldCode($arAdditionalField['ID']),
				'CODE' => Helper::call($this->strModuleId, 'AdditionalField', 'getFieldCode', [$arAdditionalField['ID']]), // ToDo: $strModuleId
				'NAME' => $arAdditionalField['NAME'],
				'SORT' => 1000000,
				'DESCRIPTION' => '',
				'REQUIRED' => false,
				'MULTIPLE' => true,
				'IS_ADDITIONAL' => true,
				'DEFAULT_VALUE' => $arDefaultValue,
			));
		}
		#
		unset($arAdditionalFields, $arAdditionalField);
		#
		$this->sortFields($arResult);
        return $arResult;
	}
	
	/**
	 *	Process single element (generate XML)
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		
		# Prepare data
		$bOffer = $arElement['IS_OFFER'];
		if($bOffer) {
			$arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		else {
			$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
        # Build XML
        $arXmlTags = array();
        if(!Helper::isEmpty($arFields['PRODUCT_CODE']))
            $arXmlTags['Product_code'] = Xml::addTag($arFields['PRODUCT_CODE']);
        $arXmlTags['Language'] = Xml::addTag(parent::getCdekLangs($arProfile['PARAMS']['LANGUAGES']));
        if(Helper::isEmpty($arFields['PRODUCT_CODE'])) {
            $arXmlTags['Product_id'] = Xml::addTag($arFields['PRODUCT_ID']);
        }
        $arXmlTags['Category'] = Xml::addTag($this->getXmlTag_Category($arProfile, $arElement,$arFields["CATEGORY"]));
         if(!Helper::isEmpty($arFields['LIST_PRICE']))
            $arXmlTags['List_price'] = Xml::addTag($arFields['LIST_PRICE']);
        if(!Helper::isEmpty($arFields['PRICE']))
            $arXmlTags['Price'] = Xml::addTag($arFields['PRICE']);
        if(!Helper::isEmpty($arFields['QUANTITY']))
            $arXmlTags['Quantity'] = Xml::addTag($arFields['QUANTITY']);
        if(!Helper::isEmpty($arFields['WEIGHT']))
            $arXmlTags['Weight'] = Xml::addTag($arFields['WEIGHT']);
        if(!Helper::isEmpty($arFields['MIN_QUANTITY']))
            $arXmlTags['Min_quantity'] = Xml::addTag($arFields['MIN_QUANTITY']);
        if(!Helper::isEmpty($arFields['MAX_QUANTITY']))
            $arXmlTags['Max_quantity'] = Xml::addTag($arFields['MAX_QUANTITY']);
        if(!Helper::isEmpty($arFields['SHIPPING_FREIGHRT']))
            $arXmlTags['Shipping_freight'] = Xml::addTag($arFields['SHIPPING_FREIGHRT']);
        if(!Helper::isEmpty($arFields['DATE_ADDED']))
            $arXmlTags['Date_added'] = Xml::addTag($arFields['DATE_ADDED']);
        if(!Helper::isEmpty($arFields['DOWNLOADABLE']))
            $arXmlTags['Downloadable'] = Xml::addTag($arFields['DOWNLOADABLE']);
        if(!Helper::isEmpty($arFields['FILES']))
            $arXmlTags['Files'] = Xml::addTag($arFields['FILES']);
        if(!Helper::isEmpty($arFields['THUMBNAIL']))
            $arXmlTags['Thumbnail'] = Xml::addTag($arFields['THUMBNAIL']);
        if(!Helper::isEmpty($arFields['DETAILED_IMAGE']))
            $arXmlTags['Detailed_image'] = Xml::addTag($arFields['DETAILED_IMAGE']);
        if(!Helper::isEmpty($arFields['PRODUCT_NAME']))
            $arXmlTags['Product_name'] = Xml::addTag($arFields['PRODUCT_NAME']);
        if(!Helper::isEmpty($arFields['DESCRIPTION']))
            $arXmlTags['Description'] = Xml::addTag($arFields['DESCRIPTION']);
        if(!Helper::isEmpty($arFields['SHORT_DESCRIPTION']))
            $arXmlTags['Short_description'] = Xml::addTag($arFields['SHORT_DESCRIPTION']);
        if(!Helper::isEmpty($arFields['META_KEYWORDS']))
            $arXmlTags['Meta_keywords'] = Xml::addTag($arFields['META_KEYWORDS']);
        if(!Helper::isEmpty($arFields['META_DESCRIPTION']))
            $arXmlTags['Meta_description'] = Xml::addTag($arFields['META_DESCRIPTION']);
        if(!Helper::isEmpty($arFields['SEARCH_WORDS']))
            $arXmlTags['Search_words'] = Xml::addTag($arFields['SEARCH_WORDS']);
        if(!Helper::isEmpty($arFields['PAGE_TITLE']))
            $arXmlTags['Page_title'] = Xml::addTag($arFields['PAGE_TITLE']);
        if(!Helper::isEmpty($arFields['PROMO_TEXT']))
            $arXmlTags['Promo_text'] = Xml::addTag($arFields['PROMO_TEXT']);
        if(!Helper::isEmpty($arFields['TAXES']))
            $arXmlTags['Taxes'] = Xml::addTag($arFields['TAXES']);
        if(!Helper::isEmpty($arFields['FEATURES']))
            $arXmlTags['Features'] = Xml::addTag($arFields['FEATURES']);
        if(!Helper::isEmpty($arFields['OPTIONS']))
            $arXmlTags['Options'] = Xml::addTag($arFields['OPTIONS']);
        if(!Helper::isEmpty($arFields['ITEMS_IN_BOX']))
            $arXmlTags['Items_in_box'] = Xml::addTag($arFields['ITEMS_IN_BOX']);
        if(!Helper::isEmpty($arFields['BOX_SIZE']))
            $arXmlTags['Box_size'] = Xml::addTag($arFields['BOX_SIZE']);
        //if(!Helper::isEmpty($arFields['USERGROUP_ID']))
        //    $arXmlTags['Usergroup_IDs'] = Xml::addTag($arFields['USERGROUP_ID']);
        if(!Helper::isEmpty($arFields['VENDOR']))
            $arXmlTags['Vendor'] = Xml::addTag($arFields['VENDOR']);

        $arXml = array(
            'offer' => array(
                '#' => $arXmlTags,
            ),
        );

		# Event handler OnYandexMarketXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCdekMarketXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}

		# Build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => Xml::arrayToXml($arXml),
			'SECTION_ID' => reset($arElementSections),
			'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
		);

		/*
		# Event handler OnYandexMarketResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexMarketResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		} 
		*/
		# Ending..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}

    protected function getXmlTag_Category($arProfile, $arElement, $mValue){
        if($arElement['IBLOCK_SECTION_ID']){
            $intCategoryID = $arElement['IBLOCK_SECTION_ID'];
        }
        elseif($arElement['PARENT']['IBLOCK_SECTION_ID']){
            $intCategoryID = $arElement['PARENT']['IBLOCK_SECTION_ID'];
        }
        $arCategoryRedefinitionsAll = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$arProfile['ID']]);
	    return str_replace("/","///",$arCategoryRedefinitionsAll[$intCategoryID]);
    }
}

?>