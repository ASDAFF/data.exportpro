<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'pulscen.ru';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Общая информация';
	$MESS[$strName.'@id'] = 'Идентификатор';
		$MESS[$strHint.'@id'] = 'Уникальный идентификатор товара	';
	$MESS[$strName.'@available'] = 'Доступность';
		$MESS[$strHint.'@available'] = 'Статус доступности товара в наличии/под заказ (true/false)';
	$MESS[$strName.'name'] = 'Наименование товара';
		$MESS[$strHint.'name'] = 'Наименование товара';
	$MESS[$strName.'url'] = 'Ссылка';
		$MESS[$strHint.'url'] = 'Адрес страницы товара';
	$MESS[$strName.'announce'] = 'Краткое описание товара';
		$MESS[$strHint.'announce'] = 'Максимальная длина - 255 символов';
	$MESS[$strName.'description'] = 'Описание товара';
		$MESS[$strHint.'description'] = 'Полное описание товара.<br/><br/>
Максимальная длина - 20 000 символов';
	$MESS[$strName.'categoryId'] = 'Категория';
		$MESS[$strHint.'categoryId'] = 'Идентификатор категории товара.<br/><br/>
Должен быть уникальным положительным целым числом, не может быть равен «0». Максимальная длина - 75 символов';
	$MESS[$strName.'picture'] = 'Ссылка на изображение';
		$MESS[$strHint.'picture'] = 'Максимальное число загружаемых изображений - 5';
	$MESS[$strName.'typePrefix'] = 'Группа товара	(тип)';
		$MESS[$strHint.'typePrefix'] = 'Группа товара	(тип). Если тег <name> отсутствует, то название товара формируется из тегов typePrefix + vendor + model.';
	$MESS[$strName.'vendor'] = 'Производитель';
		$MESS[$strHint.'vendor'] = 'Если тег <name> отсутствует, то название товара формируется из тегов typePrefix + vendor + model.';
	$MESS[$strName.'vendorCode'] = 'Артикул';
		$MESS[$strHint.'vendorCode'] = 'Артикул товара.';
	$MESS[$strName.'model'] = 'Модель товара';
		$MESS[$strHint.'model'] = 'Если тег <name> отсутствует, то название товара формируется из тегов typePrefix + vendor + model.';
	$MESS[$strName.'price'] = 'Цена товара';
		$MESS[$strHint.'price'] = 'Цена товара со скидкой';
	$MESS[$strName.'oldprice'] = 'Цена без скидки';
		$MESS[$strHint.'oldprice'] = 'Цена товара без скидки';
	$MESS[$strName.'discount_expires_at'] = 'Срок действия скидки';
		$MESS[$strHint.'discount_expires_at'] = 'Срок действия скидки (дата), например: 20.02.2015';
	$MESS[$strName.'price_min'] = 'Минимальная цена';
		$MESS[$strHint.'price_min'] = 'Минимальная цена';
	$MESS[$strName.'price_max'] = 'Максимальная цена';
		$MESS[$strHint.'price_max'] = 'Максимальная цена';
	$MESS[$strName.'currencyId'] = 'Валюта';
		$MESS[$strHint.'currencyId'] = 'Идентификатор валюты товара: RUR, USD, EUR, DOG,  BYR, KZT, UAH.';
	$MESS[$strName.'rubricId'] = 'Ссылка на рубрику';
		$MESS[$strHint.'rubricId'] = 'http(s) cсылка на рубрику.<br/><br/>
Максимальная длина - 1000 символов.';
	$MESS[$strName.'measure_unit'] = 'Единица измерения товара';
		$MESS[$strHint.'measure_unit'] = 'Возможные значения: шт., уп., ед., пара, мешок, рулон, бухта, комплект, чел., секция, т, ц, кг, г, мг, м3, л, мл, бр, гал, кв.м, га, сот, км, м, см, мм, п.м., мин, ч, сут, нед, мес, год, день, маш.смен.';
	$MESS[$strName.'min_qty'] = 'Минимальный размер заказа';
		$MESS[$strHint.'min_qty'] = 'Минимальный размер заказа';
	$MESS[$strName.'qty_measure_unit'] = 'Наименование единицы минимальной партии';
		$MESS[$strHint.'qty_measure_unit'] = 'Наименование единицы минимальной партии';
	$MESS[$strName.'sales_notes'] = 'Условия оплаты';
		$MESS[$strHint.'sales_notes'] = 'Максимальная длина - 75 символов';
	$MESS[$strName.'delivery'] = 'Условия доставки';
		$MESS[$strHint.'delivery'] = 'Возможность доставки (true/false)';
	$MESS[$strName.'local_delivery_cost'] = 'Стоимость доставки';
		$MESS[$strHint.'local_delivery_cost'] = 'Стоимость доставки';
	$MESS[$strName.'delivery_field'] = 'Условия доставки';
		$MESS[$strHint.'delivery_field'] = 'Максимальная длина - 75 символов';
	$MESS[$strName.'wholesale_price'] = 'Оптовая цена';
		$MESS[$strHint.'wholesale_price'] = 'Оптовая цена';
	$MESS[$strName.'wholesale_price_min'] = 'Оптовая цена от';
		$MESS[$strHint.'wholesale_price_min'] = 'Оптовая цена от';
	$MESS[$strName.'wholesale_currency'] = 'Валюта для оптовой цены';
		$MESS[$strHint.'wholesale_currency'] = 'Валюта для оптовой цены';
	$MESS[$strName.'wholesale_measure_unit'] = 'Единица измерения оптовой цены';
		$MESS[$strHint.'wholesale_measure_unit'] = 'Единица измерения оптовой цены';
	$MESS[$strName.'wholesale_min_qty'] = 'При заказе от, для оптовой цены';
		$MESS[$strHint.'wholesale_min_qty'] = 'При заказе от для оптовой цены. Обязательное поле, если вы хотите указывать оптовую ценуна Пульс цен';


?>