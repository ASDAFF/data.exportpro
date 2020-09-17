<?
/**
 *  Acrit Core: OZON.RU plugin
 * 	@documentation https://cb-api.ozonru.me/apiref/ru/
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
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition;

Loc::loadMessages(__FILE__);

class OzonRu extends Plugin
{

	CONST DATE_UPDATED = '2019-03-28';

	protected $strFileExt;

	public function __construct($strModuleId)
	{
		parent::__construct($strModuleId);
	}

	public static function getCode()
	{
		return 'OZON_RU';
	}

	public static function getName()
	{
		return static::getMessage('NAME');
	}

	public function getSupportedCurrencies()
	{
		return array('RUB');
	}

	protected function setAvailableExtension($strExtension)
	{
		$this->strFileExt = $strExtension;
	}

	/* END OF BASE STATIC METHODS */

	public function getFields($intProfileID, $intIBlockID, $bAdmin = false)
	{

	}

	public function processElement($arProfile, $intIBlockID, $arElement, $arFields)
	{
		// basically [in this class] do nothing, all business logic are in each format
	}

	public function showResults($arSession)
	{
		ob_start();
		$intTime = $arSession['TIME_FINISHED'] - $arSession['TIME_START'];
		if ($intTime <= 0)
		{
			$intTime = 1;
		}
		?>
		<div><?= static::getMessage('RESULT_GENERATED'); ?>: <?= IntVal($arSession['GENERATE']['INDEX']); ?></div>
		<div><?= static::getMessage('RESULT_EXPORTED'); ?>: <?= IntVal($arSession['EXPORT']['INDEX']); ?></div>
		<? if ($arSession['EXPORT']['EXPORT_FILE_SIZE_XML']): ?>
			<div><?= static::getMessage('RESULT_XML_SIZE'); ?>: <?= Helper::formatSize($arSession['EXPORT']['EXPORT_FILE_SIZE_XML']); ?></div>
		<? endif ?>
		<div><?= static::getMessage('RESULT_ELAPSED_TIME'); ?>: <?= Helper::formatElapsedTime($intTime); ?></div>
		<div><?= static::getMessage('RESULT_DATETIME'); ?>: <?= (new \Bitrix\Main\Type\DateTime())->toString(); ?></div>
		<? if (strlen($arSession['EXPORT']['XML_FILE_URL'])): ?>
			<a href="<?= $arSession['EXPORT']['XML_FILE_URL']; ?>" target="_blank"><?= static::getMessage('RESULT_FILE_URL'); ?></a>
		<? endif ?>
		<?
		return Helper::showSuccess(ob_get_clean());
	}

	/**
	 * 	Is plugin has own categories (it is optional)
	 */
	public function hasCategoryList()
	{ // static ot not?
		return true;
	}
   /**
   *	Show notices
   */
   public function showMessages(){
      print Helper::showNote(static::getMessage('NOTICE_SUPPORT'), true);
   }
}
?>