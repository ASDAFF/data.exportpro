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
		<Category>Оборудование для бизнеса</Category>        
		<GoodsType>Для салона красоты</GoodsType>      
		<Title>Электрический бойлерный пароконвектомат Ratio C1</Title>
        <Description><![CDATA[
<p><strong>Электрический бойлерный пароконвектомат RATIO C1</strong></p>
<ul>
<li>Cool Down – быстрое охлаждение рабочей камеры.
<li>Режим понижения мощности для электрических моделей (1/2 энергии).
</ul>
]]></Description>
		<Price>150000</Price>
		<Images>
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg" />
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg" />
        </Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
    </Ad>
	<Ad>
        <Id>odb3727321</Id>
        <Region>Санкт-Петербург</Region>
		<Subway>Автово</Subway>
        <Category>Готовый бизнес</Category>        
		<GoodsType>Торговля</GoodsType>        
		<Title>Магазин разливного пива в прикассовой зоне</Title>
        <Description>Продаем успешный магазин разливного пива, находящийся в прикассовой зоне супермаркета Дикси на первой линии домов крупной магистрали. Отдел расположен сразу при входе в супермаркет, не заметить невозможно. Посещаемость супермаркета в среднем 1500 человек в день</Description>
		<Price>450000</Price>
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
