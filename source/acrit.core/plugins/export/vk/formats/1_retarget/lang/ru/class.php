<?
$strMessPrefix = 'ACRIT_EXP_VK_RETARGETING_';

// General
$MESS[$strMessPrefix.'NAME'] = 'ВКонтакте (динамический ретаргетинг)';

// Default settings
$MESS[$strMessPrefix.'SHOP_NAME'] = 'Короткое название магазина';
$MESS[$strMessPrefix.'SHOP_NAME_HINT'] = 'Короткое название магазина, <b>не более 20 символов</b>';
$MESS[$strMessPrefix.'SHOP_COMPANY'] = 'Полное наименование компании';
$MESS[$strMessPrefix.'SHOP_COMPANY_HINT'] = 'Полное наименование компании, владеющей магазином. ';
$MESS[$strMessPrefix.'SHOP_URL'] = 'Адрес магазина';
$MESS[$strMessPrefix.'SHOP_URL_HINT'] = 'Адрес магазина';

$MESS[$strMessPrefix.'RETARGETING_CURRENCY'] = 'Код валюты';

//$MESS[$strMessPrefix.'DELIVERY_HINT'] = 'Укажите здесь общие условия доставки.';
$MESS[$strMessPrefix.'SETTINGS_FILE'] = 'Итоговый файл';
$MESS[$strMessPrefix.'SETTINGS_ENCODING'] = 'Кодировка файла';
$MESS[$strMessPrefix.'SETTINGS_ENCODING_HINT'] = 'Выберите кодировку файла. Принципиальной разницы между кодировками нет.';
$MESS[$strMessPrefix.'NO_EXPORT_FILE_SPECIFIED'] = 'Не указан путь к итоговому файлу.';

# Steps
$MESS[$strMessPrefix.'STEP_EXPORT'] = 'Запись в XML-файл';

# Display results
$MESS[$strMessPrefix.'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix.'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix.'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix.'RESULT_DATETIME'] = 'Время окончания';

// Fields

$MESS[$strMessPrefix.'FIELD_PRODUCTNAME_NAME'] = 'Наименование товара';
$MESS[$strMessPrefix.'FIELD_PRODUCTNAME_DESC'] = 'Наименование товара; Может быть любого размера, но видимы будут только первые 25 символов.';
$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_NAME'] = 'Код валюты';
$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_DESC'] = 'Валюта, в которой указана цена товара: RUB, USD, EUR, UAH, KZT, BYN. Цена и валюта должны соответствовать друг другу. Например, вместе с USD надо указывать цену в долларах, а не в рублях.<br/><br/><b>Примечание</b>. В текстовом формате нет возможности указать свои условия конвертации валют. При показе цены покупателю она будет пересчитана в нужную валюту по текущему курсу ЦБ РФ.';

// Fields
$MESS[$strMessPrefix.'FIELD_NAME_NAME'] = 'Наименование товара';
$MESS[$strMessPrefix.'FIELD_NAME_DESC'] = 'Наименование товара, которое будет отображаться в заголовке карточки карусели. Может быть любого размера, но видимы будут только первые 25 символов.';
$MESS[$strMessPrefix.'FIELD_URL_NAME'] = 'Заголовок публикации.';
$MESS[$strMessPrefix.'FIELD_URL_DESC'] = 'Ссылка на страницу товара на сайте, может включать в себя метки, в объявление будет подтягиваться размеченная ссылка. Заменить/удалить метки можно как непосредственно в фиде, так и в настройках прайс-листа в рекламном кабинете. Максимальная длина - 512 символов. Ссылки в фиде должны совпадать со ссылками в meta_tags на страницах сайта и со ссылками, ведущими на страницы соответствующих товаров на сайте. Это нужно для корректной работы Упрощенного сбора аудитории.';
$MESS[$strMessPrefix.'FIELD_PICTURE_NAME'] = 'Изображение.';
$MESS[$strMessPrefix.'FIELD_PICTURE_DESC'] = 'Ссылка на изображение товара, которое будет показываться в рекламном объявлении. Рекомендуемый размер: квадратное изображение от 400*400. Минимальный допустимый размер: хотя бы одна сторона изображения должна быть не менее 200 пикселей. Если изображение прямоугольное, оно достраивается до квадрата с помощью белых полей.';
$MESS[$strMessPrefix.'FIELD_ID_NAME'] = 'Уникальный идентификатор';
$MESS[$strMessPrefix.'FIELD_ID_DESC'] = 'Уникальный идентификатор, который должен совпадать с product id соответствующего товара в событиях на сайте. Длина идентификатора составляет не более 20 символов, может включать в себя только цифры и английские буквы.';

//optional fields
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена товара';
$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена товара';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_NAME'] = 'Описание предложения';
$MESS[$strMessPrefix.'FIELD_AVAILABLE_NAME'] = 'Товар в наличии';
$MESS[$strMessPrefix.'FIELD_AVAILABLE_DESC'] = 'Может быть равно true (это значит, что товар есть в наличии) и false (товара в наличии нет). Товары, которых нет в наличии, не будут показываться в рекламных объявлениях.';
$MESS[$strMessPrefix.'FIELD_GROUP_ID_NAME'] = 'Объединяет группу схожих товаров.';
$MESS[$strMessPrefix.'FIELD_GROUP_ID_DESC'] = 'Объединяет группу схожих товаров.';
$MESS[$strMessPrefix.'FIELD_AGE_NAME'] = 'Возрастная категория товара.';
$MESS[$strMessPrefix.'FIELD_AGE_DESC'] = 'Возрастная категория товара.';
$MESS[$strMessPrefix.'FIELD_REC_NAME'] = 'Рекомендованные товары.';
$MESS[$strMessPrefix.'FIELD_REC_DESC'] = 'Рекомендованные товары.';
$MESS[$strMessPrefix.'FIELD_VENDORCODE_NAME'] = 'Код производителя.';
$MESS[$strMessPrefix.'FIELD_VENDORCODE_DESC'] = 'Код производителя.';
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Идентификатор категории.';
$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Идентификатор категории.';

?>
