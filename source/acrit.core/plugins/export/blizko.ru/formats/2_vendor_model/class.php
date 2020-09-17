<?
/**
 * Acrit Core: BlizkoRu plugin
 * @documentation https://www.blizko.ru/about/FAQ/help/yml
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Bitrix\Main\EventManager,
		\Acrit\Core\Helper,
		\Acrit\Core\Export\Exporter,
		\Acrit\Core\Xml,
		\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class BlizkoRuVendorModel extends BlizkoRu {

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
		return parent::getCode() . '_VENDOR_MODEL';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}

	/* END OF BASE STATIC METHODS */

	public function getDefaultExportFilename() {
		return 'blizko_ru_vendor.xml';
	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$arResult[] = new Field(array(
				'CODE' => 'MODEL',
				'DISPLAY_CODE' => 'model',
				'NAME' => static::getMessage('FIELD_MODEL_NAME'),
				'SORT' => 100,
				'DESCRIPTION' => static::getMessage('FIELD_MODEL_DESC'),
				'REQUIRED' => true,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'PROPERTY_MODEL',
						#'VALUE' => 'PARENT.PROPERTY_HTML',
						),
				),
		));
		$arResult[] = new Field(array(
				'CODE' => 'VENDOR',
				'DISPLAY_CODE' => 'vendor',
				'NAME' => static::getMessage('FIELD_VENDOR_NAME'),
				'SORT' => 110,
				'DESCRIPTION' => static::getMessage('FIELD_VENDOR_DESC'),
				'REQUIRED' => true,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'PROPERTY_MANUFACTURER',
						),
				),
		));
		$arResult[] = new Field(array(
				'CODE' => 'VENDOR_CODE',
				'DISPLAY_CODE' => 'vendorCode',
				'NAME' => static::getMessage('FIELD_VENDOR_CODE_NAME'),
				'SORT' => 120,
				'DESCRIPTION' => static::getMessage('FIELD_VENDOR_CODE_DESC'),
				'REQUIRED' => false,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'PROPERTY_ARTNUMBER',
						),
				),
		));
		$arResult[] = new Field(array(
				'CODE' => 'TYPE_PREFIX',
				'DISPLAY_CODE' => 'typePrefix',
				'NAME' => static::getMessage('FIELD_TYPE_PREFIX_NAME'),
				'SORT' => 130,
				'DESCRIPTION' => static::getMessage('FIELD_TYPE_PREFIX_DESC'),
				'REQUIRED' => false,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => '',
						),
				),
		));
		#
		$this->sortFields($arResult);
		return $arResult;
	}

	/**
	 * 	Process single element (generate XML)
	 * 	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];

		# Prepare data
		$bOffer = $arElement['IS_OFFER'];
		if ($bOffer) {
			$arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		} else {
			$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}

		# Build XML
		$arXmlTags = array();
		if (!Helper::isEmpty($arFields['URL']))
			$arXmlTags['url'] = static::getXmlTag_Url($intProfileID, $arFields['URL'], $arFields);
		if (!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['price'] = Xml::addTag($arFields['PRICE']);
		if (!Helper::isEmpty($arFields['OLD_PRICE']) && $arFields['OLD_PRICE'] != $arFields['PRICE'])
			$arXmlTags['oldprice'] = Xml::addTag($arFields['OLD_PRICE']);
		if (!Helper::isEmpty($arFields['CURRENCY_ID']))
			$arXmlTags['currencyId'] = Xml::addTag($arFields['CURRENCY_ID']);
		$arXmlTags['categoryId'] = Xml::addTag(reset($arElementSections));
		if (!Helper::isEmpty($arFields['PICTURE']))
			$arXmlTags['picture'] = Xml::addTag($arFields['PICTURE']);
		if (!Helper::isEmpty($arFields['STORE']))
			$arXmlTags['store'] = Xml::addTag($arFields['STORE']);
		if (!Helper::isEmpty($arFields['PICKUP']))
			$arXmlTags['pickup'] = Xml::addTag($arFields['PICKUP']);
		if (!Helper::isEmpty($arFields['DELIVERY']))
			$arXmlTags['delivery'] = Xml::addTag($arFields['DELIVERY']);
		if (!Helper::isEmpty($arFields['DELIVERY_OPTIONS_COST']) && !Helper::isEmpty($arFields['DELIVERY_OPTIONS_DAYS']))
			$arXmlTags['delivery-options'] = static::getXmlTag_DeliveryOptions($intProfileID, $arFields);
		if (!Helper::isEmpty($arFields['TYPE_PREFIX']))
			$arXmlTags['typePrefix'] = Xml::addTag($arFields['TYPE_PREFIX']);
		if (!Helper::isEmpty($arFields['VENDOR']))
			$arXmlTags['vendor'] = Xml::addTag($arFields['VENDOR']);
		if (!Helper::isEmpty($arFields['MODEL']))
			$arXmlTags['model'] = Xml::addTag($arFields['MODEL']);
		if (!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['description'] = Xml::addTag($arFields['DESCRIPTION']);
		if (!Helper::isEmpty($arFields['SALES_NOTES']))
			$arXmlTags['sales_notes'] = Xml::addTag($arFields['SALES_NOTES']);
		if (!Helper::isEmpty($arFields['MANUFACTURER_WARRANTY']))
			$arXmlTags['manufacturer_warranty'] = Xml::addTag($arFields['MANUFACTURER_WARRANTY']);
		if (!Helper::isEmpty($arFields['COUNTRY_OF_ORIGIN']))
			$arXmlTags['country_of_origin'] = Xml::addTag($arFields['COUNTRY_OF_ORIGIN']);
		if (!Helper::isEmpty($arFields['BARCODE']))
			$arXmlTags['barcode'] = static::getXmlTag_Barcode($intProfileID, $arFields['BARCODE']);

		# Not in example
		if (!Helper::isEmpty($arFields['VENDOR_CODE']))
			$arXmlTags['vendorCode'] = Xml::addTag($arFields['VENDOR_CODE']);
		if (!Helper::isEmpty($arFields['EXPIRY']))
			$arXmlTags['expiry'] = Xml::addTag($arFields['EXPIRY']);
		if (!Helper::isEmpty($arFields['AGE']))
			$arXmlTags['age'] = static::getXmlTag_Age($intProfileID, $arFields['AGE']);
		if (!Helper::isEmpty($arFields['ADULT']))
			$arXmlTags['adult'] = Xml::addTag($arFields['ADULT']);
		if (!Helper::isEmpty($arFields['WEIGHT']))
			$arXmlTags['weight'] = Xml::addTag($arFields['WEIGHT']);
		if (!Helper::isEmpty($arFields['DIMENSIONS']))
			$arXmlTags['dimensions'] = Xml::addTag($arFields['DIMENSIONS']);
		if (!Helper::isEmpty($arFields['DOWNLOADABLE']))
			$arXmlTags['downloadable'] = Xml::addTag($arFields['DOWNLOADABLE']);
		$arXmlTags['param'] = static::getXmlTag_Param($arProfile, $intIBlockID, $arFields);

		# Build XML
		$arXml = array(
				'offer' => array(
						'@' => static::getXmlAttr($intProfileID, $arFields, 'vendor.model'),
						'#' => $arXmlTags,
				),
		);

		# Event handler OnBlizkoRuXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBlizkoRuXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}

		# More data
		$arDataMore = array();
		if (!empty($arFields['GIFTS_ID'])) {
			$arDataMore['GIFTS'] = array(
					'ID' => $arFields['GIFTS_ID'],
					'DESCRIPTION' => $arFields['GIFTS_DESCRIPTION'],
					'URL' => $arFields['GIFTS_URL'],
			);
		}

		# Build result
		$arResult = array(
				'TYPE' => 'XML',
				'DATA' => Xml::arrayToXml($arXml),
				'CURRENCY' => $arFields['CURRENCY_ID'],
				'SECTION_ID' => reset($arElementSections),
				'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
				'DATA_MORE' => $arDataMore,
		);

		# Event handler OnBlizkoRuResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBlizkoRuResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}

		# Ending..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}

}

?>