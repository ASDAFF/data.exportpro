<?
/**
 * Acrit Core: Yandex.Market plugin
 * @documentation https://yandex.ru/support/partnermarket/export/medicine.html
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition;

Loc::loadMessages(__FILE__);

class YandexMarketMedicine extends YandexMarket {
	
	CONST DATE_UPDATED = '2019-09-03';

	protected static $bSubclass = true;
	
	/**
	 * Base constructor
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}
	
	/* START OF BASE STATIC METHODS */
	
	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return parent::getCode().'_MEDICINE';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'ym_medicine.xml';
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$this->removeFields($arResult, array('AGE','MANUFACTURER_WARRANTY','DELIVERY_OPTIONS_COST',
			'DELIVERY_OPTIONS_DAYS','DELIVERY_OPTIONS_ORDER_BEFORE','DOWNLOADABLE','ADULT'));
		#
		$this->sortFields($arResult);
		return $arResult;
	}
	
	/**
	 *	Process single element (generate XML)
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		
		# Internal event handler
		$this->onBeforeProcessElement($arProfile, $intIBlockID, $arElement, $arFields);
		
		# Prepare data
		$bOffer = $arElement['IS_OFFER'];
		if($bOffer) {
			$arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		else {
			$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		
		# Prepare sections
		$this->prepareSaveSections($arElementSections, $arProfile, $intIBlockID, $arElement, $arFields);
		
		# Build XML
		$arXmlTags = array();
		if(!Helper::isEmpty($arFields['CURRENCY_ID']))
			$arXmlTags['currencyId'] = Xml::addTag($arFields['CURRENCY_ID']);
		if(!Helper::isEmpty($arFields['VAT']))
			$arXmlTags['vat'] = $this->getXmlTag_Vat($intProfileID, $arFields['VAT'], $arFields);
		if(!Helper::isEmpty($arFields['ENABLE_AUTO_DISCOUNTS']))
			$arXmlTags['enable_auto_discounts'] = Xml::addTag($arFields['ENABLE_AUTO_DISCOUNTS']);
		$arXmlTags['categoryId'] = Xml::addTag(reset($arElementSections));
		if(!Helper::isEmpty($arFields['MARKET_CATEGORY']))
			$arXmlTags['market_category'] = Xml::addTag($arFields['MARKET_CATEGORY']);
		if(!Helper::isEmpty($arFields['NAME']))
			$arXmlTags['name'] = Xml::addTag($arFields['NAME']);
		if(!Helper::isEmpty($arFields['VENDOR']))
			$arXmlTags['vendor'] = Xml::addTag($arFields['VENDOR']);
		if(!Helper::isEmpty($arFields['VENDOR_CODE']))
			$arXmlTags['vendorCode'] = Xml::addTag($arFields['VENDOR_CODE']);
		if(!Helper::isEmpty($arFields['URL']))
			$arXmlTags['url'] = $this->getXmlTag_Url($intProfileID, $arFields['URL'], $arFields);
		if(!Helper::isEmpty($arFields['PICTURE']))
			$arXmlTags['picture'] = Xml::addTag($arFields['PICTURE']);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['price'] = Xml::addTag($arFields['PRICE']);
		if(!Helper::isEmpty($arFields['DELIVERY']))
			$arXmlTags['delivery'] = Xml::addTag($arFields['DELIVERY']);
		if(!Helper::isEmpty($arFields['PICKUP']))
			$arXmlTags['pickup'] = Xml::addTag($arFields['PICKUP']);
		if(!Helper::isEmpty($arFields['PICKUP_OPTIONS_COST']) && !Helper::isEmpty($arFields['PICKUP_OPTIONS_DAYS']))
			$arXmlTags['pickup-options'] = $this->getXmlTag_PickupOptions($intProfileID, $arFields);
		if(!Helper::isEmpty($arFields['STORE']))
			$arXmlTags['store'] = Xml::addTag($arFields['STORE']);
		if(!Helper::isEmpty($arFields['BARCODE']))
			$arXmlTags['barcode'] = $this->getXmlTag_Barcode($intProfileID, $arFields['BARCODE']);
		if(!Helper::isEmpty($arFields['SALES_NOTES']))
			$arXmlTags['sales_notes'] = Xml::addTag($arFields['SALES_NOTES']);
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['COUNTRY_OF_ORIGIN']))
			$arXmlTags['country_of_origin'] = Xml::addTag($arFields['COUNTRY_OF_ORIGIN']);
		if(!Helper::isEmpty($arFields['EXPIRY']))
			$arXmlTags['expiry'] = Xml::addTag($arFields['EXPIRY']);
		
		# Not in example
		if(!Helper::isEmpty($arFields['OLD_PRICE']) && $arFields['OLD_PRICE'] != $arFields['PRICE'])
			$arXmlTags['oldprice'] = Xml::addTag($arFields['OLD_PRICE']);
		if(!Helper::isEmpty($arFields['WEIGHT']))
			$arXmlTags['weight'] = Xml::addTag($arFields['WEIGHT']);
		if(!Helper::isEmpty($arFields['DIMENSIONS']))
			$arXmlTags['dimensions'] = Xml::addTag($arFields['DIMENSIONS']);
		
		# More
		if(!Helper::isEmpty($arFields['REC']))
			$arXmlTags['rec'] = Xml::addTag($arFields['REC']);
		if(!Helper::isEmpty($arFields['CREDIT_TEMPLATE_ID']))
			$arXmlTags['credit-template'] = $this->getXmlTag_CreditTemplate($arFields['CREDIT_TEMPLATE_ID']);
		
		# PriceLabs params
		$this->processElementPriceLabsParams($arMainIBlockData['IBLOCK_ID'], $arFields, $arXmlTags);
		
		# Params
		$arXmlTags['param'] = $this->getXmlTag_Param($arProfile, $intIBlockID, $arFields);
		
		# Build XML
		$arXml = array(
			'offer' => array(
				'@' => $this->getXmlAttr($intProfileID, $arFields, 'medicine'),
				'#' => $arXmlTags,
			),
		);
		
		# Internal event handler
		$this->onProcessElement($arProfile, $intIBlockID, $arElement, $arFields, $arXml);
		
		# Event handler OnYandexMarketXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexMarketXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# More data
		$arDataMore = array();
		
		# Promos
		$this->processElementPromos($arFields, $arDataMore);
		
		# Build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => Xml::arrayToXml($arXml),
			'CURRENCY' => $arFields['CURRENCY_ID'],
			'SECTION_ID' => reset($arElementSections),
			'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
			'DATA_MORE' => $arDataMore,
		);
		
		# Internal event handler
		$this->onAfterProcessElement($arProfile, $intIBlockID, $arElement, $arFields, $arResult);
		
		# Event handler OnYandexMarketResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexMarketResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# Ending..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}
	
}

?>