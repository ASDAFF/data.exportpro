<?
/**
 * Acrit Core: ozon.ru api
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Export\Plugins\OzonRuHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest;

Helper::loadMessages(__FILE__);

class Api {
	
	const URL = 'http://api-seller.ozon.ru';
	
	protected $strClientId;
	protected $strApiKey;
	protected $intProfileId;
	protected $strModuleId;
	
	/**
	 *	Constructor
	 */
	public function __construct($strClientId, $strApiKey, $intProfileId, $strModuleId){
		$this->strClientId = $strClientId;
		$this->strApiKey = $strApiKey;
		$this->intProfileId = $intProfileId;
		$this->strModuleId = $strModuleId;
	}
	
	/**
	 *	Wrapper for Loc::getMessage()
	 */
	public static function getMessage($strMessage, $arReplace=null){
		static $strLang;
		$strFile = realpath(__DIR__.'/../class.php');
		if(is_null($strLang)){
			\Acrit\Core\Export\Exporter::getLangPrefix($strFile, $strLang, $strHead, $strName, $strHint);
		}
		return Helper::getMessage($strLang.$strMessage, $arReplace);
	}
	
	/**
	 *	Save data to log
	 */
	public function addToLog($strMessage, $bDebug=false){
		return Log::getInstance($this->strModuleId)->add($strMessage, $this->intProfileId, $bDebug);
	}
	
	/**
	 *	Is debug mode for log?
	 */
	public function isDebugMode(){
		return Log::getInstance($this->strModuleId)->isDebugMode();
	}
	
	/**
	 *	Execute http-request
	 */
	public function execute($strCommand, $arJson=null, $arParams=null){
		$arParams['HEADER'] = [
			'Client-Id' => $this->strClientId,
			'Api-Key' => $this->strApiKey,
			'Content-Type' => 'application/json',
		];
		if(is_array($arJson)){
			$arParams['CONTENT'] = Json::encode($arJson, JSON_UNESCAPED_SLASHES);
		}
		elseif(is_string($arJson)){
			$arParams['CONTENT'] = $arJson;
		}
		$strJson = HttpRequest::getHttpContent(static::URL.$strCommand, $arParams);
		if(strlen($strJson)){
			$arJson = Json::decode($strJson);
			if(is_array($arJson['error']) && !empty($arJson['error'])){
				$strMessage = 'ERROR_GENERAL'.($this->isDebugMode() ? '_DEBUG' : '');
				$strMessage = sprintf(static::getMessage($strMessage,  [
					'#COMMAND#' => $strCommand,
					'#JSON#' => $arParams['CONTENT'],
				]));
				$this->addToLog($strMessage);
			}
			return $arJson;
		}
		$strMessage = 'ERROR_REQUEST'.($this->isDebugMode() ? '_DEBUG' : '');
		$strMessage = sprintf(static::getMessage($strMessage,  [
			'#COMMAND#' => $strCommand,
			'#JSON#' => $arParams['CONTENT'],
			'#RESPONSE#' => $strJson,
		]));
		$this->addToLog($strMessage);
		return false;
	}
	
	/**
	 *	Get headers from last request
	 */
	public function getHeaders(){
		return HttpRequest::getHeaders();
	}

}

