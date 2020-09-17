<?
$strMessPrefix = 'ACRIT_EXP_YANDEX_MARKET_AUDIOBOOKS_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Яндекс.Маркет (аудиокниги)';

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
$MESS[$strMessPrefix.'FIELD_PERFORMED_BY_NAME'] = 'Исполнитель';
	$MESS[$strMessPrefix.'FIELD_PERFORMED_BY_DESC'] = 'Исполнитель. Если их несколько, перечисляются через запятую.';
$MESS[$strMessPrefix.'FIELD_PERFORMANCE_TYPE_NAME'] = 'Тип аудиокниги';
	$MESS[$strMessPrefix.'FIELD_PERFORMANCE_TYPE_DESC'] = 'Тип аудиокниги (радиоспектакль, «произведение начитано» и т. п.).';
$MESS[$strMessPrefix.'FIELD_STORAGE_NAME'] = 'Носитель';
	$MESS[$strMessPrefix.'FIELD_STORAGE_DESC'] = 'Носитель аудиокниги.';
$MESS[$strMessPrefix.'FIELD_FORMAT_NAME'] = 'Формат';
	$MESS[$strMessPrefix.'FIELD_FORMAT_DESC'] = 'Формат аудиокниги.';
$MESS[$strMessPrefix.'FIELD_RECORDING_LENGTH_NAME'] = 'Время звучания';
	$MESS[$strMessPrefix.'FIELD_RECORDING_LENGTH_DESC'] = 'Время звучания, задается в формате mm.ss (минуты.секунды).';

?>