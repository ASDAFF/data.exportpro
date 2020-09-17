<?
namespace Acrit\Core\Export;

use \Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

return [
	'NAME' => Helper::getMessage('ACRIT_CORE_TAB_GENERAL_GROUP_ACRITMENU'),
	'OPTIONS' => [
		'acritmenu_group_name' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_ACRITMENU_GROUP_NAME'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_ACRITMENU_GROUP_NAME_HINT'),
			'TYPE' => 'text',
			'ATTR' => 'onchange="$(\'input[name=ACRITMENU_GROUPNAME]\').val($(this).val())" size="30" placeholder="'.htmlspecialcharsbx(Helper::getMessage('ACRITMENU_GROUP_NAME_DEFAULT')).'"',
		],
		'acritmenu_group_sort' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_ACRITMENU_GROUP_SORT'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_ACRITMENU_GROUP_SORT_HINT'),
			'TYPE' => 'text',
			'ATTR' => 'onchange="$(\'input[name=ACRITMENU_GROUPNAME]\').val($(this).val())" size="20" placeholder="150"',
		],
		'acritmenu_group_image' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_ACRITMENU_GROUP_IMAGE'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_ACRITMENU_GROUP_IMAGE_HINT'),
			'TYPE' => 'text',
			'ATTR' => 'onchange="$(\'input[name=ACRITMENU_GROUPNAME]\').val($(this).val())" size="50" placeholder="/bitrix/themes/.default/images/acrit.core/acrit.png"',
		],
	],
];
?>