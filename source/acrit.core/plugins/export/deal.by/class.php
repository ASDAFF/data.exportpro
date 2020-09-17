<?
/**
 * Acrit Core: deal.by base plugin
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Export\UniversalPlugin,
	\Acrit\Core\Helper,
	#
	\PhpOffice\PhpSpreadsheet\Spreadsheet,
	\PhpOffice\PhpSpreadsheet\Writer\Xlsx,
	\PhpOffice\PhpSpreadsheet\IOFactory,
	\PhpOffice\PhpSpreadsheet\Cell\Coordinate;

abstract class DealBy extends UniversalPlugin {
	
	/**
	 *	Handle categories update
	 *	Method must process tmp file and return string for all categories, separated by "\n"
	 *	Charset must be the same as the bitrix site charset (see BX_UTF constant)
	 *	If return is false, download data will be saved as is, with charset convert (if it need)
	 */
	protected function processUpdatedCategories($strTmpFile){
		$strCategories = '';
		if(is_file($strTmpFile)){
			Helper::includePhpSpreadSheet();
			$obExcel = IOFactory::load($strTmpFile);
			$intRowStart = 2;
			$intRowCount = $obExcel->getActiveSheet()->getHighestRow();
			$arNameColumns = ['A', 'B', 'C', 'D'];
			for($intRow=$intRowStart; $intRow<=$intRowCount; $intRow++){
				$arCategoryName = [];
				foreach($arNameColumns as $strNameColumn){
					$strCategoryName = trim($obExcel->getActiveSheet()->getCell($strNameColumn.$intRow)->getValue());
					if(strlen($strCategoryName)){
						$arCategoryName[] = ' '.str_replace($this->strCategoryNameSeparator, '-', $strCategoryName).' ';
					}
				}
				$strCategoryId = $obExcel->getActiveSheet()->getCell('F'.$intRow)->getValue();
				$strCategories .= $strCategoryId.' - '.implode($this->strCategoryNameSeparator, $arCategoryName)."\n";
			}
			unset($obExcel);
		}
		$strCategories = Helper::convertEncodingFrom($strCategories, 'UTF-8');
		return $strCategories;
	}
	
}

?>