<?
/**
 * Acrit Core: JSON plugin
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

class CustomJsonGeneral extends CustomJson {
	
	const DATE_UPDATED = '2019-11-26';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'file.json';
	protected $arSupportedFormats = ['JSON'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'json';
	protected $arSupportedCurrencies = []; // 'RUB'?
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = false;
	protected $bCurrenciesExport = false;
	protected $bEscapeUtm = false;
	
	# JSON settings
	protected $arJsonTranspose = [];
	
	# Other export settings
	protected $bZip = true;
	
	/**
	 *	Get supported options for 2nd argument of Json::encode()
	 */
	public function getSupportedEncodeOptions(){
		$arOptions = [
			'JSON_PRETTY_PRINT',
			'JSON_UNESCAPED_UNICODE',
			'JSON_FORCE_OBJECT',
			'JSON_UNESCAPED_SLASHES',
			'JSON_HEX_QUOT',
			'JSON_HEX_APOS',
			'JSON_HEX_TAG',
			'JSON_HEX_AMP',
			'JSON_INVALID_UTF8_IGNORE',
			'JSON_INVALID_UTF8_SUBSTITUTE',
			'JSON_NUMERIC_CHECK',
			'JSON_PRESERVE_ZERO_FRACTION',
			'JSON_UNESCAPED_LINE_TERMINATORS',
			'JSON_PARTIAL_OUTPUT_ON_ERROR',
		];
		if(!checkVersion(PHP_VERSION, '7.2.0')){
			Helper::arrayRemoveValues($arOptions, 'JSON_INVALID_UTF8_IGNORE');
			Helper::arrayRemoveValues($arOptions, 'JSON_INVALID_UTF8_SUBSTITUTE');
		}
		if(!checkVersion(PHP_VERSION, '7.1.0')){
			Helper::arrayRemoveValues($arOptions, 'JSON_UNESCAPED_LINE_TERMINATORS');
		}
		if(!checkVersion(PHP_VERSION, '5.6.6')){
			Helper::arrayRemoveValues($arOptions, 'JSON_PRESERVE_ZERO_FRACTION');
		}
		return $arOptions;
	}
	
	/**
	 *	Get default options for 2nd argument of Json::encode()
	 */
	public function getDefaultEncodeOptions(){
		return [
			'JSON_PRETTY_PRINT',
			'JSON_UNESCAPED_SLASHES',
		];
	}
	
	/**
	 *	JSON_PRETTY_PRINT and other options for Json::encode
	 */
	protected function getJsonEncodeOptions(){
		$arOptions = $this->arParams['JSON_ENCODE_OPTIONS'];
		if(is_numeric($arOptions) && $arOptions > 0){
			return $arOptions;
		}
		elseif(is_array($arOptions) && !empty($arOptions)){
			return eval('return ('.implode('+', $arOptions).');');
		}
		return 0;
	}
	
	/**
	 *	Is it need to offers preprocess?
	 */
	public function isOffersPreprocess(){
		return $this->arParams['JSON_OFFERS_PREPROCESS'] == 'Y';
	}
	
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
		$arFields = $this->parseSavedFields($intIBlockId);
		foreach($arFields as $strField){
			$arResult[$strField] = ['NAME' => $strField, 'MULTIPLE' => true, 'PARAMS' => ['MULTIPLE' => 'join']];
		}
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected function parseSavedFields($intIBlockId){
		$arResult = [];
		$arFieldsTmp = [];
		if(Helper::isOffersIBlock($intIBlockId)){
			$arFieldsTmp = $this->arParams['JSON_OFFER_FIELDS'];
		}
		if(empty($arFieldsTmp)){
			$arFieldsTmp = $this->arParams['JSON_ELEMENT_FIELDS'];
		}
		$arFieldsTmp = explode("\n", $arFieldsTmp);
		foreach($arFieldsTmp as $strField){
			$strField = trim($strField);
			if(strlen($strField) && strpos($strField, '@') === false){
				$arResult[] = $strField;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get default fields
	 */
	public function getDefaultFields($bOffers=false){
		$arResult = [];
		$strFile = ($bOffers ? '.default_fields_offer.txt' : '.default_fields_element.txt');
		$strFields = Helper::convertUtf8(file_get_contents(__DIR__.'/'.$strFile));
		$arFieldsTmp = explode("\n", $strFields);
		foreach($arFieldsTmp as $strField){
			$strField = trim($strField);
			if(strlen($strField) && strpos($strField, '@') === false){
				$arResult[] = $strField;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = [[
			'DIV' => 'json_settings',
			'TAB' => static::getMessage('TAB_JSON_SETTINGS_NAME'),
			'TITLE' => static::getMessage('TAB_JSON_SETTINGS_TITLE'),
			'SORT' => 5,
			'FILE' => __DIR__.'/tabs/json_settings.php',
		]];
		return $arResult;
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
	 *	Build build JSON item
	 */
	protected function onUpBuildJson(&$arJson, &$arElement, &$arFields, &$arElementSections){
		if($this->isOffersPreprocess() && isset($arFields['_OFFER_PREPROCESS'])){
			$arOffers = $arFields['_OFFER_PREPROCESS'];
			unset($arFields['_OFFER_PREPROCESS']);
			unset($arJson['_OFFER_PREPROCESS']);
			if(is_array($arOffers) && !empty($arOffers)){
				$strPreprocessField = $this->arParams['JSON_OFFERS_PREPROCESS_FIELD'];
				if(!strlen($strPreprocessField)){
					$strPreprocessField = 'offers';
				}
				$arJsonOffers = [];
				foreach($arOffers as $arOffer){
					if(!Helper::isUtf()){
						$arOffer['DATA'] = Helper::convertEncoding($arOffer['DATA'], 'CP1251', 'UTF-8');
					}
					$arJsonOffers[] = Json::decode($arOffer['DATA'], true);
				}
				unset($arOffers, $arOffer);
				$this->jsonSetValue($arJson, $strPreprocessField, $arJsonOffers);
			}
		}
	}
	
	/**
	 *	Build main JSON structure
	 */
	protected function onUpGetJsonStructure(&$strJson){
		$strJsonTmp = $this->arParams['JSON_STRUCTURE'];
		$strJsonTmp = preg_replace_callback('/#DATE#/', function($arMatch){
			return '"'.date(\CDatabase::dateFormatToPHP(FORMAT_DATETIME)).'"';
		}, $strJsonTmp);
		$strJsonTmp = preg_replace_callback('/#DATE\((.*?)\)#/', function($arMatch){
			return '"'.date($arMatch[1]).'"';
		}, $strJsonTmp);
		$strJson = $strJsonTmp;
		unset($strJsonTmp);
	}
	
	/**
	 *	
	 */
	protected function onUpBeforeProcessElement(&$arResult, &$arElement, &$arFields, &$arElementSections, $intMainIBlockId){
		foreach($arFields as $key => $value){
			if(is_string($value)){
				#$arFields[$key] = Helper::escapeQuotes($value);
			}
		}
	}
	
	/**
	 *	
	 */
	protected function getJsonFieldTypes($strType=null){
		$arTypes = [
			'INTEGER' => [
				'NAME' => static::getMessage('FIELD_TYPE_INTEGER'),
				'CALLBACK' => function($value){
					return intVal($value);
				},
			],
			'FLOAT' => [
				'NAME' => static::getMessage('FIELD_TYPE_FLOAT'),
				'CALLBACK' => function($value){
					return floatVal($value);
				},
			],
			'BOOLEAN_DEFAULT_FALSE' => [
				'NAME' => static::getMessage('FIELD_TYPE_BOOLEAN_DEFAULT_FALSE'),
				'CALLBACK' => function($value){
					if($value === true || toLower($value) === 'true' || $value === 1 || $value === '1'){
						return true;
					}
					return false;
				},
			],
			'BOOLEAN_DEFAULT_TRUE' => [
				'NAME' => static::getMessage('FIELD_TYPE_BOOLEAN_DEFAULT_TRUE'),
				'CALLBACK' => function($value){
					if($value === false || toLower($value) === 'false' || $value === 0 || $value === '0'){
						return false;
					}
					return true;
				},
			],
		];
		return strlen($strType) ? $arTypes[$strType] : $arTypes;
	}
	
	/**
	 *	
	 */
	protected function onUpBeforeBuildJson(&$arResult, &$arElement, &$arFields, &$arElementSections){
		$arFieldsData = $this->arProfile['IBLOCKS'][$arElement['IBLOCK_ID']]['FIELDS'];
		if(is_array($arFieldsData)){
			foreach($arFields as $key => $value){
				$strType = $arFieldsData[$key]['PARAMS']['JSON_FIELD_TYPE'];
				if(strlen($strType)){
					if(is_array($arFields[$key])){
						foreach($arFields[$key] as $key2 => $value2){
							$this->transformJsonValue($arFields[$key][$key2], $strType);
						}
					}
					else{
						$this->transformJsonValue($arFields[$key], $strType);
					}
				}
			}
		}
	}
	
	/**
	 *	
	 */
	protected function transformJsonValue(&$mValue, $strType){
		$arType = $this->getJsonFieldTypes($strType);
		if(is_array($arType) && is_callable($arType['CALLBACK'])){
			$mValue = call_user_func($arType['CALLBACK'], $mValue);
		}
	}

}

?>