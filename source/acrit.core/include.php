<?
namespace Acrit\Core;

use
	\Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Bitrix\Main\Application,
	\Bitrix\Main\Config\Option;

define('ACRIT_CORE', 'acrit.core');
IncludeModuleLangFile(__FILE__);

$arAutoload = [
	# General
	'Acrit\Core\Helper' => 'lib/helper.php',
	'Acrit\Core\Cli' => 'lib/cli.php',
	'Acrit\Core\DiscountRecalculation' => 'lib/discountrecalculation.php',
	'Acrit\Core\DynamicRemarketing' => 'lib/dynamicremarketing.php',
	'Acrit\Core\EventHandler' => 'lib/eventhandler.php',
	'Acrit\Core\GoogleTagManager' => 'lib/googletagmanager.php',
	'Acrit\Core\HttpRequest' => 'lib/httprequest.php',
	'Acrit\Core\Json' => 'lib/json.php',
	'Acrit\Core\Log' => 'lib/log.php',
	'Acrit\Core\Options' => 'lib/options.php',
	'Acrit\Core\Thread' => 'lib/thread.php',
	'Acrit\Core\Xml' => 'lib/xml.php',
	'Acrit\Core\Update' => 'lib/update.php',
	/*** EXPORT ***/
	# CurrencyConverter
	'Acrit\Core\Export\CurrencyConverter\Base' => 'lib/export/currencyconverter/base.php',
	# Field
	'Acrit\Core\Export\Field\Field' => 'lib/export/field/field.php',
	'Acrit\Core\Export\Field\ValueBase' => 'lib/export/field/valuebase.php',
	'Acrit\Core\Export\Field\ValueSimple' => 'lib/export/field/valuesimple.php',
	'Acrit\Core\Export\Field\ValueCondition' => 'lib/export/field/valuecondition.php',
	# Migrator
	'Acrit\Core\Export\Migrator\Manager' => 'lib/export/migrator/manager.php',
	'Acrit\Core\Export\Migrator\Base' => 'lib/export/migrator/base.php',
	'Acrit\Core\Export\Migrator\FilterConverter' => 'lib/export/migrator/filter_converter.php',
	# Settings
	'Acrit\Core\Export\Settings\SettingsBase' => 'lib/export/settings/base.php',
	# Other
	'Acrit\Core\Export\AdditionalFieldTable' => 'lib/export/additionalfield.php',
	'Acrit\Core\Export\Backup' => 'lib/export/backup.php',
	'Acrit\Core\Export\CategoryCustomNameTable' => 'lib/export/categorycustomname.php',
	'Acrit\Core\Export\CategoryRedefinitionTable' => 'lib/export/categoryredefinition.php',
	'Acrit\Core\Export\Debug' => 'lib/export/debug.php',
	'Acrit\Core\Export\EventHandlerExport' => 'lib/export/eventhandler.php',
	'Acrit\Core\Export\ExportDataTable' => 'lib/export/exportdata.php',
	'Acrit\Core\Export\Exporter' => 'lib/export/exporter.php',
	'Acrit\Core\Export\ExternalIdTable' => 'lib/export/externalid.php',
	'Acrit\Core\Export\Filter' => 'lib/export/filter.php',
	'Acrit\Core\Export\HistoryTable' => 'lib/export/history.php',
	'Acrit\Core\Export\IBlockElementSubQuery' => 'lib/export/iblockelementsubquery.php',
	'Acrit\Core\Export\PluginManager' => 'lib/export/pluginmanager.php',
	'Acrit\Core\Export\Plugin' => 'lib/export/plugin.php',
	'Acrit\Core\Export\UniversalPlugin' => 'lib/export/universalplugin.php',
	'Acrit\Core\Export\ProfileTable' => 'lib/export/profile.php',
	'Acrit\Core\Export\ProfileFieldTable' => 'lib/export/profilefield.php',
	'Acrit\Core\Export\ProfileFieldFeature' => 'lib/export/profilefieldfeature.php',
	'Acrit\Core\Export\ProfileIBlockTable' => 'lib/export/profileiblock.php',
	'Acrit\Core\Export\ProfileValueTable' => 'lib/export/profilevalue.php',
	/*** CRM INTEGRATION ***/
	'Acrit\Core\Crm\ProfilesTable' => 'lib/crm/profiles.php',
];
\Bitrix\Main\Loader::registerAutoLoadClasses(ACRIT_CORE, $arAutoload);
$GLOBALS['ACRIT_CORE_AUTOLOAD_CLASSES'] = &$arAutoload;

# Antiroot
if(Helper::getOption(ACRIT_CORE, 'warn_if_root') != 'N'){
	if(Cli::isCli() && Cli::isRoot()){
		Helper::obRestart();
		$strMessage = 'This script cannot be run in root mode.';
		Log::getInstance(ACRIT_CORE)->add($strMessage.' ['.implode(' ', $_SERVER['argv']).']');
		print $strMessage.PHP_EOL;
		Helper::addNotify(ACRIT_CORE, Helper::getMessage('ACRIT_CORE_ROOT_NOTIFY', [
			'#DATETIME#' => date(\CDatabase::dateFormatToPhp(FORMAT_DATETIME)),
			'#SCRIPT_NAME#' => is_array($_SERVER['argv']) ? implode(' ', $_SERVER['argv']) : $_SERVER['SCRIPT_NAME'],
			'#LANGUAGE_ID#' => LANGUAGE_ID,
		]), 'ROOT_NOTIFY');
		die();
	}
}

/*
# JS: Log
\CJSCore::registerExt(
	'acrit-core-log',
	array(
		'js' => '/bitrix/js/'.ACRIT_CORE.'/log.js',
	)
);
# JS: updater for each module
\CJSCore::registerExt(
	'acrit-core-update-module',
	array(
		'js' => '/bitrix/js/'.ACRIT_CORE.'/check_updates.js',
	)
);
*/

?>