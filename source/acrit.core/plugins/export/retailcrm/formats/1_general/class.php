<?
/**
 * Acrit Core: Retailcrm.ru plugin
 * @documentation http://help.retailcrm.ru/Developers/ICML
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Xml,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class RetailCrmGeneral extends RetailCrm {
	
	CONST DATE_UPDATED = '2018-12-20';

  protected static $bSubclass = true;

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
        return parent::getCode().'_GENERAL';
    }
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported(){
		return true;
	}
	
	/**
	 *	Are categories export?
	 */
	public function areCategoriesExport(){
		return true;
	}
	
	/**
	 *	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict(){ // static ot not?
		return false;
	}
	
	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){ // static ot not?
		return true;
	}

    /* END OF BASE STATIC METHODS */

	
	/**
	 *	Get adailable fields for current plugin
	 */
    public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
        $arResult = array();
        if($bAdmin){
            $arResult[] = new Field(array(
                'SORT' => 499,
                'NAME' => static::getMessage('HEADER_GENERAL'),
                'IS_HEADER' => true,
            ));
        }

        $arResult[] = new Field(array(
            'CODE' => 'ACTIVE',
            'DISPLAY_CODE' => 'Active',
            'NAME' => static::getMessage('FIELD_ACTIVE'),
            'SORT' => 500,
            'DESCRIPTION' => static::getMessage('FIELD_ACTIVE_DESC'),
            'MULTIPLE' => false,
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
                'MAXLENGTH' => '2000',
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
            'CODE' => 'PURCHASE_PRICE',
            'DISPLAY_CODE' => 'purchasePrice',
            'NAME' => static::getMessage('FIELD_PURCHASEPRICE_NAME'),
            'SORT' => 1000,
            'DESCRIPTION' => static::getMessage('FIELD_PURCHASEPRICE_DESC'),
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
            'CODE' => 'CATEGORYID',
            'DISPLAY_CODE' => 'categoryId',
            'NAME' => static::getMessage('FIELD_CATEGORYID_NAME'),
            'SORT' => 500,
            'DESCRIPTION' => static::getMessage('FIELD_CATEGORYID_DESC'),
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
            'MAX_COUNT' => '10',
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
            'CODE' => 'PRODUCTNAME',
            'DISPLAY_CODE' => 'productName',
            'NAME' => static::getMessage('FIELD_PRODUCTNAME_NAME'),
            'SORT' => 100,
            'DESCRIPTION' => static::getMessage('FIELD_PRODUCTNAME_DESC'),
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
            'CODE' => 'XML_ID',
            'DISPLAY_CODE' => 'xml_id',
            'NAME' => static::getMessage('FIELD_XMLID_NAME'),
            'SORT' => 100,
            'DESCRIPTION' => static::getMessage('FIELD_XMLID_DESC'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'XML_ID',
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
            'SORT' => 110,
            'DESCRIPTION' => static::getMessage('FIELD_VENDOR_DESC'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_MANUFACTURER',
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BRAND',
                ),
            ),
            'DEFAULT_VALUE_OFFERS' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PARENT.PROPERTY_MANUFACTURER',
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PARENT.PROPERTY_BRAND',
                ),
            ),
						'PARAMS' => array(
							'MULTIPLE' => 'first',
						),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'VATRATE',
            'DISPLAY_CODE' => 'vatRate',
            'NAME' => static::getMessage('FIELD_VATRATE_NAME'),
            'SORT' => 110,
            'DESCRIPTION' => static::getMessage('FIELD_VATRATE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_VAT_VALUE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'WEIGHT',
            'DISPLAY_CODE' => 'weight',
            'NAME' => static::getMessage('FIELD_WEIGHT_NAME'),
            'SORT' => 3000,
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
            'CODE' => 'DIMENSIONS',
            'DISPLAY_CODE' => 'dimensions',
            'NAME' => static::getMessage('FIELD_DIMENSIONS_NAME'),
            'SORT' => 3100,
            'DESCRIPTION' => static::getMessage('FIELD_DIMENSIONS_DESC'),
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
            'CODE' => 'CATALOG_QUANTITY',
            'DISPLAY_CODE' => 'catalog_quantity',
            'NAME' => static::getMessage('CATALOG_QUANTITY_NAME'),
            'SORT' => 570,
            'DESCRIPTION' => static::getMessage('CATALOG_QUANTITY_DESC'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_QUANTITY',
                ),
            ),
        ));
				#
				if($bAdmin){
					$arResult[] = new Field(array(
						'SORT' => 3999,
						'NAME' => static::getMessage('HEADER_ADDITIONAL_FIELDS'),
						'IS_HEADER' => true,
					));
				}
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
						'CODE' => Helper::call($this->strModuleId, 'AdditionalField', 'getFieldCode', [$arAdditionalField['ID']]),
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
				# More fields are in each format (see getFields)
        #
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
            $parentId = $arElement["PARENT"]["ID"];
        }
        else {
            $arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
            $arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
            $parentId = $intElementID;
        }

        # Build XML
        $arXmlTags = array();
        if(!Helper::isEmpty($arFields['URL']))
            $arXmlTags['url'] = $this->getXmlTag_Url($intProfileID, $arFields['URL'], $arFields);
        if(!Helper::isEmpty($arFields['PRICE']))
            $arXmlTags['price'] = Xml::addTag($arFields['PRICE']);
        if(!Helper::isEmpty($arFields['PURCHASE_PRICE']) && $arFields['PURCHASE_PRICE'] != $arFields['PRICE'])
            $arXmlTags['purchasePrice'] = Xml::addTag($arFields['PURCHASE_PRICE']);
       $arXmlTags['categoryId'] = Xml::addTag(reset($arElementSections));
        if(!Helper::isEmpty($arFields['PICTURE']))
            $arXmlTags['picture'] = Xml::addTag($arFields['PICTURE']);
        if(!Helper::isEmpty($arFields['NAME']))
            $arXmlTags['name'] = Xml::addTag($arFields['NAME']);
        if(!Helper::isEmpty($arFields['XML_ID']))
            $arXmlTags['xmlId'] = Xml::addTag($arFields['XML_ID']);
        if(!Helper::isEmpty($arFields['PRODUCTNAME']))
            $arXmlTags['productName'] = Xml::addTag($arFields['PRODUCTNAME']);
        if(!Helper::isEmpty($arFields['VENDOR']))
            $arXmlTags['vendor'] = Xml::addTag($arFields['VENDOR']);
        if(!Helper::isEmpty($arFields['VATRATE']))
            $arXmlTags['vatRate'] = Xml::addTag($arFields['VATRATE']);
        if(!Helper::isEmpty($arFields['DIMENSIONS']))
            $arXmlTags['dimensions'] = Xml::addTag($arFields['DIMENSIONS']);
        if(!Helper::isEmpty($arFields['WEIGHT']))
            $arXmlTags['weight'] = $this->getXmlTag_Barcode($intProfileID, $arFields['WEIGHT']);
        if(!Helper::isEmpty($arFields['BARCODE']))
            $arXmlTags['barcode'] = $this->getXmlTag_Barcode($intProfileID, $arFields['BARCODE']);

        # Params
        $arXmlTags['param'] = $this->getXmlTag_Param($arProfile, $intIBlockID, $arFields);

        # Build XML
        $arXml = array(
            'offer' => array(
                '@' => $this->getXmlAttr($intProfileID, $arFields, $parentId),
                '#' => $arXmlTags,
            ),
        );

        # Event handler OnRetailCrmXml
        foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnRetailCrmXml') as $arHandler) {
            ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
        }

        # Build result
        $arResult = array(
            'TYPE' => 'XML',
            'DATA' => Xml::arrayToXml($arXml),
            'CURRENCY' => $arFields['CURRENCY_ID'],
            'SECTION_ID' => reset($arElementSections),
            'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
            'DATA_MORE' => array(),
        );

        # Event handler OnRetailCrmResult
        foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnRetailCrmResult') as $arHandler) {
            ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
        }

        # after..
        unset($intProfileID, $intElementID, $arXmlTags, $arXml);
        return $arResult;
    }


    /**
	 *	Get XML tag: <category>
	 *	” товара может быть основна€ категори€, котора€ не попадает в выгрузку, поэтому нужно чтобы лишн€€ категори€ не добавл€лась в <categories>
	 *	“еперь это перенесено в формат [$arXmlTags['categoryId'] = Xml::addTag(reset($arElementSections));]
     */
		/*
    protected function getXmlTag_Category($arProfile, $arElement){
        $intProfileID = $arProfile['ID'];
        $intCategoryID = 0;
        if($arElement['IBLOCK_SECTION_ID']){
            $intCategoryID = $arElement['IBLOCK_SECTION_ID'];
        }
        elseif($arElement['PARENT']['IBLOCK_SECTION_ID']){
            $intCategoryID = $arElement['PARENT']['IBLOCK_SECTION_ID'];
        }
        $arSectionsID = array();
        if($intCategoryID){
            $arSectionsID[] = $intCategoryID;
        }
        if(is_array($arElement['ADDITIONAL_SECTIONS'])){
            foreach($arElement['ADDITIONAL_SECTIONS'] as $intAdditionalSectionID) {
                $arSectionsID[] = $intAdditionalSectionID;
            }
        }
        $intIBlockID = $arElement['IBLOCK_ID'];
        $intIBlockOffersID = $arProfile['IBLOCKS'][$arElement['IBLOCK_ID']]['_CATALOG']['PRODUCT_IBLOCK_ID'];
        $arProfileSectionsID = array();
        if(!empty($arProfile['IBLOCKS'][$intIBlockID]['SECTIONS_ID_ARRAY'])){
            $arProfileSectionsID = &$arProfile['IBLOCKS'][$intIBlockID]['SECTIONS_ID_ARRAY'];
        }
        elseif(!empty($arProfile['IBLOCKS'][$intIBlockOffersID]['SECTIONS_ID_ARRAY'])){
            $arProfileSectionsID = &$arProfile['IBLOCKS'][$intIBlockOffersID]['SECTIONS_ID_ARRAY'];
        }
        foreach($arSectionsID as $intSectionID){
            if(in_array($intSectionID, $arProfileSectionsID)){
                $intCategoryID = $intSectionID;
                break;
            }
        }
        unset($arSectionsID, $intSectionID);
        return array('#' => $intCategoryID);
    }
		*/

    /**
     *	Get XML attributes
     */
    protected function getXmlAttr($intProfileID, $arFields, $parentId, $strType=false){
        $arResult = array(
            'id' => $arFields['ID'],
            'productId' => $parentId
        );
        if(!Helper::isEmpty($arFields['CATALOG_QUANTITY'])){
            $arResult['quantity'] = $arFields['CATALOG_QUANTITY'];
        }

        return $arResult;
    }

    /**
     *	Get XML tag: <barcode>
     */
    protected function getXmlTag_Barcode($intProfileID, $mValue){
        $mResult = '';
        $mValue = is_array($mValue) ? $mValue : array($mValue);
        if(!empty($mValue)){
            $mResult = array();
            foreach($mValue as $strPictureBarcode){
                $mResult[] = array('#' => $strPictureBarcode);
            }
        }
        return $mResult;
    }

    /**
     *	Get XML tag: <url>
     */
    protected function getXmlTag_Url($intProfileID, $mValue, $arFields){
        $strUrl = '';
        if(strlen($mValue)) {
            $strUrl = $mValue;
            $this->addUtmToUrl($strUrl, $arFields);
        }
        return array('#' => $strUrl);
    }

}

?>