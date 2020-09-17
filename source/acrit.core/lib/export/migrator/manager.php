<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Field\ValueBase,
	\Acrit\Core\Export\Migrator\FilterConverter;

Helper::loadMessages(__FILE__);

/**
 * 
 */
class Manager {
	
	protected $arMigrators = array();
	protected $strModuleId;
	protected $strModuleCode;
	protected $strModuleUnderscore;
	
	/**
	 *	Create object
	 */
	public function __construct($strModuleId){
		$this->strModuleId = $strModuleId;
		$this->strModuleCode = preg_replace('#^(.*?)\.(.*?)$#', '$2', $strModuleId);
		$this->strModuleUnderscore = preg_replace('#^(.*?)\.(.*?)$#', '$1_$2', $strModuleId);
		$this->arMigrators = $this->getMigrators();
	}
	
	/**
	 *	Get all old profiles
	 */
	public function getOldProfiles(){
		$arResult = array();
		$strSql = "SELECT `ID` FROM `{$this->strModuleUnderscore}_profile` ORDER BY `ID` ASC;";
		$resQuery = $GLOBALS['DB']->Query($strSql, true);
		while($arProfile = $resQuery->getNext()){
			$arResult[$arProfile['ID']] = $this->getOldProfile($arProfile['ID']);
		}
		return $arResult;
	}
	
	/**
	 *	Get one old profile (using decoding)
	 */
	public function getOldProfile($intProfileID){
		$intProfileID = IntVal($intProfileID);
		$strSql = "SELECT * FROM `{$this->strModuleUnderscore}_profile` WHERE `ID`='{$intProfileID}';";
		$resQuery = $GLOBALS['DB']->Query($strSql, true);
		$arProfile = $resQuery->getNext();
		if(is_array($arProfile)){
			$this->decodeSettings($arProfile);
			#
			$strSql = "SELECT * FROM `{$this->strModuleUnderscore}_profile_data` WHERE `PROFILE_ID`='{$arProfile['ID']}';";
			$resQuery = $GLOBALS['DB']->Query($strSql, true);
			if($arData = $resQuery->getNext()){
				$this->decodeSettings($arData);
				$arProfile['_DATA'] = $arData;
			}
			#
			$strSql = "SELECT * FROM `{$this->strModuleUnderscore}_profile_tools` WHERE `PROFILE_ID`='{$arProfile['ID']}';";
			$resQuery = $GLOBALS['DB']->Query($strSql, true);
			if($arData = $resQuery->getNext()){
				$this->decodeSettings($arData, true);
				$arData['CURRENCY'] = unserialize(base64_decode($arData['CURRENCY']));
				$arProfile['_TOOLS'] = $arData;
			}
		}
		return $arProfile;
	}
	
	/**
	 *	
	 */
	public function getNewProfiles(){
		#return Profile::getProfiles();
		return Helper::call($this->strModuleId, 'Profile', 'getProfiles');
	}
	
	/**
	 *	Decode array (some of it elements)
	 */
	protected function decodeSettings(&$arSettings, $X=false){
		if(is_array($arSettings)){
			$arSerial = array(
				'IBLOCK_TYPE_ID',
				'IBLOCK_ID',
				'CATEGORY',
				'IBLOCK_AUTOFILL_PROPS',
				'FORMAT',
				'CURRENCY',
				'CONDITIONS',
				'SETUP',
				'XMLDATA',
				'CONVERT_DATA',
				'MARKET_CATEGORY',
				'CONDITION',
				'OFFER_TEMPLATE',
				'LOG',
				'NAMESCHEMA',
				'VARIANT',
				'VK',
				'FB',
				'OK',
				'INSTAGRAM',
			);
			foreach($arSettings as $strKey => $strValue){
				if(in_array($strKey, $arSerial)){
					$arSettings[$strKey] = unserialize(base64_decode($strValue));
				}
			}
		}
	}
	
	/**
	 *	Get available migrators
	 */
	protected function getMigrators(){
		$resHandle = opendir(__DIR__);
		while ($strFile = readdir($resHandle)) {
			if($strFile != '.' && $strFile != '..' && is_file(__DIR__.'/'.$strFile)) {
				if(ToLower(pathinfo($strFile, PATHINFO_EXTENSION)) == 'php'){
					require_once(__DIR__.'/'.$strFile);
				}
			}
		}
		closedir($resHandle);
		#
		$arResult = array();
		foreach(get_declared_classes() as $strClass){
			if(is_subclass_of($strClass, __NAMESPACE__.'\Base') && strlen($strClass::OLD_TYPE)){
				$arResult[$strClass::OLD_TYPE] = new $strClass;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get profiles that can be migrated
	 */
	public function getMigratableProfiles(){
		$arProfiles = $this->getOldProfiles();
		foreach($arProfiles as $key => $arProfile){
			if(!array_key_exists($arProfile['TYPE'], $this->arMigrators)){
				unset($arProfiles[$key]);
			}
		}
		return $arProfiles;
	}
	
	/**
	 *	Create new profile array for restoring on new core as backup
	 */
	public function compileProfileArray(&$arOldProfile){
		$obMigrator = &$this->arMigrators[$arOldProfile['TYPE']];
		if(is_object($obMigrator)) {
			$arNewProfile = array();
			$obMigrator->setOldProfile($arOldProfile);
			$this->compileProfileGeneralData($arNewProfile, $arOldProfile, $obMigrator);
			$this->compileProfileParams($arNewProfile, $arOldProfile, $obMigrator);
			$this->compileProfileIBlocks($arNewProfile, $arOldProfile, $obMigrator);
			$this->compileProfileIBlockFields($arNewProfile, $arOldProfile, $obMigrator);
			return $arNewProfile;
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function compileProfileGeneralData(&$arNewProfile, &$arOldProfile, $obMigrator){
		$arNewProfile['EXTERNAL_ID'] = $this->compileExternalID($arOldProfile['ID']);
		$arNewProfile['ACTIVE'] = $arOldProfile['ACTIVE'];
		$arNewProfile['NAME'] = $arOldProfile['NAME'];
		$arNewProfile['CODE'] = $arOldProfile['CODE'];
		$arNewProfile['DESCRIPTION'] = $arOldProfile['DESCRIPTION'];
		$arNewProfile['SORT'] = 100;
		#
		$arNewProfile['SITE_ID'] = $arOldProfile['LID'];
		$arNewProfile['DOMAIN'] = $arOldProfile['DOMAIN_NAME'];
		$arNewProfile['IS_HTTPS'] = $arOldProfile['SITE_PROTOCOL'] == 'https' ? 'Y' : 'N';
		#$arNewProfile['ENCODING'] = $arOldProfile['ENCODING'] == 'cp1251' ? 'windows-1251' : 'UTF-8';
		#
		$arNewProfile['PLUGIN'] = $obMigrator::PLUGIN;
		$arNewProfile['FORMAT'] = $obMigrator::FORMAT;
		#
		$arNewProfile['LAST_IBLOCK_ID'] = null;
		$arNewProfile['LAST_SETTINGS_TAB'] = null;
		#
		$arNewProfile['AUTO_GENERATE'] = 'Y';
		$arNewProfile['LOCKED'] = 'N';
		$arNewProfile['DATE_CREATED'] = new \Bitrix\Main\Type\DateTime();
		$arNewProfile['DATE_MODIFIED'] = new \Bitrix\Main\Type\DateTime();
		$arNewProfile['DATE_STARTED'] = null;
		$arNewProfile['DATE_LOCKED'] = null;
		$arNewProfile['SESSION'] = null;
		$arNewProfile['LAST_EXPORTED_ITEM'] = null;
	}
	
	/**
	 *	
	 */
	protected function compileProfileParams(&$arNewProfile, &$arOldProfile, $obMigrator){
		$arParams = &$arNewProfile['PARAMS'];
		$arParams = array();
		#
		$strFile = preg_replace('#^/(acrit\.[A-z]+)/(.*?)$#i', '/upload/$1/$2', $arOldProfile['SETUP']['URL_DATA_FILE']);
		$strFile = preg_replace('#^(.*?)\.(\w+)$#i', '$1_new.$2', $strFile);
		$arParams['AUTO_DELETE'] = 'N';
		$arParams['EXPORT_FILE_NAME'] = $strFile;
		$arParams['VIEW_CATALOG'] = $arOldProfile['SETUP']['SHOW_JUST_CATALOGS'] == 'Y' ? 'Y' : 'N';
		$arParams['CATEGORIES_REDEFINITION_MODE'] = $arOldProfile['SETUP']['USE_MARKET_CATEGORY'] == 'Y' ? 'Y' : 'N';
		$arParams['CATEGORIES_EXPORT_PARENTS'] = $arOldProfile['SETUP']['EXPORT_PARENT_CATEGORIES'] == 'Y' ? 'Y' : 'N';
		if($arOldProfile['_TOOLS']['CURRENCY']['CONVERT_CURRENCY'] == 'Y'){
			$arParams['CURRENCY'] = array(
				'TARGET_CURRENCY' => $obMigrator::DEFAULT_CURRENCY,
				'RATES_SOURCE' => 'SITE',
			);
			foreach($arOldProfile['_TOOLS']['CURRENCY'] as $key => $arValue){
				if(strlen($key) == 3 && strlen($arValue['RATE'])){
					$arParams['CURRENCY']['RATES_SOURCE'] = $arValue['RATE'];
				}
			}
		}
		#
		$obMigrator->compileParams($arNewProfile);
		$arNewProfile['PARAMS'] = serialize($arNewProfile['PARAMS']);
	}
	
	/**
	 *	
	 */
	protected function compileProfileIBlocks(&$arNewProfile, &$arOldProfile, $obMigrator){
		$arIBlocks = &$arNewProfile['IBLOCKS'];
		$arIBlocks = array();
		$arIBlocksID = array();
		foreach($arOldProfile['_DATA']['IBLOCK_ID'] as $intIBlockID){
			if(is_numeric($intIBlockID) && $intIBlockID>0){
				$arCatalog = Helper::getCatalogArray($intIBlockID);
				if($arCatalog['PRODUCT_IBLOCK_ID']){
					$arIBlocksID[$arCatalog['PRODUCT_IBLOCK_ID']] = $intIBlockID;
				}
				elseif($arCatalog['OFFERS_IBLOCK_ID']){
					$arIBlocksID[$intIBlockID] = $arCatalog['OFFERS_IBLOCK_ID'];
				}
				else {
					$arIBlocksID[$intIBlockID] = $intIBlockID;
				}
			}
		}
		$arIBlocksID = array_unique($arIBlocksID);
		foreach($arIBlocksID as $intIBlockMainID => $intIBlockOffersID){
			$arCatalog = Helper::getCatalogArray($intIBlockMainID);
			$arIBlock = array();
			$arIBlock['IBLOCK_ID'] = $intIBlockMainID;
			$arIBlock['IBLOCK_MAIN'] = 'Y';
			$arIBlock['SECTIONS_ID'] = implode(',', $this->extractIBlockSections($arOldProfile['_DATA']['CATEGORY'], $intIBlockMainID));
			$arIBlock['SECTIONS_MODE'] = 'selected_with_subsections';
			$arIBlock['FILTER'] = FilterConverter::convertFilter($this->strModuleId, $arOldProfile['_TOOLS']['CONDITION'], $intIBlockMainID);
			$arIBlock['PARAMS'] = serialize(array(
				'SORT_ORDER' => 'ASC',
				'OFFERS_MODE' => $this->transformOffersMode($arNewProfile, $arOldProfile, $obMigrator),
				'OFFER_SORT2' => array('FIELD'=>array('ID'),'OTHER'=>array(),'ORDER'=>array('DESC')),
				'OFFER_SORT_ORDER' => 'ASC',
				'OFFERS_MAX_COUNT' => '',
				'FILTER_INCLUDE_SUBSECTIONS' => 'Y',
			));
			$arIBlock['FIELDS'] = array();
			$arIBlock['ADDITIONAL_FIELDS'] = $this->convertAdditionalFields($arNewProfile, $arOldProfile, $intIBlockMainID, $obMigrator);
			$arIBlock['CATEGORY_REDEFINITIONS'] = $this->convertCategoryRedefinitions($arNewProfile, $arOldProfile, $intIBlockMainID);
			$arIBlocks[$intIBlockMainID] = $arIBlock;
			// Offers!
			if($intIBlockOffersID > 0 && $intIBlockOffersID != $intIBlockMainID){
				$arCatalog = Helper::getCatalogArray($intIBlockOffersID);
				$arIBlock = array();
				$arIBlock['IBLOCK_ID'] = $intIBlockOffersID;
				$arIBlock['IBLOCK_MAIN'] = 'N';
				$arIBlock['FILTER'] = FilterConverter::convertFilter($this->strModuleId, $arOldProfile['_TOOLS']['CONDITION'], $intIBlockOffersID);
				$arIBlock['PARAMS'] = '';
				$arIBlock['FIELDS'] = array();
				$arIBlock['ADDITIONAL_FIELDS'] = $this->convertAdditionalFields($arNewProfile, $arOldProfile, $intIBlockOffersID, $obMigrator);
				$arIBlock['CATEGORY_REDEFINITIONS'] = $this->convertCategoryRedefinitions($arNewProfile, $arOldProfile, $intIBlockOffersID);
				$arIBlocks[$intIBlockOffersID] = $arIBlock;
			}
			//
			if(!isset($arNewProfile['LAST_IBLOCK_ID'])){
				$arNewProfile['LAST_IBLOCK_ID'] = $intIBlockMainID;
			}
		}
	}
	
	/**
	 *	Transform offers mode
	 *	all, only, offers, none
	 */
	protected function transformOffersMode($arNewProfile, $arOldProfile, $obMigrator){
		$strResult = 'all';
		$bProducts = $arOldProfile['EXPORT_DATA_OFFER'] == 'Y' || $arOldProfile['EXPORT_DATA_OFFER_WITH_SKU_DATA'] == 'Y';
		$bOffers = $arOldProfile['EXPORT_DATA_SKU'] == 'Y' || $arOldProfile['EXPORT_DATA_SKU_BY_OFFER'] == 'Y';
		$bSkipWithSku = $arOldProfile['SKIP_WITH_SKU'] == 'Y';
		if($bSkipWithSku){
			$strResult = 'only';
		}
		elseif($bOffers && !$bProducts){
			$strResult = 'offers';
		}
		elseif($bProducts && !$bOffers){
			$strResult = 'none';
		}
		return $strResult;
	}
	
	/**
	 *	
	 */
	protected function compileProfileIBlockFields(&$arNewProfile, $arOldProfile, $obMigrator){
		$arIBlocks = &$arNewProfile['IBLOCKS'];
		$arFieldsMap = $obMigrator->getFieldsMap();
		foreach($arIBlocks as $intIBlockID => $arIBlock){
			foreach($arFieldsMap as $strOldField => $strNewField){
				# 1. search old field
				$arOldField = array();
				foreach($arOldProfile['_DATA']['XMLDATA'] as $arField){
					if($arField['CODE'] == $strOldField){
						$arOldField = $arField;
						break;
					}
				}
				# 2. process
				if(!empty($arOldField)){
					$arNewField = array();
					$arNewField['PROFILE_ID'] = $arOldField['PROFILE_ID'];
					$arNewField['IBLOCK_ID'] = $intIBlockID;
					$arNewField['FIELD'] = $strNewField;
					$arNewField['TYPE'] = ToUpper($arOldField['TYPE']);
					#
					$this->compileField($arNewField, $arOldField, $obMigrator);
					if(!empty($arNewField)){
						$arIBlocks[$intIBlockID]['FIELDS'][$strNewField] = $arNewField;
					}
				}
			}
		}
	}
	
	/**
	 *	
	 */
	protected function compileField(&$arNewField, $arOldField, $obMigrator){
		$bCondition = $arOldField['USE_CONDITION'] == 'Y';
		$arNewField['TYPE'] = $bCondition == 'Y' ? 'CONDITION' : 'FIELD';
		$arNewField['CONDITIONS'] = $bCondition ? FilterConverter::convertFilter($this->strModuleId, $arOldField['CONDITION'], $arNewField['IBLOCK_ID']) : null;
		$arNewField['VALUES'] = array();
		#$arAvailableFields = ProfileIBlock::getAvailableElementFieldsPlain($arNewField['IBLOCK_ID']);
		$arAvailableFields = Helper::call($this->strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$arNewField['IBLOCK_ID']]);
		$arParams = array();
		switch($arOldField['TYPE']){
			case 'field':
				$this->compileField_Field($arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator);
				break;
			case 'const':
				$this->compileField_Const($arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator);
				break;
			case 'complex':
				$this->compileField_Complex($arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator);
				break;
			case 'composite':
				$this->compileField_Composite($arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator);
				break;
			case 'arithmetics':
				$this->compileField_Arithmetics($arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator);
				break;
			case 'stack':
				$this->compileField_Stack($arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator);
				break;
		}
		if(in_array($arNewField['FIELD'], $obMigrator->getMultipleFields())){
			$arParams['MULTIPLE'] = 'multiple';
		}
		$arNewField['PARAMS'] = $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], true, $arParams);
	}
	
	/**
	 *
	 */
	protected function compileField_Field(&$arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator){
		$arNewField['VALUES'][] = array(
			'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
			'FIELD' => $arNewField['FIELD'],
			'TYPE' => 'FIELD',
			'VALUE' => $this->convertFieldValue($arOldField['VALUE'], $arNewField['IBLOCK_ID']),
			#'TITLE' => ProfileIBLock::displayAvailableItemName($arAvailableFields[$arNewField['FIELD']]),
			'TITLE' => Helper::call($this->strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arAvailableFields[$arNewField['FIELD']]]),
			'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
		);
	}
	
	/**
	 *
	 */
	protected function compileField_Const(&$arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator){
		$arNewField['VALUES'][] = array(
			'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
			'FIELD' => $arNewField['FIELD'],
			'TYPE' => 'CONST',
			'CONST' => $arOldField['CONTVALUE_TRUE'],
			'SUFFIX' => $bCondition ? 'Y' : '0',
			'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
		);
		if($bCondition){
			$arNewField['VALUES'][] = array(
				'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
				'FIELD' => $arNewField['FIELD'],
				'TYPE' => 'CONST',
				'CONST' => $arOldField['CONTVALUE_FALSE'],
				'SUFFIX' => 'N',
				'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
			);
		}
	}
	
	/**
	 *
	 */
	protected function compileField_Complex(&$arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator){
		if($arOldField['COMPLEX_TRUE_TYPE'] == 'field') {
			$arNewField['VALUES'][] = array(
				'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
				'FIELD' => $arNewField['FIELD'],
				'TYPE' => 'FIELD',
				'VALUE' => $this->convertFieldValue($arOldField['COMPLEX_TRUE_VALUE'], $arNewField['IBLOCK_ID']),
				#'TITLE' => ProfileIBLock::displayAvailableItemName($arAvailableFields[$arNewField['FIELD']]),
				'TITLE' => Helper::call($this->strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arAvailableFields[$arNewField['FIELD']]]),
				'SUFFIX' => $bCondition ? 'Y' : '0',
				'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
			);
		}
		elseif($arOldField['COMPLEX_TRUE_TYPE'] == 'const') {
			$arNewField['VALUES'][] = array(
				'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
				'FIELD' => $arNewField['FIELD'],
				'TYPE' => 'CONST',
				'CONST' => $arOldField['COMPLEX_TRUE_CONTVALUE'],
				'SUFFIX' => $bCondition ? 'Y' : '0',
				'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
			);
		}
		if($bCondition){
			if($arOldField['COMPLEX_FALSE_TYPE'] == 'field') {
				$arNewField['VALUES'][] = array(
					'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
					'FIELD' => $arNewField['FIELD'],
					'TYPE' => 'FIELD',
					'VALUE' => $this->convertFieldValue($arOldField['COMPLEX_FALSE_VALUE'], $arNewField['IBLOCK_ID']),
					#'TITLE' => ProfileIBLock::displayAvailableItemName($arAvailableFields[$arNewField['FIELD']]),
					'TITLE' => Helper::call($this->strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arAvailableFields[$arNewField['FIELD']]]),
					'SUFFIX' => 'N',
					'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
				);
			}
			elseif($arOldField['COMPLEX_FALSE_TYPE'] == 'const') {
				$arNewField['VALUES'][] = array(
					'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
					'FIELD' => $arNewField['FIELD'],
					'TYPE' => 'CONST',
					'CONST' => $arOldField['COMPLEX_FALSE_CONTVALUE'],
					'SUFFIX' => 'N',
					'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
				);
			}
		}
	}
	
	/**
	 *
	 */
	protected function compileField_Composite(&$arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator){
		if(is_array($arOldField['COMPOSITE_TRUE']) && !empty($arOldField['COMPOSITE_TRUE'])) {
			foreach($arOldField['COMPOSITE_TRUE'] as $arCompositeValue){
				if($arCompositeValue['COMPOSITE_TRUE_TYPE'] == 'field') {
					$arNewField['VALUES'][] = array(
						'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
						'FIELD' => $arNewField['FIELD'],
						'TYPE' => 'FIELD',
						'VALUE' => $this->convertFieldValue($arCompositeValue['COMPOSITE_TRUE_VALUE'], $arNewField['IBLOCK_ID']),
						#'TITLE' => ProfileIBLock::displayAvailableItemName($arAvailableFields[$arNewField['FIELD']]),
						'TITLE' => Helper::call($this->strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arAvailableFields[$arNewField['FIELD']]]),
						'SUFFIX' => $bCondition ? 'Y' : '0',
						'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
					);
				}
				elseif($arCompositeValue['COMPOSITE_TRUE_TYPE'] == 'const') {
					$arNewField['VALUES'][] = array(
						'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
						'FIELD' => $arNewField['FIELD'],
						'TYPE' => 'CONST',
						'CONST' => $arCompositeValue['COMPOSITE_TRUE_CONTVALUE'],
						'SUFFIX' => $bCondition ? 'Y' : '0',
						'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
					);
				}
			}
		}
		if($bCondition && is_array($arOldField['COMPOSITE_FALSE']) && !empty($arOldField['COMPOSITE_FALSE'])){
			foreach($arOldField['COMPOSITE_FALSE'] as $arCompositeValue){
				if($arCompositeValue['COMPOSITE_FALSE_TYPE'] == 'field') {
					$arNewField['VALUES'][] = array(
						'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
						'FIELD' => $arNewField['FIELD'],
						'TYPE' => 'FIELD',
						'VALUE' => $this->convertFieldValue($arCompositeValue['COMPOSITE_FALSE_VALUE'], $arNewField['IBLOCK_ID']),
						#'TITLE' => ProfileIBLock::displayAvailableItemName($arAvailableFields[$arNewField['FIELD']]),
						'TITLE' => Helper::call($this->strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arAvailableFields[$arNewField['FIELD']]]),
						'SUFFIX' => 'N',
						'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
					);
				}
				elseif($arCompositeValue['COMPOSITE_FALSE_TYPE'] == 'const') {
					$arNewField['VALUES'][] = array(
						'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
						'FIELD' => $arNewField['FIELD'],
						'TYPE' => 'CONST',
						'CONST' => $arCompositeValue['COMPOSITE_FALSE_CONTVALUE'],
						'SUFFIX' => 'N',
						'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
					);
				}
			}
		}
		$this->compileMultipleSeparator($arNewField['PARAMS'], $arOldField['COMPOSITE_TRUE_DIVIDER']);
	}
	
	/**
	 *
	 */
	protected function compileField_Arithmetics(&$arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator){
		if(is_array($arOldField['ARITHMETICS_TRUE']) && !empty($arOldField['ARITHMETICS_TRUE'])) {
			$arOldField['ARITHMETICS_TRUE'] = array_reverse($arOldField['ARITHMETICS_TRUE'], true);
			$strExpression = $arOldField['ARITHMETICS_TRUE_DIVIDER'];
			foreach($arOldField['ARITHMETICS_TRUE'] as $intIndex => $arArithmeticsValue){
				$arArithmeticsValueText = '';
				if($arArithmeticsValue['ARITHMETICS_TRUE_TYPE'] == 'field'){
					$arArithmeticsValueText = $this->convertFieldValue($arArithmeticsValue['ARITHMETICS_TRUE_VALUE'], $arNewField['IBLOCK_ID']);
					if((is_string($arArithmeticsValueText) || is_numeric($arArithmeticsValueText)) && array_key_exists($arArithmeticsValueText, $arAvailableFields)) {
						$arArithmeticsValueText = sprintf('{=%s.%s}', 
							$arAvailableFields[$arArithmeticsValueText]['CATEGORY'], $arArithmeticsValueText);
					}
					else {
						$arArithmeticsValueText = '0';
					}
				}
				elseif($arArithmeticsValue['ARITHMETICS_TRUE_TYPE'] == 'const'){
					$arArithmeticsValueText = $arArithmeticsValue['ARITHMETICS_TRUE_CONTVALUE'];
				}
				$strExpression = str_replace('x'.$intIndex, $arArithmeticsValueText, $strExpression);
			}
			$arNewField['VALUES'][] = array(
				'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
				'FIELD' => $arNewField['FIELD'],
				'TYPE' => 'CONST',
				'CONST' => $strExpression,
				'SUFFIX' => $bCondition ? 'Y' : '0',
				'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false, array('MATH' => 'Y')),
			);
		}
		if($bCondition && is_array($arOldField['ARITHMETICS_FALSE']) && !empty($arOldField['ARITHMETICS_FALSE'])) {
			$arOldField['ARITHMETICS_FALSE'] = array_reverse($arOldField['ARITHMETICS_FALSE'], true);
			$strExpression = $arOldField['ARITHMETICS_FALSE_DIVIDER'];
			foreach($arOldField['ARITHMETICS_FALSE'] as $intIndex => $arArithmeticsValue){
				$arArithmeticsValueText = '';
				if($arArithmeticsValue['ARITHMETICS_FALSE_TYPE'] == 'field'){
					$arArithmeticsValueText = $this->convertFieldValue($arArithmeticsValue['ARITHMETICS_FALSE_VALUE'], $arNewField['IBLOCK_ID']);
					if(array_key_exists($arArithmeticsValueText, $arAvailableFields)) {
						$arArithmeticsValueText = sprintf('{=%s.%s}', 
							$arAvailableFields[$arArithmeticsValueText]['CATEGORY'], $arArithmeticsValueText);
					}
				}
				elseif($arArithmeticsValue['ARITHMETICS_FALSE_TYPE'] == 'const'){
					$arArithmeticsValueText = $arArithmeticsValue['ARITHMETICS_FALSE_CONTVALUE'];
				}
				$strExpression = str_replace('x'.$intIndex, $arArithmeticsValueText, $strExpression);
			}
			$arNewField['VALUES'][] = array(
				'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
				'FIELD' => $arNewField['FIELD'],
				'TYPE' => 'CONST',
				'CONST' => $strExpression,
				'SUFFIX' => 'N',
				'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false, array('MATH' => 'Y')),
			);
		}
	}
	
	/**
	 *
	 */
	protected function compileField_Stack(&$arNewField, $arOldField, $bCondition, $arAvailableFields, $obMigrator){
		if(is_array($arOldField['STACK_TRUE']) && !empty($arOldField['STACK_TRUE'])) {
			foreach($arOldField['STACK_TRUE'] as $arStackValue){
				if($arStackValue['STACK_TRUE_TYPE'] == 'field') {
					$arNewField['VALUES'][] = array(
						'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
						'FIELD' => $arNewField['FIELD'],
						'TYPE' => 'FIELD',
						'VALUE' => $this->convertFieldValue($arStackValue['STACK_TRUE_VALUE'], $arNewField['IBLOCK_ID']),
						#'TITLE' => ProfileIBLock::displayAvailableItemName($arAvailableFields[$arNewField['FIELD']]),
						'TITLE' => Helper::call($this->strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arAvailableFields[$arNewField['FIELD']]]),
						'SUFFIX' => $bCondition ? 'Y' : '0',
						'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
					);
				}
				elseif($arStackValue['STACK_TRUE_TYPE'] == 'const') {
					$arNewField['VALUES'][] = array(
						'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
						'FIELD' => $arNewField['FIELD'],
						'TYPE' => 'CONST',
						'CONST' => $arStackValue['STACK_TRUE_CONTVALUE'],
						'SUFFIX' => $bCondition ? 'Y' : '0',
						'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
					);
				}
			}
		}
		if($bCondition && is_array($arOldField['STACK_FALSE']) && !empty($arOldField['STACK_FALSE'])) {
			foreach($arOldField['STACK_FALSE'] as $arStackValue){
				if($arStackValue['STACK_FALSE_TYPE'] == 'field') {
					$arNewField['VALUES'][] = array(
						'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
						'FIELD' => $arNewField['FIELD'],
						'TYPE' => 'FIELD',
						'VALUE' => $this->convertFieldValue($arStackValue['STACK_FALSE_VALUE'], $arNewField['IBLOCK_ID']),
						#'TITLE' => ProfileIBLock::displayAvailableItemName($arAvailableFields[$arNewField['FIELD']]),
						'TITLE' => Helper::call($this->strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arAvailableFields[$arNewField['FIELD']]]),
						'SUFFIX' => 'N',
						'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
					);
				}
				elseif($arStackValue['STACK_FALSE_TYPE'] == 'const') {
					$arNewField['VALUES'][] = array(
						'IBLOCK_ID' => $arNewField['IBLOCK_ID'],
						'FIELD' => $arNewField['FIELD'],
						'TYPE' => 'CONST',
						'CONST' => $arStackValue['STACK_FALSE_CONTVALUE'],
						'SUFFIX' => 'N',
						'PARAMS' => $this->convertParams($arNewField, $arOldField, $arNewField['IBLOCK_ID'], false),
					);
				}
			}
		}
	}
	
	/**
	 *	Extract from all sections just sections from selected IBlock
	 */
	protected function extractIBlockSections($arAllSectionsID, $intIBlockID){
		$arResult = array();
		if(is_array($arAllSectionsID) && !empty($arAllSectionsID)){
			$arSort = array(
				'LEFT_MARGIN' => 'ASC',
			);
			$arFilter = array(
				'ID' => $arAllSectionsID,
				'IBLOCK_ID' => $intIBlockID,
				'CHECK_PERMISSIONS' => 'N',
			);
			$arSelect = array(
				'ID',
			);
			$resSections = \CIBlockSection::getList($arSort, $arFilter, false, $arSelect);
			while($arSection = $resSections->getNext(false,false)){
				$arResult[] = $arSection['ID'];
			}
			unset($resSections, $arSection, $arSort, $arFilter, $arSelect);
		}
		return $arResult;
	}
	
	/**
	 *	Convert old field, for example: CATALOG-PRICE_3_WD -> CATALOG_PRICE_3__WITH_DISCOUNT
	 *	returns: new field code || false
	 */
	protected function convertFieldValue($strOldField, $intIBlockID){
		$strResult = false;
		#
		$arFieldsGeneral = array('ID', 'EXTERNAL_ID', 'NAME', 'CODE', 'ACTIVE', 'DETAIL_PAGE_URL', 'DATE_ACTIVE_FROM', 
			'DATE_ACTIVE_TO', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'DETAIL_TEXT', 'DETAIL_PICTURE', 'IBLOCK_ID', 'IBLOCK_CODE', 
			'IBLOCK_SECTION_ID', 'CREATED_BY', 'TIMESTAMP_X', 'MODIFIED_BY');
		$arFieldsExtra = array('IBLOCK_SECTION_NAME'=>'SECTION__NAME', 'CREATED_USER_NAME'=>'CREATED_BY__NAME', 
			'USER_NAME'=>'MODIFIED_BY__NAME');
		$arFieldsSection = array('SECTION.EXTERNAL_ID'=>'SECTION__XML_ID', 'SECTION.DESCRIPTION'=>'SECTION__DESCRIPTION', 
			'SECTION.PICTURE'=>'SECTION__PICTURE', 'SECTION.DETAIL_PICTURE'=>'SECTION__DETAIL_PICTURE');
		// Is general field?
		if(in_array($strOldField, $arFieldsGeneral)){
			$strResult = $strOldField;
		}
		// Is extra field?
		elseif(array_key_exists($strOldField, $arFieldsExtra)){
			$strResult = $arFieldsExtra[$strOldField];
		}
		// Is section field?
		elseif(array_key_exists($strOldField, $arFieldsSection)){
			$strResult = $arFieldsSection[$strOldField];
		}
		// Is section property?
		elseif(preg_match('#^UF_.*?$#', $strOldField, $arMatch)){
			$strResult = 'SECTION__'.$strOldField;
		}
		// Is property?
		elseif(preg_match('#^(\d+)\-PROPERTY\-(\d+)$#', $strOldField, $arMatch)){ // 20-PROPERTY-390
			$intPropIBlockID = $arMatch[1];
			$intPropID = $arMatch[2];
			$arProperty = $this->getIBlockProperty($intIBlockID, $intPropID);
			if(is_array($arProperty)){
				$strResult = 'PROPERTY_'.(strlen($arProperty['CODE']) ? $arProperty['CODE'] : $arProperty['ID']);
			}
		}
		// Is sub property?
		elseif(preg_match('#^(\d+)\-PROPERTYHL\-(\d+)\-(\d+)\-([A-z0-9-_]+)$#', $strOldField, $arMatch)){ // 2-PROPERTYHL-24-390-UF_NAME
			$intPropHlIBlockID = $arMatch[1];
			$intPropHlFieldID = $arMatch[2];
			$intPropID = $arMatch[3];
			$strPropName = $arMatch[4];
			$arProperty = $this->getIBlockProperty($intIBlockID, $intPropID);
			if(is_array($arProperty)){
				$strResult = 'PROPERTY_'.(strlen($arProperty['CODE']) ? $arProperty['CODE'] : $arProperty['ID'])
					.ValueBase::SUBFIELD_OPERATOR.$strPropName;
			}
		}
		// Is price field?
		elseif(preg_match('#^CATALOG\-PRICE_(\d+)(|_([A-z0-9-_]+))$#', $strOldField, $arMatch)){ // CATALOG-QUANTITY
			$intPriceID = $arMatch[1];
			$strSuffix = isset($arMatch[3]) ? $arMatch[3] : '';
			switch($strSuffix){
				case 'WD':
					$strResult = 'CATALOG_PRICE_'.$intPriceID.'__WITH_DISCOUNT';
					break;
				case 'D':
					$strResult = 'CATALOG_PRICE_'.$intPriceID.'__DISCOUNT';
					break;
				case 'CURRENCY':
					$strResult = 'CATALOG_PRICE_'.$intPriceID.'__CURRENCY';
					break;
				default:
					$strResult = 'CATALOG_PRICE_'.$intPriceID;
					break;
			}
		}
		// Is store quantity field?
		elseif(preg_match('#^CATALOG\-STORE_AMOUNT_(\d+)$#', $strOldField, $arMatch)){ // CATALOG-STORE_AMOUNT_1
			$intStoreID = $arMatch[1];
			$strResult = 'CATALOG_STORE_AMOUNT_'.$intStoreID;
		}
		// Is catalog field?
		elseif(preg_match('#^CATALOG\-([A-z0-9-_]+)$#', $strOldField, $arMatch)){ // CATALOG-QUANTITY
			if($arMatch[1] == 'MEASURE'){
				$strResult = 'CATALOG_MEASURE_ID';
			}
			else{
				$strResult = $arMatch[1];
			}
		}
		// Return
		return $strResult;
	}
	
	/**
	 *	Get iblock property array
	 */
	protected function getIBlockProperty($intIBlockID, $intPropertyID){
		if(!\Bitrix\Main\Loader::includeModule('iblock')){
			return false;
		}
		return \CIBlockProperty::getList(array(),array('IBLOCK_ID'=>$intIBlockID, 'ID'=>$intPropertyID))
			->getNext(false,false);
	}
	
	/**
	 *	Convert ['PARAMS']
	 */
	protected function convertParams($arNewField, $arOldField, $intIBlockID, $bField=true, $arCustomParams=array()){
		$arResult = is_array($arNewField['PARAMS']) ? $arNewField['PARAMS'] : array();
		#
		if($bField){
			if(strlen($arOldField['MULTIPROP_DIVIDER'])){
				$this->compileMultipleSeparator($arResult, $arOldField['MULTIPROP_DIVIDER']);
				/*
				if($arOldField['MULTIPROP_DIVIDER'] == '. '){
					$arResult['MULTIPLE_separator'] = 'dot';
				}
				elseif($arOldField['MULTIPROP_DIVIDER'] == '; '){
					$arResult['MULTIPLE_separator'] = 'semicolon';
				}
				elseif($arOldField['MULTIPROP_DIVIDER'] == ' - '){
					$arResult['MULTIPLE_separator'] = 'dash';
				}
				elseif($arOldField['MULTIPROP_DIVIDER'] == ' '){
					$arResult['MULTIPLE_separator'] = 'space';
				}
				elseif($arOldField['MULTIPROP_DIVIDER'] == ', '){
					$arResult['MULTIPLE_separator'] = 'comma';
				}
				else{
					$arResult['MULTIPLE_separator'] = 'other';
					$arResult['MULTIPLE_separator_other'] = $arOldField['MULTIPROP_DIVIDER'];
				}
				*/
			}
		}
		else{
			if($arOldField['HTML_ENCODE_CUT'] == 'Y'){
				$arResult['HTMLSPECIALCHARS'] = 'cut';
			}
			elseif($arOldField['HTML_ENCODE'] == 'Y'){
				$arResult['HTMLSPECIALCHARS'] = 'escape';
			}
			else{
				$arResult['HTMLSPECIALCHARS'] = 'skip';
			}
			if($arOldField['HTML_TO_TXT'] == 'Y'){
				$arResult['HTML2TEXT'] = 'Y';
			}
			if($arOldField['URL_ENCODE'] == 'Y'){
				$arResult['URLENCODE'] = 'Y';
			}
			if($arOldField['CONVERT_CASE'] == 'Y'){
				$arResult['CASE'] = 'lower';
			}
			if(is_array($arOldField['CONVERT_DATA']) && !empty($arOldField['CONVERT_DATA'])){
				$arResult['REPLACE'] = array('from'=>array(),'to'=>array());
				foreach($arOldField['CONVERT_DATA'] as $arReplace){
					$arResult['REPLACE']['from'][] = $arReplace[0];
					$arResult['REPLACE']['to'][] = $arReplace[1];
					$arResult['REPLACE']['use_regexp'][] = $arOldField['CONVERT_DATA_REGEXP'] == 'Y' ? 'Y' : 'N';
				}
			}
			if(is_numeric($arOldField['TEXT_LIMIT']) && $arOldField['TEXT_LIMIT']>0){
				$arResult['MAXLENGTH'] = $arOldField['TEXT_LIMIT'];
			}
			if($arOldField['BITRIX_ROUND_MODE'] == 'Y'){
				$arResult['ROUND'] = 'Y';
				$arResult['ROUND_round_type'] = 'rules';
			}
			elseif(is_numeric($arOldField['ROUND']['PRECISION']) && $arOldField['ROUND']['MODE'] != 'none'){
				$arResult['ROUND'] = 'Y';
				if($arOldField['ROUND']['MODE']=='DOWN'){
					$arResult['ROUND_round_type'] = 'lower';
				}
				elseif($arOldField['ROUND']['MODE']=='UP'){
					$arResult['ROUND_round_type'] = 'upper';
				}
				else{
					$arResult['ROUND_round_type'] = 'math';
				}
				$arResult['ROUND_round_precision'] = $arOldField['ROUND']['PRECISION'];
			}
		}
		#
		if(is_array($arCustomParams)){
			$arResult = array_merge($arResult, $arCustomParams);
		}
		#
		return http_build_query($arResult);
	}
	
	/**
	 *
	 */
	public function compileExternalID($intOldProfileID){
		return 'OLD_'.$intOldProfileID;
	}
	
	/**
	 *	
	 */
	protected function compileMultipleSeparator(&$arParams, $strSeparator){
		$arParams = is_array($arParams) ? $arParams : array();
		if(strlen($strSeparator)){
			$arParams['MULTIPLE'] = 'join';
			if($strSeparator == '. ' || $strSeparator == '.'){
				$arParams['MULTIPLE_separator'] = 'dot';
			}
			elseif($strSeparator == '; ' || $strSeparator == ';'){
				$arParams['MULTIPLE_separator'] = 'semicolon';
			}
			elseif($strSeparator == ' - ' || $strSeparator == '-'){
				$arParams['MULTIPLE_separator'] = 'dash';
			}
			elseif($strSeparator == ' '){
				$arParams['MULTIPLE_separator'] = 'space';
			}
			elseif($strSeparator == ', ' || $strSeparator == ','){
				$arParams['MULTIPLE_separator'] = 'comma';
			}
			else{
				$arParams['MULTIPLE_separator'] = 'other';
				$arParams['MULTIPLE_separator_other'] = $strSeparator;
			}
		}
	}
	
	/**
	 *	Convert categories redefinitions
	 */
	protected function convertCategoryRedefinitions($arNewProfile, $arOldProfile, $intIBlockID){
		$arResult = array();
		$arOldCategories = &$arOldProfile['_TOOLS']['MARKET_CATEGORY']['CATEGORY_LIST'];
		if(is_array($arOldCategories) && !empty($arOldCategories)){
			$arFilter = array(
				'IBLOCK_ID' => $intIBlockID,
				'CHECK_PERMISSIONS' => 'N',
			);
			$resSections = \CIBlockSection::getList(array('LEFT_MARGIN'=>'ASC'), $arFilter, false,
				array('ID'));
			while($arSection = $resSections->getNext(false, false)){
				if(strlen($arOldCategories[$arSection['ID']])){
					$arResult[] = array(
						'IBLOCK_ID' => $intIBlockID,
						'SECTION_ID' => $arSection['ID'],
						'SECTION_NAME' => $arOldCategories[$arSection['ID']],
					);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Convert additional fields
	 */
	protected function convertAdditionalFields($arNewProfile, $arOldProfile, $intIBlockID, $obMigrator){
		$arResult = array();
		if(is_array($arOldProfile['_DATA']['XMLDATA'])){
			foreach($arOldProfile['_DATA']['XMLDATA'] as $strField => $arField){
				if(preg_match('#^PARAM(\d+)$#', $strField, $arMatch)){
					$arNewField = array();
					$arNewField['PROFILE_ID'] = $arNewProfile['ID'];
					$arNewField['IBLOCK_ID'] = $intIBlockID;
					$arNewField['FIELD'] = $strField;
					$arNewField['TYPE'] = 'field';
					$this->compileField($arNewField, $arField, $obMigrator);
					$strDefaultField = '';
					if(is_array($arNewField['VALUES']) && !empty($arNewField['VALUES'])){
						foreach($arNewField['VALUES'] as $arValue){
							if($arValue['TYPE'] == 'FIELD' && strlen($arValue['VALUE'])){
								$strDefaultField = $arValue['VALUE'];
							}
						}
					}
					$arResult[] = array(
						'IBLOCK_ID' => $intIBlockID,
						'NAME' => $arField['NAME'],
						'DEFAULT_FIELD' => $strDefaultField,
					);
					unset($arNewField, $strField, $arField, $arValue, $strDefaultField);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Serialize all 'PARAMS' in a new profile
	 */
	/*
	protected function serializeParamsRecursive(&$arData){
		if(isset($arData['PARAMS']) && is_array($arData['PARAMS'])){
			$arData['PARAMS'] = serialize($arData['PARAMS']);
		}
		foreach($arData as &$arItem){
			if(is_array($arItem)){
				$this->serializeParamsRecursive($arItem);
			}
		}
	}
	*/

	/**
	 *	Save one migrated profile
	 */
	public function saveProfile($arNewProfile){
		#$this->serializeParamsRecursive($arNewProfile);
		#$intNewProfileID = \Acrit\Core\Export\Backup::setProfileData($arNewProfile);
		$intNewProfileID = Helper::call($this->strModuleId, 'Backup', 'setProfileData', [$arNewProfile]);
		return !!$intNewProfileID;
	}
	
}

?>