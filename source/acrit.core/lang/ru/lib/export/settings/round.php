<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_ROUND_';

$MESS[$strMessPrefix.'NAME'] = 'Округление';
$MESS[$strMessPrefix.'DESC'] = 'Отметьте галочку, если необходиммо производить округление числового значения.<br/>
<p>Округление возможно следующими способами:</p>
<ul>
<li>математически - по правилам математики [используется php-функция <code>round</code>],</li>
<li>в меньшую сторону [используется php-функция <code>floor</code>],</li>
<li>в большую сторону [используется php-функция <code>ceil</code>].</li>
</ul>
';

$MESS[$strMessPrefix.'TYPE_RULES_BX'] = 'по правилам округления';
$MESS[$strMessPrefix.'TYPE_MATH'] = 'математически';
$MESS[$strMessPrefix.'TYPE_LOWER'] = 'в меньшую сторону';
$MESS[$strMessPrefix.'TYPE_UPPER'] = 'в большую сторону';

$MESS[$strMessPrefix.'PRECISION_MINUS_3'] = 'До тысяч';
$MESS[$strMessPrefix.'PRECISION_MINUS_2'] = 'До сотен';
$MESS[$strMessPrefix.'PRECISION_MINUS_1'] = 'До десятков';
$MESS[$strMessPrefix.'PRECISION_0'] = 'До целого числа';
$MESS[$strMessPrefix.'PRECISION_1'] = 'До десятых (0.1)';
$MESS[$strMessPrefix.'PRECISION_2'] = 'До сотых (0.01)';
$MESS[$strMessPrefix.'PRECISION_3'] = 'До тысячных (0.001)';
?>