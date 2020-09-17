<?
namespace Acrit\Core;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_LOG'),
	'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_LOG_HINT'),
	'OPTIONS' => [
		'log_write' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_LOG_WRITE'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_LOG_WRITE_HINT'),
			'TYPE' => 'checkbox',
		],
		'debug_mode' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DEBUG_MODE'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DEBUG_MODE_HINT'),
			'TYPE' => 'checkbox',
		],
		'log_max_size' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_LOG_MAX_SIZE'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_LOG_MAX_SIZE_HINT'),
			'TYPE' => 'text',
			'ATTR' => 'size="15" maxlength="10"',
			'CALLBACK_BEFORE_SAVE' => function($obOptions, &$strValue, $arOption){
				if($strValue < 1){
					$strValue = 1;
				}
			}
		],
	],
];
?>