<?
$strMessPrefix = 'ACRIT_EXP_AVITO_BUSINESS_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Для бизнеса)';

// Fields
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория объявлений';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория товара — одно из значений списка:<br/>
<ul>
	<li>Готовый бизнес</li>
	<li>Оборудование для бизнеса</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_NAME'] = 'Вид товара';
	$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_DESC'] = 'Вид товара — одно из значений списка (отдельно для каждой категории):<br/>
<ul>
	<li><b>Готовый бизнес:</b>
		<ul>
			<li>Интернет-магазин</li>
			<li>Общественное питание</li>
			<li>Производство</li>
			<li>Развлечения</li>
			<li>Сельское хозяйство</li>
			<li>Строительство</li>
			<li>Сфера услуг</li>
			<li>Торговля</li>
			<li>Другое</li>
		</ul>
	</li>
	<li><b>Оборудование для бизнеса:</b>
		<ul>
			<li>Для магазина</li>
			<li>Для офиса</li>
			<li>Для ресторана</li>
			<li>Для салона красоты</li>
			<li>Промышленное</li>
			<li>Другое</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Название объявления';
	$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Название объявления — строка до 50 символов.<br/>
Примечание: не пишите в название цену и контактную информацию — для этого есть отдельные поля — и не используйте слово «продам».';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена в рублях';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена в рублях — целое число. ';
?>