<?
namespace Acrit\Core;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Config\Option,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

/**
 * Class GoogleTagManager
 * @package Acrit\Core\Export
 */
class GoogleTagManager {
	
	const TYPE_HEAD = '#<head[^>]*>#is';
	const TYPE_BODY = '#<body[^>]*>#is';
	
	protected static $strSiteID;
	protected static $strContent;
	
	/**
	 *	Handler for 'main' => 'OnEndBufferContent'
	 */
	public static function onEndBufferContent(&$strContent){
		if(!defined('ADMIN_SECTION') || ADMIN_SECTION !== true){
			if(\Bitrix\Main\Context::getCurrent()->getServer()->getRequestMethod() != 'POST') {
				 // start
				$obRequest = \Bitrix\Main\Application::GetInstance()->getContext()->getRequest();
				if(method_exists($obRequest, 'isAjaxRequest')){
					$bAjax = $obRequest->isAjaxRequest();
				}
				else{
					$bAjax = $_SERVER['HTTP_BX_AJAX'] !== null ||	$_SERVER['HTTP_X_REQUESTED_WITH'] === "XMLHttpRequest";
				}
				if(!$bAjax) {
				// end
					$strID = static::getID();
					if(strlen($strID)){
						static::$strContent = &$strContent;
						if(stripos(static::$strContent, 'googletagmanager.com/gtm.js') === false) {
							static::addToPage(trim(static::getHeadTemplate()), static::TYPE_HEAD);
						}
						if(stripos(static::$strContent, 'googletagmanager.com/ns.html') === false) {
							static::addToPage(trim(static::getBodyTemplate()), static::TYPE_BODY);
						}
					}
				}
			}
		}
	}
	
	/**
	 *	Get template for <head>
	 */
	protected static function getHeadTemplate(){
		$strID = static::getID();
		return
'
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);})(window,document,\'script\',\'dataLayer\',\''.$strID.'\');</script>
<!-- End Google Tag Manager -->
';
	}
	
	/**
	 *	Get template for <body>
	 */
	protected static function getBodyTemplate(){
		$strID = static::getID();
		return
'
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$strID.'" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
';
	}
	
	/**
	 *	Get tag manager ID
	 */
	protected static function getID(){
		return trim(Option::get(ACRIT_CORE, 'google_tagmanager_id'));
	}
	
	/**
	 *	Add html-content to page (head or body)
	 */
	protected static function addToPage($strNewContent, $strType){
		$strPattern = $strType;
		if(preg_match($strPattern, static::$strContent, $arMatch)){
			static::$strContent = str_replace($arMatch[0], $arMatch[0]."\n".$strNewContent, static::$strContent);
		}
	}

}
?>