<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class AvitoPredlozheniyaUslug extends Base {
	
	const PLUGIN = 'AVITO';
	const FORMAT = 'AVITO_SERVICES';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'avito_predlozheniya_uslug';
	
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
			'Price' => 'PRICE',
			'Title' => 'TITLE',
			#
			'ServiceType' => 'SERVICE_TYPE',
			'ServiceSubtype' => 'SERVICE_SUBTYPE',
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