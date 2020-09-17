<?
/**
 * Acrit Core: MarketGid plugin
 * @documentation https://dashboard.marketgid.com/index/teaser-goods-export-requirements
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Bitrix\Main\EventManager,
		\Acrit\Core\Helper,
		\Acrit\Core\Export\Exporter,
		\Acrit\Core\Xml,
		\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class MarketGidSimple extends MarketGid {

	CONST DATE_UPDATED = '2019-03-07';

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
		return parent::getCode() . '_SIMPLE';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}

	/* END OF BASE STATIC METHODS */

	public function getDefaultExportFilename() {
		return 'marketgid.xml';
	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);

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
		$arXmlTags['categoryId'] = $this->getXmlTag_Category($arProfile, $arElement);
		if (!Helper::isEmpty($arFields['URL']))
			$arXmlTags['url'] = $this->getXmlTag_Url($intProfileID, $arFields['URL'], $arFields);
		if (!Helper::isEmpty($arFields['PICTURE']))
			$arXmlTags['picture'] = Xml::addTag($arFields['PICTURE']);

		if (!Helper::isEmpty($arFields['NAME']))
			$arXmlTags['title'] = Xml::addTag($arFields['NAME']);
		if (!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['text'] = Xml::addTag($arFields['DESCRIPTION']);

		if (!Helper::isEmpty($arFields['PRICE'])) {

			$arXmlTags['price'] = [[
			'@' => ['currency' => $arFields['CURRENCY_ID']],
			'#' => $arFields['PRICE'],
			]];
		}



		# Build XML
		$arXml = array(
				'teaser' => array(
						'@' => $this->getXmlAttr($intProfileID, $arFields),
						'#' => $arXmlTags,
				),
		);

		# Event handler OnMarketGidXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnMarketGidXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}


		# Build result
		$arResult = array(
				'TYPE' => 'XML',
				'DATA' => Xml::arrayToXml($arXml),
				'CURRENCY' => $arFields['CURRENCY_ID'],
				'SECTION_ID' => reset($arElementSections),
				'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
		);

		# Event handler OnMarketGidResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnMarketGidResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}

		# Ending..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}

}

?>