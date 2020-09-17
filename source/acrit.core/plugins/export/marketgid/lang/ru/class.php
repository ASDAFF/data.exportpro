<?

$strMessPrefix = 'ACRIT_EXP_MARKETGID_';

// General
$MESS[$strMessPrefix . 'NAME'] = 'MarketGid';


// Default settings
$MESS[$strMessPrefix . 'SETTINGS_FILE'] = 'Итоговый файл';
$MESS[$strMessPrefix . 'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/xml/MarketGid.xml';
$MESS[$strMessPrefix . 'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/file.xml</code>';
$MESS[$strMessPrefix . 'SETTINGS_ENCODING'] = 'Кодировка файла';
$MESS[$strMessPrefix . 'SETTINGS_ENCODING_HINT'] = 'Выберите кодировку файла. Принципиальной разницы между кодировками нет.';
$MESS[$strMessPrefix . 'SETTINGS_ZIP'] = 'Упаковать в Zip';
$MESS[$strMessPrefix . 'SETTINGS_ZIP_HINT'] = 'Данный параметр позволяет запаковать сформированный файл в Zip. Благодаря упаковке в Zip-архив, размер файла, отдаваемого в Яндекс.Маркет, существенно уменьшается, что ускоряет его скачивание сервисом.';
$MESS[$strMessPrefix . 'SETTINGS_DELETE_XML_IF_ZIP'] = 'Удалить XML-файл';
$MESS[$strMessPrefix . 'SETTINGS_DELETE_XML_IF_ZIP_HINT'] = 'Данная опция позволяет удалить сгенерированный XML-файл, оставив только ZIP-архив.';


// Fields
$MESS[$strMessPrefix . 'FIELD_ID_NAME'] = 'Идентификатор товара';
$MESS[$strMessPrefix . 'FIELD_ID_DESC'] = '	Атрибут тега teaser - обязательный. Уникальный идентификатор товара, который размещен в коде датчика на странице товара. Допустимые значения [0-9, A-Z, a-z, -, _]  ';
$MESS[$strMessPrefix . 'FIELD_AVAILABLE_NAME'] = 'Наличие товара';
$MESS[$strMessPrefix . 'FIELD_AVAILABLE_DESC'] = 'Атрибут тега teaser - НЕ обязательный. Значение по умолчанию = false Статус товара. Допустимые значения [0, 1, true, false]. При значении false тизеры не создаются.     ';
$MESS[$strMessPrefix . 'FIELD_NAME_NAME'] = 'Заголовок тизера';
$MESS[$strMessPrefix . 'FIELD_NAME_DESC'] = 'Тег- обязательный. Заголовок тизера. В идеале не должен превышать 65 символов, иначе будет обрезан до 65 средствами системы (до окончания последнего слова, не превышающего 65 символов). Допустимы те же символы, что и в создании тизера. ';
$MESS[$strMessPrefix . 'FIELD_URL_NAME'] = 'Ссылка';
$MESS[$strMessPrefix . 'FIELD_URL_DESC'] = 'Тег- обязательный. Ссылка на страницу товара.';
$MESS[$strMessPrefix . 'FIELD_PRICE_NAME'] = 'Цена товара';
$MESS[$strMessPrefix . 'FIELD_PRICE_DESC'] = 'Цена товара в указанной валюте. Допустимые значения - [0-9]';

$MESS[$strMessPrefix . 'FIELD_CURRENCY_ID_NAME'] = 'Код валюты';
$MESS[$strMessPrefix . 'FIELD_CURRENCY_ID_DESC'] = 'Идентификатор валюты, в который указана цена товара. Значение по умолчанию = USD. Должен принимать значение из списка:RUB,UAH,USD,EUR,BYN,INR,ILS,GEL,KZT,AED';
$MESS[$strMessPrefix . 'FIELD_PICTURE_NAME'] = 'Картинка';
$MESS[$strMessPrefix . 'FIELD_PICTURE_DESC'] = 'Тег- обязательный. Ссылка на изображение товара. Само изображение должно быть не меньше чем 492x328. В идеале оно должно соответствовать этому размеру. Допустымые расширения: *.jpg, *.jpeg     ';

$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_NAME'] = 'Описание';
$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_DESC'] = 'Тег- обязательный. Рекламный текст (короткое описание). В идеале не должен превышать  75 символов, иначе будет обрезан до 75 средствами системы (до окончания последнего слова, не превышающего  75 символов). Допустимы те же символы, что и в создании тизер';

$MESS[$strMessPrefix . 'FIELD_GROUP_ID_NAME'] = 'Идентификатор категории товара';
$MESS[$strMessPrefix . 'FIELD_GROUP_ID_DESC'] = 'Тег - обязательный. Уникальный идентификатор категории товара. Так же можно указать идентификатор категории, используемый в системе Маркетгид.   ';

# Steps
$MESS[$strMessPrefix . 'STEP_EXPORT'] = 'Запись в XML-файл';
$MESS[$strMessPrefix . 'STEP_ZIP'] = 'Архивация в Zip';

# Display results
$MESS[$strMessPrefix . 'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix . 'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix . 'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix . 'RESULT_DATETIME'] = 'Время окончания';
$MESS[$strMessPrefix . 'RESULT_FILE_ZIP'] = 'Скачать ZIP-архив';

#
$MESS[$strMessPrefix . 'NO_EXPORT_FILE_SPECIFIED'] = 'Не указан путь к итоговому файлу.';
$MESS[$strMessPrefix . 'WRONG_VALUE_FOR_AGE_YEAR'] = 'Некорректное значнеия для тега «age» (unit=«year»): #TEXT#';
$MESS[$strMessPrefix . 'WRONG_VALUE_FOR_AGE_MONTH'] = 'Некорректное значнеия для тега «age» (unit=«month»): #TEXT#';
$MESS[$strMessPrefix . 'GIFTS_ARE_NOT_FOUND'] = 'Подарки не найдены.';
$MESS[$strMessPrefix . 'CATEGORIES_EMPTY_ANSWER'] = 'Ошибка получения категорий ( #URL# ). Попробуйте еще раз.';
$MESS[$strMessPrefix . 'ERROR_SAVING_CATEGORIES_TMP'] = 'Ошибка сохранения временного файла категорий: #FILE#. Проверьте наличие доступа для записи в этот файл.';
$MESS[$strMessPrefix . 'CATEGORIES_ARE_EMPTY'] = 'Загруженный файл #URL# не содержит категорий. Попробуйте еще раз.';
$MESS[$strMessPrefix . 'ERROR_SAVING_CATEGORIES_TMP'] = 'Ошибка сохранения файла с категориями: #FILE#. Проверьте наличие доступа для записи в этот файл.';
?>