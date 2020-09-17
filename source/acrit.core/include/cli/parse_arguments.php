<?
$arArguments = [];
if(is_array($argv)){
	foreach(array_slice($argv, 1) as $strArgument){
		parse_str($strArgument, $arArgument);
		if(is_array($arArgument)) {
			$arArguments = array_merge($arArguments, $arArgument);
		}
	}
}
return $arArguments;