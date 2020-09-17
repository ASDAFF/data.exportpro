<?
/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/realty/
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoRealty extends Avito {
	
	CONST DATE_UPDATED = '2020-05-19';
	
	CONST CATEGORIES_PARSE_NODE = ''; // ???

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
		return parent::getCode().'_REALTY';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'avito_realty.xml';
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$this->modifyField($arResult, 'PRICE', array(
			'REQUIRED' => true,
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'STREET',
			'DISPLAY_CODE' => 'Street',
			'NAME' => static::getMessage('FIELD_STREET_NAME'),
			'SORT' => 550,
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
		$arResult[] = new Field(array(
			'CODE' => 'DISTANCE_TO_CITY',
			'DISPLAY_CODE' => 'DistanceToCity',
			'NAME' => static::getMessage('FIELD_DISTANCE_TO_CITY_NAME'),
			'SORT' => 580,
			'DESCRIPTION' => static::getMessage('FIELD_DISTANCE_TO_CITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DIRECTION_ROAD',
			'DISPLAY_CODE' => 'DirectionRoad',
			'NAME' => static::getMessage('FIELD_DIRECTION_ROAD_NAME'),
			'SORT' => 590,
			'DESCRIPTION' => static::getMessage('FIELD_DIRECTION_ROAD_DESC'),
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'OPERATION_TYPE',
			'DISPLAY_CODE' => 'OperationType',
			'NAME' => static::getMessage('FIELD_OPERATION_TYPE_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_OPERATION_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'COUNTRY',
			'DISPLAY_CODE' => 'Country',
			'NAME' => static::getMessage('FIELD_COUNTRY_NAME'),
			'SORT' => 1010,
			'DESCRIPTION' => static::getMessage('FIELD_COUNTRY_DESC'),
		));
		$this->modifyField($arResult, 'TITLE', array(
			'SORT' => 1020,
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE_TYPE',
			'DISPLAY_CODE' => 'PriceType',
			'NAME' => static::getMessage('FIELD_PRICE_TYPE_NAME'),
			'SORT' => 1030,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ROOMS',
			'DISPLAY_CODE' => 'Rooms',
			'NAME' => static::getMessage('FIELD_ROOMS_NAME'),
			'SORT' => 1040,
			'DESCRIPTION' => static::getMessage('FIELD_ROOMS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SQUARE',
			'DISPLAY_CODE' => 'Square',
			'NAME' => static::getMessage('FIELD_SQUARE_NAME'),
			'SORT' => 1050,
			'DESCRIPTION' => static::getMessage('FIELD_SQUARE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'KITCHEN_SPACE',
			'DISPLAY_CODE' => 'KitchenSpace',
			'NAME' => static::getMessage('FIELD_KITCHEN_SPACE_NAME'),
			'SORT' => 1060,
			'DESCRIPTION' => static::getMessage('FIELD_KITCHEN_SPACE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LIVING_SPACE',
			'DISPLAY_CODE' => 'LivingSpace',
			'NAME' => static::getMessage('FIELD_LIVING_SPACE_NAME'),
			'SORT' => 1070,
			'DESCRIPTION' => static::getMessage('FIELD_LIVING_SPACE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LAND_AREA',
			'DISPLAY_CODE' => 'LandArea',
			'NAME' => static::getMessage('FIELD_LAND_AREA_NAME'),
			'SORT' => 1080,
			'DESCRIPTION' => static::getMessage('FIELD_LAND_AREA_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'FLOOR',
			'DISPLAY_CODE' => 'Floor',
			'NAME' => static::getMessage('FIELD_FLOOR_NAME'),
			'SORT' => 1090,
			'DESCRIPTION' => static::getMessage('FIELD_FLOOR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'FLOORS',
			'DISPLAY_CODE' => 'Floors',
			'NAME' => static::getMessage('FIELD_FLOORS_NAME'),
			'SORT' => 1100,
			'DESCRIPTION' => static::getMessage('FIELD_FLOORS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'HOUSE_TYPE',
			'DISPLAY_CODE' => 'HouseType',
			'NAME' => static::getMessage('FIELD_HOUSE_TYPE_NAME'),
			'SORT' => 1110,
			'DESCRIPTION' => static::getMessage('FIELD_HOUSE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WALLS_TYPE',
			'DISPLAY_CODE' => 'WallsType',
			'NAME' => static::getMessage('FIELD_WALLS_TYPE_NAME'),
			'SORT' => 1120,
			'DESCRIPTION' => static::getMessage('FIELD_WALLS_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MARKET_TYPE',
			'DISPLAY_CODE' => 'MarketType',
			'NAME' => static::getMessage('FIELD_MARKET_TYPE_NAME'),
			'SORT' => 1130,
			'DESCRIPTION' => static::getMessage('FIELD_MARKET_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'NEW_DEVELOPMENT_ID',
			'DISPLAY_CODE' => 'NewDevelopmentId',
			'NAME' => static::getMessage('FIELD_NEW_DEVELOPMENT_ID_NAME'),
			'SORT' => 1140,
			'DESCRIPTION' => static::getMessage('FIELD_NEW_DEVELOPMENT_ID_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PROPERTY_RIGHTS',
			'DISPLAY_CODE' => 'PropertyRights',
			'NAME' => static::getMessage('FIELD_PROPERTY_RIGHTS_NAME'),
			'SORT' => 1150,
			'DESCRIPTION' => static::getMessage('FIELD_PROPERTY_RIGHTS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OBJECT_TYPE',
			'DISPLAY_CODE' => 'ObjectType',
			'NAME' => static::getMessage('FIELD_OBJECT_TYPE_NAME'),
			'SORT' => 1160,
			'DESCRIPTION' => static::getMessage('FIELD_OBJECT_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OBJECT_SUBTYPE',
			'DISPLAY_CODE' => 'ObjectSubtype',
			'NAME' => static::getMessage('FIELD_OBJECT_SUBTYPE_NAME'),
			'SORT' => 1170,
			'DESCRIPTION' => static::getMessage('FIELD_OBJECT_SUBTYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SECURED',
			'DISPLAY_CODE' => 'Secured',
			'NAME' => static::getMessage('FIELD_SECURED_NAME'),
			'SORT' => 1180,
			'DESCRIPTION' => static::getMessage('FIELD_SECURED_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BUILDING_CLASS',
			'DISPLAY_CODE' => 'BuildingClass',
			'NAME' => static::getMessage('FIELD_BUILDING_CLASS_NAME'),
			'SORT' => 1190,
			'DESCRIPTION' => static::getMessage('FIELD_BUILDING_CLASS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CADASTRAL_NUMBER',
			'DISPLAY_CODE' => 'CadastralNumber',
			'NAME' => static::getMessage('FIELD_CADASTRAL_NUMBER_NAME'),
			'SORT' => 1200,
			'DESCRIPTION' => static::getMessage('FIELD_CADASTRAL_NUMBER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DECORATION',
			'DISPLAY_CODE' => 'Decoration',
			'NAME' => static::getMessage('FIELD_DECORATION_NAME'),
			'SORT' => 1204,
			'DESCRIPTION' => static::getMessage('FIELD_DECORATION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SAFE_DEMONSTRATION',
			'DISPLAY_CODE' => 'SafeDemonstration',
			'NAME' => static::getMessage('FIELD_SAFE_DEMONSTRATION_NAME'),
			'SORT' => 1205,
			'DESCRIPTION' => static::getMessage('FIELD_SAFE_DEMONSTRATION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'APARTMENT_NUMBER',
			'DISPLAY_CODE' => 'ApartmentNumber',
			'NAME' => static::getMessage('FIELD_APARTMENT_NUMBER_NAME'),
			'SORT' => 1206,
			'DESCRIPTION' => static::getMessage('FIELD_APARTMENT_NUMBER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'STATUS',
			'DISPLAY_CODE' => 'Status',
			'NAME' => static::getMessage('FIELD_STATUS_NAME'),
			'SORT' => 1207,
			'DESCRIPTION' => static::getMessage('FIELD_STATUS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BALCONY_OR_LOGGIA',
			'DISPLAY_CODE' => 'BalconyOrLoggia',
			'NAME' => static::getMessage('FIELD_BALCONY_OR_LOGGIA_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_BALCONY_OR_LOGGIA_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'VIEW_FROM_WINDOWS',
			'DISPLAY_CODE' => 'ViewFromWindows',
			'NAME' => static::getMessage('FIELD_VIEW_FROM_WINDOWS_NAME'),
			'SORT' => 1208,
			'DESCRIPTION' => static::getMessage('FIELD_VIEW_FROM_WINDOWS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 1214,
				'NAME' => static::getMessage('HEADER_LEASE'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_TYPE',
			'DISPLAY_CODE' => 'LeaseType',
			'NAME' => static::getMessage('FIELD_LEASE_TYPE_NAME'),
			'SORT' => 1215,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_BEDS',
			'DISPLAY_CODE' => 'LeaseBeds',
			'NAME' => static::getMessage('FIELD_LEASE_BEDS_NAME'),
			'SORT' => 1220,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_BEDS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_SLEEPING_PLACES',
			'DISPLAY_CODE' => 'LeaseSleepingPlaces',
			'NAME' => static::getMessage('FIELD_LEASE_SLEEPING_PLACES_NAME'),
			'SORT' => 1230,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_SLEEPING_PLACES_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_MULTIMEDIA',
			'DISPLAY_CODE' => 'LeaseMultimedia',
			'NAME' => static::getMessage('FIELD_LEASE_MULTIMEDIA_NAME'),
			'SORT' => 1240,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_MULTIMEDIA_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_APPLIANCES',
			'DISPLAY_CODE' => 'LeaseAppliances',
			'NAME' => static::getMessage('FIELD_LEASE_APPLIANCES_NAME'),
			'SORT' => 1250,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_APPLIANCES_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_COMFORT',
			'DISPLAY_CODE' => 'LeaseComfort',
			'NAME' => static::getMessage('FIELD_LEASE_COMFORT_NAME'),
			'SORT' => 1260,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_COMFORT_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_ADDITIONALLY',
			'DISPLAY_CODE' => 'LeaseAdditionally',
			'NAME' => static::getMessage('FIELD_LEASE_ADDITIONALLY_NAME'),
			'SORT' => 1270,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_ADDITIONALLY_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_COMMISSION_SIZE',
			'DISPLAY_CODE' => 'LeaseCommissionSize',
			'NAME' => static::getMessage('FIELD_LEASE_COMMISSION_SIZE_NAME'),
			'SORT' => 1280,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_COMMISSION_SIZE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LEASE_DEPOSIT',
			'DISPLAY_CODE' => 'LeaseDeposit',
			'NAME' => static::getMessage('FIELD_LEASE_DEPOSIT_NAME'),
			'SORT' => 1290,
			'DESCRIPTION' => static::getMessage('FIELD_LEASE_DEPOSIT_DESC'),
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
		if(!Helper::isEmpty($arFields['TITLE']))
			$arXmlTags['Title'] = Xml::addTag($arFields['TITLE']);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['Price'] = Xml::addTag($arFields['PRICE']);
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
		if(!Helper::isEmpty($arFields['DISTANCE_TO_CITY']))
			$arXmlTags['DistanceToCity'] = Xml::addTag($arFields['DISTANCE_TO_CITY']);
		if(!Helper::isEmpty($arFields['DIRECTION_ROAD']))
			$arXmlTags['DirectionRoad'] = Xml::addTag($arFields['DIRECTION_ROAD']);
		#
		if(!Helper::isEmpty($arFields['CATEGORY']))
			$arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
		if(!Helper::isEmpty($arFields['OPERATION_TYPE']))
			$arXmlTags['OperationType'] = Xml::addTag($arFields['OPERATION_TYPE']);
		if(!Helper::isEmpty($arFields['COUNTRY']))
			$arXmlTags['Country'] = Xml::addTag($arFields['COUNTRY']);
		if(!Helper::isEmpty($arFields['PRICE_TYPE']))
			$arXmlTags['PriceType'] = Xml::addTag($arFields['PRICE_TYPE']);
		if(!Helper::isEmpty($arFields['ROOMS']))
			$arXmlTags['Rooms'] = Xml::addTag($arFields['ROOMS']);
		if(!Helper::isEmpty($arFields['SQUARE']))
			$arXmlTags['Square'] = Xml::addTag($arFields['SQUARE']);
		if(!Helper::isEmpty($arFields['KITCHEN_SPACE']))
			$arXmlTags['KitchenSpace'] = Xml::addTag($arFields['KITCHEN_SPACE']);
		if(!Helper::isEmpty($arFields['LIVING_SPACE']))
			$arXmlTags['LivingSpace'] = Xml::addTag($arFields['LIVING_SPACE']);
		if(!Helper::isEmpty($arFields['LAND_AREA']))
			$arXmlTags['LandArea'] = Xml::addTag($arFields['LAND_AREA']);
		if(!Helper::isEmpty($arFields['FLOOR']))
			$arXmlTags['Floor'] = Xml::addTag($arFields['FLOOR']);
		if(!Helper::isEmpty($arFields['FLOORS']))
			$arXmlTags['Floors'] = Xml::addTag($arFields['FLOORS']);
		if(!Helper::isEmpty($arFields['HOUSE_TYPE']))
			$arXmlTags['HouseType'] = Xml::addTag($arFields['HOUSE_TYPE']);
		if(!Helper::isEmpty($arFields['WALLS_TYPE']))
			$arXmlTags['WallsType'] = Xml::addTag($arFields['WALLS_TYPE']);
		if(!Helper::isEmpty($arFields['MARKET_TYPE']))
			$arXmlTags['MarketType'] = Xml::addTag($arFields['MARKET_TYPE']);
		if(!Helper::isEmpty($arFields['NEW_DEVELOPMENT_ID']))
			$arXmlTags['NewDevelopmentId'] = Xml::addTag($arFields['NEW_DEVELOPMENT_ID']);
		if(!Helper::isEmpty($arFields['PROPERTY_RIGHTS']))
			$arXmlTags['PropertyRights'] = Xml::addTag($arFields['PROPERTY_RIGHTS']);
		if(!Helper::isEmpty($arFields['OBJECT_TYPE']))
			$arXmlTags['ObjectType'] = Xml::addTag($arFields['OBJECT_TYPE']);
		if(!Helper::isEmpty($arFields['OBJECT_SUBTYPE']))
			$arXmlTags['ObjectSubtype'] = Xml::addTag($arFields['OBJECT_SUBTYPE']);
		if(!Helper::isEmpty($arFields['SECURED']))
			$arXmlTags['Secured'] = Xml::addTag($arFields['SECURED']);
		if(!Helper::isEmpty($arFields['BUILDING_CLASS']))
			$arXmlTags['BuildingClass'] = Xml::addTag($arFields['BUILDING_CLASS']);
		if(!Helper::isEmpty($arFields['CADASTRAL_NUMBER']))
			$arXmlTags['CadastralNumber'] = Xml::addTag($arFields['CADASTRAL_NUMBER']);
		if(!Helper::isEmpty($arFields['DECORATION']))
			$arXmlTags['Decoration'] = Xml::addTag($arFields['DECORATION']);
		if(!Helper::isEmpty($arFields['SAFE_DEMONSTRATION']))
			$arXmlTags['SafeDemonstration'] = Xml::addTag($arFields['SAFE_DEMONSTRATION']);
		if(!Helper::isEmpty($arFields['APARTMENT_NUMBER']))
			$arXmlTags['ApartmentNumber'] = Xml::addTag($arFields['APARTMENT_NUMBER']);
		if(!Helper::isEmpty($arFields['STATUS']))
			$arXmlTags['Status'] = Xml::addTag($arFields['STATUS']);
		if(!Helper::isEmpty($arFields['BALCONY_OR_LOGGIA']))
			$arXmlTags['BalconyOrLoggia'] = Xml::addTag($arFields['BALCONY_OR_LOGGIA']);
		if(!Helper::isEmpty($arFields['VIEW_FROM_WINDOWS']))
			$arXmlTags['ViewFromWindows'] = Xml::addTagWithSubtags($arFields['VIEW_FROM_WINDOWS'], 'option');
		#
		if(!Helper::isEmpty($arFields['LEASE_TYPE']))
			$arXmlTags['LeaseType'] = Xml::addTag($arFields['LEASE_TYPE']);
		if(!Helper::isEmpty($arFields['LEASE_BEDS']))
			$arXmlTags['LeaseBeds'] = Xml::addTag($arFields['LEASE_BEDS']);
		if(!Helper::isEmpty($arFields['LEASE_SLEEPING_PLACES']))
			$arXmlTags['LeaseSleepingPlaces'] = Xml::addTag($arFields['LEASE_SLEEPING_PLACES']);
		if(!Helper::isEmpty($arFields['LEASE_MULTIMEDIA']))
			$arXmlTags['LeaseMultimedia'] = Xml::addTagWithSubtags($arFields['LEASE_MULTIMEDIA'], 'option');
		if(!Helper::isEmpty($arFields['LEASE_APPLIANCES']))
			$arXmlTags['LeaseAppliances'] = Xml::addTagWithSubtags($arFields['LEASE_APPLIANCES'], 'option');
		if(!Helper::isEmpty($arFields['LEASE_COMFORT']))
			$arXmlTags['LeaseComfort'] = Xml::addTagWithSubtags($arFields['LEASE_COMFORT'], 'option');
		if(!Helper::isEmpty($arFields['LEASE_ADDITIONALLY']))
			$arXmlTags['LeaseAdditionally'] = Xml::addTagWithSubtags($arFields['LEASE_ADDITIONALLY'], 'option');
		if(!Helper::isEmpty($arFields['LEASE_COMMISSIONSIZE']))
			$arXmlTags['LeaseCommissionSize'] = Xml::addTag($arFields['LEASE_COMMISSIONSIZE']);
		if(!Helper::isEmpty($arFields['LEASE_DEPOSIT']))
			$arXmlTags['LeaseDeposit'] = Xml::addTag($arFields['LEASE_DEPOSIT']);
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