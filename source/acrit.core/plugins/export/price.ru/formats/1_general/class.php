<?
/**
 * Acrit Core: Price.ru plugin
 * @documentation https://static.price.ru/docs/pricelist_requirements.pdf
 */

namespace Acrit\Core\Export\Plugins;

use 
		\Bitrix\Main\Localization\Loc,
		\Acrit\Core\Export\Field\Field,
		\Acrit\Core\Helper,
		\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

require_once realpath(__DIR__ . '/../../../yandex.market/formats/2_vendor_model/class.php');

class PriceRuVendorModel extends YandexMarketVendorModel {
	
	CONST DATE_UPDATED = '2019-03-07';

	protected $strRootTag = 'priceru_feed';
	protected $bShopName = false;
	protected $bDelivery = false;
	protected $bEnableAutoDiscounts = false;
	protected $bPlatform = false;
	protected $bZip = false;
	protected $bPromoGift = false;
	protected $bPromoSpecialPrice = false;
	protected $bPromoCode = false;
	protected $bPromoNM = false;

	public static function getCode() {
		return 'PRICE_RU_GENERAL';
	}

	public static function getName() {
		return static::getMessage('NAME');
	}

	public function getDefaultExportFilename() {
		return 'price_ru_vendor.xml';
	}
	
	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		return array();
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		$arResult[] = new Field(array(
			'CODE' => 'NAME',
			'DISPLAY_CODE' => 'name',
			'NAME' => static::getMessage('FIELD_NAME_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_NAME_DESC'),
			'SORT' => 90,
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'NAME',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LOCAL_DELIVERY_COST',
			'DISPLAY_CODE' => 'local_delivery_cost',
			'NAME' => static::getMessage('FIELD_LOCAL_DELIVERY_COST_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_LOCAL_DELIVERY_COST_DESC'),
			'SORT' => 1600,
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		$this->modifyField($arResult, 'MODEL', array('REQUIRED' => false));
		$this->modifyField($arResult, 'VENDOR', array('REQUIRED' => false));
		$this->removeFields($arResult, array('CBID', 'VAT', 'DELIVERY', 'PICKUP', 'STORE', 'MANUFACTURER_WARRANTY', 
			'COUNTRY_OF_ORIGIN', 'ADULT', 'EXPIRY', 'WEIGHT', 'DIMENSIONS', 'DOWNLOADABLE', 'AGE', 'GROUP_ID', 'REC'));
		$this->sortFields($arResult);
		return $arResult;
	}
	
	/**
	 *	Handler 'onProcessElement'
	 */
	protected function onProcessElement(&$arProfile, &$intIBlockID, &$arElement, &$arFields, &$arXml){
		unset($arXml['offer']['@']['type']);
		# Add tag 'local_delivery_cost'
		if(!Helper::isEmpty($arFields['LOCAL_DELIVERY_COST'])){
			$arXml['offer']['#']['local_delivery_cost'] = Xml::addTag($arFields['LOCAL_DELIVERY_COST']);
		}
		# Add tag 'name'
		if(!Helper::isEmpty($arFields['NAME'])){
			Helper::arrayInsert($arXml['offer']['#'], 'name', Xml::addTag($arFields['NAME']), Helper::ARRAY_INSERT_BEGIN);
		}
	}

}

?>