<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

require_once(__DIR__.'/ym_vendormodel.php');

/**
 *	
 */
class YmClothes extends YmVendorModel {
	
	const PLUGIN = 'YANDEX_MARKET';
	const FORMAT = 'YANDEX_MARKET_VENDOR_MODEL';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'ym_clothes';
	
}

?>