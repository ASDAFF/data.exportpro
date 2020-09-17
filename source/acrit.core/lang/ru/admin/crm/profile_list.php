<?
// General
$MESS['ACRIT_EXP_PAGE_TITLE_DEFAULT'] = 'Управления профилями экспорта на торговые площадки';
$MESS['ACRIT_EXP_PAGE_TITLE'] = 'Список профилей экспорта';

// Core notice
$MESS['ACRIT_EXP_CORE_NOTICE'] = '<b>Внимание!</b> Не установлен необходимый для работы служебный модуль <a href="/bitrix/admin/update_system_partner.php?addmodule=#CORE_ID#&lang=#LANG#" target="_blank">acrit.core</a>. Установите его для продолжения работы.';

// General popup
$MESS['ACRIT_EXP_POPUP_LOADING'] = 'Загрузка...';

// Popup: backup restore
$MESS['ACRIT_EXP_POPUP_RESTORE_TITLE'] = 'Восстановление профилей из резервной копии';
$MESS['ACRIT_EXP_POPUP_RESTORE_SAVE'] = 'Восстановить';
$MESS['ACRIT_EXP_POPUP_RESTORE_CLOSE'] = 'Отменить';
$MESS['ACRIT_EXP_POPUP_RESTORE_WRONG_FILE'] = 'Выбран некорректный файл';
$MESS['ACRIT_EXP_POPUP_RESTORE_NO_FILE'] = 'Не выбран файл с резервной копией';
$MESS['ACRIT_EXP_POPUP_RESTORE_SUCCESS'] = 'Восстановление выполнено.';
$MESS['ACRIT_EXP_POPUP_RESTORE_ERROR'] = 'Ошибка при восстановлении.';

// Backup
$MESS['ACRIT_EXP_POPUP_BACKUP_ERROR'] = 'Ошибка создания резервной копии.';
$MESS['ACRIT_EXP_POPUP_BACKUP_ERROR_FILE_IS_NOT_WRITEABLE'] = 'Файл недоступен для записи (#DATA#).';
$MESS['ACRIT_EXP_POPUP_BACKUP_ERROR_DIR_IS_NOT_WRITEABLE'] = 'Папка недоступна для записи (#DATA#).';

$MESS['ACRIT_EXP_HEADER_ID'] = 'ID';
$MESS['ACRIT_EXP_HEADER_ACTIVE'] = 'Акт.';
$MESS['ACRIT_EXP_HEADER_NAME'] = 'Название';
$MESS['ACRIT_EXP_HEADER_DESCRIPTION'] = 'Описание';
$MESS['ACRIT_EXP_HEADER_SORT'] = 'Сорт.';
$MESS['ACRIT_EXP_HEADER_SITE_ID'] = 'Сайт';
$MESS['ACRIT_EXP_HEADER_DOMAIN'] = 'Домен';
$MESS['ACRIT_EXP_HEADER_IS_HTTPS'] = 'SSL';
$MESS['ACRIT_EXP_HEADER_AUTO_GENERATE'] = 'Автообработка';
$MESS['ACRIT_EXP_HEADER_AUTO_CRON'] = 'Автозапуск по Cron';
$MESS['ACRIT_EXP_HEADER_FORMAT'] = 'Формат выгрузки';
$MESS['ACRIT_EXP_HEADER_EXPORT_FILE_NAME'] = 'Файл экспорта';
	$MESS['ACRIT_EXP_HEADER_EXPORT_FILE_NAME_TITLE'] = 'Нажмите, чтобы открыть файл в новой вкладке';
$MESS['ACRIT_EXP_HEADER_DATE_CREATED'] = 'Дата создания';
$MESS['ACRIT_EXP_HEADER_DATE_MODIFIED'] = 'Дата изменения';

// Header for dynamic fields
$MESS['ACRIT_EXP_HEADER_DATE_START'] = 'Дата запуска';
$MESS['ACRIT_EXP_HEADER_DATE_END'] = 'Дата завершения';
$MESS['ACRIT_EXP_HEADER_DATE_LOCKED'] = 'Дата блокировки';
$MESS['ACRIT_EXP_HEADER_TIME_GENERATED'] = 'Время генерации';
$MESS['ACRIT_EXP_HEADER_TIME_TOTAL'] = 'Время выгрузки';
$MESS['ACRIT_EXP_HEADER_COUNT_SUCCESS'] = 'Выгружено успешно';
$MESS['ACRIT_EXP_HEADER_COUNT_ERROR'] = 'Выгружено с&nbsp;ошибками';
// Context
$MESS['ACRIT_EXP_CONTEXT_PROFILE_EDIT'] = 'Редактировать';
$MESS['ACRIT_EXP_CONTEXT_PROFILE_COPY'] = 'Копировать';
$MESS['ACRIT_EXP_CONTEXT_PROFILE_DELETE'] = 'Удалить';
$MESS['ACRIT_EXP_CONTEXT_PROFILE_DELETE_CONFIRM'] = 'Удалить профиль %s?';
$MESS['ACRIT_EXP_CONTEXT_PROFILE_BACKUP'] = 'Скачать рез. копию';
$MESS['ACRIT_EXP_CONTEXT_PROFILE_ACTIVATE'] = 'Активировать';
$MESS['ACRIT_EXP_CONTEXT_PROFILE_DEACTIVATE'] = 'Деактивировать';
$MESS['ACRIT_EXP_CONTEXT_PROFILE_UNLOCK'] = 'Снять блокировку';
$MESS['ACRIT_EXP_CONTEXT_PROFILE_REMOVE_CRONTAB'] = 'Отменить автозапуск по Cron';

// ToolBar
$MESS['ACRIT_EXP_TOOLBAR_ADD'] = 'Добавить профиль';
$MESS['ACRIT_EXP_TOOLBAR_BACKUP'] = 'Резервное копирование';
$MESS['ACRIT_EXP_TOOLBAR_BACKUP_CREATE'] = 'Скачать рез. копию выбранных профилей';
$MESS['ACRIT_EXP_TOOLBAR_BACKUP_RESTORE'] = 'Восстановить из рез. копии';

// Group actions
$MESS['ACRIT_EXP_GROUP_UNLOCK'] = 'снять блокировку';
$MESS['ACRIT_EXP_GROUP_UNCRON'] = 'отменить автозапуск по Cron';
$MESS['ACRIT_EXP_GROUP_ERROR_NOT_FOUND'] = 'Профиль #ID# не найден.';
$MESS['ACRIT_EXP_GROUP_ERROR_DELETE'] = 'Ошибка при удалении профиля #NAME#';
$MESS['ACRIT_EXP_GROUP_ERROR_UPDATE'] = 'Ошибка при изменении профиля #NAME#';
$MESS['ACRIT_EXP_GROUP_ERROR_UNLOCK'] = 'Ошибка при снятии блокировки профиля #NAME#';
$MESS['ACRIT_EXP_GROUP_ERROR_UNCRON'] = 'Ошибка отмены автозапуска профиля #NAME#';

// Filter
$MESS['ACRIT_EXP_FILTER_ID'] = 'ID профиля';
$MESS['ACRIT_EXP_FILTER_ACTIVE'] = 'Активность';
$MESS['ACRIT_EXP_FILTER_LOCKED'] = 'Блокировка';
$MESS['ACRIT_EXP_FILTER_NAME'] = 'Название';
$MESS['ACRIT_EXP_FILTER_FORMAT'] = 'Формат выгрузки';
$MESS['ACRIT_EXP_FILTER_AUTO_GENERATE'] = 'Автогенерация';
$MESS['ACRIT_EXP_FILTER_SITE_ID'] = 'Сайт';
$MESS['ACRIT_EXP_FILTER_DATE_CREATED'] = 'Дата создания';
$MESS['ACRIT_EXP_FILTER_DATE_MODIFIED'] = 'Дата изменения';

// CRM
$MESS['ACRIT_CRM_SETTINGS_LINK'] = 'Настройки подключения к Битрикс24 находятся в <a href="/bitrix/admin/settings.php?lang=ru&mid=acrit.exportproplus&acrit_exportproplus_tab_control_active_tab=crm">настройках модуля</a>.';

?>