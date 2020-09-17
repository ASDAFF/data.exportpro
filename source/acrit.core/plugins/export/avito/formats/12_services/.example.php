<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<Ads target="Avito.ru" formatVersion="3">
	<Ad>
    	<Id>632324</Id>
		<Region>Москва</Region>
    	<Street>ул. Лесная, 9</Street>
    	<Category>Предложение услуг</Category>
    	<ServiceType>Транспорт, перевозки</ServiceType>
    	<ServiceSubtype>Аренда авто</ServiceSubtype>
    	<Title>Аренда авто под такси с лицензией</Title>
		<Description>Сдаю в аренду для такси автомобили Хендай Солярис. Новые на механике.
Гражданство и В/У только РФ, прописка Москва или МО, либо ближайшие регионы к Москве.
График - 7/0. Залог - 5000 руб., Возраст от 27, стаж от 5 лет.
Безаварийный стаж вождения от 3 лет.
Оплата аренды производиться за три дня вперед и более, на карту Сбербанка.
Подключаем к Яндекс, Uber, Getting.</Description>
    	<Price>1500</Price>
    	<ManagerName>Менеджер</ManagerName>
    	<AllowEmail>Да</AllowEmail>
    	<AdStatus>Free</AdStatus>
    	<Images>
        	<Image url="https://71.img.avito.st/640x480/3847161971.jpg"/>
    	</Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
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
