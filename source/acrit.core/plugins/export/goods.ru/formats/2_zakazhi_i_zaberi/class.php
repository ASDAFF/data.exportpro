<?
/**
 * Acrit Core: JSON plugin
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin;

class GoodsZakaziIZaberi extends UniversalPlugin {
	
	const DATE_UPDATED = '2019-12-03';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'goods-zakazhi-i-zaberi.json';
	protected $arSupportedFormats = ['JSON'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'json';
	protected $arSupportedCurrencies = ['RUB'];
	
	# Basic settings
	protected $bAdditionalFields = false;
	protected $bCategoriesExport = false;
	protected $bCurrenciesExport = false;
	protected $bEscapeUtm = false;
	
	# Other export settings
	protected $bZip = true;
	
	/**
	 *	Set profile array
	 */
	public function setProfileArray(array &$arProfile){
		parent::setProfileArray($arProfile);
		#
		if(strlen($arProfile['PARAMS']['JSON_TRANSFORM_FIELDS'])){
			$this->arJsonTranspose = explode(',', $arProfile['PARAMS']['JSON_TRANSFORM_FIELDS']);
			foreach($this->arJsonTranspose as $key => $value){
				$value = trim($value);
				if(!strlen($value)){
					unset($this->arJsonTranspose[$key]);
				}
			}
		}
		#
		if(strlen($arProfile['PARAMS']['JSON_UTM_FIELD'])){
			$this->arFieldsWithUtm = explode(',', $arProfile['PARAMS']['JSON_UTM_FIELD']);
			foreach($this->arFieldsWithUtm as $key => $value){
				$value = trim($value);
				if(!strlen($value)){
					unset($this->arFieldsWithUtm[$key]);
				}
			}
		}
	}
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileId, $intIBlockId){
		$arResult = [];
		$arResult['offerId'] = ['FIELD' => 'ID'];
		#$arResult['quantity'] = ['CONST' => 'This field is ignored while exporting!!!'];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1'];
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected function onUpShowSettings(&$arSettings){
		Helper::arrayInsert($arSettings, 'STORAGE_DIRECTORY_EXTERNAL', $this->showSettingsStorageDirectoryExternal(), 'FILENAME');
		Helper::arrayInsert($arSettings, 'STORAGE_DIRECTORY_INTERNAL', $this->showSettingsStorageDirectoryInternal(), 'FILENAME');
		Helper::arrayInsert($arSettings, 'STORAGE_DIRECTORY_SWITCHER', $this->showSettingsStorageDirectorySwitcher(), 'FILENAME');
		$arSettings['MERCHANT_ID'] = $this->showSettingsMerchantId();
		$arSettings['INFO_TYPE'] = $this->showSettingsInfoType();
		$arSettings['STORES'] = $this->showSettingsStores();
	}
	
	/**
	 *	
	 */
	protected function showSettingsStores(){
		ob_start();
		require __DIR__.'/settings/stores.php';
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function showSettingsStorageDirectorySwitcher(){
		ob_start();
		require __DIR__.'/settings/storage_directory_switcher.php';
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function showSettingsStorageDirectoryInternal(){
		ob_start();
		require __DIR__.'/settings/storage_directory_internal.php';
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function showSettingsStorageDirectoryExternal(){
		ob_start();
		require __DIR__.'/settings/storage_directory_external.php';
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function showSettingsMerchantId(){
		ob_start();
		require __DIR__.'/settings/merchant_id.php';
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function showSettingsInfoType(){
		ob_start();
		require __DIR__.'/settings/info_type.php';
		return ob_get_clean();
	}
	
	/**
	 *	Build main JSON structure
	 */
	protected function onUpGetJsonStructure(&$strJson){
		$strJson = file_get_contents(__DIR__.'/.structure.json');
		$strJson = str_replace('#MERCHANT_ID#', intVal($this->arParams['MERCHANT_ID']), $strJson);
		$strJson = str_replace('#TYPE#', '"'.$this->arParams['INFO_TYPE'].'"', $strJson);
		$strJson = str_replace('#DATE#', date(\CDatabase::dateFormatToPHP(FORMAT_DATETIME)), $strJson);
		$strJson = preg_replace_callback('/#DATE\((.*?)\)#/', function($arMatch){
			$strFormat = $arMatch[1];
			$bReplace = false;
			if(substr($strFormat, 0, 1) == ':'){
				$bReplace = true;
				$strFormat = substr($strFormat, 1);
			}
			$strResult = '"'.date($strFormat).'"';
			if($bReplace){
				$strResult = str_replace(':', '-', $strResult);
			}
			return $strResult;
		}, $strJson);
	}
	
	/**
	 *	Get file suffix for selected store
	 */
	protected function getStoreFileSuffix($intStoreId){
		return 'store_'.$intStoreId;
	}
	
	/**
	 *	Get temp file for selected store
	 */
	protected function getStoreTmpFile($intStoreId){
		return $this->getExportFilenameTmp($this->getStoreFileSuffix($intStoreId));
	}
	
	/**
	 *	
	 */
	protected function onUpStepCheck(&$arSession){
		if(!\Bitrix\Main\Loader::includeModule('catalog') || !class_exists('\CCatalogStoreProduct')){
			print Helper::showError(static::getMessage('ERROR_NO_CATALOG_TITLE'), static::getMessage('ERROR_NO_CATALOG_DESCR'));
			return Exporter::RESULT_ERROR;
		}
		if(!is_array($this->arParams['STORES']) || empty($this->arParams['STORES'])){
			print Helper::showError(static::getMessage('ERROR_NO_STORES_TITLE'), static::getMessage('ERROR_NO_STORES_DESCR'));
			return Exporter::RESULT_ERROR;
		}
		if($this->arParams['STORAGE_DIRECTORY_SWITCHER'] == 'external'){
			if(!strlen($this->arParams['STORAGE_DIRECTORY_EXTERNAL'])){
				print Helper::showError(static::getMessage('ERROR_NO_STORAGE_DIRECTORY_TITLE'), static::getMessage('ERROR_NO_STORAGE_DIRECTORY_DESCR'));
				return Exporter::RESULT_ERROR;
			}
		} 
		else{
			if(!strlen($this->arParams['STORAGE_DIRECTORY'])){
				print Helper::showError(static::getMessage('ERROR_NO_STORAGE_DIRECTORY_TITLE'), static::getMessage('ERROR_NO_STORAGE_DIRECTORY_DESCR'));
				return Exporter::RESULT_ERROR;
			}
		}
		$arSession['STORES_AVAILABLE'] = true;
		foreach($this->arParams['STORES'] as $intStoreId){
			$strTmpFile = $this->getStoreTmpFile($intStoreId);
			if(is_file(Helper::root().$strTmpFile)){
				@unlink(Helper::root().$strTmpFile);
			}
		}
	}
	
	/**
	 *	Export one item for each of stores
	 */
	protected function onUpJsonExportItem(&$arItem, &$strJson, &$arSession, &$bWrite){
		$bWrite = false; // Disable further writing item to main file
		if($arSession['STORES_AVAILABLE']){
			$intElementId = $arItem['ELEMENT_ID'];
			$arFilter = array(
				'STORE_ID' => $this->arParams['STORES'],
				'PRODUCT_ID' => $intElementId,
			);
			$resStoreAmount = \CCatalogStoreProduct::getList([], $arFilter, false, false, ['STORE_ID', 'AMOUNT']);
			while($arStoreAmount = $resStoreAmount->getNext(false, false)){
				$intAmount = intVal($arStoreAmount['AMOUNT']);
				if($intAmount <= 0 && $this->arParams['INFO_TYPE'] != 'diff'){
					continue;
				}
				$intStoreId = intVal($arStoreAmount['STORE_ID']);
				$arJson = Json::decode($arItem['DATA']);
				if(is_array($arJson)){
					if(isset($arJson['price'])){
						$arJson['price'] = intVal($arJson['price']);
					}
					/*
					foreach($arJson as $key => $value){
						if($value == intVal($value)){
							$value = intVal($value);
						}
						elseif($value == floatVal($value)){
							$value = floatVal($value);
						}
						$arJson[$key] = $value;
					}
					*/
					Helper::arrayInsert($arJson, 'quantity', $intAmount, 'offerId');
					$strJson = Json::encode($arJson, JSON_PRETTY_PRINT);
					$strJson = rtrim($strJson);
					if($arSession['STORE_ITEM_WRITTEN_'.$intStoreId]) {
						$strJson = ",\n".$strJson;
					}
					Json::replaceSpaces($strJson);
					Json::addIndent($strJson, 4);
					if($arSession['STORE_ITEM_WRITTEN_'.$intStoreId]) {
						$strJson = trim($strJson);
					}
					$this->writeToFile($strJson, $this->getStoreFileSuffix($intStoreId));
					$arSession['STORE_ITEM_WRITTEN_'.$intStoreId] = true;
				}
			}
		}
	}
	
	/**
	 *	Add custom steps
	 */
	protected function onUpGetExportSteps(&$arExportSteps, &$arSession){
		$arExportSteps['MERGE_STORE_FILES'] = array(
			'NAME' => static::getMessage('STEP_MERGE_STORE_FILES'),
			'SORT' => 850,
			'FUNC' => 'stepExport_MergeStoreFiles',
		);
		if(!strlen($this->arParams['ARCHIVE'])) {
			$arExportSteps['COPY_STORE_FILES'] = array(
				'NAME' => static::getMessage('STEP_COPY_STORE_FILES'),
				'SORT' => 1050,
				'FUNC' => 'stepExport_CopyStoreFiles',
			);
		}
	}
	
	/**
	 *	
	 */
	protected function stepExport_MergeStoreFiles(){
		foreach($this->arParams['STORES'] as $intStoreId){
			$strTmpFile = Helper::root().$this->getStoreTmpFile($intStoreId);
			if(is_file($strTmpFile) && filesize($strTmpFile)){
				$this->stepExport_MergeOneFile($intStoreId, $strTmpFile);
				@unlink($strTmpFile);
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	
	 */
	protected function stepExport_MergeOneFile($intStoreId, $strFileSource){
		$arJsonOutlet = [
			'outletId' => $intStoreId,
			'offers' => '#OFFERS#',
		];
		$strJsonOutlet = Json::encode($arJsonOutlet, JSON_PRETTY_PRINT);
		Json::replaceSpaces($strJsonOutlet);
		Json::addIndent($strJsonOutlet, 2);
		$this->jsonSplitByDivider($strJsonOutlet, '#OFFERS#', $strHeader, $strFooter, $strIndent);
		#
		if($this->arSession['FIRST_OUTLET_WRITTEN']){
			$strHeader = ",\n".$strHeader;
		}
		$strFooter = "\t".$strFooter;
		#
		$this->writeToFile($strHeader, null, false);
		#
		$strFileTarget = $_SERVER['DOCUMENT_ROOT'].$this->getExportFilenameTmp();
		$resHandleSource = fopen($strFileSource, 'r');
		if($resHandleSource) {
			$intChunkSize = 4096;
			while(true) {
				$strBuffer = fread($resHandleSource, $intChunkSize);
				if($strBuffer === false || !strlen($strBuffer)){
					break;
				}
				if(!$this->writeToFile($strBuffer, null, false)){
					break;
				}
			}
			$this->writeToFile("\n", null, false);
		}
		fclose($resHandleSource);
		#
		$this->writeToFile($strFooter, null, false);
		$this->arSession['FIRST_OUTLET_WRITTEN'] = true;
	}
	
	/**
	 *	
	 */
	protected function stepExport_CopyStoreFiles(){
		$strSource = Helper::root().$this->getExportFilenameTmp();
		if(is_file($strSource)){
			$strTarget = $this->stepExport_GetTargetFilename('json');
			if(!$strTarget){
				print Helper::showError(static::getMessage('ERROR_CREATE_STORAGE_DIRECTORY'));
				return Exporter::RESULT_ERROR;
			}
			if(copy($strSource, $strTarget)){
				return Exporter::RESULT_SUCCESS;
			}
			else{
				print Helper::showError(static::getMessage('ERROR_COPY_FILE_TO_STORAGE', [
					'#SOURCE#' => $strSource,
					'#TARGET#' => $strTarget,
				]));
			}
		}
		return Exporter::RESULT_ERROR;
	}
	
	/**
	 *	On ZIP created
	 */
	protected function onUpZipSuccess($strZipFile){
		$strSource = Helper::root().$strZipFile;
		if(is_file($strSource)){
			$strTarget = $this->stepExport_GetTargetFilename('zip');
			if(!$strTarget){
				print Helper::showError(static::getMessage('ERROR_CREATE_STORAGE_DIRECTORY'));
				return Exporter::RESULT_ERROR;
			}
			if(copy($strSource, $strTarget)){
				return Exporter::RESULT_SUCCESS;
			}
			else{
				print Helper::showError(static::getMessage('ERROR_COPY_FILE_TO_STORAGE', [
					'#SOURCE#' => $strSource,
					'#TARGET#' => $strTarget,
				]));
			}
		}
		return Exporter::RESULT_ERROR;
	}
	
	/**
	 *	Get target filename
	 */
	protected function stepExport_GetTargetFilename($strExt=null){
		$strExt = is_string($strExt) ? '.'.$strExt : '';
		if($this->arParams['STORAGE_DIRECTORY_SWITCHER'] == 'external'){
			$strDir = $this->arParams['STORAGE_DIRECTORY_EXTERNAL'];
		}
		else{
			$strDir = Helper::root().$this->arParams['STORAGE_DIRECTORY'];
		}
		if(!is_dir($strDir)){
			mkdir($strDir, BX_DIR_PERMISSIONS, true);
		}
		if(!is_dir($strDir)){
			return false;
		}
		$strFilenameTarget = sprintf('%d_stocks_%s_%s%s', $this->arParams['MERCHANT_ID'], $this->arParams['INFO_TYPE'], 
			str_replace(':', '-', date('c')), $strExt);
		return str_replace('//', '/', $strDir.'/'.$strFilenameTarget);
	}
	
	/**
	 *	Show notices
	 */
	public function showMessages(){
		#if(!is_array($this->arParams['STORES']) || empty($this->arParams['STORES'])){
		#	print Helper::showError(static::getMessage('ERROR_NO_STORES_TITLE'), static::getMessage('ERROR_NO_STORES_DESCR'));
		#}
	}

}

?>