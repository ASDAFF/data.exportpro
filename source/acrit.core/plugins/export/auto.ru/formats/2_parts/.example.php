<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<parts>
    <part>
        <id>12345</id>
        <title>Б/у шины (резина) Michelin Pilot Alpin PA4 "255/40R20 101V" (Зимняя)</title>
        <stores> 
            <store>26768943</store>
        </stores>
        <part_number>463984</part_number>
        <manufacturer>Michelin</manufacturer>
        <description>Резина новая! Уточняйте наличие и сроки доставки!</description>
        <is_new>True</is_new>
        <price>25000</price>
        <availability>
            <isAvailable>True</isAvailable>
        </availability>
        <images>
            <image>http://site.ru/images/photo1.jpg</image>
        </images>
        <properties>
            <property name="width">255</property>
            <property name="height">40</property>
            <property name="diameter">20</property>
            <property name="load_index">101</property>
            <property name="speed_index">V</property>
        </properties>
    </part>
    <part>
        <id>12346</id>
        <title>Ручка в салоне Контрактная</title>
        <part_number>92041-AJ000-ME</part_number>
        <description>Отличная ручка!</description>
        <is_new>False</is_new>
        <price>500</price>
        <availability><isAvailable>True</isAvailable></availability>
        <properties>
            <property name="Расположение перед/зад">Перед.</property>
            <property name="Расположение лево/право">Прав.</property>
        </properties>
        <images>
            <image>http://site.ru/images/photo2.jpg</image>
        </images>
        <compatibility>
            <car>SUBARU OUTBACK BRD,BR9,BRF,BRG,BRM 2014</car>
            <car>SUBARU LEGACY BRD,BR9,BRF,BRG,BRM 2014</car>
        </compatibility>
    </part>
    <part>
        <id>12347</id>
        <title>Диск литой СКАД Le-Mans 7x16/5*100 D57.1 ET46 Селен</title>
        <store>4546654</store>
        <part_number>316300310101521</part_number>
        <manufacturer>УАЗ</manufacturer>
        <description>Оригинальные диски разработаны специально для каждой модели УАЗ.</description>
        <is_new>True</is_new>
        <price>8000</price>
        <availability>
            <isAvailable>True</isAvailable>
            <daysfrom>1</daysfrom>
            <daysto>3</daysto>
        </availability>
        <properties>
        <property name="Ширина">7</property>
        <property name="Диаметр">16</property>
        <property name="Вылет">46</property>
        <property name="Ступица">57.1</property>
        <property name="Диаметр расположения крепежных отверстий">100</property>
        <property name="Количество крепежных отверстий">5</property>
        </properties>
        <images>
            <image>http://site.ru/images/photo3.jpg</image>
        </images>
        <compatibility>
            <car>УАЗ Патриот 2017-н.в.</car>
            <car>УАЗ Патриот 2017-н.в.</car>
        </compatibility>
        </part>
        <part>
            <id>12348</id>
            <title>блок предохранителей</title>
            <part_number>82201</part_number>
            <description>Блок!</description>
            <is_new>False</is_new>
            <price>1500</price>
            <availability><isAvailable>True</isAvailable></availability>
            <properties>
                <property name="Цвет">Серебро</property>
            </properties>
            <images>
                <image>http://site.ru/image4.jpg</image>
            </images>
            <compatibility>
                <car>SUBARU FORESTER SJ5, SJG 2014</car>
            </compatibility>
        </part>
</parts>
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
