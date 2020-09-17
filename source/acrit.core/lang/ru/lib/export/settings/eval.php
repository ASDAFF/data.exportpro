<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_EVAL_';

$MESS[$strMessPrefix.'NAME'] = 'Произвольный код';
$MESS[$strMessPrefix.'DESC'] = 'Опция позволяет выполнить произвольный код при обработке значений. Таким образом, имеется возможность реализации собственной логики выгрузки любых полей. Доступны следующие переменные:
<ul>
	<li><b><code>$strValue</code></b> - текущее значение,</li>
	<li><b><code>$strFieldCode</code></b> - код свойства,</li>
	<li><b><code>$intProfileId</code></b> - ID профиля,</li>
	<li><b><code>$intIBlockId</code></b> ID инфоблока,</li>
	<li><b><code>$intElementId</code></b> - ID товара/предложения.</li>
</ul>
Итоговое значение будет тем, что выведет данный код (т.е. необходимо использовать echo/print).
';
?>