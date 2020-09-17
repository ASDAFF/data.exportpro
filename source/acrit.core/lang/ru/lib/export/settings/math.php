<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_MATH_';

$MESS[$strMessPrefix.'NAME'] = 'Использовать как математическую формулу';
$MESS[$strMessPrefix.'DESC'] = 'Отметьте галочку, если выражение представляет собой математическую формулу, например:<br/><code>{=fields.ID}+{properties.SUMM}+10</code><br/><br/>Если используется для всего поля, необходимо следить, чтобы между значениями имелись операторы (напр., выполнять преобразование множественного в строковое с разделителем в виде символа <b>+</b>).';
$MESS[$strMessPrefix.'EVAL_NAME'] = 'Использовать php-функцию <code><b>eval()</b></code>';
$MESS[$strMessPrefix.'EVAL_DESC'] = '';
$MESS[$strMessPrefix.'ERROR'] = 'Ошибка расчета значения выражения «#VALUE#»: #ERROR#.';
?>