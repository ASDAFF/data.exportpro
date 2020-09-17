<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'goods.ru (Закажи и забери)';

// Settings
$MESS[$strLang.'SETTINGS_NAME_STORAGE_DIRECTORY_SWITCHER'] = 'Способ указания папки для выгрузки';
	$MESS[$strLang.'SETTINGS_HINT_STORAGE_DIRECTORY_SWITCHER'] = 'Выберите способ указания папки';
	$MESS[$strLang.'SETTINGS_NAME_STORAGE_DIRECTORY_SWITCHER_INTERNAL'] = 'Папка внутри сайта (от корня сайта)';
	$MESS[$strLang.'SETTINGS_NAME_STORAGE_DIRECTORY_SWITCHER_EXTERNAL'] = 'Папка вне сайта (от корня сервера)';
$MESS[$strLang.'SETTINGS_NAME_STORAGE_DIRECTORY_INTERNAL'] = 'Папка для файлов (на сайте)';
	$MESS[$strLang.'SETTINGS_HINT_STORAGE_DIRECTORY_INTERNAL'] = 'Укажите папку (<b>от корня сайта</b>), в которую будут складироваться все сгенерированные файлы с названием вида «123_stocks_full_2019-12-02T17-45-21+03-00.json».<br/><br/>
	В эту папку необходимо будет настроить SFTP-доступ для доступа к ним со стороны сервиса.';
$MESS[$strLang.'SETTINGS_NAME_STORAGE_DIRECTORY_EXTERNAL'] = 'Папка для файлов (вне сайта)';
	$MESS[$strLang.'SETTINGS_HINT_STORAGE_DIRECTORY_EXTERNAL'] = 'Укажите папку (<b>от корня сервера</b>), в которую будут складироваться все сгенерированные файлы с названием вида «123_stocks_full_2019-12-02T17-45-21+03-00.json».<br/><br/>
	В эту папку необходимо будет настроить SFTP-доступ для доступа к ним со стороны сервиса.';
$MESS[$strLang.'SETTINGS_NAME_STORES'] = 'Склады';
	$MESS[$strLang.'SETTINGS_HINT_STORES'] = 'Укажите здесь какие склады учитывать в выгрузке.';
$MESS[$strLang.'SETTINGS_NAME_MERCHANT_ID'] = 'ID мерчанта в Goods';
	$MESS[$strLang.'SETTINGS_HINT_MERCHANT_ID'] = 'Укажите свой ID мерчанта из личного кабинета Goods.';
$MESS[$strLang.'SETTINGS_NAME_INFO_TYPE'] = 'Тип файла';
	$MESS[$strLang.'SETTINGS_HINT_INFO_TYPE'] = 'Укажите тип выгрузки';
	$MESS[$strLang.'SETTINGS_NAME_INFO_TYPE_FULL'] = 'Полная выгрузка';
	$MESS[$strLang.'SETTINGS_NAME_INFO_TYPE_DIFF'] = 'Только изменения';

// Steps
$MESS[$strLang.'STEP_MERGE_STORE_FILES'] = 'Объединение файлов';
$MESS[$strLang.'STEP_COPY_STORE_FILES'] = 'Копирование файла';

// Fields
$MESS[$strName.'offerId'] = 'ID товара';
	$MESS[$strHint.'offerId'] = 'Идентификатор товара (или торгового предложения).';
$MESS[$strName.'price'] = 'Цена';
	$MESS[$strHint.'price'] = 'Цена товара';
	
// Notice
$MESS[$strMessPrefix.'NOTICE_SUPPORT'] = '';

// Errors
$MESS[$strLang.'ERROR_NO_STORES_TITLE'] = 'Не указаны склады.';
	$MESS[$strLang.'ERROR_NO_STORES_DESCR'] = 'Необходимо выбрать не менее одного склада в списке на первой странице настроек профиля.';
$MESS[$strLang.'ERROR_NO_CATALOG_TITLE'] = 'Недоступны функции складов';
	$MESS[$strLang.'ERROR_NO_CATALOG_DESCR'] = 'Функционал складов доступен только в редакции 1С-Битрикс: Бизнес. Необходимо перейти на эту редацию для продолжения. По вопросам перехода обращайтесь, пожалуйста, к <a href="https://www.acrit-studio.ru/about/contact_information.php" target="_blank">нашим менеджерам</a>.';
$MESS[$strLang.'ERROR_NO_STORAGE_DIRECTORY_TITLE'] = 'Не указана папка для JSON';
	$MESS[$strLang.'ERROR_NO_STORAGE_DIRECTORY_DESCR'] = 'Не указана папка для хранения сгенерированных JSON-файлов.';
$MESS[$strLang.'ERROR_CREATE_STORAGE_DIRECTORY'] = 'Ошибка создания целевого раздела.';
$MESS[$strLang.'ERROR_COPY_FILE_TO_STORAGE'] = 'Ошибка копирования файла (из #SOURCE# в #TARGET#).';


?>