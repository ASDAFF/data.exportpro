<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_DYNAMIC_REMARKETING'),
	'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_DYNAMIC_REMARKETING_HINT'),
	'OPTIONS' => [
		'dynamic_remarketing_google' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DYNAMIC_REMARKETING_GOOGLE'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DYNAMIC_REMARKETING_GOOGLE_HINT'),
			'ATTR' => 'cols="50" rows="4" style="max-height:100px; min-height:36px; resize:vertical; width:96%; -webkit-box-sizing:border-box; -moz-box-sizing:border-box; box-sizing:border-box;"',
			'TYPE' => 'textarea',
		],
		'dynamic_remarketing_mailru' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DYNAMIC_REMARKETING_MAILRU'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DYNAMIC_REMARKETING_MAILRU_HINT'),
			'ATTR' => 'cols="50" rows="4" style="max-height:100px; min-height:36px; resize:vertical; width:96%; -webkit-box-sizing:border-box; -moz-box-sizing:border-box; box-sizing:border-box;"',
			'TYPE' => 'textarea',
		],
	],
];
?>