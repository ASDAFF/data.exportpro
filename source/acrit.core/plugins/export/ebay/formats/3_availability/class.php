<?
/**
* Acrit Core: Ebay.com plugin
* @package acrit.core
* @copyright 2018 Acrit
* @documentation http://pages.ebay.com/ru/ru-ru/kak-prodavat-na-ebay-spravka/mip-neobhodimie-dannie.html
*/
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Log,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class EbayAvailability extends Ebay {

    CONST DATE_UPDATED = '2019-08-08';

    protected $strFileExt;

    /**
     * Base constructor.
     */
    public function __construct($strModuleId) {
        parent::__construct($strModuleId);
    }

    /* START OF BASE STATIC METHODS */

    /**
     * Get plugin unique code ([A-Z_]+)
     */

    public static function getCode() {
        return parent::getCode().'_AVAILABILITY';
    }

    /**
     * Get plugin short name
     */
    public static function getName() {
        return static::getMessage('NAME');
    }

    /**
     *	Is it subclass?
     */
    public static function isSubclass(){
        return true;
    }

    /* END OF BASE STATIC METHODS */

    public function getDefaultExportFilename(){
        return 'ebay_availability.xml';
    }

    /**
     *	Get adailable fields for current plugin
     */
    public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
        $arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);

        $arResult[] = new Field(array(
            'CODE' => 'QUANTITY',
            'DISPLAY_CODE' => 'quantity',
            'NAME' => static::getMessage('FIELD_QUANTITY_NAME'),
            'SORT' => 110,
            'DESCRIPTION' => static::getMessage('FIELD_QUANTITY_DESC'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_QUANTITY',
                ),
            ),
        ));
        $this->sortFields($arResult);
        return $arResult;
        #
    }

    /**
     *	Process single element
     *	@return array
     */
    public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
        // basically [in this class] do nothing, all business logic are in each format
        $intProfileID = $arProfile['ID'];
        $intElementID = $arElement['ID'];
        # Get site URL
        $strSiteURL = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y');
        # Build XML
        $arXmlTags = array();

        if(!Helper::isEmpty($arFields['ID']))
            $arXmlTags['SKU'] = Xml::addTag($arFields['ID']);
        if(!Helper::isEmpty($arFields['QUANTITY']))
            $arXmlTags['totalShipToHomeQuantity'] = Xml::addTag($arFields['QUANTITY']);

        # build XML
        $arXml = array(
            'inventory' => array(
                '#' => $arXmlTags,
            ),
        );
        foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnEbayXml') as $arHandler) {
            ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
        }
        $strXml = Xml::arrayToXml($arXml);
				if(!Helper::isUtf()){
					$strXml = Helper::convertEncoding($strXml, 'CP1251', 'UTF-8');
				}
        # build result
        $arResult = array(
            'TYPE' => 'XML',
            'DATA' => $strXml,
            'CURRENCY' => '',
            'SECTION_ID' => $this->getElement_SectionID($intProfileID, $arElement),
            'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
            'DATA_MORE' => array(),
        );
        foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnEbayResult') as $arHandler) {
            ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
        }
        # after..
        unset($intProfileID, $intElementID, $strSiteURL, $arXmlTags, $arXml);
        return $arResult;
    }
}
?>