<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_MISC'),
	'OPTIONS' => [
		'check_lock' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CHECK_LOCK'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_CHECK_LOCK_HINT'),
			'TYPE' => 'checkbox',
		],
		'delete_element_data_while_exports' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DELETE_ELEMENT_DATA_WHILE_EXPORTS'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DELETE_ELEMENT_DATA_WHILE_EXPORTS_HINT'),
			'TYPE' => 'checkbox',
		],
		'show_export_file_with_uniq_argument' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_EXPORT_FILE_WITH_UNIQ_ARGUMENT'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_EXPORT_FILE_WITH_UNIQ_ARGUMENT_HINT'),
			'TYPE' => 'checkbox',
		],
		'show_iblock_multiple_notice' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_IBLOCK_MULTIPLE_NOTICE'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_IBLOCK_MULTIPLE_NOTICE_HINT'),
			'TYPE' => 'checkbox',
		],
		'categories_depth' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CATEGORIES_DEPTH'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_CATEGORIES_DEPTH_HINT'),
			'TYPE' => 'text',
			'ATTR' => 'MAXLENGTH="1"',
		],
		'history_count' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_HISTORY_COUNT'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_HISTORY_COUNT_HINT'),
			'TYPE' => 'text',
			'ATTR' => 'MAXLENGTH="6"',
		],
	],
];
	
?>