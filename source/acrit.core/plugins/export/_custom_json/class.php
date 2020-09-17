<?
/**
 * Acrit Core: JSON base plugin
 */

namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper,
	\Acrit\Core\Export\UniversalPlugin;

abstract class CustomJson extends UniversalPlugin {
	
	/**
	 *	Show notices
	 */
	public function showMessages(){
		print Helper::showNote(static::getMessage('NOTICE_SUPPORT'), true);
	}
	
}

?>