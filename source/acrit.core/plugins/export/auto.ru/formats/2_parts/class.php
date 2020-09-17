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

class AutoRuParts extends AutoRu
{

	CONST DATE_UPDATED = '2018-11-27';

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
		return parent::getCode() . '_PARTS';
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
		return 'auto_ru_parts.xml';
	}

	/**
	 * 	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported()
	{
		return true;
	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false)
	{
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$arResult[] = new Field(array(
			'CODE' => 'ID',
			'DISPLAY_CODE' => 'id',
			'NAME' => static::getMessage('FIELD_ID_NAME'),
			'SORT' => 100,
			'DESCRIPTION' => static::getMessage('FIELD_ID_DESC'),
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'ID',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TITLE',
			'DISPLAY_CODE' => 'title',
			'NAME' => static::getMessage('FIELD_TITLE_NAME'),
			'SORT' => 110,
			'DESCRIPTION' => static::getMessage('FIELD_TITLE_DESC'),
			'REQUIRED' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'NAME',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'STORES',
			'DISPLAY_CODE' => 'stores',
			'NAME' => static::getMessage('FIELD_STORES_NAME'),
			'SORT' => 120,
			'DESCRIPTION' => static::getMessage('FIELD_STORES_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PART_NUMBER',
			'DISPLAY_CODE' => 'part_number',
			'NAME' => static::getMessage('FIELD_PART_NUMBER_NAME'),
			'SORT' => 130,
			'DESCRIPTION' => static::getMessage('FIELD_PART_NUMBER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MANUFACTURER',
			'DISPLAY_CODE' => 'manufacturer',
			'NAME' => static::getMessage('FIELD_MANUFACTURER_NAME'),
			'SORT' => 140,
			'DESCRIPTION' => static::getMessage('FIELD_MANUFACTURER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DESCRIPTION',
			'DISPLAY_CODE' => 'description',
			'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
			'SORT' => 150,
			'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_TEXT'
				),
			),
			'PARAMS' => array('HTMLSPECIALCHARS' => 'cdata'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'IS_NEW',
			'DISPLAY_CODE' => 'is_new',
			'NAME' => static::getMessage('FIELD_IS_NEW_NAME'),
			'SORT' => 160,
			'DESCRIPTION' => static::getMessage('FIELD_IS_NEW_DESC'),
		));

		$arResult[] = new Field(array(
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'price',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 170,
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
			'CODE' => 'AVAILABILITY_ISAVAILABLE',
			'DISPLAY_CODE' => 'isAvailable',
			'NAME' => static::getMessage('FIELD_AVAILABILITY_ISAVAILABLE_NAME'),
			'SORT' => 180,
			'DESCRIPTION' => static::getMessage('FIELD_AVAILABILITY_ISAVAILABLE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AVAILABILITY_DAYSFROM',
			'DISPLAY_CODE' => 'daysFrom',
			'NAME' => static::getMessage('FIELD_AVAILABILITY_DAYSFROM_NAME'),
			'SORT' => 185,
			'DESCRIPTION' => static::getMessage('FIELD_AVAILABILITY_DAYSFROMM_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AVAILABILITY_DAYSTO',
			'DISPLAY_CODE' => 'daysTo',
			'NAME' => static::getMessage('FIELD_AVAILABILITY_DAYSTO_NAME'),
			'SORT' => 190,
			'DESCRIPTION' => static::getMessage('FIELD_AVAILABILITY_DAYSTO_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'IMAGES',
			'DISPLAY_CODE' => 'images',
			'NAME' => static::getMessage('FIELD_IMAGES_NAME'),
			'SORT' => 195,
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
			'CODE' => 'COMPATIBILITY',
			'DISPLAY_CODE' => 'compatibility',
			'NAME' => static::getMessage('FIELD_COMPATIBILITY_NAME'),
			'SORT' => 200,
			'DESCRIPTION' => static::getMessage('FIELD_COMPATIBILITY_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'COUNT',
			'DISPLAY_CODE' => 'count',
			'NAME' => static::getMessage('FIELD_COUNT_NAME'),
			'SORT' => 210,
			'DESCRIPTION' => static::getMessage('FIELD_COUNT_DESC'),
			'REQUIRED' => false,
		));
		$arResult[] = new Field(array(
			'CODE' => 'IS_FOR_PRIORITY',
			'DISPLAY_CODE' => 'is_for_priority',
			'NAME' => static::getMessage('FIELD_IS_FOR_PRIORITY_NAME'),
			'SORT' => 220,
			'DESCRIPTION' => static::getMessage('FIELD_IS_FOR_PRIORITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ANALOG',
			'DISPLAY_CODE' => 'analog part_number',
			'NAME' => static::getMessage('FIELD_ANALOG_NAME'),
			'SORT' => 230,
			'DESCRIPTION' => static::getMessage('FIELD_ANALOG_DESC'),
			'MULTIPLE' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'OFFER_URL',
			'DISPLAY_CODE' => 'offer_url',
			'NAME' => static::getMessage('FIELD_OFFER_URL_NAME'),
			'SORT' => 900,
			'DESCRIPTION' => static::getMessage('FIELD_OFFER_URL_DESC'),
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

		$arTagWithSub = ['AVAILABILITY'];
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

			if (in_array($code, ['IMAGES', 'STORES', 'COMPATIBILITY', 'ANALOG']) || $isSubTag)
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
		if (!Helper::isEmpty($arFields['STORES']))
		{
			$arXmlTags['stores'] = Xml::addTagWithSubtags($arFields['STORES'], 'store');
		}
		if (!Helper::isEmpty($arFields['COMPATIBILITY']))
		{
			$arXmlTags['compatibility'] = Xml::addTagWithSubtags($arFields['COMPATIBILITY'], 'car');
		}

		if (!Helper::isEmpty($arFields['ANALOG']))
		{

			if (!is_array($arFields['ANALOG']))
				$arFields['ANALOG'] = [$arFields['ANALOG']];
			foreach ($arFields['ANALOG'] as $analog)
			{
				$arAnalogs[] = [
					'#' => [
						'analog' => [[
						'#' => ['part_number' => [['#' => $analog]]],
							]]
					],
						]
				;
			}
			$arXmlTags['analogs'] = $arAnalogs;
		}
		if (!Helper::isEmpty($arFields['AVAILABILITY_ISAVAILABLE']))
		{
			$arXmlTags['availability'] = [[
			'#' => [
				'isAvailable' => [['#' => $arFields['AVAILABILITY_ISAVAILABLE']]],
				'daysFrom' => [['#' => $arFields['AVAILABILITY_DAYSFROM']]],
				'daysTo' => [['#' => $arFields['AVAILABILITY_DAYSTO']]]
			],
			]];
		}
		$arXmlTags['properties'] = $this->getXmlTag_Param($arProfile, $intIBlockID, $arFields);
		# Build XML
		$arXml = array(
			'part' => array(
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
		$strXml .= '<parts>' . "\n";
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 * 	Step: Export, write cars
	 * 	@return Exporter::RESULT_SUCCESS || Exporter::RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData)
	{
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#

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
		$strXml .= "\t" . '</parts>' . "\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
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

}
?>