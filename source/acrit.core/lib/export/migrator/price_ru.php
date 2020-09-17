<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class PriceRu extends Base {
	
	const PLUGIN = 'PRICE_RU';
	const FORMAT = 'PRICE_RU_GENERAL';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'price_ru';
	
	/**
	 *
	 */
	public function _getFieldsMap(){
		$arResult = array(
			'ID' => 'ID',
			'AVAILABLE' => 'AVAILABLE',
			'BID' => 'BID',
			'URL' => 'URL',
			'PRICE' => 'PRICE',
			'OLDPRICE' => 'OLDPRICE',
			'CURRENCYID' => 'CURRENCYID',
			'CATEGORYID' => 'CATEGORYID',
			'LOCAL_DELIVERY_COST' => 'LOCAL_DELIVERY_COST',
			'TYPEPREFIX' => 'TYPEPREFIX',
			'VENDOR' => 'VENDOR',
			'VENDORCODE' => 'VENDORCODE',
			'NAME' => 'NAME',
			'DESCRIPTION' => 'DESCRIPTION',
			'BARCODE' => 'BARCODE',
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
		$arParams['COMPANY'] = $this->arOldProfile['COMPANY'];
	}
	
}

?>