<?
/**
 * Acrit core
 * @package acrit.core
 * @copyright 2019 Acrit
 */
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Application,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
	\Acrit\Core\Export\CurrencyConverter\Base as CurrencyConverterBase,
	\Acrit\Core\Xml,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

/**
 * Base interface for universal plugin
 */
abstract class UniversalPlugin extends Plugin{
	
	const EOL = "\n";
	const UTF8 = 'UTF-8';
	const CP1251 = 'windows-1251';
	const SETTINGS_HIDDEN = '#SETTINGS_HIDDEN#';
	
	protected static $strCode = 'UNIVERSAL_PLUGIN';
	protected static $arCacheCode = [];
	
	protected static $arSettings = array();
	
	protected $arFileSuffix = array(
		'CATEGORIES' => 'categories',
		'CURRENCIES' => 'currencies',
	);
	
	protected $arUtmAll = array(
		'utm_source',
		'utm_medium',
		'utm_campaign',
		'utm_content',
		'utm_term',
	);
	
	protected $bCron = false;
	
	# Basic settings
	protected $bOffersPreprocess = false;
	protected $bStepByStep = false;
	protected $bPhased = false;
	protected $bAdditionalFields = false;
	protected $bCategoriesExport = false;
	protected $bCategoriesUpdate = false;
	protected $bCategoriesStrict = false;
	protected $bCategoriesList = false;
	protected $bHideCategoriesUpdateButton = false;
	protected $strCategoriesUrl = null;
	protected $strCategoriesFilename = 'categories.txt';
	protected $strCategoriesTimeout = 5;
	protected $bCurrenciesExport = false;
	protected $arSupportedCurrencies = array('RUB');
	protected $strDefaultDirectory = '/upload/acrit.core';
	protected $strDefaultFilename = 'file.xml';
	protected $arSupportedFormats = array('XML', 'CSV', 'XLS', 'XLSX', 'JSON');
	protected $bApi = false;
	protected $strCurrentFormat = '';
	protected $strFileExt = '*';
	protected $arSupportedEncoding = array(self::UTF8, self::CP1251);
	# XML settings
	protected $strXmlItemElement = 'item';
	protected $strXmlItemOffer = '';
	protected $intXmlDepthItems = 1;
	protected $arXmlMultiply = array();
	# JSON settings
	protected $arJsonTranspose = array();
	# XLSX/XLS settings
	protected $strExcelFileName = null;
	protected $obExcel = null;
	protected $intColStart = 1;
	protected $intRowStart = 1;
	# Other export settings
	protected $intExportPerStep = 1000;
	protected $bZip = false;
	protected $bZipDeleteOriginal = false;
	protected $bTarGz = false;
	protected $strZipFilename = '';
	protected $arFieldsWithUtm = [];
	protected $bEscapeUtm = true;
	protected $bAllCData = false;
	protected $strCategoryTag = 'category';
	protected $bCategoryNameInAttribute = false;
	# Misc settings
	protected $bAdmin = false;
	
	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
		$this->strDefaultDirectory = parent::getDefaultDirectory();
		$this->bCron = Exporter::getInstance($strModuleId)->isCron();
		if(!strlen($this->strXmlItemElement)){
			$this->strXmlItemElement = 'offer';
		}
		if(!strlen($this->strXmlItemOffer)){
			$this->strXmlItemOffer = $this->strXmlItemElement;
		}
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Get plugin unique code ([A-Z_]+)
	 *	We generate plugin code from items class name: YandexRealty => YANDEX_REALTY, AbcAbcAbc => ABC_ABC_ABC
	 */
	public static function getCode() {
		$strClass = get_called_class();
		if(array_key_exists($strClass, static::$arCacheCode)){
			return static::$arCacheCode[$strClass];
		}
		$strCode = end(explode('\\', $strClass));
		$strCode = preg_replace('#([a-z]{1})([A-Z]{1})#', '$1_$2', $strCode);
		$strCode = toUpper($strCode);
		static::$arCacheCode[$strClass] = $strCode;
		return $strCode;
	}
	
	/**
	 * Get plugin description.
	 */
	public static function getDescription() {
		$strHtml = '';
		if(static::isSubClass()){
			$obThis = new static(static::$strStaticModuleId);
			$strHtml = $obThis->includeFile('.description.php', function($strFile){
				return file_get_contents($strFile);
			});
			unset($obThis);
			if(!Helper::isUtf()){
				$strHtml = Helper::convertEncoding($strHtml, static::UTF8, static::CP1251);
			}
		}
		return $strHtml;
	}
	
	/**
	 * Get plugin example
	 */
	public static function getExample() {
		$strHtml = '';
		if(static::isSubClass()){
			$obThis = new static(static::$strStaticModuleId);
			$arFormats = array();
			foreach($obThis->arSupportedFormats as $strFormat){
				$strFile = static::getFolder().'/.example.'.toLower($strFormat);
				if(is_file($strFile) && filesize($strFile)) {
					$arFormats[$strFormat] = file_get_contents($strFile);
				}
			}
			unset($obThis);
			ob_start();
			require __DIR__.'/../../include/export/plugin_example/template.php';
			$strHtml = trim(ob_get_clean());
			if(!Helper::isUtf()){
				$strHtml = Helper::convertEncoding($strHtml, static::UTF8, static::CP1251);
			}
			unset($obThis, $arFormats);
		}
		return $strHtml;
	}
	
	/**
	 *	Set profile array
	 */
	public function setProfileArray(array &$arProfile){
		parent::setProfileArray($arProfile);
		$this->strCurrentFormat = $arProfile['PARAMS']['EXPORT_FORMAT'];
		$this->handler('onSetProfileArray');
	}
	
	/**
	 *	Get || set config
	 */
	public function config($strKey=null, $mValue=null){
		
	}
	
	/**
	 *	Execute custom handler
	 */
	protected function handler($strHandler, $arArguments=null){
		$mResult = null;
		$arArguments = is_array($arArguments) ? $arArguments : array();
		# Internal event handler
		if(method_exists($this, $strHandler)){
			$mResult = call_user_func_array(array($this, $strHandler), $arArguments);
		}
		# External event handler
		$mResultSet = false;
		$arEventHandlers = EventManager::getInstance()->findEventHandlers($this->strModuleId, $strHandler);
		if(!empty($arEventHandlers)){
			$arEventArguments = array_merge(array($this), $arArguments);
			foreach($arEventHandlers as $arEventHandler){
				$mEventResult = ExecuteModuleEventEx($arEventHandler, $arEventArguments);
				if(!$mResultSet){
					$mResult = $mEventResult;
				}
				$mResultSet = true;
			}
			unset($arEventHandlers, $arEventHandler, $arEventArguments);
		}
		return $mResult;
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 *	Is it need to offers preprocess? (see plugin 'sorokonogka')
	 */
	public function isOffersPreprocess(){
		return $this->bOffersPreprocess;
	}
	
	/**
	 *	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported(){
		return $this->bAdditionalFields;
	}
	
	/**
	 *	Are categories export?
	 */
	public function areCategoriesExport(){
		return $this->bCategoriesExport;
	}
	
	/**
	 *	Are categories updateable?
	 *	Update available if (areCategoriesExport() || areCategoriesUpdate());
	 */
	public function areCategoriesUpdate(){
		return $this->bCategoriesUpdate;
	}
	
	/**
	 *	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict(){
		return $this->bCategoriesStrict;
	}
	
	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){
		return $this->bCategoriesList;
	}
	
	/**
	 *	Hide categories update button
	 */
	public function hideCategoriesUpdateButton(){
		return $this->bHideCategoriesUpdateButton;
	}
	
	/**
	 *	Get categories list
	 */
	public function getCategoriesList($intProfileID){
		$strFileName = $this->getCategoriesCacheFile();
		if(!is_file($strFileName) || !filesize($strFileName)) {
			$this->updateCategories($intProfileID);
		}
		if(is_file($strFileName) && filesize($strFileName)) {
			$arResult = file($strFileName, FILE_IGNORE_NEW_LINES  | FILE_SKIP_EMPTY_LINES);
			#foreach($arResult as $key => $value) {
			#	$arResult[$key] = str_replace();
			#}
			return $arResult;
		}
		return false;
	}
	
	/**
	 *	Update categories from server
	 *	(using parameters: $this->strCategoriesUrl, $this->strCategoriesTimeout, $this->strCategoriesFilename)
	 */
	public function updateCategories($intProfileID){
		$bSuccess = false;
		$strFileContent = HttpRequest::get($this->strCategoriesUrl, array('TIMEOUT' => $this->strCategoriesTimeout));
		if($this->areDownloadedCategoriesCorrect($strFileContent)){
			$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$this->intProfileId]);
			$strTmpFile = $strTmpDir.'/'.pathinfo($this->strCategoriesUrl, PATHINFO_BASENAME);
			if(file_put_contents($strTmpFile, $strFileContent)){
				$strCategoriesResult = $this->processUpdatedCategories($strTmpFile);
				if($strCategoriesResult === false){
					$strCategoriesResult = implode("\n", file($strTmpFile, FILE_IGNORE_NEW_LINES  | FILE_SKIP_EMPTY_LINES));
					$bResponseUtf = HttpRequest::isResponseUtf();
					if($bResponseUtf && !Helper::isUtf()){
						$strCategoriesResult = Helper::convertEncoding($strCategoriesResult, 'UTF-8', 'CP1251');
					}
					elseif(!$bResponseUtf && Helper::isUtf()){
						$strCategoriesResult = Helper::convertEncoding($strCategoriesResult, 'CP1251', 'UTF-8');
					}
				}
				else{
					$strCategoriesResult = trim($strCategoriesResult);
				}
				if(strlen($strCategoriesResult)){
					$strCategoriesCacheFile = $this->getCategoriesCacheFile();
					if(is_file($strCategoriesCacheFile)){
						unlink($strCategoriesCacheFile);
					}
					if(file_put_contents($strCategoriesCacheFile, $strCategoriesResult)){
						$bSuccess = true;
					}
					else{
						Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES', array('#FILE#' => $strCategoriesCacheFile)), $this->intProfileId);
					}
				}
				else{
					Log::getInstance($this->strModuleId)->add(static::getMessage('CATEGORIES_ARE_EMPTY', array('#URL#' => $this->strCategoriesUrl)), $this->intProfileId);
				}
				@unlink($strTmpFile);
				unset($strCategoriesResult, $strFileContent);
			}
			else{
				Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES_TMP', array('#FILE#' => $strTmpFile)), $this->intProfileId);
			}
		}
		else {
			Log::getInstance($this->strModuleId)->add(static::getMessage('CATEGORIES_EMPTY_ANSWER', array('#URL#' => $this->strCategoriesUrl)), $this->intProfileId);
		}
		return $bSuccess;
	}
	
	/**
	 *	Handle categories update
	 *	Method must process tmp file and return string for all categories, separated by "\n"
	 *	Charset must be the same as the bitrix site charset (see BX_UTF constant)
	 *	If return is false, download data will be saved as is, with charset convert (if it need)
	 */
	protected function processUpdatedCategories($strTmpFile){
		return false;
	}
	
	/**
	 *	Check download correct
	 */
	protected function areDownloadedCategoriesCorrect($strFileContent){
		return !!strlen($strFileContent);
	}
	
	/**
	 *	Get dir for categories cache
	 */
	protected function getCategoriesCacheDir($bAbsolute=false){
		$strResult = '/upload/'.$this->strModuleId.'/categories/'.$this->intProfileId;
		if($bAbsolute){
			$strResult = $_SERVER['DOCUMENT_ROOT'].$strResult;
		}
		if(!is_dir($strResult)){
			mkdir($strResult, BX_DIR_PERMISSIONS, true);
		}
		return $strResult;
	}
	
	/**
	 *	Get filename for categories cache
	 */
	protected function getCategoriesCacheFile(){
		return $this->getCategoriesCacheDir(true).'/'.$this->strCategoriesFilename;
	}
	
	/**
	 *	Get categories date update
	 */
	public function getCategoriesDate(){
		$strFileName = $this->getCategoriesCacheFile();
		return is_file($strFileName) ? filemtime($strFileName) : false;
	}
	
	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = array();
		return $arResult;
	}
	
	/**
	 *	Get custom sub tabs for profile edit
	 */
	public function getAdditionalSubTabs($intProfileID, $intIBlockID){
		$arResult = array();
		return $arResult;
	}
	
	/**
	 *	Get list of supported currencies
	 */
	public function getSupportedCurrencies(){
		return $this->arSupportedCurrencies;
	}
	
	/**
	 *	Is step-by-step export?
	 */
	public function isStepByStepMode(){
		return $this->bStepByStep;
	}
	
	/**
	 *	Is phased export?
	 */
	public function isPhased(){
		return $this->bPhased;
	}
	
	/**
	 *	Get default directory for export
	 */
	public function getDefaultDirectory(){
		return $this->strDefaultDirectory;
	}
	
	/**
	 *	Get default directory for export
	 */
	public function getDefaultExportFilename(){
		return $this->strDefaultFilename;
	}
	
	/**
	 *	Set export file extension
	 */
	protected function setFileExt($strFileExt){
		$this->strFileExt = $strFileExt;
	}
	
	/**
	 *	Show plugin settings
	 */
	public function showSettings(){
		$arSettings = &$this->arSettings;
		$arSettings['FILENAME'] = $this->showSettingsFilename();
		$arSettings['FORMAT'] = $this->showSettingsFormat();
		$arSettings['ENCODING'] = $this->showSettingsEncoding();
		$arSettings['ARCHIVE'] = $this->showSettingsArchive();
		$this->handler('onUpShowSettings', array(&$arSettings));
		# Remove empty
		foreach($arSettings as $key => $arSettingsItem){
			if(!strlen($arSettingsItem)){
				unset($arSettings[$key]);
			}
		}
		#
		foreach($arSettings as $key => $arSettingsItem){
			if(!is_array($arSettingsItem)){
				$arSettingsItem = array(
					'HTML' => $arSettingsItem,
				);
			}
			if(!isset($arSettingsItem['NAME'])){
				$arSettingsItem['NAME'] = static::getMessage('SETTINGS_NAME_'.$key);
			}
			if(!isset($arSettingsItem['HINT'])){
				$arSettingsItem['HINT'] = static::getMessage('SETTINGS_HINT_'.$key);
			}
			$arSettingsItem['HIDDEN'] = strpos($arSettingsItem['HTML'], static::SETTINGS_HIDDEN) !== false;
			if($arSettings['HIDDEN']){
				$arSettingsItem['HTML'] = str_replace(static::SETTINGS_HIDDEN, '', $arSettingsItem['HTML']);
			}
			$arSettings[$key] = $arSettingsItem;
		}
		if(empty($arSettings)){
			return;
		}
		$arColumn = array_column($arSettings, 'SORT');
		$bSort = !empty($arColumn);
		if($bSort){
			uasort($arSettings, function($a, $b){
				$a = is_numeric($a['SORT']) ? $a['SORT'] : 100;
				$b = is_numeric($b['SORT']) ? $b['SORT'] : 100;
				if ($a == $b) {
					return 0;
				}
				return ($a < $b) ? -1 : 1;
			});
		}
		#
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
				<?foreach($arSettings as $key => $arSettingsItem):?>
					<?
					$strID = $this->getInputID($key);
					$bHeading = isset($arSettingsItem['IS_HEADER']);
					$strCssName = strlen($arSettingsItem['CSS_NAME']) ? ' style="'.$arSettingsItem['CSS_NAME'].'"' : '';
					$strCssName = strlen($arSettingsItem['CSS_VALUE']) ? ' style="'.$arSettingsItem['CSS_VALUE'].'"' : '';
					?>
					<tr<?if($bHeading):?> class="heading"<?endif?><?if($arSettingsItem['HIDDEN']):?> style="display:block; height:0; overflow:hidden; position:absolute; width:0;"<?endif?>>
						<?if($arSettingsItem['FULL'] || $bHeading):?>
							<td colspan="2" class="adm-detail-content-cell-r"<?=$strCssValue;?>>
								<?=$arSettingsItem['HTML'];?>
							</td>
						<?else:?>
							<td width="40%" class="adm-detail-content-cell-l"<?=$strCssName;?>>
								<?if(strlen($arSettingsItem['HINT'])):?>
									<?=Helper::ShowHint($arSettingsItem['HINT']);?>
								<?endif?>
								<label for="<?=$strID;?>">
									<?if(strlen($arSettingsItem['NAME'])):?>
										<?=$arSettingsItem['NAME'];?>:
									<?endif?>
								</label>
							</td>
							<td width="60%" class="adm-detail-content-cell-r"<?=$strCssValue;?>>
								<?=$arSettingsItem['HTML'];?>
							</td>
						<?endif?>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
	protected function getInputID($strID){
		return 'acrit_exp_plugin_settings_'.toLower($strID);
	}
	protected function showSettingsFilename(){
		ob_start();
		\CAdminFileDialog::showScript(Array(
			'event' => 'AcritExpPluginFilenameSelect',
			'arResultDest' => array('FUNCTION_NAME' => 'acritExpPluginFilenameSelectCallback'),
			'arPath' => array(),
			'select' => 'F',
			'operation' => 'S',
			'showUploadTab' => true,
			'showAddToMenuTab' => false,
			'fileFilter' => $this->strFileExt,
			'allowAllFiles' => true,
			'saveConfig' => true,
		))
		?>
		<script>
		function acritExpPluginFilenameSelectCallback(File,Path,Site){
			$('#<?=$this->getInputID('FILENAME');?>').val(Path+'/'+File);
		}
		</script>
		<table class="acrit-exp-plugin-settings-fileselect">
			<tbody>
				<tr>
					<td><input type="text" name="PROFILE[PARAMS][EXPORT_FILE_NAME]" size="40" 
						id="<?=$this->getInputID('FILENAME');?>" data-role="export-file-name"
						value="<?=htmlspecialcharsbx($this->arParams['EXPORT_FILE_NAME']);?>"
						placeholder="<?=static::getMessage('SETTINGS_FILE_PLACEHOLDER');?>" /></td>
					<td><input type="button" value="..." onclick="AcritExpPluginFilenameSelect()" /></td>
					<td>
						&nbsp;
					</td>
					<td>
						<?=$this->showFileOpenLink();?>
					</td>
				</tr>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
	protected function showSettingsFormat(){
		$arAvailableFormats = array_combine($this->arSupportedFormats, $this->arSupportedFormats);
		foreach($arAvailableFormats as $strFormatCode => $strFormatName){
			$arAvailableFormats[$strFormatCode] = static::getMessage('FORMAT_'.toUpper($strFormatCode));
			if(!strlen($arAvailableFormats[$strFormatCode])){
				unset($arAvailableFormats[$strFormatCode]);
			}
		}
		$strHidden = (count($arAvailableFormats) <= 1 ? static::SETTINGS_HIDDEN : '');
		$arAvailableFormats = array(
			'REFERENCE' => array_values($arAvailableFormats),
			'REFERENCE_ID' => array_keys($arAvailableFormats),
		);
		return SelectBoxFromArray('PROFILE[PARAMS][EXPORT_FORMAT]', $arAvailableFormats,
			$this->arParams['EXPORT_FORMAT'], '', 'id="'.$this->getInputID('EXPORT_FORMAT').'"').
			$strHidden;
	}
	protected function showSettingsEncoding(){
		$arEncodings = Helper::getAvailableEncodings();
		foreach($arEncodings as $strEncodingCode => $strEncodingName){
			if(!in_array($strEncodingCode, $this->arSupportedEncoding)){
				unset($arEncodings[$strEncodingCode]);
			}
		}
		$strHidden = (count($arAvailableFormats) <= 1 ? static::SETTINGS_HIDDEN : '');
		$arEncodings = array(
			'REFERENCE' => array_values($arEncodings),
			'REFERENCE_ID' => array_keys($arEncodings),
		);
		return SelectBoxFromArray('PROFILE[PARAMS][ENCODING]', $arEncodings,
			$this->arParams['ENCODING'], '', 'id="'.$this->getInputID('ENCODING').'"').
			$strHidden;
	}
	protected function showSettingsArchive(){
		if($this->bZip){
			$arArchive = array(
				'' => static::getMessage('ARCHIVE_NO'),
				static::ARCHIVE_ZIP => static::getMessage('ARCHIVE_ZIP'),
			);
			if($this->bTarGz){
				$arArchive[static::ARCHIVE_TAG_GZ] = static::getMessage('ARCHIVE_TAR_GZ');
			}
			$arArchive = array(
				'REFERENCE' => array_values($arArchive),
				'REFERENCE_ID' => array_keys($arArchive),
			);
			$strHtml = SelectBoxFromArray('PROFILE[PARAMS][ARCHIVE]', $arArchive,
				$this->arParams['ARCHIVE'], '', 'id="'.$this->getInputID('ARCHIVE').'"');
			$strHtml .= ' &nbsp; ';
			$strHtml .= ' <span id="'.$this->getInputID('ARCHIVE_JUST_WRAPPER').'"> ';
			$strHtml .= ' <input type="hidden" name="PROFILE[PARAMS][ARCHIVE_JUST]" value="N" /> ';
			$strHtml .= ' <input type="checkbox" name="PROFILE[PARAMS][ARCHIVE_JUST]" value="Y"'
				.($this->arParams['ARCHIVE_JUST'] == 'Y' ? 'checked="checked"' : '')
				.' id="'.$this->getInputID('ARCHIVE_JUST').'" /> ';
			$strHtml .= ' <label for="'.$this->getInputID('ARCHIVE_JUST').'">'
				.static::getMessage('SETTINGS_NAME_ARCHIVE_JUST').'</label>';
			$strHtml .= ' </span>';
			$strHtml .= '<script>$("#'.$this->getInputID('ARCHIVE').'").bind("change", function(e){
				$("#'.$this->getInputID('ARCHIVE_JUST_WRAPPER').'").css("display", $(this).val()=="" ? "none" : "inline");
			}).trigger("change");</script>';
			return $strHtml;
		}
	}
	
	/**
	 *	Get available fields for current plugin
	 */
	final public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = array();
		$this->bAdmin = $bAdmin;
		$arFieldsRaw = $this->getUniversalFields($intProfileID, $intIBlockID);
		$this->addUtmFieldsUniversal($arFieldsRaw);
		foreach($arFieldsRaw as $strCode => $arField){
			$bHeader = false;
			if($arField === false){
				$bHeader = true;
			}
			if(!is_array($arField)){
				$arField = array();
			}
			if(preg_match('#HEADER_(.*?).#i', $strCode, $arMatch)){
				$bHeader = true;
			}
			if(!$bAdmin && $bHeader){
				continue;
			}
			$arField = array_merge(array(
				'CODE' => isset($arField['CODE']) && strlen($arField['CODE']) ? $arField['CODE'] : $strCode,
				'DISPLAY_CODE' => isset($arField['DISPLAY_CODE']) ? $arField['DISPLAY_CODE'] : $strCode,
				'NAME' => $bHeader ? static::getMessage('F_HEAD_'.$strCode) : static::getMessage('F_NAME_'.$strCode),
				'DESCRIPTION' => static::getMessage('F_HINT_'.$strCode),
				'IS_HEADER' => $bHeader,
			), $arField);
			if(isset($arField['TYPE'])){
				$arField['DEFAULT_TYPE'] = $arField['TYPE'];
				unset($arField['TYPE']);
			}
			if(isset($arField['CONDITIONS'])){
				$arField['DEFAULT_CONDITIONS'] = $arField['CONDITIONS'];
				unset($arField['CONDITIONS']);
			}
			if(isset($arField['FIELD'])){
				if(is_array($arField['FIELD'])){
					foreach($arField['FIELD'] as &$mField){
						$mField = [
							'TYPE' => 'FIELD',
							'VALUE' => $mField,
						];
					}
					if(isset($arField['FIELD_PARAMS'])){
						foreach($arField['FIELD'] as &$mField){
							$mField['PARAMS'] = $arField['FIELD_PARAMS'];
						}
					}
					$arField['VALUE'] = $arField['FIELD'];
				}
				else{
					$arValue = array(
						'TYPE' => 'FIELD',
						'VALUE' => $arField['FIELD'],
					);
					if(isset($arField['FIELD_PARAMS'])){
						$arValue['PARAMS'] = $arField['FIELD_PARAMS'];
					}
					$arField['VALUE'] = array($arValue);
				}
				unset($arField['FIELD'], $arField['FIELD_PARAMS']);
			}
			elseif(isset($arField['CONST'])){
				if(is_array($arField['CONST'])){
					foreach($arField['CONST'] as &$mConst){
						$mConst = [
							'TYPE' => 'CONST',
							'CONST' => $mConst,
						];
					}
					if(isset($arField['CONST_PARAMS'])){
						foreach($arField['CONST'] as &$mConst){
							$mConst['PARAMS'] = $arField['CONST_PARAMS'];
						}
					}
					$arField['VALUE'] = $arField['CONST'];
				}
				else{
					$arValue = array(
						'TYPE' => 'CONST',
						'CONST' => $arField['CONST'],
					);
					if(isset($arField['CONST_PARAMS'])){
						$arValue['PARAMS'] = $arField['CONST_PARAMS'];
					}
					$arField['VALUE'] = array($arValue);
				}
				unset($arField['CONST'], $arField['CONST_PARAMS']);
			}
			if(is_array($arField['VALUE'])){
				foreach($arField['VALUE'] as $key => $arValue){
					$strType = '';
					if(isset($arValue['VALUE'])){
						$strType = 'FIELD';
					}
					elseif(isset($arValue['FIELD'])){
						$strType = 'FIELD';
						$arValue['VALUE'] = $arValue['FIELD'];
						unset($arValue['FIELD']);
					}
					elseif($arValue['CONST']){
						$strType = 'CONST';
					}
					if(strlen($strType)){
						$arValue = array_merge(array('TYPE' => $strType), $arValue);
						$arField['VALUE'][$key] = $arValue;
					}
				}
				$arField['DEFAULT_VALUE'] = $arField['VALUE'];
				unset($arField['VALUE']);
			}
			if(isset($arField['OFFER_FIELD'])){
				$arValue = array(
					'TYPE' => 'FIELD',
					'VALUE' => $arField['OFFER_FIELD'],
				);
				if(isset($arField['OFFER_FIELD_PARAMS'])){
					$arValue['PARAMS'] = $arField['OFFER_FIELD_PARAMS'];
					unset($arField['OFFER_FIELD_PARAMS']);
				}
				elseif(isset($arField['FIELD_PARAMS'])){
					$arValue['PARAMS'] = $arField['FIELD_PARAMS'];
					unset($arField['FIELD_PARAMS']);
				}
				$arField['DEFAULT_VALUE_OFFERS'] = [$arValue];
			}
			if($arField['MULTIPLE'] && (!is_array($arField['PARAMS']) || !isset($arField['PARAMS']['MULTIPLE']))){
				$arField['PARAMS']['MULTIPLE'] = 'multiple';
				if(is_array($arField['DEFAULT_VALUE'])){
					foreach($arField['DEFAULT_VALUE'] as $key => $arDefaultValue){
						if(!is_array($arDefaultValue['PARAMS']) || !isset($arDefaultValue['PARAMS']['MULTIPLE'])){
							$arField['DEFAULT_VALUE'][$key]['PARAMS']['MULTIPLE'] = 'multiple';
						}
					}
				}
			}
			if($this->bAllCData){
				$arField['CDATA'] = true;
			}
			if($arField['CDATA'] && (!is_array($arField['PARAMS']) || !isset($arField['PARAMS']['HTMLSPECIALCHARS']))){
				$arField['PARAMS']['HTMLSPECIALCHARS'] = 'cdata';
			}
			if($arField['CATEGORY_CUSTOM_NAME'] === true){
				$arField['DISPLAY_CODE'] = ' ';
				$arField['NAME'] = Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_CATEGORY_NAME_CUSTOM_NAME');
				$arField['DESCRIPTION'] = Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_CATEGORY_NAME_CUSTOM_DESC');
			}
			$arResult[] = $arField;
		}
		# Additional fields
		if($this->bAdditionalFields){
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
				$arResult[] = [
					'ID' => IntVal($arAdditionalField['ID']),
					'CODE' => Helper::call($this->strModuleId, 'AdditionalField', 'getFieldCode', [$arAdditionalField['ID']]),
					'DISPLAY_CODE' => 'param',
					'NAME' => $arAdditionalField['NAME'],
					'DESCRIPTION' => '',
					'REQUIRED' => false,
					'MULTIPLE' => true,
					'IS_ADDITIONAL' => true,
					'TAG_NAME' => 'paramX',
					'DEFAULT_VALUE' => $arDefaultValue,
				];
			}
			#
			unset($arAdditionalFields, $arAdditionalField);
		}
		# Handler 1
		$this->handler('onUpGetFields', array(&$arResult, $intProfileID, $intIBlockID, $bAdmin));
		# Search currency field
		$this->strFieldCurrency = '';
		foreach($arResult as $key => $arField){
			if($arField['IS_CURRENCY'] === true){
				$this->strFieldCurrency = $arField['CODE'];
				break;
			}
		}
		# Sort and transform
		$arColumn = array_column($arResult, 'SORT');
		if(!empty($arColumn)){
			foreach($arResult as $key => $arField){
				if(!is_numeric($arField['SORT'])){
					$arResult[$key]['SORT'] = 100;
				}
			}
			usort($arResult, __NAMESPACE__.'\Helper::sortBySort');
		}
		foreach($arResult as $key => $arField){
			$arResult[$key] = new Field($arField);
		}
		# Handler 2
		$this->handler('onUpAfterGetFields', array(&$arResult, $intProfileID, $intIBlockID, $bAdmin));
		# Return
		return $arResult;
	}
	abstract public function getUniversalFields($intProfileID, $intIBlockID);
	
	/**
	 *	Add utm fields [universal!]
	 */
	protected function addUtmFieldsUniversal(&$arFieldsRaw){
		if(is_array($this->arFieldsWithUtm) && !empty($this->arFieldsWithUtm)){
			$arFieldsRaw['HEADER_UTM'] = [
				'NAME' => static::getMessage('FIELD_HEADER_UTM'),
			];
			foreach($this->arUtmAll as $strUtm){
				$arFieldsRaw['?'.$strUtm] = [
					'DISPLAY_CODE' => $strUtm,
					'NAME' => static::getMessage('FIELD_NAME_'.toUpper($strUtm)),
					'DESC' => static::getMessage('FIELD_DESC_'.toUpper($strUtm)),
					'CONST' => '',
					'PARAMS' => [
						'URLENCODE' => 'Y',
					],
				];
			}
		}
	}
	
	/**
	 *	Add utm to URL [universal!]
	 */
	protected function addUtmToUrlUniversal(&$arFieldsRaw){
		if(is_array($this->arFieldsWithUtm) && !empty($this->arFieldsWithUtm)){
			foreach($this->arFieldsWithUtm as $strFieldWithUtm){
				if(is_array($arFieldsRaw[$strFieldWithUtm])){
					foreach($arFieldsRaw[$strFieldWithUtm] as $key => $value){
						$this->addUtmToUrlUniversal_Item($arFieldsRaw[$strFieldWithUtm][$key], $arFieldsRaw);
					}
				}
				else{
					$this->addUtmToUrlUniversal_Item($arFieldsRaw[$strFieldWithUtm], $arFieldsRaw);
				}
			}
		}
	}
	protected function addUtmToUrlUniversal_Item(&$strValue, $arFieldsRaw){
		if(strlen($strValue)){
			$arUtmValues = [];
			foreach($this->arUtmAll as $strUtm){
				if(strlen(trim($arFieldsRaw['?'.$strUtm]))){
					$arUtmValues[] = $strUtm.'='.$arFieldsRaw['?'.$strUtm];
				}
			}
			if(!empty($arUtmValues)){
				$strAmp = $this->bEscapeUtm ? '&amp;' : '&';
				$strQuery = parse_url($strValue, PHP_URL_QUERY);
				$strValue .= ($strQuery ? $strAmp : '?').implode($strAmp, $arUtmValues);
			}
		}
	}
	
	/**
	 *	Get steps
	 */
	public function getSteps(){
		$arResult = parent::getSteps();
		$arResult['CHECK'] = array( // overridden
			'NAME' => static::getMessage('STEP_CHECK'),
			'SORT' => 10,
			'FUNC' => array($this, 'stepCheck'),
		);
		$arResult['EXPORT'] = array( // overridden
			'NAME' => static::getMessage('STEP_EXPORT'),
			'SORT' => 101,
			'FUNC' => array($this, 'stepExport'),
		);
		if(strlen($this->arParams['ARCHIVE'])){
			$arResult['ARCHIVE'] = array(
				'NAME' => static::getMessage('STEP_ARCHIVE'),
				'SORT' => 1100,
				'FUNC' => array($this, 'stepArchive'),
			);
		}
		# ToDo: add handler
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
		<?
		$strFilename = $this->getExportFileName();
		print $this->showFileOpenLink($strFilename);
		return Helper::showSuccess(ob_get_clean());
	}
	
	public function showFileOpenLink($strFile=false, $strTitle=false){
		$strHtml = parent::showFileOpenLink($strFile, $strTitle);
		if(strlen($this->arParams['ARCHIVE'])){
			$strFilenameArchive = $this->getExportFileNameArchive($this->arParams['ARCHIVE']);
			if($strTitle === false){
				$strTitle = Loc::getMessage('ACRIT_EXP_FILE_OPEN_ARCHIVE');
			}
			elseif($strTitle === true){
				$strTitle = $strFilenameArchive;
			}
			if(strlen($strFilenameArchive) && is_file($_SERVER['DOCUMENT_ROOT'].$strFilenameArchive)){
				ob_start();
				?>
					<a href="<?=$strFilenameArchive;?>" target="_blank" title="<?=Loc::getMessage('ACRIT_EXP_FILE_OPEN_TITLE');?>"
						class="acrit-exp-file-open-link">
						<?=$strTitle;?>
						(<?=\CFile::FormatSize(filesize($_SERVER['DOCUMENT_ROOT'].$strFilenameArchive));?>)
					</a>
				<?
				$strHtml .= ob_get_clean();
			}
		}
		return $strHtml;
	}
	
	/**
	 *	Process single element (generate XML)
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$arDataMore = array();
		
		# Prepare data
		if($arElement['IS_OFFER']) {
			$arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
			$arElementWithSections = &$arElement['PARENT'];
		}
		else {
			$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
			$arElementWithSections = &$arElement;
		}
		$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElementWithSections, $arMainIBlockData['SECTIONS_ID'], 
			$arMainIBlockData['SECTIONS_MODE']);
		$intMainIBlockId = IntVal($arMainIBlockData['IBLOCK_ID']);
		
		$bStop = false;
		$this->handler('onUpBeforeProcessElement', array(&$arResult, &$arElement, &$arFields, &$arElementSections, $intMainIBlockId, &$bStop));
		if($bStop){
			return $arResult;
		}
		
		$this->addUtmToUrlUniversal($arFields);
		
		# Prepare sections
		if($this->prepareSaveSections($arElementSections, $arProfile, $intIBlockID, $arElement, $arFields)){
			$this->handler('onUpPrepareSaveSection', array(&$arElementSections, &$arProfile, $intIBlockID, 
				&$arElement, &$arFields));
		}
		
		# Build export data
		switch($this->arParams['EXPORT_FORMAT']){
			case 'XML':
				$arResult = $this->processElement_BuildXml($arElement, $arFields, $arElementSections, $intMainIBlockId);
				break;
			case 'JSON':
				$arResult = $this->processElement_BuildJson($arElement, $arFields, $arElementSections, $intMainIBlockId);
				break;
			case 'XLS':
			case 'XLSX':
				$arResult = $this->processElement_BuildExcel($arElement, $arFields, $arElementSections, $intMainIBlockId);
				break;
			default:
				// Format is not supported. Log this.
				break;
		}
		
		# Event handler
		$this->handler('onUpAfterProcessElement', array(&$arResult, $arElement, $arFields, $arElementSections, $intMainIBlockId));
		
		# Finishing..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}

	// *** STEPS *********************************************************************************************************

	/**
	 *	Step: check
	 */
	public function stepCheck($intProfileID, $arData){
		$this->arData = &$arData;
		$arSession = &$this->arData['SESSION']['EXPORT'];
		
		# Prepare session
		$arSession['INDEX'] = 0;
		
		# Check file writeable (target file and tmp file)
		if(!$this->bApi){
			$arSession['EXPORT_FILE_NAME'] = $this->arParams['EXPORT_FILE_NAME'];
			$arSession['EXPORT_FILE_NAME_TMP'] = $this->getExportFilenameTmp();
			if(strlen($this->arParams['ARCHIVE'])){
				$arSession['EXPORT_FILE_NAME_ARCHIVE'] = $this->getExportFileNameArchive($this->arParams['ARCHIVE']);
			}
			if(!Helper::createDirectoriesForFile($_SERVER['DOCUMENT_ROOT'].$arSession['EXPORT_FILE_NAME'])){
				$strDetails = static::getMessage('ERROR_CREATE_FILE_DIRECTORIES_DETAILS', array(
					'#DIRNAME#' => pathinfo($arSession['EXPORT_FILE_NAME'], PATHINFO_DIRNAME),
				));
				if(!$this->bCron) {
					$strError = static::getMessage('ERROR_CREATE_FILE_DIRECTORIES');
					print Helper::showError($strError, $strDetails);
				}
				Log::getInstance($this->strModuleId)->add($strDetails, $intProfileID);
				return Exporter::RESULT_ERROR;
			}
			if(!Helper::isDirWriteable(pathinfo($arSession['EXPORT_FILE_NAME'], PATHINFO_DIRNAME), true)){
				$strDetails = static::getMessage('ERROR_FILE_IS_NOT_WRITEABLE', array(
					'#FILENAME#' => $arSession['EXPORT_FILE_NAME'],
				));
				if(!$this->bCron) {
					$strError = static::getMessage('ERROR_EXPORT_FILE_IS_NOT_WRITEABLE');
					print Helper::showError($strError, $strDetails);
				}
				Log::getInstance($this->strModuleId)->add($strDetails, $intProfileID);
				return Exporter::RESULT_ERROR;
			}
			if(!Helper::isDirWriteable(pathinfo($arSession['EXPORT_FILE_NAME_TMP'], PATHINFO_DIRNAME), true)){
				$strDetails = static::getMessage('ERROR_FILE_IS_NOT_WRITEABLE', array(
					'#FILENAME#' => $arSession['EXPORT_FILE_NAME_TMP'],
				));
				if(!$this->bCron) {
					$strError = static::getMessage('ERROR_TMP_FILE_IS_NOT_WRITEABLE');
					print Helper::showError($strError, $strDetails);
				}
				Log::getInstance($this->strModuleId)->add($strDetails, $intProfileID);
				return Exporter::RESULT_ERROR;
			}
			$this->deleteTmpFile();
		}
		
		$mStepCheckResult = $this->handler('onUpStepCheck', array(&$arSession));
		if($mStepCheckResult === Exporter::RESULT_ERROR){
			return Exporter::RESULT_ERROR;
		}
		
		# Prepare data
		if($this->strCurrentFormat == 'XML'){
			$arSession['XML'] = ''; // Always in UTF-8 !!
			$this->handler('onUpGetXmlStructure', array(&$arSession['XML']));
			if(!Helper::isUtf()){
				$arSession['XML'] = Helper::convertEncoding($arSession['XML'], self::UTF8, self::CP1251);
			}
			# Split by #XML_ITEMS# - this works like the #WORK_AREA# in bitrix templates!
			list($arSession['XML_HEADER'], $arSession['XML_FOOTER']) = explode('#XML_ITEMS#', $arSession['XML']);
		}
		elseif($this->strCurrentFormat == 'JSON'){
			$arSession['JSON'] = ''; // Always in UTF-8 !!
			$this->handler('onUpGetJsonStructure', array(&$arSession['JSON']));
			if(!$this->bApi){
				if(!strlen($arSession['JSON'])){
					print 'Empty JSON.';
					Log::getInstance($this->strModuleId)->add('Empty JSON from onUpGetJsonStructure().', $intProfileID);
					return Exporter::RESULT_ERROR;
				}
				# Split by #JSON_ITEMS# - this works like the #WORK_AREA# in bitrix templates!
				$strJsonStructure = preg_replace('/(#JSON_ITEMS#)/', '$1', $arSession['JSON']);
				list($arSession['JSON_HEADER'], $arSession['JSON_FOOTER']) = explode('#JSON_ITEMS#', $strJsonStructure);
				# Remove empty lines
				$arSession['JSON_HEADER'] = preg_replace('#^([\s]*)$#m', '', $arSession['JSON_HEADER']);
				$arSession['JSON_FOOTER'] = preg_replace('#^([\s]*)$#m', '', $arSession['JSON_FOOTER']);
				# Get indent
				$arSession['JSON_ITEMS_INDENT'] = '';
				if(preg_match('/^(\s*)#JSON_ITEMS#/m', $arSession['JSON'], $arMatch)){
					$intIndent = 1;
					if(strpos($arMatch[1], "\t") !== false){
						$intIndent = strlen($arMatch[1]);
					}
					else{
						$intIndent = intVal(floor(strspn($arMatch[1], " ") / Json::getTabSize()));
					}
					$arSession['JSON_ITEMS_INDENT'] = $intIndent;
				}
			}
		}
		
		# Prepare
		$arExportSteps = &$arSession['STEPS'];
		$arExportSteps = is_array($arExportSteps) ? $arExportSteps : array();
		# Define steps
		if($this->bApi){
			$arExportSteps['EXPORT_API'] = array(
				'NAME' => 'Export by API',
				'SORT' => 50,
				'FUNC' => 'stepExport_ExportApi',
			);
		}
		else{
			if($this->bCategoriesExport){
				$arExportSteps['EXPORT_CATEGORIES'] = array(
					'NAME' => 'Exporting categories',
					'SORT' => 50,
					'FUNC' => 'stepExport_ExportCategories',
				);
			}
			if($this->bCurrenciesExport){
				$arExportSteps['EXPORT_CURRENCIES'] = array(
					'NAME' => 'Exporting currencies',
					'SORT' => 60,
					'FUNC' => 'stepExport_ExportCurrencies',
				);
			}
			$arExportSteps['EXPORT_HEADER'] = array(
				'NAME' => 'Exporting header',
				'SORT' => 100,
				'FUNC' => 'stepExport_ExportHeader',
			);
			$arExportSteps['EXPORT_ITEMS'] = array(
				'NAME' => 'Exporting items',
				'SORT' => 500,
				'FUNC' => 'stepExport_ExportItems',
			);
			$arExportSteps['EXPORT_FOOTER'] = array(
				'NAME' => 'Exporting footer',
				'SORT' => 1000,
				'FUNC' => 'stepExport_ExportFooter',
			);
			$arExportSteps['REPLACE_FILE'] = array(
				'NAME' => 'Replace file',
				'SORT' => 1100,
				'FUNC' => 'stepExport_ReplaceFile',
			);
			$arExportSteps['REPLACE_TMP_FILES'] = array(
				'NAME' => 'Remove temporary files',
				'SORT' => 1200,
				'FUNC' => 'stepExport_RemoveTmpFiles',
			);
		}
		$this->handler('onUpGetExportSteps', array(&$arExportSteps, &$arSession));
		uasort($arExportSteps, '\Acrit\Core\Helper::sortBySort');
		# ToDo: проверять CategoryRedefinition: если выбрано использовать категории торговой площадки и не все заполнено, то выдавать ошибку
		# Done
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: export (this is the main method)
	 */
	public function stepExport($intProfileID, $arData){
		$this->arData = &$arData;
		$arSession = &$this->arData['SESSION']['EXPORT'];

		# Execute all steps
		$bBreaked = false;
		$arExportSteps = &$arSession['STEPS'];
		foreach($arExportSteps as $strStep => $arStep){
			if($arStep['DONE']){
				continue;
			}
			$mStepResult = call_user_func_array(array($this, $arStep['FUNC']), array(&$arSession, $arStep));
			if($mStepResult === Exporter::RESULT_SUCCESS){
				$arExportSteps[$strStep]['DONE'] = true;
			}
			elseif($mStepResult === Exporter::RESULT_ERROR){
				return false;
			}
			elseif($mStepResult === Exporter::RESULT_CONTINUE){
				$bBreaked = true;
				break;
			}
		}
		
		# Finishing
		if(!$bBreaked){
			return Exporter::RESULT_SUCCESS;
		}
		
		return Exporter::RESULT_CONTINUE;
	}
	
	/**
	 *	Export step: Export categories
	 */
	protected function stepExport_ExportCategories($arStep){
		return call_user_func_array(array($this, $this->getMethod(__FUNCTION__)), array($arStep));
	}
	
	/**
	 *	Export step: Export categories
	 */
	protected function stepExport_ExportCurrencies($arStep){
		return call_user_func_array(array($this, $this->getMethod(__FUNCTION__)), array($arStep));
	}
	
	/**
	 *	Export step: Export header
	 */
	protected function stepExport_ExportHeader($arStep){
		return call_user_func_array(array($this, $this->getMethod(__FUNCTION__)), array($arStep));
	}
	
	/**
	 *	Export step: Export items
	 */
	protected function stepExport_ExportItems($arStep){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		#
		$this->handler('onUpBeforeExportItems', [&$arStep]);
		if($this->isExcel()){
			$this->stepExport_ExcelOpenFile();
		}
		#
		$bBreaked = false;
		while($arItems = $this->getExportDataItems()){
			foreach($arItems as $arItem){
				call_user_func_array(array($this, 'stepExport_'.$this->strCurrentFormat.'_ExportItem'), array($arItem)); // eg, stepExport_XML_ExportItem
				$this->setDataItemExported($arItem['ID']);
				$arSession['INDEX']++;
				if(!Exporter::getInstance($this->strModuleId)->haveTime()){
					$bBreaked = true;
					break 2;
				}
			}
		}
		#
		$this->handler('onUpAfterExportItems', [&$arStep, &$bBreaked]);
		if($this->isExcel()){
			$this->stepExport_ExcelSaveFile($bBreaked);
		}
		#
		if($bBreaked){
			return Exporter::RESULT_CONTINUE;
		}
		#
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Export step: Export footer
	 */
	protected function stepExport_ExportFooter($arStep){
		return call_user_func_array(array($this, $this->getMethod(__FUNCTION__)), array($arStep));
	}
	
	// *** XML ***********************************************************************************************************
	
	/**
	 *	Build XML
	 */
	protected function processElement_BuildXml($arElement, $arFields, $arElementSections, $intMainIBlockId){
		$arIBlockFields = &$this->arProfile['IBLOCKS'][$arElement['IBLOCK_ID']]['FIELDS'];
		$arResult = array();
		$this->handler('onUpBeforeBuildXml', array(&$arResult, &$arElement, &$arFields, &$arElementSections));
		if(empty($arResult)){
			# Разделяем на три массива - теги (обычные + дополнительные) и атрибуты, т.к. сначала формируем структуру тегов, потом добавляем атрибуты
			$arFieldsTags = array();
			$arFieldsAttr = array();
			$arFieldsMore = array();
			foreach($arFields as $key => $mValue){
				if(substr($key, 0, 1) == '?'){
					$arFieldsMore[$key] = $mValue;
				}
				elseif(stripos($key, '@') === false){
					$arFieldsTags[$key] = $mValue;
				}
				else{
					$arFieldsAttr[$key] = $mValue;
				}
			}
			# Additional fields
			$arAdditionalFields = [];
			foreach($arFieldsTags as $key => $value){
				if($strAdditionalFieldId = Helper::call($this->strModuleId, 'AdditionalField', 'getIdFromCode', [$key])){
					$arAdditionalFields[$key] = $value;
					unset($arFieldsTags[$key]);
				}
			}
			# Build tree
			$arXmlTags = array();
			foreach($arFieldsTags as $key => $mValue){
				if($this->handler('onUpBuildXmlTag', array(&$arXmlTags, &$arElement, &$arFields, &$arElementSections, &$key, &$mValue)) === false){
					continue;
				}
				$bEvenEmpty = is_array($arIBlockFields[$key]['PARAMS']) && $arIBlockFields[$key]['PARAMS']['EVEN_EMPTY'] == 'Y';
				if(Helper::isEmpty($mValue) && !$bEvenEmpty){
					continue;
				}
				if(stripos($key, '.') === false){
					$arXmlTags[$key] = Xml::addTag(is_array($mValue) ? $mValue : array($mValue));
				}
				else{
					$this->processElement_BuildXml_AddTag($arXmlTags, $key, $mValue);
				}
			}
			# Add additional fields
			$intIBlockId = $arElement['IBLOCK_ID'];
			$arAdditionalFieldsAll = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', 
				[$this->intProfileId, $intIBlockId]);
			if(!empty($arAdditionalFields) && is_array($arAdditionalFieldsAll) && !empty($arAdditionalFieldsAll)) {
				$arAdditionalParamsTag = [];
				$arIBlockFields = &$this->arProfile['IBLOCKS'][$intIBlockId]['FIELDS'];
				foreach($arAdditionalFields as $strParamCode => $mParamValue){
					if(!Helper::isEmpty($mParamValue)){
						$arAttributes = [];
						$arFieldId = Helper::call($this->strModuleId, 'AdditionalField', 'getIdFromCode', [$strParamCode]);
						$arAdditionalField = $arAdditionalFieldsAll[$arFieldId];
						if(strlen($arAdditionalField['NAME'])){
							$arAttributes['name'] = $arAdditionalField['NAME'];
						}
						$arAdditionalAttributes = $arIBlockFields[$strParamCode]['PARAMS']['ADDITIONAL_ATTRIBUTES'];
						if(is_array($arAdditionalAttributes) && is_array($arAdditionalAttributes['NAME']) && $arAdditionalAttributes['VALUE']){
							foreach($arAdditionalAttributes['NAME'] as $key => $strAttrName){
								$strAttrValue = $arAdditionalAttributes['VALUE'][$key];
								$arAttributes[$strAttrName] = $strAttrValue;
							}
						}
						if(is_array($arAdditionalFields[$strParamCode])){
							foreach($arAdditionalFields[$strParamCode] as $strValue){
								$arAdditionalParamsTag[] = [
									'@' => $arAttributes,
									'#' => $strValue,
								];
							}
						}
						else{
							$arAdditionalParamsTag[] = [
								'@' => $arAttributes,
								'#' => $arAdditionalFields[$strParamCode],
							];
						}
					}
				}
				$arXmlTags[$this->strParamTagName] = $arAdditionalParamsTag;
				//////////// $arAdditionalParamsTag
			}
			# Add attributes
			$arXmlAttr = array();
			foreach($arFieldsAttr as $key => $mValue){
				if($this->handler('onUpBuildXmlAttr', array(&$arXmlTags, &$arElement, &$arFields, &$arElementSections, &$key, &$mValue)) === false){
					continue;
				}
				$bEvenEmpty = is_array($arIBlockFields[$key]['PARAMS']) && $arIBlockFields[$key]['PARAMS']['EVEN_EMPTY'] == 'Y';
				if(Helper::isEmpty($mValue) && !$bEvenEmpty){
					continue;
				}
				if(substr($key, 0, 1) == '@'){
					$arXmlAttr[substr($key, 1)] = $mValue;
				}
				else{
					$this->processElement_BuildXml_AddAttribute($arXmlTags, $key, $mValue);
				}
			}
			#
			$strXmlItem = $arElement['IS_OFFER'] ? $this->strXmlItemOffer : $this->strXmlItemElement;
			$this->handler('onUpBuildXml', array(&$arXmlTags, &$arXmlAttr, &$strXmlItem, &$arElement, &$arFields, &$arElementSections));
			$this->processElement_BuildXml_MultiplyTags($arXmlTags);
			#
			$arXml = array(
				$strXmlItem => array(
					'@' => $arXmlAttr,
					'#' => $arXmlTags,
				),
			);
			$mDataMore = null;
			$this->handler('onUpGetDataMore', array(&$arElement, &$arFields, &$arElementSections, &$mDataMore, $arXml));
			$arResult = array(
				'TYPE' => 'XML',
				'DATA' => Xml::arrayToXml($arXml),
				'CURRENCY' => $arFields[$this->strFieldCurrency],
				'SECTION_ID' => reset($arElementSections),
				'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
				'DATA_MORE' => $mDataMore,
			);
		}
		$this->handler('onUpAfterBuildXml', array(&$arResult, $arElement, $arFields, $arElementSections));
		return $arResult;
	}
	/**
	 *	Add single tag to tags tree
	 */
	protected function processElement_BuildXml_AddTag(&$arResult, $strField, $mValue){
		$arPath = explode('.', $strField);
		$strTag = array_pop($arPath);
		$this->processElement_BuildXml_AddTag_($arResult, $strTag, $mValue, $arPath);
	}
	protected function processElement_BuildXml_AddTag_(&$arTags, $strTag, $mValue, $arPath){
		if(empty($arPath)){ // Добавляем конечный тег
			$arTags[$strTag] = array();
			if(is_array($mValue)){
				foreach($mValue as $mValueItem){
					$arTags[$strTag][] = array('#' => $mValueItem);
				}
			}
			else{
				$arTags[$strTag][] = array('#' => $mValue);
			}
		}
		else{ // Добавляем промежуточные (родительские) теги
			$strNextPathItem = array_shift($arPath);
			$arTag = &$arTags[$strNextPathItem];
			if(!is_array($arTag)){
				$arTag = array();
			}
			if(empty($arTag)){
				$arTag[] = array(
					'#' => null,
				);
			}
			foreach($arTag as $key => $arTagData){
				$arTag[$key]['#'] = is_array($arTag[$key]['#']) ? $arTag[$key]['#'] : array();
				$this->processElement_BuildXml_AddTag_($arTag[$key]['#'], $strTag, $mValue, $arPath, $strNextPathItem);
			}
		}
	}
	/**
	 *	Add single attribute to tags tree
	 */
	protected function processElement_BuildXml_AddAttribute(&$arResult, $strField, $strValue){
		$arField = explode('@', $strField);
		$strPath = $arField[0];
		$strAttribute = $arField[1];
		# Add tag if not exists
		if(!$this->processElement_BuildXml_AddAttribute_TagExists($arResult, $strPath)){
			$this->processElement_BuildXml_AddTag($arResult, $strPath, '');
		}
		# Process subtree
		foreach($arResult as $strFieldCode => $arFieldData){
			$this->processElement_BuildXml_AddAttr_($arResult[$strFieldCode], $strAttribute, $strValue, $strPath, $strFieldCode);
		}
	}
	protected function processElement_BuildXml_AddAttr_(&$arTagsTree, $strAttribute, $strValue, $strPath, $strCurrent){
		$bPath = $strCurrent == $strPath;
		if(is_array($arTagsTree) && is_numeric(key($arTagsTree))){
			foreach($arTagsTree as &$arTag){
				if($bPath){
					$arTag['@'][$strAttribute] = $strValue;
				}
				if(is_array($arTag['#'])){
					foreach($arTag['#'] as $strFieldCode => $arFieldData){
						$this->processElement_BuildXml_AddAttr_($arTag['#'][$strFieldCode], $strAttribute, $strValue, $strPath, $strCurrent.'.'.$strFieldCode);
					}
				}
			}
		}
	}
	/**
	 *	Is tag exists in tags tree?
	 */
	protected function processElement_BuildXml_AddAttribute_TagExists($arResult, $strPath){
		$arPath = explode('.', $strPath);
		$strEval = '$arResult';
		foreach($arPath as $strPathItem){
			$strEval .= "['{$strPathItem}'][0]['#']";
		}
		$strEval = "return isset({$strEval});";
		$bExists = @eval($strEval) === true;
		return $bExists;
	}
	
	/**
	 *	Multilpy some parent tags (<A><B>1</B><B>2</B><C>3</C><A> => <A><B>1</B><C>3</C></A> <A><B>2</B><C>3</C></A>)
	 *	And the same for tag attributes (eg, delivery-options for yandex-market)
	 */
	protected function processElement_BuildXml_MultiplyTags(&$arXmlTags){
		if(is_array($this->arXmlMultiply) && !empty($this->arXmlMultiply)){
			foreach($this->arXmlMultiply as $strField){
				$arField = explode('@', $strField);
				$arPath = explode('.', $arField[0]);
				$strTag = array_pop($arPath);
				$strAttribute = $arField[1];
				$this->processElement_BuildXml_MultiplyTags_($arXmlTags, $arPath, $strTag, $strAttribute);
			}
		}
	}
	protected function processElement_BuildXml_MultiplyTags_(&$arXmlTags, $arPath, $strTag, $strAttribute){
		$strPathItem = array_shift($arPath);
		if(is_array($arXmlTags[$strPathItem])){
			foreach($arXmlTags[$strPathItem] as $intKey => $arXmlTag){
				if(is_array($arXmlTag['#'])){
					if(empty($arPath)){
						if(is_array($arXmlTag['#'][$strTag])){
							if(strlen($strAttribute)){
								$this->processElement_BuildXml_MultiplyFoundAttribute($arXmlTags[$strPathItem], $strAttribute);
							}
							else{
								$this->processElement_BuildXml_MultiplyFoundTag($arXmlTags[$strPathItem]);
							}
						}
					}
					else{
						$this->processElement_BuildXml_MultiplyTags_($arXmlTags[$strPathItem][$intKey]['#'], $arPath, $strTag, $strAttribute);
					}
				}
			}
		}
	}
	protected function processElement_BuildXml_MultiplyFoundTag(&$arData){
		foreach($arData as &$arDataItem) {
			$arResult = [];
			$intMax = 0;
			foreach($arDataItem['#'] as $arTagValues){
				$intMax = max($intMax, count($arTagValues));
			}
			foreach($arDataItem['#'] as &$arTagValues){
				$mValue = end($arTagValues);
				$arTagValues = array_pad($arTagValues, $intMax, $mValue);
			}
			if($intMax > 1){
				$arNewItems = array();
				foreach($arDataItem['#'] as $strTag => &$arTagValues){
					foreach($arTagValues as $key => $arTagBalue){
						$arNewItems[$key]['#'][$strTag] = array($arTagBalue);
					}
				}
				$arData = $arNewItems;
			}
		}
	}
	protected function processElement_BuildXml_MultiplyFoundAttribute(&$arData, $strAttribute){
		foreach($arData as &$arDataItem) {
			$arResult = [];
			foreach($arDataItem['#'] as &$arTagValues){
				$arNewItems = array();
				foreach($arTagValues as $strTagKey => &$arTagValue){
					if(is_array($arTagValue['@'])){
						$strFirstKey = key($arTagValue['@']);
						foreach($arTagValue['@'][$strFirstKey] as $key => $value){
							$arNewItemAttrubutes = array();
							foreach($arTagValue['@'] as $strAttrKey => $arAttributes){
								$arNewItemAttrubutes[$strAttrKey] = $arAttributes[$key];
							}
							$arNewItems[] = array(
								'#' => $arTagValue['#'],
								'@' => $arNewItemAttrubutes,
							);
						}
						unset($arTagValues[$strTagKey]);
					}
				}
				$arTagValues = array_merge($arTagValues, $arNewItems);
			}
		}
	}
	
	
	/**
	 *	Step: export categories [for XML] to tmp file 'categories'
	 */
	protected function stepExport_XML_ExportCategories(){
		$strFileSuffix = $this->arFileSuffix['CATEGORIES'];
		$strFilename = $this->getExportFilenameTmp($strFileSuffix);
		if(is_file($_SERVER['DOCUMENT_ROOT'].$strFilename)){
			@unlink($_SERVER['DOCUMENT_ROOT'].$strFilename);
		}
		if(preg_match('/^(\s*)#EXPORT_CATEGORIES#$/mi', $this->arData['SESSION']['EXPORT']['XML'], $arMatch)){
			$arCategories = $this->getUsedCategories();
			foreach($arCategories as $intCategoryID => $arCategory){
				$this->handler('onUpGetXmlCategoryTag', array(&$strCategoryXml, $intCategoryID, $arCategory, $arCategories));
				if(strlen($strCategoryXml)){
					$this->writeToFile($strCategoryXml, $strFileSuffix);
				}
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Handler for build category tag
	 *	
	 */
	protected function onUpGetXmlCategoryTag(&$strCategoryXml, $intSectionID, $arSection, $arCategoriesAll){
		$arAttr = array(
			'id' => $intSectionID,
		);
		if($this->bCategoryNameInAttribute){
			$arAttr['name'] = isset($arSection['CUSTOM_NAME']) ? $arSection['CUSTOM_NAME'] : $arSection['NAME'];
		}
		if($arSection['IBLOCK_SECTION_ID'] > 0 && is_array($arCategoriesAll[$arSection['IBLOCK_SECTION_ID']])){
			$arAttr['parentId'] = $arSection['IBLOCK_SECTION_ID'];
		}
		$arXml = array(
			$this->strCategoryTag => array(
				'@' => $arAttr,
			),
		);
		if(!$this->bCategoryNameInAttribute){
			$arXml[$this->strCategoryTag]['#'] = isset($arSection['CUSTOM_NAME']) ? $arSection['CUSTOM_NAME'] : $arSection['NAME'];
		}
		$strCategoryXml = rtrim(Xml::arrayToXml($arXml)).static::EOL;
	}
	
	/**
	 *	Get used categories
	 */
	protected function getUsedCategories(){
		$arResult = array();
		#
		if($this->isCategoryCustomName() && $this->isCategoryCustomNameUsed()){
			$resCategories = $this->call('CategoryCustomName::getList', [
				'order' => [
					'CATEGORY_NAME' => 'ASC',
				],
				'filter' => [
					'PROFILE_ID' => $this->arProfile['ID'],
				],
				'select' => [
					'CATEGORY_ID',
					'CATEGORY_NAME',
					'CATEGORY_PARENT_ID',
				],
			]);
			while($arCategory = $resCategories->fetch()){
				$arResult[$arCategory['CATEGORY_ID']] = [
					'NAME' => $arCategory['CATEGORY_NAME'],
					'PARENT' => $arCategory['CATEGORY_PARENT_ID'],
				];
			}
			return $arResult;
		}
		# Find for all used sections
		foreach($this->arProfile['IBLOCKS'] as $intIBlockID => $arIBlockSettings){
			# Get used sections
			$arUsedSectionsID = array();
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $this->arProfile['ID'],
					'IBLOCK_ID' => $intIBlockID,
				),
				'order' => array('SECTION_ID' => 'ASC'),
				'select' => array('SECTION_ID', 'ADDITIONAL_SECTIONS_ID'),
				'group' => array('SECTION_ID', 'ADDITIONAL_SECTIONS_ID'),
			];
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			while($arItem = $resItems->fetch()){
				$arItemSectionsID = array();
				if(is_numeric($arItem['SECTION_ID']) && $arItem['SECTION_ID'] > 0) {
					$arItemSectionsID[] = $arItem['SECTION_ID'];
				}
				foreach($arItemSectionsID as $intSectionID){
					if(!in_array($intSectionID, $arUsedSectionsID)){
						$arUsedSectionsID[] = $intSectionID;
					}
				}
			}
			# Get involved sections ID
			$intSectionsIBlockID = $intIBlockID;
			$strSectionsID = $arIBlockSettings['SECTIONS_ID'];
			$strSectionsMode = $arIBlockSettings['SECTIONS_MODE'];
			$arCatalog = Helper::getCatalogArray($intIBlockID);
			if(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] > 0){
				$intSectionsIBlockID = $arCatalog['PRODUCT_IBLOCK_ID'];
				$strSectionsID = $this->arProfile['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['SECTIONS_ID'];
				$strSectionsMode = $this->arProfile['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['SECTIONS_MODE'];
			}
			$arSelectedSectionsID = Exporter::getInstance($this->strModuleId)->getInvolvedSectionsID($intSectionsIBlockID, $strSectionsID, $strSectionsMode);
			# Process used sections
			$arSectionsForExport = array_intersect($arSelectedSectionsID, $arUsedSectionsID);
			# Merge to all
			$arResult = array_merge($arResult, $arSectionsForExport);
			# End
			unset($arSelectedSectionsID, $arUsedSectionsID);
		}
		# Add parents
		if($this->arParams['CATEGORIES_EXPORT_PARENTS'] == 'Y'){
			$arResultWithParents = array();
			foreach($arResult as $intSectionID){
				$resChain = \CIBlockSection::getNavChain(false, $intSectionID, array('ID'));
				while($arChain = $resChain->getNext(false,false)){
					$arResultWithParents[] = $arChain['ID'];
				}
			}
			$arResult = $arResultWithParents;
			unset($arResultWithParents, $intSectionID, $resChain, $arChain);
		}
		$arResult = array_unique($arResult);
		# Get more data for sections (NAME, PARENT, ...)
		if(!empty($arResult)){
			$arSectionsAll = array();
			$arSort = array('ID' => 'ASC');
			$arFilter = array('ID' => $arResult);
			$arSelect = array('ID', 'IBLOCK_SECTION_ID', 'NAME', 'LEFT_MARGIN');
			$resSections = \CIBlockSection::getList($arSort, $arFilter, false, $arSelect);
			while($arSection = $resSections->getNext(false,false)){
				$intID = IntVal($arSection['ID']);
				unset($arSection['ID']);
				$arSection['IBLOCK_SECTION_ID'] = IntVal($arSection['IBLOCK_SECTION_ID']);
				$arSectionsAll[$intID] = $arSection;
			}
			uasort($arSectionsAll, function($a, $b){
				if($a['LEFT_MARGIN'] == $b['LEFT_MARGIN']){
					return 0;
				}
				return ($a['LEFT_MARGIN'] < $b['LEFT_MARGIN']) ? -1 : 1;
			});
			$arResult = $arSectionsAll;
			unset($arSectionsAll, $resSections, $arSection, $arSort, $arFilter, $arSelect);
		}
		# Apply category redefinitions
		$arRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$this->arProfile['ID']]);
		if(!empty($arRedefinitions)){
			foreach($arRedefinitions as $intSectionID => $strSectionName){
				if(is_array($arResult[$intSectionID])){
					$arResult[$intSectionID]['NAME_ORIGINAL'] = $arResult[$intSectionID]['NAME_ORIGINAL'];
					#
					$strSectionName = trim(end(explode($this->strCategoryNameSeparator, $strSectionName)));
					$arResult[$intSectionID]['NAME'] = $strSectionName;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	[Helper method] Prepare categories: get array of used categories, just that which in profile iblock settings
	 */
	protected function prepareProductCategories($arElement){
		$bOffer = $arElement['IS_OFFER'];
		$intElementId = $arElement['ID'];
		$intIBlockId = $bOffer ? $arElement['PRODUCT_IBLOCK_ID'] : $arElement['IBLOCK_ID'];
		$strSectionsId = $this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_ID'];
		$strSectionsMode = $this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_MODE'];
		return Exporter::getInstance($this->strModuleId)->getElementSections($bOffer ? $arElement['PARENT'] : $arElement, $strSectionsId, $strSectionsMode);
	}
	
	/**
	 *	Step: export currencies [for XML] to tmp file 'currencies'
	 */
	protected function stepExport_XML_ExportCurrencies(){
		$strFileSuffix = $this->arFileSuffix['CURRENCIES'];
		$strFilename = $this->getExportFilenameTmp($strFileSuffix);
		if(is_file($_SERVER['DOCUMENT_ROOT'].$strFilename)){
			@unlink($_SERVER['DOCUMENT_ROOT'].$strFilename);
		}
		if(preg_match('/^(\s*)#EXPORT_CURRENCIES#$/mi', $this->arData['SESSION']['EXPORT']['XML'], $arMatch)){
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $this->arProfile['ID'],
					'!CURRENCY' => false,
				),
				'order' => array('CURRENCY' => 'ASC'),
				'select' => array('CURRENCY'),
				'group' => array('CURRENCY'),
			];
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$arCurrencies = array();
			while($arItem = $resItems->fetch()){
				$arCurrency = explode(',',$arItem['CURRENCY']);
				Helper::arrayRemoveEmptyValues($arCurrency);
				foreach($arCurrency as $strCurrency){
					$arCurrencies[$strCurrency] = array(
						'CURRENCY' => $strCurrency,
						'RATE' => 1,
					);
				}
			}
			#
			$arCurrencyAll = Helper::getCurrencyList();
			$strBaseCurrency = $arData['PROFILE']['PARAMS']['CURRENCY']['TARGET_CURRENCY'];
			if(!in_array($strBaseCurrency, $this->getSupportedCurrencies())){
				$strBaseCurrency = '';
			}
			if(!strlen($strBaseCurrency) || !array_key_exists($strBaseCurrency, $arCurrencyAll)){
				foreach($arCurrencyAll as $arCurrency){
					if($arCurrency['IS_BASE']){
						$strBaseCurrency = $arCurrency['CURRENCY'];
					}
				}
			}
			$arCurrencyConverter = CurrencyConverterBase::getConverterList();
			#
			foreach($arCurrencies as $key => $arCurrency){
				if($arCurrency['CURRENCY'] == $strBaseCurrency){
					$strRate = '1';
				}
				else {
					$strRatesSource = $arData['PROFILE']['PARAMS']['CURRENCY']['RATES_SOURCE'];
					$strRate = '1.00';
					if(strlen($strRatesSource) && is_array($arCurrencyConverter[$strRatesSource])) {
						$strClass = $arCurrencyConverter[$strRatesSource]['CLASS'];
						if(class_exists($strClass)) {
							$strRate = $strClass::getFactor($arCurrency['CURRENCY'], $strBaseCurrency);
							$strRate = number_format($strRate, 2, '.', '');
						}
					}
				}
				$this->handler('onUpGetXmlCurrencyTag', array(&$strCurrencyXml, $arCurrency, $strRate));
				if(strlen($strCurrencyXml)){
					$this->writeToFile($strCurrencyXml, $strFileSuffix);
				}
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Handler for build cyrrency tag
	 */
	protected function onUpGetXmlCurrencyTag(&$strCurrencyXml, $arCurrency, $strRate){
		$arAttr = array(
			'id' => $arCurrency['CURRENCY'],
			'rate' => $strRate,
		);
		$arXml = array(
			'currency' => array(
				'@' => $arAttr,
			),
		);
		$strCurrencyXml = rtrim(Xml::arrayToXml($arXml)).static::EOL;
	}
	
	/**
	 *	Step: export header [for XML]
	 */
	protected function stepExport_XML_ExportHeader(){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		$strXmlHeader = $arSession['XML_HEADER'];
		$this->replaceStringMacrosFromTmpFiles($strXmlHeader, array(
			'EXPORT_CATEGORIES' => $this->arFileSuffix['CATEGORIES'],
			'EXPORT_CURRENCIES' => $this->arFileSuffix['CURRENCIES'],
		));
		$this->writeToFile(rtrim($strXmlHeader, "\t"));
		unset($strXmlHeader);
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: export [for XML] one item
	 */
	protected function stepExport_XML_ExportItem($arItem){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		#
		if(strlen($arItem['DATA'])){
			$strXml = rtrim(Xml::addOffset($arItem['DATA'], $this->intXmlDepthItems)).static::EOL;
			$this->handler('onUpXmlExportItem', array(&$arItem, &$strXml));
			$this->writeToFile($strXml);
			unset($strXml);
		}
	}
	
	/**
	 *	Step: export footer [for XML]
	 */
	protected function stepExport_XML_ExportFooter(){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		$this->writeToFile(ltrim($arSession['XML_FOOTER']));
		return Exporter::RESULT_SUCCESS;
	}
	
	// *** JSON **********************************************************************************************************
	
	/**
	 *	JSON_PRETTY_PRINT and other options for Json::encode
	 */
	protected function getJsonEncodeOptions(){
		return JSON_PRETTY_PRINT;
	}
	
	/**
	 *	Set value (e.g., key or sub.sub.key)
	 */
	protected function jsonSetValue(&$arJson, $strField, $mValue, $bTranspose=false){
		if(stripos($strField, '.') !== false) { // nested
			$arField = explode('.', $strField);
			$strLastKey = array_pop($arField);
			$arTarget = &$arJson;
			foreach($arField as $key){
				$arTarget = &$arTarget[$key];
				if(!is_array($arTarget)){
					$arTarget = [];
				}
			}
			if($bTranspose){
				if(is_array($arTarget[$strLastKey])){
					$arTarget[$strLastKey] = Helper::transpose($arTarget[$strLastKey]);
				}
			}
			else{
				$arTarget[$strLastKey] = $mValue;
			}
		}
		else{
			if($bTranspose){
				if(is_array($arJson[$strField]) && is_array(reset($arJson[$strField]))){
					$arJson[$strField] = Helper::transpose($arJson[$strField]);
				}
			}
			else{
				$arJson[$strField] = $mValue;
			}
		}
	}
	
	/**
	 *
	 */
	protected function processElement_BuildJson($arElement, $arFields, $arElementSections, $intMainIBlockId){
		$arResult = array();
		$arJson = [];
		$this->handler('onUpBeforeBuildJson', array(&$arResult, &$arElement, &$arFields, &$arElementSections));
		if(empty($arResult)){
			# Разделяем на три массива - теги (обычные + дополнительные) и атрибуты, т.к. сначала формируем структуру тегов, потом добавляем атрибуты
			$arFieldsTags = array();
			$arFieldsAttr = array(); // Not supported in JSON
			$arFieldsMore = array(); // Custom data, not for export
			foreach($arFields as $key => $mValue){
				if(substr($key, 0, 1) == '?'){
					$arFieldsMore[$key] = $mValue;
				}
				elseif(stripos($key, '@') === false){
					$arFieldsTags[$key] = $mValue;
				}
				else{
					$arFieldsAttr[$key] = $mValue;
				}
			}
			# Additional fields
			$arAdditionalFields = [];
			foreach($arFieldsTags as $key => $value){
				if($strAdditionalFieldId = Helper::call($this->strModuleId, 'AdditionalField', 'getIdFromCode', [$key])){
					$arAdditionalFields[$key] = $value;
					unset($arFieldsTags[$key]);
				}
			}
			# Build tree
			foreach($arFieldsTags as $key => $mValue){
				if($this->handler('onUpBuildJsonTag', array(&$arJson, &$arElement, &$arFields, &$arElementSections, &$key, &$mValue)) === false){
					continue;
				}
				if(is_null($mValue)){
					continue;
				}
				$this->jsonSetValue($arJson, $key, $mValue);
			}
			# Multiple
			if(is_array($this->arJsonTranspose) && !empty($this->arJsonTranspose)){
				foreach($this->arJsonTranspose as $strKey){
					if(strlen($strKey)){
						$this->jsonSetValue($arJson, $strKey, null, true);
					}
				}
			}
			#
			$arTmpResult = $this->handler('onUpBuildJson', array(&$arJson, &$arElement, &$arFields, &$arElementSections));
			if(is_array($arTmpResult)) {
				$arResult = $arTmpResult;
			}
			else{
				$mDataMore = null;
				$this->handler('onUpGetDataMore', array(&$arElement, &$arFields, &$arElementSections, &$mDataMore, $arJson));
				$strJson = Json::encode($arJson, $this->getJsonEncodeOptions());
				if(!Helper::isUtf()){
					$strJson = Helper::convertEncoding($strJson, 'UTF-8', 'CP1251');
				}
				$arResult = array(
					'TYPE' => 'JSON',
					'DATA' => $strJson,
					'CURRENCY' => $arFields[$this->strFieldCurrency],
					'SECTION_ID' => reset($arElementSections),
					'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
					'DATA_MORE' => $mDataMore,
				);
			}
		}
		$this->handler('onUpAfterBuildJson', array(&$arResult, $arElement, $arFields, $arElementSections));
		return $arResult;
	}
	
	/**
	 *	Split JSON structure. E.g., by #ITEMS#
	 */
	protected function jsonSplitByDivider($strJson, $strDivider, &$strHeader, &$strFooter, &$strIndent){
		$strIndent = '';
		$strDividerQ = '"'.$strDivider.'"';
		$strPattern = '#^([\s]+).*'.$strDividerEscaped.'#m';
		if(preg_match($strPattern, $strJson, $arMatch)){
			$strIndent = $arMatch[1];
		}
		$strJson = str_replace($strDividerQ, '['.PHP_EOL.$strIndent.$strIndent.$strDivider.PHP_EOL.$strIndent.']', $strJson);
		$strPattern = '~^([\s]*)'.$strDivider.'([\s]?)$~m';
		if(preg_match($strPattern, $strJson, $arMatch)){
			$strIndent = $arMatch[1];
		}
		$arJson = explode($strDivider, $strJson);
		$strHeader = preg_replace('#^([\s]{0,})$#m', '', $arJson[0]);
		$strFooter = preg_replace('#^([\s]{0,})$#m', '', $arJson[1]);
		$strFooter = ltrim($strFooter, "\r\n");
	}
	
	/**
	 *	Step: export header [for JSON]
	 */
	protected function stepExport_JSON_ExportHeader(){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		$strJsonHeader = $arSession['JSON_HEADER'];
		$bWrite = true;
		$mResult = $this->handler('onUpJsonExportHeader', array(&$strJsonHeader, &$arSession, &$bWrite));
		if($bWrite){
			$this->writeToFile($strJsonHeader);
			return Exporter::RESULT_SUCCESS;
		}
		return $mResult;
	}
	/**
	 *	Step: export [for JSON] one item
	 */
	protected function stepExport_JSON_ExportItem($arItem){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		if(strlen($arItem['DATA'])){
			$strJson = $arItem['DATA'];
			$bWrite = true;
			$this->handler('onUpJsonExportItem', array(&$arItem, &$strJson, &$arSession, &$bWrite));
			# Beautyfy
			if($bWrite){
				Json::replaceSpaces($strJson);
				Json::addIndent($strJson, $arSession['JSON_ITEMS_INDENT']);
				if($arSession['FIRST_ITEM_EXPORTED']){
					$strJson = ','.static::EOL.$strJson;
				}
				$strJson = rtrim($strJson);
				# Write
				$this->writeToFile($strJson);
			}
			# First exported!! (for commas)
			if(!$arSession['FIRST_ITEM_EXPORTED']){
				$arSession['FIRST_ITEM_EXPORTED'] = true;
			}
			unset($strJson);
		}
	}
	
	/**
	 *	Step: export footer [for JSON]
	 */
	protected function stepExport_JSON_ExportFooter(){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		$strJsonFooter = $arSession['JSON_FOOTER'];
		$bWrite = true;
		$mResult = $this->handler('onUpJsonExportFooter', array(&$strJsonFooter, &$arSession, &$bWrite));
		if($bWrite){
			$this->writeToFile($strJsonFooter);
			return Exporter::RESULT_SUCCESS;
		}
		return $mResult;
	}
		
	// *** EXCEL *********************************************************************************************************
	
	/**
	 *	
	 */
	protected function processElement_BuildExcel($arElement, $arFields, $arElementSections, $intMainIBlockId){
		$arResult = [];
		$arJson = [];
		$this->handler('onUpBeforeBuildExcel', [&$arResult, &$arElement, &$arFields, &$arElementSections]);
		if(empty($arResult)){
			$arJson = $arFields;
			#
			$this->handler('onUpBuildExcel', [&$arJson, &$arElement, &$arFields, &$arElementSections]);
			#
			$mDataMore = null;
			$this->handler('onUpGetDataMore', [&$arElement, &$arFields, &$arElementSections, &$mDataMore, $arJson]);
			$strJson = Json::encode($arJson);
			if(!Helper::isUtf()){
				$strJson = Helper::convertEncoding($strJson, 'UTF-8', 'CP1251');
			}
			$arResult = [
				'TYPE' => 'EXCEL',
				'DATA' => $strJson,
				'CURRENCY' => $arFields[$this->strFieldCurrency],
				'SECTION_ID' => reset($arElementSections),
				'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
				'DATA_MORE' => $mDataMore,
			];
		}
		$this->handler('onUpAfterBuildExcel', [&$arResult, $arElement, $arFields, $arElementSections]);
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected function stepExport_ExcelOpenFile(){
		$strFilename = $this->excelGetFilenameTmp();
		$this->handler('onUpBeforeExcelOpen', [&$strFilename]);
		Helper::includePhpSpreadSheet();
		if(strlen($strFilename) && !is_object($this->obExcel)){
			if(strlen($strFilename) && is_file($strFilename)){
				$strExcelType = $this->excelGetFormat(true);
				$obReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($strExcelType);
				$this->obExcel = $obReader->load($strFilename);
				unset($obReader);
			}
			else{
				$this->obExcel = new \PhpOffice\PhpSpreadsheet\IOFactory();
			}
		}
		$this->handler('onUpAfterExcelOpen', [&$strFilename]);
	}
	
	/**
	 *	
	 */
	protected function stepExport_ExcelSaveFile(&$bBreaked){
		$strFilename = $this->excelGetFilenameTmp();
		$strFilenameUpdated = $this->excelGetFilenameTmp('updated');
		$this->handler('onUpBeforeExcelSave', [&$strFilename, &$strFilenameUpdated]);
		if(strlen($strFilenameUpdated)){
			$strFormat = $this->excelGetFormat(true);
			$obWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->obExcel, $strFormat);
			$obWriter->save($strFilenameUpdated);
			$this->handler('onUpAfterExcelSave', [&$strFilename, &$strFilenameUpdated]);
			unlink($strFilename);
			rename($strFilenameUpdated, $strFilename);
			return true;
		}
		return false;
	}
	
	/**
	 *	Is current format excel?
	 */
	protected function isExcel(){
		return in_array($this->strCurrentFormat, ['XLSX', 'XLS', 'ODS']);
	}
	
	/**
	 *	
	 */
	protected function excelGetFilenameTmp($strSuffix=false){
		$this->handler('onUpWriteToFile');
		$strFilename = $_SERVER['DOCUMENT_ROOT'].$this->getExportFilenameTmp($strSuffix);
		return $strFilename;
	}
	
	/**
	 *	
	 */
	protected function excelWriteCell($intSheetIndex, $strValue, $intColumnIndex, $intLineIndex){
		$strColumnLetter = $this->excelGetColumnLetter($intColumnIndex);
		$strCell = $strColumnLetter.$intLineIndex;
		if(strlen($strValue)){
			if(!Helper::isUtf()){
				$strValue = Helper::convertEncoding($strValue, 'CP1251', 'UTF-8');
			}
			if(is_numeric($strValue) && $strValue > 0 && intVal($strValue) == $strValue && substr($strValue, 0, 1) != '0'){
				$this->obExcel->getSheet($intSheetIndex)->getStyle($strCell)->getNumberFormat()->setFormatCode('#');
				$this->obExcel->getSheet($intSheetIndex)->setCellValueExplicit($strCell, $strValue, 
					\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			}
			else{
				$this->obExcel->getSheet($intSheetIndex)->setCellValue($strCell, $strValue);
			}
		}
	}
	
	/**
	 *	
	 */
	protected function excelReadCell($intSheetIndex, $intColumnIndex, $intLineIndex){
		$strColumnLetter = $this->excelGetColumnLetter($intColumnIndex);
		$strCell = $strColumnLetter.$intLineIndex;
		$strValue = $this->obExcel->getSheet($intSheetIndex)->getCell($strCell)->getValue();
		if(!Helper::isUtf()){
			$strValue = Helper::convertEncoding($strValue, 'UTF-8', 'CP1251');
		}
		return $strValue;
	}
	
	/**
	 *	Get cell background color
	 */
	protected function excelGetCellBgColor($intSheetIndex, $intColumnIndex, $intLineIndex){
		$strColumnLetter = $this->excelGetColumnLetter($intColumnIndex);
		$strCell = $strColumnLetter.$intLineIndex;
		$strBg = $this->obExcel->getSheet($intSheetIndex)->getStyle($strCell)->getFill()->getStartColor()->getRGB();
		return $strBg;
	}
	
	/**
	 *	
	 */
	protected function excelGetColumnLetter($intColumnIndex){
		return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($intColumnIndex);
	}
	
	/**
	 *	
	 */
	protected function excelGetColumnIndex($strColumnLetter){
		return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($strColumnLetter);
	}
	
	/**
	 *	Get current format
	 */
	public function excelGetFormat($bReturnWriterType=false){
		$strFormat = $this->strCurrentFormat;
		$arAvailableFormats = $this->excelGetAvailableFormats();
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
	 *	
	 */
	protected function excelGetAvailableFormats(){
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
	
	// *******************************************************************************************************************
	
	/**
	 *	Get export data items
	 */
	protected function getExportDataItems($arFilter=null, $arSelect=null){
		$arResult = array();
		#
		$strSortOrder = $this->arProfile['PARAMS']['SORT_ORDER'];
		if(!in_array($strSortOrder, array('ASC', 'DESC'))){
			$strSortOrder = 'ASC';
		}
		#
		$arFilter = array_merge(array(
			'PROFILE_ID' => $this->arProfile['ID'],
			'!TYPE' => ExportData::TYPE_DUMMY,
			'EXPORTED' => false
		), is_array($arFilter) ? $arFilter : array());
		$arOrder = array(
			'SORT' => $strSortOrder,
			'ELEMENT_ID' => 'ASC',
		);
		$arSelect = is_array($arSelect) ? $arSelect : array('ID', 'IBLOCK_ID', 'DATA', 'DATA_MORE', 'ELEMENT_ID', 'SORT');
		if(!in_array('ID', $arSelect)){
			$arSelect[] = 'ID';
		}
		if(!in_array('DATA', $arSelect)){
			$arSelect[] = 'DATA';
		}
		$arQuery = [
			'filter' => $arFilter,
			'order' => $arOrder,
			'select' => $arSelect,
			'limit' => $this->intExportPerStep,
		];
		$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
		while($arItem = $resItems->fetch()){
			$arResult[] = $arItem;
		}
		return !empty($arResult) ? $arResult : false;
	}
	
	/**
	 *	Export step: replace file (tmp => real)
	 */
	protected function stepExport_ReplaceFile(&$arSession, $arStep){
		if(is_file($arSession['EXPORT_FILE_NAME'])){
			@unlink($arSession['EXPORT_FILE_NAME']);
		}
		if(is_file($arSession['EXPORT_FILE_NAME'])){
			$strDetails = static::getMessage('ERROR_DELETING_OLD_EXPORT_FILE_DETAILS', array(
				'#FILENAME#' => $arSession['EXPORT_FILE_NAME'],
			));
			if(!$this->bCron) {
				$strError = static::getMessage('ERROR_DELETING_OLD_EXPORT_FILE');
				print Helper::showError($strError, $strDetails);
			}
			Log::getInstance($this->strModuleId)->add($strDetails, $this->arProfile['ID']);
			return Exporter::RESULT_ERROR;
		}
		$strFrom = $_SERVER['DOCUMENT_ROOT'].$arSession['EXPORT_FILE_NAME_TMP'];
		$strTo = $_SERVER['DOCUMENT_ROOT'].$arSession['EXPORT_FILE_NAME'];
		if(!@rename($strFrom, $strTo)){
			$strDetails = static::getMessage('ERROR_REPLACE_TMP_FILE_DETAILS', array(
				'#FILENAME_TMP#' => $arSession['EXPORT_FILE_NAME_TMP'],
				'#FILENAME_REAL#' => $arSession['EXPORT_FILE_NAME_TMP'],
			));
			if(!$this->bCron) {
				$strError = static::getMessage('ERROR_REPLACE_TMP_FILE');
				print Helper::showError($strError, $strDetails);
			}
			Log::getInstance($this->strModuleId)->add($strDetails, $this->arProfile['ID']);
			return Exporter::RESULT_ERROR;
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Export step: remove tmp files
	 */
	protected function stepExport_RemoveTmpFiles(&$arSession, $arStep){
		# Remove suffix files
		foreach($this->arFileSuffix as $strCode => $strFileSuffix){
			$strFilename = $this->getExportFilenameTmp($strFileSuffix);
			if(is_file($_SERVER['DOCUMENT_ROOT'].$strFilename)){
				@unlink($_SERVER['DOCUMENT_ROOT'].$strFilename);
			}
		}
		# Remove tmp directory
		$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$this->arProfile['ID']]);
		if(is_dir($strTmpDir)){
			@rmdir($strTmpDir);
		}
	}
	
	/**
	 *	Put file into archive
	 */
	public function stepArchive($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		$strRoot = $_SERVER['DOCUMENT_ROOT'];
		$strFilename = $this->getExportFileName();
		$strFilenameArchive = $this->getExportFileNameArchive($this->arParams['ARCHIVE']);
		#
		if(strlen($this->arParams['ARCHIVE'])) { // zip || tar.gz
			$arZipFiles = array(
				$strRoot.$strFilename,
			);
			$obAchiver = \CBXArchive::GetArchive($strRoot.$strFilenameArchive);
			$obAchiver->setOptions(array(
				'REMOVE_PATH' => pathinfo($strRoot.$strFilename, PATHINFO_DIRNAME),
			));
			$strStartFile = '';
			if($arSession['ZIP_NEXT_STEP']){
				$strStartFile = $obAchiver->getStartFile();
			}
			$intResult = $obAchiver->pack($arZipFiles, $strStartFile);
			unset($obAchiver);
			if($this->arParams['ARCHIVE_JUST'] == 'Y' && is_file($strRoot.$strFilename)) {
				@unlink($strRoot.$strFilename);
			}
			if($intResult === \IBXArchive::StatusSuccess){
				$mCallbackResult = $this->handler('onUpZipSuccess', [$strFilenameArchive]);
				if($mCallbackResult === Exporter::RESULT_ERROR){
					return Exporter::RESULT_ERROR;
				}
				return Exporter::RESULT_SUCCESS;
			}
			elseif($intResult === \IBXArchive::StatusError){
				return Exporter::RESULT_ERROR;
			}
			elseif($intResult === \IBXArchive::StatusContinue){
				$arSession['ZIP_NEXT_STEP'] = true;
				return Exporter::RESULT_CONTINUE;
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Set exported flag to 'Y' for selected data item
	 */
	protected function setDataItemExported($intDataItemID){
		return Helper::call($this->strModuleId, 'ExportData', 'setDataItemExported', [$intDataItemID]);
	}
	
	/**
	 *	Replace macros in string to tmp files
	 *	<categories>#EXPORT_CATEGORIES#</categories> => <categories>content from tmp file `categories`</categories>
	 */
	protected function replaceStringMacrosFromTmpFiles(&$strSource, $arReplace){
		$arCallback = array($this, 'replaceStringMacrosFromTmpFilesCallback');
		foreach($arReplace as $strMacro => $strFileSuffix){
			$strMacro = '#'.$strMacro.'#';
			$this->strFileSuffix = $strFileSuffix;
			$strSource = preg_replace_callback('/(\t*)'.$strMacro.'/m', $arCallback, $strSource);
			unset($this->strFileSuffix);
		}
	}
	public function replaceStringMacrosFromTmpFilesCallback($arMatch){
		$intDepth = strlen($arMatch[1]);
		$strFilename = $this->getExportFilenameTmp($this->strFileSuffix);
		$strFileContent = '';
		if(is_file($_SERVER['DOCUMENT_ROOT'].$strFilename)){
			$strFileContent = rtrim(file_get_contents($_SERVER['DOCUMENT_ROOT'].$strFilename));
			$strFileContent = Xml::addOffset($strFileContent, $intDepth);
		}
		return $strFileContent;
	}
	
	/**
	 *	Get method name considering export format
	 *	Example: stepExport_ExportCategories => stepExport_XLSX_ExportCategories
	 */
	protected function getMethod($strMethodName){
		return preg_replace('#(_)#', '$1'.$this->strCurrentFormat.'$1', $strMethodName, $intLimit=1);
	}
	
	/**
	 *	Convert text encoding (from site encoding to $this->arParams['ENCODING'])
	 */
	protected function convertEncoding(&$strText){
		if(Helper::isUtf() && $this->arParams['ENCODING'] == self::CP1251){
			$strText = Helper::convertEncoding($strText, self::UTF8, self::CP1251);
		}
		elseif(!Helper::isUtf() && $this->arParams['ENCODING'] == self::UTF8){
			$strText = Helper::convertEncoding($strText, self::CP1251, self::UTF8);
		}
		return $strText;
	}
	
	/**
	 *	Write data to temporary file
	 *	If $strFilename is null, writing to tmp file
	 */
	protected function writeToFile($strContent, $strFilenameSuffix=null, $bAutoConvert=true){
		$this->handler('onUpWriteToFile', [&$strContent, &$strFilenameSuffix, &$bAutoConvert]);
		$strFilename = $_SERVER['DOCUMENT_ROOT'].$this->getExportFilenameTmp($strFilenameSuffix);
		if($bAutoConvert && is_null($strFilenameSuffix)){
			$this->convertEncoding($strContent);
		}
		return !!file_put_contents($strFilename, $strContent, FILE_APPEND);
	}
	
	/**
	 *	Delete temporary file
	 */
	protected function deleteTmpFile(){
		$arSession = &$this->arData['SESSION']['EXPORT'];
		$strFilenameTmp = $_SERVER['DOCUMENT_ROOT'].$arSession['EXPORT_FILE_NAME_TMP'];
		if(is_file($strFilenameTmp)){
			@unlink($strFilenameTmp);
		}
		return !is_file($strFilenameTmp);
	}
	
	/**
	 *	Build main xml structure
	 *	XML_ENCODING
	 *	EXPORT_CATEGORIES
	 *	EXPORT_CURRENCIES
	 *	XML_ITEMS
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<export>'.static::EOL;
		$strXml .= '<categories>'.static::EOL;
		$strXml .= '	#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '</categories>'.static::EOL;
		$strXml .= '<currencies>'.static::EOL;
		$strXml .= '	#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '</currencies>'.static::EOL;
		$strXml .= '<items>'.static::EOL;
		$strXml .= '<realty-feed xmlns="http://webmaster.yandex.ru/schemas/feed/realty/2010-06">'.static::EOL;
		$strXml .= '	<generation-date>#XML_GENERATION_DATE#</generation-date>'.static::EOL;
		$strXml .= '	#XML_ITEMS#'.static::EOL;
		$strXml .= '</realty-feed>'.static::EOL;
		$strXml .= '</items>'.static::EOL;
		$strXml .= '</export>';
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_GENERATION_DATE#' => date('c'),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}
	
	/**
	 *	Output string (or array) in selected encoding [if $bUtf8 => UTF-8, else - $this->arParams['ENCODING']]
	 */
	protected function output($mData, $bUtf8=false){
		$strEncodingTarget = $bUtf8 ? static::UTF8 : $this->arParams['ENCODING'];
		$strEncodingSource = Helper::isUtf() ? static::UTF8 : static::CP1251;
		if($strEncodingTarget != $strEncodingSource){
			$mData = Helper::convertEncoding($mData, $strEncodingSource, $strEncodingTarget);
		}
		return $mData;
	}
	
	/**
	 *	Wrapper for Filter::getConditionsJson
	 */
	protected function getFieldFilter($intIBlockId, $arField){
		return Filter::getConditionsJson($this->strModuleId, $intIBlockId, [$arField]);
	}

}


/* EXAMPLES and INFORMATION

[some field examples]
$arResult['name'] = array(
	'VALUE' => array(
		array(
			'VALUE' => 'NAME',
		),
	),
);
$arResult['@available'] = array(
	'TYPE' => 'CONDITION',
	'CONDITIONS' => Filter::getConditionsJson($intIBlockID, array( // optional
		array(
			'FIELD' => 'CATALOG_QUANTITY',
			'LOGIC' => 'MORE',
			'VALUE' => '0',
		),
	)),
	'VALUE' => array(
		array(
			'CONST' => 'true',
			'SUFFIX' => 'Y',
		),
		array(
			'CONST' => 'false',
			'SUFFIX' => 'N',
		),
	),
);
$arResult['delivery-options.option@cost'] = array('MULTIPLE' => true);
$arResult['delivery-options.option@days'] = array('MULTIPLE' => true);
$arResult['delivery-options.option@order-before'] = array('MULTIPLE' => true);

[multiply tags]
protected $arXmlMultiply = array('room-space.value', 'location.metro.name', 'delivery-options.option@cost');

*/