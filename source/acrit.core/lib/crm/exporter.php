<?
namespace Acrit\Core\Crm;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
//	\Acrit\Core\Export\ProfileTable as Profile,
//	\Acrit\Core\Export\ProfileIBlockTable as ProfileIBlock,
//	\Acrit\Core\Export\ProfileFieldTable as ProfileField,
//	\Acrit\Core\Export\ProfileValueTable as ProfileValue,
//	\Acrit\Core\Export\ExportDataTable as ExportData,
//	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
//	\Acrit\Core\Export\HistoryTable as History,
	\Acrit\Core\DiscountRecalculation,
	\Acrit\Core\Log,
	\Acrit\Core\Cli,
	\Acrit\Core\Json,
	\Acrit\Core\Thread;

Loc::loadMessages(__FILE__);

/**
 * Class Exporter
 * @package Acrit\Core\Export
 */

class Exporter {

	const METHOD_CRON = 1;
	const METHOD_SITE = 2;
	
	// Ajax step-by-step result
	const RESULT_SUCCESS = true;
	const RESULT_ERROR = false;
	const RESULT_CONTINUE = 200;
	
	// Modes for process element
	const PROCESS_MODE_AUTO = 1; // Just autogenerate (if AUTO_GENERATE=Y)
	const PROCESS_MODE_FORCE = 2; // Generate at all
	const PROCESS_MODE_PREVIEW = 3; // Just preview data, without saving

	// Time measure
	protected $intStartTime;
	protected $intMaxTime;
	
	// Bitrix modules
	protected $bCatalog = false;
	protected $bSale = false;
	protected $bCurrency = false;
	protected $bHighload = false;

	// Plugins
	protected static $arPlugins = [];
	protected static $arCachePluginFilename = [];
	
	// Heap of plugin-objects
	protected static $arPluginObjects = [];
	
	// Array of module objects
	protected static $arModuleObjects = [];
	
	// Own properties
	protected $strModuleId;
	protected $strModuleCode;
	protected $arArguments;
	protected $intUserId;
	
	/**
	 *	
	 */
	protected function __construct($strModuleId){
		$strModuleId = toLower($strModuleId);
		$this->strModuleId = $strModuleId;
		$this->strModuleCode = preg_replace('#^acrit\.(.*?)$#i', '$1', $strModuleId);
		$this->arArguments = Cli::getCliArguments();
		if($this->arArguments['debug'] == 'Y' && !defined('ACRIT_EXP_DEBUG')){
			define('ACRIT_EXP_DEBUG', true);
		}
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
	 *	Get module id
	 */
	public function getModuleId(){
		return $this->strModuleId;
	}
	
	/**
	 *	Get module code
	 */
	public function getModuleCode(){
		return $this->strModuleCode;
	}
	
	/**
	 *	Set user ID
	 */
	public function setUserId($intUserId){
		$this->intUserId = $intUserId;
	}
	
	/**
	 *	Get plugins
	 */
	public function findPlugins($bGroup=true) {
		$arPlugins = &static::$arPlugins[$this->strModuleId];
		if(!is_array($arPlugins) || empty($arPlugins)) {
			$arPlugins = array();
			$strPluginsDir = Helper::getPluginsDir('crm');
			// Search plugins
			try {
				$resHandle = opendir($_SERVER['DOCUMENT_ROOT'].$strPluginsDir);
				while ($strPluginDir = readdir($resHandle)) {
					if($strPluginDir != '.' && $strPluginDir != '..') {
						$strPluginFullDir = $_SERVER['DOCUMENT_ROOT'].$strPluginsDir.$strPluginDir;
						if(is_dir($strPluginFullDir) && is_file($strPluginFullDir.'/class.php')) {
							require_once($strPluginFullDir.'/class.php');
							$strFormatsDir = $strPluginsDir.$strPluginDir.'/formats/';
							if(is_dir($_SERVER['DOCUMENT_ROOT'].$strFormatsDir)){
								$resHandle2 = opendir($_SERVER['DOCUMENT_ROOT'].$strFormatsDir);
								while ($strFormatDir = readdir($resHandle2)) {
									if($strFormatDir != '.' && $strFormatDir != '..') {
										$strFormatFullDir = $_SERVER['DOCUMENT_ROOT'].$strFormatsDir.$strFormatDir;
										if(is_dir($strFormatFullDir) && is_file($strFormatFullDir.'/class.php')) {
											require_once($strFormatFullDir.'/class.php');
										}
									}
								}
								closedir($resHandle2);
							}
						}
					}
				}
				closedir($resHandle);
			}
			catch(\SystemException $obException) {
				Log::getInstance($this->strModuleId)->add(Log::getMessage('ACRIT_EXP_LOG_SEARCH_PLUGINS_ERROR', array(
					'#TEXT#' => $obException->getMessage(),
				)));
			}
			// You can add your custom plugin
			foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnFindPlugins') as $arHandler) {
				ExecuteModuleEventEx($arHandler, array());
			}
			// Search children of Plugin class - it will be our plugins
			static::$arCachePluginFilename = array();
			foreach(get_declared_classes() as $strClass) {
				if(is_subclass_of($strClass, 'Acrit\Core\Crm\Plugin') && $strClass != 'Acrit\Core\Crm\UniversalPlugin') {
					$strClass::setStaticModuleId($this->strModuleId);
					$strPluginCode = $strClass::getCode();
					$strClassFilename = Helper::getClassFilename($strClass);
					static::$arCachePluginFilename[$strPluginCode] = Helper::path($strClassFilename);
				}
			}
			foreach(get_declared_classes() as $strClass) {
				if(is_subclass_of($strClass, 'Acrit\Core\Crm\Plugin') && $strClass != 'Acrit\Core\Crm\UniversalPlugin') {
					$strPluginCode = $strClass::getCode();
					Loc::loadMessages(static::$arCachePluginFilename[$strPluginCode]);
					#
					$arPlugins[$strPluginCode] = array(
						'CLASS' => $strClass,
						'CODE' => $strPluginCode,
						'NAME' => $strClass::getName(),
						'DESCRIPTION' => $strClass::getDescription(),
						'EXAMPLE' => $strClass::getExample(),
						'IS_SUBCLASS' => $strClass::isSubclass(),
					);
					#
					if($this->strModuleCode == 'exportpro'){
						if(!$this->isPluginMatch($strPluginCode, [
							'GOOGLE_MERCHANT',
							#
							'YANDEX_MARKET',
							'YANDEX_TURBO',
							'YANDEX_WEBMASTER',
							'YANDEX_ZEN',
							#
							'ROZETKA_COM_UA',
							'EBAY',
							'HOTLINE_UA',
							'PRICE_RU',
							'PRICE_UA',
							'AVITO',
							'TORG_MAIL_RU',
							'TIU_RU',
							'GOODS_RU',
							'PROM_UA',
							'ALIEXPRESS_COM',
							'PULSCEN_RU',
							'ALL_BIZ',
							'LENGOW_COM',
							'NADAVI_NET',
							'TECHNOPORTAL_UA',
							#
							'CUSTOM_CSV',
							'CUSTOM_XML',
							'CUSTOM_EXCEL',
						])){
							unset($arPlugins[$strPluginCode]);
						}
					}
					if($this->strModuleCode == 'export'){
						if(!$this->isPluginMatch($strPluginCode, [
							'GOOGLE_MERCHANT',
							#
							'YANDEX_MARKET',
							'YANDEX_TURBO',
							'YANDEX_WEBMASTER',
							'YANDEX_ZEN',
						])){
							unset($arPlugins[$strPluginCode]);
						}
					}
					elseif($this->strModuleCode == 'googlemerchant'){
						if(!$this->isPluginMatch($strPluginCode, ['GOOGLE_MERCHANT'])){
							unset($arPlugins[$strPluginCode]);
						}
					}
				}
			}
			// Determine parent class
			foreach($arPlugins as $strPlugin => $arPlugin) {
				if($arPlugin['IS_SUBCLASS']) {
					$strParentClass = get_parent_class($arPlugin['CLASS']);
					if(strlen($strParentClass)){
						foreach($arPlugins as $strPlugin1 => $arPlugin1) {
							if($arPlugin1['CLASS'] == $strParentClass){
								$arPlugins[$strPlugin]['PARENT'] = $strPlugin1;
							}
						}
					}
				}
			}
			// You can modify plugins list
			foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAfterFindPlugins') as $arHandler) {
				ExecuteModuleEventEx($arHandler, array(&$arPlugins));
			}
			$arPlugins = is_array($arPlugins) ? $arPlugins : array();
			// Remove wrong or corrupted plugins
			foreach($arPlugins as $strPlugin => $arPlugin) {
				$bCorruptedPlugin = !is_array($arPlugin) || !strlen($arPlugin['CODE']) || !strlen($arPlugin['NAME'])
					|| $strPlugin != $arPlugin['CODE'] || is_numeric($strPlugin)
					|| !strlen($arPlugin['CLASS']) || !class_exists($arPlugin['CLASS'])
					|| !is_subclass_of($arPlugin['CLASS'], 'Acrit\Core\Crm\Plugin');
				if($bCorruptedPlugin) {
					unset($arPlugins[$strPlugin]);
					Log::getInstance($this->strModuleId)->add(Loc::getMessage('ACRIT_EXP_LOG_PLUGIN_CORRUPTED', array(
						'#TEXT#' => print_r($arPlugin, true),
					)));
				}
			}
			// Determine type of plugin - native or custom, and directory
			$strDocumentRoot = \Bitrix\Main\Application::getDocumentRoot();
			foreach($arPlugins as $strPlugin => $arPlugin) {
				$arPlugins[$strPlugin]['TYPE'] = Plugin::TYPE_NATIVE;
				$obReflectionClass = new \ReflectionClass($arPlugin['CLASS']);
				$strFileClass = $obReflectionClass->getFileName();
				if(stripos($strFileClass, $strDocumentRoot) !== 0){
					/*
					 *	Fix for some cases, e.g.:
					 *	$_SERVER['DOCUMENT_ROOT'] is /home/bitrix/ext_www/kiskashop
					 *	but Reflection determines path of plugins as
					 *	/home/bitrix/ext_www/core/bitrix/modules/acrit.exportproplus/plugins/yandex.market/class.php
					 *	In fact, that is not within the document root
					 */
					$intPos = stripos($strFileClass, '/bitrix/modules/');
					if($intPos !== false){
						$strFileClass = $strDocumentRoot.substr($strFileClass, $intPos);
					}
				}
				unset($obReflectionClass);
				if(strlen($strFileClass)) {
					$strFileClass = substr($strFileClass, strlen($strDocumentRoot));
					$arPlugins[$strPlugin]['DIRECTORY'] = Helper::path(pathinfo($strFileClass, PATHINFO_DIRNAME));
					if(stripos($strFileClass,$strPluginsDir)===0) {
						$arPlugins[$strPlugin]['TYPE'] = Plugin::TYPE_NATIVE;
					}
				}
				if($this->strModuleCode != 'exportproplus'){
					if($arPlugins[$strPlugin]['TYPE'] != Plugin::TYPE_NATIVE){
						unset($arPlugins[$strPlugin]);
					}
				}
			}
			// Get icon
			foreach($arPlugins as $strPlugin => $arPlugin) {
				$arPlugins[$strPlugin]['ICON'] = false;
				$arPlugins[$strPlugin]['ICON_BASE64'] = false;
				$strFilename = $arPlugin['DIRECTORY'].'/icon.png';
				$arPlugins[$strPlugin]['ICON_FILE'] = $strFilename;
				#
				if(!is_file($_SERVER['DOCUMENT_ROOT'].$strFilename)){
					# search icon in folder of parent plugin
					$arDirectory = explode('/', $arPlugin['DIRECTORY']);
					array_pop($arDirectory);
					if(array_pop($arDirectory) === 'formats'){
						$strFilename = implode('/', $arDirectory).'/icon.png';
					}
				}
				if(is_file($_SERVER['DOCUMENT_ROOT'].$strFilename)){
					$arPlugins[$strPlugin]['ICON'] = $strFilename;
					$arPlugins[$strPlugin]['ICON_BASE64'] = 'data:image/png;base64,'
						.base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].$strFilename));
				}
			}
			// Sort
			uasort($arPlugins, function($a, $b){
				$strNameA = toLower($a['NAME']);
				$strNameB = toLower($b['NAME']);
				$bCustomA = strpos($strNameA, '[') !== false;
				$bCustomB = strpos($strNameB, '[') !== false;
				if($bCustomA && !$bCustomB){
					return 1;
				}
				elseif(!$bCustomA && $bCustomB){
					return -1;
				}
				else{
					return strcmp($strNameA, $strNameB);
				}
			});
		}
		// Group subplugins
		if($bGroup) {
			$arPluginsTmp = $arPlugins;
			foreach($arPluginsTmp as $key1 => $arPlugin1){
				if($arPlugin1['IS_SUBCLASS']) {
					$strDir1 = $arPlugin1['DIRECTORY'].'/';
					foreach($arPluginsTmp as $key2 => $arPlugin2){
						$strDir2 = $arPlugin2['DIRECTORY'].'/';
						// Group by directory
						if(stripos($strDir1, $strDir2) === 0 && strlen($strDir1) > strlen($strDir2)){
							if(!is_array($arPluginsTmp[$key2]['FORMATS'])){
								$arPluginsTmp[$key2]['FORMATS'] = array();
							}
							$arPluginsTmp[$key2]['FORMATS'][$arPlugin1['CODE']] = $arPlugin1;
							unset($arPluginsTmp[$key1]);
						}
					}
				}
			}
			// Sort subclasses
			foreach($arPluginsTmp as $key => &$arPlugin){
				usort($arPlugin['FORMATS'], function($arItemA, $arItemB){
					return strcmp($arItemA['DIRECTORY'], $arItemB['DIRECTORY']);
				});
			}
			// Return
			return $arPluginsTmp;
		}
		else {
			foreach($arPlugins as $key1 => $arPlugin1){
				if(!$arPlugin1['IS_SUBCLASS']){
					$arPlugins[$key1]['FORMATS_COUNT'] = 0;
				}
			}
			foreach($arPlugins as $key1 => $arPlugin1){
				if($arPlugin1['IS_SUBCLASS'] && is_array($arPlugins[$arPlugin1['PARENT']])){
					$arPlugins[$arPlugin1['PARENT']]['FORMATS_COUNT']++;
				}
			}
			return $arPlugins;
		}
	}
	
	/**
	 *	Get plugin/format info
	 */
	public function getPluginInfo($strFormat){
		$arResult = false;
		$arPlugins = $this->findPlugins(false);
		$arTmp = [];
		if(strlen($strFormat) && is_array($arPlugins[$strFormat])){
			$arResult = $arPlugins[$strFormat];
		}
		if(!strlen($arResult['ICON']) && $arResult['IS_SUBCLASS']){
			$arParentPlugin = $arPlugins[$arResult['PARENT']];
			$arResult['ICON'] = $arParentPlugin['ICON'];
			$arResult['ICON_BASE64'] = $arParentPlugin['ICON_BASE64'];
		}
		unset($arPlugins);
		return $arResult;
	}
	
	/**
	 *	Get lang phrase prefixes (use in lang/ru/class.php in universal plugins)
	 */
	public static function getLangPrefix($strFile, &$strLang, &$strHead, &$strName, &$strHint){
		if(is_array(static::$arCachePluginFilename)){
			$arPath = pathinfo($strFile);
			$strPath = Helper::path(realpath($arPath['dirname'].'/../..')).'/'.$arPath['basename'];
			$strPlugin = array_search($strPath, static::$arCachePluginFilename);
			if(strlen($strPlugin)){
				$strLang = 'ACRIT_EXP_'.$strPlugin.'_';
				$strHead = $strLang.'F_HEAD_';
				$strName = $strLang.'F_NAME_';
				$strHint = $strLang.'F_HINT_';
			}
		}
	}

	/**
	 *	Include all modules
	 */
	public function includeModules(){
		$bResult = false;
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			$bResult = true;
			$this->bCatalog = \Bitrix\Main\Loader::includeModule('catalog') ? true : false;
			$this->bSale = \Bitrix\Main\Loader::includeModule('sale') ? true : false;
			$this->bCurrency = \Bitrix\Main\Loader::includeModule('currency') ? true : false;
			$this->bHighload = \Bitrix\Main\Loader::includeModule('highloadblock') ? true : false;
		}
		return $bResult;
	}
	
	/**
	 *	Get all installed acrit export modules
	 */
	public static function getExportModules($bAll=false){
		$arResult = [];
		$arModulesAll = [
			'googlemerchant',
			'export',
			'exportpro',
			'exportproplus',
		];
		foreach($arModulesAll as $key => $strModuleId){
			$arModulesAll[$key] = 'acrit.'.$strModuleId;
		}
		if($bAll){
			$arResult = $arModulesAll;
		}
		else{
			foreach($arModulesAll as $strModuleId){
				if(\Bitrix\Main\Loader::includeModule($strModuleId)){
					$arResult[] = $strModuleId;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Regular execute by cron
	 *	$intProfileId is:
	 *		- null [all profiles]
	 * 		- numeric [single profile]
	 * 		- numeric separated with comma ("1,2,5") [several profiles]
	 */
	public function execute(){
		$this->includeModules();
		$this->setMethod(static::METHOD_CRON);
		
		// Unlock?
		$bUnlock = $this->arArguments['unlock'] == 'Y';
		
		// Which profiles?
		$intProfileId = $this->arArguments['profile'];
		$arProfilesId = [];
		if(is_numeric($intProfileId)){
			$arProfilesId[] = $intProfileId;
		}
		elseif(stripos($intProfileId, ',') !== false){
			$arProfilesIdTmp = explode(',', $intProfileId);
			foreach($arProfilesIdTmp as $intProfileIdTmp){
				$intProfileIdTmp = IntVal(trim($intProfileIdTmp));
				if(is_numeric($intProfileIdTmp) && $intProfileIdTmp > 0){
					$arProfilesId[] = $intProfileIdTmp;
				}
			}
		}
		
		// Which user?
		if(is_numeric($this->arArguments['user'])){
			$this->setUserId($this->arArguments['user']);
		}
		
		// Execute
		foreach($arProfilesId as $intProfileId){
			if($bUnlock){
				Helper::call($this->strModuleId, 'Profile', 'unlock', [$intProfileId]); // Profile::unlock($intProfileId);
			}
			$bLocked = Helper::call($this->strModuleId, 'Profile', 'isLocked', [$intProfileId]); // Profile::isLocked($intProfileId);
			if(!$bLocked){
				Helper::call($this->strModuleId, 'Profile', 'clearSession', [$intProfileId]);
				$mResult = $this->executeProfile($intProfileId);
				Helper::call($this->strModuleId, 'Profile', 'clearSession', [$intProfileId]);
				if($mResult == Exporter::RESULT_SUCCESS){
					$arProfile = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$intProfileId]);
					if(is_array($arProfile) && $arProfile['ONE_TIME'] == 'Y' && Cli::isProfileOnCron($this->strModuleId, $intProfileId, 'export.php')){
						if(Cli::deleteProfileCron($this->strModuleId, $intProfileId, 'export.php')){
							Log::getInstance($this->strModuleId)->add(Loc::getMessage('ACRIT_EXP_PROFILE_ONE_TIME_DELETE_SUCCESS'), $intProfileId);
						}
						else{
							Log::getInstance($this->strModuleId)->add(Loc::getMessage('ACRIT_EXP_PROFILE_ONE_TIME_DELETE_ERROR'), $intProfileId);
						}
						$obResult = Helper::call($this->strModuleId, 'Profile', 'update', [$intProfileId, [
							'ONE_TIME' => $arPost['one_time'] == 'Y' ? 'Y' : 'N',
						]]);
					}
				}
			}
			else {
				$mDateLocked = Helper::call($this->strModuleId, 'Profile', 'getDateLocked', [$intProfileId]);
				print 'Profile '.$intProfileId.' is locked ('.$mDateLocked->toString().').'.PHP_EOL;
				Log::getInstance($this->strModuleId)->add(Loc::getMessage('ACRIT_EXP_PROFILE_LOCKED', array(
					'#DATETIME#' => $mDateLocked->toString(),
				)), $intProfileId, true);
			}
		}
	}
	
	/**
	 *	Whereis class defined?
	 */
	public static function getClassFilename($strClass){
		$obReflectionClass = new \ReflectionClass($strClass);
		$strFileClass = $obReflectionClass->getFileName();
		unset($obReflectionClass);
		return $strFileClass;
	}

	/**
	 *	Test string for plugin (or format)
	 */
	protected function isPluginMatch($strPlugin, $arTestCode){
		$bResult = false;
		if(!is_array($arTestCode)){
			$arTestCode = [$arTestCode];
		}
		foreach($arTestCode as $strTestCode){
			if($strPlugin == $strTestCode || stripos($strPlugin, $strTestCode.'_') === 0){
				$bResult = true;
			}
		}
		return $bResult;
	}

}
?>