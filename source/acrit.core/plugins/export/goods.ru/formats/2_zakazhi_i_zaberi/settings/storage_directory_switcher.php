<?
$arType = [
	'internal' => static::getMessage('SETTINGS_NAME_STORAGE_DIRECTORY_SWITCHER_INTERNAL'),
	'external' => static::getMessage('SETTINGS_NAME_STORAGE_DIRECTORY_SWITCHER_EXTERNAL'),
];
$arType = [
	'REFERENCE' => array_values($arType),
	'REFERENCE_ID' => array_keys($arType),
];
print selectBoxFromArray('PROFILE[PARAMS][STORAGE_DIRECTORY_SWITCHER]', $arType, 
	$this->arParams['STORAGE_DIRECTORY_SWITCHER'], '', 'data-role="acrit-exp-goods-json-storage-switcher"');
?>
