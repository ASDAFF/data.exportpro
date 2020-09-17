<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<Ads target="Avito.ru" formatVersion="3">
    <Ad>
        <Id>h632145</Id>
        <Category>Музыкальные инструменты</Category>
        <GoodsType>Гитары и другие струнные</GoodsType>
        <AdType>Товар приобретен на продажу</AdType>
        <Street>Москва, м. Речной вокзал</Street>
        <Title>Гитара Yamaha Pacifica</Title>
		<Description><![CDATA[
        Гитара в очень хорошем состоянии, надёжная, прекрасный звук, есть едва заметные следы использования. Не Китай, сделана в Индонезии. Была у нас группа, распалась, поэтому некоторые инструменты оказались уже не нужны. Могу предложить вместе с гитарой: шнур 3 метра, фирменный широкий ремень, хороший чехол, а также комбоусилитель. Есть два громких усилителя. С одним (10 Вт) цена за всё 10500, с другим (15 Вт) - 12500.
        ]]></Description>
        <Region>Москва</Region>
        <Price>8000</Price>
        <ManagerName>Менеджер по продажам</ManagerName>
        <AllowEmail>Да</AllowEmail>
        <Images>
            <Image url="https://64.img.avito.st/640x480/4044040064.jpg"/>
            <Image url="https://94.img.avito.st/640x480/4044040394.jpg"/>
            <Image url="https://29.img.avito.st/640x480/4044040429.jpg"/>
            <Image url="https://42.img.avito.st/640x480/4044040442.jpg"/>
            <Image url="https://43.img.avito.st/640x480/4044040443.jpg"/>
        </Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
        <AdStatus>Free</AdStatus>
    </Ad>
    <Ad>
        <Id>h4672452</Id>
        <Category>Велосипеды</Category>
        <AdType>Товар приобретен на продажу</AdType>
        <VehicleType>Дорожные</VehicleType>
        <Street>Москва, м. Тушинская</Street>
		<Title>Велосипед Merida S300</Title>        
		<Description><![CDATA[
        Рама 57 см. Комплектацию см. ниже, оборудование не менялось.
        Куплен в 2010г. Пробег 2270, по асфальту.
        Состояние хорошее. Рабочие потертости (см. фото).
        Продаю, потому что не катаюсь.

        <ul>
        <li>Рама, вилка – Specialized A1 Premium Aluminum</li>
        <li>Руль Specialized low rise</li>
        <li>Рукоятки руля – Body Geometry Comfort</li>
        <li>Тормоза (передний и задний) – дисковые, Avid BB5. Колодки еще походят.</li>
        <li>Система переключения передач – Shimano, 3х8 передач</li>
        <li>Педали – Globe anti-slip composite</li>
        <li>Обода – Specialized/Alex Globe, 700c, 32h</li>
        <li>Шины – Nimbus Sport 700x35c, 60TPI</li>
        <li>Сиденье – Specialized Sonoma 155mm</li>
        </ul>
        Отдаю с крыльями и велокомпьютером.

        Контактный номер - Николай.
        Велосипед в Красногорске, место встречи обсудим. Тушино, Митино, Красногорск и пр.
        ]]></Description>
        <Region>Москва</Region>
        <Price>19700</Price>
        <ManagerName>Менеджер по продажам</ManagerName>
        <AllowEmail>Да</AllowEmail>
        <Images>
            <Image url="https://85.img.avito.st/640x480/3577384685.jpg"/>
            <Image url="https://39.img.avito.st/640x480/3577388839.jpg"/>
            <Image url="https://99.img.avito.st/640x480/3577393799.jpg"/>
            <Image url="https://13.img.avito.st/640x480/3577394413.jpg"/>
            <Image url="https://96.img.avito.st/640x480/3577394896.jpg"/>
        </Images>
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
