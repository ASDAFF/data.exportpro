<?
/**
 * Acrit Core: nadavi.net plugin
 * @documentation https://nadavi.net/nadavi.php?idPage_=57&idBookmark_=5
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Xml;

class NadaviNetXml extends NadaviNet {
	
	const DATE_UPDATED = '2019-10-31';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'nadavi_net.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'EUR', 'USD', 'UAH', 'KZT'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCategoriesUpdate = false;
	protected $bCurrenciesExport = true;
	protected $bCategoriesList = false;
	
	# XML settings
	protected $strXmlItemElement = 'item';
	protected $intXmlDepthItems = 3;
	
	# Other export settings
	protected $arFieldsWithUtm = ['url'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@id'] = ['FIELD' => 'ID'];
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y']];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true];
		$arResult['currencyId'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'IS_CURRENCY' => true];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID'];
		$arResult['typePrefix'] = ['FIELD' => 'SECTION__NAME'];
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER'];
		$arResult['image'] = ['FIELD' => 'DETAIL_PICTURE'];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'FIELD_PARAMS' => ['HTMLSPECIALCHARS' => 'skip']];
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<yml_catalog date="#XML_GENERATION_DATE#">'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
		$strXml .= '		<name>#SHOP_NAME#</name>'.static::EOL;
		$strXml .= '		<url>#SHOP_URL#</url>'.static::EOL;
		$strXml .= '		<currencies>'.static::EOL;
		$strXml .= '			#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '		</currencies>'.static::EOL;
		$strXml .= '		<catalog>'.static::EOL;
		$strXml .= '			#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '		</catalog>'.static::EOL;
		$strXml .= '		<items>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</items>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_GENERATION_DATE#' => date('Y-m-d H:i'),
			'#SHOP_NAME#' => $this->output($this->arParams['SHOP_NAME'], true),
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
	}

}

?>