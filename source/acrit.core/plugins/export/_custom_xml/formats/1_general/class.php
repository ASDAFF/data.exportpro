<?
/**
 * Acrit Core: Custom XML format
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\CurrencyConverter\Base as CurrencyConverterBase,
	\Acrit\Core\Xml,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class CustomXmlGeneral extends CustomXml {
	
	CONST DATE_UPDATED = '2018-10-29';
	
	CONST ROLE_URL = 'URL';
	CONST ROLE_PICTURE = 'PICTURE';
	CONST ROLE_CURRENCY = 'CURRENCY';
	CONST ROLE_CATEGORY = 'CATEGORY';
	
	CONST DELETE_MODE_NO = '';
	CONST DELETE_MODE_SIMPLE = 'DELETE';
	CONST DELETE_MODE_ATTR = 'ATTR';

	protected static $bSubclass = true;
	
	protected $strFileExt;
	
	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}
	
	/* START OF BASE STATIC METHODS */
	
	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return parent::getCode().'_GENERAL';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Is it need to offers preprocess? (see plugin 'sorokonogka')
	 */
	public function isOffersPreprocess(){
		return $this->arProfile['PARAMS']['OFFERS_PREPROCESS'] == 'Y' ? true : false;
	}
	
	/**
	 *	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported(){
		return true;
	}
	
	/**
	 *	Are categories export?
	 */
	public function areCategoriesExport(){
		return true;
	}
	
	/**
	 *	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict(){ // static ot not?
		return false;
	}
	
	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){ // static ot not?
		return false;
	}
	
	/**
	 *	Get list of supported currencies
	 */
	public function getSupportedCurrencies(){
		return array('RUB', 'USD', 'EUR', 'UAH', 'KZT', 'BYN');
	}
	
	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = array();
		$arResult[] = array(
			'DIV' => 'xml_structure',
			'TAB' => static::getMessage('TAB_XML_STRUCTURE_NAME'),
			'TITLE' => static::getMessage('TAB_XML_STRUCTURE_TITLE'),
			'SORT' => 5,
			'FILE' => __DIR__.'/tabs/xml_structure.php',
		);
		return $arResult;
	}
	
	/**
	 *	Get custom subtabs for profile edit
	 */
	public function getAdditionalSubTabs($intProfileID, $intIBlockID){
		$arResult = array();
		$arResult[] = array(
			'DIV' => 'xml_structure_sub',
			'TAB' => static::getMessage('SUBTAB_XML_STRUCTURE_NAME'),
			'TITLE' => static::getMessage('SUBTAB_XML_STRUCTURE_TITLE'),
			'SORT' => 5,
			'FILE' => __DIR__.'/subtabs/xml_structure.php',
		);
		$arResult[] = array(
			'DIV' => 'xml_settings',
			'TAB' => static::getMessage('SUBTAB_XML_SETTINGS_NAME'),
			'TITLE' => static::getMessage('SUBTAB_XML_SETTINGS_TITLE'),
			'SORT' => 7,
			'FILE' => __DIR__.'/subtabs/xml_settings.php',
		);
		return $arResult;
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'file.xml';
	}
	
	/**
	 *	Set available extension
	 */
	protected function setAvailableExtension($strExtension){
		$this->strFileExt = $strExtension;
	}
	
	/**
	 *	Show plugin settings
	 */
	public function showSettings(){
		$this->setAvailableExtension('xml');
		return $this->showDefaultSettings();
	}
	
	/**
	 *	Show plugin default settings
	 */
	protected function showDefaultSettings(){
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;" data-role="settings-<?=static::getCode();?>">
			<tbody>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_FILE_HINT'));?>
						<label for="acrit_exp_plugin_xml_filename">
							<b><?=static::getMessage('SETTINGS_FILE');?>:</b>
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?\CAdminFileDialog::ShowScript(Array(
							'event' => 'AcritExpPluginXmlFilenameSelect',
							'arResultDest' => array('FUNCTION_NAME' => 'acrit_exp_plugin_xml_filename_select'),
							'arPath' => array(),
							'select' => 'F',
							'operation' => 'S',
							'showUploadTab' => true,
							'showAddToMenuTab' => false,
							'fileFilter' => $this->strFileExt,
							'allowAllFiles' => true,
							'saveConfig' => true,
						));?>
						<script>
						function acrit_exp_plugin_xml_filename_select(File,Path,Site){
							var FilePath = Path+'/'+File;
							FilePath = FilePath.replace(/\/\//g, '/');
							$('#acrit_exp_plugin_xml_filename').val(FilePath);
						}
						</script>
						<table class="acrit-exp-plugin-settings-fileselect">
							<tbody>
								<tr>
									<td><input type="text" name="PROFILE[PARAMS][EXPORT_FILE_NAME]" 
										id="acrit_exp_plugin_xml_filename" data-role="export-file-name"
										value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['EXPORT_FILE_NAME']);?>" size="40" 
										placeholder="<?=static::getMessage('SETTINGS_FILE_PLACEHOLDER');?>" /></td>
									<td><input type="button" value="..." onclick="AcritExpPluginXmlFilenameSelect()" /></td>
									<td>
										&nbsp;
										<?=$this->showFileOpenLink();?>
										<?if($this->arProfile['PARAMS']['COMPRESS_TO_ZIP']=='Y'):?>
											<?=$this->showFileOpenLink(Helper::changeFileExt($this->getExportFileName(), 'zip'), 'Zip');?>
										<?endif?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_ENCODING_HINT'));?>
						<label for="acrit_exp_plugin_encoding">
							<b><?=static::getMessage('SETTINGS_ENCODING');?>:</b>
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?
						$arEncodings = Helper::getAvailableEncodings();
						$arEncodings = array(
							'REFERENCE' => array_values($arEncodings),
							'REFERENCE_ID' => array_keys($arEncodings),
						);
						print SelectBoxFromArray('PROFILE[PARAMS][ENCODING]', $arEncodings,
							$this->arProfile['PARAMS']['ENCODING'], '', 'id="acrit_exp_plugin_encoding"');
						?>
					</td>
				</tr>
				<tr id="tr_ZIP">
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_ZIP_HINT'));?>
						<label for="acrit_exp_plugin_compress_to_zip">
							<?=static::getMessage('SETTINGS_ZIP');?>:
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input name="PROFILE[PARAMS][COMPRESS_TO_ZIP]" type="hidden" value="N"/>
						<input name="PROFILE[PARAMS][COMPRESS_TO_ZIP]" type="checkbox" value="Y" 
							<?if($this->arProfile['PARAMS']['COMPRESS_TO_ZIP']=='Y'):?>checked="checked"<?endif?>
						id="acrit_exp_plugin_compress_to_zip" />
					</td>
				</tr>
				<tr id="tr_DELETE_XML_IF_ZIP">
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_DELETE_XML_IF_ZIP_HINT'));?>
						<label for="acrit_exp_plugin_delete_xml_if_zip">
							<?=static::getMessage('SETTINGS_DELETE_XML_IF_ZIP');?>:
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input name="PROFILE[PARAMS][DELETE_XML_IF_ZIP]" type="hidden" value="N"/>
						<input name="PROFILE[PARAMS][DELETE_XML_IF_ZIP]" type="checkbox" value="Y" 
							<?if($this->arProfile['PARAMS']['DELETE_XML_IF_ZIP']=='Y'):?>checked="checked"<?endif?>
						id="acrit_exp_plugin_delete_xml_if_zip" />
					</td>
				</tr>
			</tbody>
		</table>
		<script>
		$('[data-role="settings-<?=static::getCode();?>"] #tr_ZIP input[type=checkbox]').change(function(){
			var row = $('[data-role="settings-<?=static::getCode();?>"] #tr_DELETE_XML_IF_ZIP');
			if($(this).is(':checked')){
				row.show();
			}
			else {
				row.hide();
			}
		}).trigger('change');
		</script>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Show settings for field
	 */
	public function showFieldSettings($strFieldCode, $strFieldType, $strFieldName, $arParams, $strPosition){
		ob_start();
		$strFile = __DIR__.'/field_settings/'.ToLower($strPosition).'.php';
		if(is_file($strFile)){
			require $strFile;
		}
		return ob_get_clean();
	}
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = array();
		#
		$bUTM = false;
		$arCatalog = Helper::getCatalogArray($intIBlockID);
		if(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID']){
			$bUTM = $this->arProfile['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['PARAMS']['XML_ADD_UTM'] == 'Y' ? true : false;
		}
		else {
			$bUTM = $this->arProfile['IBLOCKS'][$intIBlockID]['PARAMS']['XML_ADD_UTM'] == 'Y' ? true : false;
		}
		#
		$intSort = 0;
		$arFields = $this->parseFieldsFromStructure($intIBlockID);
		foreach($arFields as $strField){
			$intSort++;
			$arFieldParams = $this->arProfile['IBLOCKS'][$intIBlockID]['FIELDS'][$strField]['PARAMS'];
			if(!is_array($arFieldParams)){
				$arFieldParams = array();
			}
			$arResult[] = new Field(array(
				'CODE' => $strField,
				'DISPLAY_CODE' => $strField,
				'NAME' => strlen($arFieldParams['_CUSTOM_XML_NAME']) ? $arFieldParams['_CUSTOM_XML_NAME'] : $strField,
				'SORT' => $intSort*10,
				'REQUIRED' => $arFieldParams['_CUSTOM_XML_REQUIRED'] == 'Y' ? true : false,
				'MULTIPLE' => $arFieldParams['_CUSTOM_XML_MULTIPLE'] == 'Y' ? true : false,
				'CDATA' => $arFieldParams['_CUSTOM_XML_CDATA'] == 'Y' ? true : false,
				'PARAMS' => array(
					'HTMLSPECIALCHARS' => 'skip',
				),
			));
			if($bUTM && $arFieldParams['_CUSTOM_XML_ROLE'] == static::ROLE_URL){
				$this->addUtmFields($arResult, $intSort*10+1);
				$bUTM = false;
			}
		}
		#
		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]);
		foreach($arAdditionalFields as $arAdditionalField){
			$arDefaultValue = null;
			if(strlen($arAdditionalField['DEFAULT_FIELD'])){
				$arDefaultValue = array();
				$arDefaultValue[] = array(
					'TYPE' => 'FIELD',
					'VALUE' => $arAdditionalField['DEFAULT_FIELD'],
				);
			}
			$arResult[] = new Field(array(
				'ID' => IntVal($arAdditionalField['ID']),
				#'CODE' => AdditionalField::getFieldCode($arAdditionalField['ID']),
				'CODE' => Helper::call($this->strModuleId, 'AdditionalField', 'getFieldCode', [$arAdditionalField['ID']]),
				'NAME' => $arAdditionalField['NAME'],
				'SORT' => 1000000,
				'UNIT' => $arAdditionalField['UNIT'],
				'DESCRIPTION' => '',
				'REQUIRED' => false,
				'MULTIPLE' => true,
				'IS_ADDITIONAL' => true,
				'DEFAULT_VALUE' => $arDefaultValue,
			));
		}
		#
		return $arResult;
	}
	
	/**
	 *	Get available fields parsing the XML structure
	 */
	public function parseFieldsFromStructure($intIBlockID){
		$arResult = array();
		#
		$arCatalog = Helper::getCatalogArray($intIBlockID);
		$strXmlItem = $this->arProfile['IBLOCKS'][$intIBlockID]['PARAMS']['CUSTOM_XML_STRUCTURE_ITEM'];
		if(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID']){
			$strXmlOffer = $this->arProfile['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['PARAMS']['CUSTOM_XML_STRUCTURE_OFFER'];
			if(strlen($strXmlOffer)){
				$strXmlItem = $strXmlOffer;
			}
		}
		#
		if(strlen($strXmlItem)){
			$arMatches = array();
			if(preg_match_all('#\#([\w]+)\##', $strXmlItem, $arMatches)){
				$arResult = $arMatches[1];
				$arReservedTags = $this->getReservedTags();
				foreach($arResult as $key => $strTag){
					if(in_array($strTag, $arReservedTags)){
						unset($arResult[$key]);
					}
				}
			}
		}
		unset($arCatalog, $strXmlItem, $strXmlOffer, $arMatches);
		$arResult = array_unique($arResult);
		return $arResult;
	}
	
	/**
	 *	Get reserved tag names
	 */
	public function getReservedTags(){
		return array(
			'SITE_URL',
			'ENCODING',
			#
			'ITEMS',
			'CATEGORIES',
			'CURRENCIES',
			'PARAMS',
			#
			'OFFERS',
		);
	}
	
	/**
	 *	Process single element
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		
		# event handlers OnCustomXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array($this, &$arProfile, &$intIBlockID, &$arElement, &$arFields));
		}
		
		# Prepare data
		$bOffer = $arElement['IS_OFFER'];
		if($bOffer) {
			$arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
			$strItemStructure = $arMainIBlockData['PARAMS']['CUSTOM_XML_STRUCTURE_OFFER'];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		else{
			$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
			$strItemStructure = $arMainIBlockData['PARAMS']['CUSTOM_XML_STRUCTURE_ITEM'];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		
		# Prepare macros
		#$strSiteURL = Helper::siteUrl($arProfile['DOMAIN'], $arProfile['IS_HTTPS']=='Y');
		
		# Offers preprocess
		if($this->isOffersPreprocess() && is_array($arFields['_OFFER_PREPROCESS'])){
			$strOffers = '';
			foreach($arFields['_OFFER_PREPROCESS'] as $arOffer){
				$strOffers .= trim($arOffer['DATA'])."\n";
			}
			$strItemStructure = str_replace('#OFFERS#', $strOffers, $strItemStructure);
		}
		
		# Prepare XML
		$arData = Xml::xmlToArray($strItemStructure);
		if(!is_array($arData)){
			$strErrorMessage = static::getMessage('INVALID_XML_'.($bOffer?'OFFER':'ITEM'));
			return array(
				'ERRORS' => array($strErrorMessage),
			);
		}

		# Prepare data array (делаем чтобы первый уровень был как остальные)
		$strRootKey = key($arData);
		$arData = array($strRootKey => array($arData[$strRootKey]));
		
		# Roles
		$strCurrency = '';
		foreach($arFields as $strField => $mValue){
			$arFieldParams = $arMainIBlockData['FIELDS'][$strField]['PARAMS'];
			switch($arFieldParams['_CUSTOM_XML_ROLE']){
				case static::ROLE_URL:
					$arFields[$strField] = Helper::execAction($mValue, function($strValue, $arParams){
						$strUrl = strlen($strValue) ? $strValue : '';
						#$arParams['CLASS']::addUtmToUrl($strUrl, $arParams['FIELDS']);
						$arParams['THIS']->addUtmToUrl($strUrl, $arParams['FIELDS']);
						return $strUrl;
					}, array('FIELDS' => &$arFields, 'CLASS' => __CLASS__, 'THIS' => $this));
					break;
				case static::ROLE_PICTURE:
					$arFields[$strField] = Helper::execAction($mValue, function($strValue, $arParams){
						return strlen($strValue) ? $strValue : '';
					}, array('FIELDS' => &$arFields, 'CLASS' => __CLASS__));
					break;
				case static::ROLE_CURRENCY:
					$strCurrency = $mValue;
					break;
				case static::ROLE_CATEGORY:
					// Если роль = "Категория", то меняем истинный раздел на подмененный (т.к. товар может относиться к нескольким разделам, и для основного его раздела выгрузка может быть недоступной).
					$arFields[$strField] = is_array($arElementSections) && !empty($arElementSections) ? reset($arElementSections) : '';
					break;
			}
		}
		
		# Create another array of parameters
		$arAdditionalParamsValues = array();
		foreach($arFields as $strField => $mValue){
			#$intParamID = AdditionalField::getIdFromCode($strField);
			$intParamID = Helper::call($this->strModuleId, 'AdditionalField', 'getIdFromCode', [$strField]);
			if($intParamID){
				$arAdditionalParamsValues[$intParamID] = $mValue;
				unset($arFields[$strField]);
			}
		}
		
		# Replace all fields except additional
		foreach($arFields as $strField => $mValue){
			$this->processElement_ProcessTags($arData, array(__CLASS__, 'processElement_ProcessTagsCallback'), array(
				'MACRO' => '#'.$strField.'#',
				'VALUE' => $mValue,
				'MULTIPLE' => true,
				'DELETE_MODE' => $arFieldParams['_CUSTOM_XML_DELETE_MODE'],
				'CURRENCY' => &$strCurrency,
			));
		}
		
		# Process additional fields
		$bParamsExport = strpos($strItemStructure, '#PARAMS#') !== false;
		if($bParamsExport){
			$this->processElement_ProcessTags($arData, function(&$arTag, &$arTags, $arParams){
				if(is_string($arTag['#']) && strpos($arTag['#'], '#PARAMS#')!==false){
					if(empty($arParams['VALUES'])){
						$arTag = null;
					}
					else {
						# Handle situatian when there are exist <param>#PARAMS#</params> and <param>#PARAM_CUSTOM#</param> on the one level
						$bTagParamsFound = false;
						$arTagsBefore = array();
						$arTagsAfter = array();
						foreach($arTags as $key => $arTag1){
							if($arTag1 === $arTag){
								$bTagParamsFound = true;
							}
							else {
								if($bTagParamsFound){
									$arTagsAfter[] = $arTag1;
								}
								else {
									$arTagsBefore[] = $arTag1;
								}
							}
						}
						$arTags = array();
						# Continue process with tags
						foreach($arParams['VALUES'] as $intParamID => $mValue){
							if(!is_array($mValue)){
								$mValue = !Helper::isEmpty($mValue) ? array($mValue) : array();
							}
							# Start attributes
							$arAttributes = array();
							# Add name attribute
							$bNameAttributeFound = false;
							#$strParamName = AdditionalField::getFieldCode($intParamID);
							$strParamName = Helper::call($this->strModuleId, 'AdditionalField', 'getFieldCode', [$intParamID]);
							$arAdditionalAttributes = &$arParams['IBLOCK_DATA']['FIELDS'][$strParamName]['PARAMS']['ADDITIONAL_ATTRIBUTES'];
							if(is_array($arAdditionalAttributes) && is_array($arAdditionalAttributes['NAME']) && $arAdditionalAttributes['VALUE']){
								foreach($arAdditionalAttributes['NAME'] as $key => $strAttrName){
									$strAttrValue = $arAdditionalAttributes['VALUE'][$key];
									if(strpos($strAttrValue, '#NAME#') !== false) {
										$arAdditionalAttributes['VALUE'][$key] = str_replace('#NAME#', $arAdditionalAttributes['NAME'], $arParams['FIELDS_DATA'][$intParamID]['NAME']);
										$bNameAttributeFound = true;
									}
								}
							}
							if(!$bNameAttributeFound){
								$arAttributes['name'] = $arParams['FIELDS_DATA'][$intParamID]['NAME'];
							}
							# Process more attributes
							if(is_array($arAdditionalAttributes) && is_array($arAdditionalAttributes['NAME']) && $arAdditionalAttributes['VALUE']){
								foreach($arAdditionalAttributes['NAME'] as $key => $strAttrName){
									$strAttrValue = $arAdditionalAttributes['VALUE'][$key];
									$arAttributes[$strAttrName] = $strAttrValue;
								}
							}
							# Add tags
							foreach($mValue as $mValueItem){
								$arTags[] = array(
									'@' => $arAttributes,
									'#' => $mValueItem,
								);
							}
						}
						#
						$arTags = array_merge($arTagsBefore, $arTags, $arTagsAfter);
					}
				}
			}, array(
				'VALUES' => &$arAdditionalParamsValues,
				'IBLOCK_DATA' => &$arMainIBlockData,
				#'FIELDS_DATA' => AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID),
				'FIELDS_DATA' => Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]),
				'MULTIPLE' => true,
				'DELETE_MODE' => $arFieldParams['_CUSTOM_XML_DELETE_MODE'],
			));
		}
		
		# Remove empty tags (if the option is enabled)
		if($arMainIBlockData['PARAMS']['XML_DELETE_MODE'] != static::DELETE_MODE_NO){
			Log::getInstance($this->strModuleId)->add(static::getMessage('DELETING_EMPTY_TAGS_START',
				array('#ELEMENT_ID#' => $intElementID)), $intProfileID, true);
			while($this->processElement_RemoveEmptyTags($arData, array(
				'DELETE_MODE' => $arMainIBlockData['PARAMS']['XML_DELETE_MODE'],
			))){};
			Log::getInstance($this->strModuleId)->add(static::getMessage('DELETING_EMPTY_TAGS_FINISH',
				array('#ELEMENT_ID#' => $intElementID)), $intProfileID, true);
		}
		
		$arData = array($strRootKey => $arData[$strRootKey][0]);
		
		# Event handlers OnCustomXmlData
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomXmlData') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array($this, &$arData, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# Build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => Xml::arrayToXml($arData),
			'CURRENCY' => $strCurrency,
			'SECTION_ID' => reset($arElementSections),
			'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
			'DATA_MORE' => array(),
		);
		
		# Event handlers OnCustomXmlResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnCustomXmlResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array($this, &$arResult, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# Ending..
		unset($intProfileID, $intElementID, $arXmlAttr, $arXmlTags);
		return $arResult;
	}
	protected function processElement_ProcessTags(&$arItems, $fnCallback, $arCallbackParams=false){
		foreach($arItems as $strTag => &$arTags){
			foreach($arTags as $intTag => &$arTag){
				if(is_array($arTag['#'])){
					call_user_func_array($fnCallback, array(&$arTag, &$arTags, $arCallbackParams));
					$this->processElement_ProcessTags($arItems[$strTag][$intTag]['#'], $fnCallback, $arCallbackParams);// ??? 
				}
				else {
					call_user_func_array($fnCallback, array(&$arTag, &$arTags, $arCallbackParams));
					if($arTag === null || $arTag === false){
						unset($arTags[$intTag]);
					}
				}
			}
			if(empty($arTags)){
				unset($arItems[$strTag]);
			}
		}
	}
	protected function processElement_ProcessTagsCallback(&$arTag, &$arTags, $arParams){
		$bMacroFoundAttr = is_array($arTag['@']) && strpos(implode(', ', $arTag['@']), $arParams['MACRO'])!==false;
		$bMacroFoundTag = is_string($arTag['#']) && strpos($arTag['#'], $arParams['MACRO'])!==false;
		if($bMacroFoundAttr || $bMacroFoundTag){
			if(!$arParams['MULTIPLE'] && is_array($arParams['VALUE'])){
				$arParams['VALUE'] = reset($arParams['VALUE']);
			}
			if(is_array($arParams['VALUE'])){
				$arTags = array();
				foreach($arParams['VALUE'] as $strValue){
					$arNewTag = $arTag;
					call_user_func_array(array(__CLASS__, 'processElement_ReplaceCallback'),
						array(&$arNewTag, $arParams['MACRO'], $strValue));
					$arTags[] = $arNewTag;
				}
			}
			else{
				call_user_func_array(array(__CLASS__, 'processElement_ReplaceCallback'),
					array(&$arTag, $arParams['MACRO'], $arParams['VALUE']));
			}
			# Delete tag if empty
			$bTagEmpty = is_array($arTag['#']) && empty($arTag['#']) || is_string($arTag['#']) && !strlen($arTag['#']);
			$bAttrEmpty = empty($arTag['@']);
			if($arParams['DELETE_MODE']==static::DELETE_MODE_SIMPLE && $bTagEmpty && $bAttrEmpty) {
				$arTag = null;
			}
			elseif($arParams['DELETE_MODE']==static::DELETE_MODE_ATTR && $bTagEmpty) {
				$arTag = null;
			}
		}
	}
	protected function processElement_ReplaceCallback(&$arTag, $strMacro, $strMacroValue){
		if(is_array($arTag['@'])){
			foreach($arTag['@'] as $key => $strValue){
				$arTag['@'][$key] = str_replace($strMacro, $strMacroValue, $strValue);
			}
		}
		if(is_string($arTag['#'])){
			$arTag['#'] = str_replace($strMacro, $strMacroValue, $arTag['#']);
		}
	}
	protected function processElement_RemoveEmptyTags(&$arItems, $arParams){ // DELETE_MODE
		$intDeleted = 0;
		$bDeleteWithAttributes = !!$arParams['DELETE_WITH_ATTR'];
		foreach($arItems as $strTag => &$arTags){
			foreach($arTags as $intTag => &$arTag){
				# Check if delete
				$bTagEmpty = is_array($arTag['#']) && empty($arTag['#']) || is_string($arTag['#']) && !strlen($arTag['#']);
				$bAttrEmpty = empty($arTag['@']);
				#
				$bDelete = false;
				if($arParams['DELETE_MODE']==static::DELETE_MODE_SIMPLE && $bTagEmpty && $bAttrEmpty) {
					$bDelete = true;
				}
				elseif($arParams['DELETE_MODE']==static::DELETE_MODE_ATTR && $bTagEmpty) {
					$bDelete = true;
				}
				# Do delete
				if($bDelete){
					$intDeleted++;
					unset($arTags[$intTag]);
				}
				elseif(is_array($arTag['#'])) {
					$intDeleted += $this->processElement_RemoveEmptyTags($arItems[$strTag][$intTag]['#'], $arParams);
				}
			}
			if(empty($arTags)){
				unset($arItems[$strTag]);
			}
		}
		return $intDeleted;
	}
	
	/**
	 *	Show results
	 */
	public function showResults($arSession){
		ob_start();
		$intTime = $arSession['TIME_FINISHED']-$arSession['TIME_START'];
		if($intTime<=0){
			$intTime = 1;
		}
		?>
		<div><?=static::getMessage('RESULT_GENERATED');?>: <?=IntVal($arSession['GENERATE']['INDEX']);?></div>
		<div><?=static::getMessage('RESULT_EXPORTED');?>: <?=IntVal($arSession['EXPORT']['INDEX']);?></div>
		<div><?=static::getMessage('RESULT_ELAPSED_TIME');?>: <?=Helper::formatElapsedTime($intTime);?></div>
		<div><?=static::getMessage('RESULT_DATETIME');?>: <?=(new \Bitrix\Main\Type\DateTime())->toString();?></div>
		<?=$this->showFileOpenLink();?>
		<?=$this->showFileOpenLink($arSession['EXPORT']['XML_FILE_URL_ZIP'], static::getMessage('RESULT_FILE_ZIP'));?>
		<?
		return Helper::showSuccess(ob_get_clean());
	}
	
	/**
	 *	Custom ajax actions
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult){
		switch($strAction){
			case 'check_xml_valid':
				$arJsonResult['Success'] = false;
				$strXml = $arParams['POST']['xml'];
				if(!strlen($strXml)){
					$arJsonResult['Message'] = static::getMessage('CHECK_XML_VALID_EMPTY');
				}
				else {
					$arData = Xml::xmlToArray($strXml);
					if(is_array($arData)){
						if(!Helper::isUtf()){
							$arData = Helper::convertEncoding($arData, 'UTF-8', 'CP1251');
						}
					}
					unset($obXml);
					if(is_array($arData)){
						$arJsonResult['Success'] = true;
						$arJsonResult['Message'] = static::getMessage('CHECK_XML_VALID_SUCCESS');
					}
					else {
						$arJsonResult['Message'] = static::getMessage('CHECK_XML_VALID_ERROR');
					}
				}
				break;
		}
	}
	
	/**
	 *	Get steps
	 */
	public function getSteps(){
		$arResult = array();
		$arResult['CHECK'] = array(
			'NAME' => static::getMessage('ACRIT_EXP_EXPORTER_STEP_CHECK'),
			'SORT' => 10,
			'FUNC' => array($this, 'stepCheck'),
		);
		$arResult['EXPORT'] = array(
			'NAME' => static::getMessage('STEP_EXPORT'),
			'SORT' => 100,
			'FUNC' => array($this, 'stepExport'),
		);
		if($this->arProfile['PARAMS']['COMPRESS_TO_ZIP'] == 'Y') {
			$arResult['ZIP'] = array(
				'NAME' => static::getMessage('STEP_ZIP'),
				'SORT' => 110,
				'FUNC' => array($this, 'stepZip'),
			);
		}
		return $arResult;
	}
	
	/**
	 *	Step: Check input params and data
	 */
	public function stepCheck($intProfileID, $arData){
		$strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		if(!strlen($strExportFilename)){
			$strErrorMessage = static::getMessage('NO_EXPORT_FILE_SPECIFIED');
			Log::getInstance($this->strModuleId)->add($strErrorMessage, $intProfileID);
			print Helper::showError($strErrorMessage);
			return Exporter::RESULT_ERROR;
		}
		# Check general XML structures
		$strStructure = $arData['PROFILE']['PARAMS']['CUSTOM_XML_STRUCTURE_GENERAL'];
		if(empty($strStructure) || Xml::xmlToArray($strStructure) === false){
			$strErrorMessage = static::getMessage('INVALID_XML_GENERAL');
			Log::getInstance($this->strModuleId)->add($strErrorMessage, $intProfileID);
			print Helper::showError($strErrorMessage);
			return Exporter::RESULT_ERROR;
		}
		$bCategories = strpos($strStructure, '#CATEGORIES#') !== false;
		$bCurrencies = strpos($strStructure, '#CURRENCIES#') !== false;
		#
		$strStructure = $arData['PROFILE']['PARAMS']['CUSTOM_XML_STRUCTURE_CATEGORY'];
		if($bCategories && Xml::xmlToArray($strStructure) === false){
			$strErrorMessage = static::getMessage('INVALID_XML_CATEGORY');
			Log::getInstance($this->strModuleId)->add($strErrorMessage, $intProfileID);
			print Helper::showError($strErrorMessage);
			return Exporter::RESULT_ERROR;
		}
		$strStructure = $arData['PROFILE']['PARAMS']['CUSTOM_XML_STRUCTURE_CURRENCY'];
		if($bCurrencies && Xml::xmlToArray($strStructure) === false){
			$strErrorMessage = static::getMessage('INVALID_XML_CURRENCY');
			Log::getInstance($this->strModuleId)->add($strErrorMessage, $intProfileID);
			print Helper::showError($strErrorMessage);
			return Exporter::RESULT_ERROR;
		}
		# Check elements XML structures
		foreach($arData['PROFILE']['IBLOCKS'] as $intIBlockID => $arIBlock){
			$arCatalog = Helper::getCatalogArray($intIBlockID);
			if(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID']){
				continue;
			}
			$bHasOffersIBlock = is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID'];
			if(!$bHasOffersIBlock){
				$arIBlock['PARAMS']['OFFERS_MODE'] = 'none';
			}
			#
			if($arIBlock['PARAMS']['OFFERS_MODE'] != 'offers') {
				$strStructure = $arIBlock['PARAMS']['CUSTOM_XML_STRUCTURE_ITEM'];
				if(empty($strStructure) || Xml::xmlToArray($strStructure) === false) {
					$strErrorMessage = static::getMessage('INVALID_XML_ITEM', array('#IBLOCK_ID#' => $intIBlockID));
					Log::getInstance($this->strModuleId)->add($strErrorMessage, $intProfileID);
					print Helper::showError($strErrorMessage);
					return Exporter::RESULT_ERROR;
				}
			}
			#
			if($arIBlock['PARAMS']['OFFERS_MODE'] != 'none'){
				$strStructure = $arIBlock['PARAMS']['CUSTOM_XML_STRUCTURE_OFFER'];
				if(empty($strStructure) || Xml::xmlToArray($strStructure) === false) {
					$strErrorMessage = static::getMessage('INVALID_XML_OFFER', array('#IBLOCK_ID#' => $intIBlockID));
					Log::getInstance($this->strModuleId)->add($strErrorMessage.var_export($arIBlock['PARAMS'],1), $intProfileID);
					print Helper::showError($strErrorMessage);
					return Exporter::RESULT_ERROR;
				}
			}
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: Export
	 */
	public function stepExport($intProfileID, $arData){
		require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');
		#
		$arSession = &$arData['SESSION']['EXPORT'];
		$bIsCron = $arData['IS_CRON'];
		#
		$strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		#
		if(!isset($arSession['XML_FILE'])){
			#$strTmpDir = Profile::getTmpDir($intProfileID);
			$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
			$strTmpFile = pathinfo($strExportFilename, PATHINFO_BASENAME).'.tmp';
			$arSession['XML_FILE_URL'] = $strExportFilename;
			$arSession['XML_FILE'] = $_SERVER['DOCUMENT_ROOT'].$strExportFilename;
			$arSession['XML_FILE_TMP'] = $strTmpDir.'/'.$strTmpFile;
			#
			if($arData['PROFILE']['PARAMS']['COMPRESS_TO_ZIP'] == 'Y'){
				$arSession['XML_FILE_ZIP'] = Helper::changeFileExt($_SERVER['DOCUMENT_ROOT'].$strExportFilename, 'zip');
				$arSession['XML_FILE_URL_ZIP'] = Helper::changeFileExt($strExportFilename, 'zip');
			}
			if(is_file($arSession['XML_FILE_TMP'])){
				unlink($arSession['XML_FILE_TMP']);
			}
			touch($arSession['XML_FILE_TMP']);
			unset($strTmpDir, $strTmpFile);
		}
		
		#
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strDateFormat = $arData['PROFILE']['PARAMS']['FORMAT_DATETIME'];
		if(!strlen($strDateFormat) || $strDateFormat == '.datetime'){
			$strDateFormat = \CDatabase::DateFormatToPHP(FORMAT_DATETIME);
		}
		elseif($strDateFormat == '.date'){
			$strDateFormat = \CDatabase::DateFormatToPHP(FORMAT_DATE);
		}
		elseif($strDateFormat == '.other'){
			$strDateFormat = $arData['PROFILE']['PARAMS']['FORMAT_DATETIME_OTHER'];
		}
		#
		$arSession['MACROS_GENERAL'] = array(
			'ENCODING' => $arData['PROFILE']['PARAMS']['ENCODING'],
			'SITE_URL' => Helper::siteUrl($arData['PROFILE']['DOMAIN'], $arData['PROFILE']['IS_HTTPS']=='Y'),
			'DATETIME' => date($strDateFormat),
		);
		
		# Prepare
		$strStructure = $arData['PROFILE']['PARAMS']['CUSTOM_XML_STRUCTURE_GENERAL'];
		$arSession['ITEMS_ID'] = '#ITEMS_'.MD5(rand().uniqid()).'#';
		$strStructure = str_replace('#ITEMS#', $arSession['ITEMS_ID'], $strStructure);
		
		# Replace general macros
		foreach($arSession['MACROS_GENERAL'] as $strMacroName => $strMacroValue){
			$strStructure = str_replace('#'.$strMacroName.'#', $strMacroValue, $strStructure);
		}
		
		# Currencies
		if(strpos($strStructure, '#CURRENCIES#') !== false) {
			$strCurrenciesXml = $this->stepExport_getCurrenciesXml($intProfileID, $arData);
			$strStructure = str_replace('#CURRENCIES#', $strCurrenciesXml, $strStructure);
		}
		
		# Categories
		if(strpos($strStructure, '#CATEGORIES#') !== false) {
			$strCategories = $this->stepExport_getCategoriesXml($intProfileID, $arData);
			$strStructure = str_replace('#CATEGORIES#', $strCategories, $strStructure);
		}
		
		# Before items
		$arSession['ITEMS_ID_POS_1'] = strpos($strStructure, $arSession['ITEMS_ID']);
		if($arSession['ITEMS_ID_POS_1'] !== false) {
			$arSession['ITEMS_ID_POS_2'] = $arSession['ITEMS_ID_POS_1'] + strlen($arSession['ITEMS_ID']);
			$strXml = substr($strStructure, 0, $arSession['ITEMS_ID_POS_1']);
			$arSession['ITEMS_OFFSET'] = 0;
			if(preg_match('#([\t]*?)$#', $strXml, $arMatch)){
				$arSession['ITEMS_OFFSET'] = strlen($arMatch[1]);
			}
			$strXml = trim($strXml, "\t");
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
		}
		
		# Export items
		if($arSession['ITEMS_ID_POS_1'] !== false) {
			$this->stepExport_writeXmlOffers($intProfileID, $arData);
		}
		
		# After items
		if($arSession['ITEMS_ID_POS_1'] !== false) {
			$strXml = substr($strStructure, $arSession['ITEMS_ID_POS_2']);
			$strXml = trim($strXml, "\r\n");
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
		}
		
		# Save file
		if(is_file($arSession['XML_FILE'])){
			unlink($arSession['XML_FILE']);
		}
		if(!Helper::createDirectoriesForFile($arSession['XML_FILE'])){
			$strErrorMessage = Loc::getMessage('ACRIT_EXP_ERROR_CREATE_DIRECORY', array(
				'#DIR#' => Helper::getDirectoryForFile($arSession['XML_FILE']),
			));
			Log::getInstance($this->strModuleId)->add($strErrorMessage);
			print Helper::showError($strErrorMessage);
			return Exporter::RESULT_ERROR;
		}
		if(is_file($arSession['XML_FILE'])){
			@unlink($arSession['XML_FILE']);
		}
		if(!@rename($arSession['XML_FILE_TMP'], $arSession['XML_FILE'])){
			@unlink($arSession['XML_FILE_TMP']);
			$strErrorMessage = Loc::getMessage('ACRIT_EXP_FILE_NO_PERMISSIONS', array(
				'#FILE#' => $arSession['XML_FILE'],
			));
			Log::getInstance($this->strModuleId)->add($strErrorMessage);
			print Helper::showError($strErrorMessage);
			return Exporter::RESULT_ERROR;
		}
		$arSession['EXPORT_FILE_SIZE_XML'] = filesize($arSession['XML_FILE']);
			
		#
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: Export, write offers
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$intOffset = 0;
		while(true){
			$intLimit = 5000;
			$strSortOrder = ToUpper($arData['PROFILE']['PARAMS']['SORT_ORDER']);
			if(!in_array($strSortOrder, array('ASC', 'DESC'))){
				$strSortOrder = 'ASC';
			}
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'!TYPE' => ExportData::TYPE_DUMMY,
				),
				'order' => array(
					'SORT' => $strSortOrder,
					'ELEMENT_ID' => 'ASC',
				),
				'select' => array(
					'IBLOCK_ID',
					'ELEMENT_ID',
					'SECTION_ID',
					'TYPE',
					'DATA',
				),
				'limit' => $intLimit,
				'offset' => $intOffset * $intLimit,
			];
			#$resItems = ExportData::getList($arQuery);
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$strXml = '';
			$intCount = 0;
			while($arItem = $resItems->fetch()){
				$intCount++;
				$arItem['DATA'] = rtrim(Xml::addOffset($arItem['DATA'], $arSession['ITEMS_OFFSET']))."\n";
				$strXml .= $arItem['DATA'];
			}
			$arData['SESSION']['EXPORT']['INDEX'] += $intCount;
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
			if($intCount<$intLimit){
				break;
			}
			$intOffset++;
		}
	}
	
	/**
	 *	Substep: get currencies
	 */
	protected function stepExport_getCurrenciesXml($intProfileID, $arData){
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'!CURRENCY' => false,
			),
			'order' => array(
				'CURRENCY' => 'ASC',
			),
			'select' => array(
				'CURRENCY',
			),
			'group' => array(
				'CURRENCY',
			),
		];
		#$resItems = ExportData::getList($arQuery);
		$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
		$arCurrencies = array();
		while($arItem = $resItems->fetch()){
			$arCurrency = explode(',',$arItem['CURRENCY']);
			Helper::arrayRemoveEmptyValues($arCurrency);
			foreach($arCurrency as $strCurrency){
				$arCurrencies[$strCurrency] = array(
					'CURRENCY' => $strCurrency,
					'RATE' => 1,
				);
			}
		}
		unset($resItems, $arItem, $arCurrency);
		#
		$arCurrencyAll = Helper::getCurrencyList();
		$strBaseCurrency = $arData['PROFILE']['PARAMS']['CURRENCY']['TARGET_CURRENCY'];
		if(!in_array($strBaseCurrency, array('RUB', 'RUR', 'BYN', 'UAH', 'KZT'))){
			$strBaseCurrency = '';
		}
		if(!strlen($strBaseCurrency) || !array_key_exists($strBaseCurrency, $arCurrencyAll)){
			foreach($arCurrencyAll as $arCurrency){
				if($arCurrency['IS_BASE']){
					$strBaseCurrency = $arCurrency['CURRENCY'];
				}
			}
		}
		$arCurrencyConverter = CurrencyConverterBase::getConverterList();
		#
		$arXml = Xml::xmlToArray($arData['PROFILE']['PARAMS']['CUSTOM_XML_STRUCTURE_CURRENCY']);
		$strKey = key($arXml);
		$arXml = $arXml[$strKey];
		#
		$arCurrenciesXml = array();
		foreach($arCurrencies as $key => $arCurrency){
			if($arCurrency['CURRENCY'] == $strBaseCurrency){
				$strRate = 1;
			}
			else {
				$strRatesSource = $arData['PROFILE']['PARAMS']['CURRENCY']['RATES_SOURCE'];
				$strRate = '1.00';
				if(strlen($strRatesSource) && is_array($arCurrencyConverter[$strRatesSource])) {
					$strClass = $arCurrencyConverter[$strRatesSource]['CLASS'];
					if(class_exists($strClass)) {
						$strRate = $strClass::getFactor($arCurrency['CURRENCY'], $strBaseCurrency);
						$strRate = number_format($strRate, 2, '.', '');
					}
				}
			}
			$arReplace = array(
				'#CURRENCY#' => $arCurrency['CURRENCY'],
				'#RATE#' => $strRate,
				'#PLUS#' => '',
			);
			$arCurrencyXml = Helper::strReplaceRecursive($arXml, $arReplace);
			Helper::arrayRemoveEmptyValuesRecursive($arCurrencyXml);
			$arCurrenciesXml[] = $arCurrencyXml;
		}
		unset($arCurrencyAll, $arCurrencies, $arCurrency, $strRatesSource, $strRate, $strBaseCurrency, $arCurrencyXml);
		# To XML!
		$arCurrenciesXml = array($strKey => $arCurrenciesXml);
		$strStructureGeneral = $arData['PROFILE']['PARAMS']['CUSTOM_XML_STRUCTURE_GENERAL'];
		if(preg_match('#^([\t]+)(.*?)\#CURRENCIES\#(.*?)$#m', $strStructureGeneral, $arMatches)){
			$intOffset = strlen($arMatches[1])+1;
		}
		$strXml = Xml::arrayToXml($arCurrenciesXml, 1, false);
		$strXml = Xml::addOffset($strXml, $intOffset);
		$strXml = "\n".rtrim($strXml)."\n".str_repeat("\t", $intOffset-1);
		return $strXml;
	}
	
	/**
	 *	Substep: get categories
	 */
	protected function stepExport_getCategoriesXml($intProfileID, $arData){
		# All categories for XML
		$arCategoriesForXml = array();
		
		# Get category redefinitions all
		#$arCategoryRedefinitionsAll = CategoryRedefinition::getForProfile($intProfileID);
		$arCategoryRedefinitionsAll = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$intProfileID]);
		
		# All sections ID for export
		$arSectionsForExportAll = array();
		
		# Category default structure (with macros)
		$arXml = Xml::xmlToArray($arData['PROFILE']['PARAMS']['CUSTOM_XML_STRUCTURE_CATEGORY']);
		$strKey = key($arXml);
		$arXml = $arXml[$strKey];
		
		# Process each used IBlocks
		foreach($arData['PROFILE']['IBLOCKS'] as $intIBlockID => $arIBlockSettings){
			$bAllCategories = $arIBlockSettings['PARAMS']['XML_ALL_CATEGORIES'] == 'Y';
			# Получаем все разделы, указанные в выгрузке
			$intSectionsIBlockID = $intIBlockID;
			$strSectionsID = $arIBlockSettings['SECTIONS_ID'];
			$strSectionsMode = $arIBlockSettings['SECTIONS_MODE'];
			$arCatalog = Helper::getCatalogArray($intIBlockID);
			if(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] > 0){
				$intSectionsIBlockID = $arCatalog['PRODUCT_IBLOCK_ID'];
				$strSectionsID = $arData['PROFILE']['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['SECTIONS_ID'];
				$strSectionsMode = $arData['PROFILE']['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['SECTIONS_MODE'];
			}
			#
			//XML_ALL_CATEGORIES
			if($bAllCategories){
				$arSectionsForExport = [];
				$arSort = ['SORT' => 'ASC', 'NAME' => 'ASC'];
				$arFilter = ['IBLOCK_ID' => $intSectionsIBlockID, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y'];
				$resSections = \CIBlockSection::getList($arSort, $arFilter, false, ['ID']);
				while($arSection = $resSections->fetch()){
					$arSectionsForExport[] = $arSection['ID'];
				}
			}
			else{
				$arCatalog = Helper::getCatalogArray($intIBlockID);
				if(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID']){
					continue;
				}
				# Get used sections
				$arUsedSectionsID = array();
				$arQuery = [
					'filter' => array(
						'PROFILE_ID' => $intProfileID,
						'IBLOCK_ID' => $intIBlockID,
					),
					'order' => array(
						'SECTION_ID' => 'ASC',
					),
					'select' => array(
						'SECTION_ID',
						'ADDITIONAL_SECTIONS_ID',
					),
					'group' => array(
						'SECTION_ID',
						'ADDITIONAL_SECTIONS_ID',
					),
				];
				#$resItems = ExportData::getList($arQuery);
				$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
				while($arItem = $resItems->fetch()){
					$arItemSectionsID = array();
					if(is_numeric($arItem['SECTION_ID']) && $arItem['SECTION_ID']>0) {
						$arItemSectionsID[] = $arItem['SECTION_ID'];
					}
					/*
					if(strlen($arItem['ADDITIONAL_SECTIONS_ID'])){
						foreach(explode(',', $arItem['ADDITIONAL_SECTIONS_ID']) as $intAdditionalSectionID){
							if(is_numeric($intAdditionalSectionID) && $intAdditionalSectionID) {
								$arItemSectionsID[] = $intAdditionalSectionID;
							}
						}
					}
					*/
					foreach($arItemSectionsID as $intSectionID){
						if(!in_array($intSectionID, $arUsedSectionsID)){
							$arUsedSectionsID[] = $intSectionID;
						}
					}
				}
				#
				$arSelectedSectionsID = Exporter::getInstance($this->strModuleId)->getInvolvedSectionsID($intSectionsIBlockID, $strSectionsID, $strSectionsMode);
				# Отсеиваем: берем только те, для которых выгружены товары
				$arSectionsForExport = array_intersect($arSelectedSectionsID, $arUsedSectionsID);
				#
				unset($arSelectedSectionsID, $arUsedSectionsID);
			}
			# Добавляем их в общий список
			$arSectionsForExportAll = array_merge($arSectionsForExportAll, $arSectionsForExport);
		}
	
		if(!empty($arSectionsForExportAll)) {
			$arSectionsAll = array();
			$arSort = array(
				'ID' => 'ASC',
			);
			$arFilter = array(
				'ID' => $arSectionsForExportAll,
			);
			$arSelect = array(
				'ID',
				'NAME',
				'SECTION_PAGE_URL',
			);
			$strCategoryXml = $arData['PROFILE']['PARAMS']['CUSTOM_XML_STRUCTURE_CATEGORY'];
			if(preg_match_all('#\#([\w]+)\##', $strCategoryXml, $arMatches)){
				$arCategoryMacro = array(
					'EXTERNAL_ID' => 'EXTERNAL_ID',
					'CODE' => 'CODE',
					'PARENT_ID' => 'IBLOCK_SECTION_ID',
					'URL' => 'SECTION_PAGE_URL',
				);
				foreach($arCategoryMacro as $strFrom => $strTo){
					if(in_array($strFrom, $arMatches[1])){
						$arSelect[] = $strTo;
					}
				}
			}
			$resSections = \CIBlockSection::getList($arSort, $arFilter, false, $arSelect);
			while($arSection = $resSections->getNext(false,false)){
				$arSection['ID'] = IntVal($arSection['ID']);
				$arSectionsAll[$arSection['ID']] = array(
					'NAME' => $arSection['NAME'],
					'PARENT_ID' => IntVal($arSection['IBLOCK_SECTION_ID']),
					'EXTERNAL_ID' => $arSection['EXTERNAL_ID'],
					'CODE' => $arSection['CODE'],
					'URL' => $arSection['SECTION_PAGE_URL'],
				);
			}
			$arSectionsForExportAll = $arSectionsAll;
			unset($arSectionsAll, $resSections, $arSection, $arSort, $arFilter); // $arSelect тут не очищаем!
		}
		
		# Site domain
		$strDomain = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y');
		
		# Categories to XML array
		$arCategoriesXml = array();
		foreach($arSectionsForExportAll as $intCategoryID => $arCategory){
			# With parents
			if($arData['PROFILE']['PARAMS']['CATEGORIES_EXPORT_PARENTS']=='Y') {
				$resSectionsChain = \CIBlockSection::getNavChain(false, $intCategoryID, $arSelect);
				while($arSectionsChain = $resSectionsChain->getNext()){
					if(strlen($arCategoryRedefinitionsAll[$arSectionsChain['ID']])){
						$arSectionsChain['NAME'] = $arCategoryRedefinitionsAll[$arSectionsChain['ID']];
					}
					$arCategoryXml = $arXml;
					$arReplace = array(
						'#ID#' => $arSectionsChain['ID'],
						'#NAME#' => htmlspecialcharsbx($arSectionsChain['NAME']),
						'#CODE#' => htmlspecialcharsbx($arSectionsChain['CODE']),
						'#EXTERNAL_ID#' => htmlspecialcharsbx($arSectionsChain['EXTERNAL_ID']),
						'#URL#' => $strDomain.htmlspecialcharsbx($arSectionsChain['SECTION_PAGE_URL']),
						'#PARENT_ID#' => '',
					);
					if($arSectionsChain['IBLOCK_SECTION_ID']){
						$arReplace['#PARENT_ID#'] = $arSectionsChain['IBLOCK_SECTION_ID'];
					}
					$arCategoryXml = Helper::strReplaceRecursive($arXml, $arReplace);
					Helper::arrayRemoveEmptyValuesRecursive($arCategoryXml);
					$arCategoriesXml[$arSectionsChain['ID']] = $arCategoryXml;
				}
				unset($resSectionsChain, $arSectionsChain, $arCategoryXml);
			}
			# Without parents
			else {
				$strCategoryName = $arCategory['NAME'];
				if(strlen($arCategoryRedefinitionsAll[$intCategoryID])){
					$strCategoryName = $arCategoryRedefinitionsAll[$intCategoryID];
				}
				$arReplace = array(
					'#ID#' => $intCategoryID,
					'#NAME#' => htmlspecialcharsbx($strCategoryName),
					'#CODE#' => htmlspecialcharsbx($arCategory['CODE']),
					'#EXTERNAL_ID#' => htmlspecialcharsbx($arCategory['EXTERNAL_ID']),
					'#URL#' => $strDomain.htmlspecialcharsbx($arCategory['URL']),
					'#PARENT_ID#' => '',
				);
				if($arCategory['IBLOCK_SECTION_ID']){
					$arReplace['#PARENT_ID#'] = $arCategory['IBLOCK_SECTION_ID'];
				}
				$arCategoryXml = Helper::strReplaceRecursive($arXml, $arReplace);
				Helper::arrayRemoveEmptyValuesRecursive($arCategoryXml);
				$arCategoriesXml[] = $arCategoryXml;
			}
		}
		
		# To XML!
		$arCategoriesXml = array($strKey => $arCategoriesXml);
		$strStructureGeneral = $arData['PROFILE']['PARAMS']['CUSTOM_XML_STRUCTURE_GENERAL'];
		if(preg_match('#^([\t]+)(.*?)\#CATEGORIES\#(.*?)$#m', $strStructureGeneral, $arMatches)){
			$intOffset = strlen($arMatches[1])+1;
		}
		$strXml = Xml::arrayToXml($arCategoriesXml, 1, false);
		$strXml = Xml::addOffset($strXml, $intOffset);
		$strXml = "\n".rtrim($strXml)."\n".str_repeat("\t", $intOffset-1);
		return $strXml;
	}
	
	/**
	 *	Step: XML to ZIP
	 */
	public function stepZip($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		if($arData['PROFILE']['PARAMS']['COMPRESS_TO_ZIP']=='Y') {
			$arSession['COMPRESS_TO_ZIP'] = true;
			$arZipFiles = array(
				$arSession['XML_FILE'],
			);
			$obAchiver = \CBXArchive::GetArchive($arSession['XML_FILE_ZIP']);
			$obAchiver->SetOptions(array(
				'REMOVE_PATH' => pathinfo($arSession['XML_FILE'], PATHINFO_DIRNAME),
			));
			$strStartFile = '';
			if($arSession['ZIP_NEXT_STEP']){
				$strStartFile = $obAchiver->GetStartFile();
			}
			$intResult = $obAchiver->Pack($arZipFiles, $strStartFile);
			unset($obAchiver);
			if($arData['PROFILE']['PARAMS']['DELETE_XML_IF_ZIP']=='Y' && is_file($arSession['XML_FILE'])) {
				@unlink($arSession['XML_FILE']);
			}
			if($intResult === \IBXArchive::StatusSuccess){
				$arSession['EXPORT_FILE_SIZE_ZIP'] = filesize($arSession['XML_FILE_ZIP']);
				return Exporter::RESULT_SUCCESS;
			}
			elseif ($intResult === \IBXArchive::StatusError){
				return Exporter::RESULT_ERROR;
			}
			elseif($intResult === \IBXArchive::StatusContinue){
				$arSession['ZIP_NEXT_STEP'] = true;
				return Exporter::RESULT_CONTINUE;
			}
		}
		return Exporter::RESULT_SUCCESS;
	}

}

?>