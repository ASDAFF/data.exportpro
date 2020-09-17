<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_ENTITY_DECODE_';

$MESS[$strMessPrefix.'NAME'] = 'Преобразовать HTML-сущности';
$MESS[$strMessPrefix.'DESC'] = 'Отметьте данную опцию чтобы автоматически заменять HTML-сущности на соответствующие символы. К примеру, преобразуется:
<ul>
	<li><code>&amp;nbsp;</code> - в неразрывный пробел</li>
	<li><code>&amp;laquo;</code> - в символ «</li>
	<li><code>&amp;raquo;</code> - в символ »</li>
	<li><code>&amp;copy;</code> - в символ ©</li>
	<li>и т.д.</li>
</ul>
Это полезно при выгрузке в XML, т.к. наличие этих символов приводит к ошибке разбора XML.';
$MESS[$strMessPrefix.'REMOVE_AMPERSANDS'] = 'Также удалить все сммволы &amp;';
$MESS[$strMessPrefix.'REMOVE_AMPERSANDS_HINT'] = 'Отметьте опцию, если после работы опции необходимо удалить все оставшиеся символы &amp; (т.е. те, которые не относятся к html-сущностям, т.к. все html-сущности будут заменены заранее данной опцией).';
?>