<?
use \Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="utf-8"?>
    <bn-feed generation-date="2012-09-13T11:00:02+04:00"><!-- Формат даты YYYY-MM-DDTHH:mm:ss+04:00 (ISO 8601 http://en.wikipedia.org/wiki/ISO_8601) -->
        <bn-object>
            <!-- Общая информация -->
            <id>123123</id><!-- ID объявления в вашей базе -->
            <type>квартира</type>
            <!--
                тип недвижимости:
                жилая
                    квартира
                    комната

                загородная
                    дом (или коттедж, 2 дома, 1/2 дома, 1/3 дома, 1/4 дома, 2/3 дома, 3/4 дома)
                    участок

                коммерческая
                    офисы
                    помещения в строящихся домах
                    помещение для сферы услуг
                    помещения различного назначения
                    отдельно стоящие здания
                    производственно-складские помещения
                    земельные участки
             -->
            <action>продажа</action>
            <!--
                операция сделки:
                    продажа
                    аренда
            -->
            <url></url>
            <!-- Ссылка на страницу с объявлением -->
            <!-- **************** -->

            <!-- Информация о расположении -->
            <location>
                <country>Россия</country><!-- Страна -->
                <region>Новгородская область</region><!--Субъекты РФ http://ru.wikipedia.org/wiki/Коды_субъектов_Российской_Федерации -->
                <area>Боровичский район</area><!--Административный район (ФИАС)-->
                <city>Боровичи</city><!--Городские округа/Посёлки/Деревни и иные населенные пункты (ФИАС) -->
                <ctar></ctar><!--Городские территории (ФИАС) -->
                <district></district><!--район города (не по ФИАС)-->
                <place></place><!--населенный пункт входящий в состав города (ФИАС) -->
                <street>Измайловский пр.</street><!-- улица (ФИАС) -->
                <house>16</house><!-- дом -->
                <address>Измайловский пр., 16</address><!-- или адрес целиком -->
                <metro>
                    <name>Сенная площадь</name>
                    <!-- Пешком или транспортом или остановок -->
                    <time-foot>10</time-foot><!-- минут пешком до метро -->
                    <time-transport>10</time-transport><!-- минут на транспорте до метро -->
                    <time-stop>2</time-stop><!-- остановок транспортом до метро -->
                </metro>
                <railway-station>
                    <name>115 км</name>
                    <!-- Пешком или транспортом или остановок -->
                    <time-foot>10</time-foot><!-- минут пешком до ЖД станции -->
                    <time-transport>10</time-transport><!-- минут на транспорте до ЖД станции -->
                </railway-station>
                <distance>20</distance><!-- для СПб/ЛО и Мск/МО - километров до КАД/МКАД соответственно -->
                <!--  координаты -->
                <latitude></latitude>
                <longitude></longitude>
            </location>
            <!-- ************************ -->

            <date>
                <create>2012-09-13T11:00:02+04:00</create><!-- Дата создания -->
                <update>2012-09-13T11:00:02+04:00</update><!-- Дата обновления -->
            </date>

            <!-- Информация о сделке -->
            <price>
                <value>4000000</value><!-- допустимо указать "договорная" -->
                <currency>RUR</currency>
                <!-- RUB -->
                <period>месяц</period><!-- ед. срока сдачи (для аренды) -->
                <!--
                    сутки
                    месяц
                    год
                -->
                <unit></unit><!-- если цена за единицу измерения -->
                <!--
                     м (метры квадратные)
                     гектар
                     cотка

                     или пусто, если плата за весь объект
                -->
            </price>
            <additional-terms>Ипотека</additional-terms>
            <additional-terms>Кредит</additional-terms>
            <!--
                дополнительные условия сделки:
                    Д – Доля
                    И – Ипотека
                    К – Кредит
                    Н – Рента
                    О – Обмен
                    П – Городская Программа
                    Р – Рассрочка
                    Т – Отягощение
                    Ц – Цессия
            -->
            <!-- ******************* -->

            <!-- Информация о продавце -->
            <agent>
                <name>Алексей Алексеич</name>
                <phone>8(821)812-1-821</phone><!-- Сколько телефонов, столько тегов phone -->
                <phone>8(821)812-2-821</phone>
                <category>агентство</category>
                <!--
                    агентство
                    частное
                -->
                <organization>Фирма</organization><!-- Название организации -->
                <url>http://www.fidm.spb.ru/</url><!-- Сайт  -->
                <email>office@firm.spb.ru</email><!-- Контактный email  -->
                <skype>frimspbru</skype><!-- Skype -->
            </agent>
            <!-- ********************* -->

            <!-- Графическая информация. фото и видео -->
            <files>
                <image>http://example.com/img1.jpg</image><!-- Прямой путь до изображения -->
                <image>http://example.com/img2.jpg</image>
                <image>http://example.com/img3.jpg</image>
                <video>http://example.com/img3.flv</video>
                <video>http://example.com/img3.mp4</video>
            </files>

            <description>
                <short></short><!-- 40 символов -->
                <print></print><!--до 160 символов-->
                <full></full><!--до 3000 символов-->
            </description>
            <!-- **************************** -->

            <!-- Информация о здании -->
            <building>
                <name></name><!-- название жилищного комплекса (для новостроек) -->
                <year>2014</year><!-- год сдачи -->
                <quarter>3</quarter><!-- квартал сдачи -->
                <status></status><!-- ход строительства -->
                <!--
                    дом сдан
                    госкомиссия
                -->
                <type></type>
                <!--
                    Тип дома:
                        Кирпич
                        Монолит
                        Остальные
                        Современная панель
                        Сталинские
                        Старая панель
                        Старый Фонд
                -->
                <series></series>
                <!--
                    Серии домов для СПб:
                        1.090.1 серия
                        100.11 серия
                        100.96 серия
                        121 серия
                        137 серия
                        504 серия
                        504Д серия
                        528 серия
                        600.11 серия
                        600 серия
                        601 серия
                        602 серия
                        606 серия
                        Б/М – блочно-монолитный
                        БЛ – блочный
                        БР – брежневский
                        ДЕР – деревянный
                        ИНД – индивидуальный
                        К/М – кирпично-монолитный
                        К – кирпичный
                        КР – корабль
                        КТЖ – коттедж
                        ЛО-90 серия
                        ЛО-91 серия
                        М/ПН – монолитно-панельный
                        М – монолит
                        МАН - мансарда
                        НБЛ – новый блочный
                        НЕМ – немецкий
                        ПН – панельный
                        РЕК – реконструкция
                        СТ – сталинский
                        СФ – старый фонд
                        СФК– старый фонд кап. ремонт
                        ТОЧ – точечный
                        ХР – хрущевский
                -->
            </building>
            <!-- ****************** -->

            <!-- Информация о жилом помещении -->
            <total><!--общая площадь-->
                <value>56</value>
                <unit>кв.м</unit>
            </total>
            <living><!--жилая площадь-->
                <value>40</value>
                <value-rooms>8+12+10+10</value-rooms><!-- количество слагаемых должно совпадать с комнатностью rooms-total -->
                <unit>кв.м</unit>
            </living>
            <kitchen><!--кухни площадь-->
                <value>16</value>
                <unit>кв.м</unit>
            </kitchen>
            <balcony><!-- Тип балкона -->
                <!--
                    балкон
                    лоджия
                    застекленный балкон
                    застекленная лоджия
                    2 балкона
                    2 лоджии
                -->
            </balcony>
            <new-building>1</new-building><!-- объект в новостройке-->
            <is-elite>1</is-elite><!-- элитный объект-->
*           <rooms-total></rooms-total><!--комнат (в квартире) всего-->
*           <rooms-offer></rooms-offer><!--количество комнат в сделке. для комнат-->
            <holders></holders><!--кол-во съёмщиков-->
            <neighbourhoods></neighbourhoods><!--кол-во соседей-->
            <phone>1</phone><!-- наличие телефона-->
            <internet>1</internet><!-- наличие интернета-->
            <floor>1</floor><!-- этаж на котором продаётся объект-->
            <floor-range>1-10</floor-range><!-- этажи для идентичных квартир по одному стояку-->
            <floors>20</floors><!-- всего этажей в доме-->
            <furniture>1</furniture><!-- мебель-->
            <refrigerator>1</refrigerator><!-- холодильник -->
            <bathroom>
            <!--
                Б/В – без ванны
                В/К – ванна на кухне
                Д/К – душ на кухне
                Д – душ
                Р – раздельный санузел
                С – совмещенный санузел
                2 – 2 санузла
                3 – 3 санузла
            -->
            </bathroom>
            <washing-machine>1</washing-machine>
            <quality>
            <!--
                хорошее
                требует ремонта
                удовлетворительное
                косметический ремонт
                отличное
                евроремонт
                без отделки
            -->
           </quality>
            <!-- **************************** -->

            <!-- Информация о загородном объекте -->
            <lot><!--площадь участка. для участков и домов с участками-->
                <value>56</value>
                <unit>соток</unit>
            </lot>
            <lot-status>
            <!--
                Статус участка:
                    ДНП – дачное некоммерческое партнерство;
                    ИЖС – индивидуальное жилищное строительство;
                    САД – садоводство;
                    СНТ – садоводческое некоммерческое товарищество;
                    ЛПХ – личное подсобное хозяйство;
                    ФЕР – фермерское хозяйство.
            -->
            </lot-status>
            <countryside-type></countryside-type>
            <!--

                БЛ – блочный;
                К – кирпичный;
                ЩИТ – щитовой;
                БР – бревенчатый, брус;
                БК – бревенчатый, обложенный кирпичом
            -->
            <!-- ************************* -->

            <!-- Информация о коммерческом объекте -->
            <ceiling-height></ceiling-height><!-- Высота потолков -->
            <entrance></entrance><!-- подъезд -->
            <!--
                ж/д
                авто
                ж/д, авто
            -->
            <entry>1</entry><!-- вход -->
            <!--
                вход со двора;
                вход с улицы;
                отдельный вход;
                отдельный вход со двора;
                отдельный вход с улицы;
                1 вход;
                2 входа;
                3 входа;
                4 входа.
            -->
            <!-- ************************* -->
            <parking>1</parking><!-- парковка -->
            <protection>1</protection><!-- охрана -->
            <heating>1</heating><!-- отопление -->
            <water>1</water><!-- водоснабжение -->
            <gas>0</gas><!-- наличие газа -->
            <sewerage>1</sewerage><!-- канализация -->
            <electricity>1</electricity><!-- электричество -->
            <!--
                Тип квартиры.
                Применяется только для типа недвижимости "квартира".
                Возможные значения:
                    studio - студия
                    free - свободная планировка
                    standart - обычная квартира
            -->
            <kv-type>studio</kv-type>
        </bn-object>
    </bn-feed>


<!-- Примеры расположения:

            1.
            <country>Россия</country>
            <region>Нижегородская область</region>
            <area></area>
            <city>Нижний Новгород</city>
            <district>Приокский район</district>
            <place>д. Ляхово</place>

            2.
            <country>Россия</country>
            <region>Санкт-Петербург</region>
            <area></area>
            <city></city>
            <district>Московский район</district>
            <place></place>
            <street>Московский проспект</street>
            <house>115</house>

            3.
            <country>Россия</country>
            <region>Санкт-Петербург</region>
            <area></area>
            <city></city>
            <district>Курортный район</district>
            <place>Зеленогорск</place>
            <street>Невская ул.</street>
            <house>4</house>

            4.
            <country>Россия</country>
            <region>Новгородская область</region>
            <area>Новгородский район</area>
            <city></city>
            <district></district>
            <place>Панковка</place>
            <street>Строительная ул</street>
            <house></house>

            5.
            <country>Россия</country>
            <region>Московская область</region>
            <area>Мытищинский район</area>
            <city>Мытищи</city>
            <ctar>Пироговский</city>
            <district></district>
            <place></place>
            <street></street>
            <house></house>

-->
<!--

    Обязательные поля.

    Общие:
        type - тип
        action - операция сделки
        location - расположение (максимально подробное)
            metro - метро для городской недвижимости
        price - цена
            period - ед. срока сдачи (для аренды)
            unit - если цена за единицу измерения
        agent - контактная информация об агенте
            phone - один контактный телефон

    Квартира:
        rooms-total - количество комнат в квартире
        total - общая площадь квартиры

    Квартира в новостройке:
        rooms-total - количество комнат в квартире
        total - общая площадь квартиры
        building-year - год сдачи
        building-quarter - квартал сдачи
        building-name - название жилищного комплекса (рекомендуется)

    Комната:
        rooms-total - количество комнат в квартире
        rooms-offer - количество комнат участвующих в сделке

    Продажа участков:
        lot-status - статус участка
        lot - площадь участка

    Продажа домов:
        total - общая площадь дома
        lot-status - статус участка
        lot - площадь участка

    Продажа отдельно стоящих домов:
        total - общая площадь дома

    Продажа производственно складских помещений/помещений различного назначения/помещение для сферы услуг/офиса/:
        total - общая площадь помещения

    Продажа помещений в строящихся домах:
        total - общая площадь помещения
        building-year - год сдачи
        building-quarter - квартал сдачи
        building-name - название жилищного комплекса (рекомедуется)


********************************************************************

Примеры по типам.


Продажа квартиры:

<bn-object>
    <id>123123</id>
    <type>квартира</type>
    <location>
        <region>Санкт-Перебург</region>
        <district>Московский район</district>
        <street>Московский проспект</street>
        <house>115</house>
        <metro>
            <name>Сенная площадь</name>
        </metro>
    </location>
    <price>
        <value>4000000</value>
        <currency>RUR</currency>
    </price>
    <agent>
        <name>Вася Пупкин</name>
        <phone>8(821)812-1-821</phone>
        <category>частное</category>
    </agent>
    <rooms-total>3</rooms-total>
    <total>
        <value>120</value>
        <unit>кв.м</unit>
    </total>
    <kv-type>studio</kv-type>
</bn-object>


Комнаты:

<bn-object>
    <id>123123</id>
    <type>комната</type>
    <location>
        <region>Санкт-Перебург</region>
        <district>Московский район</district>
        <street>Московский проспект</street>
        <house>115</house>
        <metro>
            <name>Сенная площадь</name>
        </metro>
    </location>
    <price>
        <value>3000000</value>
        <currency>RUR</currency>
    </price>
    <agent>
        <name>Вася Пупкин</name>
        <phone>8(821)812-1-821</phone>
        <category>частное</category>
    </agent>
    <rooms-total>3</rooms-total>
    <rooms-offer>1</rooms-offer>
</bn-object>

Продажа комнаты:

<bn-object>
    <id>123123</id>
    <type>комната</type>
    <location>
        <region>Санкт-Перебург</region>
        <district>Московский район</district>
        <street>Московский проспект</street>
        <house>115</house>
        <metro>
            <name>Сенная площадь</name>
        </metro>
    </location>
    <price>
        <value>3000000</value>
        <currency>RUR</currency>
    </price>
    <agent>
        <name>Вася Пупкин</name>
        <phone>8(821)812-1-821</phone>
        <category>частное</category>
    </agent>
    <rooms-total>3</rooms-total>
    <rooms-offer>1</rooms-offer>
</bn-object>

Продажа квартиры в строящемся доме:
<bn-object>
    <id>123123</id>
    <type>квартира</type>
    <location>
        <region>Санкт-Перебург</region>
        <district>Московский район</district>
        <street>Московский проспект</street>
        <house>115</house>
        <metro>
            <name>Сенная площадь</name>
        </metro>
    </location>
    <price>
        <value>договорная</value>
    </price>
    <building-year>2014</building-year>
    <building-quarter>3</building-quarter>
    <new-building>1</new-building>
    <agent>
        <name>Вася Пупкин</name>
        <phone>8(821)812-1-821</phone>
        <category>частное</category>
    </agent>
    <rooms-total>3</rooms-total>
    <total>
        <value>120</value>
        <unit>кв.м</unit>
    </total>
    <kv-type>free</kv-type>
</bn-object>


Сдам квартиру:

<bn-object>
    <id>123123</id>
    <type>квартира</type>
    <location>
        <region>Санкт-Перебург</region>
        <district>Московский район</district>
        <street>Московский проспект</street>
        <house>115</house>
        <metro>
            <name>Сенная площадь</name>
        </metro>
    </location>
    <price>
        <value>40000</value>
        <currency>RUR</currency>
        <period>месяц</period>
    </price>
    <agent>
        <name>Вася Пупкин</name>
        <phone>8(821)812-1-821</phone>
        <category>частное</category>
    </agent>
    <rooms-total>3</rooms-total>
    <total>
        <value>120</value>
        <unit>кв.м</unit>
    </total>
    <kv-type>standart</kv-type>
</bn-object>



-->

XML;
if(!Helper::isUtf()){
    $strExample = Helper::convertEncoding($strExample, 'UTF-8', 'CP1251');
}
?>
<div class="acrit-exp-plugin-example">
    <pre><code class="xml"><?=htmlspecialcharsbx($strExample);?></code></pre>
</div>
<script>
    $('.acrit-exp-plugin-example pre code.xml').each(function(i, block) {
        highlighElement(block);
    });
</script>