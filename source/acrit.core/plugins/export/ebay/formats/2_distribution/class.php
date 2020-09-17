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

class EbayDistribution extends Ebay {

    CONST DATE_UPDATED = '2019-08-08';
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
        return parent::getCode().'_DISTRIBUTION';
    }

    /**
     * Get plugin short name
     */
    public static function getName() {
        return static::getMessage('NAME');
    }

    /**
     *	Are categories export?
     */
    public function areCategoriesExport(){
        return true;
    }

    /**
     *	Is plugin works just with own categories of products
     */
    public function isCategoryStrict(){
        return true;
    }

    /**
     *	Is plugin has own categories (it is optional)
     */
    public function hasCategoryList(){
        return true;
    }

    /**
     *	Is it subclass?
     */
    public static function isSubclass(){
        return true;
    }

    public function getDefaultExportFilename(){
        return 'ebay_distribution.xml';
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
                    <?=Helper::showHint(static::getMessage('EBAY_CHANNEL_HINT'));?>
                    <b><?=static::getMessage('EBAY_CHANNEL');?>:</b>
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <?
                    $arChannelId = parent::getEbayChannels();
                    $arChannelId = array(
                        'REFERENCE' => array_values($arChannelId),
                        'REFERENCE_ID' => array_keys($arChannelId),
                    );
                    print SelectBoxFromArray('PROFILE[PARAMS][CHANNELID]', $arChannelId,
                        $this->arProfile['PARAMS']['CHANNELID'], '', 'id="acrit_exp_plugin_channelid"');
                    ?>
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
                    <?=Helper::ShowHint(static::getMessage('SETTINGS_POLICY_HINT'));?>
                    <?=static::getMessage('SETTINGS_POLICY');?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[PARAMS][SHIPPINGPOLICY]"
                           value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['SHIPPINGPOLICY']);?>" size="14"
                           placeholder="<?=static::getMessage('SHIPPINGPOLICY')?>" />
                    <input type="text" name="PROFILE[PARAMS][PAYMENTPOLICY]"
                           value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['PAYMENTPOLICY']);?>" size="14"
                           placeholder="<?=static::getMessage('PAYMENTPOLICY')?>" />
                    <input type="text" name="PROFILE[PARAMS][RETURNPOLICY]"
                           value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['RETURNPOLICY']);?>" size="12"
                           placeholder="<?=static::getMessage('RETURNPOLICY')?>" />
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
     *	Update categories from server
     */
    public function updateCategories($intProfileID){
        $strFileName = $this->getCategoriesCacheFile();
        return (is_file($strFileName) && filesize($strFileName));
    }

    /**
     *	Get categories date update
     */
    public function getCategoriesDate(){
        $strFileName = $this->getCategoriesCacheFile();
        return is_file($strFileName) ? filemtime($strFileName) : false;
    }

    /**
     *	Get categories list
     */
    public function getCategoriesList($intProfileID){
        $strFileName = $this->getCategoriesCacheFile();
        if(!is_file($strFileName) || !filesize($strFileName)) {
            $this->updateCategories($intProfileID);
        }
        if(is_file($strFileName) && filesize($strFileName)) {
						$strContents = file_get_contents($strFileName);
						if(!Helper::isUtf()){
							$strContents = Helper::convertEncoding($strContents, 'UTF-8', 'CP1251');
						}
            return explode("\n", $strContents);
        }
        return false;
    }

    /**
     *	Get filename for categories cache
     */
    protected function getCategoriesCacheFile(){
        $strCacheDir = __DIR__.'/cache';
        if(!is_dir($strCacheDir)){
            mkdir($strCacheDir, BX_DIR_PERMISSIONS, true);
        }
        return $strCacheDir.'/'.static::CATEGORIES_FILENAME;
    }

    /* END OF BASE STATIC METHODS */

    /**
     *	Get adailable fields for current plugin
     */
    public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
        $arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
        #
        $arResult[] = new Field(array(
            'CODE' => 'SHIPPINGCOST',
            'DISPLAY_CODE' => 'shippingCost',
            'NAME' => static::getMessage('FIELD_SHIPPINGCOST_NAME'),
            'SORT' => 110,
            'DESCRIPTION' => static::getMessage('FIELD_SHIPPINGCOST_DESC'),
            'MULTIPLE' => false,
            'CDATA' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => '',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'ADDITIONALCOST',
            'DISPLAY_CODE' => 'additionalCost',
            'NAME' => static::getMessage('FIELD_ADDITIONALCOST_NAME'),
            'SORT' => 120,
            'DESCRIPTION' => static::getMessage('FIELD_ADDITIONALCOST_DESC'),
            'MULTIPLE' => false,
            'CDATA' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => '',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'CATEGORY',
            'DISPLAY_CODE' => 'category',
            'NAME' => static::getMessage('FIELD_CATEGORY_NAME'),
            'SORT' => 130,
            'DESCRIPTION' => static::getMessage('FIELD_CATEGORY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'CDATA' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'PRICE',
            'DISPLAY_CODE' => 'price',
            'NAME' => static::getMessage('FIELD_PRICE_NAME'),
            'SORT' => 140,
            'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_PRICE_1__WITH_DISCOUNT',
                ),
            ),
            'IS_PRICE' => true,
        ));
        $arResult[] = new Field(array(
            'CODE' => 'VATPERCENT',
            'DISPLAY_CODE' => 'vatPercent',
            'NAME' => static::getMessage('FIELD_VAT_NAME'),
            'SORT' => 150,
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
            'CODE' => 'LOTSIZE',
            'DISPLAY_CODE' => 'lotSize',
            'NAME' => static::getMessage('FIELD_LOTSIZE_NAME'),
            'SORT' => 160,
            'DESCRIPTION' => static::getMessage('FIELD_LOTSIZE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'CATALOG_DIMENSIONS', # ToDo: add this to available fields! + settings support (1x2x3, or 1cm x 2cm x 3cm, or ..)
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

        if(!Helper::isEmpty($arFields['ID']))
            $arXmlTags['SKU'] = Xml::addTag($arFields['ID']);
        $arXmlTags['channelDetails'] = $this->getXmlTag_channelDetails($intProfileID,$arFields,$arElement);

        # Build XML
        $arXml = array(
            'distribution' => array(
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
     *	Get XML tag: channelDetails
     */
    protected function getXmlTag_channelDetails($arProfile,$arFields,$arElement){
        $arResult = array();
        #$arProfiles = Profile::getProfiles($arProfile);
        $arProfiles = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$arProfile]);
        $arChannels = parent::getEbayChannels();
        #
        $arResult['channelID'] = Xml::addTag($arChannels[$arProfiles['PARAMS']['CHANNELID']]);
        $arResult['category'] = $this->getXmlTag_Category($arProfile, $arElement,$arFields["CATEGORY"]);
        $arResult['shippingPolicyName'] = Xml::addTag($arProfiles['PARAMS']['SHIPPINGPOLICY']);
        #
        $arResult['shippingCostOverrides'] = $this->getXmlTag_ShippingCostOverrides($arFields);
        $arResult['paymentPolicyName'] = Xml::addTag($arProfiles['PARAMS']['PAYMENTPOLICY']);
        $arResult['returnPolicyName'] = Xml::addTag($arProfiles['PARAMS']['RETURNPOLICY']);
        if(!Helper::isEmpty($arFields['RETURNPOLICY']))
            $arResult['returnPolicyName'] = Xml::addTag($arFields['RETURNPOLICY']);
        $arResult['pricingDetails'] = $this->getXmlTag_PricingDetails($arFields);
        $arResult['VATPercent'] = Xml::addTag($arFields['VATPERCENT']);
        $arResult['lotSize'] = Xml::addTag($arFields['LOTSIZE']);


        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: shippingDetails
     */
    protected function getXmlTag_ShippingCostOverrides($arFields){
        $arResult = array();
        $arResult['shippingCost'] = array(
            array(
                '#' => $arFields['SHIPPINGCOST'],
            ),
        );
        $arResult['additionalCost'] = array(
            array(
                '#' => $arFields['ADDITIONALCOST'],
            ),
        );

        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: PricingDetails
     */
    protected function getXmlTag_PricingDetails($arFields){
        $arResult = array();
        $arResult['listPrice'] = array(
            array(
                '#' => $arFields['PRICE'],
            ),
        );

        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: <category>
     */
    protected function getXmlTag_Category($arProfile, $arElement, $mValue){
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
            }
            if(is_array($arElement['ADDITIONAL_SECTIONS'])){
                foreach($arElement['ADDITIONAL_SECTIONS'] as $intAdditionalSectionID) {
                    $arSectionsID[] = $intAdditionalSectionID;
                }
            }
            #$arCategoryRedefinitions = CategoryRedefinition::getForProfile($intProfileID);
            $arCategoryRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$intProfileID]);
            foreach($arSectionsID as $intSectionID){
                if(array_key_exists($intSectionID, $arCategoryRedefinitions)){
                    $mValue = substr($arCategoryRedefinitions[$intSectionID],0,strpos($arCategoryRedefinitions[$intSectionID],":"));
                    break;
                }
            }
            unset($arCategoryRedefinitions, $arSectionsID, $intSectionID, $intCategoryID);
        }
        return array('#' => $mValue);
    }
}
?>
