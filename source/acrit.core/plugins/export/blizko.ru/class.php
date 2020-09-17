<?
/**
 * Acrit Core: BlizkoRu base plugin
 * @documentation https://www.blizko.ru/about/FAQ/help/yml
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Acrit\Core\Helper,
		\Acrit\Core\Export\Plugin,
		\Acrit\Core\Export\Field\Field,
		\Acrit\Core\HttpRequest,
		\Acrit\Core\Export\Filter,
		\Acrit\Core\Export\Exporter,
		\Acrit\Core\Export\ExportDataTable as ExportData,
		\Acrit\Core\Export\CurrencyConverter\Base as CurrencyConverterBase,
		\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
		\Acrit\Core\Xml,
		\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class BlizkoRu extends Plugin {

	CONST DATE_UPDATED = '2018-12-28';
	CONST CATEGORIES_XLS_URL = 'http://download.cdn.yandex.net/market/market_categories.xls';
	CONST CATEGORIES_FILENAME = 'categories.txt';

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
		return 'BLIZKO_RU';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}

	/**
	 * 	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported() {
		return true;
	}

	/**
	 * 	Are categories export?
	 */
	public function areCategoriesExport() {
		return true;
	}

	/**
	 * 	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict() { // static ot not?
		return false;
	}

	/**
	 * 	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList() { // static ot not?
		return true;
	}

	/**
	 * 	Update categories from server
	 */
	public function updateCategories($intProfileID) {
		$bSuccess = false;
		$strFileContent = HttpRequest::get(static::CATEGORIES_XLS_URL, array('TIMEOUT' => 5));
		if (strlen($strFileContent)) {
			#$strTmpDir = Profile::getTmpDir($intProfileID);
			$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
			$strTmpFile = $strTmpDir . '/' . pathinfo(static::CATEGORIES_XLS_URL, PATHINFO_BASENAME);
			if (file_put_contents($strTmpFile, $strFileContent)) {
				require_once(realpath(__DIR__ . '/../../include/php-excel-reader/excel_reader2.php'));
				$obExcelData = new \Spreadsheet_Excel_Reader($strTmpFile, false);
				$intRowCount = $obExcelData->rowcount();
				#
				$strCategories = '';
				for ($intLine = 0; $intLine <= $intRowCount; $intLine++) {
					$strCategories .= $obExcelData->val($intLine, 1) . "\n";
				}
				unset($obExcelData);
				$strCategories = trim($strCategories);
				if (strlen($strCategories)) {
					$strCategories = Helper::convertEncoding($strCategories, 'UTF-8', 'CP1251');
					$strFileName = $this->getCategoriesCacheFile();
					if (is_file($strFileName)) {
						unlink($strFileName);
					}
					if (file_put_contents($strFileName, $strCategories)) {
						$bSuccess = true;
					} else {
						Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES', array('#FILE#' => $strFileName)), $intProfileID);
					}
				} else {
					Log::getInstance($this->strModuleId)->add(static::getMessage('CATEGORIES_ARE_EMPTY', array('#URL#' => static::CATEGORIES_XLS_URL)), $intProfileID);
				}
				@unlink($strTmpFile);
				unset($strCategories, $strFileContent);
			} else {
				Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES_TMP', array('#FILE#' => $strTmpFile)), $intProfileID);
			}
		} else {
			Log::getInstance($this->strModuleId)->add(static::getMessage('CATEGORIES_EMPTY_ANSWER', array('#URL#' => static::CATEGORIES_XLS_URL)), $intProfileID);
		}
		return $bSuccess;
	}

	/**
	 * 	Get categories date update
	 */
	public function getCategoriesDate() {
		$strFileName = $this->getCategoriesCacheFile();
		return is_file($strFileName) ? filemtime($strFileName) : false;
	}

	/**
	 * 	Get categories list
	 */
	public function getCategoriesList($intProfileID) {
		$strFileName = $this->getCategoriesCacheFile();
		if (!is_file($strFileName) || !filesize($strFileName)) {
			$this->updateCategories($intProfileID);
		}
		if (is_file($strFileName) && filesize($strFileName)) {
			$strResult = file_get_contents($strFileName);
			if (Helper::isUtf()) {
				$strResult = Helper::convertEncoding($strResult, 'CP1251', 'UTF-8');
			}
			return explode("\n", $strResult);
		}
		return false;
	}

	/**
	 * 	Get filename for categories cache
	 */
	protected function getCategoriesCacheFile() {
		$strCacheDir = __DIR__ . '/cache';
		if (!is_dir($strCacheDir)) {
			mkdir($strCacheDir, BX_DIR_PERMISSIONS, true);
		}
		return $strCacheDir . '/' . static::CATEGORIES_FILENAME;
	}

	/**
	 * 	Get list of supported currencies
	 */
	public function getSupportedCurrencies() {
		return array('RUB', 'USD', 'EUR', 'UAH', 'KZT', 'BYN');
	}

	/**
	 * 	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID) {
		$arResult = array();
		$arResult[] = array(
				'DIV' => 'promos',
				'TAB' => static::getMessage('TAB_PROMOS_NAME'),
				'TITLE' => static::getMessage('TAB_PROMOS_DESC'),
				'SORT' => 20,
				'FILE' => __DIR__ . '/tabs/promos.php',
		);
		return $arResult;
	}

	public function getAdditionalSubTabs($intProfileID, $intIBlockID) {
		$arResult = array();
		return $arResult;
	}

	/* END OF BASE STATIC METHODS */

	public function getDefaultExportFilename() {
		return 'blizko_ru.xml';
	}

	/**
	 * 	Set available extension
	 */
	protected function setAvailableExtension($strExtension) {
		$this->strFileExt = $strExtension;
	}

	/**
	 * 	Show plugin settings
	 */
	public function showSettings() {
		$this->setAvailableExtension('xml');
		return
						$this->showShopSettings() .
						$this->showDefaultSettings();
	}

	/**
	 * 	Show plugin default settings
	 */
	protected function showDefaultSettings() {
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;" data-role="settings-<?= static::getCode(); ?>">
			<tbody>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('SETTINGS_FILE_HINT')); ?>
						<label for="acrit_exp_plugin_xml_filename">
							<b><?= static::getMessage('SETTINGS_FILE'); ?>:</b>
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?
						\CAdminFileDialog::ShowScript(Array(
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
						));
						?>
						<script>
							function acrit_exp_plugin_xml_filename_select(File, Path, Site) {
								var FilePath = Path + '/' + File;
								$('#acrit_exp_plugin_xml_filename').val(FilePath);
							}
						</script>
						<table class="acrit-exp-plugin-settings-fileselect">
							<tbody>
								<tr>
									<td><input type="text" name="PROFILE[PARAMS][EXPORT_FILE_NAME]"
														 id="acrit_exp_plugin_xml_filename" data-role="export-file-name"
														 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['EXPORT_FILE_NAME']); ?>" size="40"
														 placeholder="<?= static::getMessage('SETTINGS_FILE_PLACEHOLDER'); ?>" /></td>
									<td><input type="button" value="..." onclick="AcritExpPluginXmlFilenameSelect()" /></td>
									<td>
										&nbsp;
										<?= $this->showFileOpenLink(); ?>
										<? if ($this->arProfile['PARAMS']['COMPRESS_TO_ZIP'] == 'Y'): ?>
											<?= $this->showFileOpenLink(Helper::changeFileExt($this->getExportFileName(), 'zip'), 'Zip'); ?>
										<? endif ?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('SETTINGS_ENCODING_HINT')); ?>
						<label for="acrit_exp_plugin_encoding">
							<b><?= static::getMessage('SETTINGS_ENCODING'); ?>:</b>
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?
						$arEncodings = Helper::getAvailableEncodings();
						$arEncodings = array(
								'REFERENCE' => array_values($arEncodings),
								'REFERENCE_ID' => array_keys($arEncodings),
						);
						print SelectBoxFromArray('PROFILE[PARAMS][ENCODING]', $arEncodings, $this->arProfile['PARAMS']['ENCODING'], '', 'id="acrit_exp_plugin_encoding"');
						?>
					</td>
				</tr>
				<tr id="tr_ZIP">
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('SETTINGS_ZIP_HINT')); ?>
						<label for="acrit_exp_plugin_compress_to_zip">
							<?= static::getMessage('SETTINGS_ZIP'); ?>:
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input name="PROFILE[PARAMS][COMPRESS_TO_ZIP]" type="hidden" value="N"/>
						<input name="PROFILE[PARAMS][COMPRESS_TO_ZIP]" type="checkbox" value="Y"
									 <? if ($this->arProfile['PARAMS']['COMPRESS_TO_ZIP'] == 'Y'): ?>checked="checked"<? endif ?>
									 id="acrit_exp_plugin_compress_to_zip" />
					</td>
				</tr>
				<tr id="tr_DELETE_XML_IF_ZIP">
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('SETTINGS_DELETE_XML_IF_ZIP_HINT')); ?>
						<label for="acrit_exp_plugin_delete_xml_if_zip">
							<?= static::getMessage('SETTINGS_DELETE_XML_IF_ZIP'); ?>:
						</label>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input name="PROFILE[PARAMS][DELETE_XML_IF_ZIP]" type="hidden" value="N"/>
						<input name="PROFILE[PARAMS][DELETE_XML_IF_ZIP]" type="checkbox" value="Y"
									 <? if ($this->arProfile['PARAMS']['DELETE_XML_IF_ZIP'] == 'Y'): ?>checked="checked"<? endif ?>
									 id="acrit_exp_plugin_delete_xml_if_zip" />
					</td>
				</tr>
			</tbody>
		</table>
		<script>
			$('[data-role="settings-<?= static::getCode(); ?>"] #tr_ZIP input[type=checkbox]').change(function () {
				var row = $('[data-role="settings-<?= static::getCode(); ?>"] #tr_DELETE_XML_IF_ZIP');
				if ($(this).is(':checked')) {
					row.show();
				} else {
					row.hide();
				}
			}).trigger('change');
		</script>
		<?
		return ob_get_clean();
	}

	/**
	 * 	Show plugin default settings
	 */
	protected function showShopSettings() {
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::showHint(static::getMessage('SHOP_NAME_HINT')); ?>
						<b><?= static::getMessage('SHOP_NAME'); ?>:</b>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][SHOP_NAME]"
									 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_NAME']); ?>" size="20" />
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('SHOP_COMPANY_HINT')); ?>
						<b><?= static::getMessage('SHOP_COMPANY'); ?>:</b>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][SHOP_COMPANY]"
									 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_COMPANY']); ?>" size="50" />
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('DELIVERY_HINT')); ?>
						<?= static::getMessage('DELIVERY'); ?>:
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][DELIVERY][COST]"
									 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['DELIVERY']['COST']); ?>" size="14"
									 placeholder="<?= static::getMessage('DELIVERY_PLACEHOLDER_COST') ?>" />
						<input type="text" name="PROFILE[PARAMS][DELIVERY][DAYS]"
									 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['DELIVERY']['DAYS']); ?>" size="12"
									 placeholder="<?= static::getMessage('DELIVERY_PLACEHOLDER_DAYS') ?>" />
						<input type="text" name="PROFILE[PARAMS][DELIVERY][ORDER_BEFORE]"
									 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['DELIVERY']['ORDER_BEFORE']); ?>" size="12"
									 placeholder="<?= static::getMessage('DELIVERY_PLACEHOLDER_ORDER_BEFORE') ?>" />
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('ENABLE_AUTO_DISCOUNTS_HINT')); ?>
						<?= static::getMessage('ENABLE_AUTO_DISCOUNTS'); ?>:
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input name="PROFILE[PARAMS][ENABLE_AUTO_DISCOUNTS]" type="hidden" value="N"/>
						<input name="PROFILE[PARAMS][ENABLE_AUTO_DISCOUNTS]" type="checkbox" value="Y"
									 <? if ($this->arProfile['PARAMS']['ENABLE_AUTO_DISCOUNTS'] == 'Y'): ?>checked="checked"<? endif ?>
									 id="acrit_exp_enable_auto_discounts" />
					</td>
				</tr>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
		$arResult = array();
		if ($bAdmin) {
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
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'CATALOG_VAT_VALUE',
						),
				),
		));
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
		if ($bAdmin) {
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
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'CONST',
								'CONST' => '500',
						),
				),
		));
		$arResult[] = new Field(array(
				'CODE' => 'DELIVERY_OPTIONS_DAYS',
				'DISPLAY_CODE' => 'delivery-options -> days',
				'NAME' => static::getMessage('FIELD_DELIVERY_OPTIONS_DAYS_NAME'),
				'SORT' => 1800,
				'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_OPTIONS_DAYS_DESC'),
				'REQUIRED' => false,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'CONST',
								'CONST' => '2-4',
						),
				),
		));
		$arResult[] = new Field(array(
				'CODE' => 'DELIVERY_OPTIONS_ORDER_BEFORE',
				'DISPLAY_CODE' => 'delivery-options -> order-before',
				'NAME' => static::getMessage('FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_NAME'),
				'SORT' => 1900,
				'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_DESC'),
				'REQUIRED' => false,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'CONST',
								'CONST' => '13',
						),
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
		if ($bAdmin) {
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
		#
		if ($bAdmin) {
			$arResult[] = new Field(array(
					'SORT' => 3450,
					'CODE' => 'header_n_plus_m',
					'NAME' => static::getMessage('HEADER_N_PLUS_M'),
					'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
				'CODE' => 'N_PLUS_M',
				'DISPLAY_CODE' => ' ',
				'NAME' => static::getMessage('FIELD_N_PLUS_M_NAME'),
				'SORT' => 3451,
				'DESCRIPTION' => static::getMessage('FIELD_N_PLUS_M_DESC'),
		));
		$arResult[] = new Field(array(
				'CODE' => 'N_PLUS_M_REQUIRED_QUANTITY',
				'DISPLAY_CODE' => ' ',
				'NAME' => static::getMessage('FIELD_N_PLUS_M_REQUIRED_QUANTITY_NAME'),
				'SORT' => 3452,
				'DESCRIPTION' => static::getMessage('FIELD_N_PLUS_M_REQUIRED_QUANTITY_DESC'),
		));
		$arResult[] = new Field(array(
				'CODE' => 'N_PLUS_M_FREE_QUANTITY',
				'DISPLAY_CODE' => ' ',
				'NAME' => static::getMessage('FIELD_N_PLUS_M_FREE_QUANTITY_NAME'),
				'SORT' => 3453,
				'DESCRIPTION' => static::getMessage('FIELD_N_PLUS_M_FREE_QUANTITY_DESC'),
		));
		#
		if ($bAdmin) {
			$arResult[] = new Field(array(
					'SORT' => 3499,
					'CODE' => 'header_gifts',
					'NAME' => static::getMessage('HEADER_GIFTS'),
					'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
				'CODE' => 'GIFTS_ID',
				'DISPLAY_CODE' => ' ',
				'NAME' => static::getMessage('FIELD_GIFTS_ID_NAME'),
				'SORT' => 3500,
				'DESCRIPTION' => static::getMessage('FIELD_GIFTS_ID_DESC'),
				'REQUIRED' => false,
				'MULTIPLE' => true,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'PROPERTY_GIFTS',
								'PARAMS' => array(
										'RAW' => 'Y',
										'MULTIPLE' => 'Y',
								),
						),
				),
				'DEFAULT_VALUE_OFFERS' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'PARENT.PROPERTY_GIFTS',
								'PARAMS' => array(
										'RAW' => 'Y',
										'MULTIPLE' => 'Y',
								),
						),
				),
				'PARAMS' => array(
						'MULTIPLE' => 'Y',
				),
		));
		$arResult[] = new Field(array(
				'CODE' => 'GIFTS_DESCRIPTION',
				'DISPLAY_CODE' => ' ',
				'NAME' => static::getMessage('FIELD_GIFTS_DESCRIPTION_NAME'),
				'SORT' => 3501,
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
				'DISPLAY_CODE' => ' ',
				'NAME' => static::getMessage('FIELD_GIFTS_URL_NAME'),
				'SORT' => 3502,
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
		#
		if ($bAdmin) {
			$arResult[] = new Field(array(
					'SORT' => 3999,
					'NAME' => static::getMessage('HEADER_ADDITIONAL_FIELDS'),
					'IS_HEADER' => true,
			));
		}
		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]);
		foreach ($arAdditionalFields as $arAdditionalField) {
			$arDefaultValue = null;
			if (strlen($arAdditionalField['DEFAULT_FIELD'])) {
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
		# More fields are in each format (see getFields)
		return $arResult;
	}

	/**
	 * 	Process single element
	 * 	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
		// basically [in this class] do nothing, all business logic are in each format
	}

	/**
	 * 	Show results
	 */
	public function showResults($arSession) {
		ob_start();
		$intTime = $arSession['TIME_FINISHED'] - $arSession['TIME_START'];
		if ($intTime <= 0) {
			$intTime = 1;
		}
		?>
		<div><?= static::getMessage('RESULT_GENERATED'); ?>: <?= IntVal($arSession['GENERATE']['INDEX']); ?></div>
		<div><?= static::getMessage('RESULT_EXPORTED'); ?>: <?= IntVal($arSession['EXPORT']['INDEX']); ?></div>
		<div><?= static::getMessage('RESULT_ELAPSED_TIME'); ?>: <?= Helper::formatElapsedTime($intTime); ?></div>
		<div><?= static::getMessage('RESULT_DATETIME'); ?>: <?= (new \Bitrix\Main\Type\DateTime())->toString(); ?></div>
		<?= $this->showFileOpenLink(); ?>
		<?= $this->showFileOpenLink($arSession['EXPORT']['XML_FILE_URL_ZIP'], static::getMessage('RESULT_FILE_ZIP')); ?>
		<?
		return Helper::showSuccess(ob_get_clean());
	}

	/* START OF BASE METHODS FOR XML SUBCLASSES */

	/**
	 * 	Get steps
	 */
	public function getSteps() {
		$arResult = array();
		$arResult['CHECK'] = array(
				'NAME' => static::getMessage('ACRIT_EXP_EXPORTER_STEP_CHECK'),
				'SORT' => 10,
				#'FUNC' => __CLASS__ . '::stepCheck',
				'FUNC' => array($this, 'stepCheck'),
		);
		$arResult['EXPORT'] = array(
				'NAME' => static::getMessage('STEP_EXPORT'),
				'SORT' => 100,
				#'FUNC' => __CLASS__ . '::stepExport',
				'FUNC' => array($this, 'stepExport'),
		);
		if ($this->arProfile['PARAMS']['COMPRESS_TO_ZIP'] == 'Y') {
			$arResult['ZIP'] = array(
					'NAME' => static::getMessage('STEP_ZIP'),
					'SORT' => 110,
					#'FUNC' => __CLASS__ . '::stepZip',
					'FUNC' => array($this, 'stepZip'),
			);
		}
		return $arResult;
	}

	/**
	 * 	Step: Check input params and data
	 */
	public function stepCheck($intProfileID, $arData) {
		$strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		if (!strlen($strExportFilename)) {
			Log::getInstance($this->strModuleId)->add(static::getMessage('NO_EXPORT_FILE_SPECIFIED'), $intProfileID);
			print static::getMessage('NO_EXPORT_FILE_SPECIFIED');
			return Exporter::RESULT_ERROR;
		}
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * 	Step: Export
	 */
	public function stepExport($intProfileID, $arData) {
		$arSession = &$arData['SESSION']['EXPORT'];
		$bIsCron = $arData['IS_CRON'];
		#
		$strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		#
		if (!isset($arSession['XML_FILE'])) {
			#$strTmpDir = Profile::getTmpDir($intProfileID);
			$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
			$strTmpFile = pathinfo($strExportFilename, PATHINFO_BASENAME) . '.tmp';
			$arSession['XML_FILE_URL'] = $strExportFilename;
			$arSession['XML_FILE'] = $_SERVER['DOCUMENT_ROOT'] . $strExportFilename;
			$arSession['XML_FILE_TMP'] = $strTmpDir . '/' . $strTmpFile;
			#
			if ($arData['PROFILE']['PARAMS']['COMPRESS_TO_ZIP'] == 'Y') {
				$arSession['XML_FILE_ZIP'] = Helper::changeFileExt($_SERVER['DOCUMENT_ROOT'] . $strExportFilename, 'zip');
				$arSession['XML_FILE_URL_ZIP'] = Helper::changeFileExt($strExportFilename, 'zip');
			}
			if (is_file($arSession['XML_FILE_TMP'])) {
				unlink($arSession['XML_FILE_TMP']);
			}
			touch($arSession['XML_FILE_TMP']);
			unset($strTmpDir, $strTmpFile);
		}

		# SubStep1 [header]
		if (!isset($arSession['XML_HEADER_WROTE'])) {
			$this->stepExport_writeXmlHeader($intProfileID, $arData);
			$arSession['XML_HEADER_WROTE'] = true;
		}

		# SubStep2 [<shop>]
		if (!isset($arSession['XML_SHOP_WROTE'])) {
			$this->stepExport_writeXmlShop($intProfileID, $arData);
			$arSession['XML_SHOP_WROTE'] = true;
		}

		# SubStep2 [<currencies>]
		if (!isset($arSession['XML_CURRENCIES_WROTE'])) {
			$this->stepExport_writeXmlCurrencies($intProfileID, $arData);
			$arSession['XML_CURRENCIES_WROTE'] = true;
		}

		# SubStep3 [<categories>]
		if (!isset($arSession['XML_CATEGORIES_WROTE'])) {
			$this->stepExport_writeXmlCategories($intProfileID, $arData);
			$arSession['XML_CATEGORIES_WROTE'] = true;
		}

		# SubStep4 [<delivery-options>]
		if (!isset($arSession['XML_OFFERS_DELIVERY_OPTIONS_WROTE'])) {
			$this->stepExport_writeXmlDeliveryOptions($intProfileID, $arData);
			$arSession['XML_OFFERS_DELIVERY_OPTIONS_WROTE'] = true;
		}

		# SubStep5 [<enable_auto_discounts>]
		if (!isset($arSession['XML_OFFERS_ENABLE_AUTO_DISCOUNTS_WROTE'])) {
			$this->stepExport_writeXmlAutoDiscounts($intProfileID, $arData);
			$arSession['XML_OFFERS_ENABLE_AUTO_DISCOUNTS_WROTE'] = true;
		}

		# SubStep6 [each <offer>]
		if (!isset($arSession['XML_OFFERS_WROTE'])) {
			$this->stepExport_writeXmlOffers($intProfileID, $arData);
			$arSession['XML_OFFERS_WROTE'] = true;
		}

		# SubStep7 [<gifts>]
		$bGiftsExport = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_GIFTS'] == 'Y';
		if ($bGiftsExport && !isset($arSession['XML_GIFTS_WROTE'])) {
			$this->stepExport_writeXmlGifts($intProfileID, $arData);
			$arSession['XML_GIFTS_WROTE'] = true;
		}
		$bPromocodesExport = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES'] == 'Y';
		# SubStep8 [<promos>]
		if (($bGiftsExport || $bPromocodesExport) && !isset($arSession['XML_PROMOS_WROTE'])) {
			$this->stepExport_writeXmlPromos($intProfileID, $arData);
		}

		# SubStep9 [footer]
		if (!isset($arSession['XML_FOOTER_WROTE'])) {
			$this->stepExport_writeXmlFooter($intProfileID, $arData);
			$arSession['XML_FOOTER_WROTE'] = true;
		}

		# SubStep10 [tmp => real]
		if (is_file($arSession['XML_FILE'])) {
			unlink($arSession['XML_FILE']);
		}
		if (!Helper::createDirectoriesForFile($arSession['XML_FILE'])) {
			$strMessage = Loc::getMessage('ACRIT_EXP_ERROR_CREATE_DIRECORY', array(
									'#DIR#' => Helper::getDirectoryForFile($arSession['XML_FILE']),
			));
			Log::getInstance($this->strModuleId)->add($strMessage);
			print Helper::showError($strMessage);
			return Exporter::RESULT_ERROR;
		}
		if (is_file($arSession['XML_FILE'])) {
			@unlink($arSession['XML_FILE']);
		}
		if (!@rename($arSession['XML_FILE_TMP'], $arSession['XML_FILE'])) {
			@unlink($arSession['XML_FILE_TMP']);
			$strMessage = Loc::getMessage('ACRIT_EXP_FILE_NO_PERMISSIONS', array(
									'#FILE#' => $arSession['XML_FILE'],
			));
			Log::getInstance($this->strModuleId)->add($strMessage);
			print Helper::showError($strMessage);
			return Exporter::RESULT_ERROR;
		}

		# SubStep11
		$arSession['EXPORT_FILE_SIZE_XML'] = filesize($arSession['XML_FILE']);

		#
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * 	Step: Export, write header
	 */
	protected function stepExport_writeXmlHeader($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strEncoding = $arData['PROFILE']['PARAMS']['ENCODING'];
		#
		$strDate = (new \Bitrix\Main\Type\DateTime())->format('Y-m-d H:i');
		$strXml = '';
		$strXml .= '<?xml version="1.0" encoding="' . $strEncoding . '"?>' . "\n";
		$strXml .= '<yml_catalog date="' . $strDate . '">' . "\n";
		$strXml .= "\t" . '<shop>' . "\n";
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write shop
	 */
	protected function stepExport_writeXmlShop($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$arXml = array(
				'name' => array(
						'#' => $arData['PROFILE']['PARAMS']['SHOP_NAME'],
				),
				'company' => array(
						'#' => $arData['PROFILE']['PARAMS']['SHOP_COMPANY'],
				),
				'platform' => array(
						'#' => Loc::getMessage('ACRIT_EXP_PLATFORM_NAME'),
				),
				'version' => array(
						'#' => SM_VERSION,
				),
				'url' => array(
						'#' => Helper::siteUrl($arData['PROFILE']['DOMAIN'], $arData['PROFILE']['IS_HTTPS'] == 'Y'),
				),
		);
		$strXml = Xml::arrayToXml($arXml, $intDepthLevel = 3);
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write currencies
	 */
	protected function stepExport_writeXmlCurrencies($intProfileID, $arData) {
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
		while ($arItem = $resItems->fetch()) {
			$arCurrency = explode(',', $arItem['CURRENCY']);
			Helper::arrayRemoveEmptyValues($arCurrency);
			foreach ($arCurrency as $strCurrency) {
				$arCurrencies[$strCurrency] = array(
						'CURRENCY' => $strCurrency,
						'RATE' => 1,
				);
			}
		}
		#
		$arCurrencyAll = Helper::getCurrencyList();
		$strBaseCurrency = $arData['PROFILE']['PARAMS']['CURRENCY']['TARGET_CURRENCY'];
		if (!in_array($strBaseCurrency, array('RUB', 'RUR', 'BYN', 'UAH', 'KZT'))) {
			$strBaseCurrency = '';
		}
		if (!strlen($strBaseCurrency) || !array_key_exists($strBaseCurrency, $arCurrencyAll)) {
			foreach ($arCurrencyAll as $arCurrency) {
				if ($arCurrency['IS_BASE']) {
					$strBaseCurrency = $arCurrency['CURRENCY'];
				}
			}
		}
		$arCurrencyConverter = CurrencyConverterBase::getConverterList();
		#
		foreach ($arCurrencies as $key => $arCurrency) {
			if ($arCurrency['CURRENCY'] == $strBaseCurrency) {
				$strRate = 1;
			} else {
				$strRatesSource = $arData['PROFILE']['PARAMS']['CURRENCY']['RATES_SOURCE'];
				$strRate = '1.00';
				if (strlen($strRatesSource) && is_array($arCurrencyConverter[$strRatesSource])) {
					$strClass = $arCurrencyConverter[$strRatesSource]['CLASS'];
					if (class_exists($strClass)) {
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
		$strXml = Xml::arrayToXml($arXml, $intDepthLevel = 3);
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write categories
	 */
	protected function stepExport_writeXmlCategories($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];

		# All categories for XML
		$arCategoriesForXml = array();

		# Get category redefinitions all
		#$arCategoryRedefinitionsAll = CategoryRedefinition::getForProfile($intProfileID);
		$arCategoryRedefinitionsAll = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$intProfileID]);

		# All sections ID for export
		$arSectionsForExportAll = array();

		# Process each used IBlocks
		foreach ($arData['PROFILE']['IBLOCKS'] as $intIBlockID => $arIBlockSettings) {
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
			while ($arItem = $resItems->fetch()) {
				$arItemSectionsID = array();
				if (is_numeric($arItem['SECTION_ID']) && $arItem['SECTION_ID'] > 0) {
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
				foreach ($arItemSectionsID as $intSectionID) {
					if (!in_array($intSectionID, $arUsedSectionsID)) {
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

		if (!empty($arSectionsForExportAll)) {
			$arSectionsAll = array();
			$resSections = \CIBlockSection::getList(array(
									'ID' => 'ASC',
											), array(
									'ID' => $arSectionsForExportAll,
											), false, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
			while ($arSection = $resSections->getNext(false, false)) {
				$arSection['ID'] = IntVal($arSection['ID']);
				$arSectionsAll[$arSection['ID']] = array(
						'NAME' => $arSection['NAME'],
						'PARENT_ID' => IntVal($arSection['IBLOCK_SECTION_ID']),
				);
			}
			$arSectionsForExportAll = $arSectionsAll;
			unset($arSectionsAll, $resSections, $arSection);
		}

		switch ($arData['PROFILE']['PARAMS']['CATEGORIES_REDEFINITION_MODE']) {
			// ����� "������������ ��������� �������� ��������"
			case CategoryRedefinition::MODE_STRICT:
				#
				$strSeparator = '/';
				foreach ($arSectionsForExportAll as $intSectionID => $arSection) {
					if (isset($arCategoryRedefinitionsAll[$intSectionID])) {
						$arSectionsForExportAll[$intSectionID]['NAME'] = $arCategoryRedefinitionsAll[$intSectionID];
					}
				}

				foreach ($arSectionsForExportAll as $intSectionID => $arSection) {
					unset($arSectionsForExportAll[$intSectionID]['PARENT_ID']);
					$arSectionName = explode($strSeparator, $arSection['NAME']);
					Helper::pathArray($arSectionName, $strSeparator);
					$strLastName = end($arSectionName);
					foreach ($arSectionName as $strSectionNamePath) {
						# Search and add if not exists
						$intFoundSectionID = false;
						foreach ($arSectionsForExportAll as $intSectionID_1 => $arSection_1) {
							if ($arSection_1['NAME'] == $strSectionNamePath) {
								$intFoundSectionID = $intSectionID_1;
								break;
							}
						}
						#
						if (!$intFoundSectionID) {
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
				foreach ($arSectionsForExportAll as $intCategoryID => $arCategory) {
					$intParentID = false;
					$strCategoryName = $arCategory['NAME'];
					$intSlashPos = strrpos($strCategoryName, '/');
					if ($intSlashPos !== false) {
						$strCategoryParentName = substr($strCategoryName, 0, $intSlashPos);
						$strCategoryName = substr($strCategoryName, $intSlashPos + 1);
						# searching..
						foreach ($arSectionsForExportAll as $intCategoryID_1 => $arCategory_1) {
							if ($arCategory_1['NAME'] == $strCategoryParentName) {
								$intParentID = $intCategoryID_1;
								break;
							}
						}
					}
					$arCategory = array(
							'@' => array('id' => $intCategoryID),
							'#' => htmlspecialcharsbx($strCategoryName),
					);
					if ($intParentID) {
						$arCategory['@']['parentId'] = $intParentID;
					}
					$arCategoriesXml[] = $arCategory;
				}
				#
				break;
			// ����� "������������ ��������� �����"
			case CategoryRedefinition::MODE_CUSTOM:
				# Categories to XML array
				$arCategoriesXml = array();
				foreach ($arSectionsForExportAll as $intCategoryID => $arCategory) {
					if ($arData['PROFILE']['PARAMS']['CATEGORIES_EXPORT_PARENTS'] == 'Y') {
						$resSectionsChain = \CIBlockSection::getNavChain(false, $intCategoryID, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
						while ($arSectionsChain = $resSectionsChain->getNext()) {
							$arCategoryXml = array(
									'@' => array('id' => $arSectionsChain['ID']),
									'#' => htmlspecialcharsbx($arSectionsChain['NAME']),
							);
							if ($arSectionsChain['IBLOCK_SECTION_ID']) {
								$arCategoryXml['@']['parentId'] = $arSectionsChain['IBLOCK_SECTION_ID'];
							}
							$arCategoriesXml[$arSectionsChain['ID']] = $arCategoryXml;
						}
						unset($resSectionsChain, $arSectionsChain, $arCategoryXml);
					} else {
						$intParentID = false;
						$strCategoryName = $arCategory['NAME'];
						$arCategory = array(
								'@' => array('id' => $intCategoryID),
								'#' => htmlspecialcharsbx($strCategoryName),
						);
						if ($intParentID) {
							$arCategory['@']['parentId'] = $intParentID;
						}
						$arCategoriesXml[] = $arCategory;
					}
				}
				break;
		}

		# Sort categories
		usort($arCategoriesXml, __CLASS__ . '::usortCategoriesCallback');

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
		$strXml = Xml::arrayToXml($arXml, $intDepthLevel = 3);
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write delivery options
	 */
	protected function stepExport_writeXmlDeliveryOptions($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strCost = trim($arData['PROFILE']['PARAMS']['DELIVERY']['COST']);
		$strDays = trim($arData['PROFILE']['PARAMS']['DELIVERY']['DAYS']);
		$strOrderBefore = trim($arData['PROFILE']['PARAMS']['DELIVERY']['ORDER_BEFORE']);
		#
		if (strlen($strCost) || strlen($strDays) || strlen($strOrderBefore)) {
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
			$strXml = rtrim($strXml) . "\n";
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
		}
	}

	/**
	 * 	Step: Export, write enable_auto_discounts
	 */
	protected function stepExport_writeXmlAutoDiscounts($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		if ($arData['PROFILE']['PARAMS']['ENABLE_AUTO_DISCOUNTS'] == 'Y') {
			$arXml = array(
					'enable_auto_discounts' => Xml::addTag('yes'),
			);
			$strXml = Xml::arrayToXml($arXml);
			$strXml = Xml::addOffset($strXml, 2);
			$strXml = rtrim($strXml) . "\n";
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
		}
	}

	/**
	 * 	Step: Export, write offers
	 * 	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml .= "\t\t" . '<offers>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
		#
		$intLimit = 5000;
		$intOffset = 0;
		while (true) {
			$strSortOrder = ToUpper($arData['PROFILE']['PARAMS']['SORT_ORDER']);
			if (!in_array($strSortOrder, array('ASC', 'DESC'))) {
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
			while ($arItem = $resItems->fetch()) {
				$intCount++;
				$arItem['DATA'] = rtrim(Xml::addOffset($arItem['DATA'], 3)) . "\n";
				$strXml .= $arItem['DATA'];
			}
			$arData['SESSION']['EXPORT']['INDEX'] += $intCount;
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
			if ($intCount < $intLimit) {
				break;
			}
			$intOffset++;
		}
		#
		$strXml = '';
		$strXml .= "\t\t" . '</offers>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write gifts
	 */
	protected function stepExport_writeXmlGifts($intProfileID, $arData) {
		$arSession = &$arData['SESSION']['EXPORT'];
		$strFile = $arSession['XML_FILE_TMP'];
		#
		# 1. find all gifts id
		$arGiftIDs = array();
		$intOffset = 0;
		$intLimit = 5000;
		while (true) {
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
			while ($arItem = $resItems->fetch()) {
				$intCount++;
				$arDataMore = unserialize($arItem['DATA_MORE']);
				if (is_array($arDataMore['GIFTS']) && !empty($arDataMore['GIFTS']['ID'])) {
					// array('123', '124', '125')
					if (is_array($arDataMore['GIFTS']['ID'])) {
						foreach ($arDataMore['GIFTS']['ID'] as $intGiftID) {
							if (is_numeric($intGiftID) && $intGiftID > 0 && !in_array($intGiftID, $arGiftIDs)) {
								$arGiftIDs[] = $intGiftID;
							}
						}
					}
					// '123'
					elseif (is_numeric($arDataMore['GIFTS']['ID']) && $arDataMore['GIFTS']['ID'] > 0 && !in_array($arDataMore['GIFTS']['ID'], $arGiftIDs)) {
						$arGiftIDs[] = $arDataMore['GIFTS']['ID'];
					}
					// '123,124'
					elseif (strlen($arDataMore['GIFTS']['ID']) && preg_match('#$[0-9,]^#i', $arDataMore['GIFTS']['ID'])) {
						foreach (explode(',', $arDataMore['GIFTS']['ID']) as $intGiftID) {
							if (is_numeric($intGiftID) && $intGiftID > 0 && !in_array($intGiftID, $arGiftIDs)) {
								$arGiftIDs[] = $intGiftID;
							}
						}
					}
				}
			}
			if ($intCount < $intLimit) {
				break;
			}
			$intOffset++;
		}
		# 2. Get gifts that are in <offers>
		$arGiftIDsInOffers = &$arSession['GIFTS_ID_IN_OFFERS'];
		$arGiftIDsInOffers = array();
		$intOffset = 0;
		$intLimit = 5000;
		while (true) {
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
			while ($arItem = $resItems->fetch()) {
				$intCount++;
				$arGiftIDsInOffers[] = $arItem['ELEMENT_ID'];
			}
			if ($intCount < $intLimit) {
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
		if (!empty($arGiftIDsNotInOffers) && \Bitrix\Main\Loader::includeModule('iblock')) {
			# 3.1 write xml <gifts>
			$strXml .= "\t\t" . '<gifts>' . "\r\n";
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
			while ($arItem = $resItems->getNext()) {
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
				if (is_numeric($arItem['DETAIL_PICTURE'])) {
					$strPicture = \CFile::getPath($arItem['DETAIL_PICTURE']);
				} elseif (is_numeric($arItem['PREVIEW_PICTURE'])) {
					$strPicture = \CFile::getPath($arItem['DETAIL_PICTURE']);
				}
				if (strlen($strPicture)) {
					$strDomain = Helper::siteUrl($arData['PROFILE']['DOMAIN'], $arData['PROFILE']['IS_HTTPS'] == 'Y');
					$arXml['gift']['#']['picture'] = Xml::addTag($strDomain . $strPicture);
				}
				$strXml .= rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3)) . "\n";
				$arGiftIDsExported[] = $arItem['ID'];
			}
			$strXml .= "\t\t" . '</gifts>' . "\r\n";
			file_put_contents($strFile, $strXml, FILE_APPEND);
		}
		#
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * 	Step: Export, write promos
	 */
	protected function stepExport_writeXmlPromos($intProfileID, $arData) {
		$arSession = &$arData['SESSION']['EXPORT'];
		$strFile = $arSession['XML_FILE_TMP'];
		#
		$arGiftIDsExported = &$arSession['GIFTS_ID_EXPORTED']; // in <gifts>
		$arGiftIDsInOffers = &$arSession['GIFTS_ID_IN_OFFERS']; // in <offers>
		#
		$strXml = "\t\t" . '<promos>' . "\r\n";


		$bExportPromoCodes = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES'] == 'Y';
		$bExportSpecialPrice = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE'] == 'Y';
		$bExportNPlusM = $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M'] == 'Y';

		# Export promocodes
		if ($bExportPromoCodes && count($arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS'])) {
			$arDiscounts = $arDiscountSections = $arDiscountElements = $arDiscountUnits = [];
			$db = \Bitrix\Sale\Internals\DiscountTable::getList(array(
									'filter' => array('ID' => $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_PROMOCODES_FIELDS'], 'ACTIVE' => 'Y')
			));
			while ($arRes = $db->fetch()) {
				$discountId = $arRes['ID'];
				$arDiscounts[$discountId] = $arRes;

				// collect Elements and Sections from discount
				// with sort collected items by discount unit and value
				// because discount can include many discount values and units
				foreach ($arRes['ACTIONS_LIST']['CHILDREN'] as $arCondition) {
					$discountValue = '';
					$discountUnit = $arCondition['DATA']['Unit'];
					if ($arCondition['DATA']['Type'] == 'Discount') {
						$discountValue = $discountUnit . $arCondition['DATA']['Value'];
						if (!$arDiscountUnits[$discountId][$discountValue]) {
							$arDiscountUnits[$discountId][$discountValue] = $arCondition['DATA']['Value'];
						}
					} else {
						continue;
					}
					foreach ($arCondition['CHILDREN'] as $subCondition) {
						if ($subCondition['CLASS_ID'] == 'CondIBElement' && $subCondition['DATA']['logic'] == 'Equal') {
							if (!$arDiscountElements[$discountId][$discountValue]) {
								$arDiscountElements[$discountId][$discountValue] = [];
							}
							$arDiscountElements[$discountId][$discountValue] = array_merge($subCondition['DATA']['value'], $arDiscountElements[$discountId][$discountValue]);
						}
						if ($subCondition['CLASS_ID'] == 'CondIBSection' && $subCondition['DATA']['logic'] == 'Equal') {
							$arDiscountSections[$discountId][$discountValue][] = $subCondition['DATA']['value'];
						}
					}
				}
			}

			$arDiscountsCoupons = [];
			$db = \Bitrix\Sale\Internals\DiscountCouponTable::getList(array(
									'filter' => array('DISCOUNT_ID' => array_keys($arDiscounts), 'ACTIVE' => 'Y'),
			));
			while ($arRes = $db->fetch()) {
				$arDiscountsCoupons[$discountId] = $arRes;
			}
			foreach ($arDiscountsCoupons as $couponId => $arCoupon) {

				$arDiscount = $arDiscounts[$arCoupon['DISCOUNT_ID']];

				$startDate = ($arCoupon['ACTIVE_FROM']) ? $arCoupon['ACTIVE_FROM'] : $arDiscount['ACTIVE_FROM'];
				$endDate = ($arCoupon['ACTIVE_TO']) ? $arCoupon['ACTIVE_TO'] : $arDiscount['ACTIVE_TO'];

				foreach ($arDiscountUnits[$arDiscount['ID']] as $discountValue => $discount) {
					$unit = (strpos($discountValue, 'Perc') !== false) ? 'percent' : 'currency';
					$xmlProducts = [];
					foreach ($arDiscountElements[$arDiscount['ID']][$discountValue] as $id) {
						$xmlProducts[] = array('@' => array('offer-id' => $id));
					}
					foreach ($arDiscountSections[$arDiscount['ID']][$discountValue] as $id) {
						$xmlProducts[] = array('@' => array('category-id' => $id));
					}
					$arXml = array(
							'promo' => array(
									'@' => array(
											'id' => 'PromoCode_' . $discountValue . '_' . $arCoupon['ID'],
											'type' => 'promo code',
									),
									'#' => array(
											'start-date' => array(
													array('#' => $startDate),
											),
											'end-date' => array(
													array('#' => $endDate),
											),
											'description' => array(
													array('#' => $arCoupon['DESCRIPTION']),
											),
											'promo-code' => array(
													array('#' => $arCoupon['COUPON']),
											),
											'discount' => array(
													array(
															'#' => $discount,
															'@' => array(
																	'unit' => $unit,
																	'currency' => $arDiscount['CURRENCY'],
															),
													),
											),
											'purchase' => array(
													array(
															'#' => array(
																	'product' => $xmlProducts
															),
													),
											),
									),
							),
					);
					$strXml .= rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3)) . "\n";
				}
			}
		}

		# 2. export special price
		$arSpecialPrice = array();
		if ($bExportSpecialPrice || $bExportNPlusM) {

			$intOffset = 0;
			$intLimit = 5000;
			while (true) {
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
				$intCount = 0;
				while ($arItem = $resItems->fetch()) {
					$intCount++;
					$arDataMore = unserialize($arItem['DATA_MORE']);
					if (!empty($arDataMore['SPECIAL_PRICE']['ID'])) {
						$arSpecialPrice[] = $arDataMore['SPECIAL_PRICE'];
					}
					if (!empty($arDataMore['N_PLUS_M']['ID'])) {
						$arNPlusM[] = $arDataMore['N_PLUS_M'];
					}
				}
				if ($intCount < $intLimit) {
					break;
				}
				$intOffset++;
			}

			if (count($arSpecialPrice)) {

				$strXml .= "\t\t" . '<promo id="' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_ACTION_ID'] . '" type="flash discount">' . "\r\n";

				$strXml .= "\t\t" . '<start-date>' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_DATE_START'] . '</start-date>' . "\r\n";
				$strXml .= "\t\t" . '<end-date>' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_DATE_END'] . '</end-date>' . "\r\n";
				$strXml .= "\t\t" . '<description>' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_DESCRIPTION'] . '</description>' . "\r\n";
				$strXml .= "\t\t" . '<url>' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_SPECIAL_PRICE_URL'] . '</url>' . "\r\n";
				$strXml .= "\t\t" . '<purchase>' . "\r\n";


				foreach ($arSpecialPrice as $arItem) {

					$arXml = array(
							'product' => array(
									'@' => array(
											'offer-id' => $arItem['ID'],
									),
									'#' => array(
											'discount-price' => array(
													array(
															'@' => array(
																	'currency' => $arItem['CURRENCY_ID'],
															),
															'#' => $arItem['PRICE']
													),
											),
									),
							),
					);

					$strXml .= rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3)) . "\n";
				}
				$strXml .= "\t\t" . '</purchase>' . "\r\n";
				$strXml .= "\t\t" . '</promo>' . "\r\n";
			}
			if (count($arNPlusM)) {

				$strXml .= "\t\t" . '<promo id="' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_ACTION_ID'] . '" type="n plus m">' . "\r\n";

				$strXml .= "\t\t" . '<start-date>' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_DATE_START'] . '</start-date>' . "\r\n";
				$strXml .= "\t\t" . '<end-date>' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_DATE_END'] . '</end-date>' . "\r\n";
				$strXml .= "\t\t" . '<description>' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_DESCRIPTION'] . '</description>' . "\r\n";
				$strXml .= "\t\t" . '<url>' . $arData['PROFILE']['PARAMS']['YANDEX_MARKET_EXPORT_N_PLUS_M_URL'] . '</url>' . "\r\n";
				$strXml .= "\t\t" . '<purchase>' . "\r\n";


				foreach ($arNPlusM as $arItem) {

					$arXml = array(
							'product' => array(
									'@' => array(
											'offer-id' => $arItem['ID'],
									),
							),
							'required-quantity' => array(
									'#' => array(
											$arItem['REQUIRED_QUANTITY']
									),
							),
							'free-quantity' => array(
									'#' => array(
											$arItem['FREE_QUANTITY']
									),
							),
					);

					$strXml .= rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3)) . "\n";
				}
				$strXml .= "\t\t" . '</purchase>' . "\r\n";
				$strXml .= "\t\t" . '</promo>' . "\r\n";
			}
		}


		# 3. Export gifts

		if (!empty($arGiftIDsInOffers)) {


			# Find all gifts with its element
			$intOffset = 0;
			$intLimit = 5000;
			while (true) {
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

				$intCount = 0;
				while ($arItem = $resItems->fetch()) {
					$arGiftIDs = array();
					$intCount++;
					$arDataMore = unserialize($arItem['DATA_MORE']);
					if (is_array($arDataMore['GIFTS']) && !empty($arDataMore['GIFTS']['ID'])) {
						// array('123', '124', '125')
						if (is_array($arDataMore['GIFTS']['ID'])) {
							foreach ($arDataMore['GIFTS']['ID'] as $intGiftID) {
								if (is_numeric($intGiftID) && $intGiftID > 0 && !in_array($intGiftID, $arGiftIDs)) {
									$arGiftIDs[] = $intGiftID;
								}
							}
						}
						// '123'
						elseif (is_numeric($arDataMore['GIFTS']['ID']) && $arDataMore['GIFTS']['ID'] > 0 && !in_array($arDataMore['GIFTS']['ID'], $arGiftIDs)) {
							$arGiftIDs[] = $arDataMore['GIFTS']['ID'];
						}
						// '123,124'
						elseif (strlen($arDataMore['GIFTS']['ID']) && preg_match('#$[0-9,]^#i', $arDataMore['GIFTS']['ID'])) {
							foreach (explode(',', $arDataMore['GIFTS']['ID']) as $intGiftID) {
								if (is_numeric($intGiftID) && $intGiftID > 0 && !in_array($intGiftID, $arGiftIDs)) {
									$arGiftIDs[] = $intGiftID;
								}
							}
						}

						# Do export
						$arXml = array(
								'promo' => array(
										'@' => array(
												'id' => 'PromoGift_' . $arItem['ELEMENT_ID'],
												'type' => 'gift with purchase',
										),
										'#' => array(
												'description' => array(
														array('#' => $arDataMore['GIFTS']['DESCRIPTION']),
												),
												'url' => array(
														array('#' => $arDataMore['GIFTS']['URL']),
												),
												'purchase' => array(
														array(
																'#' => array(
																		'product' => array(
																				array(
																						'@' => array('offer-id' => $arItem['ELEMENT_ID']),
																				),
																		),
																),
														),
												),
												'promo-gifts' => array(
														array(
																'#' => array(
																		'promo-gift' => array(),
																),
														),
												),
										),
								),
						);
						foreach (array_intersect($arGiftIDs, $arGiftIDsInOffers) as $intGiftID) {
							$arXml['promo']['#']['promo-gifts'][0]['#']['promo-gift'][] = array(
									'@' => array('offer-id' => $intGiftID),
							);
						}
						foreach (array_intersect($arGiftIDs, $arGiftIDsExported) as $intGiftID) {
							$arXml['promo']['#']['promo-gifts'][0]['#']['promo-gift'][] = array(
									'@' => array('gift-id' => $intGiftID),
							);
						}
						$strXml .= rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 3)) . "\n";
					}
				}
				if ($intCount < $intLimit) {
					break;
				}
				$intOffset++;
			}
		}

		$strXml .= "\t\t" . '</promos>' . "\r\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
		#
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * 	Step: Export, write footer
	 */
	protected function stepExport_writeXmlFooter($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml = '';
		$strXml .= "\t" . '</shop>' . "\n";
		$strXml .= '</yml_catalog>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: XML to ZIP
	 */
	public function stepZip($intProfileID, $arData) {
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		if ($arData['PROFILE']['PARAMS']['COMPRESS_TO_ZIP'] == 'Y') {
			$arSession['COMPRESS_TO_ZIP'] = true;
			$arZipFiles = array(
					$arSession['XML_FILE'],
			);
			$obAchiver = \CBXArchive::GetArchive($arSession['XML_FILE_ZIP']);
			$obAchiver->SetOptions(array(
					'REMOVE_PATH' => pathinfo($arSession['XML_FILE'], PATHINFO_DIRNAME),
			));
			$strStartFile = '';
			if ($arSession['ZIP_NEXT_STEP']) {
				$strStartFile = $obAchiver->GetStartFile();
			}
			$intResult = $obAchiver->Pack($arZipFiles, $strStartFile);
			unset($obAchiver);
			if ($arData['PROFILE']['PARAMS']['DELETE_XML_IF_ZIP'] == 'Y' && is_file($arSession['XML_FILE'])) {
				@unlink($arSession['XML_FILE']);
			}
			if ($intResult === \IBXArchive::StatusSuccess) {
				$arSession['EXPORT_FILE_SIZE_ZIP'] = filesize($arSession['XML_FILE_ZIP']);
				return Exporter::RESULT_SUCCESS;
			} elseif ($intResult === \IBXArchive::StatusError) {
				return Exporter::RESULT_ERROR;
			} elseif ($intResult === \IBXArchive::StatusContinue) {
				$arSession['ZIP_NEXT_STEP'] = true;
				return Exporter::RESULT_CONTINUE;
			}
		}
		return Exporter::RESULT_SUCCESS;
	}

	/* HELPERS FOR SIMILAR XML-TYPES */

	/**
	 * 	Get XML attributes
	 */
	protected function getXmlAttr($intProfileID, $arFields, $strType = false) {
		$arResult = array(
				'id' => $arFields['ID'],
		);
		if (!Helper::isEmpty($strType)) {
			$arResult['type'] = $strType;
		} elseif (!Helper::isEmpty($arFields['TYPE'])) {
			$arResult['type'] = $arFields['TYPE'];
		}
		if (!Helper::isEmpty($arFields['AVAILABLE'])) {
			$arResult['available'] = $arFields['AVAILABLE'];
		}
		if (!Helper::isEmpty($arFields['BID'])) {
			$arResult['bid'] = $arFields['BID'];
		}
		if (!Helper::isEmpty($arFields['CBID'])) {
			$arResult['cbid'] = $arFields['CBID'];
		}
		if (!Helper::isEmpty($arFields['GROUP_ID'])) {
			$arResult['group_id'] = $arFields['GROUP_ID'];
		}
		return $arResult;
	}

	/**
	 * 	Get XML tag: <url>
	 */
	protected function getXmlTag_Url($intProfileID, $mValue, $arFields) {
		$strUrl = '';
		if (strlen($mValue)) {
			$strUrl = $mValue;
			$this->addUtmToUrl($strUrl, $arFields);
		}
		return array('#' => $strUrl);
	}

	/**
	 * 	Get XML tag: <picture>
	 */
	protected function getXmlTag_Picture($intProfileID, $mValue) {
		$mResult = '';
		$mValue = is_array($mValue) ? $mValue : array($mValue);
		if (!empty($mValue)) {
			$mResult = array();
			foreach ($mValue as $strPicture) {
				$mResult[] = array('#' => $strPicture);
			}
		}
		return $mResult;
	}

	/**
	 * 	Get XML tag: <barcode>
	 */
	protected function getXmlTag_Barcode($intProfileID, $mValue) {
		$mResult = '';
		$mValue = is_array($mValue) ? $mValue : array($mValue);
		if (!empty($mValue)) {
			$mResult = array();
			foreach ($mValue as $strPictureBarcode) {
				$mResult[] = array('#' => $strPictureBarcode);
			}
		}
		return $mResult;
	}

	/**
	 * 	Get XML tag: <delivery-options>
	 */
	protected function getXmlTag_DeliveryOptions($intProfileID, $arFields) {
		$strCost = $arFields['DELIVERY_OPTIONS_COST'];
		$strDays = $arFields['DELIVERY_OPTIONS_DAYS'];
		$strOrderBefore = $arFields['DELIVERY_OPTIONS_ORDER_BEFORE'];
		#
		$arDeliveryOptions = array();
		if (!Helper::isEmpty($strCost)) {
			$arDeliveryOptions['cost'] = $strCost;
		}
		if (!Helper::isEmpty($strDays)) {
			$arDeliveryOptions['days'] = $strDays;
		}
		if (!Helper::isEmpty($strOrderBefore)) {
			$arDeliveryOptions['order-before'] = $strOrderBefore;
		}
		if (!empty($arDeliveryOptions)) {
			return array(
					array(
							'#' => array(
									'option' => array(
											array(
													'@' => $arDeliveryOptions,
											),
									),
							)
					),
			);
		}
		return '';
	}

	/**
	 * 	Get XML tag: <age>
	 */
	protected function getXmlTag_Age($intProfileID, $mValue) {
		$mResult = NULL;
		if (!Helper::isEmpty($mValue)) {
			$strUnit = 'year';
			if (preg_match('#^0(\.|,)(\d{1,2})$#', $mValue, $arMatch)) {
				$strUnit = 'month';
				$mValue = $arMatch[2];
			} elseif (preg_match('#^(\d{1,2})[\s]?m$#', $mValue, $arMatch)) {
				$strUnit = 'month';
				$mValue = $arMatch[1];
			}
			if ($strUnit == 'year' && !in_array($mValue, array(0, 6, 12, 16, 18))) {
				Log::getInstance($this->strModuleId)->add(static::getMessage('WRONG_VALUE_FOR_AGE_YEAR', array(
										'#TEXT#' => print_r($mValue, true),
								)), $intProfileID);
				return NULL;
			} elseif ($strUnit == 'month' && !in_array($mValue, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12))) {
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
	 * 	Add additional params
	 */
	protected function getXmlTag_Param($arProfile, $intIBlockID, $arFields) {
		$intProfileID = $arProfile['ID'];
		$arIBlockFields = &$arProfile['IBLOCKS'][$intIBlockID]['FIELDS'];
		$mResult = NULL;
		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]);
		if (!empty($arAdditionalFields)) {
			$mResult = array();
			foreach ($arAdditionalFields as $arAdditionalField) {
				$strFieldCode = $arAdditionalField['FIELD'];
				if (!Helper::isEmpty($arFields[$strFieldCode])) {
					$arAttributes = array(
							'name' => $arAdditionalField['NAME'],
					);
					$arAdditionalAttributes = $arIBlockFields[$strFieldCode]['PARAMS']['ADDITIONAL_ATTRIBUTES'];
					if (is_array($arAdditionalAttributes) && is_array($arAdditionalAttributes['NAME']) && $arAdditionalAttributes['VALUE']) {
						foreach ($arAdditionalAttributes['NAME'] as $key => $strAttrName) {
							$strAttrValue = $arAdditionalAttributes['VALUE'][$key];
							$arAttributes[$strAttrName] = $strAttrValue;
						}
					}
					if (is_array($arFields[$strFieldCode])) {
						foreach ($arFields[$strFieldCode] as $strValue) {
							$mResult[] = array(
									'@' => $arAttributes,
									'#' => $strValue,
							);
						}
					} else {
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
	 * 	Callback to usort for categories
	 */
	public static function usortCategoriesCallback($a, $b) {
		$a = $a['@'];
		$b = $b['@'];
		#
		if (isset($a['parentId']) && !isset($b['parentId'])) {
			return true;
		} elseif (!isset($a['parentId']) && isset($b['parentId'])) {
			return false;
		} else {
			if ($a['id'] == $b['id']) {
				return 0;
			}
			return ($a['id'] < $b['id']) ? -1 : 1;
		}
	}

}
?>