<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Log;

Loc::loadMessages(__FILE__);

/**
 * Class IBlockElementSubQuery
 * @package Acrit\Core\Export
 */

class IBlockElementSubQuery {
	
	public $arFilter;
	
	public $sSelect = '';
	public $sFrom = '';
	public $sWhere = '';
	public $sGroupBy = '';
	public $sOrderBy = '';
	
	protected $strOrmClass = '';
	protected $strModuleId = '';

	function __construct($arFilter, $strField, $strTableName, $strOrmClass='', $strModuleId=''){
		$this->arFilter = $arFilter;
		$this->sSelect = $strField;
		$this->sFrom = $strTableName;
		$this->strOrmClass = $strOrmClass;
		$this->strModuleId = $strModuleId;
	}

	function prepareSql($arSelectFields=array(), $arFilter=array(), $arGroupBy=false, $arOrder=array('SORT'=>'ASC')){
		$this->sWhere = '';
		if(is_array($arFilter)){
			foreach($arFilter as $key => $value){
				$strEqualType = '=';
				if(substr($key, 0, 1) == '!'){
					$key = substr($key, 1);
					$strEqualType = '<>';
				}
				$this->sWhere .= ' AND ('.$key.$strEqualType.'\''.$value.'\')';
			}
		}
	}
	
	/**
	 *	For old version
	 *	(in new versions this method is unnecessary)
	 */
	function _sql_in($strField, $cOperationType){
		if(strlen($this->strOrmClass) && strlen($this->strModuleId)) {
			\Bitrix\Main\Application::getConnection()->startTracker();
			$arQuery = [
				'filter' => $this->arFilter,
				'select' => array($this->sSelect),
			];
			#$obResult = $strClass::getList($arQuery);
			$obResult = Helper::call($this->strModuleId, $this->strOrmClass, 'getList', [$arQuery]);
			$strSql = $obResult->getTrackerQuery()->getSql();
			$strNegative = strpos($cOperationType, 'N') === 0 ? ' NOT': '';
			$strSql = $strField.$strNegative.' IN ('.$strSql.')';
			return $strSql;
		}
	}

}