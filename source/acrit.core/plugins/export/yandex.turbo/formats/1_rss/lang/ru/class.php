<?
$strMessPrefix = 'ACRIT_EXP_YANDEX_TURBO_GENERAL_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Яндекс.Турбо (RSS)';
$MESS[$strMessPrefix.'TAB_SETTINGS_NAME'] = 'Доп. настройки';
	$MESS[$strMessPrefix.'TAB_SETTINGS_DESC'] = 'Дополнительные настройки Яндекс.Турбо';
$MESS[$strMessPrefix.'TAB_FILES_NAME'] = 'Статические файлы';
	$MESS[$strMessPrefix.'TAB_FILES_DESC'] = 'Настройка выгрузки статических файлов в Яндекс.Турбо';

// Default settings
$MESS[$strMessPrefix.'SETTINGS_SHOP_NAME'] = 'Заголовок файла';
	$MESS[$strMessPrefix.'SETTINGS_SHOP_NAME_HINT'] = 'Укажите здесь заголовок файла (тег name).';
$MESS[$strMessPrefix.'SETTINGS_SHOP_COMPANY'] = 'Компания';
	$MESS[$strMessPrefix.'SETTINGS_SHOP_COMPANY_HINT'] = 'Название компании (тег company).';
$MESS[$strMessPrefix.'SETTINGS_FILE'] = 'Итоговый файл';
	$MESS[$strMessPrefix.'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/xml/yandex_turbo.xml';
	$MESS[$strMessPrefix.'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/yandex_turbo.xml</code><br/><br/>Имейте ввиду, что для выгрузки действуют ограничения по количеству записей в одном файле, поэтому при выгрузке может получиться несколько файлов.';
	$MESS[$strMessPrefix.'SETTINGS_FILE_COUNT'] = 'Всего файлов: #COUNT#.';
$MESS[$strMessPrefix.'SETTINGS_ENCODING'] = 'Кодировка файла';
	$MESS[$strMessPrefix.'SETTINGS_ENCODING_HINT'] = 'Выберите кодировку файла. Принципиальной разницы между кодировками нет.';

// Header
$MESS[$strMessPrefix.'HEADER_CLOTHES'] = 'Дополнительные поля для товаров категории «Одежда и обувь»';

// Fields
$MESS[$strMessPrefix.'FIELD_LINK_NAME'] = 'URL страницы';
	$MESS[$strMessPrefix.'FIELD_LINK_DESC'] = 'URL страницы-источника.<br/><br/>
Требования:
<ul>
	<li>ссылка должна содержать схему HTTP или HTTPS;</li>
	<li>домен, указанный в ссылке, должен соответствовать домену сайта-источника;</li>
	<li>максимальная длина URL — 243 ASCII-символа;</li>
	<li>по одному URL должна быть доступна одна статья.</li>
</ul>
<p>При переходе по ссылке заголовок и начало текста должны быть видны на первом экране при разрешении 1024 &times; 768.</p>
';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Заголовок товара';
	$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Заголовок товара.';
$MESS[$strMessPrefix.'FIELD_SUBTITLE_NAME'] = 'Подзаголовок товара';
	$MESS[$strMessPrefix.'FIELD_SUBTITLE_DESC'] = 'Подзаголовок товара.';
$MESS[$strMessPrefix.'FIELD_IMAGE_NAME'] = 'Основная картинка';
	$MESS[$strMessPrefix.'FIELD_IMAGE_DESC'] = 'Основная картинка страницы.';
$MESS[$strMessPrefix.'FIELD_CONTENT_NAME'] = 'Содержимое';
	$MESS[$strMessPrefix.'FIELD_CONTENT_DESC'] = 'Содержимое элемента в формате HTML. Это содержимое будет являться частью тега turbo:content.';
$MESS[$strMessPrefix.'FIELD_TURBO_SOURCE_NAME'] = 'URL страницы для Метрики';
	$MESS[$strMessPrefix.'FIELD_TURBO_SOURCE_DESC'] = 'URL страницы-источника, который можно передать в Яндекс.Метрику.';
$MESS[$strMessPrefix.'FIELD_TURBO_TOPIC_NAME'] = 'Заголовок товара для Метрики';
	$MESS[$strMessPrefix.'FIELD_TURBO_TOPIC_DESC'] = 'Заголовок страницы, который можно передать в Яндекс.Метрику.';
$MESS[$strMessPrefix.'FIELD_PUB_DATE_NAME'] = 'Дата и время публикации';
	$MESS[$strMessPrefix.'FIELD_PUB_DATE_DESC'] = 'Время публикации контента на сайте источника.<br/><br/>
Передается в формате RFC-822 (напр., «Tue, 21 Apr 2015 14:15:00 +0300»)';
$MESS[$strMessPrefix.'FIELD_AUTHOR_NAME'] = 'Автор статьи';
	$MESS[$strMessPrefix.'FIELD_AUTHOR_DESC'] = 'Автор статьи, размещенной на странице.';
$MESS[$strMessPrefix.'FIELD_IMAGES_NAME'] = 'Изображения';
	$MESS[$strMessPrefix.'FIELD_IMAGES_DESC'] = 'Изображения для турбо-страницы.<br/><br/>В параметрах <b>каждого значения</b> должна быть установлена опция «Использовать значение без обработки».';
$MESS[$strMessPrefix.'FIELD_RELATED_NAME'] = 'Связанные элементы';
	$MESS[$strMessPrefix.'FIELD_RELATED_DESC'] = 'Вы можете разместить ссылки на другие ресурсы или настроить отображение бесконечной ленты статей, реализованной, например, с помощью AJAX.<br/><br/>
Такие ссылки будут располагаться внизу Турбо-страницы.';

# Share
$MESS[$strMessPrefix.'SHARE_WHATSAPP'] = 'WhatsApp';
$MESS[$strMessPrefix.'SHARE_TELEGRAM'] = 'Telegram';
$MESS[$strMessPrefix.'SHARE_VKONTAKTE'] = 'Вконтакте';
$MESS[$strMessPrefix.'SHARE_FACEBOOK'] = 'Facebook';
$MESS[$strMessPrefix.'SHARE_VIBER'] = 'Viber';


# Steps
$MESS[$strMessPrefix.'STEP_EXPORT'] = 'Запись в XML-файл';

# Display results
$MESS[$strMessPrefix.'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix.'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix.'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix.'RESULT_DATETIME'] = 'Время окончания';
$MESS[$strMessPrefix.'RESULT_OPEN_FILE'] = 'Перейти к файлу';
$MESS[$strMessPrefix.'RESULT_NO_FILES'] = 'Файлы не сгенерированы: ни один элемент успешно не выгрузился. Подробности - в логе.';

#
$MESS[$strMessPrefix.'NO_EXPORT_FILE_SPECIFIED'] = 'Не указан путь к итоговому файлу.';
$MESS[$strMessPrefix.'NO_CONTENT'] = 'Контент не указан (страница: #DIR#).';
$MESS[$strMessPrefix.'WRONG_HTTP_CODE'] = 'Страница #DIR# возвращает неверный код ответа: #CODE#.';

?>