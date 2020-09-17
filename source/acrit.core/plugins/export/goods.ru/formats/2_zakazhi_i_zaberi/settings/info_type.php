<?
$arType = [
	'full' => static::getMessage('SETTINGS_NAME_INFO_TYPE_FULL'),
	'diff' => static::getMessage('SETTINGS_NAME_INFO_TYPE_DIFF'),
];
$arType = [
	'REFERENCE' => array_values($arType),
	'REFERENCE_ID' => array_keys($arType),
];
print selectBoxFromArray('PROFILE[PARAMS][INFO_TYPE]', $arType, $this->arParams['INFO_TYPE']);
?>