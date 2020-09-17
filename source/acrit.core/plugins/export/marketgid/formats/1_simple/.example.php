<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML

<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<marketgid_teaser_goods_export date="2013-10-17 14:07" xmlns="http://www.w3schools.com">
    <categories>
        <category id="12">Одежда</category>
    </categories>
    <teasers>
        <teaser id="AE-124563" active="true">
            <categoryId>12</categoryId>
            <url>http://shopsiteexample.com/product-AE-124563.html</url>
            <picture>http://shopsiteexample.com/picture-AE-124563/main.jpg</picture>
            <title>Джинсы мужские Levis</title>
            <text>Джинсы мужские Levis, по самой низкой цене! Только 5 дней!</text>
            <price currency="UAH">259</price>
        </teaser>
    </teasers>
</marketgid_teaser_goods_export>

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
