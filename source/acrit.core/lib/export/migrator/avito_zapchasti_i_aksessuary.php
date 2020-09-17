<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class AvitoZapchasti extends Base {
	
	const PLUGIN = 'AVITO';
	const FORMAT = 'AVITO_PARTS';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'avito_zapchasti_i_aksessuary';
	
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
			'TypeId' => 'TYPE_ID',
			#'' => 'AD_TYPE',
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