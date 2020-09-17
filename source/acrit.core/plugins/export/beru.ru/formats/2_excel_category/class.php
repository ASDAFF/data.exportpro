<?
/**
 * Acrit Core: beru.ru plugin
 * @documentation https://yandex.ru/support/marketplace/catalog/excel.html
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

class BeruRuExcelCategory extends BeruRu {
	
	const DATE_UPDATED = '2020-05-18';
	
	const CELL_BG_REQUIRED = 'FFCC99';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'beru_ru_category.xlsx';
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
	protected $intExcelSheetIndexMain = 2;
	protected $intExcelSheetIndexProps = 3;
	protected $intExcelRowCategoryName = 1;
	protected $intExcelRowCategoryHint = 2;
	protected $intExcelRowGroupName = 3;
	protected $intExcelRowFieldName = 4;
	protected $intExcelRowFieldHint = 6;
	protected $intExcelRowStart = 7;
	protected $intExcelRowPropName = 1;
	protected $intExcelRowProps = 2;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		
		# Read excel file
		if(strlen($this->arParams['EXCEL_TEMPLATE'])){
			$strFilename = Helper::root().$this->arParams['EXCEL_TEMPLATE'];
			if(is_file($strFilename)){
				Helper::includePhpSpreadSheet();
				$strExcelType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($strFilename);
				$obReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($strExcelType);
				$this->obExcel = $obReader->load($strFilename);
				unset($obReader);
				$this->readExcelFields($arResult);
			}
		}
		#
		return $arResult;
	}
	
	/**
	 *	Parse Excel file
	 */
	protected function readExcelFields(&$arResult){
		$arProps = $this->readExcelProps();
		$strLastColumn = $this->obExcel->getSheet($this->intExcelSheetIndexMain)->getHighestColumn();
		$intColumnCount = $this->excelGetColumnIndex($strLastColumn);
		$strCurrentGroup = '';
		for($intColumnIndex = $this->intColStart; $intColumnIndex <= $intColumnCount; $intColumnIndex++){
			$strColumnLetter = $this->excelGetColumnLetter($intColumnIndex);
			$strGroup = $this->excelReadCell($this->intExcelSheetIndexMain, $intColumnIndex, $this->intExcelRowGroupName);
			$strField = $this->excelReadCell($this->intExcelSheetIndexMain, $intColumnIndex, $this->intExcelRowFieldName);
			$strHint =  $this->excelReadCell($this->intExcelSheetIndexMain, $intColumnIndex, $this->intExcelRowFieldHint);
			$strBg = $this->excelGetCellBgColor($this->intExcelSheetIndexMain, $intColumnIndex, $this->intExcelRowFieldName);
			if(strlen($strGroup)){
				$strCurrentGroup = $strGroup;
				$arResult['HEADER_'.$strColumnLetter] = ['NAME' => $strGroup];
			}
			if(strlen($strField)){
				$arItem = [
					'NAME' => $strField,
					'DESCRIPTION' => nl2br(str_replace("\n\n", "\n", $strHint)),
					'COLUMN' => $intColumnIndex,
				];
				if(isset($arProps[$strField])){
					$arItem['ALLOWED_VALUES'] = $arProps[$strField];
				}
				if($strBg == static::CELL_BG_REQUIRED){
					$arItem['REQUIRED'] = true;
				}
				$this->getFieldDefaultValue($arItem);
				$arResult['column_'.$strColumnLetter] = $arItem;
				#Helper::P($arResult);
			}
		}
	}
	
	/**
	 *	Get default value
	 */
	protected function getFieldDefaultValue(&$arItem){
		$arMap = [
			'SKU' => ['FIELD' => 'ID'],
			'NAME' => ['FIELD' => 'NAME'],
			'TRADEMARK' => ['FIELD' => ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF', 'PROPERTY_MANUFACTURER'], 'PARAMS' => ['MULTIPLE' => 'first']],
			'PICTURE' => ['FIELD' => 'DETAIL_PICTURE'],
			'DESCRIPTION' => ['FIELD' => ['DETAIL_TEXT', 'PREVIEW_TEXT'], 'PARAMS' => ['MULTIPLE' => 'first']],
			'ARTICLE' => ['FIELD' => ['PROPERTY_CML2_ARTICLE', 'PROPERTY_ARTICLE', 'PROPERTY_ARTNUMBER'], 'PARAMS' => ['MULTIPLE' => 'first']],
			'BARCODE' => ['FIELD' => ['CATALOG_BARCODE', 'PROPERTY_BARCODE'], 'PARAMS' => ['MULTIPLE' => 'first']],
			'MORE_PHOTO' => ['FIELD' => ['PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS']],
			'COLOR_FILTER' => ['FIELD' => ['PROPERTY_COLOR', 'PROPERTY_COLOUR']],
			'COLOR_DETAIL' => ['FIELD' => ['PROPERTY_COLOR', 'PROPERTY_COLOUR']],
			'MATERIAL' => ['FIELD' => 'PROPERTY_MATERIAL'],
		];
		foreach($arMap as $key => $arValue){
			if(static::getMessage('DEFAULT_'.$key) == $arItem['NAME']){
				$arItem = array_merge($arItem, $arValue);
			}
		}
	}
	
	/**
	 *	Read properties (sheet #4)
	 */
	protected function readExcelProps(){
		$arResult = [];
		$strLastColumn = $this->obExcel->getSheet($this->intExcelSheetIndexProps)->getHighestColumn();
		$intLastRow = $this->obExcel->getSheet($this->intExcelSheetIndexProps)->getHighestRow();
		$intColumnCount = $this->excelGetColumnIndex($strLastColumn);
		for($intColumnIndex = $this->intColStart; $intColumnIndex <= $intColumnCount; $intColumnIndex++){
			$strColumnLetter = $this->excelGetColumnLetter($intColumnIndex);
			$strPropValue = $this->excelReadCell($this->intExcelSheetIndexProps, $intColumnIndex, $this->intExcelRowPropName);
			if(strlen($strPropValue)){
				$arResult[$strPropValue] = [];
				for($intRowIndex = $this->intExcelRowProps; $intRowIndex <= $intLastRow; $intRowIndex++){
					$strValue = $this->excelReadCell($this->intExcelSheetIndexProps, $intColumnIndex, $intRowIndex);
					if(strlen($strValue)){
						$arResult[$strPropValue][] = $strValue;
					}
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Set profile array
	 */
	public function setProfileArray(array &$arProfile, $bSaving=false){
		parent::setProfileArray($arProfile, $bSaving);
		if($bSaving){
			if(is_array($arProfile['PARAMS']['EXCEL_TEMPLATE'])){
				$this->saveExcelFile($arProfile['PARAMS']['EXCEL_TEMPLATE']);
			}
			else{
				if(is_array($arProfile['__delete'])){
					if($arProfile['__delete']['PARAMS']['EXCEL_TEMPLATE'] == 'Y'){
						unset($arProfile['PARAMS']['EXCEL_TEMPLATE']);
					}
				}
			}
		}
	}
	
	/**
	 *	
	 */
	protected function saveExcelFile(&$arExcel){
		$strTmpRoot = \CTempFile::getAbsoluteRoot();
		if(strlen($arExcel['tmp_name']) && is_file($strTmpRoot.$arExcel['tmp_name']) && !$arExcel['error']){
			$strSourceFile = $strTmpRoot.$arExcel['tmp_name'];
			$strTargetFile = $this->getProfileTemplateFilename();
			$strTargetFileAbs = Helper::root().$strTargetFile;
			$strTargetFileTmp = $strTargetFileAbs.'.tmp';
			if(copy($strSourceFile, $strTargetFileTmp)){
				if(is_file($strTargetFileAbs)){
					unlink($strTargetFileAbs);
				}
				if(rename($strTargetFileTmp, $strTargetFileAbs)){
					$arExcel = $strTargetFile;
				}
			}
		}
		elseif(!is_string($arExcel) || !strlen($arExcel)){
			$arExcel = '';
		}
	}
	
	/**
	 *	Get filename for save 
	 */
	protected function getProfileTemplateFilename(){
		$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$this->intProfileId, true, true]);
		$strExt = pathinfo($arExcel['name'], PATHINFO_EXTENSION);
		return $strTmpDir.'/template.xlsx';
	}
	
	/**
	 *	Display settings
	 */
	protected function onUpShowSettings(&$arSettings){
		$strFile = $this->arParams['EXCEL_TEMPLATE'];
		if(!strlen($strFile) || !is_file(Helper::root().$strFile)){
			$strFile = false;
		}
		$arSettings['EXCEL_TEMPLATE'] = \Bitrix\Main\UI\FileInput::createInstance([
			'name' => 'PROFILE[PARAMS][EXCEL_TEMPLATE]',
			'description' => false,
			'upload' => true,
			'allowUpload' => 'F',
			'allowUploadExt' => 'xlsx',
			'allowSort' => 'N',
			'medialib' => false,
			'fileDialog' => false,
			'cloud' => false,
			'delete' => true,
			'maxCount' => 1
		])->show($strFile, false);
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
		$intLineIndex = isset($arSession['LAST_LINE']) ? $arSession['LAST_LINE'] : $this->intExcelRowStart;
		#
		$arRawFields = Json::decode($arItem['DATA']);
		$arFields = $this->getFieldsCached($this->intProfileId, $arItem['IBLOCK_ID'], false);
		foreach($arFields as $key => $obField){
			$strFieldCode = $obField->getCode();
			$arFieldParams = $obField->getInitialParams();
			$intColumnIndex = intVal($arFieldParams['COLUMN']);
			if($intColumnIndex > 0){
				$strFieldValue = $arRawFields[$strFieldCode];
				$this->excelWriteCell($this->intExcelSheetIndexMain, $strFieldValue, $intColumnIndex, $intLineIndex);
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
		$strColFirst = $this->excelGetColumnLetter($this->intColStart);
		$strRowFirst = $this->intExcelRowStart;
		$strColLast = $this->obExcel->getActiveSheet()->getHighestColumn();
		$intRowLast = $this->obExcel->getActiveSheet()->getHighestRow();
		$strCells = sprintf('%s%s:%s%s', $strColFirst, $strRowFirst, $strColLast, $intRowLast);
		$this->obExcel->getActiveSheet()->getStyle($strCells)->getAlignment()->setWrapText(false);
		$strCells = sprintf('%s%s:%s%s', $strColFirst, $strRowFirst, $strColFirst, $strRowFirst);
		$this->obExcel->getActiveSheet()->getStyle($strCells);
	}

}

?>