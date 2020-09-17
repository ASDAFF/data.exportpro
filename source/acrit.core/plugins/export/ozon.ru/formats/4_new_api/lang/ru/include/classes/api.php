<?
\Acrit\Core\Export\Exporter::getLangPrefix(realpath(__DIR__.'/../../../class.php'), $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'ERROR_GENERAL'] = 'Ошибка при выполнении команды #COMMAND#.';
	$MESS[$strLang.'ERROR_GENERAL_DEBUG'] = $MESS[$strLang.'ERROR_GENERAL'].PHP_EOL.'Json: #JSON#';
$MESS[$strLang.'ERROR_REQUEST'] = 'Ошибка выполнения запроса для команды #COMMAND#.';
	$MESS[$strLang.'ERROR_REQUEST_DEBUG'] = $MESS[$strLang.'ERROR_REQUEST'].PHP_EOL.'Json: #JSON#'.PHP_EOL.'Response: #RESPONSE#';



?>