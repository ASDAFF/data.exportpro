<?
/**
 * Acrit Core: Plugin for bn.ru
 * @documentation https://yandex.ru/support/partnermarket/export/yml.html
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\EventManager,
    \Acrit\Core\Helper,
    \Acrit\Core\Export\Exporter,
    \Acrit\Core\Export\Field\Field,
		\Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class BullNedGeneral extends BullNed {

    CONST DATE_UPDATED = '2018-12-10';

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
     *	Are additional fields are supported?
     */
    public function areAdditionalFieldsSupported(){
        return false;
    }

    /* END OF BASE STATIC METHODS */

    /**
     *	Get adailable fields for current plugin
     */
    public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
        $arResult = array();

        $arResult[] = new Field(array(
            'CODE' => 'ID',
            'DISPLAY_CODE' => 'id',
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
            'CODE' => 'APARTAMENT_TYPE',
            'DISPLAY_CODE' => 'apartament_type',
            'NAME' => static::getMessage('FIELD_APARTAMENT_TYPE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_APARTAMENT_TYPE_DESC'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_APARTAMENT_TYPE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'OPERATION_ACTION',
            'DISPLAY_CODE' => 'operation_action',
            'NAME' => static::getMessage('FIELD_OPERATION_ACTION_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_OPERATION_ACTION_DESC'),
            'REQUIRED' => true,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_OPERATION_ACTION',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'URL',
            'DISPLAY_CODE' => 'url',
            'NAME' => static::getMessage('FIELD_URL_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_URL_DESC'),
            'REQUIRED' => false,
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
        #
        $arResult[] = new Field(array(
            'CODE' => 'LOCATION_COUNTRY',
            'DISPLAY_CODE' => 'country',
            'NAME' => static::getMessage('FIELD_LOCATION_COUNTRY_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_COUNTRY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOCATION_COUNTRY',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOCATION_REGION',
            'DISPLAY_CODE' => 'region',
            'NAME' => static::getMessage('FIELD_LOCATION_REGION_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_REGION_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOCATION_REGION',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOCATION_AREA',
            'DISPLAY_CODE' => 'area',
            'NAME' => static::getMessage('FIELD_LOCATION_AREA_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_AREA_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOCATION_AREA',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOCATION_CITY',
            'DISPLAY_CODE' => 'city',
            'NAME' => static::getMessage('FIELD_LOCATION_CITY_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_CITY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOCATION_CITY',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOCATION_CTAR',
            'DISPLAY_CODE' => 'ctar',
            'NAME' => static::getMessage('FIELD_LOCATION_CTAR_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_CTAR_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOCATION_CTAR',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOCATION_DISTRICT',
            'DISPLAY_CODE' => 'district',
            'NAME' => static::getMessage('FIELD_LOCATION_DISTRICT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_DISTRICT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOCATION_DISTRICT',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOCATION_PLACE',
            'DISPLAY_CODE' => 'place',
            'NAME' => static::getMessage('FIELD_LOCATION_PLACE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_PLACE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOCATION_PLACE',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOCATION_STREET',
            'DISPLAY_CODE' => 'street',
            'NAME' => static::getMessage('FIELD_LOCATION_STREET_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_STREET_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOCATION_STREET',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOCATION_HOUSE',
            'DISPLAY_CODE' => 'house',
            'NAME' => static::getMessage('FIELD_LOCATION_HOUSE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_HOUSE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOCATION_HOUSE',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        #
        $arResult[] = new Field(array(
            'CODE' => 'METRO_NAME',
            'DISPLAY_CODE' => 'metro_name',
            'NAME' => static::getMessage('FIELD_LOCATION_METRO_NAME_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOCATION_METRO_NAME_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_METRO_NAME',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'TIME_FOOT',
            'DISPLAY_CODE' => 'time_foot',
            'NAME' => static::getMessage('FIELD_TIME_FOOT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_TIME_FOOT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_TIME_FOOT',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'TIME_TRANSPORT',
            'DISPLAY_CODE' => 'time_transport',
            'NAME' => static::getMessage('FIELD_TIME_TRANSPORT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_TIME_TRANSPORT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_TIME_TRANSPORT',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'TIME_STOP',
            'DISPLAY_CODE' => 'time_stop',
            'NAME' => static::getMessage('FIELD_TIME_STOP_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_TIME_STOP_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_TIME_STOP',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        #
        $arResult[] = new Field(array(
            'CODE' => 'RAILWAY_NAME',
            'DISPLAY_CODE' => 'railway_name',
            'NAME' => static::getMessage('FIELD_RAILWAY_NAME_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_RAILWAY_NAME_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_RAILWAY_NAME',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'RAILWAY_TIMEFOOT',
            'DISPLAY_CODE' => 'railway_timefoot',
            'NAME' => static::getMessage('FIELD_RAILWAY_TIME_FOOT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_RAILWAY_TIME_FOOT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_RAILWAY_TIME_FOOT',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'RAILWAY_TIMETRANSPORT',
            'DISPLAY_CODE' => 'railway_timetransport',
            'NAME' => static::getMessage('FIELD_RAILWAY_TIME_TRANSPORT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_RAILWAY_TIME_TRANSPORT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_RAILWAY_TIME_TRANSPORT',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        #
        $arResult[] = new Field(array(
            'CODE' => 'DISTANCE',
            'DISPLAY_CODE' => 'distance',
            'NAME' => static::getMessage('FIELD_DISTANCE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_DISTANCE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_DISTANCE',
                ),
            ),
            'PARAMS' => array(
                'MAXLENGTH' => '200',
                'HTMLSPECIALCHARS' => 'escape',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'DATE_BEGIN',
            'DISPLAY_CODE' => 'DateBegin',
            'NAME' => static::getMessage('FIELD_DATE_BEGIN_NAME'),
            'SORT' => 110,
            'DESCRIPTION' => static::getMessage('FIELD_DATE_BEGIN_DESC'),
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'ACTIVE_FROM',
                    'PARAMS' => array(
                        'DATEFORMAT' => 'Y',
                        'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
                        'DATEFORMAT_to' => 'Y-m-dTH:i:s+04:00',
                    ),
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'DATE_UPDATE',
            'DISPLAY_CODE' => 'DateUpdate',
            'NAME' => static::getMessage('FIELD_DATE_UPDATE_NAME'),
            'SORT' => 110,
            'DESCRIPTION' => static::getMessage('FIELD_DATE_UPDATE_DESC'),
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'ACTIVE_FROM',
                    'PARAMS' => array(
                        'DATEFORMAT' => 'Y',
                        'DATEFORMAT_from' => \CDatabase::DateFormatToPHP(FORMAT_DATETIME),
                        'DATEFORMAT_to' => 'Y-m-dTH:i:s+04:00',
                    ),
                ),
            ),
        ));
        # информация о сделке
        $arResult[] = new Field(array(
            'CODE' => 'PRICE_VALUE',
            'DISPLAY_CODE' => 'price_value',
            'NAME' => static::getMessage('FIELD_PRICE_VALUE_NAME'),
            'SORT' => 1000,
            'DESCRIPTION' => static::getMessage('FIELD_PRICE_VALUE_DESC'),
            'REQUIRED' => false,
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
            'CODE' => 'PERIOD',
            'DISPLAY_CODE' => 'period',
            'NAME' => static::getMessage('FIELD_PERIOD_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_PERIOD_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_PERIOD',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'AREA_UNIT',
            'DISPLAY_CODE' => 'area_unit',
            'NAME' => static::getMessage('FIELD_AREA_UNIT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_AREA_UNIT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AREA_UNIT',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'ADDITIONAL_TERMS',
            'DISPLAY_CODE' => 'additional_terms',
            'NAME' => static::getMessage('FIELD_ADDITIONAL_TERMS_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_ADDITIONAL_TERMS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_ADDITIONAL_TERMS',
                ),
            ),
        ));
        # информация о продавце
        $arResult[] = new Field(array(
            'CODE' => 'AGENT_NAME',
            'DISPLAY_CODE' => 'agent_name',
            'NAME' => static::getMessage('FIELD_AGENT_NAME_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_AGENT_NAME_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AGENT_NAME',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'AGENT_PHONE',
            'DISPLAY_CODE' => 'agent_phone',
            'NAME' => static::getMessage('FIELD_AGENT_PHONE_NAME'),
            'SORT' => 525,
            'DESCRIPTION' => static::getMessage('FIELD_AGENT_PHONE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AGENT_PHONE1',
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AGENT_PHONE2',
                ),
            ),
            'PARAMS' => array(
                'MULTIPLE' => 'multiple',
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'AGENT_CATEGORY',
            'DISPLAY_CODE' => 'agent_category',
            'NAME' => static::getMessage('FIELD_AGENT_CATEGORY_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_AGENT_CATEGORY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AGENT_CATEGORY',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'ORGANIZATION',
            'DISPLAY_CODE' => 'organization',
            'NAME' => static::getMessage('FIELD_ORGANIZATION_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_ORGANIZATION_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_ORGANIZATION',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'AGENT_URL',
            'DISPLAY_CODE' => 'agent_url',
            'NAME' => static::getMessage('FIELD_AGENT_URL_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_AGENT_URL_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AGENT_URL',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'AGENT_EMAIL',
            'DISPLAY_CODE' => 'agent_email',
            'NAME' => static::getMessage('FIELD_AGENT_EMAIL_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_AGENT_EMAIL_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AGENT_EMAIL',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'AGENT_SCYPE',
            'DISPLAY_CODE' => 'agent_scype',
            'NAME' => static::getMessage('FIELD_AGENT_SCYPE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_AGENT_SCYPE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_AGENT_SCYPE',
                ),
            ),
        ));
        # графическая информация. фото, видео
        $arResult[] = new Field(array(
            'CODE' => 'VIDEO',
            'DISPLAY_CODE' => 'Video',
            'NAME' => static::getMessage('FIELD_VIDEO_URL_NAME'),
            'SORT' => 330,
            'DESCRIPTION' => static::getMessage('FIELD_VIDEO_URL_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_VIDEO',
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_VIDEO1',
                ),
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_VIDEO2',
                ),
            ),
            'PARAMS' => array(
                'MULTIPLE' => 'multiple',
            ),
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
        # описание
        $arResult[] = new Field(array(
            'CODE' => 'SHORT_DESCRIPTION',
            'DISPLAY_CODE' => 'short_description',
            'NAME' => static::getMessage('FIELD_SHORT_DESCRIPTION_NAME'),
            'SORT' => 540,
            'DESCRIPTION' => static::getMessage('FIELD_SHORT_DESCRIPTION_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'CDATA' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PREVIEW_TEXT',
                    'PARAMS' => array(
                        'MAXLENGTH' => '300',
                        'HTMLSPECIALCHARS' => 'skip'),
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'FULL_DESCRIPTION',
            'DISPLAY_CODE' => 'full_description',
            'NAME' => static::getMessage('FIELD_FULL_DESCRIPTION_NAME'),
            'SORT' => 540,
            'DESCRIPTION' => static::getMessage('FIELD_FULL_DESCRIPTION_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'CDATA' => true,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'DETAIL_TEXT',
                    'PARAMS' => array('HTMLSPECIALCHARS' => 'cut'),
                ),
            ),
        ));
        # информация о здании
        $arResult[] = new Field(array(
            'CODE' => 'BUILDING_NAME',
            'DISPLAY_CODE' => 'building_name',
            'NAME' => static::getMessage('FIELD_BUILDING_NAME_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_BUILDING_NAME_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BUILDING_NAME',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'BUILDING_YEAR',
            'DISPLAY_CODE' => 'building_year',
            'NAME' => static::getMessage('FIELD_BUILDING_YEAR_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_BUILDING_YEAR_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BUILDING_YEAR',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'BUILDING_QUARTER',
            'DISPLAY_CODE' => 'building_quarter',
            'NAME' => static::getMessage('FIELD_BUILDING_QUARTER_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_BUILDING_QUARTER_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BUILDING_QUARTER',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'BUILDING_STATUS',
            'DISPLAY_CODE' => 'building_status',
            'NAME' => static::getMessage('FIELD_BUILDING_STATUS_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_BUILDING_STATUS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BUILDING_STATUS',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'BUILDING_TYPE',
            'DISPLAY_CODE' => 'building_type',
            'NAME' => static::getMessage('FIELD_BUILDING_TYPE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_BUILDING_TYPE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BUILDING_TYPE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'BUILDING_SERIES',
            'DISPLAY_CODE' => 'building_series',
            'NAME' => static::getMessage('FIELD_BUILDING_SERIES_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_BUILDING_SERIES_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BUILDING_SERIES',
                ),
            ),
        ));
        #Информация о жилом помещении
        $arResult[] = new Field(array(
            'CODE' => 'TOTAL_VALUE',
            'DISPLAY_CODE' => 'total_value',
            'NAME' => static::getMessage('FIELD_TOTAL_VALUE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_TOTAL_VALUE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_TOTAL_VALUE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'TOTAL_UNIT',
            'DISPLAY_CODE' => 'total_unit',
            'NAME' => static::getMessage('FIELD_TOTAL_UNIT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_TOTAL_UNIT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_TOTAL_UNIT',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LIVING_VALUE',
            'DISPLAY_CODE' => 'living_value',
            'NAME' => static::getMessage('FIELD_LIVING_VALUE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LIVING_VALUE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LIVING_VALUE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LIVING_ROOMS',
            'DISPLAY_CODE' => 'living_rooms',
            'NAME' => static::getMessage('FIELD_LIVING_ROOMS_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LIVING_ROOMS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LIVING_ROOMS',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LIVING_UNIT',
            'DISPLAY_CODE' => 'living_unit',
            'NAME' => static::getMessage('FIELD_LIVING_UNIT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LIVING_UNIT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LIVING_UNIT',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'KITCHEN_VALUE',
            'DISPLAY_CODE' => 'kitchen_value',
            'NAME' => static::getMessage('FIELD_KITCHEN_VALUE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_KITCHEN_VALUE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_KITCHEN_VALUE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'KITCHEN_UNIT',
            'DISPLAY_CODE' => 'kitchen_unit',
            'NAME' => static::getMessage('FIELD_KITCHEN_UNIT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_KITCHEN_UNIT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_KITCHEN_UNIT',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'BALCON_TYPE',
            'DISPLAY_CODE' => 'balcon_type',
            'NAME' => static::getMessage('FIELD_BALCON_TYPE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_BALCON_TYPE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BALCON_TYPE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'NEW_BUILDING',
            'DISPLAY_CODE' => 'new_building',
            'NAME' => static::getMessage('FIELD_NEW_BUILDING_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_NEW_BUILDING_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_NEW_BUILDING',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'IS_ELITE',
            'DISPLAY_CODE' => 'is_elite',
            'NAME' => static::getMessage('FIELD_IS_ELITE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_IS_ELITE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_ISELITE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'ROOMS_TOTAL',
            'DISPLAY_CODE' => 'rooms_total',
            'NAME' => static::getMessage('FIELD_ROOMS_TOTAL_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_ROOMS_TOTAL_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_ROOMS_TOTAL',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'ROOMS_OFFER',
            'DISPLAY_CODE' => 'rooms_offer',
            'NAME' => static::getMessage('FIELD_ROOMS_OFFER_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_ROOMS_OFFER_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_ROOMS_OFFER',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'HOLDERS',
            'DISPLAY_CODE' => 'holders',
            'NAME' => static::getMessage('FIELD_HOLDERS_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_HOLDERS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_HOLDERS',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'NEIGHBOURHOODS',
            'DISPLAY_CODE' => 'neighbourhoods',
            'NAME' => static::getMessage('FIELD_NEIGHBOURHOODS_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_NEIGHBOURHOODS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_NEIGHBOURHOODS',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'HAVE_PHONE',
            'DISPLAY_CODE' => 'have_phone',
            'NAME' => static::getMessage('FIELD_HAVE_PHONE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_HAVE_PHONE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_HAVE_PHONE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'HAVE_INTERNET',
            'DISPLAY_CODE' => 'have_internet',
            'NAME' => static::getMessage('FIELD_HAVE_INTERNET_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_HAVE_INTERNET_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_HAVE_INTERNET',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'QUARTER_FLOOR',
            'DISPLAY_CODE' => 'quarter_floor',
            'NAME' => static::getMessage('FIELD_QUARTER_FLOOR_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_QUARTER_FLOOR_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_QUARTER_FLOOR',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'FLOOR_RANGE',
            'DISPLAY_CODE' => 'floor_range',
            'NAME' => static::getMessage('FIELD_FLOOR_RANGE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_FLOOR_RANGE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_FLOOR_RANGE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'ALL_FLOORS',
            'DISPLAY_CODE' => 'all_floors',
            'NAME' => static::getMessage('FIELD_ALL_FLOORS_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_ALL_FLOORS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_ALL_FLOORS',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'FURNITURE',
            'DISPLAY_CODE' => 'furniture',
            'NAME' => static::getMessage('FIELD_FURNITURE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_FURNITURE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_FURNITURE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'REFRIGERATOR',
            'DISPLAY_CODE' => 'refrigerator',
            'NAME' => static::getMessage('FIELD_REFRIGERATOR_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_REFRIGERATOR_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_REFRIGERATOR',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'BATHROOM',
            'DISPLAY_CODE' => 'bathroom',
            'NAME' => static::getMessage('FIELD_BATHROOM_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_BATHROOM_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_BATHROOM',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'WASHING_MACHINE',
            'DISPLAY_CODE' => 'washing_machine',
            'NAME' => static::getMessage('FIELD_WASHING_MACHINE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_WASHING_MACHINE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_WASHING_MACHINE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'QUALITY',
            'DISPLAY_CODE' => 'quality',
            'NAME' => static::getMessage('FIELD_QUALITY_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_QUALITY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_QUALITY',
                ),
            ),
        ));
        #Информация о загородном объекте
        $arResult[] = new Field(array(
            'CODE' => 'LOT_VALUE',
            'DISPLAY_CODE' => 'lot_value',
            'NAME' => static::getMessage('FIELD_LOT_VALUE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOT_VALUE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOT_VALUE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOT_UNIT',
            'DISPLAY_CODE' => 'lot_unit',
            'NAME' => static::getMessage('FIELD_LOT_UNIT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOT_UNIT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOT_UNIT',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'LOT_STATUS',
            'DISPLAY_CODE' => 'lot_status',
            'NAME' => static::getMessage('FIELD_LOT_STATUS_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_LOT_STATUS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_LOT_STATUS',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COUNTRYSIDE_TYPE',
            'DISPLAY_CODE' => 'countryside_type',
            'NAME' => static::getMessage('FIELD_COUNTRYSIDE_TYPE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COUNTRYSIDE_TYPE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COUNTRYSIDE_TYPE',
                ),
            ),
        ));
        #Информация о коммерческом объекте
        $arResult[] = new Field(array(
            'CODE' => 'CEILING_HEIGHT',
            'DISPLAY_CODE' => 'ceiling_height',
            'NAME' => static::getMessage('FIELD_CEILING_HEIGHT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_CEILING_HEIGHT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_CEILING_HEIGHT',
                ),
            ),
        ));
        #Информация о коммерческом объекте
        $arResult[] = new Field(array(
            'CODE' => 'CEILING_HEIGHT',
            'DISPLAY_CODE' => 'ceiling_height',
            'NAME' => static::getMessage('FIELD_CEILING_HEIGHT_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_CEILING_HEIGHT_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_CEILING_HEIGHT',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_ENTRANCE',
            'DISPLAY_CODE' => 'com_entrance',
            'NAME' => static::getMessage('FIELD_COM_ENTRANCE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_ENTRANCE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_ENTRANCE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_ENTRY',
            'DISPLAY_CODE' => 'com_entry',
            'NAME' => static::getMessage('FIELD_COM_ENTRY_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_ENTRY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_ENTRY',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_PARKING',
            'DISPLAY_CODE' => 'com_parking',
            'NAME' => static::getMessage('FIELD_COM_PARKING_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_PARKING_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_PARKING',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_PROTECTION',
            'DISPLAY_CODE' => 'com_protection',
            'NAME' => static::getMessage('FIELD_COM_PROTECTION_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_PROTECTION_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_PROTECTION',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_HEATING',
            'DISPLAY_CODE' => 'com_heating',
            'NAME' => static::getMessage('FIELD_COM_HEATING_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_HEATING_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_HEATING',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_WATER',
            'DISPLAY_CODE' => 'com_water',
            'NAME' => static::getMessage('FIELD_COM_WATER_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_WATER_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_WATER',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_GAS',
            'DISPLAY_CODE' => 'com_gas',
            'NAME' => static::getMessage('FIELD_COM_GAS_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_GAS_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_GAS',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_SEWERAGE',
            'DISPLAY_CODE' => 'com_sewerage',
            'NAME' => static::getMessage('FIELD_COM_SEWERAGE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_SEWERAGE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_SEWERAGE',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_ELECTRICITY',
            'DISPLAY_CODE' => 'com_electricity',
            'NAME' => static::getMessage('FIELD_COM_ELECTRICITY_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_ELECTRICITY_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_ELECTRICITY',
                ),
            ),
        ));
        $arResult[] = new Field(array(
            'CODE' => 'COM_KV_TYPE',
            'DISPLAY_CODE' => 'com_kv_type',
            'NAME' => static::getMessage('FIELD_COM_KV_TYPE_NAME'),
            'SORT' => 900,
            'DESCRIPTION' => static::getMessage('FIELD_COM_KV_TYPE_DESC'),
            'REQUIRED' => false,
            'MULTIPLE' => false,
            'DEFAULT_VALUE' => array(
                array(
                    'TYPE' => 'FIELD',
                    'VALUE' => 'PROPERTY_COM_KV_TYPE',
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
        //$intProfileID = $arProfile['ID'];
        //$intElementID = $arElement['ID'];
	
				# Prepare data
				$bOffer = $arElement['IS_OFFER'];
				if($bOffer){
					$arMainIBlockData = $arProfile['IBLOCKS'][$arElement['PRODUCT_IBLOCK_ID']];
					$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement['PARENT'], $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
				} else {
					$arMainIBlockData = $arProfile['IBLOCKS'][$intIBlockID];
					$arElementSections = Exporter::getInstance($this->strModuleId)->getElementSections($arElement, $arMainIBlockData['SECTIONS_ID'], $arMainIBlockData['SECTIONS_MODE']);
				}

        # Build XML
        $arXmlTags = array();
        if(!Helper::isEmpty($arFields['ID']))
            $arXmlTags['id'] = Xml::addTag($arFields['ID']);
        if(!Helper::isEmpty($arFields['APARTAMENT_TYPE']))
            $arXmlTags['type'] = Xml::addTag($arFields['APARTAMENT_TYPE']);
        if(!Helper::isEmpty($arFields['OPERATION_ACTION']))
            $arXmlTags['action'] = Xml::addTag($arFields['OPERATION_ACTION']);
        if(!Helper::isEmpty($arFields['URL']))
            $arXmlTags['url'] = Xml::addTag($arFields['URL']);
        if(!Helper::isEmpty($arFields['LOCATION_STREET']))
            $arXmlTags['location'] = static::getXmlTag_Location($arFields);
        if(!Helper::isEmpty($arFields['DATE_BEGIN']))
            $arXmlTags['date'] = static::getXmlTag_Date($arFields);
        if(!Helper::isEmpty($arFields['PRICE_VALUE']))
            $arXmlTags['price'] = static::getXmlTag_Price($arFields);
        if(!Helper::isEmpty($arFields['ADDITIONAL_TERMS']))
            $arXmlTags['additional-terms'] = Xml::addTag($arFields['ADDITIONAL_TERMS']);
        if(!Helper::isEmpty($arFields['AGENT_NAME']))
            $arXmlTags['agent'] = static::getXmlTag_Agent($arFields);
        if(!Helper::isEmpty($arFields['IMAGE']) || !Helper::isEmpty($arFields['VIDEO']))
            $arXmlTags['files'] = static::getXmlTag_Files($arFields);
        if(!Helper::isEmpty($arFields['SHORT_DESCRIPTION']) || !Helper::isEmpty($arFields['FULL_DESCRIPTION']))
            $arXmlTags['description'] = static::getXmlTag_Description($arFields);
        if(!Helper::isEmpty($arFields['BUILDING_NAME']))
            $arXmlTags['building'] = static::getXmlTag_Building($arFields);
        if(!Helper::isEmpty($arFields['TOTAL_VALUE']))
            $arXmlTags['total'] = static::getXmlTag_Total($arFields);
        if(!Helper::isEmpty($arFields['LIVING_VALUE']))
            $arXmlTags['living'] = static::getXmlTag_Living($arFields);
        if(!Helper::isEmpty($arFields['KITCHEN_VALUE']))
            $arXmlTags['kitchen'] = static::getXmlTag_Kitchen($arFields);
        if(!Helper::isEmpty($arFields['BALCON_TYPE']))
            $arXmlTags['balcony'] = Xml::addTag($arFields['BALCON_TYPE']);
        if(!Helper::isEmpty($arFields['NEW_BUILDING']))
            $arXmlTags['new-building'] = Xml::addTag($arFields['NEW_BUILDING']);
        if(!Helper::isEmpty($arFields['IS_ELITE']))
            $arXmlTags['is-elite'] = Xml::addTag($arFields['IS_ELITE']);
        if(!Helper::isEmpty($arFields['ROOMS_TOTAL']))
            $arXmlTags['rooms-total'] = Xml::addTag($arFields['ROOMS_TOTAL']);
        if(!Helper::isEmpty($arFields['ROOMS_OFFER']))
            $arXmlTags['rooms-offer'] = Xml::addTag($arFields['ROOMS_OFFER']);
        if(!Helper::isEmpty($arFields['HOLDERS']))
            $arXmlTags['holders'] = Xml::addTag($arFields['HOLDERS']);
        if(!Helper::isEmpty($arFields['NEIGHBOURHOODS']))
            $arXmlTags['neighbourhoods'] = Xml::addTag($arFields['NEIGHBOURHOODS']);
        if(!Helper::isEmpty($arFields['HAVE_PHONE']))
            $arXmlTags['phone'] = Xml::addTag($arFields['HAVE_PHONE']);
        if(!Helper::isEmpty($arFields['HAVE_INTERNET']))
            $arXmlTags['internet'] = Xml::addTag($arFields['HAVE_INTERNET']);
        if(!Helper::isEmpty($arFields['QUARTER_FLOOR']))
            $arXmlTags['floor'] = Xml::addTag($arFields['QUARTER_FLOOR']);
        if(!Helper::isEmpty($arFields['FLOOR_RANGE']))
            $arXmlTags['floor-range'] = Xml::addTag($arFields['FLOOR_RANGE']);
        if(!Helper::isEmpty($arFields['ALL_FLOORS']))
            $arXmlTags['floors'] = Xml::addTag($arFields['ALL_FLOORS']);
        if(!Helper::isEmpty($arFields['FURNITURE']))
            $arXmlTags['furniture'] = Xml::addTag($arFields['FURNITURE']);
        if(!Helper::isEmpty($arFields['REFRIGERATOR']))
            $arXmlTags['refrigerator'] = Xml::addTag($arFields['REFRIGERATOR']);
        if(!Helper::isEmpty($arFields['BATHROOM']))
            $arXmlTags['bathroom'] = Xml::addTag($arFields['BATHROOM']);
        if(!Helper::isEmpty($arFields['WASHING_MACHINE']))
            $arXmlTags['washing-machine'] = Xml::addTag($arFields['WASHING_MACHINE']);
        if(!Helper::isEmpty($arFields['QUALITY']))
            $arXmlTags['quality'] = Xml::addTag($arFields['QUALITY']);
        if(!Helper::isEmpty($arFields['LOT_VALUE']))
            $arXmlTags['lot'] = static::getXmlTag_Lot($arFields);
        if(!Helper::isEmpty($arFields['LOT_STATUS']))
            $arXmlTags['lot-status'] = Xml::addTag($arFields['LOT_STATUS']);
        if(!Helper::isEmpty($arFields['COUNTRYSIDE_TYPE']))
            $arXmlTags['countryside-type'] = Xml::addTag($arFields['COUNTRYSIDE_TYPE']);
        if(!Helper::isEmpty($arFields['CEILING_HEIGHT']))
            $arXmlTags['ceiling-height'] = Xml::addTag($arFields['CEILING_HEIGHT']);
        if(!Helper::isEmpty($arFields['COM_ENTRANCE']))
            $arXmlTags['entrance'] = Xml::addTag($arFields['COM_ENTRANCE']);
        if(!Helper::isEmpty($arFields['COM_ENTRY']))
            $arXmlTags['entry'] = Xml::addTag($arFields['COM_ENTRY']);
        if(!Helper::isEmpty($arFields['COM_PARKING']))
            $arXmlTags['parking'] = Xml::addTag($arFields['COM_PARKING']);
        if(!Helper::isEmpty($arFields['COM_PROTECTION']))
            $arXmlTags['protection'] = Xml::addTag($arFields['COM_PROTECTION']);
        if(!Helper::isEmpty($arFields['COM_HEATING']))
            $arXmlTags['heating'] = Xml::addTag($arFields['COM_HEATING']);
        if(!Helper::isEmpty($arFields['COM_WATER']))
            $arXmlTags['water'] = Xml::addTag($arFields['COM_WATER']);
        if(!Helper::isEmpty($arFields['COM_GAS']))
            $arXmlTags['gas'] = Xml::addTag($arFields['COM_GAS']);
        if(!Helper::isEmpty($arFields['COM_SEWERAGE']))
            $arXmlTags['sewerage'] = Xml::addTag($arFields['COM_SEWERAGE']);
        if(!Helper::isEmpty($arFields['COM_ELECTRICITY']))
            $arXmlTags['electricity'] = Xml::addTag($arFields['COM_ELECTRICITY']);
        if(!Helper::isEmpty($arFields['COM_KV_TYPE']))
            $arXmlTags['electricity'] = Xml::addTag($arFields['COM_KV_TYPE']);

        # Build XML
        $arXml = array(
            'bn-object' => array(
                '#' => $arXmlTags,
            ),
        );

        # Build result
        $arResult = array(
            'TYPE' => 'XML',
            'DATA' => Xml::arrayToXml($arXml),
            'CURRENCY' => $arFields['CURRENCY_ID'],
            'SECTION_ID' => reset($arElementSections),
            'ADDITIONAL_SECTIONS_ID' => array_slice($arElementSections, 1),
            'DATA_MORE' => array(),
        );

        # Event handler OnBullNedXml
        foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnBullNedXml') as $arHandler) {
            ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
        }

        # after..
        unset($intProfileID, $intElementID, $arXmlTags, $arXml);
        return $arResult;
    }

    /**
     *	Get XML tag: location
     */
    protected function getXmlTag_Location($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['LOCATION_COUNTRY']))
            $arResult['country'] = Xml::addTag($arFields['LOCATION_COUNTRY']);
        if(!Helper::isEmpty($arFields['LOCATION_REGION']))
            $arResult['region'] = Xml::addTag($arFields['LOCATION_REGION']);
        if(!Helper::isEmpty($arFields['LOCATION_AREA']))
            $arResult['area'] = Xml::addTag($arFields['LOCATION_AREA']);
        if(!Helper::isEmpty($arFields['LOCATION_CITY']))
            $arResult['city'] = Xml::addTag($arFields['LOCATION_CITY']);
        if(!Helper::isEmpty($arFields['LOCATION_CTAR']))
            $arResult['ctar'] = Xml::addTag($arFields['LOCATION_CTAR']);
        if(!Helper::isEmpty($arFields['LOCATION_DISTRICT']))
            $arResult['district'] = Xml::addTag($arFields['LOCATION_DISTRICT']);
        if(!Helper::isEmpty($arFields['LOCATION_PLACE']))
            $arResult['place'] = Xml::addTag($arFields['LOCATION_PLACE']);
        if(!Helper::isEmpty($arFields['LOCATION_STREET']))
            $arResult['street'] = Xml::addTag($arFields['LOCATION_STREET']);
        if(!Helper::isEmpty($arFields['LOCATION_HOUSE']))
            $arResult['house'] = Xml::addTag($arFields['LOCATION_HOUSE']);
        if(!Helper::isEmpty($arFields['METRO_NAME']))
            $arResult['metro'] = static::getXmlTag_LocationMetro($arFields);
        if(!Helper::isEmpty($arFields['RAILWAY_NAME']))
            $arResult['railway-station'] = static::getXmlTag_LocationRailwayStation($arFields);
        if(!Helper::isEmpty($arFields['DISTANCE']))
            $arResult['distance'] = Xml::addTag($arFields['DISTANCE']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: metro
     */
    protected function getXmlTag_LocationMetro($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['METRO_NAME']))
            $arResult['name'] = Xml::addTag($arFields['METRO_NAME']);
        if(!Helper::isEmpty($arFields['TIME_FOOT']))
            $arResult['time-foot'] = Xml::addTag($arFields['TIME_FOOT']);
        if(!Helper::isEmpty($arFields['TIME_TRANSPORT']))
            $arResult['time-transport'] = Xml::addTag($arFields['TIME_TRANSPORT']);
        if(!Helper::isEmpty($arFields['TIME_STOP']))
            $arResult['time-stop'] = Xml::addTag($arFields['TIME_STOP']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: railway-station
     */
    protected function getXmlTag_LocationRailwayStation($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['RAILWAY_NAME']))
            $arResult['name'] = Xml::addTag($arFields['RAILWAY_NAME']);
        if(!Helper::isEmpty($arFields['RAILWAY_TIMEFOOT']))
            $arResult['time-foot'] = Xml::addTag($arFields['RAILWAY_TIMEFOOT']);
        if(!Helper::isEmpty($arFields['RAILWAY_TIMETRANSPORT']))
            $arResult['time-transport'] = Xml::addTag($arFields['RAILWAY_TIMETRANSPORT']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: date
     */
    protected function getXmlTag_Date($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['DATE_BEGIN']))
            $arResult['create'] = Xml::addTag($arFields['DATE_BEGIN']);
        if(!Helper::isEmpty($arFields['DATE_UPDATE']))
            $arResult['update'] = Xml::addTag($arFields['DATE_UPDATE']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: price
     */
    protected function getXmlTag_Price($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['PRICE_VALUE']))
            $arResult['value'] = Xml::addTag($arFields['PRICE_VALUE']);
        $arResult['currency'] = "RUB";   // toDo
        if(!Helper::isEmpty($arFields['PERIOD']))
            $arResult['period'] = Xml::addTag($arFields['PERIOD']);
        if(!Helper::isEmpty($arFields['AREA_UNIT']))
            $arResult['unit'] = Xml::addTag($arFields['AREA_UNIT']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: agent
     */
    protected function getXmlTag_Agent($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['AGENT_NAME']))
            $arResult['name'] = Xml::addTag($arFields['AGENT_NAME']);
        if(!Helper::isEmpty($arFields['AGENT_PHONE']))
            $arResult['phone'] = Xml::addTag($arFields['AGENT_PHONE']);
        if(!Helper::isEmpty($arFields['AGENT_CATEGORY']))
            $arResult['category'] = Xml::addTag($arFields['AGENT_CATEGORY']);
        if(!Helper::isEmpty($arFields['ORGANIZATION']))
            $arResult['organization'] = Xml::addTag($arFields['ORGANIZATION']);
        if(!Helper::isEmpty($arFields['AGENT_URL']))
            $arResult['url'] = Xml::addTag($arFields['AGENT_URL']);
        if(!Helper::isEmpty($arFields['AGENT_EMAIL']))
            $arResult['email'] = Xml::addTag($arFields['AGENT_EMAIL']);
        if(!Helper::isEmpty($arFields['AGENT_SCYPE']))
            $arResult['skype'] = Xml::addTag($arFields['AGENT_SCYPE']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: files
     */
    protected function getXmlTag_Files($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['IMAGE']))
            $arResult['image'] = Xml::addTag($arFields['IMAGE']);
        if(!Helper::isEmpty($arFields['VIDEO']))
            $arResult['video'] = Xml::addTag($arFields['VIDEO']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: description
     */
    protected function getXmlTag_Description($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['SHORT_DESCRIPTION']))
            $arResult['short'] = Xml::addTag($arFields['SHORT_DESCRIPTION']);
        if(!Helper::isEmpty($arFields['FULL_DESCRIPTION']))
            $arResult['full'] = Xml::addTag($arFields['FULL_DESCRIPTION']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: building
     */
    protected function getXmlTag_Building($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['BUILDING_NAME']))
            $arResult['name'] = Xml::addTag($arFields['BUILDING_NAME']);
        if(!Helper::isEmpty($arFields['BUILDING_YEAR']))
            $arResult['year'] = Xml::addTag($arFields['BUILDING_YEAR']);
        if(!Helper::isEmpty($arFields['BUILDING_QUARTER']))
            $arResult['quarter'] = Xml::addTag($arFields['BUILDING_QUARTER']);
        if(!Helper::isEmpty($arFields['BUILDING_STATUS']))
            $arResult['status'] = Xml::addTag($arFields['BUILDING_STATUS']);
        if(!Helper::isEmpty($arFields['BUILDING_TYPE']))
            $arResult['type'] = Xml::addTag($arFields['BUILDING_TYPE']);
        if(!Helper::isEmpty($arFields['BUILDING_SERIES']))
            $arResult['series'] = Xml::addTag($arFields['BUILDING_SERIES']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: total
     */
    protected function getXmlTag_Total($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['TOTAL_VALUE']))
            $arResult['value'] = Xml::addTag($arFields['TOTAL_VALUE']);
        if(!Helper::isEmpty($arFields['TOTAL_UNIT']))
            $arResult['unit'] = Xml::addTag($arFields['TOTAL_UNIT']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: living
     */
    protected function getXmlTag_Living($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['LIVING_VALUE']))
            $arResult['value'] = Xml::addTag($arFields['LIVING_VALUE']);
        if(!Helper::isEmpty($arFields['LIVING_ROOMS']))
            $arResult['value-rooms'] = Xml::addTag($arFields['LIVING_ROOMS']);
        if(!Helper::isEmpty($arFields['LIVING_UNIT']))
            $arResult['unit'] = Xml::addTag($arFields['LIVING_UNIT']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: Kitchen
     */
    protected function getXmlTag_Kitchen($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['KITCHEN_VALUE']))
            $arResult['value'] = Xml::addTag($arFields['KITCHEN_VALUE']);
        if(!Helper::isEmpty($arFields['KITCHEN_UNIT']))
            $arResult['unit'] = Xml::addTag($arFields['KITCHEN_UNIT']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }

    /**
     *	Get XML tag: Lot
     */
    protected function getXmlTag_Lot($arFields){
        $arResult = array();
        if(!Helper::isEmpty($arFields['LOT_VALUE']))
            $arResult['value'] = Xml::addTag($arFields['LOT_VALUE']);
        if(!Helper::isEmpty($arFields['LOT_UNIT']))
            $arResult['unit'] = Xml::addTag($arFields['LOT_UNIT']);
        return array(
            array(
                '#' => $arResult,
            ),
        );
    }
}

?>