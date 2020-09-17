<?

$strMessPrefix = 'ACRIT_EXP_OZON_RU_QUANTITY_';

// General
$MESS[$strMessPrefix . 'NAME'] = 'OZON.RU API остатки';
$MESS[$strMessPrefix . 'SETTINGS_TITLE'] = 'Настройки интеграции с площадкой OZON.RU';
$MESS[$strMessPrefix . 'SETTINGS_CLIENT_ID'] = 'Client Id';
$MESS[$strMessPrefix . 'SETTINGS_API_KEY'] = 'API key';
$MESS[$strMessPrefix . 'SETTINGS_GET_KEY'] = 'Получить Client Id и API key';


// Fields
$MESS[$strMessPrefix . 'FIELD_OFFER_ID_NAME'] = 'Идентификатор';
$MESS[$strMessPrefix . 'FIELD_OFFER_ID_DESC'] = 'Идентификатор товара в системе продавца';
$MESS[$strMessPrefix . 'FIELD_QUANTITY_NAME'] = 'Количество товара в наличии';
$MESS[$strMessPrefix . 'FIELD_QUANTITY_DESC'] = 'Задать наличие товару возможно только после того, как статус товара сменился на "processed"';
$MESS[$strMessPrefix . 'FIELD_PRICE_NAME'] = 'Цена после скидок';
$MESS[$strMessPrefix . 'FIELD_PRICE_DESC'] = 'Цена после скидок (будет отображаться на карточке товара), если она равна old_price, то ее также нужно передавать. Указывается в рублях. Разделитель дробной части — точка, до двух знаков после точки';
$MESS[$strMessPrefix . 'FIELD_OLD_PRICE_NAME'] = 'Цена до скидок';
$MESS[$strMessPrefix . 'FIELD_OLD_PRICE_DESC'] = 'Цена до скидок (будет зачеркнута на карточке товара). Указывается в рублях. Разделитель дробной части — точка, до двух знаков после точки';
$MESS[$strMessPrefix . 'FIELD_PREMIUM_PRICE_NAME'] = 'Цена для клиентов с премиум подпиской';
$MESS[$strMessPrefix . 'FIELD_PREMIUM_PRICE_DESC'] = 'Цена для клиентов с премиум подпиской, указывается в рублях. Разделитель дробной части — точка, до двух знаков после точки';
$MESS[$strMessPrefix . 'FIELD_VAT_NAME'] = 'НДС';
$MESS[$strMessPrefix . 'FIELD_VAT_DESC'] = 'НДС, возможные значения: 0, 0.1, 0.2';
?>