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

class CdekMarketYml extends CdekMarket {
	
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
		return parent::getCode().'_YML';
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
			'CODE' => 'ID',
			'DISPLAY_CODE' => 'id',
			'NAME' => static::getMessage('FIELD_ID_NAME'),
			'SORT' => 500,
			'DESCRIPTION' => static::getMessage('FIELD_ID_DESC'),
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
			'CODE' => 'NAME',
			'DISPLAY_CODE' => 'name',
			'NAME' => static::getMessage('FIELD_NAME_NAME'),
			'SORT' => 100,
			'DESCRIPTION' => static::getMessage('FIELD_NAME_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
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
			'CODE' => 'VENDOR',
			'DISPLAY_CODE' => 'vendor',
			'NAME' => static::getMessage('FIELD_VENDOR_NAME'),
			'SORT' => 120,
			'DESCRIPTION' => static::getMessage('FIELD_VENDOR_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MANUFACTURER',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'VENDOR_CODE',
			'DISPLAY_CODE' => 'vendorCode',
			'NAME' => static::getMessage('FIELD_VENDOR_CODE_NAME'),
			'SORT' => 130,
			'DESCRIPTION' => static::getMessage('FIELD_VENDOR_CODE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_ARTNUMBER',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'URL',
			'DISPLAY_CODE' => 'url',
			'NAME' => static::getMessage('FIELD_URL_NAME'),
			'SORT' => 900,
			'DESCRIPTION' => static::getMessage('FIELD_URL_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PAGE_URL',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => '512',
				'HTMLSPECIALCHARS' => 'escape',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'price',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1__WITH_DISCOUNT',
				),
			),
			'IS_PRICE' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'OLD_PRICE',
			'DISPLAY_CODE' => 'oldprice',
			'NAME' => static::getMessage('FIELD_OLD_PRICE_NAME'),
			'SORT' => 1100,
			'DESCRIPTION' => static::getMessage('FIELD_OLD_PRICE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1',
				),
			),
			'IS_PRICE' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CURRENCY_ID',
			'DISPLAY_CODE' => 'currencyId',
			'NAME' => static::getMessage('FIELD_CURRENCY_ID_NAME'),
			'SORT' => 1200,
			'DESCRIPTION' => static::getMessage('FIELD_CURRENCY_ID_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1__CURRENCY',
				),
			),
		));
//        <categoryId>101</categoryId>
		$arResult[] = new Field(array(
			'CODE' => 'PICTURE',
			'DISPLAY_CODE' => 'picture',
			'NAME' => static::getMessage('FIELD_PICTURE_NAME'),
			'SORT' => 1400,
			'DESCRIPTION' => static::getMessage('FIELD_PICTURE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
				),
			),
			'MAX_COUNT' => 10,
		));
		$arResult[] = new Field(array(
			'CODE' => 'STORE',
			'DISPLAY_CODE' => 'store',
			'NAME' => static::getMessage('FIELD_STORE_NAME'),
			'SORT' => 2100,
			'DESCRIPTION' => static::getMessage('FIELD_STORE_DESC'),
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
			'CODE' => 'PICKUP',
			'DISPLAY_CODE' => 'pickup',
			'NAME' => static::getMessage('FIELD_PICKUP_NAME'),
			'SORT' => 2000,
			'DESCRIPTION' => static::getMessage('FIELD_PICKUP_DESC'),
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
			'CODE' => 'DELIVERY',
			'DISPLAY_CODE' => 'delivery',
			'NAME' => static::getMessage('FIELD_DELIVERY_NAME'),
			'SORT' => 1600,
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_DESC'),
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
			'CODE' => 'DELIVERY_OPTIONS_COST',
			'DISPLAY_CODE' => 'delivery-options -> cost',
			'NAME' => static::getMessage('FIELD_DELIVERY_OPTIONS_COST_NAME'),
			'SORT' => 1700,
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_OPTIONS_COST_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '500',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY_OPTIONS_DAYS',
			'DISPLAY_CODE' => 'delivery-options -> days',
			'NAME' => static::getMessage('FIELD_DELIVERY_OPTIONS_DAYS_NAME'),
			'SORT' => 1800,
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_OPTIONS_DAYS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '2-4',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY_OPTIONS_ORDER_BEFORE',
			'DISPLAY_CODE' => 'delivery-options -> order-before',
			'NAME' => static::getMessage('FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_NAME'),
			'SORT' => 1900,
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '13',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DESCRIPTION',
			'DISPLAY_CODE' => 'description',
			'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
			'SORT' => 1500,
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
			'CODE' => 'SALES_NOTES',
			'DISPLAY_CODE' => 'sales_notes',
			'NAME' => static::getMessage('FIELD_SALES_NOTES_NAME'),
			'SORT' => 2400,
			'DESCRIPTION' => static::getMessage('FIELD_SALES_NOTES_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MANUFACTURER_WARRANTY',
			'DISPLAY_CODE' => 'manufacturer_warranty',
			'NAME' => static::getMessage('FIELD_MANUFACTURER_WARRANTY_NAME'),
			'SORT' => 2500,
			'DESCRIPTION' => static::getMessage('FIELD_MANUFACTURER_WARRANTY_DESC'),
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
			'CODE' => 'COUNTRY_OF_ORIGIN',
			'DISPLAY_CODE' => 'country_of_origin',
			'NAME' => static::getMessage('FIELD_COUNTRY_OF_ORIGIN_NAME'),
			'SORT' => 2600,
			'DESCRIPTION' => static::getMessage('FIELD_COUNTRY_OF_ORIGIN_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_COUNTRY',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BARCODE',
			'DISPLAY_CODE' => 'barcode',
			'NAME' => static::getMessage('FIELD_BARCODE_NAME'),
			'SORT' => 2800,
			'DESCRIPTION' => static::getMessage('FIELD_BARCODE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_BARCODE',
				),
			),
		));
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

		# Internal event handler
		$this->onBeforeProcessElement($arProfile, $intIBlockID, $arElement, $arFields);

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
		if(!Helper::isEmpty($arFields['URL']))
			$arXmlTags['url'] = $this->getXmlTag_Url($intProfileID, $arFields['URL'], $arFields);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['price'] = Xml::addTag($arFields['PRICE']);
		if(!Helper::isEmpty($arFields['OLD_PRICE']) && $arFields['OLD_PRICE'] != $arFields['PRICE'])
			$arXmlTags['oldprice'] = Xml::addTag($arFields['OLD_PRICE']);
		if(!Helper::isEmpty($arFields['CURRENCY_ID']))
			$arXmlTags['currencyId'] = Xml::addTag($arFields['CURRENCY_ID']);
		if(!Helper::isEmpty($arFields['VAT']))
			$arXmlTags['vat'] = $this->getXmlTag_Vat($intProfileID, $arFields['VAT'], $arFields);
		if(!Helper::isEmpty($arFields['ENABLE_AUTO_DISCOUNTS']))
			$arXmlTags['enable_auto_discounts'] = Xml::addTag($arFields['ENABLE_AUTO_DISCOUNTS']);
		$arXmlTags['categoryId'] = Xml::addTag(reset($arElementSections));
		if(!Helper::isEmpty($arFields['MARKET_CATEGORY']))
			$arXmlTags['market_category'] = Xml::addTag($arFields['MARKET_CATEGORY']);
		if(!Helper::isEmpty($arFields['PICTURE']))
			$arXmlTags['picture'] = Xml::addTag($arFields['PICTURE']);
		if(!Helper::isEmpty($arFields['STORE']))
			$arXmlTags['store'] = Xml::addTag($arFields['STORE']);
		if(!Helper::isEmpty($arFields['PICKUP']))
			$arXmlTags['pickup'] = Xml::addTag($arFields['PICKUP']);
		if(!Helper::isEmpty($arFields['PICKUP_OPTIONS_COST']) && !Helper::isEmpty($arFields['PICKUP_OPTIONS_DAYS']))
			$arXmlTags['pickup-options'] = $this->getXmlTag_PickupOptions($intProfileID, $arFields);
		if(!Helper::isEmpty($arFields['DELIVERY']))
			$arXmlTags['delivery'] = Xml::addTag($arFields['DELIVERY']);
		if(!Helper::isEmpty($arFields['DELIVERY_OPTIONS_COST']) && !Helper::isEmpty($arFields['DELIVERY_OPTIONS_DAYS']))
			$arXmlTags['delivery-options'] = $this->getXmlTag_DeliveryOptions($intProfileID, $arFields);
		if(!Helper::isEmpty($arFields['VENDOR']))
			$arXmlTags['vendor'] = Xml::addTag($arFields['VENDOR']);
		if(!Helper::isEmpty($arFields['NAME']))
			$arXmlTags['name'] = Xml::addTag($arFields['NAME']);
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['SALES_NOTES']))
			$arXmlTags['sales_notes'] = Xml::addTag($arFields['SALES_NOTES']);
		if(!Helper::isEmpty($arFields['MANUFACTURER_WARRANTY']))
			$arXmlTags['manufacturer_warranty'] = Xml::addTag($arFields['MANUFACTURER_WARRANTY']);
		if(!Helper::isEmpty($arFields['COUNTRY_OF_ORIGIN']))
			$arXmlTags['country_of_origin'] = Xml::addTag($arFields['COUNTRY_OF_ORIGIN']);
		if(!Helper::isEmpty($arFields['BARCODE']))
			$arXmlTags['barcode'] = $this->getXmlTag_Barcode($intProfileID, $arFields['BARCODE']);

		# Not in example
		if(!Helper::isEmpty($arFields['MODEL']))
			$arXmlTags['model'] = Xml::addTag($arFields['MODEL']);
		if(!Helper::isEmpty($arFields['VENDOR_CODE']))
			$arXmlTags['vendorCode'] = Xml::addTag($arFields['VENDOR_CODE']);
		if(!Helper::isEmpty($arFields['EXPIRY']))
			$arXmlTags['expiry'] = Xml::addTag($arFields['EXPIRY']);
		if(!Helper::isEmpty($arFields['AGE']))
			$arXmlTags['age'] = $this->getXmlTag_Age($intProfileID, $arFields['AGE']);
		if(!Helper::isEmpty($arFields['ADULT']))
			$arXmlTags['adult'] = Xml::addTag($arFields['ADULT']);
		if(!Helper::isEmpty($arFields['WEIGHT']))
			$arXmlTags['weight'] = Xml::addTag($arFields['WEIGHT']);
		if(!Helper::isEmpty($arFields['DIMENSIONS']))
			$arXmlTags['dimensions'] = Xml::addTag($arFields['DIMENSIONS']);
		if(!Helper::isEmpty($arFields['DOWNLOADABLE']))
			$arXmlTags['downloadable'] = Xml::addTag($arFields['DOWNLOADABLE']);

		# More
		if(!Helper::isEmpty($arFields['REC']))
			$arXmlTags['rec'] = Xml::addTag($arFields['REC']);
		if(!Helper::isEmpty($arFields['CREDIT_TEMPLATE_ID']))
			$arXmlTags['credit-template'] = $this->getXmlTag_CreditTemplate($arFields['CREDIT_TEMPLATE_ID']);
		$arXmlTags['condition'] = $this->getXmlTag_Condition($arFields['CONDITION_TYPE'], $arFields['CONDITION_REASON']);

		# Params
		$arXmlTags['param'] = $this->getXmlTag_Param($arProfile, $intIBlockID, $arFields);

		# Build XML
		$arXml = array(
			'offer' => array(
				'@' => $this->getXmlAttr($intProfileID, $arFields),
				'#' => $arXmlTags,
			),
		);

		# Internal event handler
		$this->onProcessElement($arProfile, $intIBlockID, $arElement, $arFields, $arXml);

		# Event handler OnYandexMarketXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexMarketXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}

		# More data
		$arDataMore = array();

		# Promos
//		$this->processElementPromos($arFields, $arDataMore);

		# Build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => Xml::arrayToXml($arXml),
			'CURRENCY' => $arFields['CURRENCY_ID'],
			'SECTION_ID' => reset($arElementSections),
			'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
			'DATA_MORE' => $arDataMore,
		);

		# Internal event handler
		$this->onAfterProcessElement($arProfile, $intIBlockID, $arElement, $arFields, $arResult);

		# Event handler OnYandexMarketResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexMarketResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}

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