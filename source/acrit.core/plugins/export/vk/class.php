<?
/**
 * Acrit Core: Vk.com base plugin
 * @documentation https://vk.com/dev/goods_docs
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class Vk extends Plugin {

    CONST DATE_UPDATED = '2019-02-08';

	CONST API_VERSION = '5.80';
	CONST VK_APPLICATION_ID = '5644109';
	
	CONST API_URL = 'https://api.vk.com/method/';
	CONST CATEGORIES_FILENAME = 'categories.txt';

    CONST IMAGE_RESIZE_FILL = 0;
    CONST IMAGE_RESIZE_RESIZE = 1;
    CONST IMAGE_RESIZE_CACHE_TIME = 2592000;

	protected $strFileExt;

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
		return 'VK';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Include classes
	 */
	public function includeClasses(){
		#require_once(__DIR__.'/lib/json.php');
	}

	/**
	 *	Get list of supported currencies
	 */
	public function getSupportedCurrencies(){
		return array('RUB');
	}

	/* END OF BASE STATIC METHODS */

	/**
	 *	Set available extension
	 */
	protected function setAvailableExtension($strExtension){
		$this->strFileExt = $strExtension;
	}

	/**
	 *	Show plugin settings
	 */
	public function showSettings(){
		$this->setAvailableExtension('xml');
		return $this->showDefaultSettings();
	}

	/**
	 *	Custom ajax actions
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult){
		$intProfileID = &$arParams['PROFILE_ID'];
		switch($strAction){
			/*
			case 'exec_console_command':
				$strCommand = $arParams['POST']['command'];
				if(!Helper::isUtf()){
					$strCommand = Helper::convertEncoding($strCommand, 'UTF-8', 'CP1251');
				}
				ob_start();
				eval($strCommand.';');
				$strResult = ob_get_clean();
				ob_start();
				Helper::P($strResult);
				$arJsonResult['Text'] = ob_get_clean();
				break;
			*/
		}
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
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
		// basically [in this class] do nothing, all business logic are in each format
	}

	/* START OF BASE METHODS FOR XML SUBCLASSES */

	/**
	 *	Http request
	 */
	protected function request($method, array $arParams = Array(), $intProfileID) {
		#$arProfile = Profile::getProfiles($intProfileID);
		$arProfile = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
		$access_token = $arProfile['PARAMS']['ACCESS_TOKEN'];
		$arBaseParams = array(
			'access_token' => $access_token,
			'v' => static::API_VERSION,
		);
		if(!Helper::isUtf()){
			$arParams = Helper::convertEncoding($arParams, 'CP1251', 'UTF-8');
		}
		$arParams = array_merge($arBaseParams, $arParams);
		$res = HttpRequest::post(static::API_URL.$method, array(
			'CONTENT' => http_build_query($arParams),
		));
		try {
			$arRes = Json::decode($res);
		}
		catch(Exception $e){}
		usleep(350000);
		return $arRes;
	}

	/**
	 *	Send file to vk
	 */
	protected function sendFileRemote($strFile, $strUrl) {
		if(is_file($strFile)){
			$arFile = \CFile::MakeFileArray($strFile);
			if(is_array($arFile)){
				$strResult = HttpRequest::sendFile($arFile, $strUrl);
				usleep(350000);
				try {
					return Json::decode($strResult);
				}
				catch(Exception $e){
				}
			}
		}
		return false;
	}

	/**
	 *	Get access URL
	 */
	public static function getAccessUrl(){
		$accessUrl = 'https://oauth.vk.com/authorize?client_id='.static::VK_APPLICATION_ID
			.'&scope=friends,wall,groups,offline,photos,video,market&redirect_uri=https://oauth.vk.com/blank.html'
			.'&response_type=token&v='.static::API_VERSION.'&display=page';
		return $accessUrl;
	}
}

?>
