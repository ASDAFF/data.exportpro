<?
/**
 * Acrit Core: Yandex.Market base plugin
 * @documentation https://yandex.ru/support/partnermarket/export/yml.html
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\ProfileIBlockTable as ProfileIBlock,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Export\AdditionalFieldTable as AdditionalField,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\CurrencyConverter\Base as CurrencyConverterBase,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
	\Acrit\Core\Log,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class YandexMarket extends Plugin {
	
	CONST DATE_UPDATED = '2019-03-06';
	
	CONST CATEGORIES_XLS_URL = 'http://download.cdn.yandex.net/market/market_categories.xls';
	CONST CATEGORIES_FILENAME = 'categories.txt';
	
	CONST PROMOS_DATEFORMAT = 'Y-m-d H:i:sO';
	
	protected $strFileExt;
	protected $bCategoryCustomName = true;
	
	# Export features (for manage in inherited plugins)
	protected $strRootTag = 'yml_catalog';
	protected $bShopName = true;
	protected $bShopCompany = true;
	protected $bDelivery = true;
	protected $bEnableAutoDiscounts = true;
	protected $bPlatform = true;
	protected $bZip = true;
	protected $bPromoGift = true;
	protected $bPromoSpecialPrice = true;
	protected $bPromoPromoCard = true;
	protected $bPromoCode = true;
	protected $bPromoNM = true;
	
	# Misc
	protected $arCachePriceLabsParams = [];
	
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
		return 'YANDEX_MARKET';
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
	 *	Update categories from server
	 */
	public function updateCategories($intProfileID){
		$bSuccess = false;
		$strFileContent = HttpRequest::get(static::CATEGORIES_XLS_URL, array('TIMEOUT' => 5));
		if(strlen($strFileContent)){
			#$strTmpDir = Profile::getTmpDir($intProfileID);
			$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
			$strTmpFile = $strTmpDir.'/'.pathinfo(static::CATEGORIES_XLS_URL, PATHINFO_BASENAME);
			if(file_put_contents($strTmpFile, $strFileContent)){
				require_once(realpath(__DIR__.'/../../../include/php_excel_reader/excel_reader2.php'));
				$obExcelData = new \Spreadsheet_Excel_Reader($strTmpFile, false);
				$intRowCount = $obExcelData->rowcount();
				#
				$strCategories = '';
				for($intLine=0; $intLine<=$intRowCount; $intLine++) {
					$strCategories .= $obExcelData->val($intLine, 1)."\n";
				}
				unset($obExcelData);
				$strCategories = trim($strCategories);
				if(strlen($strCategories)){
					$strCategories = Helper::convertEncoding($strCategories, 'UTF-8', 'CP1251');
					$strFileName = $this->getCategoriesCacheFile();
					if(is_file($strFileName)){
						unlink($strFileName);
					}
					if(file_put_contents($strFileName, $strCategories)){
						$bSuccess = true;
					}
					else{
						Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES', array('#FILE#' => $strFileName)), $intProfileID);
					}
				}
				else{
					Log::getInstance($this->strModuleId)->add(static::getMessage('CATEGORIES_ARE_EMPTY', array('#URL#' => static::CATEGORIES_XLS_URL)), $intProfileID);
				}
				@unlink($strTmpFile);
				unset($strCategories, $strFileContent);
			}
			else{
				Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES_TMP', array('#FILE#' => $strTmpFile)), $intProfileID);
			}
		}
		else {
			Log::getInstance($this->strModuleId)->add(static::getMessage('CATEGORIES_EMPTY_ANSWER', array('#URL#' => static::CATEGORIES_XLS_URL)), $intProfileID);
		}
		return $bSuccess;
	}
	
	/**
	 *	Get categories date update // static!?!?!?
	 */
	public function getCategoriesDate(){
		$strFileName = $this->getCategoriesCacheFile();
		return is_file($strFileName) ? filemtime($strFileName) : false;
	}
	
	/**
	 *	Get categories list
	 */
	public function getCategoriesList($intProfileID){
		$strFileName = $this->getCategoriesCacheFile();
		if(!is_file($strFileName) || !filesize($strFileName)) {
			$this->updateCategories($intProfileID);
		}
		if(is_file($strFileName) && filesize($strFileName)) {
			$strResult = file_get_contents($strFileName);
			if(Helper::isUtf()){
				$strResult = Helper::convertEncoding($strResult, 'CP1251', 'UTF-8');
			}
			return explode("\n", $strResult);
		}
		return false;
	}
	
	/**
	 *	Get filename for categories cache
	 */
	protected function getCategoriesCacheFile(){
		$strCacheDir = __DIR__.'/cache';
		if(!is_dir($strCacheDir)){
			mkdir($strCacheDir, BX_DIR_PERMISSIONS, true);
		}
		return $strCacheDir.'/'.static::CATEGORIES_FILENAME;
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
			'DIV' => 'promos',
			'TAB' => static::getMessage('TAB_PROMOS_NAME'),
			'TITLE' => static::getMessage('TAB_PROMOS_DESC'),
			'SORT' => 20,
			'FILE' => __DIR__.'/tabs/promos.php',
		);
		return $arResult;
	}
	
	public function getAdditionalSubTabs($intProfileID, $intIBlockID){
		$arResult = array();
		$arResult[] = array(
			'DIV' => 'pricelabs',
			'TAB' => static::getMessage('SUBTAB_XML_PRICELABS_NAME'),
			'TITLE' => static::getMessage('SUBTAB_XML_PRICELABS_TITLE'),
			'SORT' => 100,
			'FILE' => __DIR__.'/subtabs/pricelabs.php',
		);
		return $arResult;
		return $arResult;
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'yandex_market.xml';
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
				<?if($this->bZip):?>
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
				<?endif?>
			</tbody>
		</table>
		<?if($this->bZip):?>
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
		<?endif?>
		<?if(!strlen($this->arProfile['PARAMS']['CATEGORIES_REDEFINITION_MODE'])):?>
			<input type="hidden" name="PROFILE[PARAMS][CATEGORIES_REDEFINITION_MODE]" value="<?=CategoryRedefinition::MODE_STRICT;?>" />
		<?endif?>
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
				<?if($this->bDelivery):?>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('DELIVERY_HINT'));?>
							<?=static::getMessage('DELIVERY');?>:
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input type="text" name="PROFILE[PARAMS][DELIVERY][COST]" 
								value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['DELIVERY']['COST']);?>" size="14" 
								placeholder="<?=static::getMessage('DELIVERY_PLACEHOLDER_COST')?>" />
							<input type="text" name="PROFILE[PARAMS][DELIVERY][DAYS]" 
								value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['DELIVERY']['DAYS']);?>" size="12" 
								placeholder="<?=static::getMessage('DELIVERY_PLACEHOLDER_DAYS')?>" />
							<input type="text" name="PROFILE[PARAMS][DELIVERY][ORDER_BEFORE]" 
								value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['DELIVERY']['ORDER_BEFORE']);?>" size="12" 
								placeholder="<?=static::getMessage('DELIVERY_PLACEHOLDER_ORDER_BEFORE')?>" />
						</td>
					</tr>
				<?endif?>
				<?if($this->bEnableAutoDiscounts):?>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('ENABLE_AUTO_DISCOUNTS_HINT'));?>
							<?=static::getMessage('ENABLE_AUTO_DISCOUNTS');?>:
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input name="PROFILE[PARAMS][ENABLE_AUTO_DISCOUNTS]" type="hidden" value="N"/>
							<input name="PROFILE[PARAMS][ENABLE_AUTO_DISCOUNTS]" type="checkbox" value="Y" 
								<?if($this->arProfile['PARAMS']['ENABLE_AUTO_DISCOUNTS']=='Y'):?>checked="checked"<?endif?>
							id="acrit_exp_enable_auto_discounts" />
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
		$arResult = array();
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 499,
				'NAME' => static::getMessage('HEADER_GENERAL'),
				'IS_HEADER' => true,
			));
		}
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
			'CODE' => 'CBID',
			'DISPLAY_CODE' => 'cbid',
			'NAME' => static::getMessage('FIELD_CBID_NAME'),
			'SORT' => 600,
			'DESCRIPTION' => static::getMessage('FIELD_CBID_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'BID',
			'DISPLAY_CODE' => 'bid',
			'NAME' => static::getMessage('FIELD_BID_NAME'),
			'SORT' => 700,
			'DESCRIPTION' => static::getMessage('FIELD_BID_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'AVAILABLE',
			'DISPLAY_CODE' => 'available',
			'NAME' => static::getMessage('FIELD_AVAILABLE_NAME'),
			'SORT' => 800,
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
			'CODE' => 'URL',
			'DISPLAY_CODE' => 'url',
			'NAME' => static::getMessage('FIELD_URL_NAME'),
			'SORT' => 900,
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
				'MAXLENGTH' => '512',
				'HTMLSPECIALCHARS' => 'escape',
			),
		));
		$this->addUtmFields($arResult, 901, 'market.yandex.ru');
		$arResult[] = new Field(array(
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'price',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1__WITH_DISCOUNT',
				),
			),
			'IS_PRICE' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'OLD_PRICE',
			'DISPLAY_CODE' => 'oldprice',
			'NAME' => static::getMessage('FIELD_OLD_PRICE_NAME'),
			'SORT' => 1100,
			'DESCRIPTION' => static::getMessage('FIELD_OLD_PRICE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1',
				),
			),
			'IS_PRICE' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CURRENCY_ID',
			'DISPLAY_CODE' => 'currencyId',
			'NAME' => static::getMessage('FIELD_CURRENCY_ID_NAME'),
			'SORT' => 1200,
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
			'CODE' => 'VAT',
			'DISPLAY_CODE' => 'vat',
			'NAME' => static::getMessage('FIELD_VAT_NAME'),
			'SORT' => 1300,
			'DESCRIPTION' => static::getMessage('FIELD_VAT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		if($this->bEnableAutoDiscounts){
			$arResult[] = new Field(array(
				'CODE' => 'ENABLE_AUTO_DISCOUNTS',
				'DISPLAY_CODE' => 'enable_auto_discounts',
				'NAME' => static::getMessage('FIELD_ENABLE_AUTO_DISCOUNTS_NAME'),
				'SORT' => 1350,
				'DESCRIPTION' => static::getMessage('FIELD_ENABLE_AUTO_DISCOUNTS_DESC'),
				'REQUIRED' => false,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'CONST',
					),
				),
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'PICTURE',
			'DISPLAY_CODE' => 'picture',
			'NAME' => static::getMessage('FIELD_PICTURE_NAME'),
			'SORT' => 1400,
			'DESCRIPTION' => static::getMessage('FIELD_PICTURE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
				),
			),
			'MAX_COUNT' => 10,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DESCRIPTION',
			'DISPLAY_CODE' => 'description',
			'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
			'SORT' => 1500,
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
			'PARAMS' => array('HTMLSPECIALCHARS' => 'cdata'),
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
			'CODE' => 'MARKET_CATEGORY',
			'DISPLAY_CODE' => 'market_category',
			'NAME' => static::getMessage('FIELD_MARKET_CATEGORY_NAME'),
			'SORT' => 1506,
			'DESCRIPTION' => static::getMessage('FIELD_MARKET_CATEGORY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 1599,
				'NAME' => static::getMessage('HEADER_DELIVERY'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY',
			'DISPLAY_CODE' => 'delivery',
			'NAME' => static::getMessage('FIELD_DELIVERY_NAME'),
			'SORT' => 1600,
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'true',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY_OPTIONS_COST',
			'DISPLAY_CODE' => 'delivery-options -> cost',
			'NAME' => static::getMessage('FIELD_DELIVERY_OPTIONS_COST_NAME'),
			'SORT' => 1700,
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_OPTIONS_COST_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '500',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY_OPTIONS_DAYS',
			'DISPLAY_CODE' => 'delivery-options -> days',
			'NAME' => static::getMessage('FIELD_DELIVERY_OPTIONS_DAYS_NAME'),
			'SORT' => 1800,
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_OPTIONS_DAYS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '2-4',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY_OPTIONS_ORDER_BEFORE',
			'DISPLAY_CODE' => 'delivery-options -> order-before',
			'NAME' => static::getMessage('FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_NAME'),
			'SORT' => 1900,
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '13',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PICKUP',
			'DISPLAY_CODE' => 'pickup',
			'NAME' => static::getMessage('FIELD_PICKUP_NAME'),
			'SORT' => 2000,
			'DESCRIPTION' => static::getMessage('FIELD_PICKUP_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'true',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PICKUP_OPTIONS_COST',
			'DISPLAY_CODE' => 'pickup-options -> cost',
			'NAME' => static::getMessage('FIELD_PICKUP_OPTIONS_COST_NAME'),
			'SORT' => 2010,
			'DESCRIPTION' => static::getMessage('FIELD_PICKUP_OPTIONS_COST_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '0',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PICKUP_OPTIONS_DAYS',
			'DISPLAY_CODE' => 'pickup-options -> days',
			'NAME' => static::getMessage('FIELD_PICKUP_OPTIONS_DAYS_NAME'),
			'SORT' => 2020,
			'DESCRIPTION' => static::getMessage('FIELD_PICKUP_OPTIONS_DAYS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '0',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PICKUP_OPTIONS_ORDER_BEFORE',
			'DISPLAY_CODE' => 'pickup-options -> order-before',
			'NAME' => static::getMessage('FIELD_PICKUP_OPTIONS_ORDER_BEFORE_NAME'),
			'SORT' => 2030,
			'DESCRIPTION' => static::getMessage('FIELD_PICKUP_OPTIONS_ORDER_BEFORE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '',
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'STORE',
			'DISPLAY_CODE' => 'store',
			'NAME' => static::getMessage('FIELD_STORE_NAME'),
			'SORT' => 2100,
			'DESCRIPTION' => static::getMessage('FIELD_STORE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'true',
				),
			),
		));
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 2399,
				'NAME' => static::getMessage('HEADER_MORE'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'SALES_NOTES',
			'DISPLAY_CODE' => 'sales_notes',
			'NAME' => static::getMessage('FIELD_SALES_NOTES_NAME'),
			'SORT' => 2400,
			'DESCRIPTION' => static::getMessage('FIELD_SALES_NOTES_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MANUFACTURER_WARRANTY',
			'DISPLAY_CODE' => 'manufacturer_warranty',
			'NAME' => static::getMessage('FIELD_MANUFACTURER_WARRANTY_NAME'),
			'SORT' => 2500,
			'DESCRIPTION' => static::getMessage('FIELD_MANUFACTURER_WARRANTY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'true',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COUNTRY_OF_ORIGIN',
			'DISPLAY_CODE' => 'country_of_origin',
			'NAME' => static::getMessage('FIELD_COUNTRY_OF_ORIGIN_NAME'),
			'SORT' => 2600,
			'DESCRIPTION' => static::getMessage('FIELD_COUNTRY_OF_ORIGIN_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_COUNTRY',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ADULT',
			'DISPLAY_CODE' => 'adult',
			'NAME' => static::getMessage('FIELD_ADULT_NAME'),
			'SORT' => 2700,
			'DESCRIPTION' => static::getMessage('FIELD_ADULT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'false',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BARCODE',
			'DISPLAY_CODE' => 'barcode',
			'NAME' => static::getMessage('FIELD_BARCODE_NAME'),
			'SORT' => 2800,
			'DESCRIPTION' => static::getMessage('FIELD_BARCODE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_BARCODE',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'EXPIRY',
			'DISPLAY_CODE' => 'expiry',
			'NAME' => static::getMessage('FIELD_EXPIRY_NAME'),
			'SORT' => 2900,
			'DESCRIPTION' => static::getMessage('FIELD_EXPIRY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'WEIGHT',
			'DISPLAY_CODE' => 'weight',
			'NAME' => static::getMessage('FIELD_WEIGHT_NAME'),
			'SORT' => 3000,
			'DESCRIPTION' => static::getMessage('FIELD_WEIGHT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_WEIGHT',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DIMENSIONS',
			'DISPLAY_CODE' => 'dimensions',
			'NAME' => static::getMessage('FIELD_DIMENSIONS_NAME'),
			'SORT' => 3100,
			'DESCRIPTION' => static::getMessage('FIELD_DIMENSIONS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_DIMENSIONS', # ToDo: add this to available fields! + settings support (1x2x3, or 1cm x 2cm x 3cm, or ..)
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DOWNLOADABLE',
			'DISPLAY_CODE' => 'downloadable',
			'NAME' => static::getMessage('FIELD_DOWNLOADABLE_NAME'),
			'SORT' => 3200,
			'DESCRIPTION' => static::getMessage('FIELD_DOWNLOADABLE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'false',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AGE',
			'DISPLAY_CODE' => 'age',
			'NAME' => static::getMessage('FIELD_AGE_NAME'),
			'SORT' => 3300,
			'DESCRIPTION' => static::getMessage('FIELD_AGE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'GROUP_ID',
			'DISPLAY_CODE' => 'group_id',
			'NAME' => static::getMessage('FIELD_GROUP_ID_NAME'),
			'SORT' => 3400,
			'DESCRIPTION' => static::getMessage('FIELD_GROUP_ID_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'ID',
				),
			),
			'DEFAULT_VALUE_OFFERS' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.ID',
					'PARAMS' => array('RAW' => 'Y'),
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'REC',
			'DISPLAY_CODE' => 'rec',
			'NAME' => static::getMessage('FIELD_REC_NAME'),
			'SORT' => 3410,
			'DESCRIPTION' => static::getMessage('FIELD_REC_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CREDIT_TEMPLATE_ID',
			'DISPLAY_CODE' => 'credit-template',
			'NAME' => static::getMessage('FIELD_CREDIT_TEMPLATE_ID_NAME'),
			'SORT' => 3420,
			'DESCRIPTION' => static::getMessage('FIELD_CREDIT_TEMPLATE_ID_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		#
		if($this->bPromoSpecialPrice){
			if($bAdmin) {
				$arResult[] = new Field(array(
					'SORT' => 3450,
					'CODE' => 'HEADER_PROMOS_SPECIAL_PRICE',
					'NAME' => static::getMessage('HEADER_PROMOS_SPECIAL_PRICE'),
					'IS_HEADER' => true,
				));
			}
			$arResult[] = new Field(array(
				'CODE' => 'SPECIAL_PRICE_START',
				'DISPLAY_CODE' => 'promo -> start-date',
				'NAME' => static::getMessage('FIELD_SPECIAL_PRICE_START_NAME'),
				'SORT' => 3451,
				'DESCRIPTION' => static::getMessage('FIELD_SPECIAL_PRICE_START_DESC'),
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'ACTIVE_FROM',
						'PARAMS' => array(
							'DATEFORMAT' => 'Y',
							'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
							'DATEFORMAT_to' => static::PROMOS_DATEFORMAT,
						),
					),
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'SPECIAL_PRICE_END',
				'DISPLAY_CODE' => 'promo -> end-date',
				'NAME' => static::getMessage('FIELD_SPECIAL_PRICE_END_NAME'),
				'SORT' => 3452,
				'DESCRIPTION' => static::getMessage('FIELD_SPECIAL_PRICE_END_DESC'),
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'ACTIVE_TO',
						'PARAMS' => array(
							'DATEFORMAT' => 'Y',
							'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
							'DATEFORMAT_to' => static::PROMOS_DATEFORMAT,
						),
					),
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'SPECIAL_PRICE_DESCRIPTION',
				'DISPLAY_CODE' => 'promo -> description',
				'NAME' => static::getMessage('FIELD_SPECIAL_PRICE_DESCRIPTION_NAME'),
				'SORT' => 3453,
				'DESCRIPTION' => static::getMessage('FIELD_SPECIAL_PRICE_DESCRIPTION_DESC'),
				'CDATA' => true,
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'CONST',
						'CONST' => static::getMessage('FIELD_SPECIAL_PRICE_DESCRIPTION_DEF', array(
							'#PRODUCT_NAME#' => '{=fields.NAME}',
						)),
					),
				),
				'PARAMS' => array(
					'MAXLENGTH' => '512',
					'HTMLSPECIALCHARS' => 'skip',
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'SPECIAL_PRICE_URL',
				'DISPLAY_CODE' => 'promo -> url',
				'NAME' => static::getMessage('FIELD_SPECIAL_PRICE_URL_NAME'),
				'SORT' => 3454,
				'DESCRIPTION' => static::getMessage('FIELD_SPECIAL_PRICE_URL_DESC'),
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'DETAIL_PAGE_URL',
					),
				),
			));
		}
		#
		if($this->bPromoPromoCard){
			if($bAdmin) {
				$arResult[] = new Field(array(
					'SORT' => 3460,
					'CODE' => 'HEADER_PROMOS_PROMOCARD',
					'NAME' => static::getMessage('HEADER_PROMOS_PROMOCARD'),
					'IS_HEADER' => true,
				));
			}
			$arResult[] = new Field(array(
				'CODE' => 'PROMOCARD_START',
				'DISPLAY_CODE' => 'promo -> start-date',
				'NAME' => static::getMessage('FIELD_PROMOCARD_START_NAME'),
				'SORT' => 3461,
				'DESCRIPTION' => static::getMessage('FIELD_PROMOCARD_START_DESC'),
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'CONST',
						'CONST' => date(\CDatabase::DateFormatToPHP(FORMAT_DATETIME)),
						'PARAMS' => array(
							'DATEFORMAT' => 'Y',
							'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
							'DATEFORMAT_to' => static::PROMOS_DATEFORMAT,
						),
					),
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PROMOCARD_END',
				'DISPLAY_CODE' => 'promo -> end-date',
				'NAME' => static::getMessage('FIELD_PROMOCARD_END_NAME'),
				'SORT' => 3462,
				'DESCRIPTION' => static::getMessage('FIELD_PROMOCARD_END_DESC'),
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'CONST',
						'CONST' => date(\CDatabase::DateFormatToPHP(FORMAT_DATETIME)),
						'PARAMS' => array(
							'DATEFORMAT' => 'Y',
							'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
							'DATEFORMAT_to' => static::PROMOS_DATEFORMAT,
						),
					),
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PROMOCARD_DESCRIPTION',
				'DISPLAY_CODE' => 'promo -> description',
				'NAME' => static::getMessage('FIELD_PROMOCARD_DESCRIPTION_NAME'),
				'SORT' => 3463,
				'DESCRIPTION' => static::getMessage('FIELD_PROMOCARD_DESCRIPTION_DESC'),
				'CDATA' => true,
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'CONST',
						'CONST' => '',
					),
				),
				'PARAMS' => array(
					'MAXLENGTH' => '512',
					'HTMLSPECIALCHARS' => 'skip',
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PROMOCARD_URL',
				'DISPLAY_CODE' => 'promo -> url',
				'NAME' => static::getMessage('FIELD_PROMOCARD_URL_NAME'),
				'SORT' => 3464,
				'DESCRIPTION' => static::getMessage('FIELD_PROMOCARD_URL_DESC'),
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'CONST',
						'CONST' => '',
					),
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PROMOCARD_PRICE',
				'DISPLAY_CODE' => 'purchase -> product -> discount-price',
				'NAME' => static::getMessage('FIELD_PROMOCARD_PRICE_NAME'),
				'SORT' => 3465,
				'DESCRIPTION' => static::getMessage('FIELD_PROMOCARD_PRICE_DESC'),
				'IS_PRICE' => true,
			));
			$arResult[] = new Field(array(
				'CODE' => 'PROMOCARD_CURRENCY',
				'DISPLAY_CODE' => 'purchase -> product -> @currency',
				'NAME' => static::getMessage('FIELD_PROMOCARD_CURRENCY_NAME'),
				'SORT' => 3466,
				'DESCRIPTION' => static::getMessage('FIELD_PROMOCARD_CURRENCY_DESC'),
			));
		}
		#
		if($this->bPromoNM){
			if($bAdmin) {
				$arResult[] = new Field(array(
					'SORT' => 3470,
					'CODE' => 'HEADER_PROMOS_N_PLUS_M',
					'NAME' => static::getMessage('HEADER_PROMOS_N_PLUS_M'),
					'IS_HEADER' => true,
				));
			}
			$arResult[] = new Field(array(
				'CODE' => 'N_PLUS_M_START',
				'DISPLAY_CODE' => 'promo -> start-date',
				'NAME' => static::getMessage('FIELD_N_PLUS_M_START_NAME'),
				'SORT' => 3471,
				'DESCRIPTION' => static::getMessage('FIELD_N_PLUS_M_START_DESC'),
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'ACTIVE_FROM',
						'PARAMS' => array(
							'DATEFORMAT' => 'Y',
							'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
							'DATEFORMAT_to' => static::PROMOS_DATEFORMAT,
						),
					),
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'N_PLUS_M_END',
				'DISPLAY_CODE' => 'promo -> end-date',
				'NAME' => static::getMessage('FIELD_N_PLUS_M_END_NAME'),
				'SORT' => 3472,
				'DESCRIPTION' => static::getMessage('FIELD_N_PLUS_M_END_DESC'),
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'ACTIVE_TO',
						'PARAMS' => array(
							'DATEFORMAT' => 'Y',
							'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
							'DATEFORMAT_to' => static::PROMOS_DATEFORMAT,
						),
					),
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'N_PLUS_M_DESCRIPTION',
				'DISPLAY_CODE' => 'promo -> description',
				'NAME' => static::getMessage('FIELD_N_PLUS_M_DESCRIPTION_NAME'),
				'SORT' => 3473,
				'DESCRIPTION' => static::getMessage('FIELD_N_PLUS_M_DESCRIPTION_DESC'),
				'CDATA' => true,
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'CONST',
						'CONST' => static::getMessage('FIELD_N_PLUS_M_DESCRIPTION_DEF', array(
							'#PRODUCT_NAME#' => '{=fields.NAME}',
						)),
					),
				),
				'PARAMS' => array(
					'MAXLENGTH' => '512',
					'HTMLSPECIALCHARS' => 'skip',
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'N_PLUS_M_URL',
				'DISPLAY_CODE' => 'promo -> url',
				'NAME' => static::getMessage('FIELD_N_PLUS_M_URL_NAME'),
				'SORT' => 3474,
				'DESCRIPTION' => static::getMessage('FIELD_N_PLUS_M_URL_DESC'),
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'DETAIL_PAGE_URL',
					),
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'N_PLUS_M_REQUIRED_QUANTITY',
				'DISPLAY_CODE' => 'promo -> purchase -> required-quantity',
				'NAME' => static::getMessage('FIELD_N_PLUS_M_REQUIRED_QUANTITY_NAME'),
				'SORT' => 3475,
				'DESCRIPTION' => static::getMessage('FIELD_N_PLUS_M_REQUIRED_QUANTITY_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'N_PLUS_M_FREE_QUANTITY',
				'DISPLAY_CODE' => 'promo -> purchase -> free-quantity',
				'NAME' => static::getMessage('FIELD_N_PLUS_M_FREE_QUANTITY_NAME'),
				'SORT' => 3476,
				'DESCRIPTION' => static::getMessage('FIELD_N_PLUS_M_FREE_QUANTITY_DESC'),
			));
		}
		#
		if($this->bPromoGift){
			if($bAdmin){
				$arResult[] = new Field(array(
					'SORT' => 3480,
					'CODE' => 'HEADER_PROMOS_GIFTS',
					'NAME' => static::getMessage('HEADER_PROMOS_GIFTS'),
					'IS_HEADER' => true,
				));
			}
			$arResult[] = new Field(array(
				'CODE' => 'GIFTS_ID',
				'DISPLAY_CODE' => 'promo -> purchase -> product',
				'NAME' => static::getMessage('FIELD_GIFTS_ID_NAME'),
				'SORT' => 3481,
				'DESCRIPTION' => static::getMessage('FIELD_GIFTS_ID_DESC'),
				'REQUIRED' => false,
				'MULTIPLE' => true,
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'PROPERTY_GIFTS',
						'PARAMS' => array(
							'RAW' => 'Y',
							'MULTIPLE' => 'multiple',
						),
					),
				),
				'DEFAULT_VALUE_OFFERS' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'PARENT.PROPERTY_GIFTS',
						'PARAMS' => array(
							'RAW' => 'Y',
							'MULTIPLE' => 'multiple',
						),
					),
				),
				'PARAMS' => array(
					'MULTIPLE' => 'multiple',
				),
			));
			$arResult[] = new Field(array(
				'CODE' => 'GIFTS_DESCRIPTION',
				'DISPLAY_CODE' => 'promo -> description',
				'NAME' => static::getMessage('FIELD_GIFTS_DESCRIPTION_NAME'),
				'SORT' => 3482,
				'DESCRIPTION' => static::getMessage('FIELD_GIFTS_DESCRIPTION_DESC'),
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
				'CODE' => 'GIFTS_URL',
				'DISPLAY_CODE' => 'promo -> url',
				'NAME' => static::getMessage('FIELD_GIFTS_URL_NAME'),
				'SORT' => 3483,
				'DESCRIPTION' => static::getMessage('FIELD_GIFTS_URL_DESC'),
				'REQUIRED' => false,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'DETAIL_PAGE_URL',
					),
				),
			));
		}
		$arPriceLabsParams = $this->getPriceLabsParams($intIBlockID);
		if(!empty($arPriceLabsParams)){
			$intSort = 3500;
			if($bAdmin){
				$arResult[] = new Field(array(
					'SORT' => $intSort++,
					'NAME' => static::getMessage('HEADER_PRICELABS_PARAMS'),
					'IS_HEADER' => true,
				));
			}
			foreach($arPriceLabsParams as $strHash => $strValue){
				$arResult[] = new Field(array(
					'CODE' => $this->getPriceLabsParam($strHash),
					'DISPLAY_CODE' => 'pricelabs param: '.$strValue,
					'NAME' => htmlspecialcharsbx($strValue),
					'DESCRIPTION' => static::getMessage('FIELD_PRICELABS_PARAM_DESC'),
					'SORT' => $intSort++,
				));
			}
		}
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 3999,
				'NAME' => static::getMessage('HEADER_ADDITIONAL_FIELDS'),
				'IS_HEADER' => true,
			));
		}
		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]); // ToDo: $strModuleId
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
				'CODE' => Helper::call($this->strModuleId, 'AdditionalField', 'getFieldCode', [$arAdditionalField['ID']]), // ToDo: $strModuleId
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
		# More fields are in each format (see getFields)
		return $arResult;
	}
	
	/**
	 *	Get PriceLabs params as hash => value
	 */
	protected function getPriceLabsParams($intIBlockId){
		$arResult = &$this->arCachePriceLabsParams[$intIBlockId];
		if(!empty($arResult)){
			return $arResult;
		}
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		$intIBlockId = $arCatalog['PRODUCT_IBLOCK_ID'] ? $arCatalog['PRODUCT_IBLOCK_ID'] : $intIBlockId;
		unset($arCatalog);
		$arPriceLabsParams = preg_split("#\r\n#", $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS']['PRICELABS_PARAMS']);
		foreach($arPriceLabsParams as $strParam){
			$strParam = trim($strParam);
			if(strlen($strParam)){
				$strHash = toUpper(md5($strParam));
				$arResult[$strHash] = $strParam;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get full field name for PriceLabs param
	 */
	protected function getPriceLabsParam($strHash){
		return sprintf('PRICELABS_PARAM_%s', $strHash);
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
		
		# SubStep2 [<currencies>]
		if(!isset($arSession['XML_CURRENCIES_WROTE'])){
			$this->stepExport_writeXmlCurrencies($intProfileID, $arData);
			$arSession['XML_CURRENCIES_WROTE'] = true;
		}
		
		# SubStep3 [<categories>]
		if(!isset($arSession['XML_CATEGORIES_WROTE'])){
			$this->stepExport_writeXmlCategories($intProfileID, $arData);
			$arSession['XML_CATEGORIES_WROTE'] = true;
		}
		
		# SubStep4 [<delivery-options>]
		if(!isset($arSession['XML_OFFERS_DELIVERY_OPTIONS_WROTE'])){
			$this->stepExport_writeXmlDeliveryOptions($intProfileID, $arData);
			$arSession['XML_OFFERS_DELIVERY_OPTIONS_WROTE'] = true;
		}
		
		# SubStep5 [<enable_auto_discounts>]
		if(!isset($arSession['XML_OFFERS_ENABLE_AUTO_DISCOUNTS_WROTE'])){
			$this->stepExport_writeXmlAutoDiscounts($intProfileID, $arData);
			$arSession['XML_OFFERS_ENABLE_AUTO_DISCOUNTS_WROTE'] = true;
		}
		
		# SubStep6 [each <offer>]
		if(!isset($arSession['XML_OFFERS_WROTE'])){
			$this->stepExport_writeXmlOffers($intProfileID, $arData);
			$arSession['XML_OFFERS_WROTE'] = true;
		}
		
		# SubStep7 [<gifts>]
		$bGiftsExport = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_GIFTS']=='Y';
		if($bGiftsExport && !isset($arSession['XML_GIFTS_WROTE'])){
			$this->stepExport_writeXmlGifts($intProfileID, $arData);
			$arSession['XML_GIFTS_WROTE'] = true;
		}
		
		# SubStep8 [<promos>]
		$bExportPromoCodes = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES'] == 'Y';
		$bExportSpecialPrice = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE'] == 'Y';
		$bExportPromoCard = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCARD'] == 'Y';
		$bExportNPlusM = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M'] == 'Y';
		$bExportGifts = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_GIFTS'] == 'Y';
		$bExportPromos = $bExportPromoCodes || $bExportSpecialPrice || $bExportPromoCard || $bExportNPlusM || $bExportGifts;
		if($bExportPromos && !isset($arSession['XML_PROMOS_WROTE'])){
			$this->stepExport_writeXmlPromos($intProfileID, $arData);
		}
		
		# SubStep9 [footer]
		if(!isset($arSession['XML_FOOTER_WROTE'])){
			$this->stepExport_writeXmlFooter($intProfileID, $arData);
			$arSession['XML_FOOTER_WROTE'] = true;
		}
		
		# SubStep10 [tmp => real]
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
		
		# SubStep11
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
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
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
			
			$arCategoriesXml = array();
			
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
						$arCategoryTag = array(
							'@' => array('id' => $intCategoryID),
							'#' => htmlspecialcharsbx($strCategoryName),
						);
						if($intParentID){
							$arCategoryTag['@']['parentId'] = $intParentID;
						}
						$this->onGetCategoryTag($arCategoryTag, $intCategoryID, $arCategory, CategoryRedefinition::MODE_STRICT);
						if(is_array($arCategoryTag)){
							$arCategoriesXml[] = $arCategoryTag;
						}
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
							$arCategoryTag = array(
								'@' => array('id' => $intCategoryID),
								'#' => htmlspecialcharsbx($strCategoryName),
							);
							if($intParentID){
								$arCategoryTag['@']['parentId'] = $intParentID;
							}
							$this->onGetCategoryTag($arCategoryTag, $intCategoryID, $arCategory, CategoryRedefinition::MODE_CUSTOM);
							if(is_array($arCategoryTag)){
								$arCategoriesXml[] = $arCategoryTag;
							}
						}
					}
					break;
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
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
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
	 *	Step: Export, write delivery options
	 */
	protected function stepExport_writeXmlDeliveryOptions($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strCost = trim($arData['PROFILE']['PARAMS']['DELIVERY']['COST']);
		$strDays = trim($arData['PROFILE']['PARAMS']['DELIVERY']['DAYS']);
		$strOrderBefore = trim($arData['PROFILE']['PARAMS']['DELIVERY']['ORDER_BEFORE']);
		#
		if(strlen($strCost) || strlen($strDays) || strlen($strOrderBefore)) {
			$arXml = array(
				'delivery-options' => $this->getXmlTag_DeliveryOptions($intProfileID, array(
					'DELIVERY_OPTIONS_COST' => $strCost,
					'DELIVERY_OPTIONS_DAYS' => $strDays,
					'DELIVERY_OPTIONS_ORDER_BEFORE' => $strOrderBefore,
				)),
			);
			$arXml['delivery-options'] = $arXml['delivery-options'][0];
			$strXml = Xml::arrayToXml($arXml);
			$strXml = Xml::addOffset($strXml, 2);
			$strXml = rtrim($strXml)."\n";
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
		}
	}
	
	/**
	 *	Step: Export, write enable_auto_discounts
	 */
	protected function stepExport_writeXmlAutoDiscounts($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		if($arData['PROFILE']['PARAMS']['ENABLE_AUTO_DISCOUNTS']=='Y'){
			$arXml = array(
				'enable_auto_discounts' => Xml::addTag('yes'),
			);
			$strXml = Xml::arrayToXml($arXml);
			$strXml = Xml::addOffset($strXml, 2);
			$strXml = rtrim($strXml)."\n";
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
		}
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
	 *	Step: Export, write gifts
	 */
	protected function stepExport_writeXmlGifts($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		$strFile = $arSession['XML_FILE_TMP'];
		#
		# 1. find all gifts id
		$arGiftIDs = array();
		$intOffset = 0;
		$intLimit = 5000;
		while(true){
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'!TYPE' => ExportData::TYPE_DUMMY,
					'!DATA_MORE' => array('a:0:{}', ''),
				),
				'order' => array(
					'ELEMENT_ID' => 'ASC',
				),
				'select' => array(
					'ELEMENT_ID',
					'DATA_MORE',
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
				$arDataMore = unserialize($arItem['DATA_MORE']);
				if(is_array($arDataMore['GIFTS']) && !empty($arDataMore['GIFTS']['ID'])){
					// array('123', '124', '125')
					if(is_array($arDataMore['GIFTS']['ID'])){
						foreach($arDataMore['GIFTS']['ID'] as $intGiftID){
							if(is_numeric($intGiftID) && $intGiftID>0 && !in_array($intGiftID, $arGiftIDs)){
								$arGiftIDs[] = $intGiftID;
							}
						}
					}
					// '123'
					elseif(is_numeric($arDataMore['GIFTS']['ID']) && $arDataMore['GIFTS']['ID']>0 && !in_array($arDataMore['GIFTS']['ID'], $arGiftIDs)){
						$arGiftIDs[] = $arDataMore['GIFTS']['ID'];
					}
					// '123,124'
					elseif(strlen($arDataMore['GIFTS']['ID']) && preg_match('#$[0-9,]^#i', $arDataMore['GIFTS']['ID'])){
						foreach(explode(',', $arDataMore['GIFTS']['ID']) as $intGiftID){
							if(is_numeric($intGiftID) && $intGiftID>0 && !in_array($intGiftID, $arGiftIDs)){
								$arGiftIDs[] = $intGiftID;
							}
						}
					}
				}
			}
			if($intCount<$intLimit){
				break;
			}
			$intOffset++;
		}
		# 2. Get gifts that are in <offers>
		$arGiftIDsInOffers = &$arSession['GIFTS_ID_IN_OFFERS'];
		$arGiftIDsInOffers = array();
		$intOffset = 0;
		$intLimit = 5000;
		while(true){
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'!TYPE' => ExportData::TYPE_DUMMY,
					'ELEMENT_ID' => $arGiftIDs,
				),
				'order' => array(
					'ELEMENT_ID' => 'ASC',
				),
				'select' => array(
					'ELEMENT_ID',
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
				$arGiftIDsInOffers[] = $arItem['ELEMENT_ID'];
			}
			if($intCount<$intLimit){
				break;
			}
			$intOffset++;
		}
		# 3. Get gifts that are NOT in <offer>
		$arGiftIDsNotInOffers = &$arSession['GIFTS_ID_NOT_IN_OFFERS'];
		$arGiftIDsNotInOffers = array_diff($arGiftIDs, $arGiftIDsInOffers);
		unset($arGiftIDs);
		# 3. Export gifts
		$arGiftIDsExported = &$arSession['GIFTS_ID_EXPORTED'];
		$arGiftIDsExported = array();
		if(!empty($arGiftIDsNotInOffers) && \Bitrix\Main\Loader::includeModule('iblock')){
			# 3.1 write xml <gifts>
			$strXml .= "\t\t".'<gifts>'."\r\n";
			# 3.2 write all gifts
			$arSort = array(
				'ID' => 'ASC',
			);
			$arFilter = array(
				'ID' => $arGiftIDsNotInOffers,
				'ACTIVE' => 'Y',
				'ACTIVE_DATE' => 'Y',
			);
			$arNavParams = array();
			$arSelect = array(
				'ID',
				'NAME',
				'DETAIL_PICTURE',
				'PREVIEW_PICTURE',
			);
			$resItems = \CIBlockElement::getList($arSort, $arFilter, false, $arNavParams, $arSelect);
			while($arItem = $resItems->getNext()){
				$arXml = array(
					'gift' => array(
						'@' => array(
							'id' => $arItem['ID'],
						),
						'#' => array(
							'name' => array(
								array('#' => htmlspecialcharsbx($arItem['NAME'])),
							),
						),
					),
				);
				$strPicture = false;
				if(is_numeric($arItem['DETAIL_PICTURE'])){
					$strPicture = \CFile::getPath($arItem['DETAIL_PICTURE']);
				}
				elseif(is_numeric($arItem['PREVIEW_PICTURE'])){
					$strPicture = \CFile::getPath($arItem['DETAIL_PICTURE']);
				}
				if(strlen($strPicture)){
					$strDomain = Helper::siteUrl($arData['PROFILE']['DOMAIN'], $arData['PROFILE']['IS_HTTPS']=='Y');
					$arXml['gift']['#']['picture'] = Xml::addTag($strDomain.$strPicture);
				}
				else{
					Log::getInstance($this->strModuleId)->add(static::getMessage('GIFT_DOES_NOT_HAVE_PICTURE', [
						'#ID#' => $arItem['ID'],
					]), $intProfileID);
				}
				$strXml .= rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3))."\n";
				$arGiftIDsExported[] = $arItem['ID'];
			}
			$strXml .= "\t\t".'</gifts>'."\r\n";
			file_put_contents($strFile, $strXml, FILE_APPEND);
		}
		#
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: Export, write promos
	 */
	protected function stepExport_writeXmlPromos($intProfileID, $arData) {
		$arSession = &$arData['SESSION']['EXPORT'];
		$strFile = $arSession['XML_FILE_TMP'];
		
		$strXml = "\t\t".'<promos>'."\r\n";
		
		#
		$bExportPromoCodes = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES'] == 'Y';
		$bExportSpecialPrice = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE'] == 'Y';
		$bExportPromoCard = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCARD'] == 'Y';
		$bExportNPlusM = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M'] == 'Y';
		$bExportGifts = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_GIFTS'] == 'Y';

		# 1. Export promocodes
		if($bExportPromoCodes && count($arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS']) > 0) {
			$strXml .= $this->getProductPromo_Promocodes($arData);
		}

		# 2. Export special price, N+M, gifts
		$intOffset = 0;
		$intLimit = 5000;
		while(true) {
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'!TYPE' => ExportData::TYPE_DUMMY,
					'!DATA_MORE' => false,
				),
				'order' => array(
					'ELEMENT_ID' => 'ASC',
				),
				'select' => array(
					'ELEMENT_ID',
					'DATA_MORE',
				),
				'limit' => $intLimit,
				'offset' => $intOffset * $intLimit,
			];
			#$resItems = ExportData::getList($arQuery);
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$intCount = 0;
			while($arItem = $resItems->fetch()) {
				$intCount++;
				$arItem['DATA_MORE'] = unserialize($arItem['DATA_MORE']);
				$arItem['DATA_MORE'] = is_array($arItem['DATA_MORE']) ? $arItem['DATA_MORE'] : array();
				$arDataMore = &$arItem['DATA_MORE'];
				#
				if($bExportSpecialPrice){
					$strXml .= $this->getProductPromo_SpecialPrice($arItem, $arDataMore, $arData);
				}
				if($bExportPromoCard){
					$strXml .= $this->getProductPromo_PromoCard($arItem, $arDataMore, $arData);
				}
				if($bExportNPlusM){
					$strXml .= $this->getProductPromo_NM($arItem, $arDataMore, $arData);
				}
				if($bExportGifts){
					$strXml .= $this->getProductPromo_Gift($arItem, $arDataMore, $arData);
				}
				#
			}
			#
			if($intCount < $intLimit) {
				break;
			}
			$intOffset++;
		}

		# Add to XML
		$strXml .= "\t\t".'</promos>'."\r\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
		
		#
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	
	 */
	protected function getProductPromo_Promocodes($arData){
		$strXml = '';
		if(\Bitrix\Main\Loader::includeModule('sale') && !empty($arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS'])){
			$arDiscounts = $arDiscountSections = $arDiscountElements = $arDiscountUnits = array();
			$resDiscounts = \Bitrix\Sale\Internals\DiscountTable::getList(array(
				'filter' => array(
					'ID' => $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS'], 
					'ACTIVE' => 'Y',
					'USE_COUPONS' => 'Y',
				)
			));
			while($arDiscount = $resDiscounts->fetch()) {
				$discountId = $arDiscount['ID'];
				$arDiscounts[$discountId] = $arDiscount;
				// collect Elements and Sections from discount
				// with sort collected items by discount unit and value
				// because discount can include many discount values and units
				if($arDiscount['PRESET_ID'] == 'Sale\Handlers\DiscountPreset\SimpleProduct'){
					foreach($arDiscount['ACTIONS_LIST']['CHILDREN'] as $arCondition) {
						if($arCondition['CLASS_ID'] == 'ActSaleBsktGrp' && is_array($arCondition['CHILDREN'])){
							foreach($arCondition['CHILDREN'] as $subCondition){
								if($arCondition['DATA']['Type'] == 'Discount') {
									$discountUnit = $arCondition['DATA']['Unit'];
									$discountValue = $discountUnit.$arCondition['DATA']['Value'];
									if(!$arDiscountUnits[$discountId][$discountValue]) {
										$arDiscountUnits[$discountId][$discountValue] = $arCondition['DATA']['Value'];
									}
								}
								else {
									continue;
								}
								if($subCondition['CLASS_ID'] == 'ActSaleSubGrp' && is_array($subCondition['CHILDREN'])){
									foreach($subCondition['CHILDREN'] as $subSubCondition){
										if($subSubCondition['CLASS_ID'] == 'CondIBElement'){
											if(is_array($subSubCondition['DATA']['value']) && !empty($subSubCondition['DATA']['value'])){
												$arDiscountElements[$discountId][$discountValue] = $subSubCondition['DATA']['value'];
											}
											else{
												// ToDo: all catalog products!!!
											}
										}
										elseif($subSubCondition['CLASS_ID'] == 'CondIBSection'){
											if(is_numeric($subSubCondition['DATA']['value']) && $subSubCondition['DATA']['value'] > 0){
												$arDiscountSections[$discountId][$discountValue][] = $subSubCondition['DATA']['value'];
											}
										}
									}
								}
							}
						}
					}
				}
				else{
					foreach($arDiscount['ACTIONS_LIST']['CHILDREN'] as $arCondition) {
						$discountValue = '';
						if($arCondition['DATA']['Type'] == 'Discount') {
							$discountUnit = $arCondition['DATA']['Unit'];
							$discountValue = $discountUnit.$arCondition['DATA']['Value'];
							if(!$arDiscountUnits[$discountId][$discountValue]) {
								$arDiscountUnits[$discountId][$discountValue] = $arCondition['DATA']['Value'];
							}
						} else {
							continue;
						}
						foreach($arCondition['CHILDREN'] as $subCondition) {
							if($subCondition['CLASS_ID'] == 'CondIBElement' && $subCondition['DATA']['logic'] == 'Equal') {
								if(!$arDiscountElements[$discountId][$discountValue]) {
									$arDiscountElements[$discountId][$discountValue] = array();
								}
								$arDiscountElements[$discountId][$discountValue] = array_merge($subCondition['DATA']['value'],
									$arDiscountElements[$discountId][$discountValue]);
							}
							if($subCondition['CLASS_ID'] == 'CondIBSection' && $subCondition['DATA']['logic'] == 'Equal') {
								$arDiscountSections[$discountId][$discountValue][] = $subCondition['DATA']['value'];
							}
						}
					}
				}
			}
			if(!empty($arDiscountElements) || !empty($arDiscountSections)) {
				$arDiscountsCoupons = array();
				$resCoupons = \Bitrix\Sale\Internals\DiscountCouponTable::getList(array(
					'filter' => array(
						'DISCOUNT_ID' => array_keys($arDiscounts), 
						'ACTIVE' => 'Y',
					),
				));
				while($arCoupon = $resCoupons->fetch()) {
					$arDiscountsCoupons[$arCoupon['DISCOUNT_ID']] = $arCoupon;
				}
				foreach($arDiscountsCoupons as $couponId => $arCoupon) {
					$arDiscount = $arDiscounts[$arCoupon['DISCOUNT_ID']];
					$startDate = $arCoupon['ACTIVE_FROM'] ? $arCoupon['ACTIVE_FROM'] : $arDiscount['ACTIVE_FROM'];
					$endDate = $arCoupon['ACTIVE_TO'] ? $arCoupon['ACTIVE_TO'] : $arDiscount['ACTIVE_TO'];
					#
					$startDate = \CDatabase::FormatDate($startDate, FORMAT_DATETIME, 'YYYY-MM-DD HH:MI:SS');
					$endDate = \CDatabase::FormatDate($endDate, FORMAT_DATETIME, 'YYYY-MM-DD HH:MI:SS');
					#
					if(is_array($arDiscountUnits[$arDiscount['ID']])){
						foreach($arDiscountUnits[$arDiscount['ID']] as $discountValue => $discount) {
							$bPercent = (strpos($discountValue, 'Perc') !== false) ? true : false;
							$xmlProducts = array();
							if(is_array($arDiscountElements[$arDiscount['ID']][$discountValue])){
								foreach($arDiscountElements[$arDiscount['ID']][$discountValue] as $id) {
									$xmlProducts[] = array('@' => array('offer-id' => $id));
								}
							}
							if(is_array($arDiscountSections[$arDiscount['ID']][$discountValue])){
								foreach($arDiscountSections[$arDiscount['ID']][$discountValue] as $id) {
									$xmlProducts[] = array('@' => array('category-id' => $id));
								}
							}
							$discountValueShort = $this->getDiscountValueShort($discountValue);
							$strPromoCodeID = 'PC'.$discountValueShort.'ID'.$arCoupon['ID'];
							$arXml = array(
								'promo' => array(
									'@' => array(
										'id' => $strPromoCodeID,
										'type' => 'promo code',
									),
									'#' => array(
										'start-date' => array(array('#' => $startDate)),
										'end-date' => array(array('#' => $endDate)),
										'description' => array(array('#' => $arCoupon['DESCRIPTION'])),
										'promo-code' => array(array('#' => $arCoupon['COUPON'])),
										'discount' => array(array('#' => $discount, '@' => array(
											'unit' => $bPercent ? 'percent' : 'currency',
											'currency' => $arDiscount['CURRENCY'],
										))),
										'purchase' => array(array('#' => array('product' => $xmlProducts))),
									),
								),
							);
							if($bPercent){
								unset($arXml['promo']['#']['discount'][0]['@']['currency']);
							}
							$strXml .= rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3))."\n";
						}
					}
				}
			}
		}
		return $strXml;
	}
	
	/**
	 *	Get char for unit
	 */
	protected function getDiscountValueShort($strUnit){
		if(preg_match('#^(.*?)(\d+)$#i', $strUnit, $arMatch)){
			$arChars = array(
				'Perc' => 'P',
				'CurEach' => 'E',
				'CurAll' => 'A',
			);
			return $arChars[$arMatch[1]].$arMatch[2];
		}
		return '';
	}
	
	/**
	 *	
	 */
	protected function getProductPromo_SpecialPrice($arItem, $arDataMore, $arData){
		$strXml = '';
		$arPromo = &$arDataMore['SPECIAL_PRICE'];
		if(is_array($arPromo) && !empty($arPromo['PRICE']) && !empty($arPromo['CURRENCY'])) {
			$strPromoSpecialPriceID = 'SP'.$arPromo['OFFER_ID'];
			$arXml = array(
				'promo' => array(
					'@' => array(
						'id' => $strPromoSpecialPriceID,
						'type' => 'flash discount',
					),
					'#' => array(
						'start-date' => array(array('#' => $arPromo['START'])),
						'end-date' => array(array('#' => $arPromo['END'])),
						'description' => array(array('#' => $arPromo['DESCR'])),
						'url' => array(array('#' => $arPromo['URL'])),
						'purchase' => array(
							array(
								'#' => array(
									'product' => array(
										array(
											'@' => array('offer-id' => $arPromo['OFFER_ID']),
											'#' => array(
												'discount-price' => array(
													array(
														'@' => array('currency' => $arPromo['CURRENCY']), 
														'#' => $arPromo['PRICE'],
													)
												)
											),
										),
									),
								),
							),
						),
					),
				),
			);
			$strXml = rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3))."\n";
		}
		return $strXml;
	}
	
	/**
	 *	
	 */
	protected function getProductPromo_PromoCard($arItem, $arDataMore, $arData){
		$strXml = '';
		$arPromo = &$arDataMore['PROMOCARD'];
		if(is_array($arPromo) && !empty($arPromo['PRICE']) && !empty($arPromo['CURRENCY'])) {
			$strPromoPromoCardID = 'PC'.$arPromo['OFFER_ID'];
			$arXml = array(
				'promo' => array(
					'@' => array(
						'id' => $strPromoPromoCardID,
						'type' => 'bonus card',
					),
					'#' => array(
						'start-date' => array(array('#' => $arPromo['START'])),
						'end-date' => array(array('#' => $arPromo['END'])),
						'description' => array(array('#' => $arPromo['DESCR'])),
						'url' => array(array('#' => $arPromo['URL'])),
						'purchase' => array(
							array(
								'#' => array(
									'product' => array(
										array(
											'@' => array('offer-id' => $arPromo['OFFER_ID']),
											'#' => array(
												'discount-price' => array(
													array(
														'@' => array('currency' => $arPromo['CURRENCY']), 
														'#' => $arPromo['PRICE'],
													)
												)
											),
										),
									),
								),
							),
						),
					),
				),
			);
			$strXml = rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3))."\n";
		}
		return $strXml;
	}
	
	/**
	 *	
	 */
	protected function getProductPromo_NM($arItem, $arDataMore, $arData){
		$strXml = '';
		$arPromo = &$arDataMore['N_PLUS_M'];
		if(is_array($arPromo) && !empty($arPromo['REQ']) && !empty($arPromo['FREE'])) {
			$strPromoNmID = 'NM'.$arPromo['OFFER_ID'];
			$arXml = array(
				'promo' => array(
					'@' => array(
						'id' => $strPromoNmID,
						'type' => 'n plus m',
					),
					'#' => array(
						'start-date' => array(array('#' => $arPromo['START'])),
						'end-date' => array(array('#' => $arPromo['END'])),
						'description' => array(array('#' => $arPromo['DESCR'])),
						'url' => array(array('#' => $arPromo['URL'])),
						'purchase' => array(
							array(
								'#' => array(
									'required-quantity' => array(array('#' => $arPromo['REQ'])),
									'free-quantity' => array(array('#' => $arPromo['FREE'])),
									'product' => array(array('@' => array('offer-id' => $arPromo['OFFER_ID']))),
								),
							),
						),
					),
				),
			);
			$strXml = rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3))."\n";
		}
		return $strXml;
	}
	
	/**
	 *	
	 */
	protected function getProductPromo_Gift($arItem, $arDataMore, $arData){
		$strXml = '';
		#
		$arGiftIDsExported = &$arData['SESSION']['EXPORT']['GIFTS_ID_EXPORTED']; // in <gifts>
		$arGiftIDsInOffers = &$arData['SESSION']['EXPORT']['GIFTS_ID_IN_OFFERS']; // in <offers>
		#
		$arPromo = &$arDataMore['GIFTS'];
		$arGiftIDs = array();
		if(is_array($arPromo) && !empty($arPromo['ID'])) {
			// array('123', '124', '125')
			if(is_array($arPromo['ID'])) {
				foreach($arPromo['ID'] as $intGiftID) {
					if(is_numeric($intGiftID) && $intGiftID > 0 && !in_array($intGiftID, $arGiftIDs)) {
						$arGiftIDs[] = $intGiftID;
					}
				}
			}
			// '123'
			elseif(is_numeric($arPromo['ID']) && $arPromo['ID'] > 0 && !in_array($arPromo['ID'], $arGiftIDs)) {
				$arGiftIDs[] = $arPromo['ID'];
			}
			// '123,124'
			elseif(strlen($arPromo['ID']) && preg_match('#$[0-9,]^#i', $arPromo['ID'])) {
				foreach(explode(',', $arPromo['ID']) as $intGiftID) {
					if(is_numeric($intGiftID) && $intGiftID > 0 && !in_array($intGiftID, $arGiftIDs)) {
						$arGiftIDs[] = $intGiftID;
					}
				}
			}
			# Build xml
			$strPromoGiftID = 'G'.$arItem['ELEMENT_ID'];
			$arXml = array(
				'promo' => array(
					'@' => array(
						'id' => $strPromoGiftID,
						'type' => 'gift with purchase',
					),
					'#' => array(
						'description' => array(array('#' => $arPromo['DESCRIPTION'])),
						'url' => array(array('#' => $arPromo['URL'])),
						'purchase' => array(
							array(
								'#' => array(
									'product' => array(array('@' => array('offer-id' => $arItem['ELEMENT_ID']))),
								),
							),
						),
						'promo-gifts' => array(array('#' => array('promo-gift' => array()))),
					),
				),
			);
			foreach(array_intersect($arGiftIDs, $arGiftIDsInOffers) as $intGiftID) {
				$arXml['promo']['#']['promo-gifts'][0]['#']['promo-gift'][] = array(
					'@' => array('offer-id' => $intGiftID),
				);
			}
			foreach(array_intersect($arGiftIDs, $arGiftIDsExported) as $intGiftID) {
				$arXml['promo']['#']['promo-gifts'][0]['#']['promo-gift'][] = array(
					'@' => array('gift-id' => $intGiftID),
				);
			}
			$strXml = rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3))."\n";
		}
		return $strXml;
	}
	
	/**
	 *	Step: Export, write footer
	 */
	protected function stepExport_writeXmlFooter($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml = '';
		$strXml .= "\t".'</shop>'."\n";
		$strXml .= '</'.$this->strRootTag.'>';
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
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
			elseif($intResult === \IBXArchive::StatusError){
				return Exporter::RESULT_ERROR;
			}
			elseif($intResult === \IBXArchive::StatusContinue){
				$arSession['ZIP_NEXT_STEP'] = true;
				return Exporter::RESULT_CONTINUE;
			}
		}
		return Exporter::RESULT_SUCCESS;
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
	 *	Process element promos for further save it to DATA_MORE
	 */
	protected function processElementPromos($arFields, &$arDataMore){
		if(!is_array($arDataMore)){
			$arDataMore = [];
		}
		# Promos Special price
		if($this->bPromoSpecialPrice && $this->arProfile['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE'] == 'Y'){
			if (!Helper::isEmpty($arFields['OLD_PRICE']) && !Helper::isEmpty($arFields['PRICE'])) {
				if($arFields['OLD_PRICE'] > $arFields['PRICE']){
					$arDataMore['SPECIAL_PRICE'] = [
						'OFFER_ID' => $arFields['ID'],
						'START' => $arFields['SPECIAL_PRICE_START'],
						'END' => $arFields['SPECIAL_PRICE_END'],
						'DESCR' => $arFields['SPECIAL_PRICE_DESCRIPTION'],
						'URL' => $arFields['SPECIAL_PRICE_URL'],
						'PRICE' => $arFields['PRICE'],
						'CURRENCY' => $arFields['CURRENCY_ID'],
					];
				}
			}
		}
		# Promos Promocard
		if($this->bPromoPromoCard && $this->arProfile['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCARD'] == 'Y'){
			if(!Helper::isEmpty($arFields['PROMOCARD_PRICE']) && !Helper::isEmpty($arFields['PROMOCARD_CURRENCY'])) {
				$arDataMore['PROMOCARD'] = [
					'OFFER_ID' => $arFields['ID'],
					'START' => $arFields['PROMOCARD_START'],
					'END' => $arFields['PROMOCARD_END'],
					'DESCR' => $arFields['PROMOCARD_DESCRIPTION'],
					'URL' => $arFields['PROMOCARD_URL'],
					'PRICE' => $arFields['PROMOCARD_PRICE'],
					'CURRENCY' => $arFields['PROMOCARD_CURRENCY'],
				];
			}
		}
		# N + M
		if($this->bPromoNM && $this->arProfile['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M'] == 'Y'){
			if (!Helper::isEmpty($arFields['N_PLUS_M_FREE_QUANTITY'])) {
				$arDataMore['N_PLUS_M'] = [
					'OFFER_ID' => $arFields['ID'],
					'START' => $arFields['N_PLUS_M_START'],
					'END' => $arFields['N_PLUS_M_END'],
					'DESCR' => $arFields['N_PLUS_M_DESCRIPTION'],
					'URL' => $arFields['N_PLUS_M_URL'],
					'REQ' => $arFields['N_PLUS_M_REQUIRED_QUANTITY'],
					'FREE' => $arFields['N_PLUS_M_FREE_QUANTITY'],
				];
			}
		}
		# Gifts
		if($this->bPromoGift && $this->arProfile['PARAMS']['YANDEX_MARKET_EXPORT_GIFTS'] == 'Y'){
			if(!Helper::isEmpty($arFields['GIFTS_ID'])){
				$arDataMore['GIFTS'] = [
					'OFFER_ID' => $arFields['ID'],
					'ID' => $arFields['GIFTS_ID'],
					'DESCRIPTION' => $arFields['GIFTS_DESCRIPTION'],
					'URL' => $arFields['GIFTS_URL'],
				];
			}
		}
	}
	
	/**
	 *	Process element PriceLabs params
	 */
	protected function processElementPriceLabsParams($intIBlockId, $arFields, &$arXmlTags){
		$arValues = [];
		$arPriceLabsParams = $this->getPriceLabsParams($intIBlockId);
		foreach($arPriceLabsParams as $strHash => $strValue){
			$strField = $this->getPriceLabsParam($strHash);
			if(!Helper::isEmpty($arFields[$strField])) {
				$arValues[] = array(
					'@' => [
						'name' => $strValue,
					],
					'#' => $arFields[$strField],
				);
			}
		}
		if(!empty($arValues)){
			$arXmlTags['pricelabs_param'] = $arValues;
		}
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
	
	/**
	 *	Modify teachers
	 */
	public function modifyTeachers(&$arTeachers){
		//
	}
	
	/**
	 *	Modify default teacher
	 */
	public function modifyDefaultTeacher(&$arDefaultTeacher){
		#$this->teaacherAddItem($arDefaultTeacher['STEPS'], 'NEW', ['TITLE' => 'NEW'], 'ACTIVE');
	}

}

?>