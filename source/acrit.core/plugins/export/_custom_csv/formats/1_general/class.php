<?
/**
 * Acrit Core: Custom CSV format
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
	\Acrit\Core\Export\ExportDataTable as ExportData;

Loc::loadMessages(__FILE__);

class CustomCsvGeneral extends CustomCsv {
	
	CONST DATE_UPDATED = '2019-01-10';
	
	CONST ROLE_URL = 'URL';
	
	CONST DELETE_MODE_NO = '';
	CONST DELETE_MODE_SIMPLE = 'DELETE';
	CONST DELETE_MODE_ATTR = 'ATTR';

	protected static $bSubclass = true;
	
	protected $strFileExt;
	
	protected $arSeparators;
	protected $arLineTypes;
	
	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
		#
		$this->arSeparators = array(
			'COMMA' => ',',
			'SEMICOLON' => ';',
			'TAB' => "\t",
			'SPACE' => ' ',
		);
		foreach($this->arSeparators as $strKey => $strValue){
			$this->arSeparators[$strKey] = array(
				'NAME' => static::getMessage('SEPARATOR_'.$strKey),
				'VALUE' => $strValue,
			);
		}
		#
		$this->arLineTypes = array(
			'CRLF' => "\r\n", //windows
			'LF' => "\n", // uniq
			'CR' => "\r", // mac
		);
		foreach($this->arLineTypes as $strKey => $strValue){
			$this->arLineTypes[$strKey] = array(
				'NAME' => static::getMessage('LINE_TYPE_'.$strKey),
				'VALUE' => $strValue,
			);
		}
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
			'DIV' => 'csv_structure',
			'TAB' => static::getMessage('TAB_CSV_SETTINGS_NAME'),
			'TITLE' => static::getMessage('TAB_CSV_SETTINGS_TITLE'),
			'SORT' => 5,
			'FILE' => __DIR__.'/tabs/csv_settings.php',
		);
		return $arResult;
	}
	
	/**
	 *	Get custom subtabs for profile edit
	 */
	public function getAdditionalSubTabs($intProfileID, $intIBlockID){
		$arResult = array();
		/*
		$arResult[] = array(
			'DIV' => 'csv_structure_sub',
			'TAB' => static::getMessage('SUBTAB_CSV_STRUCTURE_NAME'),
			'TITLE' => static::getMessage('SUBTAB_CSV_STRUCTURE_TITLE'),
			'SORT' => 5,
			'FILE' => __DIR__.'/subtabs/csv_structure.php',
		);
		$arResult[] = array(
			'DIV' => 'csv_settings',
			'TAB' => static::getMessage('SUBTAB_CSV_SETTINGS_NAME'),
			'TITLE' => static::getMessage('SUBTAB_CSV_SETTINGS_TITLE'),
			'SORT' => 7,
			'FILE' => __DIR__.'/subtabs/csv_settings.php',
		);
		*/
		return $arResult;
	}
	
	/* END OF BASE STATIC METHODS */
	
	/**
	 *	Get all separators
	 */
	public function getSeparators() {
		return $this->arSeparators;
	}
	
	/**
	 *	Get line types
	 */
	public function getLineTypes($strValue=null) {
		if(!is_null($strValue) && is_array($this->arLineTypes[$strValue])) {
			return $this->arLineTypes[$strValue]['VALUE'];
		}
		return $this->arLineTypes;
	}
	public function getLineType($strValue) {
		$arLineTypes = $this->getLineTypes();
		if(!strlen($strValue) || !is_array($this->arLineTypes[$strValue])) {
			$strValue = key($arLineTypes);
		}
		return $arLineTypes[$strValue]['VALUE'];
	}
	
	public function getDefaultExportFilename(){
		return 'file.csv';
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
		$this->setAvailableExtension('csv');
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
						<label for="acrit_exp_plugin_csv_filename">
							<b><?=static::getMessage('SETTINGS_FILE');?>:</b>
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?\CAdminFileDialog::ShowScript(Array(
							'event' => 'AcritExpPluginCsvFilenameSelect',
							'arResultDest' => array('FUNCTION_NAME' => 'acrit_exp_plugin_csv_filename_select'),
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
						function acrit_exp_plugin_csv_filename_select(File,Path,Site){
							var FilePath = Path+'/'+File;
							FilePath = FilePath.replace(/\/\//g, '/');
							$('#acrit_exp_plugin_csv_filename').val(FilePath);
						}
						</script>
						<table class="acrit-exp-plugin-settings-fileselect">
							<tbody>
								<tr>
									<td><input type="text" name="PROFILE[PARAMS][EXPORT_FILE_NAME]" 
										id="acrit_exp_plugin_csv_filename" data-role="export-file-name"
										value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['EXPORT_FILE_NAME']);?>" size="40" 
										placeholder="<?=static::getMessage('SETTINGS_FILE_PLACEHOLDER');?>" /></td>
									<td><input type="button" value="..." onclick="AcritExpPluginCsvFilenameSelect()" /></td>
									<td>
										&nbsp;
										<?=$this->showFileOpenLink();?>
										<?if($this->arProfile['PARAMS']['COMPRESS_TO_ZIP']=='Y'):?>
											<?=$this->showFileOpenLink(Helper::changeFileExt($this->getExportFileName(), 'zip'), 'Zip');?>
										<?endif?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_ENCODING_HINT'));?>
						<label for="acrit_exp_plugin_encoding">
							<b><?=static::getMessage('SETTINGS_ENCODING');?>:</b>
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?
						$arEncodings = Helper::getAvailableEncodings();
						$arEncodings = array(
							'REFERENCE' => array_values($arEncodings),
							'REFERENCE_ID' => array_keys($arEncodings),
						);
						print SelectBoxFromArray('PROFILE[PARAMS][ENCODING]', $arEncodings,
							$this->arProfile['PARAMS']['ENCODING'], '', 'id="acrit_exp_plugin_encoding"');
						?>
					</td>
				</tr>
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
				<tr id="tr_DELETE_CSV_IF_ZIP">
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_DELETE_CSV_IF_ZIP_HINT'));?>
						<label for="acrit_exp_plugin_delete_csv_if_zip">
							<?=static::getMessage('SETTINGS_DELETE_CSV_IF_ZIP');?>:
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input name="PROFILE[PARAMS][DELETE_CSV_IF_ZIP]" type="hidden" value="N"/>
						<input name="PROFILE[PARAMS][DELETE_CSV_IF_ZIP]" type="checkbox" value="Y" 
							<?if($this->arProfile['PARAMS']['DELETE_CSV_IF_ZIP']=='Y'):?>checked="checked"<?endif?>
						id="acrit_exp_plugin_delete_csv_if_zip" />
					</td>
				</tr>
			</tbody>
		</table>
		<script>
		$('[data-role="settings-<?=static::getCode();?>"] #tr_ZIP input[type=checkbox]').change(function(){
			var row = $('[data-role="settings-<?=static::getCode();?>"] #tr_DELETE_CSV_IF_ZIP');
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
		$arFields = $this->parseFields();
		foreach($arFields as $strFieldCode => $strFieldName){
			$intSort++;
			$arFieldParams = $this->arProfile['IBLOCKS'][$intIBlockID]['FIELDS'][$strFieldCode]['PARAMS'];
			if(!is_array($arFieldParams)){
				$arFieldParams = array();
			}
			$arResult[] = new Field(array(
				'CODE' => $strFieldCode,
				'DISPLAY_CODE' => $strFieldName,
				'NAME' => strlen($arFieldParams['_CUSTOM_CSV_NAME']) ? $arFieldParams['_CUSTOM_CSV_NAME'] : $strFieldName,
				'SORT' => $intSort*10,
				'REQUIRED' => $arFieldParams['_CUSTOM_CSV_REQUIRED'] == 'Y' ? true : false,
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
		$this->addUtmFields($arResult, $intSort*10+1, false, false, $bAdmin?true:false);
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
	public function parseFields(){
		$arResult = array();
		#
		$strFields = $this->arProfile['PARAMS']['CUSTOM_CSV_FIELDS'];
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
	
	/**
	 *	Process single element
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		
		# event handlers OnCustomCsv
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomCsv') as $arHandler) {
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
			switch($arFieldParams['_CUSTOM_CSV_ROLE']){
				case static::ROLE_URL:
					if(strlen($arFields[$strField]) && $this->arParams['CUSTOM_CSV_ADD_UTM'] == 'Y'){
						$this->addUtmToUrl($arFields[$strField], $arFields, false);
					}
					break;
			}
		}
		
		# Process values
		$arCsvFields = $this->parseFields();
		$arCsvValues = array();
		foreach($arCsvFields as $strFieldCode => $strFieldName){
			$arCsvValues[$strFieldCode] = $this->escapeCsv($arFields[$strFieldCode], $this->arParams['CUSTOM_CSV_EXTRA_QUOTES'] != 'N');
		}
		$arCsvValuesUnique = array_unique(array_values($arCsvValues));
		if(count($arCsvValuesUnique) === 1 && (reset($arCsvValuesUnique) === '' || reset($arCsvValuesUnique) === '""')){
			$arCsvValues = array();
		}
		
		# Determine delimiter
		$strDelimiter = $arProfile['PARAMS']['CUSTOM_CSV_SEPARATOR'];
		if(!array_key_exists($strDelimiter, $this->arSeparators)){
			$strDelimiter = key($this->arSeparators);
		}
		$strDelimiter = $this->arSeparators[$strDelimiter]['VALUE'];
		
		if(implode('', $arCsvValues) == ''){
			$arCsvValues = array();
		}
		
		# Build result
		$arResult = array(
			'TYPE' => 'CSV',
			'DATA' => implode($strDelimiter, $arCsvValues),
			'CURRENCY' => '',
			'SECTION_ID' => reset($arElementSections),
			'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
			'DATA_MORE' => array(),
		);
		
		# Event handlers OnCustomCsvResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomCsvResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arFields, $arCsvFields, $arProfile, $intIBlockID, $arElement));
		}
		
		# Ending..
		unset($intProfileID, $intElementID, $arCsvFields, $arCsvValues, $strCurrency, $strCsv);
		return $arResult;
	}
	
	/**
	 *	Escape single value for CSV
	 */
	protected function escapeCsv($strValue, $bExtraQuotes=false){
		if(is_numeric($strValue)){
			if($bExtraQuotes){
				$strValue = '"'.$strValue.'"';
			}
		}
		elseif(strlen($strValue)){
			$strValue = str_replace('"', '""', $strValue);
			if($bExtraQuotes){
				$strValue = '"'.$strValue.'"';
			}
			else{
				$bQuot = (strpos($strValue, '"') !== false) || (strpos($strValue, ' ') !== false) || (strpos($strValue, "\n") !== false);
				if($bQuot){
					$strValue = '"'.$strValue.'"';
				}
			}
		}
		else{
			if($bExtraQuotes){
				$strValue = '""';
			}
		}
		return $strValue;
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
		<?=$this->showFileOpenLink($arSession['EXPORT']['CSV_FILE_URL_ZIP'], static::getMessage('RESULT_FILE_ZIP'));?>
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
		if($this->arProfile['PARAMS']['COMPRESS_TO_ZIP'] == 'Y') {
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
		$strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
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
		$arSession = &$arData['SESSION']['EXPORT'];
		$bIsCron = $arData['IS_CRON'];
		#
		$strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		#
		if(!isset($arSession['CSV_FILE'])){
			#$strTmpDir = Profile::getTmpDir($intProfileID);
			$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
			$strTmpFile = pathinfo($strExportFilename, PATHINFO_BASENAME).'.tmp';
			$arSession['CSV_FILE_URL'] = $strExportFilename;
			$arSession['CSV_FILE'] = $_SERVER['DOCUMENT_ROOT'].$strExportFilename;
			$arSession['CSV_FILE_TMP'] = $strTmpDir.'/'.$strTmpFile;
			#
			if($arData['PROFILE']['PARAMS']['COMPRESS_TO_ZIP'] == 'Y'){
				$arSession['CSV_FILE_ZIP'] = Helper::changeFileExt($_SERVER['DOCUMENT_ROOT'].$strExportFilename, 'zip');
				$arSession['CSV_FILE_URL_ZIP'] = Helper::changeFileExt($strExportFilename, 'zip');
			}
			if(is_file($arSession['CSV_FILE_TMP'])){
				unlink($arSession['CSV_FILE_TMP']);
			}
			touch($arSession['CSV_FILE_TMP']);
			unset($strTmpDir, $strTmpFile);
		}
		
		#
		$strFile = $arData['SESSION']['EXPORT']['CSV_FILE_TMP'];
		
		# Export header
		if($arSession['HEADER_WRITEN'] !== false && $arData['PROFILE']['PARAMS']['CUSTOM_CSV_ADD_HEADER'] != 'N') {
			$this->stepExport_writeCsvHeader($intProfileID, $arData);
		}
		
		# Export items
		if($arSession['ITEMS_WRITEN'] !== false) {
			$this->stepExport_writeCsvLines($intProfileID, $arData);
		}
		
		# Save file
		if(is_file($arSession['CSV_FILE'])){
			unlink($arSession['CSV_FILE']);
		}
		if(!Helper::createDirectoriesForFile($arSession['CSV_FILE'])){
			$strErrorMessage = Loc::getMessage('ACRIT_EXP_ERROR_CREATE_DIRECORY', array(
				'#DIR#' => Helper::getDirectoryForFile($arSession['CSV_FILE']),
			));
			Log::getInstance($this->strModuleId)->add($strErrorMessage);
			print Helper::showError($strErrorMessage);
			return Exporter::RESULT_ERROR;
		}
		if(is_file($arSession['CSV_FILE'])){
			@unlink($arSession['CSV_FILE']);
		}
		if(!@rename($arSession['CSV_FILE_TMP'], $arSession['CSV_FILE'])){
			@unlink($arSession['CSV_FILE_TMP']);
			$strErrorMessage = Loc::getMessage('ACRIT_EXP_FILE_NO_PERMISSIONS', array(
				'#FILE#' => $arSession['CSV_FILE'],
			));
			Log::getInstance($this->strModuleId)->add($strErrorMessage);
			print Helper::showError($strErrorMessage);
			return Exporter::RESULT_ERROR;
		}
		$arSession['EXPORT_FILE_SIZE_CSV'] = filesize($arSession['CSV_FILE']);
			
		#
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: Export, write header
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeCsvHeader($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		$strFile = $arData['SESSION']['EXPORT']['CSV_FILE_TMP'];
		
		#
		$strCsvHeader = '';
		
		# Determine fields
		$arCsvFields = $this->parseFields();
		foreach($arCsvFields as $strKey => $strFieldName){
			$arCsvFields[$strKey] = $this->escapeCsv($strFieldName, $arData['PROFILE']['PARAMS']['CUSTOM_CSV_EXTRA_QUOTES'] != 'N');
		}
		
		# Determine delimiter
		$strDelimiter = $arData['PROFILE']['PARAMS']['CUSTOM_CSV_SEPARATOR'];
		if(!array_key_exists($strDelimiter, $this->arSeparators)){
			$strDelimiter = key($this->arSeparators);
		}
		$strDelimiter = $this->arSeparators[$strDelimiter]['VALUE'];
		
		# Determine EOL symbol
		$strEOL = $this->getLineType($arData['PROFILE']['PARAMS']['CUSTOM_CSV_LINE_TYPE']);
		
		# Write
		$strCsvHeader = implode($strDelimiter, $arCsvFields).$strEOL;
		$strCsvHeader = Helper::convertEncodingTo($strCsvHeader, $arData['PROFILE']['PARAMS']['ENCODING']);
		return !!file_put_contents($strFile, $strCsvHeader, FILE_APPEND);
	}
	
	/**
	 *	Step: Export, write items
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeCsvLines($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		$strFile = $arData['SESSION']['EXPORT']['CSV_FILE_TMP'];
		# Determine EOL symbol
		$strEOL = $this->getLineType($arData['PROFILE']['PARAMS']['CUSTOM_CSV_LINE_TYPE']);
		#
		$intOffset = 0;
		while(true){
			$intLimit = 5000;
			$strSortOrder = ToUpper($arData['PROFILE']['PARAMS']['SORT_ORDER']);
			if(!in_array($strSortOrder, array('ASC', 'DESC'))){
				$strSortOrder = 'ASC';
			}
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'!TYPE' => ExportData::TYPE_DUMMY,
				),
				'order' => array(
					'SORT' => $strSortOrder,
					'ELEMENT_ID' => 'ASC',
				),
				'select' => array(
					'IBLOCK_ID',
					'ELEMENT_ID',
					'SECTION_ID',
					'TYPE',
					'DATA',
				),
				'limit' => $intLimit,
				'offset' => $intOffset * $intLimit,
			];
			#$resItems = ExportData::getList($arQuery);
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$strCsv = '';
			$intCount = 0;
			while($arItem = $resItems->fetch()){
				$intCount++;
				if(strlen($arItem['DATA'])) {
					$strCsv .= $arItem['DATA'].$strEOL;
				}
			}
			$arData['SESSION']['EXPORT']['INDEX'] += $intCount;
			$strCsv = Helper::convertEncodingTo($strCsv, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strCsv, FILE_APPEND);
			if($intCount < $intLimit){
				break;
			}
			$intOffset++;
		}
	}
	
	/**
	 *	Step: CSV to ZIP
	 */
	public function stepZip($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		if($arData['PROFILE']['PARAMS']['COMPRESS_TO_ZIP']=='Y') {
			$arSession['COMPRESS_TO_ZIP'] = true;
			$arZipFiles = array(
				$arSession['CSV_FILE'],
			);
			$obAchiver = \CBXArchive::GetArchive($arSession['CSV_FILE_ZIP']);
			$obAchiver->SetOptions(array(
				'REMOVE_PATH' => pathinfo($arSession['CSV_FILE'], PATHINFO_DIRNAME),
			));
			$strStartFile = '';
			if($arSession['ZIP_NEXT_STEP']){
				$strStartFile = $obAchiver->GetStartFile();
			}
			$intResult = $obAchiver->Pack($arZipFiles, $strStartFile);
			unset($obAchiver);
			if($arData['PROFILE']['PARAMS']['DELETE_CSV_IF_ZIP']=='Y' && is_file($arSession['CSV_FILE'])) {
				@unlink($arSession['CSV_FILE']);
			}
			if($intResult === \IBXArchive::StatusSuccess){
				$arSession['EXPORT_FILE_SIZE_ZIP'] = filesize($arSession['CSV_FILE_ZIP']);
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

}

?>