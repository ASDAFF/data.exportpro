<?
$strMessPrefix = 'ACRIT_EXP_EBAY_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Ebay';

// Default settings
$MESS[$strMessPrefix.'SETTINGS_TITLE'] = 'Заголовок файла (тег title)';
$MESS[$strMessPrefix.'SETTINGS_TITLE_HINT'] = 'Укажите здесь заголовок файла.';
$MESS[$strMessPrefix.'SETTINGS_DESCRIPTION'] = 'Описание файла (тег description)';
$MESS[$strMessPrefix.'SETTINGS_DESCRIPTION_HINT'] = 'Укажите здесь описание файла (необязательно).';
$MESS[$strMessPrefix.'SETTINGS_FILE'] = 'Итоговый файл';
$MESS[$strMessPrefix.'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/xml/google.xml';
$MESS[$strMessPrefix.'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/google.xml</code>';
$MESS[$strMessPrefix.'SETTINGS_FILE_OPEN'] = 'Открыть файл';
$MESS[$strMessPrefix.'SETTINGS_FILE_OPEN_TITLE'] = 'Файл откроется в новой вкладке';

$MESS[$strMessPrefix.'HEADER_GENERAL'] = 'Основные сведения о товарах';
$MESS[$strMessPrefix.'SETTINGS_ENCODING'] = 'Кодировка файла';
$MESS[$strMessPrefix.'SETTINGS_ENCODING_HINT'] = 'Выберите кодировку файла.';
$MESS[$strMessPrefix.'EBAY_CHANNEL'] = 'ID канала';
$MESS[$strMessPrefix.'EBAY_CHANNEL_HINT'] = 'ID канала. Поле channelID в выгрузке. Устанавливается в настройках личного кабинета Ebay';

// Fields
$MESS[$strMessPrefix.'FIELD_ID_NAME'] = 'Идентификатор товара';
	$MESS[$strMessPrefix.'FIELD_ID_DESC'] = 'Уникальный идентификатор товара. Не более 50 символов. Должен быть уникальным для каждого предложения';

// Other
$MESS[$strMessPrefix.'STEP_EXPORT'] = 'Запись в XML-файл';
$MESS[$strMessPrefix.'SETTINGS_POLICY'] = 'Настройки канала';
$MESS[$strMessPrefix.'SETTINGS_POLICY_HINT'] = 'Настройки канала. shippingPolicyName, paymentPolicyName и returnPolicyName. Указываются в личном кабинете';
$MESS[$strMessPrefix.'SHIPPINGPOLICY'] = 'shippingPolicyName';
$MESS[$strMessPrefix.'PAYMENTPOLICY'] = 'paymentPolicyName';
$MESS[$strMessPrefix.'RETURNPOLICY'] = 'returnPolicyName';

// Display results
$MESS[$strMessPrefix.'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix.'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix.'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix.'RESULT_DATETIME'] = 'Время окончания';
$MESS[$strMessPrefix.'RESULT_FILE_ZIP'] = 'Скачать ZIP-архив';
?>
