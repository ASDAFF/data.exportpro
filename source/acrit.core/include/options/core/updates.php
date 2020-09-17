<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_UPDATES'),
	'OPTIONS' => [
		'check_updates' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DYNAMIC_UPDATES'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DYNAMIC_UPDATES_DESC'),
			'TYPE' => 'checkbox',
		],
		'check_updates_regular' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DYNAMIC_UPDATES_REGULAR'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DYNAMIC_UPDATES_REGULAR_DESC'),
			'TYPE' => 'checkbox',
			'CALLBACK_SAVE' => function($obOptions, $arOption){
				\Acrit\Core\Helper::setOption(ACRIT_CORE, 'check_updates_last_time', false);
			},
		],
	],
];
?>