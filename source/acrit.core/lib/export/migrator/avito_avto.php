<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class AvitoAvto extends Base {
	
	const PLUGIN = 'AVITO';
	const FORMAT = 'AVITO_CARS';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'avito_avto';
	
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
			#
			'Street' => 'STREET',
			'Latitude' => 'LATITUDE',
			'Longitude' => 'LONGITUDE',
			'CarType' => 'CAR_TYPE',
			'Make' => 'MAKE',
			'Model' => 'MODEL',
			'Year' => 'YEAR',
			'Kilometrage' => 'KILOMETRAGE',
			'Accident' => 'ACCIDENT',
			'VIN' => 'VIN',
			'CertificationNumber' => 'CERTIFICATION_NUMBER',
			'BodyType' => 'BODY_TYPE',
			'Doors' => 'DOORS',
			'Color' => 'COLOR',
			'FuelType' => 'FUEL_TYPE',
			'EngineSize' => 'ENGINE_SIZE',
			'Power' => 'POWER',
			'Transmission' => 'TRANSMISSION',
			'DriveType' => 'DRIVE_TYPE',
			'WheelType' => 'WHEEL_TYPE',
			'Owners' => 'OWNERS',
			'PowerSteering' => 'POWER_STEERING',
			'ClimateControl' => 'CLIMATE_CONTROL',
			'ClimateControlOptionsOption' => 'CLIMATE_CONTROL_OPTIONS',
			'Interior' => 'INTERIOR',
			'InteriorOptionsOption' => 'INTERIOR_OPTIONS',
			'HeatingOption' => 'HEATING',
			'PowerWindows' => 'POWER_WINDOWS',
			'ElectricDriveOption' => 'ELECTRIC_DRIVE',
			'MemorySettingsOption' => 'MEMORY_SETTINGS',
			'DrivingAssistanceOption' => 'DRIVING_ASSISTANCE',
			'AntitheftSystemOption' => 'ANTITHEFT_SYSTEM',
			'AirbagsOption' => 'AIRBAGS',
			'ActiveSafetyOption' => 'ACTIVE_SAFETY',
			'MultimediaOption' => 'MULTIMEDIA',
			'AudioSystem' => 'AUDIO_SYSTEM',
			'AudioSystemOptionsOption' => 'AUDIO_SYSTEM_OPTIONS',
			'Lights' => 'LIGHTS',
			'LightsOptionsOption' => 'LIGHTS_OPTIONS',
			'Wheels' => 'WHEELS',
			'WheelsOptionsOption' => 'WHEELS_OPTIONS',
			'MaintenanceOption' => 'MAINTENANCE',
		);
		return $arResult;
	}
	
	/**
	 *	
	 */
	public function getMultipleFields(){
		return array('PICTURE', 'CLIMATE_CONTROL_OPTIONS', 'INTERIOR_OPTIONS', 'HEATING', 'ELECTRIC_DRIVE',
			'MEMORY_SETTINGS', 'DRIVING_ASSISTANCE', 'ANTITHEFT_SYSTEM', 'AIRBAGS', 'ACTIVE_SAFETY', 'MULTIMEDIA',
			'AUDIO_SYSTEM_OPTIONS', 'LIGHTS_OPTIONS', 'WHEELS_OPTIONS', 'MAINTENANCE');
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