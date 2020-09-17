<?php
namespace Acrit\Core\Crm;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field as Field,
	\Acrit\Core\Export\Backup,
	\Acrit\Core\Cli,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

/**
 * Class ProfilesTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> SORT int mandatory
 * <li> NAME string(255) mandatory
 * <li> ACTIVE string(4) mandatory
 * <li> PLUGIN string(255) mandatory
 * <li> CONNECT_CRED string mandatory
 * <li> CONNECT_DATA string mandatory
 * <li> OPTIONS string mandatory
 * <li> STAGES string mandatory
 * <li> FIELDS string mandatory
 * <li> CONTACTS string mandatory
 * <li> PRODUCTS string mandatory
 * <li> OTHER string mandatory
 * <li> SYNC string mandatory
 * <li> DATE_CREATED datetime mandatory
 * <li> DATE_MODIFIED datetime mandatory
 * </ul>
 *
 * @package Bitrix\Exportproplus
 **/

abstract class ProfilesTable extends Entity\DataManager
{

	const TABLE_NAME = ''; // Must be overriden! Value must contain table name! Value cannot be null!

	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!

	const FIELD_SORT_ELEMENT = '__SORT_ELEMENT';
	const FIELD_SORT_OFFER = '__SORT_OFFER';


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
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_ID_FIELD'),
			),
			'SORT' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_SORT_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_NAME_FIELD'),
			),
			'DESCRIPTION' => new Entity\TextField('DESCRIPTION', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELD_DESCRIPTION'),
			)),
			'ACTIVE' => array(
				'data_type' => 'string',
//				'required' => true,
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_ACTIVE_FIELD'),
			),
			'PLUGIN' => array(
				'data_type' => 'string',
//				'required' => true,
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_PLUGIN_FIELD'),
			),
			'CONNECT_CRED' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_CONNECT_CRED_FIELD'),
			),
			'CONNECT_DATA' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_CONNECT_DATA_FIELD'),
			),
			'OPTIONS' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_OPTIONS_FIELD'),
			),
			'STAGES' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_STAGES_FIELD'),
			),
			'FIELDS' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_FIELDS_FIELD'),
			),
			'CONTACTS' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_CONTACTS_FIELD'),
			),
			'PRODUCTS' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_PRODUCTS_FIELD'),
			),
			'OTHER' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_OTHER_FIELD'),
			),
			'SYNC' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_SYNC'),
			),
			'DATE_CREATED' => array(
				'data_type' => 'datetime',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_DATE_CREATED_FIELD'),
			),
			'DATE_MODIFIED' => array(
				'data_type' => 'datetime',
				'title' => Loc::getMessage('CRM_PROFILES_ENTITY_DATE_MODIFIED_FIELD'),
			),
		);
	}

	/**
	 *	Add item
	 */
	public static function add(array $data){
//		echo '<pre>'; print_r($data); echo '</pre>';
		$obResult = parent::add($data);
//		echo '<pre>'; print_r($obResult); echo '</pre>';
//		die();
		static::clearProfilesCache();
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
	 * Returns validators for NAME field.
	 *
	 * @return array
	 */
	public static function validateName() {
		return array(
			new Main\Entity\Validator\Length(null, 255),
		);
	}
	/**
	 * Returns validators for ACTIVE field.
	 *
	 * @return array
	 */
	public static function validateActive() {
		return array(
			new Main\Entity\Validator\Length(null, 4),
		);
	}
	/**
	 * Returns validators for PLUGIN field.
	 *
	 * @return array
	 */
	public static function validatePlugin() {
		return array(
			new Main\Entity\Validator\Length(null, 255),
		);
	}

	/**
	 *	Get profiles with CACHE
	 */
	public static function getProfiles($arFilter=array(), $arSort=array()){
		$strCacheKey = MD5(serialize($arFilter).serialize($arSort));
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
		$strCacheID = 'getProfiles_'.serialize($arFilter).serialize($arSort);
		$strCacheDir = '/acrit/'.preg_replace('#^(.*?)\.(.*?)$#i', '$2', static::MODULE_ID).'/crm/get_profiles';
//		if($obCache->InitCache($intCacheLifeTime, $strCacheID, $strCacheDir)) {
//			$arResult = $obCache->GetVars();
//		} elseif($obCache->StartDataCache()) {
			$arQuery = [
				'filter' => $arFilter,
				'order' => $arSort,
			];
			$resProfiles = Helper::call(static::MODULE_ID, 'CrmProfiles', 'getList', [$arQuery]);
			while($arProfile = $resProfiles->fetch()){
				// Unserialize params
				$arProfile['CONNECT_CRED'] = strlen($arProfile['CONNECT_CRED']) ? unserialize($arProfile['CONNECT_CRED']) : [];
				if(!is_array($arProfile['CONNECT_CRED'])){
					$arProfile['CONNECT_CRED'] = [];
				}
				$arProfile['CONNECT_DATA'] = strlen($arProfile['CONNECT_DATA']) ? unserialize($arProfile['CONNECT_DATA']) : [];
				if(!is_array($arProfile['CONNECT_DATA'])){
					$arProfile['CONNECT_DATA'] = [];
				}
				$arProfile['OPTIONS'] = strlen($arProfile['OPTIONS']) ? unserialize($arProfile['OPTIONS']) : [];
				if(!is_array($arProfile['OPTIONS'])){
					$arProfile['OPTIONS'] = [];
				}
				$arProfile['STAGES'] = strlen($arProfile['STAGES']) ? unserialize($arProfile['STAGES']) : [];
				if(!is_array($arProfile['STAGES'])){
					$arProfile['STAGES'] = [];
				}
				$arProfile['FIELDS'] = strlen($arProfile['FIELDS']) ? unserialize($arProfile['FIELDS']) : [];
				if(!is_array($arProfile['FIELDS'])){
					$arProfile['FIELDS'] = [];
				}
				$arProfile['CONTACTS'] = strlen($arProfile['CONTACTS']) ? unserialize($arProfile['CONTACTS']) : [];
				if(!is_array($arProfile['CONTACTS'])){
					$arProfile['CONTACTS'] = [];
				}
				$arProfile['PRODUCTS'] = strlen($arProfile['PRODUCTS']) ? unserialize($arProfile['PRODUCTS']) : [];
				if(!is_array($arProfile['PRODUCTS'])){
					$arProfile['PRODUCTS'] = [];
				}
				$arProfile['OTHER'] = strlen($arProfile['OTHER']) ? unserialize($arProfile['OTHER']) : [];
				if(!is_array($arProfile['OTHER'])){
					$arProfile['OTHER'] = [];
				}
				$arProfile['SYNC'] = strlen($arProfile['SYNC']) ? unserialize($arProfile['SYNC']) : [];
				if(!is_array($arProfile['SYNC'])){
					$arProfile['SYNC'] = [];
				}
				// Add to result
				$arResult[$arProfile['ID']] = $arProfile;
			}
			$GLOBALS['CACHE_MANAGER']->StartTagCache($strCacheDir);
			$GLOBALS['CACHE_MANAGER']->RegisterTag(static::TABLE_NAME);
			$GLOBALS['CACHE_MANAGER']->EndTagCache();
			$obCache->EndDataCache($arResult);
//		}
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
	 *	Set param
	 */
	public static function setArrayField($field, $primary, array $params){
		$arProfile = static::getProfiles($primary, [], false, false);
		if(!is_array($arProfile[$field])){
			$arProfile[$field] = array();
		}
		$arProfile[$field] = array_merge($arProfile[$field], $params);
		$arProfile[$field] = static::removeNullParams($arProfile[$field]);
		return static::update($primary, array(
			$field => serialize($arProfile[$field]),
		));
	}

	/**
	 *	Delete item
	 */
	public static function delete($primary) {
		$obResult = parent::delete($primary);
//		static::deleteTmpDir($primary);
//		if (Cli::isProfileOnCron(static::MODULE_ID, $primary, 'export.php')){
//			Cli::deleteProfileCron(static::MODULE_ID, $primary, 'export.php');
//		}
//		Log::getInstance(static::MODULE_ID)->deleteLog($primary);
		static::clearProfilesCache();
		return $obResult;
	}

}
