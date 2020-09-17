<?
$strMessPrefix = 'ACRIT_EXP_ROZETKA_COM_UA_GENERAL_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Rozetka.com.ua';

// Default settings
$MESS[$strMessPrefix.'SETTINGS_SHOP_NAME'] = 'Заголовок файла';
	$MESS[$strMessPrefix.'SETTINGS_SHOP_NAME_HINT'] = 'Укажите здесь заголовок файла (тег name).';
$MESS[$strMessPrefix.'SETTINGS_SHOP_COMPANY'] = 'Компания';
	$MESS[$strMessPrefix.'SETTINGS_SHOP_COMPANY_HINT'] = 'Название компании (тег company).';
$MESS[$strMessPrefix.'SETTINGS_FILE'] = 'Итоговый файл';
	$MESS[$strMessPrefix.'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/xml/rozetka.xml';
	$MESS[$strMessPrefix.'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/rozetka.xml</code>';
	$MESS[$strMessPrefix.'SETTINGS_FILE_OPEN'] = 'Открыть файл';
		$MESS[$strMessPrefix.'SETTINGS_FILE_OPEN_TITLE'] = 'Файл откроется в новой вкладке';

// Header
$MESS[$strMessPrefix.'HEADER_CLOTHES'] = 'Дополнительные поля для товаров категории «Одежда и обувь»';

// Fields
$MESS[$strMessPrefix.'FIELD_ID_NAME'] = 'Идентификатор товара';
	$MESS[$strMessPrefix.'FIELD_ID_DESC'] = 'Каждое предложение должно иметь уникальный идентификатор. При внесении изменений в прайс offer id должен оставаться неизменным.<br/><br/>
На каждую модификацию товара: цвет, размер, объем, комплектацию и т.д. – должен быть создан отдельный уникальный offer id в прайсе. После загрузки товары будут сгруппированы контент-отделом Розетки.';
$MESS[$strMessPrefix.'FIELD_AVAILABLE_NAME'] = 'Наличие товара';
	$MESS[$strMessPrefix.'FIELD_AVAILABLE_DESC'] = 'Наличие товара: true – товар в наличии; false – товар не в наличии. При первичном размещении товар должен быть в наличии и иметь статус true. Если товара нет в наличии, то его можно убрать с xml, так как при первичном размещении эти товары не будут выведены на сайт.';
$MESS[$strMessPrefix.'FIELD_STOCK_QUANTITY_NAME'] = 'Количество товара';
	$MESS[$strMessPrefix.'FIELD_STOCK_QUANTITY_DESC'] = 'Остатки количества товара. Товар будет в наличии на сайте до тех пор, пока этот параметр больше 0.';
$MESS[$strMessPrefix.'FIELD_URL_NAME'] = 'Ссылка на товар';
	$MESS[$strMessPrefix.'FIELD_URL_DESC'] = 'Ссылка на товар на сайте магазина.';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Стоимость товара';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Стоимость товара.';
$MESS[$strMessPrefix.'FIELD_PRICE_OLD_NAME'] = 'Стоимость товара без скидки';
	$MESS[$strMessPrefix.'FIELD_PRICE_OLD_DESC'] = 'Стоимость товара без скидки.';
$MESS[$strMessPrefix.'FIELD_PRICE_PROMO_NAME'] = 'Цена по акции';
	$MESS[$strMessPrefix.'FIELD_PRICE_PROMO_DESC'] = 'Акционная стоимость товара';
$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_NAME'] = 'Валюта';
	$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_DESC'] = 'Валюта товара.';
$MESS[$strMessPrefix.'FIELD_PICTURE_NAME'] = 'Изображения товара';
	$MESS[$strMessPrefix.'FIELD_PICTURE_DESC'] = 'Ссылка на фото товара. Рекомендуется добавлять несколько (до 8 фото).<br/><br/>
Первая фотография в выгрузке xml будет основной в карточке товара.<br/><br/>
<a href="http://rozetka.com.ua/sellerinfo/products/#block2" target="_blank">Требования и рекомендации к фотографиям товара.</a>';
$MESS[$strMessPrefix.'FIELD_VENDOR_NAME'] = 'Производитель товара';
	$MESS[$strMessPrefix.'FIELD_VENDOR_DESC'] = 'Бренд-производитель товара. Должен указываться так, как прописано производителем и как бренд зарегистрирован документально. При наличии созданного бренда на Розетке в прайсе указывается аналогичное наименование. В этом теге и в названии товара производитель должен прописываться одинаково. Не следует указывать производителя капсом. Не надо добавлять к названию производителя: торговая марка, ТМ, ЛТД, ООО, ФОП, ТОВ и т. п.';
$MESS[$strMessPrefix.'FIELD_NAME_NAME'] = 'Название товара';
	$MESS[$strMessPrefix.'FIELD_NAME_DESC'] = 'название товара. Не должно содержать разделительных знаков (запятые, точки, тире, дефисы), кроме относящихся к наименованию модели. Не надо писать слова в названии капсом. Названия должны быть уникальными и не повторяться. Обязательно проверьте, что производитель(бренд) был указан в названии.<br/><br/>
<a href="http://rozetka.com.ua/sellerinfo/products/#block1" target="_blank">Требования и рекомендации к названиям товаров разных категорий.</a>';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_NAME'] = 'Описание товара';
	$MESS[$strMessPrefix.'FIELD_DESCRIPTION_DESC'] = 'Описание товара может быть однотипным для всей категории. В описании должна присутствовать информация только про сам товар. Описание не должно содержать ссылок, телефонов, адресов, предложений услуг, акций, цен, картинок, видеообзоров и т. д. Описание желательно отформатировать с помощью html тегов. При наличии тегов в описании в настройках поля необходимо выбрать в опции «Спецсимволы HTML»  значение «формат CData».';

# Steps
$MESS[$strMessPrefix.'STEP_EXPORT'] = 'Запись в XML-файл';

# Display results
$MESS[$strMessPrefix.'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix.'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix.'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix.'RESULT_DATETIME'] = 'Время окончания';

#
$MESS[$strMessPrefix.'NO_EXPORT_FILE_SPECIFIED'] = 'Не указан путь к итоговому файлу.';

?>