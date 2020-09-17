<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0"?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
	<channel>
		<title>Test Store</title>
		<link>http://www.example.com</link>
		<description>An example item from the feed</description>
		<item>
			<g:id>DB_1</g:id>
			<g:title>Dog Bowl In Blue</g:title>
			<g:description>Solid plastic Dog Bowl in marine blue color</g:description>
			<g:link>http://www.example.com/bowls/db-1.html</g:link>
			<g:image_link>http://images.example.com/DB_1.png</g:image_link>
			<g:brand>Example</g:brand>
			<g:condition>new</g:condition>
			<g:availability>in stock</g:availability>
			<g:price>9.99 GBP</g:price>
			<g:shipping>
				<g:country>UK</g:country>
				<g:service>Standard</g:service>
				<g:price>4.95 GBP</g:price>
			</g:shipping>
			<g:google_product_category>Animals &gt; Pet Supplies</g:google_product_category>
			<g:custom_label_0>Made in Waterford, IE</g:custom_label_0>
		</item>
	</channel>
</rss>
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
