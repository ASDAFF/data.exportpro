<?
$strMessPrefix = 'ACRIT_EXP_AVITO_PARTS_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Запчасти и аксессуары)';

// Headers
$MESS[$strMessPrefix.'HEADER_TIRES'] = 'Параметры шин, дисков и колёс';

// Fields
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория товара';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория объявлений — строка: «Запчасти и аксессуары».';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DEFAULT'] = 'Запчасти и аксессуары';
$MESS[$strMessPrefix.'FIELD_TYPE_ID_NAME'] = 'Подкатегория товара';
	$MESS[$strMessPrefix.'FIELD_TYPE_ID_DESC'] = 'Подкатегория товара — цифровой идентификатор из <a href="http://autoload.avito.ru/format/zapchasti_i_aksessuary/#TypeId" target="_blank">списка</a>';
$MESS[$strMessPrefix.'FIELD_AD_TYPE_NAME'] = 'Вид объявления';
	$MESS[$strMessPrefix.'FIELD_AD_TYPE_DESC'] = 'Вид объявления — одно из значений списка:<br/>
<ul>
	<li>Товар приобретен на продажу</li>
	<li>Товар от производителя</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CONDITION_NAME'] = 'Состояние';
	$MESS[$strMessPrefix.'FIELD_CONDITION_DESC'] = 'Состояние — одно из значений списка:
<ul>
	<li>Новое</li>
	<li>Б/у</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OEM_NAME'] = 'Номер детали OEM';
	$MESS[$strMessPrefix.'FIELD_OEM_DESC'] = 'Строка до 50 символов (разрешены цифры, латиница и знак дефиса).<br/>
Элемент может быть использован в подкатегориях:
<ul>
	<li>Запчасти / Для автомобилей</li>
	<li>Запчасти / Для мототехники</li>
	<li>Запчасти / Для спецтехники</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OEM_NAME'] = 'Номер детали OEM';
	$MESS[$strMessPrefix.'FIELD_OEM_DESC'] = 'Номер детали OEM — строка до 50 символов (разрешены цифры, латиница и знак дефиса).<br/>
Элемент может быть использован в подкатегориях:
<ul>
	<li>Запчасти / Для автомобилей</li>
	<li>Запчасти / Для мототехники</li>
	<li>Запчасти / Для спецтехники</li>
</ul>';
	
$MESS[$strMessPrefix.'FIELD_RIM_DIAMETER_NAME'] = 'Диаметр, дюймы';
	$MESS[$strMessPrefix.'FIELD_RIM_DIAMETER_DESC'] = 'Диаметр, дюймы — десятичное число.';
$MESS[$strMessPrefix.'FIELD_TIRE_TYPE_NAME'] = 'Сезонность шин или колес';
	$MESS[$strMessPrefix.'FIELD_TIRE_TYPE_DESC'] = 'Сезонность шин или колес — одно из значений списка:
<ul>
	<li>Всесезонные</li>
	<li>Летние</li>
	<li>Зимние нешипованные</li>
	<li>Зимние шипованные</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_WHEEL_AXLE_NAME'] = 'Ось мотошины';
	$MESS[$strMessPrefix.'FIELD_WHEEL_AXLE_DESC'] = 'Ось мотошины — одно из значений списка:
<ul>
	<li>Задняя</li>
	<li>Любая</li>
	<li>Передняя</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_RIM_TYPE_NAME'] = 'Тип диска';
	$MESS[$strMessPrefix.'FIELD_RIM_TYPE_DESC'] = 'Тип диска — одно из значений списка (отдельно для каждой категории):
<ul>
<li>Шины, диски и колёса / Диски:
	<ul>
	<li>Кованые</li>
	<li>Литые</li>
	<li>Штампованные</li>
	<li>Спицованные</li>
	<li>Сборные</li>
	</ul>
</li>
<li>Шины, диски и колёса / Колёса:
<ul>
	<li>Кованые</li>
	<li>Литые</li>
	<li>Штампованные</li>
</ul>
</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_TIRE_SECTION_WIDTH_NAME'] = 'Ширина профиля шины';
	$MESS[$strMessPrefix.'FIELD_TIRE_SECTION_WIDTH_DESC'] = 'Ширина профиля шины — целое число.';
$MESS[$strMessPrefix.'FIELD_TIRE_ASPECT_RATIO_NAME'] = 'Высота профиля шины';
	$MESS[$strMessPrefix.'FIELD_TIRE_ASPECT_RATIO_DESC'] = 'Высота профиля шины — целое число.';
$MESS[$strMessPrefix.'FIELD_RIM_WIDTH_NAME'] = 'Ширина обода, дюймов';
	$MESS[$strMessPrefix.'FIELD_RIM_WIDTH_DESC'] = 'Ширина обода, дюймов — десятичное число.';
$MESS[$strMessPrefix.'FIELD_RIM_BOLTS_NAME'] = 'Количество отверстий под болты';
	$MESS[$strMessPrefix.'FIELD_RIM_BOLTS_DESC'] = 'Количество отверстий под болты — целое число.';
$MESS[$strMessPrefix.'FIELD_RIM_BOLTS_DIAMETER_NAME'] = 'Диаметр расположения отверстий под болты';
	$MESS[$strMessPrefix.'FIELD_RIM_BOLTS_DIAMETER_DESC'] = 'Диаметр расположения отверстий под болты — десятичное число.';
$MESS[$strMessPrefix.'FIELD_RIM_OFFSET_NAME'] = 'Вылет (ET)';
	$MESS[$strMessPrefix.'FIELD_RIM_OFFSET_DESC'] = 'Вылет (ET) — десятичное число.';
$MESS[$strMessPrefix.'FIELD_BRAND_NAME'] = 'Производитель шин';
	$MESS[$strMessPrefix.'FIELD_BRAND_DESC'] = 'Производитель шин - строка из списка:
Aeolus, Aeolus Neo, Altenzo, Amtel, Antares, Aplus, Autogrip, Avatyre, Barum, Bfgoodrich, Bridgestone, Cachland, Compasal, Continental, Contyre, Cordiant, CrossLeader, Delinte, Dmack, DoubleStar, Dunlop, Effiplus, Falken, Firenza, Firestone, Forward, General Tire, Gislaved, Goodyear, GT Radial, Habilead, Hankook, Hifly, Imperial, Jinyu, Joyroad, Kama, Kleber, Kormoran, Kumho, Landsail, Laufenn, LingLong, Marshal, Matador, Maxxis, Michelin, Mickey Thompson, Minerva, Nankang, Nexen, Nitto, Nokian, Nordman, Nortec, Orium, Ovation, Pirelli, Pirelli Formula, Rapid, Roadstone, Rosava, Sailun, Satoya, Sava, Starmaxx, Sunfull, Sunny, Tigar, Toyo, Trayal, Triangle, Tunga, Tyrex, Uniroyal, Viatti, Vredestein, Windforce, Yokohama, Белшина, Волтайр.';
?>