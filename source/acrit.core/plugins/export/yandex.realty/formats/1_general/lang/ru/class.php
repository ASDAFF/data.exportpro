<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Яндекс.Недвижимость';

$MESS['YANDEX_REALTY_BOOLEAN'] = 'Строго ограниченные значения:
<ul>
	<li>«да»/«нет»,</li>
	<li>«true»/«false»,</li>
	<li>«1»/«0»,</li>
	<li>«+»/«-».</li>
</ul>';
$MESS['YANDEX_REALTY_SUPPORTED_REGIONS'] = '<ul style="column-count:3;">
	<li>Республика Башкортостан</li>
	<li>Республика Крым</li>
	<li>Республика Саха (Якутия)</li>
	<li>Республика Татарстан</li>
	<li>Республика Чувашия</li>
	<li>Алтайский край</li>
	<li>Астраханская область</li>
	<li>Брянская область</li>
	<li>Владимирская область</li>
	<li>Волгоградская область</li>
	<li>Вологодская область</li>
	<li>Воронежская область</li>
	<li>Калининградская область</li>
	<li>Калужская область</li>
	<li>Кемеровская область</li>
	<li>Кировская область</li>
	<li>Костромская область</li>
	<li>Краснодарский край</li>
	<li>Красноярский край</li>
	<li>Ленинградская область</li>
	<li>Московская область</li>
	<li>Нижегородская область</li>
	<li>Новосибирская область</li>
	<li>Омская область</li>
	<li>Орловская область</li>
	<li>Пензенская область</li>
	<li>Пермский край</li>
	<li>Приморский край</li>
	<li>Ростовская область</li>
	<li>Рязанская область</li>
	<li>Самарская область</li>
	<li>Саратовская область</li>
	<li>Свердловская область</li>
	<li>Ставропольский край</li>
	<li>Тверская область</li>
	<li>Тульская область</li>
	<li>Тюменская область</li>
	<li>Ульяновская область</li>
	<li>Хабаровский край</li>
	<li>Челябинская область</li>
	<li>Ярославская область</li>
</ul>';

// Fields: General
$MESS[$strHead.'HEADER_GENERAL'] = 'Общая информация об объявлении';
$MESS[$strName.'@internal-id'] = 'Идентификатор объявления';
	$MESS[$strHint.'@internal-id'] = 'Идентификатор объявления. Должен быть уникальным для каждого объявления.';
$MESS[$strName.'type'] = 'Тип сделки';
	$MESS[$strHint.'type'] = 'Строго ограниченные значения: «продажа», «аренда».';
$MESS[$strName.'property-type'] = 'Тип недвижимости';
	$MESS[$strHint.'property-type'] = 'Строго ограниченное значение: «жилая»/«living».';
$MESS[$strName.'category'] = 'Категория объекта';
	$MESS[$strHint.'category'] = 'Возможные значения:
<ul>
	<li>«дача»/«коттедж»/«cottage»</li>
	<li>«дом»/«house»</li>
	<li>«дом с участком»/«house with lot»</li>
	<li>«участок»/«lot»</li>
	<li>«часть дома»</li>
	<li>«квартира»/«flat»</li>
	<li>«комната»/«room»</li>
	<li>«таунхаус»/«townhouse»</li>
	<li>«дуплекс»/«duplex»</li>
	<li>«гараж»/«garage».</li>
</ul>';
$MESS[$strName.'lot-number'] = 'Номер лота';
	$MESS[$strHint.'lot-number'] = 'Номер лота.';
$MESS[$strName.'cadastral-number'] = 'Кадастровый номер объекта недвижимости.';
	$MESS[$strHint.'cadastral-number'] = 'Кадастровый номер объекта недвижимости.';
$MESS[$strName.'url'] = 'URL страницы с объявлением';
	$MESS[$strHint.'url'] = 'URL страницы с объявлением.';
$MESS[$strName.'creation-date'] = 'Дата создания объявления';
	$MESS[$strHint.'creation-date'] = 'Указывается в формате YYYY-MM-DDTHH:mm:ss+00:00.';
$MESS[$strName.'last-update-date'] = 'Дата последнего обновления объявления';
	$MESS[$strHint.'last-update-date'] = 'Указывается в формате YYYY-MM-DDTHH:mm:ss+00:00.';
$MESS[$strName.'vas'] = 'Дополнительная опция по продвижению объявления';
	$MESS[$strHint.'vas'] = 'Элемент указывается, если к объявлению должна быть применена дополнительная опция.<br/><br/>
Возможные значения:
<ul>
	<li>«premium»</li>
	<li>«raise»</li>
	<li>«promotion»</li>
</ul>
Для значения «raise» («Поднятие») можно применить ежедневное автоподключение в определенное время. Для этого внутри vas укажите атрибут start-time, дату и время в формате YYYY-MM-DDTHH:mm:ss+00:00 и значение raise.<br/><br/>
Должна быть указана дата первого применения опции. Обновлять атрибут не нужно. Объявление, к которому применена опция, будет подниматься ежедневно в указанное время.<br/><br/>
<b>Внимание.</b> Опции нельзя подключить к объявлениям без фотографий.';

// Fields: Location
$MESS[$strHead.'HEADER_LOCATION'] = 'Местоположение объекта';
$MESS[$strName.'location.country'] = 'Страна, в которой расположен объект';
	$MESS[$strHint.'location.country'] = 'Примечание. В настоящее время объявления принимаются только для объектов недвижимости, расположенных в России.';
$MESS[$strName.'location.region'] = 'Название субъекта РФ';
	$MESS[$strHint.'location.region'] = 'Необязательный элемент для объектов в Москве и Санкт-Петербурге.';
$MESS[$strName.'location.district'] = 'Название района субъекта РФ';
	$MESS[$strHint.'location.district'] = 'Название района субъекта РФ.';
$MESS[$strName.'location.locality-name'] = 'Название населенного пункта';
	$MESS[$strHint.'location.locality-name'] = 'Название населенного пункта.';
$MESS[$strName.'location.sub-locality-name'] = 'Район населенного пункта';
	$MESS[$strHint.'location.sub-locality-name'] = 'Район населенного пункта.';
$MESS[$strName.'location.address'] = 'Адрес объекта (улица и номер здания)';
	$MESS[$strHint.'location.address'] = 'Для загородной недвижимости номер дома указывать необязательно.';
$MESS[$strName.'location.apartment'] = 'Номер квартиры';
	$MESS[$strHint.'location.apartment'] = 'Номер квартиры.';
$MESS[$strName.'location.direction'] = 'Шоссе';
	$MESS[$strHint.'location.direction'] = 'Элемент передается только для объектов в Москве и Московской области.';
$MESS[$strName.'location.distance'] = 'Расстояние по шоссе до МКАД';
	$MESS[$strHint.'location.distance'] = 'Значение указывается в километрах.<br/><br/>
	Элемент передается только для объектов в Москве и Московской области.';
$MESS[$strName.'location.latitude'] = 'Географическая широта';
	$MESS[$strHint.'location.latitude'] = 'Географическая широта.';
$MESS[$strName.'location.longitude'] = 'Географическая долгота';
	$MESS[$strHint.'location.longitude'] = 'Географическая долгота.';
$MESS[$strName.'location.metro.name'] = 'Название станции метро';
	$MESS[$strHint.'location.metro.name'] = 'Название станции метро.';
$MESS[$strName.'location.metro.time-on-transport'] = 'Время до метро в минутах на транспорте';
	$MESS[$strHint.'location.metro.time-on-transport'] = 'Время до метро в минутах на транспорте.';
$MESS[$strName.'location.metro.time-on-foot'] = 'Время до метро в минутах пешком';
	$MESS[$strHint.'location.metro.time-on-foot'] = 'Время до метро в минутах пешком.';
$MESS[$strName.'location.railway-station'] = 'Ближайшая железнодорожная станция';
	$MESS[$strHint.'location.railway-station'] = 'Элемент указывается только для загородной недвижимости.';

# Object general
$MESS[$strHead.'HEADER_OBJECT_GENERAL'] = 'Общая информация об объекте';

# Fields: Terms
$MESS[$strHead.'HEADER_TERMS'] = 'Информация об условиях сделки';
$MESS[$strName.'price.value'] = 'Цена';
	$MESS[$strHint.'price.value'] = 'Сумма указывается без пробелов.<br/><br/>
Цена должна включать НДС (если он есть) и постоянные эксплуатационные расходы (для коммерческой недвижимости).';
$MESS[$strName.'price.currency'] = 'Валюта, в которой указана цена';
	$MESS[$strHint.'price.currency'] = 'Цену предложения следует передавать только в той валюте, которая указана в объявлении.<br/><br/>
Возможные значения:
<ul>
	<li>«RUR» или «RUB» (российский рубль)</li>
	<li>«EUR» (евро)</li>
	<li>«USD» (американский доллар).</li>
</ul>';
$MESS[$strName.'price.period'] = 'Период для расчета стоимости аренды';
	$MESS[$strHint.'price.period'] = 'Элемент используется только для объявлений об аренде.<br/><br/>
Возможные значения:
<ul>
	<li>«день»/«day»</li>
	<li>«месяц»/«month»</li>
</ul>';
$MESS[$strName.'price.unit'] = 'Единица площади помещения или участка';
	$MESS[$strHint.'price.unit'] = 'Параметр нужно передавать, если цена указана за единицу площади.<br/><br/>
Возможные значения:
<ul>
	<li>«кв. м»/«sq. m»</li>
	<li>«cотка»</li>
	<li>«гектар»/«hectare».</li>
</ul>';
$MESS[$strName.'rent-pledge'] = 'Залог';
	$MESS[$strHint.'rent-pledge'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'deal-status'] = 'Тип сделки';
	$MESS[$strHint.'deal-status'] = 'Если элемент отсутствует, все объявления партнера в новостройках считаются квартирами от застройщика.<br/><br/>
Возможные значения:
<ul>
	<li>«первичная продажа»/«продажа от застройщика»,</li>
	<li>«переуступка»/«reassignment».</li>
</ul>
Возможные значения для вторичной недвижимости:
<ul>
	<li>«прямая продажа»/«sale»,</li>
	<li>«первичная продажа вторички»/«primary sale of secondary»,</li>
	<li>«встречная продажа»/«countersale»</li>.
</ul>';
$MESS[$strName.'haggle'] = 'Торг';
	$MESS[$strHint.'haggle'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'mortgage'] = 'Ипотека';
	$MESS[$strHint.'mortgage'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'prepayment'] = 'Предоплата';
	$MESS[$strHint.'prepayment'] = 'Указывается числовое значение в процентах без знака «%».<br/><br/>
Максимальное значение — 100.';
$MESS[$strName.'agent-fee'] = 'Комиссия агента';
	$MESS[$strHint.'agent-fee'] = 'Указывается числовое значение в процентах без знака «%».';
$MESS[$strName.'not-for-agents'] = 'Пометка «Просьба агентам не звонить»';
	$MESS[$strHint.'not-for-agents'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'utilities-included'] = 'Коммунальные услуги включены в стоимость в договоре аренды';
	$MESS[$strHint.'utilities-included'] = $MESS['YANDEX_REALTY_BOOLEAN'];


# Fields: Object info
$MESS[$strHead.'HEADER_OBJECT_INFO'] = 'Информация об объекте';
$MESS[$strName.'area.value'] = 'Общая площадь (число)';
	$MESS[$strHint.'area.value'] = 'Обязательный элемент для всех объявлений, кроме участков, где вместо этого используется lot-type';
$MESS[$strName.'area.unit'] = 'Общая площадь (ед. изм.)';
	$MESS[$strHint.'area.unit'] = 'Обязательный элемент для всех объявлений, кроме участков, где вместо этого используется lot-type';
$MESS[$strName.'image'] = 'Фотография';
	$MESS[$strHint.'image'] = 'Обязательный элемент для объявлений о сдаче недвижимости в аренду.<br/><br/>
	Может быть несколько тегов. <b>Фотографии планировок следует передавать первым тегом image</b>.<br/><br/>
	Не следует передавать изображения, не имеющие прямого отношения к объекту (например, логотипы или фотографии сотрудников).';
$MESS[$strName.'renovation'] = 'Ремонт';
	$MESS[$strHint.'renovation'] = 'Возможные значения:
<ul>
	<li>«дизайнерский»,</li>
	<li>«евро»,</li>
	<li>«с отделкой»,</li>
	<li>«требует ремонта»,</li>
	<li>«хороший»,</li>
	<li>«частичный ремонт»,</li>
	<li>«черновая отделка».</li>
</ul>';
$MESS[$strName.'quality'] = 'Состояние объекта';
	$MESS[$strHint.'quality'] = 'Возможные значения:
<ul>
<li>«отличное»,</li>
<li>«хорошее»,</li>
<li>«нормальное»,</li>
<li>«плохое»</li>.
</ul>';
$MESS[$strName.'description'] = 'Подробное описание объявления';
	$MESS[$strHint.'description'] = 'Описание в свободной форме.';

// Defaults
$MESS[$strLang.'_boolean_default_y'] = 'да';
$MESS[$strLang.'_boolean_default_n'] = 'нет';
$MESS[$strLang.'property-type_default'] = 'жилая';
$MESS[$strLang.'area_unit_default'] = 'кв. м';
$MESS[$strLang.'location.country_default'] = 'Россия';
$MESS[$strLang.'sales-agent.category_default'] = 'агентство';

// Fields: object info
$MESS[$strName.'room-space.value'] = 'Площадь комнаты (число)';
	$MESS[$strHint.'room-space.value'] = 'Количество передаваемых элементов должно соответствовать количеству комнат.<br/><br/>
	Обязательный элемент для продажи или аренды комнаты.<br/><br/>
	Элемент не используется для студий.<br/><br/>
	Элемент не используется для объектов со свободной планировкой.';
$MESS[$strName.'room-space.unit'] = 'Площадь комнаты (ед. изм.)';
	$MESS[$strHint.'room-space.unit'] = 'Количество передаваемых элементов должно соответствовать количеству комнат.<br/><br/>
	Обязательный элемент для продажи или аренды комнаты.<br/><br/>
	Элемент не используется для студий.<br/><br/>
	Элемент не используется для объектов со свободной планировкой.';
$MESS[$strName.'living-space.value'] = 'Жилая площадь (число)';
	$MESS[$strHint.'living-space.value'] = 'При продаже и сдаче комнаты указывается площадь комнаты.<br/><br/>
	Во вложенных тегах указывается подробная информация.';
$MESS[$strName.'living-space.unit'] = 'Жилая площадь (ед. изм.)';
	$MESS[$strHint.'living-space.unit'] = 'При продаже и сдаче комнаты указывается площадь комнаты.<br/><br/>
	Во вложенных тегах указывается подробная информация.';
$MESS[$strName.'kitchen-space.value'] = 'Площадь кухни (число)';
	$MESS[$strHint.'kitchen-space.value'] = 'Площадь кухни.';
$MESS[$strName.'kitchen-space.unit'] = 'Площадь кухни (ед. изм.)';
	$MESS[$strHint.'kitchen-space.unit'] = 'Площадь кухни.';
	
// Fields: additional
$MESS[$strHead.'HEADER_OBJECT_ADDITIONAL'] = 'Дополнительная информация об объекте';
$MESS[$strName.'rooms'] = 'Общее количество комнат';
	$MESS[$strHint.'rooms'] = 'При свободной планировке количество комнат указывается согласно паспорту объекта.<br/><br/>
Элемент не используется для студий.';
$MESS[$strName.'rooms-offered'] = 'Количество комнат, участвующих в сделке (для жилых)';
	$MESS[$strHint.'rooms-offered'] = 'Элемент не используется для студий.<br/><br/>
Элемент не используется для объектов со свободной планировкой.';
$MESS[$strName.'floor'] = 'Этаж';
	$MESS[$strHint.'floor'] = 'Обязательный элемент для агентств недвижимости';
$MESS[$strName.'new-flat'] = 'Признак новостройки';
	$MESS[$strHint.'new-flat'] = 'Строго ограниченные значения: «да», «true», «1», «+».';
$MESS[$strName.'apartments'] = 'Апартаменты';
	$MESS[$strHint.'apartments'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'studio'] = 'Студия';
	$MESS[$strHint.'studio'] = 'Элемент используется только для объявлений о продаже и аренде квартиры.<br/><br/>
Строго ограниченные значения: «да», «true», «1», «+».<br/><br>
Элемент не используется для объектов со свободной планировкой.';
$MESS[$strName.'open-plan'] = 'Свободная планировка';
	$MESS[$strHint.'open-plan'] = 'Элемент используется только для объявлений о продаже и аренде квартиры.<br/><br/>
Строго ограниченные значения: «да», «true», «1», «+».<br/><br/>
Элемент не используется для студий.';
$MESS[$strName.'rooms-type'] = 'Тип комнат';
	$MESS[$strHint.'rooms-type'] = 'Возможные значения:
<ul>
	<li>«смежные»</li>
	<li>«раздельные».</li>
</ul>';
$MESS[$strName.'window-view'] = 'Вид из окон';
	$MESS[$strHint.'window-view'] = 'Возможные значения:
<ul>
	<li>«во двор»</li>
	<li>«на улицу».</li>
</ul>';
$MESS[$strName.'balcony'] = 'Тип балкона';
	$MESS[$strHint.'balcony'] = 'Возможные значения:
<ul>
	<li>«балкон»</li>
	<li>«лоджия»</li>
	<li>«2 балкона»</li>
	<li>«2 лоджии»</li>
	<li>И т. п.</li>
</ul>';
$MESS[$strName.'bathroom-unit'] = 'Тип санузла';
	$MESS[$strHint.'bathroom-unit'] = 'Возможные значения:
<ul>
	<li>«совмещенный»</li>
	<li>«раздельный»</li>
	<li>числовое значение (например «2»).</li>
</ul>';
$MESS[$strName.'air-conditioner'] = 'Наличие системы кондиционирования';
	$MESS[$strHint.'air-conditioner'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'phone'] = 'Наличие телефона';
	$MESS[$strHint.'phone'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'internet'] = 'Наличие интернета';
	$MESS[$strHint.'internet'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'room-furniture'] = 'Наличие мебели';
	$MESS[$strHint.'room-furniture'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'kitchen-furniture'] = 'Наличие мебели на кухне';
	$MESS[$strHint.'kitchen-furniture'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'television'] = 'Наличие телевизора';
	$MESS[$strHint.'television'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'washing-machine'] = 'Наличие стиральной машины';
	$MESS[$strHint.'washing-machine'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'dishwasher'] = 'Наличие посудомоечной машины';
	$MESS[$strHint.'dishwasher'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'refrigerator'] = 'Наличие холодильника';
	$MESS[$strHint.'refrigerator'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'built-in-tech'] = 'Встроенная техника';
	$MESS[$strHint.'built-in-tech'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'floor-covering'] = 'Покрытие пола';
	$MESS[$strHint.'floor-covering'] = 'Возможные значения:
<ul>
	<li>«ковролин»,</li>
	<li>«ламинат»,</li>
	<li>«линолеум»,</li>
	<li>«паркет».</li>
</ul>';
$MESS[$strName.'with-children'] = 'Проживание с детьми';
	$MESS[$strHint.'with-children'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'with-pets'] = 'Проживание с животными';
	$MESS[$strHint.'with-pets'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'entrance-type'] = 'Вход в помещение';
	$MESS[$strHint.'entrance-type'] = 'Возможные значения:
<ul>
«common» (общий)
«separate» (отдельный).
</ul>';
$MESS[$strName.'phone-lines'] = 'Количество телефонных линий';
	$MESS[$strHint.'phone-lines'] = 'Количество телефонных линий';
$MESS[$strName.'adding-phone-on-request'] = 'Возможность добавления телефонных линий';
	$MESS[$strHint.'adding-phone-on-request'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'self-selection-telecom'] = 'Возможность самостоятельного выбора оператора телекоммуникационных услуг';
	$MESS[$strHint.'self-selection-telecom'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'ventilation'] = 'Наличие вентиляции';
	$MESS[$strHint.'ventilation'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'fire-alarm'] = 'Наличие пожарной сигнализации';
	$MESS[$strHint.'fire-alarm'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'electric-capacity'] = 'Выделенная электрическая мощность';
	$MESS[$strHint.'electric-capacity'] = 'Указывается целое число. Значение передается в кВт.';
$MESS[$strName.'window-type'] = 'Тип окон';
	$MESS[$strHint.'window-type'] = 'Возможные значения:
<ul>
	<li>«витринные»,</li>
	<li>«маленькие»,</li>
	<li>«обычные»</li>.
</ul>';

#
$MESS[$strName.'yandex-building-id'] = 'ИД жилого комплекса в Яндексе';
	$MESS[$strHint.'yandex-building-id'] = 'ИД жилого комплекса в базе данных Яндекса.<br/><br/>
	В настоящее время элемент поддерживается для объектов в регионах, указанных в списке: '.$MESS['YANDEX_REALTY_SUPPORTED_REGIONS'].'
	Идентификатор указан в первом столбце в <a href="https://realty.yandex.ru/newbuildings.tsv" target="_blank">списке идентификаторов yandex-building-id</a>.<br/><br/>
	Идентификатор отображается в адресе страницы, на которой размещена карточка жилого комплекса.<br/><br/>
	Элемент следует передавать, чтобы объявления корректно подгружались к соответствующему жилому комплексу.';
$MESS[$strName.'yandex-house-id'] = 'ИД корпуса жилого комплекса в Яндексе (для новостроек)';
	$MESS[$strHint.'yandex-house-id'] = 'Идентификатор корпуса жилого комплекса в базе данных Яндекса.<br/><br/>
	В настоящее время элемент поддерживается для объектов в регионах, указанных в списке: '.$MESS['YANDEX_REALTY_SUPPORTED_REGIONS'].'
	Идентификатор указан в седьмом столбце <a href="https://realty.yandex.ru/newbuildings.tsv" target="_blank">в списке идентификаторов yandex-building-id</a>.<br/><br/>
	Элемент следует передавать, чтобы объявления корректно подгружались к соответствующему корпусу жилого комплекса.';
	
#
$MESS[$strName.'office-class'] = 'Класс бизнес-центра';
	$MESS[$strHint.'office-class'] = 'Возможные значения: «A», «A+», «B», «B+», «C», «C+».';
$MESS[$strName.'building-state'] = 'Стадия строительства дома (для новостроек)';
	$MESS[$strHint.'building-state'] = 'Строго ограниченные значения:
<ul>
	<li>«built» (дом построен, но не сдан),</li>
	<li>«hand-over» (сдан в эксплуатацию),</li>
	<li>«unfinished» (строится).</li>
</ul>
Если значения built-year и ready-quarter указаны в прошедшем времени, для элемента building-state следует передавать значение hand-over.';
$MESS[$strName.'building-phase'] = 'Очередь строительства (для новостроек)';
	$MESS[$strHint.'building-phase'] = 'Возможные значения: «очередь 1», «II очередь», «3» и т. п.';
$MESS[$strName.'building-series'] = 'Серия дома (для новостроек)';
	$MESS[$strHint.'building-series'] = 'Серия дома';

#
$MESS[$strName.'floors-total'] = 'Общее количество этажей в доме';
	$MESS[$strHint.'floors-total'] = 'Общее количество этажей в доме';
$MESS[$strName.'building-name'] = 'Название жилого комплекса';
	$MESS[$strHint.'building-name'] = 'В настоящее время элемент поддерживается только для объектов в регионах, указанных в списке: '.$MESS['YANDEX_REALTY_SUPPORTED_REGIONS'].'
	Передавать нужно только название ЖК.';
$MESS[$strName.'building-type'] = 'Тип дома/здания';
	$MESS[$strHint.'building-type'] = 'Возможные значения для жилой недвижимости:
<ul>
	<li>«блочный»,</li>
	<li>«деревянный»,</li>
	<li>«кирпичный»,</li>
	<li>«кирпично-монолитный»,</li>
	<li>«монолит»,</li>
	<li>«панельный».</li>
</ul>
Возможные значения для коммерческой недвижимости:
<ul>
	<li>«business center» (бизнес-центр),</li>
	<li>«detached building» (отдельно стоящее здание),</li>
	<li>«residential building» (встроенное помещение),</li>
	<li>«shopping center» (торговый центр),</li>
	<li>«warehouse» (складской комплекс).</li>
</ul>
Возможные значения для новостроек:
<ul>
	<li>«кирпичный»,</li>
	<li>«монолит»,</li>
	<li>«панельный».</li>
</ul>
';
$MESS[$strName.'built-year'] = 'Год сдачи/постройки';
	$MESS[$strHint.'built-year'] = 'Обязательный элемент для домов (жилищных комплексов), которые были сданы менее 5 лет назад или будут сданы в будущем.<br/><br/>
Год необходимо указывать полностью, например — «1996», а не «96».';
$MESS[$strName.'ready-quarter'] = 'Квартал сдачи дома (для новостроек)';
	$MESS[$strHint.'ready-quarter'] = 'Строго ограниченные значения: «1», «2», «3», «4».';
$MESS[$strName.'building-section'] = 'Корпус дома';
	$MESS[$strHint.'building-section'] = 'Возможные значения: «корпус 1», «корпус А», «дом 3» и т. п.';
$MESS[$strName.'ceiling-height'] = 'Высота потолков';
	$MESS[$strHint.'ceiling-height'] = 'Высота потолков в метрах';
$MESS[$strName.'guarded-building'] = 'Закрытая территория';
	$MESS[$strHint.'guarded-building'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'pmg'] = 'Возможность ПМЖ';
	$MESS[$strHint.'pmg'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'access-control-system'] = 'Наличие пропускной системы';
	$MESS[$strHint.'access-control-system'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'lift'] = 'Лифт';
	$MESS[$strHint.'lift'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'rubbish-chute'] = 'Мусоропровод';
	$MESS[$strHint.'rubbish-chute'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'electricity-supply'] = 'Электричество';
	$MESS[$strHint.'electricity-supply'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'water-supply'] = 'Водопровод';
	$MESS[$strHint.'water-supply'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'gas-supply'] = 'Газ';
	$MESS[$strHint.'gas-supply'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'sewerage-supply'] = 'Канализация';
	$MESS[$strHint.'sewerage-supply'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'heating-supply'] = 'Отопление';
	$MESS[$strHint.'heating-supply'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'toilet'] = 'Туалет';
	$MESS[$strHint.'toilet'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'shower'] = 'Душ';
	$MESS[$strHint.'shower'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'pool'] = 'Бассейн';
	$MESS[$strHint.'pool'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'billiard'] = 'Бильярд';
	$MESS[$strHint.'billiard'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'sauna'] = 'Сауна';
	$MESS[$strHint.'sauna'] = 'Элемент используется для домов.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'parking'] = 'Наличие охраняемой парковки';
	$MESS[$strHint.'parking'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'parking-places'] = 'Количество предоставляемых парковочных мест';
	$MESS[$strHint.'parking-places'] = 'Количество предоставляемых парковочных мест';
$MESS[$strName.'parking-place-price'] = 'Стоимость парковочного места';
	$MESS[$strHint.'parking-place-price'] = 'Указывается стоимость одного места в месяц в рублях';
$MESS[$strName.'parking-guest'] = 'Наличие гостевых парковочных мест';
	$MESS[$strHint.'parking-guest'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'parking-guest-places'] = 'Количество гостевых парковочных мест';
	$MESS[$strHint.'parking-guest-places'] = 'Количество гостевых парковочных мест';
$MESS[$strName.'alarm'] = 'Наличие сигнализации в доме';
	$MESS[$strHint.'alarm'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'flat-alarm'] = 'Наличие сигнализации в квартире';
	$MESS[$strHint.'flat-alarm'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'security'] = 'Наличие охраны';
	$MESS[$strHint.'security'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'is-elite'] = 'Элитная недвижимость';
	$MESS[$strHint.'is-elite'] = $MESS['YANDEX_REALTY_BOOLEAN'];
	
# Just commercial
$MESS[$strHead.'HEADER_COMMERCIAL'] = 'Дополнительная информация по коммерческой недвижимости';

# Just warehouses
$MESS[$strHead.'HEADER_WAREHOUSES'] = 'Дополнительная информация по складским и производственным помещениям';
$MESS[$strName.'twenty-four-seven'] = 'Возможность круглосуточного доступа сотрудников арендатора на объект аренды 24/7';
	$MESS[$strHint.'twenty-four-seven'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'eating-facilities'] = 'Наличие предприятий общепита в здании';
	$MESS[$strHint.'eating-facilities'] = 'Элемент передается для бизнес-центров и складских комплексов.<br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'responsible-storage'] = 'Ответственное хранение';
	$MESS[$strHint.'responsible-storage'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'pallet-price'] = 'Стоимость палето-места в месяц в рублях с учетом налогов';
	$MESS[$strHint.'pallet-price'] = 'Указывается в случае ответственного хранения.';
$MESS[$strName.'freight-elevator'] = 'Наличие грузового лифта';
	$MESS[$strHint.'freight-elevator'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'truck-entrance'] = 'Возможность подъезда фуры';
	$MESS[$strHint.'truck-entrance'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'ramp'] = 'Наличие пандуса';
	$MESS[$strHint.'ramp'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'railway'] = 'Наличие ветки железной дороги';
	$MESS[$strHint.'railway'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'office-warehouse'] = 'Наличие офиса на складе';
	$MESS[$strHint.'office-warehouse'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'open-area'] = 'Наличие открытой площадки';
	$MESS[$strHint.'open-area'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'service-three-pl'] = 'Наличие 3PL (логистических) услуг';
	$MESS[$strHint.'service-three-pl'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'temperature-comment'] = 'Температурный режимг';
	$MESS[$strHint.'temperature-comment'] = 'Комментарий про температурный режим на складе.';

# Just garage
$MESS[$strHead.'HEADER_GARAGE'] = 'Дополнительная информация по гаражам';
$MESS[$strName.'garage-type'] = 'Категория гаража';
	$MESS[$strHint.'garage-type'] = 'Обязательный элемент для объявлений о продаже и аренде гаражей.<br/><br/>
Возможные значения:
<ul>
	<li>«гараж»/«garage»</li>
	<li>«машиноместо»/«parking place»</li>
	<li>«бокс»/«box»</li>
</ul>';
$MESS[$strName.'ownership-type'] = 'Статус собственности';
	$MESS[$strHint.'ownership-type'] = 'Возможные значения:
<ul>
	<li>«собственность»/«private»</li>
	<li>«кооператив»/«cooperative»</li>
	<li>«по доверенности»/«by proxy».</li>
</ul>';
$MESS[$strName.'garage-name'] = 'Название гаражно-строительного кооператива.';
	$MESS[$strHint.'garage-name'] = 'Название гаражно-строительного кооператива.';
$MESS[$strName.'parking-type'] = 'Тип парковки';
	$MESS[$strHint.'parking-type'] = 'Элемент используется только для бокса и парковочного места.<br/><br/>
Возможные значения:
<ul>
	<li>«подземная»/«underground»</li>
	<li>«наземная»/«ground»</li>
	<li>«многоуровневая»/«multilevel».</li>
</ul>';
$MESS[$strName.'automatic-gates'] = 'Наличие автоматических ворот';
	$MESS[$strHint.'automatic-gates'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'cctv'] = 'Наличие видеонаблюдения';
	$MESS[$strHint.'cctv'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'inspection-pit'] = 'Наличие смотровой ямы';
	$MESS[$strHint.'inspection-pit'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'cellar'] = 'Наличие подвала или погреба';
	$MESS[$strHint.'cellar'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'car-wash'] = 'Наличие автомойки';
	$MESS[$strHint.'car-wash'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'auto-repair'] = 'Наличие автосервиса';
	$MESS[$strHint.'auto-repair'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'new-parking'] = 'Признак гаража в новостройке';
	$MESS[$strHint.'new-parking'] = $MESS['YANDEX_REALTY_BOOLEAN'];

# Fields: Seller
$MESS[$strHead.'HEADER_SELLER'] = 'Информация о продавце или арендодателе';
$MESS[$strName.'sales-agent.name'] = 'Имя продавца, арендодателя или агента';
	$MESS[$strHint.'sales-agent.name'] = 'Имя продавца, арендодателя или агента.';
$MESS[$strName.'sales-agent.phone'] = 'Номер телефона';
	$MESS[$strHint.'sales-agent.phone'] = 'Номер указывается в международном формате.<br/><br/>
<b>Пример:</b><br/>
&lt;phone&gt;+74951234567&lt;/phone&gt;<br/><br/>
Если номеров несколько, каждый из них необходимо передавать в отдельном элементе phone.<br/><br/>
Примечание. Для агентств недвижимости обязательно должны быть указаны прямые номера агентов.';
$MESS[$strName.'sales-agent.category'] = 'Тип продавца или арендодателя';
	$MESS[$strHint.'sales-agent.category'] = 'Строго ограниченные значения:
<ul>
	<li>«агентство»/«agency»</li>
	<li>«застройщик»/«developer».</li>
</ul>
Примечание. Агентам следует указывать значение «агентство»/«agency».';
$MESS[$strName.'sales-agent.organization'] = 'Название организации';
	$MESS[$strHint.'sales-agent.organization'] = 'Название организации.';
$MESS[$strName.'sales-agent.url'] = 'Сайт агентства или застройщика';
	$MESS[$strHint.'sales-agent.url'] = 'Сайт агентства или застройщика. Например: <code>https://www.acrit-studio.ru/';
$MESS[$strName.'sales-agent.email'] = 'Электронный адрес продавца';
	$MESS[$strHint.'sales-agent.email'] = 'Электронный адрес продавца.';
$MESS[$strName.'sales-agent.photo'] = 'Ссылка на фотографию агента или логотип компании';
	$MESS[$strHint.'sales-agent.photo'] = 'Ссылка на фотографию агента или логотип компании.';



?>