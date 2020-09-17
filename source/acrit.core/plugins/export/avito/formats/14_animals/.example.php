<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<Ads target="Avito.ru" formatVersion="3">
    <Ad>
        <Id>24534225</Id>
        <Title>Щенок хаски ласковый</Title>
        <Category>Собаки</Category>
        <Breed>Сибирский хаски</Breed>
        <Description><![CDATA[
        Племенной питомник Сибирских хаски продает породных щенков Сибирский хаски!
        Красивые, умные, мальчики и девочки, любых окрасов, без наследственных заболеваний, правильного разведения и питания, с устойчивой психикой, для семьи, для выставок, для спорта!
        Полный пакет документов, родословная РКФ, все прививки.
        Скидки на снаряжение для собак, корма по цене питомника, консультации.
        Возраста щенков от 2х месяцев до 7месяцев.
        Цена на щенков разная, звоните.
        ]]></Description>
        <Region>Москва</Region>
        <Price>15000</Price>
        <ManagerName>Менеджер по продажам</ManagerName>
        <AllowEmail>Да</AllowEmail>
        <Images>
            <Image url="https://upload.wikimedia.org/wikipedia/commons/thumb/8/88/Husky_Puppy.jpg/260px-Husky_Puppy.jpg"/>
        </Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
        <AdStatus>Free</AdStatus>
    </Ad>
    <Ad>
        <Id>56352</Id>
        <Title>Умная девочка кун</Title>
        <Category>Кошки</Category>
        <Breed>Мейн-кун</Breed>
        <Description>Очаровательная кошечка мейн-кун из питомника "Oligarch" готова переехать к новым родителям. Отличные породные данные. Титулованные родители. Очень умная и ласковая девочка. Неутомимая помощница в любых домашних делах. Тарахтелка. Привита. Документы.</Description>
        <Region>Москва</Region>
        <Price>18000</Price>
        <ManagerName>Менеджер по продажам</ManagerName>
        <AllowEmail>Да</AllowEmail>
        <Images>
            <Image url="https://20.img.avito.st/640x480/3889069720.jpg"/>
            <Image url="https://48.img.avito.st/640x480/3968236548.jpg"/>
        </Images>
        <AdStatus>Free</AdStatus>
    </Ad>
    <Ad>
        <Id>634252</Id>
        <Title>Отличная кобыла от фриза</Title>
        <Category>Другие животные</Category>
        <GoodsType>Лошади</GoodsType>
        <Description>Красивая, высокая, 158 в холке, общительная, еще юная кобылка, 2015г рождения, от кобылы (орлово-першеронская) и фризского жеребца ждет своих новых владельцев!! Отвечу на все вопросы WhatsApp, Viber
        Стоим в Костромской обл</Description>
        <Region>Москва</Region>
        <Price>70000</Price>
        <ManagerName>Менеджер по продажам</ManagerName>
        <AllowEmail>Да</AllowEmail>
        <Images>
            <Image url="https://81.img.avito.st/640x480/3712723881.jpg"/>
            <Image url="https://18.img.avito.st/640x480/3712723618.jpg"/>
            <Image url="https://31.img.avito.st/640x480/3712726631.jpg"/>
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
