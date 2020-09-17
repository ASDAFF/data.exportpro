<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class AvitoHome extends Base {
	
	const PLUGIN = 'AVITO';
	const FORMAT = 'AVITO_HOME';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'avito_home_and_dacha';
	
	/**
	 *
	 */
	public function _getFieldsMap(){
		$arResult = array(
			'Id' => 'ID',
			'DateBegin' => 'DATE_BEGIN',
			'DateEnd' => 'DATE_END',
			'ListingFee' => 'LISTING_FEE',
			'AdStatus' => 'AD_STATUS',
			'AvitoId' => 'AVITO_ID',
			'AllowEmail' => 'ALLOW_EMAIL',
			'ManagerName' => 'MANAGER_NAME',
			'ContactPhone' => 'CONTACT_PHONE',
			'Region' => 'REGION',
			'City' => 'CITY',
			'Subway' => 'SUBWAY',
			'District' => 'DISTRICT',
			'Description' => 'DESCRIPTION',
			'Category' => 'CATEGORY',
			'Image' => 'IMAGES',
			'VideoURL' => 'VIDEO_URL',
			'Price' => 'PRICE',
			'Title' => 'TITLE',
			#
			'GoodsType' => 'GOODS_TYPE',
			'AdType' => 'AD_TYPE',
		);
		return $arResult;
	}
	
	/**
	 *	
	 */
	public function getMultipleFields(){
		return array('PICTURE');
	}
	
	/**
	 *
	 */
	public function compileParams(&$arNewProfile){
		$arParams = &$arNewProfile['PARAMS'];
		#
	}
	
}

?>