<?
/**
 * Acrit Core: ozon.ru sql
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Export\Plugins\OzonRuHelpers;

use
	\Acrit\Core\Helper,
	\Bitrix\Main\Entity;

Helper::loadMessages(__FILE__);

class AttributeTable extends Entity\DataManager {
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'acrit_ozon_attribute';
	}
	
	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() {
		\Acrit\Core\Export\Exporter::getLangPrefix(realpath(__DIR__.'/../../../class.php'), $strLang, $strHead, 
			$strName, $strHint);
		return array(
			'ID' => new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Helper::getMessage($strLang.'ID'),
			)),
			'CATEGORY_ID' => new Entity\IntegerField('CATEGORY_ID', array(
				'title' => Helper::getMessage($strLang.'CATEGORY_ID'),
			)),
			'ATTRIBUTE_ID' => new Entity\IntegerField('ATTRIBUTE_ID', array(
				'title' => Helper::getMessage($strLang.'ATTRIBUTE_ID'),
			)),
			'DICTIONARY_ID' => new Entity\IntegerField('DICTIONARY_ID', array(
				'title' => Helper::getMessage($strLang.'DICTIONARY_ID'),
			)),
			'NAME' => new Entity\StringField('NAME', array(
				'title' => Helper::getMessage($strLang.'NAME'),
			)),
			'DESCRIPTION' => new Entity\StringField('DESCRIPTION', array(
				'title' => Helper::getMessage($strLang.'DESCRIPTION'),
			)),
			'TYPE' => new Entity\StringField('TYPE', array(
				'title' => Helper::getMessage($strLang.'TYPE'),
			)),
			'IS_COLLECTION' => new Entity\StringField('IS_COLLECTION', array(
				'title' => Helper::getMessage($strLang.'IS_COLLECTION'),
			)),
			'IS_REQUIRED' => new Entity\StringField('IS_REQUIRED', array(
				'title' => Helper::getMessage($strLang.'IS_REQUIRED'),
			)),
			'GROUP_ID' => new Entity\IntegerField('GROUP_ID', array(
				'title' => Helper::getMessage($strLang.'GROUP_ID'),
			)),
			'GROUP_NAME' => new Entity\StringField('GROUP_NAME', array(
				'title' => Helper::getMessage($strLang.'GROUP_NAME'),
			)),
			'LAST_VALUES_COUNT' => new Entity\IntegerField('LAST_VALUES_COUNT', array(
				'title' => Helper::getMessage($strLang.'LAST_VALUES_COUNT'),
			)),
			'LAST_VALUES_DATETIME' => new Entity\DatetimeField('LAST_VALUES_DATETIME', array(
				'title' => Helper::getMessage($strLang.'LAST_VALUES_DATETIME'),
			)),
			'LAST_VALUES_ELAPSED_TIME' => new Entity\IntegerField('LAST_VALUES_ELAPSED_TIME', array(
				'title' => Helper::getMessage($strLang.'LAST_VALUES_ELAPSED_TIME'),
			)),
			'SESSION_ID' => new Entity\StringField('SESSION_ID', array(
				'title' => Helper::getMessage($strLang.'SESSION_ID'),
			)),
			'TIMESTAMP_X' => new Entity\DatetimeField('TIMESTAMP_X', array(
				'title' => Helper::getMessage($strLang.'TIMESTAMP_X'),
			)),
		);
	}
	
	/**
	 * Delete by filter
	 *
	 * @return array
	 */
	public static function deleteByFilter($arFilter=null) {
		$strTable = static::getTableName();
		$strSql = "DELETE FROM `{$strTable}` WHERE 1=1";
		if(is_array($arFilter)){
			foreach($arFilter as $strField => $strValue){
				$strEqual = '=';
				if(preg_match('#^(.*?)([A-z0-9_]+)(.*?)$#', $strField, $arMatch)){
					$strField = $arMatch[2];
					if($arMatch[1] == '!'){
						$strEqual = '!=';
					}
				}
				$strField = \Bitrix\Main\Application::getConnection()->getSqlHelper()->forSql($strField);
				$strValue = \Bitrix\Main\Application::getConnection()->getSqlHelper()->forSql($strValue);
				if(is_numeric($strField)){
					$strSql .= " AND ({$strValue})";
				}
				else{
					$strSql .= " AND (`{$strField}`{$strEqual}'{$strValue}')";
				}
			}
			$strSql .= ';';
		}
		return \Bitrix\Main\Application::getConnection()->query($strSql);
	}

}
