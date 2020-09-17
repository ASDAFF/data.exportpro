<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'nadavi.net';

// Settings
$MESS[$strLang.'SETTINGS_NAME_SHOP_NAME'] = 'Название магазина';
	$MESS[$strLang.'SETTINGS_HINT_SHOP_NAME'] = 'Укажите здесь название магазина (для вывода в XML-файле в теге &lt;name&gt;).';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Общая информация';
	$MESS[$strName.'@id'] = 'Идентификатор';
		$MESS[$strHint.'@id'] = 'Уникальный номер товара на сайте магазина';
	$MESS[$strName.'name'] = 'Модель товара';
		$MESS[$strHint.'name'] = 'Модель товара';
	$MESS[$strName.'url'] = 'Ссылка на товар';
		$MESS[$strHint.'url'] = 'Ссылка на товар на сайте продавца.';
	$MESS[$strName.'price'] = 'Цена товара';
		$MESS[$strHint.'price'] = 'Цена товара';
	$MESS[$strName.'categoryId'] = 'Категория';
		$MESS[$strHint.'categoryId'] = 'Номер категории, к которой относится товар';
	$MESS[$strName.'currencyId'] = 'Валюта';
		$MESS[$strHint.'currencyId'] = 'Валюта, в которой указана цена (RUB, UAH, BYR, KZT, EUR, USD).';
	$MESS[$strName.'typePrefix'] = 'Название товара';
		$MESS[$strHint.'typePrefix'] = 'Название товара (дополнительный параметр, например: Утюг, Чайник, Ковеварка)';
	$MESS[$strName.'vendor'] = 'Производитель';
		$MESS[$strHint.'vendor'] = 'Производитель (бренд) товара';
	$MESS[$strName.'image'] = 'Фотографии';
		$MESS[$strHint.'image'] = 'Прямая ссылка на картинку товара';
	$MESS[$strName.'description'] = 'Описание товара';
		$MESS[$strHint.'description'] = 'Краткое описание характеристик товара';
		
?>