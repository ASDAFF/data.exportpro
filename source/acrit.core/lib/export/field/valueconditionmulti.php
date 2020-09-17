<?
/**
 * Class to work with multi-conditional values in fields
 */

namespace Acrit\Core\Export\Field;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter;

class ValueConditionMulti extends ValueBase {
	
	const SUFFIX_ELSE = '_ELSE_';
	
	protected $arConditions;
	
	/**
	 *	
	 */
	public static function getRandomSuffix(){
		return randString(8).'_'.str_replace('.', '_', microtime(true));
	}
	
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
		return Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_MULTI_NAME');
	}
	
	/**
	 *	
	 */
	public static function getCode(){
		return 'MULTICONDITION';
	}
	
	/**
	 *	
	 */
	public static function getSort(){
		return 40;
	}
	
	/**
	 *	Set conditions
	 */
	public function setConditions($strConditions){
		parent::setConditions($strConditions);
		$this->arConditions = array();
		$arConditions = strlen($strConditions) ? explode(Field::CONDITIONS_SEPARATOR, $strConditions) : array();
		foreach($arConditions as $key => $strCondition){
			if(preg_match('#^\#([A-z0-9_]+)\#(.*?)$#', $strCondition, $arMatch)){
				$this->arConditions[$arMatch[1]] = $arMatch[2];
			}
		}
	}
	
	/**
	 *	
	 */
	public function groupValues(){
		$arResult = array();
		if(is_array($this->arValues)) {
			foreach($this->arValues as $arValue){
				if(strlen($arValue['SUFFIX'])){
					$arResult[$arValue['SUFFIX']][] = $arValue;
				}
			}
		}
		if(!is_array($arResult[static::SUFFIX_ELSE])){
			$arResult[static::SUFFIX_ELSE] = array();
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
		if(empty($this->arConditions)){
			$this->arConditions = array(
				static::getRandomSuffix() => '',
			);
		}
		$arValuesGrouped = $this->groupValues();
		?>
			<div>
				<div data-role="field-multicondition-values">
					<?foreach($this->arConditions as $strSuffix => $strConditions):?>
						<?=$this->showAddCondition($strSuffix, $arValuesGrouped[$strSuffix], $strConditions);?>
					<?endforeach?>
				</div>
				<div style="margin-bottom:2px; margin-top:4px;">
					<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_MULTI_ADD');?>" 
						data-role="field-multicondition-value-add" />
					<hr/>
				</div>
				<div style="margin-bottom:2px;"><b><?=Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_MULTI_BLOCK_ELSE');?></b></div>
				<?
				$arValuesElse = $arValuesGrouped[static::SUFFIX_ELSE];
				if(!isset($arValuesElse) || empty($arValuesElse)){
					$arValuesElse = array(
						array(
							'TYPE' => 'CONST',
						),
					);
				}
				$obValueElse = new ValueSimple();
				$obValueElse->setMultiple(true);
				$obValueElse->setIBlockID($this->intIBlockID);
				$obValueElse->setFieldObject($this->obField);
				$obValueElse->setFieldCode($this->strFieldCode);
				$obValueElse->setValueSuffix(static::SUFFIX_ELSE);
				$obValueElse->setValues($arValuesElse);
				print $obValueElse->displayItem();
				unset($obValueElse, $arValuesElse);
				?>
				<br/>
			</div>
		<?
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
	 *	
	 */
	public function showAddCondition($strSuffix, $arValues=array(), $strConditions=''){
		if(!is_array($arValues) || empty($arValues)){
			$arValues = array(
				array(
					'TYPE' => 'FIELD',
				),
			);
		}
		ob_start();
		?>
		<div data-role="field-multicondition-value">
			<a href="javascript:void(0)" style="float:right;" data-role="field-multicondition-value-delete">
				<?=Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_MULTI_DELETE');?>
			</a>
			<div style="margin-bottom:2px;"><b><?=Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_MULTI_BLOCK_IF');?></b></div>
			<?
			$obFilter = new Filter($this->obField->getModuleId(), $this->intIBlockID);
			$obFilter->setInputName(static::INPUTNAME_DEFAULT.'['.$this->intIBlockID.']['.$this->strFieldCode.'][field_conditions]['.$strSuffix.']');
			$obFilter->setJson($strConditions);
			print $obFilter->show();
			unset($obFilter);
			?>
			<div style="margin-bottom:2px;"><b><?=Loc::getMessage('ACRIT_EXP_FIELDVALUE_CONDITION_MULTI_BLOCK_THEN');?></b></div>
			<?
			$obValueThen = new ValueSimple();
			$obValueThen->setMultiple(true);
			$obValueThen->setIBlockID($this->intIBlockID);
			$obValueThen->setFieldObject($this->obField);
			$obValueThen->setFieldCode($this->strFieldCode);
			$obValueThen->setValueSuffix($strSuffix);
			$obValueThen->setValues($arValues);
			print $obValueThen->displayItem();
			unset($obValueThen);
			?>
			<hr/>
		</div>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Process saved values!
	 */
	public function processValuesForElement(array $arElement, array $arProfile){
		$intProfileID = $arProfile['ID'];
		#
		$arValuesGrouped = $this->groupValues();
		$arValues = array();
		#
		$bResult = false;
		$bIncludeSubsections = $arProfile['IBLOCKS'][$this->intIBlockID]['PARAMS']['FILTER_INCLUDE_SUBSECTIONS'] == 'Y';
		foreach($this->arConditions as $strSuffix => $strConditions){
			#$bResult = Profile::isItemSatisfy($strConditions, $arElement['IBLOCK_ID'], $arElement['ID'], $bIncludeSubsections);
			$bResult = Helper::call($this->obField->getModuleId(), 'Profile', 'isItemSatisfy', [$strConditions, $arElement['IBLOCK_ID'], $arElement['ID'], $bIncludeSubsections]);
			if($bResult){
				$arValues = $arValuesGrouped[$strSuffix];
				break;
			}
		}
		if(!$bResult){
			$arValues = $arValuesGrouped[static::SUFFIX_ELSE];
		}
		if(!is_array($arValues)){
			$arValues = array();
		}
		#
		$obFieldType = new ValueSimple();
		$obFieldType->setIBlockID($this->intIBlockID);
		$obFieldType->setFieldObject($this->obField);
		$obFieldType->setFieldCode($this->strCode);
		$obFieldType->setValues($arValues);
		$obFieldType->setMultiple($this->bMultiple);
		$obFieldType->setSiteID($this->strSiteID);
		$mResult = $obFieldType->processValuesForElement($arElement, $arProfile);
		#
		return $mResult;
	}
	
}

?>