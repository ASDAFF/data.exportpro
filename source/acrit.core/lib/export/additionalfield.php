<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\ProfileFieldTable as ProfileField,
	\Acrit\Core\Export\ProfileValueTable as ProfileValue,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

/**
 * Class AdditionalFieldTable
 * @package Acrit\Core\Export
 */

abstract class AdditionalFieldTable extends Entity\DataManager {
	
	const TABLE_NAME = ''; // Must be overriden! Value must contain table name! Value cannot be null!
	
	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!
	
	static $arGetAdditionalFieldsCache;
	
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
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_ADDITIONAL_FIELDS_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_ADDITIONAL_FIELDS_PROFILE_ID'),
			)),
			'IBLOCK_ID' => new Entity\IntegerField('IBLOCK_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_ADDITIONAL_FIELDS_IBLOCK_ID'),
			)),
			'NAME' => new Entity\StringField('NAME', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_ADDITIONAL_FIELDS_NAME'),
			)),
			'UNIT' => new Entity\StringField('UNIT', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_ADDITIONAL_FIELDS_UNIT'),
			)),
			'DEFAULT_FIELD' => new Entity\StringField('DEFAULT_FIELD', array(
				'title' => Loc::getMessage('ACRIT_EXP_PROFILE_ADDITIONAL_FIELDS_DEFAULT_FIELD'),
			)),
		);
	}
	
	public static function add(array $data){
		$obResult = parent::add($data);
		#Profile::clearProfilesCache();
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
		return $obResult;
	}
	
	public static function delete($intID){
		$strFieldCode = static::getFieldCode($intID);
		$arQuery = [
			'filter' => array(
				'FIELD' => static::getFieldCode($intID),
			),
		];
		#$resField = ProfileField::getList($arQuery);
		$resField = Helper::call(static::MODULE_ID, 'ProfileField', 'getList', [$arQuery]);
		if($arField = $resField->fetch()){
			$arQuery = [
				'filter' => array(
					'FIELD' => $strFieldCode,
				),
				'select' => array(
					'ID'
				),
			];
			#$resValues = ProfileValue::getList($arQuery);
			$resValues = Helper::call(static::MODULE_ID, 'ProfileValue', 'getList', [$arQuery]);
			while($arValue = $resValues->fetch()){
				#ProfileValue::delete($arValue['ID']);
				Helper::call(static::MODULE_ID, 'ProfileValue', 'delete', [$arValue['ID']]);
			}
			#ProfileField::delete($arField['ID']);
			Helper::call(static::MODULE_ID, 'ProfileField', 'delete', [$arField['ID']]);
		}
		$obResult = parent::delete($intID);
		#Profile::clearProfilesCache();
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
		#Profile::clearProfilesCache();
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
	}
	
	public static function deleteAll($intProfileID, $intIBlockID){
		$arFields = static::getListForProfileIBlock($intProfileID, $intIBlockID);
		foreach($arFields as $arField){
			static::delete($arField['ID']);
		}
	}
	
	public static function getFieldCode($intID){
		return 'PARAM_'.$intID;
	}
	
	public static function getIdFromCode($strCode){
		if(preg_match('#^PARAM_(\d+)$#', $strCode, $arMatch)){
			return IntVal($arMatch[1]);
		}
		return false;
	}
	
	protected static function getDefaultFieldArray($intID, $arProp=false){
		$arResult = array(
			'ID' => $intID,
			'CODE' => static::getFieldCode($intID),
			'NAME' => is_array($arProp) ? $arProp['NAME'] : '',
			'UNIT' => '',
			'DESCRIPTION' => is_array($arProp) ? $arProp['HINT'] : '',
			'REQUIRED' => false,
			'MULTIPLE' => is_array($arProp) && $arProp['MULTIPLE']=='N' ? false : true,
			'IS_ADDITIONAL' => true,
		);
		if(is_array($arProp)){
			$arResult['DEFAULT_VALUE'] = array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => $arProp['_CODE'],
				),
			);
		}
		return $arResult;
	}
	
	/**
	 *	Add single field and return its row html
	 */
	public static function addNewAndGetHtml($intProfileID, $intIBlockID, $intPropertyId=false){
		$strPropType = '';
		if(preg_match('#^(PARENT|OFFER)\.(\d+)$#', $intPropertyId, $arMatch)){
			$strPropType = $arMatch[1].'.';
			$intPropertyId = $arMatch[2];
		}
		if($intPropertyId){
			$resProp = \CIBlockProperty::getList(array(),array('ID'=>$intPropertyId));
			$arProp = $resProp->getNext(false,false);
			if(is_array($arProp)){
				$arProp['_CODE'] = $strPropType.'PROPERTY_'.(strlen($arProp['CODE'])?$arProp['CODE']:$arProp['ID']);
			}
		}
		$arFields = array(
			'PROFILE_ID' => $intProfileID,
			'IBLOCK_ID' => $intIBlockID,
		);
		if(is_array($arProp)){
			if(strlen($arProp['NAME'])) {
				$arFields['NAME'] = $arProp['NAME'];
			}
			$arFields['DEFAULT_FIELD'] = $arProp['_CODE'];
		}
		$obResult = static::add($arFields);
		if($obResult->isSuccess()){
			$intID = $obResult->getId();
			$obField = new Field(static::getDefaultFieldArray($intID, $arProp));
			$obField->setModuleId(static::MODULE_ID);
			$obField->setProfileID($intProfileID);
			$obField->setIBlockID($intIBlockID);
			foreach(Field::getValueTypesStatic(static::MODULE_ID) as $strValueCode => $arValueData) {
				$obField->setType($strValueCode);
				break;
			}
			return $obField->displayRow();
		}
		return false;
	}
	
	/**
	 *	Get list of additional fields (for current profile and IBlock)
	 */
	public static function getListForProfileIBlock($intProfileID, $intIBlockID){
		$arResult = &static::$arGetAdditionalFieldsCache;
		if(!is_array($arResult)){
			$arResult = array();
		}
		if(!is_array($arResult[$intProfileID][$intIBlockID])) {
			$arResult[$intProfileID][$intIBlockID] = array();
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'IBLOCK_ID' => $intIBlockID,
				),
				'order' => array('ID' => 'ASC'),
			];
			#$resFields = static::getList($arQuery);
			$resFields = Helper::call(static::MODULE_ID, get_called_class(), 'getList', [$arQuery]);
			while($arField = $resFields->fetch()){
				$arField['FIELD'] = static::getFieldCode($arField['ID']);
				$arResult[$intProfileID][$intIBlockID][$arField['ID']] = $arField;
			}
		}
		return $arResult[$intProfileID][$intIBlockID];
	}
	
}
