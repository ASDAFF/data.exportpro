<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<priceru_feed date="2016-08-31 18:35">
<shop>
	<company>ООО "Магазин съёмочной техники"</company>
	<url>http://shop.ru/</url>
	<currencies>
		<currency id="RUB" rate="1"/>
		<currency id="USD" rate="61"/>
		<currency id="EUR" rate="71"/>
	</currencies>
	<categories>
		<category id="1" parentId="0">Съемочная техника</category>
		<category id="2" parentId="1">Видеокамеры</category>
		<category id="3" parentId="1">Цифровые фотоаппараты</category>
		<category id="4" parentId="1">Объективы</category>
	</categories>
	<offers>
		<offer id="31415926535" available="true" bid="500">
			<name>Фотоаппарат Canon EOS 600D</name>
			<description>
				Матрица 18.7 МП (APS-C) - съемка видео Full HD - поворотный экран 3 - вес камеры 570
			</description>
			<url>http://shop.ru/offers/canon-eos-600d</url>
			<picture>http://shop.ru/pictures/canon-eos-600d.jpg</picture>
			<price>16500</price>
			<oldprice>18500</oldprice>
			<currencyId>RUB</currencyId>
			<categoryId>3</categoryId>
			<typePrefix>Фотоаппарат</typePrefix>
			<vendor>Canon</vendor>
			<model>EOS 600D</model>
			<vendorCode>5170B011</vendorCode>
			<local_delivery_cost>300</local_delivery_cost>
			<barcode>4960999780948</barcode>
			<param name="Масса" unit="г">570</param>
			<param name="Цвет">чёрный</param>
			<param name="Матрица" unit="МП">18.7</param>
		</offer>
	</offers>
</shop>
</priceru_feed>
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