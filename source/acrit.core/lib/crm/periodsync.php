<?php
/**
 *    Periodical synchronization
 */

namespace Acrit\Core\Crm;

use Bitrix\Main,
    Bitrix\Main\DB\Exception,
    Bitrix\Main\Config\Option,
	\Acrit\Core\Helper;

class PeriodSync
{
	static $MODULE_ID = '';

	public static function setModuleId($value) {
		self::$MODULE_ID = $value;
		Settings::setModuleId($value);
	}

	public static function set($profile_id) {
		$result = true;
		self::remove($profile_id);
		$profile = Helper::call(self::$MODULE_ID, 'CrmProfiles', 'getProfiles', [$profile_id]);
		// Create agent
		$sync_schedule = $profile['SYNC']['add']['period'];
		$agent_period = $sync_schedule * 60;
		if ($agent_period) {
			\CAgent::AddAgent("\\Acrit\\Core\\Crm\\PeriodSync::run('".self::$MODULE_ID."', $profile_id);", self::$MODULE_ID, "N", $agent_period);
		}
		return $result;
	}

	public static function remove($profile_id) {
		$result = true;
		// Remove agent
		\CAgent::RemoveAgent("\\Acrit\\Core\\Crm\\PeriodSync::run('".self::$MODULE_ID."', $profile_id);", self::$MODULE_ID);
		return $result;
	}

	// Run sync
	public static function run($module_id, $profile_id) {
		// Profile data
		$profile = Helper::call($module_id, 'CrmProfiles', 'getProfiles', [$profile_id]);
		$sync_active = ($profile['ACTIVE'] == 'Y');
		// Run sync
		if ($sync_active) {
			Controller::setModuleId($module_id);
			Controller::setProfile($profile_id);
			$sync_interval = self::getSyncInterval($profile);
			Settings::set('last_update_ts', time());
			Controller::syncStoreToCRM($sync_interval);
		}
		return "\\Acrit\\Core\\Crm\\PeriodSync::run('$module_id', $profile_id);";
	}

	public static function getSyncInterval($profile) {
		$profile_period = (int)$profile['SYNC']['add']['period'] * 60 * 3;
		$last_update_period = 0;
		$last_update_ts = Settings::get('last_update_ts');
		if ($last_update_ts) {
			$last_update_period = time() - $last_update_ts;
		}
		$sync_interval = $last_update_period > $profile_period ? $last_update_period : $profile_period;
		return $sync_interval;
	}
}
