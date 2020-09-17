<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\Cron,
	\Acrit\Core\Export\Crontab,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

/**
 * Class Debug
 * @package Acrit\Core\Export
 */
class Debug {
	
	static $strModuleId = null;
	
	static $arData = array(); // PROFILE_ID, IBLOCK_ID, OFFERS_IBLOCK_ID, PLUGIN, CATALOG
	
	/**
	 *	Set static module id
	 */
	public static function setModuleId($strModuleId){
		static::$strModuleId = $strModuleId;
	}
	
	/**
	 *	
	 */
	public static function printData($strText){
		$strText = print_r($strText, true);
		print htmlspecialcharsbx($strText);
	}
	
	/**
	 *	Find first product
	 */
	public static function findFirst(){
		static::_getProduct(array('ID' => 'ASC')); # ToDo: сортировка с учетом настройки профиля
	}
	
	/**
	 *	Find random product
	 */
	public static function findRandom(){
		static::_getProduct(array('RAND' => 'ASC'));
	}
	
	/**
	 *	Find selected product
	 */
	public static function findSelected($intElementID){
		static::_getProduct(array(), $intElementID);
	}
	
	/**
	 *	Generate first product
	 */
	public static function generateFirst(){
		static::_generateProduct(array('ID' => 'ASC')); # ToDo: сортировка с учетом настройки профиля
	}
	
	/**
	 *	Generate first product
	 */
	public static function generateRandom(){
		static::_generateProduct(array('RAND' => 'ASC'));
	}
	
	/**
	 *	Generate selected product
	 */
	public static function generateSelected($intElementID){
		static::_generateProduct(array(), $intElementID);
	}
	
	/**
	 *	Show filter for main IBlock
	 */
	public static function showFilter(){
		#print_r(Profile::getFilter(static::$arData['PROFILE_ID'], static::$arData['IBLOCK_ID']));
		static::printData(Helper::call(static::$strModuleId, 'Profile', 'getFilter', [static::$arData['PROFILE_ID'], static::$arData['IBLOCK_ID']]));
	}
	
	/**
	 *	Show filter for IBlock
	 */
	public static function showOffersFilter(){
		#print_r(Profile::getFilter(static::$arData['PROFILE_ID'], static::$arData['OFFERS_IBLOCK_ID']));
		static::printData(Helper::call(static::$strModuleId, 'Profile', 'getFilter', [static::$arData['PROFILE_ID'], static::$arData['OFFERS_IBLOCK_ID']]));
	}
	
	/**
	 *	Show SQL for filter for main IBlock
	 */
	public static function showFilterSql(){
		static::_showFilterSql(static::$arData['IBLOCK_ID']);
	}
	
	/**
	 *	Show SQL for filter for offers IBlock
	 */
	public static function showOffersFilterSql(){
		static::_showFilterSql(static::$arData['OFFERS_IBLOCK_ID']);
	}
	
	/********************************************************************************************************************/
	
	protected static function _getProduct($arSort, $intElementID=null){
		#$arFilter = Profile::getFilter(static::$arData['PROFILE_ID'], static::$arData['IBLOCK_ID']);
		$arFilter = Helper::call(static::$strModuleId, 'Profile', 'getFilter', [static::$arData['PROFILE_ID'], static::$arData['IBLOCK_ID']]);
		if(empty($arFilter)){
			print 'No IBlocks configured in profile.';
			return false;
		}
		if($intElementID > 0){
			$arFilter[] = array(
				'ID' => $intElementID,
			);
		}
		$arSelect = array(
			'ID',
			'IBLOCK_ID',
			'IBLOCK_TYPE_ID',
			'IBLOCK_SECTION_ID',
			'NAME',
			'DETAIL_PAGE_URL',
		);
		$resItem = \CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
		if($arItem = $resItem->getNext()){
			$arItem['IBLOCK_SECTION_ID'] = is_numeric($arItem['IBLOCK_SECTION_ID']) ? $arItem['IBLOCK_SECTION_ID'] : -1;
			$strLang = LANGUAGE_ID;
			$strUrl = Exporter::getInstance(static::$strModuleId)->getElementPreviewUrl($arItem['ID'], static::$arData['PROFILE_ID']);
			print "<script>window.open('{$strUrl}')</script>";
			print "<a href=\"{$strUrl}\" target=\"_blank\">[{$arItem['ID']}] {$arItem['NAME']}</a>";
		}
		else{
			print 'Not found.';
		}
	}
	
	protected static function _generateProduct($arSort, $intElementID=null){
		#$arFilter = Profile::getFilter(static::$arData['PROFILE_ID'], static::$arData['IBLOCK_ID']);
		$arFilter = Helper::call(static::$strModuleId, 'Profile', 'getFilter', [static::$arData['PROFILE_ID'], static::$arData['IBLOCK_ID']]);
		if($intElementID > 0){
			$arFilter[] = array(
				'ID' => $intElementID,
			);
		}
		$arSelect = array(
			'ID',
			'IBLOCK_ID',
		);
		$resItem = \CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
		if($arItem = $resItem->getNext()){
			$arPreview = Exporter::processElement($arItem['ID'], $arItem['IBLOCK_ID'], static::$arData['PROFILE_ID'], 
				Exporter::PROCESS_MODE_PREVIEW, static::$strModuleId);
			if(is_array($arPreview) && is_array($arPreview[static::$strModuleId])){
				foreach($arPreview[static::$strModuleId]['RESULT']['PROFILES'][static::$arData['PROFILE_ID']]['_PREVIEW'] as $arDataItem){
					print Exporter::displayPreviewResult($arDataItem);
				}
			}
			?>
			<script>
			$('div[data-role="console-results"] pre code.xml').each(function(i, block) {
				highlighElement(block);
			});
			</script>
			<?
		}
		else{
			print 'Not found.';
		}
	}
	
	protected static function _showFilterSql($intIBlockID){
		global $DB;
		$DB->ShowSqlStat = true;
		$DB->arQueryDebug = array();
		#$arFilter = Profile::getFilter(static::$arData['PROFILE_ID'], $intIBlockID);
		$arFilter = Helper::call(static::$strModuleId, 'Profile', 'getFilter', [static::$arData['PROFILE_ID'], $intIBlockID]);
		\Bitrix\Main\Loader::includeModule('iblock');
		\CIBlockElement::getList(array(), $arFilter);
		foreach($DB->arQueryDebug as $obSqlTracker){
			$strSql = rtrim($obSqlTracker->getSql());
			$strSql = preg_replace('#^\t{3}#m', '', $strSql);
			print_r(htmlspecialcharsbx($strSql));
		}
		$DB->ShowSqlStat = false;
		$DB->arQueryDebug = array();
	}
	
	/**
	 *	Log something
	 */
	public static function L($mMessage){
		static $fTime;
		$bStarted = false;
		if(is_null($fTime)){
			$fTime = microtime(true);
			$bStarted = true;
		}
		$mMessage = is_array($mMessage) ? print_r($mMessage, true) : $mMessage;
		$mMessage = '['.number_format(microtime(true) - $fTime, 5, '.', '').'] '.$mMessage;
		Log::getInstance(static::$strModuleId)->add($mMessage);
	}
	
	/**
	 *	Simple exec-wrapper
	 */
	public static function exec($strCommand){
		exec($strCommand, $arExecResult);
		static::printData($arExecResult);
	}

}
?>