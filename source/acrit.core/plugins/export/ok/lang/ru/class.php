<?
$strMessPrefix = 'ACRIT_EXP_OK_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Одноклассники';

// Default settings
$MESS[$strMessPrefix.'SETTINGS_APP_DATA_TITLE'] = 'Данные приложения';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_TITLE'] = 'Настройки выгрузки';
$MESS[$strMessPrefix.'SETTINGS_APP_ID'] = 'ID приложения';
$MESS[$strMessPrefix.'SETTINGS_APP_ID_HINT'] = 'Введите App ID для доступа по API';
$MESS[$strMessPrefix.'SETTINGS_APP_PUB_KEY'] = 'Публичный ключ';
$MESS[$strMessPrefix.'SETTINGS_APP_PUB_KEY_HINT'] = 'Введите Public Key для доступа по API';
$MESS[$strMessPrefix.'SETTINGS_APP_SEC_KEY'] = 'Секретный ключ';
$MESS[$strMessPrefix.'SETTINGS_APP_SEC_KEY_HINT'] = 'Введите Secret Key для доступа по API';
$MESS[$strMessPrefix.'SETTINGS_ACCESS_TOKEN'] = 'Токен доступа';
$MESS[$strMessPrefix.'SETTINGS_ACCESS_TOKEN_HINT'] = 'Введите Access Token для доступа по API';
$MESS[$strMessPrefix.'SETTINGS_USER_ID'] = 'ID пользователя OK';
$MESS[$strMessPrefix.'SETTINGS_USER_ID_HINT'] = 'Введите ID пользователя, в список товаров которого будут загружаться товары';
$MESS[$strMessPrefix.'SETTINGS_GROUP_ID'] = 'ID группы OK';
$MESS[$strMessPrefix.'SETTINGS_GROUP_ID_HINT'] = 'Введите ID группы, в которую будут загружаться товары';
$MESS[$strMessPrefix.'SETTINGS_GROUP_LINK'] = 'Символьный код группы';
$MESS[$strMessPrefix.'SETTINGS_GROUP_LINK_HINT'] = 'Символьное наименование группы, если таковое имеется';
$MESS[$strMessPrefix.'SETTINGS_GROUP_ID_URL'] = 'Перейти в магазин';
$MESS[$strMessPrefix.'SETTINGS_CONSOLE'] = 'PHP-консоль для отладки';
$MESS[$strMessPrefix.'SETTINGS_CONSOLE_HINT'] = 'Здесь вы можете выполнять команды ok';
$MESS[$strMessPrefix.'SETTINGS_CONSOLE_PLACEHOLDER'] = 'Например, print $this->getCode()';
$MESS[$strMessPrefix.'SETTINGS_CONSOLE_SEND'] = 'Выполнить';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_CREATE_CATALOGS'] = 'Создавать новые каталоги товаров';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_CREATE_CATALOGS_HINT'] = 'Если в профиле заполнены данные для привязки товаров к каталогу и в процессе импорта каталог не будет найден, то будет создан новый';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_DELETE_OTHER'] = 'Удалять товары, отсутствующие в выгрузке';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_DELETE_OTHER_HINT'] = 'Товары, которых не было в выгрузке, будут полностью удалены из группы. Опция несовместима с поэтапной выгрузкой!';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_DELETE_DUPLICATES'] = 'Удалять дубликаты товаров';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_DELETE_DUPLICATES_HINT'] = 'Удалять дублирующие товары с одинаковыми названиями';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_LIMIT'] = 'Выгружаемых за раз товаров';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_LIMIT_HINT'] = 'Количество товаров, выгружаемых за один запуск экспорта (0 - выгружать все товары)';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_NEXT_POS'] = 'Позиция очередой выгрузки';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_NEXT_POS_HINT'] = 'Позиция, с которой начнётся очередная выгрузка';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_NEXT_POS_RESET'] = 'Сбросить позицию';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_NEXT_POS_RESET_ALERT'] = 'Сбросить позицию начала выгрузки?';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_IMAGE_RESIZE'] = 'Ресайз изображений';
$MESS[$strMessPrefix.'SETTINGS_PROCESS_IMAGE_RESIZE_HINT'] = 'Обработка изображений, размеры которых меньше требуемых для OK';
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
