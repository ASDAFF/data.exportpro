<?
/**
 * Acrit Core: Yandex.Realty plugin
 * @documentation https://yandex.ru/support/realty/requirements/
 */

namespace Acrit\Core\Export\Plugins;

class PriceUaXml extends PriceUa {
	
	const DATE_UPDATED = '2019-09-17';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'price_ua.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8, self::CP1251];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['UAH'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCurrenciesExport = false;
	
	# XML settings
	protected $strXmlItemElement = 'item';
	protected $intXmlDepthItems = 1;
	protected $arXmlMultiply = [];
	
	# Other export settings
	protected $bZip = true;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['@id'] = ['FIELD' => 'ID'];
		$arResult['name'] = ['FIELD' => 'NAME'];
		$arResult['categoryId'] = ['FIELD' => 'SECTION__ID'];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
		$arResult['bnprice'] = [];
		$arResult['oldprice'] = ['FIELD' => 'CATALOG_PRICE_1'];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['image'] = ['FIELD' => 'DETAIL_PICTURE'];
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER'];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true];
		$arResult['guarantee'] = ['CONST' => '12'];
		$arResult['guarantee@type'] = ['CONST' => 'shop'];
		$arResult['guarantee@unit'] = ['CONST' => ''];
		#
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<price date="#XML_GENERATION_DATE#">'.static::EOL;
		$strXml .= '	<name>#SHOP_NAME#</name>'.static::EOL;
		$strXml .= '	<catalog>'.static::EOL;
		$strXml .= '		#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '	</catalog>'.static::EOL;
		$strXml .= '	<items>'.static::EOL;
		$strXml .= '		#XML_ITEMS#'.static::EOL;
		$strXml .= '	</items>'.static::EOL;
		$strXml .= '</price>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_GENERATION_DATE#' => date('c'),
			'#SHOP_NAME#' => $this->output($this->arParams['SHOP_NAME'], true),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}
	
	/**
	 *	Add option SHOP_NAME
	 */
	protected function onUpShowSettings(&$arSettings){
		$arSettings['SHOP_NAME'] = '<input type="text" name="PROFILE[PARAMS][SHOP_NAME]" size="40" 
			id="'.$this->getInputID('FILENAME').'" data-role="shop-name"
			value="'.htmlspecialcharsbx($this->arParams['SHOP_NAME']).'" />';
	}

}

?>