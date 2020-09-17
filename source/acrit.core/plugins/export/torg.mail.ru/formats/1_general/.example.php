<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="windows-1251"?>
<torg_price date="2018-09-13 10:00"> 
	<shop>
		<name>Магазин-пример</name>
		<company>ООО "Магазин-пример"</company>
		<url>http://site.ru</url>
		<currencies>
			<currency id="RUR" rate="1"/>
			<currency id="USD" rate="33.70"/>
		</currencies>
		<categories>
			<category id="1" parentId="0">Кондиционеры</category>
			<category id="2" parentId="1">Настенные кондиционеры</category>
		</categories>
		<offers>
			<offer id="1" available="true" cbid="4.50">
				<url>http://magazin-primer.ru/cond/wall/model1?from=torg</url>
				<price>10596</price>
				<currencyId>RUR</currencyId>
				<categoryId>2</categoryId>
				<picture>http://magazin-primer.ru/pictures/model1.jpeg</picture>
				<typePrefix>Кондиционер</typePrefix>
				<vendor>LG</vendor>
				<model>LS-H0561AL</model>
				<description>Для помещеня площадью до 16 кв.м. этот сплит - кондиционер является самым доступным в своей категории. Сборка - Ю.Корея. Только охлаждение.</description>
				<delivery>true</delivery>
				<pickup>false</pickup>
				<local_delivery_cost>300</local_delivery_cost>
			</offer>
			<offer id="2" available="true" cbid="4.70">
				<url>http://magazin-primer.ru/cond/wall/model2?from=torg</url>
				<price>320</price>
				<currencyId>USD</currencyId>
				<categoryId>2</categoryId>
				<picture>http://magazin-primer.ru/pictures/model2.jpeg</picture>
				<name>Кондиционер LG S07LH</name>
				<description>Сплит - кондиционер с плазменным фильтром для помещения площадью до 21 кв.м. Охлаждение/обогрев.</description>
				<delivery>true</delivery>
				<pickup>true</pickup>
				<local_delivery_cost>300</local_delivery_cost>
			</offer>
		</offers>
	</shop>
</torg_price>
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
