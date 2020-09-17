<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_COMMON'),
	'OPTIONS' => [
		'common_settings' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_COMMON_SETTINGS'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_COMMON_SETTINGS_HINT'),
			'TYPE' => 'clear',
			'CALLBACK_MORE' => function($arOption){
				return '
				<a href="/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid='.ACRIT_CORE.'">
					'.Helper::getMessage('ACRIT_CORE_OPTION_COMMON_SETTINGS_BUTTON').'
				</a>';
			},
		],
	],
];
	
?>