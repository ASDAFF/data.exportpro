<?
$strMessPrefix = 'ACRIT_EXP_AVITO_REALTY_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Недвижимость)';

// Headers
$MESS[$strMessPrefix.'HEADER_LEASE'] = 'Характеристики для аренды';

// Fields
$MESS[$strMessPrefix.'FIELD_STREET_NAME'] = 'Адрес объекта объявления';
	$MESS[$strMessPrefix.'FIELD_STREET_DESC'] = 'Адрес объекта объявления — строка до 256 символов, содержащая:<br/>
Место осмотра — строка до 256 символов, содержащая:
<ul>
	<li>название улицы и номер дома — если задан точный населенный пункт из <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочника</a>;</li>
	<li>если нужного населенного пункта нет в справочнике, то в этом элементе нужно указать:
		<ul>
			<li>район региона (если есть),</li>
			<li>населенный пункт (обязательно),</li>
			<li>улицу и номер дома, например для Тамбовской обл.: "Моршанский р-н, с. Устьи, ул. Лесная, д. 7".</li>
		</ul>
	</li>
</ul>
Примечания:<br/>
<ul>
	<li>элемент является устаревшим, рекомендуется использовать элемент "Address";</li>
	<li>для квартир-новостроек при указании NewDevelopmentId поле Street не обязательно, т. к. значение берется из внутреннего справочника Авито и не может быть переопределено;</li>
	<li>для того, чтобы ваш объект мог полноценно отображаться в поиске на карте, необходимо:
		<ul>
			<li>указать его точный адрес, известный <a href="https://yandex.ru/maps/" target="_blank">Яндекс.Картам</a>,</li>
			<li>или задать географические координаты (см. ниже).</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LATITUDE_NAME'] = 'Географическая широта';
	$MESS[$strMessPrefix.'FIELD_LATITUDE_DESC'] = 'Географическая широта объекта (для указания точки на карте), <a href="https://ru.wikipedia.org/wiki/%D0%93%D0%B5%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B5_%D0%BA%D0%BE%D0%BE%D1%80%D0%B4%D0%B8%D0%BD%D0%B0%D1%82%D1%8B#.D0.A4.D0.BE.D1.80.D0.BC.D0.B0.D1.82.D1.8B_.D0.B7.D0.B0.D0.BF.D0.B8.D1.81.D0.B8_.D0.B3.D0.B5.D0.BE.D0.B3.D1.80.D0.B0.D1.84.D0.B8.D1.87.D0.B5.D1.81.D0.BA.D0.B8.D1.85_.D0.BA.D0.BE.D0.BE.D1.80.D0.B4.D0.B8.D0.BD.D0.B0.D1.82" target="_blank">в градусах — десятичные дроби</a>.<br/><br/>
Примечания:<br/>
<ul>
	<li>если координаты указаны, определение геопозиции будет осуществлено по ним, а элемент Address будет проигнорирован.</li>
	<li>если координаты не заданы, то Авито попытается поставить точку на карту автоматически, определив местоположение объекта по значениям полей "City" и "Street";</li>
	<li>для квартир-новостроек с NewDevelopmentId координаты берутся из внутреннего справочника Авито и не могут быть переопределены,</li>
	<li>элементы Latitude и Longitude являются необязательными, но если они указаны, определение геопозиции будет осуществлено по ним, а элемент Address будет проигнорирован.</li>
</ul>
<b>Внимание!</b> С 28.10.2019 не будет осуществляться определение геопозиции по элементам Region, City, District, Subway, Street. Для определения геопозиции используйте обязательный элемент Address. С 25.11.2019 элементы Region, City, District, Subway, Street перестанут поддерживаться в XML-файле.';
$MESS[$strMessPrefix.'FIELD_LONGITUDE_NAME'] = 'Географическая долгота';
	$MESS[$strMessPrefix.'FIELD_LONGITUDE_DESC'] = 'Географическая долгота объекта (для указания точки на карте), <a href="https://ru.wikipedia.org/wiki/%D0%93%D0%B5%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B5_%D0%BA%D0%BE%D0%BE%D1%80%D0%B4%D0%B8%D0%BD%D0%B0%D1%82%D1%8B#.D0.A4.D0.BE.D1.80.D0.BC.D0.B0.D1.82.D1.8B_.D0.B7.D0.B0.D0.BF.D0.B8.D1.81.D0.B8_.D0.B3.D0.B5.D0.BE.D0.B3.D1.80.D0.B0.D1.84.D0.B8.D1.87.D0.B5.D1.81.D0.BA.D0.B8.D1.85_.D0.BA.D0.BE.D0.BE.D1.80.D0.B4.D0.B8.D0.BD.D0.B0.D1.82" target="_blank">в градусах — десятичные дроби</a>.<br/><br/>
Примечания:<br/>
<ul>
	<li>если координаты указаны, определение геопозиции будет осуществлено по ним, а элемент Address будет проигнорирован.</li>
	<li>если координаты не заданы, то Авито попытается поставить точку на карту автоматически, определив местоположение объекта по значениям полей "City" и "Street";</li>
	<li>для квартир-новостроек с NewDevelopmentId координаты берутся из внутреннего справочника Авито и не могут быть переопределены,</li>
	<li>элементы Latitude и Longitude являются необязательными, но если они указаны, определение геопозиции будет осуществлено по ним, а элемент Address будет проигнорирован.</li>
</ul>
<b>Внимание!</b> С 28.10.2019 не будет осуществляться определение геопозиции по элементам Region, City, District, Subway, Street. Для определения геопозиции используйте обязательный элемент Address. С 25.11.2019 элементы Region, City, District, Subway, Street перестанут поддерживаться в XML-файле.';
$MESS[$strMessPrefix.'FIELD_DISTANCE_TO_CITY_NAME'] = 'Расстояние до города, в км';
	$MESS[$strMessPrefix.'FIELD_DISTANCE_TO_CITY_DESC'] = 'Расстояние до города, в км — целое число.<br/><br/>
Примечание: если объект находится в черте города, то:
<ul>
	<li>нужно указывать значение "0";</li>
	<li>если в городе есть метро, то нужно обязательно указать ближайшую станцию метро (поле Subway);</li>
	<li>если по <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочнику</a> локаций в городе есть районы, то нужно указать район в соответствии со значениями справочника (поле District).</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_DIRECTION_ROAD_NAME'] = 'Направление от города';
	$MESS[$strMessPrefix.'FIELD_DIRECTION_ROAD_DESC'] = 'Направление от города — в соответствии со значениями <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочника</a>.<br/><br/>
<b>Обязательно, если в справочнике для города указаны направления</b>.<br/><br>
<b>Обязательно для объектов не в черте города</b>.';
#
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория объекта недвижимости';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория объекта недвижимости — одно из значений списка:<br/>
<ul>
	<li>Квартиры,</li>
	<li>Комнаты,</li>
	<li>Дома, дачи, коттеджи,</li>
	<li>Земельные участки,</li>
	<li>Гаражи и машиноместа,</li>
	<li>Коммерческая недвижимость,</li>
	<li>Недвижимость за рубежом.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OPERATION_TYPE_NAME'] = 'Тип объявления';
	$MESS[$strMessPrefix.'FIELD_OPERATION_TYPE_DESC'] = 'Тип объявления — одно из значений списка:
<ul>
	<li>Продам,</li>
	<li>Сдам.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_COUNTRY_NAME'] = 'Страна';
	$MESS[$strMessPrefix.'FIELD_COUNTRY_DESC'] = 'Страна, в которой находится объект объявления — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Countries.xml" target="_blank">справочника</a>.';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Название объявления (только для коммерческой недвижимости)';
	$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Названия объявлений формируются автоматически, исходя из выбранных параметров объекта.<br/><br/>
Только в категории «Коммерческая недвижимость» заголовок можно задать самостоятельно. В заголовке необходимо указывать только вид объекта и основные параметры. Указание цены, слов, привлекающих внимание, контактной информации, адреса сайта или только слова «продам / куплю» нарушает <a href="https://support.avito.ru/hc/ru/articles/200026888" target="_blank">правила Авито</a>.';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена в рублях';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена в рублях в зависимости от типа объявления — целое число:
<ul>
	<li>Продам — руб. за всё;</li>
	<li>Сдам — в зависимости от срока аренды:
		<ul>
			<li>На длительный срок — руб. в месяц за весь объект;</li>
			<li>Посуточно — руб. за сутки.</li>
		</ul>
</ul>';
$MESS[$strMessPrefix.'FIELD_PRICE_TYPE_NAME'] = 'Вариант задания цены';
	$MESS[$strMessPrefix.'FIELD_PRICE_TYPE_DESC'] = 'Вариант задания цены — одно из значений списка:<br/>
<ul>
<li>Продам — руб.;
	<ul>
		<li>за всё — значение по умолчанию,</li>
		<li>за м<sup>2</sup></li>
	</ul>
<li>Сдам — руб.:
	<ul>
		<li>в месяц — значение по умолчанию,</li>
		<li>в месяц за м<sup>2</sup>,</li>
		<li>в год,</li>
		<li>в год за м<sup>2</sup>.</li>
	</ul>
</ul>
';
$MESS[$strMessPrefix.'FIELD_ROOMS_NAME'] = 'Количество комнат в квартире';
	$MESS[$strMessPrefix.'FIELD_ROOMS_DESC'] = 'Количество комнат в квартире — целое число или текст "Студия".';
$MESS[$strMessPrefix.'FIELD_SQUARE_NAME'] = 'Общая площадь объекта';
	$MESS[$strMessPrefix.'FIELD_SQUARE_DESC'] = 'Общая площадь объекта недвижимости, выставленная на продажу, в кв. метрах — десятичное число.<br/><br/>
Примечание: для категории "Дома, дачи, коттеджи" здесь нужно указывать площадь дома, площадь участка указывается в поле LandArea.';
$MESS[$strMessPrefix.'FIELD_KITCHEN_SPACE_NAME'] = 'Площадь кухни';
	$MESS[$strMessPrefix.'FIELD_KITCHEN_SPACE_DESC'] = 'Площадь кухни, в кв. метрах — десятичное число.';
$MESS[$strMessPrefix.'FIELD_LIVING_SPACE_NAME'] = 'Жилая площадь';
	$MESS[$strMessPrefix.'FIELD_LIVING_SPACE_DESC'] = 'Жилая площадь, в кв. метрах — десятичное число.';
$MESS[$strMessPrefix.'FIELD_LAND_AREA_NAME'] = 'Площадь участка';
	$MESS[$strMessPrefix.'FIELD_LAND_AREA_DESC'] = 'Площадь участка, в сотках — десятичное число.';
$MESS[$strMessPrefix.'FIELD_FLOOR_NAME'] = 'Этаж';
	$MESS[$strMessPrefix.'FIELD_FLOOR_DESC'] = 'Этаж, на котором находится объект — целое число.';
$MESS[$strMessPrefix.'FIELD_FLOORS_NAME'] = 'Количество этажей в доме';
	$MESS[$strMessPrefix.'FIELD_FLOORS_DESC'] = 'Количество этажей в доме — целое число.';
$MESS[$strMessPrefix.'FIELD_HOUSE_TYPE_NAME'] = 'Тип дома';
	$MESS[$strMessPrefix.'FIELD_HOUSE_TYPE_DESC'] = 'Тип дома — одно из значений списка:
<ul>
	<li>Кирпичный,</li>
	<li>Панельный,</li>
	<li>Блочный,</li>
	<li>Монолитный,</li>
	<li>Деревянный.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_WALLS_TYPE_NAME'] = 'Материал стен';
	$MESS[$strMessPrefix.'FIELD_WALLS_TYPE_DESC'] = 'Материал стен — одно из значений списка:
<ul>
	<li>Кирпич,</li>
	<li>Брус,</li>
	<li>Бревно,</li>
	<li>Газоблоки,</li>
	<li>Металл,</li>
	<li>Пеноблоки,</li>
	<li>Сэндвич-панели,</li>
	<li>Ж/б панели,</li>
	<li>Экспериментальные материалы.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_MARKET_TYPE_NAME'] = 'Вторичка/новостройка';
	$MESS[$strMessPrefix.'FIELD_MARKET_TYPE_DESC'] = 'Принадлежность квартиры к рынку — одно из значений списка:
<ul>
	<li>Вторичка,</li>
	<li>Новостройка.</li>
</ul>
<b>Обязательно для типа (OperationType) "Продам"</b>.';
$MESS[$strMessPrefix.'FIELD_NEW_DEVELOPMENT_ID_NAME'] = 'Объект новостройки';
	$MESS[$strMessPrefix.'FIELD_NEW_DEVELOPMENT_ID_DESC'] = 'Объект новостройки — ID объекта из <a href="https://autoload.avito.ru/format/New_developments.xml" target="_blank">XML-справочника</a>:
<ul>
	<li>если в жилом комплексе новостроек есть корпуса, то обязательно ID корпуса (элементы Housing);</li>
	<li>если корпусов нет, то ID жилого комплекса (элементы Object).</li>
</ul>
Если задан элемент NewDevelopmentId, то значения поля Street и географических координат берутся из внутреннего справочника Авито.<br/><br/>
Важно: Если в нашем справочнике нет нужного вам объекта или вы нашли в нем ошибку, то сообщайте по адресу <a href="mailto:newdevelopments@avito.ru">newdevelopments@avito.ru</a> с указанием ссылки на сайт, где есть проектная документация:<br/>
<ul>
	<li>для ДДУ: разрешение на строительство дома, заключение строительного надзора о соответствии застройщика и проектной декларации 214-ФЗ (для проектов начатых после 01.01.2017), проектная декларация;</li>
	<li>для ЖСК: разрешение на строительство дома и устав кооператива).</li>
</ul>
<b>Обязательно для типа "Новостройка"</b>.';
$MESS[$strMessPrefix.'FIELD_PROPERTY_RIGHTS_NAME'] = 'Право собственности';
	$MESS[$strMessPrefix.'FIELD_PROPERTY_RIGHTS_DESC'] = 'Право собственности — одно из значений списка:
<ul>
	<li>Собственник;</li>
	<li>Посредник;</li>
	<li>Застройщик (доступно только в категории "Квартиры. Продам. Новостройка").</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OBJECT_TYPE_NAME'] = 'Вид объекта';
	$MESS[$strMessPrefix.'FIELD_OBJECT_TYPE_DESC'] = 'Вид объекта — одно из значений списка (отдельно для каждой категории):<br/>
<ul>
	<li>
		<b>Дома, дачи, коттеджи:</b>
		<ul>
			<li>Дом,</li>
			<li>Дача,</li>
			<li>Коттедж,</li>
			<li>Таунхаус;</li>
		</ul>
	</li>
	<li>
		<b>Земельные участки:</b>
		<ul>
			<li>Поселений (ИЖС),</li>
			<li>Сельхозназначения (СНТ, ДНП),</li>
			<li>Промназначения;</li>
		</ul>
	</li>
	<li>
		<b>Гаражи и машиноместа:</b>
		<ul>
			<li>Гараж,</li>
			<li>Машиноместо;</li>
		</ul>
	</li>
	<li>
		<b>Коммерческая недвижимость:</b>
		<ul>
			<li>Гостиница,</li>
			<li>Офисное помещение,</li>
			<li>Помещение общественного питания,</li>
			<li>Помещение свободного назначения,</li>
			<li>Производственное помещение,</li>
			<li>Складское помещение</li>
			<li>Торговое помещение;</li>
		</ul>
	</li>
	<li>
		<b>Недвижимость за рубежом:</b>
		<ul>
			<li>Квартира, апартаменты,</li>
			<li>Дом, вилла,</li>
			<li>Земельный участок,</li>
			<li>Гараж, машиноместо,</li>
			<li>Коммерческая недвижимость.</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OBJECT_SUBTYPE_NAME'] = 'Подвид объекта';
	$MESS[$strMessPrefix.'FIELD_OBJECT_SUBTYPE_DESC'] = 'Подвид объекта — одно из значений списка (отдельно для каждого типа):<br/>
<ul>
	<li>
		<b>Гараж:</b>
		<ul>
			<li>Железобетонный,</li>
			<li>Кирпичный,</li>
			<li>Металлический;</li>
		</ul>
	</li>
	<li>
		<b>Машиноместо:</b>
		<ul>
			<li>Многоуровневый паркинг,</li>
			<li>Подземный паркинг,</li>
			<li>Крытая стоянка,</li>
			<li>Открытая стоянка.</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_SECURED_NAME'] = 'Охрана объекта';
	$MESS[$strMessPrefix.'FIELD_SECURED_DESC'] = 'Охрана объекта — одно из значений списка:<br/>
<ul>
	<li>Да,</li>
	<li>Нет.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BUILDING_CLASS_NAME'] = 'Класс здания';
	$MESS[$strMessPrefix.'FIELD_BUILDING_CLASS_DESC'] = 'Класс здания (только для видов объекта "Офисное помещение" и "Складское помещение") — одно из значений списка:
<ul>
	<li>A,</li>
	<li>B,</li>
	<li>C,</li>
	<li>D.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CADASTRAL_NUMBER_NAME'] = 'Кадастровый номер';
	$MESS[$strMessPrefix.'FIELD_CADASTRAL_NUMBER_DESC'] = 'Кадастровый номер — строка.<br/><br/>
Примечание: не показывается в объявлении полностью.';
$MESS[$strMessPrefix.'FIELD_DECORATION_NAME'] = 'Отделка помещения';
	$MESS[$strMessPrefix.'FIELD_DECORATION_DESC'] = 'Отделка помещения (только для типов объекта (MarketType) "Новостройка"). Возможные значения параметра:
<ul>
	<li>"Без отделки"</li>
	<li>"Черновая"</li>
	<li>"Чистовая"</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_SAFE_DEMONSTRATION_NAME'] = 'Онлайн показ';
	$MESS[$strMessPrefix.'FIELD_SAFE_DEMONSTRATION_DESC'] = 'Онлайн показ — одно из значений списка:
<ul>
	<li>Могу провести</li>
	<li>Не хочу</li>
</ul>
<b>Важно</b>: Данный элемент не поддерживается в микрокатегориях Аренда/Посуточно (для всех указанных категорий) и Продажа/Новостройка (для категории Квартиры).';
$MESS[$strMessPrefix.'FIELD_APARTMENT_NUMBER_NAME'] = 'Номер квартиры';
	$MESS[$strMessPrefix.'FIELD_APARTMENT_NUMBER_DESC'] = 'Номер квартиры - строка, содержащая от 1 до 10 символов.';
$MESS[$strMessPrefix.'FIELD_STATUS_NAME'] = 'Статус недвижимости';
	$MESS[$strMessPrefix.'FIELD_STATUS_DESC'] = 'Статус недвижимости — одно из значений списка:
<ul>
	<li>Квартира</li>
	<li>Апартаменты</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BALCONY_OR_LOGGIA_NAME'] = 'Балкон или лоджия';
	$MESS[$strMessPrefix.'FIELD_BALCONY_OR_LOGGIA_DESC'] = 'Балкон или лоджия — одно из значений списка:
<ul>
	<li>Балкон</li>
	<li>Лоджия</li>
	<li>Нет</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_VIEW_FROM_WINDOWS_NAME'] = 'Вид из окон';
	$MESS[$strMessPrefix.'FIELD_VIEW_FROM_WINDOWS_DESC'] = 'Вид из окон — одно или более значения из списка:
<ul>
	<li>На улицу</li>
	<li>Во двор</li>
</ul>
Поле должно выгружаться как множественное!';

$MESS[$strMessPrefix.'FIELD_LEASE_TYPE_NAME'] = 'Тип аренды';
	$MESS[$strMessPrefix.'FIELD_LEASE_TYPE_DESC'] = 'Тип аренды — одно из значений списка:<br/>
<ul>
	<li>На длительный срок,</li>
	<li>Посуточно.</li>
</ul>
<b>Обязательно для типа "Сдам"</b>.';
$MESS[$strMessPrefix.'FIELD_LEASE_BEDS_NAME'] = 'Количество кроватей.';
	$MESS[$strMessPrefix.'FIELD_LEASE_BEDS_DESC'] = 'Количество кроватей (только для аренды) — целое число.';
$MESS[$strMessPrefix.'FIELD_LEASE_SLEEPING_PLACES_NAME'] = 'Количество спальных мест';
	$MESS[$strMessPrefix.'FIELD_LEASE_SLEEPING_PLACES_DESC'] = 'Количество спальных мест (только для аренды) — целое число.';
$MESS[$strMessPrefix.'FIELD_LEASE_MULTIMEDIA_NAME'] = 'Опции "Мультимедиа"';
	$MESS[$strMessPrefix.'FIELD_LEASE_MULTIMEDIA_DESC'] = 'Опции "Мультимедиа" (только для аренды) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Wi-Fi,</li>
	<li>Телевизор,</li>
	<li>Кабельное / цифровое ТВ.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_APPLIANCES_NAME'] = 'Опции "Бытовая техника"';
	$MESS[$strMessPrefix.'FIELD_LEASE_APPLIANCES_DESC'] = 'Опции "Бытовая техника" (только для аренды) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Плита,</li>
	<li>Микроволновка,</li>
	<li>Холодильник,</li>
	<li>Стиральная машина,</li>
	<li>Фен,</li>
	<li>Утюг.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_COMFORT_NAME'] = 'Опции "Комфорт"';
	$MESS[$strMessPrefix.'FIELD_LEASE_COMFORT_DESC'] = 'Опции "Комфорт" (только для аренды) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Кондиционер,</li>
	<li>Камин,</li>
	<li>только в категориях "Квартиры" и "Комнаты":<br/>
		<ul>
			<li>Балкон / лоджия,</li>
			<li>Парковочное место;</li>
		</ul>
	</li>
	<li>только в категории "Дома, дачи, коттеджи":<br/>
		<ul>
			<li>Бассейн,</li>
			<li>Баня / сауна.</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_ADDITIONALLY_NAME'] = 'Опции "Дополнительно"';
	$MESS[$strMessPrefix.'FIELD_LEASE_ADDITIONALLY_DESC'] = 'Опции "Дополнительно" (только для аренды) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Можно с питомцами,</li>
	<li>Можно с детьми,</li>
	<li>Можно для мероприятий,</li>
	<li>Можно курить.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_COMMISSION_SIZE_NAME'] = 'Размер комиссии в %';
	$MESS[$strMessPrefix.'FIELD_LEASE_COMMISSION_SIZE_DESC'] = 'Размер комиссии в % — целое число.<br/><br/>
	<b>Обязательно для долгосрочной аренды в случае права собственности "Посредник"</b>.';
$MESS[$strMessPrefix.'FIELD_LEASE_DEPOSIT_NAME'] = 'Залог';
	$MESS[$strMessPrefix.'FIELD_LEASE_DEPOSIT_DESC'] = 'Залог — одно из значений списка:
<ul>
	<li>Без залога,</li>
	<li>0,5 месяца,</li>
	<li>1 месяц,</li>
	<li>1,5 месяца,</li>
	<li>2 месяца,</li>
	<li>2,5 месяца,</li>
	<li>3 месяца.</li>
</ul>
<b>Обязательно для долгосрочной аренды</b>.';


?>