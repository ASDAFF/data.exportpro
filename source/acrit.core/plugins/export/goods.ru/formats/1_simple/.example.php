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
			<category id="1">Бытовая техника</category>
			<category id="10" parentId="1">Мелкая техника для кухни</category>
			<category id="101" parentId="10">Сэндвичницы и приборы для выпечки</category>
			<category id="102" parentId="10">Мороженицы</category>
			<category id="2">Детские товары</category>
			<category id="20" parentId="2">Детский спорт</category>
			<category id="200" parentId="20">Игровые и спортивные комплексы, горки</category>
		</categories>

		<offers>
		
			<offer id="158" available="true">                
				<url>​http://www.abc.ru/158.html​</url>                
				<name>Холодильник Indesit SB 185</name>                
				<price>18500</price>                
				<categoryId>1293</categoryId>                
				<picture>​http://www.abc.ru/1580.jpg​</picture>
				​<vat>2</vat>                
				<shipment-options>                    
					<option days="1" order-before="15"/>                
				</shipment-options>    
				<outlets>                    
					<outlet id="1" instock="50"/>                
				</outlets>                
				<vendor>Indesit</vendor>                
				<vendorCode>12345678</vendorCode>                
				<model>Indesit SB 185</model>                
				<description>Холодильник Indesit SB 185</description>    
				<param name="Weight">120</param>    
				<param name="Width">70</param>    
				<param name="Length">250</param>    
				<param name="Height">180</param>    
				<param name="Габариты">10,5 x 6,5 x 1</param>    
				<param name="Бренд">Chatte</param>    
				<param name="Материал">Натуральная кожа</param>    
				<param name="Страна изготовитель">Италия</param>                
				<barcode>7564756475648</barcode>            
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