<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field as Field,
	\Acrit\Core\Export\Backup,
	\Acrit\Core\Cli,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

/**
 * Class ProfileTable
 * @package Acrit\Core\Export
 */

abstract class ProfileTable extends Entity\DataManager {
	
	const TABLE_NAME = ''; // Must be overriden! Value must contain table name! Value cannot be null!
	
	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!
	
	const FIELD_SORT_ELEMENT = '__SORT_ELEMENT';
	const FIELD_SORT_OFFER = '__SORT_OFFER';
	
	
	protected static $arCacheGetFilter = array();
	protected static $arCacheGetProfiles = array();
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return static::TABLE_NAME;
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() {
		return array(
			'ID' => new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_ID'),
			)),
			'ACTIVE' => new Entity\StringField('ACTIVE', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_ACTIVE'),
			)),
			'NAME' => new Entity\StringField('NAME', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_NAME'),
			)),
			'CODE' => new Entity\StringField('CODE', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_CODE'),
			)),
			'DESCRIPTION' => new Entity\TextField('DESCRIPTION', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_DESCRIPTION'),
			)),
			'SORT' => new Entity\IntegerField('SORT', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_SORT'),
			)),
			'SITE_ID' => new Entity\StringField('SITE_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_SITE_ID'),
			)),
			'DOMAIN' => new Entity\StringField('DOMAIN', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_DOMAIN'),
			)),
			'IS_HTTPS' => new Entity\StringField('IS_HTTPS', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_IS_HTTPS'),
			)),
			'PLUGIN' => new Entity\StringField('PLUGIN', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_PLUGIN'),
			)),
			'FORMAT' => new Entity\StringField('FORMAT', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_FORMAT'),
			)),
			'LAST_IBLOCK_ID' => new Entity\IntegerField('LAST_IBLOCK_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_LAST_IBLOCK_ID'),
			)),
			'LAST_SETTINGS_TAB' => new Entity\StringField('LAST_SETTINGS_TAB', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_LAST_SETTINGS_TAB'),
			)),
			'PARAMS' => new Entity\TextField('PARAMS', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_PARAMS'),
			)),
			'AUTO_GENERATE' => new Entity\StringField('AUTO_GENERATE', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_AUTO_GENERATE'),
			)),
			'LOCKED' => new Entity\StringField('LOCKED', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_LOCKED'),
			)),
			'DATE_CREATED' => new Entity\DatetimeField('DATE_CREATED', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_DATE_CREATED'),
			)),
			'DATE_MODIFIED' => new Entity\DatetimeField('DATE_MODIFIED', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_DATE_MODIFIED'),
			)),
			'DATE_STARTED' => new Entity\DatetimeField('DATE_STARTED', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_DATE_STARTED'),
			)),
			'DATE_LOCKED' => new Entity\DatetimeField('DATE_LOCKED', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_DATE_LOCKED'),
			)),
			'SESSION' => new Entity\TextField('SESSION', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_SESSION'),
			)),
			'LAST_EXPORTED_ITEM' => new Entity\TextField('LAST_EXPORTED_ITEM', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_LAST_EXPORTED_ITEM'),
			)),
			'EXTERNAL_ID' => new Entity\StringField('EXTERNAL_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_EXTERNAL_ID'),
			)),
			'ONE_TIME' => new Entity\StringField('ONE_TIME', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_ONE_TIME'),
			)),
		);
	}
	
	/**
	 *	Add item
	 */
	public static function add(array $data){
		$bClearCache = $data['_SKIP_CLEAR_CACHE'] !== true;
		unset($data['_SKIP_CLEAR_CACHE']);
		$obResult = parent::add($data);
		if($bClearCache){
			static::clearProfilesCache();
		}
		return $obResult;
	}
	
	/**
	 *	Update item
	 */
	public static function update($primary, array $data) {
		if($data['_QUIET'] !== 'Y') {
			$data['DATE_MODIFIED'] = new \Bitrix\Main\Type\DateTime();
		}
		if(isset($data['_QUIET'])){
			unset($data['_QUIET']);
		}
		$obResult = parent::update($primary, $data);
		if($data['_KEEP_CACHE'] !== 'Y'){
			static::clearProfilesCache();
		}
		return $obResult;
	}
	
	/**
	 *	Set param
	 */
	public static function setParam($primary, array $params){
		$arProfile = static::getProfiles($primary, [], false, false);
		if(!is_array($arProfile['PARAMS'])){
			$arProfile['PARAMS'] = array();
		}
		$arProfile['PARAMS'] = array_merge($arProfile['PARAMS'], $params);
		$arProfile['PARAMS'] = static::removeNullParams($arProfile['PARAMS']);
		return static::update($primary, array(
			'PARAMS' => serialize($arProfile['PARAMS']),
		));
	}
	
	/**
	 *	Remove null params of profile
	 */
	protected static function removeNullParams($params){
		foreach($params as $key => $value) {
			if(is_array($value)) {
				$params[$key] = static::removeNullParams($params[$key]);
			}
			elseif(is_null($value)) {
				unset($params[$key]);
			}
		}
		return $params;
	}
	
	/**
	 *	Delete item
	 */
	public static function delete($primary) {
		$obResult = parent::delete($primary);
		Helper::call(static::MODULE_ID, 'ProfileIBlock', 'deleteProfileData', [$primary]);
		Helper::call(static::MODULE_ID, 'ProfileField', 'deleteProfileData', [$primary]);
		Helper::call(static::MODULE_ID, 'ProfileValue', 'deleteProfileData', [$primary]);
		Helper::call(static::MODULE_ID, 'AdditionalField', 'deleteProfileData', [$primary]);
		Helper::call(static::MODULE_ID, 'CategoryRedefinition', 'deleteProfileData', [$primary]);
		Helper::call(static::MODULE_ID, 'History', 'deleteProfileData', [$primary]);
		Helper::call(static::MODULE_ID, 'ExportData', 'deleteGeneratedData', [$primary]);
		static::deleteTmpDir($primary);
		static::deleteCategoriesDir($primary);
		if (Cli::isProfileOnCron(static::MODULE_ID, $primary, 'export.php')){
			Cli::deleteProfileCron(static::MODULE_ID, $primary, 'export.php');
		}
		Log::getInstance(static::MODULE_ID)->deleteLog($primary);
		static::clearProfilesCache();
		return $obResult;
	}
	
	/**
	 *	Save IBlock settings form
	 */
	public static function updateIBlockSettings($intProfileID, $intIBlockID, $strPluginClass, $arData){
		// 1. save params
		$arCatalog = Helper::getCatalogArray($intIBlockID);
		$bMain = !(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID']);
		# If no sections selected, we will save it as 'all'
		if(!strlen($arData['SECTIONS_ID'])){
			$arData['SECTIONS_MODE'] = 'all';
		}
		#
		$arProfileIBlockFields = array(
			'PROFILE_ID' => $intProfileID,
			'IBLOCK_ID' => $intIBlockID,
			'IBLOCK_MAIN' => $bMain ? 'Y' : 'N',
			'SECTIONS_ID' => $arData['SECTIONS_ID'],
			'SECTIONS_MODE' => $arData['SECTIONS_MODE'],
			'FILTER' => $arData['FILTER'],
			'PARAMS' => serialize($arData['PARAMS']),
			'DATE_MODIFIED' => new \Bitrix\Main\Type\DateTime(),
		);
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
			),
		];
		$resIBlockParams = Helper::call(static::MODULE_ID, 'ProfileIBlock', 'getList', [$arQuery]);
		if($arIBlock = $resIBlockParams->fetch()){
			Helper::call(static::MODULE_ID, 'ProfileIBlock', 'update', [$arIBlock['ID'], $arProfileIBlockFields]);
		}
		else {
			Helper::call(static::MODULE_ID, 'ProfileIBlock', 'add', [$arProfileIBlockFields]);
		}
		// 2. save fields
		if(strlen($strPluginClass) && class_exists($strPluginClass) && is_array($arData['FIELDS'])) {
			$obPlugin = new $strPluginClass(static::MODULE_ID);
			$arProfile = static::getProfiles($intProfileID);
			$obPlugin->setProfileArray($arProfile);
			$arFieldsAll = $obPlugin->getFields($intProfileID, $intIBlockID);
			static::addSystemFields($arFieldsAll, $intProfileID, $intIBlockID);
			foreach($arFieldsAll as $obField){
				$obField->setModuleId(static::MODULE_ID);
				$strFieldCode = $obField->getCode();
				$arFieldData = $arData['FIELDS'][$strFieldCode];
				$strFieldType = $arFieldData['field_type'];
				if($strFieldType===null){
					continue;
				}
				$arValue = Helper::arrayExclude($arFieldData, array('field_type','field_params','field_conditions'));
				if($obField->isAdditional()){
					Helper::call(static::MODULE_ID, 'AdditionalField', 'update', [$obField->getID(), array(
						'NAME' => trim($arFieldData['name']),
					)]);
				}
				$strConditions = $arFieldData['field_conditions'];
				if(is_array($strConditions)){ // if multicontitional
					$arConditions = array();
					foreach($strConditions as $strKey => $strCondition){
						$arConditions[] = '#'.$strKey.'#'.$strCondition;
					}
					$strConditions = implode(Field::CONDITIONS_SEPARATOR, $arConditions);
				}
				$arFieldsData = array(
					'PROFILE_ID' => $intProfileID,
					'IBLOCK_ID' => $intIBlockID,
					'FIELD_CODE' => $strFieldCode,
					'FIELD_TYPE' => $arFieldData['field_type'],
					'VALUE' => $arValue,
					'PARAMS' => $arFieldData['field_params'],
					'CONDITIONS' => $strConditions,
				);
				static::updateFieldWithValues($arFieldsData);
			}
			// Clear cache
			static::clearProfilesCache();
			unset($obPlugin);
			//
			return true;
		}
		// return
		return false;
	}
	
	/* Remove iblock params (fields, values) for selected profile */
	public static function removeIBlockSettings($intProfileID, $intIBlockID){
		global $DB;
		$DB->StartTransaction();
		// Remove values
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
			),
			'select' => array(
				'ID',
			),
		];
		$resValues = Helper::call(static::MODULE_ID, 'ProfileValue', 'getList', [$arQuery]);
		while($arValue = $resValues->fetch()){
			Helper::call(static::MODULE_ID, 'ProfileValue', 'delete', [$arValue['ID']]);
		}
		// Remove fields
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
			),
			'select' => array(
				'ID',
			),
		];
		$resFields = Helper::call(static::MODULE_ID, 'ProfileField', 'getList', [$arQuery]);
		while($arField = $resFields->fetch()){
			Helper::call(static::MODULE_ID, 'ProfileField', 'delete', [$arField['ID']]);
		}
		// Remove additional fields
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
			),
		];
		$resFields = Helper::call(static::MODULE_ID, 'AdditionalField', 'getList', [$arQuery]);
		while($arField = $resFields->fetch()){
			Helper::call(static::MODULE_ID, 'AdditionalField', 'delete', [$arField['ID']]);
		}
		// Remove iblock settings
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
			),
			'select' => array(
				'ID',
			),
		];
		$resIBlocks = Helper::call(static::MODULE_ID, 'ProfileIBlock', 'getList', [$arQuery]);
		while($arIBlock = $resIBlocks->fetch()){
			Helper::call(static::MODULE_ID, 'ProfileIBlock', 'delete', [$arIBlock['ID']]);
		}
		// Remove custom categories
		Helper::call(static::MODULE_ID, 'CategoryCustomName', 'deleteProfileData', [$intProfileID]);
		// Commit
		$DB->Commit();
		// Delete generated data
		Helper::call(static::MODULE_ID, 'ExportData', 'deleteGeneratedData', [$intProfileID, $intIBlockID]);
		// Process offers
		if($intIBlockID){
			$arCatalog = Helper::getCatalogArray($intIBlockID);
			if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID']){
				static::removeIBlockSettings($intProfileID, $arCatalog['OFFERS_IBLOCK_ID']);
			}
		}
		// Clear cache
		static::clearProfilesCache();
	}
	
	/* Update one field */
	public static function updateFieldWithValues($arData){
		$intProfileID = $arData['PROFILE_ID'];
		$intIBlockID = $arData['IBLOCK_ID'];
		$strFieldCode = $arData['FIELD_CODE'];
		$arFieldValues = $arData['VALUE'];
		# 1. update info for current field
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
				'FIELD' => $strFieldCode,
			),
			'select' => array(
				'ID',
			),
			'limit' => '1',
		];
		#$resProfileField = ProfileField::getList($arQuery);
		$resProfileField = Helper::call(static::MODULE_ID, 'ProfileField', 'getList', [$arQuery]);
		// fields
		$arProfileFieldFields = array(
			'PROFILE_ID' => $intProfileID,
			'IBLOCK_ID' => $intIBlockID,
			'FIELD' => $strFieldCode,
			'TYPE' => $arData['FIELD_TYPE'],
			'PARAMS' => $arData['PARAMS'],
			'CONDITIONS' => $arData['CONDITIONS'],
			'DATE_MODIFIED' => new \Bitrix\Main\Type\DateTime(),
		);
		// update
		if($arProfileField = $resProfileField->fetch()){
			#$obResult = ProfileField::update($arProfileField['ID'], $arProfileFieldFields);
			$obResult = Helper::call(static::MODULE_ID, 'ProfileField', 'update', [$arProfileField['ID'], $arProfileFieldFields]);
		}
		// add
		else {
			#$obResult = ProfileField::add($arProfileFieldFields);
			$obResult = Helper::call(static::MODULE_ID, 'ProfileField', 'add', [$arProfileFieldFields]);
		}
		# 2. delele all current values for this field [field is linked to profile and iblock]
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
				'FIELD' => $strFieldCode,
			),
			'select' => array(
				'ID',
			),
		];
		#$resProfileFieldValue = ProfileValue::getList($arQuery);
		$resProfileFieldValue = Helper::call(static::MODULE_ID, 'ProfileValue', 'getList', [$arQuery]);
		while($arProfileFieldValue = $resProfileFieldValue->fetch()){
			#ProfileValue::delete($arProfileFieldValue['ID']);
			Helper::call(static::MODULE_ID, 'ProfileValue', 'delete', [$arProfileFieldValue['ID']]);
		}
		# 3.prepare values for save
		$arValues = array();
		if(is_array($arFieldValues) && is_array($arFieldValues['params'])) {
			foreach($arFieldValues['params'] as $strSuffix => $arSuffixItems){
				foreach($arSuffixItems as $intIndex => $value){
					$arValue = array();
					foreach($arFieldValues as $key => $values){
						$arValue[$key] = $values[$strSuffix][$intIndex];
					}
					$arValue['suffix'] = $strSuffix;
					$arValues[] = $arValue;
				}
			}
		}
		# 4. add all new values
		foreach($arValues as $arValue){
			$arProfileValueFields = array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
				'FIELD' => $strFieldCode,
				'TYPE' => $arValue['type'],
				'VALUE' => $arValue['value'],
				'TITLE' => $arValue['title'],
				'CONST' => $arValue['const'],
				'SUFFIX' => $arValue['suffix'],
				'PARAMS' => $arValue['params'],
				'DATE_MODIFIED' => new \Bitrix\Main\Type\DateTime(),
			);
			#$obResult = ProfileValue::add($arProfileValueFields);
			$obResult = Helper::call(static::MODULE_ID, 'ProfileValue', 'add', [$arProfileValueFields]);
		}
	}
	
	/**
	 *	Get compiled filter
	 */
	public static function getFilter($intProfileID, $intIBlockID){
		$arResult = &static::$arCacheGetFilter[static::MODULE_ID][$intProfileID.'_'.$intIBlockID];
		if(!is_array($arResult)){
			$arResult = array();
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'IBLOCK_ID' => $intIBlockID,
				),
				'select' => array(
					'FILTER',
					'SECTIONS_ID',
					'SECTIONS_MODE',
					'PARAMS',
				),
			];
			#$resData = ProfileIBlock::getList($arQuery);
			$resData = Helper::call(static::MODULE_ID, 'ProfileIBlock', 'getList', [$arQuery]);
			if($arItem = $resData->fetch()){
				$arIBlockParams = unserialize($arItem['PARAMS']);
				$obFilter = new Filter(static::MODULE_ID, $intIBlockID);
				$obFilter->setJson($arItem['FILTER']);
				$obFilter->setIncludeSubsections($arIBlockParams['FILTER_INCLUDE_SUBSECTIONS'] == 'Y');
				$arResult = $obFilter->buildFilter();
				static::applySectionsFilter($arResult, $arItem['SECTIONS_ID'], $arItem['SECTIONS_MODE']);
				unset($obFilter, $arIBlockParams);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Apply sections settings to filter
	 */
	public static function applySectionsFilter(&$arFilter, $strSectionsID, $strSectionsMode){
		$arSectionsID = explode(',', $strSectionsID);
		Helper::arrayRemoveEmptyValues($arSectionsID);
		if(!empty($arSectionsID)){
			if(count($arSectionsID)===1){
				$arSectionsID = $arSectionsID[0];
			}
			switch($strSectionsMode){
				case 'selected':
					$arFilter['SECTION_ID'] = $arSectionsID;
					break;
				case 'selected_with_subsections':
					$arFilter[] = array(
						'SECTION_ID' => $arSectionsID,
						'INCLUDE_SUBSECTIONS' => 'Y',
					);
					#$arFilter['SECTION_ID'] = $arSectionsID;
					#$arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
					break;
			}
		}
	}
	
	/**
	 *	Check if item is satisfy [FOR PROFILE]
	 */
	public static function isItemFiltered($intProfileID, $intIBlockID, $intElementID){
		$arCatalog = Helper::getCatalogArray($intIBlockID);
		if(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID']){
			$arFilterTmp = [
				'ID' => $intElementID,
				'IBLOCK_ID' => $intIBlockID,
			];
			$arSelectTmp = [
				'ID',
				'PROPERTY_'.$arCatalog['SKU_PROPERTY_ID'],
			];
			$resItemTmp = \CIBlockElement::getList(array(), $arFilterTmp, false, false, $arSelectTmp);
			if($arItemTmp = $resItemTmp->getNext(false, false)){
				$intIBlockID = $arCatalog['PRODUCT_IBLOCK_ID'];
				$intElementID = $arItemTmp['PROPERTY_'.$arCatalog['SKU_PROPERTY_ID'].'_VALUE'];
			}
		}
		# Continue check..
		$arFilter = static::getFilter($intProfileID, $intIBlockID);
		$arFilter = array_merge($arFilter, array(
			'ID' => $intElementID,
		));
		$resItem = \CIBlockElement::GetList(array(), $arFilter, false, false, array('ID'));
		$bResult = !!$resItem->getNext(false, false);
		return $bResult;
	}
	
	/**
	 *	Check if item is satisfy [FOR CONDITIONS]
	 */
	public static function isItemSatisfy($strConditions, $intIBlockID, $intElementID, $bIncludeSubsections){
		$obFilter = new Filter(static::MODULE_ID, $intIBlockID);
		$obFilter->setJson($strConditions);
		$obFilter->setIncludeSubsections($bIncludeSubsections);
		$arFilter = $obFilter->buildFilter();
		#$arFilter = static::addFilterForSubsections($arFilter);
		unset($obFilter);
		$arFilter = array(
			'LOGIC' => 'AND',
			'IBLOCK_ID' => $intIBlockID,
			$arFilter,
			array('ID' => $intElementID)
		);
		$intCount = \CIBlockElement::GetList(array(), $arFilter, array());
		return !!$intCount;
	}
	
	/**
	 *	Transform filter for filtering also in subsections
	 */
	/*
	protected static function addFilterForSubsections($arFilter){
		$arResult = array();
		if(is_array($arFilter)){
			foreach($arFilter as $strKey => $mFilterItem){
				$strKeyCode = ltrim($strKey, '<=>!?%');
				if(is_array($mFilterItem)){
					$arResult[$strKey] = static::addFilterForSubsections($mFilterItem);
				}
				elseif($strKeyCode == 'IBLOCK_SECTION_ID'){
					$strOperation = substr($strKey, 0, strlen($strKey) - strlen($strKeyCode));
					$arResult[$strOperation.'SECTION_ID'] = $mFilterItem; // SECTION_ID in filter is right unlink IBLOCK_SECTION_ID
					$arResult['INCLUDE_SUBSECTIONS'] = 'Y';
				}
				else {
					$arResult[$strKey] = $mFilterItem;
				}
			}
		}
		return $arResult;
	}
	*/
	
	/**
	 *	Get profiles with CACHE
	 */
	public static function getProfiles($arFilter=array(), $arSort=array(), $bGetIBlocks=true, $bGetFields=true, $arSelect=array()){
		$strCacheKey = MD5(serialize([$arFilter, $arSort, $bGetIBlocks, $bGetFields, $arSelect]));
		$arResult = &static::$arCacheGetProfiles[static::MODULE_ID][$strCacheKey];
		if(isset($arResult)){
			return $arResult;
		}
		$arResult = array();
		//
		$bProfileByID = false;
		$intProfileID = false;
		if(!is_array($arFilter)) {
			if(strlen($arFilter) && is_numeric($arFilter) && $arFilter>0){
				$bProfileByID = true;
				$intProfileID = $arFilter;
				$arFilter = array(
					'ID' => $arFilter,
				);
			}
			else {
				$arFilter = array();
			}
		}
		if(!is_array($arSort) || empty($arSort)) {
			$arSort = array(
				'SORT' => 'ASC',
				'ID' => 'ASC',
			);
		}
		//
		$intMode = is_integer($intMode) ? $intMode : 0;
		//
		$obCache = new \CPHPCache;
		$intCacheLifeTime = 365*24*60*60;
		if($arFilter['_NO_CACHE']){
			$intCacheLifeTime = 0;
			unset($arFilter['_NO_CACHE']);
		}
		if(!Helper::isManagedCacheOn()){
			$intCacheLifeTime = 0;
		}
		$strCacheID = 'getProfiles_'.$strCacheKey;
		$strCacheDir = '/acrit/'.preg_replace('#^(.*?)\.(.*?)$#i', '$2', static::MODULE_ID).'/new/get_profiles';
		if($obCache->InitCache($intCacheLifeTime, $strCacheID, $strCacheDir)) {
			$arResult = $obCache->GetVars();
		} elseif($obCache->StartDataCache()) {
			$arQuery = [
				'filter' => $arFilter,
				'order' => $arSort,
			];
			if(!empty($arSelect)){
				$arQuery['select'] = $arSelect;
			}
			$arExistIBlocks = Helper::getIBlockList(false, true, false, false);
			$resProfiles = Helper::call(static::MODULE_ID, 'Profile', 'getList', [$arQuery]);
			while($arProfile = $resProfiles->fetch()){
				// Get profile iblocks
				$arProfile['IBLOCKS'] = array();
				if($bGetIBlocks) {
					$arQuery = [
						'filter' => array(
							'PROFILE_ID' => $arProfile['ID'],
						),
						'order' => array(
							'IBLOCK_ID' => 'ASC',
						),
					];
					$resProfileIBlocks = Helper::call(static::MODULE_ID, 'ProfileIBlock', 'getList', [$arQuery]);
					while($arProfileIBlock = $resProfileIBlocks->fetch()){
						if(!array_key_exists($arProfileIBlock['IBLOCK_ID'], $arExistIBlocks)){
							continue;
						}
						// Get iblock fields
						if($bGetFields){
							$arProfileIBlock['FIELDS'] = array();
							$arQuery = [
								'filter' => array(
									'PROFILE_ID' => $arProfile['ID'],
									'IBLOCK_ID' => $arProfileIBlock['IBLOCK_ID'],
								),
								'order' => array(
									'FIELD' => 'ASC',
								),
							];
							$resProfileFields = Helper::call(static::MODULE_ID, 'ProfileField', 'getList', [$arQuery]);
							while($arProfileField = $resProfileFields->fetch()){
								// decompile params
								$arProfileField['PARAMS'] = Helper::decompileParams($arProfileField['PARAMS']);
								if(!Helper::isUtf()){
									$arProfileField['PARAMS'] = Helper::convertEncoding($arProfileField['PARAMS'], 'UTF-8', 'CP1251');
								}
								// Get field values
								$arProfileField['VALUES'] = array();
								$arQuery = [
									'filter' => array(
										'PROFILE_ID' => $arProfile['ID'],
										'IBLOCK_ID' => $arProfileIBlock['IBLOCK_ID'],
										'FIELD' => $arProfileField['FIELD'],
									),
									'order' => array(
										'ID' => 'ASC',
									),
								];
								$resProfileValues = Helper::call(static::MODULE_ID, 'ProfileValue', 'getList', [$arQuery]);
								while($arProfileValue = $resProfileValues->fetch()){
									// decompile params
									$arProfileValue['PARAMS'] = Helper::decompileParams($arProfileValue['PARAMS']);
									if(!Helper::isUtf()){
										$arProfileValue['PARAMS'] = Helper::convertEncoding($arProfileValue['PARAMS'], 'UTF-8', 'CP1251');
									}
									//
									$arProfileField['VALUES'][] = $arProfileValue;
								}
								// Add to result
								$arProfileIBlock['FIELDS'][$arProfileField['FIELD']] = $arProfileField;
							}
						}
						// unserialize params
						$arProfileIBlock['PARAMS'] = strlen($arProfileIBlock['PARAMS']) ? unserialize($arProfileIBlock['PARAMS']) : array();
						if(!is_array($arProfileIBlock['PARAMS'])){
							$arProfileIBlock['PARAMS'] = array();
						}
						// Add to result
						$arProfile['IBLOCKS'][$arProfileIBlock['IBLOCK_ID']] = $arProfileIBlock;
					}
				}
				// unserialize params
				$arProfile['PARAMS'] = strlen($arProfile['PARAMS']) ? unserialize($arProfile['PARAMS']) : array();
				if(!is_array($arProfile['PARAMS'])){
					$arProfile['PARAMS'] = array();
				}
				// Add to result
				$arResult[$arProfile['ID']] = $arProfile;
			}
			$GLOBALS['CACHE_MANAGER']->StartTagCache($strCacheDir);
			$GLOBALS['CACHE_MANAGER']->RegisterTag(static::TABLE_NAME);
			$GLOBALS['CACHE_MANAGER']->EndTagCache();
			$obCache->EndDataCache($arResult);
		}
		//
		if($bProfileByID && array_key_exists($intProfileID, $arResult)){
			$arResult = $arResult[$intProfileID];
		}
		return $arResult;
	}
	
	/**
	 *	Clear tagged cache
	 */
	public static function clearProfilesCache(){
		$GLOBALS['CACHE_MANAGER']->clearByTag(static::TABLE_NAME);
		static::$arCacheGetProfiles = array();
	}
	
	/**
	 *	Get system field __SORT_ELEMENT for obPlugin->getFields()
	 */
	public static function getFieldSortElement($intProfileID, $intIBlockID, $arValues=array()){
		return static::getFieldSortGeneral($intProfileID, $intIBlockID, static::FIELD_SORT_ELEMENT, $arValues);
	}
	
	/**
	 *	Get system field __SORT_OFFER for obPlugin->getFields()
	 */
	public static function getFieldSortOffer($intProfileID, $intIBlockID, $arValues=array()){
		return static::getFieldSortGeneral($intProfileID, $intIBlockID, static::FIELD_SORT_OFFER, $arValues);
	}
	
	/**
	 *	Get system field __SORT_OFFER for obPlugin->getFields()
	 */
	protected static function getFieldSortGeneral($intProfileID, $intIBlockID, $strCode, $arValues){
		$arFieldParams = array(
			'CODE' => $strCode,
			'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_SORT_DATA'),
			'INPUT_NAME' => $strCode,
			'DEFAULT_TYPE' => 'FIELD',
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'SORT',
				),
			),
		);
		$obSortField = new Field($arFieldParams);
		$obSortField->setProfileID($intProfileID);
		$obSortField->setIBlockID($intIBlockID);
		#$obSortField->hideParams();
		$obSortField->setType('FIELD');
		$obSortField->setValue($arValues);
		return $obSortField;
	}
	
	/**
	 * Add system fields
	 * @return array
	 */
	public static function addSystemFields(&$arFields, $intProfileID, $intIBlockID){
		$arCatalog = Helper::getCatalogArray($intIBlockID);
		if($arCatalog['PRODUCT_IBLOCK_ID']){
			$arFields[] = static::getFieldSortOffer($intProfileID, $intIBlockID);
		}
		else {
			$arFields[] = static::getFieldSortElement($intProfileID, $intIBlockID);
		}
	}
	
	/**
	 *	Is profile locked?
	 */
	public static function getDateLocked($intProfileID){
		$resProfile = static::getList(array(
			'filter' => array(
				'ID' => $intProfileID,
			),
			'select' => array(
				'LOCKED',
				'DATE_LOCKED',
			),
		));
		if($arProfile = $resProfile->fetch()){
			return $arProfile['LOCKED']=='Y' ? $arProfile['DATE_LOCKED'] : false;
		}
		return false;
	}
	
	/**
	 *	Lock profile
	 */
	public static function lock($intProfileID){
		$arProfile = [
			'_QUIET' => 'Y',
			'LOCKED' => 'Y',
			'DATE_LOCKED' => new \Bitrix\Main\Type\DateTime(),
		];
		static::update($intProfileID, $arProfile);
		#Helper::call(static::MODULE_ID, 'Profile', 'update', [$intProfileID, $arProfile]);
	}
	
	/**
	 *	Unlock profile
	 */
	public static function unlock($intProfileID){
		$arProfile = [
			'_QUIET' => 'Y',
			'LOCKED' => 'N',
			'DATE_LOCKED' => null,
		];
		return static::update($intProfileID, $arProfile);
		#return Helper::call(static::MODULE_ID, 'Profile', 'update', [$intProfileID, $arProfile]);
	}
	
	/**
	 *	Is profile locked?
	 */
	public static function isLocked($mProfile){
		$arProfile = null;
		if(is_array($mProfile)){
			$arProfile = $mProfile;
		}
		elseif(is_numeric($mProfile)){
			$arQuery = [
				'filter' => array(
					'ID' => $mProfile,
				),
				'select' => array(
					'LOCKED',
					'DATE_LOCKED',
				),
				'limit' => 1,
			];
			#$resProfile = static::getList($arQuery);
			$resProfile = Helper::call(static::MODULE_ID, 'Profile', 'getList', [$arQuery]);
			$arProfile = $resProfile->fetch();
		}
		if(is_array($arProfile)){
			$bLockedFlag = $arProfile['LOCKED'] == 'Y';
			if($bLockedFlag){
				if($arProfile['DATE_LOCKED'] instanceof \Bitrix\Main\Type\DateTime){
					$intLockTime = IntVal(Helper::getOption(static::MODULE_ID, 'lock_time'));
					if($intLockTime > 0){
						$obNow = new \Bitrix\Main\Type\DateTime();
						$bLockedFlag = $obNow->getTimestamp() - $arProfile['DATE_LOCKED']->getTimestamp() < $intLockTime * 60;
						unset($obNow);
					}
				}
			}
			return $bLockedFlag;
		}
		return false;
	}
	
	/**
	 *	Unlock profile at the end
	 */
	public static function unlockOnShutdown($intProfileID){
		register_shutdown_function(function($intProfileID){
			#call_user_func_array(array(__CLASS__, 'unlock'), array($intProfileID));
			static::unlock($intProfileID);
		}, $intProfileID);
	}
	
	/**
	 *	Set date started
	 */
	public static function setDateStarted($intProfileID){
		/*
		static::update($intProfileID, array(
			'DATE_STARTED' => new \Bitrix\Main\Type\DateTime(),
		));
		*/
		$arProfile = [
			'_QUIET' => 'Y',
			'DATE_STARTED' => new \Bitrix\Main\Type\DateTime(),
		];
		Helper::call(static::MODULE_ID, 'Profile', 'update', [$intProfileID, $arProfile]);
	}
	
	/**
	 *	Get date started
	 */
	public static function getDateStarted($intProfileID){
		$resProfile = static::getList(array(
			'filter' => array(
				'ID' => $intProfileID,
			),
			'select' => array(
				'DATE_STARTED',
			),
			'limit' => 1,
		));
		if($arProfile = $resProfile->fetch()){
			return $arProfile['DATE_STARTED'];
		}
		return false;
	}
	
	/**
	 *	Save profile session
	 */
	public static function saveSession($intProfileID, $arSession){
		return static::update($intProfileID, array(
			'_QUIET' => 'Y',
			'SESSION' => serialize($arSession),
		));
	}
	
	/**
	 *	Clear profile session
	 */
	public static function clearSession($intProfileID){
		return static::update($intProfileID, array(
			'_QUIET' => 'Y',
			'SESSION' => null,
		));
	}
	
	/**
	 *	Get tmp directory for current profile
	 */
	public static function getTmpDir($intProfileID, $bAutoCreate=true, $bRelative=false){
		$strTmpDir = Helper::getTmpDir($bAutoCreate, $bRelative);
		$strTmpDir .= '/'.$intProfileID;
		$strRoot = $bRelative ? Helper::root() : '';
		if($bAutoCreate && !is_dir($strRoot.$strTmpDir)){
			mkdir($strRoot.$strTmpDir, BX_DIR_PERMISSIONS, true);
		}
		return $strTmpDir;
	}
	
	/**
	 *	Delete profile tmp directory
	 */
	public static function deleteTmpDir($intProfileID){
		$strDir = static::getTmpDir($intProfileID, false);
		if(is_dir($strDir)){
			DeleteDirFilesEx(substr($strDir, strlen($_SERVER['DOCUMENT_ROOT'])));
		}
	}
	
	/**
	 *	Delete profile categories directory
	 */
	public static function deleteCategoriesDir($intProfileID){
		$strDir = '/upload/'.static::MODULE_ID.'/categories/'.$intProfileID;
		if(is_dir($_SERVER['DOCUMENT_ROOT'].$strDir)){
			DeleteDirFilesEx($strDir);
		}
	}
	
	/**
	 *	Copy profile
	 */
	public static function copyProfile($intProfileID){
		$arProfile = Helper::call(static::MODULE_ID, 'Backup', 'getProfileData', [$intProfileID]);
		$intNewProfileID = Helper::call(static::MODULE_ID, 'Backup', 'setProfileData', [$arProfile]);
		return $intNewProfileID;
	}

	/**
	 *	Get all iblock ids in profiles
	 */
	public static function getIBlocksID($bJustActive=true){
		$arResult = array();
		if($bJustActive){
			$arFilter['ACTIVE'] = 'Y';
		}
		$arProfiles = static::getProfiles($arFilter);
		foreach($arProfiles as $arProfile){
			foreach($arProfile['IBLOCKS'] as $arIBlock){
				$arResult[] = $arIBlock['IBLOCK_ID'];
			}
		}
		$arResult = array_unique($arResult);
		unset($arFilter, $arProfiles, $arProfile, $arIBlock);
		return $arResult;
	}

	/**
	 *	Get all iblock ids with autogenerate
	 */
	public static function getAutogenerateIBlocksID($bJustActive=true){
		$arResult = array();
		$arFilter = array(
			'AUTO_GENERATE' => 'Y'
		);
		if($bJustActive){
			$arFilter['ACTIVE'] = 'Y';
		}
		$arProfiles = static::getProfiles($arFilter);
		foreach($arProfiles as $arProfile){
			if($arProfile['AUTO_GENERATE'] == 'Y'){
				foreach($arProfile['IBLOCKS'] as $arIBlock){
					$arResult[] = $arIBlock['IBLOCK_ID'];
				}
			}
		}
		$arResult = array_unique($arResult);
		unset($arFilter, $arProfiles, $arProfile, $arIBlock);
		return $arResult;
	}
	
	/**
	 *	Check xdebug and write notice (in profile list and profile edit pages)
	 */
	public static function checkXDebug(){
		if(extension_loaded('xdebug')){
			print Helper::showNote(Helper::getMessage('ACRIT_EXP_PROFILE_XDEBUG_NOTICE'), true);
		}
	}
	
}
?>