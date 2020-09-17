<?
/**
 *	Logging class (uses for all other modules)
 *	@example Log::getInstance($strModuleId)->add('My message', $intProfileId);
 */

namespace Acrit\Core;

use 
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

class Log {
	
	const PREVIEW_SIZE = 50; // 50 Kb
	const DETAIL_SIZE = 5120; // 5 Mb
	const LOG_DELTA = 100; // 100 Kb
	const DOWNLOAD_PARAM = 'download';
	const DOWNLOAD_PARAM_Y = 'Y';
	
	protected static $arModuleObjects = [];
	
	protected $strModuleId;
	
	/**
	 *	
	 */
	protected function __construct($strModuleId){
		$this->strModuleId = $strModuleId;
	}
	
	/**
	 *	Get instance for selected module
	 */
	public static function getInstance($strModuleId){
		$arModuleObjects = &static::$arModuleObjects;
		if(!array_key_exists($strModuleId, $arModuleObjects)){
			$arModuleObjects[$strModuleId] = new static($strModuleId);
		}
		return $arModuleObjects[$strModuleId];
	}
	
	/**
	 *	Save message to log
	 */
	public function add($mMessage, $intProfileID=false, $bDebug=false){
		if(!$this->isLoggingOn()){
			return;
		}
		if($bDebug && !$this->isDebugMode()){
			return;
		}
		if(is_array($mMessage)) {
			$mMessage = print_r($mMessage, true);
		}
		elseif($mMessage === false){
			$mMessage = '~FALSE~';
		}
		elseif($mMessage === true){
			$mMessage = '~TRUE~';
		}
		elseif($mMessage === null){
			$mMessage = '~NULL~';
		}
		elseif(is_object($mMessage)){
			ob_start();
			var_dump($mMessage);
			$mMessage = ob_get_clean();
		}
		#
		$strMicro = microtime(true);
		$strMicro = $strMicro - floor($strMicro);
		$strMicro = number_format($strMicro, 4, '.', '');
		$strMicro = substr($strMicro, 1);
		#
		$bNoFile = false;
		$strFilename = $this->getLogFilename($intProfileID);
		if(!is_file($strFilename)){
			$bNoFile = true;
			Helper::createDirectoriesForFile($strFilename, true);
		}
		#
		$mMessage = '['.date('d.m.Y H:i:s').$strMicro.'] '.$mMessage."\n";
		#
		$intLogMaxSize = $this->getLogMaxSize();
		if($intLogMaxSize > 0){
			$intCurrentSize = $this->getLogSize($intProfileID);
			$intNewSize = ($intLogMaxSize - static::LOG_DELTA * 1024) * 1024;
			if($intCurrentSize >= $intNewSize){
				$strFileContent = file_get_contents($strFilename, false, null, -1 * $intNewSize);
				file_put_contents($strFilename, $strFileContent);
				unset($strFileContent);
			}
		}
		#
		$intBytes = file_put_contents($strFilename, $mMessage, FILE_APPEND | LOCK_EX);
		#
		if($bNoFile){
			Helper::changeFileOwner($strFilename);
		}
		#
		unset($strMicro, $strFilename);
		return $intBytes > 0;
	}
	
	/**
	 *	Is logging turned on?
	 */
	public function isLoggingOn(){
		$bCoreLogging = Helper::getOption(ACRIT_CORE, 'log_write', '') != 'N';
		$bModuleLogging = Helper::getOption($this->strModuleId, 'log_write', '') != 'N';
		return $bCoreLogging && $bModuleLogging;
	}
	
	/**
	 *	Is debug mode?
	 */
	public function isDebugMode(){
		$bCoreDebug = Helper::getOption(ACRIT_CORE, 'debug_mode', '') == 'Y';
		$bModuleDebug = Helper::getOption($this->strModuleId, 'debug_mode', '') == 'Y';
		$bCliDebug = defined('ACRIT_EXP_DEBUG') && ACRIT_EXP_DEBUG === true || defined('ACRIT_DEBUG') && ACRIT_DEBUG === true;
		return $bCoreDebug || $bModuleDebug || $bCliDebug;
	}
	
	/**
	 *	Get log max size
	 */
	public function getLogMaxSize(){
		$intResult = 0;
		$intCoreLogMaxSize = Helper::getOption(ACRIT_CORE, 'log_max_size', 0);
		$intModuleLogMaxSize = Helper::getOption($this->strModuleId, 'log_max_size', 0);
		if(defined('ACRIT_EXP_LOG_MAX_SIZE') && is_numeric(ACRIT_EXP_LOG_MAX_SIZE) && ACRIT_EXP_LOG_MAX_SIZE > 0){
			$intResult = ACRIT_EXP_LOG_MAX_SIZE;
		}
		elseif(is_numeric($intModuleLogMaxSize) && $intModuleLogMaxSize > 0){
			$intResult = $intModuleLogMaxSize;
		}
		elseif(is_numeric($intCoreLogMaxSize) && $intCoreLogMaxSize > 0){
			$intResult = $intCoreLogMaxSize;
		}
		$intResult *= 1024 * 1024;
		return round($intResult);
	}
	
	/**
	 *	Get filename of log
	 */
	public function getLogFilename($intProfileID=false, $strRelative=false){
		$strServerId = Helper::getOption('main', 'server_uniq_id', '');
		$strUploadDir = Helper::getOption('main', 'upload_dir', 'upload');
		$strBasename = 'log'.($intProfileID ? '_'.$intProfileID : '').'.'.$strServerId.'.txt';
		$strResult = \Bitrix\Main\Loader::getDocumentRoot().'/'.$strUploadDir.'/'.$this->strModuleId.'/log/'.$strBasename;
		if($strRelative){
			$strResult = substr($strResult, strlen(\Bitrix\Main\Context::getCurrent()->getServer()->getDocumentRoot()));
		}
		return $strResult;
	}
	
	/**
	 *	Get log filesize
	 */
	public function getLogSize($intProfileID=false, $bFormat=false){
		$strLogFilename = $this->getLogFilename($intProfileID, false);
		$strLogSize = is_file($strLogFilename) ? filesize($strLogFilename) : 0;
		if($bFormat){
			if(!$strLogSize){
				$strLogSize = '0 '.GetMessage('FILE_SIZE_Kb');
			}
			else {
				$strLogSize = \CFile::FormatSize($strLogSize);
			}
		}
		return $strLogSize;
	}
	
	/**
	 *	Get log max filesize
	 */
	public function getMaxSize($bPreview=true, $bFormat=true){
		if($bFormat){
			if($bPreview){
				return Helper::formatSize(static::PREVIEW_SIZE * 1024);
			}
			else{
				return Helper::formatSize(static::DETAIL_SIZE * 1024);
			}
		}
		else{
			return static::PREVIEW_SIZE * 1024;
		}
	}
	
	/**
	 *	Delete log file
	 */
	public function deleteLog($intProfileID=false){
		$strLogFileName = $this->getLogFilename($intProfileID);
		if (strlen($strLogFileName) && is_file($strLogFileName) && filesize($strLogFileName)){
			@unlink($strLogFileName);
		}
	}
	
	/**
	 *	Get log preview content
	 */
	public function getLogPreview($intProfileID=false){
		return $this->getLog($intProfileID, false);
	}
	
	/**
	 *	Get log detail content
	 */
	public function getLogDetail($intProfileID=false){
		return $this->getLog($intProfileID, true);
	}
	
	/**
	 *	Get log (preview || detail)
	 */
	public function getLog($intProfileID=false, $bDetail=false){
		$strLogFileName = $this->getLogFilename($intProfileID);
		if (strlen($strLogFileName) && is_file($strLogFileName) && filesize($strLogFileName)){
			$intSizeReal = $this->getLogSize($intProfileID);
			$intSizeCut = ($bDetail ? static::DETAIL_SIZE : static::PREVIEW_SIZE) * 1024;
			if($intSizeCut > $intSizeReal){
				$intSizeCut = $intSizeReal;
			}
			return $this->getFileEndData($strLogFileName, $intSizeCut);
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function getFileEndData($strFile, $intSize){
		$resHandle = fopen($strFile, 'r');
		fseek($resHandle, -1 * $intSize, SEEK_END);
		$strResult = fread($resHandle, $intSize);
		fclose($resHandle);
		return $strResult;
	}
	
	/**
	 *	Get url for log
	 */
	public function getLogUrl($intProfileID=false, $bDownload=false){
		$arGet = array(
			'module' => $this->strModuleId,
		);
		if($intProfileID){
			$arGet['profile'] = $intProfileID;
		}
		if($bDownload){
			$arGet[static::DOWNLOAD_PARAM] = static::DOWNLOAD_PARAM_Y;
		}
		$arGet['lang'] = LANGUAGE_ID;
		$strResult = 'acrit_core_log.php?'.http_build_query($arGet);
		if(!defined('ADMIN_SECTION')){
			$strResult = '/bitrix/admin/';
		}
		return $strResult;
	}
	
	/**
	 *	Show log
	 */
	public function showLog($intProfileId=null, $bFull=false){
		#\CJSCore::init('acrit-core-log');
		\Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/'.ACRIT_CORE.'/log.js');
		print Helper::getHtmlObject($this->strModuleId, $intProfileId, 'log', ($bFull ? 'full' : 'lite'));
	}
	
	/**
	 *	Download log file
	 */
	public function downloadLog($intProfileID=null){
		$strLogFileName = $this->getLogFilename($intProfileID);
		$strDownloadFilename = $intProfileID > 0 ? 'log_'.$this->strModuleId.'_profile_'.$intProfileID : 'log_'.$this->strModuleId;
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$strDownloadFilename.'.txt');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		if(is_file($strLogFileName)){
			header('Content-Length: '.filesize($strLogFileName));
			readfile($strLogFileName);
		}
	}

}
?>