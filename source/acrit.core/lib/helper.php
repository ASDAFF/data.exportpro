<?
/**
 * Helper class with base general methods
 */

namespace Acrit\Core;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Config\Option,
	\Bitrix\Main\EventManager,
	\Bitrix\Main\Loader,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Json;

Loc::loadMessages(__FILE__);

class Helper {
	
	const PARAM_ELEMENT_PREVIEW = 'preview';
	const PARAM_ELEMENT_PROFILE_ID = 'profile_id';
	
	const ARRAY_INSERT_BEGIN = '_ARRAY_INSERT_BEGIN_';
	
	// Cache data
	protected static $arCacheCatalogArray = [];
	protected static $arCacheCurrencyList = [];
	protected static $arCacheMeasureList = [];
	protected static $arCacheVatValue = [];
	protected static $arCache = [];
	
	// Work with options.php
	protected static $obTabControl = null;
	
	/**
	 *	Simple debug
	 */
	public static function P($arData, $bJust=false) {
		if($bJust && is_object($GLOBALS['APPLICATION'])){
			#$GLOBALS['APPLICATION']->RestartBuffer();
			static::obRestart();
		}
		$strID = 'pre_'.RandString(8);
		$strResult = '<style type="text/css">pre#'.$strID.'{'.static::pCss().'}</style>';
		if(is_array($arData) && empty($arData))
			$arData = '--- Array is empty ---';
		if($arData === false)
			$arData = '[false]';
		elseif ($arData === true)
			$arData = '[true]';
		elseif ($arData === null)
			$arData = '[null]';
		$strResult .= '<pre id="'.$strID.'">'.print_r($arData, true).'</pre>';
		print $strResult;
		if($bJust){
			die();
		}
	}
	
	/**
	 *	Get style for print_r
	 */
	public static function pCss(){
		return 'background:none repeat scroll 0 0 #FAFAFA; border-color:#AAB4BE #AAB4BE #AAB4BE #B4B4B4; border-style:dotted dotted dotted solid; border-width:1px 1px 1px 20px; font:normal 11px "Courier New","Courier",monospace; margin:10px 0; padding:5px 0 5px 10px; position:relative; text-align:left; white-space:pre-wrap;';
	}
	
	/**
	 *	Simple log
	 */
	public static function L($mMessage){
		if (is_array($mMessage)) {
			$mMessage = print_r($mMessage, true);
		}
		$intTime = microtime(true);
		$strMicroTime = sprintf('%06d', ($intTime - floor($intTime)) * 1000000);
		$obDate = new \DateTime(date('d.m.Y H:i:s.'.$strMicroTime, $intTime));
		$strTime = $obDate->format('d.m.Y H:i:s.u');
		$strFilename = realpath(__DIR__.'/../').'/!log.txt';
		$resHandle = fopen($strFilename, 'a+');
		@flock($resHandle, LOCK_EX);
		fwrite($resHandle, '['.$strTime.'] '.$mMessage.PHP_EOL);
		@flock($resHandle, LOCK_UN);
		fclose($resHandle);
		unset($obDate, $resHandle, $intTime, $strMicroTime, $strTime);
	}
	
	/**
	 *	Get current core module id
	 */
	public static function id(){
		return ACRIT_CORE;
	}
	
	/**
	 *	Get document root
	 */
	public static function root(){
		return Loader::getDocumentRoot();
	}
	
	/**
	 *	SQL-query
	 */
	public static function query($strSql){
		return \Bitrix\Main\Application::getConnection()->query($strSql);
	}
	
	/**
	 *	Prepare string for use in SQL
	 */
	public static function forSql($strValue){
		return $GLOBALS['DB']->forSql($strValue);
	}
	
	/**
	 *	Is site works on ITF-8?
	 */
	public static function isUtf() {
		return defined('BX_UTF') && BX_UTF === true;
	}
	
	/**
	 *	Prevent Mysql error 'MySQL server has gone away'
	 */
	public static function setWaitTimeout($intTimeout=null){
		$intTimeout = is_numeric($intTimeout) && $intTimeout > 0 ? $intTimeout : 6*60*60;
		$strSql = "SET SESSION `wait_timeout`={$intTimeout};";
		static::query($strSql);
	}
	
	/**
	 *	Prevent Mysql error 'MySQL server has gone away'
	 */
	public static function preventSqlGoneAway(){
		static::query('SELECT 1;');
	}
	
	/**
	 *	Get current cli command
	 *	!!! We can't detect php config parameters in command line
	 */
	public static function getCurrentCliCommand(){
		if(stripos(php_sapi_name(), 'cli') === 0){
			return PHP_BINARY.' -f '.implode(' ', $_SERVER['argv']).PHP_EOL;
		}
		return null;
	}
	
	/**
	 *	Convert unit datetime to FORMAT_DATETIME
	 */
	public static function formatUnixDatetime($fDateTime) {
		return date(\CDatabase::DateFormatToPHP(FORMAT_DATETIME), $fDateTime);
	}
	
	/**
	 *	Analog for Loc::loadMessages()
	 */
	public static function loadMessages($strFile){
		Loc::loadMessages($strFile);
	}
	
	/**
	 *	Analog for Loc::getMessage()
	 */
	public static function getMessage($strMessage, $arReplace=null){
		return Loc::getMessage($strMessage, $arReplace);
	}
	
	/**
	 *	Get available encodings
	 */
	public static function getAvailableEncodings(){
		$arResult = array(
			'UTF-8',
			'windows-1251',
		);
		foreach($arResult as $key => $value){
			unset($arResult[$key]);
			$arResult[$value] = $value;
		}
		if(!static::isUtf()){
			krsort($arResult);
		}
		return $arResult;
	}
	
	/**
	 * Convert charset (CP1251->UTF-8 || UTF-8->CP1251)
	 */
	public static function convertEncoding($mText, $strFrom='UTF-8', $strTo='CP1251', $bKeys=false) {
		$error = '';
		if(is_array($mText)) {
			$arResult = [];
			foreach($mText as $key => $value){
				if($bKeys){
					$key = \Bitrix\Main\Text\Encoding::convertEncoding($key, $strFrom, $strTo, $error);
				}
				$arResult[$key] = static::convertEncoding($value, $strFrom, $strTo, $bKeys);
			}
			return $arResult;
		}
		return \Bitrix\Main\Text\Encoding::convertEncoding($mText, $strFrom, $strTo, $error);
	}
	
	/**
	 * Convert charset from site charset to specified charset
	 */
	public static function convertEncodingTo($mText, $strTo) {
		if(strlen($strTo)){
			$strFrom = static::isUtf() ? 'UTF-8' : 'CP1251';
			$strTo = ToLower($strTo) == 'windows-1251' ? 'CP1251' : $strTo;
			if($strTo != $strFrom){
				$mText = static::convertEncoding($mText, $strFrom, $strTo);
			}
		}
		return $mText;
	}
	
	/**
	 * Convert charset from specified charset to site charset
	 */
	public static function convertEncodingFrom($mText, $strFrom) {
		if(strlen($strFrom)){
			$strFrom = ToLower($strFrom) == 'windows-1251' ? 'CP1251' : $strFrom;
			$strTo = static::isUtf() ? 'UTF-8' : 'CP1251';
			if($strFrom != $strTo){
				$mText = static::convertEncoding($mText, $strFrom, $strTo);
			}
		}
		return $mText;
	}
	
	/**
	 * Convert UTF-8 text (source is UTF-8, result is site's encoding - CP1251 || UTF-8)
	 */
	public static function convertUtf8($strText) {
		if(!static::isUtf()) {
			return static::convertEncoding($strText, 'UTF-8', 'CP1251', $error);
		}
		return $strText;
	}
	
	/**
	 *	Show note
	 */
	public static function showNote($strNote, $bCompact=false, $bCenter=false, $bReturn=false) {
		if($bReturn){
			ob_start();
		}
		$arClass = array();
		if($bCompact){
			$arClass[] = 'acrit-exp-note-compact';
		}
		if($bCenter){
			$arClass[] = 'acrit-exp-note-center';
		}
		print '<div class="'.implode(' ', $arClass).'">';
		print BeginNote();
		print $strNote;
		print EndNote();
		print '</div>';
		if($bReturn){
			return ob_get_clean();
		}
	}
	
	/**
	 *	Show success
	 */
	public static function showSuccess($strMessage=null, $strDetails=null) {
		ob_start();
		\CAdminMessage::ShowMessage(array(
			'MESSAGE' => $strMessage,
			'DETAILS' => $strDetails,
			'HTML' => true,
			'TYPE' => 'OK',
		));
		return ob_get_clean();
	}
	
	/**
	 *	Show error
	 */
	public static function showError($strMessage=null, $strDetails=null) {
		ob_start();
		\CAdminMessage::ShowMessage(array(
			'MESSAGE' => $strMessage,
			'DETAILS' => $strDetails,
			'HTML' => true,
		));
		return ob_get_clean();
	}
	
	/**
	 *	Show error
	 */
	public static function showHeading($strMessage, $bNoMargin=false){
		$strResult = '';
		$strClass = $bNoMargin ? ' class="acrit-exp-table-nomargin"' : '';
		$strResult .= '<table style="width:100%"'.$strClass.'><tbody><tr class="heading"><td>'
			.$strMessage.'</td></tr></tbody></table>';
		return $strResult;
	}
	
	/**
	 *	Show hint
	 */
	public static function showHint($strText, $bWarning=false, $bPopup=false, $strPopupTitle=false, $strPopupJs=false) {
		$strCode = ToLower(RandString(8));
		$strText = str_replace('"', '\"', $strText);
		$strText = str_replace("\n", ' ', $strText);
		$strText = str_replace("\r", '', $strText);
		if($bPopup){
			static::includeJsPopupHint();
			$strImage = '/bitrix/js/main/core/images/hint.gif';
			if($bWarning){
				$strImage = '/bitrix/themes/.default/images/acrit.core/icon-warning-12.gif';
			}
			$strResult = '<span id="hint_'.$strCode.'"><img src="'.$strImage.'" style="cursor:pointer; margin-left:5px;"></span>';
			$strResult .= '<script>BX.bind(BX("hint_'.$strCode.'"), "click", function(){
				AcritPopupHint.Open("'.$strPopupTitle.'", "'.$strText.'");
				'.(strlen($strPopupJs) ? $strPopupJs : '').'
			});</script>';
		}
		else{
			$strResult = '<span id="hint_'.$strCode.'"><span></span></span>'
				.'<script>BX.hint_replace(BX("hint_'.$strCode.'").childNodes[0], "'.$strText.'");</script>';
			if($bWarning){
				$strResult .= '<script>BX("hint_'.$strCode.'").childNodes[0].src='
					.'"/bitrix/themes/.default/images/acrit.core/icon-warning-12.gif";</script>';
			}
		}
		return $strResult;
	}
	
	/**
	 *	Include js for AcritPopupHint
	 */
	public static function includeJsPopupHint(){
		$GLOBALS['APPLICATION']->addHeadScript('/bitrix/js/acrit.core/popup_hint.js');
	}
	
	/**
	 *	Show menu
			$arMenu = array(
				array(
					'ICONCLASS' => 'btn_backup_create',
					'TEXT' => '11111',
					'ONCLICK' => 'alert(1)',
				),
				array(
					'ICONCLASS' => 'btn_backup_restore',
					'TEXT' => '2222',
					'ONCLICK' => 'alert(2)',
				),
			);
			<a href="#" onclick="<?=Helper::showMenuOnClick($arMenu);?>">1111111111</a>
	 */
	public static function showMenuOnClick($arMenu, $strActiveClass=''){
		$strJson = Json::encode($arMenu);
		$strJson = htmlspecialcharsbx($strJson);
		$strResult = "BX.adminShowMenu(this, {$strJson}, {active_class:'{$strActiveClass}', public_frame:'0'}); return false;";
		return $strResult;
	}
	
	/**
	 *	Word form for russian (1 tevelizor, 2 tevelizora, 5 tevelizorov)
	 */
	public static function wordForm($intValue, $arWords) {
		$strLastSymbol = substr($intValue, -1);
		$strSubLastSymbol = substr($intValue, -2, 1);
		if (strlen($intValue) >= 2 && $strSubLastSymbol == '1') {
			return $arWords['5'];
		}
		else {
			if ($strLastSymbol == '1')
				return $arWords['1'];
			elseif ($strLastSymbol >= 2 && $strLastSymbol <= 4)
				return $arWords['2'];
			else
				return $arWords['5'];
		}
	}
	
	/**
	 *	Get all sites
	 */
	public static function getSitesList($bActiveOnly=false) {
		$arResult = array();
		$arFilter = array();
		if($bActiveOnly) {
			$arFilter['ACTIVE'] = 'Y';
		}
		$resSites = \CSite::GetList($strBy='SORT', $strOrder='ASC', $arFilter);
		while($arSite = $resSites->GetNext(false, false)) {
			$arResult[$arSite['ID']] = $arSite;
		}
		return $arResult;
	}
	
	/**
	 *	Get current http host, without port
	 */
	public static function getCurrentHost() {
		return preg_replace('#^(.*?)($|:[\d]+$)#','$1',\Bitrix\Main\Context::getCurrent()->getServer()->getHttpHost());
	}
	
	/**
	 *	Check if site works via HTTPS
	 */
	public static function isHttps() {
		return \Bitrix\Main\Context::getCurrent()->getRequest()->isHttps();
	}
	
	/**
	 *	Sort by 'SORT' key
	 */
	public static function sortBySort($arItemA, $arItemB) {
    if ($arItemA['SORT'] == $arItemB['SORT']) {
			return 0;
    }
    return ($arItemA['SORT'] < $arItemB['SORT']) ? -1 : 1;
	}
	
	/**
	 *	
	 */
	public static function escapeQuotes($strValue){
		$strResult = '';
		for($i = 0; $i < strlen($strValue); $i++){
			$strChar = substr($strValue, $i, 1);
			if($strChar == '"' && $i > 0 && substr($strValue, $i-1, 1) != '\\') {
				$strChar = '\\'.$strChar;
			}
			$strResult .= $strChar;
		}
		return '['.$strResult.']';
	}
	
	/**
	 *	
	 */
	public static function arrayRemoveValues(&$arData, $arRemove){
		$arRemove = is_array($arRemove) ? $arRemove : [$arRemove];
		foreach($arData as $key => $value){
			if(in_array($value, $arRemove)){
				unset($arData[$key]);
				continue;
			}
		}
	}
	
	/**
	 *	Get next numeric key for non-associative array
	 */
	public static function arrayGetNextKey(array $arData){
		$intKey = null;
		foreach($arData as $key => $value){
			$intKey = is_null($intKey) ? $key : max($intKey, $key);
		}
		return is_null($intKey) ? 0 : ++$intKey;
	}
	
	/**
	 *	Exclude some values from array
	 */
	public static function arrayExclude($arValues, array $arExclude){
		foreach($arExclude as $key){
			unset($arValues[$key]);
		}
		return $arValues;
	}
	
	/**
	 *	Insert new key into array (in a selected place)
	 */
	public static function arrayInsert(array &$arData, $strKey, $mItem, $strAfter=null, $strBefore=null){
		$bSuccess = false;
		if($strAfter === static::ARRAY_INSERT_BEGIN) {
			$bSuccess = true;
			$arData = array_merge(array($strKey => $mItem), $arData);
		}
		elseif(!is_null($strAfter)) {
			$intIndex = 0;
			foreach($arData as $key => $value){
				$intIndex++;
				if($key === $strAfter){
					$bSuccess = true;
					$arBefore = array_slice($arData, 0, $intIndex, true);
					$arAfter = array_slice($arData, $intIndex, null, true);
					$arData = array_merge($arBefore, array($strKey => $mItem), $arAfter);
					unset($arBefore, $arAfter);
					break;
				}
			}
		}
		elseif(!is_null($strBefore)) {
			$intIndex = 0;
			foreach($arData as $key => $value){
				if($key === $strBefore){
					$bSuccess = true;
					$arBefore = array_slice($arData, 0, $intIndex, true);
					$arAfter = array_slice($arData, $intIndex, null, true);
					$arData = array_merge($arBefore, array($strKey => $mItem), $arAfter);
					unset($arBefore, $arAfter);
					break;
				}
				$intIndex++;
			}
		}
		if(!$bSuccess){
			$arData[$strKey] = $mItem;
		}
	}
	
	/**
	 *	Get next key in array
	 */
	public static function getNextKey(array $arData, $strCurrentKey){
		$bFound = false;
		foreach($arData as $key => $item) {
			if($bFound){
				return $key;
			}
			if($key == $strCurrentKey) {
				$bFound = true;
			}
		}
		return false;
	}
	
	/**
	 *	Remove empty values from array (check by strlen(trim()))
	 */
	public static function arrayRemoveEmptyValues(&$arValues, $bTrim=true) {
    foreach($arValues as $key => $value){
			if($bTrim && !strlen(trim($value)) || !$bTrim && !strlen($value)){
				unset($arValues[$key]);
			}
		}
	}
	
	/**
	 *	Remove empty values from array (check by strlen(trim()))
	 */
	public static function arrayRemoveEmptyValuesRecursive(&$arValues) {
    foreach($arValues as $key => $value){
			if(is_array($value)){
				static::arrayRemoveEmptyValuesRecursive($arValues[$key]);
			}
			else{
				if(!strlen(trim($value))){
					unset($arValues[$key]);
				}
			}
		}
	}
	
	/**
	 *	Limit count for array items
	 *	ToDo: $strScheme - схема обработки множественного значения
	 */
	public static function arrayLimitSize(&$arItems, $intCount, $strScheme=false){
		if(is_array($arItems)){
			$arItems = array_slice($arItems, 0, $intCount);
		}
	}
	
	/**
	 *	Compile params: array(x=>1, y=>2) => x=1&y=2
	 */
	public static function compileParams($mParams){
		if(is_array($mParams)){
			$mParams = http_build_query($mParams);
		}
		if(!is_string($mParams)){
			$mParams = '';
		}
		return $mParams;
	}
	
	/**
	 *	Decompile params: x=1&y=2 => array(x=>1, y=>2)
	 */
	public static function decompileParams($mParams){
		if(is_string($mParams)){
			parse_str($mParams, $mParams);
		}
		if(!is_array($mParams)){
			$mParams = array();
		}
		return $mParams;
	}
	
	/**
	 *	Compile Site URL
	 */
	public static function siteUrl($strDomain, $bSSL=false) {
		return ($bSSL?'https://':'http://').$strDomain;
	}
	
	/**
	 *	Exec custom action for each element of array (or single if it is not array)
	 */
	public static function execAction($arData, $callbackFunction, $arParams=false){
		if(is_array($arData)) {
			foreach($arData as $Key => $arItem){
				$arData[$Key] = $callbackFunction($arItem, $arParams);
			}
		} else {
			$arData = $callbackFunction($arData, $arParams);
		}
		return $arData;
	}
	
	/**
	 *	Equivalent for empty(), but 0, 0.0 and '0' are not empty!
	 */
	public static function isEmpty($mValue, $bSimpleMode=false){
		if($bSimpleMode){
			return empty($mValue);
		}
		else {
			return empty($mValue) && $mValue !== 0 && $mValue !== 0.0 && $mValue !== '0' ? true : false;
		}
	}
	
	/**
	 *	Checkbox "Use stores" in module "catalog"
	 */
	public static function isCatalogUseStoreControl(){
		return (string)Option::get('catalog', 'default_use_store_control') == 'Y';
	}
	
	/**
	 *	Is managed cache on ?
	 */
	public static function isManagedCacheOn(){
		return (Option::get('main', 'component_managed_cache_on', 'N') != 'N' || defined('BX_COMP_MANAGED_CACHE'));
	}
	
	/**
	 *	Get all avaiable currencies
	 */
	public static function getCurrencyList(){
		$arResult = &static::$arCacheCurrencyList;
		if(!is_array($arResult) || empty($arResult)) {
			$arResult = array();
			if (Loader::includeModule('currency')) {
				$resCurrency = \CCurrency::GetList($by='SORT', $order='ASC', LANGUAGE_ID);
				while ($arCurrency = $resCurrency->GetNext(false, false)) {
					$arCurrency['IS_BASE'] = FloatVal($arCurrency['AMOUNT']) == 1 ? true: false;
					if (isset($arCurrency['DEAULT']) && !isset($arCurrency['DEFAULT'])) {
						$arCurrency['DEFAULT'] = $arCurrency['DEAULT'];
						unset($arCurrency['DEAULT']);
					}
					$arResult[ToUpper($arCurrency['CURRENCY'])] = $arCurrency;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get all avaiable prices
	 */
	public static function getPriceList($arSort=false) {
		$arResult = array();
		if(Loader::includeModule('catalog')) {
			if($arSort == false){
				$arSort = array('SORT' => 'ASC', 'ID' => 'ASC');
			}
			$resPrices = \CCatalogGroup::GetList($arSort);
			while ($arPrice = $resPrices->getNext(false, false)) {
				$arResult[] = $arPrice;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get next available numeric key in array
	 */
	public static function getNextAvailableKey($arData){
		$intKey = 1;
		while(true){
			if(!array_key_exists($intKey, $arData)) {
				break;
			}
			$intKey++;
		}
		return $intKey;
	}
	
	/**
	 *	Path array
	 *	array('1', '2', '3') => array('1', '1/2', '1/2/3')
	 */
	public static function pathArray(&$arData, $strSeparator){
		$intIndex = 0;
		foreach(array_reverse($arData, true) as $key => $item){
			$arData[$key] = implode($strSeparator, array_slice($arData, 0, count($arData) - $intIndex));
			$intIndex++;
		}
	}
	
	/**
	 *	Change file extension
	 */
	public static function changeFileExt($strFileName, $strExtension){
		$arPath = pathinfo($strFileName);
		return $arPath['dirname'].'/'.$arPath['filename'].'.'.$strExtension;
	}
	
	/**
	 *	Format size (kilobytes, megabytes, ...)
	 */
	public static function formatSize($intSize){
		$strResult = \CFile::FormatSize($intSize);
		// replace '2 Mb' to '2.00 Mb'
		$strResult = preg_replace('#^([\d]+)[\s]#', '$1.00 ', $strResult);
		// replace '2.1 Mb' to '2.10 Mb'
		$strResult = preg_replace('#^([\d]+)\.([\d]{1})[\s]#', '${1}.${2}0 ', $strResult);
		return $strResult;
	}
	
	/**
	 *	Get general tmp dir
	 */
	public static function getTmpDir($bAutoCreate=true, $bRelative=false){
		$strUploadDir = Option::get('main', 'upload_dir');
		if(!strlen($strUploadDir)){
			$strUploadDir = 'upload';
		}
		$strResult = '/'.$strUploadDir.'/'.ACRIT_CORE.'/'.'tmp';
		$strResultAbs = static::root().$strResult;
		if($bAutoCreate && !is_dir($strResultAbs)){
			mkdir($strResultAbs, BX_DIR_PERMISSIONS, true);
		}
		return $bRelative ? $strResult : $strResultAbs;
	}
	
	/**
	 *	Create directories path for file
	 */
	public static function getDirectoryForFile($strFileName){
		return pathinfo($strFileName, PATHINFO_DIRNAME);
	}
	public static function createDirectoriesForFile($strFileName, $bAutoChangeOwner=false){
		$strDirname = static::getDirectoryForFile($strFileName);
		if(!is_dir($strDirname)){
			@mkdir($strDirname, BX_DIR_PERMISSIONS, true);
		}
		if($bAutoChangeOwner){
			$strPath = substr(pathinfo($strFileName, PATHINFO_DIRNAME), strlen(static::root()));
			$strPath = trim(static::path($strPath), '/');
			$arPath = explode('/', $strPath);
			for($i=1; $i <= count($arPath); $i++){
				$strPath = implode('/', array_slice($arPath, 0, $i));
				if(strlen($strPath)){
					$strPath = '/'.$strPath;
					if(is_dir(static::root().$strPath)){
						static::changeFileOwner(static::root().$strPath);
					}
				}
			}
		}
		return is_dir($strDirname);
	}
	
	/**
	 *	Get option value
	 */
	public static function getOption($strModuleId, $strOption, $mDefaultValue=null){
		return Option::get($strModuleId, $strOption, $mDefaultValue);
	}
	
	/**
	 *	Set option value
	 */
	public static function setOption($strModuleId, $strOption, $mValue){
		return Option::set($strModuleId, $strOption, $mValue);
	}
	
	/**
	 *	Delete option value
	 */
	public static function deleteOption($strModuleId, $strOption){
		$arFilter = [
			'name' => $strOption,
		];
		return Option::delete($strModuleId, $arFilter);
	}
	
	/**
	 *	Delete all options
	 */
	public static function deleteAllOptions($strModuleId, $arFilter=[]){
		return Option::delete($strModuleId, $arFilter);
	}
	
	/**
	 *	Format elapsed time from 121 to 2:01
	 */
	public static function formatElapsedTime($intSeconds){
		$strResult = '';
		if(is_numeric($intSeconds)){
			$intHours = floor($intSeconds / (60*60));
			$intSeconds -= $intHours * 60 * 60;
			$intMinutes = floor($intSeconds / 60);
			$intMinutes = sprintf('%02d', $intMinutes);
			if($intMinutes > 0) {
				$intSeconds = $intSeconds - $intMinutes * 60;
			}
			$intSeconds = sprintf('%02d', $intSeconds);
			$strResult = ($intHours ? $intHours.':' : '').$intMinutes.':'.$intSeconds;
		}
		return $strResult;
	}
	
	/**
	 *	round, floor, ceil
	 */
	public static function roundEx($fValue, $intPrecision=0, $strFunc=false) {
		$intPow = pow(10, $intPrecision);
		$strFunc = in_array($strFunc, array('round', 'floor', 'ceil')) ? $strFunc : 'round';
		return call_user_func($strFunc, $fValue * $intPow) / $intPow;
	}
	
	/**
	 *	recursive str_replace
	 *	$arSearch = array('from1' => 'to1', 'from2' => 'to2');
	 */
	public static function strReplaceRecursive($arData, $arSearch){
		if(is_array($arData)){
			foreach($arData as $key => $value){
				$arData[$key] = static::strReplaceRecursive($value, $arSearch);
			}
		}
		else {
			foreach($arSearch as $strFrom => $strTo){
				$arData = str_replace($strFrom, $strTo, $arData);
			}
		}
		return $arData;
	}
	
	/**
	 *	Modify: file.txt => file.txt, file_1.txt, file_2.txt
	 */
	public static function getFileNameWithIndex($strFileName, $intIndex){
		$intIndex = IntVal($intIndex);
		if($intIndex <= 1){
			return $strFileName;
		}
		else {
			#$strFileName = str_replace('\\', '/', $strFileName);
			$strFileName = static::path($strFileName);
			$arPath = pathinfo($strFileName);
			$arPath['dirname'] .= substr($arPath['dirname'], -1, 1) != '/' ? '/' : '';
			$arPath['filename'] = preg_replace('#\.(\d+)$#', '', $arPath['filename']);
			return $arPath['dirname'].$arPath['filename'].'.'.$intIndex.'.'.$arPath['extension'];
		}
	}
	
	/**
	 *	Get path from URL
	 */
	public static function getPathFromUrl($strUrl){
		return parse_url($strUrl, PHP_URL_PATH);
	}
	
	/**
	 *	Add | remove exclude to Bitrix Antivirus
	 */
	public static function addAntivirusExclude($strWhiteItem){
		if (Loader::includeModule('security')){
			$arWhiteList = array();
			$resWhiteList = \CSecurityAntiVirus::GetWhiteList();
			while($arWhiteListItem = $resWhiteList->getNext(false, false)){
				$arWhiteList[$arWhiteListItem['ID']] = $arWhiteListItem['WHITE_SUBSTR'];
			}
			if(!in_array($strWhiteItem, $arWhiteList)){
				$arWhiteList['n0'] = $strWhiteItem;
				\CSecurityAntiVirus::UpdateWhiteList($arWhiteList);
			}
		}
	}
	public static function deleteAntivirusExclude($strRemoveItem){
		if (Loader::includeModule('security')){
			$arWhiteList = array();
			$resWhiteList = \CSecurityAntiVirus::GetWhiteList();
			while($arWhiteListItem = $resWhiteList->getNext(false, false)){
				$arWhiteList[$arWhiteListItem['ID']] = $arWhiteListItem['WHITE_SUBSTR'];
			}
			$bModified = false;
			foreach($arWhiteList as $key => $strWhiteItem){
				if($strWhiteItem === $strRemoveItem){
					unset($arWhiteList[$key]);
					$bModified = true;
				}
			}
			if($bModified){
				\CSecurityAntiVirus::UpdateWhiteList($arWhiteList);
			}
		}
	}
	
	/**
	 *	Get current module version
	 */
	public static function getModuleVersion($strModuleId){
		include static::root().'/bitrix/modules/'.$strModuleId.'/install/version.php';
		return $arModuleVersion['VERSION'];
	}
	
	/**
	 *	Get current module version
	 */
	public static function getModuleName($strModuleId){
		$strModuleUnderscore = str_replace('.', '_', $strModuleId);
		$strModuleIndexFile = realpath(__DIR__.'/../../'.$strModuleId).'/install/index.php';
		if(is_file($strModuleIndexFile)){
			require_once($strModuleIndexFile);
			$obModule = new $strModuleUnderscore();
			$strModuleName = $obModule->MODULE_NAME;
			unset($obModule);
			return $strModuleName;
		}
		return false;
	}
	
	/**
	 *	SQL run batch (with replace #MODULE_CODE#)
	 */
	public static function runCoreSqlBatch($strFile, $strModuleId){
		global $DB;
		$bSuccess = true;
		$arErrors = [];
		if(strlen($strFile)){
			$strFile = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ACRIT_CORE.'/install/db/mysql/'.$strFile;
			if(is_file($strFile) && filesize($strFile)){
				$arSql = $DB->parseSqlBatch(file_get_contents($strFile));
				$strModuleCode = preg_replace('#^acrit\.(.*?)$#i', '$1', $strModuleId);
				if(is_array($arSql)){
					$DB->startTransaction();
					foreach($arSql as $strSql){
						$strSql = str_replace('#MODULE_CODE#', $strModuleCode, $strSql);
						$strSql = str_replace("\r\n", "\n", $strSql);
						if(!$DB->query($strSql, true)){
							$bSuccess = false;
							$arErrors[] = "<hr><pre>Query:\n".$strSql."\n\nError:\n<font color=red>".$DB->getErrorMessage()."</font></pre>";
						}
						if($bSuccess){
							$DB->commit();
						}
						else{
							$DB->rollback();
						}
					}
				}
			}
		}
		if($bSuccess){
			return true;
		}
		else{
			return is_array($arErrors) ? $arErrors : [$arErrors];
		}
	}
	
	/**
	 *	Start bitrixcloud monitoring
	 */
	public static function startBitrixCloudMonitoring($strNewEmail){
		$bResult = false;
		if(Loader::includeModule('bitrixcloud')){
			$strDomain = $_SERVER['SERVER_NAME'];
			$obMonitoring = \CBitrixCloudMonitoring::getInstance();
			try{
				$bDomainFound = false;
				$arList = $obMonitoring->getList();
				foreach($arList as $arItem){
					if($arItem['DOMAIN'] == $strDomain){
						$bDomainFound = true;
						if(!in_array($strNewEmail, $arItem['EMAILS'])){
							$arItem['EMAILS'][] = $strNewEmail;
							$obMonitoring->startMonitoring($strDomain, $arItem['IS_HTTPS'] == 'Y', LANGUAGE_ID, 
								$arItem['EMAILS'], $arItem['TESTS']);
							$bResult = true;
						}
					}
				}
				if(!$bDomainFound){
					$arEmail = array(
						$strNewEmail,
					);
					$arTests = array(
						'test_http_response_time',
						'test_domain_registration',
						'test_lic',
						'test_ssl_cert_validity',
					);
					$obMonitoring->startMonitoring($strDomain, \CMain::IsHTTPS(), LANGUAGE_ID, $arEmail, $arTests);
					$bResult = true;
				}
			}
			catch(\Exception $e){}
		}
		return $bResult;
	}
	
	/**
	 *	Change log-file owner
	 */
	public static function changeFileOwner($strFilename){
		if(Cli::isCli() && Cli::isRoot() && function_exists('fileowner')){
			if(is_file($strFilename) || is_dir($strFilename)){
				$intBitrixUser = Cli::getBitrixUser();
				if(is_numeric($intBitrixUser)){
					$intOwner = @fileowner($strFilename);
					if($intOwner === 0){
						if(function_exists('chown')){
							if(chown($strFilename, $intBitrixUser)){
								if(function_exists('chgrp')){
									if(chgrp($strFilename, $intBitrixUser)){
										return true;
									}
								}
							}
						}
					}
					elseif($intOwner === $intBitrixUser){
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 *	Check dir is writeable
	 */
	public static function isDirWriteable($strDirname, $bRelative=false){
		$bResult = false;
		if(strlen($strDirname)){
			if($bRelative){
				$strDirname = static::root().$strDirname;
			}
			$bResult = true;
			$arPath = explode('/', $strDirname);
			$bFirstSlash = false;
			if(!strlen($arPath[0])){
				unset($arPath[0]);
				$bFirstSlash = true;
			}
			while(!empty($arPath)){
				$strPath = ($bFirstSlash?'/':'').implode('/', $arPath);
				if(is_dir($strPath)){
					$bResult = is_writeable($strPath);
					break;
				}
				else{
					array_pop($arPath);
				}
			}
			if(!is_writeable($strPath)){
				$bResult = false;
			}
		}
		return $bResult;
	}

	/**
	 *	CCatalog::GetByID with cache
	 */
	public static function getCatalogArray($intIBlockID) {
		$intIBlockID = IntVal($intIBlockID);
		$arCachedValue = &static::$arCacheCatalogArray[$intIBlockID];
		if(!is_array($arCachedValue)){
			$arCachedValue = array();
		}
		if($intIBlockID > 0) {
			if(!empty($arCachedValue)){
				return $arCachedValue;
			}
			elseif(Loader::includeModule('catalog')) {
				$arCatalog = \CCatalog::GetByID($intIBlockID);
				if(is_array($arCatalog) && !empty($arCatalog)) {
					$arCachedValue = $arCatalog;
					return $arCachedValue;
				}
				else { // Каталог теперь может не быть торговым каталогом, но может иметь торговые предложения
					$resCatalogs = \CCatalog::GetList(array(), array('PRODUCT_IBLOCK_ID' => $intIBlockID));
					if($arCatalog = $resCatalogs->getNext(false, false)){
						if (Loader::includeModule('iblock')) {
							$resIBlock = \CIBlock::GetList(array(), array('ID' => $intIBlockID));
							if($arIBlock = $resIBlock->GetNext(false, false)) {
								$arResult = array(
									'IBLOCK_ID' => $intIBlockID,
									'YANDEX_EXPORT' => 'N',
									'SUBSCRIPTION' => 'N',
									'VAT_ID' => 0,
									'PRODUCT_IBLOCK_ID' => 0,
									'SKU_PROPERTY_ID' => 0,
									'ID' => $intIBlockID,
									'IBLOCK_TYPE_ID' => $arIBlock['IBLOCK_TYPE_ID'],
									'LID' => $arIBlock['LID'],
									'NAME' => $arIBlock['NAME'],
									'OFFERS_IBLOCK_ID' => $arCatalog['IBLOCK_ID'],
									'OFFERS_PROPERTY_ID' => $arCatalog['SKU_PROPERTY_ID'],
									'OFFERS' => 'N',
								);
								return $arResult;
							}
						}
					}
				}
			}
		}
		return false;
	}
	
	/**
	 *	Check iblock is offers
	 */
	public static function isOffersIBlock($intIBlockId){
		$arCatalog = static::getCatalogArray($intIBlockId);
		return is_array($arCatalog) && is_numeric($arCatalog['PRODUCT_IBLOCK_ID']) && $arCatalog['PRODUCT_IBLOCK_ID'] > 0;
	}
	
	/**
	 *	Is property exists
	 */
	public static function isPropertyExists($strCode, $intIBlockId){
		$bPropertyExists = &static::$arCache['IS_PROPERTY_EXISTS'][$intIBlockId][$strCode];
		if(is_null($bPropertyExists) && \Bitrix\Main\Loader::includeModule('iblock')){
			$bPropertyExists = false;
			$resProp = \CIBlockProperty::getList([], ['IBLOCK_ID' => $intIBlockId, 'ACTIVE' => 'Y', 'CODE' => $strCode]);
			if($arProp = $resProp->getNext(false, false)){
				$bPropertyExists = true;
			}
		}
		return $bPropertyExists;
	}
	
	/**
	 *	Get stores list
	 */
	public static function getStoresList() {
		$arResult = array();
		if (Loader::includeModule('catalog') && class_exists('\CCatalogStore')) {
			$resStores = \CCatalogStore::GetList(array('SORT'=>'ASC'));
			while($arStore = $resStores->GetNext()) {
				$arResult[] = $arStore;
			}
			unset($resStores, $arStore);
		}
		return $arResult;
	}
	
	/**
	 *	Get stores list
	 */
	public static function getMeasuresList(){
		$arCachedValue = &static::$arCacheMeasureList[$intIBlockID];
		if(!is_array($arCachedValue)){
			$arCachedValue = array();
		}
		if(empty($arCachedValue)){
			if(Loader::includeModule('catalog')) {
				$resMeasure = \CCatalogMeasure::GetList(array(), array());
				while($arMeasure = $resMeasure->GetNext(false, false)) {
					$arCachedValue[$arMeasure['ID']] = $arMeasure;
				}
				unset($resMeasure, $arMeasure);
			}
		}
		return $arCachedValue;
	}
	
	/**
	 *	Get VAT
	 */
	public static function getVatValueByID($intVatID){
		$arVatValues = &static::$arCacheVatValue;
		if(!is_array($arVatValues)){
			$arVatValues = array();
		}
		if($intVatID > 0 && !isset($arVatValues[$intVatID]) && Loader::includeModule('catalog')){
			$resVat = \CCatalogVat::getList(array('RATE'=>'ASC'), array('ID' => $intVatID));
			if($arVat = $resVat->GetNext(false, false)) {
				if(stripos($arVat['NAME'], static::getMessage('ACRIT_CORE_NO_VAT_PRETEXT')) === false){
					return $arVat['RATE'];
				}
			}
		}
		return ''; // No vat
	}
	
	/*** IBLOCK METHODS ***/
	
	/**
	 *	Get section user fields
	 */
	public static function getSectionUserFields($intIBlockID, $strField=false) {
		$arResult = array();
		if (Loader::includeModule('iblock')) {
			$arFilter = array(
				'ENTITY_ID' => 'IBLOCK_'.$intIBlockID.'_SECTION',
			);
			if(!empty($strField)) {
				$arFilter['FIELD_NAME'] = $strField;
			}
			$resProps = \CUserTypeEntity::GetList(array('SORT'=>'ASC'), $arFilter);
			while ($arProp = $resProps->GetNext(false, false)) {
				$arProp = \CUserTypeEntity::GetByID($arProp['ID']);
				$arResult[$arProp['FIELD_NAME']] = $arProp;
			}
			if(!empty($strField)) {
				$arResult = $arResult[$strField];
				if(!is_array($arResult)) {
					$arResult = array();
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Field is property
	 */
	public static function isProperty($strCode) {
		if(preg_match('#^PROPERTY_(.*?)$#', $strCode, $arMatch)) {
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	Get list of iblocks
	 *	ToDo: cache ?
	 */
	public static function getIBlockList($bGroupByType=true, $bShowInActive=false, $bGetCount=false, $bGetCatalog=true) {
		$arCatalogs = array();
		if(Loader::includeModule('catalog') && $bGetCatalog){
			$resCatalogs = \CCatalog::GetList(array('SORT'=>'ASC', 'ID'=>'ASC'));
			while($arCatalog = $resCatalogs->getNext(false, false)){
				$arCatalogs[$arCatalog['IBLOCK_ID']] = $arCatalog;
			}
		}
		//
		$arResult = array();
		if (Loader::includeModule('iblock')) {
			$arFilter = array('CHECK_PERMISSIONS' => 'N');
			if ($bGroupByType !== false) {
				$resIBlockTypes = \CIBlockType::GetList(array('SORT'=>'ASC'), $arFilter);
				while ($arIBlockType = $resIBlockTypes->GetNext(false, false)) {
					$arIBlockTypeLang = \CIBlockType::GetByIDLang($arIBlockType['ID'], LANGUAGE_ID, false);
					$arResult[$arIBlockType['ID']] = array(
						'NAME' => $arIBlockTypeLang['NAME'],
						'ITEMS' => array(),
					);
				}
			}
			if ($bShowInActive !== true) {
				$arFilter['ACTIVE'] = 'Y';
			}
			if($bGetCount) {
				$arIBlocksCount = array();
				$resItems = \Bitrix\IBlock\ElementTable::getList(array(
					'select' => array(
						'IBLOCK_ID',
						'ELEMENT_CNT',
					),
					'runtime' => array(
						new \Bitrix\Main\Entity\ExpressionField('ELEMENT_CNT', 'COUNT(*)'),
					)
				));
				while($arItem = $resItems->fetch()){
					$arIBlocksCount[$arItem['IBLOCK_ID']] = IntVal($arItem['ELEMENT_CNT']);
				}
				unset($resItems, $arItem);
			}
			$resIBlock = \CIBlock::GetList(array('SORT'=>'ASC'), $arFilter, false);
			while ($arIBlock = $resIBlock->GetNext(false, false)){
				if(is_array($arCatalogs[$arIBlock['ID']])){
					$arIBlock['CATALOG'] = $arCatalogs[$arIBlock['ID']];
				}
				if($bGetCount) {
					$arIBlock['ELEMENT_CNT'] = $arIBlocksCount[$arIBlock['ID']];
				}
				if ($bGroupByType !== false){
					$arResult[$arIBlock['IBLOCK_TYPE_ID']]['ITEMS'][$arIBlock['ID']] = $arIBlock;
				}
				else {
					$arResult[$arIBlock['ID']] = $arIBlock;
				}
			}
		}
		foreach (EventManager::getInstance()->findEventHandlers(ACRIT_CORE, 'OnGetIBlockList') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $bGroupByType, $bShowInActive));
		}
		return $arResult;
	}
	
	/**
	 *	Get all sections for selected iblock
	 */
	public static function getIBlockSections($intIBlockID, $intMaxDepth=0) {
    $arResult = array();
		if(Loader::includeModule('iblock')){
			$arFilter = array(
				'IBLOCK_ID' => $intIBlockID,
				'CHECK_PERMISSIONS' => 'N',
			);
			if($intMaxDepth > 0){
				$arFilter['<=DEPTH_LEVEL'] = $intMaxDepth;
			}
			$resSections = \CIBlockSection::GetList(array('LEFT_MARGIN'=>'ASC'), $arFilter, false, 
				array('ID','NAME','DEPTH_LEVEL','IBLOCK_SECTION_ID'));
			while($arSection = $resSections->GetNext()){
				$arResult[] = $arSection;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get IBlockID for element
	 */
	public static function getElementIBlockID($intElementID){
		if(is_numeric($intElementID) && $intElementID > 0 && Loader::includeModule('iblock')) {
			$resElement = \CIBlockElement::GetList(array(), array('ID' => $intElementID), false, false, array('IBLOCK_ID'));
			if($arElement = $resElement->getNext(false, false)){
				return $arElement['IBLOCK_ID'];
			}
		}
		return false;
	}
	
	/**
	 *	Transform array of sections to tree of sections
	 */
	public static function sectionsArrayToTree($arSections){
		$arResult = array();
		$DepthLevel = 0;
		$arFirstSection = reset($arSections);
		$DepthLevelFirst = $arFirstSection['DEPTH_LEVEL'];
		$LastIndex = 0;
		$arParents = array();
		foreach ($arSections as $arSection) {
			$DepthLevel = $arSection['DEPTH_LEVEL'];
			if ($DepthLevel == $DepthLevelFirst) {
				$arResult[] = $arSection;
				$LastIndex = count($arResult)-1;
				$arParents[$DepthLevel] = &$arResult[$LastIndex];
			} else {
				$arParents[$DepthLevel-1]['SECTIONS'][] = $arSection;
				$LastIndex = count($arParents[$DepthLevel-1]['SECTIONS'])-1;
				$arParents[$DepthLevel] = &$arParents[$DepthLevel-1]['SECTIONS'][$LastIndex];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get element additional sections
	 */
	public static function getElementAdditionalSections($intElementID, $intMainSection=false){
		$arResult = array();
		if(Loader::includeModule('iblock')){
			if(!$intMainSection){
				$resItem = \CIBlockElement::getList(array(), array('ID' => $intElementID), false, false, array('ID', 'IBLOCK_SECTION_ID'));
				if($arItem = $resItem->getNext(false, false)){
					$intMainSection = $arItem['IBLOCK_SECTION_ID'];
				}
			}
			$resGroups = \CIBlockElement::GetElementGroups($intElementID, false, array('ID'));
			while($arGroup = $resGroups->getNext(false, false)){
				if($arGroup['ID'] !== $intMainSection) {
					$arResult[] = $arGroup['ID'];
				}
			}
		}
		unset($resItem, $arItem, $resGroups, $arGroup);
		return $arResult;
	}
	
	/**
	 *	Get property IDs by code (CIBlockProperty::getList cannot to do this)
	 */
	public static function getIBlockPropsIdByCode($intIBlockID, $arPropCodes){
		$arResult = array();
		$intIBlockID = IntVal($intIBlockID);
		$arPropCodes = array_map(function($strProp){
			return static::forSql($strProp);
		}, $arPropCodes);
		if(Loader::includeModule('iblock')){
			$resProps = \Bitrix\Iblock\PropertyTable::getList(array(
				'filter' => array(
					'IBLOCK_ID' => $intIBlockID,
					'CODE' => $arPropCodes,
				),
				'order' => array(
					'ID' => 'ASC',
				),
				'select' => array(
					'ID',
					'CODE',
				),
			));
			while($arProp = $resProps->fetch()){
				$arResult[$arProp['ID']] = $arProp['CODE'];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Include external lib: PhpSpreadsheet
	 */
	public static function includePhpSpreadSheet(){
		static $bIncluded;
		if($bIncluded){
			return true;
		}
		$strModuleDir = realpath(__DIR__.'/..');
		#
		$strPhpSpreadsheetVersion = '1.8.2';
		if(checkVersion(PHP_VERSION, '7.1.0')){
			$strPhpSpreadsheetVersion = '1.9.0';
		}
		#
		$strPhpSpreadsheetDir = Helper::path(realpath(__DIR__.'/../include/php_spreadsheet').'/'.$strPhpSpreadsheetVersion);
		if(is_dir($strPhpSpreadsheetDir)){
			$arFiles = Helper::scandir(realpath($strPhpSpreadsheetDir), ['EXT' => 'php']);
			$arPhpSpreadsheetClasses = [];
			foreach($arFiles as $strFileName){
				$strPatternClass = '#(^|(abstract|final)\s+)(class|interface)\s+(\w+)(?=\s+((extends\s+\w+)|(implements\s+\w+)))?#mi';
				$strPatternNamespace = '#^namespace\s+([^;]+);#mi';
				if(preg_match($strPatternClass, file_get_contents($strFileName), $arMatch)){
					$strClassName = $arMatch[4];
					if(preg_match($strPatternNamespace, file_get_contents($strFileName), $arMatch)){
						$strNamespace = $arMatch[1];
					}
					$arPhpSpreadsheetClasses[$strNamespace.'\\'.$strClassName] = substr($strFileName, strlen($strModuleDir) + 1);
				}
			}
			Loader::registerAutoLoadClasses(ACRIT_CORE, $arPhpSpreadsheetClasses);
			unset($arPhpSpreadsheetClasses, $arFiles, $strFileName, $strClassName, $arMatch, $strPatternClass, $strPatternNamespace);
			$bIncluded = true;
			return true;
		}
		return false;
	}
	
	/**
	 *	Get html template for object
	 */
	public static function getHtmlObject($strModuleId, $intProfileId, $strObject, $strTemplate=null, $arVariables=null){
		$strDir = realpath(__DIR__.'/../include/'.$strObject);
		if(is_dir($strDir) && !is_file($strDir.'/.exclude')){
			$strTemplate = is_string($strTemplate) ? toLower(trim($strTemplate)) : '';
			if(!strlen($strTemplate) || !is_file($strDir.'/'.$strTemplate.'.php')){
				$strTemplate = 'default';
			}
			$strFile = $strDir.'/'.$strTemplate.'.php';
			if(is_file($strFile)){
				if(is_array($arVariables)){
					foreach($arVariables as $strKey => $mValue){
						global ${$strKey};
						$GLOBALS[$strKey] = $mValue;
					}
				}
				ob_start();
				static::loadMessages($strFile);
				require $strFile;
				return trim(ob_get_clean());
			}
		}
		return null;
	}
	
	/**
	 *	
	 */
	public static function includePhpQuery(){
		if(!class_exists('\phpQuery')) {
			require_once(__DIR__.'/../include/phpquery/phpquery.php');
		}
	}
	
	/**
	 *	Stop buffering
	 */
	public static function obStop(){
		while(ob_get_level()){
			ob_clean();
		}
	}
	
	/**
	 *	Restart buffering
	 */
	public static function obRestart(){
		$GLOBALS['APPLICATION']->restartBuffer();
	}
	
	/**
	 *	Get dir with plugins
	 */
	public static function getPluginsDir($strSubdir){
		return '/bitrix/modules/'.static::id().'/plugins/'.$strSubdir.'/';
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
	 *	Call
	 */
	public static function call($strModuleId, $strClass, $strMethod, $arArguments=null){
		if(strlen($strModuleId) && strlen($strClass) && strlen($strMethod)) {
			if(Loader::includeModule($strModuleId)){
				if(!is_array($arArguments)){
					$arArguments = [];
				}
				$strIdShort = preg_replace('#^([a-z0-9]+)\.([a-z0-9]+)$#', '$2', $strModuleId);
				$strIdShort = toUpper(substr($strIdShort, 0, 1)).toLower(substr($strIdShort, 1));
				if(strpos($strClass, '\\') !== false){
					$strClass = end(explode('\\', $strClass));
				}
				$arReplace = [
					'#ID#' => $strIdShort,
					'#CLASS#' => $strClass,
					'#METHOD#' => $strMethod,
				];
				$strClass = '\Acrit\#ID#\#CLASS#';
				$strClass = str_replace(array_keys($arReplace), array_values($arReplace), $strClass);
				$strClassReal = null;
				if(class_exists($strClass)){
					$strClassReal = $strClass;
				}
				elseif(class_exists($strClass.'Table')){
					$strClassReal = $strClass.'Table';
				}
				if(strlen($strClassReal) && method_exists($strClassReal, $strMethod)){
					return call_user_func_array($strClassReal.'::'.$strMethod, $arArguments);
				}
			}
		}
		return null;
	}
	
	/**
	 *	Autoload classes
	 */
	public static function setModuleAutoloadClasses($strModuleId, $arClasses, $strNamespace=null){
		# Determine filename and get full classname
		$arClassesTmp = [];
		foreach($arClasses as $key => $strClass){
			$strFilename = 'lib/'.toLower(preg_replace('#Table$#i', '', $strClass)).'.php';
			$arClassesTmp[$strNamespace.'\\'.$strClass] = $strFilename;
		}
		$arClasses = $arClassesTmp;
		# Autoload
		Loader::registerAutoLoadClasses($strModuleId, $arClasses);
	}
	
	/**
	 *	Get current domain (without port)
	 */
	public static function getCurrentDomain(){
		return preg_replace('#:(\d+)$#', '', \Bitrix\Main\Context::getCurrent()->getServer()->getHttpHost());
	}
	
	/**
	 *	Add notify
	 */
	public static function addNotify($strModuleId, $strMesage, $strTag, $bClose=true){
		$arParams = [
			'MODULE_ID' => $strModuleId,
			'MESSAGE' => $strMesage,
			'TAG' => $strTag,
			'ENABLE_CLOSE' => $bClose ? 'Y' : 'N',
		];
		static::deleteNotify($strTag);
		return \CAdminNotify::add($arParams);
	}
	
	/**
	 *	Delete notify
	 */
	public static function deleteNotify($strTag){
		return \CAdminNotify::deleteByTag($strTag);
	}
	
	/**
	 *	Get notify list
	 */
	public static function getNotifyList($strModuleId){
		$arResult = [];
		$arSort = [
			'ID' => 'ASC',
		];
		$arFilter = [
			'MODULE_ID' => $strModuleId,
		];
		$resItems = \CAdminNotify::getList($arSort, $arFilter);
		while($arItem = $resItems->getNext()){
			$arResult[] = $arItem;
		}
		return $arResult;
	}
	
	/**
	 *	Replace \ to /
	 */
	public static function path($strPath){
		return str_replace('\\', '/', $strPath);
	}
	
	/**
	 *	Scan directory [can be recursively]
	 *	Params:
	 *		CALLBACK($strFileName, $arParams), default null
	 *		RECURSIVELY [true|false], default true
	 *		FILES [true|false], default true
	 *		DIRS [true|false], default false
	 */
	function scandir($strDir, $arParams=[]) {
		$arResult = [];
		if(!is_array($arParams)){
			$arParams = [];
		}
		if($arParams['RECURSIVELY'] !== false){
			$arParams['RECURSIVELY'] = true;
		}
		if($arParams['FILES'] !== false){
			$arParams['FILES'] = true;
		}
		if(strlen($strDir) && is_dir($strDir)){
			$resHandle = opendir($strDir);
			while(($strItem = readdir($resHandle)) !== false)  {
				if(!in_array($strItem, ['.', '..'])) {
					if(is_file($strDir.'/'.$strItem)) {
						if($arParams['FILES']){
							if(isset($arParams['EXT'])){
								$strExt = toUpper(pathinfo($strItem, PATHINFO_EXTENSION));
								$bAppropriate = (is_string($arParams['EXT']) && toUpper($arParams['EXT']) == $strExt) 
									|| is_array($arParams['EXT']) && in_array($strExt, array_map(function($strItem){
										return toUpper($strItem);
									}, $arParams['EXT']));
								if(!$bAppropriate){
									continue;
								}
							}
							$mCallbackResult = null;
							if(is_callable($arParams['CALLBACK'])){
								$mCallbackResult = call_user_func_array($arParams['CALLBACK'], [$strDir.'/'.$strItem, $arParams]);
							}
							if($mCallbackResult === false){
								continue;
							}
							$arResult[] = $strDir.'/'.$strItem;
						}
					} elseif(is_dir($strDir.'/'.$strItem)) {
						if($arParams['DIRS']){
							$arResult[] = $strDir.'/'.$strItem;
						}
						if($arParams['RECURSIVELY']){
							$arResult = array_merge($arResult, static::scandir($strDir.'/'.$strItem, $arParams));
						}
					}
				}
			}
			closedir($resHandle);
		}
		sort($arResult);
		return $arResult;
	}
	
	/**
	 *	Get url for module renew
	 */
	public static function getRenewUrl($strModuleId){
		$strRenewUrl = 'https://marketplace.1c-bitrix.ru/tobasket.php?ID='.$strModuleId;
		if(LICENSE_KEY != 'DEMO') {
			$strLicense = md5('BITRIX'.LICENSE_KEY.'LICENCE');
			$strRenewUrl .= '&lckey='.$strLicense;
		}
		return $strRenewUrl;
	}
	
	/**
	 *	Transpose array
	 */
	public static function transpose($arData){
		$arResult = [];
		if(is_array($arData)){
			foreach ($arData as $key1 => $arValue) {
				foreach ($arValue as $key2 => $value) {
					$arResult[$key2][$key1] = $value;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Send email
	 */
	public static function email($strEmailTo, $strSubject, $strBody, $bHtml=false, $strEmailFrom=false){
		if(!$strEmailFrom){
			$strEmailFrom = \Bitrix\Main\Config\Option::get('main', 'email_from');
		}
		return \Bitrix\Main\Mail\Mail::send([
			'TO' => $strEmailTo,
			'SUBJECT' => $strSubject,
			'BODY' => $strBody,
			'HEADER' => [
				'From' => $strEmailFrom,
			],
			'CHARSET' => defined('BX_UTF') && BX_UTF === true ? 'UTF-8' : 'windows-1251',
			'CONTENT_TYPE' => $bHtml ? 'html' : 'text',
		]);
	}
	
	/**
	 *	
	 */
	public static function checkDatabase($strModuleId){
		global $DB;
		$arSql = [];
		$strTableName = Helper::call($strModuleId, 'ExportData', 'getTableName');
		$arFields = array_keys(\Bitrix\Main\Application::getConnection()->getTableFields($strTableName));
		if(!in_array('OFFERS_SUCCESS', $arFields)){
			$arSql[] = "ALTER TABLE `{$strTableName}` ADD `OFFERS_SUCCESS` INT NULL AFTER `TIME`;";
		}
		if(!in_array('IS_OFFER', $arFields)){
			$arSql[] = "ALTER TABLE `{$strTableName}` ADD `IS_OFFER` CHAR(1) NULL DEFAULT NULL AFTER `TIME`;";
		}
		#
		foreach($arSql as $strSql){
			$mResult = !!$DB->query($strSql, true);
			Log::getInstance($strModuleId)->add($strSql.' => '.var_export($mResult, true));
		}
	}
	
	/**
	 *	1, 2,   3,4 => [1, 2, 3, 4]
	 */
	public static function explodeValues($strValue){
		return preg_split('#,\s*#', $strValue);
	}
	
	/**
	 *	Log for developers
	 */
	public static function devLog($mMessage=''){
		if($_SESSION['ACRIT_DEV'] == 'Y'){
			$arDebug = debug_backtrace(2);
			if(is_array($arDebug) && !empty($arDebug)){
				$arDebug = reset($arDebug);
				if(is_array($mMessage) && empty($mMessage))
					$mMessage = '--- Array is empty ---';
				elseif(is_array($mMessage) && !empty($mMessage))
					$mMessage = print_r($mMessage, true);
				elseif($mMessage === false)
					$mMessage = '[false]';
				elseif ($mMessage === true)
					$mMessage = '[true]';
				elseif ($mMessage === null)
					$mMessage = '[null]';
				$strFile = preg_replace('#^.*?(/bitrix/modules/.*?)$#', '$1', $arDebug['file']);
				$strLine = $arDebug['line'];
				$strMessage = sprintf('%s:%d', $strFile, $strLine);
				if(strlen($mMessage)){
					$strMessage = sprintf('%s: %s', $strMessage, $mMessage);
				}
				static::L($strMessage);
			}
		}
	}
	
}
?>