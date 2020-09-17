<?
$strMessPrefix = 'ACRIT_EXP_AVITO_CARS_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Авто)';

// Headers
$MESS[$strMessPrefix.'HEADER_ADDITIONAL'] = 'Дополнительные параметры и опции';

// Fields
$MESS[$strMessPrefix.'FIELD_STREET_NAME'] = 'Место осмотра';
	$MESS[$strMessPrefix.'FIELD_STREET_DESC'] = 'Место осмотра — строка до 256 символов, содержащая:<br/>
<ul>
	<li>название улицы и номер дома — если задан точный населенный пункт из справочника;</li>
	<li>если нужного населенного пункта нет в справочнике, то в этом элементе нужно указать:
		<ul>
			<li>район региона (если есть)</li>
			<li>населенный пункт (обязательно)</li>
			<li>улицу и номер дома, например для Тамбовской обл.: "Моршанский р-н, с. Устьи, ул. Лесная, д. 7"</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория — "Автомобили".';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DEFAULT'] = 'Автомобили';
$MESS[$strMessPrefix.'FIELD_CAR_TYPE_NAME'] = 'Тип автомобиля';
	$MESS[$strMessPrefix.'FIELD_CAR_TYPE_DESC'] = 'Тип автомобиля — одно из значений списка:<br/>
<ul>
	<li>С пробегом</li>
	<li>Новые</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_MAKE_NAME'] = 'Марка автомобиля';
	$MESS[$strMessPrefix.'FIELD_MAKE_DESC'] = 'Марка автомобиля — в соответствии со значениями из <a href="https://autoload.avito.ru/format/Models.xml" target="_blank">справочника</a>.';
$MESS[$strMessPrefix.'FIELD_MODEL_NAME'] = 'Модель автомобиля';
	$MESS[$strMessPrefix.'FIELD_MODEL_DESC'] = 'Модель автомобиля — в соответствии со значениями из <a href="https://autoload.avito.ru/format/Models.xml" target="_blank">справочника</a>.';
$MESS[$strMessPrefix.'FIELD_YEAR_NAME'] = 'Год выпуска.';
	$MESS[$strMessPrefix.'FIELD_YEAR_DESC'] = 'Год выпуска — целое четырехзначное число.';
$MESS[$strMessPrefix.'FIELD_KILOMETRAGE_NAME'] = 'Пробег, км';
	$MESS[$strMessPrefix.'FIELD_KILOMETRAGE_DESC'] = 'Только для автомобилей с пробегом: пробег, км — целое число.';
$MESS[$strMessPrefix.'FIELD_ACCIDENT_NAME'] = 'Состояние';
	$MESS[$strMessPrefix.'FIELD_ACCIDENT_DESC'] = 'Только для автомобилей с пробегом: состояние — одно из значений списка:<br/>
<ul>
	<li>Не битый</li>
	<li>Битый</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_VIN_NAME'] = 'VIN-номер';
	$MESS[$strMessPrefix.'FIELD_VIN_DESC'] = 'VIN-номер (<a href="https://ru.wikipedia.org/wiki/%D0%98%D0%B4%D0%B5%D0%BD%D1%82%D0%B8%D1%84%D0%B8%D0%BA%D0%B0%D1%86%D0%B8%D0%BE%D0%BD%D0%BD%D1%8B%D0%B9_%D0%BD%D0%BE%D0%BC%D0%B5%D1%80_%D1%82%D1%80%D0%B0%D0%BD%D1%81%D0%BF%D0%BE%D1%80%D1%82%D0%BD%D0%BE%D0%B3%D0%BE_%D1%81%D1%80%D0%B5%D0%B4%D1%81%D1%82%D0%B2%D0%B0" target="_blank">vehicle identification number</a>) — строка из 17 символов.';
$MESS[$strMessPrefix.'FIELD_CERTIFICATION_NUMBER_NAME'] = 'Номер свидетельства ТС';
	$MESS[$strMessPrefix.'FIELD_CERTIFICATION_NUMBER_DESC'] = 'Номер свидетельства ТС — строка (без пробелов).<br/><br/>
Не отображается другим пользователям.<br/><br/>
Если для автомобиля заданы элементы VIN и CertificationNumber, то c помощью <a href="https://avtokod.mos.ru/" target="_blank">avtokod.mos.ru</a> Авито проверяет данные о продаваемых автомобилях: нахождение в залоге, наложение ограничений на регистрацию, нахождение в розыске и прочее. Все автомобили, успешно прошедшие проверку, получают специальный значок <img src="https://www.avito.ru/s/a/i/ic/vin-ok.svg?5a8a599" alt="" width="21" height="15" />.<br/><br/>
Примечание: в настоящий момент проверка работает только для автомобилей, зарегистрированных в Москве и Московской области.';
$MESS[$strMessPrefix.'FIELD_BODY_TYPE_NAME'] = 'Тип кузова';
	$MESS[$strMessPrefix.'FIELD_BODY_TYPE_DESC'] = 'Тип кузова — одно из значений списка:<br/>
<ul>
	<li>Седан</li>
	<li>Хетчбэк</li>
	<li>Универсал</li>
	<li>Внедорожник</li>
	<li>Кабриолет</li>
	<li>Купе</li>
	<li>Лимузин</li>
	<li>Минивэн</li>
	<li>Пикап</li>
	<li>Фургон</li>
	<li>Микроавтобус</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_DOORS_NAME'] = 'Количество дверей';
	$MESS[$strMessPrefix.'FIELD_DOORS_DESC'] = 'Количество дверей — целое число.';
$MESS[$strMessPrefix.'FIELD_GENERATION_ID_NAME'] = 'Поколение';
	$MESS[$strMessPrefix.'FIELD_GENERATION_ID_DESC'] = 'Поколение — числовой идентификатор из <a href="http://autoload.avito.ru/format/Autocatalog.xml" target="_blank">Справочника</a>.<br/>
Обратите внимание, что для данного поля требуется указывать числовой идентификатор.';
$MESS[$strMessPrefix.'FIELD_MODIFICATION_ID_NAME'] = 'Модификация';
	$MESS[$strMessPrefix.'FIELD_MODIFICATION_ID_DESC'] = 'Модификация — числовой идентификатор из <a href="http://autoload.avito.ru/format/Autocatalog.xml" target="_blank">Справочника</a>.
Обратите внимание, что для данного поля требуется указывать числовой идентификатор';
$MESS[$strMessPrefix.'FIELD_COMPLECTATION_ID_NAME'] = 'Комплектация';
	$MESS[$strMessPrefix.'FIELD_COMPLECTATION_ID_DESC'] = 'Комплектация — числовой идентификатор из <a href="http://autoload.avito.ru/format/Autocatalog.xml" target="_blank">Справочника</a>.<br/><br/>
Если значение Комплектации установить не возможно, то необходимо передать пустое значение (см. вариант 2), в этом случае будет выбрано значение по умолчанию.<br/><br/>
Обратите внимание, что для данного поля требуется указывать числовой идентификатор';
$MESS[$strMessPrefix.'FIELD_COLOR_NAME'] = 'Цвет';
	$MESS[$strMessPrefix.'FIELD_COLOR_DESC'] = 'Цвет — одно из значений списка:<br/>
<ul>
	<li>Красный</li>
	<li>Коричневый</li>
	<li>Оранжевый</li>
	<li>Бежевый</li>
	<li>Жёлтый</li>
	<li>Зелёный</li>
	<li>Голубой</li>
	<li>Синий</li>
	<li>Фиолетовый</li>
	<li>Пурпурный</li>
	<li>Розовый</li>
	<li>Белый</li>
	<li>Серый</li>
	<li>Чёрный</li>
	<li>Золотой</li>
	<li>Серебряный</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_FUEL_TYPE_NAME'] = 'Тип двигателя';
	$MESS[$strMessPrefix.'FIELD_FUEL_TYPE_DESC'] = 'Тип двигателя — одно из значений списка:<br/>
<ul>
	<li>Бензин</li>
	<li>Дизель</li>
	<li>Гибрид</li>
	<li>Электро</li>
	<li>Газ</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ENGINE_SIZE_NAME'] = 'Объем двигателя, л';
	$MESS[$strMessPrefix.'FIELD_ENGINE_SIZE_DESC'] = 'Обязательно для всех двигателей кроме электрических: объем двигателя, л — десятичное число.';
$MESS[$strMessPrefix.'FIELD_POWER_NAME'] = 'Мощность двигателя, л.с.';
	$MESS[$strMessPrefix.'FIELD_POWER_DESC'] = 'Мощность двигателя, л.с. — целое число.';
$MESS[$strMessPrefix.'FIELD_TRANSMISSION_NAME'] = 'Коробка передач';
	$MESS[$strMessPrefix.'FIELD_TRANSMISSION_DESC'] = 'Коробка передач — одно из значений списка:<br/>
<ul>
	<li>Механика</li>
	<li>Автомат</li>
	<li>Робот</li>
	<li>Вариатор</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_DRIVE_TYPE_NAME'] = 'Привод';
	$MESS[$strMessPrefix.'FIELD_DRIVE_TYPE_DESC'] = 'Привод — одно из значений списка:<br/>
<ul>
	<li>Передний</li>
	<li>Задний</li>
	<li>Полный</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_WHEEL_TYPE_NAME'] = 'Руль';
	$MESS[$strMessPrefix.'FIELD_WHEEL_TYPE_DESC'] = 'Руль — одно из значений списка:<br/>
<ul>
	<li>Левый</li>
	<li>Правый</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OWNERS_NAME'] = 'Количество владельцев по ПТС';
	$MESS[$strMessPrefix.'FIELD_OWNERS_DESC'] = 'Только для автомобилей с пробегом: количество владельцев по ПТС — целое число.';
$MESS[$strMessPrefix.'FIELD_AD_TYPE_NAME'] = 'Вид объявления';
	$MESS[$strMessPrefix.'FIELD_AD_TYPE_DEFAULT'] = 'Автомобиль приобретён на продажу';
	$MESS[$strMessPrefix.'FIELD_AD_TYPE_DESC'] = 'Только для автомобилей с пробегом: вид объявления — при размещении объявлений через Автозагрузку всегда выставляется значение "'.$MESS[$strMessPrefix.'FIELD_AD_TYPE_DEFAULT'].'".';
#
$MESS[$strMessPrefix.'FIELD_POWER_STEERING_NAME'] = 'Усилитель руля';
	$MESS[$strMessPrefix.'FIELD_POWER_STEERING_DESC'] = 'Усилитель руля — одно из значений списка:<br/>
<ul>
	<li>Гидро-</li>
	<li>Электро-</li>
	<li>Электрогидро-</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CLIMATE_CONTROL_NAME'] = 'Управление климатом';
	$MESS[$strMessPrefix.'FIELD_CLIMATE_CONTROL_DESC'] = 'Управление климатом — одно из значений списка:<br/>
<ul>
	<li>Кондиционер</li>
	<li>Климат-контроль однозонный</li>
	<li>Климат-контроль многозонный</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CLIMATE_CONTROL_OPTIONS_NAME'] = 'Управление климатом (дополнительные опции)';
	$MESS[$strMessPrefix.'FIELD_CLIMATE_CONTROL_OPTIONS_DESC'] = 'Управление климатом (дополнительные опции) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Управление на руле</li>
	<li>Атермальное остекление</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_INTERIOR_NAME'] = 'Салон';
	$MESS[$strMessPrefix.'FIELD_INTERIOR_DESC'] = 'Салон — одно из значений списка:<br/>
<ul>
	<li>Кожа</li>
	<li>Ткань</li>
	<li>Велюр</li>
	<li>Комбинированный</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_INTERIOR_OPTIONS_NAME'] = 'Салон (дополнительные опции)';
	$MESS[$strMessPrefix.'FIELD_INTERIOR_OPTIONS_DESC'] = 'Салон (дополнительные опции) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Кожаный руль</li>
	<li>Люк</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_HEATING_NAME'] = 'Обогрев';
	$MESS[$strMessPrefix.'FIELD_HEATING_DESC'] = 'Обогрев — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Передних сидений</li>
	<li>Задних сидений</li>
	<li>Зеркал</li>
	<li>Заднего стекла</li>
	<li>Руля</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_POWER_WINDOWS_NAME'] = 'Электростеклоподъемники';
	$MESS[$strMessPrefix.'FIELD_POWER_WINDOWS_DESC'] = 'Электростеклоподъемники — одно из значений списка:
<ul>
	<li>Только передние</li>
	<li>Передние и задние</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ELECTRIC_DRIVE_NAME'] = 'Электропривод';
	$MESS[$strMessPrefix.'FIELD_ELECTRIC_DRIVE_DESC'] = 'Электропривод — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Передних сидений</li>
	<li>Задних сидений</li>
	<li>Зеркал</li>
	<li>Рулевой колонки</li>
	<li>Складывания зеркал</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_MEMORY_SETTINGS_NAME'] = 'Память настроек';
	$MESS[$strMessPrefix.'FIELD_MEMORY_SETTINGS_DESC'] = 'Память настроек — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Передних сидений</li>
	<li>Задних сидений</li>
	<li>Зеркал</li>
	<li>Рулевой колонки</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_DRIVING_ASSISTANCE_NAME'] = 'Помощь при вождении';
	$MESS[$strMessPrefix.'FIELD_DRIVING_ASSISTANCE_DESC'] = 'Помощь при вождении — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Автоматический парковщик</li>
	<li>Датчик дождя</li>
	<li>Датчик света</li>
	<li>Парктроник задний</li>
	<li>Парктроник передний</li>
	<li>Система контроля слепых зон</li>
	<li>Камера заднего вида</li>
	<li>Круиз-контроль</li>
	<li>Бортовой компьютер</li>
<ul>';
$MESS[$strMessPrefix.'FIELD_ANTITHEFT_SYSTEM_NAME'] = 'Противоугонная система';
	$MESS[$strMessPrefix.'FIELD_ANTITHEFT_SYSTEM_DESC'] = 'Противоугонная система — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Сигнализация</li>
	<li>Центральный замок</li>
	<li>Иммобилайзер</li>
	<li>Спутник</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_AIRBAGS_NAME'] = 'Подушки безопасности';
	$MESS[$strMessPrefix.'FIELD_AIRBAGS_DESC'] = 'Подушки безопасности — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Фронтальные</li>
	<li>Коленные</li>
	<li>Шторки</li>
	<li>Боковые передние</li>
	<li>Боковые задние</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ACTIVE_SAFETY_NAME'] = 'Активная безопасность';
	$MESS[$strMessPrefix.'FIELD_ACTIVE_SAFETY_DESC'] = 'Активная безопасность — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Антиблокировка тормозов</li>
	<li>Антипробуксовка</li>
	<li>Курсовая устойчивость</li>
	<li>Распред. тормозных усилий</li>
	<li>Экстренное торможение</li>
	<li>Блок. дифференциала</li>
	<li>Обнаружение пешеходов</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_MULTIMEDIA_NAME'] = 'Мультимедиа и навигация';
	$MESS[$strMessPrefix.'FIELD_MULTIMEDIA_DESC'] = 'Мультимедиа и навигация — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>CD/DVD/Blu-ray</li>
	<li>MP3</li>
	<li>Радио</li>
	<li>TV</li>
	<li>Видео</li>
	<li>Управление на руле</li>
	<li>USB</li>
	<li>AUX</li>
	<li>Bluetooth</li>
	<li>GPS-навигатор</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_AUDIO_SYSTEM_NAME'] = 'Аудиосистема';
	$MESS[$strMessPrefix.'FIELD_AUDIO_SYSTEM_DESC'] = 'Аудиосистема — одно из значений списка:<br/>
<ul>
	<li>2 колонки</li>
	<li>4 колонки</li>
	<li>6 колонок</li>
	<li>8+ колонок</li>
</ul>
';
$MESS[$strMessPrefix.'FIELD_AUDIO_SYSTEM_OPTIONS_NAME'] = 'Аудиосистема (дополнительные опции)';
	$MESS[$strMessPrefix.'FIELD_AUDIO_SYSTEM_OPTIONS_DESC'] = 'Аудиосистема (дополнительные опции) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Сабвуфер</li>.
</ul>';
$MESS[$strMessPrefix.'FIELD_LIGHTS_NAME'] = 'Фары';
	$MESS[$strMessPrefix.'FIELD_LIGHTS_DESC'] = 'Фары — одно из значений списка:<br/>
<ul>
<li>Галогенные</li>
<li>Ксеноновые</li>
<li>Светодиодные</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LIGHTS_OPTIONS_NAME'] = 'Фары (дополнительные опции)';
	$MESS[$strMessPrefix.'FIELD_LIGHTS_OPTIONS_DESC'] = 'Фары (дополнительные опции) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Противотуманные</li>
	<li>Омыватели фар</li>
	<li>Адаптивное освещение</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_WHEELS_NAME'] = 'Шины и диски (диаметр), дюймы';
	$MESS[$strMessPrefix.'FIELD_WHEELS_DESC'] = 'Шины и диски (диаметр), дюймы — целое число.';
$MESS[$strMessPrefix.'FIELD_WHEELS_OPTIONS_NAME'] = 'Шины и диски (дополнительные опции)';
	$MESS[$strMessPrefix.'FIELD_WHEELS_OPTIONS_DESC'] = 'Шины и диски (дополнительные опции) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Зимние шины в комплекте</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_MAINTENANCE_NAME'] = 'Данные о ТО';
	$MESS[$strMessPrefix.'FIELD_MAINTENANCE_DESC'] = 'Данные о ТО — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Есть сервисная книжка</li>
	<li>Обслуживался у дилера</li>
	<li>На гарантии</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_TRADEIN_DISCOUNT_NAME'] = 'Скидка при покупке в трейд-ин';
	$MESS[$strMessPrefix.'FIELD_TRADEIN_DISCOUNT_DESC'] = 'Скидка при покупке по программе трейд-ин - числовое значение в рублях.<br/><br/>
Только для новых автомобилей.';
$MESS[$strMessPrefix.'FIELD_CREDIT_DISCOUNT_NAME'] = 'Скидка при покупке в кредит';
	$MESS[$strMessPrefix.'FIELD_CREDIT_DISCOUNT_DESC'] = 'Скидка при покупке в кредит - числовое значение в рублях.<br/><br/>
Только для новых автомобилей.';
$MESS[$strMessPrefix.'FIELD_INSURANCE_DISCOUNT_NAME'] = 'Скидка при покупке в кредит';
	$MESS[$strMessPrefix.'FIELD_INSURANCE_DISCOUNT_DESC'] = 'Скидка при покупке со страховкой - числовое значение в рублях.<br/><br/>
Только для новых автомобилей.';
$MESS[$strMessPrefix.'FIELD_MAX_DISCOUNT_NAME'] = 'Максимальная суммарная скидка';
	$MESS[$strMessPrefix.'FIELD_MAX_DISCOUNT_DESC'] = 'Максимальная суммарная скидка на автомобиль, возможная при выполнении условий всех действующих в этот момент акций - числовое значение в рублях.<br/><br/>
Максимальная суммарная скидка не может быть больше суммы скидок.<br/><br/>
Только для новых автомобилей.';



# Change description for price
$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена в рублях — целое число.<br/>
Что бы назначить скидку для новых автомобилей используйте элементы:<br/>
<ul>
	<li>MaxDiscount</li>
	<li>TradeinDiscount</li>
	<li>CreditDiscount</li>
	<li>InsuranceDiscount</li>
</ul>
Цена со скидкой рассчитывается автоматически как разница между Price и MaxDiscount.<br/><br/>
<b>Внимание</b>, скидка не отобразится, если:<br>
<ul>
<li>Ввести только MaxDiscount — должен быть хотя бы один элемент TradeinDiscount, CreditDiscount или InsuranceDiscount,</li>
<li>Ввести MaxDiscount, который больше суммы TradeinDiscount, CreditDiscount и InsuranceDiscount.</li>
</ul>';

?>