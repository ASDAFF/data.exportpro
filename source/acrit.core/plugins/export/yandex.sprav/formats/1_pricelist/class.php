<?
/**
 * Acrit Core: Yandex.Spravochnik plugin
 * @https://yandex.ru/sprav/1530227/edit/price-lists/
 */

namespace Acrit\Core\Export\Plugins;

use 
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Xml;

Helper::loadMessages(__FILE__);

require_once realpath(__DIR__ . '/../../../_custom_excel/class.php');
require_once realpath(__DIR__ . '/../../../_custom_excel/formats/1_general/class.php');

class YandexSpravPricelist extends CustomExcelGeneral {
	
	CONST DATE_UPDATED = '2019-09-16';
	
	protected $arAvailableFormats = ['XLSX', 'XLS'];
	protected $bZip = false;
	protected $bEditableColumns = false;
	protected $bAdditionalSettings = false;
	protected $bAddHeader = true;
	protected $bUtm = false;

	public static function getCode() {
		return 'YANDEX_SPRAV_PRICELIST';
	}

	public static function getName() {
		return static::getMessage('NAME');
	}

	public function getDefaultExportFilename() {
		return 'yandex_sprav_pricelist.xlsx';
	}
	
	/**
	 *	Get default fields
	 */
	public function getDefaultFields(){
		return Helper::convertUtf8(file_get_contents(__DIR__.'/default_columns.txt'));
	}
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		$arFields = [
			'KATEGORIYA' => [
				'DEFAULT_VALUE' => [[
					'TYPE' => 'FIELD',
					'VALUE' => 'SECTION__NAME',
				]],
				'PARAMS' => [
					'_CUSTOM_EXCEL_WIDTH' => '14',
				],
			],
			'NAZVANIE' => [
				'DEFAULT_VALUE' => [[
					'TYPE' => 'FIELD',
					'VALUE' => 'NAME',
				]],
				'PARAMS' => [
					'_CUSTOM_EXCEL_WIDTH' => '26',
				],
			],
			'OPISANIE' => [
				'DEFAULT_VALUE' => [[
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_TEXT',
				]],
				'PARAMS' => [
					'_CUSTOM_EXCEL_WIDTH' => '26',
				],
			],
			'TSENA' => [
				'DEFAULT_VALUE' => [[
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1__WITH_DISCOUNT',
				]],
				'PARAMS' => [
					'_CUSTOM_EXCEL_WIDTH' => '9',
				],
			],
			'FOTO' => [
				'DEFAULT_VALUE' => [[
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
				]],
				'PARAMS' => [
					'_CUSTOM_EXCEL_WIDTH' => '12',
				],
			],
			'POPULYARNYY_TOVAR' => [
				'DEFAULT_VALUE' => [[
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('MAIN_NO'),
				]],
				'PARAMS' => [
					'_CUSTOM_EXCEL_WIDTH' => '18',
				],
			],
			'V_NALICHII' => [
				'DEFAULT_TYPE' => 'CONDITION',
				'DEFAULT_CONDITIONS' => Filter::getConditionsJson($this->strModuleId, $intIBlockID, array(
					array(
						'FIELD' => 'CATALOG_QUANTITY',
						'LOGIC' => 'MORE',
						'VALUE' => '0',
					),
				)),
				'DEFAULT_VALUE' => [[
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('MAIN_YES'),
					'SUFFIX' => 'Y',
				], [
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('MAIN_NO'),
					'SUFFIX' => 'N',
				]],
				'PARAMS' => [
					'_CUSTOM_EXCEL_WIDTH' => '11',
				],
			],
		];
		foreach($arResult as $key => $obField){
			$strFieldCode = $obField->getCode();
			$arFieldParams = $obField->getInitialParams();
			$arFieldParamsCustom = $arFields[$strFieldCode];
			if(is_array($arFieldParamsCustom)){
				$arFieldParams = array_merge($arFieldParams, $arFieldParamsCustom);
			}
			unset($obField);
			$arResult[$key] = new Field($arFieldParams);
		}
		return $arResult;
	}
	
	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		return [];
	}
	
	/**
	 *	Show notices
	 */
	public function showMessages(){
		//
	}

}

?>