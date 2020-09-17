<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'zakupki.mos.ru (YML)';
$MESS[$strLang.'SETTINGS_NAME_SHOP_NAME'] = 'Название магазина';
	$MESS[$strLang.'SETTINGS_HINT_SHOP_NAME'] = 'Название магазина';
$MESS[$strLang.'SETTINGS_NAME_COMPANY_NAME'] = 'Название организации';
	$MESS[$strLang.'SETTINGS_HINT_COMPANY_NAME'] = 'Название организации';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Общие данные';
	$MESS[$strName.'@id'] = 'Идентификатор предложения';
		$MESS[$strHint.'@id'] = 'Идентификатор предложения. Может состоять только из цифр и латинских букв. Максимальная длина — 20 символов. Должен быть уникальным для каждого предложения.';
	$MESS[$strName.'@available'] = 'Статус товара';
		$MESS[$strHint.'@available'] = 'Статус товара:<br/>true — «в наличии» / «готов к отправке» false — «на заказ» Элемент не используется, когда условия локальной курьерской доставки настроены в прайс-листе (любого формата).<br/>Если элемент не указан, используется значение по умолчанию — true.';
	$MESS[$strName.'@group_id'] = 'ID группы товаров';
		$MESS[$strHint.'@group_id'] = 'Элемент используется только в формате YML и только в категориях: Одежда, обувь и аксессуары, Мебель, Косметика, парфюмерия и уход, Детские товары, Аксессуары для портативной электроники. Элемент объединяет все предложения, которые являются вариациями одной модели и должен иметь одинаковое значение. Значение должно быть целым числом, максимум 9 знаков.';
	$MESS[$strName.'currencyId'] = 'Валюта';
		$MESS[$strHint.'currencyId'] = 'Валюта, в которой указана цена товара. <a href="https://old.zakupki.mos.ru/api/cssp/data/exchange/v1.1/currency" target="_blank">Справочник</a>';
	$MESS[$strName.'name'] = 'Наименование товара';
		$MESS[$strHint.'name'] = 'Полное наименование товара в прайс-листе поставщика.';
	$MESS[$strName.'picture'] = 'Картинка';
		$MESS[$strHint.'picture'] = 'URL-ссылка на картинку товара.';
	$MESS[$strName.'price'] = 'Актуальная цена товара';
		$MESS[$strHint.'price'] = 'Если товар продается по весу, метражу и т. п. (не штуками), указывайте цену за вашу единицу продажи. Например, если вы продаете кабель бухтами, указывайте цену за бухту. В некоторых категориях (если прайс-лист передается в формате YML) допустимо указывать начальную цену «от» — с помощью атрибута from="true". Посмотреть список категорий Пример: <price from="true">2000</price>.';
	$MESS[$strName.'vendor'] = 'Название производителя.';
		$MESS[$strHint.'vendor'] = 'Название производителя.';
	$MESS[$strName.'model'] = 'Модель товара.';
		$MESS[$strHint.'model'] = 'Модель товара.';
	$MESS[$strName.'vendorCode'] = 'Код производителя для данного товара.';
		$MESS[$strHint.'vendorCode'] = 'Код производителя для данного товара.';
	$MESS[$strName.'vat'] = 'НДС';
		$MESS[$strHint.'vat'] = 'Ставка НДС для товара. Используется, только если вы включили загрузку ставок из прайс-листа.';
	$MESS[$strName.'delivery'] = 'Возможность курьерской доставки';
		$MESS[$strHint.'delivery'] = 'Возможность курьерской доставки по региону магазина. Возможные значения: true — товар может быть доставлен курьером. false — товар не может быть доставлен курьером (только самовывоз); Элемент delivery должен обязательно иметь значение false, если товар запрещено продавать дистанционно (ювелирные изделия, лекарственные средства).';
	$MESS[$strName.'manufacturer_warranty'] = 'Гарантия';
		$MESS[$strHint.'manufacturer_warranty'] = 'Официальная гарантия производителя. Возможные значения: true — товар имеет официальную гарантию производителя; false — товар не имеет официальной гарантии производителя.';
	$MESS[$strName.'barcode'] = 'Штрихкод товара';
		$MESS[$strHint.'barcode'] = 'Штрихкод товара от производителя в одном из форматов: EAN-13, EAN-8, UPC-A, UPC-E. В YML элемент offer может содержать несколько элементов barcode.';
	$MESS[$strName.'expiry'] = 'Срок годности';
		$MESS[$strHint.'expiry'] = 'Срок годности / срок службы либо дата истечения срока годности / срока службы. Значение элемента должно быть в формате ISO8601: Для срока годности / срока службы — P1Y2M10DT2H30M. Расшифровка примера — 1 год, 2 месяца, 10 дней, 2 часа и 30 минут. Для даты истечения срока годности / срока службы — YYYY-MM-DDThh:mm.';
	$MESS[$strName.'weight'] = 'Вес товара в килограммах';
		$MESS[$strHint.'weight'] = 'Вес товара в килограммах с учетом упаковки. Для некоторых категорий установлены ограничения по минимальному или максимальному значению веса. В любой категории вес можно указывать с точностью до тысячных (например, 1.001 кг; разделитель целой и дробной части — точка). Если минимальное значение указано 0, ограничений по минимальному весу нет, и можно указывать начиная с одного грамма (0.001 кг).';
	$MESS[$strName.'dimensions'] = 'Габариты товара';
		$MESS[$strHint.'dimensions'] = 'Габариты товара (длина, ширина, высота) в упаковке. Размеры укажите в сантиметрах. Формат: три положительных числа с точностью 0.001, разделитель целой и дробной части — точка. Числа должны быть разделены символом «/» без пробелов.';
	$MESS[$strName.'downloadable'] = 'Продукт можно скачать';
		$MESS[$strHint.'downloadable'] = 'Продукт можно скачать. Если указано true, предложение показывается во всех регионах.';
	$MESS[$strName.'age'] = 'Возрастная категория';
		$MESS[$strHint.'age'] = 'Возрастная категория товара. В формате YML: Годы задаются с помощью атрибута unitсо значением year. Допустимые значения параметра age при unit="year": 0, 6, 12, 16, 18. Месяцы задаются с помощью атрибута unitсо значением month. Допустимые значения параметра age при unit="month": 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12.';
	$MESS[$strName.'age@unit'] = 'Возрастная категория (unit)';
		$MESS[$strHint.'age@unit'] = 'Возрастная категория товара. В формате YML: Годы задаются с помощью атрибута unitсо значением year. Допустимые значения параметра age при unit="year": 0, 6, 12, 16, 18. Месяцы задаются с помощью атрибута unitсо значением month. Допустимые значения параметра age при unit="month": 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12.';
	$MESS[$strName.'delivery-options.option@cost'] = 'Стоимость доставки';
		$MESS[$strHint.'delivery-options.option@cost'] = 'Стоимость курьерской доставки';
	$MESS[$strName.'delivery-options.option@days'] = 'Срок доставки';
		$MESS[$strHint.'delivery-options.option@days'] = 'Срок курьерской доставки (в рабочих днях)';
	$MESS[$strName.'delivery-options.option@order-before'] = 'Время курьерской доставки';
		$MESS[$strHint.'delivery-options.option@order-before'] = 'Время, до которого нужно сделать заказ, чтобы получить его в этот срок. ';
	

$MESS[$strHead.'HEADER_PP'] = 'Специальные поля Портала Поставщиков';
	$MESS[$strName.'ste'] = 'СТЕ';
		$MESS[$strHint.'ste'] = 'Идентификатор СТЕ на Портале поставщиков.';
	$MESS[$strName.'isVisibleToStateCustomers'] = 'Доступность для государственных заказчиков.';
		$MESS[$strHint.'isVisibleToStateCustomers'] = 'Статус товара. Доступность для государственных заказчиков.';
	$MESS[$strName.'isAvailableToIndividuals'] = 'Доступность для негосударственных заказчиков.';
		$MESS[$strHint.'isAvailableToIndividuals'] = 'Статус товара. Доступность для негосударственных заказчиков.';
	$MESS[$strName.'ppCategory'] = 'Категория товара (из справочника портала)';
		$MESS[$strHint.'ppCategory'] = 'Категория товара из <a href="https://old.zakupki.mos.ru/api/cssp/data/exchange/v1.1/category" target="_blank">справочника на Портале поставщиков</a>.';
	$MESS[$strName.'okei'] = 'Название типа единицы измерения';
		$MESS[$strHint.'okei'] = 'Название типа единицы измерения согласно «<a href="https://old.zakupki.mos.ru/api/cssp/data/exchange/v1.1/okei" target="_blank">Общероссийскому классификатору единиц измерения</a>».';
	$MESS[$strName.'okei@id'] = 'Идентификатор типа единицы измерения';
		$MESS[$strHint.'okei@id'] = 'Идентификатор типа единицы измерения согласно «<a href="https://old.zakupki.mos.ru/api/cssp/data/exchange/v1.1/okei" target="_blank">Общероссийскому классификатору единиц измерения</a>».';
	$MESS[$strName.'min-quantity'] = 'Мин. заказ';
		$MESS[$strHint.'min-quantity'] = 'Минимальное количество в одной поставке (минимум товаров в заказе).';
	$MESS[$strName.'max-quantity'] = 'Макс. заказ';
		$MESS[$strHint.'max-quantity'] = 'Максимум поставки (если бесконечность, оставить пустым).';
	$MESS[$strName.'beginDate'] = 'Дата начала предложения.';
		$MESS[$strHint.'beginDate'] = 'Дата начала предложения.';
	$MESS[$strName.'endDate'] = 'Дата окончания предложения.';
		$MESS[$strHint.'endDate'] = 'Дата окончания предложения.';
	$MESS[$strName.'package@id'] = 'Тип упаковки';
		$MESS[$strHint.'package@id'] = 'Тип упаковки согласно <a href="https://old.zakupki.mos.ru/api/cssp/data/exchange/v1.1/package" target="_blank">справочнику на Портале поставщиков</a>.';
	$MESS[$strName.'regions.region'] = 'Регион';
		$MESS[$strHint.'regions.region'] = 'Регион';
	$MESS[$strName.'regions.region@id'] = 'Регион (ID)';
		$MESS[$strHint.'regions.region@id'] = 'Регион (идентификатор, из <a href="https://old.zakupki.mos.ru/api/cssp/data/exchange/v1.1/region/top" target="_blank">справочника на Портале Поставщиков</a>).';
?>