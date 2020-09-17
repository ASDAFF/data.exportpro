<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Cli;

Loc::loadMessages(__FILE__);

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_OLD_CORE'),
	'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_OLD_CORE_HINT'),
	'OPTIONS' => [
		'disable_old_core' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DISABLE_OLD_CORE'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DISABLE_OLD_CORE_HINT'),
			'TYPE' => 'checkbox',
			'CALLBACK_SAVE' => function($obOptions, $arOption){
				if($arOption['VALUE_NEW'] == 'Y'){
					$resAgents = \CAgent::getList([], ['MODULE_ID' => $obOptions->strModuleId]);
					while($arAgent = $resAgents->getNext(false, false)){
						if(preg_match('#^C([\w]+)Agent::StartExport\([\d\s,]+\);?$#i', $arAgent['NAME'], $arMatch)){
							\CAgent::removeAgent($arAgent['NAME'], $obOptions->strModuleId);
						}
					}
					unset($resAgents, $arAgent);
				}
			},
		],
	],
];
?>