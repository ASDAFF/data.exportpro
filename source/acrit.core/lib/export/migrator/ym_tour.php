<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class YmTour extends Base {
	
	const PLUGIN = 'YANDEX_MARKET';
	const FORMAT = 'YANDEX_MARKET_TOURS';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'ym_tour';
	
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
			'WORLDREGION' => 'WORLD_REGION',
			'COUNTRY' => 'COUNTRY',
			'REGION' => 'REGION',
			'DAYS' => 'DAYS',
			'DATAtOUR' => 'DATA_TOUR',
			'NAME' => 'NAME',
			'HOTEL_STARS' => 'HOTEL_STARS',
			'ROOM' => 'ROOM',
			'MEAL' => 'MEAL',
			'INCLUDED' => 'INCLUDED',
			'TRANSPORT' => 'TRANSPORT',
			'DESCRIPTION' => 'DESCRIPTION',
			'AGE' => 'AGE',
			'PRICE_MIN' => 'PRICE_MIN',
			'PRICE_MAX' => 'PRICE_MAX',
			'OPTIONS' => 'OPTIONS',
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
		return array('PICTURE', 'BARCODE', 'DATA_TOUR');
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