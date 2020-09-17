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
			<category id="4062">Лекарства</category>
		</categories>

		<offers>
		
			<offer id="12345" available="true" type="medicine" bid="80" cbid="90">
				<currencyId>RUB</currencyId>
				<categoryId>4062</categoryId>
				<name>БАД Селен-актив n30 таблетки</name>
				<vendor>ОАО ДИОД Завод эко.тех.и питания</vendor>
				<vendorCode>123456</vendorCode>
				<url>http://www.example-apteka.ru/selen-aktiv.html</url>
				<picture>http://www.example-apteka.ru/selen-aktiv.jpg</picture>
				<price>1000</price>
				<delivery>false</delivery>
				<pickup>true</pickup>
				<store>true</store>
				<barcode>4981046350037</barcode>
				<sales_notes>Самовывоз возможен через 3 часа после заказа</sales_notes>
				<description>Биоусвояемый селен 50 мкг, витамин С 50 мг. Селен-актив обеспечивает оптимальную и постоянную антиоксидантную защиту.</description>
				<country_of_origin>Россия</country_of_origin>
				<expiry>P1Y2M10DT2H30M</expiry>
				<param name="Побочные действия">нет</param>
				<param name="Код egk">123456</param>
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