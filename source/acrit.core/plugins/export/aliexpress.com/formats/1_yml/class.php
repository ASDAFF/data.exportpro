<?
/**
 * Acrit Core: Aliexpress plugin
 * @documentation https://service.aliexpress.com/page/knowledge?pageId=44&category=1000023367&knowledge=1060047979&language=ru
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Xml;

class AliexpressComYml extends AliexpressCom {
	
	const DATE_UPDATED = '2019-10-24';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'aliexpress_yml.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB'];
	protected $bCategoryCustomName = true;
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 3;
	protected $arXmlMultiply = ['prices.price.value', 'prices.price.quantity'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['@group_id'] = ['FIELD' => 'ID', 'OFFER_FIELD' => 'PARENT.ID', 'REQUIRED' => true];
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y']];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'REQUIRED' => true];
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER', 'OFFER_FIELD' => 'PARENT.PROPERTY_MANUFACTURER', 'REQUIRED' => true];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'REQUIRED' => true, 'IS_PRICE' => true];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID', 'REQUIRED' => true];
		$arResult['CATEGORY_CUSTOM_NAME'] = ['CATEGORY_CUSTOM_NAME' => true];
		$arResult['picture'] = ['FIELD' => 'DETAIL_PICTURE', 'MULTIPLE' => true, 'MAX_COUNT' => 10, 'REQUIRED' => true];
		$arResult['weight'] = ['CONST' => '{=catalog.CATALOG_WEIGHT} / 1000', 'CONST_PARAMS' => ['MATH' => 'Y'], 'REQUIRED' => true];
		$arResult['length'] = ['CONST' => '{=catalog.CATALOG_LENGTH} / 10', 'CONST_PARAMS' => ['MATH' => 'Y'], 'REQUIRED' => true];
		$arResult['width'] = ['CONST' => '{=catalog.CATALOG_WIDTH} / 10', 'CONST_PARAMS' => ['MATH' => 'Y'], 'REQUIRED' => true];
		$arResult['height'] = ['CONST' => '{=catalog.CATALOG_HEIGHT} / 10', 'CONST_PARAMS' => ['MATH' => 'Y'], 'REQUIRED' => true];
		$arResult['quantity'] = ['FIELD' => 'CATALOG_QUANTITY'];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<yml_catalog>'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
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
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}
	
	/**
	 *	Use CategoryCustomName
	 */
	protected function onUpPrepareSaveSection(&$arElementSections, &$arProfile, $intIBlockId, &$arElement, &$arFields){
		$arFields['categoryId'] = reset($arElementSections);
	}

}

?>