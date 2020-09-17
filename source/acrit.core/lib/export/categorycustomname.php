<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\ProfileTable as Profile;

Loc::loadMessages(__FILE__);

/**
 * Class CategoryCustomNameTable
 * @package Acrit\Core\Export
 */

abstract class CategoryCustomNameTable extends Entity\DataManager {
	
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
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_CUSTOM_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_CUSTOM_PROFILE_ID'),
			)),
			'IBLOCK_ID' => new Entity\IntegerField('IBLOCK_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_CUSTOM_IBLOCK_ID'),
			)),
			'CATEGORY_NAME' => new Entity\TextField('CATEGORY_NAME', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_CUSTOM_SECTION_NAME'),
			)),
			'CATEGORY_ID' => new Entity\IntegerField('CATEGORY_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_CUSTOM_CATEGORY_ID'),
			)),
			'CATEGORY_PARENT_ID' => new Entity\IntegerField('CATEGORY_PARENT_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_CUSTOM_CATEGORY_PARENT_ID'),
			)),
		);
	}
	
	/**
	 *	
	 */
	public static function deleteProfileData($intProfileID){
		$strTableName = static::getTableName();
		$strSql = "DELETE FROM `{$strTableName}` WHERE `PROFILE_ID`='{$intProfileID}';";
		return \Bitrix\Main\Application::getConnection()->query($strSql);
	}
	
}
