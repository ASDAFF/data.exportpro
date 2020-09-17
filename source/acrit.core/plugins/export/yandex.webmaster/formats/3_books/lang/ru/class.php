<?
$strMessPrefix = 'ACRIT_EXP_YANDEX_WEBMASTER_BOOKS_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Яндекс.Вебмастер (книги)';

// Fields
$MESS[$strMessPrefix.'FIELD_NAME_NAME'] = 'Название предложения';
	$MESS[$strMessPrefix.'FIELD_NAME_DESC'] = 'Название предложения.';
$MESS[$strMessPrefix.'FIELD_PUBLISHER_NAME'] = 'Издательство';
	$MESS[$strMessPrefix.'FIELD_PUBLISHER_DESC'] = 'Издательство.';
$MESS[$strMessPrefix.'FIELD_ISBN_NAME'] = 'ISBN';
	$MESS[$strMessPrefix.'FIELD_ISBN_DESC'] = 'International Standard Book Number — международный уникальный номер книжного издания. Если их несколько, укажите все через запятую.<br/><br/>Форматы ISBN и SBN проверяются на корректность. Валидация кодов происходит не только по длине, также проверяется контрольная цифра (check-digit) — последняя цифра кода должна согласовываться с остальными цифрами по определенной формуле. При разбиении ISBN на части при помощи дефиса (например, 978-5-94878-004-7) код проверяется на соответствие дополнительным требованиям к количеству цифр в каждой из частей.';
$MESS[$strMessPrefix.'FIELD_AUTHOR_NAME'] = 'Автор';
	$MESS[$strMessPrefix.'FIELD_AUTHOR_DESC'] = 'Автор произведения.';
$MESS[$strMessPrefix.'FIELD_SERIES_NAME'] = 'Серия';
	$MESS[$strMessPrefix.'FIELD_SERIES_DESC'] = 'Серия.';
$MESS[$strMessPrefix.'FIELD_YEAR_NAME'] = 'Год издания';
	$MESS[$strMessPrefix.'FIELD_YEAR_DESC'] = 'Год издания.';
$MESS[$strMessPrefix.'FIELD_VOLUME_NAME'] = 'Общее количество томов';
	$MESS[$strMessPrefix.'FIELD_VOLUME_DESC'] = 'Общее количество томов, если издание состоит из нескольких томов.';
$MESS[$strMessPrefix.'FIELD_PART_NAME'] = 'Номер тома';
	$MESS[$strMessPrefix.'FIELD_PART_DESC'] = 'Укажите номер тома, если издание состоит из нескольких томов.';
$MESS[$strMessPrefix.'FIELD_LANGUAGE_NAME'] = 'Язык';
	$MESS[$strMessPrefix.'FIELD_LANGUAGE_DESC'] = 'Язык, на котором издано произведение.';
$MESS[$strMessPrefix.'FIELD_TABLE_OF_CONTENTS_NAME'] = 'Оглавление';
	$MESS[$strMessPrefix.'FIELD_TABLE_OF_CONTENTS_DESC'] = 'Оглавление.';
$MESS[$strMessPrefix.'FIELD_BINDING_NAME'] = 'Формат';
	$MESS[$strMessPrefix.'FIELD_BINDING_DESC'] = 'Формат.';
$MESS[$strMessPrefix.'FIELD_PAGE_EXTENT_NAME'] = 'Количество страниц';
	$MESS[$strMessPrefix.'FIELD_PAGE_EXTENT_DESC'] = 'Количество страниц в книге, должно быть целым положительным числом.';

?>