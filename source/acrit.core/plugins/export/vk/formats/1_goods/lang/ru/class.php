<?
$strMessPrefix = 'ACRIT_EXP_VK_GOODS_';

// General

$MESS[$strMessPrefix.'NAME'] = 'ВКонтакте (товары)';

// Headers
$MESS[$strMessPrefix.'HEADER_GENERAL'] = 'Общие данные';

$MESS[$strMessPrefix."FIELD_ITEM_ID_NAME"] = "Идентификатор товара";
	$MESS[$strMessPrefix."FIELD_ITEM_ID_DESC"] = "Идентификатор товара.";
$MESS[$strMessPrefix."FIELD_OWNER_ID_NAME"] = "Идентификатор владельца товаров";
$MESS[$strMessPrefix."FIELD_NAME_NAME"] = "Название товара";
	$MESS[$strMessPrefix."FIELD_NAME_DESC"] = "Длина названия - от 4 до 100 символов";
$MESS[$strMessPrefix."FIELD_DESCRIPTION_NAME"] = "Описание товара";
	$MESS[$strMessPrefix."FIELD_DESCRIPTION_DESC"] = "Минимальная длина 10 символов";
$MESS[$strMessPrefix."FIELD_CATEGORY_ID_NAME"] = "Идентификатор категории товара";
$MESS[$strMessPrefix."FIELD_PRICE_NAME"] = "Цена товара";
$MESS[$strMessPrefix."FIELD_OLD_PRICE_NAME"] = "Старая цена";
$MESS[$strMessPrefix."FIELD_MAIN_PHOTO_ID_NAME"] = "Фотография обложки товара";
	$MESS[$strMessPrefix."FIELD_MAIN_PHOTO_ID_DESC"] = "Допустимые форматы: JPG, PNG, GIF.<br/>
Ограничения: минимальный размер фото — 400x400px, сумма высоты и ширины не более 14000px, файл объемом не более 50 МБ. ";
$MESS[$strMessPrefix."FIELD_PHOTO_IDS_NAME"] = "Дополнительные фотографии";
	$MESS[$strMessPrefix."FIELD_PHOTO_IDS_DESC"] = "Дополнительные фотографии товара.";
$MESS[$strMessPrefix."FIELD_ALBUM_NAME_NAME"] = "Название подборки товаров";
	$MESS[$strMessPrefix."FIELD_ALBUM_NAME_DESC"] = "Название подборки товаров группы VK.";
$MESS[$strMessPrefix."FIELD_ALBUM_PHOTO_ID_NAME"] = "Картинка подборки";
	$MESS[$strMessPrefix."FIELD_ALBUM_PHOTO_ID_DESC"] = "Допустимые форматы: JPG, PNG, GIF.<br/>
Ограничения: минимальный размер фото — 1280x720px, сумма высоты и ширины не более 14000px, файл объемом не более 50 МБ. ";
$MESS[$strMessPrefix."FIELD_DELETED_NAME"] = "Недоступный товар";
	$MESS[$strMessPrefix."FIELD_DELETED_DESC"] = "Укажите 0, если товар доступен, 1 если товар скрыт.";
$MESS[$strMessPrefix."FIELD_PORTAL_REQUIREMENTS"] = "https://vk.com/dev/methods";

# Steps
$MESS[$strMessPrefix.'STEP_EXPORT'] = 'Экспорт в VK';

# Tabs
$MESS[$strMessPrefix.'TAB_CLEAR_NAME'] = 'Очистка';
$MESS[$strMessPrefix.'TAB_CLEAR_DESC'] = 'Удаление товаров группы';
$MESS[$strMessPrefix.'TAB_ALBUMS_NAME'] = 'Подборки';
$MESS[$strMessPrefix.'TAB_ALBUMS_DESC'] = 'Работа с подборками группы';
$MESS[$strMessPrefix.'CLEAR_HEADER'] = 'Удаление товаров';
$MESS[$strMessPrefix.'CLEAR_ALERT'] = 'Удалить товары?';
$MESS[$strMessPrefix.'CLEAR_ALL'] = 'Удаление всех товаров';
$MESS[$strMessPrefix.'CLEAR_ALL_DESC'] = 'Удаление всех товаров группы';
$MESS[$strMessPrefix.'CLEAR_ALL_BTN_TITLE'] = 'Удалить все товары';
$MESS[$strMessPrefix.'CLEAR_LOADED'] = 'Удаление загруженных товаров';
$MESS[$strMessPrefix.'CLEAR_LOADED_DESC'] = 'Удаление товаров, совпадающих с теми, что представлены в данном профиле выгрузки';
$MESS[$strMessPrefix.'CLEAR_LOADED_BTN_TITLE'] = 'Удалить загруженные товары';
$MESS[$strMessPrefix.'CLEAR_ALBUM'] = 'Удаление товаров подборки';
$MESS[$strMessPrefix.'CLEAR_ALBUM_DESC'] = 'Удаление товаров выбранной подборки';
$MESS[$strMessPrefix.'CLEAR_ALBUM_BTN_TITLE'] = 'Удалить товары подборки';
$MESS[$strMessPrefix.'CLEAR_ALBUM_SELECT_TITLE'] = 'Выберите подборку';
$MESS[$strMessPrefix.'ALBUMS_HEADER'] = 'Соответствие имён подборок и разделов';
$MESS[$strMessPrefix.'ALBUMS_TABLE_H1'] = 'Раздел';
$MESS[$strMessPrefix.'ALBUMS_TABLE_H2'] = 'Название подборки';
$MESS[$strMessPrefix.'ALBUMS_TABLE_H3'] = 'ID подборки (заполняется автоматически)';
$MESS[$strMessPrefix.'ALBUMS_TABLE_NOTE'] = 'Данная таблица замен сработает лишь в том случае, если для поля "Название подборки товаров (album_name)" из списка вариантов выбран "ID (раздел)" товара (см. вкладку "Настройки инфоблоков" / "Поля товаров").
<br><br>В таблице замен, столбец "ID подборки" не нужно заполнять. Значения в нём появятся при очередной выгрузке для тех строк, у которых заполнен столбец "Название подборки".';

# Process
$MESS[$strMessPrefix.'PROCESS_PHASED_END_STEP'] = 'Завершён этап экспорта. При следующем запуске экспорт продолжится с позиции #POSITION#.';
$MESS[$strMessPrefix.'PROCESS_PHASED_END_ALL'] = 'Завершены все этапы экспорта.';

?>
