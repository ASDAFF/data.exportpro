<?
/**
 * Acrit Core: Yandex.Turbo plugin
 * @documentation https://yandex.ru/support/webmaster/turbo/feed.html
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\ProfileIBlockTable as ProfileIBlock,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Log,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class YandexTurboGeneral extends YandexTurbo {
	
	CONST DATE_UPDATED = '2019-09-04';
	
	CONST COUNT_PER_FILE_MAX = 1000;
	CONST COUNT_PER_FILE_DEFAULT = 500;
	
	CONST MAX_SHOW_FILES = 10;
	
	CONST YANDEX_PLUGIN_ID = '6B32B333E0E9E2D88FECC4EC73723DBD';

	protected static $bSubclass = true;
	
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
		return parent::getCode().'_GENERAL';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = array();
		$arResult[] = array(
			'DIV' => 'settings',
			'TAB' => static::getMessage('TAB_SETTINGS_NAME'),
			'TITLE' => static::getMessage('TAB_SETTINGS_DESC'),
			'SORT' => 15,
			'FILE' => __DIR__.'/tabs/tab_settings.php',
		);
		$arResult[] = array(
			'DIV' => 'files',
			'TAB' => static::getMessage('TAB_FILES_NAME'),
			'TITLE' => static::getMessage('TAB_FILES_DESC'),
			'SORT' => 20,
			'FILE' => __DIR__.'/tabs/tab_files.php',
		);
		return $arResult;
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'yandex_turbo_rss.xml';
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
		return $this->showDefaultSettings();
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
										id="acrit_exp_plugin_xml_filename" data-role="export-file-name"
										value="<?=htmlspecialcharsbx($this->arProfile['PARAMS']['EXPORT_FILE_NAME']);?>" size="40"
										placeholder="<?=static::getMessage('SETTINGS_FILE_PLACEHOLDER');?>" /></td>
									<td><input type="button" value="..." onclick="AcritExpPluginXmlFilenameSelect()" /></td>
									<td style="padding-left:10px">
										<?
										$arFiles = array();
										$strFile = $this->arProfile['PARAMS']['EXPORT_FILE_NAME'];
										$intFileIndex = 0;
										while(true){
											$intFileIndex++;
											$strFilename = Helper::getFileNameWithIndex($strFile, $intFileIndex);
											if(is_file($_SERVER['DOCUMENT_ROOT'].$strFilename)){
												$arFiles[$intFileIndex] = $strFilename;
												continue;
											}
											break;
										}
										$intFilesCount = count($arFiles);
										$arFiles = array_slice($arFiles, 0, static::MAX_SHOW_FILES, true);
										?>
										<?foreach($arFiles as $intFileIndex => $strFile):?>
											<?=$this->showFileOpenLink($strFile, '#'.$intFileIndex);?>
										<?endforeach?>
										<?if($intFilesCount>1):?>
											<div><?=static::getMessage('SETTINGS_FILE_COUNT', array('#COUNT#' => $intFilesCount));?>
										<?endif?>
									</td>
								</tr>
							</tbody>
						</table>
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
		$arResult = array();
		$arResult[] = new Field(array(
			'CODE' => 'LINK',
			'DISPLAY_CODE' => 'link',
			'NAME' => static::getMessage('FIELD_LINK_NAME'),
			'SORT' => 510,
			'DESCRIPTION' => static::getMessage('FIELD_LINK_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PAGE_URL',
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'escape',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TITLE',
			'DISPLAY_CODE' => 'title',
			'NAME' => static::getMessage('FIELD_TITLE_NAME'),
			'SORT' => 520,
			'DESCRIPTION' => static::getMessage('FIELD_TITLE_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'NAME',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 240,
				'CASE' => 'skip',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SUBTITLE',
			'DISPLAY_CODE' => 'subtitle',
			'NAME' => static::getMessage('FIELD_SUBTITLE_NAME'),
			'SORT' => 520,
			'DESCRIPTION' => static::getMessage('FIELD_SUBTITLE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => 240,
				'CASE' => 'skip',
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
			'CODE' => 'CONTENT',
			'DISPLAY_CODE' => 'content',
			'NAME' => static::getMessage('FIELD_CONTENT_NAME'),
			'SORT' => 530,
			'DESCRIPTION' => static::getMessage('FIELD_CONTENT_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_TEXT',
					'PARAMS' => array(
						'HTMLSPECIALCHARS' => 'skip',
					),
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'skip',
				'REPLACE' => array(
					'from' => array('&#60;', '&#62;', '&amp;', '&lt;', '&gt;', '&quot;', '&pos;'),
					'to' => array('<', '>', '&', '<', '>', '"', '\''),
					'use_regexp' => array('', '', '', '', '', '', ''),
					'case_sensitive' => array('', '', '', '', '', '', ''),
				),
			),
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'TURBO_SOURCE',
			'DISPLAY_CODE' => 'turbo:source',
			'NAME' => static::getMessage('FIELD_TURBO_SOURCE_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_TURBO_SOURCE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PAGE_URL',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TURBO_TOPIC',
			'DISPLAY_CODE' => 'turbo:topic',
			'NAME' => static::getMessage('FIELD_TURBO_TOPIC_NAME'),
			'SORT' => 550,
			'DESCRIPTION' => static::getMessage('FIELD_TURBO_TOPIC_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'NAME',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PUB_DATE',
			'DISPLAY_CODE' => 'pubDate',
			'NAME' => static::getMessage('FIELD_PUB_DATE_NAME'),
			'SORT' => 560,
			'DESCRIPTION' => static::getMessage('FIELD_PUB_DATE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DATE_CREATE',
					'PARAMS' => array(
						'DATEFORMAT' => 'Y',
						'DATEFORMAT_from' => \CDatabase::dateFormatToPhp(FORMAT_DATETIME),
						'DATEFORMAT_to' => \DateTime::RFC822,
					),
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AUTHOR',
			'DISPLAY_CODE' => 'author',
			'NAME' => static::getMessage('FIELD_AUTHOR_NAME'),
			'SORT' => 570,
			'DESCRIPTION' => static::getMessage('FIELD_AUTHOR_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => '',
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'cut',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'IMAGES',
			'DISPLAY_CODE' => ' ',
			'NAME' => static::getMessage('FIELD_IMAGES_NAME'),
			'SORT' => 580,
			'DESCRIPTION' => static::getMessage('FIELD_IMAGES_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
					'PARAMS' => array(
						'RAW' => 'Y',
					),
				),
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_MORE_PHOTO',
					'PARAMS' => array(
						'MULTIPLE' => 'multiple',
						'RAW' => 'Y',
					),
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'RELATED',
			'DISPLAY_CODE' => 'yandex:related',
			'NAME' => static::getMessage('FIELD_RELATED_NAME'),
			'SORT' => 580,
			'DESCRIPTION' => static::getMessage('FIELD_RELATED_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_RELATED',
					'PARAMS' => array(
						'RAW' => 'Y',
						'MULTIPLE' => 'multiple',
					),
				),
			),
			'DEFAULT_VALUE_OFFERS' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.PROPERTY_RELATED',
					'PARAMS' => array(
						'RAW' => 'Y',
						'MULTIPLE' => 'multiple',
					),
				),
			),
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		#
		unset($arAvailableFields, $strQuantityField, $strQuantityLogic, $arQuantityField, $arQuantityLogic,
			$arFilterJsonAvailable, $strFilterJsonAvailable, $arAdditionalFields);
		return $arResult;
	}
	
	/**
	 *	Process single element
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		
		# Build XML
		$arXmlTags = array();
		if(!Helper::isEmpty($arFields['TITLE']))
			$arXmlTags['title'] = Xml::addTag($arFields['TITLE']);
		if(!Helper::isEmpty($arFields['LINK']))
			$arXmlTags['link'] = Xml::addTag($arFields['LINK']);
		$arXmlTags['turbo:content'] = $this->getXmlTag_TurboContent($arProfile, $intIBlockID, $arElement, $arFields);
		if(!Helper::isEmpty($arFields['TURBO_SOURCE']))
			$arXmlTags['turbo:source'] = Xml::addTag($arFields['TURBO_SOURCE']);
		if(!Helper::isEmpty($arFields['TURBO_TOPIC']))
			$arXmlTags['turbo:topic'] = Xml::addTag($arFields['TURBO_TOPIC']);
		if(!Helper::isEmpty($arFields['PUB_DATE']))
			$arXmlTags['pubDate'] = Xml::addTag($arFields['PUB_DATE']);
		if(!Helper::isEmpty($arFields['AUTHOR']))
			$arXmlTags['author'] = Xml::addTag($arFields['AUTHOR']);
		if(!Helper::isEmpty($arFields['RELATED']))
			$arXmlTags['yandex:related'] =  $this->getXmlTag_Related($intProfileID, $arFields['RELATED']);
		
		# Build XML
		$arXml = array(
			'item' => array(
				'@' => array('turbo' => 'true'),
				'#' => $arXmlTags,
			),
		);
		
		# Event handler OnYandexTurboXml
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexTurboXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# Build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => Xml::arrayToXml($arXml),
			'CURRENCY' => '',
			'SECTION_ID' => '',
			'ADDITIONAL_SECTIONS_ID' => '',
			'DATA_MORE' => array(),
		);
		
		# Event handlers OnYandexTurboResult
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnYandexTurboResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		
		# After..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
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
		<div>
			<?
			if(!empty($arSession['EXPORT']['EXPORT_FILES'])){
				print static::getMessage('RESULT_OPEN_FILE').':';
				foreach($arSession['EXPORT']['EXPORT_FILES'] as $intFileIndex => $arFile){
					print $this->showFileOpenLink(substr($arFile[1], strlen($_SERVER['DOCUMENT_ROOT'])), '#'.$intFileIndex);
				}
			}
			else {
				print '<div style="color:red">'.static::getMessage('RESULT_NO_FILES').'</div>';
			}
			?>
		</div>
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
			#'FUNC' => __CLASS__.'::stepExport',
			'FUNC' => array($this, 'stepExport'),
		);
		return $arResult;
	}
	
	/**
	 *	Get actual and correct value for COUNT_PER_FILE
	 */
	public function getCountPerPage($intSavedValue){
		$intSavedValue = IntVal($intSavedValue);
		if($intSavedValue <= 0){
			$intSavedValue = static::COUNT_PER_FILE_DEFAULT;
		}
		return min($intSavedValue, static::COUNT_PER_FILE_MAX);
	}
	
	/**
	 *	Get temp filename for selected filename
	 */
	public function getFileNameTmp($strFileName){
		return $strFileName.'.tmp';
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
		if(!isset($arSession['XML_ITEMS_WROTE'])){
			if(!$this->stepExport_writeXmlItems($intProfileID, $arData)){
				return Exporter::RESULT_ERROR;
			}
			$arSession['XML_ITEMS_WROTE'] = true;
		}
		#
		if(!isset($arSession['XML_STATIC_WROTE'])){
			$mWriteStaticFiles = $this->stepExport_writeStaticFiles($intProfileID, $arData);
			if($mWriteStaticFiles === Exporter::RESULT_CONTINUE){
				return Exporter::RESULT_CONTINUE;
			}
			elseif($mWriteStaticFiles === Exporter::RESULT_ERROR){
				return Exporter::RESULT_ERROR;
			}
			elseif($mWriteStaticFiles === Exporter::RESULT_SUCCESS){
				$arSession['XML_STATIC_WROTE'] = true;
			}
		}
		#
		if(!isset($arSession['XML_FILES_RENAMED'])){
			if(!$this->stepExport_renameFiles($intProfileID, $arData)){
				return Exporter::RESULT_ERROR;
			}
			else {
				$arSession['XML_FILES_RENAMED'] = true;
			}
		}
		#
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Step: Export, write items
	 */
	protected function stepExport_writeXmlItems($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		$strEncoding = $arData['PROFILE']['PARAMS']['ENCODING'];
		$strFile = $_SERVER['DOCUMENT_ROOT'].$arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		#
		if(!Helper::createDirectoriesForFile($strFile)){
			$strMessage = Loc::getMessage('ACRIT_EXP_ERROR_CREATE_DIRECORY', array(
				'#DIR#' => Helper::getDirectoryForFile($strFile),
			));
			Log::getInstance($this->strModuleId)->add($strMessage);
			print Helper::showError($strMessage);
			return Exporter::RESULT_ERROR;
		}
		#
		$arSession['EXPORT_FILES'] = array();
		# Export IBlock elements
		$intOffset = 0;
		$intFileIndex = 0;
		while(true){
			$intFileIndex++;
			#
			$strXml = $this->getXmlHeader($strEncoding);
			#
			$intLimit = $this->getCountPerPage($arData['PROFILE']['PARAMS']['_PLUGINS'][static::getCode()]['COUNT_PER_FILE']);
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
			$intCount = 0;
			while($arItem = $resItems->fetch()){
				$intCount++;
				$arItem['DATA'] = rtrim(Xml::addOffset($arItem['DATA'], 2))."\n";
				$strXml .= $arItem['DATA'];
			}
			$arData['SESSION']['EXPORT']['INDEX'] += $intCount;
			if($intCount == 0){
				break;
			}
			#
			list($strFilenameTmp, $strFilename) = $this->getFilename($intProfileID, $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'], $intFileIndex);
			#
			if(is_file($strFilenameTmp)){
				@unlink($strFilenameTmp);
			}
			#
			$strXml .= $this->getXmlFooter();
			#
			$strXml = Helper::convertEncodingTo($strXml, $strEncoding);
			file_put_contents($strFilenameTmp, $strXml, FILE_APPEND);
			$arSession['EXPORT_FILES'][$intFileIndex] = array(
				$strFilenameTmp,
				$strFilename,
			);
			#
			if($intCount<$intLimit){
				break;
			}
			$intOffset++;
		}
		$arSession['LAST_FILE_INDEX'] = $intFileIndex;
		#
		return Exporter::RESULT_SUCCESS;
	}
	
	protected function getXmlHeader($strEncoding){
		$strXml = '';
		$strXml .= '<?xml version="1.0" encoding="'.$strEncoding.'"?>'."\n";
		$strXml .= '<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">'."\n";
		$strXml .= "\t".'<channel>'."\n";
		$strXml .= "\t\t".'<turbo:cms_plugin>'.static::YANDEX_PLUGIN_ID.'</turbo:cms_plugin>'."\n";
		return $strXml;
	}
	
	protected function getXmlFooter(){
		$strXml = '';
		$strXml .= "\n\t".'</channel>'."\n";
		$strXml .= '</rss>'."\n";
		return $strXml;
	}
	
	/**
	 *	Step: Export, write static files
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_writeStaticFiles($intProfileID, &$arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		$strEncoding = $arData['PROFILE']['PARAMS']['ENCODING'];
		$intCountPerFile = $this->getCountPerPage($arData['PROFILE']['PARAMS']['_PLUGINS'][static::getCode()]['COUNT_PER_FILE']);
		#
		$arPluginParams = $arData['PROFILE']['PARAMS']['_PLUGINS'][static::getCode()];
		if(is_array($arPluginParams['DIRS']) && !empty($arPluginParams['DIRS'])){
			$intFileIndex = &$arSession['LAST_FILE_INDEX'];
			$strFilenameOpen = &$arSession['FILENAME_OPEN'];
			$strFilenameCount = &$arSession['FILENAME_COUNT'];
			foreach($arPluginParams['DIRS'] as $intDirIndex => $strDir){
				if(is_numeric($arSession['STATIC_FILES_LAST_INDEX']) && $intDirIndex <= $arSession['STATIC_FILES_LAST_INDEX']){
					continue;
				}
				###
				# Get turbo content
				$strContent = $this->getTurboItem($strDir, $intProfileID, $arData);
				# Start write to file
				if(strlen($strContent)){
					# Write header
					if(!strlen($strFilenameOpen)){
						$intFileIndex++;
						list($strFilenameOpen, $strFilename) = $this->getFilename($intProfileID,
							$arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'], $intFileIndex);
						$arSession['EXPORT_FILES'][$intFileIndex] = array(
							$strFilenameOpen,
							$strFilename,
						);
						file_put_contents($strFilenameOpen, $this->getXmlHeader($strEncoding), FILE_APPEND);
					}
					# Write content
					$strContent = Helper::convertEncodingTo($strContent, $strEncoding);
					file_put_contents($strFilenameOpen, $strContent, FILE_APPEND);
					$strFilenameCount++;
					# Write footer
					if($strFilenameCount == $intCountPerFile && strlen($strFilenameOpen)){
						file_put_contents($strFilenameOpen, $this->getXmlFooter(), FILE_APPEND);
						$strFilenameOpen = false;
						$strFilenameCount = 0;
					}
				}
				###
				$arSession['STATIC_FILES_LAST_INDEX'] = $intDirIndex;
				if(!Exporter::getInstance($this->strModuleId)->haveTime()){
					return Exporter::RESULT_CONTINUE;
				}
			}
			if(strlen($strFilenameOpen)){
				file_put_contents($strFilenameOpen, $this->getXmlFooter(), FILE_APPEND);
				$strFilenameOpen = false;
				$strFilenameCount = 0;
			}
		}
		#unset($arSession['STATIC_FILES_LAST_INDEX'], $arSession['STATIC_FILES_IN_ONE_FILE']);
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Get turbo content from URL (without additional turbo data)
	 */
	protected function getTurboItem($strDir, $intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		#
		$arParams = &$arData['PROFILE']['PARAMS']['_PLUGINS'][static::getCode()];
		#
		$strDomain = &$arSession['TURBO_DOMAIN'];
		if(!strlen($strDomain)){
			$strDomain = Helper::siteUrl($arData['PROFILE']['DOMAIN'], $arData['PROFILE']['IS_HTTPS']=='Y');
		}
		$strUrl = $strDomain.$strDir;
		$strUrlCustom = $strUrl;
		if(strlen($arParams['HTTP_CUSTOM_PARAM'])){
			$arParams['HTTP_CUSTOM_PARAM'] = trim($arParams['HTTP_CUSTOM_PARAM'], ' ?&');
			if(strlen($arParams['HTTP_CUSTOM_PARAM'])){
				$strUrlCustom .= strpos($strUrlCustom, '?') === false ? '?' : '&amp;';
				$strUrlCustom .= $arParams['HTTP_CUSTOM_PARAM'];
			}
		}
		$strContents = HttpRequest::get($strUrlCustom);
		if(HttpRequest::getCode()==200){
			if(strlen($strContents)){
				#Parse <title></title>
				$strTitle = '';
				if(preg_match('#<head>.*?<title>(.*?)</title>.*?</head>#is', $strContents, $arMatch)){
					$strTitle = $arMatch[1];
				}
				# Parse date
				$strDateModified = '';
				$arHeaders = HttpRequest::getHeaders();
				foreach($arHeaders as $strHeader){
					if(preg_match('#Date:\s?(.*?)$#', $strHeader, $arMatch)){
						$strDateModified = $arMatch[1];
					}
				}
				if(preg_match_all('#<!--TurboContent-->(.+?)<!--/TurboContent-->#is', $strContents, $arMatches)){
					$strContents = implode("\n", $arMatches[1]);
					return $this->getTurboItemFull($strContents, $strTitle, $strUrl, $strDateModified, $intProfileID, $arData);
				}
			}
		}
		else {
			Log::getInstance($this->strModuleId)->add(static::getMessage('WRONG_HTTP_CODE', array(
				'#DIR#' => $strDir,
				'#CODE#' => HttpRequest::getCode(),
			)), $intProfileID);
		}
		Log::getInstance($this->strModuleId)->add(static::getMessage('NO_CONTENT', array(
			'#DIR#' => $strDir,
		)), $intProfileID);
		return false;
	}
	
	/**
	 *	Get full turbo content (with additional turbo data)
	 */
	protected function getTurboItemFull($strContents, $strTitle, $strUrl, $strDateModified, $intProfileID, $arData){
		$arXmlTags = array();
		#
		#$arProfile = Profile::getProfiles($intProfileID);
		$arProfile = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
		$arFields = array(
			'TITLE' => $strTitle,
			'CONTENT' => $strContents,
		);
		#
		$arXmlTags['title'] = Xml::addTag($strTitle);
		$arXmlTags['link'] = Xml::addTag($strUrl);
		$arXmlTags['turbo:content'] = $this->getXmlTag_TurboContent($arProfile, null, null, $arFields);
		$arXmlTags['turbo:source'] = Xml::addTag($strUrl);
		$arXmlTags['turbo:topic'] = Xml::addTag($strTitle);
		if($strDateModified){
			$arXmlTags['pubDate'] = Xml::addTag($strDateModified);
		}
		$arXml = array(
			'item' => array(
				'@' => array('turbo' => 'true'),
				'#' => $arXmlTags,
			),
		);
		return rtrim(Xml::addOffset(Xml::arrayToXml($arXml), 2));
	}
	
	/**
	 *	Step: Export, rename files
	 */
	protected function stepExport_renameFiles($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		if(!empty($arSession['EXPORT_FILES']) && !$this->renameFiles($intProfileID, $arData)){
			return Exporter::RESULT_ERROR;
		}
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 *	Step: Export, rename all files from temp to real
	 */
	protected function renameFiles($intProfileID, $arData){
		$arSession = &$arData['SESSION']['EXPORT'];
		$strFile = $arData['PROFILE']['PARAMS']['EXPORT_FILE_NAME'];
		# Remove old files
		$intMinCheckRemoveIndex = 1000;
		$intFileIndex = 0;
		while(true){
			$intFileIndex++;
			$strFilename = Helper::getFileNameWithIndex($strFile, $intFileIndex);
			if(is_file($_SERVER['DOCUMENT_ROOT'].$strFilename)){
				@unlink($_SERVER['DOCUMENT_ROOT'].$strFilename);
			}
			if($intFileIndex >= $intMinCheckRemoveIndex){
				break;
			}
		}
		# Rename
		foreach($arSession['EXPORT_FILES'] as $arFile){
			if(!@rename($arFile[0], $arFile[1])){
				@unlink($arFile[0]);
				$strMessage = Loc::getMessage('ACRIT_EXP_FILE_NO_PERMISSIONS', array(
					'#FILE#' => $arSession['XML_FILE'],
				));
				Log::getInstance($this->strModuleId)->add($strMessage);
				print Helper::showError($strMessage);
				return false;
			}
		}
		return true;
	}
	
	/**
	 *	Get filename with index: real + tmp
	 */
	protected function getFilename($intProfileID, $strInputFilename, $intFileIndex){
		#$strTmpDir = Profile::getTmpDir($intProfileID);
		$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
		$strFilename = Helper::getFileNameWithIndex($strInputFilename, $intFileIndex);
		$strFilenameTmp = $strTmpDir.'/'.pathinfo($this->getFileNameTmp($strFilename), PATHINFO_BASENAME);
		return array(
			$strFilenameTmp,
			$_SERVER['DOCUMENT_ROOT'].$strFilename,
		);
	}
	
	/**
	 *	Step: Export, write footer
	 */
	protected function stepExport_writeXmlFooter($intProfileID, $arData){
		$strFile = $arData['SESSION']['EXPORT']['XML_FILE_TMP'];
		#
		$strXml = '';
		$strXml .= "\t".'</channel>'."\n";
		$strXml .= '</rss>'."\n";
		file_put_contents($strFile, $strXml, FILE_APPEND);
	}
	
	/* HELPERS FOR SIMILAR XML-TYPES */
	
	/**
	 *	Get XML tag: <url>
	 */
	
	/**
	 *	Get XML tag: <url>
	 */
	protected function getXmlTag_TurboContent($arProfile, $intIBlockID, $arElement, $arFields){
		$arParams = &$arProfile['PARAMS']['_PLUGINS'][static::getCode()];
		$strContent = '
			<header>
				#TITLE#
				#SUBTITLE#
				#IMAGE#
			</header>
			<p>
				#CONTENT#
			</p>
			#IMAGES#
			#BUTTON#
			#SHARE#
		';
		#
		$strContent = trim($strContent);
		# Event handler
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBeforeYandexTurboContent') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$strContent, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# Title
		$arFields['TITLE'] = '<h1>'.$arFields['TITLE'].'</h1>';
		$strContent = str_replace('#TITLE#', $arFields['TITLE'], $strContent);
		# Subtitle
		$strSubtitle = '';
		if(!Helper::isEmpty($arFields['SUBTITLE'])){
			$strSubtitle = '<h2>'.$arFields['SUBTITLE'].'</h2>';
		}
		$strContent = str_replace('#SUBTITLE#', $strSubtitle, $strContent);
		# Content
		$strContent = str_replace('#CONTENT#', $arFields['CONTENT'], $strContent);
		# Image
		$strImage = '';
		if(!Helper::isEmpty($arFields['IMAGE'])){
			$strImage = '<figure><img src="'.$arFields['IMAGE'].'" /></figure>';
		}
		$strContent = str_replace('#IMAGE#', $strImage, $strContent);
		#
		$strImages = '';
    if(!Helper::isEmpty($arFields['IMAGES'])){
			$arFields['IMAGES'] = is_array($arFields['IMAGES']) ? $arFields['IMAGES'] : array($arFields['IMAGES']);
			foreach($arFields['IMAGES'] as $strImage){
				if(is_numeric($strImage)){
					$arPicture = $arElement['ALL_IMAGES'][$strImage];
					if(!is_array($arPicture)){
						$arPicture = \CFile::getFileArray($strImage);
					}
					if(is_array($arPicture)){
						$strImages .= '<figure>';
						$strImages .= 	'<img src="'.$arPicture['SRC'].'" />';
						if(strlen($arPicture['DESCRIPTION'])){
							$strImages .= '<figcaption>'.htmlspecialcharsbx($arPicture['DESCRIPTION']).'</figcaption>';
						}
						$strImages .= '</figure>';
					}
				}
				elseif(strlen($strImage)) {
					$strImages .= '<figure>';
					$strImages .= 	'<img src="'.$strImage.'" />';
					$strImages .= '</figure>';
				}
			}
		}
		$strContent = str_replace('#IMAGES#', $strImages, $strContent);
		# Button
		$strButton = '';
		if($arParams['SHOW_BUTTON']=='Y' && strlen($arParams['BUTTON_ACTION']) && strlen($arParams['BUTTON_TEXT'])){
			$arAttr = array(
				'formaction' => $arParams['BUTTON_ACTION'],
				'data-primary' => 'true',
			);
			if(strlen($arParams['BUTTON_BACKGROUND_COLOR'])){
				$arAttr['data-background-color'] = $arParams['BUTTON_BACKGROUND_COLOR'];
			}
			if(strlen($arParams['BUTTON_COLOR'])){
				$arAttr['data-color'] = $arParams['BUTTON_COLOR'];
			}
			$arXml = array(
				'@' => &$arAttr,
				'#' => htmlspecialcharsbx($arParams['BUTTON_TEXT']),
			);
			$strButton = Xml::arrayToXml(array('button'=>$arXml));
			unset($arXml);
		}
		$strContent = str_replace('#BUTTON#', $strButton, $strContent);
		# Share
		$strShare = '';
		if(is_array($arParams['SHARE']) && !empty($arParams['SHARE'])){
			$arXml = array(
				'div' => array(
					'@' => array(
						'data-block' => 'widget-feedback',
						'data-stick' => 'false',
					),
					'#' => array(
						'div' => array(),
					),
				),
			);
			foreach($this->getSharesAll() as $key => $arShare){
				if(in_array($key, $arParams['SHARE'])) {
					$arXml['div']['#']['div'][] = array(
						'@' => array(
							'data-block' => 'chat',
							'data-type' => $key,
							'data-url' => trim($arParams['SHARE_URL'][$key]),
						),
						'#' => ' ',
					);
				}
			}
			$strShare = Xml::arrayToXml($arXml);
		}
		$strContent = str_replace('#SHARE#', $strShare, $strContent);
		# Remove empty lines
		$strContent = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $strContent);
		# Event handler
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAfterYandexTurboContent') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$strContent, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# End
		$strContent = '<![CDATA['.$strContent.']]>';
		return Xml::addTag($strContent);
	}
	
	/**
	 *	Get all available shares
	 */
	public function getSharesAll(){
		$arResult = array(
			'whatsapp' => array(
				'NAME' => static::getMessage('SHARE_WHATSAPP'),
			),
			'telegram' => array(
				'NAME' => static::getMessage('SHARE_TELEGRAM'),
			),
			'vkontakte' => array(
				'NAME' => static::getMessage('SHARE_VKONTAKTE'),
			),
			'facebook' => array(
				'NAME' => static::getMessage('SHARE_FACEBOOK'),
			),
			'viber' => array(
				'NAME' => static::getMessage('SHARE_VIBER'),
			),
		);
		return $arResult;
	}
	
	/**
	 *	Get XML tag: <yandex:related>
	 */
	protected function getXmlTag_Related($intProfileID, $arRelatedID){
		$intMaxRelated = 30;
		$arRelatedLinks = array();
		$arSort = array('ID' => 'ASC');
		$arFilter = array('ID' => $arRelatedID);
		$arSelect = array('ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE');
		$arNavParams = array('nTopCount' => $intMaxRelated);
		$resRelated = \CIBlockElement::getList($arSort, $arFilter, false, $arNavParams, $arSelect);
		$strSiteURL = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y');
		while($arRelated = $resRelated->getNext()){
			$intImage = $arRelated['PREVIEW_PICTURE'] ? $arRelated['PREVIEW_PICTURE'] : $arRelated['DETAIL_PICTURE'];
			$arRelatedLink = array(
				'@' => array(
					'url' => $strSiteURL.$arRelated['DETAIL_PAGE_URL'],
				),
				'#' => htmlspecialcharsbx($arRelated['NAME']),
			);
			if($intImage){
				$arRelatedLink['@']['img'] = \CFile::getPath($intImage);
			}
			$arRelatedLinks[] = $arRelatedLink;
		}
		return array(
			array(
				'#' => array(
					'link' => $arRelatedLinks,
				)
			),
		);
	}
	
	/* END OF BASE METHODS FOR XML SUBCLASSES */
	
	/**
	 *	Get logical structure of site (recursively)
	 */
	public function getDirStructure($strSiteID, $strDir=false){
		$bFirstLevel = !$strDir;
		$strDir = strlen($strDir) ? $strDir : '/';
		$arExclude = array(
			'/404.php',
			'/500.html',
		);
		$arStructure = \CSeoUtils::getDirStructure(true, $strSiteID, $strDir);
		foreach($arStructure as $key => $arItem){
			$arItem['DIR'] = $strDir.'/'.$arItem['FILE'];
			if($arItem['TYPE']=='D'){
				$arItem['DIR'] .= '/';
			}
			$arItem['DIR'] = str_replace('//', '/', $arItem['DIR']);
			#
			if($arItem['TYPE']=='D'){
				$arItem['ITEMS'] = $this->getDirStructure($strSiteID, $arItem['DIR']);
			}
			elseif($arItem['TYPE']=='F' && in_array($arItem['DIR'], $arExclude)){
				$arItem = null;
			}
			elseif($arItem['TYPE']=='F' && ToLower($arItem['FILE'])=='index.php' && !$bFirstLevel){
				$arItem = null;
			}
			elseif($arItem['TYPE']=='F' && ToLower($arItem['FILE'])=='index.php' && $bFirstLevel){
				if(defined('BX_DISABLE_INDEX_PAGE') && BX_DISABLE_INDEX_PAGE===true){
					$arItem['DIR'] = '/';
				}
			}
			#
			if(is_null($arItem)){
				unset($arStructure[$key]);
			}
			else{
				$arStructure[$key] = $arItem;
			}
		}
		if($bFirstLevel){
			foreach($arStructure as $key => $arItem){
				if($arItem['TYPE']=='F' && $arItem['FILE']=='index.php'){
					unset($arStructure[$key]);
					$arItem['ITEMS'] = $arStructure;
					$arStructure = array($arItem);
				}
			}
		}
		return $arStructure;
	}

	public function displayDirsStructure($arStructure, $intDepthLevel=false){
		$strResult = '';
		$intDepthLevel = IntVal($intDepthLevel);
		$intMaxDepth = 5;
		if($intDepthLevel <= $intMaxDepth){
			$strPluginParamsInputname = $this->getPluginParamsInputName();
			$arPluginParams = $this->getPluginParams();
			if(is_array($arStructure) && !empty($arStructure)) {
				$strResult .= '<ul>';
				foreach($arStructure as $arItem){
					$strResult .= '<li>';
					$strResult .= '<label title="'.htmlspecialcharsbx($arItem['DIR']).'">';
					$strResult .= '<input type="checkbox" name="'.$strPluginParamsInputname.'[DIRS][]"';
					$strResult .= 'value="'.$arItem['DIR'].'"';
					if(is_array($arPluginParams['DIRS']) && in_array($arItem['DIR'], $arPluginParams['DIRS'])){
						$strResult .= ' checked="checked"';
					}
					$strResult .= '/>';
					$strResult .= ' '.$arItem['NAME'].' ('.$arItem['FILE'].')';
					$strResult .= '</label>';
					if(is_array($arItem['ITEMS']) && !empty($arItem['ITEMS'])){
						$strResult .= $this->displayDirsStructure($arItem['ITEMS'], $intDepthLevel+1);
					}
					$strResult .= '</li>';
				}
				$strResult .= '</ul>';
			}
		}
		return $strResult;
	}
	
}

?>