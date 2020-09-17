<?
/**
 * Acrit Core: beru.ru plugin
 * @documentation https://yandex.ru/support/marketplace/catalog/excel.html
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

class BeruRuExcelGeneral extends BeruRu {
	
	const DATE_UPDATED = '2020-05-18';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'beru_ru.xlsx';
	protected $arSupportedFormats = ['XLSX'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xlsx';
	protected $arSupportedCurrencies = ['RUB'];
	
	# Basic settings
	protected $bCategoriesExport = true;
	protected $bCurrenciesExport = true;
	
	# Other export settings
	protected $bZip = false;
	
	# Own settings
	protected $intExcelSheetIndex = 2;
	protected $intExcelStartCol = 2;
	protected $intExcelStartRow = 5;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		
		# General
		$arResult['HEADER_GENERAL'] = [];
		$arResult['sku'] = ['COLUMN' => 2, 'REQUIRED' => true, 'FIELD' => 'ID'];
		$arResult['name'] = ['COLUMN' => 3, 'REQUIRED' => true, 'FIELD' => 'NAME'];
		$arResult['category'] = ['COLUMN' => 4, 'REQUIRED' => true, 'FIELD' => 'SECTION__NAME'];
		$arResult['trademark'] = ['COLUMN' => 5, 'REQUIRED' => true, 'FIELD' => ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF', 'PROPERTY_MANUFACTURER'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['url'] = ['COLUMN' => 6, 'REQUIRED' => true, 'FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['manufacturer'] = ['COLUMN' => 7, 'REQUIRED' => true, 'FIELD' => ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF', 'PROPERTY_MANUFACTURER'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['country'] = ['COLUMN' => 8, 'REQUIRED' => true, 'FIELD' => 'PROPERTY_COUNTRY'];
		$arResult['barcode'] = ['COLUMN' => 9, 'FIELD' => ['CATALOG_BARCODE', 'PROPERTY_BARCODE'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['article'] = ['COLUMN' => 10, 'FIELD' => ['PROPERTY_CML2_ARTICLE', 'PROPERTY_ARTICLE', 'PROPERTY_ARTNUMBER'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['description'] = ['COLUMN' => 11, 'FIELD' => ['DETAIL_TEXT', 'PREVIEW_TEXT'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['shelf_life'] = ['COLUMN' => 12];
		$arResult['shelf_life_comment'] = ['COLUMN' => 13];
		$arResult['life_time'] = ['COLUMN' => 14];
		$arResult['life_time_comment'] = ['COLUMN' => 15];
		$arResult['guarantee_period'] = ['COLUMN' => 16];
		$arResult['guarantee_period_comment'] = ['COLUMN' => 17];
		$arResult['document_number'] = ['COLUMN' => 18];
		$arResult['hs_code'] = ['COLUMN' => 19];
		$arResult['dimensions'] = ['COLUMN' => 20, 'REQUIRED' => true, 'CONST' => ['{=catalog.CATALOG_LENGTH}/100', '{=catalog.CATALOG_WIDTH}/100', '{=catalog.CATALOG_HEIGHT}/100'], 'CONST_PARAMS' => ['MATH' => 'Y'], 'PARAMS' => ['MULTIPLE_separator' => 'other', 'MULTIPLE_separator_other' => '/']];
		$arResult['weight'] = ['COLUMN' => 21, 'REQUIRED' => true, 'CONST' => '{=catalog.CATALOG_WEIGHT}/1000', 'CONST_PARAMS' => ['MATH' => 'Y']];
		
		#
		$arResult['HEADER_WAREHOUSE'] = [];
		$arResult['supply_plans'] = ['COLUMN' => 22];
		$arResult['quantity_in_a_package'] = ['COLUMN' => 23];
		$arResult['minimum_amount_for_delivery'] = ['COLUMN' => 24];
		$arResult['additional_batch'] = ['COLUMN' => 25];
		$arResult['delivery_days'] = ['COLUMN' => 26];
		$arResult['delivery_time'] = ['COLUMN' => 27];
		$arResult['more_than_one_place'] = ['COLUMN' => 28];
		
		#
		$arResult['HEADER_YANDEX_SKU'] = [];
		$arResult['yandex_sku'] = ['COLUMN' => 29, 'FIELD' => 'PROPERTY_YANDEX_SKU'];
		
		#
		$arResult['HEADER_PRODUCT'] = [];
		$arResult['price'] = ['COLUMN' => 30, 'REQUIRED' => true, 'FIELD' => ['OFFER.CATALOG_PRICE_1__WITH_DISCOUNT', 'CATALOG_PRICE_1__WITH_DISCOUNT'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['old_price'] = ['COLUMN' => 31, 'FIELD' => ['OFFER.CATALOG_PRICE_1', 'CATALOG_PRICE_1'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['vat'] = ['COLUMN' => 32];
		$arResult['disable'] = ['COLUMN' => 33];
		$arResult['amount'] = ['COLUMN' => 34, 'FIELD' => 'CATALOG_QUANTITY'];
		
		#
		return $arResult;
	}
	
	/**
	 *	Export excel item
	 */
	protected function stepExport_XLS_ExportItem($arItem){
		return $stepExport_XLS_ExportItem;($arItem);
	}
	protected function stepExport_XLSX_ExportItem($arItem){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		#
		$intLineIndex = isset($arSession['LAST_LINE']) ? $arSession['LAST_LINE'] : $this->intExcelStartRow;
		#
		$arRawFields = Json::decode($arItem['DATA']);
		$arFields = $this->getFieldsCached($this->intProfileId, $arItem['IBLOCK_ID'], false);
		foreach($arFields as $key => $obField){
			$strFieldCode = $obField->getCode();
			$arFieldParams = $obField->getInitialParams();
			$intColumnIndex = intVal($arFieldParams['COLUMN']);
			if($intColumnIndex > 0){
				$strFieldValue = $arRawFields[$strFieldCode];
				$this->excelWriteCell($this->intExcelSheetIndex, $strFieldValue, $intColumnIndex, $intLineIndex);
			}
		}
		#
		$arSession['LAST_LINE'] = $intLineIndex + 1;
	}
	
	/**
	 *	
	 */
	protected function onUpBeforeExcelOpen(&$strFilename){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		#
		$bCopied = &$arSession['XLSX_TEMPLATE_COPIED'];
		if(!$bCopied){
			$strSourceFile = static::getFolder().'/file/template.xlsx';
			if(!copy($strSourceFile, $strFilename)){
				$strFilename = false;
				$this->addToLog('Error copying file template.xlsx');
			}
			$bCopied = true;
		}
	}
	
	/**
	 *	
	 */
	protected function onUpBeforeExcelSave(&$strFilename, &$strFilenameUpdated){
		$strColFirst = $this->excelGetColumnLetter($this->intExcelStartCol);
		$strRowFirst = $this->intExcelStartRow;
		$strColLast = $this->obExcel->getActiveSheet()->getHighestColumn();
		$intRowLast = $this->obExcel->getActiveSheet()->getHighestRow();
		$strCells = sprintf('%s%s:%s%s', $strColFirst, $strRowFirst, $strColLast, $intRowLast);
		$this->obExcel->getActiveSheet()->getStyle($strCells)->getAlignment()->setWrapText(false);
		$strCells = sprintf('%s%s:%s%s', $strColFirst, $strRowFirst, $strColFirst, $strRowFirst);
		$this->obExcel->getActiveSheet()->getStyle($strCells);
	}

}

?>