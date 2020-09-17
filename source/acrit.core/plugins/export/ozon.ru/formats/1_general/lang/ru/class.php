<?

$strMessPrefix = 'ACRIT_EXP_OZON_RU_GENERAL_';

// General
$MESS[$strMessPrefix . 'NAME'] = 'OZON.RU API ';
$MESS[$strMessPrefix . 'SETTINGS_TITLE'] = 'Настройки интеграции с площадкой OZON.RU';
$MESS[$strMessPrefix . 'SETTINGS_PAGE_ID'] = 'Страница или группа';
$MESS[$strMessPrefix . 'SETTINGS_CLIENT_ID'] = 'Client Id';
$MESS[$strMessPrefix . 'SETTINGS_API_KEY'] = 'API key';
$MESS[$strMessPrefix . 'SETTINGS_GET_KEY'] = 'Получить Client Id и API key';
$MESS[$strMessPrefix . 'OZON_SECTION_FROM_PROPERTY'] = 'Название раздела из свойства товара.';
$MESS[$strMessPrefix . 'OZON_SECTION_FROM_PROPERTY_HINT'] = 'Название раздела из свойства товара.
Если ваша структура разделов не позволяет сделать сопоставление с разделами ozon, вы можете указывать название раздела.
<br/><b>Важно!</b> Название раздела должно содержать полный путь разделов, вместе с родительскими разделами. Например: "Одежда / Футболки и топы / Футболка спортивная мужская"';
$MESS[$strMessPrefix . 'OZON_SECTION_CODE'] = 'Код свойства инфоблока с названием раздела.';
$MESS[$strMessPrefix . 'OZON_SECTION_CODE_HINT'] = 'Укажите символьный код свойства элемента инфоблока в котором хранится название раздела с Ozon.Ru Например "OZON_SECTION"';
$MESS[$strMessPrefix . 'FIRST_RUN_SYNC'] = 'Первоочередная синхронизация товаров с ozon.ru';
$MESS[$strMessPrefix . 'FIRST_RUN_SYNC_HINT'] = 'Если у вас уже имеются созданные товары на площадке ozon.ru, можно запустить разовую синхронизацию id товаров, чтобы при выгрузке товаров с сайта избежать задублирования товаров. Сопоставление товаров, происходит, по полю "Название"';
$MESS[$strMessPrefix . 'FIRST_RUN_SYNC_BUTTON'] = 'Запустить.';

$MESS[$strMessPrefix . 'TAB_TASKS_NAME'] = 'Статус импорта товаров';
$MESS[$strMessPrefix . 'TAB_TASKS_DESC'] = 'Статус заданий';


// Errors
$MESS[$strMessPrefix . 'ERROR_ATTRIBUTE_OPTION_VALUE'] = 'Ошибка значения поля (возможно установлено значение не из вариантов ozon)';

// Fields
$MESS[$strMessPrefix . 'FIELD_OZON_SECTION_NAME'] = 'Раздел Ozon.Ru';
$MESS[$strMessPrefix . 'FIELD_OZON_SECTION_DESC'] = 'Свойство инфолока в котором хранится название раздела Ozon.Ru(полное название, например: "Одежда / Футболки и топы / Футболка спортивная мужская")';
$MESS[$strMessPrefix . 'FIELD_OFFER_ID_NAME'] = 'Идентификатор';
$MESS[$strMessPrefix . 'FIELD_OFFER_ID_DESC'] = 'Идентификатор товара в системе продавца';
$MESS[$strMessPrefix . 'FIELD_NAME_NAME'] = 'Название товара';
$MESS[$strMessPrefix . 'FIELD_NAME_DESC'] = 'Название товара. До 500 знаков<br/>По схеме: тип + бренд или производитель + серия (если есть) + модель + артикул производителя (если есть) + особенности товара (например, цвет).';
$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_NAME'] = 'Описание товара';
$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_DESC'] = 'Для переноса строки в описании необходимо использовать HTML-тег br';
$MESS[$strMessPrefix . 'FIELD_BARCODE_NAME'] = 'Штрих-код товара в формате UPC или EAN';
$MESS[$strMessPrefix . 'FIELD_BARCODE_DESC'] = '';
$MESS[$strMessPrefix . 'FIELD_PRICE_NAME'] = 'Цена после скидок';
$MESS[$strMessPrefix . 'FIELD_PRICE_DESC'] = 'Цена после скидок (будет отображаться на карточке товара), если она равна old_price, то ее также нужно передавать. Указывается в рублях. Разделитель дробной части — точка, до двух знаков после точки';
$MESS[$strMessPrefix . 'FIELD_OLD_PRICE_NAME'] = 'Цена до скидок';
$MESS[$strMessPrefix . 'FIELD_OLD_PRICE_DESC'] = 'Цена до скидок (будет зачеркнута на карточке товара). Указывается в рублях. Разделитель дробной части — точка, до двух знаков после точки';
$MESS[$strMessPrefix . 'FIELD_PREMIUM_PRICE_NAME'] = 'Цена для клиентов с премиум подпиской';
$MESS[$strMessPrefix . 'FIELD_PREMIUM_PRICE_DESC'] = 'Цена для клиентов с премиум подпиской, указывается в рублях. Разделитель дробной части — точка, до двух знаков после точки';
$MESS[$strMessPrefix . 'FIELD_VAT_NAME'] = 'НДС';
$MESS[$strMessPrefix . 'FIELD_VAT_DESC'] = 'НДС, возможные значения: 0, 0.1, 0.2';
$MESS[$strMessPrefix . 'FIELD_VENDOR_NAME'] = 'Производитель';
$MESS[$strMessPrefix . 'FIELD_VENDOR_DESC'] = 'Производитель. До 100 знаков';
$MESS[$strMessPrefix . 'FIELD_VENDOR_CODE_NAME'] = 'Код производителя';
$MESS[$strMessPrefix . 'FIELD_VENDOR_CODE_DESC'] = 'Код производителя. До 100 знаков';
$MESS[$strMessPrefix . 'FIELD_IMAGES_NAME'] = 'Массив с изображениями';
$MESS[$strMessPrefix . 'FIELD_IMAGES_DESC'] = 'Ссылка на изображение формата http:// или https://. До 1000 знаков, форматы изображения .jpg, .png. Размер изображения по каждой стороне должен быть между 400 и 10000 px';
$MESS[$strMessPrefix . 'FIELD_HEIGHT_NAME'] = 'Высота упаковки';
$MESS[$strMessPrefix . 'FIELD_HEIGHT_DESC'] = 'Высота упаковки Предельное значение - 10 метров (или конвертированная величина в других единицах измерения)';
$MESS[$strMessPrefix . 'FIELD_DEPTH_NAME'] = 'Глубина упаковки';
$MESS[$strMessPrefix . 'FIELD_DEPTH_DESC'] = 'Глубина упаковки Предельное значение - 10 метров (или конвертированная величина в других единицах измерения)';
$MESS[$strMessPrefix . 'FIELD_WIDTH_NAME'] = 'Ширина упаковки';
$MESS[$strMessPrefix . 'FIELD_WIDTH_DESC'] = 'Ширина упаковки. Предельное значение - 10 метров (или конвертированная величина в других единицах измерения)';
$MESS[$strMessPrefix . 'FIELD_DIMENSION_UNIT_NAME'] = 'Единица измерения габаритов';
$MESS[$strMessPrefix . 'FIELD_DIMENSION_UNIT_DESC'] = 'Единица измерения габаритов. Доступные варианты: mm (миллиметры), cm (сантиметры), in (дюймы)';
$MESS[$strMessPrefix . 'FIELD_DIMENSION_UNIT_ALLOWED_VALUES'] = 'mm (миллиметры), cm (сантиметры), in (дюймы)';
$MESS[$strMessPrefix . 'FIELD_WEIGHT_NAME'] = 'Вес товара в упаковке';
$MESS[$strMessPrefix . 'FIELD_WEIGHT_DESC'] = 'Вес товара в упаковке. Предельное значение - 1000 килограмм (или конвертированная величина в других единицах измерения)';
$MESS[$strMessPrefix . 'FIELD_WEIGHT_UNIT_NAME'] = 'Единицы измерения веса';
$MESS[$strMessPrefix . 'FIELD_WEIGHT_UNIT_DESC'] = 'Единицы измерения веса. Доступные варианты: g (граммы), kg (килограммы), lb (фунты)';
$MESS[$strMessPrefix . 'FIELD_WEIGHT_UNIT_ALLOWED_VALUES'] = 'g (граммы), kg (килограммы), lb (фунты)';
$MESS[$strMessPrefix . 'FIELD_IS_MULTIPLE'] = '(Поле множественное) <br/>';
$MESS[$strMessPrefix . 'RELOAD_STATUS'] = 'Обновить статусы товаров ';
$MESS[$strMessPrefix . 'STATUS_1'] = 'Новый';
$MESS[$strMessPrefix . 'STATUS_2'] = 'Загрузка';
$MESS[$strMessPrefix . 'STATUS_3'] = 'Обработка';
$MESS[$strMessPrefix . 'STATUS_4'] = 'На модерации';
$MESS[$strMessPrefix . 'STATUS_5'] = 'Обработан';
$MESS[$strMessPrefix . 'STATUS_6'] = 'Ошибка модерации';
$MESS[$strMessPrefix . 'STATUS_7'] = 'Ошибка валидации';
$MESS[$strMessPrefix . 'STATUS_8'] = 'Ошибка';
$MESS[$strMessPrefix . 'STATUS_9'] = 'Импортирован';
$MESS[$strMessPrefix . 'EXPORTED_ALL'] = 'Выгружено всего';
$MESS[$strMessPrefix . 'STATUS_EXPORTED'] = 'Статусы импорта товаров';
?>