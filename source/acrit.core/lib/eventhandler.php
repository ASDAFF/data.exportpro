<?
/**
 *	Class to work with handlers
 */

namespace Acrit\Core;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Update;
	
Helper::loadMessages(__FILE__);

/**
 * Event handler
 */
class EventHandler {

	/**
	 *	Add menu section
			\Bitrix\Main\EventManager::getInstance()->registerEventHandler(
				'main',
				'OnBuildGlobalMenu',
				$strModuleId,
				'\Acrit\Core\EventHandler',
				'OnBuildGlobalMenu'
			);
	 */
	public static function OnBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu){
		global $obAdminMenu, $APPLICATION;
		if(is_array($obAdminMenu->aGlobalMenu) && key_exists('global_menu_acrit', $obAdminMenu->aGlobalMenu)){
			return;
		}
		#
		$strAcritMenuGroupName = Helper::getOption(ACRIT_CORE, 'acritmenu_group_name');
		$strAcritMenuGroupSort = Helper::getOption(ACRIT_CORE, 'acritmenu_group_sort');
		$strAcritMenuGroupImage = Helper::getOption(ACRIT_CORE, 'acritmenu_group_image');
		#
		if(!strlen($strAcritMenuGroupName)){
			$strAcritMenuGroupName = Helper::getMessage('ACRITMENU_GROUP_NAME_DEFAULT');
		}
		if(!is_numeric($strAcritMenuGroupSort) || $strAcritMenuGroupSort <= 0){
			$strAcritMenuGroupSort = 150;
		}
		if(strlen($strAcritMenuGroupImage)){
			$APPLICATION->addHeadString('<style>
				.adm-main-menu-item.adm-acrit .adm-main-menu-item-icon{
					background:url("'.$strAcritMenuGroupImage.'") center center no-repeat;
				}
			</style>');
		}
		#
		$aMenu = array(
			'menu_id' => 'acrit',
			'sort' => $strAcritMenuGroupSort,
			'text' => $strAcritMenuGroupName,
			'icon' => 'clouds_menu_icon',
			'page_icon' => 'clouds_page_icon',
			'items_id' => 'global_menu_acrit',
			'items' => array()
		);
		$arGlobalMenu['global_menu_acrit'] = $aMenu;
	}
	
	/**
	 *	
	 */
	public static function onAfterEpilog(){
		Update::onAfterEpilog();
		# Auto start access check
		if(defined('ADMIN_SECTION')){
			if($GLOBALS['APPLICATION']->getCurPage() == '/bitrix/admin/site_checker.php'){
				if($_GET['tabControl_active_tab'] == 'edit2'){
					print('<script>
						BX.fireEvent(BX("access_submit"), "click");
					</script>');
				}
			}
		}
	}
	
	/**
	 *	
	 */
	public static function onModuleUpdate($arModules){
		Update::onModuleUpdate($arModules);
	}

}
