<?
/**
 * Acrit Core: Retailcrm.ru base plugin
 * @documentation http://help.retailcrm.ru/Developers/ICML
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
	\Acrit\Core\Xml,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class RetailCrm extends Plugin {
	
	CONST DATE_UPDATED = '2018-12-20';

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
		return 'RETAILCRM';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	
	public function getDefaultExportFilename(){
			return 'retailcrm.xml';
	}
	/**
	 *	Set available extension
	 */
	protected function setAvailableExtension($strExtension){
		$this->strFileExt = $strExtension;
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
	
	/**
	 *	Show plugin default settings
	 */
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
     *	Get adailable fields for current plugin
     */
    public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
        return array();
    }

    /**
     *	Process single element
     *	@return array
     */
    public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
     //   return parent::processElement($arProfile, $intIBlockID, $arElement, $arFields);
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
        <?=$this->showFileOpenLink($arSession['EXPORT']['XML_FILE_URL_ZIP'], static::getMessage('RESULT_FILE_ZIP'));?>
        <?
        return Helper::showSuccess(ob_get_clean());
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
            'FUNC' => array($this, 'stepCheck'),
        );
        $arResult['EXPORT'] = array(
            'NAME' => static::getMessage('STEP_EXPORT'),
            'SORT' => 100,
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
            if($arData['PROFILE']['PARAMS']['COMPRESS_TO_ZIP'] == 'Y'){
                $arSession['XML_FILE_ZIP'] = Helper::changeFileExt($_SERVER['DOCUMENT_ROOT'].$strExportFilename, 'zip');
                $arSession['XML_FILE_URL_ZIP'] = Helper::changeFileExt($strExportFilename, 'zip');
            }
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

        # SubStep2 [<categories>]
        if(!isset($arSession['XML_CATEGORIES_WROTE'])){
            $this->stepExport_writeXmlCategories($intProfileID, $arData);
            $arSession['XML_CATEGORIES_WROTE'] = true;
        }

        # SubStep6 [each <offer>]
        if(!isset($arSession['XML_OFFERS_WROTE'])){
            $this->stepExport_writeXmlOffers($intProfileID, $arData);
            $arSession['XML_OFFERS_WROTE'] = true;
        }

        # SubStep7 [footer]
        if(!isset($arSession['XML_FOOTER_WROTE'])){
            $this->stepExport_writeXmlFooter($intProfileID, $arData);
            $arSession['XML_FOOTER_WROTE'] = true;
        }

        # SubStep8 [tmp => real]
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
        $strXml .= '<yml_catalog date="'.$strDate.'">'."\n";
        $strXml .= "\t".'<shop>'."\n";
        $strXml .= "\t".'<name>'.$arData['PROFILE']['PARAMS']['SHOP_NAME'].'</name>'."\n";
        $strXml .= "\t".'<company>'.$arData['PROFILE']['PARAMS']['SHOP_COMPANY'].'</company>'."\n";
        #
        file_put_contents($strFile, $strXml, FILE_APPEND);
    }

    /**
     *	Step: Export, write categories
     */
    protected function stepExport_writeXmlCategories($intProfileID, $arData){
        $strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];

        # All categories for XML
        $arCategoriesForXml = array();

        # Get category redefinitions all
        #$arCategoryRedefinitionsAll = CategoryRedefinition::getForProfile($intProfileID);
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
            #$resItems = ExportData::getList($arQuery);
            $resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
            while($arItem = $resItems->fetch()){
                $arItemSectionsID = array();
                if(is_numeric($arItem['SECTION_ID']) && $arItem['SECTION_ID']>0) {
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
						$arSelectedSectionsID = Exporter::getInstance($this->strModuleId)->getInvolvedSectionsID($intSectionsIBlockID, $strSectionsID, $strSectionsMode);
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
                    $arCategory = array(
                        '@' => array('id' => $intCategoryID),
                        '#' => htmlspecialcharsbx($strCategoryName),
                    );
                    if($intParentID){
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
                foreach($arSectionsForExportAll as $intCategoryID => $arCategory){
                    if($arData['PROFILE']['PARAMS']['CATEGORIES_EXPORT_PARENTS']=='Y') {
                        $resSectionsChain = \CIBlockSection::getNavChain(false, $intCategoryID, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
                        while($arSectionsChain = $resSectionsChain->getNext()){
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
                        $arCategory = array(
                            '@' => array('id' => $intCategoryID),
                            '#' => htmlspecialcharsbx($strCategoryName),
                        );
                        if($intParentID){
                            $arCategory['@']['parentId'] = $intParentID;
                        }
                        $arCategoriesXml[] = $arCategory;
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
        $strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
        file_put_contents($strFile, $strXml, FILE_APPEND);
    }


    /**
     *	Step: Export, write offers
     *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
     */
    protected function stepExport_writeXmlOffers($intProfileID, $arData){
        $strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
        #
        $strXml .= "\t\t".'<offers>'."\n";
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
				$strXml = '';
        $strXml .= "\t\t".'</offers>'."\n";
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
        $strXml .= "\t".'</shop>'."\n";
        $strXml .= '</yml_catalog>'."\n";
        $strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
        file_put_contents($strFile, $strXml, FILE_APPEND);
    }

    /**
     *	Add additional params
     */
    protected function getXmlTag_Param($arProfile, $intIBlockID, $arFields){
        $intProfileID = $arProfile['ID'];
        $arIBlockFields = &$arProfile['IBLOCKS'][$intIBlockID]['FIELDS'];
        $mResult = NULL;
        #$arAdditionalFields = AdditionalField::getListForProfileIBlock($intProfileID, $intIBlockID);
        $arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock', [$intProfileID, $intIBlockID]);
        if(!empty($arAdditionalFields)) {
            $mResult = array();
            foreach($arAdditionalFields as $arAdditionalField){
                $strFieldCode = $arAdditionalField['FIELD'];
                if(!Helper::isEmpty($arFields[$strFieldCode])) {
                    $arAttributes = array(
                        'name' => $arAdditionalField['NAME'],
                    );
                    $arAdditionalAttributes = $arIBlockFields[$strFieldCode]['PARAMS']['ADDITIONAL_ATTRIBUTES'];
                    if(is_array($arAdditionalAttributes) && is_array($arAdditionalAttributes['NAME']) && $arAdditionalAttributes['VALUE']){
                        foreach($arAdditionalAttributes['NAME'] as $key => $strAttrName){
                            $strAttrValue = $arAdditionalAttributes['VALUE'][$key];
                            $arAttributes[$strAttrName] = $strAttrValue;
                        }
                    }
                    if(is_array($arFields[$strFieldCode])){
                        foreach($arFields[$strFieldCode] as $strValue){
                            $mResult[] = array(
                                '@' => $arAttributes,
                                '#' => $strValue,
                            );
                        }
                    }
                    else{
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

	
	/**
	 *	Callback to usort for categories
	 */
	public static function usortCategoriesCallback($a, $b){
		$a = $a['@'];
		$b = $b['@'];
		#
		if(isset($a['parentId']) && !isset($b['parentId'])){
			return true;
		}
		elseif(!isset($a['parentId']) && isset($b['parentId'])){
			return false;
		}
		else{
			if($a['id'] == $b['id']) {
				return 0;
			}
			return ($a['id'] < $b['id']) ? -1 : 1;
		}
	}

}

?>