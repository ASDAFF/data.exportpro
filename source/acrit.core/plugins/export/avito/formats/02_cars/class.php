<?
/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/cars
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoCars extends Avito {
	
	CONST DATE_UPDATED = '2019-09-03';

	protected static $bSubclass = true;
	
	/**
	 * Base constructor
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}
	
	/* START OF BASE STATIC METHODS */
	
	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return parent::getCode().'_CARS';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'avito_cars.xml';
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$this->removeFields($arResult, array('TITLE'));
		#
		$this->modifyField($arResult, 'PRICE', array(
			'REQUIRED' => true,
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'STREET',
			'DISPLAY_CODE' => 'Street',
			'NAME' => static::getMessage('FIELD_STREET_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_STREET_DESC'),
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_STREET',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => '256',
			),
		));
		#
		$this->modifyField($arResult, 'CATEGORY', array(
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('FIELD_CATEGORY_DEFAULT'),
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CAR_TYPE',
			'DISPLAY_CODE' => 'CarType',
			'NAME' => static::getMessage('FIELD_CAR_TYPE_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_CAR_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAKE',
			'DISPLAY_CODE' => 'Make',
			'NAME' => static::getMessage('FIELD_MAKE_NAME'),
			'SORT' => 1020,
			'DESCRIPTION' => static::getMessage('FIELD_MAKE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODEL',
			'DISPLAY_CODE' => 'Model',
			'NAME' => static::getMessage('FIELD_MODEL_NAME'),
			'SORT' => 1030,
			'DESCRIPTION' => static::getMessage('FIELD_MODEL_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'YEAR',
			'DISPLAY_CODE' => 'Year',
			'NAME' => static::getMessage('FIELD_YEAR_NAME'),
			'SORT' => 1040,
			'DESCRIPTION' => static::getMessage('FIELD_YEAR_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'KILOMETRAGE',
			'DISPLAY_CODE' => 'Kilometrage',
			'NAME' => static::getMessage('FIELD_KILOMETRAGE_NAME'),
			'SORT' => 1050,
			'DESCRIPTION' => static::getMessage('FIELD_KILOMETRAGE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ACCIDENT',
			'DISPLAY_CODE' => 'Accident',
			'NAME' => static::getMessage('FIELD_ACCIDENT_NAME'),
			'SORT' => 1060,
			'DESCRIPTION' => static::getMessage('FIELD_ACCIDENT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'VIN',
			'DISPLAY_CODE' => 'VIN',
			'NAME' => static::getMessage('FIELD_VIN_NAME'),
			'SORT' => 1070,
			'DESCRIPTION' => static::getMessage('FIELD_VIN_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CERTIFICATION_NUMBER',
			'DISPLAY_CODE' => 'CertificationNumber',
			'NAME' => static::getMessage('FIELD_CERTIFICATION_NUMBER_NAME'),
			'SORT' => 1080,
			'DESCRIPTION' => static::getMessage('FIELD_CERTIFICATION_NUMBER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BODY_TYPE',
			'DISPLAY_CODE' => 'BodyType',
			'NAME' => static::getMessage('FIELD_BODY_TYPE_NAME'),
			'SORT' => 1090,
			'DESCRIPTION' => static::getMessage('FIELD_BODY_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DOORS',
			'DISPLAY_CODE' => 'Doors',
			'NAME' => static::getMessage('FIELD_DOORS_NAME'),
			'SORT' => 1100,
			'DESCRIPTION' => static::getMessage('FIELD_DOORS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'GENERATION_ID',
			'DISPLAY_CODE' => 'GenerationId',
			'NAME' => static::getMessage('FIELD_GENERATION_ID_NAME'),
			'SORT' => 1104,
			'DESCRIPTION' => static::getMessage('FIELD_GENERATION_ID_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODIFICATION_ID',
			'DISPLAY_CODE' => 'ModificationId',
			'NAME' => static::getMessage('FIELD_MODIFICATION_ID_NAME'),
			'SORT' => 1106,
			'DESCRIPTION' => static::getMessage('FIELD_MODIFICATION_ID_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COMPLECTATION_ID',
			'DISPLAY_CODE' => 'ComplectationId',
			'NAME' => static::getMessage('FIELD_COMPLECTATION_ID_NAME'),
			'SORT' => 1108,
			'DESCRIPTION' => static::getMessage('FIELD_COMPLECTATION_ID_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COLOR',
			'DISPLAY_CODE' => 'Color',
			'NAME' => static::getMessage('FIELD_COLOR_NAME'),
			'SORT' => 1110,
			'DESCRIPTION' => static::getMessage('FIELD_COLOR_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'FUEL_TYPE',
			'DISPLAY_CODE' => 'FuelType',
			'NAME' => static::getMessage('FIELD_FUEL_TYPE_NAME'),
			'SORT' => 1120,
			'DESCRIPTION' => static::getMessage('FIELD_FUEL_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENGINE_SIZE',
			'DISPLAY_CODE' => 'EngineSize',
			'NAME' => static::getMessage('FIELD_ENGINE_SIZE_NAME'),
			'SORT' => 1130,
			'DESCRIPTION' => static::getMessage('FIELD_ENGINE_SIZE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'POWER',
			'DISPLAY_CODE' => 'Power',
			'NAME' => static::getMessage('FIELD_POWER_NAME'),
			'SORT' => 1140,
			'DESCRIPTION' => static::getMessage('FIELD_POWER_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRANSMISSION',
			'DISPLAY_CODE' => 'Transmission',
			'NAME' => static::getMessage('FIELD_TRANSMISSION_NAME'),
			'SORT' => 1150,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSMISSION_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DRIVE_TYPE',
			'DISPLAY_CODE' => 'DriveType',
			'NAME' => static::getMessage('FIELD_DRIVE_TYPE_NAME'),
			'SORT' => 1160,
			'DESCRIPTION' => static::getMessage('FIELD_DRIVE_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'WHEEL_TYPE',
			'DISPLAY_CODE' => 'WheelType',
			'NAME' => static::getMessage('FIELD_WHEEL_TYPE_NAME'),
			'SORT' => 1170,
			'DESCRIPTION' => static::getMessage('FIELD_WHEEL_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'OWNERS',
			'DISPLAY_CODE' => 'Owners',
			'NAME' => static::getMessage('FIELD_OWNERS_NAME'),
			'SORT' => 1180,
			'DESCRIPTION' => static::getMessage('FIELD_OWNERS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AD_TYPE',
			'DISPLAY_CODE' => 'AdType',
			'NAME' => static::getMessage('FIELD_AD_TYPE_NAME'),
			'SORT' => 1184,
			'DESCRIPTION' => static::getMessage('FIELD_AD_TYPE_DESC'),
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('FIELD_AD_TYPE_DEFAULT'),
				),
			),
		));
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 1189,
				'NAME' => static::getMessage('HEADER_ADDITIONAL'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'POWER_STEERING',
			'DISPLAY_CODE' => 'PowerSteering',
			'NAME' => static::getMessage('FIELD_POWER_STEERING_NAME'),
			'SORT' => 1190,
			'DESCRIPTION' => static::getMessage('FIELD_POWER_STEERING_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CLIMATE_CONTROL',
			'DISPLAY_CODE' => 'ClimateControl',
			'NAME' => static::getMessage('FIELD_CLIMATE_CONTROL_NAME'),
			'SORT' => 1200,
			'DESCRIPTION' => static::getMessage('FIELD_CLIMATE_CONTROL_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CLIMATE_CONTROL_OPTIONS',
			'DISPLAY_CODE' => 'ClimateControlOptions',
			'NAME' => static::getMessage('FIELD_CLIMATE_CONTROL_OPTIONS_NAME'),
			'SORT' => 1210,
			'DESCRIPTION' => static::getMessage('FIELD_CLIMATE_CONTROL_OPTIONS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'INTERIOR',
			'DISPLAY_CODE' => 'Interior',
			'NAME' => static::getMessage('FIELD_INTERIOR_NAME'),
			'SORT' => 1220,
			'DESCRIPTION' => static::getMessage('FIELD_INTERIOR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'INTERIOR_OPTIONS',
			'DISPLAY_CODE' => 'InteriorOptions',
			'NAME' => static::getMessage('FIELD_INTERIOR_OPTIONS_NAME'),
			'SORT' => 1230,
			'DESCRIPTION' => static::getMessage('FIELD_INTERIOR_OPTIONS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'HEATING',
			'DISPLAY_CODE' => 'Heating',
			'NAME' => static::getMessage('FIELD_HEATING_NAME'),
			'SORT' => 1240,
			'DESCRIPTION' => static::getMessage('FIELD_HEATING_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'POWER_WINDOWS',
			'DISPLAY_CODE' => 'PowerWindows',
			'NAME' => static::getMessage('FIELD_POWER_WINDOWS_NAME'),
			'SORT' => 1250,
			'DESCRIPTION' => static::getMessage('FIELD_POWER_WINDOWS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ELECTRIC_DRIVE',
			'DISPLAY_CODE' => 'ElectricDrive',
			'NAME' => static::getMessage('FIELD_ELECTRIC_DRIVE_NAME'),
			'SORT' => 1260,
			'DESCRIPTION' => static::getMessage('FIELD_ELECTRIC_DRIVE_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MEMORY_SETTINGS',
			'DISPLAY_CODE' => 'MemorySettings',
			'NAME' => static::getMessage('FIELD_MEMORY_SETTINGS_NAME'),
			'SORT' => 1270,
			'DESCRIPTION' => static::getMessage('FIELD_MEMORY_SETTINGS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DRIVING_ASSISTANCE',
			'DISPLAY_CODE' => 'DrivingAssistance',
			'NAME' => static::getMessage('FIELD_DRIVING_ASSISTANCE_NAME'),
			'SORT' => 1280,
			'DESCRIPTION' => static::getMessage('FIELD_DRIVING_ASSISTANCE_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ANTITHEFT_SYSTEM',
			'DISPLAY_CODE' => 'AntitheftSystem',
			'NAME' => static::getMessage('FIELD_ANTITHEFT_SYSTEM_NAME'),
			'SORT' => 1209,
			'DESCRIPTION' => static::getMessage('FIELD_ANTITHEFT_SYSTEM_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AIRBAGS',
			'DISPLAY_CODE' => 'Airbags',
			'NAME' => static::getMessage('FIELD_AIRBAGS_NAME'),
			'SORT' => 1300,
			'DESCRIPTION' => static::getMessage('FIELD_AIRBAGS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ACTIVE_SAFETY',
			'DISPLAY_CODE' => 'ActiveSafety',
			'NAME' => static::getMessage('FIELD_ACTIVE_SAFETY_NAME'),
			'SORT' => 1310,
			'DESCRIPTION' => static::getMessage('FIELD_ACTIVE_SAFETY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MULTIMEDIA',
			'DISPLAY_CODE' => 'Multimedia',
			'NAME' => static::getMessage('FIELD_MULTIMEDIA_NAME'),
			'SORT' => 1320,
			'DESCRIPTION' => static::getMessage('FIELD_MULTIMEDIA_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AUDIO_SYSTEM',
			'DISPLAY_CODE' => 'AudioSystem',
			'NAME' => static::getMessage('FIELD_AUDIO_SYSTEM_NAME'),
			'SORT' => 1330,
			'DESCRIPTION' => static::getMessage('FIELD_AUDIO_SYSTEM_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AUDIO_SYSTEM_OPTIONS',
			'DISPLAY_CODE' => 'AudioSystemOptions',
			'NAME' => static::getMessage('FIELD_AUDIO_SYSTEM_OPTIONS_NAME'),
			'SORT' => 1340,
			'DESCRIPTION' => static::getMessage('FIELD_AUDIO_SYSTEM_OPTIONS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LIGHTS',
			'DISPLAY_CODE' => 'Lights',
			'NAME' => static::getMessage('FIELD_LIGHTS_NAME'),
			'SORT' => 1350,
			'DESCRIPTION' => static::getMessage('FIELD_LIGHTS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LIGHTS_OPTIONS',
			'DISPLAY_CODE' => 'LightsOptions',
			'NAME' => static::getMessage('FIELD_LIGHTS_OPTIONS_NAME'),
			'SORT' => 1360,
			'DESCRIPTION' => static::getMessage('FIELD_LIGHTS_OPTIONS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WHEELS',
			'DISPLAY_CODE' => 'Wheels',
			'NAME' => static::getMessage('FIELD_WHEELS_NAME'),
			'SORT' => 1370,
			'DESCRIPTION' => static::getMessage('FIELD_WHEELS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WHEELS_OPTIONS',
			'DISPLAY_CODE' => 'WheelsOptions',
			'NAME' => static::getMessage('FIELD_WHEELS_OPTIONS_NAME'),
			'SORT' => 1380,
			'DESCRIPTION' => static::getMessage('FIELD_WHEELS_OPTIONS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAINTENANCE',
			'DISPLAY_CODE' => 'Maintenance',
			'NAME' => static::getMessage('FIELD_MAINTENANCE_NAME'),
			'SORT' => 1390,
			'DESCRIPTION' => static::getMessage('FIELD_MAINTENANCE_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'TRADEIN_DISCOUNT',
			'DISPLAY_CODE' => 'TradeinDiscount',
			'NAME' => static::getMessage('FIELD_TRADEIN_DISCOUNT_NAME'),
			'SORT' => 360,
			'DESCRIPTION' => static::getMessage('FIELD_TRADEIN_DISCOUNT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CREDIT_DISCOUNT',
			'DISPLAY_CODE' => 'CreditDiscount',
			'NAME' => static::getMessage('FIELD_CREDIT_DISCOUNT_NAME'),
			'SORT' => 370,
			'DESCRIPTION' => static::getMessage('FIELD_CREDIT_DISCOUNT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'INSURANCE_DISCOUNT',
			'DISPLAY_CODE' => 'InsuranceDiscount',
			'NAME' => static::getMessage('FIELD_INSURANCE_DISCOUNT_NAME'),
			'SORT' => 380,
			'DESCRIPTION' => static::getMessage('FIELD_INSURANCE_DISCOUNT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAX_DISCOUNT',
			'DISPLAY_CODE' => 'MaxDiscount',
			'NAME' => static::getMessage('FIELD_MAX_DISCOUNT_NAME'),
			'SORT' => 390,
			'DESCRIPTION' => static::getMessage('FIELD_MAX_DISCOUNT_DESC'),
		));
		#
		$this->sortFields($arResult);
		return $arResult;
	}
	
	/**
	 *	Process single element (generate XML)
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		# Build XML
		$arXmlTags = array(
			'Id' => array('#' => $arFields['ID']),
		);
		if(!Helper::isEmpty($arFields['DATE_BEGIN']))
			$arXmlTags['DateBegin'] = Xml::addTag($arFields['DATE_BEGIN']);
		if(!Helper::isEmpty($arFields['DATE_END']))
			$arXmlTags['DateEnd'] = Xml::addTag($arFields['DATE_END']);
		if(!Helper::isEmpty($arFields['LISTING_FEE']))
			$arXmlTags['ListingFee'] = Xml::addTag($arFields['LISTING_FEE']);
		if(!Helper::isEmpty($arFields['AD_STATUS']))
			$arXmlTags['AdStatus'] = Xml::addTag($arFields['AD_STATUS']);
		if(!Helper::isEmpty($arFields['AVITO_ID']))
			$arXmlTags['AvitoId'] = Xml::addTag($arFields['AVITO_ID']);
		#
		if(!Helper::isEmpty($arFields['ALLOW_EMAIL']))
			$arXmlTags['AllowEmail'] = Xml::addTag($arFields['ALLOW_EMAIL']);
		if(!Helper::isEmpty($arFields['MANAGER_NAME']))
			$arXmlTags['ManagerName'] = Xml::addTag($arFields['MANAGER_NAME']);
		if(!Helper::isEmpty($arFields['CONTACT_PHONE']))
			$arXmlTags['ContactPhone'] = Xml::addTag($arFields['CONTACT_PHONE']);
		#
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['Description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['IMAGES']))
			$arXmlTags['Images'] = $this->getXmlTag_Images($arFields['IMAGES']);
		if(!Helper::isEmpty($arFields['VIDEO_URL']))
			$arXmlTags['VideoURL'] = Xml::addTag($arFields['VIDEO_URL']);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['Price'] = Xml::addTag($arFields['PRICE']);
		if(!Helper::isEmpty($arFields['TRADEIN_DISCOUNT']))
			$arXmlTags['TradeinDiscount'] = Xml::addTag($arFields['TRADEIN_DISCOUNT']);
		if(!Helper::isEmpty($arFields['CREDIT_DISCOUNT']))
			$arXmlTags['CreditDiscount'] = Xml::addTag($arFields['CREDIT_DISCOUNT']);
		if(!Helper::isEmpty($arFields['INSURANCE_DISCOUNT']))
			$arXmlTags['InsuranceDiscount'] = Xml::addTag($arFields['INSURANCE_DISCOUNT']);
		if(!Helper::isEmpty($arFields['MAX_DISCOUNT']))
			$arXmlTags['MaxDiscount'] = Xml::addTag($arFields['MAX_DISCOUNT']);
		#
		if(!Helper::isEmpty($arFields['ADDRESS']))
			$arXmlTags['Address'] = Xml::addTag($arFields['ADDRESS']);
		if(!Helper::isEmpty($arFields['REGION']))
			$arXmlTags['Region'] = Xml::addTag($arFields['REGION']);
		if(!Helper::isEmpty($arFields['CITY']))
			$arXmlTags['City'] = Xml::addTag($arFields['CITY']);
		if(!Helper::isEmpty($arFields['SUBWAY']))
			$arXmlTags['Subway'] = Xml::addTag($arFields['SUBWAY']);
		if(!Helper::isEmpty($arFields['DISTRICT']))
			$arXmlTags['District'] = Xml::addTag($arFields['DISTRICT']);
		if(!Helper::isEmpty($arFields['STREET']))
			$arXmlTags['Street'] = Xml::addTag($arFields['STREET']);
		if(!Helper::isEmpty($arFields['LATITUDE']))
			$arXmlTags['Latitude'] = Xml::addTag($arFields['LATITUDE']);
		if(!Helper::isEmpty($arFields['LONGITUDE']))
			$arXmlTags['Longitude'] = Xml::addTag($arFields['LONGITUDE']);
		#
		if(!Helper::isEmpty($arFields['CATEGORY']))
			$arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
		if(!Helper::isEmpty($arFields['CAR_TYPE']))
			$arXmlTags['CarType'] = Xml::addTag($arFields['CAR_TYPE']);
		if(!Helper::isEmpty($arFields['MAKE']))
			$arXmlTags['Make'] = Xml::addTag($arFields['MAKE']);
		if(!Helper::isEmpty($arFields['MODEL']))
			$arXmlTags['Model'] = Xml::addTag($arFields['MODEL']);
		if(!Helper::isEmpty($arFields['YEAR']))
			$arXmlTags['Year'] = Xml::addTag($arFields['YEAR']);
		if(!Helper::isEmpty($arFields['KILOMETRAGE']))
			$arXmlTags['Kilometrage'] = Xml::addTag($arFields['KILOMETRAGE']);
		if(!Helper::isEmpty($arFields['ACCIDENT']))
			$arXmlTags['Accident'] = Xml::addTag($arFields['ACCIDENT']);
		if(!Helper::isEmpty($arFields['VIN']))
			$arXmlTags['VIN'] = Xml::addTag($arFields['VIN']);
		if(!Helper::isEmpty($arFields['CERTIFICATION_NUMBER']))
			$arXmlTags['CertificationNumber'] = Xml::addTag($arFields['CERTIFICATION_NUMBER']);
		if(!Helper::isEmpty($arFields['BODY_TYPE']))
			$arXmlTags['BodyType'] = Xml::addTag($arFields['BODY_TYPE']);
		if(!Helper::isEmpty($arFields['DOORS']))
			$arXmlTags['Doors'] = Xml::addTag($arFields['DOORS']);
		if(!Helper::isEmpty($arFields['GENERATION_ID']))
			$arXmlTags['GenerationId'] = Xml::addTag($arFields['GENERATION_ID']);
		if(!Helper::isEmpty($arFields['MODIFICATION_ID']))
			$arXmlTags['ModificationId'] = Xml::addTag($arFields['MODIFICATION_ID']);
		if(!Helper::isEmpty($arFields['COMPLECTATION_ID']))
			$arXmlTags['ComplectationId'] = Xml::addTag($arFields['COMPLECTATION_ID']);
		if(!Helper::isEmpty($arFields['COLOR']))
			$arXmlTags['Color'] = Xml::addTag($arFields['COLOR']);
		if(!Helper::isEmpty($arFields['FUEL_TYPE']))
			$arXmlTags['FuelType'] = Xml::addTag($arFields['FUEL_TYPE']);
		if(!Helper::isEmpty($arFields['ENGINE_SIZE']))
			$arXmlTags['EngineSize'] = Xml::addTag($arFields['ENGINE_SIZE']);
		if(!Helper::isEmpty($arFields['POWER']))
			$arXmlTags['Power'] = Xml::addTag($arFields['POWER']);
		if(!Helper::isEmpty($arFields['TRANSMISSION']))
			$arXmlTags['Transmission'] = Xml::addTag($arFields['TRANSMISSION']);
		if(!Helper::isEmpty($arFields['DRIVE_TYPE']))
			$arXmlTags['DriveType'] = Xml::addTag($arFields['DRIVE_TYPE']);
		if(!Helper::isEmpty($arFields['WHEEL_TYPE']))
			$arXmlTags['WheelType'] = Xml::addTag($arFields['WHEEL_TYPE']);
		if(!Helper::isEmpty($arFields['OWNERS']))
			$arXmlTags['Owners'] = Xml::addTag($arFields['OWNERS']);
		if(!Helper::isEmpty($arFields['AD_TYPE']))
			$arXmlTags['AdType'] = Xml::addTag($arFields['AD_TYPE']);
		#
		if(!Helper::isEmpty($arFields['POWER_STEERING']))
			$arXmlTags['PowerSteering'] = Xml::addTag($arFields['POWER_STEERING']);
		if(!Helper::isEmpty($arFields['CLIMATE_CONTROL']))
			$arXmlTags['ClimateControl'] = Xml::addTag($arFields['CLIMATE_CONTROL']);
		if(!Helper::isEmpty($arFields['CLIMATE_CONTROL_OPTIONS']))
			$arXmlTags['ClimateControlOptions'] = Xml::addTagWithSubtags($arFields['CLIMATE_CONTROL_OPTIONS'], 'option');
		if(!Helper::isEmpty($arFields['INTERIOR']))
			$arXmlTags['Interior'] = Xml::addTag($arFields['INTERIOR']);
		if(!Helper::isEmpty($arFields['INTERIOR_OPTIONS']))
			$arXmlTags['InteriorOptions'] = Xml::addTagWithSubtags($arFields['INTERIOR_OPTIONS'], 'option');
		if(!Helper::isEmpty($arFields['HEATING']))
			$arXmlTags['Heating'] = Xml::addTagWithSubtags($arFields['HEATING'], 'option');
		if(!Helper::isEmpty($arFields['POWER_WINDOWS']))
			$arXmlTags['PowerWindows'] = Xml::addTag($arFields['POWER_WINDOWS']);
		if(!Helper::isEmpty($arFields['ELECTRIC_DRIVE']))
			$arXmlTags['ElectricDrive'] = Xml::addTagWithSubtags($arFields['ELECTRIC_DRIVE'], 'option');
		if(!Helper::isEmpty($arFields['MEMORY_SETTINGS']))
			$arXmlTags['MemorySettings'] = Xml::addTagWithSubtags($arFields['MEMORY_SETTINGS'], 'option');
		if(!Helper::isEmpty($arFields['DRIVING_ASSISTANCE']))
			$arXmlTags['DrivingAssistance'] = Xml::addTagWithSubtags($arFields['DRIVING_ASSISTANCE'], 'option');
		if(!Helper::isEmpty($arFields['ANTITHEFT_SYSTEM']))
			$arXmlTags['AntitheftSystem'] = Xml::addTagWithSubtags($arFields['ANTITHEFT_SYSTEM'], 'option');
		if(!Helper::isEmpty($arFields['AIRBAGS']))
			$arXmlTags['Airbags'] = Xml::addTagWithSubtags($arFields['AIRBAGS'], 'option');
		if(!Helper::isEmpty($arFields['ACTIVE_SAFETY']))
			$arXmlTags['ActiveSafety'] = Xml::addTagWithSubtags($arFields['ACTIVE_SAFETY'], 'option');
		if(!Helper::isEmpty($arFields['MULTIMEDIA']))
			$arXmlTags['Multimedia'] = Xml::addTagWithSubtags($arFields['MULTIMEDIA'], 'option');
		if(!Helper::isEmpty($arFields['AUDIO_SYSTEM']))
			$arXmlTags['AudioSystem'] = Xml::addTag($arFields['AUDIO_SYSTEM']);
		if(!Helper::isEmpty($arFields['AUDIO_SYSTEM_OPTIONS']))
			$arXmlTags['AudioSystemOptions'] = Xml::addTagWithSubtags($arFields['AUDIO_SYSTEM_OPTIONS'], 'option');
		if(!Helper::isEmpty($arFields['LIGHTS']))
			$arXmlTags['Lights'] = Xml::addTag($arFields['LIGHTS']);
		if(!Helper::isEmpty($arFields['LIGHTS_OPTIONS']))
			$arXmlTags['LightsOptions'] = Xml::addTagWithSubtags($arFields['LIGHTS_OPTIONS'], 'option');
		if(!Helper::isEmpty($arFields['WHEELS']))
			$arXmlTags['Wheels'] = Xml::addTag($arFields['WHEELS']);
		if(!Helper::isEmpty($arFields['WHEELS_OPTIONS']))
			$arXmlTags['WheelsOptions'] = Xml::addTagWithSubtags($arFields['WHEELS_OPTIONS'], 'option');
		if(!Helper::isEmpty($arFields['MAINTENANCE']))
			$arXmlTags['Maintenance'] = Xml::addTagWithSubtags($arFields['MAINTENANCE'], 'option');
		
		# build XML
		$arXml = array(
			'Ad' => array(
				'#' => $arXmlTags,
			),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		$strXml = Xml::arrayToXml($arXml);
		# build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => $strXml,
			'CURRENCY' => '',
			'SECTION_ID' => static::getElement_SectionID($intProfileID, $arElement),
			'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
			'DATA_MORE' => array(),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# after..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}
	
}

?>