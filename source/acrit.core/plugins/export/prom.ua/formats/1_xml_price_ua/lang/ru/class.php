<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Prom.ua (формат XML price.ua)';

// Settings
$MESS[$strLang.'SETTINGS_NAME_SHOP_NAME'] = 'Название магазина';
	$MESS[$strLang.'SETTINGS_HINT_SHOP_NAME'] = 'Укажите здесь название магазина (для вывода в XML-файле в теге &lt;name&gt;).';

// Fields
$MESS[$strName.'@id'] = 'Идентификатор объявления';
	$MESS[$strHint.'@id'] = 'Идентификатор объявления. Должен быть уникальным для каждого объявления.';
$MESS[$strName.'@available'] = 'Наличие';
	$MESS[$strHint.'@available'] = 'Указание наличия для товара. Значение «склад» или «true» соответствует статусу товара «В наличии», значение «false» — статусу «Под заказ». Если в данном поле пусто — товар будет импортирован в статусе «Нет в наличии».<br/><br/>
	Для выгрузки пустых значений необходимо в настройках поля указывать галочку «Выгружать даже пустые значения».';
$MESS[$strName.'@presence_sure'] = 'Гарантированное наличие';
	$MESS[$strHint.'@presence_sure'] = 'Параметр presence_sure="true"указывает статус «Гарантированное наличие».';
$MESS[$strName.'name'] = 'Заголовок объявления';
	$MESS[$strHint.'name'] = 'Заголовок объявления.';
$MESS[$strName.'categoryId'] = 'Категория объявления';
	$MESS[$strHint.'categoryId'] = 'Категория объявления.';
$MESS[$strName.'price'] = 'Стоимость товара (в гривнах)';
	$MESS[$strHint.'price'] = 'Стоимость товара. Всегда указывается в гривнах.';
$MESS[$strName.'bnprice'] = 'Безналичная цена (в гривнах)';
	$MESS[$strHint.'bnprice'] = 'Безналичная цена (в гривнах). Всегда указывается в гривнах.';
$MESS[$strName.'oldprice'] = 'Старая стоимость товара (в гривнах)';
	$MESS[$strHint.'oldprice'] = 'Старая стоимость товара (если хотите чтобы товар попа в раздел «Акции»). Всегда указывается в гривнах.';
$MESS[$strName.'url'] = 'Ссылка на товар';
	$MESS[$strHint.'url'] = 'Ссылка на подробное описание товара.';
$MESS[$strName.'image'] = 'Фото товара';
	$MESS[$strHint.'image'] = 'Фотография (изображение) товара.';
$MESS[$strName.'vendor'] = 'Производитель';
	$MESS[$strHint.'vendor'] = 'Производитель';
$MESS[$strName.'description'] = 'Подробное описание';
	$MESS[$strHint.'description'] = 'Подробное описание товара.';
$MESS[$strName.'guarantee'] = 'Гарантия';
	$MESS[$strHint.'guarantee'] = 'Срок гарантии.';
$MESS[$strName.'guarantee@type'] = 'Тип гарантии';
	$MESS[$strHint.'guarantee@type'] = 'Тип гарантии. Допустимы два различных значения: manufacturer (гарантия поставщика) и shop (гарантия магазина).';
$MESS[$strName.'guarantee@unit'] = 'Тип срока гарантии';
	$MESS[$strHint.'guarantee@unit'] = 'Тип срока гарантии. Должно быть указано "days", если срок гарантии исчисляется в днях, в противном случае значение должно быть пустым.';
?>