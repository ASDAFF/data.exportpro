<?
namespace Acrit\Core\Export\Plugins;

use	
	\Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Loc::loadMessages(__FILE__);

/**
 * Class Rest
 * @package Acrit\Core
 */
class BitrixRest {

	// Execute REST-query
	static function executeMethod($strModuleId, $method, $params, $intProfileID, $only_res=true) {
		$result = false;
		$arAuthData = self::getAuthData($strModuleId, $intProfileID);
		if ($arAuthData) {
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
		if ($res) {
			try {
				$result = Json::decode($res);
			} catch (Exception $e) {
			}
		}
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