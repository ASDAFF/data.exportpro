<?

$strMessPrefix = 'ACRIT_EXP_HOTLINE_UA_GENERAL_';

// General
$MESS[$strMessPrefix . 'NAME'] = 'hotline.ua';

// Default settings
$MESS[$strMessPrefix . 'SETTINGS_SHOP_NAME'] = 'Название магазина';
$MESS[$strMessPrefix . 'SETTINGS_SHOP_NAME_HINT'] = 'Укажите здесь заголовок файла (тег name).';
$MESS[$strMessPrefix . 'SETTINGS_SHOP_ID'] = 'Уникальный ID (код) магазина';
$MESS[$strMessPrefix . 'SETTINGS_SHOP_ID_HINT'] = 'Уникальный ID (код) магазина, указан в Вашем аккаунте и в текстах почтовых уведомлений';
$MESS[$strMessPrefix . 'SETTINGS_SHOP_RATE'] = 'Курс доллара';
$MESS[$strMessPrefix . 'SETTINGS_SHOP_RATE_HINT'] = 'Обязателен, если цены в прайс-листе даны в долларах. Если цены даны в гривнах, можно оставить пустым либо не использовать этот элемент';

$MESS[$strMessPrefix . 'SETTINGS_DELIVERY'] = 'Доставки (валюта - грн.)';
$MESS[$strMessPrefix . 'FIELD_ADD_NEW_DELIVERY'] = 'Добавить';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_HINT'] = '';
$MESS[$strMessPrefix . 'SETTINGS_FILE'] = 'Итоговый файл';
$MESS[$strMessPrefix . 'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/xml/hotline.xml';
$MESS[$strMessPrefix . 'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/hotline.xml</code>';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN'] = 'Открыть файл';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN_TITLE'] = 'Файл откроется в новой вкладке';

$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_TYPE_PICKUP'] = 'самовывоз из пункта выдачи';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_TYPE_WAREHOUSE'] = 'на склад перевозчика (или в почтомат)';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_TYPE_ADDRESS'] = 'доставка по адресу пользователя, курьером или перевозчиком';

$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_'] = '';

$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_CAT'] = 'CAT';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_DF'] = 'Delfast';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_DHL'] = 'DHL';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_IP'] = 'InPost 24/7';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_ND'] = 'nextDay';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_PP'] = 'PickPoint';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_TMM'] = 'TMM Express';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_AL'] = 'Автолюкс';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_VC'] = 'Ваш Час';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_VP'] = 'Ваша Почта';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_GU'] = 'Гюнсел';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_DA'] = 'Деливери';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_ЕЕ'] = 'ЕвроЭкспресс';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_ZD'] = 'Зручна доставка';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_IT'] = 'Ин-Тайм';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_CE'] = 'Карго Экспресс';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_KSD'] = 'КСД';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_ME'] = 'Мист Экспресс';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_NP'] = 'Новая почта';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_NE'] = 'Ночной Экспресс';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_PE'] = 'Пони Экспресс';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_PB'] = 'ПриватБанк';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_MET'] = 'СЦ ТОЧКА';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_UPG'] = 'Украинская почтовая группа';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_UP'] = 'Укрпочта';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_EM'] = 'Экспресс Мейл';
$MESS[$strMessPrefix . 'SETTINGS_DELIVERY_CARRIER_YT'] = 'ЯрТранс Лоджистик';

$MESS[$strMessPrefix . 'FIELD_AVAILABLE_VALUE_ON'] = 'В наличии.';
$MESS[$strMessPrefix . 'FIELD_AVAILABLE_VALUE_OFF'] = 'Под заказ.';
// Fields
$MESS[$strMessPrefix . 'FIELD_ID_NAME'] = 'Идентификатор товара';
$MESS[$strMessPrefix . 'FIELD_ID_DESC'] = 'Идентификатор товарного предложения в базе магазина.<br/>
Длина – до 20 символов, может содержать цифры, латинские буквы, знаки - (минус) и _ (подчеркивание). Должен быть уникальным и неизменным для одного и того же товара от загрузки к загрузке прайс-листа.<br/>
<id>3278</id>';
$MESS[$strMessPrefix . 'FIELD_SECTION_ID_NAME'] = 'Идентификатор категории товара из блока';
$MESS[$strMessPrefix . 'FIELD_SECTION_ID_DESC'] = 'Товар может принадлежать только к одной категории. Конечная категория, к которой отнесен товар в прайс-листе, должна соответствовать таковой в каталоге Хотлайн:<a href="https://hotline.ua/download/hotline/hotline_tree.csv" target="_blank">Каталог hotline.ua</a>';
$MESS[$strMessPrefix . 'FIELD_BARCODE_NAME'] = 'Штрихкод товара';
$MESS[$strMessPrefix . 'FIELD_BARCODE_DESC'] = 'штрихкод товара, указанный производителем';
$MESS[$strMessPrefix . 'FIELD_CODE_NAME'] = 'Код модели (артикул от производителя)';
$MESS[$strMessPrefix . 'FIELD_CODE_DESC'] = '	код модели (артикул от производителя)
Обязателен в случаях, описанных в <a href="https://hotline.ua/about/pricelists_recommendations/" target="_blank">Требованиях к контенту прайс-листов.</a>';
$MESS[$strMessPrefix . 'FIELD_VENDOR_NAME'] = 'Производитель товара';
$MESS[$strMessPrefix . 'FIELD_VENDOR_DESC'] = 'Допускается указание только одного производителя товара. В элементе <vendor> не разрешается указание страны-производителя товара.';
$MESS[$strMessPrefix . 'FIELD_NAME_NAME'] = 'Название товара';
$MESS[$strMessPrefix . 'FIELD_NAME_DESC'] = 'Допускается указание только одной модели товара, без перечислений. Запрещается указывать любую рекламную и другую информацию, не относящуюся к наименованию товара.<br/>
Подробнее о корректном наименовании моделей товаров смотрите в <a href="https://hotline.ua/about/pricelists_recommendations/" target="_blank">Требованиях к контенту прайс-листов.</a>';
$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_NAME'] = 'Описание товара';
$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_DESC'] = 'Запрещается указывать слова, не относящиеся к описанию товара (кроме определения размера, конфигурации, комплектации).';
$MESS[$strMessPrefix . 'FIELD_URL_NAME'] = 'Ссылка на товар';
$MESS[$strMessPrefix . 'FIELD_URL_DESC'] = 'Ссылка перехода на страницу товара на сайте магазина';
$MESS[$strMessPrefix . 'FIELD_PICTURE_NAME'] = 'Изображения товара';
$MESS[$strMessPrefix . 'FIELD_PICTURE_DESC'] = 'Ссылка на изображение товара на сайте магазина<br/>
Возможные форматы изображения товара: JPEG (предпочтительно) или GIF/PNG (без прозрачных областей). Путь к файлу изображения должен содержать только латинские буквы, цифры, знак «минус», знак подчеркивания. Запрещается указывать ссылки на изображения, не имеющие отношения к внешнему виду товара.';
$MESS[$strMessPrefix . 'FIELD_PRICE_NAME'] = 'Актуальная розничная цена товара в гривнах с учетом всех налогов';
$MESS[$strMessPrefix . 'FIELD_PRICE_OLD_NAME'] = 'Розничная цена до скидки в грн.';
$MESS[$strMessPrefix . 'FIELD_PRICE_OLD_DESC'] = 'Розничная цена до скидки в грн. Подается только в гривневом эквиваленте, должна быть выше, чем действующая цена на товар, на сайте отображается в виде перечеркнутой цены рядом с действующей';
$MESS[$strMessPrefix . 'FIELD_PRICE_USD_NAME'] = 'Актуальная розничная цена в долларах';
$MESS[$strMessPrefix . 'FIELD_PRICE_USD_DESC'] = 'Если цены в прайс-листе даны только в долларах, обязательно указывать курс пересчета в элементе <rate>';
$MESS[$strMessPrefix . 'FIELD_AVAILABLE_NAME'] = 'Наличие товара';
$MESS[$strMessPrefix . 'FIELD_AVAILABLE_DESC'] = 'Возможные значения:<br/>
В наличии. Этот статус следует указывать, если товар физически находится на складе магазина или местного партнера (поставщика), и магазин готов начать процесс доставки немедленно<br/>
Под заказ. Товар отсутствует на складе магазина, и магазину необходимо время для заказа и получения товара от своего поставщика. С помощью атрибута days=" " можно указать количество дней от заказа товара покупателем до начала процесса доставки.<br/>
';

$MESS[$strMessPrefix . 'FIELD_STOCK_DAYS_NAME'] = 'Магазин готов начать процесс доставки через N дней';
$MESS[$strMessPrefix . 'FIELD_STOCK_DAYS_DESC'] = 'Указывается токлько если "Наличие товара" = "Под заказ." ';
$MESS[$strMessPrefix . 'FIELD_GUARANTEE_NAME'] = 'срок и тип гарантии (официальная от производителя или собственная от магазина)';
$MESS[$strMessPrefix . 'FIELD_GUARANTEE_DESC'] = 'По умолчанию срок гарантии указывается в месяцах. Если необходимо указать срок гарантии в днях, следует использовать guarantee_days<br/>
С помощью атрибута guarantee_type можно указать тип гарантии';

$MESS[$strMessPrefix . 'FIELD_GUARANTEE_DAYS_NAME'] = 'Если необходимо указать срок гарантии в днях';
$MESS[$strMessPrefix . 'FIELD_GUARANTEE_TYPE_NAME'] = 'Тип гарантии';
$MESS[$strMessPrefix . 'FIELD_GUARANTEE_TYPE_DESC'] = 'type="manufacturer" - товар обеспечивается официальной гарантией производителя
type="shop" - товар обеспечивается гарантией магазина';
$MESS[$strMessPrefix . 'FIELD_PARAM_ORIGINAL_NAME'] = 'Оригинальность товара';
$MESS[$strMessPrefix . 'FIELD_PARAM_ORIGINAL_DESC'] = 'Данный параметр используется для разделения в прайс-листе оригинальных товаров и их реплик (копий).
<param name="Оригинальность">Оригинал</param>';
$MESS[$strMessPrefix . 'FIELD_PARAM_MANUF_COUNTRY_NAME'] = 'Страна изготовления товара';

$MESS[$strMessPrefix . 'FIELD_DELIVERY_ID_NAME'] = 'ID';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_ID_DESC'] = 'Уникальный идентификатор способа доставки. Целое число.';

$MESS[$strMessPrefix . 'FIELD_DELIVERY_TYPE_NAME'] = 'Тип доставки';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_TYPE_DESC'] = 'Доступны следующие 3 типа доставки:<br/>
pickup – самовывоз из пункта выдачи. Пользователю будет предложен список из точек продаж вашего магазина, для которых установлен тип "пункт выдачи" или "магазин и пункт выдачи". Добавить или изменить точки продаж вашего магазина вы можете в разделе <a href="https://hotline.ua/cabinet/edit-stores/" target="_blank">Анкета магазина</a> в кабинете на hotline.ua;<br/>
warehouse – на склад перевозчика (или в почтомат). Для данного значения является обязательным указание параметра carrier;<br/>
address – доставка по адресу пользователя, курьером или перевозчиком. Для данного значения является обязательным указание параметра carrier.
Если ваш магазин является участником системы Hotline Checkout, то, в зависимости от выбранного типа, пользователю будет предложено заполнить адрес или указать склад получения товара при оформлении заказа.';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_COST_NAME'] = 'Cтоимость';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_COST_DESC'] = 'Укажите числом с плавающей точкой стоимость доставки в грн. Если указан 0 в качестве стоимости - доставка производится бесплатно, если указан null - "По тарифам перевозчика". Если данный параметр не указан, то способ доставки будет недоступен пользователям hotline.ua';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_FREEFROM_NAME'] = 'бесплатно от  (сумма заказа)';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_FREEFROM_DESC'] = 'Укажите числом сумму заказа в грн (например, 299.99), от которой доставка осуществляется бесплатно. Если в заказе, оформляемом через Hotline Checkout, несколько товаров, их стоимость будет просуммирована.
Если данный параметр не указан, считается, что все товары доставляются платно, вне зависимости от суммы заказа.';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_TIME_NAME'] = 'Срок';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_TIME_DESC'] = 'При помощи значений 1, 2, 3 или 4 укажите срок доставки. Где<br/>
1 = 1-3 дня<br/>
2 = 4-9 дней<br/>
3 = 10-14 дней<br/>
4 = 0-24 часа<br/>
При указании способа 4 к названию доставки будет добавлена отметка об экспресс-доставке.<br/>
Если данный параметр не указан, то способ доставки будет недоступен пользователям hotline.ua.';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_INCHECKOUT_NAME'] = 'Hotline Checkout';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_INCHECKOUT_DESC'] = 'Стоимость доставки включается в сумму заказа для оплаты через Hotline Checkout;
false – стоимость доставки не включается в сумму заказа для оплаты через Hotline Checkout. При этом указанная стоимость доставки будет показана пользователю с отметкой о необходимости оплатить ее отдельно наличными при получении товара.
Примечание: если вы не указали данный параметр и его значение, по умолчанию стоимость доставки включается в сумму заказа для оплаты через Hotline Checkout.';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_REGION_NAME'] = 'Географический регион Украины';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_REGION_DESC'] = 'Области Украины, АР Крым, а также города, указываются одним или несколькими <a href="https://ru.wikipedia.org/wiki/%D0%9F%D0%BE%D1%87%D1%82%D0%BE%D0%B2%D0%BE%D0%B5_%D0%B4%D0%B5%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5_%D0%A3%D0%BA%D1%80%D0%B0%D0%B8%D0%BD%D1%8B" target="_blank">почтовыми индексами</a> через запятую (или диапазоном через дефис), с подстановочным знаком *, либо полным индексом.
<a href="http://services.ukrposhta.com/postindex_new/" target="_blank">Почтовые индексы Укрпочта</a>
Примечание. Если не указан регион, стоимость доставки используется для всей страны';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_CARRIER_NAME'] = 'Перевозчик ';
$MESS[$strMessPrefix . 'FIELD_DELIVERY_CARRIER_DESC'] = 'Одному элементу delivery может соответствовать один перевозчик. Если вы осуществляете доставку несколькими перевозчиками (например, Новая Почта, Автолюкс, Укрпочта), создайте отдельный элемент delivery для каждого из них.
Возможные значения: SLF – Собственными силами<br/>
CAT – CAT<br/>
DF – Delfast<br/>
DHL – DHL<br/>
IP – InPost 24/7<br/>
ND – nextDay<br/>
PP – PickPoint<br/>
TMM – TMM Express<br/>
AL – Автолюкс<br/>
VC – Ваш Час<br/>
VP – Ваша Почта<br/>
GU – Гюнсел<br/>
DA – Деливери<br/>
ЕЕ – ЕвроЭкспресс<br/>
ZD – Зручна доставка<br/>
IT – Ин-Тайм<br/>
CE – Карго Экспресс<br/>
KSD – КСД<br/>
ME – Мист Экспресс<br/>
NP – Новая почта<br/>
NE – Ночной Экспресс<br/>
PE – Пони Экспресс<br/>
PB – ПриватБанк<br/>
MET – СЦ ТОЧКА<br/>
UPG – Украинская почтовая группа<br/>
UP – Укрпочта<br/>
EM – Экспресс Мейл<br/>
YT – ЯрТранс Лоджистик';
$MESS[$strMessPrefix . 'FIELD_CONDITION_NAME'] = 'Состояние товара';
$MESS[$strMessPrefix . 'FIELD_CONDITION_DESC'] = 'Состояние товара. Заполняется в случае, если нужно указать состояние товара, отличающееся от "новый". Возможные значения: 0, 1, 2, 3. Где 0 - "новый", 1 - "refurbished" (заводское восстановление, без признаков использования (Refurbished Grade A)), 2 - "уцененный", 3 -"бывший в употреблении (в том числе Refurbished Grade B, C)".';
$MESS[$strMessPrefix . 'FIELD_CUSTOM_NAME'] = 'Отбор товаров в Управлении аукционными ставками';
$MESS[$strMessPrefix . 'FIELD_CUSTOM_DESC'] = 'Поле Custom используется для отбора товаров в Управлении аукционными ставками. Читайте подробнее в разделе Аукцион Hotline. Целочисленное значение
<custom>1</custom>
Для применения поля Custom обязательно присутствие в прайс-листе <id> товара';



$MESS[$strMessPrefix . 'FIELD_STOCK_QUANTITY_NAME'] = 'Количество товара';
$MESS[$strMessPrefix . 'FIELD_STOCK_QUANTITY_DESC'] = 'Остатки количества товара. Товар будет в наличии на сайте до тех пор, пока этот параметр больше 0.';


$MESS[$strMessPrefix . 'FIELD_CURRENCY_ID_NAME'] = 'Валюта';
$MESS[$strMessPrefix . 'FIELD_CURRENCY_ID_DESC'] = 'Валюта товара.';



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