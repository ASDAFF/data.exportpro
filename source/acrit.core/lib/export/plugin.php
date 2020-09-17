<?
/**
 * Acrit core
 * @package acrit.core
 * @copyright 2018 Acrit
 */
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Application,
	\Acrit\Core\Helper,
	\Acrit\Core\Teacher,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

/**
 * Base interface for plugin
 */
abstract class Plugin {

	CONST TYPE_NATIVE = 'NATIVE';
	CONST TYPE_CUSTOM = 'CUSTOM';
	
	CONST OWN_CATEGORIES = false;
	
	CONST ARCHIVE_ZIP = 'ZIP';
	CONST ARCHIVE_TAG_GZ = 'TAR.GZ';
	
	CONST TEACHER_DEFAULT = 'DEFAULT';
	
	#
	protected static $strStaticModuleId = null;
	protected static $bSubclass = false;
	
	# Дата актуализации интеграции плагина с онлайн-сервисом
	CONST DATE_UPDATED = NULL;
	
	protected $strModuleId = null;
	protected $arEventHandlers = array();
	protected $intProfileId = null;
	protected $arProfile = array();
	protected $arParams = array(); // Ref to $this->arProfile['PARAMS']
	protected $arData = array();
	protected $arSession = array();
	protected $arFieldsCached = array();
	protected $arSessionExportData = null;
	protected $strParamTagName = 'param';
	protected $strCategoryNameSeparator = '|';
	protected $bCategoryCustomName = false;
	protected $fTimeStart = null;
	
	
	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId) {
		$this->includeClasses();
		$this->strModuleId = $strModuleId;
		$this->fTimeStart = microtime(true);
	}
	
	/**
	 *	Set static::$strModuleId (for static purposes)
	 */
	public static function setStaticModuleId($strModuleId){
		static::$strStaticModuleId = $strModuleId;
	}
	
	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode(){
		return 'PLUGIN';
	}
	
	/**
	 * Get plugin short name
	 */
	abstract public static function getName();
	
	/**
	 *	Is current plugin universal?
	 */
	public static function isUniversal(){
		
	}
	
	/**
	 * Get plugin description.
	 */
	public static function getDescription() {
		$strFile = static::getFolder().'/.description.php';
		if(is_file($strFile) && filesize($strFile)) {
			Loc::loadMessages($strFile);
			ob_start();
			require $strFile;
			$strResult = ob_get_clean();
			if(strlen(static::DATE_UPDATED)){
				$strResult .= PHP_EOL.Helper::getMessage('ACRIT_EXP_DATE_UPDATED', array('#DATE#' => static::DATE_UPDATED));
			}
			return trim($strResult);
		}
		return false;
	}
	
	/**
	 * Get plugin example
	 */
	public static function getExample() {
		$strFile = static::getFolder().'/.example.php';
		if(is_file($strFile) && filesize($strFile)) {
			Loc::loadMessages($strFile);
			ob_start();
			require $strFile;
			$strResult = ob_get_clean();
			return trim($strResult);
		}
		return false;
	}
	
	/**
	 *	Is it subclass?
	 */
	public static function isSubclass(){
		return static::$bSubclass;
	}
	
	/**
	 *	Is it need to offers preprocess? (see plugin 'sorokonogka')
	 */
	public function isOffersPreprocess(){
		return false;
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
	 *	Are categories updateable?
	 *	Update available if (areCategoriesExport() || areCategoriesUpdate());
	 */
	public function areCategoriesUpdate(){
		return false;
	}
	
	/**
	 *	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict(){
		return false;
	}
	
	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){
		return false;
	}
	
	/**
	 *	Hide categories update button
	 */
	public function hideCategoriesUpdateButton(){
		return false;
	}
	
	/**
	 *	Get all categories (/a/b/c, /d/e/f, ...)
	 */
	public function getCategoriesList($intProfileID){
		return false;
	}
	
	/**
	 *	Update categories from server
	 */
	public function updateCategories($intProfileID){
		return false;
	}
	
	/**
	 *	Get categories date update
	 */
	public function getCategoriesDate(){
		return false;
	}
	
	/**
	 *	We can add custom html to categories block;
	 */
	public function categoriesCustomActions(){
		return '';
	}
	
	/**
	 *	Is available category name as general field?
	 */
	public function isCategoryCustomName(){
		return $this->bCategoryCustomName;
	}
	
	/**
	 *	Is used category name as general field?
	 */
	public function isCategoryCustomNameUsed(){
		$bResult = false;
		foreach($this->arProfile['IBLOCKS'] as $arIBlock){
			if($arIBlock['PARAMS']['CATEGORIES_REDEFINITION_SOURCE'] == CategoryRedefinition::SOURCE_CUSTOM){
				$bResult = true;
				break;
			}
		}
		return $bResult;
	}
	
	/**
	 * Get lang message
	 */
	public static function getMessage($strLangKey, $arReplace=array()){
		$strPhrase = 'ACRIT_EXP_'.(static::getCode()).'_'.$strLangKey;
		$strMessage = Helper::getMessage($strPhrase, $arReplace);
		if(empty($strMessage)){
			$strClass = get_called_class();
			$arClasses = array($strClass);
			$i = 0;
			while(true){
				if (++$i > 10) break;
				$strClass = get_parent_class($strClass);
				if($strClass !== false) {
					$arClasses[] = $strClass;
				}
			}
			foreach($arClasses as $strClass){
				$strPhrase = 'ACRIT_EXP_'.($strClass::getCode()).'_'.$strLangKey;
				$strMessage = Helper::getMessage($strPhrase, $arReplace);
				if(!empty($strMessage)){
					break;
				}
			}
		}
		if(empty($strMessage)) {
			$strMessage = Helper::getMessage($strLangKey, $arReplace);
		}
		return $strMessage;
	}
	
	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = array();
		/*
		$arResult[] = array(
			'DIV' => 'mytab',
			'TAB' => 'My tab name',
			'TITLE' => 'My tab title',
			'SORT' => 5,
			'FILE' => __DIR__.'/tabs/mytab.php',
		);
		*/
		return $arResult;
	}
	
	/**
	 *	Get custom sub tabs for profile edit
	 */
	public function getAdditionalSubTabs($intProfileID, $intIBlockID){
		$arResult = array();
		/*
		$arResult[] = array(
			'DIV' => 'mysubtab',
			'TAB' => 'My subtab name',
			'TITLE' => 'My subtab description',
			'SORT' => 5,
			'FILE' => __DIR__.'/subtabs/mysubtab.php',
		);
		*/
		return $arResult;
	}
	
	/**
	 *	Get folder of current test (child class)
	 */
	protected static function getFolder($strRelative=false){
		$strClassName = get_called_class();
		$obReflectionClass = new \ReflectionClass($strClassName);
		$strFileName = $obReflectionClass->getFileName();
		unset($obReflectionClass);
		$strResult = pathinfo($strFileName,PATHINFO_DIRNAME);
		if($strRelative) {
			$strResult = substr($strResult,strlen($_SERVER['DOCUMENT_ROOT']));
		}
		return $strResult;
	}
	
	/**
	 *	Get list of supported currencies
	 */
	public function getSupportedCurrencies(){
		return array('RUB');
	}
	
	/**
	 *	
	 */
	public function isStepByStepMode(){
		return false;
	}
	
	/**
	 *	Show settings for field
	 */
	public function showFieldSettings($strFieldCode, $strFieldType, $strFieldName, $arParams, $strPosition){
		return '';
	}
	
	/**
	 *	Get steps
	 */
	public function getSteps(){
		return array();
	}
	
	/* HELPERS FOR SIMILAR XML-TYPES */
	
	/**
	 *	Get XML element section ID (for db field 'SECTION_ID')
	 */
	protected static function getElement_SectionID($intProfileID, $arElement){
		$intSectionID = 0;
		if($arElement['IBLOCK_SECTION_ID']){
			$intSectionID = $arElement['IBLOCK_SECTION_ID'];
		}
		elseif($arElement['PARENT']['IBLOCK_SECTION_ID']){
			$intSectionID = $arElement['PARENT']['IBLOCK_SECTION_ID'];
		}
		return $intSectionID;
	}
	
	/* END OF BASE METHODS FOR XML SUBCLASSES */
	
	/**
	 *	Include selected plugin's file
	 */
	protected function includeFile($strFile, $callbackFile){
		global $APPLICATION;
		$strClass = get_called_class();
		$arFiles = array();
		while(true){
			$strPathAbsolute = $strClass::getFolder().'/'.$strFile;
			if(is_file($strPathAbsolute) && filesize($strPathAbsolute)>0){
				$arFiles[] = $strPathAbsolute;
			}
			$strClass = get_parent_class($strClass);
			if(!is_subclass_of($strClass, __CLASS__)){
				break;
			}
		}
		$arFiles = array_reverse($arFiles);
		$strResult = '';
		foreach($arFiles as $strFile){
			$strResult .= call_user_func_array($callbackFile, array($strFile, $bWrite));
		}
		return $strResult;
	}
	
	public function includeCss(){
		return $this->includeFile('style.css', function($strFile){
			return '<style>'.file_get_contents($strFile).'</style>';
		});
	}
	
	public function includeJs(){
		return $this->includeFile('script.js', function($strFile){
			return '<script>'.file_get_contents($strFile).'</script>';
		});
	}
	
	/**
	 *	Include classes
	 */
	public function includeClasses(){
		//
	}
	
	/**
	 *	Show plugin settings
	 */
	public function showSettings(){
		return '';
	}
	
	/**
	 *	Include html item with outbut buffering (for eg, it can be used for settings)
	 */
	public function includeHtml($strFilename, $arParams=[]){
		ob_start();
		require $strFilename;
		return trim(ob_get_clean());
	}
	
	/**
	 *	Show step settings
	 */
	protected function showStepByStepSettings(){
		if($this->isStepByStepMode()){
			$this->arProfile['PARAMS']['STEP_BY_STEP_COUNT'] = IntVal($this->arProfile['PARAMS']['STEP_BY_STEP_COUNT']);
			if($this->arProfile['PARAMS']['STEP_BY_STEP_COUNT'] <= 0){
				$this->arProfile['PARAMS']['STEP_BY_STEP_COUNT'] = '';
			}
			if(trim($this->arProfile['PARAMS']['STEP_BY_STEP_COUNT']) == ''){
				$this->arProfile['PARAMS']['STEP_BY_STEP'] = 'N';
			}
			$arLastExportedItem = unserialize($this->arProfile['LAST_EXPORTED_ITEM']);
			ob_start();
			?>
				<table class="acrit-exp-plugin-settings" style="width:100%;">
					<tbody>
						<tr id="row_STEP_BY_STEP">
							<td width="40%" class="adm-detail-content-cell-l">
								<label for="checkbox_STEP_BY_STEP">
									<?=Helper::ShowHint(Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_STEP_BY_STEP_HINT'));?>
									<?=static::getMessage('ACRIT_EXP_PLUGIN_FIELD_STEP_BY_STEP');?>:
								</label>
							</td>
							<td width="60%" class="adm-detail-content-cell-r">
								<input type="hidden" name="PROFILE[PARAMS][STEP_BY_STEP]" value="N" />
								<input type="checkbox" name="PROFILE[PARAMS][STEP_BY_STEP]" value="Y" id="checkbox_STEP_BY_STEP"
									<?if($this->arProfile['PARAMS']['STEP_BY_STEP']=='Y'):?>checked="checked"<?endif?> />
							</td>
						</tr>
						<tr id="row_STEP_BY_STEP_COUNT" style="display:none">
							<td width="40%" class="adm-detail-content-cell-l">
								<?=Helper::ShowHint(Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_STEP_BY_STEP_COUNT_HINT'));?>
								<?=static::getMessage('ACRIT_EXP_PLUGIN_FIELD_STEP_BY_STEP_COUNT');?>:
							</td>
							<td width="60%" class="adm-detail-content-cell-r">
								<input type="text" name="PROFILE[PARAMS][STEP_BY_STEP_COUNT]" 
									value="<?=$this->arProfile['PARAMS']['STEP_BY_STEP_COUNT'];?>" size="40" 
									placeholder="<?=static::getMessage('ACRIT_EXP_PLUGIN_FIELD_STEP_BY_STEP_COUNT_PLACEHOLDER');?>" />
							</td>
						</tr>
						<?if(is_array($arLastExportedItem) && is_numeric($arLastExportedItem['STEP'])):?>
							<tr id="row_STEP_BY_STEP_INDEX">
								<td width="40%" class="adm-detail-content-cell-l">
								<?=static::getMessage('ACRIT_EXP_PLUGIN_FIELD_STEP_BY_STEP_INDEX');?>:
								</td>
								<td width="60%" class="adm-detail-content-cell-r">
									<span style="vertical-align:middle;"><?=$arLastExportedItem['STEP'];?></span>
									&nbsp;
									<input type="button" data-role="step-export-reset"
										value="<?=static::getMessage('ACRIT_EXP_PLUGIN_FIELD_STEP_BY_STEP_RESET');?>" />
								</td>
							</tr>
						<?endif?>
					</tbody>
				</table>
			<?
			return ob_get_clean();
		}
		else{
			return '';
		}
	}
	
	/**
	 *	Show link for open file
	 */
	public function showFileOpenLink($strFile=false, $strTitle=false){
		ob_start();
		if($strFile === false){
			$strFile = $this->getExportFileName();
		}
		if($strTitle === false){
			$strTitle = Helper::getMessage('ACRIT_EXP_FILE_OPEN');
		}
		elseif($strTitle === true){
			$strTitle = $strFile;
		}
		if(strlen($strFile) && preg_match('#^(http|https)://.*?$#i', $strFile)){
			?>
			<a href="<?=$strFile;?>" target="_blank" title="<?=Helper::getMessage('ACRIT_EXP_URL_OPEN_TITLE');?>"
				class="acrit-exp-file-open-link">
				<?=$strTitle;?>
			</a>
			<?
		}
		elseif(strlen($strFile) && is_file($_SERVER['DOCUMENT_ROOT'].$strFile)) {
			$strFileHref = $strFile;
			if(Helper::getOption($this->strModuleId, 'show_export_file_with_uniq_argument') != 'N'){
				$strFileHref = $strFileHref.(strpos($strFileHref, '?') === false ? '?' : '&').time();
			}
			?>
			<a href="<?=$strFileHref;?>" target="_blank" title="<?=Helper::getMessage('ACRIT_EXP_FILE_OPEN_TITLE');?>"
				class="acrit-exp-file-open-link">
				<?=$strTitle;?>
				(<?=\CFile::FormatSize(filesize($_SERVER['DOCUMENT_ROOT'].$strFile));?>)
			</a>
			<?
		}
		elseif(strlen($strFile)){
			print $strFile;
		}
		return ob_get_clean();
	}
	
	/**
	 *	Get export file name
	 */
	public function getExportFilename(){
		$strExportFileName = &$this->arProfile['PARAMS']['EXPORT_FILE_NAME'];
		if(strlen($strExportFileName)){
			return $strExportFileName;
		}
		return false;
	}
	
	/**
	 *	Get temporary file name
	 */
	protected function getExportFilenameTmp($strSuffix=null, $bTmpExtension=true){
		#$strTmpDir = Profile::getTmpDir($this->arProfile['ID']);
		$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$this->arProfile['ID']]);
		$strSuffix = is_string($strSuffix) ? '.'.$strSuffix : '';
		$strExt = $bTmpExtension ? '.tmp' : '';
		$strFilename = $strTmpDir.'/'.pathinfo($this->arParams['EXPORT_FILE_NAME'], PATHINFO_BASENAME).$strSuffix.$strExt;
		$strFilename = substr($strFilename, strlen($_SERVER['DOCUMENT_ROOT']));
		return $strFilename;
	}
	
	/**
	 *	Get archive file name
	 */
	public function getExportFilenameArchive($strType){
		$strResult = $this->getExportFileName();
		if(strlen($strResult)){
			return Helper::changeFileExt($strResult, toLower($strType));
		}
		return false;
	}
	
	/**
	 *	Custom ajax actions for plugins
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult){
		// nothing, code in plugin
	}
	
	/* *** */
	
	/**
	 *	Set profile array
	 */
	public function setProfileArray(array &$arProfile, $bSaving=false){
		$this->arProfile = $arProfile;
		$this->arParams = &$this->arProfile['PARAMS'];
		$this->intProfileId = $arProfile['ID'];
	}
	
	/**
	 *	Get profile array
	 */
	public function getProfileArray(){
		return $this->arProfile;
	}
	
	/**
	 *	Set profile param ($arProfile['PARAMS']) and save it
	 */
	public function setProfileParam($arParams){
		return Helper::call($this->strModuleId, 'Profile', 'setParam', [$this->arProfile['ID'], $arParams]);
	}
	
	/**
	 *	Get default directory for export
	 */
	public function getDefaultDirectory(){
		return '/upload/'.$this->strModuleId;
	}
	
	/**
	 *	Get default directory for export
	 */
	public function getDefaultExportFilename(){
		return 'file.xml';
	}
	
	/**
	 * Get adailable fields for current plugin
	 * @return array
	 */
	abstract public function getFields($intProfileID, $intIBlockID, $bAdmin=false);
	
	/**
	 *	Add additional fields to result
	 */
	protected function addAdditionalFields(&$arResult, $intSort=null){
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]); // ToDo: $strModuleId
		$intSort = IntVal($intSort) > 0 ? $intVal($intSort) : 1000000;
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
				'CODE' => Helper::call($this->strModuleId, 'AdditionalField', 'getFieldCode', [$arAdditionalField['ID']]), // ToDo: $strModuleId
				'NAME' => $arAdditionalField['NAME'],
				'SORT' => $intSort++,
				'DESCRIPTION' => '',
				'REQUIRED' => false,
				'MULTIPLE' => true,
				'IS_ADDITIONAL' => true,
				'DEFAULT_VALUE' => $arDefaultValue,
			));
		}
	}
	
	/**
	 *	Set strModuleId to all fields
	 */
	/*
	protected function setFieldsModuleId(&$arFields){
		foreach($arFields as &$obField){
			$obField->setModuleId($this->strModuleId);
		}
	}
	*/
	
	/**
	 *	Same as getFields but with caching
	 */
	public function getFieldsCached($intProfileID, $intIBlockID, $bUseKeys=false){
		$strKey = $intProfileID.'_'.$intIBlockID;
		$arResult = &$this->arFieldsCached[$strKey];
		if(!isset($arResult)){
			$arResult = $this->getFields($intProfileID, $intIBlockID);
			foreach($arResult as $key => $obField){
				$arResult[$key]->setModuleId($this->strModuleId);
			}
		}
		if($bUseKeys){
			$arResultTmp = [];
			foreach($arResult as $obField){
				$arResultTmp[$obField->getCode()] = $obField;
			}
			unset($arResult);
			return $arResultTmp;
		}
		return $arResult;
	}
	
	/**
	 *	Sort all fields
	 */
	protected function sortFields(&$arFields){
		usort($arFields, function($obField1, $obField2){
			if ($obField1->getSort() == $obField2->getSort()) {
				return 0;
			}
			return ($obField1->getSort() < $obField2->getSort()) ? -1 : 1;
		});
	}
	
	/**
	 *	Remove fields by Code
	 */
	protected function removeFields(&$arFieldsAll, $arRemoveFields){
		if(!is_array($arRemoveFields)){
			if(strlen($arRemoveFields)){
				$arRemoveFields = array($arRemoveFields);
			}
			else {
				$arRemoveFields = array();
			}
		}
		foreach($arFieldsAll as $key => $obField){
			if(in_array($obField->getCode(), $arRemoveFields)){
				unset($arFieldsAll[$key]);
			}
		}
	}
	
	/**
	 *	Modify one field
	 */
	protected function modifyField(&$arFieldsAll, $strFieldCode, $arNewFieldData){
		foreach($arFieldsAll as $key => $obField){
			if($obField->getCode() == $strFieldCode) {
				$arInitialParam = $obField->getInitialParams();
				$arNewParams = array_merge($arInitialParam, $arNewFieldData);
				$arFieldsAll[$key] = new Field($arNewParams);
			}
		}
	}
	
	/**
	 *	Add UTM-fields
	 */
	protected function addUtmFields(&$arFieldsAll, $intSortStart=1000, $strUtmSourceDefault=false, $strUtmMediumDefault=false, $bAddHeader=false){
		$strUtmSourceDefault = is_string($strUtmSourceDefault) ? $strUtmSourceDefault : '';
		$strUtmMediumDefault = is_string($strUtmMediumDefault) ? $strUtmMediumDefault : 'ppc';
		if($bAddHeader){
			$arFieldsAll[] = new Field(array(
				'CODE' => '_UTM_FIELDS',
				'SORT' => $intSortStart++,
				'NAME' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_FIELDS_NAME'),
				'IS_HEADER' => true,
			));
		}
		$arFieldsAll[] = new Field(array(
			'CODE' => 'UTM_SOURCE',
			'DISPLAY_CODE' => 'utm_source',
			'NAME' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_SOURCE_NAME'),
			'SORT' => $intSortStart++,
			'DESCRIPTION' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_SOURCE_DESC'),
			'REQUIRED' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => $strUtmSourceDefault,
				),
			),
			'PARAMS' => array(
				'URLENCODE' => 'Y',
			),
		));
		$arFieldsAll[] = new Field(array(
			'CODE' => 'UTM_MEDIUM',
			'DISPLAY_CODE' => 'utm_medium',
			'NAME' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_MEDIUM_NAME'),
			'SORT' => $intSortStart++,
			'DESCRIPTION' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_MEDIUM_DESC'),
			'REQUIRED' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => $strUtmMediumDefault,
				),
			),
			'PARAMS' => array(
				'URLENCODE' => 'Y',
			),
		));
		$arFieldsAll[] = new Field(array(
			'CODE' => 'UTM_CAMPAIGN',
			'DISPLAY_CODE' => 'utm_campaign',
			'NAME' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_CAMPAIGN_NAME'),
			'SORT' => $intSortStart++,
			'DESCRIPTION' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_CAMPAIGN_DESC'),
			'REQUIRED' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
			'PARAMS' => array(
				'URLENCODE' => 'Y',
			),
		));
		$arFieldsAll[] = new Field(array(
			'CODE' => 'UTM_CONTENT',
			'DISPLAY_CODE' => 'utm_content',
			'NAME' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_CONTENT_NAME'),
			'SORT' => $intSortStart++,
			'DESCRIPTION' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_CONTENT_DESC'),
			'REQUIRED' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
			'PARAMS' => array(
				'URLENCODE' => 'Y',
			),
		));
		$arFieldsAll[] = new Field(array(
			'CODE' => 'UTM_TERM',
			'DISPLAY_CODE' => 'utm_term',
			'NAME' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_TERM_NAME'),
			'SORT' => $intSortStart++,
			'DESCRIPTION' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_UTM_TERM_DESC'),
			'REQUIRED' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
			'PARAMS' => array(
				'URLENCODE' => 'Y',
			),
		));
	}
	
	/**
	 *	Add UTM to URL
	 */
	public function addUtmToUrl(&$strUrl, $arFields, $bEscape=true){
		$arUtmValues = array();
		$arUtm = array(
			'utm_source' => 'UTM_SOURCE',
			'utm_medium' => 'UTM_MEDIUM',
			'utm_campaign' => 'UTM_CAMPAIGN',
			'utm_content' => 'UTM_CONTENT',
			'utm_term' => 'UTM_TERM',
		);
		foreach($arUtm as $strUtmParam => $strUtmCode){
			if(strlen(trim($arFields[$strUtmCode]))){
				$arUtmValues[] = $strUtmParam.'='.$arFields[$strUtmCode];
			}
		}
		$strAmp = $bEscape ? '&amp;' : '&';
		if(!empty($arUtmValues)){
			$strQuery = parse_url($strUrl, PHP_URL_QUERY);
			$strUrl .= ($strQuery ? $strAmp : '?').implode($strAmp, $arUtmValues);
		}
		unset($arUtmValues, $arUtm, $strUtmParam, $strUtmCode, $strQuery);
	}
	
	/**
	 *	Handler 'onBeforeProcessField'
	 */
	public function onBeforeProcessField(&$obField, &$arField, &$arElement, &$arProfile){
		return null;
	}
	
	/**
	 *	Handler 'onAfterProcessField'
	 */
	public function onAfterProcessField($mResult, $obField, $arField, $arElement, $arProfile){
		return null;
	}
	
	/**
	 *	Handler 'onBeforeProcessElement'
	 */
	protected function onBeforeProcessElement(&$arProfile, &$intIBlockID, &$arElement, &$arFields){
		return null;
	}
	
	/**
	 *	Handler 'onProcessElement'
	 *	$mData is $arXml, $arJson, $obCsv, ...
	 */
	protected function onProcessElement(&$arProfile, &$intIBlockID, &$arElement, &$arFields, &$mData){
		return null;
	}
	
	/**
	 *	Handler 'onAfterProcessElement'
	 *	$arResult is array for save generated data
	 */
	protected function onAfterProcessElement(&$arProfile, &$intIBlockID, &$arElement, &$arFields, &$arResult){
		return null;
	}
	
	/**
	 *	Handler 'onGetCategoryTag'
	 */
	protected function onGetCategoryTag(&$arCategoryTag, $intCategoryId, $arCategory, $intMode){
		return null;
	}
	
	/**
	 *	Process single element
	 *	@return array
	 */
	abstract public function processElement($arProfile, $intIBlockID, $arElement, $arFields);
	
	/**
	 *	Get element first sections before save to ExportDataTable
	 */
	protected function prepareSaveSections(&$arSectionsId, &$arProfile, &$intIBlockID, &$arElement, &$arFields){
		$bResult = false;
		$arFieldsAll = $this->getFieldsCached($arProfile['ID'], $intIBlockID);
		if($this->isCategoryCustomName() && $this->isCategoryCustomNameUsed()){
			$arSectionsId = [];
			foreach($arFieldsAll as $obField){
				if($obField->isCategoryCustomName()){
					$strCode = $obField->getCode();
					$strCategoryName = $arFields[$strCode];
					unset($arFields[$strCode]);
					$arSectionsId[] = $this->addCustomCategory($strCategoryName, $intIBlockID);
					$bResult = true;
					break;
				}
			}
			unset($arFieldsAll);
		}
		# Remove system field from $arFields
		else{
			foreach($arFieldsAll as $obField){
				if($obField->isCategoryCustomName()){
					$strCode = $obField->getCode();
					unset($arFields[$strCode]);
					break;
				}
			}
		}
		return $bResult;
	}
	
	/**
	 *	Add custom category to current export
	 */
	protected function addCustomCategory($strCategoryName, $intIBlockID){
		$intResult = false;
		$resCategory = $this->call('CategoryCustomName::getList', [
			'filter' => [
				'PROFILE_ID' => $this->intProfileId,
				'=CATEGORY_NAME' => $strCategoryName,
			]
		]);
		if($arCategory = $resCategory->fetch()){
			$intResult = $arCategory['CATEGORY_ID'];
		}
		else{
			$intNextCategoryId = $this->getCustomCategoryNextId();
			$obResult = $this->call('CategoryCustomName::add', [
				'PROFILE_ID' => $this->intProfileId,
				'IBLOCK_ID' => $intIBlockID,
				'CATEGORY_NAME' => $strCategoryName,
				'CATEGORY_ID' => $intNextCategoryId,
			]);
			if($obResult->isSuccess()){
				$intResult = $intNextCategoryId;
			}
		}
		return $intResult;
	}
	
	/**
	 *	Pseudo autoincrement
	 */
	protected function getCustomCategoryNextId(){
		$intResult = 1;
		$resCategory = $this->call('CategoryCustomName::getList', [
			'order' => [
				'CATEGORY_ID' => 'DESC',
			],
			'filter' => [
				'PROFILE_ID' => $this->intProfileId,
			]
		]);
		if($arCategory = $resCategory->fetch()){
			$intResult = $arCategory['CATEGORY_ID'] + 1;
		}
		return $intResult;
	}
	
	/**
	 *	Show results, just for popup (not for Cron)
	 */
	public function showResults($arSession){
		return '';
	}
	
	/**
	 *	Get plugin params from $this->arProfile['PARAMS']
	 */
	public function getPluginParams(){
		return $this->arProfile['PARAMS']['_PLUGINS'][static::getCode()];
	}
	public function getPluginParamsInputName(){
		return 'PROFILE[PARAMS][_PLUGINS]['.static::getCode().']';
	}
	
	/**
	 *	Execute command from console
	 */
	public function executeConsole($strCommand){
		ob_start();
		eval($strCommand.';');
		return ob_get_clean();
	}
	
	/**
	 *	Set last exported item
	 */
	public function setLastExportedItem($intProfileID, $arItem, $intStep=false){
		if(is_array($arItem) && !empty($arItem) && $intStep>0){
			$arItem['STEP'] = $intStep;
			$arItem = serialize($arItem);
		}
		else {
			$arItem = null;
		}
		return Helper::call($this->strModuleId, 'Profile', 'update', [$intProfileID, [
			'LAST_EXPORTED_ITEM' => $arItem,
			'_QUIET' => 'Y',
		]])->isSuccess();
	}
	
	/**
	 *	Show messages in profile edit
	 */
	public function showMessages(){
		//
	}
	
	/**
	 *	Add message to log
	 */
	public function addToLog($strMessage, $bDebug=false){
		return Log::getInstance($this->strModuleId)->add($strMessage, $this->intProfileId, $bDebug);
	}
	
	/**
	 *	Log and print message
	 */
	protected function logAndPrintError($strLangPhraseID, $intProfileID){
		$strMessage = static::getMessage($strLangPhraseID);
		if(strlen($strMessage)){
			Log::getInstance($this->strModuleId)->add($strMessage, $intProfileID);
			print Helper::showError($strMessage);
		}
		unset($strMessage);
	}
	
	/**
	 *	Get datetime format for coverting
	 */
	public static function getDateFormats(){
		return array(
			'Y-m-d H:i',
			'Y-m-d h:i',
			'Y-m-d h:i A',
			'd/m/Y',
			'Y/m/d',
			'd.m.Y',
			'Y-m-d h:i:s',
			'YmdThis',
			'Y/m/d h:i:s',
			'd/m/Y h:i:s',
			'd.m.Y h:i:s',
			'c',
			'Y-m-d h:i:s ±h',
			'Y-m-d h:i:s ±hi',
			'Y-m-d h:i:s ±h:i',
			'Y/m/d h:i:s ±h',
			'Y/m/d h:i:s ±hi',
			'Y/m/d h:i:s ±h:i',
			'D/m/Y h:i:s ±h',
			'D/m/Y h:i:s ±hi',
			'D/m/Y h:i:s ±h:i',
			'd.m.Y h:i:s±h',
			'd.m.Y h:i:s±hi',
			'd.m.Y h:i:s±h:i',
			'Ymd',
			'Y-m-d',
			'YmdThis±h',
			'YmdThis±hi',
			'Y-m-dTh:i:s±h',
			'Y-m-dTh:i:s±h:i',
			'Y-m-dTh:i:s',
			'YmdThi±h',
			'YmdThi±hi',
			'Y-m-dTh:i±h',
			'Y-m-dTh:i±h:i',
			'YmdThi',
			'Y-m-dTh:i',
		);
	}
	
	/**
	 *	Wrapper for work with generated data on export step
	 */
	protected function processExportData($mCallback, &$arParams=[]){
		if(!is_callable($mCallback)){
			return Exporter::RESULT_ERROR;
		}
		if(!is_array($arParams)){
			$arParams = [];
		}
		# Items per one query (sql LIMIT)
		$arParams['LIMIT'] = is_numeric($arParams['LIMIT']) && $arParams['LIMIT'] > 0 ? IntVal($arParams['LIMIT']) : 1000;
		# Items per step (for stepped export)
		$arParams['PER_STEP'] = is_numeric($arParams['PER_STEP']) && $arParams['PER_STEP'] > 0 ? IntVal($arParams['PER_STEP']) : null;
		#
		$mResult = Exporter::RESULT_SUCCESS;
		$intOffset = 0;
		$intProcessedCount = 0;
		$bStepExportFoundFirstItem = false;
		$bStepExport = $arParams['PER_STEP'] > 0;
		while(true){
			$intLimit = $arParams['LIMIT'];
			$strSortOrder = ToUpper($this->arProfile['PARAMS']['SORT_ORDER']);
			if(!in_array($strSortOrder, ['ASC', 'DESC'])){
				$strSortOrder = 'ASC';
			}
			$arLastExportedItem = unserialize($this->arProfile['LAST_EXPORTED_ITEM']);
			if(!is_array($arLastExportedItem)){
				$arLastExportedItem = [];
			}
			$arQuery = [
				'filter' => [
					'PROFILE_ID' => $this->intProfileId,
					'!TYPE' => ExportData::TYPE_DUMMY,
				],
				'order' => [
					'SORT' => $strSortOrder,
					'ELEMENT_ID' => 'ASC',
				],
				'select' => [
					'IBLOCK_ID',
					'ELEMENT_ID',
					'SECTION_ID',
					'TYPE',
					'DATA',
					'DATA_MORE',
				],
				'limit' => $intLimit,
				'offset' => $intOffset * $intLimit,
			];
			if(isset($arLastExportedItem['SORT']) && isset($arLastExportedItem['ID'])){
				$arQuery['filter']['>=SORT'] = $arLastExportedItem['SORT'];
			}
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$intCount = 0;
			while($arItem = $resItems->fetch()){
				$intCount++;
				if(isset($arLastExportedItem['SORT']) && isset($arLastExportedItem['ID'])){ # For step export
					if(!$bStepExportFoundFirstItem){
						$bStepExportFoundFirstItem = $arItem['ID'] > $arLastExportedItem['ID'];
					}
					if(!$bStepExportFoundFirstItem){
						continue;
					}
				}
				# Process callback
				call_user_func_array($mCallback, [$arItem, &$arParams]);
				# Handling stop
				$bCron = Exporter::getInstance($this->strModuleId)->isCron();
				$bStopByLimit = $bStepExport && ++$intProcessedCount >= $arParams['PER_STEP'];
				$bStopByTime = $bCron ? false : !Exporter::getInstance($this->strModuleId)->haveTime();
				if($bStopByLimit || $bStopByTime){
					# Step is over
					$intStepIndex = $bStopByLimit ? IntVal($arLastExportedItem['STEP']) + 1 : IntVal($arLastExportedItem['STEP']);
					$this->setLastExportedItem($this->intProfileId, [
						'SORT' => $arItem['SORT'],
						'ID' => $arItem['ID'],
					], $intStepIndex);
					$this->arSessionExportData['STEP_INDEX'] = $intStepIndex;
					# 
					$mResult = Exporter::RESULT_CONTINUE;
					break 2;
				}
			}
			if($intCount < $intLimit){
				break;
			}
			$intOffset++;
		}
		return $mResult;
	}
	
	/**
	 *	Get items count for export
	 */
	protected function processExportDataGetCount(){
		$arQuery = [
			'select' => ['CNT'],
			'filter' => [
				'PROFILE_ID' => $this->intProfileId,
				'!TYPE' => ExportData::TYPE_DUMMY,
			],
			'runtime' => [
				new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'),
			],
		];
		$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
		if($arItem = $resItems->fetch()){
			return IntVal($arItem['CNT']);
		}
		return 0;
	}
	
	/**
	 *	For use byAJAX
	 */
	protected function resetLastExportedItem(){
		$this->setLastExportedItem($this->intProfileId, null, null);
	}
	
	/**
	 *	Save export file (replace tmp file to real file)
	 */
	protected function saveExportFile($strFileTmp, $strFileReal, &$strErrorMessage){
		$strErrorMessage = null;
		if(is_file($strFileReal)){
			@unlink($strFileReal);
		}
		if(!Helper::createDirectoriesForFile($strFileReal)){
			$strErrorMessage = Helper::getMessage('ACRIT_CORE_ERROR_CREATE_DIRECORY', array(
				'#DIR#' => Helper::getDirectoryForFile($strFileReal),
			));
			return false;
		}
		if(is_file($strFileReal)){
			@unlink($strFileReal);
		}
		if(!@rename($strFileTmp, $strFileReal)){
			@unlink($strFileTmp);
			$strErrorMessage = Helper::getMessage('ACRIT_CORE_FILE_NO_PERMISSIONS', array(
				'#FILE#' => $strFileReal,
			));
			return false;
		}
		return true;
	}
	
	/**
	 *	Get main IBlock
	 */
	protected function getMainIBlock(){
		if(is_array($this->arProfile['IBLOCKS'])){
			foreach($this->arProfile['IBLOCKS'] as $arIBlock){
				if($arIBlock['IBLOCK_MAIN'] == 'Y'){
					return $arIBlock;
				}
			}
		}
		return null;
	}
	
	/**
	 *	Write <param>
	 */
	protected function getXmlTag_Param($arProfile, $intIBlockID, $arFields){
		$intProfileID = $arProfile['ID'];
		$arIBlockFields = &$arProfile['IBLOCKS'][$intIBlockID]['FIELDS'];
		$mResult = NULL;
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]); // ToDo: $strModuleId
		if(!empty($arAdditionalFields)) {
			$mResult = array();
			foreach($arAdditionalFields as $arAdditionalField){
				$strFieldCode = $arAdditionalField['FIELD'];
				if(!Helper::isEmpty($arFields[$strFieldCode])) {
					$arAttributes = array(
						'name' => $arAdditionalField['NAME'],
					);
					$arAdditionalAttributes = $arIBlockFields[$strFieldCode]['PARAMS']['ADDITIONAL_ATTRIBUTES'];
					if(is_array($arAdditionalAttributes) && is_array($arAdditionalAttributes['NAME']) && $arAdditionalAttributes['VALUE']){
						foreach($arAdditionalAttributes['NAME'] as $key => $strAttrName){
							$strAttrValue = $arAdditionalAttributes['VALUE'][$key];
							$arAttributes[$strAttrName] = $strAttrValue;
						}
					}
					if(is_array($arFields[$strFieldCode])){
						foreach($arFields[$strFieldCode] as $strValue){
							$mResult[] = array(
								'@' => $arAttributes,
								'#' => $strValue,
							);
						}
					}
					else{
						$mResult[] = array(
							'@' => $arAttributes,
							'#' => $arFields[$strFieldCode],
						);
					}
				}
			}
		}
		return $mResult;
	}
	
	/**
	 *	Check plugin data
			return [
				['TITLE' => '213123123', 'MESSAGE' => 'My error!', 'IS_ERROR' => true],
				['TITLE' => '213123123', 'MESSAGE' => 'My note!'],
			];
	 */
	public function checkData(){
		$arMessages = [];
		# Check filename is unique
		$T = microtime(true);
		$strExportFile = Helper::path($this->arParams['EXPORT_FILE_NAME']);
		if(strlen($strExportFile)){
			$arConflictProfilesId = [];
			$arFilter = ['!ID' => $this->intProfileId];
			$arProfiles = Helper::call($this->strModuleId, 'ProfileTable', 'getProfiles', [$arFilter, [], false, false]);
			foreach($arProfiles as $arProfile){
				if(strlen($arProfile['PARAMS']['EXPORT_FILE_NAME'])){
					if(Helper::path($arProfile['PARAMS']['EXPORT_FILE_NAME']) == $strExportFile){
						$arConflictProfilesId[] = $arProfile['ID'];
					}
				}
			}
			if(!empty($arConflictProfilesId)){
				$arMessages[] = [
					'TITLE' => Helper::getMessage('ACRIT_EXP_PLUGIN_CHECK_DATA_CONFLICT_FILENAME_TITLE', [
						'#FILENAME#' => $this->arParams['EXPORT_FILE_NAME'],
					]),
					'MESSAGE' => Helper::getMessage('ACRIT_EXP_PLUGIN_CHECK_DATA_CONFLICT_FILENAME_DESC', [
						'#ID#' => implode(', ', $arConflictProfilesId),
					]),
					'IS_ERROR' => true,
				];
			}
		}
		#
		return $arMessages;
	}
	
	/**
	 *	Wrapper for Helper::call
	 */
	public function call($strMethod, $arArguments){
		$arMethod = explode('::', $strMethod);
		return Helper::call($this->strModuleId, $arMethod[0], $arMethod[1], [$arArguments]);
	}
	
	/**
	 *	Get custom categories
	 */
	public function getCustomCatagories(){
		$arResult = [];
		$resCategories = $this->call('CategoryCustomName::getList', [
			'order' => [
				'CATEGORY_NAME' => 'ASC',
			],
			'filter' => [
				'PROFILE_ID' => $this->intProfileId,
			],
			'select' => [
				'CATEGORY_ID',
				'CATEGORY_NAME',
				'CATEGORY_PARENT_ID',
			],
		]);
		while($arCategory = $resCategories->fetch()){
			$arResult[intVal($arCategory['CATEGORY_ID'])] = [
				'NAME' => $arCategory['CATEGORY_NAME'],
				'PARENT_ID' => $arCategory['CATEGORY_PARENT_ID'],
			];
		}
		return $arResult;
	}
	
	/**
	 *	Save PHP array
	 */
	public function saveArrayToPhp($strFile, $arData){
		return file_put_contents($strFile, sprintf("<?php%sreturn %s;%s", PHP_EOL, var_export($arData, true), PHP_EOL));
	}
	
	/**
	 *	
	 */
	public function getLogContent(){
		return false;
	}
	
	/**
	 *	Used for teacher wizard
	 *	Some examples:
			If accessible required for just one of elements:
			'CALLBACK_BEFORE' => 'function(options, stepData){
				nextStepData.elements.each(function(){
					if(...){
						this.acritTeacherAccessible = true;
					}
				});
			}',
	 */
	public static function getDefaultTeacher(){
		$strLang = 'ACRIT_EXP_PLUGIN_TEACHER_';
		$strStepNameLang = $strLang.'STEP_NAME_';
		$strStepDescLang = $strLang.'STEP_DESC_';
		$arResult = [
			'DEBUG' => false,
			'CODE' => 'EXPORT_PROFILES_DEFAULT',
			'NAME' => Helper::getMessage($strLang.'NAME'),
			'TITLE' => Helper::getMessage($strLang.'TITLE'),
			'SPLASH_SCREEN' => [
				'DESCRIPTION' => Helper::getMessage($strLang.'SPLASH_SCREEN_DESCRIPTION'),
				'CSS' => '',
			],
			'TAB_CONTROL' => 'AcritExpProfile',
			'CLOSE_WINDOWS' => 'Y',
			'STEPS' => [
				'TABS_ALL' => [
					'ELEMENTS' => '$(".adm-detail-tab")',
					'ACCESSIBLE' => 'Y','TAB' => 'general',
				],
				# Tab: General
				'TAB_GENERAL' => [
					'ELEMENTS' => '$("#tab_cont_general")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
				],
				'ACTIVE' => [
					'ELEMENTS' => '$("input[name=\"PROFILE[ACTIVE]\"] + label")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
				],
				'NAME' => [
					'ELEMENTS' => '$("input[data-role=\"profile-name\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
				],
				'DESCRIPTION' => [
					'ELEMENTS' => '$("textarea#field_DESCRIPTION")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
				],
				'SORT' => [
					'ELEMENTS' => '$("input[name=\"PROFILE[SORT]\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
				],
				#
				'SITE_ID' => [
					'ELEMENTS' => '$("select#field_SITE_ID")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
				],
				'DOMAIN' => [
					'ELEMENTS' => '$("input[name=\"PROFILE[DOMAIN]\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
				],
				'IS_HTTPS' => [
					'ELEMENTS' => '$("input#field_IS_HTTPS + label")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
				],
				'AUTO_GENERATE' => [
					'ELEMENTS' => '$("input#field_AUTO_GENERATE + label")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
				],
				#
				'FORMAT' => [
					'CALLBACK_ELEMENTS' => 'function(options, stepData){
						return $("select#field_PLUGIN + span, select#field_PLUGIN + span + input, select#field_FORMAT + span");
					}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
					'CALLBACK_IN' => 'function(options, prevStepData, currStepData){
						let select = $("select#field_PLUGIN");
						this.handler = this.addHandler(select, "select2:open", $.proxy(function(){
							let dropdown = $("select#field_PLUGIN").parent().find(".select2-dropdown");
							this.strokeElement(dropdown, true, true);
						}, this));
					}',
					'CALLBACK_OUT' => 'function(options, prevStepData, currStepData){
						let select = $("select#field_PLUGIN");
						this.removeHandler(select, "select2:open", this.handler);
					}',
				],
				'PLUGIN_SETTINGS' => [
					'CALLBACK_ELEMENTS' => 'function(options, stepData){
						return $("#div_PLUGIN_SETTINGS");
					}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !$("#div_PLUGIN_SETTINGS").children().length;
					}',
					'CALLBACK_OUT' => 'function(){
						setTimeout(function(){$("#BX_file_dialog_close").trigger("click");}, 10);
					}',
					'CSS' => '
						#div_PLUGIN_SETTINGS {
							background:#f5f9f9;
						}
					',
				],
				'SUBMIT_APPLY' => [
					'ELEMENTS' => '$("input[type=\"submit\"][name=\"apply\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'general',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !$("#tr_PLUGIN_NEED_SAVE .adm-info-message").length;
					}',
					'CALLBACK_IN' => 'function(options, prevStepData, currStepData){
						this.addHandler(currStepData.elements, "click", $.proxy(function(){
							this.teacherClose(true);
						}, this));
					}',
				],
				# Tab: Structure
				'TAB_STRUCTURE' => [
					'CALLBACK_ELEMENTS' => 'function(options, stepData){
						return $("#tab_cont_structure");
					}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
				],
				'SELECT_IBLOCK' => [
					'CALLBACK_ELEMENTS' => 'function(options, stepData){
						return $("#field_IBLOCK + .select2-container");
					}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'CALLBACK_IN' => 'function(options, prevStepData, currStepData){
						let select = $("select#field_IBLOCK");
						this.handler = this.addHandler(select, "select2:open", $.proxy(function(){
							let dropdown = $("select#field_IBLOCK").parent().find(".select2-dropdown");
							this.strokeElement(dropdown, true, true);
						}, this));
					}',
					'CALLBACK_OUT' => 'function(options, prevStepData, currStepData){
						let select = $("select#field_IBLOCK");
						this.removeHandler(select, "select2:open", this.handler);
						if(!$("#field_IBLOCK").val() || !$("div[data-role=\"profile-iblock-settings\"]").length){
							alert("'.Helper::getMessage($strLang."SELECT_IBLOCK_ALERT").'");
							return false;
						}
					}',
				],
				'DELETE_IBLOCK' => [
					'CALLBACK_ELEMENTS' => 'function(options, stepData){
						return $("#field_IBLOCK_clear");
					}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
				],
				'SELECT_JUST_CATALOGS' => [
					'CALLBACK_ELEMENTS' => 'function(options, stepData){
						return $("#field_IBLOCK_just_catalogs");
					}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'CSS' => '
						#field_IBLOCK_just_catalogs {
							color:#fff;
						}
					',
				],
				'PREVIEW_IBLOCKS_BUTTON' => [
					'ELEMENTS' => '$("input[data-role=\"preview-iblocks\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'CALLBACK_IN' => 'function(options, prevStepData, currStepData){
						this.handler = this.addHandler(currStepData.elements, "click", function(){
							this.removeHandler(currStepData.elements, "click", this.handler);
							this.goNextDelay(10);
						});
					}',
					'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
						this.removeHandler(currStepData.elements, "click", this.handler);
						delete this.handler;
					}',
				],
				'PREVIEW_IBLOCKS_POPUP' => [
					'CALLBACK_ELEMENTS' => 'function(options, stepData){
						return $(".acrit-exp-table-iblocks-preview").closest(".bx-core-window");
					}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'CALLBACK_IN' => 'function(options, currStepData, nextStepData){
						if(!AcritExpPopupIBlocksPreview.isOpen){
							AcritExpPopupIBlocksPreview.Open();
						}
						this.handler = this.addHandler(window, "onWindowClose", function(popup){
							if(popup == AcritExpPopupIBlocksPreview && AcritExpPopupIBlocksPreview.isOpen){
								this.removeHandler(AcritExpPopupIBlocksPreview, "onWindowClose", this.handler);
								this.goNextDelay(10);
							}
						});
					}',
					'CALLBACK_BEFORE' => 'function(options, stepData){
						return AcritExpPopupIBlocksPreview.isOpen && !!$(".acrit-exp-table-iblocks-preview").length;
					}',
					'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
						this.removeHandler(window, "onWindowClose", this.handler);
						delete this.handler;
						AcritExpPopupIBlocksPreview.Close();
					}',
				],
				'SUBTABS' => [
					'CALLBACK_ELEMENTS' => 'function(options, stepData){
						return $("div[data-role=\"iblock-structure-settings-tabs\"] .adm-detail-subtabs");
					}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
				],
				# Subtab: Product fields
				'SUBTAB_FIELDS_PRODUCT' => [
					'CALLBACK_ELEMENTS' => 'function(options, stepData){
						return $("#view_tab_fields_product");
					}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'fields_product',
				],
				'PRODUCT_FIELD' => [
					'ELEMENTS' => '$("#fields_product tr[data-role=\"field_row\"]").first()',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'fields_product',
					'CALLBACK_IN' => 'function(options, currStepData, nextStepData){
						//let elements = $.proxy(nextStepData.callbackElements, this)(options, nextStepData);
						let elements = nextStepData.elements;
						elements.filter("tr").addClass("acrit_teacher_control_highlighted_absolute");
						//
						this.btnSelect = $("input[data-role=\"field-simple--button-select-const\"]", elements);
						this.btnSelect = this.btnSelect.add($("input[data-role=\"field-simple--value-title\"]"));
						this.handlerSelect = this.addHandler(this.btnSelect, "click", function(popup){
							let interval = setInterval($.proxy(function(){
								if(AcritExpPopupSelectField.isOpen && $(AcritExpPopupSelectField.DIV).is(":visible")){
									clearInterval(interval);
									let
										scrollTop = this.getElementsViewportScrollTop(elements.add(AcritExpPopupSelectField.DIV)),
										animateTime = 100;
									$("html, body").animate({scrollTop: scrollTop}, animateTime);
									setTimeout($.proxy(function(){
										this.strokeElement($(AcritExpPopupSelectField.DIV), true, true);
									}, this), animateTime);
								}
							}, this), 100);
						});
						//
						this.btnSettingsValue = $("input[data-role=\"field-simple--button-params\"]", elements);
						this.handlerSettingsValue = this.addHandler(this.btnSettingsValue, "click", function(popup){
							let interval = setInterval($.proxy(function(){
								if(AcritExpPopupValueSettings.isOpen && $(AcritExpPopupValueSettings.DIV).is(":visible")){
									clearInterval(interval);
									let
										scrollTop = this.getElementsViewportScrollTop(elements.add(AcritExpPopupValueSettings.DIV)),
										animateTime = 100;
									$("html, body").animate({scrollTop: scrollTop}, animateTime);
									setTimeout($.proxy(function(){
										this.strokeElement($(AcritExpPopupValueSettings.DIV), true, true);
									}, this), animateTime);
								}
							}, this), 100);
						});
						//
						this.btnSettingsField = $("input[data-role=\"field--button-params\"]", elements);
						this.handlerSettingsField = this.addHandler(this.btnSettingsField, "click", function(popup){
							let interval = setInterval($.proxy(function(){
								if(AcritExpPopupFieldSettings.isOpen && $(AcritExpPopupFieldSettings.DIV).is(":visible")){
									clearInterval(interval);
									let
										scrollTop = this.getElementsViewportScrollTop(elements.add(AcritExpPopupFieldSettings.DIV)),
										animateTime = 100;
									$("html, body").animate({scrollTop: scrollTop}, animateTime);
									setTimeout($.proxy(function(){
										this.strokeElement($(AcritExpPopupFieldSettings.DIV), true, true);
									}, this), animateTime);
								}
							}, this), 100);
						});
					}',
					'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
						let elements = currStepData.elements;
						elements.filter("tr").removeClass("acrit_teacher_control_highlighted_absolute");
						//
						this.removeHandler(this.btnSettingsField, "click", this.handlerSelect);
						this.removeHandler(this.btnSettingsField, "click", this.handlerSettingsValue);
						this.removeHandler(this.btnSettingsField, "click", this.handlerSettingsField);
						//
						AcritExpPopupSelectField.Close();
						AcritExpPopupValueSettings.Close();
						AcritExpPopupFieldSettings.Close();
					}',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !stepData.elements.length;
					}',
				],
				# Subtab: Offer fields
				'SUBTAB_FIELDS_OFFER' => [
					'ELEMENTS' => '$("#view_tab_fields_offer")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'fields_offer',
				],
				'OFFER_FIELD' => [
					'ELEMENTS' => '$("#fields_offer tr[data-role=\"field_row\"]").first()',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'fields_offer',
					'CALLBACK_IN' => 'function(options, currStepData, nextStepData){
						//let elements = $.proxy(nextStepData.callbackElements, this)(options, nextStepData);
						let elements = nextStepData.elements;
						elements.filter("tr").addClass("acrit_teacher_control_highlighted_absolute");
						//
						this.btnSelect = $("input[data-role=\"field-simple--button-select-const\"]", elements);
						this.btnSelect = this.btnSelect.add($("input[data-role=\"field-simple--value-title\"]"));
						this.handlerSelect = this.addHandler(this.btnSelect, "click", function(popup){
							let interval = setInterval($.proxy(function(){
								if(AcritExpPopupSelectField.isOpen && $(AcritExpPopupSelectField.DIV).is(":visible")){
									clearInterval(interval);
									let
										scrollTop = this.getElementsViewportScrollTop(elements.add(AcritExpPopupSelectField.DIV)),
										animateTime = 100;
									$("html, body").animate({scrollTop: scrollTop}, animateTime);
									setTimeout($.proxy(function(){
										this.strokeElement($(AcritExpPopupSelectField.DIV), true, true);
									}, this), animateTime);
								}
							}, this), 100);
						});
						//
						this.btnSettingsValue = $("input[data-role=\"field-simple--button-params\"]", elements);
						this.handlerSettingsValue = this.addHandler(this.btnSettingsValue, "click", function(popup){
							let interval = setInterval($.proxy(function(){
								if(AcritExpPopupValueSettings.isOpen && $(AcritExpPopupValueSettings.DIV).is(":visible")){
									clearInterval(interval);
									let
										scrollTop = this.getElementsViewportScrollTop(elements.add(AcritExpPopupValueSettings.DIV)),
										animateTime = 100;
									$("html, body").animate({scrollTop: scrollTop}, animateTime);
									setTimeout($.proxy(function(){
										this.strokeElement($(AcritExpPopupValueSettings.DIV), true, true);
									}, this), animateTime);
								}
							}, this), 100);
						});
						//
						this.btnSettingsField = $("input[data-role=\"field--button-params\"]", elements);
						this.handlerSettingsField = this.addHandler(this.btnSettingsField, "click", function(popup){
							let interval = setInterval($.proxy(function(){
								if(AcritExpPopupFieldSettings.isOpen && $(AcritExpPopupFieldSettings.DIV).is(":visible")){
									clearInterval(interval);
									let
										scrollTop = this.getElementsViewportScrollTop(elements.add(AcritExpPopupFieldSettings.DIV)),
										animateTime = 100;
									$("html, body").animate({scrollTop: scrollTop}, animateTime);
									setTimeout($.proxy(function(){
										this.strokeElement($(AcritExpPopupFieldSettings.DIV), true, true);
									}, this), animateTime);
								}
							}, this), 100);
						});
					}',
					'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
						let elements = currStepData.elements;
						elements.filter("tr").removeClass("acrit_teacher_control_highlighted_absolute");
						//
						this.removeHandler(this.btnSettingsField, "click", this.handlerSelect);
						this.removeHandler(this.btnSettingsField, "click", this.handlerSettingsValue);
						this.removeHandler(this.btnSettingsField, "click", this.handlerSettingsField);
						//
						AcritExpPopupSelectField.Close();
						AcritExpPopupValueSettings.Close();
						AcritExpPopupFieldSettings.Close();
					}',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !stepData.elements.length;
					}',
				],
				# Subtab: Offers
				'SUBTAB_OFFERS' => [
					'ELEMENTS' => '$("#view_tab_subtab_offers")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_offers',
				],
				'OFFERS_MODE' => [
					'ELEMENTS' => '$("select[id*=\"[OFFERS_MODE]\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_offers',
				],
				# Subtab: Categories
				'SUBTAB_CATEGORIES' => [
					'ELEMENTS' => '$("#view_tab_subtab_categories")',
					#'CALLBACK_ELEMENTS' => 'function(options, stepData){
					#	return $("#view_tab_subtab_categories");
					#}',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_categories',
				],
				'CATEGORIES_MODE' => [
					'ELEMENTS' => '$("select[data-role=\"sections-mode\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_categories',
				],
				'CATEGORIES_LIST' => [
					'ELEMENTS' => '$("select[data-role=\"categories-list\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_categories',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return $("select[data-role=\"sections-mode\"]").val() == "all";
					}',
				],
				'CATEGORIES_REDEFINITION_MODE' => [
					'ELEMENTS' => '$("select[data-role=\"categories-redefinition-mode\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_categories',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !stepData.elements.length || stepData.elements.is("[disabled]");
					}',
				],
				'CATEGORIES_SOURCE_MODE' => [
					'ELEMENTS' => '$("select[data-role=\"categories-redefinition-source\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_categories',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !stepData.elements.length || !stepData.elements.is(":visible");
					}',
				],
				'CATEGORIES_REDEFINITIONS' => [
					'ELEMENTS' => '$("input[data-role=\"categories-redefinition-button\"]")',
					'ACCESSIBLE' => 'N',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_categories',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !stepData.elements.length;
					}',
				],
				'CATEGORIES_UPDATE' => [
					'ELEMENTS' => '$("input[data-role=\"categories-update\"], span[data-role=\"categories-update-date\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_categories',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !stepData.elements.length;
					}',
					'CSS' => '
						span[data-role="categories-update-date"] {
							color:#fff;
						}
					',
				],
				# Subtab: Filter
				'SUBTAB_FILTER' => [
					'ELEMENTS' => '$("#view_tab_subtab_filter")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_filter',
				],
				'FILTER' => [
					'ELEMENTS' => '$("#subtab_filter div[data-role=\"filter\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_filter',
				],
				'FILTER_INCLUDE_SUBSECTIONS' => [
					'ELEMENTS' => '$("select[id*=\"[FILTER_INCLUDE_SUBSECTIONS]\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_filter',
				],
				# Subtab: General
				'SUBTAB_GENERAL' => [
					'ELEMENTS' => '$("#view_tab_subtab_general")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_general',
				],
				'ELEMENTS_SORT' => [
					'ELEMENTS' => '$("#subtab_general .adm-list-table")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
					'SUB_TAB' => 'subtab_general',
					'CALLBACK_IN' => 'function(options, currStepData, nextStepData){
						let elements = nextStepData.elements;
						//
						this.btnSelect = $("input[data-role=\"field-simple--button-select-field\"]", elements);
						this.btnSelect = this.btnSelect.add($("input[data-role=\"field-simple--value-title\"]"));
						this.handlerSelect = this.addHandler(this.btnSelect, "click", function(popup){
							let interval = setInterval($.proxy(function(){
								if(AcritExpPopupSelectField.isOpen && $(AcritExpPopupSelectField.DIV).is(":visible")){
									clearInterval(interval);
									let
										scrollTop = this.getElementsViewportScrollTop(elements.add(AcritExpPopupSelectField.DIV)),
										animateTime = 100;
									$("html, body").animate({scrollTop: scrollTop}, animateTime);
									setTimeout($.proxy(function(){
										this.strokeElement($(AcritExpPopupSelectField.DIV), true, true);
									}, this), animateTime);
								}
							}, this), 100);
						});
						//
						this.btnSettingsValue = $("input[data-role=\"field-simple--button-params\"]", elements);
						this.handlerSettingsValue = this.addHandler(this.btnSettingsValue, "click", function(popup){
							let interval = setInterval($.proxy(function(){
								if(AcritExpPopupValueSettings.isOpen && $(AcritExpPopupValueSettings.DIV).is(":visible")){
									clearInterval(interval);
									let
										scrollTop = this.getElementsViewportScrollTop(elements.add(AcritExpPopupValueSettings.DIV)),
										animateTime = 100;
									$("html, body").animate({scrollTop: scrollTop}, animateTime);
									setTimeout($.proxy(function(){
										this.strokeElement($(AcritExpPopupValueSettings.DIV), true, true);
									}, this), animateTime);
								}
							}, this), 100);
						});
					}',
					'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
						this.removeHandler(this.btnSettingsField, "click", this.handlerSelect);
						this.removeHandler(this.btnSettingsField, "click", this.handlerSettingsValue);
						AcritExpPopupSelectField.Close();
						AcritExpPopupValueSettings.Close();
					}',
					'CSS' => '
						#subtab_general table.adm-list-table {
							background-color:#fff;
						}
					',
				],
				# IBlock save / clear
				'IBLOCK_SETTINGS_SAVE' => [
					'ELEMENTS' => '$("input[data-role=\"iblock-settings-save\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'structure',
				],
				'IBLOCK_SETTINGS_CLEAR' => [
					'ELEMENTS' => '$("input[data-role=\"iblock-settings-clear\"]")',
					'ACCESSIBLE' => 'N',
					'TAB' => 'structure',
				],
				# Tab: Currency
				'TAB_CURRENCY' => [
					'ELEMENTS' => '$("#tab_cont_currency")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'currency',
				],
				'CURRENCY_TARGET' => [
					'ELEMENTS' => '$("#field_CURRENCY_TARGET_CURRENCY")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'currency',
				],
				'CURRENCY_RATES_SOURCE' => [
					'ELEMENTS' => '$("#field_CURRENCY_RATES_SOURCE")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'currency',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return $("#field_CURRENCY_TARGET_CURRENCY").val() == "";
					}',
				],
				# Tab: Cron
				'SETTINGS_COMPLETE' => [
					'ELEMENTS' => '$("#tab_cont_cron")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
				],
				'RUN_MANUAL' => [
					'ELEMENTS' => '$("input[data-role=\"run-manual\"], #acrit-exp-button-run")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
					'CALLBACK_IN' => 'function(options, currStepData, nextStepData){
						this.handler = this.addHandler(nextStepData.elements, "click", function(popup){
							let interval = setInterval($.proxy(function(){
								if(AcritExpPopupExecute.isOpen && $(AcritExpPopupExecute.DIV).is(":visible")){
									clearInterval(interval);
									let
										scrollTop = this.getElementsViewportScrollTop(nextStepData.elements.add(AcritExpPopupExecute.DIV)),
										animateTime = 100;
									$("html, body").animate({scrollTop: scrollTop}, animateTime);
									setTimeout($.proxy(function(){
										this.strokeElement($(AcritExpPopupExecute.DIV), true, true);
									}, this), animateTime);
								}
							}, this), 100);
						});
					}',
					'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
						this.removeHandler(currStepData.elements, "click", this.handler);
						AcritExpPopupExecute.Close();
					}',
				],
				'RUN_BACKGROUND' => [
					'ELEMENTS' => '$("input[data-role=\"run-background\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
				],
				'SERVER_TIME' => [
					'ELEMENTS' => '$("span[data-role=\"acrit-core-server-time\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
					'CSS' => '
						span[data-role="acrit-core-server-time"] {
							background:#f5f9f9;
							padding:4px 10px;
						}
					',
				],
				'CRON_STATUS' => [
					'ELEMENTS' => '$("div[data-cron-status] > span[data-status]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !!$(".acrit-core-cron-cannot-autoset").length;
					}',
					'CSS' => '
						div[data-cron-status] > span[data-status] {
							background:#f5f9f9;
							padding:4px 10px;
						}
					',
				],
				'CRON_TIME' => [
					'ELEMENTS' => '$("div[data-role=\"cron_setup_time\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !!$(".acrit-core-cron-cannot-autoset").length;
					}',
					'CSS' => '
						div[data-role="cron_setup_time"] {
							background:#f5f9f9;
							padding:4px 10px;
						}
					',
				],
				'CRON_SETUP' => [
					'ELEMENTS' => '$("input[data-role=\"cron-setup\"], input[data-role=\"cron-clear\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !!$(".acrit-core-cron-cannot-autoset").length;
					}',
				],
				'CRON_CANNOT_AUTO_SET' => [
					'ELEMENTS' => '$(".acrit-core-cron-cannot-autoset")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !$(".acrit-core-cron-cannot-autoset").length;
					}',
					'CSS' => '
						.acrit-core-cron-cannot-autoset {
							background:#f5f9f9;
						}
					',
				],
				'CRON_ONETIME' => [
					'ELEMENTS' => '$("label[for=\"acrit-core-cron-one-time\"], input[data-role=\"cron-one-time\"] + label")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
					'CALLBACK_SKIP' => 'function(options, stepData){
						return !!$(".acrit-core-cron-cannot-autoset").length;
					}',
					'CSS' => '
						label[for="acrit-core-cron-one-time"] {
							background:#f5f9f9;
						}
					',
				],
				'CRON_COMMAND' => [
					'ELEMENTS' => '$(".acrit-core-cron-form-command")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'cron',
				],
				# Tab: Log & history
				'TAB_LOG' => [
					'ELEMENTS' => '$("#tab_cont_log")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'log',
				],
				'LOG_WRAPPER' => [
					'ELEMENTS' => '$("#tr_LOG .acrit-exp-log-wrapper")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'log',
					'CSS' => '
						#tr_LOG .acrit-exp-log-wrapper {
							background:#f5f9f9;
							padding:4px 10px;
						}
					',
				],
				'HISTORY_WRAPPER' => [
					'ELEMENTS' => '$("div[data-role=\"profile-history-wrapper\"]")',
					'ACCESSIBLE' => 'Y',
					'TAB' => 'log',
					'CSS' => '
						div[data-role="profile-history-wrapper"] {
							background:#f5f9f9;
							padding:4px 10px;
						}
					',
				],
				# Misc
				'MISC_INTRO' => [
					//
				],
				'PROFILE_BACKUP' => [
					'ELEMENTS' => '$(".adm-btn-menu[onclick*=\"/bitrix/admin/acrit_\"]")',
					'ACCESSIBLE' => 'Y',
					'CALLBACK_IN' => 'function(options, prevStepData, currStepData){
						currStepData.elements.bind("click", $.proxy(function(){
							this.repaintStrokesDelay(1);
						}, this));
						//
						this.handler = this.addHandler(currStepData.elements, "click", function(popup){
							setTimeout(function(){
								elements.get(0).OPENER.MENU.DIV.style.zIndex = 1000013;
							}, 10);
						});
						//
						let
							elements = currStepData.elements,
							element = elements.get(0),
							button = elements.get(0),
							onclick = $(button).attr("onclick").replace(/return false;/, "");
						var result = function(str){
							return eval(str);
						}.call(button, onclick);
						currStepData.elements = currStepData.elements.add(element.OPENER.MENU.DIV);
					}',
					'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
						this.removeHandler(currStepData.elements, "click", this.handler);
					}',
				],
				'HOT_KEYS' => [
					//
				],
			],
		];
		foreach($arResult['STEPS'] as $strStep => &$arStep){
			$arStep['TITLE'] = Helper::getMessage($strStepNameLang.$strStep);
			$arStep['DESCRIPTION'] = Helper::getMessage($strStepDescLang.$strStep);
		}
		unset($arStep);
		return $arResult;
	}
	
	/**
	 *	Modify teachers array: you can add your teachers or change existing
	 */
	public function addTeachers(&$arTeachers){
		// Default nothing
	}
	
	/**
	 *	Modify default teacher
	 */
	public function modifyDefaultTeacher(&$arDefaultTeacher){
		// Default nothing
	}
	
	/**
	 *	Add item to teacher array
	 */
	protected function teaacherAddItem(&$arTeacher, $strKey, $arItem, $strAfter=null, $strBefore=null){
		Helper::arrayInsert($arTeacher, $strKey, $arItem, $strAfter, $strBefore);
	}

}
