<?
/**
 * Acrit Core: Facebook base plugin
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
		\Acrit\Core\Helper,
		\Acrit\Core\Export\Plugin,
		\Acrit\Core\Export\Filter;

Loc::loadMessages(__FILE__);

class Facebook extends Plugin
{

	CONST DATE_UPDATED = '2018-08-23';

	protected $strFileExt;

	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId)
	{
		parent::__construct($strModuleId);
	}

	/* START OF BASE STATIC METHODS */

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode()
	{
		return 'FACEBOOK';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName()
	{
		return static::getMessage('NAME');
	}

	/**
	 * 	Get list of supported currencies
	 */
	public function getSupportedCurrencies()
	{
		return array('RUB');
	}

	/* END OF BASE STATIC METHODS */

	/**
	 * 	Show plugin settings
	 */
	public function showSettings()
	{

		return $this->showDefaultSettings();
	}

	/**
	 * 	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin = false)
	{
		return [];
	}

	/**
	 * 	Process single element
	 * 	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields)
	{
		// basically [in this class] do nothing, all business logic are in each format
	}

	/**
	 * 	Show results
	 */
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
		<div><?= static::getMessage('RESULT_XML_SIZE'); ?>: <?= Helper::formatSize($arSession['EXPORT']['EXPORT_FILE_SIZE_XML']); ?></div>
		<div><?= static::getMessage('RESULT_ELAPSED_TIME'); ?>: <?= Helper::formatElapsedTime($intTime); ?></div>
		<div><?= static::getMessage('RESULT_DATETIME'); ?>: <?= (new \Bitrix\Main\Type\DateTime())->toString(); ?></div>
		<? if (strlen($arSession['EXPORT']['XML_FILE_URL'])): ?>
			<a href="<?= $arSession['EXPORT']['XML_FILE_URL']; ?>" target="_blank"><?= static::getMessage('RESULT_FILE_URL'); ?></a>
		<? endif ?>
		<?
		return Helper::showSuccess(ob_get_clean());
	}

	/* START OF BASE METHODS FOR XML SUBCLASSES */



	/**
	 *
	 */
	/* END OF BASE METHODS FOR XML SUBCLASSES */
}
?>