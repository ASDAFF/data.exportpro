<?
/**
 * Base class to work with values in fields
 */

namespace Acrit\Core\Export\Field;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Highloadblock\HighloadBlockTable as HighloadBlock,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Settings\SettingsBase as Settings,
	\Acrit\Core\Export\CurrencyConverter\Base as CurrencyConverterBase;

abstract class ValueBase {
	
	const INPUTNAME_DEFAULT = 'fieldvalues';
	
	const SUBFIELD_OPERATOR = '->';
	
	const CONST_VALUES_SEARCH_PATTERN = '#{=[A-z0-9->_.]*}#i';
	
	protected $strFieldCode;
	protected $intIBlockID;
	protected $bMultiple;
	protected $strValueSuffix;
	protected $arValues;
	protected $strConditions;
	protected $strSiteID;
	protected $arParams;
	protected $obField;
	
	# Are params hidden?
	protected $bHiddenParams;
	
	# Just for preg_replace_callback
	static $arElement;
	
	# Cache
	static $bCurrencyModule;
	static $arCacheProductPrice = array();
	static $arCacheOptimalPrice = array();
	
	/**
	 *	Create
	 */
	public function __construct(){
		$this->setValueSuffix('0');
		$this->setValues(array());
		$this->setConditions('');
		$this->setMultiple(false);
	}
	
	/**
	 *	Get lang message (short wrapper for Loc::getMessage)
	 */
	public static function getMessage($strKey){
		$strCode = static::getCode();
		$strKey = 'ACRIT_EXP_FIELDVALUE_'.$strCode.'_'.$strKey;
		return Loc::getMessage($strKey);
	}
	
	/**
	 *	Display field type name
	 *	@return string
	 */
	public static function getName(){
		return '';
	}
	
	/**
	 *	Display field type code
	 *	@return string
	 */
	public static function getCode(){
		return '';
	}
	
	/**
	 *	Sort order
	 *	@return integer
	 */
	public static function getSort(){
		return 100;
	}
	
	/**
	 *	Set multiple
	 */
	public function setMultiple($bMultiple){
		$this->bMultiple = $bMultiple;
	}
	
	/**
	 *	Set field object
	 */
	public function setFieldObject($obField){
		$this->obField = $obField;
	}
	
	/**
	 *	Set field code
	 */
	public function setFieldCode($strFieldCode){
		$this->strFieldCode = $strFieldCode;
	}
	
	/**
	 *	Set iblock id
	 */
	public function setIBlockID($intIBlockID){
		$this->intIBlockID = $intIBlockID;
	}
	
	/**
	 *	Set suffix (array key)
	 */
	public function setValueSuffix($strValueSuffix){
		$this->strValueSuffix = $strValueSuffix;
	}
	
	/**
	 *	Set values
	 */
	public function setValues($arValues){
		if(is_array($arValues)) {
			$this->arValues = $arValues;
		}
	}
	
	/**
	 *	Set conditions
	 */
	public function setConditions($strConditions){
		if(strlen($strConditions)) {
			$this->strConditions = $strConditions;
		}
	}
	
	/**
	 *	Set site id (it need for GetOptimalPrice)
	 */
	public function setSiteID($strSiteID){
		if(strlen($strSiteID)) {
			$this->strSiteID = $strSiteID;
		}
	}

	/**
	 *	Show html-code for item
	 *	@return string [html]
	 */
	abstract protected function displayItem();
	
	/**
	 *	Hide params
	 */
	public function hideParams(){
		$this->bHiddenParams = true;
	}
	
	/**
	 *	Display field
	 *	@return string [html]
	 */
	public function display(){
		return $this->displayItem();
	}
	
	/**
	 *	Show field settings
	 */
	public static function showFieldSettings($obField, $strFieldCode, $strFieldName, $arParams){
		ob_start();
		$arSettings = Settings::getListForFields($obField, $arParams);
		?>
		<table class="acrit-exp-field-settings">
			<tbody>
				<?foreach($arSettings as $strGroupCode => $arGroup):?>
					<?if(count($arSettings)>1):?>
						<tr class="heading">
							<td colspan="2">
								<?=$arGroup['NAME'];?><?if(strlen($arGroup['HINT'])):?><?=Helper::showHint($arGroup['HINT']);?><?endif?>
							</td>
						</tr>
					<?endif?>
					<?foreach($arGroup['ITEMS'] as $arItem):?>
						<tr>
							<?if(!$arItem['FULL_WIDTH']):?>
								<td>
									<label for="<?=$arItem['CLASS']::getInputId();?>"><?=$arItem['NAME'];?>:</label>
								</td>
							<?endif?>
							<td<?if($arItem['FULL_WIDTH']):?> colspan="2"<?endif?>>
								<?=$arItem['CLASS']::showSettings($strFieldCode, $obField, $arParams);?>
							</td>
						</tr>
					<?endforeach?>
				<?endforeach?>
			</tbody>
		</table>
		<br/>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Show value settings
	 */
	public static function showValueSettings($obField, $strFieldCode, $strFieldName, $arParams){
		ob_start();
		$arSettings = Settings::getListForValues($obField, $arParams);
		?>
		<table class="acrit-exp-field-settings">
			<tbody>
				<?foreach($arSettings as $strGroupCode => $arGroup):?>
					<?if(count($arSettings)>1):?>
						<tr class="heading">
							<td colspan="2">
								<?=$arGroup['NAME'];?><?if(strlen($arGroup['HINT'])):?><?=Helper::showHint($arGroup['HINT']);?><?endif?>
							</td>
						</tr>
					<?endif?>
					<?foreach($arGroup['ITEMS'] as $arItem):?>
						<tr>
							<?if(!$arItem['FULL_WIDTH']):?>
								<td>
									<label for="<?=$arItem['CLASS']::getInputId();?>"><?=$arItem['NAME'];?>:</label>
								</td>
							<?endif?>
							<td<?if($arItem['FULL_WIDTH']):?> colspan="2"<?endif?>>
								<?=$arItem['CLASS']::showSettings($strFieldCode, $obField, $arParams);?>
							</td>
						</tr>
					<?endforeach?>
				<?endforeach?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Set params
	 */
	public function setParams($strParams){
		$this->arParams = unserialize($strParams);
	}
	
	/**
	 *	Process saved values!
	 */
	abstract public function processValuesForElement(array $arElement, array $arProfile);
	
	/**
	 *	Process single value
	 */
	public function processSingleValue($arValue, $arElement, $arProfile, $obField){
		$intIBlockID = $arElement['IBLOCK_ID'];
		$arProfileParams = $arProfile['PARAMS'];
		$arIBlockParams = $arProfile['IBLOCKS'][$intIBlockID]['PARAMS'];
		#
		$mResult = '';
		#
		if(!is_array($arValue['PARAMS'])){
			$arValue['PARAMS'] = array();
		}
		if($arValue['TYPE']=='FIELD') {
			$strFieldValue = $arValue['VALUE'];
			$arValueExploded = explode(static::SUBFIELD_OPERATOR, $strFieldValue);
			$strFieldValue = $arValueExploded[0];
			$strSubField = $arValueExploded[1];
			$bFieldFromOffer = strpos($strFieldValue, 'OFFER.') === 0; #Old: $bFieldFromOffer = preg_match('#^OFFER\.#', $strFieldValue);
			$bFieldFromParent = strpos($strFieldValue, 'PARENT.') === 0; #Old: $bFieldFromParent = preg_match('#^PARENT\.#', $strFieldValue);
			if($bFieldFromOffer){ #Old: $strFieldValue = preg_replace('#^(OFFER|PARENT)\.#', '', $strFieldValue);
				$strFieldValue = substr($strFieldValue, 6);
			}
			elseif($bFieldFromParent){
				$strFieldValue = substr($strFieldValue, 7);
			}
			$arFields = &$arElement;
			if($bFieldFromOffer) {
				$arFields = &$arElement['OFFER'];
			}
			elseif($bFieldFromParent){
				$arFields = &$arElement['PARENT'];
			}
			$arAvailableFields = Helper::call($this->obField->getModuleId(), 'ProfileIBlock', 'getAvailableElementFields', [$arFields['IBLOCK_ID']]);
			# check type == field
			if(array_key_exists($strFieldValue, $arAvailableFields['fields']['ITEMS'])){
				if($strFieldValue=='__IBLOCK_SECTION_CHAIN') {
					if($arFields['IBLOCK_SECTION_ID']){
						$mResult = array();
						$resSections = \CIBlockSection::getNavChain($arFields['IBLOCK_ID'], $arFields['IBLOCK_SECTION_ID'], array('ID', 'NAME'));
						while($arSection = $resSections->getNext()){
							$mResult[] = $arSection['NAME'];
						}
					}
				}
				// Add site URL to DETAIL_PAGE_URL
				elseif($strFieldValue == 'DETAIL_PAGE_URL' && (true || $arValue['_IS_CONST'])){
					$strSiteUrl = Helper::siteUrl($arProfile['DOMAIN'], $arProfile['IS_HTTPS']=='Y');
					$mResult = $strSiteUrl.$arFields[$strFieldValue];
				}
				// Add site URL to PREVIEW_PICTURE and DETAIL_PICTURE
				elseif(in_array($strFieldValue, array('PREVIEW_PICTURE', 'DETAIL_PICTURE')) && $arValue['PARAMS']['RAW']!='Y'){
					if(strlen($arFields[$strFieldValue])){
						$strSiteUrl = Helper::siteUrl($arProfile['DOMAIN'], $arProfile['IS_HTTPS']=='Y');
						$mResult = $strSiteUrl.$arFields[$strFieldValue];
					}
				}
				// Raw images ID
				elseif(in_array($strFieldValue, array('PREVIEW_PICTURE', 'DETAIL_PICTURE')) && $arValue['PARAMS']['RAW']=='Y'){
					$mResult = $arFields['~'.$strFieldValue];
				}
				// Created by name
				elseif($strFieldValue == 'CREATED_BY__NAME'){
					$mResult = $arFields['CREATED_USER_NAME'];
				}
				// Modified by name
				elseif($strFieldValue == 'MODIFIED_BY__NAME'){
					$mResult = $arFields['USER_NAME'];
				}
				// SEO
				elseif(preg_match('#^SEO_(.*?)$#', $strFieldValue, $arMatch)){
					$mResult = $arFields[$strFieldValue];
				}
				// General fieds
				else {
					$mResult = $arFields[$strFieldValue];
				}
			}
			# check type == property
			elseif(preg_match('#^PROPERTY_(.*?)$#', $strFieldValue, $arMatch)){
				$strPropCode = $arMatch[1];
				if (array_key_exists($strPropCode, $arAvailableFields['properties']['ITEMS'])){
					$arProp = $arFields['PROPERTIES'][$strPropCode];
					$mResult = $arProp['VALUE'];
					if($arProp['PROPERTY_TYPE'] == 'S' && $arProp['USER_TYPE'] == 'HTML'){
						if($arProp['~VALUE']['TYPE'] == 'HTML' && strlen($arProp['~VALUE']['TEXT'])) {
							$mResult = $arProp['~VALUE']['TEXT'];
						}
						else{
							$mResult = $arProp['VALUE']['TEXT'];
						}
					}
					if($arValue['PARAMS']['RAW']=='Y') {
						if($arProp['PROPERTY_TYPE']=='L'){ # If PROPERTY_TYPE == 'L', then RAW value is in VALUE_ENUM_ID
							$mResult = $arProp['VALUE_ENUM_ID'];
						}
					}
					else {
						if($arProp['PROPERTY_TYPE']=='E') {
							$mResult = Helper::execAction($mResult, function($intElementID, $arParams){
								if(strlen($intElementID) && is_numeric($intElementID) && $intElementID>0){
									$arFilter = array(
										'ID' => $intElementID,
									);
									if($arParams['PROPERTY']['LINK_IBLOCK_ID']>0){
										$arFilter['IBLOCK_ID'] = $arParams['PROPERTY']['LINK_IBLOCK_ID'];
									}
									$resDbElement = \CIBlockElement::GetList(array('ID'=>'ASC'),$arFilter,false,false,array('NAME'));
									if($arDbElement = $resDbElement->getNext()){
										return $arDbElement['NAME'];
									}
								}
								return '';
							}, array('PROPERTY'=>$arProp));
						}
						// PROPERTY = G
						elseif ($arProp['PROPERTY_TYPE']=='G'){
							$mResult = Helper::execAction($mResult, function($intSectionID, $arParams){
								if(strlen($intSectionID) && is_numeric($intSectionID) && $intSectionID>0){
									$arFilter = array(
										'ID' => $intSectionID,
										'CHECK_PERMISSIONS' => 'N',
									);
									if($arParams['PROPERTY']['LINK_IBLOCK_ID']>0){
										$arFilter['IBLOCK_ID'] = $arParams['PROPERTY']['LINK_IBLOCK_ID'];
									}
									$resSection = \CIBlockSection::GetList(array('ID'=>'ASC'),$arFilter,false,array('NAME'),false);
									if($arSection = $resSection->getNext()){
										return $arSection['NAME'];
									}
								}
								return '';
							}, array('PROPERTY'=>$arProp));
						}
						// PROPERTY = S:directory
						elseif ($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory'){
							$mResult = Helper::execAction($mResult, function($strItemXmlID, $arParams){
								if(strlen($strItemXmlID)){
									$strTableName = $arParams['PROPERTY']['USER_TYPE_SETTINGS']['TABLE_NAME'];
									if(strlen($strTableName) && \Bitrix\Main\Loader::IncludeModule('highloadblock')) {
										$arHLBlock = highloadblock::getList(array('filter' => array('TABLE_NAME'=>$strTableName)))->fetch();
										if($arHLBlock) {
											$obEntity = highloadblock::compileEntity($arHLBlock);
											if(is_object($obEntity)){
												$strEntityDataClass = $obEntity->getDataClass();
												if(strlen($strEntityDataClass)) {
													$intHighloadID = $arHLBlock['ID'];
													$strSubField = &$arParams['SUBFIELD'];
													$strSubField = strlen($strSubField) && (substr($strSubField, 0, 3) == 'UF_' || $strSubField == 'ID') ? $strSubField : 'UF_NAME';
													$resItem = $strEntityDataClass::getlist(array(
														'filter' => array('UF_XML_ID' => $strItemXmlID),
														'select' => array($strSubField),
														'order' => array('ID' => 'ASC'),
														'limit' => '1',
													));
													if($arItem = $resItem->fetch()) {
														$arEntityFilter = array(
															'ENTITY_ID' => 'HLBLOCK_'.$intHighloadID,
															'FIELD_NAME' => $strSubField,
														);
														$resField = \CUserTypeEntity::GetList(array('ID'=>'ASC'), $arEntityFilter);
														if($arField = $resField->getNext(false,false)){
															if($arField['USER_TYPE_ID']=='file' && is_numeric($arItem[$strSubField]) && $arItem[$strSubField]>0){
																$arFile = \CFile::getFileArray($arItem[$strSubField]);
																$strSiteUrl = Helper::siteUrl($arParams['PROFILE']['DOMAIN'], $arParams['PROFILE']['IS_HTTPS']=='Y');
																$arItem[$strSubField] = $strSiteUrl.$arFile['SRC'];
															}
														}
														unset($resField, $arField, $arEntityFilter);
														return $arItem[$strSubField];
													}
												}
											}
										}
									}
								}
								return '';
							}, array('PROPERTY'=>$arProp, 'SUBFIELD'=>$strSubField, 'PROFILE'=>$arProfile));
						}
						// PROPERTY = S:ElementXmlID
						elseif ($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='ElementXmlID'){
							$mResult = Helper::execAction($mResult, function($strItemXmlID, $arParams){
								$strSubField = &$arParams['SUBFIELD'];
								if(!$strSubField){
									return $strItemXmlID;
								}
								if(strlen($strItemXmlID)){
									$resElements = \CIBlockElement::getList(array(), array("=XML_ID"=>$strItemXmlID, "SHOW_HISTORY"=>"Y"), false, array('nTopCount'=>1), array('*'));
									if($arElement = $resElements->getNext()){
										if(in_array($strSubField, array('PREVIEW_PICTURE', 'DETAIL_PICTURE')) && is_numeric($arElement[$strSubField])){
											$arElement[$strSubField] = \CFile::getPath($arElement[$strSubField]);
											if(strlen($arElement[$strSubField])){
												$strSiteUrl = Helper::siteUrl($arParams['PROFILE']['DOMAIN'], $arParams['PROFILE']['IS_HTTPS']=='Y');
												$arElement[$strSubField] = $strSiteUrl.$arElement[$strSubField];
											}
										}
										elseif($strSubField == 'DETAIL_PAGE_URL'){
											$strSiteUrl = Helper::siteUrl($arParams['PROFILE']['DOMAIN'], $arParams['PROFILE']['IS_HTTPS']=='Y');
											$arElement[$strSubField] = $strSiteUrl.$arElement[$strSubField];
										}
										return $arElement[$strSubField];
									}
								}
								return '';
							}, array('PROPERTY'=>$arProp, 'SUBFIELD'=>$strSubField, 'PROFILE'=>$arProfile));
						}
						// PROPERTY = F
						elseif ($arProp['PROPERTY_TYPE']=='F'){
							$mResult = Helper::execAction($mResult, function($intFileID, $arParams){
								if(strlen($intFileID) && is_numeric($intFileID) && $intFileID>0) {
									$arFile = \CFile::getFileArray($intFileID);
									if(is_array($arFile)){
										if(true || $arParams['VALUE']['_IS_CONST']){
											// Add site URL to [PROPERTY_TYPE=F]
											if(strlen($arFile['SRC'])){
												$strSiteUrl = Helper::siteUrl($arParams['PROFILE']['DOMAIN'], $arParams['PROFILE']['IS_HTTPS']=='Y');
												return $strSiteUrl.$arFile['SRC'];
											}
											else {
												return '';
											}
										}
										else {
											return $arFile['SRC'];
										}
									}
								}
								return '';
							}, array('PROPERTY'=>$arProp, 'PROFILE'=>$arProfile, 'VALUE'=>$arValue));
						}
					}
				}
			}
			# check type == price
			elseif(preg_match('#^CATALOG_PRICE_(\d+)(__|)(.*?)$#', $strFieldValue, $arMatch)){
				$intPriceID = $arMatch[1];
				$strSuffix = $arMatch[3]; // WITH_DISCOUNT, WITH_DISCOUNT_CURR, DISCOUNT, DISCOUNT_CURR, PERCENT, PERCENT_CURR, CURRENCY
				if (array_key_exists($intPriceID, $arAvailableFields['prices']['ITEMS'])){
					$arPrice = $this->getProductPrice($arFields['ID'], $intPriceID);
					if(is_array($arPrice) && !empty($arPrice)){
						$strCurrency = $arPrice['CURRENCY'];
						if(strlen($arProfileParams['CURRENCY']['TARGET_CURRENCY'])){
							$strCurrency = $arProfileParams['CURRENCY']['TARGET_CURRENCY'];
						}
						if($strSuffix == 'CURRENCY'){
							$mResult = $strCurrency;
						}
						else{
							$this->convertPriceCurrency($arPrice, $arProfileParams['CURRENCY']);
							$arOptimalPrice = $this->getProductOptimalPrice($arFields['ID'], $arPrice);
							if(is_array($arOptimalPrice)){
								#$this->convertOptimalPriceCurrency($arOptimalPrice, $arProfileParams['CURRENCY']);
								switch($strSuffix){
									case 'WITH_DISCOUNT':
										$mResult = $arOptimalPrice['DISCOUNT_PRICE'];
										break;
									case 'WITH_DISCOUNT__CURR':
										$mResult = $this->formatCurrency($arOptimalPrice['DISCOUNT_PRICE'], $strCurrency);
										break;
									case 'DISCOUNT':
										$mResult = $arOptimalPrice['DISCOUNT'];
										break;
									case 'DISCOUNT__CURR':
										$mResult = $this->formatCurrency($arOptimalPrice['DISCOUNT'], $strCurrency);
										break;
									case 'PERCENT':
										$mResult = $arOptimalPrice['PERCENT'];
										break;
									case 'PERCENT__CURR':
										$mResult = $this->formatCurrency($arOptimalPrice['PERCENT'], $strCurrency);
										break;
									case 'CURR':
										$mResult = $this->formatCurrency($arOptimalPrice['BASE_PRICE'], $strCurrency);
										break;
									default:
										$mResult = $arOptimalPrice['BASE_PRICE'];
										break;
								}
							}
						}
					}
				}
			}
			# Catalog offers exists
			elseif($strFieldValue == 'CATALOG_OFFERS'){
				$arCatalog = Helper::getCatalogArray($intIBlockID);
				if($arCatalog['OFFERS_IBLOCK_ID'] && $arCatalog['OFFERS_PROPERTY_ID']){
					$arFilter = array(
						'IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK_ID'],
						'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'] => $arElement['ID'],
					);
					$intCount = \CIBlockElement::getList(array(),$arFilter,array());
					$mResult = $intCount > 0 ? Loc::getMessage('MAIN_YES') : Loc::getMessage('MAIN_NO');
				}
				unset($arCatalog, $arFilter, $intCount);
			}
			# Is store field?
			elseif(preg_match('#^CATALOG_STORE_AMOUNT_(\d+)$#', $strFieldValue, $arMatch)){
				$intStoreID = $arMatch[1];
				if(\Bitrix\Main\Loader::includeModule('catalog') && class_exists('\CCatalogStoreProduct')){
					$arFilter = array(
						'STORE_ID' => $intStoreID,
						'PRODUCT_ID' => $arElement['ID'],
					);
					$arStoreAmount = \CCatalogStoreProduct::GetList(array(),$arFilter, false,false,array('AMOUNT'))
						->getNext(false,false);
					if(is_array($arStoreAmount)){
						$mResult = FloatVal($arStoreAmount['AMOUNT']);
					}
				}
				unset($intStoreID, $arFilter, $arStoreAmount);
			}
			# Catalog vat value (20%)
			elseif(preg_match('#^CATALOG_VAT_VALUE$#', $strFieldValue, $arMatch)){
				$strVatValue = Helper::getVatValueByID(IntVal($arFields[$arMatch[0]]));
				$mResult = strlen($strVatValue) ? FloatVal($strVatValue).'%' : '';
			}
			# Catalog vat value (0.2)
			elseif(preg_match('#^CATALOG_VAT_VALUE_FLOAT$#', $strFieldValue, $arMatch)){
				$strVatValue = Helper::getVatValueByID($arFields['CATALOG_VAT_ID']);
				$mResult = strlen($strVatValue) ? round($strVatValue / 100, 2) : '0';
			}
			# Is catalog field?
			elseif(preg_match('#^CATALOG_(.*?)$#', $strFieldValue, $arMatch)){
				switch($arMatch[1]){
					case 'MEASURE_ID':
						$mResult = $arFields['CATALOG_MEASURE'];
						break;
					case 'MEASURE_UNIT':
						$arMeasures = Helper::getMeasuresList();
						$mResult = $arMeasures[$arFields['CATALOG_MEASURE']]['SYMBOL'];
						unset($arMeasures);
						break;
					case 'MEASURE_NAME':
						$arMeasures = Helper::getMeasuresList();
						$mResult = $arMeasures[$arFields['CATALOG_MEASURE']]['MEASURE_TITLE'];
						unset($arMeasures);
						break;
					default:
						$mResult = $arFields[$strFieldValue];
						break;
				}
			}
			# Is section field?
			elseif(preg_match('#^SECTION__(.*?)$#', $strFieldValue, $arMatch)){
				if(is_array($arFields['PARENT']) && empty($arFields['SECTION'])){
					$arFields['SECTION'] = &$arFields['PARENT']['SECTION'];
				}
				#
				$strField = $arMatch[1];
				# Using parents
				$bUf = !!preg_match('#^UF_.*?$#i', $strField);
				$bUfChain = $arValue['PARAMS']['UF_CHAIN'] == 'Y';
				#
				if(is_array($arFields['SECTION']) && !empty($arFields['SECTION'])) {
					# SEO
					if(preg_match('#^SEO_(.*?)$#', $strField, $arMatch)){
						$mResult = $arFields[$strFieldValue];
					}
					else{
						# UF_*, considering option UF_CHAIN
						if($bUf && $bUfChain && Helper::isEmpty($arFields['SECTION'][$strField])){
							$intParentSectionID = $arFields['SECTION'];
							while($intParentSectionID > 0){
								$arFilter = array(
									'ID' => $intParentSectionID,
									'IBLOCK_ID' => $arFields['IBLOCK_ID'],
									'CHECK_PERMISSIONS' => 'N',
								);
								$arSelect = array(
									'ID',
									'IBLOCK_SECTION_ID',
									'NAME',
									$strField, // UF_***
								);
								$resSection = \CIBlockSection::getList(array('ID'=>'ASC'), $arFilter, false, $arSelect);
								if($arSection = $resSection->getNext()){
									$intParentSectionID = $arSection['IBLOCK_SECTION_ID'];
									if(Helper::isEmpty($arFields['SECTION'][$strField]) && !Helper::isEmpty($arSection[$strField])){
										$arFields['SECTION'][$strField] = $arSection[$strField];
										break;
									}
								}
							}
						}
						# Continue
						$mResult = $arFields['SECTION'][$strField];
						if(is_string($mResult) && strlen($mResult) && in_array($strField, array('PICTURE', 'DETAIL_PICTURE'))){
							$strSiteUrl = Helper::siteUrl($arProfile['DOMAIN'], $arProfile['IS_HTTPS']=='Y');
							$mResult = $strSiteUrl.$mResult;
						}
					}
				}
			}
			# Is iblock field?
			elseif(preg_match('#^IBLOCK__(.*?)$#', $strFieldValue, $arMatch)){
				if(is_array($arFields['IBLOCK']) && !empty($arFields['IBLOCK'])) {
					$mResult = $arFields['IBLOCK'][$arMatch[1]];
				}
				if(strlen($mResult) && in_array($arMatch[1], array('PICTURE'))){
					$strSiteUrl = Helper::siteUrl($arProfile['DOMAIN'], $arProfile['IS_HTTPS']=='Y');
					$mResult = $strSiteUrl.$mResult;
				}
			}
			else {
				$mResult = $strFieldValue;
			}
			# end types
		}
		elseif($arValue['TYPE']=='CONST') {
			$mResult = $arValue['CONST'];
			# Process macros - e.g., {=fields.NAME} {=properties.ARTICUL} {=prices.BASE}
			$obThis = $this;
			$mResult = preg_replace_callback(
				static::CONST_VALUES_SEARCH_PATTERN, //'#{=[A-z0-9->_.]*}#i',
				function($arMatches) use ($obThis, $arValue, $arElement, $arProfile, $obField) {
					return $obThis->constReplaceCallback($arMatches[0], $arValue, $arElement, $arProfile, $obField);
				},
				$mResult
			);
		}
		$arValue['PARAMS'] = array_merge(array( # Мы добавляем те же поля, которые добавляются при настройке поля в админке
			'iblock_id' => $arElement['IBLOCK_ID'],
			'field_code' => $obField->getCode(),
			'field_type' => $obField->getType(),
			'field_name' => $obField->getName(),
			'value_type' => $arValue['TYPE'],
			'current_value' => $arValue['VALUE'],
		), $arValue['PARAMS']);
		Settings::applySettingsForValue($mResult, $this->obField, $arValue['PARAMS']);
		#
		return $mResult;
	}
	
	/**
	 *	Callback to preg_replace_callback (for CONST)
	 */
	public function constReplaceCallback($strMatch, $arValue, $arElement, $arProfile, $obField){
		$arMatch = explode('.', trim($strMatch, '{=}'));
		if(ToUpper($arMatch[0]) == 'OFFER') {
			$arElement = $arElement['OFFER'];
		}
		elseif(ToUpper($arMatch[0]) == 'PARENT') {
			$arElement = $arElement['PARENT'];
		}
		$arValue['TYPE'] = 'FIELD';
		$arValue['VALUE'] = end($arMatch);
		$arValue['_IS_CONST'] = true;
		return $this->processSingleValue($arValue, $arElement, $arProfile, $obField);
	}
	
	/**
	 *	Get single price for single product
	 */
	protected function getProductPrice($intProductID, $intPriceID){
		$intMaxCacheItems = 10;
		$strKey = $intProductID.'_'.$intPriceID;
		$arResult = &static::$arCacheProductPrice[$strKey];
		if(!isset($arResult)){
			$arSelect = array(
				'ID',
				'PRICE',
				'CURRENCY',
				'CATALOG_GROUP_ID',
				#'PRODUCT_ID',
				#'EXTRA_ID',
				#'QUANTITY_FROM',
				#'QUANTITY_TO',
				#'BASE',
				#'CAN_ACCESS',
				#'CAN_BUY',
				#'TIMESTAMP_X',
				#'SORT',
				#'CATALOG_GROUP_NAME',
			);
			$arFilter = array(
				'PRODUCT_ID' => $intProductID,
				'CATALOG_GROUP_ID' => $intPriceID,
			);
			$resPrice = \CPrice::GetList(array(), $arFilter, false, false, $arSelect);
			$arResult = $resPrice->getNext(false, false);
			if(!is_array($arResult)){
				$arResult = array();
			}
			if(count(static::$arCacheProductPrice) > $intMaxCacheItems)	{
				array_shift(static::$arCacheProductPrice);
			}
			unset($resPrice, $arSelect, $arFilter);
		}
		return $arResult;
	}
	
	protected function getProductOptimalPrice($intProductID, $arPrice){
		$intMaxCacheItems = 10;
		$strKey = $intProductID.'_'.$arPrice['CATALOG_GROUP_ID'];
		$arResult = &static::$arCacheOptimalPrice[$strKey];
		if(!isset($arResult)){
			\CCatalogProduct::setUsedCurrency($arPrice['CURRENCY']);
			$arResult = \CCatalogProduct::getOptimalPrice($intProductID, 1, array(), 'N', array($arPrice), $this->strSiteID);
			if(is_array($arResult)){
				$arResult = $arResult['RESULT_PRICE'];
				if(is_array($arResult) && isset($arResult['BASE_PRICE'])){
					if(is_numeric($arResult['UNROUND_BASE_PRICE'])){
						$arResult['BASE_PRICE'] = $arResult['UNROUND_BASE_PRICE'];
					}
					if(is_numeric($arResult['UNROUND_DISCOUNT_PRICE'])){
						$arResult['DISCOUNT_PRICE'] = $arResult['UNROUND_DISCOUNT_PRICE'];
					}
				}
			}
			if(count(static::$arCacheOptimalPrice) > $intMaxCacheItems)	{
				array_shift(static::$arCacheOptimalPrice);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Convert price currency before get optimal price
	 */
	protected function convertPriceCurrency(&$arPrice, $arCurrencyParams){
		if(is_array($arPrice) && strlen($arCurrencyParams['TARGET_CURRENCY'])){
			$arConverters = CurrencyConverterBase::getConverterList();
			if(is_array($arConverters) && is_array($arConverters[$arCurrencyParams['RATES_SOURCE']])) {
				$strClass = $arConverters[$arCurrencyParams['RATES_SOURCE']]['CLASS'];
				$strFrom = $arPrice['CURRENCY'];
				$strTo = $arCurrencyParams['TARGET_CURRENCY'];
				if($strFrom != $strTo){
					$arPrice['PRICE'] = $strClass::convert($arPrice['PRICE'], $strFrom, $strTo);
					$arPrice['CURRENCY'] = $strTo;
				}
			}
		}
	}
	
	/**
	 *	Convert currency
	 */
	/*
	protected function convertOptimalPriceCurrency(&$arPrice, $arCurrencyParams){
		if(is_array($arPrice) && strlen($arCurrencyParams['TARGET_CURRENCY'])){
			$arConverters = CurrencyConverterBase::getConverterList();
			if(is_array($arConverters) && is_array($arConverters[$arCurrencyParams['RATES_SOURCE']])) {
				$strClass = $arConverters[$arCurrencyParams['RATES_SOURCE']]['CLASS'];
				$strFrom = $arPrice['CURRENCY'];
				$strTo = $arCurrencyParams['TARGET_CURRENCY'];
				if($strFrom == $strTo){
					return;
				}
				$fDiscount = $arPrice['BASE_PRICE'] - $arPrice['DISCOUNT_PRICE'];
				#
				$arPrice['CURRENCY'] = $strTo;
				$arPrice['DISCOUNT_PRICE'] = $strClass::convert($arPrice['DISCOUNT_PRICE'], $strFrom, $strTo);
				$arPrice['DISCOUNT'] = $strClass::convert($fDiscount, $strFrom, $strTo);
				$arPrice['BASE_PRICE'] = $strClass::convert($arPrice['BASE_PRICE'], $strFrom, $strTo);
				#
				$arPrice['DISCOUNT_PRICE'] = number_format($arPrice['DISCOUNT_PRICE'], 2, '.', '');
				$arPrice['DISCOUNT'] = number_format($arPrice['DISCOUNT'], 2, '.', '');
				$arPrice['BASE_PRICE'] = number_format($arPrice['BASE_PRICE'], 2, '.', '');
			}
		}
	}
	*/
	
	/**
	 *	Format currency
	 *	Its very monstrously because we need to trim insignificant zeros in admin section
	 */
	protected function formatCurrency($fPrice, $strCurrency){
		$strCurrencyClass = 'CustomCurrencyLang';
		if(is_null(static::$bCurrencyModule)){
			static::$bCurrencyModule = \Bitrix\Main\Loader::includeModule('currency');
			if(static::$bCurrencyModule && !class_exists('\Acrit\Core\Export\\'.$strCurrencyClass)){
				$strClassPhp = '
					namespace Acrit\Core\Export;
					class '.$strCurrencyClass.' extends \CCurrencyLang {
						public static function isAllowUseHideZero(){
							return true;
						}
						public static function CurrencyFormat($price, $currency, $useTemplate = true){
								static $eventExists = null;

								$useTemplate = !!$useTemplate;
								if ($useTemplate)
								{
										if ($eventExists === true || $eventExists === null)
										{
												foreach (GetModuleEvents(\'currency\', \'CurrencyFormat\', true) as $arEvent)
												{
														$eventExists = true;
														$result = ExecuteModuleEventEx($arEvent, array($price, $currency));
														if ($result != \'\')
																return $result;
												}
												if ($eventExists === null)
														$eventExists = false;
										}
								}

								if (!isset($price) || $price === \'\')
										return \'\';

								$currency = \Bitrix\Currency\CurrencyManager::checkCurrencyID($currency);
								if ($currency === false)
										return \'\';

								$price = (float)$price;
								$arCurFormat = (isset(self::$arCurrencyFormat[$currency]) ? self::$arCurrencyFormat[$currency] : self::GetFormatDescription($currency));
								$intDecimals = $arCurFormat[\'DECIMALS\'];
								if (self::isAllowUseHideZero() && $arCurFormat[\'HIDE_ZERO\'] == \'Y\')
								{
										if (round($price, $arCurFormat["DECIMALS"]) == round($price, 0))
												$intDecimals = 0;
								}
								$price = number_format($price, $intDecimals, $arCurFormat[\'DEC_POINT\'], $arCurFormat[\'THOUSANDS_SEP\']);

								return (
										$useTemplate
										? self::applyTemplate($price, $arCurFormat[\'FORMAT_STRING\'])
										: $price
								);
						}
						public static function applyTemplate($value, $template){
								return preg_replace(\'/(^|[^&])#/\', \'${1}\'.$value, $template);
						}
					};
				';
				try {
					eval($strClassPhp);
				}
				catch(\Exception $obException){}
			}
		}
		
		if(static::$bCurrencyModule){
			if(class_exists('\Acrit\Core\Export\\'.$strCurrencyClass)){
				$mCallback = array('\Acrit\Core\Export\\'.$strCurrencyClass, 'CurrencyFormat');
				return call_user_func($mCallback, $fPrice, $strCurrency, true);
			}
			else{
				return \CCurrencyLang::CurrencyFormat($fPrice, $strCurrency, true);
			}
		}
		else{
			return $fPrice.' '.$strCurrency;
		}
	}
	
}

?>