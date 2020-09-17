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
			<category id="3">Билеты</category>
		</categories>

		<offers>
		
			<offer id="1234" type="event-ticket"  available="true" bid="80"> 
				<url>http://best.seller.ru/product_page.asp?pid=57384</url>
				<price>1000</price>
				<oldprice>1100</oldprice>
				<currencyId>RUR</currencyId>
				<categoryId>3</categoryId> 
				<picture>http://best.seller.ru/product_page.asp?pid=72945.jpg</picture>
				<store>false</store>
				<pickup>false</pickup>
				<delivery>true</delivery>
				<local_delivery_cost>300</local_delivery_cost>
				<name>Дмитрий Хворостовский и Национальный филармонический оркестр России...</name>
				<place>Московский  международный Дом музыки</place>
				<hall>Большой зал</hall>
				<hall_part>Партер р. 1-5<hall_part>
				<date>2012-02-25 12:03:14</date> 
				<is_premiere>0<is_premiere>
				<is_kids>0</is_kids>
				<description>
				Концерт Дмитрия Хворостовского и Национального филармонического оркестра России...
				</description>
				<age>6</age>
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