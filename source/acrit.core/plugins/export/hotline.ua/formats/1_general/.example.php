<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8" ?>
<price>
    <date>2018-09-10 17:00</date>
    <firmName>Shop.ua</firmName>
    <firmId>31234</firmId>
    <rate>28.2</rate>
    <delivery id="1" type="warehouse" cost="45" freeFrom="500" time="1" carrier="NP" region="01*-94*" />
    <categories>
        <category>
            <id>1</id>
            <name>Компьютеры, сети</name>
        </category>
        <category>
            <id>2</id>
            <parentId>1</parentId>
            <name>Планшеты</name>
        </category>
        <category>
            <id>3</id>
            <name>Детские товары</name>
        </category>
        <category>
            <id>4</id>
            <parentId>3</parentId>
            <name>Детские игрушки</name>
        </category>
        <category>
            <id>5</id>
            <parentId>4</parentId>
            <name>Конструкторы</name>
        </category>
    </categories>
    <items>
        <item>
            <id>3278</id>
            <categoryId>2</categoryId>
            <code>MRJP2</code>
            <barcode>0190198233301</barcode>
            <vendor>Apple</vendor>
            <name>iPad 2018 128GB Wi-Fi Gold</name>
            <description>Отлично подходит для работы с видео 4K, игр со сложной графикой и новейших приложений с дополненной реальностью.</description>
            <url>http://shop.ua/1/2/123.html</url>
            <image>http://shop.ua/img/1/2/123.jpg</image>
            <priceRUAH>15400</priceRUAH>
            <oldprice>16900</oldprice>
            <priceRUSD>546</priceRUSD>
            <stock days="10">Под заказ</stock>
            <delivery id="1" cost="70" freeFrom="5000" time="3" />
            <guarantee type="manufacturer">12</guarantee>
            <param name="Страна изготовления">Китай</param>
            <param name="Оригинальность">Оригинал</param>
            <condition>0</condition>
            <custom>1</custom>
        </item>
        <item>
            <id>5541</id>
            <categoryId>5</categoryId>
            <code>60190</code>
            <barcode>5702016108781</barcode>
            <vendor>LEGO</vendor>
            <name>Конструктор City Arctic Expedition Аэросани</name>
            <description>Возрастная группа - от 5 лет, количество деталей - 50 шт</description>
            <url>http://shop.ua/1/3/553.html</url>
            <image>http://shop.ua/img/1/3/gg9923.jpg</image>
            <priceRUAH>199</priceRUAH>
            <stock>В наличии</stock>
            <guarantee unit="days" type="shop">14</guarantee>
            <param name="Страна изготовления">Дания</param>
            <param name="Оригинальность">Оригинал</param>
            <condition>0</condition>
            <custom>1</custom>
        </item>
    </items>
</price> 
XML;
if (!Helper::isUtf())
{
	$strExample = Helper::convertEncoding($strExample, 'UTF-8', 'CP1251');
}
?>
<div class="acrit-exp-plugin-example">
	<pre><code class="xml"><?= htmlspecialcharsbx($strExample); ?></code></pre>
</div>
<script>
	$('.acrit-exp-plugin-example pre code.xml').each(function (i, block) {
		highlighElement(block);
	});
</script>
