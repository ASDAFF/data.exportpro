<?

/**
 * Acrit Core: TiuRu base plugin
 * @package acrit.core
 * @copyright 2019 Acrit
 */

namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

require_once realpath(__DIR__ . '/../yandex.market/class.php');

class TiuRu extends YandexMarket
{

	public static function getCode()
	{
		return 'TIU_RU';
	}

	public static function getName()
	{
		return static::getMessage('NAME');
	}

}

?>