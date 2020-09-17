<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_REPLACE_';

$MESS[$strMessPrefix.'NAME'] = 'Замены в тексте';
$MESS[$strMessPrefix.'DESC'] = 'Укажите необходимые текстовые замены.<br/>
<p>Галочка «Регистр» обозначает учет регистра текста при замене (т.е. при включенной галочке «АБВ» не заменится, если будет задано «абв»).</p>
<p>Галочка «RegExp» (доступна только в старшей редакции модуля) обозначает использование регулярных выражений, что дает широкие возможности по замене текста. При выборе данной опции появляется поле для указания <a href="http://php.net/manual/ru/reference.pcre.pattern.modifiers.php" target="_blank">модификаторов регулярных выражений</a>.</p>
<p><b>Внимание!</b> При снятой галочке «RegExp» и установленной галочке «Регистр» замена осуществляется php-функцией <code>str_ireplace</code>, которая имеет баг: не работает замена в случае поиска одной русской буквы (напр., поиск «абв» будет работать, «а» - нет).</p>
<p>Пример замены с использование регулярного выражения (заменяет число в миллиметрах на сантиметры с добавением "cm"):<br/>
что ищем:<br/>
<code>^([\d]*)([\d])$</code><br/>
на что заменяем:<br/>
<code>$1.$2 cm</code><br/>
</p>
';
$MESS[$strMessPrefix.'GROUP'] = 'Замены в тексте';

$MESS[$strMessPrefix.'FROM'] = 'что ищем?';
$MESS[$strMessPrefix.'TO'] = 'на что заменяем?';
$MESS[$strMessPrefix.'USE_REGEXP'] = 'RegExp';
$MESS[$strMessPrefix.'USE_REGEXP_HINT'] = 'Отметьте галочку, если необходимо использовать регулярные выражения при замене';
$MESS[$strMessPrefix.'MODIFIER'] = 'Модиф.';
$MESS[$strMessPrefix.'MODIFIER_HINT'] = 'Модификаторы для RegExp';
$MESS[$strMessPrefix.'CASE_SENSITIVE'] = 'Регистр';
$MESS[$strMessPrefix.'CASE_SENSITIVE_HINT'] = 'Отметьте галочку, если необходимо учитывать регистр при поиске текста';
$MESS[$strMessPrefix.'ADD'] = 'Добавить';
$MESS[$strMessPrefix.'NOTHING'] = 'Пока не назначены';
$MESS[$strMessPrefix.'DELETE_HINT'] = 'Нажмите чтобы удалить запись';
?>