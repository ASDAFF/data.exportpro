<?
/**
 * Acrit Core: Rozetka.com.ua plugin
 * @documentation https://rozetka.com.ua/sellerinfo/pricelist/
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\CurrencyConverter\Base as CurrencyConverterBase,
	\Acrit\Core\Log,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class RozetkaComUaGeneral extends RozetkaComUa {
	
	protected $bCategoryCustomName = true;
	
	CONST DATE_UPDATED = '2018-09-13';

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
	public function isCategoryStrict(){
		return false;
	}
	
	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){
		return false;
	}
	
	/**
	 *	Get list of supported currencies
	 */
	public function getSupportedCurrencies(){
		return array('RUB', 'RUR', 'UAH', 'BYR', 'USD', 'EUR');
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'rozetka.xml';
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
						<?=Helper::ShowHint(static::getMessage('SETTINGS_SHOP_NAME_HINT'));?>
						<b><?=static::getMessage('SETTINGS_SHOP_NAME');?>:</b>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][SHOP_NAME]" size="46"
							value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_NAME']);?>"/>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_SHOP_COMPANY_HINT'));?>
						<?=static::getMessage('SETTINGS_SHOP_COMPANY');?>:
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][SHOP_COMPANY]" size="46"
							value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_COMPANY']);?>"/>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_FILE_HINT'));?>
						<b><?=static::getMessage('SETTINGS_FILE');?>:</b>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?\CAdminFileDialog::ShowScript(array(
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
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = array();
		$arResult[] = new Field(array(
			'CODE' => 'ID',
			'DISPLAY_CODE' => 'id',
			'NAME' => static::getMessage('FIELD_ID_NAME'),
			'SORT' => 500,
			'DESCRIPTION' => static::getMessage('FIELD_ID_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'ID',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AVAILABLE',
			'DISPLAY_CODE' => 'available',
			'NAME' => static::getMessage('FIELD_AVAILABLE_NAME'),
			'SORT' => 510,
			'DESCRIPTION' => static::getMessage('FIELD_AVAILABLE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_TYPE' => 'CONDITION',
			'DEFAULT_CONDITIONS' => Filter::getConditionsJson($this->strModuleId, $intIBlockID, array(
				array(
					'FIELD' => 'CATALOG_QUANTITY',
					'LOGIC' => 'MORE',
					'VALUE' => '0',
				),
			)),
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'true',
					'SUFFIX' => 'Y',
				),
				array(
					'TYPE' => 'CONST',
					'CONST' => 'false',
					'SUFFIX' => 'N',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'STOCK_QUANTITY',
			'DISPLAY_CODE' => 'stock_quantity',
			'NAME' => static::getMessage('FIELD_STOCK_QUANTITY_NAME'),
			'SORT' => 520,
			'DESCRIPTION' => static::getMessage('FIELD_STOCK_QUANTITY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_QUANTITY',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'URL',
			'DISPLAY_CODE' => 'url',
			'NAME' => static::getMessage('FIELD_URL_NAME'),
			'SORT' => 530,
			'DESCRIPTION' => static::getMessage('FIELD_URL_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PAGE_URL',
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'escape',
			),
		));
		$this->addUtmFields($arResult, 531, 'rozetka');
		$arResult[] = new Field(array(
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'price',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'IS_PRICE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1__WITH_DISCOUNT',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE_OLD',
			'DISPLAY_CODE' => 'price_old',
			'NAME' => static::getMessage('FIELD_PRICE_OLD_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_OLD_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'IS_PRICE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE_PROMO',
			'DISPLAY_CODE' => 'price_promo',
			'NAME' => static::getMessage('FIELD_PRICE_PROMO_NAME'),
			'SORT' => 545,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_PROMO_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'IS_PRICE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CURRENCY_ID',
			'DISPLAY_CODE' => 'currencyId',
			'NAME' => static::getMessage('FIELD_CURRENCY_ID_NAME'),
			'SORT' => 550,
			'DESCRIPTION' => static::getMessage('FIELD_CURRENCY_ID_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1__CURRENCY',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PICTURE',
			'DISPLAY_CODE' => 'picture',
			'NAME' => static::getMessage('FIELD_PICTURE_NAME'),
			'SORT' => 560,
			'DESCRIPTION' => static::getMessage('FIELD_PICTURE_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
				),
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MORE_PHOTO',
					'PARAMS' => array(
						'MULTIPLE' => 'multiple',
					),
				),
			),
			'DEFAULT_VALUE_OFFERS' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
				),
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.PROPERTY_MORE_PHOTO',
					'PARAMS' => array(
						'MULTIPLE' => 'multiple',
					),
				),
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MORE_PHOTO',
					'PARAMS' => array(
						'MULTIPLE' => 'multiple',
					),
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'escape',
				'MULTIPLE' => 'multiple',
				'MAXLENGTH' => 2000,
			),
			'MAX_COUNT' => 8,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CATEGORY_NAME_CUSTOM',
			'DISPLAY_CODE' => ' ',
			'NAME' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_CATEGORY_NAME_CUSTOM_NAME'),
			'SORT' => 565,
			'DESCRIPTION' => Helper::getMessage('ACRIT_EXP_PLUGIN_FIELD_CATEGORY_NAME_CUSTOM_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'CATEGORY_CUSTOM_NAME' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'VENDOR',
			'DISPLAY_CODE' => 'vendor',
			'NAME' => static::getMessage('FIELD_VENDOR_NAME'),
			'SORT' => 570,
			'DESCRIPTION' => static::getMessage('FIELD_VENDOR_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_BRAND',
				),
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MANUFACTURER',
				),
			),
			'DEFAULT_VALUE_OFFERS' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.PROPERTY_BRAND',
				),
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.PROPERTY_MANUFACTURER',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'first',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'NAME',
			'DISPLAY_CODE' => 'name',
			'NAME' => static::getMessage('FIELD_NAME_NAME'),
			'SORT' => 580,
			'DESCRIPTION' => static::getMessage('FIELD_NAME_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'NAME',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DESCRIPTION',
			'DISPLAY_CODE' => 'description',
			'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
			'SORT' => 590,
			'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'CDATA' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_TEXT',
					'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
				),
			),
			'DEFAULT_VALUE_OFFERS' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_TEXT',
					'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
				),
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.DETAIL_TEXT',
					'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
				),
			),
			'PARAMS' => array('HTMLSPECIALCHARS' => 'cdata'),
		));
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
				'DESCRIPTION' => '',
				'REQUIRED' => false,
				'MULTIPLE' => true,
				'IS_ADDITIONAL' => true,
				'DEFAULT_VALUE' => $arDefaultValue,
			));
		}
		#
		unset($arAdditionalFields, $arAdditionalField);
		#
		return $arResult;
	}
	
	/**
	 *	Process single element
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		
		# Prepare data
		$bOffer = $arElement['IS_OFFER'];
		if($bOffer) {
			$arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		else {
			$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		
		$this->prepareSaveSections($arElementSections, $arProfile, $intIBlockID, $arElement, $arFields);
		
		# Build XML
		$arXmlTags = array();
		if(!Helper::isEmpty($arFields['URL']))
			$arXmlTags['url'] = $this->getXmlTag_Url($intProfileID, $arFields['URL'], $arFields);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['price'] = Xml::addTag($arFields['PRICE']);
		if(!Helper::isEmpty($arFields['PRICE_OLD']) && $arFields['PRICE_OLD'] != $arFields['PRICE'])
			$arXmlTags['price_old'] = Xml::addTag($arFields['PRICE_OLD']);
		if(!Helper::isEmpty($arFields['PRICE_PROMO']) && $arFields['PRICE_PROMO'] != $arFields['PRICE'])
			$arXmlTags['price_promo'] = Xml::addTag($arFields['PRICE_PROMO']);
		if(!Helper::isEmpty($arFields['CURRENCY_ID']))
			$arXmlTags['currencyId'] = Xml::addTag($arFields['CURRENCY_ID']);
		$arXmlTags['picture'] = Xml::addTag($arFields['PICTURE']);
		$arXmlTags['categoryId'] = Xml::addTag(reset($arElementSections));
		if(!Helper::isEmpty($arFields['TYPE_PREFIX']))
			$arXmlTags['typePrefix'] = Xml::addTag($arFields['TYPE_PREFIX']);
		if(!Helper::isEmpty($arFields['VENDOR']))
			$arXmlTags['vendor'] = Xml::addTag($arFields['VENDOR']);
		if(!Helper::isEmpty($arFields['NAME']))
			$arXmlTags['name'] = Xml::addTag($arFields['NAME']);
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['STOCK_QUANTITY']))
			$arXmlTags['stock_quantity'] = Xml::addTag($arFields['STOCK_QUANTITY']);
				
		# Params
		$arXmlTags['param'] = $this->getXmlTag_Param($arProfile, $intIBlockID, $arFields);
		
		# Build XML
		$arXml = array(
			'offer' => array(
				'@' => $this->getXmlAttr($intProfileID, $arFields),
				'#' => $arXmlTags,
			),
		);
		
		# Event handler OnTorgMailRuXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnTorgMailRuXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# Build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => Xml::arrayToXml($arXml),
			'CURRENCY' => $arFields['CURRENCY_ID'],
			'SECTION_ID' => reset($arElementSections),
			'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
			'DATA_MORE' => array(),
		);
		
		# Event handlers OnTorgMailRuResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnTorgMailRuResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# After..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
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
		<?
		return Helper::showSuccess(ob_get_clean());
	}

	/* START OF BASE METHODS FOR XML SUBCLASSES */
	
	/**
	 *	Get steps
	 */
	public function getSteps(){
		$arResult = array();
		$arResult['CHECK'] = array(
			'NAME' => static::getMessage('ACRIT_EXP_EXPORTER_STEP_CHECK'),
			'SORT' => 10,
			#'FUNC' => __CLASS__.'::stepCheck',
			'FUNC' => array($this, 'stepCheck'),
		);
		$arResult['EXPORT'] = array(
			'NAME' => static::getMessage('STEP_EXPORT'),
			'SORT' => 100,
			#'FUNC' => __CLASS__.'::stepExport',
			'FUNC' => array($this, 'stepExport'),
		);
		return $arResult;
	}
	
	/**
	 *	Step: Check input params and data
	 */
	public function stepCheck($intProfileID, $arData){
		$strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		if(!strlen($strExportFilename)){
			Log::getInstance($this->strModuleId)->add(static::getMessage('NO_EXPORT_FILE_SPECIFIED'), $intProfileID);
			print static::getMessage('NO_EXPORT_FILE_SPECIFIED');
			return Exporter::RESULT_ERROR;
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: Export
	 */
	public function stepExport($intProfileID, $arData){
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
			if(is_file($arSession['XML_FILE_TMP'])){
				unlink($arSession['XML_FILE_TMP']);
			}
			touch($arSession['XML_FILE_TMP']);
			unset($strTmpDir, $strTmpFile);
		}
		
		# SubStep1 [header]
		if(!isset($arSession['XML_HEADER_WROTE'])){
			$this->stepExport_writeXmlHeader($intProfileID, $arData);
			$arSession['XML_HEADER_WROTE'] = true;
		}
		
		# SubStep2 [currencies]
		if(!isset($arSession['XML_CURRENCIES_WROTE'])){
			$this->stepExport_writeXmlCurrencies($intProfileID, $arData);
			$arSession['XML_CURRENCIES_WROTE'] = true;
		}
		
		# SubStep3 [<categories>]
		if(!isset($arSession['XML_CATEGORIES_WROTE'])){
			$this->stepExport_writeXmlCategories($intProfileID, $arData);
			$arSession['XML_CATEGORIES_WROTE'] = true;
		}
		
		# SubStep4 [each <offer>]
		if(!isset($arSession['XML_OFFERS_WROTE'])){
			$this->stepExport_writeXmlOffers($intProfileID, $arData);
			$arSession['XML_OFFERS_WROTE'] = true;
		}
		
		# SubStep5 [footer]
		if(!isset($arSession['XML_FOOTER_WROTE'])){
			$this->stepExport_writeXmlFooter($intProfileID, $arData);
			$arSession['XML_FOOTER_WROTE'] = true;
		}
		
		# SubStep6 [tmp => real]
		if(is_file($arSession['XML_FILE'])){
			unlink($arSession['XML_FILE']);
		}
		if(!Helper::createDirectoriesForFile($arSession['XML_FILE'])){
			$strMessage = Loc::getMessage('ACRIT_EXP_ERROR_CREATE_DIRECORY', array(
				'#DIR#' => Helper::getDirectoryForFile($arSession['XML_FILE']),
			));
			Log::getInstance($this->strModuleId)->add($strMessage);
			print Helper::showError($strMessage);
			return Exporter::RESULT_ERROR;
		}
		if(is_file($arSession['XML_FILE'])){
			@unlink($arSession['XML_FILE']);
		}
		if(!@rename($arSession['XML_FILE_TMP'], $arSession['XML_FILE'])){
			@unlink($arSession['XML_FILE_TMP']);
			$strMessage = Loc::getMessage('ACRIT_EXP_FILE_NO_PERMISSIONS', array(
				'#FILE#' => $arSession['XML_FILE'],
			));
			Log::getInstance($this->strModuleId)->add($strMessage);
			print Helper::showError($strMessage);
			return Exporter::RESULT_ERROR;
		}
		
		# SubStep7
		$arSession['EXPORT_FILE_SIZE_XML'] = filesize($arSession['XML_FILE']);
			
		#
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: Export, write header
	 */
	protected function stepExport_writeXmlHeader($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strDate = (new \Bitrix\Main\Type\DateTime())->format('Y-m-d H:i');
		#
		$strXml = '';
		$strXml .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$strXml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'."\n";
		$strXml .= '<yml_catalog date="'.$strDate.'">'."\n";
		$strXml .= "\t".'<shop>'."\n";
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
		#
		$arXml = array(
			'name' => array(
				'#' => $arData['PROFILE']['PARAMS']['SHOP_NAME'],
			),
			'company' => array(
				'#' => $arData['PROFILE']['PARAMS']['SHOP_COMPANY'],
			),
			'url' => array(
				'#' => Helper::siteUrl($arData['PROFILE']['DOMAIN'], $arData['PROFILE']['IS_HTTPS']=='Y'),
			),
		);
		$strXml = Xml::arrayToXml($arXml, $intDepthLevel=3);
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
	
	/**
	 *	Step: Export, write currencies
	 */
	protected function stepExport_writeXmlCurrencies($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
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
			$arCurrencies[$key] = array(
				'@' => array('id' => $arCurrency['CURRENCY'], 'rate' => $strRate),
			);
		}
		$arXml = array(
			'currencies' => array(
				array(
					'#' => array(
						'currency' => $arCurrencies,
					),
				),
			),
		);
		$strXml = Xml::arrayToXml($arXml, $intDepthLevel=3);
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
		
	/**
	 *	Step: Export, write categories
	 */
	protected function stepExport_writeXmlCategories($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		
		# If $this->bCategoryCustomName == true
		if($this->isCategoryCustomName() && $this->isCategoryCustomNameUsed()){
			
			$arCategoriesXml = $this->stepExport_writeXmlCategories_CustomNames($intProfileID, $arData);
			
		}
		else{
			
			# All categories for XML
			$arCategoriesForXml = array();
			
			# Get category redefinitions all
			#$arCategoryRedefinitionsAll = CategoryRedefinition::getForProfile($intProfileID);
			$arCategoryRedefinitionsAll = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$intProfileID]);
			
			# All sections ID for export
			$arSectionsForExportAll = array();
			
			# Process each used IBlocks
			foreach($arData['PROFILE']['IBLOCKS'] as $intIBlockID => $arIBlockSettings){
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
				# Get involded sections ID
				$intSectionsIBlockID = $intIBlockID;
				$strSectionsID = $arIBlockSettings['SECTIONS_ID'];
				$strSectionsMode = $arIBlockSettings['SECTIONS_MODE'];
				$arCatalog = Helper::getCatalogArray($intIBlockID);
				if(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] > 0){
					$intSectionsIBlockID = $arCatalog['PRODUCT_IBLOCK_ID'];
					$strSectionsID = $arData['PROFILE']['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['SECTIONS_ID'];
					$strSectionsMode = $arData['PROFILE']['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['SECTIONS_MODE'];
				}
				$arSelectedSectionsID = Exporter::getInstance($this->strModuleId)->getInvolvedSectionsID($intSectionsIBlockID, $strSectionsID, $strSectionsMode);
				# Process used sections
				$arSectionsForExport = array_intersect($arSelectedSectionsID, $arUsedSectionsID);
				# Merge to all
				$arSectionsForExportAll = array_merge($arSectionsForExportAll, $arSectionsForExport);
				# End
				unset($arSelectedSectionsID, $arUsedSectionsID);
			}
		
			if(!empty($arSectionsForExportAll)) {
				$arSectionsAll = array();
				$resSections = \CIBlockSection::getList(array(
					'ID' => 'ASC',
				),array(
					'ID' => $arSectionsForExportAll,
				), false, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
				while($arSection = $resSections->getNext()){
					$arSection['ID'] = IntVal($arSection['ID']);
					$arSectionsAll[$arSection['ID']] = array(
						'NAME' => trim($arSection['NAME']),
						'PARENT_ID' => IntVal($arSection['IBLOCK_SECTION_ID']),
					);
				}
				$arSectionsForExportAll = $arSectionsAll;
				unset($arSectionsAll, $resSections, $arSection);
			}
			
			# Categories to XML array
			$arCategoriesXml = array();
			foreach($arSectionsForExportAll as $intCategoryID => $arCategory){
				if($arData['PROFILE']['PARAMS']['CATEGORIES_EXPORT_PARENTS']=='Y') {
					$resSectionsChain = \CIBlockSection::getNavChain(false, $intCategoryID, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
					while($arSectionsChain = $resSectionsChain->getNext()){
						$strCategoryName = $arSectionsChain['NAME'];
						if(strlen($arCategoryRedefinitionsAll[$arSectionsChain['ID']])){
							$strCategoryName = $arCategoryRedefinitionsAll[$arSectionsChain['ID']];
						}
						$arCategoryXml = array(
							'@' => array('id' => $arSectionsChain['ID']),
							'#' => htmlspecialcharsbx($strCategoryName),
						);
						if($arSectionsChain['IBLOCK_SECTION_ID']){
							$arCategoryXml['@']['parentId'] = $arSectionsChain['IBLOCK_SECTION_ID'];
						}
						$arCategoriesXml[$arSectionsChain['ID']] = $arCategoryXml;
					}
					unset($resSectionsChain, $arSectionsChain, $arCategoryXml);
				}
				else {
					$intParentID = false;
					$strCategoryName = $arCategory['NAME'];
					if(strlen($arCategoryRedefinitionsAll[$intCategoryID])){
						$strCategoryName = $arCategoryRedefinitionsAll[$intCategoryID];
					}
					$arCategory = array(
						'@' => array('id' => $intCategoryID),
						'#' => htmlspecialcharsbx($strCategoryName),
					);
					if($intParentID){
						$arCategory['@']['parentId'] = $intParentID;
					}
					$arCategoriesXml[] = $arCategory;
				}
			}
			
			# Sort categories
			usort($arCategoriesXml, __CLASS__.'::usortCategoriesCallback');
			
		}
		
		# Categories to XML
		$arXml = array(
			'categories' => array(
				array(
					'#' => array(
						'category' => $arCategoriesXml,
					),
				),
			),
		);
		# Export categories
		$strXml = Xml::arrayToXml($arXml, $intDepthLevel=3);
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
	
	/**
	 *	If $this->bCategoryCustomName == true
	 */
	protected function stepExport_writeXmlCategories_CustomNames($intProfileID, $arData){
		$arCategoriesXml = [];
		foreach($this->getCustomCatagories() as $intCategoryId => $arCategory){
			$arCategoryTag = [
				'@' => ['id' => $intCategoryId],
				'#' => htmlspecialcharsbx($arCategory['NAME']),
			];
			if($arCategory['PARENT_ID']){
				$arCategoryTag['@']['parentId'] = $arCategory['PARENT_ID'];
			}
			$arCategoriesXml[] = $arCategoryTag;
		}
		return $arCategoriesXml;
	}
	
	/**
	 *	Step: Export, write offers
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml .= "\t\t".'<offers>'."\n";
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
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
				$arItem['DATA'] = rtrim(Xml::addOffset($arItem['DATA'], 3))."\n";
				$strXml .= $arItem['DATA'];
			}
			$arData['SESSION']['EXPORT']['INDEX'] += $intCount;
			#
			$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
			#
			file_put_contents($strFile, $strXml, FILE_APPEND);
			if($intCount<$intLimit){
				break;
			}
			$intOffset++;
		}
		#
		$strXml = "\t\t".'</offers>'."\n";
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
	
	/**
	 *	Step: Export, write footer
	 */
	protected function stepExport_writeXmlFooter($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml = '';
		$strXml .= "\t".'</shop>'."\n";
		$strXml .= '</yml_catalog>'."\n";
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
	
	/* HELPERS FOR SIMILAR XML-TYPES */
	
	/**
	 *	Get XML attributes
	 */
	protected function getXmlAttr($intProfileID, $arFields, $strType=false){
		$arResult = array(
			'id' => $arFields['ID'],
		);
		if(!Helper::isEmpty($arFields['AVAILABLE'])){
			$arResult['available'] = $arFields['AVAILABLE'];
		}
		return $arResult;
	}
	
	/**
	 *	Get XML tag: <url>
	 */
	protected function getXmlTag_Url($intProfileID, $strUrl, $arFields){
		if(strlen($strUrl)) {
			$this->addUtmToUrl($strUrl, $arFields);
		}
		return array('#' => $strUrl);
	}
	
	/**
	 *	Add additional params
	 */
	protected function getXmlTag_Param($arProfile, $intIBlockID, $arFields){
		$intProfileID = $arProfile['ID'];
		$arIBlockFields = &$arProfile['IBLOCKS'][$intIBlockID]['FIELDS'];
		$mResult = NULL;
		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]);
		if(!empty($arAdditionalFields)) {
			$mResult = array();
			foreach($arAdditionalFields as $arAdditionalField){
				$strFieldCode = $arAdditionalField['FIELD'];
				if(!Helper::isEmpty($arFields[$strFieldCode])) {
					$arAttributes = array(
						'name' => $arAdditionalField['NAME'],
					);
					$arAdditionalAttributes = $arIBlockFields[$strFieldCode]['PARAMS']['ADDITIONAL_ATTRIBUTES'];
					if(is_array($arAdditionalAttributes) && is_array($arAdditionalAttributes['NAME']) && $arAdditionalAttributes['VALUE']){
						foreach($arAdditionalAttributes['NAME'] as $key => $strAttrName){
							$strAttrValue = $arAdditionalAttributes['VALUE'][$key];
							$arAttributes[$strAttrName] = $strAttrValue;
						}
					}
					if(is_array($arFields[$strFieldCode])){
						foreach($arFields[$strFieldCode] as $strValue){
							$mResult[] = array(
								'@' => $arAttributes,
								'#' => $strValue,
							);
						}
					}
					else {
						$mResult[] = array(
							'@' => $arAttributes,
							'#' => $arFields[$strFieldCode],
						);
					}
				}
			}
		}
		return $mResult;
	}
	
	/* END OF BASE METHODS FOR XML SUBCLASSES */
	
	/**
	 *	Callback to usort for categories
	 */
	public static function usortCategoriesCallback($a, $b){
		$a = $a['@'];
		$b = $b['@'];
		#
		if(isset($a['parentId']) && !isset($b['parentId'])){
			return true;
		}
		elseif(!isset($a['parentId']) && isset($b['parentId'])){
			return false;
		}
		else{
			if($a['id'] == $b['id']) {
				return 0;
			}
			return ($a['id'] < $b['id']) ? -1 : 1;
		}
	}

}

?>