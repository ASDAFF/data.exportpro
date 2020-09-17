<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_HTML2TEXT_';

$MESS[$strMessPrefix.'NAME'] = 'Преобразовать HTML в текст';
$MESS[$strMessPrefix.'DESC'] = 'Преобразование HTML-содержимого в текст (удаление тегов с сохранением текста в них).<br/>
<p>Данный параметр полезен в случаях, когда имеется текст в формате HTML (с форматированием), но выгружать его нужно без форматирования.</p>
<p>Доступны следующие режимы:</p>
<ul>
	<li><b>Стандартный режим</b> - логика преобразования управляется модулем (используется php-функция strip_tags с небольшими улучшениями),</li>
	<li><b>Битрикс режим</b> - преобразование основано на штатной функции HTMLToTxt,</li>
	<li><b>Html2text</b> - используется внешняя библиотека <a href="https://github.com/soundasleep/html2text" target="_blank">html2text</a>,</li>
	<li><b>Собственный обработчик</b> - будет использован собственный обработчик «OnCustomHtmlToText» (разместите его подключение в файле init.php):<br/>
		<pre>
AddEventHandler("'.$GLOBALS['strModuleId'].'", "OnCustomHtmlToText", "MyOnCustomHtmlToText");<br/>
function MyOnCustomHtmlToText(&$mValue, $arParams, $obField){<br/>
	$intProfileID = $obField->getProfileID();<br/>
	if($intProfileID == 5) {<br/>
		$mValue = strip_tags($mValue);<br/>
	}<br/>
	else {<br/>
		$mValue = null;<br/>
	}<br/>
}
		</pre>
		Если значение станет <b>null</b>, то будет автоматически применен стандартный режим.
	</li>
</ul>
';
$MESS[$strMessPrefix.'TYPE_SIMPLE'] = 'Стандартный режим';
$MESS[$strMessPrefix.'TYPE_BITRIX'] = 'Битрикс-режим';
$MESS[$strMessPrefix.'TYPE_HTML2TEXT'] = 'Html2text';
$MESS[$strMessPrefix.'TYPE_CUSTOM'] = 'Собственный обработчик';
?>