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
		\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
		\Bitrix\Highloadblock as HL,
		\Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class OzonRuGeneral extends OzonRu
{

	CONST DATE_UPDATED = '2019-09-04';
	CONST APP_ID = '466'; // DEMO
	CONST APP_SECRET = '9753260e-2324-fde7-97f1-7848ed7ed097'; // DEMO
	CONST GRAPH_VERSION = '1';
	CONST API_URL = 'api-seller.ozon.ru';
	CONST CATEGORIES_FILENAME = 'categories.txt';

//CONST API_URL = 'https://cb-api.ozonru.me';

	static $intProfileID = false;
	static $arCategoriesAttributes = [];
	static $arCategoryRedefinitionsAll = [];
	static $arExportedItems = [];
	static $arOzonSectionCodeProp = false;
	static $arOzonSectionCodePropValues = false;

	public function __construct($strModuleId)
	{
		parent::__construct($strModuleId);
	}

	public static function getCode()
	{
		return parent::getCode() . '_GENERAL';
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
		//$this->setAvailableExtension('xml');
		return $this->showDefaultSettings();
	}

	public function areCategoriesExport()
	{
		return true;
	}

	public function isCategoryStrict()
	{
		return false;
	}

	public function hasCategoryList()
	{
		return true;
	}

	/**
	 * 	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID)
	{
		$arResult = array();
		$arResult[] = array(
			'DIV' => 'tasks',
			'TAB' => static::getMessage('TAB_TASKS_NAME'),
			'TITLE' => static::getMessage('TAB_TASKS_DESC'),
			'SORT' => 20,
			'FILE' => __DIR__ . '/tabs/tasks.php',
		);
		return $arResult;
	}

	public function getAdditionalSubTabs($intProfileID, $intIBlockID)
	{
		$arResult = array();
		return $arResult;
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
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('OZON_SECTION_FROM_PROPERTY_HINT')); ?>
						<?= static::getMessage('OZON_SECTION_FROM_PROPERTY'); ?>
					</td>
					<? $checked = ($this->arProfile['PARAMS']['OZON_SECTION_FROM_PROPERTY'] == 'Y') ? 'checked="checked"' : ''; ?>
					<td width="60%" class="adm-detail-content-cell-r">
						<input <?= $checked ?> type="checkbox" name="PROFILE[PARAMS][OZON_SECTION_FROM_PROPERTY]"  value="Y" />
					</td>
				</tr>
				<? /* /$style = ($this->arProfile['PARAMS']['OZON_SECTION_FROM_PROPERTY'] != 'Y') ? 'style="display:none;"' : ''; ?>
				  <tr <?= $style ?>>
				  <td width="40%" class="adm-detail-content-cell-l">
				  <?= Helper::ShowHint(static::getMessage('OZON_SECTION_CODE_HINT')); ?>
				  <?= static::getMessage('OZON_SECTION_CODE'); ?>
				  </td>
				  <td width="60%" class="adm-detail-content-cell-r">
				  <input  type="text" name="PROFILE[PARAMS][OZON_SECTION_CODE]"  value="<?= $this->arProfile['PARAMS']['OZON_SECTION_CODE'] ?>" size="40" " />
				  </td>
				  </tr>
				  <?/* */ ?>
				<tr>
					<td width="40%" class="adm-detail-content-cell-l">
						<?= Helper::ShowHint(static::getMessage('FIRST_RUN_SYNC_HINT')); ?>
						<?= static::getMessage('FIRST_RUN_SYNC'); ?>
					</td>
					<td width="60%" class="adm-detail-content-cell-r">

						<div class="sync_ext_id_div"><a href="javascript:void(0)" class="adm-btn run_sync_ext_id" title=""><?= static::getMessage('FIRST_RUN_SYNC_BUTTON'); ?></a></div>

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
		if ($this->arProfile['PARAMS']['OZON_SECTION_FROM_PROPERTY'] == 'Y')
		{
			$arResult[] = new Field(array(
				'CODE' => 'OZON_SECTION',
				'DISPLAY_CODE' => 'OZON_SECTION',
				'NAME' => static::getMessage('FIELD_OZON_SECTION_NAME'),
				'SORT' => 40,
				'DESCRIPTION' => static::getMessage('FIELD_OZON_SECTIOND_DESC'),
				'REQUIRED' => true,
				'MULTIPLE' => false,
				'DEFAULT_VALUE' => array(
					array(
						'TYPE' => 'FIELD',
						'VALUE' => 'PROPERTY_OZON_SECTION',
					),
				),
				'PARAMS' => array(
					'HTMLSPECIALCHARS' => 'escape',
				),
			));
		}
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
			'CODE' => 'NAME',
			'DISPLAY_CODE' => 'name',
			'NAME' => static::getMessage('FIELD_NAME_NAME'),
			'SORT' => 100,
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
			'DISPLAY_CODE' => 'description',
			'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
			'SORT' => 200,
			'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
			'REQUIRED' => true,
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
			'CODE' => 'BARCODE',
			'DISPLAY_CODE' => 'barcode',
			'NAME' => static::getMessage('FIELD_BARCODE_NAME'),
			'SORT' => 300,
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
		$arResult[] = new Field(array(
			'CODE' => 'VENDOR',
			'DISPLAY_CODE' => 'vendor',
			'NAME' => static::getMessage('FIELD_VENDOR_NAME'),
			'SORT' => 800,
			'DESCRIPTION' => static::getMessage('FIELD_VENDOR_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MANUFACTURER',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'VENDOR_CODE',
			'DISPLAY_CODE' => 'vendorCode',
			'NAME' => static::getMessage('FIELD_VENDOR_CODE_NAME'),
			'SORT' => 900,
			'DESCRIPTION' => static::getMessage('FIELD_VENDOR_CODE_DESC'),
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
			'CODE' => 'IMAGES',
			'DISPLAY_CODE' => 'images',
			'NAME' => static::getMessage('FIELD_IMAGES_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_IMAGES_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
				),
			),
			$arFields['PARAMS'] = array(
		'MULTIPLE' => 'multiple',
			),
			'MAX_COUNT' => 10,
		));
		$arResult[] = new Field(array(
			'CODE' => 'HEIGHT',
			'DISPLAY_CODE' => 'height',
			'REQUIRED' => true,
			'NAME' => static::getMessage('FIELD_HEIGHT_NAME'),
			'SORT' => 1100,
			'DESCRIPTION' => static::getMessage('FIELD_HEIGHT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DEPTH',
			'DISPLAY_CODE' => 'depth',
			'REQUIRED' => true,
			'NAME' => static::getMessage('FIELD_DEPTH_NAME'),
			'SORT' => 1200,
			'DESCRIPTION' => static::getMessage('FIELD_DEPTH_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WIDTH',
			'DISPLAY_CODE' => 'width',
			'REQUIRED' => true,
			'NAME' => static::getMessage('FIELD_WIDTH_NAME'),
			'SORT' => 1300,
			'DESCRIPTION' => static::getMessage('FIELD_WIDTH_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DIMENSION_UNIT',
			'DISPLAY_CODE' => 'dimension_unit',
			'REQUIRED' => true,
			'NAME' => static::getMessage('FIELD_DIMENSION_UNIT_NAME'),
			'SORT' => 1400,
			'DESCRIPTION' => static::getMessage('FIELD_DIMENSION_UNIT_DESC'),
			'ALLOWED_VALUES' => static::getMessage('FIELD_DIMENSION_UNIT_ALLOWED_VALUES'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WEIGHT',
			'DISPLAY_CODE' => 'weight',
			'REQUIRED' => true,
			'NAME' => static::getMessage('FIELD_WEIGHT_NAME'),
			'SORT' => 1500,
			'DESCRIPTION' => static::getMessage('FIELD_WEIGHT_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'WEIGHT_UNIT',
			'DISPLAY_CODE' => 'weight_unit',
			'REQUIRED' => true,
			'NAME' => static::getMessage('FIELD_WEIGHT_UNIT_NAME'),
			'SORT' => 1600,
			'DESCRIPTION' => static::getMessage('FIELD_WEIGHT_UNIT_DESC'),
			'ALLOWED_VALUES' => static::getMessage('FIELD_WEIGHT_UNIT_ALLOWED_VALUES'),
			'REQUIRED' => true,
		));
		$categories = [];

// ozon categories, resort from id=>name TO name=>id
		foreach ($this->getCategoriesList($this->arProfile['ID']) as $id => $name)
		{
			$categories[$name] = $id;
		}
		$intIBlockID = $this->arProfile['LAST_IBLOCK_ID'];

		$arCategoryRedefinitionsAll = $this->getCategoriesRedefinitions($intIBlockID);

		$attributes = $categoriesChecked = [];

		foreach ($arCategoryRedefinitionsAll as $categoryName)
		{
			if ($categoryName)
			{
				$categoryId = $categories[$categoryName];
				if (in_array($categoryId, $categoriesChecked) || !$categoryId)
				{
					continue;
				}
				$categoriesChecked[] = $categoryId;

				//$result = static::request('/v1/category/attribute', ['category_id' => $categoryId]);
				foreach ($this->getCategoryAttr($categoryId, $intProfileID) as $attr)
				{
					if ($this->deb())
					{
						$attr['name'] = 'catId' . $categoryId . ' ' . $attr['name'];
					}

					if ($attr['type'] == 'option' && $attributes[$attr['id']])
					{
						if ($attr['option'])
						{
							array_unshift($attr['option'], ['id' => 'label', 'value' => "<b>$categoryName</b><br/>"]);
							array_push($attr['option'], ['id' => 'label', 'value' => "<br/><br/>"]);
						}
						$attributes[$attr['id']]['option'] = array_merge($attr['option'], $attributes[$attr['id']]['option']);
					} else
					{
						if ($attr['option'])
						{
							array_unshift($attr['option'], ['id' => 'label', 'value' => "<b>$categoryName</b><br/>"]);
							array_push($attr['option'], ['id' => 'label', 'value' => "<br/><br/>"]);
						}
						$attributes[$attr['id']] = $attr;
					}
				}
			}
		}

		foreach ($attributes as $attr)
		{
			$arResult = array_merge($arResult, $this->makeField($attr));
		}

#

		return $arResult;
	}

	function getOzonSectionPropCode()
	{
		return str_replace('PROPERTY_', '', $this->arProfile['IBLOCKS'][$this->arProfile['LAST_IBLOCK_ID']]['FIELDS']['OZON_SECTION']['VALUES']['0']['VALUE']);
	}

	/**
	 * 	Get category redefinitions. Check arProfile['PARAMS']['OZON_SECTION_FROM_PROPERTY'] and arProfile['PARAMS']['OZON_SECTION_CODE']
	 *  return $arCategoryRedefinitionsAll
	 */
	function getCategoriesRedefinitions($intIBlockID)
	{
		if ($this->arProfile['PARAMS']['OZON_SECTION_FROM_PROPERTY'] == 'Y')
		{
			//$strOzonSectionPropCode = $this->arProfile['PARAMS']['OZON_SECTION_CODE'];
			$strOzonSectionPropCode = $this->getOzonSectionPropCode();

			if (!self::$arOzonSectionCodeProp && $strOzonSectionPropCode)
			{
				$arFilter = ['IBLOCK_ID' => $intIBlockID, 'CODE' => $strOzonSectionPropCode];
				self::$arOzonSectionCodeProp = \CIBlockProperty::GetList(["sort" => "asc"], $arFilter)->GetNext();
			}
			if (!self::$arOzonSectionCodePropValues && self::$arOzonSectionCodeProp)
			{
				if (self::$arOzonSectionCodeProp['PROPERTY_TYPE'] == 'L')
				{
					$atFilter = ["IBLOCK_ID" => $intIBlockID, "CODE" => $strOzonSectionPropCode];
					$db = \CIBlockPropertyEnum::GetList(Array("DEF" => "DESC"), $atFilter);
					while ($res = $db->GetNext())
					{
						self::$arOzonSectionCodePropValues[] = $res['VALUE'];
					}
				} elseif (self::$arOzonSectionCodeProp['PROPERTY_TYPE'] == 'S' && self::$arOzonSectionCodeProp['USER_TYPE'] == 'directory')
				{
					$arFilter = ['TABLE_NAME' => self::$arOzonSectionCodeProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]];
					$hldata = array_pop(HL\HighloadBlockTable::getList(['filter' => $arFilter])->fetchAll());
					$entityClass = HL\HighloadBlockTable::compileEntity($hldata)->getDataClass();
					$res = $entityClass::getList(array('select' => array('*'), 'order' => array('ID' => 'ASC')))->fetchAll();
					if (is_array($res) && !empty($res))
					{
						self::$arOzonSectionCodePropValues[] = $res["UF_NAME"];
					}
				}
			}
			if (self::$arOzonSectionCodePropValues)
			{
				$arCategoryRedefinitionsAll = self::$arOzonSectionCodePropValues;
			}
		} else
		{
			$arCategoryRedefinitionsAll = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$this->arProfile['ID']]);
		}
		return $arCategoryRedefinitionsAll;
	}

	/**
	 * 	$strFileName = __DIR__ . '/cache/' . 'attributes_' . $categoryId;
	 * 	if (!is_file($strFileName) || !filesize($strFileName))
	 * 		requestBx('/v1/category/attribute', ['category_id' => $categoryId], $intProfileID);
	 * */
	function getCategoryAttr($categoryId, $intProfileID)
	{
		if (!static::$arCategoriesAttributes[$categoryId])
		{
			$strFileName = __DIR__ . '/cache/' . 'attributes_' . $categoryId;

			if (!is_file($strFileName) || !filesize($strFileName))
			{
				$result = $this->requestBx('/v1/category/attribute', ['category_id' => $categoryId], $intProfileID);
				if (file_put_contents($strFileName, serialize($result['result'])))
				{
					$bSuccess = true;
					static::$arCategoriesAttributes[$categoryId] = static::resortAttributes($result['result']);
					return static::$arCategoriesAttributes[$categoryId];
				} else
				{

					Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES', array('#FILE#' => $strFileName)), $intProfileID);
				}
			}
			if (is_file($strFileName) && filesize($strFileName) && !$result['result'])
			{
				$strResult = file_get_contents($strFileName);
				static::$arCategoriesAttributes[$categoryId] = $this->resortAttributes(unserialize($strResult));
				if (!Helper::isUtf())
				{
					static::$arCategoriesAttributes[$categoryId] = Helper::convertEncoding(static::$arCategoriesAttributes[$categoryId], 'UTF-8', 'CP1251');
				}

				return static::$arCategoriesAttributes[$categoryId];
			}
		} else
		{
			return static::$arCategoriesAttributes[$categoryId];
		}
	}

	/**
	 * $arResult[] = new Field($arFields);
	 */
	function makeField($attr)
	{
		$intProfileID = 1;
		$arResult = $arAllowedValues = [];
		if ($attr['type'] == 'option')
		{
			foreach ($attr['option'] as $option)
			{
				$arAllowedValues[$option['value']] = $option['value'];
			}
		}
		if ($attr['type'] == 'child')
		{
			foreach ($attr['child'] as $subAttr)
			{
				$sub = '';
				if ($this->deb())
					$sub = 'type[' . $attr['type'] . ']col[' . $attr['is_collection'] . ']id[' . $attr['id'] . '] ';
				$subAttr['name'] = $sub . $attr['name'] . ': ' . $subAttr['name'];
				if ($attr['description'])
				{
					if ($subAttr['description'])
					{
						$subAttr['description'] = $attr['description'] . ': ' . $subAttr['description'];
					} else
					{
						$subAttr['description'] = $attr['description'];
					}
				}
				if ($attr['is_collection'])
				{
					$subAttr['is_collection'] = $attr['is_collection'];
				}
				$arResult = array_merge($arResult, $this->makeField($subAttr));
			}
		} else
		{
			$star = ($attr['is_required']) ? ' *' : '';
			$multiple = ($attr['is_collection'] || $attr['type'] == 'option') ? true : false;
			$attr['description'] = ($attr['is_collection']) ? static::getMessage('FIELD_IS_MULTIPLE') . ' ' . $attr['description'] : $attr['description'];

			$arFields = array(
				'CODE' => $attr['id'],
				'NAME' => $attr['name'] . $star,
				'DESCRIPTION' => $attr['description'],
				//'REQUIRED' => $attr['is_required'],
				'MULTIPLE' => $attr['is_collection'],
				'ALLOWED_VALUES' => $arAllowedValues,
				'POPUP_ALLOWED_VALUES' => true,
			);
			if ($this->deb())
			{
				$arFields['NAME'] = $attr['name'] . $star . ' col=' . $attr['is_collection'] . ' t=' . $attr['type'] . ' complex=' . $attr['complex'] . ' complex_collection=' . $attr['complex_collection'];
			}
			if ($attr['is_collection'] === TRUE || $attr['type'] == 'option')
			{
				$arFields['PARAMS'] = array(
					'MULTIPLE' => 'multiple',
				);
			}
			$arResult[] = new Field($arFields);
		}
		return $arResult;
	}

	/**
	 * 	getCategoryRedefinitionName($categoryIdBitrix)
	 * 	CategoryRedefinition::getForProfile($this->arProfile['ID'])
	 * 	return $arCategoryRedefinitionsAll[$categoryIdBitrix];
	 */
	function getCategoryRedefinitionName($categoryIdBitrix)
	{
		$arCategoryRedefinitionsAll = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$this->arProfile['ID']]);
		return $arCategoryRedefinitionsAll[$categoryIdBitrix];
	}

	/**
	 * 	getCategoryOzoneID($categoryName)
	 * 	getCategoriesList($this->arProfile['ID']) as $id => $name)
	 * 	return $categories[$categoryName];
	 */
	public function getCategoryOzoneID($categoryName)
	{
		if (!$categoryName)
			return false;
		$categories = [];
		// resort from id=>name TO name=>id
		foreach ($this->getCategoriesList($this->arProfile['ID']) as $id => $name)
		{
			$categories[$name] = $id;
		}
		return $categories[$categoryName];
	}

	/**
	 *
	 */
	function getCategoryNameFromProperty($arFields)
	{
		$propertyCode = $this->arProfile['IBLOCKS'][$arElement['IBLOCK_ID']]['FIELDS']['OZON_SECTION']['VALUES']['0']['VALUE'];
		if ($propertyCode)
		{
			$propertyCode = str_replace('PROPERTY_', '', $propertyCode);
			return $arElement['PROPERTIES'][$propertyCode]['VALUE'];
			//return $arElement['PROPERTIES'][$this->arProfile['PARAMS']['OZON_SECTION_CODE']]['VALUE'];
		}
		return false;
	}

	public function processElement($arProfile, $intIBlockID, $arElement, $arFields)
	{
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
		if ($this->arProfile['PARAMS']['OZON_SECTION_FROM_PROPERTY'] == 'Y')
		{
			//$categoryName = $this->getCategoryNameFromProperty($arElement);
			$categoryName = $arFields['OZON_SECTION'];
		} else // section name from redefinition
		{
			$categoryName = $this->getCategoryRedefinitionName(reset($arElementSections));
		}

		$arFields['category_id'] = $this->getCategoryOzoneID($categoryName);
		unset($arFields['OZON_SECTION']);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnOzoneRu') as $arHandler)
		{
			ExecuteModuleEventEx($arHandler, array(&$arFields, $arProfile, $intIBlockID, $arElement, $arFields));
		}

# build result
		$arResult = array(
			'TYPE' => 'JSON',
			'DATA' => \Bitrix\Main\Web\Json::encode($arFields),
			'CURRENCY' => '',
			'DATA_MORE' => array('TIMESTAMP_X' => $arElement['TIMESTAMP_X']),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnOzoneRuResult') as $arHandler)
		{
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
# after..
		unset($arDataFields);
		return $arResult;
	}

	/**
	 * Check string for json valid
	 */
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
		$API_URL = static::API_URL;
		$arProfile = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
		if (!$arProfile['PARAMS']['APP_ID'] || !$arProfile['PARAMS']['APP_SECRET'])
			return;
		$arParams = array(
			'Client-Id' => $arProfile['PARAMS']['APP_ID'],
			'Api-Key' => $arProfile['PARAMS']['APP_SECRET']
		);
		if ($arProfile['PARAMS']['APP_ID'] == '466')
		{
			$API_URL = 'cb-api.ozonru.me';
			$arBaseParams = array_merge($arParams, $arBaseParams);
			$arBaseParams['Host'] = $API_URL;
		}
		$httpClient = new \Bitrix\Main\Web\HttpClient();
		$httpClient->setHeader('Host', $API_URL, true);
		$httpClient->setHeader('Client-Id', $arProfile['PARAMS']['APP_ID'], true);
		$httpClient->setHeader('Api-Key', $arProfile['PARAMS']['APP_SECRET'], true);
		$httpClient->setHeader('Content-Type', 'application/json', true);
		Log::getInstance($this->strModuleId)->add('requestBx $method[' . $API_URL . $method . '] $arBaseParams', $intProfileID, true);
		Log::getInstance($this->strModuleId)->add($arBaseParams, $intProfileID, true);
		if (!Helper::isUtf())
		{
			$arBaseParams = Helper::convertEncoding($arBaseParams, 'CP1251', 'UTF-8');
		}
		$json = json_encode($arBaseParams, JSON_UNESCAPED_SLASHES);
		//Log::getInstance($this->strModuleId)->add('requestBx $method[' . static::API_URL . $method . '] $json', $intProfileID, true);
		//Log::getInstance($this->strModuleId)->add($json, $intProfileID, true);
		//slog($json, 0, 0, 'requestBx $method[' . static::API_URL . $method . '] $json');
		$requestRes = $httpClient->post('https://' . $API_URL . $method, $json);
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
	 * if ($attribute['type'] == 'option')
	 * change $value to option ID
	 * 	 */
	protected function setValOption($attribute, $value, $intProfileID, $intElementId)
	{
		$valueOld = $value;
		if ($attribute['type'] == 'option')
		{
			if (is_array($value) && count($value) > 1)
			{
				$array = [];
				foreach ($value as $item)
				{
					$newVal = (string) static::resortAttributesOptionsByValue($attribute['option'])[$item];
					if ($item && !$newVal)
					{
						Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_ATTRIBUTE_OPTION_VALUE') . ' ELID ' . $intElementId . ' attributeId[' . $attribute['id'] . '] siteValue[' . $item . '] (array)', $intProfileID);
					}
					$array[] = $newVal;
				}
				return $array;
			} else
			{
				if (is_array($value))
					$valueOld = $value = array_shift($value);
				if ($attribute['is_collection'] === TRUE)
				{
					$newVal = (string) static::resortAttributesOptionsByValue($attribute['option'])[$value];
					if ($valueOld && !$newVal)
					{
						Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_ATTRIBUTE_OPTION_VALUE') . ' ELID ' . $intElementId . ' attributeId[' . $attribute['id'] . '] siteValue[' . $valueOld . ']', $intProfileID);
					}
					$value = [$newVal];
				} else
				{
					$value = (string) static::resortAttributesOptionsByValue($attribute['option'])[$value];
				}
			}
		}
		if ($valueOld && !$value)
		{
			Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_ATTRIBUTE_OPTION_VALUE') . ' ELID ' . $intElementId . ' attributeId[' . $attribute['id'] . '] siteValue[' . $valueOld . ']', $intProfileID);
		}
		return $value;
	}

	/**
	 * 	Step: Export, write offers
	 * 	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_sendApiOffers($intProfileID, $arData)
	{
		static::$intProfileID = $intProfileID;

		$this->ozonCheckTasks($intProfileID);
		//$this->ozonSyncItemsOnFirstExport($intProfileID);
		// Get export data
		$intOffset = 0;
		while (true)
		{
			$arExportedItems = $arSendNewItems = [];
			$intLimit = 10;
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
				$arExportedItems[] = ['ELEMENT_ID' => $arPostFields['offer_id'], 'IBLOCK_ID' => $arItem['IBLOCK_ID']];

				$itemData = $attributes = [];

				//$strExtId = ExternalIdTable::get($intProfileID, $arItem['IBLOCK_ID'], $arItem['ELEMENT_ID']);
				$strExtId = Helper::call($this->strModuleId, 'ExternalIdTable', 'get', [$intProfileID, $arItem['IBLOCK_ID'], $arPostFields['offer_id']]);
				if ($strExtId)
				{
					$itemData['product_id'] = (int) $strExtId;
				} else
				{
					// do sync items with ozon. Only on first export
				}
				$arCategoryAttr = $this->getCategoryAttr($arPostFields['category_id'], $intProfileID);
				// $arPostFields => $itemData
				foreach ($arPostFields as $key => $val)
				{
					if ($val)
					{
						if (!is_int($key))
						{ // FIELDS
							if ($key == 'images')
							{
								if (is_array($val))
								{
									$bDef = 0;
									foreach ($val as $k => $v)
									{
										$ar = ['file_name' => $v];
										if ($k == 0)
											$ar['default'] = true;

										$itemData['images'][] = $ar;
									}
								} else
								{
									$itemData['images'][] = ['file_name' => (string) $val, 'default' => true];
								}
							} else
							{
								if ($key == 'vat' && $val == '-')
								{
									$val = '0';
								}
								if (in_array($key, ['height', 'depth', 'width', 'weight']))
									$itemData[$key] = (int) $val;
								else
									$itemData[$key] = $val;
							}
// !FIELDS
						}
					}
				}
// ATTRIBUTES

				foreach ($arCategoryAttr as $key => $attribute)
				{
					$val = $arPostFields[$key];
					if ($attribute['type'] == 'child')
					{ // CHILD
						$valueChild = [];

						$type = 'complex';
						if ($attribute['is_collection'] === TRUE)
						{
							$type = 'complex_collection';
						}
						foreach ($attribute['child'] as $subAttr)
						{
							$val = $arPostFields[$subAttr['id']];
							if ($val)
							{

								if (is_array($val))
								{
									foreach ($val as $key => $item)
									{
										if ($item)
										{

											$valueChild[$key]['collection'][] = ['id' => $subAttr['id'], 'value' => $this->setValOption($subAttr, $item, $intProfileID, $arItem['ELEMENT_ID'])];
										}
									}
								} else
								{
									$valueChild[] = ['id' => $subAttr['id'], 'value' => $this->setValOption($subAttr, $val, $intProfileID, $arItem['ELEMENT_ID'])];
								}
							}
						}

						if ($valueChild)
							$attributes[] = ['id' => $key, $type => $valueChild];
						// !CHILD
					} else
					{
						if ($val)
						{
// IS_COLLECTION
							if ($attribute['is_collection'] === TRUE)
							{
								$collection = [];                        
								$collection = $this->setValOption($attribute, $val, $intProfileID, $arItem['ELEMENT_ID']);								
								$attributes[] = ['id' => $key, 'collection' => $collection];
// !IS_COLLECTION
							} else
							{
								$attributes[] = ['id' => $key, 'value' => $this->setValOption($attribute, $val, $intProfileID, $arItem['ELEMENT_ID'])];
							}
						}
					}
				}
// !ATTRIBUTES
				$itemData['attributes'] = $attributes;
				if ($strExtId)
				{
					// /v1/products/update
					$result = $this->requestBx('/v1/products/update', $itemData, $intProfileID);
					//$this->logErrorFromRequest($result, 'Error update product ' . $arItem['ELEMENT_ID'], $intProfileID);
					if ($result['error']['data']['0']['name'] == 'id' && $result['error']['data']['0']['code'] == 'EMPTY')
					{
						$arSendNewItems['items'][] = $itemData;
					}
				} else
				{
					$arSendNewItems['items'][] = $itemData;
				}

				$intCount ++;
			}

			if (count($arSendNewItems))
			{
				$resProductImport = $this->requestBx('/v1/product/import', $arSendNewItems, $intProfileID);
			}

			if (is_array($resProductImport) && $resProductImport['result']['task_id'])
			{
				$arData['SESSION']['EXPORT']['INDEX'] += $intCount;
				$this->saveExportedItem($resProductImport['result']['task_id'], $arExportedItems, $intProfileID);
			} else
			{
				$this->logErrorFromRequest($resProductImport, 'request /v1/product/import ', $intProfileID);
			}

			if ($intCount < $intLimit)
			{
				break;
			}
			$intOffset++;
		}
		$this->ozonCheckTasks($intProfileID);
	}

	/**
	 * 	OzonRuGeneral::getOzoneTasks($intProfileID)
	 * 	$this->requestBx('/v1/product/import/info', ['task_id' => $taskId])
	 */
	function ozonCheckTasks($intProfileID)
	{
		$arResult = $this->getOzoneTasks($intProfileID);

		foreach ($arResult['TASKS'] as $intTaskId)
		{
			$result = $this->requestBx('/v1/product/import/info', ['task_id' => $intTaskId], $intProfileID);
			foreach ($result['result']['items'] as $item)
			{
				if ($item['product_id'])
				{
					$this->setExternalData($item, $intProfileID);
				}
				unset($arResult['ITEMS'][$item['offer_id']]);
			}
		}
		if (count($arResult['ITEMS']))
		{
			$this->dropFaildExtIdRecords($arResult['ITEMS']);
		}
	}

	/**
	 * 	if on ExternalIdTable exists records with tasks, but in tasks have not this items
	 * 	drop this items from ExternalIdTable
	 * 	$items = [$offerId=>'',$offerId=>''];
	 * 	$arQuery = ['filter' => ['ELEMENT_ID' => $offerId]];
	 */
	function dropFaildExtIdRecords($items)
	{
		foreach ($items as $offerId => $null)
		{
			if (!$offerId)
				continue;
			$arQuery = ['filter' => ['ELEMENT_ID' => $offerId]];
			$arItem = Helper::call($this->strModuleId, 'ExternalIdTable', 'getList', [$arQuery])->fetch();
			if ($arItem['ID'])
			{
				Helper::call($this->strModuleId, 'ExternalIdTable', 'delete', [$arItem['ID']]);
			}
		}
	}

	/**
	 * 	if (!$arItem['EXTERNAL_ID'])
	 * 		return;
	 * 	requestBx('/v1/product/info', ['product_id' => (int) $arItem['EXTERNAL_ID']], $intProfileID);
	 * 	setExternalData($result['result'], $intProfileID);
	 */
	function ozonCheckItemStatus($arItem, $intProfileID)
	{

		if (!$arItem['EXTERNAL_ID'])
			return;
		$result = $this->requestBx('/v1/product/info', ['product_id' => (int) $arItem['EXTERNAL_ID']], $intProfileID);
		if (is_array($result) && !$result['error'])
		{
			$this->setExternalData($result['result'], $intProfileID);
		} elseif ($result['error']['code'] == 'NOT_FOUND_ERROR' && $arItem['ELEMENT_ID'])
		{
			$this->dropFaildExtIdRecords([$arItem['ELEMENT_ID'] => '']);
		}
	}

	static function getIntStatus($strExternalStatus)
	{
		$arStatus = [
			'new' => '1',
			'pending' => '2',
			'processing' => '3',
			'moderating' => '4',
			'processed' => '5',
			'failed_moderation' => '6',
			'failed_validation' => '7',
			'failed' => '8',
			'imported' => '9',
		];
		return $arStatus[$strExternalStatus];
	}

	/**
	 * 	ExternalIdTable::getList
	 * 	ExternalIdTable::update
	 */
	function setExternalData($arItemExt, $intProfileID)
	{

		$intExtProductId = $arItemExt['product_id'];
		if (!$arItemExt['product_id'] && $arItemExt['id'])
			$intExtProductId = $arItemExt['id'];

		if (!$arItemExt['status'] && $arItemExt['state'])// /v1/product/info && /v1/product/import/info
			$arItemExt['status'] = $arItemExt['state'];

		if (!$arItemExt['offer_id'] || !$intExtProductId)
			return;

		//$arItem = ExternalIdTable::getList(['filter' => ['ELEMENT_ID' => $arItemExt['offer_id'], 'PROFILE_ID' => $intProfileID]])->fetch();
		$arQuery = ['filter' => ['ELEMENT_ID' => $arItemExt['offer_id'], 'PROFILE_ID' => $intProfileID]];
		$arItem = Helper::call($this->strModuleId, 'ExternalIdTable', 'getList', [$arQuery])->fetch();

		$arItemExternalData = unserialize($arItem['EXTERNAL_DATA']);
		$arFields = ['EXTERNAL_STATUS' => $this->getIntStatus($arItemExt['status']), 'EXTERNAL_ID' => $intExtProductId];
		if ($arFields['EXTERNAL_STATUS'] == 9)
		{
			$arItemExternalData['TASK_ID'] = '';
		}
		$arFields['EXTERNAL_DATA'] = serialize($arItemExternalData);
		if (intval($arItem['ID']) > 0)
		{
			//ExternalIdTable::update($arItem['ID'], $arFields);
			Helper::call($this->strModuleId, 'ExternalIdTable', 'update', [$arItem['ID'], $arFields]);
		} else
		{
			$arFields['ELEMENT_ID'] = $arItemExt['offer_id'];
			$arFields['PROFILE_ID'] = $intProfileID;

			Helper::call($this->strModuleId, 'ExternalIdTable', 'add', [$arFields]);
		}
	}

	/**
	 * 	ExternalIdTable::add
	 */
	function saveExportedItem($intOzoneTaskId, $arItems, $intProfileID)
	{
		if (!$intOzoneTaskId)
			return;
		foreach ($arItems as $arItem)
		{
			$arQuery = ['filter' => ['ELEMENT_ID' => $arItem['ELEMENT_ID'], 'PROFILE_ID' => $intProfileID]];
			$arItemExisted = Helper::call($this->strModuleId, 'ExternalIdTable', 'getList', [$arQuery])->fetch();
			$arItem['PROFILE_ID'] = $intProfileID;
			$arItem['EXTERNAL_STATUS'] = 1;
			$arItem['EXTERNAL_DATA'] = serialize(['TASK_ID' => $intOzoneTaskId]);
			if (!$arItemExisted['ID'])
			{
				Helper::call($this->strModuleId, 'ExternalIdTable', 'add', [$arItem]);
			} else
			{
				Helper::call($this->strModuleId, 'ExternalIdTable', 'update', [$arItemExisted['ID'], $arItem]);
			}
		}
	}

	/**
	 *  ExternalIdTable::getList(['filter' => ['EXTERNAL_STATUS' => '1', 'PROFILE_ID' => $intProfileID]])
	 */
	public function getOzoneTasks($intProfileID)
	{
		$arResult = [];
		//$obResDb = ExternalIdTable::getList(['filter' => ['PROFILE_ID' => $intProfileID]]);
		$obResDb = Helper::call($this->strModuleId, 'ExternalIdTable', 'getList', [['filter' => ['PROFILE_ID' => $intProfileID]]]);
		//$obResDb = ExternalIdTable::getList(['filter' => ['EXTERNAL_STATUS' => '1', 'PROFILE_ID' => $intProfileID]]);
		$arTasks = [];
		while ($arItem = $obResDb->fetch())
		{
			$arExternalData = unserialize($arItem['EXTERNAL_DATA']);
			if ($arExternalData['TASK_ID'])
			{
				$arTasks[$arExternalData['TASK_ID']] = '';
				$arResult['ITEMS'][$arItem['ELEMENT_ID']] = '';
			}
		}
		$arResult['TASKS'] = array_keys($arTasks);
		return $arResult;
	}

	/**
	 * 	make array with key "id"
	 */
	protected static function resortAttributes($array)
	{
		$newArray = [];
		foreach ($array as $item)
		{
			$newArray[$item['id']] = $item;
		}
		return $newArray;
	}

	/**
	 * 	make array with key "value"
	 * 	foreach ($array as $item)
	 * 	{
	 * 		$newArray[$item['value']] = $item['id'];
	 * 	}
	 */
	protected static function resortAttributesOptionsByValue($array)
	{
		$newArray = [];
		foreach ($array as $item)
		{
			$newArray[$item['value']] = $item['id'];
		}
		return $newArray;
	}

	/**
	 * 	Update categories from server
	 * 	requestBx('/v1/category/tree', ['language' => LANGUAGE_ID], $intProfileID);
	 */
	public function updateCategories($intProfileID)
	{
		$bSuccess = false;
		$result = $this->requestBx('/v1/category/tree', ['language' => LANGUAGE_ID], $intProfileID);
		if ($result['result'])
		{
			$strFileName = static::getCategoriesCacheFile();
			if (is_file($strFileName))
			{
				unlink($strFileName);
			}
			if (file_put_contents($strFileName, serialize($result['result'])))
			{
				$bSuccess = true;
			} else
			{
				Log::getInstance($this->strModuleId)->add(static::getMessage('ERROR_SAVING_CATEGORIES', array('#FILE#' => $strFileName)), $intProfileID);
			}
		} else
		{
			Log::getInstance($this->strModuleId)->add(static::getMessage('CATEGORIES_EMPTY_ANSWER', array('#URL#' => '/v1/category/tree')), $intProfileID);
		}
		return $bSuccess;
	}

	/**
	 * 	Get categories date update
	 */
	public function getCategoriesDate()
	{
		$strFileName = static::getCategoriesCacheFile();
		return is_file($strFileName) ? filemtime($strFileName) : false;
	}

	/**
	 * 	Get categories list
	 * 	All categories in OZON.RU
	 */
	public function getCategoriesList($intProfileID)
	{

		$strFileName = $this->getCategoriesCacheFile();

		if (!is_file($strFileName) || !filesize($strFileName))
		{
			$this->updateCategories($intProfileID);
		}
		if (is_file($strFileName) && filesize($strFileName))
		{
			$strResult = file_get_contents($strFileName);
			$categories = $this->getCategoryPath(unserialize($strResult));
			if (!Helper::isUtf())
			{
				$categories = Helper::convertEncoding($categories, 'UTF-8', 'CP1251');
			}

			return $categories;
		}
		return false;
	}

	/**
	 * id => names path (breadcrumbs)
	 */
	protected function getCategoryPath($items)
	{
		$categories = [];
		foreach ($items as $item)
		{
			foreach ($item['children'] as $item2)
			{
				if ($item2['children'])
				{
					foreach ($item2['children'] as $item3)
					{
						if ($item3['children'])
						{
							foreach ($item3['children'] as $item4)
							{
								$categories[$item4['category_id']] = $item['title'] . ' / ' . $item2['title'] . ' / ' . $item3['title'] . ' / ' . $item4['title'];
							}
						} else
						{
							$categories[$item3['category_id']] = $item['title'] . ' / ' . $item2['title'] . ' / ' . $item3['title'];
						}
					}
				} else
				{
					$categories[$item2['category_id']] = $item['title'] . ' / ' . $item2['title'];
				}
			}
		}
		return $categories;
	}

	/**
	 * 	Get filename for categories cache
	 */
	protected function getCategoriesCacheFile()
	{
		$strCacheDir = __DIR__ . '/cache';
		if (!is_dir($strCacheDir))
		{
			mkdir($strCacheDir, BX_DIR_PERMISSIONS, true);
		}
		return $strCacheDir . '/' . static::CATEGORIES_FILENAME;
	}

	/**
	 * TEMP NOT USED
	 */
	protected static function request($method, array $arParams = Array(), $intProfileID)
	{
//$arProfile = Profile::getProfiles($intProfileID);
		$arBaseParams = array(
			'Client-Id' => static::APP_ID,
			'Api-Key' => static::APP_SECRET
		);
		$arBaseParams = array_merge($arBaseParams, $arParams);


		$arParams = [
			'METHOD' => 'POST',
			'HEADER' => implode("\r\n", [
				'Content-Type: application/json',
				'Client-Id: ' . static::APP_ID,
				'Api-Key: ' . static::APP_SECRET,
			]),
			'CONTENT' => json_encode($arBaseParams),
		];

		$res = HttpRequest::getHttpContent(static::API_URL . $method, $arParams);

		return \Bitrix\Main\Web\Json::decode($res);
// BITRIX
		if (!Helper::isUtf())
		{
			$arParams = Helper::convertEncoding($arParams, 'CP1251', 'UTF-8');
		}
		$arParams = array_merge($arBaseParams, $arParams);
		$httpClient = new \Bitrix\Main\Web\HttpClient();
//$httpClient->setHeader('Host', 'cb-api.ozonru.me', true);
		$httpClient->setHeader('Client-Id', static::APP_ID, true);
		$httpClient->setHeader('Api-Key', static::APP_SECRET, true);
		$httpClient->setHeader('Content-Type', 'application/json', true);

		$res = $httpClient->post(static::API_URL . $method, json_encode($arBaseParams));
//$res = Helper::convertEncoding($res, 'UTF-8', 'CP1251');
//		$res = HttpRequest::get(static::API_URL . $method, $arBaseParams);

		try
		{
			$arRes = \Bitrix\Main\Web\Json::decode($res, true);
		} catch (Exception $e)
		{

		}
		usleep(350000);
		return $arRes;
	}

	/**
	 * 	Custom ajax actions
	 * 		update_items_status
	 * 		sync_ext_id
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult)
	{
		$intProfileID = &$arParams['PROFILE_ID'];

		switch ($strAction)
		{
			case 'update_items_status':
				$this->ozonCheckTasks($intProfileID);

				//$db = ExternalIdTable::getList(['filter' => ['PROFILE_ID' => $intProfileID]]);
				$db = Helper::call($this->strModuleId, 'ExternalIdTable', 'getList', [['filter' => ['PROFILE_ID' => $intProfileID, '!EXTERNAL_STATUS' => '9']]]);
				while ($arItem = $db->fetch())
				{
					$this->ozonCheckItemStatus($arItem, $intProfileID);
				}
				ob_start();
				$this->ozoneItemsStatusTable($intProfileID, $strModuleId);
				$arJsonResult['Text'] = ob_get_clean();
				break;
			case 'sync_ext_id':
				ob_start();
				echo $this->ozonSyncItemsOnFirstExport($intProfileID);
				//OzonRuGeneral::ozoneItemsStatusTable($intProfileID);
				$arJsonResult['Text'] = ob_get_clean();
				break;
		}
	}

	/**
	 * 	ExternalIdTable  getList
	 * 	table
	 */
	public function ozoneItemsStatusTable($intProfileID, $strModuleId)
	{

		$arItems = [];

		//$db = ExternalIdTable::getList(['filter' => ['PROFILE_ID' => $intProfileID]]);
		$db = Helper::call($this->strModuleId, 'ExternalIdTable', 'getList', [['filter' => ['PROFILE_ID' => $intProfileID]]]);
		$arStatus = [];
		while ($arItem = $db->fetch())
		{
			$arItems[] = $arItem;
			$arItemsStatus[$arItem['EXTERNAL_STATUS']] ++;
		}
		?>

		<table style="margin: auto 40%;">
			<tr>
				<td style="text-align: left;"><?= static::getMessage('EXPORTED_ALL') ?></td>
				<td><?= count($arItems) ?></td>
			</tr>
			<tr>
				<td colspan="2"><b><?= static::getMessage('STATUS_EXPORTED') ?>:</b></td>
			</tr>

			<?
			foreach ($this->getStatusTitles() as $key => $title)
			{
				?>
				<tr>
					<td><?= $title ?></td>
					<td><?= $arItemsStatus[$key] ?></td>
				</tr>
			<? }
			?>

		</table>
		<?
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
				$strErrorMessage .= ', [' . $error['name'] . ']' . $error['code'] . ' - ' . $error['value'] . '(' . $error['message'] . ')';
			}

			Log::getInstance($this->strModuleId)->add('Error ' . $strActionTitle . ': code[' . $result['error']['code'] . ']message[' . $result['error']['message'] . ']' . $strErrorMessage, $intProfileID);
		}
	}

	/**
	 *
	 */
	public function getStatusTitles()
	{
		return [
			'1' => static::getMessage('STATUS_1'),
			'2' => static::getMessage('STATUS_2'),
			'3' => static::getMessage('STATUS_3'),
			'4' => static::getMessage('STATUS_4'),
			'5' => static::getMessage('STATUS_5'),
			'6' => static::getMessage('STATUS_6'),
			'7' => static::getMessage('STATUS_7'),
			'8' => static::getMessage('STATUS_8'),
			'9' => static::getMessage('STATUS_9'),
		];
	}

	/**
	 * requestBx('/v1/product/list', ['page' => $page], $intProfileID);
	 */
	public function ozonGetItems($page = 1, $intProfileID)
	{

		return $this->requestBx('/v1/product/list', ['page' => $page], $intProfileID);
		//pp($result, true);
	}

	/**
	 * 	Syncing items which existed on ozon, only first export
	 * 	ozonGetItems($page, $intProfileID);
	 * 	ozonProceedSyncItems($result['result']['items'], $iblockIds, $intProfileID);
	 */
	public function ozonSyncItemsOnFirstExport($intProfileID)
	{

		//Profile::setParam($intProfileID,['' => '']);
		//$arProfile = Profile::getProfiles($intProfileID);
		$arProfile = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
		if (!$arProfile['PARAMS']['APP_ID'] || !$arProfile['PARAMS']['APP_SECRET'])
			return;
		//if($arProfile['PARAMS']['FIRST_EXPORT_SYNC_ITEMS']=='Y')
		$iblockIds = array_keys($arProfile['IBLOCKS']);
		while (true)
		{
			$page = $_SESSION['EXPORT_OZONE'][$intProfileID]['PAGE'];
			if (!$page)
				$page = 1;
			$result = $this->ozonGetItems($page, $intProfileID);

			if ($result['result']['total'] > 0)
			{
				$countAllPages = ceil($result['result']['total'] / 100);

				$this->ozonProceedSyncItems($result['result']['items'], $iblockIds, $intProfileID);
				if ($page != $countAllPages)
				{
					$_SESSION['EXPORT_OZONE'][$intProfileID]['PAGE'] = ($page + 1);
				} else
				{
					return 'OK';
				}
			}

			if ($page > $countAllPages)
			{
				die('stop');
			}
		}
	}

	/**
	 * 	requestBx('/v1/product/info', ['product_id' => $item['product_id']], $intProfileID);
	 * 	ExternalIdTable add update
	 */
	public function ozonProceedSyncItems($items, $iblockIds, $intProfileID)
	{


		foreach ($items as $item)
		{

			$arProfile = Helper::call($strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
			$strSyncFieldCode = $arProfile['IBLOCKS'][$iblockIds[0]]['FIELDS']['OFFER_ID']['VALUES']['0']['VALUE'];
			$result = $this->requestBx('/v1/product/info', ['product_id' => $item['product_id']], $intProfileID);
			if (!Helper::isUtf())
			{
				$result['result']['name'] = Helper::convertEncoding($result['result']['name'], 'UTF-8', 'CP1251');
			}

			if ($result['result']['name'])
			{
				$arFilter = [
					'LOGIC' => 'OR',
					[$strSyncFieldCode => $result['result']['offer_id']],
					['NAME' => $result['result']['name']],
				];
				$arElement = \CIBlockElement::GetList(false, $arFilter, false, false, ['ID', 'IBLOCK_ID'])->Fetch();

				if ($arElement['ID'])
				{
					$arFields = ['ELEMENT_ID' => $arElement['ID'], 'EXTERNAL_ID' => $item['product_id'], 'IBLOCK_ID' => $arElement['IBLOCK_ID']];
					$arFields['PROFILE_ID'] = $intProfileID;
					$arFields['EXTERNAL_STATUS'] = 9;

					//$arExtIdRecord = ExternalIdTable::getList(['filter' => ['PROFILE_ID' => $intProfileID, 'ELEMENT_ID' => $arElement['ID']]])->fetch();
					$arQuery = ['filter' => ['PROFILE_ID' => $intProfileID, 'ELEMENT_ID' => $arElement['ID']]];
					$arExtIdRecord = Helper::call($this->strModuleId, 'ExternalIdTable', 'getList', [$arQuery])->fetch();
					if (!$arExtIdRecord['ID'])
					{
						//$arExtIdRecord = ExternalIdTable::getList(['filter' => ['PROFILE_ID' => $intProfileID, 'EXTERNAL_ID' => $item['product_id']]])->fetch();
						$arQuery = ['filter' => ['PROFILE_ID' => $intProfileID, 'EXTERNAL_ID' => $item['product_id']]];
						$arExtIdRecord = Helper::call($this->strModuleId, 'ExternalIdTable', 'getList', [$arQuery])->fetch();
					}
					if ($arExtIdRecord['ID'])
					{
						//ExternalIdTable::update($arExtIdRecord['ID'], $arFields);
						Helper::call($this->strModuleId, 'ExternalIdTable', 'update', [$arExtIdRecord['ID'], $arFields]);
					} else
					{
						//$res = ExternalIdTable::add($arFields);
						$res = Helper::call($this->strModuleId, 'ExternalIdTable', 'add', [$arFields]);
					}




					//ExternalIdTable::update($arItem['ID'], ['ELEMENT_ID' => $arElement['ID'], 'EXTERNAL_ID' => $item['product_id']]);
					// TODO
					//
					//$arItem = $db->fetch()
				}
			}
		}
	}

	public function deb()
	{
		return $_GET['deb'] != '';
	}

}
?>