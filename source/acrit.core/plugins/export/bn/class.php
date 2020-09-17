<?
/**
 * Acrit Core: Plugin for bn.ru
 * @documentation https://yandex.ru/support/partnermarket/export/yml.html
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Xml,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class BullNed extends Plugin {
	
	CONST DATE_UPDATED = '2018-12-10';

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
		return 'BULL_NED';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	
    public function getDefaultExportFilename(){
        return 'bullned.xml';
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
            #'FUNC' => __CLASS__.'::stepCheck',
            'FUNC' => array($this, 'stepCheck'),
        );
        $arResult['EXPORT'] = array(
            'NAME' => static::getMessage('STEP_EXPORT'),
            'SORT' => 100,
           # 'FUNC' => __CLASS__.'::stepExport',
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
        $strDate = date('Y-m-d')."T".date('H:i:s')."+04:00";
        $strXml = '';
        $strXml .= '<?xml version="1.0" encoding="'.$strEncoding.'"?>'."\n";
        $strXml .= '<bn-feed generation-date="'.$strDate.'">'."\n";
        #
        file_put_contents($strFile, $strXml, FILE_APPEND);
    }


    /**
     *	Step: Export, write offers
     *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
     */
    protected function stepExport_writeXmlOffers($intProfileID, $arData){
        $strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
        #
        //$strXml .= "\t\t".'<offers>'."\n";
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
                $arItem['DATA'] = rtrim(Xml::addOffset($arItem['DATA'], 1))."\n";
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
    }

    /**
     *	Step: Export, write footer
     */
    protected function stepExport_writeXmlFooter($intProfileID, $arData){
        $strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
        #
        $strXml = '';
        $strXml .= "\t".'</bn-feed>'."\n";
        $strXml = Helper::convertEncodingTo($strXml, $arData['PROFILE']['PARAMS']['ENCODING']);
        file_put_contents($strFile, $strXml, FILE_APPEND);
    }

}

?>