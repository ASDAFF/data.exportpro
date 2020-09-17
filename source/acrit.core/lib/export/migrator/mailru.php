<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class MailRu extends Base {
	
	const PLUGIN = 'TORG_MAIL_RU';
	const FORMAT = 'TORG_MAIL_RU_GENERAL';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'mailru';
	
	/**
	 *
	 */
	public function _getFieldsMap(){
		$arResult = array(
			'ID' => 'ID',
			'AVAILABLE' => 'AVAILABLE',
			'BID' => 'CBID',
			'URL' => 'URL',
			'PRICE' => 'PRICE',
			'OLD_PRICE' => 'OLDPRICE',
			'CURRENCYID' => 'CURRENCY_ID',
			'PICTURE' => 'PICTURE',
			'TYPEPREFIX' => 'TYPE_PREFIX',
			'VENDOR' => 'VENDOR',
			'MODEL' => 'MODEL',
			'VENDORCODE' => 'VENDOR_CODE',
			'NAME' => 'NAME',
			'DESCRIPTION' => 'DESCRIPTION',
			'DELIVERY' => 'DELIVERY',
			'PICKUP' => 'PICKUP',
			'LOCAL_DELIVERY_COST' => 'LOCAL_DELIVERY_COST',
			'UTM_SOURCE' => 'UTM_SOURCE',
			'UTM_MEDIUM' => 'UTM_MEDIUM',
			'UTM_TERM' => 'UTM_TERM',
			'UTM_CONTENT' => 'UTM_CONTENT',
			'UTM_CAMPAIGN' => 'UTM_CAMPAIGN',
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