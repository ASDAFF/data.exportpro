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
		<Latitude>51.537047</Latitude>
		<Longitude>46.056939</Longitude>
		<District>Ленинский</District>
		<Category>Грузовики и спецтехника</Category>        
		<GoodsType>Строительная техника</GoodsType>      
		<Title>Автобетононасос Junjin JXZ 37-4.16HP на Daewoo</Title>
		<Description><![CDATA[
			<p><strong>Характеристики насоса:</strong></p>
			<ul>
				<li>Объем подачи: 158 м3/ч
				<li>Диаметр цилиндра: 230 мм
				<li>Ход поршня: 2100 мм
			</ul>
			]]></Description>
		<Price>9000000</Price>
		<Images>
			<Image url="http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg" />
			<Image url="http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg" />
		</Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
	</Ad>
	<Ad>
		<Id>odb3727321-12</Id>
		<Region>Санкт-Петербург</Region>
		<Subway>Автово</Subway>
		<Category>Грузовики и спецтехника</Category>        
		<GoodsType>Автокраны</GoodsType>        
		<Title>Автокран Днепр 25 т</Title>
		<Latitude>51.537047</Latitude>
		<Longitude>46.056939</Longitude>
		<Description>Продам автокран:
- Дата выпуска: 05.2014 г.
- Техника в отличном состоянии. 
- Наработка около 700 м/ч.
- Вложений не требует.</Description>
		<Price>1100000</Price>
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
