<?
/**
 * Acrit Core: Yandex.Realty plugin
 * @documentation https://yandex.ru/support/realty/requirements/
 */

namespace Acrit\Core\Export\Plugins;

class YandexRealtyGeneral extends YandexRealty {
	
	const DATE_UPDATED = '2019-03-28';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'yandex_realty.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'USD', 'EUR'];
	
	# Basic settings
	protected $bCategoriesExport = true;
	protected $bCurrenciesExport = true;
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 1;
	protected $arXmlMultiply = ['room-space.value', 'location.metro.name'];
	
	# Other export settings
	protected $bZip = true;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		
		# General
		$arResult['HEADER_GENERAL'] = [];
		$arResult['@internal-id'] = ['FIELD' => 'ID'];
		$arResult['type'] = [];
		$arResult['property-type'] = ['CONST' => static::getMessage('property-type_default')];
		$arResult['category'] = [];
		$arResult['lot-number'] = [];
		$arResult['cadastral-number'] = [];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['creation-date'] = ['FIELD' => 'DATE_CREATE'];
		$arResult['last-update-date'] = ['FIELD' => 'TIMESTAMP_X'];
		$arResult['vas'] = [];
		
		# Location
		$arResult['HEADER_LOCATION'] = [];
		$arResult['location.country'] = ['CONST' => static::getMessage('location.country_default')];
		$arResult['location.region'] = [];
		$arResult['location.district'] = [];
		$arResult['location.locality-name'] = [];
		$arResult['location.sub-locality-name'] = [];
		$arResult['location.address'] = [];
		$arResult['location.apartment'] = [];
		$arResult['location.direction'] = [];
		$arResult['location.distance'] = [];
		$arResult['location.latitude'] = [];
		$arResult['location.longitude'] = [];
		$arResult['location.metro.name'] = ['MULTIPLE' => true];
		$arResult['location.metro.time-on-transport'] = [];
		$arResult['location.metro.time-on-foot'] = [];
		$arResult['location.railway-station'] = [];
		
		# Terms
		$arResult['HEADER_TERMS'] = [];
		$arResult['price.value'] = [];
		$arResult['price.currency'] = ['IS_CURRENCY' => true];
		$arResult['price.period'] = [];
		$arResult['price.unit'] = [];
		$arResult['rent-pledge'] = ['CONST' => static::getMessage('_boolean_default_n')];
		$arResult['deal-status'] = [];
		$arResult['haggle'] = [];
		$arResult['mortgage'] = [];
		$arResult['prepayment'] = [];
		$arResult['not-for-agents'] = [];
		$arResult['utilities-included'] = [];
		
		# Object general
		$arResult['HEADER_OBJECT_GENERAL'] = [];
		$arResult['building-type'] = [];
		$arResult['yandex-building-id'] = [];
		$arResult['yandex-house-id'] = [];
		$arResult['building-name'] = [];
		$arResult['built-year'] = [];
		$arResult['ready-quarter'] = [];
		$arResult['building-state'] = [];
		$arResult['building-phase'] = [];
		$arResult['building-series'] = [];
		$arResult['building-section'] = [];
		$arResult['is-elite'] = [];
		
		# Object info
		$arResult['HEADER_OBJECT_INFO'] = [];
		$arResult['area.value'] = [];
		$arResult['area.unit'] = ['CONST' => static::getMessage('area_unit_default')];
		$arResult['living-space.value'] = [];
		$arResult['living-space.unit'] = ['CONST' => static::getMessage('area_unit_default')];
		$arResult['kitchen-space.value'] = [];
		$arResult['kitchen-space.unit'] = ['CONST' => static::getMessage('area_unit_default')];
		$arResult['room-space.value'] = ['MULTIPLE' => true];
		$arResult['room-space.unit'] = ['CONST' => static::getMessage('area_unit_default'), 'MULTIPLE' => true];
		$arResult['image'] = [];
		$arResult['renovation'] = [];
		$arResult['quality'] = [];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT'];
		
		# Additional information
		$arResult['HEADER_OBJECT_ADDITIONAL'] = [];
		$arResult['rooms'] = [];
		$arResult['rooms-offered'] = [];
		$arResult['new-flat'] = [];
		$arResult['floor'] = [];
		$arResult['floors-total'] = [];
		$arResult['apartments'] = [];
		$arResult['studio'] = [];
		$arResult['open-plan'] = [];
		$arResult['rooms-type'] = [];
		$arResult['window-view'] = [];
		$arResult['balcony'] = [];
		$arResult['floor-covering'] = [];
		$arResult['bathroom-unit'] = [];
		$arResult['air-conditioner'] = [];
		$arResult['phone'] = [];
		$arResult['internet'] = [];
		$arResult['room-furniture'] = [];
		$arResult['kitchen-furniture'] = [];
		$arResult['television'] = [];
		$arResult['washing-machine'] = [];
		$arResult['dishwasher'] = [];
		$arResult['refrigerator'] = [];
		$arResult['built-in-tech'] = [];
		$arResult['with-children'] = [];
		$arResult['with-pets'] = [];
		$arResult['fire-alarm'] = [];
		$arResult['electricity-supply'] = [];
		$arResult['electric-capacity'] = [];
		$arResult['ceiling-height'] = [];
		$arResult['guarded-building'] = [];
		$arResult['pmg'] = [];
		$arResult['lift'] = [];
		$arResult['rubbish-chute'] = [];
		$arResult['water-supply'] = [];
		$arResult['gas-supply'] = [];
		$arResult['sewerage-supply'] = [];
		$arResult['heating-supply'] = [];
		$arResult['toilet'] = [];
		$arResult['shower'] = [];
		$arResult['pool'] = [];
		$arResult['billiard'] = [];
		$arResult['sauna'] = [];
		$arResult['parking'] = [];
		$arResult['parking-places'] = [];
		$arResult['parking-place-price'] = [];
		$arResult['parking-guest'] = [];
		$arResult['parking-guest-places'] = [];
		$arResult['alarm'] = [];
		$arResult['flat-alarm'] = [];
		$arResult['security'] = [];
		
		# Commercial
		$arResult['HEADER_COMMERCIAL'] = [];
		$arResult['entrance-type'] = [];
		$arResult['phone-lines'] = [];
		$arResult['adding-phone-on-request'] = [];
		$arResult['self-selection-telecom'] = [];
		$arResult['ventilation'] = [];
		$arResult['window-type'] = [];
		$arResult['eating-facilities'] = [];
		$arResult['office-class'] = [];
		$arResult['twenty-four-seven'] = [];
		
		# Warehouses 
		$arResult['HEADER_WAREHOUSES'] = [];
		$arResult['responsible-storage'] = [];
		$arResult['pallet-price'] = [];
		$arResult['freight-elevator'] = [];
		$arResult['truck-entrance'] = [];
		$arResult['ramp'] = [];
		$arResult['railway'] = [];
		$arResult['office-warehouse'] = [];
		$arResult['open-area'] = [];
		$arResult['service-three-pl'] = [];
		$arResult['temperature-comment'] = [];
		
		# Garage
		$arResult['HEADER_GARAGE'] = [];
		$arResult['garage-type'] = [];
		$arResult['ownership-type'] = [];
		$arResult['garage-name'] = [];
		$arResult['parking-type'] = [];
		$arResult['automatic-gates'] = [];
		$arResult['cctv'] = [];
		$arResult['access-control-system'] = [];
		$arResult['inspection-pit'] = [];
		$arResult['cellar'] = [];
		$arResult['car-wash'] = [];
		$arResult['auto-repair'] = [];
		$arResult['new-parking'] = [];
		
		# Seller
		$arResult['HEADER_SELLER'] = [];
		$arResult['sales-agent.name'] = ['CONST' => ''];
		$arResult['sales-agent.phone'] = ['CONST' => '', 	'MULTIPLE' => true];
		$arResult['sales-agent.category'] = ['CONST' => static::getMessage('sales-agent.category_default')];
		$arResult['sales-agent.organization'] = ['CONST' => ''];
		$arResult['sales-agent.url'] = ['CONST' => ''];
		$arResult['sales-agent.email'] = ['CONST' => ''];
		$arResult['sales-agent.photo'] = ['CONST' => ''];
		
		#
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<realty-feed xmlns="http://webmaster.yandex.ru/schemas/feed/realty/2010-06">'.static::EOL;
		$strXml .= '	<generation-date>#XML_GENERATION_DATE#</generation-date>'.static::EOL;
		$strXml .= '	#XML_ITEMS#'.static::EOL;
		$strXml .= '</realty-feed>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_GENERATION_DATE#' => date('c'),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}

}

?>