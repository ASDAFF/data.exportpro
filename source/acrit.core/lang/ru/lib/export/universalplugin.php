<?
$strLang = 'ACRIT_EXP_UNIVERSAL_PLUGIN_';

# Settings
$MESS[$strLang.'SETTINGS_NAME_FILENAME'] = 'Файл выгрузки';
	$MESS[$strLang.'SETTINGS_HINT_FILENAME'] = 'Выберите конечный файл для выгрузки.';
$MESS[$strLang.'SETTINGS_NAME_ENCODING'] = 'Кодировка';
	$MESS[$strLang.'SETTINGS_HINT_ENCODING'] = 'Выберите кодировку файла выгрузки.';
$MESS[$strLang.'SETTINGS_NAME_FORMAT'] = 'Формат файла';
	$MESS[$strLang.'SETTINGS_HINT_FORMAT'] = 'Выберите требуемый формат файла.';
$MESS[$strLang.'SETTINGS_NAME_ARCHIVE'] = 'Упаковать в архив';
	$MESS[$strLang.'SETTINGS_HINT_ARCHIVE'] = 'Отметьте опцию, если необходимо упаковать файл выгрузки в архив';
	$MESS[$strLang.'SETTINGS_NAME_ARCHIVE_JUST'] = 'Удалить файл выгрузки, оставив только архив';

# Formats
$MESS[$strLang.'FORMAT_XML'] = 'XML';
$MESS[$strLang.'FORMAT_CSV'] = 'CSV';
$MESS[$strLang.'FORMAT_XLS'] = 'XLS';
$MESS[$strLang.'FORMAT_XLSX'] = 'XLSX';
$MESS[$strLang.'FORMAT_JSON'] = 'JSON';
$MESS[$strLang.'FORMAT_API'] = 'API';

# Archive
$MESS[$strLang.'ARCHIVE_NO'] = '--- не упаковывать ---';
$MESS[$strLang.'ARCHIVE_ZIP'] = 'Упаковать в архив zip';
$MESS[$strLang.'ARCHIVE_TAR_GZ'] = 'Упаковать в архив tag.gz';

# Steps
$MESS[$strLang.'STEP_CHECK'] = 'Проверка настроек';
$MESS[$strLang.'STEP_EXPORT'] = 'Экспорт данных';
$MESS[$strLang.'STEP_ARCHIVE'] = 'Архивация файла';

# Results info
$MESS[$strLang.'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strLang.'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strLang.'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strLang.'RESULT_DATETIME'] = 'Время окончания';

# Default fields
$MESS[$strLang.'FIELD_HEADER_UTM'] = 'UTM-метки';
$MESS[$strLang.'FIELD_NAME_UTM_SOURCE'] = 'UTM-метка: источник';
	$MESS[$strLang.'FIELD_DESC_UTM_SOURCE'] = 'UTM-метка: источник трафика (Вконтакте, Яндекс, рассылка)';
$MESS[$strLang.'FIELD_NAME_UTM_MEDIUM'] = 'UTM-метка: тип рекламы';
	$MESS[$strLang.'FIELD_DESC_UTM_MEDIUM'] = 'UTM-метка: тип рекламы (контекстная реклама, баннер, пост, письмо)';
$MESS[$strLang.'FIELD_NAME_UTM_CAMPAIGN'] = 'UTM-метка: название кампании';
	$MESS[$strLang.'FIELD_DESC_UTM_CAMPAIGN'] = 'UTM-метка: название рекламной кампании или объявления';
$MESS[$strLang.'FIELD_NAME_UTM_CONTENT'] = 'UTM-метка: доп. информация';
	$MESS[$strLang.'FIELD_DESC_UTM_CONTENT'] = 'UTM-метка: дополнительная информация';
$MESS[$strLang.'FIELD_NAME_UTM_TERM'] = 'UTM-метка: ключевое слово';
	$MESS[$strLang.'FIELD_DESC_UTM_TERM'] = 'UTM-метка: ключевое слово';

# Errors
$MESS[$strLang.'ERROR_NO_FILE_SPECIFIED'] = 'Не указан файл для выгрузки.';
$MESS[$strLang.'ERROR_EXPORT_FILE_IS_NOT_WRITEABLE'] = 'Нет доступа для записи в экспортируемый файл.';
$MESS[$strLang.'ERROR_TMP_FILE_IS_NOT_WRITEABLE'] = 'Нет доступа для записи во временный файл.';
$MESS[$strLang.'ERROR_FILE_IS_NOT_WRITEABLE'] = 'Файл <code><b>#FILENAME#</b></code> недоступен для записи, продолжение невозможно. Проверьте параметры доступа к файлу и директории файла - модуль должен иметь возможность удалить и создать данный файл.';
$MESS[$strLang.'ERROR_CREATE_FILE_DIRECTORIES'] = 'Ошибка создания папки для выгрузки файла';
$MESS[$strLang.'ERROR_CREATE_FILE_DIRECTORIES_DETAILS'] = 'Ошибка создания директории в файловой системе для выгрузки файла (<code><b>#DIRNAME#</b></code>). Проверьте права доступа к папке, в которую выгружается файл.';
$MESS[$strLang.'ERROR_DELETING_OLD_EXPORT_FILE'] = 'Ошибка удаления старого файла выгрузки';
$MESS[$strLang.'ERROR_DELETING_OLD_EXPORT_FILE_DETAILS'] = 'Ошибка удаления старого файла выгрузки (<code><b>#FILENAME#</b></code>). Проверьте права доступа.';
$MESS[$strLang.'CATEGORIES_EMPTY_ANSWER'] = 'Ошибка скачивания категорий. Попробуйте еще раз.';
$MESS[$strLang.'ERROR_SAVING_CATEGORIES'] = 'Ошибка сохранения файла категорий: #FILE#. Проверьте наличие доступа для записи в этот файл.';
$MESS[$strLang.'ERROR_REPLACE_TMP_FILE'] = 'Ошибка перемещения временного файла';
$MESS[$strLang.'ERROR_REPLACE_TMP_FILE_DETAILS'] = 'Ошибка перемещения временного файла (<code><b>#FILENAME_TMP#</b></code>) на место старого файла выгрузки (<code><b>#FILENAME_REAL#</b></code>). Проверьте права доступа.';

?>