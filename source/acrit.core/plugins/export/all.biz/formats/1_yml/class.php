<?
/**
 * Acrit Core: All.biz plugin
 * @documentation http://help.all.biz/import-tovarov-kak-importirovat-tovary-iz-yml-fajla-ans186
 */

namespace Acrit\Core\Export\Plugins;

class AllBizYml extends AllBiz {
	
	const DATE_UPDATED = '2019-10-28';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'all_biz.xml';
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
		$arResult['@id'] = ['FIELD' => 'ID'];
		$arResult['@available'] = ['CONST' => 'true'];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID'];
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y']];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true];
		$arResult['currencyId'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'IS_CURRENCY' => true];
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER'];
		$arResult['vendorCode'] = ['FIELD' => 'PROPERTY_CML2_ARTICLE'];
		$arResult['country_of_origin'] = ['FIELD' => 'PROPERTY_COUNTRY'];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['picture'] = ['FIELD' => 'DETAIL_PICTURE', 'MULTIPLE' => true, 'MAX_COUNT' => 10];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true];
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
		$strXml .= '		<categories>'.static::EOL;
		$strXml .= '			#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '		</categories>'.static::EOL;
		$strXml .= '		<currencies>'.static::EOL;
		$strXml .= '			#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '		</currencies>'.static::EOL;
		$strXml .= '		<offers>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</offers>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}

}

?>