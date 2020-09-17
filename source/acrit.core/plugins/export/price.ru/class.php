<?
/**
 * Acrit Core: Price.ru base plugin
 * @documentation https://static.price.ru/docs/pricelist_requirements.pdf
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

require_once realpath(__DIR__ . '/../yandex.market/class.php');

class PriceRu extends YandexMarket {
	
	CONST DATE_UPDATED = '2019-03-07';

	public static function getCode() {
		return 'PRICE_RU';
	}

	public static function getName() {
		return static::getMessage('NAME');
	}

}

?>