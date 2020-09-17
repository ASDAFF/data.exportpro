<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<Ads target="Avito.ru" formatVersion="3">
    <Ad>
        <Id>436453624543</Id>
        <Title>Отличный гидроцикл Yamaha VX</Title>
        <Category>Водный транспорт</Category>
        <Description>Новый гидроцикл Yamaha VX700S 2017</Description>
        <VehicleType>Гидроциклы</VehicleType>
        <Region>Москва</Region>
        <Price>500000</Price>
        <ManagerName>Менеджер по продажам</ManagerName>
        <AllowEmail>Да</AllowEmail>
        <Images>
            <Image url="http://www.jest-yamaha.ru/cms-images/yamaha_gallery/2675image_normal_2015-Yamaha-VX700S-RU-Green-Static-001.jpg"/>
        </Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
        <AdStatus>Free</AdStatus>
    </Ad>
</Ads>
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
