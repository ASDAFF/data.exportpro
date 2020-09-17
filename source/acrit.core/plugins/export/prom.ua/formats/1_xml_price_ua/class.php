<?
/**
 * Acrit Core: Yandex.Realty plugin
 * @documentation http://support.prom.ua/documents/844
 */

namespace Acrit\Core\Export\Plugins;

class PromUaXml extends PromUa {
	
	const DATE_UPDATED = '2019-10-10';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'prom_ua.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8, self::CP1251];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['UAH'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCategoriesUpdate = true;
	protected $bCurrenciesExport = false;
	protected $bCategoriesList = true;
	protected $strCategoriesUrl = 'http://my.prom.ua/cabinet/export_categories/xls';
	
	# XML settings
	protected $strXmlItemElement = 'item';
	protected $intXmlDepthItems = 1;
	protected $arXmlMultiply = [];
	
	# Other export settings
	protected $bZip = true;
	protected $arFieldsWithUtm = ['url'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['@id'] = ['FIELD' => 'ID'];
		$arResult['@available'] = [
			'TYPE' => 'CONDITION',
			'CONDITIONS' => $this->getFieldFilter($intIBlockID, [
				'FIELD' => 'CATALOG_QUANTITY',
				'LOGIC' => 'MORE',
				'VALUE' => '0',
			]),
			'DEFAULT_VALUE' => [
				[
					'TYPE' => 'CONST',
					'CONST' => 'true',
					'SUFFIX' => 'Y',
				],
				[
					'TYPE' => 'CONST',
					'CONST' => 'false',
					'SUFFIX' => 'N',
				],
			],
		];
		$arResult['@presence_sure'] = ['CONST' => ''];
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y']];
		$arResult['categoryId'] = ['FIELD' => 'SECTION__ID'];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true];
		$arResult['bnprice'] = ['IS_PRICE' => true];
		$arResult['oldprice'] = ['FIELD' => 'CATALOG_PRICE_1', 'IS_PRICE' => true];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['image'] = ['FIELD' => 'DETAIL_PICTURE', 'MULTIPLE' => true, 'MAX_COUNT' => 10];
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