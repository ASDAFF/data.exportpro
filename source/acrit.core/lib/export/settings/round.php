<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class SettingsRound extends SettingsBase {
	
	static $bSaleAndCatalogIncluded = null;
	
	public static function getCode(){
		return 'ROUND';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 1200;
	}
	
	public static function getGroup(){
		return array(
			'CODE' => 'ADDITIONAL',
		);
	}
	
	public static function isForFields(){
		return true;
	}
	
	public static function isForValues(){
		return true;
	}
	
	public static function isShown($obField, $arParams, $arValue=null){
		return true;//$arParams['value_type']=='CONST';
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		?>
		<input type="checkbox" name="<?=static::getCode();?>" value="Y" id="<?=static::getInputID();?>"
			<?if($arParams[static::getCode()]=='Y'):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		&nbsp;
		<span id="<?=static::getInputID();?>_span">
			<?
			$arOptions = array(
				'math' =>  static::getMessage('TYPE_MATH'),
				'lower' => static::getMessage('TYPE_LOWER'),
				'upper' => static::getMessage('TYPE_UPPER'),
			);
			if(static::parsePriceID($arParams['current_value'])){
				$arOptions['rules'] = static::getMessage('TYPE_RULES_BX');
			}
			$arOptions = array(
				'REFERENCE' => array_values($arOptions),
				'REFERENCE_ID' => array_keys($arOptions),
			);
			print SelectBoxFromArray(static::getCode().'_round_type', $arOptions, 
				$arParams[static::getCode().'_round_type'], '', 'id="'.static::getInputID().'_round_type"');
			?>
			<?
			$arOptions = array(
				'-3' => static::getMessage('PRECISION_MINUS_3'),
				'-2' => static::getMessage('PRECISION_MINUS_2'),
				'-1' => static::getMessage('PRECISION_MINUS_1'),
				'0' => static::getMessage('PRECISION_0'),
				'1' => static::getMessage('PRECISION_1'),
				'2' => static::getMessage('PRECISION_2'),
				'3' => static::getMessage('PRECISION_3'),
			);
			$arOptions = array(
				'REFERENCE' => array_values($arOptions),
				'REFERENCE_ID' => array_keys($arOptions),
			);
			print SelectBoxFromArray(static::getCode().'_round_precision', $arOptions, 
				IntVal($arParams[static::getCode().'_round_precision']), '', 'id="'.static::getInputID().'_round_precision"');
			?>
		</span>
		<script>
		$('#<?=static::getInputID();?>').bind('change', function(){
			var subSettings = $('#<?=static::getInputID();?>_span');
			if($(this).is(':checked')){
				subSettings.show();
			}
			else{
				subSettings.hide();
			}
		}).trigger('change');
		$('#<?=static::getInputID();?>_round_type').bind('change', function(){
			if($(this).val()=='rules'){
				$('#<?=static::getInputID();?>_round_precision').hide();
			}
			else{
				$('#<?=static::getInputID();?>_round_precision').show();
			}
		}).trigger('change');
		</script>
		<?
	}
	
	protected static function parsePriceID($strCurrentValue){
		if(preg_match('#^CATALOG_PRICE_(\d+)(__|)(|WITH_DISCOUNT|DISCOUNT)$#', $strCurrentValue, $arMatch)){
			return $arMatch[1];
		}
		return false;
	}
	
	protected static function getPriceRoundedByRules($intPriceID, &$fPrice){
		$bSuccess = false;
		if(is_null(static::$bSaleAndCatalogIncluded)){
			static::$bSaleAndCatalogIncluded = \Bitrix\Main\Loader::includeModule('sale') && \Bitrix\Main\Loader::includeModule('catalog');
		}
		if(static::$bSaleAndCatalogIncluded){
			$arRoundRules = \Bitrix\Catalog\Product\Price::getRoundRules($intPriceID);
			if(is_array($arRoundRules) && !empty($arRoundRules)){
				foreach($arRoundRules as $arRoundRule){
					$fPrice = \Bitrix\Catalog\Product\Price::roundValue($fPrice, $arRoundRule['ROUND_PRECISION'], $arRoundRule['ROUND_TYPE']);
					$bSuccess = true;
				}
			}
		}
		return $bSuccess;
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		#
		if($arParams[static::getCode()] == 'Y'){
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				$intPriceID = static::parsePriceID($arParams['current_value']);
				if($intPriceID){
					$fPriceRoundedByBitrixRules = $strValue;
					$bPriceRoundedByBitrixRules = static::getPriceRoundedByRules($intPriceID, $fPriceRoundedByBitrixRules);
				}
				$arAvailableRoundTypes = array('math', 'lower', 'upper');
				if($bPriceRoundedByBitrixRules){
					$arAvailableRoundTypes[] = 'rules';
				}
				$strType = $arParams[static::getCode().'_round_type'];
				$strType = in_array($strType, $arAvailableRoundTypes) ? $strType : 'math';
				#
				$intPrecision = IntVal($arParams[static::getCode().'_round_precision']);
				#
				switch($strType){
					case 'math':
						$strValue = Helper::roundEx($strValue, $intPrecision, 'round');
						break;
					case 'lower':
						$strValue = Helper::roundEx($strValue, $intPrecision, 'floor');
						break;
					case 'upper':
						$strValue = Helper::roundEx($strValue, $intPrecision, 'ceil');
						break;
					case 'rules':
						$strValue = $fPriceRoundedByBitrixRules;
						break;
				}
			});
		}
		#
	}
	
}
