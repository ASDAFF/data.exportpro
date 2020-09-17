<?

/**
 * Acrit Core: GoodsRu plugin
 * @package acrit.core
 * @copyright 2019 Acrit
 */

namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper,
		\Acrit\Core\Xml,
		\Acrit\Core\HttpRequest,
		\Acrit\Core\Export\Field\Field,
		\PhpOffice\PhpSpreadsheet\Spreadsheet,
		\PhpOffice\PhpSpreadsheet\Writer\Xlsx,
		\PhpOffice\PhpSpreadsheet\IOFactory,
		\PhpOffice\PhpSpreadsheet\Cell\Coordinate;

Helper::loadMessages(__FILE__);

require_once realpath(__DIR__ . '/../../../yandex.market/class.php');
require_once realpath(__DIR__ . '/../../../yandex.market/formats/1_simple/class.php');

class GoodsRuSimple extends YandexMarketSimple
{

	CONST DATE_UPDATED = '2019-10-08';

	protected $bShopName = true;
	protected $bDelivery = true;
	protected $bEnableAutoDiscounts = false;
	protected $bPlatform = true;
	protected $bZip = false;
	protected $bPromoGift = false;
	protected $bPromoSpecialPrice = false;
	protected $bPromoCode = false;
	protected $bPromoNM = false;

	/**
	 * Base constructor
	 */
	public function __construct($strModuleId)
	{
		parent::__construct($strModuleId);
	}

	/* START OF BASE STATIC METHODS */

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode()
	{
		return 'GOODS_RU_SIMPLE';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName()
	{
		return static::getMessage('NAME');
	}

	/**
	 * 	Is it subclass?
	 */
	public static function isSubclass()
	{
		return true;
	}

	/* END OF BASE STATIC METHODS */

	public function getDefaultExportFilename()
	{
		return 'goods_ru_simple.xml';
	}

	/**
	 * 	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID)
	{
		return array();
	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false)
	{
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$arResult[] = new Field(array(
			'CODE' => 'SHIPMENT_OPTIONS_DAYS',
			'DISPLAY_CODE' => 'shipment_options_days',
			'NAME' => static::getMessage('FIELD_SHIPMENT_OPTIONS_DAYS_NAME'),
			'SORT' => 200,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPMENT_OPTIONS_DAYS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		)); 
		$arResult[] = new Field(array(
			'CODE' => 'SHIPMENT_OPTIONS_ORDER_BEFORE',
			'DISPLAY_CODE' => 'shipment_options_order_before',
			'NAME' => static::getMessage('FIELD_SHIPMENT_OPTIONS_ORDER_BEFORE_NAME'),
			'SORT' => 300,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPMENT_OPTIONS_ORDER_BEFORE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHIPMENT_OPTIONS_ID',
			'DISPLAY_CODE' => 'shipment_options_id',
			'NAME' => static::getMessage('FIELD_SHIPMENT_OPTIONS_ID_NAME'),
			'SORT' => 310,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPMENT_OPTIONS_ID_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OUTLETS_ID',
			'DISPLAY_CODE' => 'outlets_id',
			'NAME' => static::getMessage('FIELD_OUTLETS_ID_NAME'),
			'SORT' => 400,
			'DESCRIPTION' => static::getMessage('FIELD_OUTLETS_ID_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OUTLETS_INSTOCK',
			'DISPLAY_CODE' => 'outlets_instock',
			'NAME' => static::getMessage('FIELD_OUTLETS_INSTOCK_NAME'),
			'SORT' => 450,
			'DESCRIPTION' => static::getMessage('FIELD_OUTLETS_INSTOCK_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));

		$this->sortFields($arResult);
		return $arResult;
	}

	protected function onProcessElement(&$arProfile, &$intIBlockID, &$arElement, &$arFields, &$mData)
	{

		if (!Helper::isEmpty($arFields['SHIPMENT_OPTIONS_DAYS']) && !Helper::isEmpty($arFields['SHIPMENT_OPTIONS_ORDER_BEFORE']))
			$mData['offer']['#']['shipment-options'] = $this->getXmlTag_ShipmentOptions($intProfileID, $arFields);
		if (!Helper::isEmpty($arFields['OUTLETS_ID']) && !Helper::isEmpty($arFields['OUTLETS_INSTOCK']))
			$mData['offer']['#']['outlets'] = $this->getXmlTag_Outlets($intProfileID, $arFields);
	}

	protected function getXmlTag_ShipmentOptions($intProfileID, $arFields)
	{
		$m1 = $arFields['SHIPMENT_OPTIONS_DAYS'];
		$m2 = $arFields['SHIPMENT_OPTIONS_ORDER_BEFORE'];
		$m3 = $arFields['SHIPMENT_OPTIONS_ID'];

		#
		$m1 = is_array($m1) ? $m1 : (!Helper::isEmpty($m1) ? array($m1) : array());
		$m2 = is_array($m2) ? $m2 : (!Helper::isEmpty($m2) ? array($m2) : array());
		$m3 = is_array($m3) ? $m3 : (!Helper::isEmpty($m3) ? array($m3) : array());

		#
		$arTag = array();
		foreach ($m1 as $key => $value)
		{
			$arTag[] = array(
				'#' => [],
				'@' => array(
					'days' => $m1[$key],
					'order-before' => $m2[$key],
					'id' => $m3[$key],
				),
			);
		}
		if (!empty($arTag))
		{
			return array(
				array(
					'#' => ['option' => $arTag]
				),
			);
		}
		return '';
	}

	protected function getXmlTag_Outlets($intProfileID, $arFields)
	{
		$m1 = $arFields['OUTLETS_ID'];
		$m2 = $arFields['OUTLETS_INSTOCK'];

		#
		$m1 = is_array($m1) ? $m1 : (!Helper::isEmpty($m1) ? array($m1) : array());
		$m2 = is_array($m2) ? $m2 : (!Helper::isEmpty($m2) ? array($m2) : array());

		#
		$arTag = array();
		foreach ($m1 as $key => $value)
		{
			$arTag[] = array(
				'#' => [],
				'@' => array(
					'id' => $m1[$key],
					'instock' => $m2[$key],
				),
			);
		}
		if (!empty($arTag))
		{
			return array(
				array(
					'#' => ['outlet' => $arTag]
				),
			);
		}
		return '';
	}

}

?>