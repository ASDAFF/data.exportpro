<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Loc::loadMessages(__FILE__);

/**
 * Class Filter
 * @package Acrit\Core\Export
 */

class Filter {
	
	const VALUE_SEPARATOR = '#|#'; // For filter values
	
	static $arAllLogicCache = array();
	
	protected $strModuleId;
	protected $intIBlockID;
	protected $intIBlockOffersID;
	protected $intOffersPropertyID;
	protected $strJson;
	protected $arAvailableElementFields;
	protected $arAvailableOfferFields;
	protected $strInputName;
	protected $arFilter;
	protected $bIncludeSubsections;
	
	public function __construct($strModuleId, $intIBlockID){
		$this->strModuleId = $strModuleId;
		$this->intIBlockID = $intIBlockID;
		#$this->arAvailableElementFields = ProfileIBlock::getAvailableElementFieldsPlain($intIBlockID);
		$this->arAvailableElementFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$intIBlockID]);
		$this->bIncludeSubsections = false;
		$arCatalogArray = Helper::getCatalogArray($intIBlockID);
		if(is_array($arCatalogArray) && $arCatalogArray['OFFERS_IBLOCK_ID']) {
			$this->intIBlockOffersID = $arCatalogArray['OFFERS_IBLOCK_ID'];
			$this->intOffersPropertyID = $arCatalogArray['OFFERS_PROPERTY_ID'];
			#$this->arAvailableOfferFields = ProfileIBlock::getAvailableElementFieldsPlain($arCatalogArray['OFFERS_IBLOCK_ID']);
			$this->arAvailableOfferFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$arCatalogArray['OFFERS_IBLOCK_ID']]);
		}
	}
	
	public static function addJs(){
		ob_start();
		?>
		<script>
		BX.message({
			'ACRIT_EXP_CONDITIONS_VALUE_SEPARATOR': '<?=static::VALUE_SEPARATOR;?>',
			'ACRIT_EXP_CONDITIONS_POPUP_LOADING': '<?=Loc::getMessage('ACRIT_EXP_POPUP_LOADING');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_SELECT_FIELD': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_SELECT_FIELD');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_SELECT_LOGIC': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_SELECT_LOGIC');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_SELECT_VALUE': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_SELECT_VALUE');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_SAVE': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_SAVE');?>',
			'ACRIT_EXP_CONDITIONS_POPUP_CANCEL': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_POPUP_CANCEL');?>',
			//
			'ACRIT_EXP_CONDITIONS_ADD_ITEM': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ADD_ITEM');?>',
			'ACRIT_EXP_CONDITIONS_ADD_GROUP': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ADD_GROUP');?>',
			'ACRIT_EXP_CONDITIONS_ENTITY_FIELD': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ENTITY_FIELD');?>',
			'ACRIT_EXP_CONDITIONS_ENTITY_LOGIC': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ENTITY_LOGIC');?>',
			'ACRIT_EXP_CONDITIONS_ENTITY_VALUE': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_ENTITY_VALUE');?>',
			'ACRIT_EXP_CONDITIONS_AGGREGATOR_ALL': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_AGGREGATOR_ALL');?>',
			'ACRIT_EXP_CONDITIONS_AGGREGATOR_ANY': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_AGGREGATOR_ANY');?>',
			'ACRIT_EXP_CONDITIONS_AGGREGATOR_Y': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_AGGREGATOR_Y');?>',
			'ACRIT_EXP_CONDITIONS_AGGREGATOR_N': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_AGGREGATOR_N');?>',
			'ACRIT_EXP_CONDITIONS_DELETE_ITEM': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_DELETE_ITEM');?>',
			'ACRIT_EXP_CONDITIONS_DELETE_GROUP': '<?=Loc::getMessage('ACRIT_EXP_CONDITIONS_DELETE_GROUP');?>'
		});
		var acritFilterLang = {
			addItem: BX.message('ACRIT_EXP_CONDITIONS_ADD_ITEM'),
			addGroup: BX.message('ACRIT_EXP_CONDITIONS_ADD_GROUP'),
			//
			selectField: BX.message('ACRIT_EXP_CONDITIONS_ENTITY_FIELD'),
			selectLogic: BX.message('ACRIT_EXP_CONDITIONS_ENTITY_LOGIC'),
			selectValue: BX.message('ACRIT_EXP_CONDITIONS_ENTITY_VALUE'),
			//
			aggregatorAll: BX.message('ACRIT_EXP_CONDITIONS_AGGREGATOR_ALL'),
			aggregatorAny: BX.message('ACRIT_EXP_CONDITIONS_AGGREGATOR_ANY'),
			aggregatorY: BX.message('ACRIT_EXP_CONDITIONS_AGGREGATOR_Y'),
			aggregatorN: BX.message('ACRIT_EXP_CONDITIONS_AGGREGATOR_N'),
			//
			deleteItemConfirm: BX.message('ACRIT_EXP_CONDITIONS_DELETE_ITEM'),
			deleteGroupConfirm: BX.message('ACRIT_EXP_CONDITIONS_DELETE_GROUP')
		};
		</script>
		<?
		\Bitrix\Main\Page\Asset::GetInstance()->AddString(ob_get_clean());
	}
	
	/**
	 *	Display HTML
	 */
	public function show(){
		$strFilterUniqID = 'filter_'.uniqid().time();
		?>
		<div class="acrit-filter" id="<?=$strFilterUniqID;?>" data-role="filter" data-iblock-id="<?=$this->intIBlockID;?>"></div>
		<input type="hidden" name="<?=$this->strInputName;?>" value="<?=htmlspecialcharsbx($this->strJson);?>" id="<?=$strFilterUniqID;?>_input" />
		<script>
		// Main filter
		$('#<?=$strFilterUniqID;?>').acritFilter({
			lang: acritFilterLang,
			field: $('#<?=$strFilterUniqID;?>_input'),
			callbackClickEntity: AcritExpConditionsPopupCallbackClickEntity
		});
		</script>
		<?
	}
	
	/**
	 *	Set input name
	 */
	public function setInputName($strInputName){
		$this->strInputName = $strInputName;
	}
	
	/**
	 *	Set saved JSON
	 */
	public function setJson($strJson){
		$this->strJson = $strJson;
	}
	
	/**
	 *	Parse json
	 */
	public function getJsonArray(){
		$strJson = $this->strJson;
		if(!Helper::isUtf()){
			$strJson = Helper::convertEncoding($strJson, 'CP1251', 'UTF-8');
		}
		$arJsonResult = json_decode($strJson, true);
		if(!Helper::isUtf()){
			$arJsonResult = Helper::convertEncoding($arJsonResult, 'UTF-8', 'CP1251');
		}
		return $arJsonResult;
	}
	
	/**
	 *	Set include_subsections mode for filtering
	 */
	public function setIncludeSubsections($bIncludeSubsections){
		$this->bIncludeSubsections = $bIncludeSubsections;
	}
	
	/**
	 *	
	 */
	public static function getDatetimeFilterValues($bWithTime=true){
		$arResult = [
			'days' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_DAYS'),
			'months' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_MONTHS'),
			'years' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_YEARS'),
			'hours' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_HOURS'),
			'minutes' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_MINUTES'),
			'seconds' => Helper::getMessage('ACRIT_EXP_PROFILE_VALUES_DATETIME_SECONDS'),
		];
		if(!$bWithTime){
			unset($arResult['hours'], $arResult['minutes'], $arResult['seconds']);
		}
		return $arResult;
	}
	
	/**
	 *	Parse datetime value
	 */
	public static function parseDatetimeValue($strValue, $strField, $bReturnArray=false){
		if(preg_match('#^(\d+)([a-z]+)$#', $strValue, $arMatch)){
			$strDatetime = false;
			$strValue = $arMatch[1];
			$strType = $arMatch[2];
			if(strlen($strType)){
				$arTypes = static::getDatetimeFilterValues();
				if(isset($arTypes[$strType])){
					$obDate = new \Bitrix\Main\Type\DateTime();
					$strDiff = sprintf('- %d %s', $strValue, $strType);
					$obDate->add($strDiff);
					if(Helper::isProperty($strField)){
						$strFormat = 'Y-m-d H:i:s';
					}
					else{
						$strFormat = \Bitrix\Main\Type\DateTime::convertFormatToPhp(FORMAT_DATETIME);
					}
					$strDatetime = $obDate->format($strFormat);
				}
			}
			if($bReturnArray){
				return [
					$strDatetime,
					$strValue,
					$strType,
				];
			}
			else{
				return $strDatetime;
			}
		}
		return false;
	}
	
	/**
	 *	Get logic for values
	 */
	public static function getLogicAll($strType, $strUserType=false){
		if(is_array(static::$arAllLogicCache[$strType][$strUserType]) && !empty(static::$arAllLogicCache[$strType][$strUserType])){
			return static::$arAllLogicCache[$strType][$strUserType];
		}
		
		$arResult = array();
		
		$arResult = array(
			'EQUAL' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_EQUAL'),
				'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					return static::buildFilterItem($strModuleId, array($strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
			'NOT_EQUAL' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_EQUAL'),
				'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					return static::buildFilterItem($strModuleId, array('!'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
			'ISSET' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_ISSET'),
				'HIDE_VALUE' => true,
				'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					return static::buildFilterItem($strModuleId, array('!'.$strField => false), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
			'NOT_ISSET' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_ISSET'),
				'HIDE_VALUE' => true,
				'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					return static::buildFilterItem($strModuleId, array($strField => false), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
		);
		
		switch($strType){
			case 'S':
				if($strUserType=='_Checkbox' || $strUserType=='SASDCheckbox') {
					$arResult = array(
						'CHECKED' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array($strField => 'Y'), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_CHECKED' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('!'.$strField => 'Y'), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						)
					);
				}
				elseif($strUserType=='directory') {
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				elseif($strUserType=='Date' || $strUserType=='DateTime') {
					$arResult = array_merge($arResult, array(
						'LESS' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('<'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('<='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('>'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'FOR_THE_LAST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_FOR_THE_LAST'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$strValueParsed = static::parseDatetimeValue($strValue, $strField);
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValueParsed), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_FOR_THE_LAST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_FOR_THE_LAST'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$strValueParsed = static::parseDatetimeValue($strValue, $strField);
								return static::buildFilterItem($strModuleId, array('<'.$strField => $strValueParsed), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				elseif($strUserType=='_Currency') {
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				else {
					$arResult = array_merge($arResult, array(
						'EXACT' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_EXACT'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_EXACT' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_EXACT'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('!='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'SUBSTRING' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_SUBSTRING'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('%'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_SUBSTRING' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_SUBSTRING'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('!%'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'BEGINS_WITH' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_BEGINS_WITH'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array($strField => $strValue.'%'), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_BEGINS_WITH' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_BEGINS_WITH'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('!'.$strField => $strValue.'%'), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'ENDS_WITH' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_ENDS_WITH'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array($strField => '%'.$strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_ENDS_WITH' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_ENDS_WITH'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('!'.$strField => '%'.$strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LOGIC' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LOGIC'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('?'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_LOGIC' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_LOGIC'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('!?'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LESS' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('<'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('<='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('>'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				break;
			case 'N':
				if($strUserType=='_ID_LIST') {
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				elseif($strUserType=='SASDCheckboxNum') {
					$arResult = array(
						'CHECKED' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array($strField => 1), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_CHECKED' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array($strField => 2), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						)
					);
				}
				else {
					$arResult = array_merge($arResult, array(
						'LESS' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('<'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('<='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('>'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return static::buildFilterItem($strModuleId, array('>='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				if($strUserType == '_SectionId'){
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				break;
			case 'L':
				$arResult = array_merge($arResult, array(
					'IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
					'NOT_IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
				));
				break;
			case 'E':
				$arResult = array_merge($arResult, array(
					'IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
					'NOT_IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
				));
				break;
			case 'G':
				$arResult = array_merge($arResult, array(
					'IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
					'NOT_IN_LIST' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_NOT_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return static::buildFilterItem($strModuleId, array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
				));
				break;
			case 'F':
				$arExclude = array('ISSET','NOT_ISSET');
				foreach($arResult as $key => $arItem){
					if(!in_array($key, $arExclude)){
						unset($arResult[$key]);
					}
				}
				break;
			case 'X':
				switch($strUserType){
					case '_OffersFlag':
						$arResult = array(
							'X_WITH_OFFERS' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_X_WITH_OFFERS'),
								'MULTIPLE' => true,
								'HIDE_VALUE' => true,
								'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false){
									if(!$bIsOffers && $intIBlockOffersID){
										return static::buildFilterItem($strModuleId, array(), true, $intIBlockOffersID, $intOffersPropertyID, false);
									}
								},
							),
							'X_WITHOUT_OFFERS' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_PROFILE_VALUES_LOGIC_X_WITHOUT_OFFERS'),
								'MULTIPLE' => true,
								'HIDE_VALUE' => true,
								'CALLBACK' => function($strModuleId, $strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false){
									if(!$bIsOffers && $intIBlockOffersID){
										return static::buildFilterItem($strModuleId, array(), true, $intIBlockOffersID, $intOffersPropertyID, true);
									}
								},
							),
						);
						break;
				}
				break;
		}
		
		static::$arAllLogicCache[$strType][$strUserType] = $arResult;
		return $arResult;
	}
	
	/**
	 *	Get logic item
	 */
	public static function getLogicItem($strType, $strUserType, $strLogic){
		$arLogicAll = static::getLogicAll($strType, $strUserType);
		return $arLogicAll[$strLogic];
	}
	
	/**
	 *	Build PHP filter
	 */
	public function buildFilter(){
		$this->arFilter = array(
			'IBLOCK_ID' => $this->intIBlockID,
		);
		$arJson = $this->getJsonArray();
		$this->appendFilter($arJson, $this->arFilter);
		if($this->bIncludeSubsections){
			$this->arFilter = static::addFilterForSubsections($this->arFilter);
		}
		// Event handler
		foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBuildFilter') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$this->arFilter, $this));
		}
		return $this->arFilter;
	}
	
	/**
	 *	Transform filter for filtering also in subsections
	 */
	protected static function addFilterForSubsections($arFilter){
		$arResult = array();
		if(is_array($arFilter)){
			foreach($arFilter as $strKey => $mFilterItem){
				$strKeyCode = ltrim($strKey, '<=>!?%');
				if(is_array($mFilterItem)){
					$arResult[$strKey] = static::addFilterForSubsections($mFilterItem);
				}
				elseif($strKeyCode == 'IBLOCK_SECTION_ID'){
					$strOperation = substr($strKey, 0, strlen($strKey) - strlen($strKeyCode));
					$arResult[$strOperation.'SECTION_ID'] = $mFilterItem; // SECTION_ID in filter is right unlink IBLOCK_SECTION_ID
					$arResult['INCLUDE_SUBSECTIONS'] = 'Y';
				}
				else {
					$arResult[$strKey] = $mFilterItem;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Append filter (this function will work recursively)
	 */
	protected function appendFilter($arJsonItems, &$arFilter){
		if(is_array($arJsonItems)) {
			foreach($arJsonItems as $key => $arJsonItem){
				if($arJsonItem['type']=='group') {
					if(is_array($arJsonItem['items']) && !empty($arJsonItem['items'])) {
						$arSubFilter = array(
							'LOGIC' => $arJsonItem['aggregatorType']=='ANY' ? 'OR' : 'AND',
						);
						$this->appendFilter($arJsonItem['items'], $arSubFilter);
						if(!(count($arSubFilter)==1 && isset($arSubFilter['LOGIC']))){
							$arFilter[] = $arSubFilter;
						}
					}
				}
				elseif($arJsonItem['type']=='item'){
					$arFilterItem = array();
					$bIsOffers = $arJsonItem['iblockType']=='offers' ? true : false;
					$strFieldProcess = $strFieldOriginal = $arJsonItem['field']['value'];
					$this->remapFilterField($strFieldProcess);
					if($bIsOffers) {
						$arField = $this->arAvailableOfferFields[$strFieldOriginal];
					}
					else {
						$arField = $this->arAvailableElementFields[$strFieldOriginal];
					}
					if(is_array($arField)) {
						$strLogic = $arJsonItem['logic']['value'];
						$arLogic = static::getLogicItem($arField['TYPE'], $arField['USER_TYPE'], $strLogic);
						$strValue = $arJsonItem['value']['value'];
						if($arLogic['CALLBACK']) {
							$arFilterItem = call_user_func_array($arLogic['CALLBACK'], array(
								$this->strModuleId, $strFieldProcess, $strLogic, $strValue, $this->intIBlockID, $bIsOffers,
									$this->intIBlockOffersID, $this->intOffersPropertyID
							));
						}
						if(is_array($arFilterItem) && !empty($arFilterItem)) {
							$arFilter[] = $arFilterItem;
						}
					}
				}
			}
		}
	}
	
	/**
	 *	Change field code for correct use in API
	 */
	protected function remapFilterField(&$strField){
		if(preg_match('#CATALOG_PRICE_(\d+)__CURRENCY#i', $strField, $arMatch)){
			$strField = 'CATALOG_CURRENCY_'.$arMatch[1];
		}
	}
	
	/**
	 *	Build filter item (this use in each logic item)
	 */
	protected static function buildFilterItem($strModuleId, $arItem, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bNegation=false){
		$arResult = array();
		if($bIsOffers){
			$strKey = $bNegation ? '!ID' : 'ID';
			$arResult = array(
				$strKey => \CIBlockElement::SubQuery('PROPERTY_'.$intOffersPropertyID, array_merge($arItem, array(
					'IBLOCK_ID' => $intIBlockOffersID,
				))),
			);
		}
		else {
			$arResult = $arItem;
		}
		// Event handler
		foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers($strModuleId, 'OnBuildFilterItem') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arItem, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bNegation));
		}
		return $arResult;
	}
	
	/**
	 *	Get conditions json
	 */
	public static function getConditionsJson($strModuleId, $intIBlockID, $arItems, $strType='ALL'){
		if(is_array($arItems)) {
			$arAvailableFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$intIBlockID]);
			$arXmlItems = array();
			if(is_array($arItems) && isset($arItems['FIELD'])){
				$arItems = array($arItems);
			}
			foreach($arItems as $arItem){
				$strField = $arItem['FIELD'];
				$strLogic = $arItem['LOGIC'];
				$strValue = $arItem['VALUE'];
				$strTitle = $arItem['TITLE'];
				#
				$strValue = !is_null($strValue) ? $strValue : '';
				if(strlen($strValue) && !strlen($strTitle)){
					$strTitle = $strValue;
				}
				#
				$arField = $arAvailableFields[$strField];
				if(is_array($arField)){
					$arLogic = static::getLogicItem($arField['TYPE'], $arField['USER_TYPE'], $strLogic);
					if(is_array($arLogic)){
						$arXmlItems[] = array(
							'type' => 'item',
							'iblockType' => 'main',
							'field' => array(
								'name' => $arField['NAME'],
								'value' => $strField,
							),
							'logic' => array(
								'name' => $arLogic['NAME'],
								'value' => $strLogic,
								'hide' => $arLogic['HIDE_VALUE'] ? 'Y' : 'N',
							),
							'value' => array(
								'name' => $strTitle,
								'value' => $strValue,
							),
						);
					}
				}
			}
		}
		#
		$strType = in_array($strType, ['ANY', 'ALL']) ? $strType : 'ALL';
		$arFilterJson = array(
			array(
				'type' => 'group',
				'aggregatorType' => $strType,
				'items' => $arXmlItems,
			),
		);
		return Json::encode($arFilterJson);
	}
	
	/**
	 *	Just for conditions_value.php
	 */
	public static function getPropertyItems_L($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		$arFilter = array(
			'IBLOCK_ID' => $intFieldIBlockID,
			'PROPERTY_ID' => $arProperty['DATA']['ID'],
		);
		$resEnums = \CIBlockPropertyEnum::GetList(array('SORT'=>'ASC'), $arFilter);
		while($arEnum = $resEnums->getNext()){
			if(in_array($arEnum['ID'], $arValues)) {
				$arResult[$arEnum['ID']] = $arEnum['VALUE'].' ['.$arEnum['ID'].']';
			}
		}
		unset($arFilter, $resEnums, $arEnum);
		return $arResult;
	}
	public static function getPropertyItems_E($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		if(!empty($arValues) && \Bitrix\Main\Loader::includeModule('iblock')){
			$arFilter = array(
				'ID' => $arValues,
			);
			$resItems = \CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, array('ID', 'NAME'));
			while($arItem = $resItems->GetNext()){
				$arResult[IntVal($arItem['ID'])] = $arItem['~NAME'].' ['.$arItem['ID'].']';
			}
		}
		unset($resItems, $arItem, $arFilter);
		return $arResult;
	}
	public static function getPropertyItems_G($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		if(!empty($arValues) && \Bitrix\Main\Loader::includeModule('iblock')){
			$arFilter = array(
				'ID' => $arValues,
				'CHECK_PERMISSIONS' => 'N',
			);
			$resItems = \CIBlockSection::GetList(array('ID' => 'ASC'), $arFilter, false, array('ID', 'NAME'), false);
			while($arItem = $resItems->GetNext()){
				$arResult[IntVal($arItem['ID'])] = $arItem['~NAME'].' ['.$arItem['ID'].']';
			}
		}
		unset($resItems, $arItem, $arFilter);
		return $arResult;
	}
	public static function getPropertyItems_S_directory($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		$strHlTableName = $arProperty['DATA']['USER_TYPE_SETTINGS']['TABLE_NAME'];
		if(!empty($arValues) && \Bitrix\Main\Loader::includeModule('highloadblock')) {
			if(strlen($strHlTableName)){
				$arFilter = array(
					'UF_XML_ID' => $arValues,
				);
				$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME'=>$strHlTableName)))->fetch();
				$obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
				$strEntityDataClass = $obEntity->getDataClass();
				$resData = $strEntityDataClass::GetList(array(
					'filter' => $arFilter,
					'select' => array('ID', 'UF_NAME', 'UF_XML_ID'),
					'order' => array('ID' => 'ASC'),
				));
				while ($arItem = $resData->Fetch()) {
					$arResult[$arItem['UF_XML_ID']] = $arItem['UF_NAME'];
				}
			}
		}
		unset($strHlTableName, $arFilter, $arHLBlock, $obEntity, $strEntityDataClass, $resData, $arItem);
		return $arResult;
	}
	
	/**
	 *	Search sections
	 *	(because CIBlockSection does not support LOGIC OR)
	 */
	public static function searchSectionsByText($intIBlockID, $strSearch){
		$intIBlockID = IntVal($intIBlockID);
		$arWhere = array();
		if(strlen($strSearch)){
			$arWhere[] = "(BS.CODE IS NULL OR (BS.CODE LIKE '%{$strSearch}%'))";
			$arWhere[] = "(BS.NAME LIKE '%{$strSearch}%')";
			if(is_numeric($strSearch) && $strSearch > 0){
				$intID = IntVal($intID);
				$arWhere[] = "BS.ID = {$intID}";
			}
		}
		$strWhere = "(BS.IBLOCK_ID = '{$intIBlockID}')";
		if(!empty($arWhere)){
			$strWhere .= ' AND '.implode(' OR ', $arWhere);
		}
		$strSql = "
			SELECT
				DISTINCT BS.ID AS ID, BS.NAME AS NAME
			FROM
				b_iblock_section BS
			INNER JOIN
				b_iblock B ON BS.IBLOCK_ID = B.ID
			WHERE
				{$strWhere};
		";
		return $GLOBALS['DB']->query($strSql);
	}
	
}
