<?
/**
 * Class to work with fields and its values
 */

namespace Acrit\Core\Export\Field;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\ValueBase,
	\Acrit\Core\Export\Field\ValueSimple,
	\Acrit\Core\Export\ProfileIBlockTable as ProfileIBlock,
	\Acrit\Core\Export\Settings\SettingsBase as Settings;

class Field {
	
	const CONDITIONS_SEPARATOR = '{{{#SEPARATOR#}}}'; // for multicondition
	
	protected $strModuleId;
	protected $obPlugin;
	protected $arInitialParams;
	protected $strCode;
	protected $strDisplayCode;
	protected $strName;
	protected $intSort;
	protected $strInputName;
	protected $strDescription;
	protected $bPopupDescription;
	protected $intProfileID;
	protected $intIBlockID;
	protected $bRequired;
	protected $bCustomRequired;
	protected $mDefaultValue;
	protected $mDefaultValueOffers; // default value for offers iblock
	protected $mAllowedValues; // allowed values for field
	protected $bAllowedValuesCustom; // used custom allowed values logic
	protected $mAllowedValuesUseSelect; // use <select> for const values
	protected $mAllowedValuesAssociative;
	protected $bAllowedValuesList;
	protected $bAllowedValuesPopup;
	protected $bAllowedValuesFilter;
	protected $bMultiple;
	protected $strConditions;
	protected $bSupportCData;
	protected $strParams;
	protected $arParams;
	protected $strSiteID; // just for GetOptimalPrice
	protected $bIsPrice; // for currency convert
	protected $bIsCurrency; // for <currencies> in universal plugins
	protected $intMaxCount;
	protected $bSimpleEmptyMode;
	protected $bIsAdditional;
	protected $intID; // if additional field only
	protected $strPath; // Path for universal plugins (e.g., location, or location.metro)
	protected $bCategoryCustomName; // Flag if this field is for custom category name
	protected $bNormalCase; // Flag if heading must have normal Name, but not NAME
	
	protected $bIsHeader;
	
	# array of value
	protected $value;
	
	# field type
	protected $strType;
	
	# Are params hidden?
	protected $bHiddenParams;
	
	# Is profile copying? (we need to restrict delete additional fields)
	protected $bCopyProfileMode;
	
	# cache
	static $arTypesCache;
	
	/**
	 *	Create
	 *	Mandatory fields: CODE, NAME, INPUT_NAME, PROFILE_ID, IBLOCK_ID
	 */
	public function __construct($arParams){
		$this->arInitialParams = $arParams;
		#
		$this->strCode = $arParams['CODE'];
		$this->strDisplayCode = $arParams['DISPLAY_CODE'];
		$this->strName = $arParams['NAME'];
		$this->intSort = isset($arParams['SORT']) && is_numeric($arParams['SORT']) ? IntVal($arParams['SORT']) : 100;
		$this->strType = isset($arParams['DEFAULT_TYPE']) ? $arParams['DEFAULT_TYPE'] : 'FIELD';
		$this->strInputName = $arParams['INPUT_NAME'];
		$this->strDescription = strlen($arParams['DESCRIPTION']) ? $arParams['DESCRIPTION'] : null;
		$this->bPopupDescription = $arParams['POPUP_DESCRIPTION'] === true ? true : false;
		$this->bRequired = $arParams['REQUIRED'] === true ? true : false;
		$this->bCustomRequired = $arParams['CUSTOM_REQUIRED'] === true ? true : false;
		$this->mDefaultValue = isset($arParams['DEFAULT_VALUE']) ? $arParams['DEFAULT_VALUE'] : null;
		$this->mDefaultValueOffers = isset($arParams['DEFAULT_VALUE_OFFERS']) ? $arParams['DEFAULT_VALUE_OFFERS'] : null;
		$this->mAllowedValues = isset($arParams['ALLOWED_VALUES']) ? $arParams['ALLOWED_VALUES'] : null;
		$this->bAllowedValuesCustom = $arParams['ALLOWED_VALUES_CUSTOM'] === true ? true : false;
		$this->mAllowedValuesUseSelect = $arParams['ALLOWED_VALUES_USE_SELECT'] === true ? true : false;
		$this->mAllowedValuesAssociative = $arParams['ALLOWED_VALUES_ASSOCIATIVE'] === true ? true : false;
		$this->bAllowedValuesList = $arParams['ALLOWED_VALUES_LIST'] === true ? true : false;
		$this->bAllowedValuesPopup = ($arParams['POPUP_ALLOWED_VALUES'] === true || $arParams['ALLOWED_VALUES_POPUP'] === true) ? true : false;
		$this->bAllowedValuesFilter = $arParams['ALLOWED_VALUES_FILTER'] === true ? true : false;
		$this->bMultiple = $arParams['MULTIPLE'] === true ? true : false;
		$this->strConditions = strlen($arParams['CONDITIONS']) ? $arParams['CONDITIONS'] : (strlen($arParams['DEFAULT_CONDITIONS']) ? $arParams['DEFAULT_CONDITIONS'] : '');
		$this->bSupportCData = $arParams['CDATA'] ? true : false;
		$this->bIsPrice = $arParams['IS_PRICE'] ? true : false;
		$this->bIsCurrency = $arParams['IS_CURRENCY'] ? true : false;
		$this->intMaxCount = is_numeric($arParams['MAX_COUNT']) && $arParams['MAX_COUNT']>0 ? $arParams['MAX_COUNT'] : 0;
		$this->bSimpleEmptyMode = $arParams['SIMPLE_EMPTY_MODE'] ? true : false;
		$this->bIsAdditional = $arParams['IS_ADDITIONAL'] ? true : false;
		$this->intID = $arParams['IS_ADDITIONAL'] && is_numeric($arParams['ID']) && $arParams['ID']>0 ? $arParams['ID'] : 0;
		if(!empty($arParams['PARAMS'])){
			$this->arParams = is_array($arParams['PARAMS']) ? $arParams['PARAMS'] : array();
		}
		$this->bIsHeader = $arParams['IS_HEADER'] === true;
		if($this->bIsHeader && (!is_string($arParams['CODE']) || !strlen($arParams['CODE']))){
			$this->strCode = MD5(microtime().uniqid().RandString(8));
		}
		$this->strPath = isset($arParams['PATH']) ? $arParams['PATH'] : '';
		$this->bCategoryCustomName = $arParams['CATEGORY_CUSTOM_NAME'] === true ? true : false;
		$this->bNormalCase = $arParams['NORMAL_CASE'] === true ? true : false;
	}
	
	/**
	 *	Set module id
	 */
	public function setModuleId($strModuleId){
		$this->strModuleId = $strModuleId;
	}
	
	/**
	 *	Get module id
	 */
	public function getModuleId(){
		return $this->strModuleId;
	}
	
	/**
	 *	Set plugin
	 */
	public function setPlugin($obPlugin){
		$this->obPlugin = $obPlugin;
	}
	
	/**
	 *	Get module id
	 */
	public function getPlugin(){
		return $this->obPlugin;
	}
	
	/**
	 *	Get all value types
	 */
	public function getValueTypes(){
		$arResult = &static::$arTypesCache;
		#
		if(!is_array($arResult) || empty($arResult)) {
			$resHandle = opendir(__DIR__);
			while ($strFile = readdir($resHandle)) {
				if($strFile != '.' && $strFile != '..') {
					$strFullFilename = __DIR__.DIRECTORY_SEPARATOR.$strFile;
					if(ToUpper(pathinfo($strFile, PATHINFO_EXTENSION))=='PHP') {
						Loc::loadMessages($strFullFilename);
						require_once($strFullFilename);
					}
				}
			}
			closedir($resHandle);
			foreach(get_declared_classes() as $strClass){
				if(is_subclass_of($strClass, __NAMESPACE__.'\ValueBase')) {
					$strCode = $strClass::getCode();
					$arResult[$strCode] = array(
						'NAME' => $strClass::getName(),
						'SORT' => $strClass::getSort(),
						'CLASS' => $strClass,
					);
					if(end(\Acrit\Core\Export\Exporter::getInstance($this->strModuleId)->getExportModules(true)) != $this->strModuleId){
						unset($arResult['MULTICONDITION']);
					}
				}
			}
			uasort($arResult, '\Acrit\Core\Helper::sortBySort');
		}
		#
		return $arResult;
	}
	
	/**
	 *	Same as getValueTypes, but statically called
	 */
	public static function getValueTypesStatic($strModuleId){
		$obFieldTmp = new static([]);
		$obFieldTmp->setModuleId($strModuleId);
		$arValueTypes = $obFieldTmp->getValueTypes();
		unset($obFieldTmp);
		return $arValueTypes;
	}
	
	/**
	 *	Get code
	 */
	public function getCode(){
		return $this->strCode;
	}
	
	/**
	 *	Get name
	 */
	public function getName(){
		return $this->strName;
	}
	
	/**
	 *	Get sort
	 */
	public function getSort(){
		return $this->intSort;
	}
	
	/**
	 *	Get input name
	 */
	public function getInputName(){
		return $this->strInputName;
	}
	
	/**
	 *	Get description
	 */
	public function getDescription(){
		return $this->strDescription;
	}
	
	/**
	 *	Get default type
	 */
	public function getDefaultType(){
		return $this->strType;
	}
	
	/**
	 *	Get default value
	 */
	public function getDefaultValue(){
		return $this->mDefaultValue;
	}
	
	/**
	 *	Set default value
	 */
	public function setDefaultValue($arValue){
		$this->mDefaultValue = $arValue;;
	}
	
	/**
	 *	Set value
	 */
	public function setValue($arValues){
		if(is_array($arValues)) {
			$this->value = $arValues;
		}
	}
	
	/**
	 *	Get allowed values
	 */
	public function getAllowedValues(){
		return $this->mAllowedValues;
	}
	
	/**
	 *	Show select for allowed values (for const values)
	 */
	public function isAllowedValuesUseSelect(){
		return $this->mAllowedValuesUseSelect;
	}
	
	/**
	 *	is value a $key => $value
	 */
	public function isAllowedValuesAssociative(){
		return $this->mAllowedValuesAssociative;
	}
	
	/**
	 *	Set value
	 */
	public function setType($strType){
		if(strlen($strType)) {
			$this->strType = $strType;
		}
	}
	
	/**
	 *	Get type
	 */
	public function getType(){
		return $this->strType;
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
	 *	Set params
	 */
	public function setParams($arParams){
		if(is_array($arParams)) {
			$this->arParams = $arParams;
		}
	}
	
	/**
	 *	Set SiteID
	 */
	public function setSiteID($strSiteID){
		if(strlen($strSiteID)) {
			$this->strSiteID = $strSiteID;
		}
	}
	
	/**
	 *	Set profile ID
	 */
	public function setProfileID($intProfileID){
		if($intProfileID) {
			$this->intProfileID = $intProfileID;
		}
	}
	
	/**
	 *	Get profile ID
	 */
	public function getProfileID(){
		return $this->intProfileID;
	}
	
	/**
	 *	Set iblock ID
	 */
	public function setIBlockID($intIBlockID){
		if($intIBlockID) {
			$this->intIBlockID = $intIBlockID;
			# Set default value for offers
			if(is_array($this->mDefaultValueOffers)) {
				$arCatalog = Helper::getCatalogArray($intIBlockID);
				if($arCatalog['PRODUCT_IBLOCK_ID']>0){
					$this->mDefaultValue = $this->mDefaultValueOffers;
				}
			}
		}
	}
	
	/**
	 *	Get iblock id
	 */
	public function getIBlockID(){
		return $this->intIBlockID;
	}
	
	/**
	 *	Get initial params array
	 */
	public function getInitialParams(){
		return $this->arInitialParams;
	}
	
	/**
	 *	Is field required?
	 */
	public function isRequired(){
		return $this->bRequired;
	}
	
	/**
	 *	Is field custom required?
	 */
	public function isCustomRequired(){
		return $this->bCustomRequired;
	}
	
	/**
	 *	Is field multiple?
	 */
	public function isMultiple(){
		return $this->bMultiple;
	}
	
	/**
	 *	Is it price?
	 */
	public function isPrice(){
		return $this->bIsPrice;
	}
	
	/**
	 *	Is it currency?
	 */
	public function isCurrency(){
		return $this->bIsCurrency;
	}
	
	/**
	 *	Is field works in mode 'simple empty' (see Exporter::isEmpty)
	 */
	public function isSimpleEmptyMode(){
		return $this->bSimpleEmptyMode;
	}
	
	/**
	 *	Is it additional field?
	 */
	public function isAdditional(){
		return $this->bIsAdditional;
	}
	
	/**
	 *	Get path (for universal plugins)
	 */
	public function getPath(){
		return $this->strPath;
	}
	
	/**
	 *	Is this field flag for custom category name
	 */
	public function isCategoryCustomName(){
		return !!$this->bCategoryCustomName;
	}
	
	/**
	 *	Get ID (if this additional field)
	 */
	public function getID(){
		return $this->intID;
	}
	
	/**
	 *	Is support CData
	 */
	public function isSupportCData(){
		return $this->bSupportCData;
	}
	
	/**
	 *	Is it header?
	 */
	public function isHeader(){
		return $this->bIsHeader;
	}
	
	/**
	 *	Hide params
	 */
	public function hideParams(){
		$this->bHiddenParams = true;
	}
	
	/**
	 *	Set copy mode
	 */
	public function setCopyProfileMode($bFlag){
		$this->bCopyProfileMode = $bFlag;
	}

	/**
	 *	Show html-code
	 */
	public function displayField(){
		$strHtml = '';
		if(strlen($this->strType)) {
			$arFieldsTypesAll = $this->getValueTypes();
			if(is_array($arFieldsTypesAll[$this->strType])){
				$strClassName = $arFieldsTypesAll[$this->strType]['CLASS'];
				$obFieldValue = new $strClassName();
				$obFieldValue->setFieldObject($this);
				$obFieldValue->setFieldCode($this->getCode());
				$obFieldValue->setIBlockID($this->intIBlockID);
				if(is_null($this->value) && is_array($this->mDefaultValue) && !empty($this->mDefaultValue)){
					// Value is not set, set up default values from plugin
					$arDefaultValues = $this->mDefaultValue;
					if(isset($arDefaultValues['TYPE'])){
						$arDefaultValues = array($arDefaultValues);
					}
					// Get name for default fields (eg, if CATALOG_PRICE_1 => Cena 1, PROPERTY_MANUFACTURER => Proizvoditel)
					$arCatalog = Helper::getCatalogArray($this->intIBlockID);
					foreach($arDefaultValues as $intIndex => $arField){
						if($arField['TYPE']=='FIELD' && strlen($arField['VALUE'])){
							$intIBlockID = $this->intIBlockID;
							$bParent = false;
							$bOffer = false;
							if($arCatalog['PRODUCT_IBLOCK_ID'] && preg_match('#^PARENT\.(.*?)$#i', $arField['VALUE'], $arMatch)){
								$arField['VALUE'] = $arMatch[1];
								$intIBlockID = $arCatalog['PRODUCT_IBLOCK_ID'];
								$bParent = true;
							}
							elseif($arCatalog['OFFERS_IBLOCK_ID'] && preg_match('#^OFFER\.(.*?)$#i', $arField['VALUE'], $arMatch)){
								$arField['VALUE'] = $arMatch[1];
								$intIBlockID = $arCatalog['OFFERS_IBLOCK_ID'];
								$bOffer = true;
							}
							$arAvailableFields = Helper::call($this->strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$intIBlockID]);
							#
							$arAvailableField = $arAvailableFields[$arField['VALUE']];
							if(is_array($arAvailableField)){
								$arDefaultValues[$intIndex]['TITLE'] = Helper::call($this->strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arAvailableField, $bParent, $bOffer]);
							}
							else {
								if(count($arDefaultValues)>1){
									unset($arDefaultValues[$intIndex]);
									continue;
								}
								else {
									$arDefaultValues[$intIndex]['VALUE'] = '';
									$arDefaultValues[$intIndex]['TITLE'] = '';
								}
							}
						}
						if(is_array($arField['PARAMS'])){
							$arDefaultValues[$intIndex]['PARAMS'] = Helper::compileParams($arField['PARAMS']);
						}
					}
					$obFieldValue->setValues($arDefaultValues);
				}
				else {
					// Value is set and not emoty: refresh titles
					$arCatalog = Helper::getCatalogArray($this->intIBlockID);
					if(is_array($this->value) && !empty($this->value)){
						foreach($this->value as $intIndex => $arField) {
							$intIBlockID = $this->intIBlockID;
							$bParent = false;
							$bOffer = false;
							if($arCatalog['PRODUCT_IBLOCK_ID'] && preg_match('#^PARENT\.(.*?)$#i', $arField['VALUE'], $arMatch)){
								$arField['VALUE'] = $arMatch[1];
								$intIBlockID = $arCatalog['PRODUCT_IBLOCK_ID'];
								$bParent = true;
							}
							elseif($arCatalog['OFFERS_IBLOCK_ID'] && preg_match('#^OFFER\.(.*?)$#i', $arField['VALUE'], $arMatch)){
								$arField['VALUE'] = $arMatch[1];
								$intIBlockID = $arCatalog['OFFERS_IBLOCK_ID'];
								$bOffer = true;
							}
							$arAvailableFields = Helper::call($this->strModuleId, 'ProfileIBlock', 'getAvailableElementFieldsPlain', [$intIBlockID]);
							$arAvailableField = $arAvailableFields[$arField['VALUE']];
							if(is_array($arAvailableField)){
								$this->value[$intIndex]['TITLE'] = Helper::call($this->strModuleId, 'ProfileIBlock', 'displayAvailableItemName', [$arAvailableField, $bParent, $bOffer]);
							}
						}
					}
					// Value is not set (we use one clear value)
					elseif(!is_array($this->value) || empty($this->value)) {
						$this->value = array(
							array(
								'TYPE' => 'FIELD',
							)
						);
					}
					$obFieldValue->setValues($this->value);
				}
				if(strlen($this->strConditions)) {
					$obFieldValue->setConditions($this->strConditions);
				}
				if($this->bHiddenParams){
					$obFieldValue->hideParams();
				}
				$strHtml = $obFieldValue->display();
				unset($obFieldValue);
			}
			unset($arFieldsTypesAll);
		}
		return $strHtml;
	}
	
	/**
	 *	Process saved values
	 */
	public function processFieldForElement($arElement, $arProfile){
		$mResult = null;
		# Event 'OnBeforeProcessElementField'
		$bStop = false;
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBeforeProcessElementField') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$mResult, $this, &$arElement, &$arProfile, &$bStop));
		}
		if($bStop){
			return $mResult;
		}
		#
		$arFieldTypes = $this->getValueTypes();
		$strClass = $arFieldTypes[$this->strType]['CLASS'];
		if(!strlen($strClass)){
			foreach($arFieldTypes as $strType => $arFieldType){
				$strClass = $arFieldType['CLASS'];
				break;
			}
		}
		$obFieldType = new $strClass;
		$obFieldType->setIBlockID($this->intIBlockID);
		$obFieldType->setFieldObject($this);
		$obFieldType->setFieldCode($this->strCode);
		$obFieldType->setValues($this->value);
		$obFieldType->setMultiple($this->bMultiple);
		$obFieldType->setConditions($this->strConditions);
		$obFieldType->setSiteID($this->strSiteID);
		$mResult = $obFieldType->processValuesForElement($arElement, $arProfile);
		Settings::applySettingsForField($mResult, $this, $this->arParams);
		if(is_array($mResult) && $this->intMaxCount>0){
			$mResult = array_slice($mResult, 0, $this->intMaxCount);
		}
		unset($arFieldTypes, $obFieldType);
		# Event 'OnAfterProcessElementField'
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAfterProcessElementField') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$mResult, $this, &$arElement, &$arProfile));
		}
		#
		return $mResult;
	}
	
	/**
	 *	Display one field
	 */
	public function displayRow(){
		# Get all value types
		$arValueTypes = $this->getValueTypes();
		#
		$strFieldCode = $this->getCode();
		$strName = $this->getName();
		$strHint = $this->getDescription();
		$mAllowedValues = $this->getAllowedValues();
		ob_start();
		?>
		<?if($this->isHeader()):?>
			<tr class="heading acrit-exp-fields-table-heading<?if($this->bNormalCase):?> acrit-exp-fields-table-heading-normal-case<?endif?>" data-header="<?=$strFieldCode;?>">
				<td colspan="4"><?=$strName;?></td>
			</tr>
		<?else:?>
			<tr class="adm-list-table-row" data-role="field_row" data-field="<?=$strFieldCode;?>"
				data-field-id="<?=$this->getID();?>" data-name="<?=htmlspecialcharsbx($strName);?>"
				data-multiple="<?=($this->bMultiple ? 'Y' : 'N');?>"
				<?if($this->isCategoryCustomName()):?>data-category-custom-name="Y"<?endif?>>
				<td class="adm-list-table-cell acrit-exp-fields-table-field align-right">
					<?if($this->isAdditional()):?>
						<div class="acrit-exp-fields-table-field-name acrit-exp-fields-table-additional-field-name">
							<input type="text"
								value="<?=htmlspecialcharsbx($strName);?>"
								name="<?=ValueBase::INPUTNAME_DEFAULT;?>[<?=$this->intIBlockID;?>][<?=$strFieldCode;?>][name]"
								placeholder="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_ADDITIONAL_FIELD_PLACEHOLDER');?>"
							/>
							<?if(!$this->bCopyProfileMode):?>
								<a href="#" class="acrit-exp-fields-table-additional-field-delete" data-role="additional-field-delete"
									title="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_ADDITIONAL_FIELD_DELETE_TITLE');?>"
								>&times;</a>
							<?endif?>
						</div>
					<?else:?>
						<div class="acrit-exp-fields-table-field-name"><?
							if(strlen($strHint)) {
								print Helper::showHint($strHint, false, $this->bPopupDescription, $strName);
							}
							if(!Helper::isEmpty($mAllowedValues)) {
								$arAllowedValues = is_array($mAllowedValues) ? $mAllowedValues : [$mAllowedValues];
								$strPopupTitle = Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_FIELD_ALLOWED_VALUES', [
									'#FIELD#' => $strName,
								]);
								$strAllowedValuesHtml = $this->allowedValuesToHtml($arAllowedValues);
								print Helper::showHint($strAllowedValuesHtml, true, $this->bAllowedValuesPopup, $strPopupTitle,
									$this->bAllowedValuesFilter);
							}
							elseif($this->bAllowedValuesCustom){
								$strPopupTitle = Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_FIELD_ALLOWED_VALUES', [
									'#FIELD#' => $strName,
								]);
								print Helper::showHint($strContent, true, true, $strPopupTitle, $this->getCustomAllowedValuesJs());
							}
							if($this->isRequired()){
								print '<b>'.$strName.' *</b>';
							}
							elseif($this->isCustomRequired()){
								print $strName.' *';
							}
							else {
								print $strName;
							}
						?></div>
						<div class="acrit-exp-fields-table-field-code">
							<?$strDisplayCode = strlen($this->strDisplayCode) ? $this->strDisplayCode : $strFieldCode;?>
							<?if($strDisplayCode != $strName):?>
								<code><?=htmlspecialcharsbx($strDisplayCode);?></code>
							<?endif?>
						</div>
					<?endif?>
					<?if($this->bMultiple):?>
						<div class="acrit-exp-fields-table-field-is-multiple"
							title="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_FIELD_IS_MULTIPLE');?>"></div>
					<?endif?>
				</td>
				<td class="adm-list-table-cell acrit-exp-fields-table-type align-right">
					<select name="<?=ValueBase::INPUTNAME_DEFAULT;?>[<?=$this->intIBlockID;?>][<?=$strFieldCode;?>][field_type]" data-role="field-type">
						<?foreach($arValueTypes as $strValueCode => $arValueData):?>
							<option value="<?=$strValueCode;?>"<?if($strValueCode==$this->strType):?> selected="selected"<?endif?>><?=$arValueData['NAME'];?></option>
						<?endforeach?>
					</select>
				</td>
				<td class="adm-list-table-cell acrit-exp-fields-table-value align-right" data-role="field-value-cell">
					<?=$this->displayField();?>
				</td>
				<?if(!$this->bHiddenParams):?>
					<td class="adm-list-table-cell acrit-exp-fields-table-settings align-right">
						<input type="hidden" name="<?=ValueBase::INPUTNAME_DEFAULT;?>[<?=$this->intIBlockID;?>][<?=$strFieldCode;?>][field_params]" value="<?=Helper::compileParams($this->arParams);?>" data-role="field--params" />
						<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_BUTTON_FIELD_SETTINGS');?>" 
							title="<?=Loc::getMessage('ACRIT_EXP_STRUCTURE_IBLOCK_BUTTON_FIELD_SETTINGS_TITLE');?>"
							data-role="field--button-params" />
					</td>
				<?endif?>
			</tr>
		<?endif?>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function allowedValuesToHtml($arValues){
		$strHtmlResult = '';
		$arGroups = [];
		$intGroupIndex = 0;
		foreach($arValues as $key => $strItem){
			if($this->isAllowedValueItemGroup($strItem)){
				$arGroups[++$intGroupIndex] = [
					'NAME' => $strItem,
					'ITEMS' => [],
				];
			}
			else{
				$arGroups[$intGroupIndex]['ITEMS'][$key] = $strItem;
			}
		}
		return Helper::getHtmlObject(ACRIT_CORE, null, 'field_hint', 'default', [
			'GROUPS' => &$arGroups,
			'POPUP' => $this->bAllowedValuesPopup,
			'FILTER' => $this->bAllowedValuesFilter,
			'LIST' => $this->bAllowedValuesList,
		]);
	}
	
	/**
	 *	
	 */
	public function isAllowedValueItemGroup(&$strItem){
		if(substr($strItem, 0, 1) == '#' && substr($strItem, -1, 1) == '#') {
			$strItem = substr($strItem, 1, -1);
			return true;
		}
		return false;
	}
	
	/**
	 *	Get JS for popup for custom allowed values
	 */
	protected function getCustomAllowedValuesJs(){
		return "
			(function(){
				let
					data = {
						field: '{$this->getCode()}'
					};
				acritExpAjax(['plugin_ajax_action', 'allowed_values_custom'], data, function(arJsonResult){
					AcritPopupHint.SetHtml(arJsonResult.HTML);
				});
			})();
		";
	}
	
}
?>