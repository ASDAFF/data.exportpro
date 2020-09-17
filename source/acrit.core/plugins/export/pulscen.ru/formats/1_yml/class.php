<?
/**
 * Acrit Core: pulscen.ru plugin
 * @documentation hhttps://www.pulscen.ru/about/site/import-yml
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

class PulscenRuYml extends PulscenRu {
	
	const DATE_UPDATED = '2019-10-28';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'pulscen_ru.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'RUR', 'USD', 'UAH', 'KZT'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCategoriesUpdate = false;
	protected $bCurrenciesExport = true;
	protected $bCategoriesList = false;
	
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
		$arResult['@id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['@available'] = ['CONST' => 'true'];
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y']];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['announce'] = ['FIELD' => 'PREVIEW_TEXT', 'PARAMS' => ['MAXLENGTH' => 'Y', 'MAXLENGTH_value' => 255, 'HTML2TEXT' => 'Y', 'HTML2TEXT_mode' => 'simple']];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'FIELD_PARAMS' => ['HTMLSPECIALCHARS' => 'skip'], 'PARAMS' => ['MAXLENGTH' => 'Y', 'MAXLENGTH_value' => 20000]];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID', 'REQUIRED' => true];
		$arResult['picture'] = ['FIELD' => 'DETAIL_PICTURE', 'MULTIPLE' => true, 'MAX_COUNT' => 5];
		$arResult['typePrefix'] = ['FIELD' => 'SECTION__NAME'];
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER'];
		$arResult['vendorCode'] = ['FIELD' => 'PROPERTY_CML2_ARTICLE'];
		$arResult['model'] = ['FIELD' => 'PROPERTY_MODEL'];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
		$arResult['oldprice'] = ['FIELD' => 'CATALOG_PRICE_1'];
		$arResult['discount_expires_at'] = ['FIELD' => ''];
		$arResult['price_min'] = ['FIELD' => ''];
		$arResult['price_max'] = ['FIELD' => ''];
		$arResult['currencyId'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'REQUIRED' => true, 'IS_CURRENCY' => true];
		$arResult['rubricId'] = ['FIELD' => '', 'PARAMS' => ['MAXLENGTH' => 'Y', 'MAXLENGTH_value' => 1000]];
		$arResult['measure_unit'] = ['FIELD' => 'CATALOG_MEASURE_UNIT'];
		$arResult['min_qty'] = ['FIELD' => ''];
		$arResult['qty_measure_unit'] = ['FIELD' => ''];
		$arResult['sales_notes'] = ['FIELD' => '', 'PARAMS' => ['MAXLENGTH' => 'Y', 'MAXLENGTH_value' => 75]];
		$arResult['delivery'] = ['FIELD' => ''];
		$arResult['local_delivery_cost'] = ['FIELD' => ''];
		$arResult['delivery_field'] = ['FIELD' => '', 'PARAMS' => ['MAXLENGTH' => 'Y', 'MAXLENGTH_value' => 75]];
		$arResult['wholesale_price'] = ['FIELD' => ''];
		$arResult['wholesale_price_min'] = ['FIELD' => ''];
		$arResult['wholesale_currency'] = ['FIELD' => ''];
		$arResult['wholesale_measure_unit'] = ['FIELD' => ''];
		$arResult['wholesale_min_qty'] = ['FIELD' => ''];
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.static::EOL;
		$strXml .= '<yml_catalog>'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
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
			'#SHOP_URL#' => $this->output(Helper::siteUrl($this->arProfile['DOMAIN'], $his->arProfile['IS_HTTPS'] == 'Y'), 
				true),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}
	
	/**
	 *	Add option SHOP_NAME
	 */
	/*
	protected function onUpShowSettings(&$arSettings){
		$arSettings['SHOP_URL'] = '<input type="text" name="PROFILE[PARAMS][SHOP_URL]" size="40" 
			id="'.$this->getInputID('SHOP_URL').'" data-role="shop-url"
			value="'.htmlspecialcharsbx($this->arParams['SHOP_URL']).'" />';
	}
	*/

}

?>