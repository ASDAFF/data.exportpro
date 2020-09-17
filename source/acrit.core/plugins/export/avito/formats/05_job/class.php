<?
/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/job
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoJob extends Avito {
	
	CONST DATE_UPDATED = '2019-09-03';

	protected static $bSubclass = true;
	
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
		return parent::getCode().'_JOB';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'avito_job.xml';
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$this->removeFields($arResult, array('VIDEO_URL', 'PRICE'));
		#
		$arResult[] = new Field(array(
			'CODE' => 'STREET',
			'DISPLAY_CODE' => 'Street',
			'NAME' => static::getMessage('FIELD_STREET_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_STREET_DESC'),
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_STREET',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => '256',
			),
		));
		#
		$this->modifyField($arResult, 'CATEGORY', array(
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('FIELD_CATEGORY_DEFAULT'),
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'INDUSTRY',
			'DISPLAY_CODE' => 'Industry',
			'NAME' => static::getMessage('FIELD_INDUSTRY_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_INDUSTRY_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'JOB_TYPE',
			'DISPLAY_CODE' => 'JobType',
			'NAME' => static::getMessage('FIELD_JOB_TYPE_NAME'),
			'SORT' => 1010,
			'DESCRIPTION' => static::getMessage('FIELD_JOB_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'EXPERIENCE',
			'DISPLAY_CODE' => 'Experience',
			'NAME' => static::getMessage('FIELD_EXPERIENCE_NAME'),
			'SORT' => 1020,
			'DESCRIPTION' => static::getMessage('FIELD_EXPERIENCE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SALARY',
			'DISPLAY_CODE' => 'Salary',
			'NAME' => static::getMessage('FIELD_SALARY_NAME'),
			'SORT' => 1030,
			'DESCRIPTION' => static::getMessage('FIELD_SALARY_DESC'),
		));
		#
		$this->sortFields($arResult);
		return $arResult;
	}
	
	/**
	 *	Process single element (generate XML)
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		# Build XML
		$arXmlTags = array(
			'Id' => array('#' => $arFields['ID']),
		);
		if(!Helper::isEmpty($arFields['DATE_BEGIN']))
			$arXmlTags['DateBegin'] = Xml::addTag($arFields['DATE_BEGIN']);
		if(!Helper::isEmpty($arFields['DATE_END']))
			$arXmlTags['DateEnd'] = Xml::addTag($arFields['DATE_END']);
		if(!Helper::isEmpty($arFields['LISTING_FEE']))
			$arXmlTags['ListingFee'] = Xml::addTag($arFields['LISTING_FEE']);
		if(!Helper::isEmpty($arFields['AD_STATUS']))
			$arXmlTags['AdStatus'] = Xml::addTag($arFields['AD_STATUS']);
		if(!Helper::isEmpty($arFields['AVITO_ID']))
			$arXmlTags['AvitoId'] = Xml::addTag($arFields['AVITO_ID']);
		#
		if(!Helper::isEmpty($arFields['ALLOW_EMAIL']))
			$arXmlTags['AllowEmail'] = Xml::addTag($arFields['ALLOW_EMAIL']);
		if(!Helper::isEmpty($arFields['MANAGER_NAME']))
			$arXmlTags['ManagerName'] = Xml::addTag($arFields['MANAGER_NAME']);
		if(!Helper::isEmpty($arFields['CONTACT_PHONE']))
			$arXmlTags['ContactPhone'] = Xml::addTag($arFields['CONTACT_PHONE']);
		#
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['Description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['IMAGES']))
			$arXmlTags['Images'] = $this->getXmlTag_Images($arFields['IMAGES']);
		if(!Helper::isEmpty($arFields['TITLE']))
			$arXmlTags['Title'] = Xml::addTag($arFields['TITLE']);
		#
		if(!Helper::isEmpty($arFields['ADDRESS']))
			$arXmlTags['Address'] = Xml::addTag($arFields['ADDRESS']);
		if(!Helper::isEmpty($arFields['REGION']))
			$arXmlTags['Region'] = Xml::addTag($arFields['REGION']);
		if(!Helper::isEmpty($arFields['CITY']))
			$arXmlTags['City'] = Xml::addTag($arFields['CITY']);
		if(!Helper::isEmpty($arFields['SUBWAY']))
			$arXmlTags['Subway'] = Xml::addTag($arFields['SUBWAY']);
		if(!Helper::isEmpty($arFields['DISTRICT']))
			$arXmlTags['District'] = Xml::addTag($arFields['DISTRICT']);
		if(!Helper::isEmpty($arFields['STREET']))
			$arXmlTags['Street'] = Xml::addTag($arFields['STREET']);
		if(!Helper::isEmpty($arFields['LATITUDE']))
			$arXmlTags['Latitude'] = Xml::addTag($arFields['LATITUDE']);
		if(!Helper::isEmpty($arFields['LONGITUDE']))
			$arXmlTags['Longitude'] = Xml::addTag($arFields['LONGITUDE']);
		#
		if(!Helper::isEmpty($arFields['CATEGORY']))
			$arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
		if(!Helper::isEmpty($arFields['INDUSTRY']))
			$arXmlTags['Industry'] = Xml::addTag($arFields['INDUSTRY']);
		if(!Helper::isEmpty($arFields['JOB_TYPE']))
			$arXmlTags['JobType'] = Xml::addTag($arFields['JOB_TYPE']);
		if(!Helper::isEmpty($arFields['EXPERIENCE']))
			$arXmlTags['Experience'] = Xml::addTag($arFields['EXPERIENCE']);
		if(!Helper::isEmpty($arFields['SALARY']))
			$arXmlTags['Salary'] = Xml::addTag($arFields['SALARY']);
		# build XML
		$arXml = array(
			'Ad' => array(
				'#' => $arXmlTags,
			),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoXml') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		$strXml = Xml::arrayToXml($arXml);
		# build result
		$arResult = array(
			'TYPE' => 'XML',
			'DATA' => $strXml,
			'CURRENCY' => '',
			'SECTION_ID' => static::getElement_SectionID($intProfileID, $arElement),
			'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
			'DATA_MORE' => array(),
		);
		foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoResult') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
		}
		# after..
		unset($intProfileID, $intElementID, $arXmlTags, $arXml);
		return $arResult;
	}
	
}

?>