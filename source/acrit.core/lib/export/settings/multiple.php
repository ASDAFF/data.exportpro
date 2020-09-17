<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class SettingsMultiple extends SettingsBase {
	
	public static function getCode(){
		return 'MULTIPLE';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 0; # this settings must be first at all!
	}
	
	public static function getGroup(){
		return array(
			'CODE' => 'GENERAL',
		);
	}
	
	public static function isForFields(){
		return true;
	}
	
	public static function isForValues(){
		return true;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		$arOptions = array(
			'join' => static::getMessage('JOIN'),
			'first' => static::getMessage('FIRST'),
		);
		if($obField->isMultiple()){
			$arOptions['multiple'] = static::getMessage('MULTIPLE');
		}
		$arOptions = array(
			'REFERENCE' => array_values($arOptions),
			'REFERENCE_ID' => array_keys($arOptions),
		);
		$strName = static::getCode();
		$strID = static::getInputID();
		print SelectBoxFromArray($strName, $arOptions, $arParams[$strName], '', 'id="'.$strID.'"');
		print Helper::ShowHint(static::getHint());
		//
		$arSeparator = array(
			'comma' => static::getMessage('SEPARATOR_COMMA'),
			'dot' => static::getMessage('SEPARATOR_DOT'),
			'semicolon' => static::getMessage('SEPARATOR_SEMICOLON'),
			'dash' => static::getMessage('SEPARATOR_DASH'),
			'space' => static::getMessage('SEPARATOR_SPACE'),
			'new_line' => static::getMessage('SEPARATOR_NEW_LINE'),
			'new_line_2' => static::getMessage('SEPARATOR_NEW_LINE_2'),
			'other' => static::getMessage('SEPARATOR_OTHER'),
			'empty' => static::getMessage('SEPARATOR_EMPTY'),
		);
		$arSeparator = array(
			'REFERENCE' => array_values($arSeparator),
			'REFERENCE_ID' => array_keys($arSeparator),
		);
		print '<br/>';
		$strName_Separator = static::getCode().'_separator';
		$strID_Separator = static::getInputID().'_separator';
		$strName_SeparatorOther = static::getCode().'_separator_other';
		$strID_SeparatorOther = static::getInputID().'_separator_other';
		if($arParams[$strName_Separator] == 'other' && !strlen($arParams[$strName_SeparatorOther])){
			$arParams[$strName_Separator] = 'empty';
		}
		print SelectBoxFromArray($strName_Separator, $arSeparator, $arParams[$strName_Separator], '', 
			'id="'.$strID_Separator.'" style="display:none;margin-top:3px;"');
		?>
		<input name="<?=$strName_SeparatorOther;?>" type="text" id="<?=$strID_SeparatorOther;?>" size="5"
			value="<?=$arParams[$strName_SeparatorOther];?>" style="display:none;margin-top:3px;" />
		<?
		$strName_Scheme = static::getCode().'_scheme';
		$strID_Scheme = static::getInputID().'_scheme';
		?>
		<input name="<?=$strName_Scheme;?>" type="text" id="<?=$strID_Scheme;?>" size="20"
			placeholder="<?=static::getMessage('SCHEME_PLACEHOLDER');?>"
			value="<?=$arParams[$strName_Scheme];?>" style="margin-top:3px;" />
		<script>
		// 2. Select separator
		$('#<?=$strID_Separator;?>').bind('change', function(){
			var input = $('#<?=$strID_SeparatorOther;?>');
			if($(this).val()=='other' && $(this).is(':visible')){
				input.show();
			}
			else{
				input.hide();
			}
		}).trigger('change');
		// 1. Select type
		$('#<?=$strID;?>').bind('change', function(){
			var selectSeparator = $('#<?=$strID_Separator;?>');
			if($(this).val()=='join'){
				selectSeparator.show();
			}
			else{
				selectSeparator.hide();
			}
			selectSeparator.trigger('change');
		}).trigger('change');
		</script>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		$strCode = static::getCode();
		if(is_array($mValue)) {
			$mParamValue = $arParams[$strCode];
			if($mParamValue=='first'){
				# Search first non-empty value
				$bFound = false;
				static::applyScheme($mValue, $arParams[$strCode.'_scheme']);
				foreach($mValue as $mValueItem){
					if (is_string($mValueItem) && strlen($mValueItem) || is_numeric($mValueItem) && $mValueItem>0 || is_array($mValueItem) && !empty($mValueItem)) {
						$mValue = $mValueItem;
						$bFound = true;
						break;
					}
				}
				if(!$bFound){
					$mValue = '';
				}
			}
			elseif($mParamValue=='multiple'){
				# Nothing, keep it as is
				$arValueTmp = array();
				foreach($mValue as $mValueItem){
					if(is_array($mValueItem)){
						foreach($mValueItem as $mValueSubItem){
							$arValueTmp[] = $mValueSubItem;
						}
					}
					else {
						$arValueTmp[] = $mValueItem;
					}
				}
				Helper::arrayRemoveEmptyValues($arValueTmp, false);
				static::applyScheme($arValueTmp, $arParams[$strCode.'_scheme']);
				$mValue = $arValueTmp;
				unset($arValueTmp);
			}
			else {
				# Join
				$arValueTmp = array();
				foreach($mValue as $mValueItem){
					if(is_array($mValueItem)){
						foreach($mValueItem as $mValueSubItem){
							$arValueTmp[] = $mValueSubItem;
						}
					}
					else{
						$arValueTmp[] = $mValueItem;
					}
				}
				Helper::arrayRemoveEmptyValues($arValueTmp, false);
				static::applyScheme($arValueTmp, $arParams[$strCode.'_scheme']);
				switch($arParams[$strCode.'_separator']){
					case 'dot':
						$strSeparator = '. ';
						break;
					case 'semicolon':
						$strSeparator = '; ';
						break;
					case 'dash':
						$strSeparator = ' - ';
						break;
					case 'space':
						$strSeparator = ' ';
						break;
					case 'new_line':
						$strSeparator = "\n";
						break;
					case 'new_line_2':
						$strSeparator = "\n\n";
						break;
					case 'other':
						$strSeparator = $arParams[$strCode.'_separator_other'];
						break;
					case 'empty':
						$strSeparator = '';
						break;
					default:
						$strSeparator = ', ';
						break;
				}
				$mValue = implode($strSeparator, $arValueTmp);
			}
		}
	}
	
	protected static function applyScheme(&$arValue, $strScheme){
		if(empty($arValue) || empty($strScheme)){
			return;
		}
		$arScheme = explode(',', $strScheme);
		$arScheme = array_map(function($mItem){
			$mItem = trim($mItem);
			$mItemTmp = array();
			if(preg_match('#^(\d+)\-(\d+)$#', $mItem, $arMatch)){
				$intFrom = $arMatch[1];
				$intTo = $arMatch[2];
				if($intTo == $intFrom){
					$mItemTmp[] = $intFrom;
				}
				elseif($intTo > $intFrom){
					for($i = $intFrom; $i <= $intTo; $i++){
						$mItemTmp[] = $i;
					}
				}
				elseif($intTo < $intFrom){
					for($i = $intFrom; $i >= $intTo; $i--){
						$mItemTmp[] = $i;
					}
				}
			}
			else{
				if(is_numeric($mItem)){
					$mItem = IntVal($mItem);
					if($mItem > 0){
						$mItemTmp[] = $mItem;
					}
				}
			}
			return $mItemTmp;
		}, $arScheme);
		$arSchemeTmp = array();
		foreach($arScheme as $arSchemeItem){
			foreach($arSchemeItem as $intValue){
				if(!in_array($intValue, $arSchemeTmp)){
					$arSchemeTmp[] = $intValue;
				}
			}
		}
		#
		$arValueTmp = array();
		$intIndex = 0;
		foreach($arValue as $mValue){
			$arValueTmp[++$intIndex] = $mValue;
		}
		$arValue = array();
		#
		foreach($arSchemeTmp as $intIndex){
			if(isset($arValueTmp[$intIndex])){
				$arValue[] = $arValueTmp[$intIndex];
			}
		}
		#
		unset($arScheme, $arSchemeTmp, $arSchemeItem, $arValueTmp, $mItem, $intValue, $intIndex);
	}
	
}
