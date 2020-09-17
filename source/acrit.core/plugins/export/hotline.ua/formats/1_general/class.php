<?
/**
 * Acrit Core: Hotline.ua plugin
 * @documentation https://hotline.ua/about/pricelists_specs/#tr1
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Bitrix\Main\EventManager,
		\Acrit\Core\Helper,
		\Acrit\Core\Export\Exporter,
		\Acrit\Core\Export\ExportDataTable as ExportData,
		\Acrit\Core\Export\Field\Field,
		\Acrit\Core\Export\Filter,
		\Acrit\Core\Log,
		\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class HotlineUaGeneral extends HotlineUa
{

	CONST DATE_UPDATED = '2018-10-18';

	protected static $bSubclass = true;
	protected $strFileExt;

	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId)
	{
		parent::__construct($strModuleId);
	}

	/* START OF BASE STATIC METHODS */

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode()
	{
		return parent::getCode() . '_GENERAL';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName()
	{
		return static::getMessage('NAME');
	}

	/**
	 * 	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported()
	{
		return true;
	}

	/**
	 * 	Are categories export?
	 */
	public function areCategoriesExport()
	{
		return true;
	}

	/**
	 * 	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict()
	{
		return true;
	}

	/**
	 * 	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList()
	{
		return true;
	}

	/**
	 * 	Get list of supported currencies
	 */
	public function getSupportedCurrencies()
	{
		return array('UAH', 'USD');
	}

	/* END OF BASE STATIC METHODS */

	public function getDefaultExportFilename()
	{
		return 'hotline.xml';
	}

	/**
	 * 	Set available extension
	 */
	protected function setAvailableExtension($strExtension)
	{
		$this->strFileExt = $strExtension;
	}

	/**
	 * 	Show plugin settings
	 */
	public function showSettings()
	{
		$this->setAvailableExtension('xml');
		return $this->showDefaultSettings();
	}

	/**
	 * 	Show plugin default settings
	 */
	protected function showDefaultSettings()
	{
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;" data-role="settings-<?= static::getCode(); ?>">
			<tbody>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('SETTINGS_SHOP_NAME_HINT')); ?>
						<b><?= static::getMessage('SETTINGS_SHOP_NAME'); ?>:</b>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][SHOP_NAME]" size="46"
									 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_NAME']); ?>"/>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('SETTINGS_SHOP_ID_HINT')); ?>
						<?= static::getMessage('SETTINGS_SHOP_ID'); ?>:
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][SHOP_ID]" size="46"
									 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_ID']); ?>"/>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('SETTINGS_SHOP_RATE')); ?>
						<?= static::getMessage('SETTINGS_SHOP_RATE'); ?>:
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][SHOP_RATE]" size="46"
									 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_RATE']); ?>"/>
					</td>
				</tr>
				<tr><td colspan="2">
						<? echo Helper::showHeading(static::getMessage('SETTINGS_DELIVERY')); ?>

					</td></tr>
				<tr>
					<td colspan="2">
						<table class="hotline-config-deliveries-table">
							<th>
								<?= Helper::ShowHint(static::getMessage('FIELD_DELIVERY_ID_DESC')); ?>
								<?= static::getMessage('FIELD_DELIVERY_ID_NAME'); ?>
							</th>
							<th>
								<?= Helper::ShowHint(static::getMessage('FIELD_DELIVERY_TYPE_DESC')); ?>
								<?= static::getMessage('FIELD_DELIVERY_TYPE_NAME'); ?>
							</th>
							<th>
								<?= Helper::ShowHint(static::getMessage('FIELD_DELIVERY_COST_DESC')); ?>
								<?= static::getMessage('FIELD_DELIVERY_COST_NAME'); ?>
							</th>
							<th>
								<?= Helper::ShowHint(static::getMessage('FIELD_DELIVERY_FREEFROM_DESC')); ?>
								<?= static::getMessage('FIELD_DELIVERY_FREEFROM_NAME'); ?>
							</th>
							<th>
								<?= Helper::ShowHint(static::getMessage('FIELD_DELIVERY_TIME_DESC')); ?>
								<?= static::getMessage('FIELD_DELIVERY_TIME_NAME'); ?>
							</th>
							<th>
								<?= Helper::ShowHint(static::getMessage('FIELD_DELIVERY_INCHECKOUT_DESC')); ?>
								<?= static::getMessage('FIELD_DELIVERY_INCHECKOUT_NAME'); ?>
							</th>
							<th>
								<?= Helper::ShowHint(static::getMessage('FIELD_DELIVERY_REGION_DESC')); ?>
								<?= static::getMessage('FIELD_DELIVERY_REGION_NAME'); ?>
							</th>
							<th>
								<?= Helper::ShowHint(static::getMessage('FIELD_DELIVERY_CARRIER_DESC')); ?>
								<?= static::getMessage('FIELD_DELIVERY_CARRIER_NAME'); ?>
							</th>
							<?
							if (!$this->arProfile['PARAMS']['DELIVERIES'])
								$this->arProfile['PARAMS']['DELIVERIES'] = 1;
							for ($i = 1; $i <= $this->arProfile['PARAMS']['DELIVERIES']; $i++)
							{
								?>
								<tr data-id="<?= $i ?>">
									<td>
										<input type="text" name="PROFILE[PARAMS][DELIVERY_ID_<?= $i ?>]" size="2"
													 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['DELIVERY_ID_' . $i]); ?>"/>
									</td>
									<td>
										<?
										$arTypes = [
											'pickup' => static::getMessage('SETTINGS_DELIVERY_TYPE_PICKUP'),
											'warehouse' => static::getMessage('SETTINGS_DELIVERY_TYPE_WAREHOUSE'),
											'address' => static::getMessage('SETTINGS_DELIVERY_TYPE_ADDRESS'),
										];
										?>
										<select name="PROFILE[PARAMS][DELIVERY_TYPE_<?= $i ?>]" >
											<?
											foreach ($arTypes as $typeCode => $typeName)
											{
												$selected = ($this->arProfile['PARAMS']['DELIVERY_TYPE_' . $i] == $typeCode) ? 'selected="selected"' : '';
												?>
												<option value="<?= $typeCode ?>" <?= $selected ?>><?= $typeName ?></option>
												<?
											}
											?>
										</select>

									</td>
									<td>
										<input type="text" name="PROFILE[PARAMS][DELIVERY_COST_<?= $i ?>]" size="6"
													 value="<?= $this->arProfile['PARAMS']['DELIVERY_COST_' . $i] ?>"/>
									</td>
									<td>
										<input type="text" name="PROFILE[PARAMS][DELIVERY_FREEFROM_<?= $i ?>]" size="10"
													 value="<?= $this->arProfile['PARAMS']['DELIVERY_FREEFROM_' . $i] ?>"/>
									</td>
									<td>
										<input type="text" name="PROFILE[PARAMS][DELIVERY_TIME_<?= $i ?>]" size="3"
													 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['DELIVERY_TIME_' . $i]); ?>"/>
									</td>
									<td>
										<? $checked = ($this->arProfile['PARAMS']['DELIVERY_INCHECKOUT_' . $i] == 'true') ? 'checked="checked"' : ''; ?>
										<input type="checkbox" name="PROFILE[PARAMS][DELIVERY_INCHECKOUT_<?= $i ?>]" <?= $checked ?>
													 value="true"/>
									</td>
									<td>
										<input type="text" name="PROFILE[PARAMS][DELIVERY_REGION_<?= $i ?>]" size="15"
													 value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['DELIVERY_REGION_' . $i]); ?>"/>
									</td>
									<td>
										<?
										$arCarriers = [
											'CAT' => static::getMessage('SETTINGS_DELIVERY_CARRIER_CAT'),
											'DF' => static::getMessage('SETTINGS_DELIVERY_CARRIER_DF'),
											'DHL' => static::getMessage('SETTINGS_DELIVERY_CARRIER_DHL'),
											'IP' => static::getMessage('SETTINGS_DELIVERY_CARRIER_IP'),
											'ND' => static::getMessage('SETTINGS_DELIVERY_CARRIER_ND'),
											'PP' => static::getMessage('SETTINGS_DELIVERY_CARRIER_PP'),
											'TMM' => static::getMessage('SETTINGS_DELIVERY_CARRIER_TMM'),
											'AL' => static::getMessage('SETTINGS_DELIVERY_CARRIER_AL'),
											'VC' => static::getMessage('SETTINGS_DELIVERY_CARRIER_VC'),
											'VP' => static::getMessage('SETTINGS_DELIVERY_CARRIER_VP'),
											'GU' => static::getMessage('SETTINGS_DELIVERY_CARRIER_GU'),
											'DA' => static::getMessage('SETTINGS_DELIVERY_CARRIER_DA'),
											'ее' => static::getMessage('SETTINGS_DELIVERY_CARRIER_ее'),
											'ZD' => static::getMessage('SETTINGS_DELIVERY_CARRIER_ZD'),
											'IT' => static::getMessage('SETTINGS_DELIVERY_CARRIER_IT'),
											'CE' => static::getMessage('SETTINGS_DELIVERY_CARRIER_CE'),
											'KSD' => static::getMessage('SETTINGS_DELIVERY_CARRIER_KSD'),
											'ME' => static::getMessage('SETTINGS_DELIVERY_CARRIER_ME'),
											'NP' => static::getMessage('SETTINGS_DELIVERY_CARRIER_NP'),
											'NE' => static::getMessage('SETTINGS_DELIVERY_CARRIER_NE'),
											'PE' => static::getMessage('SETTINGS_DELIVERY_CARRIER_PE'),
											'PB' => static::getMessage('SETTINGS_DELIVERY_CARRIER_PB'),
											'MET' => static::getMessage('SETTINGS_DELIVERY_CARRIER_MET'),
											'UPG' => static::getMessage('SETTINGS_DELIVERY_CARRIER_UPG'),
											'UP' => static::getMessage('SETTINGS_DELIVERY_CARRIER_UP'),
											'EM' => static::getMessage('SETTINGS_DELIVERY_CARRIER_EM'),
											'YT' => static::getMessage('SETTINGS_DELIVERY_CARRIER_YT')
										];
										?>
										<select name="PROFILE[PARAMS][DELIVERY_CARRIER_<?= $i ?>]" >
											<?
											foreach ($arCarriers as $typeCode => $typeName)
											{
												$selected = ($this->arProfile['PARAMS']['DELIVERY_CARRIER_' . $i] == $typeCode) ? 'selected="selected"' : '';
												?>
												<option value="<?= $typeCode ?>" <?= $selected ?>><?= $typeName ?></option>
												<?
											}
											?>
										</select>
									</td>
								</tr>
								<?
							}
							?>
							<tr data-id="#" style="display: none;" class="hotline-config-deliveries-new-row">
								<td>
									<input type="text" name="PROFILE[PARAMS][DELIVERY_ID_#]" size="2" value="" />
								</td>
								<td>

									<select name="PROFILE[PARAMS][DELIVERY_TYPE_#]" >
										<?
										foreach ($arTypes as $typeCode => $typeName)
										{
											?>
											<option value="<?= $typeCode ?>"><?= $typeName ?></option>
											<?
										}
										?>
									</select>

								</td>
								<td>
									<input type="text" name="PROFILE[PARAMS][DELIVERY_COST_#]" size="6" value=""/>
								</td>
								<td>
									<input type="text" name="PROFILE[PARAMS][DELIVERY_FREEFROM_#]" size="10" value=""/>
								</td>
								<td>
									<input type="text" name="PROFILE[PARAMS][DELIVERY_TIME_#]" size="3"	value=""/>
								</td>
								<td>
									<input type="checkbox" name="PROFILE[PARAMS][DELIVERY_INCHECKOUT_#]" value="true"/>
								</td>
								<td>
									<input type="text" name="PROFILE[PARAMS][DELIVERY_REGION_#]" size="15" value=""/>
								</td>
								<td>

									<select name="PROFILE[PARAMS][DELIVERY_CARRIER_#]" >
										<?
										foreach ($arCarriers as $typeCode => $typeName)
										{
											?>
											<option value="<?= $typeCode ?>"><?= $typeName ?></option>
											<?
										}
										?>
									</select>
								</td>
							</tr>
						</table>
						<input class="hotline-config-deliveries-add-new"  type="button"  value="<?= static::getMessage('FIELD_ADD_NEW_DELIVERY'); ?>"/>
						<input class="hotline-config-deliveries-count" type="hidden" name="PROFILE[PARAMS][DELIVERIES]" value="<?= $this->arProfile['PARAMS']['DELIVERIES'] ?>"/>
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('SETTINGS_FILE_HINT')); ?>
						<b><?= static::getMessage('SETTINGS_FILE'); ?>:</b>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<?
						\CAdminFileDialog::ShowScript(array(
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
	 * 	Update categories from server
	 */
	public function updateCategories($intProfileID)
	{
		$strCategories = '';

		$fileContent = file_get_contents('https://hotline.ua/download/hotline/hotline_tree.csv');
		$strFileName = $this->getCategoriesFile();
		if (strlen($fileContent))
		{
			file_put_contents($strFileName, $fileContent);
			#
			unset($fileContent);
		}
		#
		return (is_file($strFileName) && filesize($strFileName));
	}

	/**
	 * Get categories file name
	 * */
	public function getCategoriesFile()
	{
		$strFileName = realpath(__DIR__ . '/../../') . '/hotline_tree.csv';
		return $strFileName;
	}

	/**
	 * 	Get categories date update
	 */
	public function getCategoriesDate()
	{
		$strFileName = $this->getCategoriesFile();
		return is_file($strFileName) ? filemtime($strFileName) : false;
	}

	/**
	 * 	Get categories list
	 */
	public function getCategoriesList($intProfileID)
	{
		$arCategories = [];
		$strFileName = $this->getCategoriesFile();
		$handle = fopen($strFileName, "r");
		while (($data = fgetcsv($handle)) !== FALSE)
		{
			$arCategories[] = str_replace(';', '', $data[0]);
		}
		fclose($handle);
		if (Helper::isUtf())
		{
			return Helper::convertEncoding($arCategories, 'CP1251', 'UTF-8');
		} else
		{
			return $arCategories;
		}
	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false)
	{
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
			'CODE' => 'SECTION_ID',
			'DISPLAY_CODE' => 'categoryId',
			'NAME' => static::getMessage('FIELD_SECTION_ID_NAME'),
			'SORT' => 505,
			'DESCRIPTION' => static::getMessage('FIELD_SECTION_ID_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'SECTION__ID',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CODE',
			'DISPLAY_CODE' => 'code',
			'NAME' => static::getMessage('FIELD_CODE_NAME'),
			'SORT' => 505,
			'DESCRIPTION' => static::getMessage('FIELD_CODE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_ARTNUMBER',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BARCODE',
			'DISPLAY_CODE' => 'barcode',
			'NAME' => static::getMessage('FIELD_BARCODE_NAME'),
			'SORT' => 505,
			'DESCRIPTION' => static::getMessage('FIELD_BARCODE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'VENDOR',
			'DISPLAY_CODE' => 'vendor',
			'NAME' => static::getMessage('FIELD_VENDOR_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_VENDOR_DESC'),
			'REQUIRED' => true,
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
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_NAME_DESC'),
			'REQUIRED' => true,
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
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'CDATA' => false,
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
			'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
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
		$this->addUtmFields($arResult, 531, 'hotline');
		$arResult[] = new Field(array(
			'CODE' => 'PICTURE',
			'DISPLAY_CODE' => 'image',
			'NAME' => static::getMessage('FIELD_PICTURE_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_PICTURE_DESC'),
			'REQUIRED' => false,
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
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'priceRUAH',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 540,
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
			'CODE' => 'PRICE_OLD',
			'DISPLAY_CODE' => 'oldprice',
			'NAME' => static::getMessage('FIELD_PRICE_OLD_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_OLD_DESC'),
			'SORT' => 540,
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE_USD',
			'DISPLAY_CODE' => 'priceRUSD',
			'NAME' => static::getMessage('FIELD_PRICE_USD_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_USD_DESC'),
			'SORT' => 540,
			'REQUIRED' => false,
			'MULTIPLE' => false,
		));

		$arResult[] = new Field(array(
			'CODE' => 'AVAILABLE',
			'DISPLAY_CODE' => 'stock',
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
					'CONST' => static::getMessage('FIELD_AVAILABLE_VALUE_ON'),
					'SUFFIX' => 'Y',
				),
				array(
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('FIELD_AVAILABLE_VALUE_OFF'),
					'SUFFIX' => 'N',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'STOCK_DAYS',
			'DISPLAY_CODE' => 'stock_days',
			'NAME' => static::getMessage('FIELD_STOCK_DAYS_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_STOCK_DAYS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'GUARANTEE',
			'DISPLAY_CODE' => 'guarantee',
			'NAME' => static::getMessage('FIELD_GUARANTEE_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_GUARANTEE_DESC'),
			'SORT' => 541,
		));
		$arResult[] = new Field(array(
			'CODE' => 'GUARANTEE_DAYS',
			'DISPLAY_CODE' => 'guarantee_days',
			'NAME' => static::getMessage('FIELD_GUARANTEE_DAYS_NAME'),
			'SORT' => 542,
		));
		$arResult[] = new Field(array(
			'CODE' => 'GUARANTEE_TYPE',
			'DISPLAY_CODE' => 'guarantee_type',
			'NAME' => static::getMessage('FIELD_GUARANTEE_TYPE_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_GUARANTEE_TYPE_DESC'),
			'SORT' => 543,
		));
		$arResult[] = new Field(array(
			'CODE' => 'PARAM_ORIGINAL',
			'DISPLAY_CODE' => 'param_original',
			'NAME' => static::getMessage('FIELD_PARAM_ORIGINAL_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_PARAM_ORIGINAL_DESC'),
			'SORT' => 550,
		));
		$arResult[] = new Field(array(
			'CODE' => 'PARAM_MANUF_COUNTRY',
			'DISPLAY_CODE' => 'param_manuf_country',
			'NAME' => static::getMessage('FIELD_PARAM_MANUF_COUNTRY_NAME'),
			'SORT' => 560,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY_ID',
			'DISPLAY_CODE' => 'delivery_id',
			'NAME' => static::getMessage('FIELD_DELIVERY_ID_NAME'),
			'SORT' => 600,
		));

		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY_COST',
			'DISPLAY_CODE' => 'delivery_cost',
			'NAME' => static::getMessage('FIELD_DELIVERY_COST_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_COST_DESC'),
			'SORT' => 620,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY_FREEFROM',
			'DISPLAY_CODE' => 'delivery_freeFrom',
			'NAME' => static::getMessage('FIELD_DELIVERY_FREEFROM_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_FREEFROM_DESC'),
			'SORT' => 630,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY_TIME',
			'DISPLAY_CODE' => 'delivery_time',
			'NAME' => static::getMessage('FIELD_DELIVERY_TIME_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_TIME_DESC'),
			'SORT' => 640,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CONDITION',
			'DISPLAY_CODE' => 'condition',
			'NAME' => static::getMessage('FIELD_CONDITION_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_CONDITION_DESC'),
			'SORT' => 650,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CUSTOM',
			'DISPLAY_CODE' => 'custom',
			'NAME' => static::getMessage('FIELD_CUSTOM_NAME'),
			'DESCRIPTION' => static::getMessage('FIELD_CUSTOM_DESC'),
			'SORT' => 660,
		));

		#
		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]);
		foreach ($arAdditionalFields as $arAdditionalField)
		{
			$arDefaultValue = null;
			if (strlen($arAdditionalField['DEFAULT_FIELD']))
			{
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
	 * 	Process single element
	 * 	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields)
	{
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];

		# Prepare data
		$bOffer = $arElement['IS_OFFER'];
		if ($bOffer)
		{
			$arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		} else
		{
			$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
			$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
		}

		# Build XML
		$arXmlTags = array();
		if (!Helper::isEmpty($arFields['ID']))
			$arXmlTags['id'] = Xml::addTag($arFields['ID']);
		$arXmlTags['categoryId'] = Xml::addTag(reset($arElementSections));
		if (!Helper::isEmpty($arFields['CODE']))
			$arXmlTags['code'] = Xml::addTag($arFields['CODE']);
		if (!Helper::isEmpty($arFields['barcode']))
			$arXmlTags['barcode'] = Xml::addTag($arFields['BARCODE']);
		if (!Helper::isEmpty($arFields['VENDOR']))
			$arXmlTags['vendor'] = Xml::addTag($arFields['VENDOR']);
		if (!Helper::isEmpty($arFields['NAME']))
			$arXmlTags['name'] = Xml::addTag($arFields['NAME']);
		if (!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['description'] = Xml::addTag($arFields['DESCRIPTION']);
		if (!Helper::isEmpty($arFields['URL']))
			$arXmlTags['url'] = $this->getXmlTag_Url($intProfileID, $arFields['URL'], $arFields);
		$arXmlTags['image'] = Xml::addTag($arFields['PICTURE']);
		if (!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['priceRUAH'] = Xml::addTag($arFields['PRICE']);
		if (!Helper::isEmpty($arFields['PRICE_OLD']))
			$arXmlTags['oldprice'] = Xml::addTag($arFields['PRICE_OLD']);
		if (!Helper::isEmpty($arFields['PRICE_USD']))
			$arXmlTags['priceRUSD'] = Xml::addTag($arFields['PRICE_USD']);
		if (!Helper::isEmpty($arFields['CONDITION']))
			$arXmlTags['condition'] = Xml::addTag($arFields['CONDITION']);

		if (!Helper::isEmpty($arFields['AVAILABLE']))
			$stockTagParam = [];
		if (!Helper::isEmpty($arFields['STOCK_DAYS']))
		{
			$stockTagParam = ['days' => $arFields['STOCK_DAYS']];
		}
		$arXmlTags['stock'] = [[
		'#' => $arFields['AVAILABLE'],
		'@' => $stockTagParam,
		]];




		if (!Helper::isEmpty($arFields['DELIVERY_ID']))
		{
			$deliveryTag = [
				'#' => '',
				'@' => [
					'id' => $arFields['DELIVERY_ID'],
				]
			];

			if (!Helper::isEmpty($arFields['DELIVERY_COST']))
			{
				$deliveryTag['@']['cost'] = $arFields['DELIVERY_COST'];
			}

			if (!Helper::isEmpty($arFields['DELIVERY_FREEFROM']))
			{
				$deliveryTag['@']['freeFrom'] = $arFields['DELIVERY_FREEFROM'];
			}
			if (!Helper::isEmpty($arFields['DELIVERY_TIME']))
			{
				$deliveryTag['@']['time'] = $arFields['DELIVERY_TIME'];
			}
			$arXmlTags['delivery'] = [$deliveryTag];
		}
		if (!Helper::isEmpty($arFields['GUARANTEE']))
		{
			$guaranteeTag = [
				'#' => $arFields['GUARANTEE'],
				'@' => []
			];

			if (!Helper::isEmpty($arFields['GUARANTEE_DAYS']))
			{
				$guaranteeTag['@']['days'] = $arFields['GUARANTEE_DAYS'];
			}

			if (!Helper::isEmpty($arFields['GUARANTEE_TYPE']))
			{
				$guaranteeTag['@']['type'] = $arFields['GUARANTEE_TYPE'];
			}

			$arXmlTags['guarantee'] = [$guaranteeTag];
		}


		if (!Helper::isEmpty($arFields['STOCK_QUANTITY']))
			$arXmlTags['stock_quantity'] = Xml::addTag($arFields['STOCK_QUANTITY']);

		# Params
		$arXmlTags['param'] = $this->getXmlTag_Param($arProfile, $intIBlockID, $arFields);

		# Build XML
		$arXml = array(
			'item' => array(
				'@' => $this->getXmlAttr($intProfileID, $arFields),
				'#' => $arXmlTags,
			),
		);

		# Event handler OnTorgMailRuXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnTorgMailRuXml') as $arHandler)
		{
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
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnTorgMailRuResult') as $arHandler)
		{
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}

		# After..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}

	/**
	 * 	Show results
	 */
	public function showResults($arSession)
	{
		ob_start();
		$intTime = $arSession['TIME_FINISHED'] - $arSession['TIME_START'];
		if ($intTime <= 0)
		{
			$intTime = 1;
		}
		?>
		<div><?= static::getMessage('RESULT_GENERATED'); ?>: <?= IntVal($arSession['GENERATE']['INDEX']); ?></div>
		<div><?= static::getMessage('RESULT_EXPORTED'); ?>: <?= IntVal($arSession['EXPORT']['INDEX']); ?></div>
		<div><?= static::getMessage('RESULT_ELAPSED_TIME'); ?>: <?= Helper::formatElapsedTime($intTime); ?></div>
		<div><?= static::getMessage('RESULT_DATETIME'); ?>: <?= (new \Bitrix\Main\Type\DateTime())->toString(); ?></div>
		<?= $this->showFileOpenLink(); ?>
		<?
		return Helper::showSuccess(ob_get_clean());
	}

	/* START OF BASE METHODS FOR XML SUBCLASSES */

	/**
	 * 	Get steps
	 */
	public function getSteps()
	{
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
		return $arResult;
	}

	/**
	 * 	Step: Check input params and data
	 */
	public function stepCheck($intProfileID, $arData)
	{
		$strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		if (!strlen($strExportFilename))
		{
			Log::getInstance($this->strModuleId)->add(static::getMessage('NO_EXPORT_FILE_SPECIFIED'), $intProfileID);
			print static::getMessage('NO_EXPORT_FILE_SPECIFIED');
			return Exporter::RESULT_ERROR;
		}
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * 	Step: Export
	 */
	public function stepExport($intProfileID, $arData)
	{
		$arSession = &$arData['SESSION']['EXPORT'];
		$bIsCron = $arData['IS_CRON'];
		#
		$strExportFilename = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		#
		if (!isset($arSession['XML_FILE']))
		{
			#$strTmpDir = Profile::getTmpDir($intProfileID);
			$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
			$strTmpFile = pathinfo($strExportFilename, PATHINFO_BASENAME) . '.tmp';
			$arSession['XML_FILE_URL'] = $strExportFilename;
			$arSession['XML_FILE'] = $_SERVER['DOCUMENT_ROOT'] . $strExportFilename;
			$arSession['XML_FILE_TMP'] = $strTmpDir . '/' . $strTmpFile;
			#
			if (is_file($arSession['XML_FILE_TMP']))
			{
				unlink($arSession['XML_FILE_TMP']);
			}
			touch($arSession['XML_FILE_TMP']);
			unset($strTmpDir, $strTmpFile);
		}

		# SubStep1 [header]
		if (!isset($arSession['XML_HEADER_WROTE']))
		{
			$this->stepExport_writeXmlHeader($intProfileID, $arData);
			$arSession['XML_HEADER_WROTE'] = true;
		}

		# SubStep2 [currencies]
		if (!isset($arSession['XML_DELIVERIES_WROTE']))
		{
			$this->stepExport_writeXmlDeliveries($intProfileID, $arData);
			$arSession['XML_DELIVERIES_WROTE'] = true;
		}

		# SubStep3 [<categories>]
		if (!isset($arSession['XML_CATEGORIES_WROTE']))
		{
			$this->stepExport_writeXmlCategories($intProfileID, $arData);
			$arSession['XML_CATEGORIES_WROTE'] = true;
		}

		# SubStep4 [each <offer>]
		if (!isset($arSession['XML_OFFERS_WROTE']))
		{
			$this->stepExport_writeXmlOffers($intProfileID, $arData);
			$arSession['XML_OFFERS_WROTE'] = true;
		}

		# SubStep5 [footer]
		if (!isset($arSession['XML_FOOTER_WROTE']))
		{
			$this->stepExport_writeXmlFooter($intProfileID, $arData);
			$arSession['XML_FOOTER_WROTE'] = true;
		}

		# SubStep6 [tmp => real]
		if (is_file($arSession['XML_FILE']))
		{
			unlink($arSession['XML_FILE']);
		}
		if (!Helper::createDirectoriesForFile($arSession['XML_FILE']))
		{
			$strMessage = Loc::getMessage('ACRIT_EXP_ERROR_CREATE_DIRECORY', array(
						'#DIR#' => Helper::getDirectoryForFile($arSession['XML_FILE']),
			));
			Log::getInstance($this->strModuleId)->add($strMessage);
			print Helper::showError($strMessage);
			return Exporter::RESULT_ERROR;
		}
		if (is_file($arSession['XML_FILE']))
		{
			@unlink($arSession['XML_FILE']);
		}
		if (!@rename($arSession['XML_FILE_TMP'], $arSession['XML_FILE']))
		{
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
	 * 	Step: Export, write header
	 */
	protected function stepExport_writeXmlHeader($intProfileID, $arData)
	{
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strDate = (new \Bitrix\Main\Type\DateTime())->format('Y-m-d H:i');
		#
		$strXml = '';
		$strXml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$strXml .= "\t" . '<price>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
		#
		$arXml = array(
			'date' => array(
				'#' => date("Y-m-d H:i"),
			),
			'firmName' => array(
				'#' => $arData['PROFILE']['PARAMS']['SHOP_NAME'],
			),
			'firmId' => array(
				'#' => $arData['PROFILE']['PARAMS']['SHOP_ID'],
			),
			'rate' => array(
				'#' => $arData['PROFILE']['PARAMS']['SHOP_RATE'],
			),
		);
		$strXml = Xml::arrayToXml($arXml, $intDepthLevel = 3);
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write currencies
	 */
	protected function stepExport_writeXmlDeliveries($intProfileID, $arData)
	{
		$strXmlAll = '';
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$arDeliveriesTags = [];
		$arParamsList = ['ID', 'TYPE', 'COST', 'FREEFROM', 'TIME', 'INCHECKOUT', 'REGION', 'CARRIER'];
		for ($i = 1; $i <= $arData['PROFILE']['PARAMS']['DELIVERIES']; $i++)
		{
			$arParams = [];
			foreach ($arParamsList as $code)
			{

				$val = $arData['PROFILE']['PARAMS']['DELIVERY_' . $code . '_' . $i];

				if ($val)
					$arParams[strtolower($code)] = $val;
			}
			$arXml = array(
				'delivery' => array(
					'@' => $arParams
				),
			);

			$strXml = Xml::arrayToXml($arXml);
			$strXml = rtrim(Xml::addOffset($strXml, 2)) . "\n";
			$strXmlAll .= Helper::convertEncodingTo($strXml, 'UTF-8');
		}

		file_put_contents($strFile, $strXmlAll, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write categories
	 */
	protected function stepExport_writeXmlCategories($intProfileID, $arData)
	{
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];

		# All categories for XML
		$arCategoriesForXml = array();

		# Get category redefinitions all
		#$arCategoryRedefinitionsAll = CategoryRedefinition::getForProfile($intProfileID);
		$arCategoryRedefinitionsAll = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$intProfileID]);

		# All sections ID for export
		$arSectionsForExportAll = array();

		# Process each used IBlocks
		foreach ($arData['PROFILE']['IBLOCKS'] as $intIBlockID => $arIBlockSettings)
		{
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
			while ($arItem = $resItems->fetch())
			{

				$arItemSectionsID = array();
				if (is_numeric($arItem['SECTION_ID']) && $arItem['SECTION_ID'] > 0)
				{
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
				foreach ($arItemSectionsID as $intSectionID)
				{
					if (!in_array($intSectionID, $arUsedSectionsID))
					{
						$arUsedSectionsID[] = $intSectionID;
					}
				}
			}
			# Get involded sections ID
			$intSectionsIBlockID = $intIBlockID;
			$strSectionsID = $arIBlockSettings['SECTIONS_ID'];
			$strSectionsMode = $arIBlockSettings['SECTIONS_MODE'];
			$arCatalog = Helper::getCatalogArray($intIBlockID);
			if (is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] > 0)
			{
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

		if (!empty($arSectionsForExportAll))
		{
			$arSectionsAll = array();
			$resSections = \CIBlockSection::getList(array(
						'ID' => 'ASC',
							), array(
						'ID' => $arSectionsForExportAll,
							), false, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
			while ($arSection = $resSections->getNext(false, false))
			{
				$sectionAlternativeName = rtrim($arCategoryRedefinitionsAll[$arSection['ID']]);
				if (!$sectionAlternativeName)
					$sectionAlternativeName = $arSection['NAME'];
				$arSection['ID'] = IntVal($arSection['ID']);
				$arSectionsAll[$arSection['ID']] = array(
					'NAME' => $sectionAlternativeName,
					'PARENT_ID' => IntVal($arSection['IBLOCK_SECTION_ID']),
				);
			}
			$arSectionsForExportAll = $arSectionsAll;
			unset($arSectionsAll, $resSections, $arSection);
		}

		# Categories to XML array
		$arCategoriesXml = array();
		foreach ($arSectionsForExportAll as $intCategoryID => $arCategory)
		{
			if ($arData['PROFILE']['PARAMS']['CATEGORIES_EXPORT_PARENTS'] == 'Y')
			{
				$resSectionsChain = \CIBlockSection::getNavChain(false, $intCategoryID, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
				while ($arSectionsChain = $resSectionsChain->getNext())
				{
					$arCategoryXml = array(
						'#' => [
							'name' => ['#' => htmlspecialcharsbx($arSectionsChain['NAME'])],
							'id' => ['#' => $arSectionsChain['ID']],
						],
					);
					if ($arSectionsChain['IBLOCK_SECTION_ID'])
					{
						$arCategoryXml['#']['parentId'] = ['#' => $arSectionsChain['IBLOCK_SECTION_ID']];
					}
					$arCategoriesXml[$arSectionsChain['ID']] = $arCategoryXml;
				}
				unset($resSectionsChain, $arSectionsChain, $arCategoryXml);
			} else
			{
				$intParentID = false;
				$strCategoryName = $arCategory['NAME'];
				$arCategoryXml = array(
					'#' => [
						'name' => ['#' => htmlspecialcharsbx($strCategoryName)],
						'id' => ['#' => $intCategoryID],
					],
				);

				$arCategoriesXml[] = $arCategoryXml;
			}
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
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write offers
	 * 	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData)
	{
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml .= "\t\t" . '<items>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
		#
		$intOffset = 0;
		while (true)
		{
			$intLimit = 5000;
			$strSortOrder = ToUpper($arData['PROFILE']['PARAMS']['SORT_ORDER']);
			if (!in_array($strSortOrder, array('ASC', 'DESC')))
			{
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
			while ($arItem = $resItems->fetch())
			{
				$intCount++;
				$arItem['DATA'] = rtrim(Xml::addOffset($arItem['DATA'], 3)) . "\n";
				$strXml .= $arItem['DATA'];
			}
			$arData['SESSION']['EXPORT']['INDEX'] += $intCount;
			#
			$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
			#
			file_put_contents($strFile, $strXml, FILE_APPEND);
			if ($intCount < $intLimit)
			{
				break;
			}
			$intOffset++;
		}
		#
		$strXml = "\t\t" . '</items>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, 'UTF-8');
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write footer
	 */
	protected function stepExport_writeXmlFooter($intProfileID, $arData)
	{
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml = '';
		$strXml .= "\t" . '</price>' . "\n";
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/* HELPERS FOR SIMILAR XML-TYPES */

	/**
	 * 	Get XML attributes
	 */
	protected function getXmlAttr($intProfileID, $arFields, $strType = false)
	{
		$arResult = array(
			'id' => $arFields['ID'],
		);
		if (!Helper::isEmpty($arFields['AVAILABLE']))
		{
			$arResult['available'] = $arFields['AVAILABLE'];
		}
		return $arResult;
	}

	/**
	 * 	Get XML tag: <url>
	 */
	protected function getXmlTag_Url($intProfileID, $strUrl, $arFields)
	{
		if (strlen($strUrl))
		{
			$this->addUtmToUrl($strUrl, $arFields);
		}
		return array('#' => $strUrl);
	}

	/**
	 * 	Add additional params
	 */
	protected function getXmlTag_Param($arProfile, $intIBlockID, $arFields)
	{
		$intProfileID = $arProfile['ID'];
		$arIBlockFields = &$arProfile['IBLOCKS'][$intIBlockID]['FIELDS'];
		$mResult = NULL;
		#$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]);
		if (!empty($arAdditionalFields))
		{
			$mResult = array();
			foreach ($arAdditionalFields as $arAdditionalField)
			{
				$strFieldCode = $arAdditionalField['FIELD'];
				if (!Helper::isEmpty($arFields[$strFieldCode]))
				{
					$arAttributes = array(
						'name' => $arAdditionalField['NAME'],
					);
					$arAdditionalAttributes = $arIBlockFields[$strFieldCode]['PARAMS']['ADDITIONAL_ATTRIBUTES'];
					if (is_array($arAdditionalAttributes) && is_array($arAdditionalAttributes['NAME']) && $arAdditionalAttributes['VALUE'])
					{
						foreach ($arAdditionalAttributes['NAME'] as $key => $strAttrName)
						{
							$strAttrValue = $arAdditionalAttributes['VALUE'][$key];
							$arAttributes[$strAttrName] = $strAttrValue;
						}
					}
					if (is_array($arFields[$strFieldCode]))
					{
						foreach ($arFields[$strFieldCode] as $strValue)
						{
							$mResult[] = array(
								'@' => $arAttributes,
								'#' => $strValue,
							);
						}
					} else
					{
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
	public static function usortCategoriesCallback($a, $b)
	{
		$a = $a['@'];
		$b = $b['@'];
		#
		if (isset($a['parentId']) && !isset($b['parentId']))
		{
			return true;
		} elseif (!isset($a['parentId']) && isset($b['parentId']))
		{
			return false;
		} else
		{
			if ($a['id'] == $b['id'])
			{
				return 0;
			}
			return ($a['id'] < $b['id']) ? -1 : 1;
		}
	}

}
?>