<?
namespace Acrit\Core;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Config\Option,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

/**
 * Class DynamicRemarketing
 * @package Acrit\Core
 */
class DynamicRemarketing {
	
	protected static $strSiteID;
	protected static $strContent;
	
	/**
	 *	Handler for 'main' => 'OnEndBufferContent'
	 */
	public static function onEndBufferContent(&$strContent){
		if(!defined('ADMIN_SECTION') || ADMIN_SECTION !== true){
			if(\Bitrix\Main\Context::getCurrent()->getServer()->getRequestMethod() != 'POST') {
				if(!\Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->isAjaxRequest()) {
					$strJsGoogle = trim(Option::get(ACRIT_CORE, 'dynamic_remarketing_google'));
					$strJsMailru = trim(Option::get(ACRIT_CORE, 'dynamic_remarketing_mailru'));
					if(strlen($strJsGoogle) || strlen($strJsMailru)) {
						if(\Bitrix\Main\Loader::includeModule('iblock')){
							static::$strContent = &$strContent;
							static::$strSiteID = SITE_ID;
							$arVariables = static::getCurrentVariables();
							if(is_numeric($arVariables['ELEMENT_ID']) && $arVariables['ELEMENT_ID']>0){
								$arJs = array();
								if(strlen($strJsGoogle)){
									$arJs[] = static::replaceMacros($strJsGoogle, $arVariables);
								}
								if(strlen($strJsMailru)){
									$arJs[] = static::replaceMacros($strJsMailru, $arVariables);
								}
								static::addToPage(implode("\n", $arJs));
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 *	Get all variables for current page
	 */
	protected static function getCurrentVariables(){
		$strSiteRoot = \CSite::GetSiteDocRoot(static::$strSiteID);
		$arRule = &$GLOBALS['val'];
		$arResult = array();
		$bUrlRewriteSuccess = false;
		if(defined('BX_URLREWRITE') && BX_URLREWRITE===true && is_array($arRule)){ // see /bitrix/modules/main/include/urlrewrite.php::116 [foreach($arUrlRewrite as $val)]
			if(isset($arRule['CONDITION'], $arRule['RULE'], $arRule['ID'], $arRule['PATH'])) {
				if($_SERVER['REAL_FILE_PATH'] == $arRule['PATH']) {
					$bUrlRewriteSuccess = true;
					$strComponent = &$arRule['ID'];
					$arResult = static::parsePhpScript($strSiteRoot.$arRule['PATH'], $strComponent);
				}
			}
		}
		if(!$bUrlRewriteSuccess){
			$arResult = static::parsePhpScript($_SERVER['SCRIPT_FILENAME']);
		}
		return $arResult;
	}
	
	/**
	 *	Parse single php-script (current)
	 */
	protected static function parsePhpScript($strFilename, $strComponent=false){
		$arResult = array();
		$arPageComponents = \PHPParser::ParseScript($GLOBALS['APPLICATION']->GetFileContent($strFilename));
		if(is_array($arPageComponents)){
			foreach($arPageComponents as $arPageComponent){
				if(strlen($strComponent) && $arPageComponent['DATA']['COMPONENT_NAME'] != $strComponent){
					continue;
				}
				$arParams = &$arPageComponent['DATA']['PARAMS'];
				if(is_array($arParams)){
					$arResult = static::parseVariables($arParams);
					break;
				}
			}
		}
		unset($arPageComponents, $arPageComponent);
		return $arResult;
	}
	
	/**
	 *	Parse variables from current page
	 */
	protected static function parseVariables($arParams){
		$arResult = array();
		// 1. bitrix:catalog and similar
		if(isset($arParams['SEF_MODE'])){
			$arResult = static::processBitrixCatalog($arParams);
		}
		// 2. bitrix:catalog.element and similar
		else {
			$arResult = static::processBitrixCatalogElement($arParams);
		}
		return $arResult;
	}
	
	/**
	 *	Process component like 'bitrix:catalog'
	 */
	protected static function processBitrixCatalog($arParams){
		$arResult = array();
		$arComponentVariables = array(
			'SECTION_ID',
			'SECTION_CODE',
			'ELEMENT_ID',
			'ELEMENT_CODE',
			'action',
		);
		$arVariables = array();
		$arUrlTemplates = \CComponentEngine::makeComponentUrlTemplates(array(), $arParams['SEF_URL_TEMPLATES']);
		$arVariableAliases = \CComponentEngine::makeComponentVariableAliases(array(), $arParams['VARIABLE_ALIASES']);
		$obComponent = new \CComponentEngine();
		$obComponent->addGreedyPart('#SECTION_CODE_PATH#');
		$obComponent->addGreedyPart('#SMART_FILTER_PATH#');
		$obComponent->setResolveCallback(array('CIBlockFindTools', 'resolveComponentEngine'));
		$strComponentPage = $obComponent->guessComponentPath($arParams['SEF_FOLDER'], $arUrlTemplates, $arVariables);
		\CComponentEngine::InitComponentVariables($strComponentPage, $arComponentVariables, $arVariableAliases, $arVariables);
		$arResult = &$arVariables;
		if(is_array($arResult) && !is_numeric($arResult['ELEMENT_ID']) && strlen($arResult['ELEMENT_CODE'])){
			$arFilter = array(
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'IBLOCK_LID' => static::$strSiteID,
				'IBLOCK_ACTIVE' => 'Y',
				'ACTIVE_DATE' => 'Y',
				'CHECK_PERMISSIONS' => 'Y',
				'MIN_PERMISSION' => 'R',
			);
			if(!is_numeric($arFilter['IBLOCK_ID'])){
				unset($arFilter['IBLOCK_ID']);
			}
			if($arParams['SHOW_DEACTIVATED'] !== 'Y'){
				$arFilter['ACTIVE'] = 'Y';
			}
			$arResult['ELEMENT_ID'] = \CIBlockFindTools::GetElementID(
				$arResult['ELEMENT_ID'],
				$arResult['ELEMENT_CODE'],
				$arResult['SECTION_ID'],
				$arResult['SECTION_CODE'],
				$arFilter
			);
			// because it can be incorrect url for one product
			if(!$arResult['ELEMENT_ID']){
				$arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
				$arResult['ELEMENT_ID'] = \CIBlockFindTools::GetElementID(
					$arResult['ELEMENT_ID'],
					$arResult['ELEMENT_CODE'],
					$arResult['SECTION_ID'],
					$arResult['SECTION_CODE'],
					$arFilter
				);
			}
			if(!$arResult['ELEMENT_ID']){
				$arResult['ELEMENT_ID'] = \CIBlockFindTools::GetElementID(
					$arResult['ELEMENT_ID'],
					$arResult['ELEMENT_CODE'],
					null,
					null,
					$arFilter
				);
			}
			unset($arFilter);
		}
		unset($arComponentVariables, $arUrlTemplates, $arVariableAliases, $obComponent, $strComponentPage);
		return $arResult;
	}
	
	/**
	 *	Process component like 'bitrix:catalog.element'
	 */
	protected static function processBitrixCatalogElement($arParams){
		$arResult = array();
		$arFilter = array(
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'IBLOCK_LID' => static::$strSiteID,
			'IBLOCK_ACTIVE' => 'Y',
			'ACTIVE_DATE' => 'Y',
			'CHECK_PERMISSIONS' => 'Y',
			'MIN_PERMISSION' => 'R',
		);
		if(!is_numeric($arFilter['IBLOCK_ID'])){
			unset($arFilter['IBLOCK_ID']);
		}
		if($arParams['SHOW_DEACTIVATED'] !== 'Y'){
			$arFilter['ACTIVE'] = 'Y';
		}
		$intElementID = \CIBlockFindTools::GetElementID(
			$arParams['ELEMENT_ID'],
			$arParams['ELEMENT_CODE'],
			$arParams['SECTION_ID'],
			$arParams['SECTION_CODE'],
			$arFilter
		);
		if($intElementID){
			$arResult['ELEMENT_ID'] = $intElementID;
		}
		$intSectionID = \CIBlockFindTools::GetSectionID(
			$arParams['SECTION_ID'],
			$arParams['SECTION_CODE'],
			$arFilter
		);
		if($intSectionID){
			$arResult['SECTION_ID'] = $intSectionID;
		}
		unset($arFilter, $intElementID, $intSectionID);
		return $arResult;
	}
	
	/**
	 *	Replace macros (#ELEMENT_ID# => 123) from variables
	 */
	protected static function replaceMacros($strJs, $arVariables){
		return str_replace('#ELEMENT_ID#', $arVariables['ELEMENT_ID'], $strJs);
	}
	
	/**
	 *	Add html-content to page
	 */
	protected static function addToPage($strNewContent){
		if(preg_match('#<body[^>]*>#is', static::$strContent, $arBodyMatch)){
			static::$strContent = str_replace($arBodyMatch[0], $arBodyMatch[0]."\n".$strNewContent, static::$strContent);
		}
	}

}
?>