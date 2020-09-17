<?
/**
 * Acrit Core: zakupki.mos.ru
 */

namespace Acrit\Core\Export\Plugins;

use
	\Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\UniversalPlugin;

Loc::loadMessages(__FILE__);

require_once realpath(__DIR__ . '/../yandex.market/class.php');

abstract class ZakupkiMosRu extends UniversalPlugin {
	
	//
	
}

?>