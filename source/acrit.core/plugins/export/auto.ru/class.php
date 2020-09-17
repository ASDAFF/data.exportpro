<?
/**
 * Acrit Core: Auto.ru base plugin
 * @documentation https://yandex.ru/support/direct/smart-banners/feeds.html#feeds__requirements
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Acrit\Core\Helper,
		\Acrit\Core\HttpRequest,
		\Acrit\Core\Log,
		\Acrit\Core\Export\Plugin,
		\Acrit\Core\Export\Field\Field,
		\Acrit\Core\Export\Filter;

Loc::loadMessages(__FILE__);

class AutoRu extends Plugin
{

	CONST DATE_UPDATED = '2018-11-13';

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
		return 'AUTO_RU';
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
		return false;
	}

	/**
	 * 	Are categories export?
	 */
	public function areCategoriesExport()
	{
		return false;
	}

	/**
	 * 	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict()
	{ // static ot not?
		return false;
	}

	/**
	 * 	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList()
	{ // static ot not?
		return false;
	}

	/**
	 * 	Get list of supported currencies
	 */
	public function getSupportedCurrencies()
	{
		return array('RUB', 'USD', 'EUR');
	}

	/* END OF BASE STATIC METHODS */

	public function getDefaultExportFilename()
	{
		return 'auto_ru.xml';
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
		return
				$this->showShopSettings() .
				$this->showDefaultSettings();
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
	protected function showShopSettings()
	{

	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false)
	{

	}

	/**
	 * 	Process single element
	 * 	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields)
	{
		// basically [in this class] do nothing, all business logic are in each format
	}

}
?>