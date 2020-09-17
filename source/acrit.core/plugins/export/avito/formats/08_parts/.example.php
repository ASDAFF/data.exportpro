<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<Ads formatVersion="3" target="Avito.ru">
    <Ad>
        <Id>001</Id>
		<Category>Запчасти и аксессуары</Category>
        <TypeId>16-806</TypeId> 
		<AdType>Товар приобретен на продажу</AdType>
        <ContactPhone>+7 916 683-78-22</ContactPhone>
        <Region>Москва</Region>
        <Subway>Белорусская</Subway>
        <Title>Бампер передний Audi A4 MW0218681</Title>
        <Description>Передний бампер Ауди А4 Б8 рестайлинг 2011-2015 под фароомыватели
- Доставка по всей России.
- Запчасти продаются оптом и в розницу.
Цена на б/у бампер передний Audi A4 4 B8 (2007-2015) рестайлинг 2011-2015 под фароомыватели (MW-000186810219052016) зависит от состояния запчасти и не является фиксированной.
Действуют скидки для постоянных покупателей. Гарантия качества.
</Description>
        <Price>2500</Price>
				<Condition>Новое</Condition>
				<OEM>84501SC020</OEM>
        <Images>
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg" />
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg" />
        </Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
    </Ad>
    <Ad>
        <Id>002</Id>
		<Category>Запчасти и аксессуары</Category>
        <TypeId>10-045</TypeId> 
		<AdType>Товар приобретен на продажу</AdType>
        <RimDiameter>22</RimDiameter>
        <RimType>Литые</RimType>
        <TireType>Летние</TireType>
        <TireSectionWidth>205</TireSectionWidth>
        <TireAspectRatio>80</TireAspectRatio>
        <RimWidth>10</RimWidth>
        <RimBolts>5</RimBolts>
        <RimBoltsDiameter>120.65</RimBoltsDiameter>
        <RimOffset>-38</RimOffset>
        <CompanyName>ООО "Рога и копыта"</CompanyName>
        <ManagerName>Иван Петров-Водкин</ManagerName>
        <ContactPhone>+7 916 683-78-22</ContactPhone>
        <Region>Москва</Region>
        <Subway>Белорусская</Subway>
        <Title>Летние колеса R-22 для Mercedes Gelandewagen</Title>
        <Description><![CDATA[
<p>Артикул 967 MB (модель Brabus 850)</p>
<p>Диски R-22 модель Brabus 850 для Мерседес Геленваген (Mercedes Gelandewagen) G-класса (W-463). В сборе с высокоскоростными шинами. Колеса совершенно новые, отбалансированы и готовы к установке.</p>
<ul>
<li>Оплата наличным или безналичным расчетом.
<li>Доставка в пределах МКАД-бесплатно, за МКАД – 30 руб./км
<li>Доставка в регионы осуществляется транспортной компанией.
<li>При покупке комплекта колес установка и балансировка бесплатно!
</ul>]]></Description>
        <Price>100000</Price>
				<Condition>Новое</Condition>
				<OEM>84501SC021</OEM>
        <Images>
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2BA4.jpg" />
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2XA5.jpg" />
        </Images>
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
