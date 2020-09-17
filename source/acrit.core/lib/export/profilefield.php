<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

/**
 * Class ProfileFieldTable
 * @package Acrit\Core\Export
 */

abstract class ProfileFieldTable extends Entity\DataManager {
	
	const TABLE_NAME = ''; // Must be overriden! Value must contain table name! Value cannot be null!
	
	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!
	
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
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELDS_FIELD_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELDS_FIELD_PROFILE_ID'),
			)),
			'IBLOCK_ID' => new Entity\IntegerField('IBLOCK_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELDS_FIELD_IBLOCK_ID'),
			)),
			'FIELD' => new Entity\StringField('FIELD', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELDS_FIELD_FIELD'),
			)),
			'TYPE' => new Entity\StringField('TYPE', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELDS_FIELD_TYPE'),
			)),
			'PARAMS' => new Entity\TextField('PARAMS', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELDS_FIELD_PARAMS'),
			)),
			'CONDITIONS' => new Entity\TextField('CONDITIONS', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELDS_FIELD_CONDITIONS'),
			)),
			'DATE_MODIFIED' => new Entity\DateTimeField('DATE_MODIFIED', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_FIELDS_FIELD_DATE_MODIFIED'),
			)),
		);
	}
	
	/**
	 *	Add item
	 */
	public static function add(array $data){
		$obResult = parent::add($data);
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
		return $obResult;
	}

	/**
	 *	Update item
	 */
	public static function update($primary, array $data){
		$obResult = parent::update($primary, $data);
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
		return $obResult;
	}
	
	/**
	 *	Delete item
	 */
	public static function delete($primary){
		$obResult = parent::delete($primary);
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
		return $obResult;
	}
	
	/**
	 *	Delete profile data by its deleting
	 */
	public static function deleteProfileData($intProfileID){
		$strTableName = static::getTableName();
		$strSql = "DELETE FROM `{$strTableName}` WHERE `PROFILE_ID`='{$intProfileID}';";
		\Bitrix\Main\Application::getConnection()->query($strSql);
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
	}
	
	/**
	 *	Load saved fields (we need it to get fields types and params)
	 */
	public function loadSavedFields($intProfileID, $intIBlockID, $strField=false){ # ToDo: перенести в Profile.php
		$arResult = array();
		$arFilter = array(
			'PROFILE_ID' => $intProfileID,
			'IBLOCK_ID' => $intIBlockID,
		);
		if(strlen($strField)){
			$arFilter['FIELD'] = $strField;
		}
		$arSort = array(
			'FIELD' => 'ASC',
		);
		$resItems = static::getList(array(
			'filter' => $arFilter,
			'order' => $arSort,
		));
		while($arItem = $resItems->fetch()){
			$arItem['PARAMS'] = Helper::decompileParams($arItem['PARAMS']);
			$arResult[$arItem['FIELD']] = $arItem;
		}
		if(strlen($strField)){
			$arResult = $arResult[$strField];
		}
		return $arResult;
	}
	
}
