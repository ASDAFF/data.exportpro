<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">
	<title>Example - Online Store</title>
	<link rel="self" href="http://www.example.com"/>
	<updated>20011-07-11T12:00:00Z</updated> 
		
	<!-- Первый пример: показывает какие теги являются обязательными и рекомендуемыми (кроме товаров категории «Одежда»)  -->
	<entry>
		<!-- Эти теги всегда обязательны -->
		<g:id>TV_123456</g:id>
		<g:title>LG 22LB4510 - 22" LED TV - 1080p (FullHD)</g:title>
		<g:description>Attractively styled and boasting stunning picture quality, the LG 22LB4510 - 22&quot; LED TV - 1080p (FullHD) is an excellent television/monitor. The LG 22LB4510 - 22&quot; LED TV - 1080p (FullHD) sports a widescreen 1080p panel, perfect for watching movies in their original format, whilst also providing plenty of working space for your other applications.</g:description>
		<g:link>http://www.example.com/electronics/tv/22LB4510.html</g:link>
		<g:image_link>http://images.example.com/TV_123456.png</g:image_link>
		<g:condition>used</g:condition>
		<g:availability>in stock</g:availability>
		<g:price>159.00 USD</g:price>
		<g:shipping>
			<g:country>US</g:country>
			<g:service>Standard</g:service>
			<g:price>14.95 USD</g:price>
		</g:shipping>
			
		<!-- 2 из 3 этих тегов обязательны -->
		<g:gtin>71919219405200</g:gtin>
		<g:brand>LG</g:brand>
		<g:mpn>22LB4510/US</g:mpn>
			
		<!-- Эти теги не обязательны, но рекомендуется их указывать -->
		<g:google_product_category>Electronics > Video > Televisions > Flat Panel Televisions</g:google_product_category>
		<g:product_type>Consumer Electronics &gt; TVs &gt; Flat Panel TVs</g:product_type>
	</entry>
		
	<!-- Второй пример: показывает использование CDATA вместо обычных значений для использования спецсимволов -->
	<entry>
		<!-- Эти теги всегда обязательны -->
		<g:id>DVD-0564738</g:id>
		<g:title><![CDATA[Merlin: Series 3 - Volume 2 - 3 DVD Box set]]></g:title>
		<g:description><![CDATA[Episodes 7-13 from the third series of the BBC fantasy drama set in the mythical city of Camelot, telling the tale of the relationship between the young King Arthur (Bradley James) & Merlin (Colin Morgan), the wise sorcerer who guides him to power and beyond. Episodes are: 'The Castle of Fyrien', 'The Eye of the Phoenix', 'Love in the Time of Dragons', 'Queen of Hearts', 'The Sorcerer's Shadow', 'The Coming of Arthur: Part 1' & 'The Coming of Arthur: Part 2']]></g:description>
		<g:link><![CDATA[http://www.example.com/media/dvd/?sku=384616&src=gshopping&lang=en]]></g:link>
		<g:image_link><![CDATA[http://images.example.com/DVD-0564738?size=large&format=PNG]]></g:image_link>
		<g:condition>new</g:condition>
		<g:availability>in stock</g:availability>
		<g:price>11.99 USD</g:price>
		<g:shipping>
			<g:country>US</g:country>
			<g:service>Express Mail</g:service>
			<g:price>3.80 USD</g:price>
		</g:shipping>
			
		<!-- 2 из 3 этих тегов обязательны -->
		<g:gtin>88392916560500</g:gtin>
		<g:brand>BBC</g:brand>
			
		<!-- Эти теги обязательны, т.к. это товар категории «Медиа» -->
		<g:google_product_category><![CDATA[Media > DVDs & Videos]]></g:google_product_category>
			
		<!-- Эти теги не обязательны для данного товара, но рекомендуется их указывать -->
		<g:product_type><![CDATA[DVDs & Movies > TV Series > Fantasy Drama]]></g:product_type>
	</entry>
		
	<!-- Третий пример: показывает как указывать множественные картинки и типы доставки -->
	<entry>
		<!-- Эти теги всегда обязательны -->
		<g:id>PFM654321</g:id>
		<g:title>Dior Capture XP Ultimate Wrinkle Correction Creme 1.7 oz</g:title>
		<g:description>Dior Capture XP Ultimate Wrinkle Correction Creme 1.7 oz reinvents anti-wrinkle care by protecting and relaunching skin cell activity to encourage faster, healthier regeneration.</g:description>
		<g:link>http://www.example.com/perfumes/product?Dior%20Capture%20R6080%20XP</g:link>
		<g:image_link>http://images.example.com/PFM654321_1.jpg</g:image_link>
		<g:condition>new</g:condition>
		<g:availability>in stock</g:availability>
		<g:price>99 USD</g:price>
		<g:shipping>
			<g:country>US</g:country>
			<g:service>Standard Rate</g:service>
			<g:price>4.95 USD</g:price>
		</g:shipping>
		<g:shipping>
			<g:country>US</g:country>
			<g:service>Next Day</g:service>
			<g:price>8.50 USD</g:price>
		</g:shipping>
			
		<!-- 2 из 3 уникальных идентификатора товаров обязательны для данного товара  -->
		<g:gtin>3348901056069</g:gtin>
		<g:brand>Dior</g:brand>
			
		<!-- Эти теги не обязательны для данного товара, но рекомендуется их указывать -->
		<g:product_type>Health &amp; Beauty &gt; Personal Care &gt; Cosmetics &gt; Skin Care &gt; Lotion</g:product_type>
		<g:google_product_category>Health &amp; Beauty &gt; Personal Care &gt; Cosmetics &gt; Skin Care &gt; Anti-Aging Skin Care Kits</g:google_product_category>
		<g:additional_image_link>http://images.example.com/PFM654321_2.jpg</g:additional_image_link>
		<g:additional_image_link>http://images.example.com/PFM654321_3.jpg</g:additional_image_link>
	</entry>
		
	<!-- Четвертый пример: показывает какие теги являются обязательными и рекомендуемыми для товаров категории «Одежда» -->
	<entry>
		<!-- Эти теги всегда обязательны -->
		<g:id>CLO-29473856-1</g:id>
		<g:title>Roma Cotton Rich Bootcut Jeans - Size 8 Standard</g:title>
		<g:description>A smart pair of bootcut jeans in stretch cotton.</g:description>
		<g:link>http://www.example.com/clothing/women/Roma-Cotton-Bootcut-Jeans/?extid=CLO-29473856</g:link>
		<g:image_link>http://images.example.com/CLO-29473856-front.jpg</g:image_link>
		<g:condition>new</g:condition>
		<g:availability>out of stock</g:availability>	
		<g:price>29.50 USD</g:price>
					
		<!-- Эти теги обязательны, т.к. это товар категории «Одежда» -->
		<g:google_product_category>Apparel &amp; Accessories &gt; Clothing &gt; Pants &gt; Jeans</g:google_product_category>
		<g:brand>M&amp;S</g:brand>
		<g:gender>Female</g:gender>
		<g:age_group>Adult</g:age_group>
		<g:color>Navy</g:color>
		<g:size>8 Standard</g:size>
			
		<!-- Эти теги обязательны, т.к. этот товар имеет варианты -->
		<g:item_group_id>CLO-29473856</g:item_group_id>
			
		<!-- Эти теги не обязательны для данного товара, но рекомендуется их указывать -->
		<g:mpn>B003J5F5EY</g:mpn>
		<g:product_type>Women's Clothing &gt; Jeans &gt; Bootcut Jeans</g:product_type>
		<g:additional_image_link>http://images.example.com/CLO-29473856-side.jpg</g:additional_image_link>
		<g:additional_image_link>http://images.example.com/CLO-29473856-back.jpg</g:additional_image_link>
	</entry>
	
	<!-- Это вариант последнего примера. В этом случае товары различаются только по размеру, но товары могут повторяться в различных вариантах. -->
	<entry>
		<!-- Эти теги всегда обязательны -->
		<g:id>CLO-29473856-2</g:id>
		<g:title>Roma Cotton Rich Bootcut Jeans - Size 8 Tall</g:title>
		<g:description>A smart pair of bootcut jeans in stretch cotton.</g:description>
		<g:link>http://www.example.com/clothing/women/Roma-Cotton-Bootcut-Jeans/?extid=CLO-29473856</g:link>
		<g:image_link>http://images.example.com/CLO-29473856-front.jpg</g:image_link>
		<g:condition>new</g:condition>
		<g:availability>in stock</g:availability>
		<g:price>29.50 USD</g:price>
						
		<!-- Эти теги обязательны, т.к. это товар категории «Одежда» -->
		<g:google_product_category>Apparel &amp; Accessories &gt; Clothing &gt; Pants &gt; Jeans</g:google_product_category>
		<g:brand>M&amp;S</g:brand>
		<g:gender>Female</g:gender>
		<g:age_group>Adult</g:age_group>
		<g:color>Navy</g:color>
		<g:size>8 Tall</g:size>
			
		<!-- Эти теги обязательны, т.к. этот товар имеет варианты -->
		<g:item_group_id>CLO-29473856</g:item_group_id>
			
		<!-- Эти теги не обязательны для данного товара, но рекомендуется их указывать -->
		<g:mpn>B003J5F5EY</g:mpn>
		<g:product_type>Women's Clothing &gt; Jeans &gt; Bootcut Jeans</g:product_type>
		<g:additional_image_link>http://images.example.com/CLO-29473856-side.jpg</g:additional_image_link>
		<g:additional_image_link>http://images.example.com/CLO-29473856-back.jpg</g:additional_image_link>
	</entry>
		
	<!-- Пятый пример: показывает использование цен  -->
	<entry>
		<!-- Эти теги всегда обязательны -->
		<g:id>CLO-1029384</g:id>
		<g:title>Tenn Cool Flow Ladies Long Sleeved Cycle Jersey</g:title>
		<g:description>A ladies' cycling jersey designed for the serious cyclist, tailored to fit a feminine frame. This sporty, vibrant red, black and white jersey is constructed of a special polyester weave that is extremely effective at drawing moisture away from your body, helping to keep you dry.  With an elasticised, gripping waist, it will stay in place for the duration of your cycle, and won't creep up like many other products. It has two elasticised rear pockets and the sleeves are elasticated to prevent creep-up.</g:description>
		<g:link>http://www.example.com/clothing/sports/product?id=CLO1029384&amp;src=gshopping&amp;popup=false</g:link>
		<g:image_link>http://images.example.com/CLO-1029384.jpg</g:image_link>
		<g:condition>new</g:condition>
		<g:availability>in stock</g:availability>
		<g:price>33.99 USD</g:price>
		<g:shipping>
			<g:country>US</g:country>
			<g:service>Standard Free Shipping</g:service>
			<g:price>0 USD</g:price>
		</g:shipping>
			
		<!-- Эти теги обязательны, т.к. это товар категории «Одежда» -->
		<g:brand>Tenn Cool</g:brand>
		<g:google_product_category>Apparel &amp; Accessories &gt; Clothing &gt; Activewear &gt; Bicycle Activewear &gt; Bicycle Jerseys</g:google_product_category>
		<g:gender>Female</g:gender>
		<g:age_group>Adult</g:age_group>
		<g:color>Black/Red/White</g:color> <!-- Указание используемых цветов одежды в порядке преобладания -->
		<g:size>M</g:size>
			
		<!-- Демонстрация использования цен -->
		<g:sale_price>25.49 USD</g:sale_price>
		<g:sale_price_effective_date>2011-09-01T16:00-08:00/2011-09-03T16:00-08:00</g:sale_price_effective_date>
			
		<!-- Эти теги не обязательны для данного товара, но рекомендуется их указывать -->
		<g:gtin>5060155240282</g:gtin>			
	</entry>
</feed>
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
