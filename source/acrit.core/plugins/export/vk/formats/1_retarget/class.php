<?
/**
 * Acrit Core: Retargeting VK plugin
 * @package acrit.core
 * @copyright 2019 Acrit
 * @documentation https://vk.com/faq12163
 */
namespace Acrit\Core\Export\Plugins;

use Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition;
use Acrit\Core\Export\Filter;
use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class VkRetargeting extends Vk {
	
	CONST DATE_UPDATED = '2019-12-02';

	protected $strFileExt;
    CONST CATEGORIES_FILENAME = 'categories.txt';
	
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
        return parent::getCode().'_RETARGETING';
    }
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}

	public function getDefaultExportFilename(){
		return 'vk_retargeting.xml';
	}

	/**
	 *	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported(){
		return true;
	}
	
    /**
     *	Is it subclass?
     */
    public static function isSubclass(){
        return true;
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

	public function getRetargetingCurrency($currency) {
		$arCurrencies = array('RUB','RUR','BYN','BYR','UAH','KZT','USD','EUR');
		return $currency == "all" ? $arCurrencies : $arCurrencies[$currency];
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
							           id="acrit_exp_plugin_xml_filename"
							           value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['EXPORT_FILE_NAME']);?>"
							           size="40" placeholder="<?=static::getMessage('SETTINGS_FILE_PLACEHOLDER');?>" /></td>
							<td><input type="button" value="..." onclick="AcritExpPluginXmlFilenameSelect()" /></td>
							<td>
								&nbsp;
								<?=$this->showFileOpenLink();?>
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

	protected function showShopSettings(){
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
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
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SHOP_URL_HINT'));?>
					<b><?=static::getMessage('SHOP_URL');?>:</b>
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][SHOP_URL]"
					       value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['SHOP_URL']);?>" size="50" />
				</td>
			</tr>
<?/*
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::showHint(static::getMessage('RETARGETING_CURRENCY_HINT'));?>
					<b><?=static::getMessage('RETARGETING_CURRENCY');?>:</b>
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<?
					$arCurrenciesId = self::getRetargetingCurrency("all");
					$arCurrenciesId = array(
						'REFERENCE' => array_values($arCurrenciesId),
						'REFERENCE_ID' => array_keys($arCurrenciesId),
					);
					print SelectBoxFromArray('PROFILE[PARAMS][CURRENCIES]', $arCurrenciesId,
						$this->arProfile['PARAMS']['CURRENCIES'], '', 'id="acrit_exp_plugin_currencies"');
					?>
				</td>
			</tr>
*/?>
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
			</tbody>
		</table>
		<?
		return ob_get_clean();
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

    /* END OF BASE STATIC METHODS */
	
	/**
	 *	Get adailable fields for current plugin
	 */
    public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
        $arResult = array();
        $arResult[] = new Field(array(
            'CODE' => 'ID',
            'DISPLAY_CODE' => 'offer_id',
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
                'MAXLENGTH' => '2000',
                'HTMLSPECIALCHARS' => 'escape',
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
        ));

        /*
         * Optional fields
         */
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
            'CODE' => 'DESCRIPTION',
            'DISPLAY_CODE' => 'description',
            'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
            'SORT' => 540,
            'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'CDATA' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'DETAIL_TEXT',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '3000',
                'HTMLSPECIALCHARS' => 'escape',
            )
        ));
        $arResult[] = new Field(array(
            'CODE' => 'AVAILABLE',
            'DISPLAY_CODE' => 'available',
            'NAME' => static::getMessage('FIELD_AVAILABLE_NAME'),
            'SORT' => 580,
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
            'CODE' => 'GROUP_ID',
            'DISPLAY_CODE' => 'group_id',
            'NAME' => static::getMessage('FIELD_GROUP_ID_NAME'),
            'SORT' => 1050,
            'DESCRIPTION' => static::getMessage('FIELD_GROUP_ID_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_GROUP_ID',
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
            'CODE' => 'REC',
            'DISPLAY_CODE' => 'rec',
            'NAME' => static::getMessage('FIELD_REC_NAME'),
            'SORT' => 140,
            'DESCRIPTION' => static::getMessage('FIELD_REC_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_RECOMMENDED',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'VENDORCODE',
            'DISPLAY_CODE' => 'vendorCode',
            'NAME' => static::getMessage('FIELD_VENDORCODE_NAME'),
            'SORT' => 140,
            'DESCRIPTION' => static::getMessage('FIELD_VENDORCODE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_CML2_ARTICLE',
                ),
            ),
        ));
        #
        return $arResult;
    }


    /**
     *	Process single element (generate XML)
     *	@return array
     */
    public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
        $intProfileID = $arProfile['ID'];
        $intElementID = $arElement['ID'];

        # Prepare data
        $bOffer = $arElement['IS_OFFER'];
        if($bOffer) {
            $arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
            $arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
        }
        else {
            $arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
            $arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
        }
        # Build XML
        $arXmlTags = array();
        if(!Helper::isEmpty($arFields['URL']))
            $arXmlTags['url'] = Xml::addTag($arFields['URL']);
        if(!Helper::isEmpty($arFields['PICTURE']))
            $arXmlTags['picture'] = Xml::addTag($arFields['PICTURE']);
        if(!Helper::isEmpty($arFields['NAME']))
            $arXmlTags['name'] = Xml::addTag($arFields['NAME']);
        if(!Helper::isEmpty($arFields['PRICE'])) {
            $arXmlTags['price'] = Xml::addTag($arFields['PRICE']);
//            $arXmlTags['currencyId'] = self::getRetargetingCurrency($arProfile['PROFILE']['PARAMS']['CURRENCIES']);
        }
	    if(!Helper::isEmpty($arFields['CURRENCY_ID']))
		    $arXmlTags['currencyId'] = Xml::addTag($arFields['CURRENCY_ID']);
        $arXmlTags['categoryId'] = $this->getXmlTag_Category($arProfile, $arElement);
         if(!Helper::isEmpty($arFields['DESCRIPTION']))
            $arXmlTags['description'] = Xml::addTag($arFields['DESCRIPTION']);
        if(!Helper::isEmpty($arFields['GROUP_ID']))
            $arXmlTags['group_id'] = Xml::addTag($arFields['GROUP_ID']);
        if(!Helper::isEmpty($arFields['AGE']))
            $arXmlTags['age'] = Xml::addTag($arFields['AGE']);
        if(!Helper::isEmpty($arFields['REC']))
            $arXmlTags['rec'] = Xml::addTag($arFields['REC']);
        if(!Helper::isEmpty($arFields['VENDORCODE']))
            $arXmlTags['vendorCode'] = Xml::addTag($arFields['VENDORCODE']);

        # Build XML
        $arXml = array(
            'offer' => array(
                '@' => $this->getXmlAttr($arFields),
                '#' => $arXmlTags,
            ),
        );

        # Event handler OnOnYandexZenXml
        foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexZenXml') as $arHandler) {
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

        # Event handler OnOnYandexZenXml
        foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexZenResult') as $arHandler) {
            ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
        }

        # after..
        unset($intProfileID, $intElementID, $arXmlTags, $arXml);
        return $arResult;
    }


    /*
     *  Get offer attribute: id
     */
    protected function getXmlAttr($arFields){
        $arResult = array(
            'id' => $arFields['ID'],
            'available' => $arFields['AVAILABLE'],
        );
        return $arResult;
    }


    /**
     *	Get XML tag: <category>
     */
    protected function getXmlTag_Category($arProfile, $arElement){
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
                $mValue = $intCategoryID;
            }
            unset($arCategoryRedefinitions, $arSectionsID, $intSectionID, $intCategoryID);
        }
        return array('#' => $mValue);
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

		# SubStep2 [each <offer>]
		if(!isset($arSession['XML_OFFERS_WROTE'])){
			$this->stepExport_writeXmlOffers($intProfileID, $arData);
			$arSession['XML_OFFERS_WROTE'] = true;
		}

		# SubStep3 [footer]
		if(!isset($arSession['XML_FOOTER_WROTE'])){
			$this->stepExport_writeXmlFooter($intProfileID, $arData);
			$arSession['XML_FOOTER_WROTE'] = true;
		}

		# SubStep4 [tmp => real]
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

		# SubStep9
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
		$strXml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'."\n";
		$strXml .= '<yml_catalog date="'.$strDate.'">'."\n";
		$strXml .= "\t".'<shop>'."\n";
		$strXml .= "\t".'<name>'.$arData['PROFILE']['PARAMS']['SHOP_NAME'].'</name>'."\n";
		$strXml .= "\t".'<company>'.$arData['PROFILE']['PARAMS']['SHOP_COMPANY'].'</company>'."\n";
		$strXml .= "\t".'<url>'.$arData['PROFILE']['PARAMS']['SHOP_URL'].'</url>'."\n";
//		$strXml .= "\t".'<currencies>'.self::getCurrencyTag($arData['PROFILE']['PARAMS']['CURRENCIES']).'</currencies>'."\n";
		$strXml .= "\t".self::stepExport_writeXmlCategories($arData['PROFILE']['ID'],$arData)."\n";
		$strXml .= "\t".'<offers>'."\n";
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 *	Get Currency tag
	 */
	protected function getCurrencyTag($currencyId) {
		$arXml = array(
			'currency' => array(
				'@' => self::getCurrencyAttr($currencyId),
			),
		);
		return Xml::arrayToXml($arXml);
	}

	/**
	 *	Get Currency tag arttributes
	 */
	protected function getCurrencyAttr($currencyId) {
		return $arResult = array(
			'id' => self::getRetargetingCurrency($currencyId),
			'rate' => '1'
		);
	}

	/**
	 *	Step: Export, write categories
	 */
	protected function stepExport_writeXmlCategories($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		# All categories for XML
		$arCategoriesForXml = array();
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
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			while($arItem = $resItems->fetch()){
				$arItemSectionsID = array();
				if(is_numeric($arItem['SECTION_ID']) && $arItem['SECTION_ID']>0) {
					$arItemSectionsID[] = $arItem['SECTION_ID'];
				}
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

		$arData['PROFILE']['PARAMS']['CATEGORIES_REDEFINITION_MODE'] = CategoryRedefinition::MODE_CUSTOM;
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
		return $strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
	}

	/**
	 *	Step: Export, write offers
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeXmlOffers($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
		#
		$intOffset = 0;
		while(true){
			$intLimit = 5000;
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
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

	/**
	 *	Step: Export, write footer
	 */
	protected function stepExport_writeXmlFooter($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml = '';
		$strXml .= "\t".'</offers>'."\n";
		$strXml .= "\t".'</shop>'."\n";
		$strXml .= "\t".'</yml_catalog>'."\n";
		$strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}

}

?>