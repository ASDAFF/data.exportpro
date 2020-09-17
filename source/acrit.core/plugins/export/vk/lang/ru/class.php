<?
$strMessPrefix = 'ACRIT_EXP_VK_';

// General
$MESS[$strMessPrefix.'NAME'] = 'ВКонтакте';

// Default settings
$MESS[$strMessPrefix.'SETTINGS_ACCESS_TOKEN'] = 'Access Token';
$MESS[$strMessPrefix.'SETTINGS_ACCESS_TOKEN_HINT'] = 'Введите токен VK для доступа по API';
$MESS[$strMessPrefix.'SETTINGS_USER_ID'] = 'ID пользователя VK';
$MESS[$strMessPrefix.'SETTINGS_USER_ID_HINT'] = 'Введите ID пользователя, в список товаров которого будут загружаться товары';
$MESS[$strMessPrefix.'SETTINGS_GROUP_ID'] = 'ID группы VK';
$MESS[$strMessPrefix.'SETTINGS_GROUP_ID_HINT'] = 'Введите ID группы, в которую будут загружаться товары';
$MESS[$strMessPrefix.'SETTINGS_GROUP_ID_URL'] = 'Перейти в магазин';
$MESS[$strMessPrefix.'SETTINGS_CONSOLE'] = 'PHP-консоль для отладки';
$MESS[$strMessPrefix.'SETTINGS_CONSOLE_HINT'] = 'Здесь вы можете выполнять команды vk';
$MESS[$strMessPrefix.'SETTINGS_CONSOLE_PLACEHOLDER'] = 'Например, print $this->getCode()';
$MESS[$strMessPrefix.'SETTINGS_CONSOLE_SEND'] = 'Выполнить';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_CREATE_ALBUMS'] = 'Создавать новые подборки товаров';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_CREATE_ALBUMS_HINT'] = 'Если в профиле заполнены данные для привязки товаров к подборкам и в процессе импорта подборка не будет найдена, то будет создана новая';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_DELETE_OTHER'] = 'Удалять товары, отсутствующие в выгрузке';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_DELETE_OTHER_HINT'] = 'Товары, которых не было в выгрузке, будут полностью удалены из группы';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_DELETE_DUPLICATES'] = 'Удалять дубликаты товаров';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_DELETE_DUPLICATES_HINT'] = 'Удалять дублирующие товары с одинаковыми названиями';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_LIMIT'] = 'Выгружаемых за раз товаров';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_LIMIT_HINT'] = 'Количество товаров, выгружаемых за один запуск экспорта (0 - выгружать все товары)';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_NEXT_POS'] = 'Позиция очередой выгрузки';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_NEXT_POS_HINT'] = 'Позиция, с которой начнётся очередная выгрузка';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_NEXT_POS_RESET'] = 'Сбросить позицию';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_NEXT_POS_RESET_ALERT'] = 'Сбросить позицию начала выгрузки?';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_IMAGE_RESIZE'] = 'Ресайз изображений';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_IMAGE_RESIZE_HINT'] = 'Обработка изображений, размеры которых меньше требуемых для VK';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_IMAGE_RESIZE_V_FILL'] = 'Заполнить белым фоном';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_IMAGE_RESIZE_V_RESIZE'] = 'Растянуть до необходимого размера';

// Fields
$MESS[$strMessPrefix.'FIELD_ID_NAME'] = 'Идентификатор предложения';

# Display results
$MESS[$strMessPrefix.'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix.'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix.'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix.'RESULT_DATETIME'] = 'Время окончания';
$MESS[$strMessPrefix.'RESULT_FILE_URL'] = 'Перейти к файлу выгрузки';

?>
