<?

$strMessPrefix = 'ACRIT_EXP_TIU_RU_VENDOR_MODEL_';

// General
$MESS[$strMessPrefix . 'NAME'] = 'tiu.ru (произвольный тип)';

// Fields
$MESS[$strMessPrefix . 'FIELD_MODEL_NAME'] = 'Модель товара';
$MESS[$strMessPrefix . 'FIELD_MODEL_DESC'] = 'Модель товара.';
$MESS[$strMessPrefix . 'FIELD_VENDOR_NAME'] = 'Название производителя';
$MESS[$strMessPrefix . 'FIELD_VENDOR_DESC'] = 'Название производителя данного товара.';
$MESS[$strMessPrefix . 'FIELD_VENDOR_CODE_NAME'] = 'Код производителя';
$MESS[$strMessPrefix . 'FIELD_VENDOR_CODE_DESC'] = 'Код производителя для данного товара.';
$MESS[$strMessPrefix . 'FIELD_TYPE_PREFIX_NAME'] = 'Тип / категория товара';
$MESS[$strMessPrefix . 'FIELD_TYPE_PREFIX_DESC'] = 'Тип / категория товара (например, «мобильный телефон», «стиральная машина», «угловой диван»).';
// Fields personal
$MESS[$strMessPrefix . 'FIELD_SELLING_TYPE_NAME'] = 'Тип товара на Tiu.ru';
$MESS[$strMessPrefix . 'FIELD_SELLING_TYPE_DESC'] = 'Параметр selling_type - это тип товара на Tiu.ru. Тип товара определяет размещение товара в каталоге по признаку оптовой продажи. Тип «Услуга» предназначен для размещения услуг, предоставляемых частным лицам или компаниям. Внимание! Данный параметр используется только на Tiu.ru. Файл с данным параметром может вызывать ошибку при импорте в другие системы.  Возможные значения: r, w, u, s.<br/><br/>
<br/>
    r — «Товар продается только в розницу» для потребительских и промышленных товаров с розничными ценами.<br/>
    w — «Товар продается только оптом» для потребительских и промышленных товаров, которые продаются только оптом.<br/>
    u — «Товар продается оптом и в розницу» для товаров, которые продаются и оптом и в розницу.<br/>
    s — услуга.<br/>
';
$MESS[$strMessPrefix . 'FIELD_PRICES_VALUE_NAME'] = 'Оптовая цена - значение';
$MESS[$strMessPrefix . 'FIELD_PRICES_VALUE_DESC'] = 'Указание оптовых цен для типов товаров «Товар продается только оптом» и «Товар продается оптом и в розницу». Можно указывать несколько значений, добавление поля через [+]';
$MESS[$strMessPrefix . 'FIELD_PRICES_QUANTITY_NAME'] = 'Оптовая цена - количество';

$MESS[$strMessPrefix . 'FIELD_DISCOUNT_NAME'] = 'Скидка';
$MESS[$strMessPrefix . 'FIELD_DISCOUNT_DESC'] = 'Если у товара есть скидка, в данном поле указывается величина скидки или процент. Пример: 12.5, 30%. При наличии данного тега тег <price> является обязательным..';
$MESS[$strMessPrefix . 'FIELD_QUANTITY_IN_STOCK_NAME'] = 'Количество товара на складе';
$MESS[$strMessPrefix . 'FIELD_QUANTITY_IN_STOCK_DESC'] = 'Используется для указания остатка товаров на складе.';
?>