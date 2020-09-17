<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Entity,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\Field\Valuebase;

Loc::loadMessages(__FILE__);

/**
 * Class ProfileIBlockTable
 * @package Acrit\Core\Export
 */

abstract class ProfileIBlockTable extends Entity\DataManager {
	
	const TABLE_NAME = ''; // Must be overriden! Value must contain table name! Value cannot be null!
	
	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!
	
	const TYPE_FIELD = 'FIELD';
	const TYPE_PROPERTY = 'PROPERTY';
	const TYPE_IBLOCK = 'IBLOCK';
	const TYPE_SECTION = 'SECTION';
	const TYPE_CATALOG = 'CATALOG';
	const TYPE_PRICE = 'PRICE';
	
	static $arCacheAvailableFields = array();
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return static::TABLE_NAME;
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() {
		return array(
			'ID' => new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_PROFILE_ID'),
			)),
			'IBLOCK_ID' => new Entity\IntegerField('IBLOCK_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_IBLOCK_ID'),
			)),
			'IBLOCK_MAIN' => new Entity\StringField('IBLOCK_MAIN', array(
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_IBLOCK_MAIN'),
			)),
			'SECTIONS_ID' => new Entity\TextField('SECTIONS_ID', array(
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_SECTIONS_ID'),
			)),
			'SECTIONS_MODE' => new Entity\StringField('SECTIONS_MODE', array(
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_SECTIONS_MODE'),
			)),
			'USE_FILTER' => new Entity\StringField('USE_FILTER', array(
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_USE_FILTER'),
			)),
			'FILTER' => new Entity\TextField('FILTER', array(
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_FILTER'),
			)),
			'PARAMS' => new Entity\TextField('PARAMS', array(
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_PARAMS'),
			)),
			'DATE_MODIFIED' => new Entity\DatetimeField('DATE_MODIFIED', array(
				'title' => Loc::getMessage('ACRIT_EXP_FIELD_DATE_MODIFIED'),
			)),
		);
	}

	/**
	 *	Add item
	 */
	public static function add(array $data){
		$obResult = parent::add($data);
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
		return $obResult;
	}

	/**
	 *	Update item
	 */
	public static function update($primary, array $data){
		$obResult = parent::update($primary, $data);
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
		return $obResult;
	}
	
	/**
	 *	Delete item
	 */
	public static function delete($primary){
		$obResult = parent::delete($primary);
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
		return $obResult;
	}
	
	/**
	 *	Delete profile data by its deleting
	 */
	public static function deleteProfileData($intProfileID){
		$strTableName = static::getTableName();
		$strSql = "DELETE FROM `{$strTableName}` WHERE `PROFILE_ID`='{$intProfileID}';";
		\Bitrix\Main\Application::getConnection()->query($strSql);
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
	}
	
	/**
	 *	Get iblocks ID for profile
	 */
	public static function getProfileIBlocks($intProfileID, $bMain=false){
		$arResult = array();
		$arFilter = array(
			'PROFILE_ID' => $intProfileID,
		);
		if($bMain){
			$arFilter['IBLOCK_MAIN'] = 'Y';
		}
		$resIBlocks = static::getList(array(
			'filter' => $arFilter,
			'select' => array(
				'IBLOCK_ID',
			),
		));
		while($arIBlock = $resIBlocks->fetch()){
			$arResult[] = IntVal($arIBlock['IBLOCK_ID']);
		}
		return $arResult;
	}
	
	/**
	 *	Load
	 */
	public static function loadIBlockData($intProfileID, $intIBlockID){
		$arResult = array();
		$resItem = static::getList(array(
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
			),
		));
		if($arItem = $resItem->fetch()){
			$arItem['~PARAMS'] = $arItem['PARAMS'];
			$arItem['PARAMS'] = unserialize($arItem['PARAMS']);
			if(!is_array($arItem['PARAMS'])){
				$arItem['PARAMS'] = array();
			}
			$arResult = $arItem;
		}
		return $arResult;
	}
	
	/**
	 *	Get all fields for selected iblock
	 */
	public static function getAvailableElementFieldsPlain($intIBlockID){
		$arResult = array();
		$arAvailableFields = static::getAvailableElementFields($intIBlockID);
		foreach($arAvailableFields as $strGroup => $arGroup){
			if(is_array($arGroup['ITEMS'])){
				foreach($arGroup['ITEMS'] as $strItem => $arItem){
					$arItem['GROUP'] = $arGroup['TYPE'];
					$arItem['NAME_PREFIX'] = $arGroup['NAME_PREFIX'];
					$arItem['CATEGORY'] = $strGroup;
					$strKey = strlen($arGroup['PREFIX']) ? $arGroup['PREFIX'].$strItem : $strItem;
					$arResult[$strKey] = $arItem;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get all fields for selected iblock
	 */
	public static function getAvailableElementFields($intIBlockID){
		if(is_array(static::$arCacheAvailableFields[$intIBlockID]) && !empty(static::$arCacheAvailableFields[$intIBlockID])){
			return static::$arCacheAvailableFields[$intIBlockID];
		}
		$arResult = array(
			'ID' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__ID'),
				'TYPE' => 'N',
				'READONLY' => true,
				'USER_TYPE' => '_ID_LIST',
			),
			'NAME' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__NAME'),
				'TYPE' => 'S',
			),
			'TIMESTAMP_X' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__TIMESTAMP_X'),
				'TYPE' => 'S',
				'USER_TYPE' => 'DateTime',
			),
			'DATE_CREATE' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__DATE_CREATE'),
				'TYPE' => 'S',
				'USER_TYPE' => 'DateTime',
			),
			'IBLOCK_ID' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK_ID'),
				'TYPE' => 'N',
				'FILTRABLE' => false,
			),
			'IBLOCK_SECTION_ID' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK_SECTION_ID'),
				'TYPE' => 'N',
				'USER_TYPE' => '_SectionId',
			),
			'__IBLOCK_SECTION_CHAIN' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK_SECTION_CHAIN'),
				'TYPE' => 'N',
				'FILTRABLE' => false,
			),
			'ACTIVE' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__ACTIVE'),
				'TYPE' => 'S',
				'USER_TYPE' => '_Checkbox',
			),
			'ACTIVE_FROM' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__ACTIVE_FROM'),
				'TYPE' => 'S',
				'USER_TYPE' => 'DateTime',
			),
			'ACTIVE_TO' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__ACTIVE_TO'),
				'TYPE' => 'S',
				'USER_TYPE' => 'DateTime',
			),
			'SORT' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SORT'),
				'TYPE' => 'N',
			),
			'PREVIEW_PICTURE' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__PREVIEW_PICTURE'),
				'TYPE' => 'F',
			),
			'PREVIEW_TEXT' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__PREVIEW_TEXT'),
				'TYPE' => 'S',
			),
			'DETAIL_PICTURE' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__DETAIL_PICTURE'),
				'TYPE' => 'F',
			),
			'DETAIL_TEXT' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__DETAIL_TEXT'),
				'TYPE' => 'S',
			),
			'DETAIL_PAGE_URL' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__DETAIL_PAGE_URL'),
				'TYPE' => 'S',
			),
			'CODE' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CODE'),
				'TYPE' => 'S',
			),
			'XML_ID' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__XML_ID'),
				'TYPE' => 'S',
			),
			'TAGS' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__TAGS'),
				'TYPE' => 'S',
			),
			'SHOW_COUNTER' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SHOW_COUNTER'),
				'TYPE' => 'N',
				'READONLY' => true,
			),
			'SHOW_COUNTER_START' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SHOW_COUNTER_START'),
				'TYPE' => 'S',
				'USER_TYPE' => 'DateTime',
				'READONLY' => true,
			),
			'CREATED_BY' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CREATED_BY'),
				'TYPE' => 'N',
			),
			'CREATED_BY__NAME' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CREATED_BY__NAME'),
				'TYPE' => 'S',
				'FILTRABLE' => false,
			),
			'MODIFIED_BY' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__MODIFIED_BY'),
				'TYPE' => 'N',
			),
			'MODIFIED_BY__NAME' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__MODIFIED_BY__NAME'),
				'TYPE' => 'S',
				'FILTRABLE' => false,
			),
			'SEO_TITLE' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SEO_TITLE'),
				'TYPE' => 'S',
				'FILTRABLE' => false,
			),
			'SEO_KEYWORDS' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SEO_KEYWORDS'),
				'TYPE' => 'S',
				'FILTRABLE' => false,
			),
			'SEO_DESCRIPTION' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SEO_DESCRIPTION'),
				'TYPE' => 'S',
				'FILTRABLE' => false,
			),
			'SEO_H1' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SEO_H1'),
				'TYPE' => 'S',
				'FILTRABLE' => false,
			),
		);
		$arResult = array(
			'fields' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__GROUP__FIELDS'),
				'ITEMS' => $arResult,
				'PREFIX' => '', // used for system purposes
				'TYPE' => static::TYPE_FIELD,
			),
			'properties' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__GROUP__PROPERTIES'),
				'ITEMS' => static::_getAvailableElementProperties($intIBlockID),
				'PREFIX' => 'PROPERTY_',
				'SHOW_MORE' => true,
				'TYPE' => static::TYPE_PROPERTY,
			),
			'section' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__GROUP__SECTION'),
				'ITEMS' => static::_getAvailableElementSectionFields($intIBlockID),
				'PREFIX' => 'SECTION__',
				'NAME_PREFIX' => '',
				'TYPE' => static::TYPE_SECTION,
			),
			'iblock' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__GROUP__IBLOCK'),
				'ITEMS' => static::_getAvailableElementIBlockFields($intIBlockID),
				'PREFIX' => 'IBLOCK__',
				'NAME_PREFIX' => '',
				'TYPE' => static::TYPE_IBLOCK,
			),
			'catalog' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__GROUP__CATALOG'),
				'ITEMS' => static::_getAvailableElementCatalogFields($intIBlockID),
				'PREFIX' => 'CATALOG_',
				'TYPE' => static::TYPE_CATALOG,
			),
			'prices' => array(
				'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__GROUP__PRICES'),
				'ITEMS' => static::_getAvailableElementPrices($intIBlockID),
				'PREFIX' => 'CATALOG_PRICE_',
				'SHOW_MORE' => true,
				'TYPE' => static::TYPE_PRICE,
			),
		);
		static::$arCacheAvailableFields[$intIBlockID] = $arResult;
		return $arResult;
	}
	
	/**
	 *	Properties
	 */
	protected static function _getAvailableElementProperties($intIBlockID, $bUsePropCode=true){
		$arResult = array();
		if($intIBlockID && \Bitrix\Main\Loader::includeModule('iblock')){
			$arSort = array(
				'SORT' => 'ASC',
				'NAME' => 'ASC',
			);
			$arFilter = array(
				'IBLOCK_ID' => $intIBlockID,
				'ACTIVE' => 'Y',
			);
			$resProps = \CIBlockProperty::GetList($arSort, $arFilter);
			while($arProp = $resProps->GetNext(false,false)){
				$strType = $arProp['PROPERTY_TYPE'].(strlen($arProp['USER_TYPE']) ? ':'.$arProp['USER_TYPE'] : '');
				switch($strType){
					case 'S:directory':
						static::_getAvailableElementProperties_S_directory($arResult, $arProp, $intIBlockID, $bUsePropCode);
						break;
					case 'S:ElementXmlID':
						static::_getAvailableElementProperties_S_ElementXmlID($arResult, $arProp, $intIBlockID, $bUsePropCode);
						break;
					default:
						static::_getAvailableElementProperties_default($arResult, $arProp, $intIBlockID, $bUsePropCode);
						break;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *
	 */
	protected static function _getAvailableElementProperties_S_directory(&$arProps, $arProp, $intIBlockID, $bUsePropCode){
		static::_getAvailableElementProperties_default($arProps, $arProp, $intIBlockID, $bUsePropCode);
		$strTableName = &$arProp['USER_TYPE_SETTINGS']['TABLE_NAME'];
		if(strlen($strTableName)){
			$arFilter = array('TABLE_NAME' => $strTableName);
			$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => $arFilter))->fetch();
			if($arHLBlock) {
				$obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
				if(is_object($obEntity)){
					$strEntityDataClass = $obEntity->getDataClass();
					if(strlen($strEntityDataClass)){
						$intHighloadID = $arHLBlock['ID'];
						$resFields = \CUserTypeEntity::GetList(array('ID'=>'ASC'), array('ENTITY_ID'=>'HLBLOCK_'.$intHighloadID, 'LANG' => LANGUAGE_ID));
						$arPropItem = array(
							'NAME' => $arProp['NAME'].' (ID)',
							'CODE' => $arProp['CODE'].'__ID',
							'TYPE' => $arProp['PROPERTY_TYPE'],
							'ID' => $arProp['ID'],
							'IS_PROPERTY' => true,
							'IS_MULTIPLE' => $arProp['MULTIPLE'] == 'Y',
							'FILTRABLE' => false,
							'DATA' => $arProp,
						);
						$arPropItem['USER_TYPE'] = $arProp['USER_TYPE'];
						$strKey = $bUsePropCode && strlen($arProp['CODE']) ? $arProp['CODE'] : $arProp['ID'];
						$arProps[$strKey.Field\ValueBase::SUBFIELD_OPERATOR.'ID'] = $arPropItem;
						while($arField = $resFields->getNext()){
							$arPropItem = array(
								'NAME' => $arProp['NAME'].(strlen($arField['EDIT_FORM_LABEL']) ? ' ('.$arField['EDIT_FORM_LABEL'].')' : ''),
								'CODE' => $arProp['CODE'].'__'.$arField['FIELD_NAME'],
								'TYPE' => $arProp['PROPERTY_TYPE'],
								'ID' => $arProp['ID'],
								'IS_PROPERTY' => true,
								'IS_MULTIPLE' => $arProp['MULTIPLE'] == 'Y',
								'FILTRABLE' => false,
								'DATA' => $arProp,
							);
							$arPropItem['USER_TYPE'] = $arProp['USER_TYPE'];
							$strKey = $bUsePropCode && strlen($arProp['CODE']) ? $arProp['CODE'] : $arProp['ID'];
							$arProps[$strKey.Field\ValueBase::SUBFIELD_OPERATOR.$arField['FIELD_NAME']] = $arPropItem;
						}
					}
					unset($obEntity);
				}
				unset($arHLBlock, $arFilter);
			}
		}
	}
	protected static function _getAvailableElementProperties_S_ElementXmlID(&$arProps, $arProp, $intIBlockID, $bUsePropCode){
		static::_getAvailableElementProperties_default($arProps, $arProp, $intIBlockID, $bUsePropCode);
		$arFields = array(
			'ID' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__ID'),
			'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__NAME'),
			'CODE' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CODE'),
			'SORT' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SORT'),
			'XML_ID' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__XML_ID'),
			'PREVIEW_TEXT' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__PREVIEW_TEXT'),
			'DETAIL_TEXT' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__DETAIL_TEXT'),
			'PREVIEW_PICTURE' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__PREVIEW_PICTURE'),
			'DETAIL_PICTURE' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__DETAIL_PICTURE'),
			'DETAIL_PAGE_URL' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__DETAIL_PAGE_URL'),
		);
		foreach($arFields as $strField => $strName) {
			$arPropItem = array(
				'NAME' => $arProp['NAME'].' ('.$strName.')',
				'CODE' => $arProp['CODE'].'__'.$strField,
				'TYPE' => $arProp['PROPERTY_TYPE'],
				'ID' => $arProp['ID'],
				'IS_PROPERTY' => true,
				'IS_MULTIPLE' => $arProp['MULTIPLE'] == 'Y',
				'FILTRABLE' => false,
				'DATA' => $arProp,
			);
			$arPropItem['USER_TYPE'] = $arProp['USER_TYPE'];
			$strKey = $bUsePropCode && strlen($arProp['CODE']) ? $arProp['CODE'] : $arProp['ID'];
			$arProps[$strKey.Field\ValueBase::SUBFIELD_OPERATOR.$strField] = $arPropItem;
		}
		unset($arFields);
	}
	protected static function _getAvailableElementProperties_default(&$arProps, $arProp, $intIBlockID, $bUsePropCode){
		$arPropItem = array(
			'NAME' => $arProp['NAME'],
			'CODE' => $arProp['CODE'],
			'TYPE' => $arProp['PROPERTY_TYPE'],
			'ID' => $arProp['ID'],
			'IS_PROPERTY' => true,
			'IS_MULTIPLE' => $arProp['MULTIPLE'] == 'Y',
			'DATA' => $arProp,
		);
		if(strlen($arProp['USER_TYPE'])){
			$arPropItem['USER_TYPE'] = $arProp['USER_TYPE'];
		}
		$strKey = $bUsePropCode && strlen($arProp['CODE']) ? $arProp['CODE'] : $arProp['ID'];
		$arProps[$strKey] = $arPropItem;
	}
	
	/**
	 *	IBlock fields
	 */
	protected static function _getAvailableElementIBlockFields($intIBlockID){
		$arResult = array();
		if($intIBlockID && \Bitrix\Main\Loader::includeModule('iblock')){
			$arResult = array(
				'ID' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__ID'),
					'TYPE' => 'N',
					'READONLY' => true,
				),
				'TIMESTAMP_X' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__TIMESTAMP_X'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
				),
				'IBLOCK_TYPE_ID' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__TYPE_ID'),
					'TYPE' => 'S',
				),
				'LID' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__LID'),
					'TYPE' => 'S',
				),
				'CODE' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__CODE'),
					'TYPE' => 'S',
				),
				'NAME' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__NAME'),
					'TYPE' => 'S',
				),
				'SORT' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__SORT'),
					'TYPE' => 'N',
				),
				'PICTURE' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__PICTURE'),
					'TYPE' => 'F',
				),
				'DESCRIPTION' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__DESCRIPTION'),
					'TYPE' => 'S',
					'USER_TYPE' => 'HTML',
				),
				'ELEMENT_NAME' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__ELEMENT_NAME'),
					'TYPE' => 'S',
				),
				'XML_ID' => array(
					'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__IBLOCK__XML_ID'),
					'TYPE' => 'S',
				),
			);
			foreach($arResult as $key => $arItem){
				$arResult[$key]['FILTRABLE'] = false;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Section fields
	 */
	protected static function _getAvailableElementSectionFields($intIBlockID){
		$arResult = array();
		if($intIBlockID && \Bitrix\Main\Loader::includeModule('iblock')){
			$resIBlock = \CIBlock::getList(array(), array('ID' => $intIBlockID, 'CHECK_PERMISSIONS' => 'N'));
			if($arIBlock = $resIBlock->getNext(false,false)){
				$resIBlockType = \CIBlockType::GetByID($arIBlock['IBLOCK_TYPE_ID']);
				if($arIBlockType = $resIBlockType->getNext(false,false)){
					if($arIBlockType['SECTIONS']=='Y') {
						$arResult = array(
							'ID' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__ID'),
								'TYPE' => 'N',
								'READONLY' => true,
							),
							'TIMESTAMP_X' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__TIMESTAMP_X'),
								'TYPE' => 'S',
								'USER_TYPE' => 'DateTime',
							),
							'DATE_CREATE' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__DATE_CREATE'),
								'TYPE' => 'S',
								'USER_TYPE' => 'DateTime',
							),
							'SORT' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__SORT'),
								'TYPE' => 'N',
							),
							'NAME' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__NAME'),
								'TYPE' => 'S',
							),
							'PICTURE' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__PICTURE'),
								'TYPE' => 'F',
							),
							'DETAIL_PICTURE' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__DETAIL_PICTURE'),
								'TYPE' => 'F',
							),
							'DESCRIPTION' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__DESCRIPTION'),
								'TYPE' => 'S',
								'USER_TYPE' => 'HTML',
							),
							'CODE' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__CODE'),
								'TYPE' => 'S',
							),
							'XML_ID' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__XML_ID'),
								'TYPE' => 'S',
							),
							'SEO_TITLE' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__SEO_TITLE'),
								'TYPE' => 'S',
							),
							'SEO_KEYWORDS' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__SEO_KEYWORDS'),
								'TYPE' => 'S',
							),
							'SEO_DESCRIPTION' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__SEO_DESCRIPTION'),
								'TYPE' => 'S',
							),
							'SEO_H1' => array(
								'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__SECTION__SEO_H1'),
								'TYPE' => 'S',
							),
						);
						$arCatalog = Helper::getCatalogArray($intIBlockID);
						if($arCatalog['PRODUCT_IBLOCK_ID']){
							$intIBlockID = $arCatalog['PRODUCT_IBLOCK_ID'];
						}
						$arUserFields = Helper::getSectionUserFields($intIBlockID);
						foreach($arUserFields as $strFieldCode => $arUserField){
							$strFieldName = '';
							if(is_array($arUserField['EDIT_FORM_LABEL'])) {
								$strFieldName = $arUserField['EDIT_FORM_LABEL'][LANGUAGE_ID];
								if(!strlen($strFieldName)){
									$strFieldName = reset($arUserField['EDIT_FORM_LABEL']);
								}
							}
							if(!strlen($strFieldName)){
								$strFieldName = $strFieldCode;
							}
							$arTypes = array(
								'integer' => 'N',
								'double' => 'N',
								'date' => 'S', // S:Date
								'datetime' => 'S', // S:DateTime
								'boolean' => 'S',
								'file' => 'F',
								'iblock_section' => 'G',
								'iblock_element' => 'E',
								'hlblock' => 'S', // S:directory
								'enumeration' => 'L',
							);
							$strType = isset($arTypes[$arUserField['USER_TYPE_ID']]) ? $arTypes[$arUserField['USER_TYPE_ID']] : 'S';
							$arResult[$strFieldCode] = array(
								'NAME' => $strFieldName,
								'CODE' => $strFieldCode,
								'TYPE' => $arUserField['USER_TYPE_ID'],
								'ID' => $arUserField['ID'],
								'DATA' => $arUserField,
							);
						}
						foreach($arResult as $key => $arItem){
							$arResult[$key]['FILTRABLE'] = false;
						}
					}
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Catalog fields
	 */
	protected static function _getAvailableElementCatalogFields($intIBlockID){
		$arResult = array();
		if($intIBlockID && \Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('catalog')){
			$arCatalog = Helper::getCatalogArray($intIBlockID);
			$intIBlockOffersID = IntVal($arCatalog['OFFERS_IBLOCK_ID']);
			if(is_array($arCatalog) && $arCatalog['IBLOCK_ID']==$intIBlockID){
				$arResult = array(
					'QUANTITY' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_QUANTITY'),
						'TYPE' => 'N',
					),
					'QUANTITY_RESERVED' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_QUANTITY_RESERVED'),
						'TYPE' => 'N',
					),
					'AVAILABLE' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_AVAILABLE'),
						'TYPE' => 'S',
						'USER_TYPE' => '_Checkbox',
						'READONLY' => true,
					),
					'WEIGHT' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_WEIGHT'),
						'TYPE' => 'N',
					),
					'WIDTH' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_WIDTH'),
						'TYPE' => 'N',
					),
					'LENGTH' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_LENGTH'),
						'TYPE' => 'N',
					),
					'HEIGHT' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_HEIGHT'),
						'TYPE' => 'N',
					),
					'VAT_ID' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_VAT_ID'),
						'TYPE' => 'N',
					),
					'VAT_VALUE' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_VAT_VALUE'),
						'TYPE' => 'N',
					),
					'VAT_VALUE_FLOAT' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_VAT_VALUE_FLOAT'),
						'TYPE' => 'N',
					),
					'VAT_INCLUDED' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_VAT_INCLUDED'),
						'TYPE' => 'S',
						'USER_TYPE' => '_Checkbox',
					),
					'PURCHASING_PRICE' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PURCHASING_PRICE'),
						'TYPE' => 'N',
					),
					'PURCHASING_CURRENCY' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PURCHASING_CURRENCY'),
						'TYPE' => 'S',
						'USER_TYPE' => '_Currency',
					),
					'MEASURE_ID' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_MEASURE_ID'),
						'TYPE' => 'N',
					),
					'MEASURE_UNIT' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_MEASURE_UNIT'),
						'TYPE' => 'S',
					),
					'MEASURE_NAME' => array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_MEASURE_NAME'),
						'TYPE' => 'S',
					),
				);
				if(class_exists('\CCatalogStoreBarCode')/* && Helper::isCatalogUseStoreControl()*/){
					$arResult['BARCODE'] = array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_BARCODE'),
						'TYPE' => 'S',
					);
				}
				if($intIBlockOffersID) {
					#$arResult['TYPE'] = array(
					#	'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_TYPE'),
					#	'TYPE' => 'N',
					#	'READONLY' => true,
					#);
					$arResult['OFFERS'] = array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_OFFERS'),
						'TYPE' => 'X',
						'USER_TYPE' => '_OffersFlag',
						'READONLY' => true,
					);
				}
				$arStores = Helper::getStoresList();
				foreach($arStores as $arStore){
					$arResult['STORE_AMOUNT_'.$arStore['ID']] = array(
						'NAME' => Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_STORE_AMOUNT', array(
							'#NAME#' => $arStore['~TITLE'],
							'#ADDRESS#' => $arStore['~ADDRESS'],
						)),
						'TYPE' => 'N',
					);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Prices
	 */
	protected static function _getAvailableElementPrices($intIBlockID){
		$arResult = array();
		if($intIBlockID && \Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('catalog')){
			$arCatalog = Helper::getCatalogArray($intIBlockID);
			if(is_array($arCatalog) && $arCatalog['IBLOCK_ID']==$intIBlockID){
				$arPrices = Helper::getPriceList(array('SORT' => 'ASC', 'ID' => 'ASC'));
				foreach($arPrices as $arPrice) {
					$arPrice['NAME_LANG'] = strlen(trim($arPrice['NAME_LANG'])) ? $arPrice['NAME_LANG'] : $arPrice['NAME'];
					$arResult[$arPrice['ID'].'__WITH_DISCOUNT'] = array(
						'NAME' => $arPrice['NAME_LANG'].Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PRICE_WITH_DISCOUNT'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'N',
						'ID' => $arPrice['ID'],
						'FILTRABLE' => false,
					);
					$arResult[$arPrice['ID']] = array(
						'NAME' => $arPrice['NAME_LANG'].Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PRICE_NO_DISCOUNT'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'N',
						'ID' => $arPrice['ID'],
					);
					$arResult[$arPrice['ID'].'__DISCOUNT'] = array(
						'NAME' => $arPrice['NAME_LANG'].Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PRICE_DISCOUNT'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'N',
						'ID' => $arPrice['ID'],
						'FILTRABLE' => false,
					);
					#
					$arResult[$arPrice['ID'].'__WITH_DISCOUNT__CURR'] = array(
						'NAME' => $arPrice['NAME_LANG'].Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PRICE_WITH_DISCOUNT_CURR'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'S',
						'ID' => $arPrice['ID'],
						'FILTRABLE' => false,
					);
					$arResult[$arPrice['ID'].'__CURR'] = array(
						'NAME' => $arPrice['NAME_LANG'].Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PRICE_NO_DISCOUNT_CURR'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'S',
						'ID' => $arPrice['ID'],
						'FILTRABLE' => false,
					);
					$arResult[$arPrice['ID'].'__DISCOUNT__CURR'] = array(
						'NAME' => $arPrice['NAME_LANG'].Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PRICE_DISCOUNT_CURR'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'S',
						'ID' => $arPrice['ID'],
						'FILTRABLE' => false,
					);
					$arResult[$arPrice['ID'].'__PERCENT'] = array(
						'NAME' => $arPrice['NAME_LANG'].Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PRICE_PERCENT'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'N',
						'ID' => $arPrice['ID'],
						'FILTRABLE' => false,
					);
					#
					$arResult[$arPrice['ID'].'__CURRENCY'] = array(
						'NAME' => $arPrice['NAME_LANG'].Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__CATALOG_PRICE_CURRENCY'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'S',
						'ID' => $arPrice['ID'],
						'USER_TYPE' => '_Currency',
						#'FILTRABLE' => false,
					);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Display one available item
	 *	As minimum, it used in popup 'Select field' and on display field's default value
	 */
	public static function displayAvailableItemName($arItem, $bParent=false, $bOffer=false){
		$strResult = '';
		$arInfo = array();
		if(strlen($arItem['NAME_PREFIX'])){
			$strResult .= $arItem['NAME_PREFIX'].' ';
		}
		if(strlen($arItem['ID'])){
			$arInfo[] = $arItem['ID'];
		}
		if(strlen($arItem['CODE'])){
			$arInfo[] = $arItem['CODE'];
		}
		if(is_array($arItem['DATA']) && strlen($arItem['DATA']['PROPERTY_TYPE'])){
			$strItem = $arItem['DATA']['PROPERTY_TYPE'];
			$strItem .= strlen($arItem['DATA']['USER_TYPE']) ? ':'.$arItem['DATA']['USER_TYPE'] : '';
			$strItem .= $arItem['DATA']['MULTIPLE'] == 'Y' ? '+' : '';
			$arInfo[] = $strItem;
		}
		elseif(is_array($arItem['DATA']) && strlen($arItem['DATA']['USER_TYPE_ID'])){
			$strItem = $arItem['DATA']['USER_TYPE_ID'];
			$strItem .= $arItem['DATA']['MULTIPLE'] == 'Y' ? '+' : '';
			$arInfo[] = $strItem;
		}
		else{
			if(strlen($arItem['IS_MULTIPLE'])){
				$arInfo[] = '+';
			}
		}
		$strResult .= $arItem['NAME'];
		if(!empty($arInfo)) {
			$strResult .= ' ['.implode(', ', $arInfo).']';
		}
		if(!empty($arItem['MORE'])) {
			$strResult .= ' '.$arItem['MORE'];
		}
		if($bOffer){
			$strResult = Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__PREFIX_OFFER').$strResult;
		}
		elseif($bParent){
			$strResult = Loc::getMessage('ACRIT_EXP_ELEMENT_FIELD__PREFIX_PRODUCT').$strResult;
		}
		return $strResult;
	}
	
	/**
	 *	Get array of ID of used sections
	 *	$strUsedSectionsID - это все разделы, в т.ч. по доп. привязкам, и тут могут быть и те разделы, которые
	 *	не выбраны в списке - их не должно быть в результирующем массиве
	 *	$strMode = all || selected || selected_with_subsections
	 */
	public static function getInvolvedSectionsID($intIBlockID, $strSelectedCategoriesID, $strMode){
		$arResult = array();
		$arSort = array(
			'LEFT_MARGIN' => 'ASC',
		);
		$arFilter = array(
			'IBLOCK_ID' => $intIBlockID,
			'CHECK_PERMISSIONS' => 'N',
		);
		$arSectionsAll = array();
		$resSections = \CIBlockSection::getList($arSort, $arFilter, false, array('ID','DEPTH_LEVEL'));
		while($arSection = $resSections->getNext(false,false)){
			$arSectionsAll[$arSection['ID']] = array(
				'DEPTH_LEVEL' => IntVal($arSection['DEPTH_LEVEL']),
			);
		}
		unset($resSections, $arSection);
		#
		$arSelectedSectionsID = explode(',', $strSelectedCategoriesID);
		Helper::arrayRemoveEmptyValues($arSelectedSectionsID);
		#
		switch($strMode){
			case 'all':
				$arResult = array_keys($arSectionsAll);
				break;
			case 'selected':
				foreach($arSelectedSectionsID as $intSelectedSectionID){
					if(isset($arSectionsAll[$intSelectedSectionID])){
						$arResult[] = $intSelectedSectionID;
					}
				}
				break;
			case 'selected_with_subsections':
				foreach($arSelectedSectionsID as $intSelectedSectionID){
					# для каждого раздела $intSelectedSectionID ищем его в $arSectionsAll и отбираем все подразделы (там где DEPT_LEVEL больше чем у него)
					$intSelectedDepthLevel = false;
					foreach($arSectionsAll as $intSectionID => $arSection){
						if($intSelectedDepthLevel){
							if($arSection['DEPTH_LEVEL']>$intSelectedDepthLevel) {
								$arResult[] = $intSectionID;
							}
							else {
								break;
							}
						}
						if($intSectionID == $intSelectedSectionID){
							$arResult[] = $intSectionID;
							$intSelectedDepthLevel = $arSection['DEPTH_LEVEL'];
						}
					}
					$arResult = array_unique($arResult); // т.к. может быть выбран и родитель, и дети - в таком случае будут дубли
				}
				break;
		}
		unset($arSectionsAll, $arSelectedSectionsID);
		return $arResult;
	}
	
	/**
	 *	Check IBlock (Props, URLs, ...)
	 */
	public static function checkIBlock($intIBlockID){
		$strResult = '';
		$arIBlockErrors = array();
		if($intIBlockID > 0) {
			$arCatalog = Helper::getCatalogArray($intIBlockID);
			$arIBlockErrors[$intIBlockID] = static::checkSingleIBlock($intIBlockID, false);
			if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID'] > 0){
				$arIBlockErrors[$arCatalog['OFFERS_IBLOCK_ID']] = static::checkSingleIBlock($arCatalog['OFFERS_IBLOCK_ID'], 
					true);
			}
		}
		$strResult .= '<ul style="margin:0 0 0 20px;padding:0;">';
		foreach($arIBlockErrors as $arErrors){
			$arIBlock = array_shift($arErrors);
			if(!empty($arErrors)){
				$strResult .= '<li>';
				$strIBlock = Loc::getMessage('ACRIT_EXP_IBLOCK_ERROR_'.($arIBlock['ID'] == $intIBlockID ? 'MAIN' : 'OFFERS'))
					.' - ['.$arIBlock['ID'].'] '.$arIBlock['NAME'];
				$strResult .= '<b>'.$strIBlock.'</b>';
				$strResult .= '<ul style="margin:0 0 0 20px;padding:0;">';
				foreach($arErrors as $arError){
					$bErrors = true;
					$strResult .= '<li>'.$arError['TEXT'].' <a href="'.$arError['URL'].'" target="_blank">'
						.Loc::getMessage('ACRIT_EXP_IBLOCK_ERROR_CHECK').'</a></li>';
				}
				$strResult .= '</ul>';
				$strResult .= '</li>';
			}
		}
		$strResult .= '</ul>';
		return $bErrors && strlen($strResult) ? Helper::showNote($strResult, true, false, true).'<br/>' : '';
	}
	protected static function checkSingleIBlock($intIBlockID, $bOffer=false){
		$arErrors = array();
		$arFilter = array(
			'ID' => $intIBlockID,
			'CHECK_PERMISSIONS' => 'N',
		);
		$resIBlock = \CIBlock::GetList(array(), $arFilter, false);
		if($arIBlock = $resIBlock->getNext(false, false)){
			$arErrors['IBLOCK'] = $arIBlock;
			# Check props
			$intMaxShow = 10;
			$arPropCodes = array();
			$resProps = \CIBlockProperty::getList(array(), array('IBLOCK_ID' => $arIBlock['ID'], 'ACTIVE' => 'Y'));
			$arEmptyCodeProps = array();
			$arExistCodeProps = array();
			while($arProp = $resProps->getNext(false, false)){
				if(!strlen(trim($arProp['CODE']))){
					$arEmptyCodeProps[] = $arProp['ID'];
				}
				if(!in_array($arProp['CODE'], $arPropCodes)){
					$arPropCodes[$arProp['ID']] = $arProp['CODE'];
				}
				elseif(!in_array($arProp['CODE'], $arExistCodeProps)){
					$arExistCodeProps[$arProp['ID']] = $arProp['CODE'];
				}
			}
			$intCountEmpty  = count($arEmptyCodeProps);
			$intCountDouble = count($arExistCodeProps);
			$arEmptyCodeProps = array_slice($arEmptyCodeProps, 0, $intMaxShow);
			$arExistCodeProps = array_slice($arExistCodeProps, 0, $intMaxShow);
			if(!empty($arExistCodeProps) && !(count($arExistCodeProps) == 1 && empty($arExistCodeProps[0]))){
				$arErrors[] = array(
					'TEXT' => Loc::getMessage('ACRIT_EXP_IBLOCK_ERROR_PROPS_DOUBLE', array(
						'#PROPS#' => implode(', ', $arExistCodeProps),
					)),
					'URL' => static::getIBlockUrl($arIBlock, 'edit2'),
				);
			}
			if(!empty($arEmptyCodeProps)){
				$arErrors[] = array(
					'TEXT' => Loc::getMessage('ACRIT_EXP_IBLOCK_ERROR_PROPS_EMPTY', array(
						'#PROPS#' => implode(', ', $arEmptyCodeProps),
					)),
					'URL' => static::getIBlockUrl($arIBlock, 'edit2'),
				);
			}
			# Check DETAIL_PAGE_URL, SECTION_PAGE_URL
			$arIBlock['DETAIL_PAGE_URL'] = trim($arIBlock['DETAIL_PAGE_URL']);
			if(!strlen($arIBlock['DETAIL_PAGE_URL'])){
				$arErrors[] = array(
					'TEXT' => Loc::getMessage('ACRIT_EXP_IBLOCK_ERROR_DETAIL_PAGE_URL'),
					'URL' => static::getIBlockUrl($arIBlock, 'edit1'),
				);
			}
			$bIBlockSupportSections = static::isIBlockSupportSections($arIBlock['IBLOCK_TYPE_ID']);
			$arIBlock['SECTION_PAGE_URL'] = trim($arIBlock['SECTION_PAGE_URL']);
			if($bIBlockSupportSections && !strlen($arIBlock['SECTION_PAGE_URL']) && !$bOffer){
				$arErrors[] = array(
					'TEXT' => Loc::getMessage('ACRIT_EXP_IBLOCK_ERROR_SECTION_PAGE_URL'),
					'URL' => static::getIBlockUrl($arIBlock, 'edit1'),
				);
			}
			# Check permissions
			$arFilterWithPermissions = array(
				'ID' => $intIBlockID,
				'PERMISSIONS_BY' => 0,
				'CHECK_PERMISSIONS' => 'Y',
			);
			$resIBlockWithPermissions = \CIBlock::GetList(array(), $arFilterWithPermissions, false);
			if(!$resIBlockWithPermissions->getNext(false, false)){
				$arErrors[] = array(
					'TEXT' => Loc::getMessage('ACRIT_EXP_IBLOCK_ERROR_PERMISSIONS'),
					'URL' => static::getIBlockUrl($arIBlock, 'edit4'),
				);
			}
		}
		return $arErrors;
	}
	
	/**
	 *	
	 */
	protected static function isIBlockSupportSections($intIBlockTypeId){
		static $arIBlockTypes;
		if(!is_array($arIBlockTypes)){
			$arIBlockTypes = [];
			$resIBlockType = \CIBlockType::getList();
			while($arIBlockType = $resIBlockType->getNext(false, false)){
				$arIBlockTypes[$arIBlockType['ID']] = $arIBlockType['SECTIONS'] == 'Y' ? true : false;
			}
		}
		return !!$arIBlockTypes[$intIBlockTypeId];
	}
	
	/**
	 *	Get tab for IBlock
	 */
	protected static function getIBlockUrl($arIBlock, $strTab){
		return sprintf("/bitrix/admin/iblock_edit.php?type=%s&lang=%s&ID=%d&admin=Y&tabControl_active_tab=%s", 
			$arIBlock['IBLOCK_TYPE_ID'], LANGUAGE_ID, $arIBlock['ID'], $strTab);
	}
	
}
