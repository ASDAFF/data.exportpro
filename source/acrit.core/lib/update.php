<?

namespace Acrit\Core;

use 
	\Acrit\Core\Helper;

class Update{
	
	const UPDATE_INTERVAL = 86400; // 24*60*60
	
	/**
	 *	Check module updates
	 */
	public static function checkModuleUpdates($strModuleId, &$intDateTo, &$strLastVersion, $bCheckCore=true){
		$arAvailableUpdates = [];
		$arAllUpdates = static::getAllAvailableUpdates();
		if(is_array($arAllUpdates) && is_array($arAllUpdates['MODULE'])){
			foreach($arAllUpdates['MODULE'] as $arModuleData){
				if($arModuleData['@']['ID'] == $strModuleId){
					if(preg_match('#^(\d{1,2})\.(\d{1,2})\.(\d{4})$#', $arModuleData['@']['DATE_TO'], $arMatch)){
						$intDateTo = mktime(23, 59, 59, $arMatch[2], $arMatch[1], $arMatch[3]);
					}
					if(is_array($arModuleData['#']) && is_array($arModuleData['#']['VERSION'])){
						foreach($arModuleData['#']['VERSION'] as $arVersion){
							$arAvailableUpdates[$strModuleId][$arVersion['@']['ID']] = $arVersion['#']['DESCRIPTION'][0]['#'];
							$strLastVersion = $arVersion['@']['ID'];
						}
					}
				}
				elseif($bCheckCore && $arModuleData['@']['ID'] == ACRIT_CORE){
					if(is_array($arModuleData['#']) && is_array($arModuleData['#']['VERSION'])){
						foreach($arModuleData['#']['VERSION'] as $arVersion){
							$arAvailableUpdates[ACRIT_CORE][$arVersion['@']['ID']] = $arVersion['#']['DESCRIPTION'][0]['#'];
						}
					}
				}
			}
		}
		return $arAvailableUpdates;
	}
	
	/**
	 *	
	 */
	protected static function getAllAvailableUpdates(){
		$arResult = [];
		include_once(Helper::root().'/bitrix/modules/main/classes/general/update_client_partner.php');
		if(class_exists('\CUpdateClientPartner')) {
			if(php_sapi_name() == 'cli'){
				$_SERVER['SERVER_NAME'] = Helper::getOption('main', 'server_name');
			}
			$arModulesId = [ACRIT_CORE];
			$arResult = \CUpdateClientPartner::getUpdatesList($strError=null, LANGUAGE_ID, 'Y', $arModulesId, ['fullmoduleinfo'=>'Y']);
			if(!is_array($arResult)){
				$arResult = [];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Display check updates in any our module
	 */
	public static function display(){
		if(Helper::getOption(ACRIT_CORE, 'check_updates') == 'Y'){
			#\CJSCore::init('acrit-core-update-module');
			\Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/'.ACRIT_CORE.'/check_updates.js');
			print '<div id="acrit-module-update-notifier"></div>';
		}
	}
	
	/**
	 *	Check all updates
	 */
	public static function checkUpdates(){
		# Delete previous notify messages
		$strCompare = static::getNotifyTag('');
		foreach(Helper::getNotifyList(ACRIT_CORE) as $arItem){
			if(stripos($arItem['TAG'], $strCompare) === 0){
				Helper::deleteNotify($arItem['TAG']);
			}
		}
		# Check new updates
		$arAllUpdates = static::getAllAvailableUpdates();
		if(is_array($arAllUpdates) && is_array($arAllUpdates['MODULE'])){
			foreach($arAllUpdates['MODULE'] as $arModuleData){
				if(is_array($arModuleData['#']) && is_array($arModuleData['#']['VERSION'])){
					$strModuleId = $arModuleData['@']['ID'];
					if(preg_match('#^acrit\.(.*?)$#', $strModuleId, $arMatch) && strlen(Helper::getModuleVersion($strModuleId))){
						$strLastVersion = null;
						foreach($arModuleData['#']['VERSION'] as $arVersion){
							$strLastVersion = $arVersion['@']['ID'];
						}
						if($strLastVersion){
							$arLang = [
								'#MODULE_NAME#' => Helper::getModuleName($strModuleId),
								'#MODULE_ID#' => $strModuleId,
								'#VERSION_CURRENT#' => Helper::getModuleVersion($strModuleId),
								'#VERSION_NEW#' => $strLastVersion,
								'#LANGUAGE_ID#' => LANGUAGE_ID,
							];
							if($arModuleData['@']['UPDATE_END'] == 'Y'){
								$strMessage = Helper::getMessage('ACRIT_CORE_RENEWAL_NOTIFY', $arLang);
							}
							else{
								$strMessage = Helper::getMessage('ACRIT_CORE_UPDATE_NOTIFY', $arLang);
							}
							Helper::addNotify(ACRIT_CORE, $strMessage, static::getNotifyTag($strModuleId), true);
						}
					}
				}
			}
		}
		Helper::setOption(ACRIT_CORE, 'check_updates_last_time', time());
		return true;
	}
	
	/**
	 *	Get notify tag for module
	 */
	public static function getNotifyTag($strModuleId){
		return ACRIT_CORE.'_update_for_'.$strModuleId;
	}
	
	/**
	 *	Auto check updates
	 */
	public static function onAfterEpilog(){
		if(defined('ADMIN_SECTION') && is_object($GLOBALS['USER']) && $GLOBALS['USER']->isAdmin()){
			$bAjax = \Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->isAjaxRequest();
			$bPost = \Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->isPost();
			if(!$bAjax && !$bPost){
				$arExclude = [
					'/bitrix/admin/update_system_partner_act.php',
					'/bitrix/admin/update_system_act.php',
				];
				$strUrl = $GLOBALS['APPLICATION']->getCurPage(false);
				if(!in_array($strUrl, $arExclude)){
					if(Helper::getOption(ACRIT_CORE, 'check_updates_regular') == 'Y'){
						$intLastTimeCheck = Helper::getOption(ACRIT_CORE, 'check_updates_last_time');
						if(!is_numeric($intLastTimeCheck) || $intLastTimeCheck <= 0){
							$intLastTimeCheck = 0;
						}
						if(!$intLastTimeCheck ||(time() - $intLastTimeCheck >= static::UPDATE_INTERVAL)){
							print '<script src="/bitrix/js/'.ACRIT_CORE.'/check_updates_regular.js"></script>';
						}
					}
				}
			}
		}
	}

	/**
	 *	Delete update notification after update is installed
	 */
	public static function onModuleUpdate($arModules){
		if(is_array($arModules)){
			foreach($arModules as $strModuleId){
				Helper::deleteNotify(static::getNotifyTag($strModuleId));
			}
		}
	}
	
}
?>