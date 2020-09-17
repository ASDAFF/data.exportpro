<?

$strMessPrefix = 'ACRIT_EXP_AUTO_RU_PARTS_';

// General
$MESS[$strMessPrefix . 'NAME'] = 'Авто.ру Запчасти';

// Fields
$MESS[$strMessPrefix . 'FIELD_ID_NAME'] = 'Идентификатор товара в базе продавца.';
$MESS[$strMessPrefix . 'FIELD_TITLE_NAME'] = 'Название товара.';
$MESS[$strMessPrefix . 'FIELD_TITLE_DESC'] = 'Чтобы объявление попало в нужную категорию (аккумуляторы, аксессуары, колеса и т. п.), укажите ее в названии товара. Если категория не указана в названии, объявление попадет в категорию Разное. Название категории и запчасти следует указывать полностью, иначе при обработке прайс-листа могут возникнуть ошибки. Например, объявление с названием «Диск торм.» попадет в категорию «Шины и диски», но с названием «Диск тормозной» — в категорию «Тормозные диски».<br/>

Полный список категорий вы можете найти на странице <a href="https://auto.ru/parts/">Запчасти.</a><br/>

Если нужной категории нет, <a href="https://yandex.ru/support/autoru-legal/parts-form.html">напишите нам.</a><br/>

Если запчасть контрактная, укажите это в названии.';

$MESS[$strMessPrefix . 'FIELD_STORES_NAME'] = 'Идентификаторы магазинов, в которых есть товар';
$MESS[$strMessPrefix . 'FIELD_STORES_DESC'] = 'Идентификаторы указаны в личном кабинете: Настройки ? Пункты продаж и доставка ? ID.';
$MESS[$strMessPrefix . 'FIELD_PART_NUMBER_NAME'] = 'Код товара в каталоге производителя.';

$MESS[$strMessPrefix . 'FIELD_MANUFACTURER_NAME'] = 'Название производителя запчасти.';

$MESS[$strMessPrefix . 'FIELD_IS_NEW_NAME'] = 'Признак нового товара.';
$MESS[$strMessPrefix . 'FIELD_IS_NEW_DESC'] = 'Строго ограниченные значения:<br/>

«да»/«нет»<br/>
«true»/«false»<br/>
«1»/«0»<br/>
«+»/«-».';
$MESS[$strMessPrefix . 'FIELD_PRICE_NAME'] = 'Цена товара в рублях без копеек.';
$MESS[$strMessPrefix . 'FIELD_PRICE_DESC'] = 'Целое число';
$MESS[$strMessPrefix . 'FIELD_AVAILABILITY_ISAVAILABLE_NAME'] = 'Наличие товара.';
$MESS[$strMessPrefix . 'FIELD_AVAILABILITY_ISAVAILABLE_DESC'] = 'Строго ограниченные значения:<br/>
«да»/«нет»<br/>
«true»/«false»<br/>
«1»/«0»<br/>
«+»/«-».';

$MESS[$strMessPrefix . 'FIELD_AVAILABILITY_DAYSFROM_NAME'] = 'Минимальное количество дней ожидания заказа.';
$MESS[$strMessPrefix . 'FIELD_AVAILABILITY_DAYSTO_NAME'] = 'Максимальное количество дней ожидания заказа.';

$MESS[$strMessPrefix . 'FIELD_COMPATIBILITY_NAME'] = 'Применимость запчасти.';
$MESS[$strMessPrefix . 'FIELD_COMPATIBILITY_DESC'] = 'Список автомобилей, для которых подходит товар.<br/>
<compatibilily>
  <car/>
</compatibility>
Элемент передается, если товар нельзя выбрать без этой информации. Например, для двигателей, деталей кузова и т. д.

Объявления, в которых не указаны обязательные характеристики, не выгружаются на сайт.';
$MESS[$strMessPrefix . 'FIELD_COUNT_NAME'] = 'Количество единиц товара на складе.';

$MESS[$strMessPrefix . 'FIELD_IS_FOR_PRIORITY_NAME'] = 'Приоритетное размещение.';
$MESS[$strMessPrefix . 'FIELD_IS_FOR_PRIORITY_DESC'] = 'Строго ограниченные значения:<br/>

«да»/«нет»;<br/>
«true»/«false»;<br/>
«1»/«0»;<br/>
«+»/«-».<br/>
Доступно только для б/у товаров с платным размещением.<br/><br/>

<b>Примечание.</b> Деньги списываются после подключения услуги в личном кабинете.';

$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_NAME'] = 'Описание товара в свободной форме.';
$MESS[$strMessPrefix . 'FIELD_IMAGES_NAME'] = 'Фотографии товара.';
$MESS[$strMessPrefix . 'FIELD_IMAGES_DESC'] = 'При обработке прайс-листа фотографии скачиваются один раз. Чтобы обновить фотографии в объявлении, загрузите их по новым ссылкам. Новые фотографии по старым ссылкам не будут загружены.<br/>

Оптимальное соотношение сторон изображения — 4 : 3.';
$MESS[$strMessPrefix . 'FIELD_ANALOG_NAME'] = 'Набор элементов, характеризующих аналоги запчасти.(part_number)';
$MESS[$strMessPrefix . 'FIELD_OFFER_URL_NAME'] = 'URL ссылка страницы товара на вашем сайте. ';
$MESS[$strMessPrefix . 'FIELD_OFFER_URL_DESC'] = 'URL ссылка страницы товара на вашем сайте. Максимальная длина ссылки — 512 символов. Указывается только для новых товаров с оплатой за клики.<br/>
Пример:<br/>

<offer_url>http://your-site.ru/offer1234</offer_url><br/>
Допускаются кириллические ссылки.<br/>
Если вы используете сайт с URL на кириллице, он должен быть доступен по протоколу HTTP (не HTTPS). Рекомендуем преобразовать ссылку с помощью Punycode. ';
?>