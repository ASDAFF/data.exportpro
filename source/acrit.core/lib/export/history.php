<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Log;

Loc::loadMessages(__FILE__);

/**
 * Class HistoryTable
 * @package Acrit\Core\Export
 */

abstract class HistoryTable extends Entity\DataManager {
	
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
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'required' => true,
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_PROFILE_ID'),
			)),
			'DATE_START' => new Entity\DatetimeField('DATE_START', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_DATE_START'),
			)),
			'DATE_END' => new Entity\DatetimeField('DATE_END', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_DATE_END'),
			)),
			'ELEMENTS_COUNT' => new Entity\IntegerField('ELEMENTS_COUNT', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_ELEMENTS_COUNT'),
			)),
			'ELEMENTS_Y' => new Entity\IntegerField('ELEMENTS_Y', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_ELEMENTS_Y'),
			)),
			'ELEMENTS_N' => new Entity\IntegerField('ELEMENTS_N', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_ELEMENTS_N'),
			)),
			'OFFERS_Y' => new Entity\IntegerField('OFFERS_Y', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_OFFERS_Y'),
			)),
			'OFFERS_N' => new Entity\IntegerField('OFFERS_N', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_OFFERS_N'),
			)),
			'TIME_GENERATED' => new Entity\IntegerField('TIME_GENERATED', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_TIME_GENERATED'),
			)),
			'TIME_TOTAL' => new Entity\IntegerField('TIME_TOTAL', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_TIME_TOTAL'),
			)),
			'AUTO' => new Entity\StringField('AUTO', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_AUTO'),
			)),
			'COMMAND' => new Entity\StringField('COMMAND', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_COMMAND'),
			)),
			'PID' => new Entity\StringField('PID', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_PID'),
			)),
			'MULTITHREADING' => new Entity\StringField('MULTITHREADING', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_MULTITHREADING'),
			)),
			'THREADS' => new Entity\IntegerField('THREADS', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_THREADS'),
			)),
			'ELEMENTS_PER_THREAD' => new Entity\IntegerField('ELEMENTS_PER_THREAD', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_ELEMENTS_PER_THREAD'),
			)),
			'STOPPED' => new Entity\StringField('STOPPED', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_STOPPED'),
			)),
			'USER_ID' => new Entity\IntegerField('USER_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_USER_ID'),
			)),
			'IP' => new Entity\StringField('IP', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_IP'),
			)),
			'VERSION' => new Entity\StringField('VERSION', array(
				'title' => Loc::getMessage('ACRIT_EXP_HISTORY_FIELD_VERSION'),
			)),
		);
	}
	
	/**
	 *	Delete profile data by its deleting
	 */
	public static function deleteProfileData($intProfileID){
		$strTableName = static::getTableName();
		$strSql = "DELETE FROM `{$strTableName}` WHERE `PROFILE_ID`='{$intProfileID}';";
		\Bitrix\Main\Application::getConnection()->query($strSql);
	}
	
	/**
	 *	Add item
	 */
	public static function add(array $data){
		$obResult = parent::add($data);
		if($obResult->isSuccess() && $data['PROFILE_ID']){
			static::deleteExcessItems($data['PROFILE_ID']);
		}
		return $obResult;
	}
	
	/**
	 *	Delete excess items from profile history
	 */
	public static function deleteExcessItems($intProfileId){
		$intCount = IntVal(Helper::getOption(static::MODULE_ID, 'history_count'));
		$intCountDefault = 1000;
		if($intCount <= 0){
			$intCount = $intCountDefault;
		}
		$strTableName = static::getTableName();
		$strSql = "SELECT `ID` FROM `{$strTableName}` WHERE `PROFILE_ID`='{$intProfileId}' ORDER BY `ID` DESC LIMIT {$intCount}, 1;";
		$resQueryResult = \Bitrix\Main\Application::getConnection()->query($strSql);
		if($arQueryResult = $resQueryResult->fetch()){
			$arQueryResult['ID'] = IntVal($arQueryResult['ID']);
			$strSql = "DELETE FROM `{$strTableName}` WHERE `ID` <= {$arQueryResult['ID']} ORDER BY `ID` DESC;";
			\Bitrix\Main\Application::getConnection()->query($strSql);
		}
	}
	
}
?>