<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class AvitoRealty extends Base {
	
	const PLUGIN = 'AVITO';
	const FORMAT = 'AVITO_REALTY';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'avito_realty';
	
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
			'Title' => 'TITLE',
			'Price' => 'PRICE',
			#
			'Street' => 'STREET',
			'Latitude' => 'LATITUDE',
			'Longitude' => 'LONGITUDE',
			'DistanceToCity' => 'DISTANCE_TO_CITY',
			'DirectionRoad' => 'DIRECTION_ROAD',
			'OperationType' => 'OPERATION_TYPE',
			'Country' => 'COUNTRY',
			'PriceType' => 'PRICE_TYPE',
			'Rooms' => 'ROOMS',
			'Square' => 'SQUARE',
			'KitchenSpace' => 'KITCHEN_SPACE',
			'LivingSpace' => 'LIVING_SPACE',
			'LandArea' => 'LAND_AREA',
			'Floor' => 'FLOOR',
			'Floors' => 'FLOORS',
			'HouseType' => 'HOUSE_TYPE',
			'WallsType' => 'WALLS_TYPE',
			'MarketType' => 'MARKET_TYPE',
			'NewDevelopmentId' => 'NEW_DEVELOPMENT_ID',
			'PropertyRights' => 'PROPERTY_RIGHTS',
			'ObjectType' => 'OBJECT_TYPE',
			'ObjectSubtype' => 'OBJECT_SUBTYPE',
			'Secured' => 'SECURED',
			'BuildingClass' => 'BUILDING_CLASS',
			'CadastralNumber' => 'CADASTRAL_NUMBER',
			'LeaseType' => 'LEASE_TYPE',
			'LeaseBeds' => 'LEASE_BEDS',
			'LeaseSleepingPlaces' => 'LEASE_SLEEPING_PLACES',
			'LeaseMultimediaOption' => 'LEASE_MULTIMEDIA',
			'LeaseAppliancesOption' => 'LEASE_APPLIANCES',
			'LeaseComfortOption' => 'LEASE_COMFORT',
			'LeaseAdditionallyOption' => 'LEASE_ADDITIONALLY',
			'LeaseCommissionSize' => 'LEASE_COMMISSION_SIZE',
			'LeaseDeposit' => 'LEASE_DEPOSIT',
		);
		return $arResult;
	}
	
	/**
	 *	
	 */
	public function getMultipleFields(){
		return array('PICTURE', 'LEASE_MULTIMEDIA', 'LEASE_APPLIANCES', 'LEASE_COMFORT', 'LEASE_ADDITIONALLY');
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