<?
/**
 * Acrit Core: Yandex.Turbo plugin
 * @documentation https://yandex.ru/support/partnermarket/offers.html#offers__list
 */

namespace Acrit\Core\Export\Plugins;

use 
	\Bitrix\Main\Localization\Loc,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Helper,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class ZakupkiMosRuSimple extends ZakupkiMosRu {
	
	const DATE_UPDATED = '2019-09-24';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'zakupki.mos.ru.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8, self::CP1251];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUR', 'RUB'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCurrenciesExport = true;
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 2;
	protected $arXmlMultiply = ['delivery-options.option@cost'];
	
	# Other export settings
	protected $bZip = false;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@id'] = ['FIELD' => 'ID'];
		$arResult['@available'] = ['CONST' => 'true'];
		$arResult['@group_id'] = ['FIELD' => 'ID'];
		$arResult['currencyId'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'IS_CURRENCY' => true];
		$arResult['name'] = ['FIELD' => 'NAME'];
		$arResult['picture'] = ['FIELD' => 'DETAIL_PICTURE'];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER'];
		$arResult['model'] = ['FIELD' => 'PROPERTY_MODEL'];
		$arResult['vendorCode'] = ['FIELD' => 'PROPERTY_VENDOR_CODE'];
		$arResult['vat'] = ['FIELD' => 'CATALOG_VAT_VALUE'];
		$arResult['delivery'] = ['CONST' => ''];
		$arResult['manufacturer_warranty'] = ['CONST' => ''];
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE'];
		$arResult['expiry'] = ['CONST' => ''];
		$arResult['weight'] = ['FIELD' => 'CATALOG_WEIGHT'];
		$arResult['dimensions'] = ['CONST' => ''];
		$arResult['downloadable'] = ['CONST' => 'false'];
		$arResult['age'] = ['CONST' => ''];
		$arResult['age@unit'] = ['CONST' => ''];
		$arResult['delivery-options.option@cost'] = array('MULTIPLE' => true);
		$arResult['delivery-options.option@days'] = array('MULTIPLE' => true);
		$arResult['delivery-options.option@order-before'] = array('MULTIPLE' => true);
		$arResult['HEADER_PP'] = [];
		$arResult['ste'] = ['FIELD' => ''];
		$arResult['isVisibleToStateCustomers'] = ['CONST' => 'true'];
		$arResult['isAvailableToIndividuals'] = ['CONST' => 'true'];
		$arResult['ppCategory'] = ['FIELD' => ''];
		$arResult['okei'] = ['FIELD' => ''];
		$arResult['okei@id'] = ['FIELD' => ''];
		$arResult['min-quantity'] = ['CONST' => '1'];
		$arResult['max-quantity'] = ['CONST' => ''];
		$arResult['beginDate'] = ['FIELD' => ''];
		$arResult['endDate'] = ['FIELD' => ''];
		$arResult['package@id'] = ['FIELD' => ''];
		$arResult['regions.region'] = ['CONST' => ''];
		$arResult['regions.region@id'] = ['CONST' => ''];
		#
		return $arResult;
	}
	
	/**
	 *	Add some new tags
	 */
	protected function onUpBeforeProcessElement(&$arResult, &$arElement, &$arFields, &$arElementSections, $intMainIBlockId){
		Helper::arrayInsert($arFields, 'categoryId', reset($arElementSections), 'ppCategory');
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<yml_catalog date="#XML_GENERATION_DATE#" xmlns="http://market.zakupki.mos.ru/spIntegration/Yml/1.0">'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
		$strXml .= '		<name>#SHOP_NAME#</name>'.static::EOL;
		$strXml .= '		<company>#COMPANY_NAME#</company>'.static::EOL;
		$strXml .= '		<url>#SHOP_URL#</url>'.static::EOL;
		$strXml .= '		<currencies>'.static::EOL;
		$strXml .= '			#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '		</currencies>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '	<categories>'.static::EOL;
		$strXml .= '		#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '	</categories>'.static::EOL;
		$strXml .= '	<offers>'.static::EOL;
		$strXml .= '		#XML_ITEMS#'.static::EOL;
		$strXml .= '	</offers>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_GENERATION_DATE#' => date('c'),
			'#SHOP_NAME#' => $this->output($this->arParams['SHOP_NAME'], true),
			'#COMPANY_NAME#' => $this->output($this->arParams['COMPANY_NAME'], true),
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
			id="'.$this->getInputID('FILENAME').'" data-role="shop-name"
			value="'.htmlspecialcharsbx($this->arParams['SHOP_NAME']).'" />';
		$arSettings['COMPANY_NAME'] = '<input type="text" name="PROFILE[PARAMS][COMPANY_NAME]" size="40" 
			id="'.$this->getInputID('FILENAME').'" data-role="company-name"
			value="'.htmlspecialcharsbx($this->arParams['COMPANY_NAME']).'" />';
	}

}

?>