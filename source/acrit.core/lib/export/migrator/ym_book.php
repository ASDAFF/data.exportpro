<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class YmBook extends Base {
	
	const PLUGIN = 'YANDEX_MARKET';
	const FORMAT = 'YANDEX_MARKET_BOOKS';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'ym_book';
	
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
			'CURRENCYID' => 'CURRENCY_ID',
			'VAT' => 'VAT',
			'PICTURE' => 'GROUP_ID',
			'STORE' => 'STORE',
			'PICKUP' => 'PICKUP',
			'DELIVERY' => 'DELIVERY',
			'LOCAL_DELIVERY_COST' => 'DELIVERY_OPTIONS_COST',
			'LOCAL_DELIVERY_DAYS' => 'DELIVERY_OPTIONS_DAYS',
			'LOCAL_ORDER_BEFORE' => 'DELIVERY_OPTIONS_ORDER_BEFORE',
			'AUTHOR' => 'AUTHOR',
			'NAME' => 'NAME',
			'PUBLISHER' => 'PUBLISHER',
			'SERIES' => 'SERIES',
			'YEAR' => 'YEAR',
			'ISBN' => 'ISBN',
			'VOLUME' => 'VOLUME',
			'PART' => 'PART',
			'LANGUAGE' => 'LANGUAGE',
			'BINDING' => 'BINDING',
			'PAGE_EXTENT' => 'PAGE_EXTENT',
			'TABLE_OF_CONTENTS' => 'TABLE_OF_CONTENTS',
			'DESCRIPTION' => 'DESCRIPTION',
			'DOWNLOADABLE' => 'DOWNLOADABLE',
			'AGE' => 'AGE',
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