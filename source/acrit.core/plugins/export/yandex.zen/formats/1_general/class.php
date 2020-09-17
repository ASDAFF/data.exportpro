<?
/**
 * Acrit Core: Yandex market base plugin
 * @package acrit.core
 * @copyright 2018 Acrit
 * @documentation https://yandex.ru/support/partnermarket/export/yml.html
 */
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class YandexZenGeneral extends YandexZen {
	
	CONST DATE_UPDATED = '2019-08-07';

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
        return parent::getCode().'_GENERAL';
    }
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Are additional fields are supported?
	 */
	public function areAdditionalFieldsSupported(){
		return true;
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
	public function isCategoryStrict(){ // static ot not?
		return true;
	}

	/**
	 *	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList(){ // static ot not?
		return false;
	}

    /**
     *	Is it subclass?
     */
    public static function isSubclass(){
        return true;
    }

    /**
     *	Get categories list
     */
    public function getCategoriesList($intProfileID){
        #$arProfile = Profile::getProfiles($intProfileID);
				$arProfile = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
        $strFileName = $this->getCategoriesCacheFile();
        if(!is_file($strFileName) || !filesize($strFileName)) {
            $this->updateCategories($intProfileID);
        }
        if(is_file($strFileName) && filesize($strFileName)) {
            $strCategList = explode("\n", file_get_contents($strFileName));
            if (!Helper::isUtf()) {
                $strCategList = Helper::convertEncoding($strCategList, 'UTF-8', 'CP1251');
            }
            return $strCategList;
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
        $arResult = array();
        $arResult[] = new Field(array(
            'CODE' => 'TITLE',
            'DISPLAY_CODE' => 'title',
            'NAME' => static::getMessage('FIELD_TITLE_NAME'),
            'SORT' => 100,
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
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LINK',
            'DISPLAY_CODE' => 'link',
            'NAME' => static::getMessage('FIELD_LINK_NAME'),
            'SORT' => 900,
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
                'MAXLENGTH' => '2000',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'PDALINK',
            'DISPLAY_CODE' => 'pdalink',
            'NAME' => static::getMessage('FIELD_PDALINK_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_PDALINK_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_PDALINK',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '2000',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'AMPLINK',
            'DISPLAY_CODE' => 'amplink',
            'NAME' => static::getMessage('FIELD_AMPLINK_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_AMPLINK_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AMPLINK',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '2000',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'RATING',
            'DISPLAY_CODE' => 'media:rating',
            'NAME' => static::getMessage('FIELD_RATING_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_RATING_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_RATING',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'PUBDATE',
            'DISPLAY_CODE' => 'pubDate',
            'NAME' => static::getMessage('FIELD_PUBDATE_NAME'),
            'SORT' => 140,
            'DESCRIPTION' => static::getMessage('FIELD_PUBDATE_DESC'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_DATE',
                    'PARAMS' => array(
                        'DATEFORMAT' => 'Y',
                        'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
                        'DATEFORMAT_to' => 'Y-m-d H:i:s',
                        'DATEFORMAT_keep_wrong' => 'Y',
                    ),
                ),
            ),
        ));

        $arResult[] = new Field(array(
            'CODE' => 'AUTHOR',
            'DISPLAY_CODE' => 'author',
            'NAME' => static::getMessage('FIELD_AUTHOR_NAME'),
            'SORT' => 130,
            'DESCRIPTION' => static::getMessage('FIELD_AUTHOR_DESC'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AUTHOR',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'CATEGORY',
            'DISPLAY_CODE' => 'category',
            'NAME' => static::getMessage('FIELD_CATEGORY_NAME'),
            'SORT' => 140,
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
                    'PARAMS' => array(
                        'MAXLENGTH' => '300',
                        'HTMLSPECIALCHARS' => 'skip'),
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '300',
                'HTMLSPECIALCHARS' => 'escape',
            )
        ));
        $arResult[] = new Field(array(
            'CODE' => 'CONTENT',
            'DISPLAY_CODE' => 'content:encoded',
            'NAME' => static::getMessage('FIELD_CONTENT_NAME'),
            'SORT' => 540,
            'DESCRIPTION' => static::getMessage('FIELD_CONTENT_DESC'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'CDATA' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'DETAIL_TEXT',
                    'PARAMS' => array('HTMLSPECIALCHARS' => 'cut'),
                ),
            ),
            'DEFAULT_VALUE_OFFERS' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'DETAIL_TEXT',
                    'PARAMS' => array('HTMLSPECIALCHARS' => 'cut'),
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PARENT.DETAIL_TEXT',
                    'PARAMS' => array('HTMLSPECIALCHARS' => 'cut'),
                ),
            ),
            'PARAMS' => array('HTMLSPECIALCHARS' => 'cut'),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'IMAGE',
            'DISPLAY_CODE' => 'image',
            'NAME' => static::getMessage('FIELD_IMAGE_NAME'),
            'SORT' => 525,
            'DESCRIPTION' => static::getMessage('FIELD_IMAGE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'DETAIL_PICTURE',
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PREVIEW_PICTURE',
                ),
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
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'VIDEO_URL',
            'DISPLAY_CODE' => 'VideoURL',
            'NAME' => static::getMessage('FIELD_VIDEO_URL_NAME'),
            'SORT' => 330,
            'DESCRIPTION' => static::getMessage('FIELD_VIDEO_URL_DESC'),
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_VIDEOURL',
                ),
            ),
            'PARAMS' => array(
                'MULTIPLE' => 'multiple',
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
        if(!Helper::isEmpty($arFields['TITLE']))
            $arXmlTags['title'] = Xml::addTag($arFields['TITLE']);
        if(!Helper::isEmpty($arFields['LINK']))
            $arXmlTags['link'] = Xml::addTag($arFields['LINK']);
        if(!Helper::isEmpty($arFields['PDALINK']))
            $arXmlTags['pdalink'] = Xml::addTag($arFields['PDALINK']);
        if(!Helper::isEmpty($arFields['AMPLINK']))
            $arXmlTags['amplink'] = Xml::addTag($arFields['AMPLINK']);
        if(!Helper::isEmpty($arFields['PUBDATE']))
            $arXmlTags['pubDate'] = Xml::addTag($arFields['PUBDATE']);
        if(!Helper::isEmpty($arFields['RATING']))
            $arXmlTags['media:rating'] = $this->getMediaRating($intProfileID, $arFields);
        if(!Helper::isEmpty($arFields['AUTHOR']))
            $arXmlTags['author'] = Xml::addTag($arFields['AUTHOR']);
        $arXmlTags['category'] = $this->getXmlTag_Category($arProfile, $arElement, $arFields["CATEGORY"]);
        if(!Helper::isEmpty($arFields['DESCRIPTION']))
            $arXmlTags['description'] = Xml::addTag($arFields['DESCRIPTION']);
        if(!Helper::isEmpty($arFields['CONTENT']))
            $arXmlTags['content:encoded'] = Xml::addTag($arFields['CONTENT']);
        if(!Helper::isEmpty($arFields['CONTENT']))
            $arXmlTags['enclosure'] = $this->getXmlTag_Enclosure($intProfileID, $arFields);

        # Build XML
        $arXml = array(
            'item' => array(
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

    /**
     *	Get MediaRating
     */
    protected function getMediaRating($intProfileID, $arFields){
        $arResult = array(
            array(
                '@' => array(
                    'scheme' => "urn:simple"
                ),
                '#' => $arFields['RATING']
            ),
        );
        return $arResult;
    }

    /**
     *	Get XML tag: Enclosure
     */
    protected function getXmlTag_Enclosure($intProfileID, $arFields) {
			$arImages = $arFields['IMAGE'];
			if(!is_array($arImages)){
				$arImages = strlen($arImages) ? array($arImages) : array();
			}
			$arVideos = $arFields['VIDEO_URL'];
			if(!is_array($arVideos)){
				$arVideos = strlen($arVideos) ? array($arVideos) : array();
			}
			if(!empty($arImages) || !empty($arVideos)){
				$arXml = array(
					#array('@'=>array('type'=>'image/*'), '#' => 'Example'),
				);
				#
				$arImagesType = array('jpg'=>'jpeg', 'jpeg'=>'jpeg', 'png'=>'png', 'gif'=>'gif', 'bmp'=>'bmp');
				foreach($arImages as $strImage) {
					$strExtension = ToLower(pathinfo($strImage, PATHINFO_EXTENSION));
					$strType = 'image/'.$arImagesType[$strExtension];
					$arXml[] = array(
						'@' => array(
							'url' => $strImage,
							'type' => $strType,
						),
					);
				}
				#
				foreach($arVideos as $strVideo) {
					$strType = 'video/x-ms-asf'; // ToDo!
					$arXml[] = array(
						'@' => array(
							'url' => $strImage,
							'type' => $strType,
						),
					);
				}
				#
				return $arXml;
			}
			return null;
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
                    $mValue = $arCategoryRedefinitions[$intSectionID];
                    break;
                }
            }
            unset($arCategoryRedefinitions, $arSectionsID, $intSectionID, $intCategoryID);
        }
        return array('#' => $mValue);
    }

}

?>