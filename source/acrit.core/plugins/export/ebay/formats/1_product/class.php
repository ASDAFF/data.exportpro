<?
/**
* Acrit Core: Ebay.com plugin
* @package acrit.core
* @copyright 2018 Acrit
* @documentation http://pages.ebay.com/ru/ru-ru/kak-prodavat-na-ebay-spravka/mip-neobhodimie-dannie.html
*/
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Log,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class EbayProduct extends Ebay {

    CONST DATE_UPDATED = '2019-08-08';

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
        return parent::getCode().'_PRODUCT';
    }
    /**
     * Get plugin short name
     */
    public static function getName() {
        return static::getMessage('NAME');
    }

    /**
     *	Is it subclass?
     */
    public static function isSubclass(){
        return true;
    }

    /* END OF BASE STATIC METHODS */

    public function getDefaultExportFilename(){
        return 'ebay_product.xml';
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
                    <?=Helper::showHint(static::getMessage('EBAY_LOCALIZED_HINT'));?>
                    <b><?=static::getMessage('EBAY_LOCALIZED');?>:</b>
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <?
                    $arChannelId = parent::getEbayLocal();
                    $arChannelId = array(
                        'REFERENCE' => array_values($arChannelId),
                        'REFERENCE_ID' => array_keys($arChannelId),
                    );
                    print SelectBoxFromArray('PROFILE[PARAMS][LOCALIZED]', $arChannelId,
                        $this->arProfile['PARAMS']['LOCALIZED'], '', 'id="acrit_exp_plugin_localized"');
                    ?>
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

    /**
     *	Get adailable fields for current plugin
     */
    public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
        $arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
        #

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
                'MAXLENGHT' => 150,
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'DESCRIPTION',
            'DISPLAY_CODE' => 'description',
            'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
            'SORT' => 520,
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
            'PARAMS' => array('HTMLSPECIALCHARS' => 'cut'),
        ));
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
                    'CONST' => 'in_stock',
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
            'CODE' => 'CONDITION',
            'DISPLAY_CODE' => 'condition',
            'NAME' => static::getMessage('FIELD_CONDITION_NAME'),
            'SORT' => 340,
            'DESCRIPTION' => static::getMessage('FIELD_CONDITION_DESC'),
            'REQUIRED' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'New',
                ),
            ),
        ));
				/*
        $arResult[] = new Field(array(
            'CODE' => 'IMAGE',
            'DISPLAY_CODE' => 'image',
            'NAME' => static::getMessage('FIELD_IMAGE_NAME'),
            'SORT' => 550,
            'DESCRIPTION' => static::getMessage('FIELD_IMAGE_DESC'),
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
				*/
        $arResult[] = new Field(array(
            'CODE' => 'LENGTH',
            'DISPLAY_CODE' => 'length',
            'NAME' => static::getMessage('FIELD_LENGTH_NAME'),
            'SORT' => 3000,
            'DESCRIPTION' => static::getMessage('FIELD_LENGTH_DESC'),
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
            'CODE' => 'WIDTH',
            'DISPLAY_CODE' => 'width',
            'NAME' => static::getMessage('FIELD_WIDTH_NAME'),
            'SORT' => 3000,
            'DESCRIPTION' => static::getMessage('FIELD_WIDTH_DESC'),
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
            'CODE' => 'HEIGHT',
            'DISPLAY_CODE' => 'height',
            'NAME' => static::getMessage('FIELD_HEIGHT_NAME'),
            'SORT' => 3000,
            'DESCRIPTION' => static::getMessage('FIELD_HEIGHT_DESC'),
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
            'CODE' => 'IMAGE',
            'DISPLAY_CODE' => 'image',
            'NAME' => static::getMessage('FIELD_IMAGE_NAME'),
            'SORT' => 525,
            'DESCRIPTION' => static::getMessage('FIELD_IMAGE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'DETAIL_PICTURE',
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PREVIEW_PICTURE',
                ),
            ),
            'PARAMS' => array(
                'MULTIPLE' => 'first',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'BRAND',
            'DISPLAY_CODE' => 'brand',
            'NAME' => static::getMessage('FIELD_BRAND_NAME'),
            'SORT' => 3000,
            'DESCRIPTION' => static::getMessage('FIELD_BRAND_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'Brand',
                ),
            ),
        ));
        #
        $this->sortFields($arResult);
        return $arResult;
    }

    /**
     *	Process single element
     *	@return array
     */
    public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
        $intProfileID = $arProfile['ID'];
        $intElementID = $arElement['ID'];
        # Build XML
        $arXmlTags = array();
        //$arSkuTag = array();

        if(!Helper::isEmpty($arFields['ID']))
            $arXmlTags['SKU'] = Xml::addTag($arFields['ID']);
        $arXmlTags['productInformation'] = $this->getXmlTag_ProductInformation($intProfileID,$arFields);

        # Build XML
        $arXml = array(
            'product' => array(
                '#' => $arXmlTags,
            ),
        );

        foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnEbayXml') as $arHandler) {
            ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
        }
        $strXml = Xml::arrayToXml($arXml);
				if(!Helper::isUtf()){
					$strXml = Helper::convertEncoding($strXml, 'CP1251', 'UTF-8');
				}
        # build result
        $arResult = array(
            'TYPE' => 'XML',
            'DATA' => $strXml,
            'CURRENCY' => '',
            'SECTION_ID' => $this->getElement_SectionID($intProfileID, $arElement),
            'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
            'DATA_MORE' => array(),
        );
        foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnEbayResult') as $arHandler) {
            ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
        }
        # after..
        unset($intProfileID, $intElementID, $strSiteURL, $arXmlTags, $arXml);
        return $arResult;
    }

    /**
     *	Get XML tag: ProductInformation
     */
    protected function getXmlTag_ProductInformation($arProfile,$arFields){
        #$arProfiles = Profile::getProfiles($arProfile);
        $arProfiles = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$arProfile]);
        $arLocals = parent::getEbayLocal();
        $arResult = array();
        if(!Helper::isEmpty($arFields['TITLE']))
            $arResult['title'] = Xml::addTag($arFields['TITLE']);
        if(!Helper::isEmpty($arFields['DESCRIPTION']))
            $arResult['description'] = $this->getXmlTag_Description($intProfileID, $arFields['DESCRIPTION']);
        if(!Helper::isEmpty($arFields['BRAND']))
            $arResult['Brand'] = Xml::addTag($arFields['BRAND']);
        $this->getXmlTag_PictureURL($intProfileID, $arResult, $arFields);
        $arResult['shippingDetails'] = $this->getXmlTag_ShippingDetails($intProfileID, $arFields);
        if(!Helper::isEmpty($arFields['CONDITION']))
            $arResult['conditionInfo'] = $this->getXmlTag_Condition($intProfileID, $arFields['CONDITION']);

        return array(
            array(
                '@' => array("localizedFor" => $arLocals[$arProfiles['PARAMS']['LOCALIZED']]),
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: description
     */
    protected function getXmlTag_Description($arProfile, $mValue){
        $arResult = array();
        if(!Helper::isEmpty($mValue)) {
            $arResult['productDescription'] = array(
                array(
                    '#' => $mValue,
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
     *	Get XML tag: shippingDetails
     */
    protected function getXmlTag_ShippingDetails($arProfile, $arFields){
        $arResult = array();
        $arResult['weightMajor'] = array(
            array(
                '#' => $arFields['WEIGHT'],
            ),
        );
        $arResult['length'] = array(
            array(
                '#' => $arFields['LENGTH'],
            ),
        );
        $arResult['width'] = array(
            array(
                '#' => $arFields['WIDTH'],
            ),
        );
        $arResult['height'] = array(
            array(
                '#' => $arFields['HEIGHT'],
            ),
        );
        $arResult['packageType'] = array(
            array(
                '#' => $arFields['PACKAGETYPE'],
            ),
        );

        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: PictureURL
     */
    protected function getXmlTag_PictureURL($intProfileID,&$arResult,$arFields) {
        if(!Helper::isEmpty($arFields['IMAGE'])) {
            $arResult['pictureURL'][0] =  Xml::addTag(trim($arFields['IMAGE']));
        }
        if($arFields['IMAGE'] && is_array($arFields['IMAGE'])) {
            foreach($arFields['IMAGE'] as $iPictures) {
                if(is_array($arrPicture = explode(',',$iPictures))) {
                    foreach($arrPicture as $picture) {
                        $arResult['pictureURL'][] =  Xml::addTag(trim($picture));
                    }
                } else {
                    $arResult['pictureURL'][] =  Xml::addTag(trim($iPictures));
                }
            }
        }

        /*
        return array(
            array(
                '#' => $arResult,
            ),
        ); */
    }

    /**
     *	Get XML tag: conditionInfo
     */
    protected function getXmlTag_Condition($arProfile, $mValue){
        $arResult = array();
        if(!Helper::isEmpty($mValue)) {
            $arResult['condition'] = array(
                array(
                    '#' => $mValue,
                ),
            );
        }
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

}
?>