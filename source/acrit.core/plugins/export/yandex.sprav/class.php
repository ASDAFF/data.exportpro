<?
/**
 * Acrit Core: Yandex.Spravochnik plugin
 * @https://yandex.ru/sprav/1530227/edit/price-lists/
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Log;

Helper::loadMessages(__FILE__);

class YandexSprav extends Plugin {
	
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
		return 'YANDEX_SPRAV';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Get adailable fields for current plugin
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
		//
	}
	
}

?>