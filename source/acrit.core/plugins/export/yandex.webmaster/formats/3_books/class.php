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

class YandexWebmasterBooks extends YandexWebmaster {
	
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
		return parent::getCode().'_BOOKS';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'yw_books.xml';
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
			'CODE' => 'PUBLISHER',
			'DISPLAY_CODE' => 'publisher',
			'NAME' => static::getMessage('FIELD_PUBLISHER_NAME'),
			'SORT' => 110,
			'DESCRIPTION' => static::getMessage('FIELD_PUBLISHER_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_PUBLISHER',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ISBN',
			'DISPLAY_CODE' => 'ISBN',
			'NAME' => static::getMessage('FIELD_ISBN_NAME'),
			'SORT' => 120,
			'DESCRIPTION' => static::getMessage('FIELD_ISBN_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_ISBN',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AUTHOR',
			'DISPLAY_CODE' => 'author',
			'NAME' => static::getMessage('FIELD_AUTHOR_NAME'),
			'SORT' => 130,
			'DESCRIPTION' => static::getMessage('FIELD_AUTHOR_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_AUTHOR',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SERIES',
			'DISPLAY_CODE' => 'series',
			'NAME' => static::getMessage('FIELD_SERIES_NAME'),
			'SORT' => 140,
			'DESCRIPTION' => static::getMessage('FIELD_SERIES_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_SERIES',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'YEAR',
			'DISPLAY_CODE' => 'year',
			'NAME' => static::getMessage('FIELD_YEAR_NAME'),
			'SORT' => 150,
			'DESCRIPTION' => static::getMessage('FIELD_YEAR_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_YEAR',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'VOLUME',
			'DISPLAY_CODE' => 'volume',
			'NAME' => static::getMessage('FIELD_VOLUME_NAME'),
			'SORT' => 160,
			'DESCRIPTION' => static::getMessage('FIELD_VOLUME_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_VOLUME',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PART',
			'DISPLAY_CODE' => 'part',
			'NAME' => static::getMessage('FIELD_PART_NAME'),
			'SORT' => 170,
			'DESCRIPTION' => static::getMessage('FIELD_PART_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_PART',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LANGUAGE',
			'DISPLAY_CODE' => 'language',
			'NAME' => static::getMessage('FIELD_LANGUAGE_NAME'),
			'SORT' => 180,
			'DESCRIPTION' => static::getMessage('FIELD_LANGUAGE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_LANGUAGE',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TABLE_OF_CONTENTS',
			'DISPLAY_CODE' => 'table_of_contents',
			'NAME' => static::getMessage('FIELD_TABLE_OF_CONTENTS_NAME'),
			'SORT' => 190,
			'DESCRIPTION' => static::getMessage('FIELD_TABLE_OF_CONTENTS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_TABLE_OF_CONTENTS',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BINDING',
			'DISPLAY_CODE' => 'binding',
			'NAME' => static::getMessage('FIELD_BINDING_NAME'),
			'SORT' => 200,
			'DESCRIPTION' => static::getMessage('FIELD_BINDING_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_BINDING',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PAGE_EXTENT',
			'DISPLAY_CODE' => 'page_extent',
			'NAME' => static::getMessage('FIELD_PAGE_EXTENT_NAME'),
			'SORT' => 210,
			'DESCRIPTION' => static::getMessage('FIELD_PAGE_EXTENT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_PAGE_EXTENT',
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
		if(!Helper::isEmpty($arFields['DELIVERY_OPTIONS_COST']) && !Helper::isEmpty($arFields['DELIVERY_OPTIONS_DAYS']))
			$arXmlTags['delivery-options'] = $this->getXmlTag_DeliveryOptions($intProfileID, $arFields);
		if(!Helper::isEmpty($arFields['AUTHOR']))
			$arXmlTags['author'] = Xml::addTag($arFields['AUTHOR']);
		if(!Helper::isEmpty($arFields['NAME']))
			$arXmlTags['name'] = Xml::addTag($arFields['NAME']);
		if(!Helper::isEmpty($arFields['PUBLISHER']))
			$arXmlTags['publisher'] = Xml::addTag($arFields['PUBLISHER']);
		if(!Helper::isEmpty($arFields['SERIES']))
			$arXmlTags['series'] = Xml::addTag($arFields['SERIES']);
		if(!Helper::isEmpty($arFields['YEAR']))
			$arXmlTags['year'] = Xml::addTag($arFields['YEAR']);
		if(!Helper::isEmpty($arFields['ISBN']))
			$arXmlTags['ISBN'] = Xml::addTag($arFields['ISBN']);
		if(!Helper::isEmpty($arFields['VOLUME']))
			$arXmlTags['volume'] = Xml::addTag($arFields['VOLUME']);
		if(!Helper::isEmpty($arFields['PART']))
			$arXmlTags['part'] = Xml::addTag($arFields['PART']);
		if(!Helper::isEmpty($arFields['LANGUAGE']))
			$arXmlTags['language'] = Xml::addTag($arFields['LANGUAGE']);
		if(!Helper::isEmpty($arFields['BINDING']))
			$arXmlTags['binding'] = Xml::addTag($arFields['BINDING']);
		if(!Helper::isEmpty($arFields['PAGE_EXTENT']))
			$arXmlTags['page_extent'] = Xml::addTag($arFields['PAGE_EXTENT']);
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['DOWNLOADABLE']))
			$arXmlTags['downloadable'] = Xml::addTag($arFields['DOWNLOADABLE']);
		if(!Helper::isEmpty($arFields['AGE']))
			$arXmlTags['age'] = $this->getXmlTag_Age($intProfileID, $arFields['AGE']);
		
		# Not in example
		if(!Helper::isEmpty($arFields['TABLE_OF_CONTENTS']))
			$arXmlTags['table_of_contents'] = Xml::addTag($arFields['TABLE_OF_CONTENTS']);
		if(!Helper::isEmpty($arFields['SALES_NOTES']))
			$arXmlTags['sales_notes'] = Xml::addTag($arFields['SALES_NOTES']);
		if(!Helper::isEmpty($arFields['MANUFACTURER_WARRANTY']))
			$arXmlTags['manufacturer_warranty'] = Xml::addTag($arFields['MANUFACTURER_WARRANTY']);
		if(!Helper::isEmpty($arFields['COUNTRY_OF_ORIGIN']))
			$arXmlTags['country_of_origin'] = Xml::addTag($arFields['COUNTRY_OF_ORIGIN']);
		if(!Helper::isEmpty($arFields['BARCODE']))
			$arXmlTags['barcode'] = $this->getXmlTag_Barcode($intProfileID, $arFields['BARCODE']);
		if(!Helper::isEmpty($arFields['EXPIRY']))
			$arXmlTags['expiry'] = Xml::addTag($arFields['EXPIRY']);
		if(!Helper::isEmpty($arFields['ADULT']))
			$arXmlTags['adult'] = Xml::addTag($arFields['ADULT']);
		if(!Helper::isEmpty($arFields['WEIGHT']))
			$arXmlTags['weight'] = Xml::addTag($arFields['WEIGHT']);
		if(!Helper::isEmpty($arFields['DIMENSIONS']))
			$arXmlTags['dimensions'] = Xml::addTag($arFields['DIMENSIONS']);
		
		# More
		if(!Helper::isEmpty($arFields['REC']))
			$arXmlTags['rec'] = Xml::addTag($arFields['REC']);
		
		# Params
		$arXmlTags['param'] = $this->getXmlTag_Param($arProfile, $intIBlockID, $arFields);
		
		# Build XML
		$arXml = array(
			'offer' => array(
				'@' => $this->getXmlAttr($intProfileID, $arFields, 'book'),
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