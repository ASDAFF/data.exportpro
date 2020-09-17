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
        <Category>Одежда, обувь, аксессуары</Category>
        <GoodsType>Женская одежда</GoodsType> 
		<AdType>Товар приобретен на продажу</AdType>
        <Apparel>Платья и юбки</Apparel>
        <Size>S</Size>
        <Title>Прекрасное платье</Title>
      <Description><![CDATA[
<p>Лёгкая и изящная юбка, не сковывает движения откроет ваши стройные гибкие ноги. На сцене такая юбка смотрится невероятно красиво и прелестно, она словно выступает неотъемлемым элементом происходящего там действия. Идеально подходит для вечера, корпоратива или же для повседневной жизни.</p>
<p>Сделана из тонких полупрозрачных тканей:</p>
<ul>
<li>шифона</li>
<li>фатина</li>
<li>сетки</li>
</ul>
]]></Description>
        <Price>25300</Price>
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
