<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<Ads target="Avito.ru" formatVersion="3">
    <Ad>
        <Id>234132234143</Id>
		<Title>Yamaha Z1000</Title>
        <Category>Мотоциклы и мототехника</Category>
        <VehicleType>Мотоциклы</VehicleType>
        <MotoType>Дорожные</MotoType>
        <Description><![CDATA[Мотоцикл куплен у официального дилера. 
        Оригинал ПТС.

        Дополнительное оборудование: 
        - Прямоточная выхлопная система FMF. 
        - Центральный кофр. 
        - Бачки тормозной жидкости.]]></Description>
        <Region>Москва</Region>
        <Price>399000</Price>
        <ManagerName>Менеджер по продажам</ManagerName>
        <AllowEmail>Да</AllowEmail>
        <Images>
            <Image url="https://popmotor.ru/wp-content/uploads/2013/10/2015-Kawasaki-Z1000-ABS4.jpg"/>
            <Image url="http://onlymotorbikes.com/public/81/kawasaki-z-1000-2011-moto.jpeg"/>
        </Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
        <AdStatus>Free</AdStatus>
				<Latitude>51.537047</Latitude>
				<Longitude>46.056939</Longitude>
    </Ad>
    <Ad>
        <Id>45623463456</Id>
		<Title>Buran Leader</Title>
        <Category>Мотоциклы и мототехника</Category>
		<VehicleType>Снегоходы</VehicleType>
        <Description>Продается снегоход, 2007г., 34л.с. , в хорошем рабочем состоянии, трехрядная цепь, датчик температуры, замена подшипников ходовой в прошлом сезоне, подогрев ручек руля, электростартер, карбюратор "Микуни" наст. Япония</Description>
        <InspectionPlace>Находится в начале Владимирской обл. Киржачский р-н</InspectionPlace>
        <Region>Москва</Region>
        <Price>130000</Price>
        <ManagerName>Менеджер по продажам</ManagerName>
        <AllowEmail>Да</AllowEmail>
        <Images>
            <Image url="http://rossnegohod.ru/image/cache/catalog/buran-leader-74x74.jpg"/>
            <Image url="http://rossnegohod.ru/image/cache/catalog/buran-leader_color_4-74x74.jpg"/>
        </Images>
        <AdStatus>Free</AdStatus>
				<Latitude>51.537047</Latitude>
				<Longitude>46.056939</Longitude>
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
