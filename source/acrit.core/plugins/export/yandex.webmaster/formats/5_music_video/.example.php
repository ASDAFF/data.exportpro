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
			<category id="3">Аудиокниги</category>
		</categories>

		<offers>
		
			<offer id="12342" type="audiobook" available="true" bid="80">
				<url>http://best.seller.ru/product_page.asp?pid=14345</url>
				<price>200</price>
				<oldprice>250</oldprice>
				<currencyId>RUR</currencyId>
				<categoryId>3</categoryId>
				<picture>http://best.seller.ru/product_page.asp?pid=14345.jpg</picture>
				<author>Владимир Кунин</author>
				<name>Иваnов и Rабинович, или Аj'гоу ту 'Хаjфа!</name>
				<publisher>1С-Паблишинг, Союз</publisher>
				<year>2008</year>
				<ISBN>978-5-9677-0757-5</ISBN>
				<language>ru</language>
				<performed_by>Николай Фоменко</performed_by>
				<performance_type>начитана </performance_type>
				<storage>CD</storage>
				<format>mp3</format>
				<description>
					Перу Владимира Кунина принадлежат десятки сценариев к кинофильмам, серия книг про КЫСЮ и многое, многое другое.
				</description>
				<downloadable>true</downloadable>
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