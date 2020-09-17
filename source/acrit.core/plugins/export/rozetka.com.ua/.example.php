<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="2011-07-20 14:58">
	<shop>
		<name>ABC</name>
		<company>ABC inc.</company>
		<url>http://www.abc.ua/</url>
		<currencies>
			<currency id="UAH" rate="1"/>
		</currencies>
		<categories>
			<category id="2">Женская одежда</category>
			<category id="261" parentId="2">Платья</category>
			<category id="3">Мужская одежда</category>
			<category id="391" parentId="3">Куртки</category>
		</categories>
		<offers>
			<offer id="19305" available="true">
				<url>http://abc.ua/catalog/muzhskaya_odezhda/kurtki/kurtkabx.html</url>
				<price>4499</price>
				<currencyId>UAH</currencyId>
				<categoryId>391</categoryId>
				<picture>http://abc.ua/upload/iblock/a53/a5391cddb40be91705.jpg</picture>
				<picture>http://abc.ua/upload/iblock/9d0/9d06805d219fb525fc.jpg</picture>
				<picture>http://abc.ua/upload/iblock/93d/93de38537e1cc1f8f2.jpg</picture>
				<vendor>Abc clothes</vendor>
				<stock_quantity>100</stock_quantity>
				<name>Куртка Abc clothes Scoperandom-HH XL Черная (1323280942900)</name>
				<description><![CDATA[<p>Одежда<b>Abc clothes</b> способствует развитию функций головного мозга за счет поощрения мелкой моторики.</p><p>В Abc <b>New Collection</b> будет особенно удобно лазать, прыгать, бегать.</p><p>За счет своей универсальноcти и многофункциональности, <b>Abc clothes</b> отлично подходит:</p><ul><li><b>Для весны</b></li><li><b>Для лета</b></li><li><b>Для ранней осени</b> </li></ul><br><p><b>Состав:</b><br>• 92% полиэстер, 8% эластан, нетоксичность подтверждена лабораторно.</p><p><b>Вес:</b> 305 г</p>]]></description>
				<param name="Вид">Куртка</param>
				<param name="Размер">XL</param>
				<param name="Сезон">Весна-Осень</param>
				<param name="Категория">Мужская</param>
				<param name="Цвет">Черный</param>
				<param name="Длина">Средней длины</param>
				<param name="Стиль">Повседневный (casual)</param>
				<param name="Особенности">Модель с капюшоном</param>
				<param name="Состав">92% полиэстер, 8% эластан</param>
				<param name="Артикул">58265468</param>
			</offer>
		</offers>
	</shop>
</yml_catalog>
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
