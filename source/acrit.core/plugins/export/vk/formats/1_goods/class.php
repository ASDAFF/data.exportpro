<?
/**
 * Acrit Core: Vk.com plugin
 * @documentation https://vk.com/dev/goods_docs
 */

namespace Acrit\Core\Export\Plugins;

require_once __DIR__.'/lib/Image/autoload.php';
require_once __DIR__.'/lib/Cache/autoload.php';

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Xml,
	\Acrit\Core\Json,
	\Acrit\Core\Log,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Gregwar\Image\Image;

Loc::loadMessages(__FILE__);

class VkGoods extends Vk {
	
	CONST DATE_UPDATED = '2018-12-18';


	/**
	 * Base constructor
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}
	
	/* START OF BASE STATIC METHODS */
	
	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return parent::getCode().'_GOODS';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}

	/**
	 *	Get EXPORT_FILE_NAME
	 */
	public function getExportFileName(){
		if(strlen($this->arProfile['PARAMS']['GROUP_ID'])){
			return 'https://vk.com/market-'.$this->arProfile['PARAMS']['GROUP_ID'];
		}
		return false;
	}

	/**
	 *	Show plugin default settings
	 */
	protected function showDefaultSettings(){
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_ACCESS_TOKEN_HINT'));?>
					<?=static::getMessage('SETTINGS_ACCESS_TOKEN');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][ACCESS_TOKEN]" id="acrit_exp_plugin_vk_access_token" value="<?=$this->arProfile['PARAMS']['ACCESS_TOKEN'];?>" size="90" />
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_GROUP_ID_HINT'));?>
					<?=static::getMessage('SETTINGS_GROUP_ID');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][GROUP_ID]" id="acrit_exp_plugin_vk_group_id" value="<?=$this->arProfile['PARAMS']['GROUP_ID'];?>" size="30" />
					<?if(strlen($this->arProfile['PARAMS']['GROUP_ID'])):?>
						&nbsp;
						<?=$this->showFileOpenLink('https://vk.com/market-'.$this->arProfile['PARAMS']['GROUP_ID'], static::getMessage('SETTINGS_GROUP_ID_URL'));?>
					<?endif?>
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_CREATE_ALBUMS_HINT'));?>
					<?=static::getMessage('SETTINGS_PROCESS_CREATE_ALBUMS');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="checkbox" name="PROFILE[PARAMS][PROCESS_CREATE_ALBUMS]" id="acrit_exp_plugin_vk_process_create_albums" value="Y"<?=$this->arProfile['PARAMS']['PROCESS_CREATE_ALBUMS']=='Y'?' checked':'';?> />
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_DELETE_OTHER_HINT'));?>
					<?=static::getMessage('SETTINGS_PROCESS_DELETE_OTHER');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="checkbox" name="PROFILE[PARAMS][PROCESS_DELETE_OTHER]" id="acrit_exp_plugin_vk_process_delete_other" value="Y"<?=$this->arProfile['PARAMS']['PROCESS_DELETE_OTHER']=='Y'?' checked':'';?> />
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_DELETE_DUPLICATES_HINT'));?>
					<?=static::getMessage('SETTINGS_PROCESS_DELETE_DUPLICATES');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="checkbox" name="PROFILE[PARAMS][PROCESS_DELETE_DUPLICATES]" id="acrit_exp_plugin_vk_process_delete_duplicates" value="Y"<?=$this->arProfile['PARAMS']['PROCESS_DELETE_DUPLICATES']=='Y'?' checked':'';?> />
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_LIMIT_HINT'));?>
					<?=static::getMessage('SETTINGS_PROCESS_LIMIT');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][PROCESS_LIMIT]" id="acrit_exp_plugin_vk_process_run_limit" value="<?=$this->arProfile['PARAMS']['PROCESS_LIMIT']?$this->arProfile['PARAMS']['PROCESS_LIMIT']:0;?>" />
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_NEXT_POS_HINT'));?>
					<?=static::getMessage('SETTINGS_PROCESS_NEXT_POS');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<span style="margin-right: 20px;" id="acrit_exp_plugin_vk_process_next_pos_view"><?=$this->arProfile['PARAMS']['PROCESS_NEXT_POS']?$this->arProfile['PARAMS']['PROCESS_NEXT_POS']:0;?></span>
					<input type="hidden" name="PROFILE[PARAMS][PROCESS_NEXT_POS]" id="acrit_exp_plugin_vk_process_next_pos" value="0" />
					<a href="#" class="adm-btn" id="acrit_exp_plugin_vk_process_next_pos_reset"><?=static::getMessage('SETTINGS_PROCESS_NEXT_POS_RESET');?></a>
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_IMAGE_RESIZE_HINT'));?>
					<?=static::getMessage('SETTINGS_PROCESS_IMAGE_RESIZE');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<select name="PROFILE[PARAMS][IMAGE_RESIZE]">
						<option value="<?=self::IMAGE_RESIZE_FILL;?>"<?=($this->arProfile['PARAMS']['IMAGE_RESIZE']==self::IMAGE_RESIZE_FILL||!$this->arProfile['PARAMS']['IMAGE_RESIZE'])?' selected':'';?>><?=static::getMessage('SETTINGS_PROCESS_IMAGE_RESIZE_V_FILL');?></option>
						<option value="<?=self::IMAGE_RESIZE_RESIZE;?>"<?=($this->arProfile['PARAMS']['IMAGE_RESIZE']==self::IMAGE_RESIZE_RESIZE)?' selected':'';?>><?=static::getMessage('SETTINGS_PROCESS_IMAGE_RESIZE_V_RESIZE');?></option>
					</select>
				</td>
			</tr>
			<?/*
					<tr id="acrit_exp_vk_console">
						<td width="40%" class="adm-detail-content-cell-l" valign="top">
							<?=Helper::ShowHint(static::getMessage('SETTINGS_CONSOLE_HINT'));?>
							<?=static::getMessage('SETTINGS_CONSOLE');?>:
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<div><textarea placeholder="<?=static::getMessage('SETTINGS_CONSOLE_PLACEHOLDER')?>"></textarea></div>
							<div><input type="button" value="<?=static::getMessage('SETTINGS_CONSOLE_SEND');?>" /></div>
							<div id="acrit_exp_vk_console_result"></div>
						</td>
					</tr>
					*/?>
			</tbody>
		</table>
		<script>
            BX.message({'SETTINGS_PROCESS_NEXT_POS_RESET_ALERT': '<?=static::getMessage('SETTINGS_PROCESS_NEXT_POS_RESET_ALERT');?>'});
		</script>
		<?
		return ob_get_clean();
	}

	/**
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = array();
		$arResult[] = array(
			'DIV' => 'clear',
			'TAB' => static::getMessage('TAB_CLEAR_NAME'),
			'TITLE' => static::getMessage('TAB_CLEAR_DESC'),
			'SORT' => 20,
			'FILE' => __DIR__.'/tabs/clear.php',
		);
		$arResult[] = array(
			'DIV' => 'albums',
			'TAB' => static::getMessage('TAB_ALBUMS_NAME'),
			'TITLE' => static::getMessage('TAB_ALBUMS_DESC'),
			'SORT' => 21,
			'FILE' => __DIR__.'/tabs/albums.php',
		);
		return $arResult;
	}

	/**
	 *	Is it subclass?
	 */
	public static function isSubclass(){
		return true;
	}

	/**
	 *	Are categories export?
	 */
	public function areCategoriesExport(){
		return true;
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
		<?=$this->showFileOpenLink('https://vk.com/market-'.$this->arProfile['PARAMS']['GROUP_ID'], static::getMessage('SETTINGS_GROUP_ID_URL'));?>
		<?
		return Helper::showSuccess(ob_get_clean());
	}

	/* END OF BASE STATIC METHODS */

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
	 *	Update categories from server
	 */
	public function updateCategories($intProfileID){
		$strCategories = '';
		$strFileName = $this->getCategoriesCacheFile();
		$arRes = $this->request('market.getCategories', array(
			'count' => 1000,
			'offset' => 0,
		), $intProfileID);
		if ($arRes['response']['count']) {
			foreach ($arRes['response']['items'] as $arItem) {
				$strCategories .= $arItem['id'].': '.$arItem['name']."\n";
			}
		}
		if (strlen($strCategories)) {
//			if(!Helper::isUtf()){
//				$strCategories = Helper::convertEncoding($strCategories, 'CP1251', 'UTF-8');
//			}
			file_put_contents($strFileName, $strCategories);
			#
			unset($strCategories);
		}
		#
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
//			if(!Helper::isUtf()) {
//				return explode("\n", Helper::convertEncoding(file_get_contents($strFileName), 'UTF-8', 'CP1251'));
//			}
//			else {
				return explode("\n", file_get_contents($strFileName));
//			}
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

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
//		$arVKAlbumsRedefs = self::getAlbumsRedefs($intProfileID);
//		echo '<pre>'; print_r($arVKAlbumsRedefs); echo '</pre>';

		if($bAdmin){
			$arResult[] = new Field(array(
				'SORT' => 99,
				'NAME' => static::getMessage('HEADER_GENERAL'),
				'IS_HEADER' => true,
			));
		}
		$arResult[] = new Field(array(
			'CODE' => 'ITEM_ID',
			'DISPLAY_CODE' => 'item_id',
			'NAME' => static::getMessage('FIELD_ITEM_ID_NAME'),
			'SORT' => 500,
			'DESCRIPTION' => static::getMessage('FIELD_ITEM_ID_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'ID',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => '256',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'NAME',
			'DISPLAY_CODE' => 'name',
			'NAME' => static::getMessage('FIELD_NAME_NAME'),
			'SORT' => 520,
			'DESCRIPTION' => static::getMessage('FIELD_NAME_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'NAME',
					'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
				),
			),
			'PARAMS' => array(
				'MAXLENGHT' => 100,
				'HTMLSPECIALCHARS' => 'skip'
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DESCRIPTION',
			'DISPLAY_CODE' => 'description',
			'NAME' => static::getMessage('FIELD_DESCRIPTION_NAME'),
			'SORT' => 530,
			'DESCRIPTION' => static::getMessage('FIELD_DESCRIPTION_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_TEXT',
					'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
				),
			),
			'DEFAULT_VALUE_OFFERS' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.DETAIL_TEXT',
					'PARAMS' => array('HTMLSPECIALCHARS' => 'skip'),
				),
			),
			'PARAMS' => array('HTML2TEXT' => 'Y', 'HTMLSPECIALCHARS' => 'cut'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CATEGORY_ID',
			'DISPLAY_CODE' => 'category_id',
			'NAME' => static::getMessage('FIELD_CATEGORY_ID_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_CATEGORY_ID_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'SECTION__ID',
				),
			),
			'DEFAULT_VALUE_OFFERS' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.SECTION__ID',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'price',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 550,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'IS_PRICE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OLD_PRICE',
			'DISPLAY_CODE' => 'old_price',
			'NAME' => static::getMessage('FIELD_OLD_PRICE_NAME'),
			'SORT' => 550,
			'DESCRIPTION' => static::getMessage('FIELD_OLD_PRICE_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'IS_PRICE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'CATALOG_PRICE_1',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAIN_PHOTO_ID',
			'DISPLAY_CODE' => 'main_photo_id',
			'NAME' => static::getMessage('FIELD_MAIN_PHOTO_ID_NAME'),
			'SORT' => 560,
			'DESCRIPTION' => static::getMessage('FIELD_MAIN_PHOTO_ID_DESC'),
			'REQUIRED' => true,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'DETAIL_PICTURE',
					'MULTIPLE' => 'first',
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'escape',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PHOTO_IDS',
			'DISPLAY_CODE' => 'photo_ids',
			'NAME' => static::getMessage('FIELD_PHOTO_IDS_NAME'),
			'SORT' => 570,
			'DESCRIPTION' => static::getMessage('FIELD_PHOTO_IDS_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
				),
			),
			'MAX_COUNT' => 4,
		));
		$arResult[] = new Field(array(
			'CODE' => 'ALBUM',
			'DISPLAY_CODE' => 'album_name',
			'NAME' => static::getMessage('FIELD_ALBUM_NAME_NAME'),
			'SORT' => 580,
			'DESCRIPTION' => static::getMessage('FIELD_ALBUM_NAME_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'SECTION__NAME',
				),
			),
			'DEFAULT_VALUE_OFFERS' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.SECTION__NAME',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ALBUM_PHOTO',
			'DISPLAY_CODE' => 'album_photo_id',
			'NAME' => static::getMessage('FIELD_ALBUM_PHOTO_ID_NAME'),
			'SORT' => 585,
			'DESCRIPTION' => static::getMessage('FIELD_ALBUM_PHOTO_ID_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'SECTION__PICTURE',
					'MULTIPLE' => 'first',
				),
			),
			'DEFAULT_VALUE_OFFERS' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PARENT.SECTION__PICTURE',
					'MULTIPLE' => 'first',
				),
			),
			'PARAMS' => array(
				'HTMLSPECIALCHARS' => 'escape',
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELETED',
			'DISPLAY_CODE' => 'deleted',
			'NAME' => static::getMessage('FIELD_DELETED_NAME'),
			'SORT' => 590,
			'DESCRIPTION' => static::getMessage('FIELD_DELETED_DESC'),
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
					'CONST' => '0',
					'SUFFIX' => 'Y',
				),
				array(
					'TYPE' => 'CONST',
					'CONST' => '1',
					'SUFFIX' => 'N',
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

        # Build exported data
		$arApiFields = array();
		if(!Helper::isEmpty($arFields['OWNER_ID']))
			$arApiFields['owner_id'] = Json::addValue($arFields['OWNER_ID']);
		if(!Helper::isEmpty($arFields['NAME']))
			$arApiFields['name'] = Json::addValue($arFields['NAME']);
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arApiFields['description'] = Json::addValue($arFields['DESCRIPTION']);
		if(is_array($arElementSections) && !empty($arElementSections))
			$arApiFields['category_id'] = $this->getValue_getCategoryId($arProfile, $arFields, reset($arElementSections));
		if(!Helper::isEmpty($arFields['PRICE']))
			$arApiFields['price'] = Json::addValue($arFields['PRICE']);
		if(!Helper::isEmpty($arFields['OLD_PRICE']))
			$arApiFields['old_price'] = Json::addValue($arFields['OLD_PRICE']);
		if(!Helper::isEmpty($arFields['MAIN_PHOTO_ID']))
			$arApiFields['main_photo_id'] = Json::addValue($arFields['MAIN_PHOTO_ID']);
		if(!Helper::isEmpty($arFields['PHOTO_IDS']))
			$arApiFields['photo_ids'] = Json::addValue($arFields['PHOTO_IDS']);
		if(!Helper::isEmpty($arFields['DELETED']))
			$arApiFields['deleted'] = Json::addValue($arFields['DELETED']);
		if(!Helper::isEmpty($arFields['ALBUM']))
			$arApiFields['album_name'] = Json::addValue($arFields['ALBUM']);
		if(!Helper::isEmpty($arFields['ALBUM_PHOTO']))
			$arApiFields['album_photo'] = Json::addValue($arFields['ALBUM_PHOTO']);
		# Additional data
		$arApiFields['iblock_item_id'] = Json::addValue($arElement['ID']);
		$arApiFields['iblock_id'] = Json::addValue($arElement['IBLOCK_ID']);
		$arApiFields['iblock_section_id'] = Json::addValue($arElement['IBLOCK_SECTION_ID']);
		# build JSON
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnVkGoodsJson') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arApiFields, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# build result
		$arResult = array(
			'TYPE' => 'JSON',
			'DATA' => Json::encode($arApiFields),
			'CURRENCY' => '',
			'SECTION_ID' => $this->getElement_SectionID($intProfileID, $arElement),
			'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
			'DATA_MORE' => array(),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnVkGoodsResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arApiFields, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# after..
		unset($intProfileID, $intElementID, $arApiFields);
		return $arResult;
	}

	protected function getValue_getCategoryId($arProfile, $arFields, $mValue) {
		$intProfileID = $arProfile['ID'];
		#$arCategoryRedefinitions = CategoryRedefinition::getForProfile( $intProfileID );
		$arCategoryRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$intProfileID]);
		if (intval($mValue) && $arCategoryRedefinitions[$mValue]) {
			$sVKCateg = $arCategoryRedefinitions[$mValue];
			$arCateg = explode(':', $sVKCateg);
			$mValue = intval($arCateg[0]);
		}
		else {
			$mValue = $arFields['CATEGORY_ID'];
		}
		return Json::addValue($mValue);
	}

	/**
	 *	Get steps
	 */
	public function getSteps() {
		$arResult = array();
		$arResult['EXPORT'] = array(
			'NAME' => static::getMessage('STEP_EXPORT'),
			'SORT' => 100,
			'FUNC' => [$this, 'stepExport'],
		);
		return $arResult;
	}
	
	/**
	 *	Step: Export
	 */
	public function stepExport($intProfileID, $arData) {
		$arSession = &$arData['SESSION']['EXPORT'];
		$bIsCron = $arData['IS_CRON'];

		// Export
		$this->stepExport_sendApiOffers($intProfileID, $arData);

		return Exporter::RESULT_SUCCESS;
	}

	/**
	 *	Step: Export, write offers
	 *	@return Exporter::RESULT_SUCCESS || RESULT_CONTINUE
	 */
	protected function stepExport_sendApiOffers($intProfileID, $arData) {
		$intProcessLimit = intval($arData['PROFILE']['PARAMS']['PROCESS_LIMIT']);
		$intProcessNextPos = intval($arData['PROFILE']['PARAMS']['PROCESS_NEXT_POS']);
		// VK Params
		$strVkGroupId = strval($arData['PROFILE']['PARAMS']['GROUP_ID']);
		$strVkOwnerId = intval('-' . $strVkGroupId);
		// Find all goods in the group
		$arVKItemsIDs = $this->getGroupItemsIDs(true, false, $strVkOwnerId, $intProfileID);
		// Get albums
		$arVKAlbumsIDs = $this->getGroupAlbums($strVkOwnerId, $intProfileID);
		// Goods by albums
        $arVKAlbumsGoods = array();
        $arVKItems = $this->getGroupItems(false, false, true, $strVkOwnerId, $intProfileID);
        if (count($arVKItems)) {
            foreach ($arVKItems as $arVKItem) {
                if (count($arVKItem['albums_ids'])) {
                    foreach ($arVKItem['albums_ids'] as $album_id) {
                        $arVKAlbumsGoods[$album_id][] = $arVKItem['id'];
                    }
                }
            }
        }
		// Update albums photos array
		$arVKAlbums = array();
        // Albums redefinisions
		$arVKAlbumsRedefs = self::getAlbumsRedefs($intProfileID);
		//Log::getInstance($this->strModuleId)->add('$arVKAlbumsRedefs: '.print_r($arVKAlbumsRedefs, true), $intProfileID);
		self::updateVKAlbumsByRedefs($intProfileID);
		// Processed items
        $arChangedIDs = array();
		// Get export data
		$arQuery = [
			'PROFILE_ID' => $intProfileID,
			'!TYPE' => ExportData::TYPE_DUMMY,
		];
    #$intExportCount = ExportData::getCount($arQuery);
    $intExportCount = Helper::call($this->strModuleId, 'ExportData', 'getCount', [$arQuery]);
		$intOffset = 0;
        $intIndex = 0;
        $intProcessIndex = $intProcessNextPos;
		while ($intIndex < $intExportCount) {
			$intLimit = 1000;
			$strSortOrder = ToUpper($arData['PROFILE']['PARAMS']['SORT_ORDER']);
			if (!in_array($strSortOrder, array('ASC', 'DESC'))) {
				$strSortOrder = 'ASC';
			}
			$arQuery = [
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
				),
				'limit' => $intLimit,
				'offset' => $intOffset * $intLimit,
			];
			#$resItems = ExportData::getList($arQuery);
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$intExportedCount = 0;
			while ($arItem = $resItems->fetch()) {
				// Phased import
				if ($intProcessLimit) {
					if ($intIndex < $intProcessNextPos || $intIndex >= $intProcessNextPos + $intProcessLimit) {
						$intIndex++;
						continue;
					}
				}
				// Item data
				$arItemData = Json::decode($arItem['DATA']);
				$arItemData['owner_id'] = $strVkOwnerId;
				// Upload photo and get photo id
				if (strlen($arItemData['main_photo_id'])) {
                    $arItemData['main_photo_id'] = $this->uploadMarketPhoto($arItemData['main_photo_id'], 1, $strVkGroupId, $intProfileID);
					// Upload gallery
                    $arVKPhotoIDs = array();
                    if ($arItemData['photo_ids']) {
                        $arPhotoIDs = explode(',', $arItemData['photo_ids']);
                        $arPhotoIDs = array_map('trim', $arPhotoIDs);
                        $i = 0;
                        foreach ($arPhotoIDs as $photo_id) {
                            if ($i >= 4) {
                                continue;
                            }
                            if (intval($photo_id)) {
                                $arFile = \CFile::GetFileArray($photo_id);
                                $photo_url = $arFile['SRC'];
                            }
                            else {
                                $photo_url = $photo_id;
                            }
                            $photo_url .= (strpos($photo_url, '?') === false ? '?' : '&').rand(10000000,99999999);
                            $arVKPhotoIDs[] = $this->uploadMarketPhoto($photo_url, 0, $strVkGroupId, $intProfileID);
                            $i++;
                        }
                    }
                    $arItemData['photo_ids'] = implode(',', $arVKPhotoIDs);
					// Export item
                    $strVKItemId = $this->stepExport_findItem($arItemData['name'], $arVKItemsIDs);
					if ($strVKItemId) {
						Log::getInstance($this->strModuleId)->add('Editing product', $intProfileID, true);
						$arItemData['item_id'] = $strVKItemId;
						$arRes = $this->request('market.edit', $arItemData, $intProfileID);
						if (isset($arRes['response'])) {
							#
							$intExportedCount++;
						}
					}
					else {
						Log::getInstance($this->strModuleId)->add('Adding product', $intProfileID, true);
						$arRes = $this->request('market.add', $arItemData, $intProfileID);
						if (isset($arRes['response'])) {
							$strVKItemId = $arRes['response']['market_item_id'];
							#
							$intExportedCount++;
						}
						elseif(is_array($arRes['error'])) {
							$strErrorMessage = $arRes['error']['error_msg'].' ['.$arRes['error']['error_code'].']';
							Log::getInstance($this->strModuleId)->add('Error adding item '.$arItemData['iblock_item_id'].': '.$strErrorMessage, $intProfileID);
						}
					}
					if ($strVKItemId) {
                        $arChangedIDs[] = $strVKItemId;
                    }
                    // Add item to album
                    if ($strVKItemId) {
	                    //Log::getInstance($this->strModuleId)->add('$arItemData: '.print_r($arItemData, true), $intProfileID);
                        $arAddToAlbums = array();
						//Log::getInstance($this->strModuleId)->add('album_name: '.print_r($arItemData['album_name'], true), $intProfileID);
                        if ($arItemData['album_name']) {
                        	if (!is_array($arItemData['album_name'])) {
								$arAlbumNames = explode(',', $arItemData['album_name']);
							}
                        	else {
								$arAlbumNames = $arItemData['album_name'];
							}
                        	if (!empty($arAlbumNames)) {
								foreach ($arAlbumNames as $album_name) {
									$intAlbumId = false;
									$strSourceType = $arData['PROFILE']['IBLOCKS'][$arItemData['iblock_id']]['FIELDS']['ALBUM']['VALUES'][0]['VALUE'];
									if ($strSourceType == 'SECTION__ID') {
										// Check redefinitions
										$arRedef = self::findAlbumsRedef($intProfileID, $arItemData['iblock_id'], $arItemData['iblock_section_id'], $arVKAlbumsRedefs);
										//Log::getInstance($this->strModuleId)->add('$arRedef: '.print_r($arRedef, true), $intProfileID);
										if ($arRedef) {
											// If has album ID
											if ($arRedef['album_id']) {
												// Use this album ID
												$intAlbumId = $arRedef['album_id'];
											}
											// If has album redefinition name
											if ($arRedef['album_name']) {
												// Use this name for search
												$album_name = $arRedef['album_name'];
											} else {
												$album_name = $arRedef['section_name'];
											}
										}
									}
									//Log::getInstance($this->strModuleId)->add('$intAlbumId 1: '.print_r($intAlbumId, true), $intProfileID);
									if (!$intAlbumId) {
										$intAlbumId = $this->stepExport_findItem($album_name, $arVKAlbumsIDs);
										if ($intAlbumId) {
											if ( ! in_array($strVKItemId, $arVKAlbumsGoods[$intAlbumId])) {
												$arAddToAlbums[] = $intAlbumId;
											}
										} else {
											// Create album
											$intAlbumId = $this->vkCreateAlbum($intProfileID, array(
												'owner_id' => $strVkOwnerId,
												'name'     => $album_name,
											));
											Log::getInstance($this->strModuleId)->add('vkCreateAlbum: '.print_r($intAlbumId, true), $intProfileID);
											if ($intAlbumId) {
												if ( ! in_array($arVKAlbumsGoods[$intAlbumId])) {
													$arAddToAlbums[] = $intAlbumId;
												}
												$arVKAlbumsIDs[$album_name] = $intAlbumId;
											}
										}
										// Save album ID to redefinitions table
										if ($strSourceType == 'SECTION__ID' && $arVKAlbumsRedefs && $intAlbumId) {
											self::updateVKAlbumsRedefRow($intProfileID, $arItemData['iblock_id'], $arItemData['iblock_section_id'], $intAlbumId, $arVKAlbumsRedefs);
											$arVKAlbumsRedefs = self::getAlbumsRedefs($intProfileID);
										}
									}
									//Log::getInstance($this->strModuleId)->add('$intAlbumId 2: '.print_r($intAlbumId, true), $intProfileID);
									if ($intAlbumId && $arItemData['album_photo']) {
										$arVKAlbums[$intAlbumId]['title'] = $album_name;
										$arVKAlbums[$intAlbumId]['photo'] = $arItemData['album_photo'];
									}
								}
							}
                        }
                        if (!empty($arAddToAlbums)) {
                            $arRes = $this->request('market.addToAlbum', array(
                                'owner_id' => $strVkOwnerId,
                                'item_id' => $strVKItemId,
                                'album_ids' => $arAddToAlbums,
                            ), $intProfileID);
                            if (is_array($arRes['error'])) {
                                $strErrorMessage = $arRes['error']['error_msg'] . ' [' . $arRes['error']['error_code'] . ']';
                                Log::getInstance($this->strModuleId)->add('Error adding to album: ' . $strErrorMessage, $intProfileID);
                            }
                        }
                    }
				}
				$intIndex++;
                $intProcessIndex++;
			}
			// Count result
			$arData['SESSION']['EXPORT']['INDEX'] += $intExportedCount;
			$intOffset++;
		}
        // Phased import: reset start position
        if ($intProcessLimit) {
            if ($intProcessIndex == $intExportCount) {
                #Profile::setParam($intProfileID, array('PROCESS_NEXT_POS' => 0));
                Helper::call($this->strModuleId, 'Profile', 'setParam', [$intProfileID, array('PROCESS_NEXT_POS' => 0)]);
                Log::getInstance($this->strModuleId)->add(static::getMessage('PROCESS_PHASED_END_ALL'), $intProfileID);
            }
            else {
                #Profile::setParam($intProfileID, array('PROCESS_NEXT_POS' => $intProcessNextPos + $intProcessLimit));
								Helper::call($this->strModuleId, 'Profile', 'setParam', [$intProfileID, array('PROCESS_NEXT_POS' => $intProcessNextPos + $intProcessLimit)]);
                Log::getInstance($this->strModuleId)->add(static::getMessage('PROCESS_PHASED_END_STEP', array('#POSITION#' => ($intProcessNextPos + $intProcessLimit))), $intProfileID);
            }
        }
        // Delete elements that are not in the sample
        if (!$intProcessLimit && $arData['PROFILE']['PARAMS']['PROCESS_DELETE_OTHER'] == 'Y') {
            $this->stepExport_delItemsOther($arChangedIDs, $strVkOwnerId, $intProfileID);
        }
        // Delete duplicates of elements
        elseif ($arData['PROFILE']['PARAMS']['PROCESS_DELETE_DUPLICATES'] == 'Y') {
            $this->stepExport_delItemsDuplicates($arChangedIDs, $strVkOwnerId, $intProfileID);
        }
        // Update albums photos
        $this->stepExport_updateAlbumsPhoto($arVKAlbums, $strVkGroupId, $strVkOwnerId, $intProfileID);
	}

    // Get item ID by name
    protected function stepExport_findItem($strName, $arIDs) {
        $intItem = false;
        if (isset($arIDs[$strName])) {
            $intItem = $arIDs[$strName];
        }
        return $intItem;
    }

    // Delete elements that are not in the sample
    protected function stepExport_delItemsOther($arChangedIDs, $strVkOwnerId, $intProfileID) {
        $arVKAllIDs = $this->getGroupItemsIDs(false, false, $strVkOwnerId, $intProfileID);
        foreach ($arVKAllIDs as $item_id) {
            if (!in_array($item_id, $arChangedIDs)) {
                $arRes = $this->request('market.delete', array(
                    'owner_id' => $strVkOwnerId,
                    'item_id' => $item_id,
                ), $intProfileID);
            }
        }
    }

    // Delete duplicates of elements
    protected function stepExport_delItemsDuplicates($arChangedIDs, $strVkOwnerId, $intProfileID) {
	    $arDelIDs = array();
        $arVKAllIDs = $this->getGroupItemsIDs(true, true, $strVkOwnerId, $intProfileID);
        foreach ($arVKAllIDs as $arItemsIds) {
            if (count($arItemsIds) > 1) {
                $has_changed = false;
                foreach ($arItemsIds as $item_id) {
                    if (in_array($item_id, $arChangedIDs)) {
                        $has_changed = true;
                    }
                }
                $i = 0;
                foreach ($arItemsIds as $item_id) {
                    if (($has_changed && !in_array($item_id, $arChangedIDs)) || (!$has_changed && $i)) {
                        $arDelIDs[] = $item_id;
                    }
                    $i++;
                }
            }
        }
        foreach ($arDelIDs as $item_id) {
            $arRes = $this->request('market.delete', array(
                'owner_id' => $strVkOwnerId,
                'item_id' => $item_id,
            ), $intProfileID);
        }
    }

	// Find all goods in the group
	protected function getGroupItems($assoc, $all_variants, $extended, $strVkOwnerId, $intProfileID) {
		$arItems = array();
		$intCountPerTime = 200;
		$arRes = $this->request('market.search', array(
			'owner_id' => $strVkOwnerId,
			'count' => 1,
			'status' => 0,
		), $intProfileID);
		$count = $arRes['response']['count'];
		for ($i = 0; $i < $count; $i += $intCountPerTime) {
			$arRes = $this->request('market.search', array(
				'owner_id' => $strVkOwnerId,
				'count' => $intCountPerTime,
				'offset' => $i,
				'status' => 0,
				'extended' => $extended?1:0,
			), $intProfileID);
			if (!empty($arRes['response']['items'])) {
				foreach ($arRes['response']['items'] as $arItem) {
					if ($assoc) {
						if ($all_variants) {
							$arItems[trim($arItem['title'])][] = $arItem;
						}
						else {
							$arItems[trim($arItem['title'])] = $arItem;
						}
					}
					else {
						$arItems[] = $arItem;
					}
				}
			}
		}
		$arRes = $this->request('market.search', array(
			'owner_id' => $strVkOwnerId,
			'count' => 1,
			'status' => 2,
		), $intProfileID);
		$count = $arRes['response']['count'];
		for ($i = 0; $i < $count; $i += $intCountPerTime) {
			$arRes = $this->request('market.search', array(
				'owner_id' => $strVkOwnerId,
				'count' => $intCountPerTime,
				'offset' => $i,
				'status' => 2,
				'extended' => $extended?1:0,
			), $intProfileID);
			if (!empty($arRes['response']['items'])) {
				foreach ($arRes['response']['items'] as $arItem) {
					if ($assoc) {
						if ($all_variants) {
							$arItems[trim($arItem['title'])][] = $arItem;
						}
						else {
							$arItems[trim($arItem['title'])] = $arItem;
						}
					}
					else {
						$arItems[] = $arItem;
					}
				}
			}
		}
		return $arItems;
	}

	// Find goods IDs in the group
	protected function getGroupItemsIDs($assoc, $all_variants, $strVkOwnerId, $intProfileID, $arAddParams=false) {
		$arIDs = array();
		$intCountPerTime = 200;
		// Request params
		$arReqParamsCount = array(
			'owner_id' => $strVkOwnerId,
			'count' => 1,
		);
		$arReqParamsItems = array(
			'owner_id' => $strVkOwnerId,
			'count' => $intCountPerTime,
			'offset' => 0,
		);
		if ($arAddParams) {
			$arReqParamsCount = array_merge($arAddParams, $arReqParamsCount);
			$arReqParamsItems = array_merge($arAddParams, $arReqParamsItems);
		}
		// Request for active items
		$arRes = $this->request('market.get', $arReqParamsCount, $intProfileID);
		$count = $arRes['response']['count'];
		for ($i = 0; $i < $count; $i += $intCountPerTime) {
			$arReqParamsItems['offset'] = $i;
			$arRes = $this->request('market.get', $arReqParamsItems, $intProfileID);
			if (!empty($arRes['response']['items'])) {
				foreach ($arRes['response']['items'] as $arItem) {
					if ($assoc) {
						if ($all_variants) {
							$arIDs[trim($arItem['title'])][] = $arItem['id'];
						}
						else {
							$arIDs[trim($arItem['title'])] = $arItem['id'];
						}
					}
					else {
						$arIDs[] = $arItem['id'];
					}
				}
			}
		}
		// Request for inactive items
		$arReqParamsCount['status'] = 2;
		$arReqParamsItems['status'] = 2;
		$arRes = $this->request('market.search', $arReqParamsCount, $intProfileID);
		$count = $arRes['response']['count'];
		for ($i = 0; $i < $count; $i += $intCountPerTime) {
			$arReqParamsItems['offset'] = $i;
			$arRes = $this->request('market.search', $arReqParamsItems, $intProfileID);
			if (!empty($arRes['response']['items'])) {
				foreach ($arRes['response']['items'] as $arItem) {
					if ($assoc) {
						if ($all_variants) {
							$arIDs[trim($arItem['title'])][] = $arItem['id'];
						}
						else {
							$arIDs[trim($arItem['title'])] = $arItem['id'];
						}
					}
					else {
						$arIDs[] = $arItem['id'];
					}
				}
			}
		}
		return $arIDs;
	}

    // Get albums
    public function getGroupAlbums($strVkOwnerId, $intProfileID) {
        $arIDs = array();
        $arRes = $this->request('market.getAlbums', array(
            'owner_id' => $strVkOwnerId,
            'count' => 1,
        ), $intProfileID);
        $count = $arRes['response']['count'];
        $intCountPerTime = 100;
        for ($i=0; $i<$count; $i+=$intCountPerTime) {
            $arRes = $this->request('market.getAlbums', array(
                'owner_id' => $strVkOwnerId,
                'count' => $intCountPerTime,
                'offset' => $i,
            ), $intProfileID);
            if (!empty($arRes['response']['items'])) {
                foreach ($arRes['response']['items'] as $arItem) {
                    $arIDs[$arItem['title']] = $arItem['id'];
                }
            }
        }
        return $arIDs;
    }

    // Get albums
    public function getGroupAlbumsFull($strVkOwnerId, $intProfileID) {
        $arList = array();
        $arRes = $this->request('market.getAlbums', array(
            'owner_id' => $strVkOwnerId,
            'count' => 1,
        ), $intProfileID);
        $count = $arRes['response']['count'];
        $intCountPerTime = 100;
        for ($i=0; $i<$count; $i+=$intCountPerTime) {
            $arRes = $this->request('market.getAlbums', array(
                'owner_id' => $strVkOwnerId,
                'count' => $intCountPerTime,
                'offset' => $i,
            ), $intProfileID);
            if (!empty($arRes['response']['items'])) {
                foreach ($arRes['response']['items'] as $arItem) {
                    $arList[] = $arItem;
                }
            }
        }
        return $arList;
    }

    // Upload photo and get photo id
    protected function uploadMarketPhoto($photo_url, $main_photo, $strVkGroupId, $intProfileID) {
	    $photo_id = false;
        if (strlen($photo_url)) {
            $arParams = array(
                'group_id' => $strVkGroupId,
                'main_photo' => $main_photo,
            );
            $arRes = $this->request('photos.getMarketUploadServer', $arParams, $intProfileID);
            if ($arRes['response']['upload_url']) {
                $strUrlPath = Helper::getPathFromUrl($photo_url);
                // Resize
                $strUrlPath = $this->resizePicture($strUrlPath, 400, 400, $intProfileID);
                // Send to the VK server
                $arRes = $this->sendFileRemote($strUrlPath, $arRes['response']['upload_url']);
                if (strlen($arRes['photo'])) {
                    $arParams = array(
                        'group_id' => $strVkGroupId,
                        'photo' => stripslashes($arRes['photo']),
                        'server' => $arRes['server'],
                        'hash' => $arRes['hash'],
                        'crop_data' => $arRes['crop_data'],
                        'crop_hash' => $arRes['crop_hash'],
                    );
                    $arRes = $this->request('photos.saveMarketPhoto', $arParams, $intProfileID);
                    if ($arRes['response'][0]['id']) {
                        $photo_id = $arRes['response'][0]['id'];
                    }
                } elseif (is_array($arRes['error'])) {
                    $strErrorMessage = $arRes['error']['error_msg'] . ' [' . $arRes['error']['error_code'] . ']';
                    Log::getInstance($this->strModuleId)->add('Error loading photo [' . $strUrlPath . ']: ' . $strErrorMessage, $intProfileID);
                } elseif (strlen($arRes['error'])) {
                    Log::getInstance($this->strModuleId)->add('Error loading photo [' . $strUrlPath . ']: ' . $arRes['error'], $intProfileID);
                }
            } else {
                $strErrorMessage = $arRes['error']['error_msg'] . ' [' . $arRes['error']['error_code'] . ']';
                Log::getInstance($this->strModuleId)->add('Error getting upload server: ' . $strErrorMessage, $intProfileID);
            }
        }
        return $photo_id;
    }

    // Resize picture
    protected function resizePicture($strFileRelPath, $width, $height, $intProfileID) {
		#$strTmpDir = Profile::getTmpDir($intProfileID);
		$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$intProfileID]);
		$strTmpResizeDir = $strTmpDir . '/resize';
		if(!is_dir($strTmpResizeDir)){
			mkdir($strTmpResizeDir, BX_DIR_PERMISSIONS, true);
		}
		$strFilePath = $_SERVER['DOCUMENT_ROOT'] . $strFileRelPath;
        $arPath = explode('/', $strFileRelPath);
        $strFileNameTarget = $arPath[count($arPath)-4].'_'.$arPath[count($arPath)-3].'_'.$arPath[count($arPath)-2].'_'.$arPath[count($arPath)-1];
        $strFilePathTarget = $strTmpResizeDir . '/' . $strFileNameTarget;
        if (file_exists($strFilePathTarget) && (time() - filemtime($strFilePathTarget)) < self::IMAGE_RESIZE_CACHE_TIME) {
            $strFilePath = $strFilePathTarget;
        }
        else {
			#$arProfile = Profile::getProfiles($intProfileID);
			$type = $this->arProfile['PARAMS']['IMAGE_RESIZE'];
            $arSize = \CFile::GetImageSize($strFilePath);
            if ($arSize[0] < $width || $arSize[1] < $height) {
            	if ($type == self::IMAGE_RESIZE_RESIZE) {
					Image::open($strFilePath)
						->scaleResize($width, $height)
						->save($strFilePathTarget);
				}
            	else {
					Image::open($strFilePath)
						->resize($width, $height)
						->save($strFilePathTarget);
				}
                $strFilePath = $strFilePathTarget;
            }
        }
        return $strFilePath;
    }

    // Update albums photos
    protected function stepExport_updateAlbumsPhoto($arAlbums, $strVkGroupId, $strVkOwnerId, $intProfileID) {
        if (!empty($arAlbums)) {
            foreach ($arAlbums as $album_id => $arAlbum) {
                if ($arAlbum['photo']) {
                    $arParams = array(
                        'group_id' => $strVkGroupId,
                    );
                    $arRes = $this->request('photos.getMarketAlbumUploadServer', $arParams, $intProfileID);
                    if (isset($arRes['response'])) {
                        $strUrlPath = Helper::getPathFromUrl($arAlbum['photo']);
                        // Resize
                        $strUrlPath = $this->resizePicture($strUrlPath, 1280, 720, $intProfileID);
                        // Send to the VK server
                        $arRes = $this->sendFileRemote($strUrlPath, $arRes['response']['upload_url']);
                        if (!isset($arRes['error'])) {
                            $arParams = array(
                                'group_id' => $strVkGroupId,
                                'photo' => $arRes['photo'],
                                'server' => $arRes['server'],
                                'hash' => $arRes['hash'],
                                'gid' => $arRes['gid'],
                            );
                            $arRes = $this->request('photos.saveMarketAlbumPhoto', $arParams, $intProfileID);
                            if (isset($arRes['response'])) {
                                $arRes = $this->request('market.editAlbum', array(
                                    'owner_id' => $strVkOwnerId,
                                    'album_id' => $album_id,
                                    'title' => $arAlbum['title'],
                                    'photo_id' => $arRes['response'][0]['id'],
                                ), $intProfileID);
                                if(is_array($arRes['error'])) {
                                    $strErrorMessage = $arRes['error']['error_msg'].' ['.$arRes['error']['error_code'].']';
                                    Log::getInstance($this->strModuleId)->add('Error editing album: '.$strErrorMessage, $intProfileID);
                                }
                            }
                            elseif(is_array($arRes['error'])) {
                                $strErrorMessage = $arRes['error']['error_msg'].' ['.$arRes['error']['error_code'].']';
                                Log::getInstance($this->strModuleId)->add('Error saving album photo ['.$strUrlPath.']: '.$strErrorMessage, $intProfileID);
                            }
                        } else {
                            $strErrorMessage = $arRes['error']['error_msg'].' ['.$arRes['error']['error_code'].']';
                            Log::getInstance($this->strModuleId)->add('Error sending file '.$strErrorMessage, $intProfileID);
                        }
                    }
                    elseif(is_array($arRes['error'])) {
                        $strErrorMessage = $arRes['error']['error_msg'].' ['.$arRes['error']['error_code'].']';
                        Log::getInstance($this->strModuleId)->add('Error getting upload server for album: '.$strErrorMessage, $intProfileID);
                    }
                }
            }
        }
    }

	/**
	 *	Create album
	 */

    protected function vkCreateAlbum($intProfileID, $arParams) {
        $res = false;
        #$arProfile = Profile::getProfiles($intProfileID);
        if ($this->arProfile['PARAMS']['PROCESS_CREATE_ALBUMS'] == 'Y') {
            $arRes = $this->request('market.addAlbum', array(
                'owner_id' => $arParams['owner_id'],
                'title'    => $arParams['name'],
            ), $intProfileID);
            if ($arRes['response']['market_album_id']) {
                $res = $arRes['response']['market_album_id'];
            }
        }
        return $res;
    }

    /**
	 * Ajax actions
	 */

	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		#$arProfile = Profile::getProfiles($arParams['PROFILE_ID']);
		$strVkGroupId = strval($this->arProfile['PARAMS']['GROUP_ID']);
		$strVkOwnerId = intval('-' . $strVkGroupId);
		switch ($strAction) {
			case 'items_clear_all_get_list':
				$arJsonResult['list'] = $this->vkItemsClearAllGetIDs($this->arProfile, $strVkOwnerId);
				$arJsonResult['result'] = 'ok';
			break;
			case 'items_clear_loaded_get_list':
				$arJsonResult['list'] = $this->vkItemsClearAllLoadedGetIDs($this->arProfile, $strVkOwnerId);
				$arJsonResult['result'] = 'ok';
			break;
			case 'items_clear_album_get_list':
				$arJsonResult['list'] = $this->vkItemsClearAlbumGetIDs($this->arProfile, $strVkOwnerId, $arParams['POST']['id']);
				$arJsonResult['result'] = 'ok';
			break;
			case 'items_clear_delete':
				$step_limit = 50;
				$arIDs = $arParams['POST']['list'];
				$step = $arParams['POST']['step'];
				$arJsonResult['not_empty'] = $this->vkItemsClearDeleteIDs($this->arProfile, $strVkOwnerId, $arIDs, $step_limit, $step);
				$arJsonResult['result'] = 'ok';
				break;
			case 'params_next_pos_reset':
				#$res = Profile::setParam($arParams['PROFILE_ID'], array('PROCESS_NEXT_POS' => 0));
				$res = Helper::call($this->strModuleId, 'Profile', 'setParam', [$arParams['PROFILE_ID'], array('PROCESS_NEXT_POS' => 0)]);
				if ($res) {
					$arJsonResult['result'] = 'ok';
				}
				else {
					$arJsonResult['result'] = 'error';
				}
				break;
			case 'albums_sections_list':
				//$arList = $this->getGroupAlbumsFull($strVkOwnerId, $arParams['PROFILE_ID']);
				$arList = $this->getAlbumsRedefs($arParams['PROFILE_ID']);
				if ($arList) {
					$arJsonResult['result'] = 'ok';
					$arJsonResult['list'] = $arList;
				}
				else {
					$arJsonResult['result'] = 'error';
				}
				break;
			case 'albums_sections_update':
				$arRedefs = $arParams['POST']['table'];
				if (!empty($arRedefs)) {
					foreach ($arRedefs as $k => $arRedef) {
						foreach ($arRedef as $j => $value) {
							if (!Helper::isUtf()){
								$arRedefs[$k][$j] = Helper::convertEncoding($value, 'UTF-8', 'CP1251');
							}
						}
					}
				}
				$res = self::updateAlbumRedef($arParams['PROFILE_ID'], $arRedefs);
				if ($res) {
					$arJsonResult['result'] = 'ok';
				}
				else {
					$arJsonResult['result'] = 'error';
				}
				break;
		}
	}

	/**
	 * Clear items
	 */

	protected function vkItemsClearAllGetIDs($arProfile, $strVkOwnerId) {
		$arDelIDs = array();
		$intProfileID = $arProfile['ID'];

		$arVKAllIDs = $this->getGroupItemsIDs(true, true, $strVkOwnerId, $intProfileID);
		if (!empty($arVKAllIDs)) {
			foreach ($arVKAllIDs as $arIDs) {
				foreach ($arIDs as $item_id) {
					$arDelIDs[] = $item_id;
				}
			}
		}
		return $arDelIDs;
	}

	protected function vkItemsClearAllLoadedGetIDs($arProfile, $strVkOwnerId) {
		$arDelIDs = array();
		$intProfileID = $arProfile['ID'];
		// Get items ready for export
		$arExpItems = [];
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'!TYPE' => ExportData::TYPE_DUMMY,
			),
			'order' => array(
				'SORT' => 'ASC'
			),
			'select' => array(
				'IBLOCK_ID',
				'ELEMENT_ID',
				'SECTION_ID',
				'TYPE',
				'DATA',
			),
		];
		#$resItems = ExportData::getList($arQuery);
		$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
		while($arItem = $resItems->fetch()) {
			$arItemData = Json::decode($arItem['DATA']);
			$arExpItems[] = $arItemData['name'];
		}
//		foreach ($arProfile['IBLOCKS'] as $arIblock) {
//			$intIBlockID = $arIblock['IBLOCK_ID'];
//			$arFilter = \Acrit\Core\ProfileTable::getFilter($intProfileID, $intIBlockID);
//			$res = \CIBlockElement::GetList(['SORT' => 'asc'], $arFilter, false, false, ['ID', 'NAME']);
//			while ($ob = $res->GetNextElement()) {
//			    $arItem = $ob->GetFields();
//				$arExpItems[] = $arItem['NAME'];
//			}
//		}
		$arExpItems = array_unique($arExpItems);
		// Delete only found items
		$arVKAllIDs = $this->getGroupItemsIDs(true, true, $strVkOwnerId, $intProfileID);
		if (!empty($arVKAllIDs)) {
			foreach ($arVKAllIDs as $name => $arIDs) {
				if (in_array(trim($name), $arExpItems)) {
					foreach ($arIDs as $item_id) {
						$arDelIDs[] = $item_id;
					}
				}
			}
		}
		return $arDelIDs;
	}

	protected function vkItemsClearAlbumGetIDs($arProfile, $strVkOwnerId, $intVkAlbumId) {
		$arDelIDs = array();
		if (!$intVkAlbumId) {
			return;
		}
		$intProfileID = $arProfile['ID'];
		$arVKAllIDs = $this->getAlbumItemsIDs(true, true, $strVkOwnerId, $intProfileID, $intVkAlbumId);
		if (!empty($arVKAllIDs)) {
			foreach ($arVKAllIDs as $arIDs) {
				foreach ($arIDs as $item_id) {
					$arDelIDs[] = $item_id;
				}
			}
		}
		return $arDelIDs;
	}

	protected function vkItemsClearDeleteIDs($arProfile, $strVkOwnerId, $arIDs, $limit=0, $step=0) {
		$result = true;
		$intProfileID = $arProfile['ID'];
		$i = 0;
		//Log::getInstance($this->strModuleId)->add('(vkItemsClearDeleteIDs) $arDelIDs: '.print_r($arIDs, true), $intProfileID);
		foreach ($arIDs as $item_id) {
			if (!$limit || ($i >= $limit * $step && $i < $limit * ($step + 1))) {
				//Log::getInstance($this->strModuleId)->add('(vkItemsClearDeleteIDs) $item_id: '.$item_id, $intProfileID);
				$this->request('market.delete', array(
					'owner_id' => $strVkOwnerId,
					'item_id'  => $item_id,
				), $intProfileID);
			}
			$i++;
		}
		if (!$limit || ($limit * $step) >= count($arIDs)) {
			$result = false;
		}
		return $result;
	}

	protected function getAlbumItemsIDs($assoc, $all_variants, $strVkOwnerId, $intProfileID, $intVkAlbumId) {
		$arFilter = array(
			'album_id' => $intVkAlbumId,
		);
		$arVKAllIDs = $this->getGroupItemsIDs(true, true, $strVkOwnerId, $intProfileID, $arFilter);
		return $arVKAllIDs;
	}


	/**
	 * Albums
	 */

	// Get redefinations table for albums
	protected function getAlbumsRedefs($intProfileID) {
		#$arProfile = Profile::getProfiles($intProfileID);
		$arList = false;
		if ($this->arProfile['PARAMS']['ALBUMS_REDEF'] && is_array($this->arProfile['PARAMS']['ALBUMS_REDEF'])) {
			$arRedefs = $this->arProfile['PARAMS']['ALBUMS_REDEF'];
		}
		$arSections = array();
		if ($this->arProfile['IBLOCKS'] && is_array($this->arProfile['IBLOCKS']) && ! empty($this->arProfile['IBLOCKS'])) {
			foreach ($this->arProfile['IBLOCKS'] as $arIblock) {
				if ($arIblock['SECTIONS_MODE'] == 'all') {
					$arFilter = Array('IBLOCK_ID' => $arIblock['IBLOCK_ID']);
					$dbList   = \CIBlockSection::GetList(Array('SORT' => 'ASC'), $arFilter);
					while ($arSection = $dbList->GetNext()) {
						$arSections[] = $arSection;
					}
				}
				elseif ($arIblock['SECTIONS_ID']) {
					$arSIDs = explode(',', $arIblock['SECTIONS_ID']);
					if ($arSIDs) {
						$arFilter = Array('IBLOCK_ID' => $arIblock['IBLOCK_ID'], 'ID' => $arSIDs);
						$dbList   = \CIBlockSection::GetList(Array('SORT' => 'ASC'), $arFilter);
						while ($arSection = $dbList->GetNext()) {
							if ($arIblock['SECTIONS_MODE'] == 'selected_with_subsections') {
								$arFilter = Array('IBLOCK_ID' => $arIblock['IBLOCK_ID'], 'LEFT_MARGIN' => $arSection['LEFT_MARGIN'], 'RIGHT_MARGIN' => $arSection['RIGHT_MARGIN']);
								$dbList   = \CIBlockSection::GetList(Array('LEFT_MARGIN' => 'ASC'), $arFilter);
								while ($arSection = $dbList->GetNext()) {
									$arSections[] = $arSection;
								}
							}
							else {
								$arSections[] = $arSection;
							}
						}
					}
				}
			}
			if (!empty($arSections)) {
				foreach ($arSections as $arSection) {
					$arItem = array(
						'iblock_id'   => $arSection['IBLOCK_ID'],
						'section_id'   => $arSection['ID'],
						'section_name' => $arSection['NAME'],
						'album_id'     => 0,
						'album_name'   => '',
					);
					if ($arRedefs[$arSection['ID']]) {
						$arItem['album_id'] = $arRedefs[$arSection['ID']]['album_id'];
						$arItem['album_name'] = $arRedefs[$arSection['ID']]['album_name'];
					}
					$arList[$arSection['ID']] = $arItem;
				}
			}
		}
		return $arList;
	}

	protected function updateAlbumRedef($intProfileID, $arRedefs) {
		#$res = Profile::setParam($intProfileID, array('ALBUMS_REDEF' => $arRedefs));
		$res = Helper::call($this->strModule, 'Profile', 'setParam', [$intProfileID, array('ALBUMS_REDEF' => $arRedefs)]);
		return $res;
	}

	// Get redefinations table for albums
	protected function findAlbumsRedef($intProfileID, $iblock_id, $section_id, $arRedefs=false) {
		$arResult = false;
		if (!$arRedefs) {
			$arRedefs = self::getAlbumsRedefs($intProfileID);
		}
		if (!empty($arRedefs)) {
			foreach ($arRedefs as $arRedef) {
				if ($iblock_id == $arRedef['iblock_id'] && $section_id == $arRedef['section_id']) {
					$arResult = $arRedef;
				}
			}
		}
		return $arResult;
	}

	// Update redefinations table for albums
	protected function updateVKAlbumsByRedefs($intProfileID) {
		$arRedefs = self::getAlbumsRedefs($intProfileID);
		// Get redefinations table
		$arAIDs = array();
		$arNames = array();
		if (is_array($arRedefs)) {
			foreach ($arRedefs as $arRedef) {
				if ($arRedef['album_id']) {
					$arAIDs[] = $arRedef['album_id'];
					if ($arRedef['album_name']) {
						$arNames[$arRedef['album_id']] = $arRedef['album_name'];
					}
				}
			}
		}
		// Update albums names by table (if IDs sets)
		$arRes = $this->request('market.getAlbumById', array(
			'owner_id' => '-170462830',
			'album_ids' => implode(',', $arAIDs),
		), $intProfileID);
		if ($arRes['response']) {
			if (!empty($arRes['response']['items'])) {
				foreach ($arRes['response']['items'] as $arItem) {
					if ($arNames[$arItem['id']] && $arItem['title'] != $arNames[$arItem['id']]) {
						$arRes = $this->request('market.editAlbum', array(
							'owner_id' => $arItem['owner_id'],
							'album_id' => $arItem['id'],
							'title' => $arNames[$arItem['id']],
							//'photo_id' => $arRes['response'][0]['id'],
						), $intProfileID);
						if(is_array($arRes['error'])) {
							$strErrorMessage = $arRes['error']['error_msg'].' ['.$arRes['error']['error_code'].']';
							Log::getInstance($this->strModuleId)->add('Error editing album: '.$strErrorMessage, $intProfileID);
						}
					}
				}
			}
		}
	}

	protected function updateVKAlbumsRedefRow($intProfileID, $iblock_id, $section_id, $album_id, $arRedefs=false) {
		$result = false;
		if (!$arRedefs) {
			$arRedefs = self::getAlbumsRedefs($intProfileID);
		}
		if (!empty($arRedefs)) {
			foreach ($arRedefs as $k => $arRedef) {
				if ($iblock_id == $arRedef['iblock_id'] && $section_id == $arRedef['section_id']) {
					$arRedefs[$k]['album_id'] = $album_id;
				}
			}
		}
		self::updateAlbumRedef($intProfileID, $arRedefs);
		return $result;
	}

}

?>