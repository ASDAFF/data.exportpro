<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class YmEventTicket extends Base {
	
	const PLUGIN = 'YANDEX_MARKET';
	const FORMAT = 'YANDEX_MARKET_EVENT_TICKETS';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'ym_event_ticket';
	
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
			'NAME' => 'NAME',
			'DESCRIPTION' => 'DESCRIPTION',
			'AGE' => 'AGE',
			'SALES_NOTES' => 'SALES_NOTES',
			'ADULT' => 'ADULT',
			'BARCODE' => 'BARCODE',
			'PLACE' => 'PLACE',
			'HALL' => 'HALL',
			'HALL_PART' => 'HALL_PART',
			'DATE' => 'DATE',
			'IS_PREMIERE' => 'IS_PREMIERE',
			'IS_KIDS' => 'IS_KIDS',
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