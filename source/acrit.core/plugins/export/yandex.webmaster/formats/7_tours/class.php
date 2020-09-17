<?
/**
 * Acrit Core: Yandex.Webmaster plugin
 * @documentation https://yandex.ru/support/webmaster/goods-prices/technical-requirements.html
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class YandexWebmasterTours extends YandexWebmaster {
	
	CONST DATE_UPDATED = '2018-07-20';

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
		return parent::getCode().'_TOURS';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'yw_tours.xml';
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$arResult[] = new Field(array(
			'CODE' => 'NAME',
			'DISPLAY_CODE' => 'name',
			'NAME' => static::getMessage('FIELD_NAME_NAME'),
			'SORT' => 100,
			'DESCRIPTION' => static::getMessage('FIELD_NAME_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'NAME',
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'escape',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WORLD_REGION',
			'DISPLAY_CODE' => 'worldRegion',
			'NAME' => static::getMessage('FIELD_WORLD_REGION_NAME'),
			'SORT' => 110,
			'DESCRIPTION' => static::getMessage('FIELD_WORLD_REGION_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_WORLD_REGION',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COUNTRY',
			'DISPLAY_CODE' => 'country',
			'NAME' => static::getMessage('FIELD_COUNTRY_NAME'),
			'SORT' => 120,
			'DESCRIPTION' => static::getMessage('FIELD_COUNTRY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_COUNTRY',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'REGION',
			'DISPLAY_CODE' => 'region',
			'NAME' => static::getMessage('FIELD_REGION_NAME'),
			'SORT' => 130,
			'DESCRIPTION' => static::getMessage('FIELD_REGION_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_REGION',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DAYS',
			'DISPLAY_CODE' => 'days',
			'NAME' => static::getMessage('FIELD_DAYS_NAME'),
			'SORT' => 140,
			'DESCRIPTION' => static::getMessage('FIELD_DAYS_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_DAYS',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DATA_TOUR',
			'DISPLAY_CODE' => 'dataTour',
			'NAME' => static::getMessage('FIELD_DATA_TOUR_NAME'),
			'SORT' => 150,
			'DESCRIPTION' => static::getMessage('FIELD_DATA_TOUR_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_DATA_TOUR',
					'PARAMS' => array(
						'MULTIPLE' => 'multiple',
						'DATEFORMAT' => 'Y',
						'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
						'DATEFORMAT_to' => 'Y-m-d H:i:s',
						'DATEFORMAT_keep_wrong' => 'Y',
					),
				),
			),
			'PARAMS' => array('MULTIPLE' => 'multiple'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'HOTEL_STARS',
			'DISPLAY_CODE' => 'hotel_stars',
			'NAME' => static::getMessage('FIELD_HOTEL_STARS_NAME'),
			'SORT' => 160,
			'DESCRIPTION' => static::getMessage('FIELD_HOTEL_STARS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_HOTEL_STARS',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ROOM',
			'DISPLAY_CODE' => 'room',
			'NAME' => static::getMessage('FIELD_ROOM_NAME'),
			'SORT' => 170,
			'DESCRIPTION' => static::getMessage('FIELD_ROOM_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_ROOM',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MEAL',
			'DISPLAY_CODE' => 'meal',
			'NAME' => static::getMessage('FIELD_MEAL_NAME'),
			'SORT' => 180,
			'DESCRIPTION' => static::getMessage('FIELD_MEAL_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MEAL',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'INCLUDED',
			'DISPLAY_CODE' => 'included',
			'NAME' => static::getMessage('FIELD_INCLUDED_NAME'),
			'SORT' => 190,
			'DESCRIPTION' => static::getMessage('FIELD_INCLUDED_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_INCLUDED',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRANSPORT',
			'DISPLAY_CODE' => 'transport',
			'NAME' => static::getMessage('FIELD_TRANSPORT_NAME'),
			'SORT' => 200,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSPORT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_TRANSPORT',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE_MIN',
			'DISPLAY_CODE' => 'price_min',
			'NAME' => static::getMessage('FIELD_PRICE_MIN_NAME'),
			'SORT' => 210,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_MIN_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_PRICE_MIN',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE_MAX',
			'DISPLAY_CODE' => 'price_max',
			'NAME' => static::getMessage('FIELD_PRICE_MAX_NAME'),
			'SORT' => 220,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_MAX_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_PRICE_MAX',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OPTIONS',
			'DISPLAY_CODE' => 'options',
			'NAME' => static::getMessage('FIELD_OPTIONS_NAME'),
			'SORT' => 230,
			'DESCRIPTION' => static::getMessage('FIELD_OPTIONS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_OPTIONS',
				),
			),
		));
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
		
		# Build XML
		$arXmlTags = array();
		if(!Helper::isEmpty($arFields['URL']))
			$arXmlTags['url'] = $this->getXmlTag_Url($intProfileID, $arFields['URL'], $arFields);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['price'] = Xml::addTag($arFields['PRICE']);
		if(!Helper::isEmpty($arFields['OLD_PRICE']) && $arFields['OLD_PRICE'] != $arFields['PRICE'])
			$arXmlTags['oldprice'] = Xml::addTag($arFields['OLD_PRICE']);
		if(!Helper::isEmpty($arFields['CURRENCY_ID']))
			$arXmlTags['currencyId'] = Xml::addTag($arFields['CURRENCY_ID']);
		if(!Helper::isEmpty($arFields['VAT']))
			$arXmlTags['vat'] = $this->getXmlTag_Vat($intProfileID, $arFields['VAT'], $arFields);
		if(!Helper::isEmpty($arFields['ENABLE_AUTO_DISCOUNTS']))
			$arXmlTags['enable_auto_discounts'] = Xml::addTag($arFields['ENABLE_AUTO_DISCOUNTS']);
		$arXmlTags['categoryId'] = Xml::addTag(reset($arElementSections));
		if(!Helper::isEmpty($arFields['PICTURE']))
			$arXmlTags['picture'] = Xml::addTag($arFields['PICTURE']);
		if(!Helper::isEmpty($arFields['STORE']))
			$arXmlTags['store'] = Xml::addTag($arFields['STORE']);
		if(!Helper::isEmpty($arFields['PICKUP']))
			$arXmlTags['pickup'] = Xml::addTag($arFields['PICKUP']);
		if(!Helper::isEmpty($arFields['DELIVERY']))
			$arXmlTags['delivery'] = Xml::addTag($arFields['DELIVERY']);
		
		#
		if(!Helper::isEmpty($arFields['WORLD_REGION']))
			$arXmlTags['worldRegion'] = Xml::addTag($arFields['WORLD_REGION']);
		if(!Helper::isEmpty($arFields['COUNTRY']))
			$arXmlTags['country'] = Xml::addTag($arFields['COUNTRY']);
		if(!Helper::isEmpty($arFields['REGION']))
			$arXmlTags['region'] = Xml::addTag($arFields['REGION']);
		if(!Helper::isEmpty($arFields['DAYS']))
			$arXmlTags['days'] = Xml::addTag($arFields['DAYS']);
		if(!Helper::isEmpty($arFields['DATA_TOUR']))
			$arXmlTags['dataTour'] = Xml::addTag($arFields['DATA_TOUR']);
		if(!Helper::isEmpty($arFields['NAME']))
			$arXmlTags['name'] = Xml::addTag($arFields['NAME']);
		if(!Helper::isEmpty($arFields['HOTEL_STARS']))
			$arXmlTags['hotel_stars'] = Xml::addTag($arFields['HOTEL_STARS']);
		if(!Helper::isEmpty($arFields['ROOM']))
			$arXmlTags['room'] = Xml::addTag($arFields['ROOM']);
		if(!Helper::isEmpty($arFields['MEAL']))
			$arXmlTags['meal'] = Xml::addTag($arFields['MEAL']);
		if(!Helper::isEmpty($arFields['INCLUDED']))
			$arXmlTags['included'] = Xml::addTag($arFields['INCLUDED']);
		if(!Helper::isEmpty($arFields['TRANSPORT']))
			$arXmlTags['transport'] = Xml::addTag($arFields['TRANSPORT']);
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['PRICE_MIN']))
			$arXmlTags['price_min'] = Xml::addTag($arFields['PRICE_MIN']);
		if(!Helper::isEmpty($arFields['PRICE_MAX']))
			$arXmlTags['price_max'] = Xml::addTag($arFields['PRICE_MAX']);
		if(!Helper::isEmpty($arFields['OPTIONS']))
			$arXmlTags['options'] = Xml::addTag($arFields['OPTIONS']);
		
		# Not in example
		if(!Helper::isEmpty($arFields['SALES_NOTES']))
			$arXmlTags['sales_notes'] = Xml::addTag($arFields['SALES_NOTES']);
		if(!Helper::isEmpty($arFields['MANUFACTURER_WARRANTY']))
			$arXmlTags['manufacturer_warranty'] = Xml::addTag($arFields['MANUFACTURER_WARRANTY']);
		if(!Helper::isEmpty($arFields['COUNTRY_OF_ORIGIN']))
			$arXmlTags['country_of_origin'] = Xml::addTag($arFields['COUNTRY_OF_ORIGIN']);
		if(!Helper::isEmpty($arFields['BARCODE']))
			$arXmlTags['barcode'] = $this->getXmlTag_Barcode($intProfileID, $arFields['BARCODE']);
		if(!Helper::isEmpty($arFields['DELIVERY_OPTIONS_COST']) && !Helper::isEmpty($arFields['DELIVERY_OPTIONS_DAYS']))
			$arXmlTags['delivery-options'] = $this->getXmlTag_DeliveryOptions($intProfileID, $arFields);
		if(!Helper::isEmpty($arFields['EXPIRY']))
			$arXmlTags['expiry'] = Xml::addTag($arFields['EXPIRY']);
		if(!Helper::isEmpty($arFields['AGE']))
			$arXmlTags['age'] = $this->getXmlTag_Age($intProfileID, $arFields['AGE']);
		if(!Helper::isEmpty($arFields['ADULT']))
			$arXmlTags['adult'] = Xml::addTag($arFields['ADULT']);
		if(!Helper::isEmpty($arFields['WEIGHT']))
			$arXmlTags['weight'] = Xml::addTag($arFields['WEIGHT']);
		if(!Helper::isEmpty($arFields['DIMENSIONS']))
			$arXmlTags['dimensions'] = Xml::addTag($arFields['DIMENSIONS']);
		if(!Helper::isEmpty($arFields['DOWNLOADABLE']))
			$arXmlTags['downloadable'] = Xml::addTag($arFields['DOWNLOADABLE']);
		
		# More
		if(!Helper::isEmpty($arFields['REC']))
			$arXmlTags['rec'] = Xml::addTag($arFields['REC']);
		
		# Params
		$arXmlTags['param'] = $this->getXmlTag_Param($arProfile, $intIBlockID, $arFields);
		
		# Build XML
		$arXml = array(
			'offer' => array(
				'@' => $this->getXmlAttr($intProfileID, $arFields, 'tour'),
				'#' => $arXmlTags,
			),
		);
		
		# Event handler OnYandexWebmasterXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexWebmasterXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# Build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => Xml::arrayToXml($arXml),
			'CURRENCY' => $arFields['CURRENCY_ID'],
			'SECTION_ID' => reset($arElementSections),
			'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
			'DATA_MORE' => array(),
		);
		
		# Event handler OnYandexWebmasterResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexWebmasterResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# after..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}
	
}

?>