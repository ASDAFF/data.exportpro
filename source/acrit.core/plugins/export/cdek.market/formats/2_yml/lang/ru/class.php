<?
$strMessPrefix = 'ACRIT_EXP_CDEK_MARKET_YML_';

// General
$MESS[$strMessPrefix.'NAME'] = 'СДЕК.Маркет New';

// Fields
$MESS[$strMessPrefix.'FIELD_ID_NAME'] = 'Идентификатор товара';
$MESS[$strMessPrefix.'FIELD_ID_DESC'] = 'Идентификатор предложения. Может состоять только из цифр и латинских букв. Максимальная длина — 20 символов. Должен быть уникальным для каждого предложения.<br/><br/>Является атрибутом для offer.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/id-type-available.html" target="_blank">Подробное описание элемента.</a>';
$MESS[$strMessPrefix.'FIELD_NAME_NAME'] = 'Название товара';
$MESS[$strMessPrefix.'FIELD_NAME_DESC'] = 'Полное название предложения, в которое входит: тип товара, производитель, название товара. Составляйте по схеме: что (тип товара) + кто (производитель) + товар (модель, название).';
$MESS[$strMessPrefix.'FIELD_VENDOR_NAME'] = 'Название производителя';
$MESS[$strMessPrefix.'FIELD_VENDOR_DESC'] = 'Название производителя данного товара.';
$MESS[$strMessPrefix.'FIELD_VENDOR_CODE_NAME'] = 'Код производителя';
$MESS[$strMessPrefix.'FIELD_VENDOR_CODE_DESC'] = 'Код производителя для данного товара.';
$MESS[$strMessPrefix.'FIELD_URL_NAME'] = 'URL товара';
$MESS[$strMessPrefix.'FIELD_URL_DESC'] = 'URL страницы товара на сайте магазина. Максимальная длина ссылки — 512 символов. Допускаются кириллические ссылки.<br/><br/>Если вы используете кириллический сайт, он должен быть доступен по протоколу HTTP (не HTTPS). Рекомендуем преобразовать ссылку с помощью Punycode.';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена товара';
$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Актуальная цена товара.<br/><br/><b>Примечание</b>. Если товар продается по весу, метражу и т. п. (не штуками), указывайте цену за вашу единицу продажи. Например, если вы продаете кабель бухтами, указывайте цену за бухту.<br/>В некоторых категориях (если прайс-лист передается в формате YML) допустимо указывать начальную цену «от» — с помощью атрибута from="true".<br/>Пример: <code>&lt;price from="true"&gt;2000&lt;/price&gt;</code><br/><br/>Это относится к следующим категориями: «Банкетки и скамьи», «Ванные комнаты», «Гостиные», «Детские», «Детские комоды», «Диваны», «Кабинеты», «Колыбели и люльки», «Комоды», «Компьютерные столы», «Кресла», «Кровати», «Кухонные гарнитуры», «Кухонные уголки и обеденные группы», «Манежи», «Парты и стулья», «Полки», «Прихожие», «Пуфики», «Спальни», «Стеллажи», «Столы и столики», «Стулья, табуретки», «Тумбы», «Шкафы».';
$MESS[$strMessPrefix.'FIELD_OLD_PRICE_NAME'] = 'Старая цена товара';
$MESS[$strMessPrefix.'FIELD_OLD_PRICE_DESC'] = 'Старая цена товара. Должна быть выше актуальной цены. Маркет автоматически рассчитывает разницу между старой и актуальной ценой и показывает пользователям скидку.<br/><br/><a href="https://yandex.ru/support/partnermarket/oldprice.html" target="_blank">Подробное описание элемента</a>.<br/><br/><b>Примечание</b>. <a href="https://yandex.ru/support/partnermarket/efficiency/data-update.html" target="_blank">Скидка обновляется</a> на Маркете каждые 40–80 минут.';
$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_NAME'] = 'Код валюты';
$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_DESC'] = 'Валюта, в которой указана цена товара: RUB, USD, EUR, UAH, KZT, BYN. Цена и валюта должны соответствовать друг другу. Например, вместе с USD надо указывать цену в долларах, а не в рублях.<br/><br/><b>Примечание</b>. В текстовом формате нет возможности указать свои условия конвертации валют. При показе цены покупателю она будет пересчитана в нужную валюту по текущему курсу ЦБ РФ.';
$MESS[$strMessPrefix.'FIELD_PICTURE_NAME'] = 'Картинка';
$MESS[$strMessPrefix.'FIELD_PICTURE_DESC'] = 'URL-ссылка на картинку товара.<br/><br/><a href="https://yandex.ru/support/partnermarket/picture.html#requirements" target="_blank">Рекомендуем ознакомиться с требованиями к ссылке и изображению</a>.';
$MESS[$strMessPrefix.'FIELD_STORE_NAME'] = 'Возможность купить товар без предварительного заказа';
$MESS[$strMessPrefix.'FIELD_STORE_DESC'] = 'Возможность купить товар без предварительного заказа.<br/><br/>Возможные значения:<br/><b>true</b> — товар можно купить без предварительного заказа.<br/><b>false</b> — товар нельзя купить без предварительного заказа;<br/><br/>Если элемент не указан, то принимается значение по умолчанию, см. <a href="https://yandex.ru/support/partnermarket/delivery.html" target="_blank">подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_PICKUP_NAME'] = 'Возможность самовывоза';
$MESS[$strMessPrefix.'FIELD_PICKUP_DESC'] = 'Возможность самовывоза из пунктов выдачи.<br/><br/>Возможные значения:<br/><b>true</b> — товар можно забрать в пунктах выдачи («самовывозом»);<br/><b>false</b> — товар нельзя забрать в пунктах выдачи.<br/><br/>Если элемент не указан, то принимается значение по умолчанию, см. <a href="https://yandex.ru/support/partnermarket/delivery.html" target="_blank">подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_NAME'] = 'Возможность курьерской доставки';
$MESS[$strMessPrefix.'FIELD_DELIVERY_DESC'] = 'Возможность курьерской доставки по региону магазина.<br/><br/>Возможные значения:<br/><b>true</b> — товар может быть доставлен курьером.<br/><b>false</b> — товар не может быть доставлен курьером (только самовывоз);<br/><br/><b>Внимание</b>. Элемент delivery должен обязательно иметь значение false, если товар запрещено продавать дистанционно (ювелирные изделия, лекарственные средства).<br/><br/>Если элемент не указан, то принимается значение по умолчанию, см. <a href="https://yandex.ru/support/partnermarket/delivery.html" target="_blank">подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_COST_NAME'] = 'Стоимость курьерской доставки';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_COST_DESC'] = 'Стоимость доставки.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/delivery-options.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_DAYS_NAME'] = 'Срок курьерской доставки';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_DAYS_DESC'] = 'Срок доставки в рабочих днях.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/delivery-options.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_NAME'] = 'Время курьерской доставки';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_DESC'] = 'Время, до которого нужно сделать заказ, чтобы получить его в этот срок.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/delivery-options.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_NAME'] = 'Описание товара';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_DESC'] = 'Описание предложения. Длина текста не более 3000 символов (включая знаки препинания). В описании запрещено указывать:<br/><ul><li>Номера телефонов, адреса электронной почты, почтовые адреса, номера ICQ, логины мессенджеров, любые ссылки.</li><li>Слова «скидка», «распродажа», «дешевый», «подарок» (кроме подарочных категорий), «бесплатно», «акция», «специальная цена», «новинка», «new», «аналог», «заказ», «хит».</li><li>Условия продажи товара, например, данные об акциях или предоплате (их нужно передавать в элементе sales_notes).</li><li>Регион, в котором продается товар.</li><li>Информацию о разных модификациях товара (например, нельзя писать «товар в ассортименте»). Для каждой модификации нужно создать отдельное предложение.</li></ul><br/>В формате YML допустимо использовать следующие xhtml-теги &lt;h3&gt;...&lt;/h3&gt;, &lt;ul&gt;&lt;li&gt;...&lt;/li&gt;&lt;/ul&gt;, &lt;p&gt;...&lt;/p&gt;, &lt;br/&gt; при условии, что:<br/><ul><li>они заключены в блок CDATA в формате &lt;![CDATA[ Текст с использованием xhtml-разметки ]]&gt;;</li><li>соблюдены общие правила стандарта XHTML.</li></ul><br/><a href="https://yandex.ru/support/partnermarket/elements/description.html" target="_blank">Подробное описание элемента</a>.';
$MESS[$strMessPrefix.'FIELD_SALES_NOTES_NAME'] = 'Условия продажи товара';
$MESS[$strMessPrefix.'FIELD_SALES_NOTES_DESC'] = 'Условия продажи товара.<br/><br/>Обязательно укажите ограничения при заказе товара (например, минимальная сумма заказа, минимальное количество товаров или необходимость предоплаты), если они есть в вашем магазине.<br/><br/>Также можно указать данные о способах оплаты, акциях и дополнительных услугах (например, доставке товара или установке).<br/><br/>Данные о продаже товара должны быть точными и актуальными, их длина не должна превышать 50 символов.';
$MESS[$strMessPrefix.'FIELD_MANUFACTURER_WARRANTY_NAME'] = 'Официальная гарантия производителя';
$MESS[$strMessPrefix.'FIELD_MANUFACTURER_WARRANTY_DESC'] = 'Официальная гарантия производителя.<br/><br/>Возможные значения:<br/><b>true</b> — товар имеет официальную гарантию производителя;<br/><br/><b>false</b> — товар не имеет официальной гарантии производителя.';
$MESS[$strMessPrefix.'FIELD_COUNTRY_OF_ORIGIN_NAME'] = 'Страна производства';
$MESS[$strMessPrefix.'FIELD_COUNTRY_OF_ORIGIN_DESC'] = 'Страна производства товара.<br/><br/>Список стран, которые могут быть указаны в этом элементе: <a href="http://partner.market.yandex.ru/pages/help/Countries.pdf" target="_blank">http://partner.market.yandex.ru/pages/help/Countries.pdf</a>.';
$MESS[$strMessPrefix.'FIELD_BARCODE_NAME'] = 'Штрихкод товара';
$MESS[$strMessPrefix.'FIELD_BARCODE_DESC'] = 'Штрихкод товара от производителя в одном из форматов: EAN-13, EAN-8, UPC-A, UPC-E.<br/><br/>В YML элемент offer может содержать несколько элементов barcode.';


?>