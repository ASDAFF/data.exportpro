<?
/**
 * Acrit Core: Google merchant plugin
 * @documentation https://support.google.com/merchants/answer/7052112?hl=ru
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Log,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class GoogleMerchantGeneral extends GoogleMerchant {
	
	CONST DATE_UPDATED = '2018-12-17';
	
	CONST CATEGORIES_TXT_URL_RUSSIAN = 'http://www.google.com/basepages/producttype/taxonomy-with-ids.ru-RU.txt';
	CONST CATEGORIES_TXT_URL_ENGLISH = 'http://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt';
	CONST CATEGORIES_FILENAME = 'categories.txt';

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
	 *	Are categories export?
	 */
	public function areCategoriesExport(){
		return true;
	}
	
	/**
	 *	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict(){
		return true;
	}
	
	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){
		return true;
	}
	
	/**
	 *	Update categories from server
	 */
	public function updateCategories($intProfileID, $arAdditionalData=null){
		$strFileName = $this->getCategoriesCacheFile();
		$strUrl = static::CATEGORIES_TXT_URL_RUSSIAN;
		$bNumeric = false;
		if(is_array($arAdditionalData) && is_array($arAdditionalData['PARAMS'])){
			if($arAdditionalData['PARAMS']['GOOGLE_CATEGORIES_MODE'] == 'english'){
				$strUrl = static::CATEGORIES_TXT_URL_ENGLISH;
			}
			elseif($arAdditionalData['PARAMS']['GOOGLE_CATEGORIES_MODE'] == 'numeric'){
				$bNumeric = true;
			}
		}
		$strCategories = HttpRequest::get($strUrl, array('TIMEOUT' => 20));
		if(strlen($strCategories)) {
			$strCategories = trim($strCategories);
			$intPos = strpos($strCategories, "\n");
			if($intPos){
				$strCategories = substr($strCategories, $intPos+1);
			}
			if(!$bNumeric){
				$strCategories = preg_replace('#^[\d]+[\s]?\-[\s]?#m', '', $strCategories);
			}
			if(!Helper::isUtf()) {
				$strCategories = Helper::convertEncoding($strCategories, 'UTF-8', 'CP1251');
			}
			file_put_contents($strFileName, $strCategories);
			#
			unset($strCategories);
		}
		#
		return (is_file($strFileName) && filesize($strFileName));
	}
	
	/**
	 *	Get categories date update
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
			return explode("\n", file_get_contents($strFileName));
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
	 *	Get custom subtabs for profile edit
	 */
	public function getAdditionalSubTabs($intProfileID, $intIBlockID){
		$arResult = array();
		$arResult['categories'] = array(
			'FILE' => __DIR__.'/subtabs/categories.php',
		);
		return $arResult;
	}
	
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'google_marchants.xml';
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
						<?=Helper::ShowHint(static::getMessage('SETTINGS_TITLE_HINT'));?>
						<b><?=static::getMessage('SETTINGS_TITLE');?>:</b>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][TITLE]" size="46"
							value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['TITLE']);?>"/>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?=Helper::ShowHint(static::getMessage('SETTINGS_DESCRIPTION_HINT'));?>
						<?=static::getMessage('SETTINGS_DESCRIPTION');?>:
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][DESCRIPTION]" size="46"
							value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['DESCRIPTION']);?>"/>
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
									</td>
									<td>
										<?=$this->showFileOpenLink();?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td></td>					
					<td>
						<?=Helper::ShowHint(static::getMessage('SETTINGS_ADD_FEED_HINT'));?>
						<a target="_blank" href="https://merchants.google.com/mc/products/sources/createDataSource"><?=static::getMessage('SETTINGS_ADD_FEED');?></a>
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
			'PARAMS' => array(
				'MAXLENGTH' => 50,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TITLE',
			'DISPLAY_CODE' => 'title',
			'NAME' => static::getMessage('FIELD_TITLE_NAME'),
			'SORT' => 510,
			'DESCRIPTION' => static::getMessage('FIELD_TITLE_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'CDATA' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'NAME',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 150,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DESCRIPTION',
			'DISPLAY_CODE' => 'description',
			'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
			'SORT' => 520,
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
			'PARAMS' => array('HTMLSPECIALCHARS' => 'cut'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LINK',
			'DISPLAY_CODE' => 'link',
			'NAME' => static::getMessage('FIELD_LINK_NAME'),
			'SORT' => 530,
			'DESCRIPTION' => static::getMessage('FIELD_LINK_DESC'),
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
		$arResult[] = new Field(array(
			'CODE' => 'IMAGE_LINK',
			'DISPLAY_CODE' => 'image_link',
			'NAME' => static::getMessage('FIELD_IMAGE_LINK_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_IMAGE_LINK_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
					'MULTIPLE' => 'first',
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'escape',
			),
		));
		$this->addUtmFields($arResult, 541, '', '');
		$arResult[] = new Field(array(
			'CODE' => 'ADDITIONAL_IMAGE_LINK',
			'DISPLAY_CODE' => 'additional_image_link',
			'NAME' => static::getMessage('FIELD_ADDITIONAL_IMAGE_LINK_NAME'),
			'SORT' => 550,
			'DESCRIPTION' => static::getMessage('FIELD_ADDITIONAL_IMAGE_LINK_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MORE_PHOTO',
					'PARAMS' => array(
						'MULTIPLE' => 'multiple',
					),
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
				'MAXLENGTH' => 2000,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MOBILE_LINK',
			'DISPLAY_CODE' => 'mobile_link',
			'NAME' => static::getMessage('FIELD_MOBILE_LINK_NAME'),
			'SORT' => 560,
			'DESCRIPTION' => static::getMessage('FIELD_MOBILE_LINK_DESC'),
			'REQUIRED' => false,
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
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 569,
				'NAME' => static::getMessage('HEADER_PRICE_AND_QUANTITY'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'AVAILABILITY',
			'DISPLAY_CODE' => 'availability',
			'NAME' => static::getMessage('FIELD_AVAILABILITY_NAME'),
			'SORT' => 570,
			'DESCRIPTION' => static::getMessage('FIELD_AVAILABILITY_DESC'),
			'REQUIRED' => true,
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
					'CONST' => 'in stock',
					'SUFFIX' => 'Y',
				),
				array(
					'TYPE' => 'CONST',
					'CONST' => 'preorder',
					'SUFFIX' => 'N',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AVAILABILITY_DATE',
			'DISPLAY_CODE' => 'availability_date',
			'NAME' => static::getMessage('FIELD_AVAILABILITY_DATE_NAME'),
			'SORT' => 580,
			'DESCRIPTION' => static::getMessage('FIELD_AVAILABILITY_DATE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COST_OF_GOODS_SOLD',
			'DISPLAY_CODE' => 'cost_of_goods_sold',
			'NAME' => static::getMessage('FIELD_COST_OF_GOODS_SOLD_NAME'),
			'SORT' => 590,
			'DESCRIPTION' => static::getMessage('FIELD_COST_OF_GOODS_SOLD_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'EXPIRATION_DATE',
			'DISPLAY_CODE' => 'expiration_date',
			'NAME' => static::getMessage('FIELD_EXPIRATION_DATE_NAME'),
			'SORT' => 600,
			'DESCRIPTION' => static::getMessage('FIELD_EXPIRATION_DATE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DATE_ACTIVE_TO',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'price',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 610,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
			'REQUIRED' => true,
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
			'CODE' => 'SALE_PRICE',
			'DISPLAY_CODE' => 'sale_price',
			'NAME' => static::getMessage('FIELD_SALE_PRICE_NAME'),
			'SORT' => 611,
			'DESCRIPTION' => static::getMessage('FIELD_SALE_PRICE_DESC'),
			'REQUIRED' => false,
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
			'CODE' => '_CURRENCY',
			'DISPLAY_CODE' => ' ',
			'NAME' => static::getMessage('FIELD_CURRENCY_NAME'),
			'SORT' => 612,
			'DESCRIPTION' => static::getMessage('FIELD_CURRENCY_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'IS_PRICE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1__CURRENCY',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SALE_PRICE_EFFECTIVE_DATE',
			'DISPLAY_CODE' => 'sale_price_effective_date',
			'NAME' => static::getMessage('FIELD_SALE_PRICE_EFFECTIVE_DATE_NAME'),
			'SORT' => 620,
			'DESCRIPTION' => static::getMessage('FIELD_SALE_PRICE_EFFECTIVE_DATE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1__DATE_TO',  // ToDo
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'UNIT_PRICING_MEASURE',
			'DISPLAY_CODE' => 'unit_pricing_measure',
			'NAME' => static::getMessage('FIELD_UNIT_PRICING_MEASURE_NAME'),
			'SORT' => 630,
			'DESCRIPTION' => static::getMessage('FIELD_UNIT_PRICING_MEASURE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'UNIT_PRICING_BASE_MEASURE',
			'DISPLAY_CODE' => 'unit_pricing_base_measure',
			'NAME' => static::getMessage('FIELD_UNIT_PRICING_BASE_MEASURE_NAME'),
			'SORT' => 640,
			'DESCRIPTION' => static::getMessage('FIELD_UNIT_PRICING_BASE_MEASURE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		#
		/*
		$arResult[] = new Field(array(
			'CODE' => 'INSTALLMENT',
			'DISPLAY_CODE' => 'installment',
			'NAME' => static::getMessage('FIELD_INSTALLMENT_NAME'),
			'SORT' => 650,
			'DESCRIPTION' => static::getMessage('FIELD_INSTALLMENT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		*/
		$arResult[] = new Field(array(
			'CODE' => 'INSTALLMENT_MONTHS',
			'DISPLAY_CODE' => 'installment -> months',
			'NAME' => static::getMessage('FIELD_INSTALLMENT_MONTHS_NAME'),
			'SORT' => 650,
			'DESCRIPTION' => static::getMessage('FIELD_INSTALLMENT_MONTHS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'INSTALLMENT_AMOUNT',
			'DISPLAY_CODE' => 'installment -> amount',
			'NAME' => static::getMessage('FIELD_INSTALLMENT_AMOUNT_NAME'),
			'SORT' => 651,
			'DESCRIPTION' => static::getMessage('FIELD_INSTALLMENT_AMOUNT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		#
		/*
		$arResult[] = new Field(array(
			'CODE' => 'LOYALTY_POINTS',
			'DISPLAY_CODE' => 'loyalty_points',
			'NAME' => static::getMessage('FIELD_LOYALTY_POINTS_NAME'),
			'SORT' => 650,
			'DESCRIPTION' => static::getMessage('FIELD_LOYALTY_POINTS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		*/
		$arResult[] = new Field(array(
			'CODE' => 'LOYALTY_POINTS_VALUE',
			'DISPLAY_CODE' => 'loyalty_points -> value',
			'NAME' => static::getMessage('FIELD_LOYALTY_POINTS_VALUE_NAME'),
			'SORT' => 660,
			'DESCRIPTION' => static::getMessage('FIELD_LOYALTY_POINTS_VALUE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LOYALTY_NAME',
			'DISPLAY_CODE' => 'loyalty_points -> name',
			'NAME' => static::getMessage('FIELD_LOYALTY_NAME_NAME'),
			'SORT' => 661,
			'DESCRIPTION' => static::getMessage('FIELD_LOYALTY_NAME_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 24,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LOYALTY_RATIO',
			'DISPLAY_CODE' => 'loyalty_points -> ratio',
			'NAME' => static::getMessage('FIELD_LOYALTY_RATIO_NAME'),
			'SORT' => 662,
			'DESCRIPTION' => static::getMessage('FIELD_LOYALTY_RATIO_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 699,
				'NAME' => static::getMessage('HEADER_CATEGORIES'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'GOOGLE_PRODUCT_CATEGORY',
			'DISPLAY_CODE' => 'google_product_category',
			'NAME' => static::getMessage('FIELD_GOOGLE_PRODUCT_CATEGORY_NAME'),
			'SORT' => 700,
			'DESCRIPTION' => static::getMessage('FIELD_GOOGLE_PRODUCT_CATEGORY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'CDATA' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRODUCT_TYPE',
			'DISPLAY_CODE' => 'product_type',
			'NAME' => static::getMessage('FIELD_PRODUCT_TYPE_NAME'),
			'SORT' => 710,
			'DESCRIPTION' => static::getMessage('FIELD_PRODUCT_TYPE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => '__IBLOCK_SECTION_CHAIN',
					'PARAMS' => array(
						'MULTIPLE' => 'join',
						'MULTIPLE_separator' => 'other',
						'MULTIPLE_separator_other' => ' &gt; ',
					),
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'skip',
				'MAXLENGTH' => 750,
			),
		));
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 799,
				'NAME' => static::getMessage('HEADER_IDENTIFIERS'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'BRAND',
			'DISPLAY_CODE' => 'brand',
			'NAME' => static::getMessage('FIELD_BRAND_NAME'),
			'SORT' => 800,
			'DESCRIPTION' => static::getMessage('FIELD_BRAND_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_BRAND',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 70,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'GTIN',
			'DISPLAY_CODE' => 'gtin',
			'NAME' => static::getMessage('FIELD_GTIN_NAME'),
			'SORT' => 810,
			'DESCRIPTION' => static::getMessage('FIELD_GTIN_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_GTIN',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 14,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MPN',
			'DISPLAY_CODE' => 'mpn',
			'NAME' => static::getMessage('FIELD_MPN_NAME'),
			'SORT' => 820,
			'DESCRIPTION' => static::getMessage('FIELD_MPN_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MPN',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 70,
			),
		));
		$arField = [
			'CODE' => 'IDENTIFIER_EXISTS',
			'DISPLAY_CODE' => 'identifier_exists',
			'NAME' => static::getMessage('FIELD_IDENTIFIER_EXISTS_NAME'),
			'SORT' => 830,
			'DESCRIPTION' => static::getMessage('FIELD_IDENTIFIER_EXISTS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'no',
				),
			),
		];
		if(Helper::isPropertyExists('GTIN', $intIBlockID) || Helper::isPropertyExists('MPN', $intIBlockID)){
			$arField = array_merge($arField, [
				'DEFAULT_TYPE' => 'CONDITION',
				'DEFAULT_CONDITIONS' => Filter::getConditionsJson($this->strModuleId, $intIBlockID, [
					[
						'FIELD' => 'PROPERTY_GTIN',
						'LOGIC' => 'ISSET',
					],
					[
						'FIELD' => 'PROPERTY_MPN',
						'LOGIC' => 'ISSET',
					],
				], 'ANY'),
				'DEFAULT_VALUE' => [
					[
						'TYPE' => 'CONST',
						'CONST' => 'yes',
						'SUFFIX' => 'Y',
					],
					[
						'TYPE' => 'CONST',
						'CONST' => 'no',
						'SUFFIX' => 'N',
					],
				],
			]);
		}
		$arResult[] = new Field($arField);
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 899,
				'NAME' => static::getMessage('HEADER_DETAILS'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'CONDITION',
			'DISPLAY_CODE' => 'condition',
			'NAME' => static::getMessage('FIELD_CONDITION_NAME'),
			'SORT' => 900,
			'DESCRIPTION' => static::getMessage('FIELD_CONDITION_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'new',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ADULT',
			'DISPLAY_CODE' => 'adult',
			'NAME' => static::getMessage('FIELD_ADULT_NAME'),
			'SORT' => 910,
			'DESCRIPTION' => static::getMessage('FIELD_ADULT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'no',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MULTIPACK',
			'DISPLAY_CODE' => 'multipack',
			'NAME' => static::getMessage('FIELD_MULTIPACK_NAME'),
			'SORT' => 920,
			'DESCRIPTION' => static::getMessage('FIELD_MULTIPACK_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'IS_BUNDLE',
			'DISPLAY_CODE' => 'is_bundle',
			'NAME' => static::getMessage('FIELD_IS_BUNDLE_NAME'),
			'SORT' => 930,
			'DESCRIPTION' => static::getMessage('FIELD_IS_BUNDLE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'no',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENERGY_EFFICIENCY_CLASS',
			'DISPLAY_CODE' => 'energy_efficiency_class',
			'NAME' => static::getMessage('FIELD_ENERGY_EFFICIENCY_CLASS_NAME'),
			'SORT' => 940,
			'DESCRIPTION' => static::getMessage('FIELD_ENERGY_EFFICIENCY_CLASS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MIN_ENERGY_EFFICIENCY_CLASS',
			'DISPLAY_CODE' => 'min_energy_efficiency_class',
			'NAME' => static::getMessage('FIELD_MIN_ENERGY_EFFICIENCY_CLASS_NAME'),
			'SORT' => 950,
			'DESCRIPTION' => static::getMessage('FIELD_MIN_ENERGY_EFFICIENCY_CLASS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAX_ENERGY_EFFICIENCY_CLASS',
			'DISPLAY_CODE' => 'max_energy_efficiency_class',
			'NAME' => static::getMessage('FIELD_MAX_ENERGY_EFFICIENCY_CLASS_NAME'),
			'SORT' => 960,
			'DESCRIPTION' => static::getMessage('FIELD_MAX_ENERGY_EFFICIENCY_CLASS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AGE_GROUP',
			'DISPLAY_CODE' => 'age_group',
			'NAME' => static::getMessage('FIELD_AGE_GROUP_NAME'),
			'SORT' => 970,
			'DESCRIPTION' => static::getMessage('FIELD_AGE_GROUP_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COLOR',
			'DISPLAY_CODE' => 'color',
			'NAME' => static::getMessage('FIELD_COLOR_NAME'),
			'SORT' => 980,
			'DESCRIPTION' => static::getMessage('FIELD_COLOR_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_COLOR',
					'PARAMS' => array(
						'MAXLENGTH' => 40,
					),
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 100,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'GENDER',
			'DISPLAY_CODE' => 'gender',
			'NAME' => static::getMessage('FIELD_GENDER_NAME'),
			'SORT' => 990,
			'DESCRIPTION' => static::getMessage('FIELD_GENDER_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_GENDER',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MATERIAL',
			'DISPLAY_CODE' => 'material',
			'NAME' => static::getMessage('FIELD_MATERIAL_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_MATERIAL_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MATERIAL',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 200,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PATTERN',
			'DISPLAY_CODE' => 'pattern',
			'NAME' => static::getMessage('FIELD_PATTERN_NAME'),
			'SORT' => 1010,
			'DESCRIPTION' => static::getMessage('FIELD_PATTERN_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 100,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SIZE',
			'DISPLAY_CODE' => 'size',
			'NAME' => static::getMessage('FIELD_SIZE_NAME'),
			'SORT' => 1020,
			'DESCRIPTION' => static::getMessage('FIELD_SIZE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_SIZE',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 100,
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SIZE_TYPE',
			'DISPLAY_CODE' => 'size_type',
			'NAME' => static::getMessage('FIELD_SIZE_TYPE_NAME'),
			'SORT' => 1030,
			'DESCRIPTION' => static::getMessage('FIELD_SIZE_TYPE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SIZE_SYSTEM',
			'DISPLAY_CODE' => 'size_system',
			'NAME' => static::getMessage('FIELD_SIZE_SYSTEM_NAME'),
			'SORT' => 1040,
			'DESCRIPTION' => static::getMessage('FIELD_SIZE_SYSTEM_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ITEM_GROUP_ID',
			'DISPLAY_CODE' => 'item_group_id',
			'NAME' => static::getMessage('FIELD_ITEM_GROUP_ID_NAME'),
			'SORT' => 1050,
			'DESCRIPTION' => static::getMessage('FIELD_ITEM_GROUP_ID_DESC'),
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
					'VALUE' => 'PROPERTY_CML2_LINK',
					'PARAMS' => array('RAW' => 'Y'),
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 50,
			),
		));
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 1099,
				'NAME' => static::getMessage('HEADER_CAMPAIGNS'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'ADS_REDIRECT',
			'DISPLAY_CODE' => 'ads_redirect',
			'NAME' => static::getMessage('FIELD_ADS_REDIRECT_NAME'),
			'SORT' => 1100,
			'DESCRIPTION' => static::getMessage('FIELD_ADS_REDIRECT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 2000,
			),
		));
		for($i=0; $i<5; $i++) {
			$arResult[] = new Field(array(
				'CODE' => 'CUSTOM_LABEL_'.$i,
				'DISPLAY_CODE' => 'custom_label_'.$i,
				'NAME' => static::getMessage('FIELD_CUSTOM_LABEL_NAME', array('#INDEX#' => $i)),
				'SORT' => 1110,
				'DESCRIPTION' => static::getMessage('FIELD_CUSTOM_LABEL_DESC', array('#INDEX#' => $i)),
				'REQUIRED' => false,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'CONST',
						'CONST' => '',
					),
				),
				'PARAMS' => array(
					'MAXLENGTH' => 100,
				),
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'PROMOTION_ID',
			'DISPLAY_CODE' => 'promotion_id',
			'NAME' => static::getMessage('FIELD_PROMOTION_ID_NAME'),
			'SORT' => 1120,
			'DESCRIPTION' => static::getMessage('FIELD_PROMOTION_ID_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 50,
			),
		));
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 1199,
				'NAME' => static::getMessage('HEADER_CAMPAIGNS'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'EXCLUDED_DESTINATION',
			'DISPLAY_CODE' => 'excluded_destination',
			'NAME' => static::getMessage('FIELD_EXCLUDED_DESTINATION_NAME'),
			'SORT' => 1200,
			'DESCRIPTION' => static::getMessage('FIELD_EXCLUDED_DESTINATION_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'INCLUDED_DESTINATION',
			'DISPLAY_CODE' => 'included_destination',
			'NAME' => static::getMessage('FIELD_INCLUDED_DESTINATION_NAME'),
			'SORT' => 1210,
			'DESCRIPTION' => static::getMessage('FIELD_INCLUDED_DESTINATION_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 1299,
				'NAME' => static::getMessage('HEADER_DELIVERY'),
				'IS_HEADER' => true,
			));
		}
		#
		/*
		$arResult[] = new Field(array(
			'CODE' => 'SHIPPING',
			'DISPLAY_CODE' => 'shipping',
			'NAME' => static::getMessage('FIELD_SHIPPING_NAME'),
			'SORT' => 1300,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		*/
		$arResult[] = new Field(array(
			'CODE' => 'SHIPPING_COUNTRY',
			'DISPLAY_CODE' => 'shipping -> country',
			'NAME' => static::getMessage('FIELD_SHIPPING_COUNTRY_NAME'),
			'SORT' => 1300,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_COUNTRY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'RU',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHIPPING_REGION',
			'DISPLAY_CODE' => 'shipping -> region',
			'NAME' => static::getMessage('FIELD_SHIPPING_REGION_NAME'),
			'SORT' => 1301,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_REGION_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHIPPING_SERVICE',
			'DISPLAY_CODE' => 'shipping -> service',
			'NAME' => static::getMessage('FIELD_SHIPPING_SERVICE_NAME'),
			'SORT' => 1302,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_SERVICE_DESC'),
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
			'CODE' => 'SHIPPING_PRICE',
			'DISPLAY_CODE' => 'shipping -> price',
			'NAME' => static::getMessage('FIELD_SHIPPING_PRICE_NAME'),
			'SORT' => 1303,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_PRICE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '',
				),
			),
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'SHIPPING_LABEL',
			'DISPLAY_CODE' => 'shipping_label',
			'NAME' => static::getMessage('FIELD_SHIPPING_LABEL_NAME'),
			'SORT' => 1310,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_LABEL_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHIPPING_WEIGHT',
			'DISPLAY_CODE' => 'shipping_weight',
			'NAME' => static::getMessage('FIELD_SHIPPING_WEIGHT_NAME'),
			'SORT' => 1320,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_WEIGHT_DESC'),
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
			'CODE' => 'SHIPPING_LENGTH',
			'DISPLAY_CODE' => 'shipping_length',
			'NAME' => static::getMessage('FIELD_SHIPPING_LENGTH_NAME'),
			'SORT' => 1330,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_LENGTH_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_LENGTH',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHIPPING_WIDTH',
			'DISPLAY_CODE' => 'shipping_width',
			'NAME' => static::getMessage('FIELD_SHIPPING_WIDTH_NAME'),
			'SORT' => 1340,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_WIDTH_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_WIDTH',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SHIPPING_HEIGHT',
			'DISPLAY_CODE' => 'shipping_height',
			'NAME' => static::getMessage('FIELD_SHIPPING_HEIGHT_NAME'),
			'SORT' => 1350,
			'DESCRIPTION' => static::getMessage('FIELD_SHIPPING_HEIGHT_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_HEIGHT',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAX_HANDLING_TIME',
			'DISPLAY_CODE' => 'max_handling_time',
			'NAME' => static::getMessage('FIELD_MAX_HANDLING_TIME_NAME'),
			'SORT' => 1360,
			'DESCRIPTION' => static::getMessage('FIELD_MAX_HANDLING_TIME_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MIN_HANDLING_TIME',
			'DISPLAY_CODE' => 'min_handling_time',
			'NAME' => static::getMessage('FIELD_MIN_HANDLING_TIME_NAME'),
			'SORT' => 1370,
			'DESCRIPTION' => static::getMessage('FIELD_MIN_HANDLING_TIME_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
		));
		#
		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 1399,
				'NAME' => static::getMessage('HEADER_TAXES'),
				'IS_HEADER' => true,
			));
		}
		#
		/*
		$arResult[] = new Field(array(
			'CODE' => 'TAX',
			'DISPLAY_CODE' => 'tax',
			'NAME' => static::getMessage('FIELD_TAX_NAME'),
			'SORT' => 1400,
			'DESCRIPTION' => static::getMessage('FIELD_TAX_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
		));
		*/
		$arResult[] = new Field(array(
			'CODE' => 'TAX_RATE',
			'DISPLAY_CODE' => 'tax -> rate',
			'NAME' => static::getMessage('FIELD_TAX_RATE_NAME'),
			'SORT' => 1400,
			'DESCRIPTION' => static::getMessage('FIELD_TAX_RATE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TAX_COUNTRY',
			'DISPLAY_CODE' => 'tax -> country',
			'NAME' => static::getMessage('FIELD_TAX_COUNTRY_NAME'),
			'SORT' => 1400,
			'DESCRIPTION' => static::getMessage('FIELD_TAX_COUNTRY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TAX_REGION',
			'DISPLAY_CODE' => 'tax -> region',
			'NAME' => static::getMessage('FIELD_TAX_REGION_NAME'),
			'SORT' => 1400,
			'DESCRIPTION' => static::getMessage('FIELD_TAX_REGION_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TAX_TAX_SHIP',
			'DISPLAY_CODE' => 'tax -> tax_ship',
			'NAME' => static::getMessage('FIELD_TAX_TAX_SHIP_NAME'),
			'SORT' => 1400,
			'DESCRIPTION' => static::getMessage('FIELD_TAX_TAX_SHIP_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'TAX_CATEGORY',
			'DISPLAY_CODE' => 'tax_category',
			'NAME' => static::getMessage('FIELD_TAX_CATEGORY_NAME'),
			'SORT' => 1410,
			'DESCRIPTION' => static::getMessage('FIELD_TAX_CATEGORY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
				),
			),
		));
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
			$arElementSections = Exporter::getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		else {
			$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
			$arElementSections = Exporter::getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}
		
		# Build XML
		$arXmlTags = array();
		if(!Helper::isEmpty($arFields['ID']))
			$arXmlTags['g:id'] = Xml::addTag($arFields['ID']);
		if(!Helper::isEmpty($arFields['TITLE']))
			$arXmlTags['g:title'] = Xml::addTag($arFields['TITLE']);
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['g:description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['LINK']))
			$arXmlTags['g:link'] = $this->getXmlTag_Url($intProfileID, $arFields['LINK'], $arFields);
		if(!Helper::isEmpty($arFields['IMAGE_LINK']))
			$arXmlTags['g:image_link'] = Xml::addTag($arFields['IMAGE_LINK']);
		if(!Helper::isEmpty($arFields['ADDITIONAL_IMAGE_LINK']))
			$arXmlTags['g:additional_image_link'] = Xml::addTag($arFields['ADDITIONAL_IMAGE_LINK']);
		if(!Helper::isEmpty($arFields['MOBILE_LINK']))
			$arXmlTags['g:mobile_link'] = Xml::addTag($arFields['MOBILE_LINK']);
		
		#
		if(!Helper::isEmpty($arFields['AVAILABILITY']))
			$arXmlTags['g:availability'] = Xml::addTag($arFields['AVAILABILITY']);
		if(!Helper::isEmpty($arFields['AVAILABILITY_DATE']))
			$arXmlTags['g:availability_date'] = Xml::addTag($arFields['AVAILABILITY_DATE']);
		if(!Helper::isEmpty($arFields['COST_OF_GOODS_SOLD']))
			$arXmlTags['g:cost_of_goods_sold'] = Xml::addTag($arFields['COST_OF_GOODS_SOLD']);
		if(!Helper::isEmpty($arFields['EXPIRATION_DATE']))
			$arXmlTags['g:expiration_date'] = Xml::addTag($arFields['EXPIRATION_DATE']);
		if(!Helper::isEmpty($arFields['SALE_PRICE_EFFECTIVE_DATE']))
			$arXmlTags['g:sale_price_effective_date'] = Xml::addTag($arFields['SALE_PRICE_EFFECTIVE_DATE']);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['g:price'] = $this->getXmlTag_Price($intProfileID, $arFields['PRICE'], $arFields['_CURRENCY']);
		if(!Helper::isEmpty($arFields['SALE_PRICE']) && $arFields['SALE_PRICE'] != $arFields['PRICE'])
			$arXmlTags['g:sale_price'] = $this->getXmlTag_Price($intProfileID, $arFields['SALE_PRICE'], $arFields['_CURRENCY']);
		if(!Helper::isEmpty($arFields['UNIT_PRICING_MEASURE']))
			$arXmlTags['g:unit_pricing_measure'] = Xml::addTag($arFields['UNIT_PRICING_MEASURE']);
		if(!Helper::isEmpty($arFields['UNIT_PRICING_BASE_MEASURE']))
			$arXmlTags['g:unit_pricing_base_measure'] = Xml::addTag($arFields['UNIT_PRICING_BASE_MEASURE']);
		if(!Helper::isEmpty($arFields['INSTALLMENT_MONTHS']))
			$arXmlTags['g:installment'] = $this->getXmlTag_Installment($intProfileID, $arFields);
		if(!Helper::isEmpty($arFields['LOYALTY_POINTS_VALUE']))
			$arXmlTags['g:loyalty_points'] = $this->getXmlTag_LoyaltyPoints($intProfileID, $arFields);
		$arXmlTags['g:google_product_category'] = $this->getXmlTag_Category($arProfile, $arElement, $arFields['GOOGLE_PRODUCT_CATEGORY']);
		if(!Helper::isEmpty($arFields['PRODUCT_TYPE']))
			$arXmlTags['g:product_type'] = Xml::addTag($arFields['PRODUCT_TYPE']);
		if(!Helper::isEmpty($arFields['BRAND']))
			$arXmlTags['g:brand'] = Xml::addTag($arFields['BRAND']);
		if(!Helper::isEmpty($arFields['GTIN']))
			$arXmlTags['g:gtin'] = Xml::addTag($arFields['GTIN']);
		if(!Helper::isEmpty($arFields['MPN']))
			$arXmlTags['g:mpn'] = Xml::addTag($arFields['MPN']);
		if(!Helper::isEmpty($arFields['IDENTIFIER_EXISTS']))
			$arXmlTags['g:identifier_exists'] = Xml::addTag($arFields['IDENTIFIER_EXISTS']);
		
		#
		if(!Helper::isEmpty($arFields['CONDITION']))
			$arXmlTags['g:condition'] = Xml::addTag($arFields['CONDITION']);
		if(!Helper::isEmpty($arFields['ADULT']))
			$arXmlTags['g:adult'] = Xml::addTag($arFields['ADULT']);
		if(!Helper::isEmpty($arFields['MULTIPACK']))
			$arXmlTags['g:multipack'] = Xml::addTag($arFields['MULTIPACK']);
		if(!Helper::isEmpty($arFields['IS_BUNDLE']))
			$arXmlTags['g:is_bundle'] = Xml::addTag($arFields['IS_BUNDLE']);
		if(!Helper::isEmpty($arFields['ENERGY_EFFICIENCY_CLASS']))
			$arXmlTags['g:energy_efficiency_class'] = Xml::addTag($arFields['ENERGY_EFFICIENCY_CLASS']);
		if(!Helper::isEmpty($arFields['MIN_ENERGY_EFFICIENCY_CLASS']))
			$arXmlTags['g:min_energy_efficiency_class'] = Xml::addTag($arFields['MIN_ENERGY_EFFICIENCY_CLASS']);
		if(!Helper::isEmpty($arFields['MAX_ENERGY_EFFICIENCY_CLASS']))
			$arXmlTags['g:max_energy_efficiency_class'] = Xml::addTag($arFields['MAX_ENERGY_EFFICIENCY_CLASS']);
		if(!Helper::isEmpty($arFields['AGE_GROUP']))
			$arXmlTags['g:age_group'] = Xml::addTag($arFields['AGE_GROUP']);
		if(!Helper::isEmpty($arFields['COLOR']))
			$arXmlTags['g:color'] = Xml::addTag($arFields['COLOR']);
		if(!Helper::isEmpty($arFields['GENDER']))
			$arXmlTags['g:gender'] = Xml::addTag($arFields['GENDER']);
		if(!Helper::isEmpty($arFields['MATERIAL']))
			$arXmlTags['g:material'] = Xml::addTag($arFields['MATERIAL']);
		if(!Helper::isEmpty($arFields['PATTERN']))
			$arXmlTags['g:pattern'] = Xml::addTag($arFields['PATTERN']);
		if(!Helper::isEmpty($arFields['SIZE']))
			$arXmlTags['g:size'] = Xml::addTag($arFields['SIZE']);
		if(!Helper::isEmpty($arFields['SIZE_TYPE']))
			$arXmlTags['g:size_type'] = Xml::addTag($arFields['SIZE_TYPE']);
		if(!Helper::isEmpty($arFields['SIZE_SYSTEM']))
			$arXmlTags['g:size_system'] = Xml::addTag($arFields['SIZE_SYSTEM']);
		if(!Helper::isEmpty($arFields['ITEM_GROUP_ID']))
			$arXmlTags['g:item_group_id'] = Xml::addTag($arFields['ITEM_GROUP_ID']);
		
		#
		if(!Helper::isEmpty($arFields['ADS_REDIRECT']))
			$arXmlTags['g:ads_redirect'] = Xml::addTag($arFields['ADS_REDIRECT']);
		for($i=0; $i<5; $i++) {
			if(!Helper::isEmpty($arFields['CUSTOM_LABEL_'.$i]))
				$arXmlTags['g:custom_label_'.$i] = Xml::addTag($arFields['CUSTOM_LABEL_'.$i]);
		}
		if(!Helper::isEmpty($arFields['PROMOTION_ID']))
			$arXmlTags['g:promotion_id'] = Xml::addTag($arFields['PROMOTION_ID']);
		
		#
		if(!Helper::isEmpty($arFields['EXCLUDED_DESTINATION']))
			$arXmlTags['g:excluded_destination'] = Xml::addTag($arFields['EXCLUDED_DESTINATION']);
		if(!Helper::isEmpty($arFields['INCLUDED_DESTINATION']))
			$arXmlTags['g:included_destination'] = Xml::addTag($arFields['INCLUDED_DESTINATION']);
		
		#
		if(!Helper::isEmpty($arFields['SHIPPING_PRICE']))
			$arXmlTags['g:shipping'] = $this->getXmlTag_Shipping($intProfileID, $arFields);
		if(!Helper::isEmpty($arFields['SHIPPING_LABEL']))
			$arXmlTags['g:shipping_label'] = Xml::addTag($arFields['SHIPPING_LABEL']);
		if(!Helper::isEmpty($arFields['SHIPPING_WEIGHT']))
			$arXmlTags['g:shipping_weight'] = Xml::addTag($arFields['SHIPPING_WEIGHT']);
		if(!Helper::isEmpty($arFields['SHIPPING_LENGTH']))
			$arXmlTags['g:shipping_length'] = Xml::addTag($arFields['SHIPPING_LENGTH']);
		if(!Helper::isEmpty($arFields['SHIPPING_WIDTH']))
			$arXmlTags['g:shipping_width'] = Xml::addTag($arFields['SHIPPING_WIDTH']);
		if(!Helper::isEmpty($arFields['SHIPPING_HEIGHT']))
			$arXmlTags['g:shipping_height'] = Xml::addTag($arFields['SHIPPING_HEIGHT']);
		if(!Helper::isEmpty($arFields['MAX_HANDLING_TIME']))
			$arXmlTags['g:max_handling_time'] = Xml::addTag($arFields['MAX_HANDLING_TIME']);
		if(!Helper::isEmpty($arFields['MIN_HANDLING_TIME']))
			$arXmlTags['g:min_handling_time'] = Xml::addTag($arFields['MIN_HANDLING_TIME']);
		
		#
		if(!Helper::isEmpty($arFields['TAX_RATE']))
			$arXmlTags['g:tax'] = $this->getXmlTag_Tax($intProfileID, $arFields);
		if(!Helper::isEmpty($arFields['TAX_CATEGORY']))
			$arXmlTags['g:tax_category'] = Xml::addTag($arFields['TAX_CATEGORY']);
		
		# Build XML
		$arXml = array(
			'item' => array(
				'#' => $arXmlTags,
			),
		);
		
		# Event handler OnGoogleMerchantXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnGoogleMerchantXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# Build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => Xml::arrayToXml($arXml),
			'CURRENCY' => $arFields['_CURRENCY'],
			'SECTION_ID' => reset($arElementSections),
			'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
			'DATA_MORE' => array(),
		);
		
		# Event handlers OnGoogleMerchantResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnGoogleMerchantResult') as $arHandler) {
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
		
		# SubStep4 [each <offer>]
		if(!isset($arSession['XML_OFFERS_WROTE'])){
			$this->stepExport_writeXmlOffers($intProfileID, $arData);
			$arSession['XML_OFFERS_WROTE'] = true;
		}
		
		# SubStep6 [footer]
		if(!isset($arSession['XML_FOOTER_WROTE'])){
			$this->stepExport_writeXmlFooter($intProfileID, $arData);
			$arSession['XML_FOOTER_WROTE'] = true;
		}
		
		# SubStep7 [tmp => real]
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
		
		# SubStep8
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
		$strDate = new \Bitrix\Main\Type\DateTime(null, 'Y-m-d H:i');
		$strXml = '';
		$strXml .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		#$strXml .= '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">'."\n"; # Atavism from feed format
		$strXml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">'."\n";
		$strXml .= "\t".'<channel>'."\n";
		#
		$strXml .= "\t\t".'<title>'.htmlspecialcharsbx($arData['PROFILE']['PARAMS']['TITLE']).'</title>'."\n";
		$strXml .= "\t\t".'<link>'.Helper::siteUrl($arData['PROFILE']['DOMAIN'], $arData['PROFILE']['IS_HTTPS']=='Y').'</link>'."\n";
		$strXml .= "\t\t".'<description>'.htmlspecialcharsbx($arData['PROFILE']['PARAMS']['DESCRIPTION']).'</description>'."\n";
		#$strXml .= "\t".'<updated>'.date('Y-m-d\TH:i:sP').'</updated> '."\n"; # Atavism from feed format
		#
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		#
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
	
	/**
	 *	Step: Export, write offers
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$intOffset = 0;
		while(true){
			$intLimit = 1000;
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
				$arItem['DATA'] = rtrim(Xml::addOffset($arItem['DATA'], 2))."\n";
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
	}
	
	/**
	 *	Step: Export, write footer
	 */
	protected function stepExport_writeXmlFooter($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml = '';
		#$strXml .= '</feed>'."\n"; # Atavism from feed format
		$strXml .= "\t".'</channel>'."\n";
		$strXml .= '</rss>'."\n";
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
	
	/* HELPERS FOR SIMILAR XML-TYPES */
	
	/**
	 *	Get XML tag: <g:price>
	 */
	protected function getXmlTag_Price($intProfileID, $strPrice, $strCurrency){
		if(strlen($strPrice)) {
			$strPrice = $strPrice.(strlen($strCurrency) ? ' '.$strCurrency : '');
		}
		return array('#' => $strPrice);
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
	 *	Get XML tag: <g:google_product_category>
	 */
	protected function getXmlTag_Category($arProfile, $arElement, $mValue){
		if(empty($mValue)) {
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
			#$arCategoryRedefinitions = CategoryRedefinition::getForProfile($intProfileID);
			$arCategoryRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$intProfileID]);
			foreach($arSectionsID as $intSectionID){
				if(array_key_exists($intSectionID, $arCategoryRedefinitions)){
					$mValue = $arCategoryRedefinitions[$intSectionID];
					if($arProfile['PARAMS']['GOOGLE_CATEGORIES_MODE'] == 'numeric'){
						$mValue = preg_replace('#^(\d+).*?$#', '$1', $mValue);
					}
					break;
				}
			}
			unset($arCategoryRedefinitions, $arSectionsID, $intSectionID, $intCategoryID);
		}
		return array('#' => $mValue);
	}
	
	/**
	 *	Get XML tag: <g:installment>
	 */
	protected function getXmlTag_Installment($intProfileID, $arFields){
		$arResult = array();
		$arResult['g:months'] = array(
			array(
				'#' => $arFields['INSTALLMENT_MONTHS'],
			),
		);
		if(!Helper::isEmpty($arFields['INSTALLMENT_AMOUNT'])) {
			$arResult['g:amount'] = array(
				array(
					'#' => $arFields['INSTALLMENT_AMOUNT'],
				),
			);
		}
		return array(
			array(
				'#' => $arResult,
			),
		);
	}
	
	/**
	 *	Get XML tag: <g:loyalty_points>
	 */
	protected function getXmlTag_LoyaltyPoints($intProfileID, $arFields){
		$arResult = array();
		$arResult['g:points_value'] = array(
			array(
				'#' => $arFields['LOYALTY_POINTS_VALUE'],
			),
		);
		if(!Helper::isEmpty($arFields['LOYALTY_NAME'])) {
			$arResult['g:name'] = array(
				array(
					'#' => $arFields['LOYALTY_NAME'],
				),
			);
		}
		if(!Helper::isEmpty($arFields['LOYALTY_RATIO'])) {
			$arResult['g:ratio'] = array(
				array(
					'#' => $arFields['LOYALTY_RATIO'],
				),
			);
		}
		return array(
			array(
				'#' => $arResult,
			),
		);
	}
	
	/**
	 *	Get XML tag: <g:shipping>
	 */
	protected function getXmlTag_Shipping($intProfileID, $arFields){
		$arResult = array();
		if(!Helper::isEmpty($arFields['SHIPPING_COUNTRY'])) {
			$arResult['g:country'] = array(
				array(
					'#' => $arFields['SHIPPING_COUNTRY'],
				),
			);
		}
		if(!Helper::isEmpty($arFields['SHIPPING_REGION'])) {
			$arResult['g:region'] = array(
				array(
					'#' => $arFields['SHIPPING_REGION'],
				),
			);
		}
		if(!Helper::isEmpty($arFields['SHIPPING_SERVICE'])) {
			$arResult['g:service'] = array(
				array(
					'#' => $arFields['SHIPPING_SERVICE'],
				),
			);
		}
		$arResult['g:price'] = array(
			array(
				'#' => $arFields['SHIPPING_PRICE'],
			),
		);
		return array(
			array(
				'#' => $arResult,
			),
		);
	}
	
	/**
	 *	Get XML tag: <g:tax>
	 */
	protected function getXmlTag_Tax($intProfileID, $arFields){
		$arResult = array();
		$arResult['g:rate'] = array(
			array(
				'#' => $arFields['TAX_RATE'],
			),
		);
		if(!Helper::isEmpty($arFields['TAX_COUNTRY'])) {
			$arResult['g:country'] = array(
				array(
					'#' => $arFields['TAX_COUNTRY'],
				),
			);
		}
		if(!Helper::isEmpty($arFields['TAX_REGION'])) {
			$arResult['g:region'] = array(
				array(
					'#' => $arFields['TAX_REGION'],
				),
			);
		}
		if(!Helper::isEmpty($arFields['TAX_TAX_SHIP'])) {
			$arResult['g:tax_ship'] = array(
				array(
					'#' => $arFields['TAX_TAX_SHIP'],
				),
			);
		}
		return array(
			array(
				'#' => $arResult,
			),
		);
	}
	
	/**
	 *	Get XML element section ID (for db field 'SECTION_ID')
	 */
	/*
	protected static function getElement_SectionID($intProfileID, $arElement){
		$intSectionID = 0;
		if($arElement['IBLOCK_SECTION_ID']){
			$intSectionID = $arElement['IBLOCK_SECTION_ID'];
		}
		elseif($arElement['PARENT']['IBLOCK_SECTION_ID']){
			$intSectionID = $arElement['PARENT']['IBLOCK_SECTION_ID'];
		}
		return $intSectionID;
	}
	*/
	
	/* END OF BASE METHODS FOR XML SUBCLASSES */

}

?>