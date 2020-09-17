<?

$strMessPrefix = 'ACRIT_EXP_APORT_RU_GENERAL_';

// General
$MESS[$strMessPrefix . 'NAME'] = 'Aport.Ru';

// Default settings
$MESS[$strMessPrefix . 'SETTINGS_SHOP_NAME'] = 'Название магазина';
$MESS[$strMessPrefix . 'SETTINGS_SHOP_NAME_HINT'] = 'Укажите здесь заголовок файла (тег name).';
$MESS[$strMessPrefix . 'SETTINGS_SHOP_RATE'] = 'Курс доллара';
$MESS[$strMessPrefix . 'SETTINGS_SHOP_RATE_HINT'] = 'Обязателен, если цены в прайс-листе даны в долларах. Если цены даны в гривнах, можно оставить пустым либо не использовать этот элемент';

$MESS[$strMessPrefix . 'SETTINGS_FILE'] = 'Итоговый файл';
$MESS[$strMessPrefix . 'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/xml/aport_ru.xml';
$MESS[$strMessPrefix . 'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/aport_ru</code>';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN'] = 'Открыть файл';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN_TITLE'] = 'Файл откроется в новой вкладке';

$MESS[$strMessPrefix . 'FIELD_AVAILABLE_VALUE_ON'] = 'Склад';
$MESS[$strMessPrefix . 'FIELD_AVAILABLE_VALUE_OFF'] = 'Заказ';
// Fields
$MESS[$strMessPrefix . 'FIELD_ID_NAME'] = 'Идентификатор товара';
$MESS[$strMessPrefix . 'FIELD_ID_DESC'] = 'Идентификатор товарного предложения в базе магазина.';
$MESS[$strMessPrefix . 'FIELD_NAME_NAME'] = 'Наименование товара';

$MESS[$strMessPrefix . 'FIELD_SECTION_ID_NAME'] = 'Идентификатор категории товара';
$MESS[$strMessPrefix . 'FIELD_SECTION_ID_DESC'] = 'Товар может принадлежать только к одной категории. Конечная категория, к которой отнесен товар в прайс-листе, должна соответствовать таковой в каталоге Хотлайн:<a href="https://hotline.ua/download/hotline/hotline_tree.csv" target="_blank">Каталог hotline.ua</a>';
$MESS[$strMessPrefix . 'FIELD_VENDOR_NAME'] = 'Производитель товара';
$MESS[$strMessPrefix . 'FIELD_VENDOR_DESC'] = 'Допускается указание только одного производителя товара. В элементе <vendor> не разрешается указание страны-производителя товара.';

$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_NAME'] = 'Описание товара';
$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_DESC'] = 'Не допускается размещение в описании товара рекламной информации (о доставке, скидках, акциях, и т.п.), любого рода контактной информации;';
$MESS[$strMessPrefix . 'FIELD_URL_NAME'] = 'Ссылка на товар';
$MESS[$strMessPrefix . 'FIELD_URL_DESC'] = 'Ссылка перехода на страницу товара на сайте магазина';
$MESS[$strMessPrefix . 'FIELD_PICTURE_NAME'] = 'Изображения товара';
$MESS[$strMessPrefix . 'FIELD_PICTURE_DESC'] = 'Ссылка на изображение товара на сайте магазина<br/>
Возможные форматы изображения товара: JPEG (предпочтительно) или GIF/PNG (без прозрачных областей). Путь к файлу изображения должен содержать только латинские буквы, цифры, знак «минус», знак подчеркивания. Запрещается указывать ссылки на изображения, не имеющие отношения к внешнему виду товара.';
$MESS[$strMessPrefix . 'FIELD_PRICE_NAME'] = 'Актуальная розничная цена товара в гривнах с учетом всех налогов';
$MESS[$strMessPrefix . 'FIELD_PRICE_USD_NAME'] = 'Актуальная розничная цена в долларах';
$MESS[$strMessPrefix . 'FIELD_PRICE_USD_DESC'] = 'Если цены в прайс-листе даны только в долларах, обязательно указывать курс пересчета в элементе <rate>';

$MESS[$strMessPrefix . 'FIELD_BN_PRICE_RUB_NAME'] = 'Безналичная цена (рубль)';

$MESS[$strMessPrefix . 'FIELD_AVAILABLE_NAME'] = 'Наличие товара';
$MESS[$strMessPrefix . 'FIELD_AVAILABLE_DESC'] = 'Возможные значения:<br/>
В наличии. Этот статус следует указывать, если товар физически находится на складе магазина или местного партнера (поставщика), и магазин готов начать процесс доставки немедленно<br/>
Под заказ. Товар отсутствует на складе магазина, и магазину необходимо время для заказа и получения товара от своего поставщика. С помощью атрибута days=" " можно указать количество дней от заказа товара покупателем до начала процесса доставки.<br/>
';

$MESS[$strMessPrefix . 'FIELD_GUARANTEE_NAME'] = 'срок и тип гарантии (официальная от производителя или собственная от магазина)';
$MESS[$strMessPrefix . 'FIELD_GUARANTEE_DESC'] = 'По умолчанию срок гарантии указывается в месяцах. Если необходимо указать срок гарантии в днях, следует использовать guarantee_days<br/>
С помощью атрибута guarantee_type можно указать тип гарантии';

$MESS[$strMessPrefix . 'FIELD_GUARANTEE_DAYS_NAME'] = 'Если необходимо указать срок гарантии в днях';
$MESS[$strMessPrefix . 'FIELD_GUARANTEE_TYPE_NAME'] = 'Тип гарантии';
$MESS[$strMessPrefix . 'FIELD_GUARANTEE_TYPE_DESC'] = 'Где "Тип гарантии" может принимать значения "manufacturer" – от производителя или "shop" – от магазина.';

$MESS[$strMessPrefix . 'FIELD_PARAM_MANUF_COUNTRY_NAME'] = 'Страна изготовления товара';
$MESS[$strMessPrefix . 'FIELD_PARAM_MANUF_COUNTRY_FIELD'] = 'Страна изготовления';





# Steps
$MESS[$strMessPrefix . 'STEP_EXPORT'] = 'Запись в XML-файл';

# Display results
$MESS[$strMessPrefix . 'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix . 'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix . 'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix . 'RESULT_DATETIME'] = 'Время окончания';

#
$MESS[$strMessPrefix . 'NO_EXPORT_FILE_SPECIFIED'] = 'Не указан путь к итоговому файлу.';
?>