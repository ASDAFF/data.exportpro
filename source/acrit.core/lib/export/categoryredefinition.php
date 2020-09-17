<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\ProfileTable as Profile;

Loc::loadMessages(__FILE__);

/**
 * Class CategoryRedefinitionTable
 * @package Acrit\Core\Export
 */

abstract class CategoryRedefinitionTable extends Entity\DataManager {
	
	const TABLE_NAME = ''; // Must be overriden! Value must contain table name! Value cannot be null!
	
	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!
	
	CONST MODE_STRICT = 1;
	CONST MODE_CUSTOM = 2;
	
	CONST SOURCE_REDEFINITIONS = 1;
	CONST SOURCE_USER_FIELDS = 2;
	CONST SOURCE_CUSTOM = 3;
	
	static $arCacheProfileCategoryRedefinitions = array();
	
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
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_REDEFINITION_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_REDEFINITION_PROFILE_ID'),
			)),
			'IBLOCK_ID' => new Entity\IntegerField('IBLOCK_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_REDEFINITION_IBLOCK_ID'),
			)),
			'SECTION_ID' => new Entity\IntegerField('SECTION_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_REDEFINITION_SECTION_ID'),
			)),
			'SECTION_NAME' => new Entity\TextField('SECTION_NAME', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_REDEFINITION_SECTION_NAME'),
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
	 *	Get category redefinitions for profile
	 */
	public static function getForProfile($intProfileID, $intIBlockID=false){
		$arResult = &static::$arCacheProfileCategoryRedefinitions[$intProfileID];
		if(!is_array($arResult)){
			$arResult = [];
			$arProfile = Helper::call(static::MODULE_ID, 'Profile', 'getProfiles', [$intProfileID, [], true, false]);
			if(is_array($arProfile['IBLOCKS'])){
				foreach($arProfile['IBLOCKS'] as $arIBlock){
					if($intIBlockID && $arIBlock['IBLOCK_ID'] != $intIBlockID){
						continue;
					}
					$bHandled = false;
					$arHandlers = EventManager::getInstance()->findEventHandlers(static::MODULE_ID, 'OnGetCategoryRedefinitions');
					foreach ($arHandlers as $arHandler) {
						$bHandled = ExecuteModuleEventEx($arHandler, [&$arIBlock, &$arResult]);
					}
					if(!$bHandled){
						if($arIBlock['PARAMS']['CATEGORIES_REDEFINITION_SOURCE'] == static::SOURCE_USER_FIELDS){
							$arIBlockRedefinitions = static::_getForProfile_ByUserFields($arIBlock);
						}
						else{
							$arIBlockRedefinitions = static::_getForProfile_ByRedefinitions($arIBlock);
						}
					}
					if(is_array($arIBlockRedefinitions)){
						foreach($arIBlockRedefinitions as $intSectionId => $strSectionName){
							$arResult[$intSectionId] = $strSectionName;
						}
					}
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get category redefinitions for profile [redefinitions]
	 */
	protected static function _getForProfile_ByRedefinitions($arIBlock){
		$arResult = [];
		$resCategoryRedefinition = static::getList([
			'filter' => [
				'PROFILE_ID' => $arIBlock['PROFILE_ID'],
				'IBLOCK_ID' => $arIBlock['IBLOCK_ID'],
			],
			'select' => [
				'SECTION_ID',
				'SECTION_NAME',
			],
		]);
		while($arCategoryRedefinition = $resCategoryRedefinition->fetch()){
			$arResult[$arCategoryRedefinition['SECTION_ID']] = $arCategoryRedefinition['SECTION_NAME'];
		}
		return $arResult;
	}
	
	/**
	 *	Get category redefinitions for profile [redefinitions]
	 */
	protected static function _getForProfile_ByUserFields($arIBlock){
		$arResult = [];
		if(is_array($arIBlock['PARAMS']) && strlen($arIBlock['PARAMS']['CATEGORIES_REDEFINITION_SOURCE_UF'])){
			if(\Bitrix\Main\Loader::includeModule('iblock')){
				$arSort = [
					'ID' => 'ASC',
				];
				$arFilter = [
					'IBLOCK_ID' => $arIBlock['IBLOCK_ID'],
					'!'.$arIBlock['PARAMS']['CATEGORIES_REDEFINITION_SOURCE_UF'] => false,
				];
				$arSelect = [
					'ID',
					$arIBlock['PARAMS']['CATEGORIES_REDEFINITION_SOURCE_UF'],
				];
				$resSections = \CIBlockSection::getList($arSort, $arFilter, false, $arSelect);
				while($arSection = $resSections->getNext(false, false)){
					$arResult[$arSection['ID']] = $arSection[$arIBlock['PARAMS']['CATEGORIES_REDEFINITION_SOURCE_UF']];
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	
	 */
	public static function getSectionUserFields($intProfileID, $intIBlockId){
		$arResult = [];
		$arSort = [
			'SORT' => 'ASC',
			'FIELD_NAME' => 'ASC',
		];
		$arFilter = [
			'ENTITY_ID' => 'IBLOCK_'.$intIBlockId.'_SECTION',
			'LANG' => LANGUAGE_ID,
		];
		$arEntityType = [
			'string',
		];
		$resFields = \CUserTypeEntity::getList($arSort, $arFilter);
		while($arField = $resFields->getNext(false, false)){
			if(in_array($arField['USER_TYPE_ID'], $arEntityType)){
				$strName = $arField['EDIT_FORM_LABEL'];
				$strCode = $arField['FIELD_NAME'];
				if(strlen($strName) && $strName != $strCode){
					$strName = '['.$strName.'] ('.$strCode.')';
				}
				$arResult[$arField['FIELD_NAME']] = $strName;
			}
		}
		return $arResult;
	}
	
}
