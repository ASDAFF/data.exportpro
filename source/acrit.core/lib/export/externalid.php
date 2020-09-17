<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

/**
 * Class ExternalIdTable
 * @package Acrit\Core\Export
 */

abstract class ExternalIdTable extends Entity\DataManager {
	
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
				'title' => Loc::getMessage('ACRIT_EXP_EXTERNAL_FIELD_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_EXTERNAL_FIELD_PROFILE_ID'),
			)),
			'IBLOCK_ID' => new Entity\IntegerField('IBLOCK_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_EXTERNAL_FIELD_IBLOCK_ID'),
			)),
			'ELEMENT_ID' => new Entity\StringField('ELEMENT_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_EXTERNAL_FIELD_ELEMENT_ID'),
			)),
			'EXTERNAL_ID' => new Entity\StringField('EXTERNAL_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXTERNAL_FIELD_EXTERNAL_ID'),
			)),
			'EXTERNAL_STATUS' => new Entity\StringField('EXTERNAL_STATUS', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXTERNAL_FIELD_EXTERNAL_STATUS'),
			)),
			'EXTERNAL_DATA' => new Entity\StringField('EXTERNAL_DATA', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXTERNAL_FIELD_EXTERNAL_DATA'),
			)),
			'DATE_CREATED' => new Entity\DatetimeField('DATE_CREATED', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXTERNAL_FIELD_DATE_CREATED'),
			)),
		);
	}
	
	/**
	 *	Add item
	 */
	public static function add(array $data){
		$data['DATE_CREATED'] = new \Bitrix\Main\Type\DateTime();
		return parent::add($data);
	}
	
	/**
	 *	Get value for single item
	 */
	public static function get($intProfileId, $intIBlockId, $strElementId){
		$arElement = static::getList(array(
			'filter' => array(
				'PROFILE_ID' => $intProfileId,
				'IBLOCK_ID' => $intIBlockId,
				'ELEMENT_ID' => $strElementId,
			),
		))->fetch();
		if($arElement === false){
			return false;
		}
		return $arElement['EXTERNAL_ID'];
	}
	
	/**
	 *	Add (or update if exists) single item
	 */
	public static function set($intProfileId, $intIBlockId, $strElementId, $strExternalId){
		$resElement = static::getList(array(
			'filter' => array(
				'PROFILE_ID' => $intProfileId,
				'IBLOCK_ID' => $intIBlockId,
				'ELEMENT_ID' => $strElementId,
			),
		));
		if($arElement = $resElement->fetch()) {
			return static::update($arElement['ID'], array(
				'EXTERNAL_ID' => $strExternalId,
			))->isSuccess();
		}
		else{
			return static::add(array(
				'PROFILE_ID' => $intProfileId,
				'IBLOCK_ID' => $intIBlockId,
				'ELEMENT_ID' => $strElementId,
				'EXTERNAL_ID' => $strExternalId,
			))->isSuccess();
		}
	}
	
	/**
	 *	Delete single item
	 */
	public static function deleteElement($intProfileId, $intIBlockId, $strElementId){
		$bResult = true;
		$resElement = static::getList(array(
			'filter' => array(
				'PROFILE_ID' => $intProfileId,
				'IBLOCK_ID' => $intIBlockId,
				'ELEMENT_ID' => $strElementId,
			),
		));
		if($arElement = $resElement->fetch()) {
			if(!static::delete($arElement['ID'])->isSuccess()){
				$bResult = false;
			}
		}
		return $bResult;
	}
	
	/**
	 *	Delete all items for profile iblock
	 */
	public static function deleteIBlock($intProfileId, $intIBlockId){
		$bResult = true;
		$resElement = static::getList(array(
			'filter' => array(
				'PROFILE_ID' => $intProfileId,
				'IBLOCK_ID' => $intIBlockId,
			),
		));
		while($arElement = $resElement->fetch()) {
			if(!static::delete($arElement['ID'])->isSuccess()){
				$bResult = false;
			}
		}
		return $bResult;
	}
	
	/**
	 *	Delete all items for profile
	 */
	public static function deleteProfile($intProfileId){
		$bResult = true;
		$resElement = static::getList(array(
			'filter' => array(
				'PROFILE_ID' => $intProfileId,
			),
		));
		while($arElement = $resElement->fetch()) {
			if(!static::delete($arElement['ID'])->isSuccess()){
				$bResult = false;
			}
		}
		return $bResult;
	}

}
?>