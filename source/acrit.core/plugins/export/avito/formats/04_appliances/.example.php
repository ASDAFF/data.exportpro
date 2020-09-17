<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<Ads formatVersion="3" target="Avito.ru">
	<Ad>
       <Id>idobj01</Id>
       <Region>Санкт-Петербург</Region>
   	   <Subway>Автово</Subway>
       <Category>Настольные компьютеры</Category>
       <Title>Компьютер игровой</Title>
       <Description>Продается компьютер, бу 2 года, игры работают хорошо.</Description>
       <Price>25300</Price>
   </Ad>
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
        <Category>Аудио и видео</Category>       
        <GoodsType>Музыкальные центры, магнитолы</GoodsType>       
        <Title>Музыкальный центр</Title>
        <Description><![CDATA[
			<p>Современное устройство, обладающее стильным дизайном, 
			широкими мультимедийными возможностями, а также невероятным удобством в использовании
				</p>
			<p>Характеристики:</p>
			<ul>
			<li>отдельно стоящая колонка
			<li>60x40x85 см
			<li>фронтальная загрузка
			</ul>
		]]></Description>
        <Price>15000</Price>
        <Images>
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg" />
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg" />
        </Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
    </Ad>
</Ads><Ads formatVersion="3" target="Avito.ru">
	<Ad>
       <Id>idobj01</Id>
       <Region>Санкт-Петербург</Region>
   	   <Subway>Автово</Subway>
       <Category>Настольные компьютеры</Category>
       <Title>Компьютер игровой</Title>
       <Description>Продается компьютер, бу 2 года, игры работают хорошо.</Description>
       <Price>25300</Price>
   </Ad>
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
        <Category>Аудио и видео</Category>       
        <GoodsType>Музыкальные центры, магнитолы</GoodsType>       
        <Title>Музыкальный центр</Title>
        <Description><![CDATA[
			<p>Современное устройство, обладающее стильным дизайном, 
			широкими мультимедийными возможностями, а также невероятным удобством в использовании
				</p>
			<p>Характеристики:</p>
			<ul>
			<li>отдельно стоящая колонка
			<li>60x40x85 см
			<li>фронтальная загрузка
			</ul>
		]]></Description>
        <Price>15000</Price>
        <Images>
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg" />
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg" />
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
