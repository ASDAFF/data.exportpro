<?
/**
 *	Class to work with command-line-interface, requires php-function 'exec' is enabled
 */
namespace Acrit\Core;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Thread,
	\Acrit\Core\Log;

Helper::loadMessages(__FILE__);

class Cli {
	
	const CRON_JOB = '#^(([a-z0-9*/,]+)\s+([a-z0-9*/,]+)\s+([a-z0-9*/,]+)\s+([a-z0-9*/,]+)\s+([a-z0-9*/,]+))\s+(.*?)$#';
	const MULTITHREAD_TEST_SUCCESS = 'SUCCESS';
	
	static $bIsRoot;
	static $strSiteId;
	static $intBitrixUser;
	static $arError;
	
	/**
	 *	Check if script are executing by cli (command-line-interface)
	 */
	public static function isCli(){
		return php_sapi_name() == 'cli';
	}
	
	/**
	 *	Check ifuser is root
	 */
	public static function isRoot(){
		if(is_null(static::$bIsRoot) && static::isExec() && static::isLinux()){
			@exec('whoami', $arExec);
			static::$bIsRoot = count($arExec) == 1 && reset($arExec) == 'root';
		}
		return static::$bIsRoot;
	}
	
	/**
	 *	
	 */
	public static function setSiteId($strSiteId){
		static::$strSiteId = $strSiteId;
	}
	
	/**
	 *	Check ifuser is root
	 */
	public static function getBitrixUser(){
		if(!is_numeric(static::$intBitrixUser) && function_exists('fileowner')){
			$strDir = $_SERVER['DOCUMENT_ROOT'].'/upload/';
			if(!is_dir($strDir)){
				$strDir = $_SERVER['DOCUMENT_ROOT'];
			}
			static::$intBitrixUser = fileowner($strDir);
		}
		return static::$intBitrixUser;
	}
	
	/**
	 *	Get process ID
	 */
	public static function getPid(){
		return getMyPid();
	}
	
	/**
	 *	Is OS linux?
	 */
	public static function isLinux(){
		return stripos(PHP_OS, 'linux') !== false;
	}
	
	/**
	 *	Is OS windows?
	 */
	public static function isWindows(){
		return stripos(PHP_OS, 'win') !== false;
	}
	
	/**
	 *	Check ifhosting is timeweb
	 */
	public static function isHostingTimeweb(){
		if(static::isExec() && static::isLinux()) {
			@exec('uname -a', $arExec);
			if(stripos($arExec[0], 'timeweb.ru') !== false) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Is `exec` function available?
	 */
	public static function isExec(){
		return function_exists('exec');
	}
	
	/**
	 *	Is `proc_open` function available?
	 */
	public static function isProcOpen(){
		return function_exists('proc_open');
	}
	
	/**
	 *	Parse parameters passed to php-file
	 */
	public static function getCliArguments($strArgumentKey=null){
		global $argv;
		$arResult = array();
		if(is_array($argv)){
			foreach(array_slice($argv, 1) as $strArgument){
				parse_str($strArgument, $arArgument);
				if(is_array($arArgument)) {
					$arResult = array_merge($arResult, $arArgument);
				}
			}
		}
		if(is_string($strArgumentKey)){
			return $arResult[$strArgumentKey];
		}
		return $arResult;
	}
	
	/**
	 *	Check ifcrontab can be managed by this script (only ifLinux OS)
	 */
	public static function canAutoSet(){
		if(static::isManuallyDisabled()){
			return false;
		}
		if(static::isLinux() && static::isExec()) {
			$strPhpFile = Helper::root().'/bitrix/modules/'.Helper::id().'/cli/check_autoset.php';
			$strCommand = 'php '.$strPhpFile.' > /dev/null 2>&1'; // ToDo! 
			$strSchedule = '00 00 01 01 01';
			if(static::addCronTask(ACRIT_CORE, $strCommand, $strSchedule)) {
				static::deleteCronTask(ACRIT_CORE, $strCommand, $strSchedule);
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Check if cli is manually disabed in module settings
	 */
	public static function isManuallyDisabled(){
		return Helper::getOption(ACRIT_CORE, 'disable_crontab_set') == 'Y';
	}
	
	/**
	 *	Compile command from elements
	 */
	public static function buildCommand($strModuleId, $strPhpPath, $strCommand, $strScriptName=null, $bSetMbstring=true, $strConfig='', $strOutput=''){
		$arCommand = array();
		$arCommand[] = $strPhpPath;
		if($bSetMbstring) {
			$arCommand[] = static::getMbstringParams();
		}
		if(strlen($strConfig)){
			$arCommand[] = $strConfig;
		}
		if(is_numeric($strCommand)){ // ifit is profile ID
			$strCommand = static::getProfilePhpCommand($strModuleId, $strCommand, $strScriptName);
		}
		$arCommand[] = '-f '.$strCommand;
		if(strlen($strOutput)){
			$arCommand[] = '>> '.$strOutput.' 2>&1';
		}
		return implode(' ', $arCommand);
	}
	
	/**
	 *	Get mbstring params for command
	 */
	public static function getMbstringParams(){
		if(Helper::isUtf()) {
			return '-d mbstring.func_overload=2 -d mbstring.internal_encoding=UTF-8';
		}
		else {
			return '-d mbstring.func_overload=0 -d mbstring.internal_encoding=CP1251';
		}
		return 'php';
	}
	
	/**
	 *	Get full path to cron php-file
	 */
	public static function getPhpFile($strModuleId, $strFile=null){
		$strFile = strlen($strFile) ? $strFile : 'import.php';
		return Helper::root().'/bitrix/modules/'.$strModuleId.'/cli/'.$strFile;
	}
	
	/**
	 *	Get profile command for cron (without php and configs)
	 */
	public static function getProfilePhpCommand($strModuleId, $intProfileID, $strScriptName, $arArguments=null, $bClear=false){
		$strScriptName = !is_null($strScriptName) ? $strScriptName : 'run.php';
		$arArgumentsTmp = [];
		if(!is_null($intProfileID)){
			$arArgumentsTmp['profile'] = $intProfileID;
		}
		if(is_array($arArguments)){
			foreach($arArguments as $key => $value){
				$arArgumentsTmp[$key] = $value;
			}
		}
		$arArgumentsTmp['auto'] = 'Y';
		if(strlen(static::$strSiteId) && !$bClear && Helper::getOption(ACRIT_CORE, 'php_add_site') != 'N'){
			$arArgumentsTmp['site'] = static::$strSiteId;
		}
		$arArguments = $arArgumentsTmp;
		unset($arArgumentsTmp);
		$strCommand = static::getPhpFile($strModuleId, $strScriptName);
		foreach($arArguments as $key => $value){
			$strCommand .= ' '.$key.'='.(is_array($value)?implode(',', $value):$value);
		}
		return $strCommand;
	}
	
	/**
	 *	Check wheather profile is configured in crontab
	 */
	public static function isProfileOnCron($strModuleId, $intProfileID, $strScriptName){
		$strCommand = static::getProfilePhpCommand($strModuleId, $intProfileID, $strScriptName, null, true);
		return static::isCronTaskConfigured($strModuleId, $strCommand);
	}
	
	/**
	 *	Delete task for profile
	 */
	public static function deleteProfileCron($strModuleId, $intProfileID, $strScriptName){
		$strCommandClear = static::getProfilePhpCommand($strModuleId, $intProfileID, $strScriptName, null, true);
		return static::deleteCronTask($strModuleId, $strCommandClear);
	}
	
	/**
	 *	Get path to default php binary
	 */
	public static function getDefaultPhpPath(){
		if(static::isLinux() && static::isExec()) {
			@exec('which php', $arExecResult);
			if(is_array($arExecResult) && strlen($arExecResult[0])){
				return $arExecResult[0];
			}
		}
		return 'php';
	}
	
	/**
	 *	Try to get path to php binary (cli)
	 */
	public static function getPhpPath(){
		if(static::isLinux() && static::isExec()) {
			$arPhpVariants = array();
			# Detect from PHP_BINARY
			if(defined('PHP_BINARY') && strlen(PHP_BINARY)){
				$strBinary = PHP_BINARY;
				$strBinary = str_replace('/bin/php-cgi', '/bin/php', $strBinary);
				$strBinary = str_replace('-cgi', '', $strBinary);
				$arPhpVariants[] = $strBinary;
			}
			# Detect from whereis
			$arPotentialPhpPaths = static::getPotentialPhpPaths();
			foreach($arPotentialPhpPaths as $strPath){
				$arPhpVariants[] = $strPath;
			}
			# Check detected variants
			$strUsedPhpVersion = static::getSitePhpVersion();
			foreach($arPhpVariants as $strPath){
				@exec($strPath.' -v', $arOutput);
				foreach($arOutput as $strOutput){
					if(preg_match('#PHP\s?(\d+\.\d+.\d+)#i', $strOutput, $arMatch)) {
						$strCheckVersion = $arMatch[1];
						if($strCheckVersion == $strUsedVersion) {
							return $strPath;
						}
					}
				}
				unset($arOutput);
			}
		}
		# Return default value
		return static::getDefaultPhpPath();
	}
	
	/**
	 *	Get potential php-paths
	 */
	public static function getPotentialPhpPaths(){
		$arResult = [];
		if(static::isLinux() && static::isExec()) {
			@exec('whereis php', $arExecResult);
			if(preg_match('#^php:\s?(.*?)$#i', $arExecResult[0], $arMatch)){
				$arExecResult = array_slice(explode(' ', $arExecResult[0]), 1);
				$arExclude = [
					'#\.gz$#i',
					'#\.ini$#i',
					'#^/etc/#i',
					'#^/usr/lib/#i',
					'#^/usr/lib64/#i',
					'#^/usr/share/#i',
				];
				foreach($arExecResult as $strPath){
					$bExcluded = false;
					foreach($arExclude as $strPattern){
						if(preg_match($strPattern, $strPath)){
							$bExcluded = true;
							break;
						}
					}
					if(!$bExcluded){
						$arResult[] = $strPath;
					}
				}
			}
		}
		return $arResult;
	}

	/**
 	 *	Get used PHP version
	 */
	public static function getSitePhpVersion(){
		$strResult = PHP_VERSION;
		if(preg_match('#(\d+\.\d+.\d+)#', $strResult, $arMatch)){
			$strResult = $arMatch[0];
		}
		return $strResult;
	}
	
	/**
	 *	Check threads ase supported
	 */
	public static function isMultithreadingSupported(){
		if(!static::isProcOpen()){
			return false;
		}
		$arCommand = static::getFullCommand(ACRIT_CORE, 'check_thread.php');
		$arArguments = array(
			'profile' => '0',
			'iblock' => '0',
			'id' => '0',
		);
		$obThread = new Thread($arCommand['COMMAND'], $arArguments);
		$fTime = microtime(true);
		$intMaxTime = 3;
		while($obThread->isRunning()){
			if(microtime(true) - $fTime >= $intMaxTime){
				break;
			}
			usleep(100000);
		}
		if(!$obThread->isRunning()){
			$arResult = $obThread->result();
			if(static::isWindows()){
				$arResult['stderr'] = Helper::convertEncodingFrom($arResult['stderr'], 'cp866');
				$arResult['stdout'] = Helper::convertEncodingFrom($arResult['stdout'], 'cp866');
			}
			if($arResult['stdout'] == static::MULTITHREAD_TEST_SUCCESS){
				return true;
			}
			else{
				$arResult = [
					'ERROR' => true,
					'COMMAND' => $arCommand['COMMAND'],
					'STDOUT' => $arResult['stdout'],
					'STDERR' => $arResult['stderr'],
				];
				static::$arError = array_slice($arResult, 1);
				return $arResult;
			}
		}
		return false;
	}
	
	/**
	 *	Get CPU cores count
	 */
	public static function getCpuCoresCount(){
		if(static::isExec()) {
			if(static::isWindows()){
				$strCoreCount = @exec('echo %NUMBER_OF_PROCESSORS%');
				if(is_numeric($strCoreCount) && $strCoreCount > 0){
					return $strCoreCount;
				}
			}
			else {
				$strCoreCount = @exec('grep -c processor /proc/cpuinfo');
				if(is_numeric($strCoreCount) && $strCoreCount > 0){
					return $strCoreCount;
				}
			}
		}
		return false;
	}

	/*** CRON ***/
	
	/**
	 *	Add cron job
	 */
	public static function addCronTask($strModuleId, $mCommand, $strSchedule=''){
		if(static::isManuallyDisabled()){
			return false;
		}
		if(static::isLinux()){
			if(is_array($mCommand)) {
				$mCommand = static::buildCommand($strModuleId, $mCommand[0], $mCommand[1], $mCommand[2], $mCommand[3], $mCommand[4]);
			}
			if(!strlen($mCommand) || !static::isExec()) {
				return false;
			}
			$strSchedule = strlen($strSchedule) ? trim($strSchedule).' ' : '* * * * * ';
			if(!static::isCronTaskConfigured($strModuleId, $mCommand, $strSchedule)) {
				$strCommandEscaped = str_replace('"', '\"', $mCommand);
				@exec('(crontab -l 2>/dev/null; echo "'.$strSchedule.$strCommandEscaped.'") | crontab -', $arExecResult);
			}
			return static::isCronTaskConfigured($strModuleId, $mCommand, $strSchedule);
		}
		return false;
	}

	/**
	 *	Delete cron job
	 */
	public static function deleteCronTask($strModuleId, $strCommand, $strSchedule=null){
		if(static::isManuallyDisabled()){
			return false;
		}
		if(!strlen($strCommand) || !static::isExec() || !static::isLinux()) {
			return false;
		}
		$strSchedule = is_string($strSchedule) && strlen($strSchedule) ? trim($strSchedule).' ' : '';
		$strCommandEscaped = str_replace('"', '\"', $strCommand);
		$strExecCommand = 'crontab -l | grep -v -F "'.$strSchedule.$strCommandEscaped.'" | crontab -';
		@exec($strExecCommand, $arExecResult);
		return !static::isCronTaskConfigured($strModuleId, $strCommand, $strSchedule);
	}
	
	/**
	 *	Get cron jobs
	 */
	public static function getCronTasks($strModuleId=null){
		if(static::isManuallyDisabled()){
			return array();
		}
		$arResult = array();
		if(static::isExec() && static::isLinux()) {
			$strCommand = 'crontab -l;';
			@exec($strCommand, $arCommandResult);
			$strPath = '';
			if(is_string($strModuleId) && strlen(is_string($strModuleId))){
				$strPath = '/bitrix/modules/'.$strModuleId.'/';
			}
			foreach($arCommandResult as $Key => $strCommandResult){
				$arCommand = static::parseCronTask($strModuleId, $strCommandResult);
				if(is_array($arCommand) && (!is_string($strPath) || stripos($arCommand['COMMAND'], $strPath) !== false)) {
					$arResult[] = $arCommand;
				}
			}
		}
		return $arResult;
	}

	/**
	 *	Check cron job exists
	 */
	public static function isCronTaskConfigured($strModuleId, $strCommand, $strSchedule=''){
		if(!strlen($strCommand) || !static::isExec() || !static::isLinux()) {
			return false;
		}
		$strSchedule = strlen($strSchedule) ? trim($strSchedule).' ' : '';
		$strCommandEscaped = str_replace('"', '\"', $strCommand);
		$strPattern = preg_quote($strCommand).'(\s|$)';
		foreach(static::getCronTasks($strModuleId) as $arTask){
			if(preg_match('#'.$strPattern.'#i', $arTask['COMMAND'], $arMatch)){
				return true;
			}
		}
		return false;
	}

	/**
	 *	Check selected task schedule
	 */
	public static function getCronTaskSchedule($strModuleId, $strCommand, $bAsArray=false){
		if(static::isCronTaskConfigured($strModuleId, $strCommand)) {
			$arJobs = static::getCronTasks($strModuleId);
			foreach($arJobs as $arJob){
				if(stripos($arJob['COMMAND_FULL'], $strCommand) !== false) {
					$arJob = explode(' ', $arJob['COMMAND_FULL']);
					$arSchedule = array_slice($arJob,0,5);
					if($bAsArray) {
						return $arSchedule;
					}
					return implode(' ', $arSchedule);
				}
			}
		}
		return false;
	}
	
	/**
	 *	Parse full command (with time and command)
	 */
	public static function parseCronTask($strModuleId, $strCommand){
		if(preg_match(static::CRON_JOB, $strCommand, $arMatch)) {
			return array(
				'COMMAND_FULL' => $arMatch[0],
				'COMMAND' => $arMatch[7],
				'SCHEDULE' => $arMatch[1],
				'MINUTE' => $arMatch[2],
				'HOUR' => $arMatch[3],
				'DAY' => $arMatch[4],
				'MONTH' => $arMatch[5],
				'WEEKDAY' => $arMatch[6],
			);
		}
		return false;
	}
	
	/********************************************************************************************************************/
	
	/**
	 *	Get full cli command info
	 */
	public static function getFullCommand($strModuleId, $strScriptName, $intProfileId=null, $strOutputFilename=null){
		$bCanAutoSet = static::canAutoSet();
		$strPhpPath = Helper::getOption(ACRIT_CORE, 'php_path');
		if(!strlen($strPhpPath)) {
			$strPhpPath = static::getDefaultPhpPath();
		}
		$strCommand = static::getProfilePhpCommand($strModuleId, $intProfileId, $strScriptName);
		$strCommandClear = static::getProfilePhpCommand($strModuleId, $intProfileId, $strScriptName, null, true);
		$arSchedule = static::getCronTaskSchedule($strModuleId, $strCommandClear, true);
		$arSchedule = is_array($arSchedule) ? $arSchedule : [];
		$bMbstring = Helper::getOption(ACRIT_CORE, 'php_mbstring', 'Y') == 'Y' ? true : false;
		$strConfig = Helper::getOption(ACRIT_CORE, 'php_config', '');
		$strStdout = Helper::getOption(ACRIT_CORE, 'php_output_stdout') == 'Y' && !is_null($strOutputFilename) ? $strOutputFilename : null;
		$strCommandFull = static::buildCommand($strModuleId, $strPhpPath, $strCommand, $strScriptName, $bMbstring, $strConfig, $strStdout);
		$strCommandFullNoOutput = static::buildCommand($strModuleId, $strPhpPath, $strCommand, $strScriptName, $bMbstring, $strConfig, null);
		#
		$bAlreadyInstalled = static::isProfileOnCron($strModuleId, $intProfileId, $strScriptName);
		if(!$bAlreadyInstalled){
			$arSchedule = ['*', '*', '*', '*', '*'];
		}
		#
		return [
			'COMMAND' => $strCommandFull,
			'COMMAND_NO_OUTPUT' => $strCommandFullNoOutput,
			'COMMAND_SHORT' => $strCommand,
			'COMMAND_CLEAR' => $strCommandClear,
			'SCHEDULE' => $arSchedule,
			'ALREADY_INSTALLED' => $bAlreadyInstalled,
			'CAN_AUTO_SET' => $bCanAutoSet,
			'PHP_PATH' => $strPhpPath,
			'PHP_MBSTRING' => $bMbstring,
			'PHP_CONFIG' => $strConfig,
			'MODULE' => $strModuleId,
			'SCRIPT_NAME' => $strScriptName,
			'PROFILE_ID' => $intProfileId,
			'OUTPUT_FILENAME' => $strOutputFilename,
		];
	}
	
	/**
	 *	Check php version
	 */
	public static function checkPhpVersion($strManualPhpPath){
		$arResult = [
			'SUCCESS' => false,
			'MESSAGE' => null,
			'VERSION' => null,
		];
		$strUsedPhpVersion = static::getSitePhpVersion();
		#$strPhpPath = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->get('php_path');
		if(strlen($strManualPhpPath) && static::isExec()){
			if(!preg_match('#\s#', $strManualPhpPath)){
				exec($strManualPhpPath.' -v', $arOutput);
				$bFound = false;
				foreach($arOutput as $strOutput){
					if(preg_match('#PHP\s?(\d+\.\d+.\d+)#i', $strOutput, $arMatch)) {
						$bFound = true;
						$strCheckVersion = $arMatch[1];
						$arResult['VERSION'] = $strCheckVersion;
						if($strCheckVersion == $strUsedPhpVersion) {
							$arResult['SUCCESS'] = true;
							$arResult['MESSAGE'] = Helper::getMessage('ACRIT_CORE_PHP_PATH_CHECK_SUCCESS', array(
								'#VERSION#' => $strUsedPhpVersion,
							));
						}
						else{
							$arResult['MESSAGE'] = Helper::getMessage('ACRIT_CORE_PHP_PATH_CHECK_MISMATCH', array(
								'#PHP_PATH#' => $strManualPhpPath,
								'#VERSION_TEST#' => $strCheckVersion,
								'#VERSION_SITE#' => $strUsedPhpVersion,
							));
						}
					}
				}
				unset($arOutput);
				if(!$bFound){
					$arResult['MESSAGE'] = Helper::getMessage('ACRIT_CORE_PHP_PATH_CHECK_ERROR', array(
						'#PHP_PATH#' => $strManualPhpPath,
						'#VERSION#' => $strUsedPhpVersion,
					));
				}
			}
			else{
				$arResult['MESSAGE'] = Helper::getMessage('ACRIT_CORE_PHP_PATH_CHECK_ERROR', array(
					'#PHP_PATH#' => $strManualPhpPath,
					'#VERSION#' => $strUsedPhpVersion,
				));
			}
		}
		return $arResult;
	}

}
?>