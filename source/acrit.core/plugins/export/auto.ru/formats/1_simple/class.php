<?
/**
 * Acrit Core: Auto.ru plugin
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Bitrix\Main\EventManager,
		\Acrit\Core\Helper,
		\Acrit\Core\HttpRequest,
		\Acrit\Core\Log,
		\Acrit\Core\Xml,
		\Acrit\Core\Export\Plugin,
		\Acrit\Core\Export\Field\Field,
		\Acrit\Core\Export\Filter,
		\Acrit\Core\Export\CurrencyConverter\Base as CurrencyConverterBase,
		\Acrit\Core\Export\Exporter,
		\Acrit\Core\Export\ExportDataTable as ExportData;

Loc::loadMessages(__FILE__);

class AutoRuSimple extends AutoRu
{

	CONST DATE_UPDATED = '2018-11-13';

	protected static $bSubclass = true;

	/**
	 * Base constructor
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
		return parent::getCode() . '_SIMPLE';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName()
	{
		return static::getMessage('NAME');
	}

	/* END OF BASE STATIC METHODS */

	public function getDefaultExportFilename()
	{
		return 'auto_ru.xml';
	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false)
	{
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$arResult[] = new Field(array(
			'CODE' => 'MARK_ID',
			'DISPLAY_CODE' => 'mark_id',
			'NAME' => static::getMessage('FIELD_MARK_ID_NAME'),
			'SORT' => 100,
			'DESCRIPTION' => static::getMessage('FIELD_MARK_ID_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'FOLDER_ID',
			'DISPLAY_CODE' => 'folder_id',
			'NAME' => static::getMessage('FIELD_FOLDER_ID_NAME'),
			'SORT' => 110,
			'DESCRIPTION' => static::getMessage('FIELD_FOLDER_ID_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODIFICATION_ID',
			'DISPLAY_CODE' => 'modification_id',
			'NAME' => static::getMessage('FIELD_MODIFICATION_ID_NAME'),
			'SORT' => 120,
			'DESCRIPTION' => static::getMessage('FIELD_MODIFICATION_ID_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'BODY_TYPE',
			'DISPLAY_CODE' => 'body_type',
			'NAME' => static::getMessage('FIELD_BODY_TYPE_NAME'),
			'SORT' => 130,
			'DESCRIPTION' => static::getMessage('FIELD_BODY_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'WHEEL',
			'DISPLAY_CODE' => 'wheel',
			'NAME' => static::getMessage('FIELD_WHEEL_NAME'),
			'SORT' => 140,
			'DESCRIPTION' => static::getMessage('FIELD_WHEEL_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'COLOR',
			'DISPLAY_CODE' => 'color',
			'NAME' => static::getMessage('FIELD_COLOR_NAME'),
			'SORT' => 150,
			'DESCRIPTION' => static::getMessage('FIELD_COLOR_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'METALLIC',
			'DISPLAY_CODE' => 'metallic',
			'NAME' => static::getMessage('FIELD_METALLIC_NAME'),
			'SORT' => 160,
			'DESCRIPTION' => static::getMessage('FIELD_METALLIC_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'AVAILABILITY',
			'DISPLAY_CODE' => 'availability',
			'NAME' => static::getMessage('FIELD_AVAILABILITY_NAME'),
			'SORT' => 170,
			'DESCRIPTION' => static::getMessage('FIELD_AVAILABILITY_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CUSTOM',
			'DISPLAY_CODE' => 'custom',
			'NAME' => static::getMessage('FIELD_CUSTOM_NAME'),
			'SORT' => 180,
			'DESCRIPTION' => static::getMessage('FIELD_CUSTOM_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'STATE',
			'DISPLAY_CODE' => 'state',
			'NAME' => static::getMessage('FIELD_STATE_NAME'),
			'SORT' => 190,
			'DESCRIPTION' => static::getMessage('FIELD_STATE_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'OWNERS_NUMBER',
			'DISPLAY_CODE' => 'owners_number',
			'NAME' => static::getMessage('FIELD_OWNERS_NUMBER_NAME'),
			'SORT' => 200,
			'DESCRIPTION' => static::getMessage('FIELD_OWNERS_NUMBER_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'RUN',
			'DISPLAY_CODE' => 'run',
			'NAME' => static::getMessage('FIELD_RUN_NAME'),
			'SORT' => 210,
			'DESCRIPTION' => static::getMessage('FIELD_RUN_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'YEAR',
			'DISPLAY_CODE' => 'year',
			'NAME' => static::getMessage('FIELD_YEAR_NAME'),
			'SORT' => 220,
			'DESCRIPTION' => static::getMessage('FIELD_YEAR_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'REGISTRY_YEAR',
			'DISPLAY_CODE' => 'registry_year',
			'NAME' => static::getMessage('FIELD_REGISTRY_YEAR_NAME'),
			'SORT' => 230,
			'DESCRIPTION' => static::getMessage('FIELD_REGISTRY_YEAR_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'price',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 240,
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
			'CODE' => 'CURRENCY',
			'DISPLAY_CODE' => 'currency',
			'NAME' => static::getMessage('FIELD_CURRENCY_NAME'),
			'SORT' => 250,
			'DESCRIPTION' => static::getMessage('FIELD_CURRENCY_DESC'),
			'REQUIRED' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1__CURRENCY',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'VIN',
			'DISPLAY_CODE' => 'vin',
			'NAME' => static::getMessage('FIELD_VIN_NAME'),
			'SORT' => 260,
			'DESCRIPTION' => static::getMessage('FIELD_VIN_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DESCRIPTION',
			'DISPLAY_CODE' => 'description',
			'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
			'SORT' => 270,
			'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
			'REQUIRED' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_TEXT'
				),
			),
			'PARAMS' => array('HTMLSPECIALCHARS' => 'cdata'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'EXTRAS',
			'DISPLAY_CODE' => 'extras',
			'NAME' => static::getMessage('FIELD_EXTRAS_NAME'),
			'SORT' => 280,
			'DESCRIPTION' => static::getMessage('FIELD_EXTRAS_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'IMAGES',
			'DISPLAY_CODE' => 'images',
			'NAME' => static::getMessage('FIELD_IMAGES_NAME'),
			'SORT' => 290,
			'DESCRIPTION' => static::getMessage('FIELD_IMAGES_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
				),
			),
			'MAX_COUNT' => 30,
		));

		$arResult[] = new Field(array(
			'CODE' => 'VIDEO',
			'DISPLAY_CODE' => 'video',
			'NAME' => static::getMessage('FIELD_VIDEO_NAME'),
			'SORT' => 300,
			'DESCRIPTION' => static::getMessage('FIELD_VIDEO_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'POI_ID',
			'DISPLAY_CODE' => 'poi_id',
			'NAME' => static::getMessage('FIELD_POI_ID_NAME'),
			'SORT' => 310,
			'DESCRIPTION' => static::getMessage('FIELD_POI_ID_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'SALE_SERVICES',
			'DISPLAY_CODE' => 'sale_services',
			'NAME' => static::getMessage('FIELD_SALE_SERVICES_NAME'),
			'SORT' => 320,
			'DESCRIPTION' => static::getMessage('FIELD_SALE_SERVICES_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'FRESH_SWITCH',
			'DISPLAY_CODE' => 'fresh > switch',
			'NAME' => static::getMessage('FIELD_FRESH_SWITCH_NAME'),
			'SORT' => 340,
			'DESCRIPTION' => static::getMessage('FIELD_FRESH_SWITCH_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'FRESH_WEEKDAYS',
			'DISPLAY_CODE' => 'fresh > weekdays',
			'NAME' => static::getMessage('FIELD_FRESH_WEEKDAYS_NAME'),
			'SORT' => 350,
			'DESCRIPTION' => static::getMessage('FIELD_FRESH_WEEKDAYS_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'FRESH_TIME',
			'DISPLAY_CODE' => 'fresh > time',
			'NAME' => static::getMessage('FIELD_FRESH_TIME_NAME'),
			'SORT' => 360,
			'DESCRIPTION' => static::getMessage('FIELD_FRESH_TIME_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'FRESH_FREQUENCY',
			'DISPLAY_CODE' => 'fresh > frequency',
			'NAME' => static::getMessage('FIELD_FRESH_FREQUENCY_NAME'),
			'SORT' => 370,
			'DESCRIPTION' => static::getMessage('FIELD_FRESH_FREQUENCY_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CONTACT_INFO_CONTACT',
			'DISPLAY_CODE' => 'contact_info > contact> name',
			'NAME' => static::getMessage('FIELD_CONTACT_INFO_CONTACT_NAME'),
			'SORT' => 380,
			'DESCRIPTION' => static::getMessage('FIELD_CONTACT_INFO_CONTACT_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CONTACT_INFO_PHONE',
			'DISPLAY_CODE' => 'contact_info > contact > phone',
			'NAME' => static::getMessage('FIELD_CONTACT_INFO_PHONE_NAME'),
			'SORT' => 390,
			'DESCRIPTION' => static::getMessage('FIELD_CONTACT_INFO_PHONE_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CONTACT_INFO_TIME',
			'DISPLAY_CODE' => 'contact_info > contact > time',
			'NAME' => static::getMessage('FIELD_CONTACT_INFO_TIME_NAME'),
			'SORT' => 400,
			'DESCRIPTION' => static::getMessage('FIELD_CONTACT_INFO_TIME_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'WARRANTY_EXPIRE',
			'DISPLAY_CODE' => 'warranty_expire',
			'NAME' => static::getMessage('FIELD_WARRANTY_EXPIRE_NAME'),
			'SORT' => 410,
			'DESCRIPTION' => static::getMessage('FIELD_WARRANTY_EXPIRE_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'PTS',
			'DISPLAY_CODE' => 'pts',
			'NAME' => static::getMessage('FIELD_PTS_NAME'),
			'SORT' => 420,
			'DESCRIPTION' => static::getMessage('FIELD_PTS_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'STS',
			'DISPLAY_CODE' => 'sts',
			'NAME' => static::getMessage('FIELD_STS_NAME'),
			'SORT' => 430,
			'DESCRIPTION' => static::getMessage('FIELD_STS_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'ARMORED',
			'DISPLAY_CODE' => 'armored',
			'NAME' => static::getMessage('FIELD_ARMORED_NAME'),
			'SORT' => 440,
			'DESCRIPTION' => static::getMessage('FIELD_ARMORED_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'UNIQUE_ID',
			'DISPLAY_CODE' => 'unique_id',
			'NAME' => static::getMessage('FIELD_UNIQUE_ID_NAME'),
			'SORT' => 450,
			'DESCRIPTION' => static::getMessage('FIELD_UNIQUE_ID_DESC'),
			'REQUIRED' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'ID',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODIFICATION_CODE',
			'DISPLAY_CODE' => 'modification-code',
			'NAME' => static::getMessage('FIELD_MODIFICATION_CODE_NAME'),
			'SORT' => 460,
			'DESCRIPTION' => static::getMessage('FIELD_MODIFICATION_CODE_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'COLOR_CODE',
			'DISPLAY_CODE' => 'color-code',
			'NAME' => static::getMessage('FIELD_COLOR_CODE_NAME'),
			'SORT' => 470,
			'DESCRIPTION' => static::getMessage('FIELD_COLOR_CODE_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'INTERIOR_CODE',
			'DISPLAY_CODE' => 'interior-code',
			'NAME' => static::getMessage('FIELD_INTERIOR_CODE_NAME'),
			'SORT' => 480,
			'DESCRIPTION' => static::getMessage('FIELD_INTERIOR_CODE_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'EQUIPMENT_CODE',
			'DISPLAY_CODE' => 'equipment-code',
			'NAME' => static::getMessage('FIELD_EQUIPMENT_CODE_NAME'),
			'SORT' => 490,
			'DESCRIPTION' => static::getMessage('FIELD_EQUIPMENT_CODE_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'ACTION',
			'DISPLAY_CODE' => 'action',
			'NAME' => static::getMessage('FIELD_ACTION_NAME'),
			'SORT' => 500,
			'DESCRIPTION' => static::getMessage('FIELD_ACTION_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'EXCHANGE',
			'DISPLAY_CODE' => 'exchange',
			'NAME' => static::getMessage('FIELD_EXCHANGE_NAME'),
			'SORT' => 510,
			'DESCRIPTION' => static::getMessage('FIELD_EXCHANGE_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DISCOUNT_PRICE',
			'DISPLAY_CODE' => 'discount_price',
			'NAME' => static::getMessage('FIELD_DISCOUNT_PRICE_NAME'),
			'SORT' => 520,
			'DESCRIPTION' => static::getMessage('FIELD_DISCOUNT_PRICE_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DISCOUNT_PRICE_SHOW',
			'DISPLAY_CODE' => 'discount_price show',
			'NAME' => static::getMessage('FIELD_DISCOUNT_PRICE_SHOW_NAME'),
			'SORT' => 530,
			'DESCRIPTION' => static::getMessage('FIELD_DISCOUNT_PRICE_SHOW_DESC'),
			'REQUIRED' => false,
		));

		#
		$this->sortFields($arResult);
		return $arResult;
	}

	/**
	 * 	Process single element (generate XML)
	 * 	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields)
	{
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];


		# Build XML
		$arXmlTags = array();

		$arTagWithSub = ['FRESH', 'CONTACT', 'DISCOUNT_PRICE'];
		foreach ($arFields as $code => $value)
		{
			$smallCode = strtolower($code);
			$isSubTag = 0;
			// take all subtags by tag
			foreach ($arTagWithSub as $tagCode)
			{
				if (strpos($code, $tagCode . '_') !== false)
				{
					$arAllSubTags[$tagCode][] = $code;
					$isSubTag = 1;
				}
			}

			if (in_array($code, ['IMAGES', 'MODIFICATION_CODE', 'COLOR_CODE', 'INTERIOR_CODE', 'EQUIPMENT_CODE']) || $isSubTag)
			{
				continue;
			}
			if (!Helper::isEmpty($arFields[$code]))
			{
				$arXmlTags[$smallCode] = Xml::addTag($arFields[$code]);
			}
		}
		if (!Helper::isEmpty($arFields['IMAGES']))
		{
			$arXmlTags['images'] = Xml::addTagWithSubtags($arFields['IMAGES'], 'image');
		}
		if (!Helper::isEmpty($arFields['MODIFICATION_CODE']))
		{
			$arXmlTags['modification-code'] = Xml::addTag($arFields['MODIFICATION_CODE']);
		}
		if (!Helper::isEmpty($arFields['COLOR_CODE']))
		{
			$arXmlTags['color-code'] = Xml::addTag($arFields['COLOR_CODE']);
		}
		if (!Helper::isEmpty($arFields['INTERIOR_CODE']))
		{
			$arXmlTags['interior-code'] = Xml::addTag($arFields['INTERIOR_CODE']);
		}
		if (!Helper::isEmpty($arFields['EQUIPMENT_CODE']))
		{
			$arXmlTags['equipment-code'] = Xml::addTag($arFields['EQUIPMENT_CODE']);
		}
		if (!Helper::isEmpty($arFields['EQUIPMENT_CODE']))
		{
			$arXmlTags['equipment-code'] = Xml::addTag($arFields['EQUIPMENT_CODE']);
		}
		if (!Helper::isEmpty($arFields['FRESH_SWITCH']))
		{
			$arXmlTags['service_auto_apply'] = [[
			'#' => [
				'fresh' => [[
				'#' => [
					'switch' => ['#' => $arFields['FRESH_SWITCH']],
					'weekdays' => ['#' => $arFields['FRESH_WEEKDAYS']],
					'time' => ['#' => $arFields['FRESH_TIME']],
					'frequency' => ['#' => $arFields['FRESH_FREQUENCY']],
				],
					]]
			],
			]];
		}
		if (!Helper::isEmpty($arFields['CONTACT_INFO_CONTACT']))
		{
			$arXmlTags['contact_info'] = [[
			'#' => [
				'contact' => [[
				'#' => [
					'name' => ['#' => $arFields['CONTACT_INFO_CONTACT']],
					'phone' => ['#' => $arFields['CONTACT_INFO_PHONE']],
					'time' => ['#' => $arFields['CONTACT_INFO_TIME']]
				],
					]]
			],
			]];
		}
		if (!Helper::isEmpty($arFields['DISCOUNT_PRICE']))
		{
			$arXmlTags['discount_price'] = [[
			'#' => [
				'price' => [['#' => $arFields['DISCOUNT_PRICE']]],
				'action' => [['#' => $arFields['DISCOUNT_PRICE_SHOW']]]
			],
			]];
		}
		# Build XML
		$arXml = array(
			'car' => array(
				'#' => $arXmlTags,
			),
		);

		# Event handler OnYandexMarketXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAutoRuXml') as $arHandler)
		{
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}


		# Build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => Xml::arrayToXml($arXml),
		);

		# Event handler OnYandexMarketResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAutoRuResult') as $arHandler)
		{
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}

		# Ending..
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
		<?= $this->showFileOpenLink($arSession['EXPORT']['XML_FILE_URL_ZIP'], static::getMessage('RESULT_FILE_ZIP')); ?>
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
			if ($arData['PROFILE']['PARAMS']['COMPRESS_TO_ZIP'] == 'Y')
			{
				$arSession['XML_FILE_ZIP'] = Helper::changeFileExt($_SERVER['DOCUMENT_ROOT'] . $strExportFilename, 'zip');
				$arSession['XML_FILE_URL_ZIP'] = Helper::changeFileExt($strExportFilename, 'zip');
			}
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


		# SubStep2 [each <offer>]
		if (!isset($arSession['XML_OFFERS_WROTE']))
		{
			$this->stepExport_writeXmlOffers($intProfileID, $arData);
			$arSession['XML_OFFERS_WROTE'] = true;
		}


		# SubStep3 [footer]
		if (!isset($arSession['XML_FOOTER_WROTE']))
		{
			$this->stepExport_writeXmlFooter($intProfileID, $arData);
			$arSession['XML_FOOTER_WROTE'] = true;
		}

		# SubStep4 [tmp => real]
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

		# SubStep11
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
		$strEncoding = $arData['PROFILE']['PARAMS']['ENCODING'];
		#
		$strDate = (new \Bitrix\Main\Type\DateTime())->format('Y-m-d H:i');
		$strXml = '';
		$strXml .= '<?xml version="1.0" encoding="' . $strEncoding . '"?>' . "\n";
		$strXml .= '<data>' . "\n";
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write cars
	 * 	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData)
	{
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml .= "\t\t" . '<cars>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
		#
		$intLimit = 5000;
		$intOffset = 0;
		while (true)
		{
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
			$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
			file_put_contents($strFile, $strXml, FILE_APPEND);
			if ($intCount < $intLimit)
			{
				break;
			}
			$intOffset++;
		}
		#
		$strXml = '';
		$strXml .= "\t\t" . '</cars>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
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
		$strXml .= "\t" . '</data>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

}
?>