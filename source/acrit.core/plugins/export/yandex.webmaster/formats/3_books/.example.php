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
			<category id="3">Книги</category>
		</categories>

		<offers>
		
			<offer id="12342" type="book" available="true" bid="80">
				<url>http://best.seller.ru/product_page.asp?pid=14345</url>
				<price>80</price>
				<oldprice>90</oldprice>
				<currencyId>RUR</currencyId>
				<categoryId>3</categoryId>
				<picture>http://best.seller.ru/product_page.asp?pid=14345.jpg</picture>
				<store>false</store>
				<pickup>false</pickup>
				<delivery>true</delivery>
				<delivery-options>
					<option cost="200" days="1"/>
				</delivery-options>
				<author>Александра Маринина</author>
				<name>Все не так. В 2 томах. Том 1</name>
				<publisher>Эксмо</publisher>
				<series>А. Маринина — королева детектива</series>
				<year>2007</year>
				<ISBN>978-5-699-23647-3</ISBN>
				<volume>2</volume>
				<part>1</part>
				<language>rus</language>
				<binding>70x90/32</binding>
				<page_extent>288</page_extent>
				<description>
					Все прекрасно в большом патриархальном семействе Руденко...
				</description>
				<downloadable>false</downloadable>
				<age unit="year">18</age>
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