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
			<category id="6">Туры</category>
		</categories>

		<offers>
		
			<offer id="12341" type="tour" available="true" bid="80">
				<url>http://best.seller.ru/product_page.asp?pid=12344</url>
				<price>24129</price>
				<oldprice>25000</oldprice>
				<currencyId>USD</currencyId>
				<categoryId>6</categoryId>
				<picture>http://best.seller.ru/img/device12345.jpg</picture>
				<store>false</store>
				<pickup>true</pickup>
				<delivery>false</delivery>
				<local_delivery_cost>300</local_delivery_cost>
				<worldRegion>Африка</worldRegion>
				<country>Египет</country>
				<region>Хургада</region>
				<days>7</days>
				<dataTour>2012-01-01 12:00:00</dataTour>
				<dataTour>2012-01-08 12:00:00</dataTour>
				<name>Hilton</name>
				<hotel_stars>5*****</hotel_stars>
				<room>SNG</room>
				<meal>ALL</meal>
				<included>авиаперелет, трансфер, проживание, питание, страховка</included>
				<transport>Авиа</transport>
				<description>Отдых в Египте.</description>
				<price_min>24000</price_min>
				<price_max>25000</price_max>
				<options>?</options>
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