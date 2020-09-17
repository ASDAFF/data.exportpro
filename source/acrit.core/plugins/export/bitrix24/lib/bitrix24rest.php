<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Loc::loadMessages(__FILE__);

/**
 * Class Rest
 * @package Acrit\Core
 */
 
class Bitrix24Rest {

	// Execute REST-query
	static function executeMethod($method, $strModuleId, $params, $intProfileID, $only_res=true) {
		$result = false;
		$arAuthData = self::getAuthData($strModuleId, $intProfileID);
		if ($arAuthData) {
			self::controlLimits();
			$rest_url = 'https://' . $arAuthData['PORTAL'] . '/rest/' . $arAuthData['USER_ID'] . '/' . $arAuthData['WEBHOOK'] . '/' . $method . '/';
			$arResp = self::executeHTTPRequest($rest_url, $params);
			if ($only_res) {
				$result = $arResp['result'];
			}
			else {
				$result = $arResp;
			}
		}
		return $result;
	}

	static function executeHTTPRequest($queryUrl, array $params = array()) {
		$result = array();
		/*$res = HttpRequest::post($queryUrl, array(
			'CONTENT' => http_build_query($params),
		));*/
		$queryData = http_build_query($params);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_POST => 1,
			CURLOPT_URL => $queryUrl,
			CURLOPT_POSTFIELDS => $queryData,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
		));
		$res = curl_exec($curl);
		curl_close($curl);
		try {
			$result = Json::decode($res);
		}
		catch(\Exception $e){}
		return $result;
	}

	static function getAuthData($strModuleId, $intProfileID) {
		#$arProfile = Profile::getProfiles($intProfileID);
		$arProfile = Helper::call($strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
		$res = false;
		if ($arProfile) {
			$res = [
				'USER_ID' => $arProfile['PARAMS']['ACCESS_USER_ID'],
				'WEBHOOK' => $arProfile['PARAMS']['ACCESS_WEBHOOK'],
				'PORTAL' => $arProfile['PARAMS']['ACCESS_PORTAL'],
			];
		}
		return $res;
	}


	/**
	 * Limits control
	 */

	static function controlLimits() {
		$delay = 0;
		// Get values
		$last_exec = \Bitrix\Main\Config\Option::get(ACRIT_CORE,'rest_last_exec');
		$count_exec = \Bitrix\Main\Config\Option::get(ACRIT_CORE,'rest_count_exec');
		// Waiting for end of executions
		$current_exec = microtime(true);
		if ($current_exec < $last_exec) {
			$diff = $last_exec - $current_exec;
			$delay += $diff * 1000000;
			$current_exec = $last_exec;
		}
		// Update limits
		$diff = $current_exec - $last_exec;
		$count_exec -= $diff * 2;
		$count_exec = $count_exec >= 0 ? $count_exec : 0;
		$count_exec++;
		// Calc delay
		if ($count_exec > 30) {
			$diff = 1;
			$delay += $diff * 1000000;
			$current_exec += $diff;
			$count_exec -= $diff * 1;
		}
		// Save values
		\Bitrix\Main\Config\Option::set(ACRIT_CORE,'rest_last_exec', $current_exec);
		\Bitrix\Main\Config\Option::set(ACRIT_CORE,'rest_count_exec', $count_exec);
		// Delay
		if ($delay) {
			usleep($delay);
		}
	}

	// Convert encoding
	static function convEncForPortal($value) {
		if (LANG_CHARSET == 'windows-1251') {
			if (!is_array($value)) {
				$value = mb_convert_encoding($value, 'UTF-8', 'CP1251');
			}
			else {
				foreach ($value as $k => $value_val) {
					if (!is_array($value_val)) {
						$value[$k] = mb_convert_encoding($value_val, 'UTF-8', 'CP1251');
					}
				}
			}
		}
		return $value;
	}
}
?>