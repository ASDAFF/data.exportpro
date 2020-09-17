<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class YmVendorModel extends Base {
	
	const PLUGIN = 'YANDEX_MARKET';
	const FORMAT = 'YANDEX_MARKET_VENDOR_MODEL';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'ym_vendormodel';
	
	/**
	 *
	 */
	public function _getFieldsMap(){
		$arResult = array(
			'ID' => 'ID',
			'AVAILABLE' => 'AVAILABLE',
			'BID' => 'BID',
			'CBID' => 'CBID',
			'URL' => 'URL',
			'PRICE' => 'PRICE',
			'OLDPRICE' => 'OLD_PRICE',
			'CURRENCYID' => 'CURRENCY_ID',
			'VAT' => 'VAT',
			'GROUPID' => 'GROUP_ID',
			'PICTURE' => 'PICTURE',
			'STORE' => 'STORE',
			'PICKUP' => 'PICKUP',
			'DELIVERY' => 'DELIVERY',
			'LOCAL_DELIVERY_COST' => 'DELIVERY_OPTIONS_COST',
			'LOCAL_DELIVERY_DAYS' => 'DELIVERY_OPTIONS_DAYS',
			'LOCAL_ORDER_BEFORE' => 'DELIVERY_OPTIONS_ORDER_BEFORE',
			'TYPEPREFIX' => 'TYPE_PREFIX',
			'VENDOR' => 'VENDOR',
			'VENDORCODE' => 'VENDOR_CODE',
			'MODEL' => 'MODEL',
			'DESCRIPTION' => 'DESCRIPTION',
			'SALES_NOTES' => 'SALES_NOTES',
			'MANUFACTURER_WARRANTY' => 'MANUFACTURER_WARRANTY',
			'COUNTRY_OF_ORIGIN' => 'COUNTRY_OF_ORIGIN',
			'DOWNLOADABLE' => 'DOWNLOADABLE',
			'ADULT' => 'ADULT',
			'AGE' => 'AGE',
			'BARCODE' => 'BARCODE',
			'EXPIRY' => 'EXPIRY',
			'WEIGHT' => 'WEIGHT',
			'DIMENSIONS' => 'DIMENSIONS',
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
	public function getMultipleFields(){
		return array('PICTURE', 'BARCODE');
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