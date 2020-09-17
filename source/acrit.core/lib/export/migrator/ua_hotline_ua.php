<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class UaHotlineUa extends Base {
	
	const PLUGIN = 'HOTLINE_UA';
	const FORMAT = 'HOTLINE_UA_GENERAL';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'ua_hotline_ua';
	
	/**
	 *
	 */
	public function _getFieldsMap(){
		$arResult = array(
			'ID' => 'ID',
			'AVAILABLE' => 'AVAILABLE',
			'CATEGORYID' => 'SECTION_ID',
			'CODE' => 'CODE',
			'BARCODE' => 'BARCODE',
			'VENDOR' => 'VENDOR',
			'NAME' => 'NAME',
			'DESCRIPTION' => 'DESCRIPTION',
			'URL' => 'URL',
			'PICTURE' => 'PICTURE',
			'PRICE_RUAH' => 'PRICE',
			'OLDPRICE' => 'PRICE_OLD',
			'PRICE_RUSD' => 'PRICE_USD',
			'STOCK_DAYS' => 'STOCK_DAYS',
			'GUATANTEE_TYPE' => 'GUARANTEE_TYPE',
			'GUATANTEE_TIME' => 'GUARANTEE_DAYS',
			'ORIGINAL' => 'PARAM_ORIGINAL',
			'COUNTRY_OF_ORIGIN' => 'PARAM_MANUF_COUNTRY',
			'CUSTOM' => 'CUSTOM',
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
		$arParams['SHOP_ID'] = $this->arOldProfile['HOTLINE_FIRM_ID'];
		if(\Bitrix\Main\Loader::includeModule('currency')){
			$fFactor = \CCurrencyRates::GetConvertFactor('USD', 'UAH');
			if(is_numeric($fFactor) && $fFactor > 0){
				$arParams['SHOP_RATE'] = number_format($fFactor, 4, '.', '');
			}
		}
	}
	
}

?>