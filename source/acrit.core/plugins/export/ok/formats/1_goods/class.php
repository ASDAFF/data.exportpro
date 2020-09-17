<?
/**
 * Acrit Core: Ok.ru plugin
 * @documentation https://apiok.ru/dev/methods/rest/market/
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Xml,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Log,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\Plugins\OdnoklassnikiSDK as OkSDK;

Loc::loadMessages(__FILE__);

class OkGoods extends Ok {
	
	CONST DATE_UPDATED = '2019-06-27';


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
	 *	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID){
		$arResult = array();
//		$arResult[] = array(
//			'DIV' => 'clear',
//			'TAB' => static::getMessage('TAB_CLEAR_NAME'),
//			'TITLE' => static::getMessage('TAB_CLEAR_DESC'),
//			'SORT' => 20,
//			'FILE' => __DIR__.'/tabs/clear.php',
//		);
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

	/* END OF BASE STATIC METHODS */

	/**
	 *	Is plugin works just with own categories of products
	 */
	public function isCategoryStrict(){
		return false;
	}

	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){
		return false;
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);

//		if($bAdmin){
//			$arResult[] = new Field(array(
//				'SORT' => 99,
//				'NAME' => static::getMessage('HEADER_GENERAL'),
//				'IS_HEADER' => true,
//			));
//		}
		$arResult[] = new Field(array(
			'CODE' => 'NAME',
			'DISPLAY_CODE' => 'name',
			'NAME' => static::getMessage('FIELD_NAME_NAME'),
			'SORT' => 510,
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
			'SORT' => 520,
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
			'CODE' => 'PICTURE',
			'DISPLAY_CODE' => 'picture',
			'NAME' => static::getMessage('FIELD_MAIN_PHOTO_ID_NAME'),
			'SORT' => 530,
			'DESCRIPTION' => static::getMessage('FIELD_MAIN_PHOTO_ID_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
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
			'CODE' => 'PRICE',
			'DISPLAY_CODE' => 'price',
			'NAME' => static::getMessage('FIELD_PRICE_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_PRICE_DESC'),
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
			'CODE' => 'CURRENCY',
			'DISPLAY_CODE' => 'currency',
			'NAME' => static::getMessage('FIELD_CURRENCY_NAME'),
			'SORT' => 550,
			'DESCRIPTION' => static::getMessage('FIELD_CURRENCY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => false,
			'IS_PRICE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => 'RUB',
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CATEGORY',
			'DISPLAY_CODE' => 'category',
			'NAME' => static::getMessage('FIELD_CATEGORY_NAME'),
			'SORT' => 560,
			'DESCRIPTION' => static::getMessage('FIELD_CATEGORY_DESC'),
			'REQUIRED' => false,
			'MULTIPLE' => true,
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
			'CODE' => 'CATEGORY_PHOTO',
			'DISPLAY_CODE' => 'category_photo',
			'NAME' => static::getMessage('FIELD_CATEGORY_PHOTO_NAME'),
			'SORT' => 570,
			'DESCRIPTION' => static::getMessage('FIELD_CATEGORY_PHOTO_DESC'),
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
					'CONST' => '1',
					'SUFFIX' => 'Y',
				),
				array(
					'TYPE' => 'CONST',
					'CONST' => '0',
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
		//Log::getInstance($this->strModuleId)->add('$arElementSections: '.print_r($arElementSections, true), $intProfileID);

        # Build exported data
		$arApiFields = array();
		if(!Helper::isEmpty($arFields['NAME']))
			$arApiFields['name'] = Json::addValue($arFields['NAME']);
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arApiFields['description_market'] = Json::addValue($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['PICTURE']))
			$arApiFields['picture'] = Json::addValue($arFields['PICTURE']);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arApiFields['price'] = Json::addValue($arFields['PRICE']);
		if(!Helper::isEmpty($arFields['CURRENCY']))
			$arApiFields['currency'] = Json::addValue($arFields['CURRENCY']);
//		if(is_array($arElementSections) && !empty($arElementSections))
//			$arApiFields['category_id'] = static::getCategoryRedef($arProfile, $arFields, reset($arElementSections));
		if(!Helper::isEmpty($arFields['CATEGORY'])) {
			if (!is_array($arFields['CATEGORY'])) {
				$arFields['CATEGORY'] = [ $arFields['CATEGORY'] ];
			}
			foreach ($arFields['CATEGORY'] as $category) {
				if((int)$category) {
					$default_name = false;
					if ($arElement['SECTION']['ID']) {
						$resS         = \CIBlockSection::GetByID($arElement['SECTION']['ID']);
						$arSection    = $resS->GetNext();
						$default_name = $arSection['NAME'];
					}
					$categ_name = static::getCategoryRedef($arProfile, $arFields, (int)$category, $default_name);
				}
				else {
					$categ_name = $category;
				}
				if ($categ_name) {
					$arApiFields['category'][] = Json::addValue($categ_name);
				}
			}
		}
		if(!Helper::isEmpty($arFields['CATEGORY_PHOTO']))
			$arApiFields['category_photo'] = Json::addValue($arFields['CATEGORY_PHOTO']);
		if(!Helper::isEmpty($arFields['AVAILABLE']))
			$arApiFields['available'] = Json::addValue($arFields['AVAILABLE']);
		# Additional data
		$arApiFields['iblock_item_id'] = Json::addValue($arElement['ID']);
		$arApiFields['iblock_id'] = Json::addValue($arElement['IBLOCK_ID']);
		$arApiFields['iblock_section_id'] = Json::addValue($arElement['IBLOCK_SECTION_ID']);
		# build JSON
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnOkGoodsJson') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arApiFields, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# build result
		$arResult = array(
			'TYPE' => 'JSON',
			'DATA' => Json::encode($arApiFields),
			'CURRENCY' => '',
			'SECTION_ID' => static::getElement_SectionID($intProfileID, $arElement),
			'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
			'DATA_MORE' => array(),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnOkGoodsResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arApiFields, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# after..
		unset($intProfileID, $intElementID, $arApiFields);
		return $arResult;
	}


	/**
	 *	Utilities
	 */

	function getCurlFilename( $fileName ){
		if( version_compare( PHP_VERSION, "5.6.0", "<" ) ){
			return "@".$fileName;
		}
		else{
			return new \CURLFile( $fileName );
		}
	}

	function curlPost( $url, $postData, $params = array() ){
		if( $url == "" )
			return false;

		$isHttps = ( strpos( $url, "https" ) === 0 );

		if( is_array( $cookiePostfix ) ){
			$cookieType = $cookiePostfix[0];
			$cookiePostfix = $cookiePostfix[1];
		}
		else
			$cookieType = ( empty( $cookiePostfix )? 0 : 1 );

		$cookieFile = "/upload/tmp/acrit.core/";
		if( $params["cookie_type"] and ( is_dir( $cookieFile ) or mkdir( $cookieFile, BX_DIR_PERMISSIONS, true ) ) ){
			if( empty( $params["cookie_postfix"] ) ){
				$cookieFile .= "cookie.txt";
			}
			else{
				$cookieFile .= "cookie_".$params["cookie_postfix"].".txt";
			}
		}
		else{
			$cookieFile .= "cookie.txt";
		}

		$c = curl_init( $url );
		if( $params["CUSTOM_REQUEST"] ){
			curl_setopt( $c, CURLOPT_CUSTOMREQUEST, $params["CUSTOM_REQUEST"] );
		}

		curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $c, CURLOPT_FOLLOWLOCATION, true );

		if( !!$params["user_agent"] ){
			curl_setopt( $c, CURLOPT_USERAGENT, $params["user_agent"] );
		}

		if( $params["cookie_type"] == 1 ){
			curl_setopt( $c, CURLOPT_COOKIEFILE, $cookieFile );
			curl_setopt( $c, CURLOPT_COOKIEJAR, $cookieFile );
		}
		elseif( $params["cookie_type"] == 2 ){
			curl_setopt( $c, CURLOPT_COOKIEJAR, $cookieFile );
		}
		elseif( $params["cookie_type"] == 3 ){
			curl_setopt( $c, CURLOPT_COOKIEFILE, $cookieFile );
		}

		if( $isHttps ){
			curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt( $c, CURLOPT_SSL_VERIFYHOST, 0 );
		}

		if( !empty( $postData ) ){
			if( !$params["CUSTOM_REQUEST"] or ( $params["CUSTOM_REQUEST"] == "POST" ) ){
				curl_setopt( $c, CURLOPT_POST, true );
			}

			if( isset( $params["enctype"] ) && ( $params["enctype"] == self::CURL_ENCTYPE_APPLICATION ) ){
				$postData = http_build_query( $postData );
				curl_setopt( $c, CURLOPT_HTTPHEADER, array( "Content-Length: " . strlen( $postData ) ) );
			}
			curl_setopt( $c, CURLOPT_POSTFIELDS, $postData );
		}

		curl_setopt( $c, CURLOPT_TIMEOUT, $params["timeout"] );
		$res = curl_exec( $c );
		curl_close( $c );
		usleep( 500000 );

		return $res;
	}

	function _curlPost( $url, $postData, $cookiePostfix = "", $userAgent = false, $timeout = 120 ){
		$params = array();
		if( is_array( $cookiePostfix ) ){
			$params["cookie_type"] = $cookiePostfix[0] ?: $cookiePostfix["type"];
			$params["cookie_postfix"] = $cookiePostfix[1] ?: $cookiePostfix["postfix"];
			$params["cookies"] = $cookiePostfix["cookies"];
		}
		elseif( !empty( $cookiePostfix ) ){
			$params["cookie_type"] = 1;
			$params["cookie_postfix"] = $cookiePostfix;
		}

		$params["user_agent"] = $userAgent;
		$params["timeout"] = $timeout;

		return self::curlPost( $url, $postData, $params );
	}


	/**
	 *	Get steps
	 */

	public function getSteps() {
		$arResult = array();
		$arResult['EXPORT'] = array(
			'NAME' => static::getMessage('STEP_EXPORT'),
			'SORT' => 100,
			'FUNC' => array($this, 'stepExport'),
		);
		return $arResult;
	}

	public function stepExport_init($intProfileID, $arData) {
		#self::$intProfileID = $intProfileID;
		$this->strGroupId = $arData['PROFILE']['PARAMS']['GROUP_ID'];
	}

	/**
	 *	Step: Export
	 */

	public function stepExport($intProfileID, $arData) {
		$arSession = &$arData['SESSION']['EXPORT'];
		$bIsCron = $arData['IS_CRON'];
		$intProcessLimit = intval($arData['PROFILE']['PARAMS']['PROCESS_LIMIT']);
		$intProcessNextPos = intval($arData['PROFILE']['PARAMS']['PROCESS_NEXT_POS']);
		self::stepExport_init($intProfileID, $arData);
		self::initConnection($intProfileID);
		$arOKCatalogsUpdate = [];

		Log::getInstance($this->strModuleId)->add('step 1: ', 16);
		// Find all goods in the group
		$arOKItemsIDs = self::getMarketItems();
		// Find all goods in the group
		$arOKCatalogsIDs = self::getMarketCatalogsByGroup();
		//Log::getInstance($this->strModuleId)->add('$arOKCatalogsIDs: '.print_r($arOKCatalogsIDs, true), self::$intProfileID);

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
			$intLimit     = 1000;
			$strSortOrder = ToUpper($arData['PROFILE']['PARAMS']['SORT_ORDER']);
			if ( ! in_array($strSortOrder, array('ASC', 'DESC'))) {
				$strSortOrder = 'ASC';
			}
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'!TYPE'      => ExportData::TYPE_DUMMY,
				),
				'order'  => array(
					'SORT' => $strSortOrder,
				),
				'select' => array(
					'IBLOCK_ID',
					'ELEMENT_ID',
					'SECTION_ID',
					'TYPE',
					'DATA',
				),
				'limit'  => $intLimit,
				'offset' => $intOffset * $intLimit,
			];
			#$resItems = ExportData::getList($arQuery);
			$resItems = Helper::call($this->strModuleId, 'ExportData', 'getList', [$arQuery]);
			$intExportedCount = 0;
			// Export process
			while ($arItem = $resItems->fetch()) {
				// Phased import
				if ($intProcessLimit) {
					if ($intIndex < $intProcessNextPos || $intIndex >= $intProcessNextPos + $intProcessLimit) {
						$intIndex ++;
						continue;
					}
				}
				// Item data
				$arItemData = Json::decode($arItem['DATA']);
				// Convert encoding
				if (!Helper::isUtf()){
					$arItemData['name'] = Helper::convertEncoding($arItemData['name'], 'CP1251', 'UTF-8');
					$arItemData['description_market'] = Helper::convertEncoding($arItemData['description_market'], 'CP1251', 'UTF-8');
					$arItemData['category'] = Helper::convertEncoding($arItemData['category'], 'CP1251', 'UTF-8');
				}

				// Catalogs
				$arOKCatalogIds = false;
				if (is_array($arItemData['category']) && !empty($arItemData['category'])) {
					foreach ($arItemData['category'] as $category_name) {
						$intOKCatalogId = static::findItemID($category_name, $arOKCatalogsIDs);
						if (!$intOKCatalogId) {
							$arCategData = [
								'name'    => $category_name,
								'picture' => $arItemData['category_photo']
							];
							$arResult    = self::addMarketCatalog($arCategData);
							if ($arResult['success']) {
								$intOKCatalogId = $arResult['catalog_id'];
								$arOKCatalogsIDs[$category_name] = $intOKCatalogId;
							}
						}
						else {
							$arOKCatalogsUpdate[$intOKCatalogId]['name']    = $category_name;
							$arOKCatalogsUpdate[$intOKCatalogId]['picture'] = $arItemData['category_photo'];
						}
						if ($intOKCatalogId) {
							$arOKCatalogIds[] = $intOKCatalogId;
						}
					}
				}
				if ($arOKCatalogIds) {
					$arItemData['catalogs'] = $arOKCatalogIds;
				}
				// Pictures
				if ($arItemData['picture']) {
					$arItemData['picture'] = ! is_array($arItemData['picture']) ? [$arItemData['picture']] : $arItemData['picture'];
				}

				// Export item
				$strOKItemId = static::findItemID($arItemData['name'], $arOKItemsIDs);
				$arResult = self::addMarketItem($arItemData, $strOKItemId);
				if ($arResult["success"]) {
					//Log::getInstance($this->strModuleId)->add('addMarketItem: '.print_r($arResult, true), self::$intProfileID);
					if (!$strOKItemId) {
						$strOKItemId = $arResult['product_id'];
					}
					$intExportedCount++;
				}
				if ($strOKItemId) {
					$arChangedIDs[] = $strOKItemId;
				}

				// End step
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
				$this->setProfileParam(['PROCESS_NEXT_POS' => 0]);
				Log::getInstance($this->strModuleId)->add(static::getMessage('PROCESS_PHASED_END_ALL'), $intProfileID);
			}
			else {
				#Profile::setParam($intProfileID, array('PROCESS_NEXT_POS' => $intProcessNextPos + $intProcessLimit));
				$this->setProfileParam(['PROCESS_NEXT_POS' => $intProcessNextPos + $intProcessLimit]);
				Log::getInstance($this->strModuleId)->add(static::getMessage('PROCESS_PHASED_END_STEP', array('#POSITION#' => ($intProcessNextPos + $intProcessLimit))), $intProfileID);
			}
		}
		// Delete elements that are not in the sample
		if (!$intProcessLimit && $arData['PROFILE']['PARAMS']['PROCESS_DELETE_OTHER'] == 'Y') {
			static::delItemsOther($arChangedIDs);
		}
		// Update catalogs data
		foreach ($arOKCatalogsUpdate as $arOKCatalogIds => $arCatalogFields) {
			self::addMarketCatalog($arCatalogFields, $arOKCatalogIds);
		}

		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * Add item to a group
	 */

	function addMarketItem($arItem, $product_id){
		$arResponse = false;

		//$arPostData = self::PreparePostData( $arData );
		$arPostData = $arItem;

		$obMarketItem = (object)array( "media" => array() );

		$obMarketItem->media[] = (object)array(
			"type" => "text",
			"text" => $arPostData["name"]
		);

		$obMarketItem->media[] = (object)array(
			"type" => "text",
			"text" => $arPostData["description_market"]
		);

		$arPhotoList = self::uploadPhotos( $arPostData["picture"] );

		if( !empty( $arPhotoList ) ){
			$obMarketItem->media[] = (object)array(
				"type" => "photo",
				"list" => $arPhotoList["attach"],
			);
		}

		$obMarketItem->media[] = (object)array(
			"type" => "product",
			"price" => $arPostData["price"],
			"currency" => $arPostData["currency"]
		);

		$arPostParams = array(
			"attachment" => json_encode( $obMarketItem ),
			"gid" => $this->strGroupId,
			"type" => "GROUP_PRODUCT",
		);

		if(is_array( $arItem["catalogs"] ) && !empty( $arItem["catalogs"] ) ){
			$arPostParams["catalog_ids"] = implode( ",", $arItem["catalogs"] );
		}

		if( $product_id ){
			$arPostParams["product_id"] = $product_id;
			$arResponse = OkSDK::makeRequest( "market.edit", $arPostParams );
		}
		else{
			$arResponse = OkSDK::makeRequest( "market.add", $arPostParams );
		}

		if( isset( $arResponse["error_code"] ) ){
			Log::getInstance($this->strModuleId)->add('Error item add/edit [' . $arResponse["error_code"] . ']: ' . $arResponse["error_msg"], $this->intProfileID);
		}

		return $arResponse;
	}

	protected function getCategoryRedef($arProfile, $arFields, $mValue, $strValueDef='') {
		$intProfileID = $arProfile['ID'];
		#$arCategoryRedefinitions = CategoryRedefinition::getForProfile( $intProfileID );
		$arCategoryRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$intProfileID]);
		if (intval($mValue) && $arCategoryRedefinitions[$mValue]) {
			$mValue = $arCategoryRedefinitions[$mValue];
		}
		elseif ($strValueDef) {
			$mValue = $strValueDef;
		}
		else {
			$mValue = false;
		}
		return $mValue;
	}

	function uploadPhotos( $arPhotos, $aid = false ){
		$arSavedPhotos = array();

		$arGetUploadUrlParams = array(
			"gid" => $this->strGroupId,
			"count" => count( $arPhotos ),
		);

		if( intval( $aid ) > 0 ){
			$arGetUploadUrlParams["aid"] = intval( $aid );
		}

		$responseUploadUrl = OkSDK::makeRequest( "photosV2.getUploadUrl", $arGetUploadUrlParams );

		if( !isset( $responseUploadUrl["error_code"] ) ){
			$arUploadedFiles = array();
			foreach( $arPhotos as $photoIndex => $photoUrl ){
				$strUrlPath = $_SERVER['DOCUMENT_ROOT'] . Helper::getPathFromUrl($photoUrl);
				$arUploadedFiles["pic".$photoIndex] = self::getCurlFilename( $strUrlPath );
			}

			$responseSavedPhotos = json_decode( self::_curlPost( $responseUploadUrl["upload_url"], $arUploadedFiles ) );

			if( isset( $responseSavedPhotos->photos ) ){
				$arSavedPhotos["fullinfo"] = $responseSavedPhotos->photos;

				$arSavedPhotos["attach"] = array();
				foreach( $responseSavedPhotos->photos as $index => $value ){
					$arSavedPhotos["attach"][] = (object) array( "id" => $value->token );
				}
			}
		}

		return $arSavedPhotos;
	}



	//TODO
	function deleteMarketItem( $marketItemId ){
		$arResponse = false;

		$arPostParams = array(
			"product_id" => $marketItemId
		);

		$arResponse = OkSDK::makeRequest( "market.delete", $arPostParams );

		if( isset( $arResponse["error_code"] ) ){
			$this->addToLog('(ID: '.$marketItemId.') : '.$arResponse["error_msg"]);
		}

		return $arResponse;
	}

	//TODO
	function deleteAllMarketItems(){
		$arResponse = false;

		$arMarketItems = self::getMarketItems();

		if( is_array( $arMarketItems ) && !empty( $arMarketItems ) ){
			foreach( $arMarketItems as $marketItemId ){
				$arResponse = self::deleteMarketItem( $marketItemId );
			}
		}

		return $arResponse;
	}

	function getMarketItems($assoc=true){
		$arMarketItems = array();
		$arMarketItemsData = array();
		$arGetMarketItems = self::getMarketItemsPart();
		if (count($arGetMarketItems["short_products"])) {
			$arMarketItemsData = $arGetMarketItems["short_products"];
		}
		while ($arGetMarketItems["has_more"] && $arGetMarketItems["anchor"]) {
			$arGetMarketItems = self::getMarketItemsPart($arGetMarketItems["anchor"]);
			if (count($arGetMarketItems["short_products"])) {
				$arMarketItemsData = array_merge($arMarketItemsData, $arGetMarketItems["short_products"]);
			}
		}
		foreach ($arMarketItemsData as $arMarketItemsDataItem) {
			if ($assoc) {
				$arMarketItems[$arMarketItemsDataItem["title"]] = $arMarketItemsDataItem["id"];
			}
			else {
				$arMarketItems[] = $arMarketItemsDataItem["id"];
			}
		}
		return $arMarketItems;
	}

	function getMarketItemsPart( $anchor = false ){
		$arResponse = false;
		$arPostParams = array(
			"gid" => $this->strGroupId,
			"tab" => "PRODUCTS",
			"count" => 10,
		);
		if( $anchor ){
			$arPostParams["anchor"] = $anchor;
		}
		$arResponse = OkSDK::makeRequest( "market.getProducts", $arPostParams );
		return $arResponse;
	}

	//TODO
	function getMarketItemsByCatalog( $catalogId ){
		$arMarketItems = array();
		$arMarketItemsData = array();

		$arGetMarketItems = self::getMarketItemsByCatalogPart($catalogId);

		if (count($arGetMarketItems["short_products"])) {
			$arMarketItemsData = $arGetMarketItems["short_products"];
		}
		while( $arGetMarketItems["has_more"] && $arGetMarketItems["anchor"] ){
			$arGetMarketItems = self::getMarketItemsByCatalogPart( $catalogId, $arGetMarketItems["anchor"] );

			if( count( $arGetMarketItems["short_products"] ) ){
				$arMarketItemsData = array_merge( $arMarketItemsData, $arGetMarketItems["short_products"] );
			}
		}

		foreach( $arMarketItemsData as $arMarketItemsDataItem ){
			$arMarketItems[] = $arMarketItemsDataItem["id"];
		}

		return $arMarketItems;
	}

	function getMarketItemsByCatalogPart( $strOKCatalogId, $anchor = false ){
		$arResponse = false;

		$arPostParams = array(
			"gid" => $this->strGroupId,
			"catalog_id" => $strOKCatalogId,
			"count" => 10,
		);

		if( $anchor ){
			$arPostParams["anchor"] = $anchor;
		}

		$arResponse = OkSDK::makeRequest( "market.getByCatalog", $arPostParams );

		return $arResponse;
	}

	function addMarketCatalog( $arData, $intOKCatalogId = false ){
		$arResponse = false;

		#$arProfile = Profile::getProfiles(self::$intProfileID);
		if ($this->arProfile['PARAMS']['PROCESS_CREATE_CATALOGS'] != 'Y' && !$intOKCatalogId) {
			return false;
		}

		$arPostParams = array(
			"gid" => $this->strGroupId,
			"name" => $arData["name"],
		);

		if ($arData["picture"]) {
			$arData["picture"] = !is_array($arData["picture"]) ? [ $arData["picture"] ] : $arData["picture"];
			$arPhotoList = self::uploadPhotos($arData["picture"]);
			$arCatalogPhoto = (array) $arPhotoList["attach"][0];
			$arPostParams['photo_id'] = $arCatalogPhoto["id"];
		}

		if( $intOKCatalogId ){
			$arPostParams["catalog_id"] = $intOKCatalogId;
			$arResponse = OkSDK::makeRequest( "market.editCatalog", $arPostParams );
		}
		else{
			$arResponse = OkSDK::makeRequest( "market.addCatalog", $arPostParams );
		}

		if( isset( $arResponse["error_code"] ) ){
			Log::getInstance($this->strModuleId)->add('Error category add/edit [' . $arResponse["error_code"] . ']: ' . $arResponse["error_msg"], $this->intProfileID);
		}

		return $arResponse;
	}

	//TODO
	function deleteMarketCatalog( $marketCatalogId, $bDeleteProducts = false ){
		$arResponse = false;

		$arPostParams = array(
			"gid" => $this->strGroupId,
			"catalog_id" => $marketCatalogId,
		);

		if( $bDeleteProducts ){
			$arPostParams["delete_products"] = true;
		}

		$arResponse = OkSDK::makeRequest( "market.deleteCatalog", $arPostParams );

		if( isset( $arResponse["error_code"] ) ){
			$this->addToLog('(ID: '.$marketItemId.') : '.$arResponse["error_msg"]);
		}

		return $arResponse;
	}

	//TODO
	function deleteAllMarketCatalogs(){
		$arResponse = false;

		$arMarketCatalogs = self::GetMarketCatalogsByGroup();

		if( is_array( $arMarketCatalogs ) && !empty( $arMarketCatalogs ) ){
			foreach( $arMarketCatalogs as $marketCatalogId ){
				$arResponse = self::DeleteMarketCatalog( $marketCatalogId );
			}
		}

		return $arResponse;
	}

	function getMarketCatalogsByGroup(){
		$arMarketCatalogs = array();

		$arGetMarketCatalogs = self::GetMarketCatalogsByGroupPart();

		$arMarketCatalogsData = $arGetMarketCatalogs["catalogs"];
		while( $arGetMarketCatalogs["has_more"] ){
			$arGetMarketCatalogs = self::GetMarketCatalogsByGroupPart( $arGetMarketCatalogs["anchor"] );

			if( count( $arGetMarketCatalogs["catalogs"] ) ){
				$arMarketCatalogsData = array_merge( $arMarketCatalogsData, $arGetMarketCatalogs["catalogs"] );
			}
		}

		foreach( $arMarketCatalogsData as $arMarketCatalogsDataItem ){
			$arMarketCatalogs[$arMarketCatalogsDataItem["name"]] = $arMarketCatalogsDataItem["id"];
		}

		return $arMarketCatalogs;
	}

	function getMarketCatalogsByGroupPart( $anchor = false ){
		$arResponse = false;

		$arPostParams = array(
			"gid" => $this->strGroupId,
			"count" => 10,
			"fields" => "ID,NAME",
		);

		if( $anchor ){
			$arPostParams["anchor"] = $anchor;
		}

		$arResponse = OkSDK::makeRequest( "market.getCatalogsByGroup", $arPostParams );

		return $arResponse;
	}

	//TODO
	function setMarketItemCatalogsList( $arData ){
		$arResponse = false;

		$arPostParams = array(
			"gid" => $this->strGroupId,
			"product_id" => $arData["ID"],
		);

		if( isset( $arData["CATALOGS"] ) && ( strlen( trim( $arData["CATALOGS"] ) ) > 0 ) ){
			$arPostParams["catalog_ids"] = $arData["CATALOGS"];
		}

		$arResponse = OkSDK::makeRequest( "market.updateCatalogsList", $arPostParams );

		return $arResponse;
	}


	// Get item ID by name
	protected function findItemID($strName, $arIDs) {
		$intItem = false;
		if (isset($arIDs[$strName])) {
			$intItem = $arIDs[$strName];
		}
		return $intItem;
	}

	// Delete elements that are not in the sample
	protected function delItemsOther($arChangedIDs) {
		$arOKAllIDs = self::getMarketItems(false);
		foreach ($arOKAllIDs as $item_id) {
			if (!in_array($item_id, $arChangedIDs)) {
				$arResult = self::deleteMarketItem( $item_id );
			}
		}
	}


	//------------------------------------------------------------------------------------------------------------------

    // Delete duplicates of elements
    protected function stepExport_delItemsDuplicates($arChangedIDs, $strOkOwnerId, $intProfileID) {
	    $arDelIDs = array();
        $arOKAllIDs = static::getGroupItemsIDs(true, true, $strOkOwnerId, $intProfileID);
        foreach ($arOKAllIDs as $arItemsIds) {
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
            $arRes = static::request('market.delete', array(
                'owner_id' => $strOkOwnerId,
                'item_id' => $item_id,
            ), $intProfileID);
        }
    }

	// Find all goods in the group
	protected function getGroupItems($assoc, $all_variants, $extended, $strOkOwnerId, $intProfileID) {
		$arItems = array();
		$intCountPerTime = 200;
		$arRes = static::request('market.search', array(
			'owner_id' => $strOkOwnerId,
			'count' => 1,
			'status' => 0,
		), $intProfileID);
		$count = $arRes['response']['count'];
		for ($i = 0; $i < $count; $i += $intCountPerTime) {
			$arRes = static::request('market.search', array(
				'owner_id' => $strOkOwnerId,
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
		$arRes = static::request('market.search', array(
			'owner_id' => $strOkOwnerId,
			'count' => 1,
			'status' => 2,
		), $intProfileID);
		$count = $arRes['response']['count'];
		for ($i = 0; $i < $count; $i += $intCountPerTime) {
			$arRes = static::request('market.search', array(
				'owner_id' => $strOkOwnerId,
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
	protected function getGroupItemsIDs($assoc, $all_variants, $strOkOwnerId, $intProfileID, $arAddParams=false) {
		$arIDs = array();
		$intCountPerTime = 200;
		// Request params
		$arReqParamsCount = array(
			'owner_id' => $strOkOwnerId,
			'count' => 1,
			'status' => 0,
		);
		$arReqParamsItems = array(
			'owner_id' => $strOkOwnerId,
			'count' => $intCountPerTime,
			'offset' => 0,
			'status' => 0,
		);
		if ($arAddParams) {
			$arReqParamsCount = array_merge($arAddParams, $arReqParamsCount);
			$arReqParamsItems = array_merge($arAddParams, $arReqParamsItems);
		}
		// Request for active items
		$arRes = static::request('market.search', $arReqParamsCount, $intProfileID);
		$count = $arRes['response']['count'];
		for ($i = 0; $i < $count; $i += $intCountPerTime) {
			$arReqParamsItems['offset'] = $i;
			$arRes = static::request('market.search', $arReqParamsItems, $intProfileID);
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
		$arRes = static::request('market.search', $arReqParamsCount, $intProfileID);
		$count = $arRes['response']['count'];
		for ($i = 0; $i < $count; $i += $intCountPerTime) {
			$arReqParamsItems['offset'] = $i;
			$arRes = static::request('market.search', $arReqParamsItems, $intProfileID);
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


    /**
	 * Ajax actions
	 */

	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		#$arProfile = Profile::getProfiles($arParams['PROFILE_ID']);
		$strOkGroupId = strval($this->arProfile['PARAMS']['GROUP_ID']);
		$strOkOwnerId = intval('-' . $strOkGroupId);
		switch ($strAction) {
			case 'items_clear_all_get_list':
				$arJsonResult['list'] = $this->okItemsClearAllGetIDs($this->arProfile, $strOkOwnerId);
				$arJsonResult['result'] = 'ok';
			break;
			case 'items_clear_loaded_get_list':
				$arJsonResult['list'] = $this->okItemsClearAllLoadedGetIDs($this->arProfile, $strOkOwnerId);
				$arJsonResult['result'] = 'ok';
			break;
			case 'items_clear_album_get_list':
				$arJsonResult['list'] = $this->okItemsClearAlbumGetIDs($this->arProfile, $strOkOwnerId, $arParams['POST']['id']);
				$arJsonResult['result'] = 'ok';
			break;
			case 'items_clear_delete':
				$step_limit = 50;
				$arIDs = $arParams['POST']['list'];
				$step = $arParams['POST']['step'];
				$arJsonResult['not_empty'] = $this->okItemsClearDeleteIDs($this->arProfile, $strOkOwnerId, $arIDs, $step_limit, $step);
				$arJsonResult['result'] = 'ok';
				break;
			case 'params_next_pos_reset':
				#$res = Profile::setParam($arParams['PROFILE_ID'], array('PROCESS_NEXT_POS' => 0));
				$res = $this->setProfileParam(['PROCESS_NEXT_POS' => 0]);
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

	protected function okItemsClearAllGetIDs($arProfile, $strOkOwnerId) {
		$arDelIDs = array();
		$intProfileID = $arProfile['ID'];

		$arOKAllIDs = static::getGroupItemsIDs(true, true, $strOkOwnerId, $intProfileID);
		if (!empty($arOKAllIDs)) {
			foreach ($arOKAllIDs as $arIDs) {
				foreach ($arIDs as $item_id) {
					$arDelIDs[] = $item_id;
				}
			}
		}
		return $arDelIDs;
	}

	protected function okItemsClearAllLoadedGetIDs($arProfile, $strOkOwnerId) {
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
		$arOKAllIDs = static::getGroupItemsIDs(true, true, $strOkOwnerId, $intProfileID);
		if (!empty($arOKAllIDs)) {
			foreach ($arOKAllIDs as $name => $arIDs) {
				if (in_array(trim($name), $arExpItems)) {
					foreach ($arIDs as $item_id) {
						$arDelIDs[] = $item_id;
					}
				}
			}
		}
		return $arDelIDs;
	}

	protected function okItemsClearAlbumGetIDs($arProfile, $strOkOwnerId, $intOkAlbumId) {
		$arDelIDs = array();
		if (!$intOkAlbumId) {
			return;
		}
		$intProfileID = $arProfile['ID'];
		$arOKAllIDs = static::getAlbumItemsIDs(true, true, $strOkOwnerId, $intProfileID, $intOkAlbumId);
		if (!empty($arOKAllIDs)) {
			foreach ($arOKAllIDs as $arIDs) {
				foreach ($arIDs as $item_id) {
					$arDelIDs[] = $item_id;
				}
			}
		}
		return $arDelIDs;
	}

	protected function okItemsClearDeleteIDs($arProfile, $strOkOwnerId, $arIDs, $limit=0, $step=0) {
		$result = true;
		$intProfileID = $arProfile['ID'];
		$i = 0;
		//Log::getInstance($this->strModuleId)->add('(okItemsClearDeleteIDs) $arDelIDs: '.print_r($arIDs, true), $intProfileID);
		foreach ($arIDs as $item_id) {
			if (!$limit || ($i >= $limit * $step && $i < $limit * ($step + 1))) {
				//Log::getInstance($this->strModuleId)->add('(okItemsClearDeleteIDs) $item_id: '.$item_id, $intProfileID);
				static::request('market.delete', array(
					'owner_id' => $strOkOwnerId,
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

	protected function getAlbumItemsIDs($assoc, $all_variants, $strOkOwnerId, $intProfileID, $intOkAlbumId) {
		$arFilter = array(
			'album_id' => $intOkAlbumId,
		);
		$arOKAllIDs = static::getGroupItemsIDs(true, true, $strOkOwnerId, $intProfileID, $arFilter);
		return $arOKAllIDs;
	}
}

?>