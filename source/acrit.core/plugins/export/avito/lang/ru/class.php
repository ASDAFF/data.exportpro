<?

$strMessPrefix = 'ACRIT_EXP_AVITO_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито';

// Documentation
$MESS[$strMessPrefix.'PARAGRAPH_ABOUT_REQUIRED_PARAMS'] = '<b>Внимание!</b><br/>
Некоторые параметры являются <b>необязательными</b> в одних случаях и <b>обязательными</b> в других,<br/>
поэтому в форме настроек обязательными отмечены только некоторые.<br/>
При возникновении затруднений внимательно читайте подробную информацию по каждому элементу<br/>
(в подсказках и в документации).';
$MESS[$strMessPrefix.'IMAGES_MAX_COUNT'] = 'Максимальное количество изображений для категории «#NAME#»: <b>#COUNT#</b>.';
$MESS[$strMessPrefix.'USEFUL_LINKS'] = 'Полезные ссылки:';
$MESS[$strMessPrefix.'DOCUMENTATION'] = 'Документация';
$MESS[$strMessPrefix.'CHECK_XML'] = 'Проверить XML';
$MESS[$strMessPrefix.'FAQ'] = 'Вопросы и ответы';

// Default settings
$MESS[$strMessPrefix.'SETTINGS_FILE'] = 'Итоговый файл';
$MESS[$strMessPrefix.'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/xml/avito.xml';
$MESS[$strMessPrefix.'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/avito.xml</code>';
$MESS[$strMessPrefix.'SETTINGS_ENCODING'] = 'Кодировка файла';
$MESS[$strMessPrefix.'SETTINGS_ENCODING_HINT'] = 'Выберите кодировку файла. Принципиальной разницы между кодировками нет.';
$MESS[$strMessPrefix.'SETTINGS_ZIP'] = 'Упаковать в Zip';
$MESS[$strMessPrefix.'SETTINGS_ZIP_HINT'] = 'Данный параметр позволяет запаковать сформированный файл в Zip. Благодаря упаковке в Zip-архив, размер файла, отдаваемого в Яндекс.Маркет, существенно уменьшается, что ускоряет его скачивание сервисом.';

// Headers
$MESS[$strMessPrefix.'HEADER_GENERAL'] = 'Общие данные';
$MESS[$strMessPrefix.'HEADER_LOCATION'] = 'Данные о местоположении';
$MESS[$strMessPrefix.'HEADER_CHARACTERISTICS'] = 'Характеристики';

// Fields
$MESS[$strMessPrefix.'FIELD_ID_NAME'] = 'Идентификатор объявления';
$MESS[$strMessPrefix.'FIELD_ID_DESC'] = 'Уникальный идентификатор объявления в вашей базе данных — строка до 100 символов.<br/><br/>
У одного и того же объявления должен сохраняться один и тот же идентификатор от файла к файлу. Несоблюдение этого правила приведет к блокировке повторяющихся объявлений сайтом Авито.<br/><br/>
Для размещения нового объявления необходимо использовать новый идентификатор.';
$MESS[$strMessPrefix.'FIELD_DATE_BEGIN_NAME'] = 'Дата и время начала размещения объявления';
$MESS[$strMessPrefix.'FIELD_DATE_BEGIN_DESC'] = 'Дата и время начала размещения объявления — можно задать одним из двух способов согласно стандарту <a href="http://ru.wikipedia.org/wiki/ISO_8601" target="_blank">ISO 8601</a>:<br/><br/>
	только дата в формате "YYYY-MM-DD" (MSK);<br/>
	или дата и время в формате "YYYY-MM-DDTHH:mm:ss+hh:mm".<br/>
	Важно: объявление будет опубликовано в указанную дату (если указана только дата) или после указанного времени (если заданы дата и время) в течение часа (если нет препятствующих этому настроек режима вашей выгрузки).<br/><br/>
	Если элемент не задан, объявление будет опубликовано сразу же после первого получения XML-файла с ним.';
$MESS[$strMessPrefix.'FIELD_DATE_END_NAME'] = 'Дата и время, до которых объявление актуально';
$MESS[$strMessPrefix.'FIELD_DATE_END_DESC'] = 'Дата и время, до которых объявление актуально — можно задать одним из двух способов согласно стандарту <a href="http://ru.wikipedia.org/wiki/ISO_8601" target="_blank">ISO 8601</a>:<br/><br/>
	только дата в формате "YYYY-MM-DD" (MSK);<br/>
	или дата и время в формате "YYYY-MM-DDTHH:mm:ss+hh:mm".<br/>
	Если значение в прошлом, то новое объявление не будет опубликовано, а существующее — будет снято с публикации.<br/><br/>
	Важно: в режиме автозагрузки по умолчанию значение элемента DateEnd учитывается несколько раз в час, а при загрузке по расписанию — во время каждой загрузке.<br/><br/>
	Дата окончания размещения объявления на сайте Авито является стандартной (время окончания можно увидеть в Личном кабинете или отчетах Автозагрузки). Если после окончания публикации объявление всё ещё присутствует в XML-файле и DateEnd не указан или указано значение в будущем, то объявление будет снова активировано. Если в элементе DateEnd указана только дата (без времени) и она совпадает с датой окончания размещения объявления, то реактивация объявления не происходит.';
$MESS[$strMessPrefix.'FIELD_LISTING_FEE_NAME'] = 'Вариант платного размещения';
$MESS[$strMessPrefix.'FIELD_LISTING_FEE_DESC'] = 'Вариант <a href="https://support.avito.ru/hc/ru/articles/203867766" target="_blank">платного размещения</a> — одно из значений списка:<br/><br/>
<ul>
	<li>«Package»</li> — размещение объявления осуществляется только при наличии подходящего пакета размещения;<br/>
	<li>«PackageSingle»</li> — при наличии подходящего пакета оплата размещения объявления произойдет с него; если нет подходящего пакета, но достаточно денег на кошельке Авито, то произойдет разовое размещение;<br/>
	<li>«Single»</li> — только разовое размещение, произойдет при наличии достаточной суммы на кошельке Авито; если есть подходящий пакет размещения, он будет проигнорирован.<br/>Если элемент пуст или отсутствует, то значение по умолчанию — «Package».
</ul>
';
$MESS[$strMessPrefix.'FIELD_AD_STATUS_NAME'] = 'Платная услуга';
$MESS[$strMessPrefix.'FIELD_AD_STATUS_DESC'] = '<a href="https://support.avito.ru/hc/ru/sections/200009758" target="_blank">Платная услуга</a>, которую нужно применить к объявлению — одно из значений списка:
<ul>
	<li>«Free» — обычное объявление;</li>
	<li>«Premium» — <a href="https://support.avito.ru/hc/ru/articles/200026868" target="_blank">премиум-объявление</a>;</li>
	<li>«VIP» — <a href="https://support.avito.ru/hc/ru/articles/200026848" target="_blank">VIP-объявление</a>;</li>
	<li>«PushUp» — <a href="https://support.avito.ru/hc/ru/articles/200026828" target="_blank">поднятие объявления в поиске</a>;</li>
	<li>«Highlight» — <a href="https://support.avito.ru/hc/ru/articles/200026858" target="_blank">выделение объявления</a>;</li>
	<li>«TurboSale»— применение пакета «<a href="https://support.avito.ru/hc/ru/articles/200026838" target="_blank">Турбо-продажа</a>»;</li>
	<li>«QuickSale» — применение пакета «<a href="https://support.avito.ru/hc/ru/articles/200026838" target="_blank">Быстрая продажа</a>».</li>
</ul>
Если элемент пуст или отсутствует, то статус объявления по умолчанию — «Free».<br/><br/>
Для успешного применения платной услуги необходимо наличие денег на <a href="https://www.avito.ru/account" target="_blank">Кошельке Авито</a>. Если денег на Кошельке недостаточно для применения услуги, объявление выгружается как обычное (Free).<br/><br/>
Одна платная услуга применяется к одному объявлению не чаще, чем один раз в определенный период времени:<br/>
<ul>
	<li>для услуг «Premium», «VIP», «Highlight» — раз в 7 дней,</li>
	<li>для «PushUp» — раз в 2 дня,</li>
	<li>пакеты услуг «QuickSale», «TurboSale» — раз в 7 дней.</li>
</ul>
Если по истечении указанного времени статус объявления в XML все еще отличается от «Free», то услуга будет применена повторно.
Разные услуги для одного объявления активируются независимо друг от друга: одна услуга может быть активирована, пока еще не закончился срок действия другой. За один цикл автозагрузки можно применить только одну услугу.';
$MESS[$strMessPrefix.'FIELD_AVITO_ID_NAME'] = 'Номер объявления на Авито';
$MESS[$strMessPrefix.'FIELD_AVITO_ID_DESC'] = 'Номер объявления на Авито — целое число.<br/><br/>
Если вы размещали объявления вручную, а теперь хотите управлять ими с помощью Автозагрузки, то возможны 2 основных варианта. Вариант 1 — воспользоваться включаемым по умолчанию режимом автоматической связки объявлений (подробнее смотрите в разделе «<a href="http://autoload.avito.ru/format/faq/" target="_blank">Вопросы и ответы</a>»). К сожалению, в этом варианте неизбежен определенный процент ошибок.<br/><br/>
Второй вариант — чтобы избежать ошибок автоматической связки, можно указать в XML-файле в элементах AvitoId номера ранее размещенных объявлений. При корректных данных с вашей стороны, функционал позволит полностью избежать проблем с блокировкой объявлений за дубли и повторной оплаты размещения.<br/><br/>
Важно: Если есть сложности с добавлением отдельного элемента в XML, можно указать ссылку на номер объявления внутри элементов Description в следующем формате: «AvitoId: XXX» (где «XXX» — номер объявления). Эта информация в объявлениях на сайте отображаться не будет.';
$MESS[$strMessPrefix.'FIELD_ALLOW_EMAIL_NAME'] = 'Возможность написать сообщение по объявлению через сайт';
$MESS[$strMessPrefix.'FIELD_ALLOW_EMAIL_DESC'] = 'Возможность написать сообщение по объявлению через сайт — одно из значений списка:<br/>
<ul>
	<li>«Да»,</li>
	<li>«Нет».</li>
</ul>
Примечание: значение по умолчанию — «Да».';
$MESS[$strMessPrefix.'FIELD_ALLOW_EMAIL_DEFAULT'] = 'Да';
$MESS[$strMessPrefix.'FIELD_MANAGER_NAME_NAME'] = 'Имя менеджера, контактного лица';
$MESS[$strMessPrefix.'FIELD_MANAGER_NAME_DESC'] = 'Имя менеджера, контактного лица компании по данному объявлению — строка не более 40 символов.';
$MESS[$strMessPrefix.'FIELD_CONTACT_PHONE_NAME'] = 'Контактный телефон';
$MESS[$strMessPrefix.'FIELD_CONTACT_PHONE_DESC'] = 'Контактный телефон — строка, содержащая только один российский номер телефона; должен быть обязательно указан код города или мобильного оператора. Корректные примеры:<br/>
<ul>
	<li>+7 (495) 777-10-66,</li>
	<li>(81374) 4-55-75,</li>
	<li>8 905 207 04 90,</li>
	<li>+7 905 2070490,</li>
	<li>88123855085,</li>
	<li>9052070490.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LATITUDE_NAME'] = 'Широта';
$MESS[$strMessPrefix.'FIELD_LATITUDE_DESC'] = 'Географическая широта. Совместно с Longitude является альтернативой элементу Address.';
$MESS[$strMessPrefix.'FIELD_LONGITUDE_NAME'] = 'Долгота';
$MESS[$strMessPrefix.'FIELD_LONGITUDE_DESC'] = 'Географическая долгота. Совместно с Latitude является альтернативой элементу Address.';
$MESS[$strMessPrefix.'FIELD_ADDRESS_NAME'] = 'Полный адрес объекта';
$MESS[$strMessPrefix.'FIELD_ADDRESS_DESC'] = 'Полный адрес объекта — строка до 256 символов.<br/><br/>
Является альтернативой элементов "Region", "City", "Subway", "District", "Street" — при заполнении "Address", значения перечисленных элементов указывать не нужно, они будут проигнорированы.';
$MESS[$strMessPrefix.'FIELD_REGION_NAME'] = 'Регион';
$MESS[$strMessPrefix.'FIELD_REGION_DESC'] = 'Регион, в котором находится объект объявления — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочника</a>.<br/><br/>
Примечание: Элемент является устаревшим, рекомендуется использовать элемент "Address".';
$MESS[$strMessPrefix.'FIELD_CITY_NAME'] = 'Город или населенный пункт';
$MESS[$strMessPrefix.'FIELD_CITY_DESC'] = 'Город или населенный пункт, в котором находится объект объявления — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочника</a>.<br/><br/>
Элемент обязателен для всех регионов, кроме Москвы и Санкт-Петербурга.<br/><br/>
Справочник является неполным. Если требуемое значение в нем отсутствует, то укажите ближайший к вашему объекту пункт из справочника, а точное название населенного пункта — в элементе Street.<br/><br/>
Примечание: Элемент является устаревшим, рекомендуется использовать элемент "Address".';
$MESS[$strMessPrefix.'FIELD_SUBWAY_NAME'] = 'Ближайшая станция метро';
$MESS[$strMessPrefix.'FIELD_SUBWAY_DESC'] = '
Ближайшая станция метро — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочника</a>.<br/><br/>
Примечание: Элемент является устаревшим, рекомендуется использовать элемент "Address".';
$MESS[$strMessPrefix.'FIELD_DISTRICT_NAME'] = 'Район города';
$MESS[$strMessPrefix.'FIELD_DISTRICT_DESC'] = 'Район города — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочника</a>.<br/><br/>
Примечание: Элемент является устаревшим, рекомендуется использовать элемент "Address".';
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория';
$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_NAME'] = 'Описание объявления';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_DESC'] = 'Текстовое описание объявления в соответствии с <a href="https://support.avito.ru/hc/ru/articles/200026968" target="_blank">правилами Авито</a> — строка не более 3000 символов.
Если у вас есть оплаченная <a href="https://support.avito.ru/hc/ru/articles/226597708" target="_blank">Подписка</a>, то поместив описание внутрь <a href="https://ru.wikipedia.org/wiki/CDATA#CDATA_.D0.B2_XML" target="_blank">CDATA</a>, вы можете использовать дополнительное форматирование с помощью HTML-тегов — строго из указанного списка: p, br, strong, em, ul, ol, li.';
$MESS[$strMessPrefix.'FIELD_IMAGES_NAME'] = 'Фотографии';
$MESS[$strMessPrefix.'FIELD_IMAGES_DESC'] = 'Фотографии — вложенные элементы, по одному элементу «Image» на каждое изображение.<br/><br/>
Допустимые графические форматы фотографий: JPEG (*.jpg), PNG (*.png).<br/><br/>
Для каждой категории определено максимальное количество фотографий, которые можно прикрепить к объявлению (все фотографии свыше этого количества игнорируются).';
$MESS[$strMessPrefix.'FIELD_VIDEO_URL_NAME'] = 'Видео c YouTube';
$MESS[$strMessPrefix.'FIELD_VIDEO_URL_DESC'] = 'Видео c YouTube — ссылка. Например:<br/>http://www.youtube.com/watch?v=YKmDXNrDdBI';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Название объявления';
$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Название объявления — строка до 50 символов.<br/>
Примечание: не пишите в название цену и контактную информацию — для этого есть отдельные поля — и не используйте слово «продам».';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена в рублях';
$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена в рублях — целое число.';
$MESS[$strMessPrefix.'FIELD_CONDITION_NAME'] = 'Состояние вещи';
$MESS[$strMessPrefix.'FIELD_CONDITION_DESC'] = 'Состояние вещи — одно из значений списка:
<ul>
	<li>Новое</li>
	<li>Б/у</li>
</ul>';

# Steps
$MESS[$strMessPrefix.'STEP_EXPORT'] = 'Запись в XML-файл';

# Display results
$MESS[$strMessPrefix.'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix.'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix.'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix.'RESULT_DATETIME'] = 'Время окончания';
$MESS[$strMessPrefix.'RESULT_STEP'] = 'Выполнено шагов';

#
$MESS[$strMessPrefix.'NO_EXPORT_FILE_SPECIFIED'] = 'Не указан путь к итоговому файлу.';
?>