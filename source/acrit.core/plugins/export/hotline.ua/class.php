<?
/**
 * Acrit Core: Hotline.ua plugin
 * @documentation https://hotline.ua/about/pricelists_specs/#tr1
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Acrit\Core\Helper,
		\Acrit\Core\Export\Plugin;

Loc::loadMessages(__FILE__);

class HotlineUa extends Plugin
{

	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId)
	{
		parent::__construct($strModuleId);
	}

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode()
	{
		return 'HOTLINE_UA';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName()
	{
		return static::getMessage('NAME');
	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false)
	{
		return array();
	}

	/**
	 * 	Process single element
	 * 	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields)
	{
		return parent::processElement($arProfile, $intIBlockID, $arElement, $arFields);
	}

}

?>