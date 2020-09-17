<?
/**
 *  Acrit Core: OZON.RU plugin
 * 	@documentation https://cb-api.ozonru.me/apiref/ru/
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Bitrix\Main\EventManager,
		\Acrit\Core\Helper,
		\Acrit\Core\HttpRequest,
		\Acrit\Core\Export\Plugin,
		\Acrit\Core\Export\Field\Field,
		\Acrit\Core\Export\Exporter,
		\Acrit\Core\Export\ExternalIdTable,
		\Acrit\Core\Export\ProfileTable as Profile,
		\Acrit\Core\Export\ProfileIBlockTable as ProfileIBlock,
		\Acrit\Core\Export\Filter,
		\Acrit\Core\Export\ExportDataTable as ExportData,
		\Acrit\Core\Log,
		\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition;

Loc::loadMessages(__FILE__);

class OzonRuQuantity extends OzonRu
{

	CONST DATE_UPDATED = '2019-08-23';
	CONST APP_ID = '466'; // DEMO
	CONST APP_SECRET = '9753260e-2324-fde7-97f1-7848ed7ed097'; // DEMO
	CONST GRAPH_VERSION = '1';
	CONST API_URL = 'https://api-seller.ozon.ru';
	CONST CATEGORIES_FILENAME = 'categories.txt';

//CONST API_URL = 'https://cb-api.ozonru.me';

	static $intProfileID = false;
	static $arCategoriesAttributes = [];
	static $arCategoryRedefinitionsAll = [];
	static $arExportedItems = [];

	public function __construct($strModuleId)
	{
		parent::__construct($strModuleId);
	}

	public static function getCode()
	{
		return parent::getCode() . '_QUANTITY';
	}

	public static function getName()
	{
		return static::getMessage('NAME');
	}

	public static function isSubclass()
	{
		return true;
	}

	public function showSettings()
	{
		$this->setAvailableExtension('xml');
		return $this->showDefaultSettings();
	}

	public function areCategoriesExport()
	{
		return false;
	}

	public function isCategoryStrict()
	{
		return false;
	}

	public function hasCategoryList()
	{
		return false;
	}

	/* END OF BASE STATIC METHODS */

	protected function showDefaultSettings()
	{
		ob_start();
		echo Helper::showHeading(static::getMessage('SETTINGS_TITLE'));
		?>
		<? if (!strlen($this->arProfile['PARAMS']['CATEGORIES_REDEFINITION_MODE'])): ?>
			<input type="hidden" name="PROFILE[PARAMS][CATEGORIES_REDEFINITION_MODE]" value="<?= CategoryRedefinition::MODE_STRICT; ?>" />
		<? endif ?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">

					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<a target="_blank" href="https://seller.ozon.ru/settings/api-keys"><?= static::getMessage('SETTINGS_GET_KEY'); ?></a>

					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;color: red;">
						<?= static::getMessage('WARNINGS'); ?>
					</td>

				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= static::getMessage('SETTINGS_CLIENT_ID'); ?>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][APP_ID]"  value="<?= $this->arProfile['PARAMS']['APP_ID'] ?>" size="40" />
					</td>
				</tr>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= static::getMessage('SETTINGS_API_KEY'); ?>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">
						<input type="text" name="PROFILE[PARAMS][APP_SECRET]"  value="<?= $this->arProfile['PARAMS']['APP_SECRET'] ?>" size="40" />
					</td>
				</tr>
			</tbody>
		</table>
		<?
		//pp(static::request('/v1/category/attribute', ['category_id' => '17038143']), true);


		return ob_get_clean();
	}

	public function getFields($intProfileID, $intIBlockID, $bAdmin = false)
	{
		$arResult = [];
		$arResult[] = new Field(array(
			'CODE' => 'OFFER_ID',
			'DISPLAY_CODE' => 'offer_id',
			'NAME' => static::getMessage('FIELD_OFFER_ID_NAME'),
			'SORT' => 50,
			'DESCRIPTION' => static::getMessage('FIELD_OFFER_ID_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'ID',
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'escape',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'QUANTITY',
			'DISPLAY_CODE' => 'stock',
			'NAME' => static::getMessage('FIELD_QUANTITY_NAME'),
			'SORT' => 100,
			'DESCRIPTION' => static::getMessage('FIELD_QUANTITY_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_QUANTITY',
				),
			)
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'price',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 400,
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
			'SORT' => 500,
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
			'CODE' => 'PREMIUM_PRICE',
			'DISPLAY_CODE' => 'premium_price',
			'NAME' => static::getMessage('FIELD_PREMIUM_PRICE_NAME'),
			'SORT' => 600,
			'DESCRIPTION' => static::getMessage('FIELD_PREMIUM_PRICE_DESC'),
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
			'CODE' => 'VAT',
			'DISPLAY_CODE' => 'vat',
			'NAME' => static::getMessage('FIELD_VAT_NAME'),
			'SORT' => 700,
			'DESCRIPTION' => static::getMessage('FIELD_VAT_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_VAT_VALUE',
				),
			),
		));

#

		return $arResult;
	}

	public function processElement($arProfile, $intIBlockID, $arElement, $arFields)
	{

		global $APPLICATION;

		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnOzoneRuQuantity') as $arHandler)
		{
			ExecuteModuleEventEx($arHandler, array(&$arFields, $arProfile, $intIBlockID, $arElement, $arFields));
		}

# build result
		$arResult = array(
			'TYPE' => 'JSON',
			'DATA' => \Bitrix\Main\Web\Json::encode($arFields),
			'DATA_MORE' => array('TIMESTAMP_X' => $arElement['TIMESTAMP_X']),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnOzoneRuQuantityResult') as $arHandler)
		{
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
# after..
		unset($arDataFields);
		return $arResult;
	}

	static function json_validate($string)
	{
		if (is_string($string))
		{
			@json_decode($string);
			return (json_last_error() === JSON_ERROR_NONE);
		}
		return false;
	}

	/**
	 * \Bitrix\Main\Web\HttpClient()
	 */
	public function requestBx($method, $arBaseParams, $intProfileID)
	{
		$arProfile = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
		if (!$arProfile['PARAMS']['APP_ID'] || !$arProfile['PARAMS']['APP_SECRET'])
			return;
		$arParams = array(
			'Client-Id' => $arProfile['PARAMS']['APP_ID'],
			'Api-Key' => $arProfile['PARAMS']['APP_SECRET']
		);
		$httpClient = new \Bitrix\Main\Web\HttpClient();
		$httpClient->setHeader('Host', static::API_URL, true);
		$httpClient->setHeader('Client-Id', $arProfile['PARAMS']['APP_ID'], true);
		$httpClient->setHeader('Api-Key', $arProfile['PARAMS']['APP_SECRET'], true);
		$httpClient->setHeader('Content-Type', 'application/json', true);
		if (!Helper::isUtf())
		{
			$arBaseParams = Helper::convertEncoding($arBaseParams, 'CP1251', 'UTF-8');
		}
		$json = json_encode($arBaseParams);
		Log::getInstance($this->strModuleId)->add('requestBx $method[' . static::API_URL . $method . '] $json', $intProfileID, true);
		Log::getInstance($this->strModuleId)->add($json, $intProfileID, true);
		//slog($json, 0, 0, 'requestBx $method[' . static::API_URL . $method . '] $json');
		$requestRes = $httpClient->post(static::API_URL . $method, $json);
		if ($this->json_validate($requestRes))
			$requestRes = json_decode($requestRes, true);
		Log::getInstance($this->strModuleId)->add('requestBx $requestRes json_decode', $intProfileID, true);
		Log::getInstance($this->strModuleId)->add($requestRes, $intProfileID, true);
		//slog($requestRes, 0, 0, 'requestBx $requestRes json_decode');
		return $requestRes;
	}

	/**
	 * 	Get steps
	 */
	public function getSteps()
	{
		$arResult = array();
		$arResult['EXPORT'] = array(
			'NAME' => static::getMessage('STEP_EXPORT'),
			'SORT' => 100,
			'FUNC' => [$this, 'stepExport'],
		);
		return $arResult;
	}

	/**
	 * 	Step: Export
	 */
	public function stepExport($intProfileID, $arData)
	{
		$this->stepExport_sendApiOffers($intProfileID, $arData);

		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * 	Step: Export, write offers
	 * 	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_sendApiOffers($intProfileID, $arData)
	{

		static::$intProfileID = $intProfileID;

		// Get export data
		$intOffset = 0;
		while (true)
		{
			$arSendNewItemsStocks = $arSendNewItemsPrices = [];
			$intLimit = 99;
			$strSortOrder = ToUpper($arData['PROFILE']['PARAMS']['SORT_ORDER']);
			if (!in_array($strSortOrder, array('ASC', 'DESC')))
			{
				$strSortOrder = 'ASC';
			}
			$arQuery = array(
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'!TYPE' => ExportData::TYPE_DUMMY,
				),
				'order' => array(
					'SORT' => $strSortOrder,
				),
				'select' => array(
					'IBLOCK_ID',
					'ELEMENT_ID',
					'SECTION_ID',
					'TYPE',
					'DATA',
					'DATA_MORE',
				),
				'limit' => $intLimit,
				'offset' => $intOffset * $intLimit,
			);
			#$resItems = ExportData::getList($arQuery);
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$intCount = 0;

			while ($arItem = $resItems->fetch())
			{
				$arPostFields = array_change_key_case(\Bitrix\Main\Web\Json::decode($arItem['DATA']), CASE_LOWER);

				$itemDataStock = $itemDataPrice = [];

				$itemDataPrice['offer_id'] = (string) $arPostFields['offer_id'];
				$itemDataPrice['price'] = (string) $arPostFields['price'];
				$itemDataPrice['old_price'] = (string) $arPostFields['old_price'];
				$itemDataPrice['premium_price'] = (string) $arPostFields['premium_price'];
				$itemDataPrice['vat'] = (string) $arPostFields['vat'];

				$arSendNewItemsPrices['prices'][] = $itemDataPrice;

				$itemDataStock['offer_id'] = (string) $arPostFields['offer_id'];
				$itemDataStock['stock'] = (int) $arPostFields['quantity'];
				$arSendNewItemsStocks['stocks'][] = $itemDataStock;

				$intCount ++;
			}
			// STOCKS
			if (count($arSendNewItemsStocks))
			{
				$resProductImport = $this->requestBx('/v1/product/import/stocks', $arSendNewItemsStocks, $intProfileID);
			}
			if (is_array($resProductImport) && $resProductImport['result'])
			{
				$arData['SESSION']['EXPORT']['INDEX'] += $intCount;
			} else
			{
				$this->logErrorFromRequest($resProductImport, 'request /v1/product/import/stocks ', $intProfileID);
			}
			// PRICES
			if (count($arSendNewItemsPrices))
			{
				$resProductImport = $this->requestBx('/v1/product/import/prices', $arSendNewItemsPrices, $intProfileID);
			}

			if (is_array($resProductImport) && $resProductImport['result'])
			{

			} else
			{
				$this->logErrorFromRequest($resProductImport, 'request /v1/product/import/prices ', $intProfileID);
			}

			if ($intCount < $intLimit)
			{
				break;
			}
			$intOffset++;
		}
	}

	/**
	 * Log::getInstance
	 */
	public function logErrorFromRequest($result, $strActionTitle, $intProfileID)
	{
		if ($result['error'])
		{
			$strErrorMessage = '';
			foreach ($result['error']['data'] as $error)
			{
				$strErrorMessage .= ', ' . $error['key'] . '-' . $error['value'];
			}
			$strErrorMessage = $result['error']['code'] = $result['error']['message'];
			Log::getInstance($this->strModuleId)->add('Error ' . $strActionTitle . ': code[' . $result['error']['code'] . ']message[' . $result['error']['message'] . ']' . $strErrorMessage, $intProfileID);
		}
	}

	public function deb()
	{
		return $_GET['deb'] != '';
	}

}
?>