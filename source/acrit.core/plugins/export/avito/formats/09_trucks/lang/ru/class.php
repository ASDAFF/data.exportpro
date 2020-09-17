<?
$strMessPrefix = 'ACRIT_EXP_AVITO_TRUCKS_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Грузовики и спецтехника)';

// Fields
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория товара';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория товара — строка «Грузовики и спецтехника»';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DEFAULT'] = 'Грузовики и спецтехника';
$MESS[$strMessPrefix.'FIELD_CONDITION_NAME'] = 'Состояние';
	$MESS[$strMessPrefix.'FIELD_CONDITION_DESC'] = 'Состояние вещи — одно из значений списка:<br/>
<ul>
	<li>Новое</li>
	<li>Б/у</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_KILOMETRAGE_NAME'] = 'Пробег';
	$MESS[$strMessPrefix.'FIELD_KILOMETRAGE_DESC'] = 'Пробег транспортного средства в км - целое число, в диапазоне от 1 до 1000000';
$MESS[$strMessPrefix.'FIELD_TECHNICAL_PASSPORT_NAME'] = 'Наличие ПТС';
	$MESS[$strMessPrefix.'FIELD_TECHNICAL_PASSPORT_DESC'] = 'Наличие паспорта транспортного средства (ПТС) - одно из значений списка:
<ul>
	<li>Нет</li>
	<li>В наличии</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ENGINE_HOURS_NAME'] = 'Моточасы';
	$MESS[$strMessPrefix.'FIELD_ENGINE_HOURS_DESC'] = 'Моточасы транспортного средства - целое число, в диапазоне от 1 до 1000000';
$MESS[$strMessPrefix.'FIELD_VIN_NAME'] = 'VIN-номер';
	$MESS[$strMessPrefix.'FIELD_VIN_DESC'] = 'VIN-номер (vehicle identification number) — строка из 17 символов.';
$MESS[$strMessPrefix.'FIELD_MAKE_NAME'] = 'Марка';
	$MESS[$strMessPrefix.'FIELD_MAKE_DESC'] = 'Марка - марка транспортного средства, текстовое значение из <a href="https://autoload.avito.ru/format/truck_catalog.xml" target="_blank">Справочника</a> (Brand).';
$MESS[$strMessPrefix.'FIELD_MODEL_NAME'] = 'Модель';
	$MESS[$strMessPrefix.'FIELD_MODEL_DESC'] = 'Модель - модель транспортного средства, текстовое значение из <a href="https://autoload.avito.ru/format/truck_catalog.xml" target="_blank">Справочника</a> (Model).';
$MESS[$strMessPrefix.'FIELD_BODY_TYPE_NAME'] = 'Тип кузова';
	$MESS[$strMessPrefix.'FIELD_BODY_TYPE_DESC'] = 'Тип кузова - тип кузова транспортного средства, текстовое значение из <a href="https://autoload.avito.ru/format/truck_catalog.xml" target="_blank">Справочника</a> (Modification).';

$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_NAME'] = 'Вид техники';
	$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_DESC'] = 'Вид техники — одно из значений списка:<br/>
<ul>
	<li>Автобусы<li>
	<li>Автодома<li>
	<li>Автокраны<li>
	<li>Бульдозеры<li>
	<li>Грузовики<li>
	<li>Коммунальная техника<li>
	<li>Лёгкий транспорт<li>
	<li>Погрузчики<li>
	<li>Прицепы<li>
	<li>Сельхозтехника<li>
	<li>Строительная техника<li>
	<li>Техника для лесозаготовки<li>
	<li>Тягачи<li>
	<li>Экскаваторы<li>
</ul>';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Название объявления';
	$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Название объявления — строка до 50 символов.<br/>
Примечание: не пишите в название цену и контактную информацию — для этого есть отдельные поля — и не используйте слово «продам».';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена в рублях';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена в рублях — целое число.';
?>