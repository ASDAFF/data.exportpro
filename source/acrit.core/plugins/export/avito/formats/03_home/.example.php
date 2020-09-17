<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<Ads formatVersion="3" target="Avito.ru">
    <Ad>
		<Id>723681273</Id>
		<DateBegin>2015-11-27</DateBegin>
        <DateEnd>2079-08-28</DateEnd>
        <AdStatus>TurboSale</AdStatus>
        <AllowEmail>Да</AllowEmail>
        <ManagerName>Иван Петров-Водкин</ManagerName>
        <ContactPhone>+7 916 683-78-22</ContactPhone>
        <Region>Владимирская область</Region>
        <City>Владимир</City>
		<District>Ленинский</District>
		<Category>Бытовая техника</Category>        
		<GoodsType>Стиральные машины</GoodsType>        
		<AdType>Товар приобретен на продажу</AdType>
        <Title>Стиральная машина Candy GC4 1051 D</Title>
        <Description><![CDATA[
<p>Характеристики:</p>
<ul>
<li>отдельно стоящая стиральная машина
<li>60x40x85 см
<li>фронтальная загрузка
<li>cтирка до 5 кг
<li>класс энергопотребления: A+
<li>электронное управление
<li>отжим при 1000 об/мин
<li>защита от протечек
</ul>
]]></Description>
		<Price>15000</Price>
		<Images>
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg" />
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg" />
        </Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
    </Ad>
	<Ad>
        <Id>remont_i_stroitelstvo001</Id>
        <AdStatus>Free</AdStatus>
        <AllowEmail>Нет</AllowEmail>
        <Region>Санкт-Петербург</Region>
		<Subway>Автово</Subway>
		<ContactPhone>+7 495 777-10-66</ContactPhone>
        <Category>Ремонт и строительство</Category>        
		<GoodsType>Инструменты</GoodsType>                
		<AdType>Товар приобретен на продажу</AdType>
		<Title>Перфоратор Makita HR3200C</Title>
        <Description>Перфоратор имеет три режима работы: сверление, сверление с ударом, долбление.
 
Мощность, Вт 850 
Max диаметр сверления коронкой (бетон), мм: 90 
Max диаметр сверления</Description>
		<Price>250000</Price>
    </Ad>		
    <Ad>
        <Id>mebel_i_interer002</Id>
        <AdStatus>Free</AdStatus>
        <AllowEmail>Нет</AllowEmail>
        <Region>Москва</Region>
		<Subway>Белорусская</Subway>
		<ContactPhone>+7 495 777-10-66</ContactPhone>
        <Category>Мебель и интерьер</Category>
        <GoodsType>Кровати, диваны и кресла</GoodsType>        
		<AdType>Товар от производителя</AdType>
        <Title>Кровать детская Легенда 24</Title>
        <Description>Спальное место: 80х160 см, габариты: 1642×882×500 мм, материал: ЛДСП, цвет корпуса: венге светлый.</Description>
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
