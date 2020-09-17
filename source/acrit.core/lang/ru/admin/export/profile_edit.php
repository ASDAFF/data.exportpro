<?
// Titles
$MESS['ACRIT_EXP_PAGE_TITLE_DEFAULT'] = 'Управления профилями экспорта на торговые площадки';
$MESS['ACRIT_EXP_PAGE_TITLE_ADD'] = 'Добавление профиля экспорта';
$MESS['ACRIT_EXP_PAGE_TITLE_COPY'] = 'Копирование профиля экспорта ##ID#';
$MESS['ACRIT_EXP_PAGE_TITLE_EDIT'] = 'Редактирование профиля экспорта ##ID#';

// Core notice
$MESS['ACRIT_EXP_CORE_NOTICE'] = '<b>Внимание!</b> Не установлен необходимый для работы служебный модуль <a href="/bitrix/admin/update_system_partner.php?addmodule=#CORE_ID#&lang=#LANG#" target="_blank">acrit.core</a>. Установите его для продолжения работы.';

// Main notice
$MESS['ACRIT_EXP_MAIN_NOTICE_FOR_HINTS'] = '<b style="color:red;display:inline;">Внимательно читайте подсказки к настройкам!</b> Подсказки (<span id="hint_main_noice"></span><span style="display:none;"><script>BX.hint_replace(BX("hint_main_noice"), "Это подсказка!");</script></span> ) содержат много важной информации, без знания которой легко совершить ошибки в настройке. <a href="#" data-role="main-notice-hide" style="display:inline;">Больше не показывать</a>';

// Lock notice
$MESS['ACRIT_EXP_LOCK_NOTICE'] = '<b>Внимание!</b> Текущий профиль заблокирован (т.е. выполняется загрузка). <b>Внесение изменений в профиль не рекомендуется.</b> Дата блокировки: #DATE#. <a href="javascript:void(0)" data-role="profile-unlock" data-confirm="Действительно разблокировать?">Разблокировать</a>';

// General popup
$MESS['ACRIT_EXP_POPUP_LOADING'] = 'Загрузка...';
$MESS['ACRIT_EXP_POPUP_SAVE'] = 'Сохранить';
$MESS['ACRIT_EXP_POPUP_CLOSE'] = 'Закрыть';
$MESS['ACRIT_EXP_POPUP_CANCEL'] = 'Отменить';
$MESS['ACRIT_EXP_POPUP_REFRESH'] = 'Обновить';

// Context menu
$MESS['ACRIT_EXP_MENU_LIST'] = 'Список профилей';
$MESS['ACRIT_EXP_MENU_CRON'] = 'Настроить автозапуск';
$MESS['ACRIT_EXP_MENU_RUN'] = 'Запустить!';
$MESS['ACRIT_EXP_MENU_ACTIONS'] = 'Действия';
$MESS['ACRIT_EXP_MENU_ADD'] = 'Добавить новый профиль';
$MESS['ACRIT_EXP_MENU_COPY'] = 'Копировать текущий профиль';
$MESS['ACRIT_EXP_MENU_DELETE'] = 'Удалить текущий профиль';
	$MESS['ACRIT_EXP_MENU_DELETE_CONFIRM'] = 'Вы уверены что хотите удалить текущий профиль?';
$MESS['ACRIT_EXP_MENU_BACKUP'] = 'Скачать рез. копию профиля';
$MESS['ACRIT_EXP_MENU_HELP'] = 'Помощь';
$MESS['ACRIT_EXP_GET_FILE_URL'] = 'Ссылка на файл выгрузки';

// Tabs
$MESS['ACRIT_EXP_TAB_GENERAL_NAME'] = 'Общие настройки';
	$MESS['ACRIT_EXP_TAB_GENERAL_DESC'] = 'Общие настройки профиля экспорта';
$MESS['ACRIT_EXP_TAB_STRUCTURE_NAME'] = 'Настройки инфоблоков';
	$MESS['ACRIT_EXP_TAB_STRUCTURE_DESC'] = 'Настройки выгрузки для каждого инфоблока';
$MESS['ACRIT_EXP_TAB_CURRENCY_NAME'] = 'Валюты и цены';
	$MESS['ACRIT_EXP_TAB_CURRENCY_DESC'] = 'Настройки валют и цен';
$MESS['ACRIT_EXP_TAB_MORE_NAME'] = 'Дополнительно';
	$MESS['ACRIT_EXP_TAB_MORE_DESC'] = 'Дополнительные настройки профиля';
$MESS['ACRIT_EXP_TAB_CRON_NAME'] = 'Автозапуск';
	$MESS['ACRIT_EXP_TAB_CRON_DESC'] = 'Добавление выгрузки профиля в планировщик Cron';
$MESS['ACRIT_EXP_TAB_LOG_NAME'] = 'Лог и история';
	$MESS['ACRIT_EXP_TAB_LOG_DESC'] = 'Лог профиля и история выгрузки';

// Popup: SelectField
$MESS['ACRIT_EXP_POPUP_SELECT_FIELD_TITLE'] = 'Выбор поля';

// Popup: ValueSettings
$MESS['ACRIT_EXP_POPUP_VALUE_SETTINGS_TITLE'] = 'Настройки значения поля ';

// Popup: FieldSettings
$MESS['ACRIT_EXP_POPUP_FIELD_SETTINGS_TITLE'] = 'Настройки поля';

// Popup: AdditionalFields
$MESS['ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_TITLE'] = 'Добавление дополнительных полей';

// Popup: CategoriesRedefinition
$MESS['ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_TITLE'] = 'Переопределение названий разделов';
$MESS['ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_ALL'] = 'Удалить все';
$MESS['ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_CONFIRM'] = 'Удалить все сохраненные данные для названий категорий?';

// Popup: CategoriesRedefinitionSelect
$MESS['ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_SELECT_TITLE'] = 'Выбор категории';

// Popup: execute
$MESS['ACRIT_EXP_POPUP_EXECUTE_TITLE'] = 'Запуск экспорта';
$MESS['ACRIT_EXP_POPUP_EXECUTE_BUTTON_START'] = 'Запустить!';
$MESS['ACRIT_EXP_POPUP_EXECUTE_BUTTON_STOP'] = 'Остановить';
$MESS['ACRIT_EXP_POPUP_EXECUTE_STOPPED'] = 'Процесс остановлен.';
$MESS['ACRIT_EXP_POPUP_EXECUTE_ERROR'] = 'Произошла ошибка. Подробности в консоли браузера.';

// Popup: cron
$MESS['ACRIT_EXP_POPUP_CRON_ERROR'] = 'Ошибка установки задания планировщика.';

// Popup: Iblocks preview
$MESS['ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_TITLE'] = 'Просмотр инфоблоков';

// Get file url messages
$MESS['ACRIT_EXP_GET_FILE_TITLE'] = 'Ссылка на файл выгрузки:';
$MESS['ACRIT_EXP_GET_FILE_URL_NO_DOMAIN'] = 'Не указан домен сайта.';
$MESS['ACRIT_EXP_GET_FILE_URL_NO_FILENAME'] = 'Не указан путь к файлу выгрузки.';

// IBlock save result
$MESS['ACRIT_EXP_IBLOCK_SETTINGS_SAVE_PROGRESS'] = '&nbsp; <span style="color:orange">Настройки сохраняются..</span>';
$MESS['ACRIT_EXP_IBLOCK_SETTINGS_SAVE_SUCCESS'] = '&nbsp; <span style="color:green">Настройки успешно сохранены!</span>';
$MESS['ACRIT_EXP_IBLOCK_SETTINGS_SAVE_ERROR'] = '&nbsp; <span style="color:red">Ошибка при сохранении настроек</span>';
$MESS['ACRIT_EXP_IBLOCK_SETTINGS_CLEAR_CONFIRM'] = 'Действительно очистить заполненные поля для данного инфоблока \n(#ID#, #NAME#)?';

// Additional fields
$MESS['ACRIT_EXP_ADDITIONAL_FIELD_DELETE_CONFIRM'] = 'Поле будет немедленно удалено.\nПродолжить?';
$MESS['ACRIT_EXP_ADDITIONAL_FIELDS_DELETE_ALL_CONFIRM'] = 'Все созданные дополнительные поля будут немедленно удалены.\nПродолжить?';

//
$MESS['ACRIT_EXP_UPDATE_CATEGORIES_UNAVAILABLE'] = 'Обновление категорий доступно только при активной лицензии модуля экспорта.<br/>Необходимо <a href="#LINK#" target="_blank">продлить лицензию</a> для возможности обновления категорий.';
$MESS['ACRIT_EXP_UPDATE_CATEGORIES_SUCCESS'] = '<span style="color:green;"><b>Категории успешно обновлены!</b></span>';
$MESS['ACRIT_EXP_UPDATE_CATEGORIES_ERROR'] = '<span style="color:red;"><b>Ошибка обновления категорий.</b></span>';
$MESS['ACRIT_EXP_UPDATE_CATEGORIES_ERROR_NOTE'] = 'Попробуйте еще раз.';

//
$MESS['ACRIT_EXP_AJAX_AUTH_REQUIRED'] = 'Необходима авторизация. Вы можете выполнить авторизацию в отдельном окне и затем в текущем окне повторить операцию.';
$MESS['ACRIT_EXP_AJAX_CONFIRM_CLEAR_EXPORT_DATA'] = 'Это действие удалит все ранее сгенерированные данные экспорта для каждого из товаров. Продолжить?';
$MESS['ACRIT_EXP_AJAX_CONSOLE_TIME'] = 'Время выполнения: #TIME#';

// Run in background
$MESS['ACRIT_EXP_RUN_BACKGROUND_SUCCESS'] = 'Процесс экспорта запущен!';
$MESS['ACRIT_EXP_RUN_BACKGROUND_DISABLED'] = 'На данном сайте нет возможности запуска в фоне (возможно, недоступна php-функции proc_open и proc_close).';
$MESS['ACRIT_EXP_RUN_BACKGROUND_INACTIVE'] = 'Текущий профиль неактивен, поэтому его выгрузка невозможна.';
$MESS['ACRIT_EXP_RUN_BACKGROUND_BLOCKED'] = 'Текущий профиль заблокирован, т.к. выполняется другой процесс экспорта.';
$MESS['ACRIT_EXP_RUN_BACKGROUND_ERROR'] = 'Ошибка запуска.';

//
$MESS['ACRIT_EXP_AJAX_CRON_SETUP_SUCCESS'] = 'Настроена автоматическая загрузка профиля: #COMMAND#';
$MESS['ACRIT_EXP_AJAX_CRON_SETUP_ERROR'] = 'Ошибка добавления задания в планировщик: #COMMAND#';
$MESS['ACRIT_EXP_AJAX_CRON_DELETE_SUCCESS'] = 'Автоматическая загрузка профиля отменена.';

//
$MESS['ACRIT_EXP_ERROR_FORMAT_NOT_FOUND_TITLE'] = 'Ошибка! Формат выгрузки не найден.';
$MESS['ACRIT_EXP_ERROR_FORMAT_NOT_FOUND_DETAILS'] = 'Формат выгрузки (#FORMAT#) не найден. Возможно, он удален.<br/>
Необходимо выбрать в поле «Плагин» и «Формат» нужный плагин и (если необходимо) нужный формат, затем применить настройки профиля и после этого проверить все настройки.';
$MESS['ACRIT_EXP_ERROR_CONSOLE_ACCESS_DENIED'] = 'Вы не имеете доступа к выполнению кода в консоли. Только администраторы сайта имеют право выполнения кода.';
$MESS['ACRIT_EXP_ERROR_FIRST_ELEMENT_IS_NOT_FOUND'] = 'Подходящих элементов нет.';
$MESS['ACRIT_EXP_ERROR_FILENAME_IS_NOT_UNIQUE'] = 'Один из профилей (ID=#ID#) уже настроен на выгрузку в данный файл:'."\n".'#FILENAME#';

?>