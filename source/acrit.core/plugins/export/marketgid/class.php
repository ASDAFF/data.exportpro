<?
/**
 * Acrit Core: MarketGid base plugin
 * @documentation https://dashboard.marketgid.com/index/teaser-goods-export-requirements
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Acrit\Core\Helper,
		\Acrit\Core\Export\Plugin,
		\Acrit\Core\Export\Field\Field,
		\Acrit\Core\Export\Filter,
		\Acrit\Core\Export\Exporter,
		\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
		\Acrit\Core\Export\ExportDataTable as ExportData,
		\Acrit\Core\Log,
		\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class MarketGid extends Plugin {

	CONST DATE_UPDATED = '2019-03-07';

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
		return 'MARKETGID';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
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
		return false;
	}

	/**
	 * 	Get list of supported currencies
	 */
	public function getSupportedCurrencies() {
		return array('RUB', 'USD', 'EUR', 'UAH', 'KZT', 'BYN');
	}

	/* END OF BASE STATIC METHODS */

	public function getDefaultExportFilename() {
		return 'marketgid.xml';
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

	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
		$arResult = array();
		$arResult[] = new Field(array(
				'CODE' => 'ID',
				'DISPLAY_CODE' => 'id',
				'NAME' => static::getMessage('FIELD_ID_NAME'),
				'SORT' => 98,
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
				'DISPLAY_CODE' => 'active',
				'NAME' => static::getMessage('FIELD_AVAILABLE_NAME'),
				'SORT' => 99,
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
				'SORT' => 110,
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
		$arResult[] = new Field(array(
				'CODE' => 'PICTURE',
				'DISPLAY_CODE' => 'picture',
				'NAME' => static::getMessage('FIELD_PICTURE_NAME'),
				'SORT' => 120,
				'DESCRIPTION' => static::getMessage('FIELD_PICTURE_DESC'),
				'REQUIRED' => true,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'DETAIL_PICTURE',
						),
				),
				'MAX_COUNT' => 10,
		));
		$arResult[] = new Field(array(
				'CODE' => 'NAME',
				'DISPLAY_CODE' => 'title',
				'NAME' => static::getMessage('FIELD_NAME_NAME'),
				'SORT' => 130,
				'DESCRIPTION' => static::getMessage('FIELD_NAME_DESC'),
				'REQUIRED' => true,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'NAME',
						),
				),
				'PARAMS' => array(
						'HTMLSPECIALCHARS' => 'escape',
				),
		));
		$arResult[] = new Field(array(
				'CODE' => 'DESCRIPTION',
				'DISPLAY_CODE' => 'text',
				'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
				'SORT' => 140,
				'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
				'REQUIRED' => true,
				'MULTIPLE' => false,
				'CDATA' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'DETAIL_TEXT',
								'PARAMS' => array('HTMLSPECIALCHARS' => 'cut',
										'HTML2TEXT' => 'Y'),
						),
				),
				'PARAMS' => array('HTMLSPECIALCHARS' => 'cut', 'HTML2TEXT' => 'Y'),
		));
		$arResult[] = new Field(array(
				'CODE' => 'CURRENCY_ID',
				'DISPLAY_CODE' => 'currencyId',
				'NAME' => static::getMessage('FIELD_CURRENCY_ID_NAME'),
				'SORT' => 150,
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
				'CODE' => 'PRICE',
				'DISPLAY_CODE' => 'price',
				'NAME' => static::getMessage('FIELD_PRICE_NAME'),
				'SORT' => 160,
				'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
				'REQUIRED' => false,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
						array(
								'TYPE' => 'FIELD',
								'VALUE' => 'CATALOG_PRICE_1__WITH_DISCOUNT',
						),
				),
				'IS_PRICE' => true,
		));

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



		# SubStep3 [<categories>]
		if (!isset($arSession['XML_CATEGORIES_WROTE'])) {
			$this->stepExport_writeXmlCategories($intProfileID, $arData);
			$arSession['XML_CATEGORIES_WROTE'] = true;
		}



		# SubStep6 [each <offer>]
		if (!isset($arSession['XML_OFFERS_WROTE'])) {
			$this->stepExport_writeXmlOffers($intProfileID, $arData);
			$arSession['XML_OFFERS_WROTE'] = true;
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
			Log::getInstance($this->strModuleId)->add($strMessage, $intProfileID);
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
	 * 	Step: Export, write header
	 */
	protected function stepExport_writeXmlHeader($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strEncoding = $arData['PROFILE']['PARAMS']['ENCODING'];
		#
		$strDate = (new \Bitrix\Main\Type\DateTime())->format('Y-m-d H:i');
		$strXml = '';
		$strXml .= '<?xml version="1.0" encoding="' . $strEncoding . '" standalone="no"?>' . "\n";
		$strXml .= '<mgid_teaser_goods_export date="' . $strDate . '" xmlns="http://www.w3schools.com">' . "\n";
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
			$arSelectedSectionsID = Exporter::getInvolvedSectionsID($intIBlockID, $arIBlockSettings['SECTIONS_ID'], $arIBlockSettings['SECTIONS_MODE']);
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
			// Режим "Использовать категории торговой площадки"
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
			// Режим "Использовать категории сайта"
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
		$strXml = Xml::arrayToXml($arXml, $intDepthLevel = 2);
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write offers
	 * 	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml .= "\t" . '<teasers>' . "\n";
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
				$arItem['DATA'] = rtrim(Xml::addOffset($arItem['DATA'], 2)) . "\n";
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
		$strXml .= "\t" . '</teasers>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write footer
	 */
	protected function stepExport_writeXmlFooter($intProfileID, $arData) {
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml = '';

		$strXml .= '</mgid_teaser_goods_export>' . "\n";
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
		if (!Helper::isEmpty($arFields['AVAILABLE'])) {
			$arResult['active'] = $arFields['AVAILABLE'];
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
		}
		return array('#' => $strUrl);
	}

	/**
	 * 	Get XML tag: <category>
	 * 	У товара может быть основная категория, которая не попадает в выгрузку, поэтому нужно чтобы лишняя категория не добавлялась в <categories>
	 */
	protected function getXmlTag_Category($arProfile, $arElement) {
		$intProfileID = $arProfile['ID'];
		$intCategoryID = 0;
		if ($arElement['IBLOCK_SECTION_ID']) {
			$intCategoryID = $arElement['IBLOCK_SECTION_ID'];
		} elseif ($arElement['PARENT']['IBLOCK_SECTION_ID']) {
			$intCategoryID = $arElement['PARENT']['IBLOCK_SECTION_ID'];
		}
		$arSectionsID = array();
		if ($intCategoryID) {
			$arSectionsID[] = $intCategoryID;
		}
		if (is_array($arElement['ADDITIONAL_SECTIONS'])) {
			foreach ($arElement['ADDITIONAL_SECTIONS'] as $intAdditionalSectionID) {
				$arSectionsID[] = $intAdditionalSectionID;
			}
		}
		$intIBlockID = $arElement['IBLOCK_ID'];
		$intIBlockOffersID = $arProfile['IBLOCKS'][$arElement['IBLOCK_ID']]['_CATALOG']['PRODUCT_IBLOCK_ID'];
		$arProfileSectionsID = array();
		if (!empty($arProfile['IBLOCKS'][$intIBlockID]['SECTIONS_ID_ARRAY'])) {
			$arProfileSectionsID = &$arProfile['IBLOCKS'][$intIBlockID]['SECTIONS_ID_ARRAY'];
		} elseif (!empty($arProfile['IBLOCKS'][$intIBlockOffersID]['SECTIONS_ID_ARRAY'])) {
			$arProfileSectionsID = &$arProfile['IBLOCKS'][$intIBlockOffersID]['SECTIONS_ID_ARRAY'];
		}
		foreach ($arSectionsID as $intSectionID) {
			if (in_array($intSectionID, $arProfileSectionsID)) {
				$intCategoryID = $intSectionID;
				break;
			}
		}
		unset($arSectionsID, $intSectionID);
		return array('#' => $intCategoryID);
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