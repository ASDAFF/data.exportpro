<?php
/**
 *    Settings
 */

namespace Acrit\Core\Crm;

use Bitrix\Main,
    Bitrix\Main\DB\Exception,
    Bitrix\Main\Config\Option,
	\Acrit\Core\Helper;

class Settings
{
	static $MODULE_ID = '';
	var $options;

	public static function setModuleId($value) {
		self::$MODULE_ID = $value;
	}

	public static function get($name, $serialized=false) {
		$value = false;
		if ($name) {
			$value = Helper::getOption(self::$MODULE_ID, $name);
		}
		if ($serialized && $value) {
			$value = unserialize($value);
		}
		return $value;
	}

	public static function set($name, $value, $serialized=false) {
		$result = true;
		if ($serialized) {
			$value = serialize($value);
		}
		Helper::setOption(self::$MODULE_ID, $name, $value);
		return $result;
	}
}
