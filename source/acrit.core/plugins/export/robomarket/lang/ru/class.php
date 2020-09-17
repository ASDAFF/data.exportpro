<?
$strMessPrefix = 'ACRIT_EXP_ROBO_MARKET_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Робо.Маркет';

// Default settings
$MESS[$strMessPrefix.'SETTINGS_FILE'] = 'Итоговый файл';
	$MESS[$strMessPrefix.'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/xml/robo_market.xml';
	$MESS[$strMessPrefix.'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/file.xml</code>';
$MESS[$strMessPrefix.'SETTINGS_ENCODING'] = 'Кодировка файла';
	$MESS[$strMessPrefix.'SETTINGS_ENCODING_HINT'] = 'Выберите кодировку файла. Принципиальной разницы между кодировками нет.';
$MESS[$strMessPrefix.'SETTINGS_ZIP'] = 'Упаковать в Zip';
	$MESS[$strMessPrefix.'SETTINGS_ZIP_HINT'] = 'Данный параметр позволяет запаковать сформированный файл в Zip. Благодаря упаковке в Zip-архив, размер файла, отдаваемого в Яндекс.Маркет, существенно уменьшается, что ускоряет его скачивание сервисом.';
$MESS[$strMessPrefix.'SETTINGS_DELETE_XML_IF_ZIP'] = 'Удалить XML-файл';
	$MESS[$strMessPrefix.'SETTINGS_DELETE_XML_IF_ZIP_HINT'] = 'Данная опция позволяет удалить сгенерированный XML-файл, оставив только ZIP-архив.';

// Shop settings
$MESS[$strMessPrefix.'SHOP_NAME'] = 'Короткое название магазина';
	$MESS[$strMessPrefix.'SHOP_NAME_HINT'] = 'Короткое название магазина, <b>не более 20 символов</b>. В названии нельзя использовать слова, не имеющие отношения к наименованию магазина, например «лучший», «дешевый», указывать номер телефона и т. п.<br/><br/>Название магазина должно совпадать с фактическим названием магазина, которое публикуется на сайте. При несоблюдении этого требования наименование Яндекс.Маркет может самостоятельно изменить название без уведомления магазина.<br/><br/>Обязательный элемент.';
$MESS[$strMessPrefix.'SHOP_COMPANY'] = 'Полное наименование компании';
	$MESS[$strMessPrefix.'SHOP_COMPANY_HINT'] = 'Полное наименование компании, владеющей магазином. Не публикуется, используется для внутренней идентификации.<br/><br/>Обязательный элемент.';
$MESS[$strMessPrefix.'DELIVERY'] = 'Общие условия доставки';
	$MESS[$strMessPrefix.'DELIVERY_PLACEHOLDER_COST'] = 'стоимость доставки';
	$MESS[$strMessPrefix.'DELIVERY_PLACEHOLDER_DAYS'] = 'срок доставки';
	$MESS[$strMessPrefix.'DELIVERY_PLACEHOLDER_ORDER_BEFORE'] = 'время заказа';
	$MESS[$strMessPrefix.'DELIVERY_HINT'] = 'Укажите здесь общие условия доставки.';

// Headers
$MESS[$strMessPrefix.'HEADER_GENERAL'] = 'Общие данные';
$MESS[$strMessPrefix.'HEADER_DELIVERY'] = 'Данные о доставке';
$MESS[$strMessPrefix.'HEADER_MORE'] = 'Дополнительные данные';

// Fields
$MESS[$strMessPrefix.'FIELD_ID_NAME'] = 'Идентификатор товара';
$MESS[$strMessPrefix.'FIELD_ID_DESC'] = 'Идентификатор предложения. Может состоять только из цифр и латинских букв.';

$MESS[$strMessPrefix.'FIELD_CBID_NAME'] = 'Размер ставки на карточке товара (устарело!)';
	$MESS[$strMessPrefix.'FIELD_CBID_DESC'] = 'Размер ставки на карточке товара.<br/><br/>Является атрибутом для offer.<br/><br/><a href="https://yandex.ru/support/partnermarket/bid-cbid.html" target="_blank">Подробное описание атрибута.</a><br/><br/><b style="color:red">Внимание!</b> Данное поле устарело, его использование будет вызывать ошибки в Яндекс.Маркете. Оно может быть актуально только для некоторых других площадок, работающих на схожем с Яндексом формате, которые все еще поддерживают данное поле.';
$MESS[$strMessPrefix.'FIELD_BID_NAME'] = 'Размер ставки на остальных местах размещения';
	$MESS[$strMessPrefix.'FIELD_BID_DESC'] = 'Размер ставки на остальных местах размещения (все, кроме карточки товара).<br/><br/>Является атрибутом для offer.<br/><br/><a href="https://yandex.ru/support/partnermarket/bid-cbid.html" target="_blank">Подробное описание атрибута.</a>';
$MESS[$strMessPrefix.'FIELD_AVAILABLE_NAME'] = 'Наличие товара';
	$MESS[$strMessPrefix.'FIELD_AVAILABLE_DESC'] = 'Статус товара:<br/><b>true — «в наличии» / «готов к отправке»</b><br/>Товар будет доставлен курьером или в пункт выдачи в сроки, которые вы настроили в личном кабинете. На Маркете у товара будет показан конкретный срок доставки.<br/><b>false — «на заказ»</b><br/>Точный срок доставки курьером или в пункт выдачи неизвестен. Срок будет согласован с покупателем персонально (максимальный срок — два месяца). На Маркете у товара будет показана надпись «на заказ» вместо срока.<br/><br/><b>Внимание</b>. Элемент используется в дополнение к данным, настроенным в личном кабинете. Элемент не используется, когда условия локальной курьерской доставки настроены в прайс-листе (любого формата).<br/><br/>Является атрибутом для offer. Если элемент не указан, используется значение по умолчанию — true.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/id-type-available.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_URL_NAME'] = 'URL товара';
	$MESS[$strMessPrefix.'FIELD_URL_DESC'] = 'URL страницы товара на сайте магазина. Максимальная длина ссылки — 512 символов. Допускаются кириллические ссылки.<br/><br/>Если вы используете кириллический сайт, он должен быть доступен по протоколу HTTP (не HTTPS). Рекомендуем преобразовать ссылку с помощью Punycode.';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена товара';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Актуальная цена товара.';
$MESS[$strMessPrefix.'FIELD_OLD_PRICE_NAME'] = 'Старая цена товара';
	$MESS[$strMessPrefix.'FIELD_OLD_PRICE_DESC'] = 'Старая цена товара. Должна быть выше актуальной цены.';
$MESS[$strMessPrefix.'FIELD_VAT_NAME'] = 'Ставка НДС';
	$MESS[$strMessPrefix.'FIELD_VAT_DESC'] = 'Ставка НДС для товара.';
$MESS[$strMessPrefix.'FIELD_ENABLE_AUTO_DISCOUNTS_NAME'] = 'Автоматический расчет скидок';

$MESS[$strMessPrefix.'FIELD_ENABLE_AUTO_DISCOUNTS_DESC'] = 'Данное поле позволяет включить или отключить автоматический расчет и показ скидок для отдельных товаров.';
$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_NAME'] = 'Код валюты';
	$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_DESC'] = 'Валюта, в которой указана цена товара: RUB, USD, EUR, UAH, KZT, BYN. Цена и валюта должны соответствовать друг другу. Например, вместе с USD надо указывать цену в долларах, а не в рублях.<br/><br/><b>Примечание</b>. В текстовом формате нет возможности указать свои условия конвертации валют. При показе цены покупателю она будет пересчитана в нужную валюту по текущему курсу ЦБ РФ.';
$MESS[$strMessPrefix.'FIELD_PICTURE_NAME'] = 'Картинка';
	$MESS[$strMessPrefix.'FIELD_PICTURE_DESC'] = 'URL-ссылка на картинку товара.<br/><br/><a href="https://yandex.ru/support/partnermarket/picture.html#requirements" target="_blank">Рекомендуем ознакомиться с требованиями к ссылке и изображению</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_NAME'] = 'Возможность курьерской доставки';
	$MESS[$strMessPrefix.'FIELD_DELIVERY_DESC'] = 'Возможность курьерской доставки по региону магазина.<br/><br/>Возможные значения:<br/><b>true</b> — товар может быть доставлен курьером.<br/><b>false</b> — товар не может быть доставлен курьером (только самовывоз);<br/><br/><b>Внимание</b>. Элемент delivery должен обязательно иметь значение false, если товар запрещено продавать дистанционно (ювелирные изделия, лекарственные средства).<br/><br/>Если элемент не указан, то принимается значение по умолчанию, см. <a href="https://yandex.ru/support/partnermarket/delivery.html" target="_blank">подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_COST_NAME'] = 'Стоимость курьерской доставки';
	$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_COST_DESC'] = 'Стоимость доставки.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/delivery-options.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_DAYS_NAME'] = 'Срок курьерской доставки';
	$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_DAYS_DESC'] = 'Срок доставки в рабочих днях.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/delivery-options.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_NAME'] = 'Время курьерской доставки';
	$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_DESC'] = 'Время, до которого нужно сделать заказ, чтобы получить его в этот срок.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/delivery-options.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_PICKUP_NAME'] = 'Возможность самовывоза';
	$MESS[$strMessPrefix.'FIELD_PICKUP_DESC'] = 'Возможность самовывоза из пунктов выдачи.<br/><br/>Возможные значения:<br/><b>true</b> — товар можно забрать в пунктах выдачи («самовывозом»);<br/><b>false</b> — товар нельзя забрать в пунктах выдачи.<br/><br/>Если элемент не указан, то принимается значение по умолчанию, см. <a href="https://yandex.ru/support/partnermarket/delivery.html" target="_blank">подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_PICKUP_OPTIONS_COST_NAME'] = 'Стоимость самовывоза';
	$MESS[$strMessPrefix.'FIELD_PICKUP_OPTIONS_COST_DESC'] = 'Стоимость самовывоза.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/pickup-options.html#pickup-options" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_PICKUP_OPTIONS_DAYS_NAME'] = 'Cрок поставки товара в пункт выдачи';
	$MESS[$strMessPrefix.'FIELD_PICKUP_OPTIONS_DAYS_DESC'] = 'Cрок поставки товара в пункт выдачи в рабочих днях.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/pickup-options.html#pickup-options" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_PICKUP_OPTIONS_ORDER_BEFORE_NAME'] = 'Время самовывоза';
	$MESS[$strMessPrefix.'FIELD_PICKUP_OPTIONS_ORDER_BEFORE_DESC'] = 'Время, до которого нужно сделать заказ, чтобы получить товар в пункте выдачи в указанный срок.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/pickup-options.html#pickup-options" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_STORE_NAME'] = 'Возможность купить товар без предварительного заказа';
	$MESS[$strMessPrefix.'FIELD_STORE_DESC'] = 'Возможность купить товар без предварительного заказа.<br/><br/>Возможные значения:<br/><b>true</b> — товар можно купить без предварительного заказа.<br/><b>false</b> — товар нельзя купить без предварительного заказа;<br/><br/>Если элемент не указан, то принимается значение по умолчанию, см. <a href="https://yandex.ru/support/partnermarket/delivery.html" target="_blank">подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_NAME'] = 'Описание товара';
	$MESS[$strMessPrefix.'FIELD_DESCRIPTION_DESC'] = 'Описание предложения. Длина текста не более 3000 символов (включая знаки препинания). В описании запрещено указывать:<br/><ul><li>Номера телефонов, адреса электронной почты, почтовые адреса, номера ICQ, логины мессенджеров, любые ссылки.</li><li>Слова «скидка», «распродажа», «дешевый», «подарок» (кроме подарочных категорий), «бесплатно», «акция», «специальная цена», «новинка», «new», «аналог», «заказ», «хит».</li><li>Условия продажи товара, например, данные об акциях или предоплате (их нужно передавать в элементе sales_notes).</li><li>Регион, в котором продается товар.</li><li>Информацию о разных модификациях товара (например, нельзя писать «товар в ассортименте»). Для каждой модификации нужно создать отдельное предложение.</li></ul><br/>В формате YML допустимо использовать следующие xhtml-теги &lt;h3&gt;...&lt;/h3&gt;, &lt;ul&gt;&lt;li&gt;...&lt;/li&gt;&lt;/ul&gt;, &lt;p&gt;...&lt;/p&gt;, &lt;br/&gt; при условии, что:<br/><ul><li>они заключены в блок CDATA в формате &lt;![CDATA[ Текст с использованием xhtml-разметки ]]&gt;;</li><li>соблюдены общие правила стандарта XHTML.</li></ul><br/><a href="https://yandex.ru/support/partnermarket/elements/description.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_MARKET_CATEGORY_NAME'] = 'Доп. поле для указания категории';
	$MESS[$strMessPrefix.'FIELD_MARKET_CATEGORY_DESC'] = 'Дополнительное поле для указания категории. Данного поля в документации нет, однако в некоторых случаях оно может быть использовано.';
$MESS[$strMessPrefix.'FIELD_SALES_NOTES_NAME'] = 'Условия продажи товара';
	$MESS[$strMessPrefix.'FIELD_SALES_NOTES_DESC'] = 'Условия продажи товара.<br/><br/>Обязательно укажите ограничения при заказе товара (например, минимальная сумма заказа, минимальное количество товаров или необходимость предоплаты), если они есть в вашем магазине.<br/><br/>Также можно указать данные о способах оплаты, акциях и дополнительных услугах (например, доставке товара или установке).<br/><br/>Данные о продаже товара должны быть точными и актуальными, их длина не должна превышать 50 символов.';
$MESS[$strMessPrefix.'FIELD_MIN_QUANTITY_NAME'] = 'Минимальное кол-во одинаковых товаров';
	$MESS[$strMessPrefix.'FIELD_MIN_QUANTITY_DESC'] = 'Минимальное количество одинаковых товаров в одном заказе (для случаев, когда покупка возможна только комплектом, а не поштучно). Элемент используется только в категориях «Автошины» , «Грузовые шины», «Мотошины», «Диски».<br/><br/>Если элемент не указан, используется значение по умолчанию — 1.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/moq.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_STEP_QUANTITY_NAME'] = 'Шаг добавления кол-ва';
	$MESS[$strMessPrefix.'FIELD_STEP_QUANTITY_DESC'] = 'Количество товара, которое покупатель может добавлять к минимальному в корзине Яндекс.Маркета. Элемент используется в дополнение к min-quantity и только в категориях «Автошины» , «Грузовые шины», «Мотошины», «Диски».<br/><br/>Если элемент не указан, используется значение по умолчанию — 1.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/moq.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_MANUFACTURER_WARRANTY_NAME'] = 'Официальная гарантия производителя';
	$MESS[$strMessPrefix.'FIELD_MANUFACTURER_WARRANTY_DESC'] = 'Официальная гарантия производителя.<br/><br/>Возможные значения:<br/><b>true</b> — товар имеет официальную гарантию производителя;<br/><br/><b>false</b> — товар не имеет официальной гарантии производителя.';
$MESS[$strMessPrefix.'FIELD_COUNTRY_OF_ORIGIN_NAME'] = 'Страна производства';
	$MESS[$strMessPrefix.'FIELD_COUNTRY_OF_ORIGIN_DESC'] = 'Страна производства товара.<br/><br/>Список стран, которые могут быть указаны в этом элементе: <a href="http://partner.market.yandex.ru/pages/help/Countries.pdf" target="_blank">http://partner.market.yandex.ru/pages/help/Countries.pdf</a>.';
$MESS[$strMessPrefix.'FIELD_ADULT_NAME'] = 'Товар для взрослых';
	$MESS[$strMessPrefix.'FIELD_ADULT_DESC'] = 'Товар имеет отношение к удовлетворению сексуальных потребностей, либо иным образом эксплуатирует интерес к сексу. Возможные значения — true, false.';
$MESS[$strMessPrefix.'FIELD_BARCODE_NAME'] = 'Штрихкод товара';
	$MESS[$strMessPrefix.'FIELD_BARCODE_DESC'] = 'Штрихкод товара от производителя в одном из форматов: EAN-13, EAN-8, UPC-A, UPC-E.<br/><br/>В YML элемент offer может содержать несколько элементов barcode.';
$MESS[$strMessPrefix.'FIELD_EXPIRY_NAME'] = 'Срок годности';
	$MESS[$strMessPrefix.'FIELD_EXPIRY_DESC'] = 'Срок годности / срок службы либо дата истечения срока годности / срока службы.<br/><br/>Значение элемента должно быть в формате ISO8601:<br/><br/>Для срока годности / срока службы — P1Y2M10DT2H30M. Расшифровка примера — 1 год, 2 месяца, 10 дней, 2 часа и 30 минут.<br/><br/>Для даты истечения срока годности / срока службы — YYYY-MM-DDThh:mm.';
$MESS[$strMessPrefix.'FIELD_WEIGHT_NAME'] = 'Вес товара';
	$MESS[$strMessPrefix.'FIELD_WEIGHT_DESC'] = 'Вес товара в килограммах с учетом упаковки.<br/><br/>Формат: положительное число с точностью 0.001, разделитель целой и дробной части — точка.<br/><br/>При указании более высокой точности значение автоматически округляется следующим способом:<br/><ul><li>если четвертый знак после разделителя меньше 5, то третий знак сохраняется, а все последующие обнуляются;</li><li>если четвертый знак после разделителя больше или равен 5, то третий знак увеличивается на единицу, а все последующие обнуляются.</li></ul>';
$MESS[$strMessPrefix.'FIELD_DIMENSIONS_NAME'] = 'Габариты товара';
	$MESS[$strMessPrefix.'FIELD_DIMENSIONS_DESC'] = 'Габариты товара (длина, ширина, высота) в упаковке. Размеры укажите в сантиметрах.<br/><br/>Формат: три положительных числа с точностью 0.001, разделитель целой и дробной части — точка. Числа должны быть разделены символом «/» без пробелов.<br/><br/>При указании более высокой точности значение автоматически округляется следующим способом:<br/><ul><li>если четвертый знак после разделителя меньше 5, то третий знак сохраняется, а все последующие обнуляются;</li><li>если четвертый знак после разделителя больше или равен 5, то третий знак увеличивается на единицу, а все последующие обнуляются.</li></ul>';
$MESS[$strMessPrefix.'FIELD_DOWNLOADABLE_NAME'] = 'Возможность скачать';
	$MESS[$strMessPrefix.'FIELD_DOWNLOADABLE_DESC'] = 'Продукт можно скачать. Если указано true, предложение показывается во всех регионах.';
$MESS[$strMessPrefix.'FIELD_AGE_NAME'] = 'Возрастная категория товара';
	$MESS[$strMessPrefix.'FIELD_AGE_DESC'] = 'Возрастная категория товара.<br/><br/>Годы задаются с помощью атрибута unit со значением year. Допустимые значения параметра age при unit="year": 0, 6, 12, 16, 18.<br/><br/>';
$MESS[$strMessPrefix.'FIELD_GROUP_ID_NAME'] = 'Группа товара';
	$MESS[$strMessPrefix.'FIELD_GROUP_ID_DESC'] = 'Элемент объединяет всех предложения, которые являются вариациями одной модели и должен иметь одинаковое значение. Значение должно быть целым числом, максимум 9 разрядов.<br/><br/>Является атрибутом элемента offer.<br/><br/>Используется только в категориях Одежда, обувь и аксессуары, Мебель, Косметика, парфюмерия и уход, Детские товары, Аксессуары для портативной электроники.';
$MESS[$strMessPrefix.'FIELD_REC_NAME'] = 'Рекомендуемые товары';
	$MESS[$strMessPrefix.'FIELD_REC_DESC'] = 'Товары, рекомендуемые к данному товару (должны перечисляться через запятую).';
$MESS[$strMessPrefix.'FIELD_CREDIT_TEMPLATE_ID_NAME'] = 'Кредитная программа';
	$MESS[$strMessPrefix.'FIELD_CREDIT_TEMPLATE_ID_DESC'] = 'Укажите здесь идентификатор кредитной программы.<br/><br/><a href="https://yandex.ru/support/partnermarket/efficiency/credit.html" target="_blank">Подробнее</a>';
#
$MESS[$strMessPrefix.'FIELD_SPECIAL_PRICE_START_NAME'] = 'Дата и время начала акции';
	$MESS[$strMessPrefix.'FIELD_SPECIAL_PRICE_START_DESC'] = 'Примеры:<ul>2018-09-01</li><li>2018-09-01 09:00</li><li>2018-09-01 09:00:01</li><li>2018-09-01 09:00+0500</li><li>2018-09-01 09:00:01+0500</li></ul>';
$MESS[$strMessPrefix.'FIELD_SPECIAL_PRICE_END_NAME'] = 'Дата и время завершения акции';
	$MESS[$strMessPrefix.'FIELD_SPECIAL_PRICE_END_DESC'] = 'Примеры:<ul>2018-09-01</li><li>2018-09-01 09:00</li><li>2018-09-01 09:00:01</li><li>2018-09-01 09:00+0500</li><li>2018-09-01 09:00:01+0500</li></ul>';
$MESS[$strMessPrefix.'FIELD_SPECIAL_PRICE_DESCRIPTION_NAME'] = 'Краткое описание акции';
	$MESS[$strMessPrefix.'FIELD_SPECIAL_PRICE_DESCRIPTION_DESC'] = 'Краткое описание акции. Максимум 500 символов. Можно использовать xhtml-разметку, но только в виде блока символьных данных CDATA.';
	$MESS[$strMessPrefix.'FIELD_SPECIAL_PRICE_DESCRIPTION_DEF'] = 'Специальная цена на #PRODUCT_NAME#';
$MESS[$strMessPrefix.'FIELD_SPECIAL_PRICE_URL_NAME'] = 'Ссылка на описание акции на сайте магазина';
	$MESS[$strMessPrefix.'FIELD_SPECIAL_PRICE_URL_DESC'] = 'Ссылка на описание акции на сайте магазина.';
#
$MESS[$strMessPrefix.'FIELD_N_PLUS_M_START_NAME'] = 'Дата и время начала акции';
	$MESS[$strMessPrefix.'FIELD_N_PLUS_M_START_DESC'] = 'Примеры:<ul>2018-09-01</li><li>2018-09-01 09:00</li><li>2018-09-01 09:00:01</li><li>2018-09-01 09:00+0500</li><li>2018-09-01 09:00:01+0500</li></ul>';
$MESS[$strMessPrefix.'FIELD_N_PLUS_M_END_NAME'] = 'Дата и время завершения акции';
	$MESS[$strMessPrefix.'FIELD_N_PLUS_M_END_DESC'] = 'Примеры:<ul>2018-09-01</li><li>2018-09-01 09:00</li><li>2018-09-01 09:00:01</li><li>2018-09-01 09:00+0500</li><li>2018-09-01 09:00:01+0500</li></ul>';
$MESS[$strMessPrefix.'FIELD_N_PLUS_M_DESCRIPTION_NAME'] = 'Краткое описание акции';
	$MESS[$strMessPrefix.'FIELD_N_PLUS_M_DESCRIPTION_DESC'] = 'Краткое описание акции. Максимум 500 символов. Можно использовать xhtml-разметку, но только в виде блока символьных данных CDATA.';
	$MESS[$strMessPrefix.'FIELD_N_PLUS_M_DESCRIPTION_DEF'] = 'Купи #PRODUCT_NAME# и получи еще!';
$MESS[$strMessPrefix.'FIELD_N_PLUS_M_URL_NAME'] = 'Ссылка на описание акции на сайте магазина';
	$MESS[$strMessPrefix.'FIELD_N_PLUS_M_URL_DESC'] = 'Ссылка на описание акции на сайте магазина.';
$MESS[$strMessPrefix.'FIELD_N_PLUS_M_REQUIRED_QUANTITY_NAME'] = 'Количество товаров нужно приобрести';
	$MESS[$strMessPrefix.'FIELD_N_PLUS_M_REQUIRED_QUANTITY_DESC'] = 'Количество товаров, которое нужно приобрести, чтобы получить подарок. Максимально допустимое значение — 24.';
$MESS[$strMessPrefix.'FIELD_N_PLUS_M_FREE_QUANTITY_NAME'] = 'Количество товаров в подарок';
	$MESS[$strMessPrefix.'FIELD_N_PLUS_M_FREE_QUANTITY_DESC'] = 'Количество товаров, которые покупатель получит в подарок. Максимально допустимое значение — 24.';
#
$MESS[$strMessPrefix.'FIELD_GIFTS_ID_NAME'] = 'Подарки';
	$MESS[$strMessPrefix.'FIELD_GIFTS_ID_DESC'] = 'Данное служебное поле позволяет настроить выгрузку подарков для товаров (в виде промо-акций).<br/><br/>Результатом должно быть ID товаров-подарков (или массив таких ID), поэтому здесь можно выбрать свойство типа «Привязка к элементам», в котором хранится привязка к элементам-подаркам.<br/><br/>Если привязанных товаров не будет в выгрузке, то для каждого из них будет выгружен отдельный тег &lt;gifts&gt;.';
$MESS[$strMessPrefix.'FIELD_GIFTS_DESCRIPTION_NAME'] = 'Подарки (описание)';
	$MESS[$strMessPrefix.'FIELD_GIFTS_DESCRIPTION_DESC'] = 'Данном служебное поле позволяет для каждого товара указывать свое название акции (напр., «Купи iPhone и получи чехол в подарок.»).';
$MESS[$strMessPrefix.'FIELD_GIFTS_URL_NAME'] = 'Подарки (URL)';
	$MESS[$strMessPrefix.'FIELD_GIFTS_URL_DESC'] = 'Данном служебное поле позволяет для каждого товара указать URL страницы с описанием акции.';

$MESS[$strMessPrefix.'FIELD_PARAM_NAME'] = 'Доп. параметры';
	$MESS[$strMessPrefix.'FIELD_PARAM_DESC'] = 'Доп. параметры';

# Steps
$MESS[$strMessPrefix.'STEP_EXPORT'] = 'Запись в XML-файл';
$MESS[$strMessPrefix.'STEP_ZIP'] = 'Архивация в Zip';

# Display results
$MESS[$strMessPrefix.'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix.'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix.'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix.'RESULT_DATETIME'] = 'Время окончания';
$MESS[$strMessPrefix.'RESULT_FILE_ZIP'] = 'Скачать ZIP-архив';

#
$MESS[$strMessPrefix.'NO_EXPORT_FILE_SPECIFIED'] = 'Не указан путь к итоговому файлу.';
$MESS[$strMessPrefix.'WRONG_VALUE_FOR_AGE_YEAR'] = 'Некорректное значнеия для тега «age» (unit=«year»): #TEXT#';
$MESS[$strMessPrefix.'WRONG_VALUE_FOR_AGE_MONTH'] = 'Некорректное значнеия для тега «age» (unit=«month»): #TEXT#';
$MESS[$strMessPrefix.'GIFTS_ARE_NOT_FOUND'] = 'Подарки не найдены.';
$MESS[$strMessPrefix.'CATEGORIES_EMPTY_ANSWER'] = 'Ошибка получения категорий ( #URL# ). Попробуйте еще раз.';
$MESS[$strMessPrefix.'CATEGORIES_ARE_EMPTY'] = 'Загруженный файл #URL# не содержит категорий. Попробуйте еще раз.';
$MESS[$strMessPrefix.'ERROR_SAVING_CATEGORIES_TMP'] = 'Ошибка сохранения файла с категориями: #FILE#. Проверьте наличие доступа для записи в этот файл.';
$MESS[$strMessPrefix.'GIFT_DOES_NOT_HAVE_PICTURE'] = 'Товар-подарок (ID=#ID#) не имеет изображения. Необходимо загрузить изображение в поле «Детальная картинка» или «Картинка для анонса».';
$MESS[$strMessPrefix.'ACRIT_EXP_ERROR_CREATE_DIRECTORY'] = 'Ошибка создания раздела (#DIR#) для сохранения файла выгрузки. Проверьте права доступа.';
$MESS[$strMessPrefix.'ACRIT_EXP_FILE_NO_PERMISSIONS'] = 'Ошибка сохранения файла выгрузки (#FILE#). Проверьте права доступа.';

?>