<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class Google extends Base {
	
	const PLUGIN = 'GOOGLE_MERCHANT';
	const FORMAT = 'GOOGLE_MERCHANT_GENERAL';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'google';
	
	/**
	 *
	 */
	public function _getFieldsMap(){
		$arResult = array(
			'id' => 'ID',
			'title' => 'TITLE',
			'description' => 'DESCRIPTION',
			'link' => 'LINK',
			'image_link' => 'IMAGE_LINK',
			'additional_image_link' => 'ADDITIONAL_IMAGE_LINK',
			'mobile_link' => 'MOBILE_LINK',
			'availability' => 'AVAILABILITY',
			'availability_date' => 'AVAILABILITY_DATE',
			'expiration_date' => 'EXPIRATION_DATE',
			'price' => 'PRICE',
			'price_currency' => '_CURRENCY',
			'sale_price' => 'SALE_PRICE',
			'sale_price_currency' => '_CURRENCY',
			'sale_price_effective_date' => 'SALE_PRICE_EFFECTIVE_DATE',
			'unit_pricing_measure' => 'UNIT_PRICING_MEASURE',
			'unit_pricing_base_measure' => 'UNIT_PRICING_BASE_MEASURE',
			'google_product_category' => 'GOOGLE_PRODUCT_CATEGORY',
			'product_type' => 'PRODUCT_TYPE',
			'brand' => 'BRAND',
			'gtin' => 'GTIN',
			'mpn' => 'MPN',
			'identifier_exists' => 'IDENTIFIER_EXISTS',
			'condition' => 'CONDITION',
			'adult' => 'ADULT',
			'multipack' => 'MULTIPACK',
			'is_bundle' => 'IS_BUNDLE',
			'energy_efficiency_class' => 'ENERGY_EFFICIENCY_CLASS',
			'age_group' => 'AGE_GROUP',
			'color' => 'COLOR',
			'gender' => 'GENDER',
			'material' => 'MATERIAL',
			'pattern' => 'PATTERN',
			'size' => 'SIZE',
			'size_type' => 'SIZE_TYPE',
			'size_system' => 'SIZE_SYSTEM',
			'item_group_id' => 'ITEM_GROUP_ID',
			'excluded_destination' => 'EXCLUDED_DESTINATION',
			'custom_label_0' => 'CUSTOM_LABEL_1',
			'custom_label_1' => 'CUSTOM_LABEL_2',
			'custom_label_2' => 'CUSTOM_LABEL_3',
			'custom_label_3' => 'CUSTOM_LABEL_4',
			'custom_label_4' => 'CUSTOM_LABEL_5',
			'promotion_id' => 'PROMOTION_ID',
			'shipping_country' => 'SHIPPING_COUNTRY',
			'shipping_service' => 'SHIPPING_SERVICE',
			'shipping_price' => 'SHIPPING_PRICE',
			'shipping_label' => 'SHIPPING_LABEL',
			'shipping_weight' => 'SHIPPING_WEIGHT',
			'shipping_length' => 'SHIPPING_LENGTH',
			'shipping_width' => 'SHIPPING_WIDTH',
			'shipping_height' => 'SHIPPING_HEIGHT',
			'min_handling_time' => 'MIN_HANDLING_TIME',
			'max_handling_time' => 'MAX_HANDLING_TIME',
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
		$arParams['SHOP_COMPANY'] = $this->arOldProfile['COMPANY'];
		$arParams['DELIVERY'] = array('COST' => '', 'DAYS' => '', 'ORDER_BEFORE' => '');
		$arParams['ENCODING'] = Helper::isUtf() ? 'UTF-8' : 'windows-1251';
		$arParams['COMPRESS_TO_ZIP'] = $this->arOldProfile['USE_COMPRESS'] == 'Y' ? 'Y' : 'N';
		$arParams['DELETE_XML_IF_ZIP'] = 'N';
		$arParams['ENABLE_AUTO_DISCOUNTS'] = 'N';
		$arParams['COMPRESS_TO_ZIP'] = $this->arOldProfile['USE_COMPRESS'] == 'Y' ? 'Y' : 'N';
	}
	
}

?>