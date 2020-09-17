<?
/**
 * Acrit Core: Custom Excel format
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Log,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Json,
	#
	\PhpOffice\PhpSpreadsheet\Spreadsheet,
	\PhpOffice\PhpSpreadsheet\Writer\Xlsx,
	\PhpOffice\PhpSpreadsheet\IOFactory,
	\PhpOffice\PhpSpreadsheet\Cell\Coordinate;

Loc::loadMessages(__FILE__);

class CustomExcelGeneral extends CustomExcel {
	
	CONST DATE_UPDATED = '2019-09-06';
	
	CONST ROLE_URL = 'URL';
	
	CONST DELETE_MODE_NO = '';
	CONST DELETE_MODE_SIMPLE = 'DELETE';
	CONST DELETE_MODE_ATTR = 'ATTR';
	
	CONST SINGLE_IBLOCK_ID = 0;
	CONST FIRST_SHEET_INDEX = 0;
	CONST FIRST_LINE_INDEX = 1;

	protected static $bSubclass = true;
	
	protected $strFileExt;
	
	protected $strExcelFileName;
	protected $obExcel;
	protected $arExcelHeaders;
	
	protected $arAvailableFormats = ['XLSX', 'XLS', 'ODS'];
	protected $bZip = true;
	protected $bEditableColumns = true;
	protected $bAdditionalSettings = true;
	protected $bUtm = true;
	
	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}
	
	/* START OF BASE STATIC METHODS */
	
	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return parent::getCode().'_GENERAL';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported(){
		return false;
	}
	
	/**
	 *	Are categories export?
	 */
	public function areCategoriesExport(){
		return false;
	}
	
	/**
	 *	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict(){ // static ot not?
		return false;
	}
	
	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){ // static ot not?
		return false;
	}
	
	/**
	 *	Get list of supported currencies
	 */
	public function getSupportedCurrencies(){
		return array('RUB', 'USD', 'EUR', 'UAH', 'KZT', 'BYN');
	}
	
	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = array();
		$arResult[] = array(
			'DIV' => 'excel_structure',
			'TAB' => static::getMessage('TAB_EXCEL_SETTINGS_NAME'),
			'TITLE' => static::getMessage('TAB_EXCEL_SETTINGS_TITLE'),
			'SORT' => 5,
			'FILE' => __DIR__.'/tabs/excel_settings.php',
		);
		return $arResult;
	}
	
	/**
	 *	Get custom subtabs for profile edit
	 */
	public function getAdditionalSubTabs($intProfileID, $intIBlockID){
		$arResult = array();
		return $arResult;
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'file.xlsx';
	}
	
	/**
	 *	Prepare sheet title
	 */
	public function prepareSheetTitle(&$strSheetTitle){
		if(!is_string($strSheetTitle)){
			$strSheetTitle = '';
		}
		if(!strlen($strSheetTitle)){
			$strSheetTitle = static::getMessage('DEFAULT_SHEET_TITLE');
		}
		$strSheetTitle = str_replace([':', '\\', '/', '?', '*', '[', ']'], '', $strSheetTitle);
		$intMaxWidth = 31;
		if(strlen($strSheetTitle) > $intMaxWidth){
			$strSheetTitle = substr($strSheetTitle, 0, $intMaxWidth);
		}
	}
	/**
	 *	Set available extension
	 */
	protected function setAvailableExtension($strExtension){
		$this->strFileExt = $strExtension;
	}
	
	/**
	 *	Show plugin settings
	 */
	public function showSettings(){
		$this->setAvailableExtension('xls');
		return $this->showDefaultSettings();
	}
	
	/**
	 *	Show plugin default settings
	 */
	protected function showDefaultSettings(){
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;" data-role="settings-<?=static::getCode();?>">
			<tbody>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_FILE_HINT'));?>
						<label for="acrit_exp_plugin_excel_filename">
							<b><?=static::getMessage('SETTINGS_FILE');?>:</b>
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?\CAdminFileDialog::ShowScript(Array(
							'event' => 'AcritExpPluginExcelFilenameSelect',
							'arResultDest' => array('FUNCTION_NAME' => 'acrit_exp_plugin_excel_filename_select'),
							'arPath' => array(),
							'select' => 'F',
							'operation' => 'S',
							'showUploadTab' => true,
							'showAddToMenuTab' => false,
							'fileFilter' => $this->strFileExt,
							'allowAllFiles' => true,
							'saveConfig' => true,
						));?>
						<script>
						function acrit_exp_plugin_excel_filename_select(File,Path,Site){
							var FilePath = Path+'/'+File;
							FilePath = FilePath.replace(/\/\//g, '/');
							$('#acrit_exp_plugin_excel_filename').val(FilePath);
						}
						</script>
						<table class="acrit-exp-plugin-settings-fileselect">
							<tbody>
								<tr>
									<td><input type="text" name="PROFILE[PARAMS][EXPORT_FILE_NAME]" 
										id="acrit_exp_plugin_excel_filename" data-role="export-file-name"
										value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['EXPORT_FILE_NAME']);?>" size="40" 
										placeholder="<?=static::getMessage('SETTINGS_FILE_PLACEHOLDER');?>" /></td>
									<td><input type="button" value="..." onclick="AcritExpPluginExcelFilenameSelect()" /></td>
									<td>
										&nbsp;
										<?=$this->showFileOpenLink();?>
										<?if($this->bZip && $this->arProfile['PARAMS']['COMPRESS_TO_ZIP']=='Y'):?>
											<?=$this->showFileOpenLink(Helper::changeFileExt($this->getExportFileName(), 'zip'), 'Zip');?>
										<?endif?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr id="tr_FORMAT">
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_FORMAT_HINT'));?>
						<label for="acrit_exp_plugin_format">
							<?=static::getMessage('SETTINGS_FORMAT');?>:
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?
						$arOptions = array_map(function($item){
							return $item['NAME'];
						}, $this->getAvailableFormats());
						$arOptions = array(
							'REFERENCE' => array_values($arOptions),
							'REFERENCE_ID' => array_keys($arOptions),
						);
						print SelectBoxFromArray('PROFILE[PARAMS][EXCEL_FORMAT]', $arOptions, 
							$this->getFormat(), '', 'id="acrit_exp_plugin_format" data-role="excel-general-format"');
						?>
					</td>
				</tr>
				<tr id="tr_FORMAT_NO_XML_WRITER" style="display:none;">
					<td width="40%" class="adm-detail-content-cell-l"></td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?
						if(!extension_loaded('xmlwriter')){
							print Helper::showNote(static::getMessage('NOTICE_NO_XML_WRITER'), true);
						}
						?>
					</td>
				</tr>
				<?if($this->bZip):?>
					<tr id="tr_ZIP">
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('SETTINGS_ZIP_HINT'));?>
							<label for="acrit_exp_plugin_compress_to_zip">
								<?=static::getMessage('SETTINGS_ZIP');?>:
							</label>
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input name="PROFILE[PARAMS][COMPRESS_TO_ZIP]" type="hidden" value="N"/>
							<input name="PROFILE[PARAMS][COMPRESS_TO_ZIP]" type="checkbox" value="Y" 
								<?if($this->arProfile['PARAMS']['COMPRESS_TO_ZIP']=='Y'):?>checked="checked"<?endif?>
							id="acrit_exp_plugin_compress_to_zip" />
						</td>
					</tr>
					<tr id="tr_DELETE_EXCEL_IF_ZIP">
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('SETTINGS_DELETE_EXCEL_IF_ZIP_HINT'));?>
							<label for="acrit_exp_plugin_delete_excel_if_zip">
								<?=static::getMessage('SETTINGS_DELETE_EXCEL_IF_ZIP');?>:
							</label>
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input name="PROFILE[PARAMS][DELETE_EXCEL_IF_ZIP]" type="hidden" value="N"/>
							<input name="PROFILE[PARAMS][DELETE_EXCEL_IF_ZIP]" type="checkbox" value="Y" 
								<?if($this->arProfile['PARAMS']['DELETE_EXCEL_IF_ZIP']=='Y'):?>checked="checked"<?endif?>
							id="acrit_exp_plugin_delete_excel_if_zip" />
						</td>
					</tr>
				<?endif?>
			</tbody>
		</table>
		<script>
		$('[data-role="settings-<?=static::getCode();?>"] #tr_ZIP input[type=checkbox]').change(function(){
			var row = $('[data-role="settings-<?=static::getCode();?>"] #tr_DELETE_EXCEL_IF_ZIP');
			if($(this).is(':checked')){
				row.show();
			}
			else {
				row.hide();
			}
		}).trigger('change');
		</script>
		<?
		return ob_get_clean();
	}
	
	/**
	 *
	 */
	public function areEditableColumns(){
		return $this->bEditableColumns;
	}
	
	/**
	 *
	 */
	public function areAdditionalSettingsAvailable(){
		return $this->bAdditionalSettings;
	}
	
	/**
	 *	Get available file formats
	 */
	public function getAvailableFormats(){
		$arResult = [
			'XLSX' => [
				'NAME' => static::getMessage('FORMAT_XLSX'),
				'WRITER_TYPE' => 'Xlsx',
				'DEFAULT' => true,
			],
			'XLS' => [
				'NAME' => static::getMessage('FORMAT_XLS'),
				'WRITER_TYPE' => 'Xls',
			],
			'ODS' => [
				'NAME' => static::getMessage('FORMAT_ODS'),
				'WRITER_TYPE' => 'Ods',
			],
		];
		if(is_array($this->arAvailableFormats) && !empty($this->arAvailableFormats)){
			foreach($arResult as $strFormat => $arFormat){
				if(!in_array($strFormat, $this->arAvailableFormats)){
					unset($arResult[$strFormat]);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get current format
	 */
	public function getFormat($bReturnWriterType=false){
		$strFormat = $this->arProfile['PARAMS']['EXCEL_FORMAT'];
		$arAvailableFormats = $this->getAvailableFormats();
		if(empty($strFormat) || !array_key_exists($strFormat, $arAvailableFormats)){
			foreach($arAvailableFormats as $strFormatKey => $arFormat){
				if($arFormat['DEFAULT']){
					$strFormat = $strFormatKey;
					break;
				}
			}
		}
		if($bReturnWriterType){
			return $arAvailableFormats[$strFormat]['WRITER_TYPE'];
		}
		return $strFormat;
	}
	
	/**
	 *	Show settings for field
	 */
	public function showFieldSettings($strFieldCode, $strFieldType, $strFieldName, $arParams, $strPosition){
		ob_start();
		$strFile = __DIR__.'/field_settings/'.ToLower($strPosition).'.php';
		if(is_file($strFile)){
			require $strFile;
		}
		return ob_get_clean();
	}
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = array();
		#
		$intSort = 0;
		$arFields = $this->parseHeaders();
		foreach($arFields as $strFieldCode => $strFieldName){
			$intSort++;
			$arFieldParams = $this->arProfile['IBLOCKS'][$intIBlockID]['FIELDS'][$strFieldCode]['PARAMS'];
			if(!is_array($arFieldParams)){
				$arFieldParams = array();
			}
			$arResult[] = new Field(array(
				'CODE' => $strFieldCode,
				'DISPLAY_CODE' => $strFieldName,
				'NAME' => strlen($arFieldParams['_CUSTOM_EXCEL_NAME']) ? $arFieldParams['_CUSTOM_EXCEL_NAME'] : $strFieldName,
				'SORT' => $intSort*10,
				'REQUIRED' => $arFieldParams['_CUSTOM_EXCEL_REQUIRED'] == 'Y' ? true : false,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => '',
						'PARAMS' => array(
							'HTMLSPECIALCHARS' => 'skip',
							'MULTIPLE' => 'first',
						),
					),
				),
				'PARAMS' => array(
					'HTMLSPECIALCHARS' => 'skip',
				),
			));
		}
		#
		if($this->bUtm){
			$this->addUtmFields($arResult, $intSort*10+1, false, false, $bAdmin ? true : false);
		}
		#
		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]);
		foreach($arAdditionalFields as $arAdditionalField){
			$arDefaultValue = null;
			if(strlen($arAdditionalField['DEFAULT_FIELD'])){
				$arDefaultValue = array();
				$arDefaultValue[] = array(
					'TYPE' => 'FIELD',
					'VALUE' => $arAdditionalField['DEFAULT_FIELD'],
				);
			}
			$arResult[] = new Field(array(
				'ID' => IntVal($arAdditionalField['ID']),
				#'CODE' => AdditionalField::getFieldCode($arAdditionalField['ID']),
				'CODE' => Helper::call($this->strModuleId, 'AdditionalField', 'getFieldCode', [$arAdditionalField['ID']]),
				'NAME' => $arAdditionalField['NAME'],
				'SORT' => 1000000,
				'UNIT' => $arAdditionalField['UNIT'],
				'DESCRIPTION' => '',
				'REQUIRED' => false,
				'MULTIPLE' => true,
				'IS_ADDITIONAL' => true,
				'DEFAULT_VALUE' => $arDefaultValue,
			));
		}
		#
		return $arResult;
	}
	
	/**
	 *	Get default fields
	 */
	public function getDefaultFields(){
		return Helper::convertUtf8(file_get_contents(__DIR__.'/default_columns.txt'));
	}
	
	/**
	 *	Get available fields from fields TEXTAREA
	 */
	public function parseHeaders(){
		$arResult = array();
		#
		$strFields = $this->arProfile['PARAMS']['CUSTOM_EXCEL']['FIELDS'];
		if(is_null($strFields)){
			$strFields = $this->getDefaultFields();
		}
		if(strlen($strFields)){
			$arResult = explode("\n", $strFields);
			foreach($arResult as $key => $strItem){
				$strItem = trim($strItem);
				if(strlen($strItem)){
					$arResult[$key] = $strItem;
				}
				else{
					unset($arResult[$key]);
				}
			}
		}
		$arResult = array_unique($arResult);
		#
		$arResultTmp = array();
		foreach($arResult as $strItem){
			$strCode = \CUtil::translit($strItem, LANGUAGE_ID, array(
				'max_len' => 255,
				'change_case' => 'U',
				'replace_space' => '_',
				'replace_other' => '_',
				'delete_repeat_replace' => true,
			));
			$arResultTmp[$strCode] = $strItem;
		}
		$arResult = $arResultTmp;
		unset($arResultTmp);
		#
		return $arResult;
	}
	
	public function prepareFields($arFields){
		$arResult = [];
		if(!is_array($arFields)){
			$arFields = [];
		}
		foreach($this->arExcelHeaders as $key => $value){
			$arResult[$key] = (isset($arFields[$key]) ? $arFields[$key] : '');
		}
		return $arResult;
	}
	
	/**
	 *	Process single element
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		
		# event handlers OnCustomExcel
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomExcel') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arProfile, &$intIBlockID, &$arElement, &$arFields));
		}
		
		# Prepare data
		$bOffer = $arElement['IS_OFFER'];
		if($bOffer) {
			$arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
			$arElementSections = Exporter::getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		else{
			$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
			$arElementSections = Exporter::getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		
		# Process roles
		$strCurrency = '';
		foreach($arFields as $strField => $mValue){
			$arFieldParams = $arMainIBlockData['FIELDS'][$strField]['PARAMS'];
			switch($arFieldParams['_CUSTOM_EXCEL_ROLE']){
				case static::ROLE_URL:
					if($this->bUtm && strlen($arFields[$strField]) && $this->arParams['CUSTOM_EXCEL']['ADD_UTM'] == 'Y'){
						$this->addUtmToUrl($arFields[$strField], $arFields, false);
					}
					break;
			}
		}
		
		# Process values
		$arExcelFields = $this->parseHeaders();
		if(implode('', $arExcelValues) == ''){
			$arExcelValues = [];
		}
		
		# Build result
		$arResult = array(
			'TYPE' => 'JSON',
			'DATA' => Json::encode($arFields),
			'CURRENCY' => '',
			'SECTION_ID' => reset($arElementSections),
			'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
			'DATA_MORE' => array(),
		);
		
		# Event handlers OnCustomExcelResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomExcelResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arFields, $arExcelFields, $arProfile, $intIBlockID, $arElement));
		}
		
		# Ending..
		unset($intProfileID, $intElementID, $arExcelFields, $arExcelValues, $strCurrency, $strExcel);
		return $arResult;
	}
	
	/**
	 *	Show results
	 */
	public function showResults($arSession){
		ob_start();
		$intTime = $arSession['TIME_FINISHED']-$arSession['TIME_START'];
		if($intTime<=0){
			$intTime = 1;
		}
		?>
		<div><?=static::getMessage('RESULT_GENERATED');?>: <?=IntVal($arSession['GENERATE']['INDEX']);?></div>
		<div><?=static::getMessage('RESULT_EXPORTED');?>: <?=IntVal($arSession['EXPORT']['INDEX']);?></div>
		<div><?=static::getMessage('RESULT_ELAPSED_TIME');?>: <?=Helper::formatElapsedTime($intTime);?></div>
		<div><?=static::getMessage('RESULT_DATETIME');?>: <?=(new \Bitrix\Main\Type\DateTime())->toString();?></div>
		<?=$this->showFileOpenLink();?>
		<?if($this->bZip && $this->arProfile['PARAMS']['COMPRESS_TO_ZIP']=='Y'):?>
			<?=$this->showFileOpenLink($arSession['EXPORT']['EXCEL_FILE_URL_ZIP'], static::getMessage('RESULT_FILE_ZIP'));?>
		<?endif?>
		<?
		return Helper::showSuccess(ob_get_clean());
	}
	
	/**
	 *	Get steps
	 */
	public function getSteps(){
		$arResult = array();
		$arResult['CHECK'] = array(
			'NAME' => static::getMessage('ACRIT_EXP_EXPORTER_STEP_CHECK'),
			'SORT' => 10,
			'FUNC' => [$this, 'stepCheck'],
		);
		$arResult['EXPORT'] = array(
			'NAME' => static::getMessage('STEP_EXPORT'),
			'SORT' => 100,
			'FUNC' => [$this, 'stepExport'],
		);
		if($this->bZip && $this->arProfile['PARAMS']['COMPRESS_TO_ZIP'] == 'Y') {
			$arResult['ZIP'] = array(
				'NAME' => static::getMessage('STEP_ZIP'),
				'SORT' => 110,
				'FUNC' => [$this, 'stepZip'],
			);
		}
		return $arResult;
	}
	
	/**
	 *	Step: Check input params and data
	 */
	public function stepCheck($intProfileID, $arData){
		$strExportFilename = $this->arProfile['PARAMS']['EXPORT_FILE_NAME'];
		if(!strlen($strExportFilename)){
			$strErrorMessage = static::getMessage('NO_EXPORT_FILE_SPECIFIED');
			Log::getInstance($this->strModuleId)->add($strErrorMessage, $intProfileID);
			print Helper::showError($strErrorMessage);
			return Exporter::RESULT_ERROR;
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: Export
	 */
	public function stepExport($intProfileID, $arData){
		$bCron = $arData['IS_CRON'];
		
		Helper::includePhpSpreadSheet();
		
		# Start session
		$arSession = &$arData['SESSION']['EXPORT'];
		$strExportFilename = $this->arProfile['PARAMS']['EXPORT_FILE_NAME'];
		if(!isset($arSession['EXCEL_FILE'])){
			$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
			$strTmpFile = pathinfo($strExportFilename, PATHINFO_BASENAME).'.tmp';
			$arSession['EXCEL_FILE_URL'] = $strExportFilename;
			$arSession['EXCEL_FILE'] = $_SERVER['DOCUMENT_ROOT'].$strExportFilename;
			$arSession['EXCEL_FILE_TMP'] = $strTmpDir.'/'.$strTmpFile;
			#
			if($this->bZip && $this->arProfile['PARAMS']['COMPRESS_TO_ZIP'] == 'Y'){
				$arSession['EXCEL_FILE_ZIP'] = Helper::changeFileExt($_SERVER['DOCUMENT_ROOT'].$strExportFilename, 'zip');
				$arSession['EXCEL_FILE_URL_ZIP'] = Helper::changeFileExt($strExportFilename, 'zip');
			}
			if(is_file($arSession['EXCEL_FILE_TMP'])){
				unlink($arSession['EXCEL_FILE_TMP']);
			}
			#touch($arSession['EXCEL_FILE_TMP']);
			unset($strTmpDir, $strTmpFile);
		}
		$this->strExcelFileName = $arSession['EXCEL_FILE_TMP'];
		$this->arExcelHeaders = $this->parseHeaders();
		
		# Create sheets
		if($arSession['SHEETS_WRITEN'] !== true) {
			if($this->stepExport_createSheets($intProfileID, $arData) === Exporter::RESULT_SUCCESS){
				$arSession['SHEETS_WRITEN'] = true;
			}
			if(!$bCron){
				return Exporter::RESULT_CONTINUE;
			}
		}
		
		# Export items
		if($arSession['ITEMS_WRITEN'] !== true) {
			if($this->stepExport_writeExcelOffers($intProfileID, $arData) === Exporter::RESULT_SUCCESS){
				$arSession['ITEMS_WRITEN'] = true;
			}
			if(!$bCron){
				return Exporter::RESULT_CONTINUE;
			}
		}
		
		# Set columns width
		if($arSession['WIDTH_WRITEN'] !== true) {
			if($this->stepExport_setColumnsWidth($intProfileID, $arData) === Exporter::RESULT_SUCCESS){
				$arSession['WIDTH_WRITEN'] = true;
			}
			if(!$bCron){
				return Exporter::RESULT_CONTINUE;
			}
		}
		
		# Save export file (replace tmp file to real file)
		if(!$this->saveExportFile($arSession['EXCEL_FILE_TMP'], $arSession['EXCEL_FILE'], $strErrorMessage)){
			Log::getInstance($this->strModuleId)->add($strErrorMessage);
			print Helper::showError($strErrorMessage);
			if(!$bCron){
				return Exporter::RESULT_CONTINUE;
			}
		}
		
		# Finishing
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Create sheets
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_createSheets($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		$arSession['SHEETS'] = [];
		#
		$intSheetIndex = static::FIRST_SHEET_INDEX;
		$this->excelOpen();
		# Create sheets
		if($this->isMultisheet()){
			if(is_array($this->arProfile['IBLOCKS'])){
				foreach($this->arProfile['IBLOCKS'] as $arIBlock){
					if($arIBlock['IBLOCK_MAIN'] == 'Y'){
						$strTitle = $this->getIBlockName($arIBlock['IBLOCK_ID']);
						if(!Helper::isUtf()){
							$strTitle = Helper::convertEncoding($strTitle, 'CP1251', 'UTF-8');
						}
						if($intSheetIndex == static::FIRST_SHEET_INDEX){
							$this->obExcel->getActiveSheet()->setTitle($strTitle);
						}
						else{
							$this->obExcel->createSheet()->setTitle($strTitle);
						}
						$arSession['SHEETS'][$arIBlock['IBLOCK_ID']] = [
							'INDEX' => $intSheetIndex, // Sheet index
							'LINE' => static::FIRST_LINE_INDEX, // Line index
						];
						$intSheetIndex++;
					}
				}
				$this->obExcel->setActiveSheetIndex(0);
			}
		}
		else{
			$strSheetTitle = $this->arProfile['PARAMS']['CUSTOM_EXCEL']['SHEET_TITLE'];
			$this->prepareSheetTitle($strSheetTitle);
			if(!Helper::isUtf()){
				$strSheetTitle = Helper::convertEncoding($strSheetTitle, 'CP1251', 'UTF-8');
			}
			$this->obExcel->getActiveSheet()->setTitle($strSheetTitle);
			$arSession['SHEETS'][static::SINGLE_IBLOCK_ID] = [
				'INDEX' => static::FIRST_SHEET_INDEX, // Sheet index
				'LINE' => static::FIRST_LINE_INDEX, // Line index
			];
		}
		# Write headers with styles
		if($this->bAddHeader || $this->arProfile['PARAMS']['CUSTOM_EXCEL']['ADD_HEADER'] == 'Y'){
			foreach($arSession['SHEETS'] as $intIBlockId => &$arSheet){
				# Add
				$this->excelLine($this->arExcelHeaders, $arSheet['INDEX'], $arSheet['LINE']++);
				# Stylish
				$intColumnIndex = count($this->arExcelHeaders);
				$strColumnString = static::getColumnString($intColumnIndex);
				$this->obExcel->getSheet($arSheet['INDEX'])->getStyle('A1:'.$strColumnString.'1')->applyFromArray([
					'font' => [
						'bold' => true,
					],
					'fill' => [
						'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
						'startColor' => [
							'argb' => 'FFD9D9D9',
						],
						'endColor' => [
							'argb' => 'FFD9D9D9',
						],
					],
				]);
				# Freeze
				if($this->arProfile['PARAMS']['CUSTOM_EXCEL']['FREEZE_HEADER'] == 'Y'){
					$this->obExcel->getSheet($arSheet['INDEX'])->freezePane('A2');
				}
				# Handlers
				foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomExcelCreateSheets') as $arHandler) {
					ExecuteModuleEventEx($arHandler, array($this, $arSession, $intIBlockId, $arSheet));
				}
			}
		}
		# Save
		$this->excelSave();
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Get iblock name for tab title
	 */
	private function getIBlockName($intIBlockId){
		if(\Bitrix\Main\Loader::includeModule('iblock')) {
			$resIBlock = \CIBlock::GetList([], ['ID' => $intIBlockId]);
			if($arIBlock = $resIBlock->GetNext(false, false)) {
				return $arIBlock['NAME'];
			}
		}
		return null;
	}
	
	/**
	 *	Step: Export, write items
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
		
	protected function stepExport_writeExcelOffers($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		if(!isset($arSession['COUNT'])){
			$arSession['COUNT'] = $this->processExportDataGetCount();
		}
		#
		$arProcessParams = [
			'LIMIT' => 1000,
		];
		$this->arSessionExportData = &$arData['SESSION']['EXPORT'];
		$this->excelOpen();
		$mResult = $this->processExportData(function($arItem, $arParams){
			$this->arSessionExportData['INDEX']++;
			$arItemFields = $this->prepareFields(Json::decode($arItem['DATA']));
			if(strlen(implode(' ', array_filter($arItemFields)))){
				$intSheetIBlockId = $this->isMultisheet() ? $arItem['IBLOCK_ID'] : static::SINGLE_IBLOCK_ID;
				$arSheet = &$this->arSessionExportData['SHEETS'][$intSheetIBlockId];
				$this->excelLine($arItemFields, $arSheet['INDEX'], $arSheet['LINE']++);
			}
			unset($arItemFields);
		}, $arProcessParams);
		$this->excelSave();
		$arSession['PERCENT'] = $arSession['INDEX'] / $arSession['COUNT'] * 100;
		return $mResult;
	}
	
	/**
	 *	Step: Set columns width (after all done)
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_setColumnsWidth($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		$this->excelOpen();
		#
		foreach($arSession['SHEETS'] as $intIBlockId => $arSheet){
			foreach($this->arProfile['IBLOCKS'] as &$arIBlock){
				if(($arIBlock['IBLOCK_ID'] == $intIBlockId || $intIBlockId === static::SINGLE_IBLOCK_ID) && $arIBlock['IBLOCK_MAIN'] == 'Y'){
					if(is_array($arIBlock) && is_array($arIBlock['FIELDS'])){
						foreach($arIBlock['FIELDS'] as $strField => $mValue){
							$arFieldParams = &$arIBlock['FIELDS'][$strField]['PARAMS'];
							if(is_numeric($arFieldParams['_CUSTOM_EXCEL_WIDTH']) && isset($this->arExcelHeaders[$strField])){
								$intColumnIndex = array_search($strField, array_keys($this->arExcelHeaders));
								if(is_numeric($intColumnIndex) && $intColumnIndex >= 0){
									$strColumnString = static::getColumnString($intColumnIndex+1);
									if($arFieldParams['_CUSTOM_EXCEL_WIDTH'] == 0){
										$this->obExcel->getSheet($arSheet['INDEX'])->getColumnDimension($strColumnString)->setAutoSize(true);
									}
									elseif($arFieldParams['_CUSTOM_EXCEL_WIDTH'] > 0){
										$this->obExcel->getSheet($arSheet['INDEX'])->getColumnDimension($strColumnString)
											->setWidth($arFieldParams['_CUSTOM_EXCEL_WIDTH']);
									}
								}
							}
						}
					}
					break;
				}
			}
		}
		# Handlers
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomExcelColumnsWidth') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array($this, $arSession));
		}
		#
		$this->excelSave();
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: Excel to ZIP
	 */
	public function stepZip($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		if($this->arProfile['PARAMS']['COMPRESS_TO_ZIP']=='Y') {
			$arSession['COMPRESS_TO_ZIP'] = true;
			$arZipFiles = array(
				$arSession['EXCEL_FILE'],
			);
			$obAchiver = \CBXArchive::GetArchive($arSession['EXCEL_FILE_ZIP']);
			$obAchiver->SetOptions(array(
				'REMOVE_PATH' => pathinfo($arSession['EXCEL_FILE'], PATHINFO_DIRNAME),
			));
			$strStartFile = '';
			if($arSession['ZIP_NEXT_STEP']){
				$strStartFile = $obAchiver->GetStartFile();
			}
			$intResult = $obAchiver->Pack($arZipFiles, $strStartFile);
			unset($obAchiver);
			if($this->arProfile['PARAMS']['DELETE_EXCEL_IF_ZIP']=='Y' && is_file($arSession['EXCEL_FILE'])) {
				@unlink($arSession['EXCEL_FILE']);
			}
			if($intResult === \IBXArchive::StatusSuccess){
				$arSession['EXPORT_FILE_SIZE_ZIP'] = filesize($arSession['EXCEL_FILE_ZIP']);
				return Exporter::RESULT_SUCCESS;
			}
			elseif ($intResult === \IBXArchive::StatusError){
				return Exporter::RESULT_ERROR;
			}
			elseif($intResult === \IBXArchive::StatusContinue){
				$arSession['ZIP_NEXT_STEP'] = true;
				return Exporter::RESULT_CONTINUE;
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	private static function getColumnString($intColumnIndex){
		return Coordinate::stringFromColumnIndex($intColumnIndex);
	}
	
	/**
	 *	Open Excel file (auto create if not exists)
	 */
	protected function excelOpen(){
		if(!is_object($this->obExcel)){
			if(strlen($this->strExcelFileName) && is_file($this->strExcelFileName)){
				$this->obExcel = IOFactory::load($this->strExcelFileName);
			}
			else{
				$this->obExcel = new Spreadsheet();
			}
		}
	}
	
	/**
	 *	Write one line to excel
	 */
	protected function excelLine($arExcelFields, $intSheetIndex, $intLineIndex){
		$intSheetIndex = IntVal($intSheetIndex);
		if($intSheetIndex < static::FIRST_SHEET_INDEX){
			$intSheetIndex = static::FIRST_SHEET_INDEX;
		}
		$intColumnIndex = 1;
		foreach($arExcelFields as $strCode => $strValue){
			$strColumnString = static::getColumnString($intColumnIndex++);
			if(!Helper::isUtf()){
				$strValue = Helper::convertEncoding($strValue, 'CP1251', 'UTF-8');
			}
			$strCell = $strColumnString.$intLineIndex;
			if(is_numeric($strValue) && $strValue > 0 && IntVal($strValue) == $strValue && substr($strValue, 0, 1) != '0'){
				$this->obExcel->getSheet($intSheetIndex)->getStyle($strCell)->getNumberFormat()->setFormatCode('#');
				$this->obExcel->getSheet($intSheetIndex)->setCellValueExplicit($strCell, $strValue, 
					\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			}
			else{
				$this->obExcel->getSheet($intSheetIndex)->setCellValue($strCell, $strValue);
			}
			# Handlers
			foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomExcelSetValue') as $arHandler) {
				ExecuteModuleEventEx($arHandler, array($this, $intColumnIndex, $strColumnString, $strValue, $arExcelFields, $intSheetIndex, $intLineIndex, $strCode));
			}
		}
	}
	
	/**
	 *	Save Excel file
	 */
	protected function excelSave(){
		$obWriter = IOFactory::createWriter($this->obExcel, $this->getFormat(true));
		$obWriter->save($this->strExcelFileName);
		unset($obWriter, $this->obExcel);
	}
	
	/**
	 *	Is multisheet using?
	 */
	private function isMultisheet(){
		return $this->bAdditionalSettings && $this->arProfile['PARAMS']['CUSTOM_EXCEL']['MULTISHEET'] == 'Y';
	}
	
	/**
	 *	Show messages in profile edit
	 */
	public function showMessages(){
		parent::showMessages();
		print Helper::showNote(static::getMessage('LARGE_FILE_NOTICE'), true);
	}

}

?>