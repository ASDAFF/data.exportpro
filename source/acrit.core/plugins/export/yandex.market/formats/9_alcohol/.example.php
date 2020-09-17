<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<yml_catalog date="2017-02-05 17:22">
	<shop>
	 <name>BestSeller</name>
	 <company>Tne Best inc.</company>
	 <url>http://best.seller.ru</url>
	 <currencies>
		 <currency id="RUR" rate="1"/>
		 <currency id="USD" rate="60"/>
	 </currencies>
	 <categories>
		 <category id="1">Алкоголь</category>
		 <category id="2" parentId="1">Шампанское и игристые вины</category>
	 </categories>
	 <offers>
		 <offer id="345925" type="alco" bid="80">
			<name>Игристое вино брют розовое Santa Margherita Rose Италия, 0.75 л</name>
			<vendor>Santa Margherita</vendor>
			<url>http://shop.seller.ru/products/342995</url>
			<price>1750</price>
			<oldprice>1600</price>
			<currencyId>RUR</currencyId>
			<categoryId>3</categoryId>
			<picture>http://media.seller.ru/shop/342995_large.jpg</picture>
			<description>Прекрасный аперитив. Отлично сочетается с морепродуктами и белым 
	мясом птицы.</description>
			<country_of_origin>Италия</country_of_origin>
			<pickup>true</pickup>
			<param name="Объем" unit="л">0.75</param>
			<param name="содержание сахара">брют</param>
			<param name="год урожая">2016</param>
			<param name="крепость">12.5%</param>
			<param name="цвет">розовое</param>
			<barcode>4607071772810</barcode>
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
