<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Acrit\Core\Export\Helper;

Loc::loadMessages(__FILE__);

/**
 * Class ExportDataTable
 * @package Acrit\Core\Export
 */
abstract class ExportDataTable extends Entity\DataManager {
	
	const TABLE_NAME = ''; // Must be overriden! Value must contain table name! Value cannot be null!
	
	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!
	
	const TYPE_DUMMY = 'DUMMY';
	const TYPE_DUMMY_ERROR = 'DUMMY_ERROR';
	
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
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_PROFILE_ID'),
			)),
			'IBLOCK_ID' => new Entity\IntegerField('IBLOCK_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_IBLOCK_ID'),
			)),
			'ELEMENT_ID' => new Entity\IntegerField('ELEMENT_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_ELEMENT_ID'),
			)),
			'SECTION_ID' => new Entity\IntegerField('SECTION_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_SECTION_ID'),
			)),
			'CATEGORY_CUSTOM_ID' => new Entity\IntegerField('CATEGORY_CUSTOM_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_CATEGORY_CUSTOM_ID'),
			)),
			'ADDITIONAL_SECTIONS_ID' => new Entity\TextField('ADDITIONAL_SECTIONS_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_ADDITIONAL_SECTIONS_ID'),
			)),
			'CURRENCY' => new Entity\StringField('CURRENCY', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_CURRENCY'),
			)),
			'DATA' => new Entity\StringField('DATA', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_DATA'),
			)),
			'DATA_MORE' => new Entity\TextField('DATA_MORE', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_DATA_MORE'),
			)),
			'SORT' => new Entity\StringField('SORT', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_SORT'),
			)),
			'TYPE' => new Entity\StringField('TYPE', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_TYPE'),
			)),
			'IS_ERROR' => new Entity\StringField('IS_ERROR', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_IS_ERROR'),
			)),
			'DATE_GENERATED' => new Entity\DatetimeField('DATE_GENERATED', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_DATE_GENERATED'),
			)),
			'TIME' => new Entity\StringField('TIME', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_TIME'),
			)),
			'IS_OFFER' => new Entity\StringField('IS_OFFER', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_IS_OFFER'),
			)),
			'OFFERS_SUCCESS' => new Entity\IntegerField('OFFERS_SUCCESS', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_OFFERS_SUCCESS'),
			)),
			'OFFERS_ERRORS' => new Entity\IntegerField('OFFERS_ERRORS', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_OFFERS_ERRORS'),
			)),
			'EXPORTED' => new Entity\StringField('EXPORTED', array(
				'title' => Loc::getMessage('ACRIT_EXP_EXPORT_DATA_FIELD_EXPORTED'),
			)),
		);
	}
	
	/**
	 *	Delete generated data
	 */
	public static function deleteGeneratedData($intProfileID, $intIBlockID=false){
		$strTableName = static::getTableName();
		if($intIBlockID) {
			$strSql = "DELETE FROM `{$strTableName}` WHERE `PROFILE_ID`='{$intProfileID}' AND `IBLOCK_ID`='{$intIBlockID}';";
		}
		else {
			$strSql = "DELETE FROM `{$strTableName}` WHERE `PROFILE_ID`='{$intProfileID}';";
		}
		return \Bitrix\Main\Application::getConnection()->query($strSql);
	}
	
	/**
	 *	Delete elements by IBLOCK_ID
	 */
	public static function deleteProfileElementsByIBlockID($intProfileID, $intIBlockID){
		$strTableName = static::getTableName();
		$strSql = "DELETE FROM {$strTableName} WHERE `PROFILE_ID`='{$intProfileID}' AND `IBLOCK_ID`='{$intIBlockID}';";
		\Bitrix\Main\Application::getConnection()->query($strSql);
	}
	
	/**
	 *	Delete generated with errors
	 */
	public static function deleteGeneratedWithErrors($intProfileID, $intIBlockID=false){
		$strTableName = static::getTableName();
		$strDummy = static::TYPE_DUMMY;
		$strIBlock = $intIBlockID ? " AND `IBLOCK_ID`='{$intIBlockID}'" : '';
		$strSql = "
		DELETE 
		FROM `{$strTableName}`
		WHERE `PROFILE_ID`='{$intProfileID}'{$strIBlock} 
			AND (`TYPE`='{$strDummy}' OR `IS_ERROR`='Y' OR `OFFERS_ERRORS` > 0)
		";
		return \Bitrix\Main\Application::getConnection()->query($strSql);
	}
	
	/**
	 *	Set EXPORTED = NULL to all
	 */
	public static function clearExportedFlag($intProfileID){
		$strTableName = static::getTableName();
		$strSql = "UPDATE {$strTableName} SET `EXPORTED`=NULL WHERE `PROFILE_ID`='{$intProfileID}';";
		\Bitrix\Main\Application::getConnection()->query($strSql);
	}
	
	/**
	 *	Set exported flag to 'Y' for selected data item
	 */
	public static function setDataItemExported($intDataItemID){
		return static::update($intDataItemID, array(
			'EXPORTED' => 'Y',
		))->isSuccess();
	}
	
	/**
	 *	Get ID of elements that have been generated
	 */
	public static function getGeneratedElementsID($intProfileID, $intIBlockID){
		$arResult = array();
		$arCatalog = Helper::getCatalogArray($intIBlockID);
		# considering relative IBlocks
		/*
		if(is_array($arCatalog)){
			$arIBlocksID = array($intIBlockID);
			if($arCatalog['PRODUCT_IBLOCK_ID']){
				$arIBlocksID[] = $arCatalog['PRODUCT_IBLOCK_ID'];
			}
			if($arCatalog['OFFERS_IBLOCK_ID']){
				$arIBlocksID[] = $arCatalog['OFFERS_IBLOCK_ID'];
			}
			$arIBlocksID = array_unique($arIBlocksID);
			if(count($arIBlocksID)>0){
				$intIBlockID = $arIBlocksID;
			}
		}
		*/
		#
		$resItems = static::getList(array(
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
			),
			'select' => array(
				'ELEMENT_ID',
			),
		));
		while($arItem = $resItems->fetch()){
			$arResult[] = IntVal($arItem['ELEMENT_ID']);
		}
		return $arResult;
	}
	
	/**
	 *	Delete elements by ELEMENT_ID ($mID contains ID of iblock elements)
	 */
	public static function deleteProfileElementsByID($intProfileID, $mID){
		$intProfileID = IntVal($intProfileID);
		$mID = is_array($mID) ? $mID : ($mID>0 ? array($mID) : false);
		if(is_array($mID) && !empty($mID)){
			foreach($mID as $key => $intID){
				$intID = IntVal($intID);
				$mID[$key] = "`ELEMENT_ID`='{$intID}'";
			}
			$strTableName = static::getTableName();
			$mID = implode(' OR ', $mID);
			$strSql = "DELETE FROM {$strTableName} WHERE `PROFILE_ID`='{$intProfileID}' AND {$mID};";
			\Bitrix\Main\Application::getConnection()->query($strSql);
		}
	}

}
?>