<?
/**
 * Acrit Core: Yandex.Turbo plugin
 * @documentation https://yandex.ru/support/partnermarket/offers.html#offers__list
 */

namespace Acrit\Core\Export\Plugins;

use 
		\Bitrix\Main\Localization\Loc,
		\Acrit\Core\Export\Field\Field,
		\Acrit\Core\Helper,
		\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

require_once realpath(__DIR__ . '/../../../yandex.market/class.php');
require_once realpath(__DIR__ . '/../../../yandex.market/formats/1_simple/class.php');

class YandexTurboSimple extends YandexMarketSimple {
	
	CONST DATE_UPDATED = '2019-09-04';
	
	CONST YANDEX_PLUGIN_ID = '6B32B333E0E9E2D88FECC4EC73723DBD';

	protected $bShopName = true;
	protected $bDelivery = true;
	protected $bEnableAutoDiscounts = true;
	protected $bPlatform = true;
	protected $bZip = false;
	protected $bPromoGift = false;
	protected $bPromoSpecialPrice = false;
	protected $bPromoCode = false;
	protected $bPromoNM = false;

	public static function getCode() {
		return 'YANDEX_TURBO_SIMPLE';
	}

	public static function getName() {
		return static::getMessage('NAME');
	}

	public function getDefaultExportFilename() {
		return 'yandex_turbo_yml_simple.xml';
	}
	
	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		return array();
	}
	
	/**
	 *	
	 */
	protected function onStepExportWriteXmlShop(&$arXml){
		#$arXml['turbo:cms_plugin'] = Xml::addTag(static::YANDEX_PLUGIN_ID);
	}

}

?>