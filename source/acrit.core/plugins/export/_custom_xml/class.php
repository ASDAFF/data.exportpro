<?
/**
 * Acrit Core: Custom XML format
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Export\Plugin;

Loc::loadMessages(__FILE__);

class CustomXml extends Plugin {
	
	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}
	
	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return 'CUSTOM_XML';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		return array();
	}
	
	/**
	 *	Process single element
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		return parent::processElement($arProfile, $intIBlockID, $arElement, $arFields);
	}
	
	/**
	 *	Show notices
	 */
	public function showMessages(){
		print Helper::showNote(static::getMessage('NOTICE_SUPPORT'), true);
	}
	
}

?>