<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';

// General
$MESS[$strLang.'NAME'] = 'OZON.RU API v2 (универсальный)';

// Settings
$MESS[$strSName.'CLIENT_ID'] = 'Клиентский идентификатор [Client ID]';
	$MESS[$strSHint.'CLIENT_ID'] = 'Укажите здесь клиентский идентификатор («<code><a href="https://seller.ozon.ru/settings/api-keys" target="_blank">Client Id</a></code>») вашей учетной записи продавца.';
$MESS[$strSName.'API_KEY'] = 'Уникальный ключ [API Key]';
	$MESS[$strSHint.'API_KEY'] = 'Укажите здесь уникальный ключ («<code><a href="https://seller.ozon.ru/settings/api-keys" target="_blank">API key</a></code>»).';
	$MESS[$strLang.'API_KEY_CHECK'] = 'Проверить доступ';

//
$MESS[$strLang.'GUESS_BRAND'] = 'Бренд';

// Excel columns
#
$MESS[$strHead.'HEADER_GENERAL'] = 'Основные данные о товарах';
$MESS[$strName.'offer_id'] = 'Идентификатор товара (артикул)';
	$MESS[$strHint.'offer_id'] = 'Идентификатор товара в системе продавца — артикул.<br/><br/>
	Артикул должен быть уникальным в рамках вашего ассортимента.';
$MESS[$strName.'name'] = 'Название товара';
	$MESS[$strHint.'name'] = 'Название товара. До 500 символов.';
$MESS[$strName.'images'] = 'Изображения';
	$MESS[$strHint.'images'] = 'Изображения. Не больше 10.';
$MESS[$strName.'image_group_id'] = 'Идентификатор пакетной загрузки изображений';
	$MESS[$strHint.'image_group_id'] = 'Идентификатор для последующей пакетной загрузки изображений.';
$MESS[$strName.'pdf_list'] = 'PDF-файлы';
	$MESS[$strHint.'pdf_list'] = 'Список pdf-файлов';
$MESS[$strName.'price'] = 'Цена (с учетом скидок)';
	$MESS[$strHint.'price'] = 'Цена товара с учетом скидок, отображается на карточке товара. Если на товар нет скидок — укажите значение old_price.';
$MESS[$strName.'old_price'] = 'Цена (без учета скидок)';
	$MESS[$strHint.'old_price'] = 'Цена до скидок (будет зачеркнута на карточке товара). Указывается в рублях. Разделитель дробной части — точка, до двух знаков после точки.';
$MESS[$strName.'premium_price'] = 'Цена Premium';
	$MESS[$strHint.'premium_price'] = 'Цена для клиентов с подпиской <a href="https://docs.ozon.ru/common/ozon-premium" target="_blank">Ozon Premium</a>.';
$MESS[$strName.'vat'] = 'Ставка НДС для товара';
	$MESS[$strHint.'vat'] = 'Ставка НДС для товара.<br/>
		<ul>
			<li>0 — не облагается НДС</li>
			<li>0.1 — 10%</li>
			<li>0.2 — 20%</li>
		</ul>';
$MESS[$strName.'barcode'] = 'Штрихкод';
	$MESS[$strHint.'barcode'] = 'Введите штрихкод товара от производителя. Если у товара нет такого штрихкода, позже вы можете самостоятельно сгенерировать его в Озон.<br/><br/>
	Штрихкод нужен для продажи со склада Ozon, а также для продажи товара, подлежащего обязательной маркировке (обувь)';
$MESS[$strName.'depth'] = 'Длина упаковки';
	$MESS[$strHint.'depth'] = 'Длина — это наибольшая сторона упаковки товара. Перед измерением длины:
	<ul>
		<li>Одежда, текстиль, наборы для вышивания — сложите товар в упаковке пополам.</li>
		<li>Карты и интерьерные наклейки — скрутите в рулон. Длина рулона — самая большая величина.</li>
	</ul>
	Длина книжного комплекта — это длина всей стопки книг, которые входят в комплект.<br/><br/>
	Указывается в миллиметрах, сантиметрах, или дюймах - единицу измерения необходимо указывать в поле «dimension_unit».';
$MESS[$strName.'width'] = 'Ширина упаковки';
	$MESS[$strHint.'width'] = 'Сначала измерьте длину и высоту, оставшаяся сторона — это ширина. Перед измерением ширины:
	<ul>
		<li>Одежда, текстиль, наборы для вышивания — сложите товар в упаковке пополам.</li>
		<li>Карты и интерьерные наклейки — скрутите в рулон. Ширина рулона — это его диаметр.</li>
	</ul>	
	Ширина книжного комплекта — это ширина всей стопки книг, которые входят в комплект.<br/><br/>
	Указывается в миллиметрах, сантиметрах, или дюймах - единицу измерения необходимо указывать в поле «dimension_unit».';
$MESS[$strName.'height'] = 'Высота упаковки';
	$MESS[$strHint.'height'] = 'Высота — это наименьшая сторона упаковки товара. Перед измерением высоты:
	<ul>
		<li>Одежда, текстиль, наборы для вышивания — сложите товар в упаковке пополам.</li>
		<li>Карты и интерьерные наклейки — скрутите в рулон. Высота рулона — это его диаметр.</li>
	</ul>
	Высота книжного комплекта — это высота всей стопки книг, которые входят в комплект.<br/><br/>
	Указывается в миллиметрах, сантиметрах, или дюймах - единицу измерения необходимо указывать в поле «Единица измерения габаритов».';
$MESS[$strName.'dimension_unit'] = 'Единица измерения габаритов';
	$MESS[$strHint.'dimension_unit'] = 'Единица измерения габаритов
		<ul>
			<li>mm — миллиметры</li>
			<li>cm — сантиметры</li>
			<li>in — дюймы</li>
		</ul>';
$MESS[$strName.'weight'] = 'Вес товара в упаковке';
	$MESS[$strHint.'weight'] = 'Вес товара в упаковке. Предельное значение - 1000 килограмм или конвертированная величина в других единицах измерения.<br/><br/>
	Указывается в граммах, килограммах, или фунтах - единицу измерения необходимо указывать в поле «Единица измерения веса».';
$MESS[$strName.'weight_unit'] = 'Единица измерения веса';
	$MESS[$strHint.'weight_unit'] = 'Единица измерения веса:
		<ul>
			<li>g — граммы</li>
			<li>kg — килограммы</li>
			<li>lb — фунты</li>
		</ul>';
$MESS[$strName.'category_id'] = 'ID категории';
	$MESS[$strHint.'category_id'] = 'Укажите здесь ID категории.<br/><br/>
	Используется только при отмеченной галочке «Нестандартный режим выбора категорий»';

$MESS[$strLang.'MESSAGE_CHECK_ACCESS_SUCCESS'] = 'Проверка успешна. Доступ разрешен.';
$MESS[$strLang.'MESSAGE_CHECK_ACCESS_DENIED'] = 'Указаны некорректные данные (ClientId и/или ApiKey).';

$MESS[$strLang.'ERROR_WRONG_PRODUCT_SECTION'] = 'Для товара #ELEMENT_ID# раздел инфоблока не определен.';
$MESS[$strLang.'ERROR_WRONG_PRODUCT_CATEGORY'] = 'Для товара #ELEMENT_ID# категория не определена.';
$MESS[$strLang.'ERROR_EMPTY_REQUIRED_FIELDS'] = 'Для категории «#CATEGORY#» не заполнены обязательные поля: #FIELDS#';
$MESS[$strLang.'ERROR_WRONG_DICTIONARY_VALUE'] = 'Для товара #ELEMENT_ID# в атрибуте "#ATTRIBUTE#" указано некорректное значение &laquo;#VALUE#&raquo;. Проверьте значение по справочнику.';
$MESS[$strLang.'ERROR_CATEGORIES_EMPTY_ANSWER'] = 'Ошибка обновления категорий (#URL#). Попробуйте еще раз.';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API'] = 'Ошибка отправки товаров в OZON: #ERROR#.';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API_TASK_0'] = 'Нулевое значение task_id';
$MESS[$strLang.'ERROR_JSON_NOT_FOUND'] = 'JSON-данные не найдены.';
