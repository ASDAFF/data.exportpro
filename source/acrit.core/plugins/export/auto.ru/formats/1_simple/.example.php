<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="utf-8"?>
<data>
    <cars>
        <car>
            <mark_id>Ford</mark_id>
            <folder_id>Fusion</folder_id>
            <modification_id>1.4d AT (68 л.с.)</modification_id>
            <body_type>Хэтчбек 5 дв.</body_type>
            <wheel>левый</wheel>
            <color>красный</color>
            <metallic>нет</metallic>
            <availability>в наличии</availability>
            <custom>растаможен</custom>
            <year>2015</year>
            <price>575000</price>
            <currency>RUR</currency>
            <vin>XWBCA41ZXDK259205</vin>
            <description>Машина в идеальном состоянии, растоможена,ездил на своих,американских номерах.Сделано полное ТО.Ни одной проблемы и проблемки.</description>
            <extras>Антиблокировочная система (ABS), Тонированные стекла, Люк на крыше, Круиз-контроль, Ксеноновые фары</extras>
            <images>example1.jpg, example2.jpg, example3.jpg, example4.jpg, example5.jpg</images>
            <unique_id>a970d571ef3f5478cccf6d2878a4d700</unique_id>
            <sale_services>color,special,toplist</sale_services>
            <poi_id>Москва, Тверская, 4</poi_id>
            <contact_info>
                <contact>
                    <name>Иван</name>
                    <phone>89031234567</phone>
                    <time>08:00-18:00</time>
                </contact>
            </contact_info>
            <armored>Нет</armored>
            <color-code>KNM</color-code>
            <interior-code>HARM03</interior-code>
            <modification-code>A2S6D1617D216</modification-code>
            <equipment-code>RA4, WSA, PP4</equipment-code>
        </car>
</cars>
</data>
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
