<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'deal.by (формат YML)';

// Settings
$MESS[$strLang.'SETTINGS_NAME_SHOP_NAME'] = 'Название магазина';
	$MESS[$strLang.'SETTINGS_HINT_SHOP_NAME'] = 'Укажите здесь название магазина (для вывода в XML-файле в теге &lt;name&gt;).';
$MESS[$strLang.'SETTINGS_NAME_SHOP_COMPANY'] = 'Название организации';
	$MESS[$strLang.'SETTINGS_HINT_SHOP_COMPANY'] = 'Укажите здесь название организации (для вывода в XML-файле в теге &lt;company&gt;).';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Общая информация';
	$MESS[$strName.'@id'] = 'Идентификатор';
		$MESS[$strHint.'@id'] = 'Уникальный идентификатор товара.';
	$MESS[$strName.'@available'] = 'Доступность';
		$MESS[$strHint.'@available'] = 'Показатель доступности товара (true / false)';
	$MESS[$strName.'@type'] = 'Тип товара в выгрузке';
		$MESS[$strHint.'@type'] = 'Тип товара в выгрузке.<br/><br/>
Тип vendor.model используется для «склейки» названия импортируемой позиции на deal.by по принципу «typePrefix + vendor + model».<br/><br/>
При указании vendor.model должен быть обязательно установлен параметр «Название_модели».';
	$MESS[$strName.'@selling_type'] = 'Тип товара на deal.by';
		$MESS[$strHint.'@selling_type'] = 'Тип_товара определяет размещение товара в каталоге по признаку оптовой продажи.<br/><br/>
Возможные значения: r, w, u, s.
<ul>
	<li>r — «Товар продается только в розницу» для потребительских и промышленных товаров с розничными ценами.</li>
	<li>w — «Товар продается только оптом» для потребительских и промышленных товаров, которые продаются только оптом.</li>
	<li>u — «Товар продается оптом и в розницу» для товаров, которые продаются и оптом и в розницу.</li>
	<li>s — услуга.</li>
</ul>
Тип «Услуга» предназначен для размещения услуг, предоставляемых частным лицам или компаниям.';
	$MESS[$strName.'@group_id'] = 'Идентификатор';
		$MESS[$strHint.'@group_id'] = 'Параметр "group_id" — это уникальный номер, который используется для маркировки основного товара и его  разновидностей. Позиция, у которой есть номер "group_id", а также указаны характеристики через параметры "param name" считается разновидностью основного товара, который имеет такой-же номер "group_id".';
	$MESS[$strName.'name'] = 'Название товара';
		$MESS[$strHint.'name'] = 'Название товара. Обязательное поле при отсутствии параметра type="vendor.model". При использовании type="vendor.model" Название_товара не должно быть задано. В случае использования type="vendor.model", название товара будет сформировано из значений трех тегов по следующей формуле: typePrefix + vendor + model.';
	$MESS[$strName.'url'] = 'Ссылка на товар';
		$MESS[$strHint.'url'] = 'Ссылка на товар на сайте продавца.';
	$MESS[$strName.'typePrefix'] = 'Префикс названия';
		$MESS[$strHint.'typePrefix'] = 'В случае использования type="vendor.model", название товара будет сформировано из значений трех тегов по следующей формуле: typePrefix + vendor + model.';
	$MESS[$strName.'categoryId'] = 'Идентификатор категории';
		$MESS[$strHint.'categoryId'] = 'Номер группы (подгруппы), в которой будет размещена позиция на сайте компании после импорта; соответствует номеру группы или номеру подгруппы в блоке описания групп <catalog> в начале данного файла импорта.';
	$MESS[$strName.'portal_category_id'] = 'Идентификатор категории';
		$MESS[$strHint.'portal_category_id'] = 'ID_категории_на_портале — уникальный идентификатор категории портала, в которой будет опубликован данный товар после импорта.';
	$MESS[$strName.'portal_category_url'] = 'URL категории';
		$MESS[$strHint.'portal_category_url'] = 'URL категории';
	$MESS[$strName.'price'] = 'Цена товара';
		$MESS[$strHint.'price'] = 'Цена или цена с учетом скидки. Параметр обязательный только при указании тега oldprice.
Внимание! Если в файле импорта не указан тег available для товара, после импорта файла с такими же данными скидка на товар отображаться не будет. Цена товара будет взята из тега oldprice (цена товара без скидки).';
	$MESS[$strName.'oldprice'] = 'Цена товара без скидки';
		$MESS[$strHint.'oldprice'] = 'Если у товара есть скидка, в данном поле указывается цена без учета скидки. При наличии данного тега тег price является обязательным. Тег oldprice нельзя использовать совместно с тегом discount.';
	$MESS[$strName.'minimum_order_quantity'] = 'Количество_товаров';
		$MESS[$strHint.'minimum_order_quantity'] = 'Используется для указания минимального количества (поле «При заказе от»)  для основной цены товаров с типом «Товар продается только оптом».';
	$MESS[$strName.'quantity_in_stock'] = 'Количество товара на складе';
		$MESS[$strHint.'quantity_in_stock'] = 'Используется для указания остатка товаров на складе.';
	$MESS[$strName.'prices.price.value'] = 'Цена';
		$MESS[$strHint.'prices.price.value'] = 'Цена товара. Данное поле работает совместно с нижерасположенным полем с ценой: число значений в обоих полях должно быть одинаковым.';
	$MESS[$strName.'prices.price.quantity'] = 'Мин. заказ для указанной цены';
		$MESS[$strHint.'prices.price.quantity'] = 'Укажите здесь минимальный заказ товар для действия этой цены. Данное поле работает совместно с вышерасположенным полем с ценой: число значений в обоих полях должно быть одинаковым.';
	$MESS[$strName.'discount'] = 'Скидка';
		$MESS[$strHint.'discount'] = 'Размер скидки. Если у товара есть скидка, в данном поле указывается величина скидки или процент. Пример: 12.5, 30%. При наличии данного тега тег price является обязательным.';
	$MESS[$strName.'currencyId'] = 'Валюта';
		$MESS[$strHint.'currencyId'] = 'Валюта, в которой указана цена (RUB, UAH, BYR, KZT, EUR, USD).';
	$MESS[$strName.'picture'] = 'Фотографии';
		$MESS[$strHint.'picture'] = 'Ссылки на фотографию товара. Может быть указано от 1 до 10 ссылок, в зависимости от пакета услуг.
Обратите внимание: можно импортировать изображения с Google Диска. Для этого нужно дать доступ к файлу изображения адресу import@uaprom-prod-1495098375216.iam.gserviceaccount.com.';
	$MESS[$strName.'vendor'] = 'Название производителя';
		$MESS[$strHint.'vendor'] = 'Бренд, торговая марка или название предприятия-производителя, под знаком которого изготовлен товар (возможно значение «Собственное производство»). Название производителя импортируется в атрибут «Производитель» в описании товара.<br/><br/>
		<b>Внимание!</b> Указываемый вами производитель будет импортирован только если он есть в базе производителей портала. Проверить его наличие и добавить нового производителя можно при добавлении/редактировании товара в поле «Производитель» (блок «Характеристики»). При использовании тега «vendor.model», тег vendor является обязательным, так как участвует в формировании названия товара.';
	$MESS[$strName.'vendorCode'] = 'Код (артикул)';
		$MESS[$strHint.'vendorCode'] = 'Код товара (артикул) необходим для быстрого и удобного поиска нужной позиции на сайте компании и в личном кабинете при телефонном обращении клиента. Длина артикула - 25 символов (цифры, кириллица, латиница, знаки «-», «_», «.», «/» и пробел).';
	$MESS[$strName.'model'] = 'Модель товара';
		$MESS[$strHint.'model'] = 'Модель товара, участвует в формировании названия товара при использовании типа vendor.model';
	$MESS[$strName.'barcode'] = 'Код (артикул)';
		$MESS[$strHint.'barcode'] = 'Код товара (артикул) необходим для быстрого и удобного поиска нужной позиции на сайте компании и в личном кабинете при телефонном обращении клиента. Длина артикула - 25 символов (цифры, кириллица, латиница, знаки «-», «_», «.», «/» и пробел).';
	$MESS[$strName.'country'] = 'Страна-производитель';
		$MESS[$strHint.'country'] = 'Импортируется в атрибут «Страна производитель» в описании товара.';
	$MESS[$strName.'country_of_origin'] = 'Страна происхождения';
		$MESS[$strHint.'country_of_origin'] = 'Страна происхождения';
	$MESS[$strName.'description'] = 'Описание товара';
		$MESS[$strHint.'description'] = 'Описание товара. Обязательное поле. Текст описания товара может содержать HTML-теги и обязательно должен быть заключён в тег &lt;![CDATA[...]]&gt;.';
	$MESS[$strName.'keywords'] = 'Ключевые слова';
		$MESS[$strHint.'keywords'] = 'Ключевые слова (поисковые запросы, теги) товарной позиции или услуги, через запятую';
	$MESS[$strName.'delivery'] = 'Доставка';
		$MESS[$strHint.'delivery'] = 'Доставка';
	$MESS[$strName.'local_delivery_cost'] = 'Стоимость доставки';
		$MESS[$strHint.'local_delivery_cost'] = 'Стоимость курьерской доставки';
	$MESS[$strName.'manufacturer_warranty'] = 'Гарантия производителя';
		$MESS[$strHint.'manufacturer_warranty'] = 'Гарантия производителя';
	$MESS[$strName.'downloadable'] = 'Продукт можно скачать';
		$MESS[$strHint.'downloadable'] = 'Продукт можно скачать. Если указано true, предложение показывается во всех регионах.';
$MESS[$strHead.'HEADER_BOOK'] = 'Характеристики книг';
	$MESS[$strName.'author'] = 'Автор';
		$MESS[$strHint.'author'] = 'Автор произведения.';
	$MESS[$strName.'publisher'] = 'Издательство';
		$MESS[$strHint.'publisher'] = 'Издательство';
	$MESS[$strName.'series'] = 'Серия';
		$MESS[$strHint.'series'] = 'Серия';
	$MESS[$strName.'year'] = 'Год.';
		$MESS[$strHint.'year'] = 'Год издания.';
	$MESS[$strName.'ISBN'] = 'Код ISBN';
		$MESS[$strHint.'ISBN'] = 'International Standard Book Number — международный уникальный номер книжного издания. Если их несколько, укажите все через запятую.<br/><br/>
		Форматы ISBN и SBN проверяются на корректность. Валидация кодов происходит не только по длине, также проверяется контрольная цифра (check-digit) — последняя цифра кода должна согласовываться с остальными цифрами по определенной формуле. При разбиении ISBN на части при помощи дефиса (например, 978-5-94878-004-7) код проверяется на соответствие дополнительным требованиям к количеству цифр в каждой из частей.';
	$MESS[$strName.'volume'] = 'Количество томов';
		$MESS[$strHint.'volume'] = 'Общее количество томов, если издание состоит из нескольких томов.';
	$MESS[$strName.'part'] = 'Номер тома';
		$MESS[$strHint.'part'] = 'Укажите номер тома, если издание состоит из нескольких томов.';
	$MESS[$strName.'language'] = 'Язык';
		$MESS[$strHint.'language'] = 'Язык, на котором издано произведение.';
	$MESS[$strName.'binding'] = 'Формат';
		$MESS[$strHint.'binding'] = 'Формат';
	$MESS[$strName.'page_extent'] = 'Количество страниц';
		$MESS[$strHint.'page_extent'] = 'Количество страниц в книге, должно быть целым положительным числом.';
$MESS[$strHead.'HEADER_AUDIOBOOK'] = 'Характеристики аудиокниг';
	$MESS[$strName.'performed_by'] = 'Исполнитель';
		$MESS[$strHint.'performed_by'] = 'Исполнитель. Если их несколько, перечисляются через запятую.';
	$MESS[$strName.'performance_type'] = 'Тип аудиокниги';
		$MESS[$strHint.'performance_type'] = 'Тип аудиокниги (радиоспектакль, «произведение начитано» и т. п.).';
	$MESS[$strName.'storage'] = 'Носитель';
		$MESS[$strHint.'storage'] = 'Носитель аудиокниги.';
	$MESS[$strName.'format'] = 'Формат';
		$MESS[$strHint.'format'] = 'Формат аудиокниги.';
	$MESS[$strName.'recording_length'] = 'Время звучания';
		$MESS[$strHint.'recording_length'] = 'Время звучания, задается в формате mm.ss (минуты.секунды).';
$MESS[$strHead.'HEADER_MEDIA'] = 'Характеристики медиа';
	$MESS[$strName.'artist'] = 'Исполнитель';
		$MESS[$strHint.'artist'] = 'Исполнитель';
	$MESS[$strName.'title'] = 'Название';
		$MESS[$strHint.'title'] = 'Название';
	$MESS[$strName.'media'] = 'Носитель';
		$MESS[$strHint.'media'] = 'Носитель';
	$MESS[$strName.'starring'] = 'Актеры';
		$MESS[$strHint.'starring'] = 'Актеры';
	$MESS[$strName.'director'] = 'Режиссер';
		$MESS[$strHint.'director'] = 'Режиссер';
	$MESS[$strName.'originalName'] = 'Оригинальное название';
		$MESS[$strHint.'originalName'] = 'Оригинальное название';
$MESS[$strHead.'HEADER_TOURS'] = 'Характеристики туров';
	$MESS[$strName.'worldRegion'] = 'Часть света';
		$MESS[$strHint.'worldRegion'] = 'Часть света';
	$MESS[$strName.'region'] = 'Курорт или город';
		$MESS[$strHint.'region'] = 'Курорт или город';
	$MESS[$strName.'days'] = 'Количество дней тура';
		$MESS[$strHint.'days'] = 'Количество дней тура';
	$MESS[$strName.'dataTour'] = 'Даты заездов';
		$MESS[$strHint.'dataTour'] = 'Даты заездов. Предпочтительный формат: YYYY-MM-DD hh:mm:ss. <a href="https://yandex.ru/support/partnermarket/export/date-format.html" target="_blank">Рекомендуемые форматы</a>.
В формате YML элемент offer может содержать несколько элементов dataTour.';
	$MESS[$strName.'hotel_stars'] = 'Звезды отеля';
		$MESS[$strHint.'hotel_stars'] = 'Звезды отеля';
	$MESS[$strName.'room'] = 'Тип комнаты';
		$MESS[$strHint.'room'] = 'Тип комнаты (SNG, DBL и т. п.).';
	$MESS[$strName.'meal'] = 'Тип питания';
		$MESS[$strHint.'meal'] = 'Тип питания (All, HB и т. п.).';
	$MESS[$strName.'included'] = 'Что включено';
		$MESS[$strHint.'included'] = 'Что включено в стоимость тура.';
	$MESS[$strName.'transport'] = 'Транспорт';
		$MESS[$strHint.'transport'] = 'Транспорт';
$MESS[$strHead.'HEADER_TICKETS'] = 'Характеристики билетов';
	$MESS[$strName.'place'] = 'Место проведения';
		$MESS[$strHint.'place'] = 'Место проведения';
	$MESS[$strName.'hall'] = 'Зал';
		$MESS[$strHint.'hall'] = 'Зал';
	$MESS[$strName.'hall@plan'] = 'План зала';
		$MESS[$strHint.'hall@plan'] = 'План зала';
	$MESS[$strName.'hall_part'] = 'Ряд и место в зале';
		$MESS[$strHint.'hall_part'] = 'Ряд и место в зале';
	$MESS[$strName.'date'] = 'Дата и время сеанса';
		$MESS[$strHint.'date'] = 'Дата и время сеанса. Предпочтительный формат: YYYY-MM-DD hh:mm:ss. <a href="https://yandex.ru/support/partnermarket/export/date-format.html" target="_blank">Рекомендуемые форматы</a>.';
	$MESS[$strName.'is_premiere'] = 'Премьера';
		$MESS[$strHint.'is_premiere'] = 'Признак премьерности мероприятия (true / false).';
	$MESS[$strName.'is_kids'] = 'Детское мероприятие';
		$MESS[$strHint.'is_kids'] = 'Признак детского мероприятия (true / false).';

?>