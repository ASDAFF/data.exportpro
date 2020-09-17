<?
/**
 * Class to work with conditional values in fields
 */

namespace Acrit\Core\Export\Field;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter;

class ValueCondition extends ValueBase {
	
	const SUFFIX_TRUE = 'Y';
	const SUFFIX_FALSE = 'N';
	
	/**
	 *	Create
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 *	
	 */
	public static function getName(){
		return Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_NAME');
	}
	
	/**
	 *	
	 */
	public static function getCode(){
		return 'CONDITION';
	}
	
	/**
	 *	
	 */
	public static function getSort(){
		return 30;
	}
	
	/**
	 *	
	 */
	public function groupValues(){
		$arResult = array(
			static::SUFFIX_TRUE => array(),
			static::SUFFIX_FALSE => array(),
		);
		if(is_array($this->arValues)) {
			foreach($this->arValues as $arValue){
				if($arValue['SUFFIX']==static::SUFFIX_TRUE) {
					$arResult[static::SUFFIX_TRUE][] = $arValue;
				}
				elseif($arValue['SUFFIX']==static::SUFFIX_FALSE){
					$arResult[static::SUFFIX_FALSE][] = $arValue;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Show html-code for item
	 *	@return string [html]
	 */
	protected function displayItem(){
		ob_start();
		if(!is_array($this->arValues)){
			$this->arValues = array();
		}
		if(empty($this->arValues)){
			$this->arValues[] = array(
				array(
					'TYPE' => 'FIELD',
				),
			);
		}
		$arValuesGrouped = $this->groupValues();
		if(empty($arValuesGrouped[static::SUFFIX_TRUE])) {
			$arValuesGrouped[static::SUFFIX_TRUE][] = array(
				'TYPE' => 'FIELD',
			);
		}
		if(empty($arValuesGrouped[static::SUFFIX_FALSE])) {
			$arValuesGrouped[static::SUFFIX_FALSE][] = array(
				'TYPE' => 'FIELD',
			);
		}
		#
		print '<div style="margin-bottom:2px;"><b>'.Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_BLOCK_HEADER').'</b></div>';
		$obFilter = new Filter($this->obField->getModuleId(), $this->intIBlockID);
		$obFilter->setInputName(static::INPUTNAME_DEFAULT.'['.$this->intIBlockID.']['.$this->strFieldCode.'][field_conditions]');
		$obFilter->setJson($this->strConditions);
		print $obFilter->show();
		#$obFilter->buildFilter();
		unset($obFilter);
		print '<br/>';
		#
		print '<div style="margin-bottom:2px;"><b>'.Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_BLOCK_TRUE').'</b></div>';
		$obValueSimple = new ValueSimple();
		$obValueSimple->setMultiple(true);
		$obValueSimple->setIBlockID($this->intIBlockID);
		$obValueSimple->setFieldObject($this->obField);
		$obValueSimple->setFieldCode($this->strFieldCode);
		$obValueSimple->setValueSuffix(static::SUFFIX_TRUE);
		$obValueSimple->setValues($arValuesGrouped[static::SUFFIX_TRUE]);
		print $obValueSimple->displayItem();
		unset($obValueSimple);
		print '<br/>';
		#
		print '<div style="margin-bottom:2px;"><b>'.Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_BLOCK_FALSE').'</b></div>';
		$obValueSimple = new ValueSimple();
		$obValueSimple->setMultiple(true);
		$obValueSimple->setIBlockID($this->intIBlockID);
		$obValueSimple->setFieldObject($this->obField);
		$obValueSimple->setFieldCode($this->strFieldCode);
		$obValueSimple->setValueSuffix(static::SUFFIX_FALSE);
		$obValueSimple->setValues($arValuesGrouped[static::SUFFIX_FALSE]);
		print $obValueSimple->displayItem();
		unset($obValueSimple);
		#
		return ob_get_clean();
	}
	
	/**
	 *	Display field
	 *	@return string [html]
	 */
	public function display(){
		return $this->displayItem();
	}
	
	/**
	 *	Process saved values!
	 */
	public function processValuesForElement(array $arElement, array $arProfile){
		$intProfileID = $arProfile['ID'];
		#
		$bIncludeSubsections = $arProfile['IBLOCKS'][$this->intIBlockID]['PARAMS']['FILTER_INCLUDE_SUBSECTIONS'] == 'Y';
		#$bResult = Profile::isItemSatisfy($this->strConditions, $arElement['IBLOCK_ID'], $arElement['ID'], $bIncludeSubsections);
		$bResult = Helper::call($this->obField->getModuleId(), 'Profile', 'isItemSatisfy', [$this->strConditions, $arElement['IBLOCK_ID'], $arElement['ID'], $bIncludeSubsections]);
		$arValuesGrouped = $this->groupValues();
		#
		$obFieldType = new ValueSimple();
		$obFieldType->setIBlockID($this->intIBlockID);
		$obFieldType->setFieldObject($this->obField);
		$obFieldType->setFieldCode($this->strCode);
		if($bResult) {
			$obFieldType->setValues($arValuesGrouped[static::SUFFIX_TRUE]);
		}
		else {
			$obFieldType->setValues($arValuesGrouped[static::SUFFIX_FALSE]);
		}
		$obFieldType->setMultiple($this->bMultiple);
		$obFieldType->setSiteID($this->strSiteID);
		$mResult = $obFieldType->processValuesForElement($arElement, $arProfile);
		#
		return $mResult;
	}
	
}

?>