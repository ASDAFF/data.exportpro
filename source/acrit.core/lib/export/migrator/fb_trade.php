<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class FbTrade extends Base {
	
	const PLUGIN = 'FACEBOOK';
	const FORMAT = 'FACEBOOK_TRADE';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'fb_trade';
	
	/**
	 *
	 */
	public function _getFieldsMap(){
		$arResult = array(
			'ID' => 'ID',
			'AVAILABLE' => 'AVAILABILITY',
			'URL' => 'LINK',
			'PRICE' => 'PRICE',
			'CURRENCYID' => '_CURRENCY',
			'PHOTO' => 'IMAGE_LINK',
			'NAME' => 'TITLE',
			'DESCRIPTION' => 'DESCRIPTION',
		);
		return $arResult;
	}
	
	/**
	 *
	 */
	public function compileParams(&$arNewProfile){
		$arParams = &$arNewProfile['PARAMS'];
		#
		$arParams['SHOP_NAME'] = $this->arOldProfile['SHOPNAME'];
		$arParams['SHOP_COMPANY'] = $this->arOldProfile['COMPANY'];
		$arParams['DELIVERY'] = array('COST' => '', 'DAYS' => '', 'ORDER_BEFORE' => '');
		$arParams['ENCODING'] = Helper::isUtf() ? 'UTF-8' : 'windows-1251';
		$arParams['COMPRESS_TO_ZIP'] = $this->arOldProfile['USE_COMPRESS'] == 'Y' ? 'Y' : 'N';
		$arParams['DELETE_XML_IF_ZIP'] = 'N';
		$arParams['ENABLE_AUTO_DISCOUNTS'] = 'N';
		$arParams['COMPRESS_TO_ZIP'] = $this->arOldProfile['USE_COMPRESS'] == 'Y' ? 'Y' : 'N';
	}
	
}

?>