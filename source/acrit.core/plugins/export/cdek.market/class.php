<?
/**
 * Acrit Core: Cdek.Market base plugin
 //* @documentation https://docs.cdek.market/prodavcam/instrukcii/import-tovarov.html
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
	\Acrit\Core\Log,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class CdekMarket extends Plugin {
	
	CONST DATE_UPDATED = '2019-09-15';
	CONST CATEGORIES_FILENAME = 'categories.txt';
	CONST PROMOS_DATEFORMAT = 'Y-m-d H:i:s';
	
	protected $strFileExt;
	
	# Export features (for manage in inherited plugins)
	protected $strRootTag = 'yml_catalog';
	protected $bShopName = true;
	protected $bShopCompany = true;
	protected $bPlatform = true;

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
		return 'CDEK_MARKET';
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
	public function isCategoryStrict(){ // static ot not?
		return false;
	}
	
	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){ // static ot not?
		return true;
	}
	
	/**
	 *	Get list of supported currencies
	 */
	public function getSupportedCurrencies(){
		return array('RUB', 'USD', 'EUR', 'UAH', 'KZT', 'BYN');
	}

	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'cdek_market.xml';
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
		return
			$this->showShopSettings().
			$this->showDefaultSettings();
	}

    public function getCdekLangs($langNum) {
        $arLangs = array('ru','ua','en','de');
        return $langNum == "all" ? $arLangs : $arLangs[$langNum];
    }

	/* HELPERS FOR SIMILAR XML-TYPES */

	/**
	 *	Get XML attributes
	 */
	protected function getXmlAttr($intProfileID, $arFields, $strType=false){
		$arResult = array(
			'id' => $arFields['ID'],
		);
		if(!Helper::isEmpty($strType)){
			$arResult['type'] = $strType;
		}
        elseif(!Helper::isEmpty($arFields['TYPE'])){
			$arResult['type'] = $arFields['TYPE'];
		}
		if(!Helper::isEmpty($arFields['AVAILABLE'])){
			$arResult['available'] = $arFields['AVAILABLE'];
		}
		if(!Helper::isEmpty($arFields['BID'])){
			$arResult['bid'] = $arFields['BID'];
		}
		if(!Helper::isEmpty($arFields['CBID'])){
			$arResult['cbid'] = $arFields['CBID'];
		}
		if(!Helper::isEmpty($arFields['GROUP_ID'])){
			$arResult['group_id'] = $arFields['GROUP_ID'];
		}
		return $arResult;
	}

	/**
	 *	Get XML tag: <url>
	 */
	protected function getXmlTag_Url($intProfileID, $mValue, $arFields){
		$strUrl = '';
		if(strlen($mValue)) {
			$strUrl = $mValue;
			$this->addUtmToUrl($strUrl, $arFields);
		}
		return array('#' => $strUrl);
	}

	/**
	 *	Get XML tag: <vat>
	 */
	protected function getXmlTag_Vat($intProfileID, $mValue, $arFields){
		$strVat = '';
		if(strlen($mValue)) {
			$strVat = $mValue;
		}
		return array('#' => $strVat);
	}

	/**
	 *	Get XML tag: <category>
	 *	У товара может быть основная категория, которая не попадает в выгрузку, поэтому нужно чтобы лишняя категория не добавлялась в <categories>
	 *	Теперь это перенесено в формат [$arXmlTags['categoryId'] = Xml::addTag(reset($arElementSections));]
	 */
	/*
	protected function getXmlTag_Category($arProfile, $arElement, $intDefaultCategoryID=null){
		if(is_numeric($intDefaultCategoryID) && $intDefaultCategoryID > 0){
			#return array('#' => $intDefaultCategoryID);
		}
		$intProfileID = $arProfile['ID'];
		$intCategoryID = 0;
		if($arElement['IBLOCK_SECTION_ID']){
			$intCategoryID = $arElement['IBLOCK_SECTION_ID'];
		}
		elseif($arElement['PARENT']['IBLOCK_SECTION_ID']){
			$intCategoryID = $arElement['PARENT']['IBLOCK_SECTION_ID'];
		}
		$arSectionsID = array();
		if($intCategoryID){
			$arSectionsID[] = $intCategoryID;
		}
		if(is_array($arElement['ADDITIONAL_SECTIONS'])){
			foreach($arElement['ADDITIONAL_SECTIONS'] as $intAdditionalSectionID) {
				$arSectionsID[] = $intAdditionalSectionID;
			}
		}
		$intIBlockID = $arElement['IBLOCK_ID'];
		$intIBlockOffersID = $arProfile['IBLOCKS'][$arElement['IBLOCK_ID']]['_CATALOG']['PRODUCT_IBLOCK_ID'];
		$arProfileSectionsID = array();
		if(!empty($arProfile['IBLOCKS'][$intIBlockID]['SECTIONS_ID_ARRAY'])){
			$arProfileSectionsID = &$arProfile['IBLOCKS'][$intIBlockID]['SECTIONS_ID_ARRAY'];
		}
		elseif(!empty($arProfile['IBLOCKS'][$intIBlockOffersID]['SECTIONS_ID_ARRAY'])){
			$arProfileSectionsID = &$arProfile['IBLOCKS'][$intIBlockOffersID]['SECTIONS_ID_ARRAY'];
		}
		foreach($arSectionsID as $intSectionID){
			if(in_array($intSectionID, $arProfileSectionsID)){
				$intCategoryID = $intSectionID;
				break;
			}
		}
		unset($arSectionsID, $intSectionID);
		return array('#' => $intCategoryID);
	}
	*/

	/**
	 *	Get XML tag: <picture>
	 */
	protected function getXmlTag_Picture($intProfileID, $mValue){
		$mResult = '';
		$mValue = is_array($mValue) ? $mValue : array($mValue);
		if(!empty($mValue)){
			$mResult = array();
			foreach($mValue as $strPicture){
				$mResult[] = array('#' => $strPicture);
			}
		}
		return $mResult;
	}

	/**
	 *	Get XML tag: <barcode>
	 */
	protected function getXmlTag_Barcode($intProfileID, $mValue){
		$mResult = '';
		$mValue = is_array($mValue) ? $mValue : array($mValue);
		if(!empty($mValue)){
			$mResult = array();
			foreach($mValue as $strPictureBarcode){
				$mResult[] = array('#' => $strPictureBarcode);
			}
		}
		return $mResult;
	}

	/**
	 *	Get XML tag: <delivery-options>
	 */
	protected function getXmlTag_DeliveryOptions($intProfileID, $arFields){
		$mCost = $arFields['DELIVERY_OPTIONS_COST'];
		$mDays = $arFields['DELIVERY_OPTIONS_DAYS'];
		$mTime = $arFields['DELIVERY_OPTIONS_ORDER_BEFORE'];
		#
		$mCost = is_array($mCost) ? $mCost : (!Helper::isEmpty($mCost) ? array($mCost) : array());
		$mDays = is_array($mDays) ? $mDays : (!Helper::isEmpty($mDays) ? array($mDays) : array());
		$mTime = is_array($mTime) ? $mTime : (!Helper::isEmpty($mTime) ? array($mTime) : array());
		#
		$arOptions = array();
		foreach($mCost as $key => $value){
			$arOptions[] = array(
				'@' => array(
					'cost' => $mCost[$key],
					'days' => $mDays[$key],
					'order-before' => $mTime[$key],
				),
			);
		}
		if(!empty($arOptions)) {
			return array(
				array(
					'#' => array(
						'option' => $arOptions,
					)
				),
			);
		}
		return '';
	}

	/**
	 *	Get XML tag: <pickup-options>
	 */
	protected function getXmlTag_PickupOptions($intProfileID, $arFields){
		$mCost = $arFields['PICKUP_OPTIONS_COST'];
		$mDays = $arFields['PICKUP_OPTIONS_DAYS'];
		$mTime = $arFields['PICKUP_OPTIONS_ORDER_BEFORE'];
		#
		$mCost = is_array($mCost) ? $mCost : (!Helper::isEmpty($mCost) ? array($mCost) : array());
		$mDays = is_array($mDays) ? $mDays : (!Helper::isEmpty($mDays) ? array($mDays) : array());
		$mTime = is_array($mTime) ? $mTime : (!Helper::isEmpty($mTime) ? array($mTime) : array());
		#
		$arOptions = array();
		foreach($mCost as $key => $value){
			$arOptions[] = array(
				'@' => array(
					'cost' => $mCost[$key],
					'days' => $mDays[$key],
					'order-before' => $mTime[$key],
				),
			);
		}
		if(!empty($arOptions)) {
			return array(
				array(
					'#' => array(
						'option' => $arOptions,
					)
				),
			);
		}
		return '';
	}

	/**
	 *	Get XML tag: <age>
	 */
	protected function getXmlTag_Age($intProfileID, $mValue){
		$mResult = NULL;
		if(!Helper::isEmpty($mValue)){
			$strUnit = 'year';
			if(preg_match('#^0(\.|,)(\d{1,2})$#', $mValue, $arMatch)){
				$strUnit = 'month';
				$mValue = $arMatch[2];
			}
            elseif(preg_match('#^(\d{1,2})[\s]?m$#', $mValue, $arMatch)){
				$strUnit = 'month';
				$mValue = $arMatch[1];
			}
			if($strUnit == 'year' && !in_array($mValue, array(0, 6, 12, 16, 18))){
				Log::getInstance($this->strModuleId)->add(static::getMessage('WRONG_VALUE_FOR_AGE_YEAR', array(
					'#TEXT#' => print_r($mValue, true),
				)), $intProfileID);
				return NULL;
			}
            elseif($strUnit == 'month' && !in_array($mValue, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12))){
				Log::getInstance($this->strModuleId)->add(static::getMessage('WRONG_VALUE_FOR_AGE_MONTH', array(
					'#TEXT#' => print_r($mValue, true),
				)), $intProfileID);
				return NULL;
			}
			$mResult = array(
				array(
					'@' => array('unit' => $strUnit),
					'#' => $mValue,
				),
			);
		}
		return $mResult;
	}

	/**
	 *	Get XML tag: <condition>
	 */
	protected function getXmlTag_Condition($strConditionType, $strConditionReason){
		$mResult = '';
		if(strlen($strConditionType)){
			if(is_array($strConditionReason)){
				$strConditionReason = implode(', ', $strConditionReason);
			}
			$mResult = array(
				array(
					'@' => array(
						'type' => $strConditionType,
					),
					'#' => array(
						'reason' => array(
							array(
								'#' => $strConditionReason,
							),
						),
					),
				),
			);
		}
		return $mResult;
	}

	/**
	 *	Get XML tag: <credit-template>
	 */
	protected function getXmlTag_CreditTemplate($strCreditTemplateId){
		$mResult = '';
		if(is_array($strCreditTemplateId)){
			$strCreditTemplateId = implode(', ', $strCreditTemplateId);
		}
		$mResult = array(
			array(
				'@' => array(
					'id' => $strCreditTemplateId,
				),
			),
		);
		return $mResult;
	}

	/**
	 *	Add additional params
	 */
	protected function getXmlTag_Param($arProfile, $intIBlockID, $arFields){
		$intProfileID = $arProfile['ID'];
		$arIBlockFields = &$arProfile['IBLOCKS'][$intIBlockID]['FIELDS'];
		$mResult = NULL;
		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]); // ToDo: $strModuleId
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
					else{
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
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Show plugin default settings
	 */
	protected function showShopSettings(){
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l">
                        <?=Helper::showHint(static::getMessage('CDEK_LANGUAGES_HINT'));?>
                        <b><?=static::getMessage('CDEK_LANGUAGES');?>:</b>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?
                        $arLanguagesId = self::getCdekLangs("all");
                        $arLanguagesId = array(
                            'REFERENCE' => array_values($arLanguagesId),
                            'REFERENCE_ID' => array_keys($arLanguagesId),
                        );
                        print SelectBoxFromArray('PROFILE[PARAMS][LANGUAGES]', $arLanguagesId,
                            $this->arProfile['PARAMS']['LANGUAGES'], '', 'id="acrit_exp_plugin_languages"');
                        ?>
                    </td>
                </tr>
				<?if($this->bShopName):?>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::showHint(static::getMessage('SHOP_NAME_HINT'));?>
							<b><?=static::getMessage('SHOP_NAME');?>:</b>
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input type="text" name="PROFILE[PARAMS][SHOP_NAME]" 
								value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_NAME']);?>" size="20" />
						</td>
					</tr>
				<?endif?>
				<?if($this->bShopCompany):?>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('SHOP_COMPANY_HINT'));?>
							<b><?=static::getMessage('SHOP_COMPANY');?>:</b>
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input type="text" name="PROFILE[PARAMS][SHOP_COMPANY]" 
								value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_COMPANY']);?>" size="50" />
						</td>
					</tr>
				<?endif?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
        return array();
	}
	
	/**
	 *	Process single element
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		// basically [in this class] do nothing, all business logic are in each format
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
		if($this->arProfile['PARAMS']['COMPRESS_TO_ZIP'] == 'Y') {
			$arResult['ZIP'] = array(
				'NAME' => static::getMessage('STEP_ZIP'),
				'SORT' => 110,
				#'FUNC' => __CLASS__.'::stepZip',
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
		
		# SubStep1 [header]
		if(!isset($arSession['XML_HEADER_WROTE'])){
			$this->stepExport_writeXmlHeader($intProfileID, $arData);
			$arSession['XML_HEADER_WROTE'] = true;
		}
		
		# SubStep2 [<shop>]
		if(!isset($arSession['XML_SHOP_WROTE'])){
			$this->stepExport_writeXmlShop($intProfileID, $arData);
			$arSession['XML_SHOP_WROTE'] = true;
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
			$strMessage = static::getMessage('ACRIT_EXP_ERROR_CREATE_DIRECTORY', array(
				'#DIR#' => Helper::getDirectoryForFile($arSession['XML_FILE']),
			));
			Log::getInstance($this->strModuleId)->add($strMessage, $intProfileID);
			print Helper::showError($strMessage);
			return Exporter::RESULT_ERROR;
		}
		if(is_file($arSession['XML_FILE'])){
			@unlink($arSession['XML_FILE']);
		}
		if(!@rename($arSession['XML_FILE_TMP'], $arSession['XML_FILE'])){
			@unlink($arSession['XML_FILE_TMP']);
			$strMessage = static::getMessage('ACRIT_EXP_FILE_NO_PERMISSIONS', array(
				'#FILE#' => $arSession['XML_FILE'],
			));
			Log::getInstance($this->strModuleId)->add($strMessage, $intProfileID);
			print Helper::showError($strMessage);
			return Exporter::RESULT_ERROR;
		}
		
		# SubStep9
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
		$strEncoding = $arData['PROFILE']['PARAMS']['ENCODING'];
		#
		$strDate = (new \Bitrix\Main\Type\DateTime())->format('Y-m-d H:i');
		$strXml = '';
		$strXml .= '<?xml version="1.0" encoding="'.$strEncoding.'"?>'."\n";
		$strXml .= '<'.$this->strRootTag.' date="'.$strDate.'">'."\n";
		$strXml .= "\t".'<shop>'."\n";
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
	
	/**
	 *	Step: Export, write shop
	 */
	protected function stepExport_writeXmlShop($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$arXml = array();
		if($this->bShopName){
			$arXml['name'] = Xml::addTag($arData['PROFILE']['PARAMS']['SHOP_NAME']);
		}
		if($this->bShopCompany){
			$arXml['company'] = Xml::addTag($arData['PROFILE']['PARAMS']['SHOP_COMPANY']);
		}
		if($this->bPlatform){
			$arXml['platform'] = Xml::addTag(Loc::getMessage('ACRIT_EXP_PLATFORM_NAME'));
			$arXml['version'] = Xml::addTag(SM_VERSION);
		}
		$arXml['url'] = Xml::addTag(Helper::siteUrl($arData['PROFILE']['DOMAIN'], $arData['PROFILE']['IS_HTTPS']=='Y'));
		if(method_exists($this, 'onStepExportWriteXmlShop')){
			$this->onStepExportWriteXmlShop($arXml);
		}
		$strXml = Xml::arrayToXml($arXml, $intDepthLevel=3);
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
	
	/**
	 *	Step: Export, write categories
	 */
	protected function stepExport_writeXmlCategories($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		
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
			$arSelectedSectionsID = Exporter::getInvolvedSectionsID($intSectionsIBlockID, $strSectionsID, $strSectionsMode);
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
			while($arSection = $resSections->getNext(false,false)){
				$arSection['ID'] = IntVal($arSection['ID']);
				$arSectionsAll[$arSection['ID']] = array(
					'NAME' => $arSection['NAME'],
					'PARENT_ID' => IntVal($arSection['IBLOCK_SECTION_ID']),
				);
			}
			$arSectionsForExportAll = $arSectionsAll;
			unset($arSectionsAll, $resSections, $arSection);
		}
		
		switch($arData['PROFILE']['PARAMS']['CATEGORIES_REDEFINITION_MODE']){
			// Режим "Использовать категории торговой площадки"
			case CategoryRedefinition::MODE_STRICT:
				#
				$strSeparator = '/';
				foreach($arSectionsForExportAll as $intSectionID => $arSection){
					if(isset($arCategoryRedefinitionsAll[$intSectionID])){
						$arSectionsForExportAll[$intSectionID]['NAME'] = $arCategoryRedefinitionsAll[$intSectionID];
					}
				}
				
				foreach($arSectionsForExportAll as $intSectionID => $arSection){
					unset($arSectionsForExportAll[$intSectionID]['PARENT_ID']);
					$arSectionName = explode($strSeparator, $arSection['NAME']);
					Helper::pathArray($arSectionName, $strSeparator);
					$strLastName = end($arSectionName);
					foreach($arSectionName as $strSectionNamePath){
						# Search and add if not exists
						$intFoundSectionID = false;
						foreach($arSectionsForExportAll as $intSectionID_1 => $arSection_1){
							if($arSection_1['NAME'] == $strSectionNamePath){
								$intFoundSectionID = $intSectionID_1;
								break;
							}
						}
						#
						if(!$intFoundSectionID){
							$bIsLast = $strSectionNamePath === $strLastName;
							$intID = $bIsLast ? $intSectionID : Helper::getNextAvailableKey($arSectionsForExportAll);
							$arSectionsForExportAll[$intID] = array(
								'NAME' => $strSectionNamePath,
							);
						}
					}
				}
				# Categories to XML array
				$arCategoriesXml = array();
				foreach($arSectionsForExportAll as $intCategoryID => $arCategory){
					$intParentID = false;
					$strCategoryName = $arCategory['NAME'];
					$intSlashPos = strrpos($strCategoryName, '/');
					if($intSlashPos !== false) {
						$strCategoryParentName = substr($strCategoryName, 0, $intSlashPos);
						$strCategoryName = substr($strCategoryName, $intSlashPos+1);
						# searching..
						foreach($arSectionsForExportAll as $intCategoryID_1 => $arCategory_1){
							if($arCategory_1['NAME'] == $strCategoryParentName){
								$intParentID = $intCategoryID_1;
								break;
							}
						}
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
				#
				break;
			// Режим "Использовать категории сайта"
			case CategoryRedefinition::MODE_CUSTOM:
				# Categories to XML array
				$arCategoriesXml = array();
				foreach($arSectionsForExportAll as $intCategoryID => $arCategory){
					if($arData['PROFILE']['PARAMS']['CATEGORIES_EXPORT_PARENTS']=='Y') {
						$resSectionsChain = \CIBlockSection::getNavChain(false, $intCategoryID, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
						while($arSectionsChain = $resSectionsChain->getNext()){
							if(strlen($arCategoryRedefinitionsAll[$arSectionsChain['ID']])){
								$arSectionsChain['NAME'] = $arCategoryRedefinitionsAll[$arSectionsChain['ID']];
							}
							$arCategoryXml = array(
								'@' => array('id' => $arSectionsChain['ID']),
								'#' => htmlspecialcharsbx($arSectionsChain['NAME']),
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
				break;
		}
		
		# Sort categories
		usort($arCategoriesXml, __CLASS__.'::usortCategoriesCallback');
		
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
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 *	Step: Export, write offers
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml .= "\t\t".'<offers>'."\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
		#
		$intLimit = 5000;
		$intOffset = 0;
		while(true){
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
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
			if($intCount<$intLimit){
				break;
			}
			$intOffset++;
		}
		#
		$strXml = '';
		$strXml .= "\t\t".'</offers>'."\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
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
		$strXml .= '</'.$this->strRootTag.'>'."\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
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