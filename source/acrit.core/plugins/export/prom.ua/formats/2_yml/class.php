<?
/**
 * Acrit Core: Prom.ua plugin
 * @documentation http://support.prom.ua/documents/844
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

class PromUaYml extends PromUa {
	
	const DATE_UPDATED = '2019-10-10';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'prom_ua_yml.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8, self::CP1251];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'UAH', 'BYR', 'KZT', 'EUR', 'USD'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCategoriesUpdate = true;
	protected $bCurrenciesExport = true;
	protected $bCategoriesList = true;
	protected $strCategoriesUrl = 'http://my.prom.ua/cabinet/export_categories/xls';
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 3;
	
	# Other export settings
	protected $bZip = true;
	protected $arFieldsWithUtm = ['url'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@id'] = ['FIELD' => 'ID'];
		$arResult['@available'] = ['CONST' => 'true'];
		$arResult['@type'] = ['CONST' => 'vendor.model'];
		$arResult['@selling_type'] = ['CONST' => 'r'];
		$arResult['@group_id'] = ['FIELD' => 'ID'];
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y']];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['typePrefix'] = ['CONST' => ''];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID'];
		$arResult['portal_category_id'] = ['FIELD' => ''];
		$arResult['portal_category_url'] = ['FIELD' => ''];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true];
		$arResult['oldprice'] = ['FIELD' => 'CATALOG_PRICE_1', 'IS_PRICE' => true];
		$arResult['minimum_order_quantity'] = ['CONST' => '1'];
		$arResult['quantity_in_stock'] = ['FIELD' => 'CATALOG_QUANTITY'];
		$arResult['prices.price.value'] = ['CONST' => '', 'MULTIPLE' => true, 'IS_PRICE' => true];
		$arResult['prices.price.quantity'] = ['CONST' => '', 'MULTIPLE' => true];
		$arResult['discount'] = ['FIELD' => 'CATALOG_PRICE_1__DISCOUNT'];
		$arResult['currencyId'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'IS_CURRENCY' => true];
		$arResult['picture'] = ['FIELD' => 'DETAIL_PICTURE', 'MULTIPLE' => true, 'MAX_COUNT' => 10];
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER'];
		$arResult['vendorCode'] = ['FIELD' => 'PROPERTY_CML2_ARTICLE'];
		$arResult['model'] = ['FIELD' => 'PROPERTY_MODEL'];
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE'];
		$arResult['country'] = ['FIELD' => 'PROPERTY_COUNTRY'];
		$arResult['country_of_origin'] = ['FIELD' => 'PROPERTY_COUNTRY'];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true];
		#$arResult['available'] = ['CONST' => 'true'];
		$arResult['keywords'] = ['CONST' => ''];
		$arResult['delivery'] = ['CONST' => ''];
		$arResult['local_delivery_cost'] = ['CONST' => ''];
		$arResult['manufacturer_warranty'] = ['CONST' => ''];
		$arResult['downloadable'] = ['CONST' => ''];
		# Book
		$arResult['HEADER_BOOK'] = [];
		$arResult['author'] = ['CONST' => ''];
		$arResult['publisher'] = ['CONST' => ''];
		$arResult['series'] = ['CONST' => ''];
		$arResult['year'] = ['CONST' => ''];
		$arResult['ISBN'] = ['CONST' => ''];
		$arResult['volume'] = ['CONST' => ''];
		$arResult['part'] = ['CONST' => ''];
		$arResult['language'] = ['CONST' => ''];
		$arResult['binding'] = ['CONST' => ''];
		$arResult['page_extent'] = ['CONST' => ''];
		# Audiobook
		$arResult['HEADER_AUDIOBOOK'] = [];
		$arResult['performed_by'] = ['CONST' => ''];
		$arResult['performance_type'] = ['CONST' => ''];
		$arResult['storage'] = ['CONST' => ''];
		$arResult['format'] = ['CONST' => ''];
		$arResult['recording_length'] = ['CONST' => ''];
		# Media
		$arResult['HEADER_MEDIA'] = [];
		$arResult['artist'] = ['CONST' => ''];
		$arResult['title'] = ['CONST' => ''];
		$arResult['media'] = ['CONST' => ''];
		$arResult['starring'] = ['CONST' => ''];
		$arResult['director'] = ['CONST' => ''];
		$arResult['originalName'] = ['CONST' => ''];
		# Tours
		$arResult['HEADER_TOURS'] = [];
		$arResult['worldRegion'] = ['CONST' => ''];
		$arResult['region'] = ['CONST' => ''];
		$arResult['days'] = ['CONST' => ''];
		$arResult['dataTour'] = ['CONST' => ''];
		$arResult['hotel_stars'] = ['CONST' => ''];
		$arResult['room'] = ['CONST' => ''];
		$arResult['meal'] = ['CONST' => ''];
		$arResult['included'] = ['CONST' => ''];
		$arResult['transport'] = ['CONST' => ''];
		# Tickets
		$arResult['HEADER_TICKETS'] = [];
		$arResult['place'] = ['CONST' => ''];
		$arResult['hall'] = ['CONST' => ''];
		$arResult['hall@plan'] = ['CONST' => ''];
		$arResult['hall_part'] = ['CONST' => ''];
		$arResult['date'] = ['CONST' => ''];
		$arResult['is_premiere'] = ['CONST' => ''];
		$arResult['is_kids'] = ['CONST' => ''];
		#
		#
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.static::EOL;
		$strXml .= '<yml_catalog date="#XML_GENERATION_DATE#">'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
		$strXml .= '		<name>#SHOP_NAME#</name>'.static::EOL;
		$strXml .= '		<company>#SHOP_COMPANY#</company>'.static::EOL;
		$strXml .= '		<url>#SHOP_URL#</url>'.static::EOL;
		$strXml .= '		<currencies>'.static::EOL;
		$strXml .= '			#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '		</currencies>'.static::EOL;
		$strXml .= '		<categories>'.static::EOL;
		$strXml .= '			#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '		</categories>'.static::EOL;
		$strXml .= '		<offers>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</offers>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_GENERATION_DATE#' => date('c'),
			'#SHOP_NAME#' => $this->output($this->arParams['SHOP_NAME'], true),
			'#SHOP_COMPANY#' => $this->output($this->arParams['SHOP_COMPANY'], true),
			'#SHOP_URL#' => $this->output(Helper::siteUrl($this->arProfile['DOMAIN'], $his->arProfile['IS_HTTPS'] == 'Y'), 
				true),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}
	
	/**
	 *	Add option SHOP_NAME
	 */
	protected function onUpShowSettings(&$arSettings){
		$arSettings['SHOP_NAME'] = '<input type="text" name="PROFILE[PARAMS][SHOP_NAME]" size="40" 
			id="'.$this->getInputID('SHOP_NAME').'" data-role="shop-name"
			value="'.htmlspecialcharsbx($this->arParams['SHOP_NAME']).'" />';
		$arSettings['SHOP_COMPANY'] = '<input type="text" name="PROFILE[PARAMS][SHOP_COMPANY]" size="40" 
			id="'.$this->getInputID('SHOP_COMPANY').'" data-role="shop-company"
			value="'.htmlspecialcharsbx($this->arParams['SHOP_COMPANY']).'" />';
	}

}

?>