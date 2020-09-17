<?
/**
 * Acrit Core: Lengow.com plugin
 * @documentation https://support.lengow.com/hc/en-us/articles/360007020232-Products-Catalogue
 */

namespace Acrit\Core\Export\Plugins;

class LengowComXml extends LengowCom {
	
	const DATE_UPDATED = '2019-10-28';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'lengow_com.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'EUR', 'USD', 'UAH', 'KZT'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	
	# XML settings
	protected $strXmlItemElement = 'product';
	protected $intXmlDepthItems = 0;
	
	# Other export settings
	protected $arFieldsWithUtm = ['product_URL'];
	protected $bAllCData = true;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['HEADER_GENERAL'] = [];
		$arResult['product_id'] = ['FIELD' => 'ID'];
		$arResult['title'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y']];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true];
		$arResult['price_including_tax'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true];
		$arResult['pricenorebate'] = ['FIELD' => 'CATALOG_PRICE_1', 'IS_PRICE' => true];
		$arResult['sale_price'] = ['FIELD' => 'CATALOG_PRICE_1', 'IS_PRICE' => true];
		$arResult['category'] = ['FIELD' => '__IBLOCK_SECTION_CHAIN', 'FIELD_PARAMS' => [
			'MULTIPLE' => 'join',
			'MULTIPLE_separator' => 'other',
			'MULTIPLE_separator_other' => ' &gt; ',
		]];
		$arResult['sub_category1'] = ['FIELD' => 'SECTION__NAME'];
		$arResult['sub_category2'] = ['FIELD' => 'SECTION__NAME'];
		$arResult['sub_category3'] = ['FIELD' => 'SECTION__NAME'];
		$arResult['product_URL'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['image_URL'] = ['FIELD' => 'DETAIL_PICTURE'];
		$arResult['EAN'] = ['FIELD' => 'PROPERTY_EAN'];
		$arResult['MPN'] = ['FIELD' => 'PROPERTY_MPN'];
		$arResult['brand'] = ['FIELD' => 'PROPERTY_MANUFACTURER'];
		$arResult['delivery_costs'] = ['FIELD' => ''];
		$arResult['delivery_description'] = ['FIELD' => ''];
		$arResult['quantity_in_stock'] = ['FIELD' => 'CATALOG_QUANTITY'];
		$arResult['Availability'] = ['FIELD' => 'CATALOG_QUANTITY'];
		$arResult['Warranty'] = ['CONST' => '3'];
		$arResult['size'] = ['FIELD' => 'PROPERTY_SIZE'];
		$arResult['colour'] = ['FIELD' => 'PROPERTY_COLOR'];
		$arResult['material'] = ['FIELD' => 'PROPERTY_MATERIAL'];
		$arResult['gender'] = ['FIELD' => 'PROPERTY_GENDER'];
		$arResult['weight'] = ['FIELD' => 'CATALOG_WEIGHT'];
		$arResult['condition'] = ['CONST' => 'new'];
		$arResult['sales'] = ['CONST' => '1'];
		$arResult['promo_texte'] = ['CONST' => ''];
		$arResult['promo_percentage'] = ['CONST' => '1'];
		$arResult['start_date_promo'] = ['CONST' => ''];
		$arResult['end_date_promo'] = ['CONST' => ''];
		$arResult['currency'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'IS_CURRENCY' => true];
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '#XML_ITEMS#'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}

}

?>