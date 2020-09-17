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
		<Street>ул. Ленина, д. 9</Street>
        <Category>Вакансии</Category>           
		<Industry>Производство, сырьё, с/х</Industry>
		<Title>Продавец-консультант</Title>
		<JobType>Полный день</JobType>
		<Experience>Более 1 года</Experience>
		<Description><![CDATA[
<p>В Компании <strong>конкурс на вакансию</strong> «Специалист офиса продаж».<br />
Своим сотрудникам Розничная сеть <em>обеспечивает</em>.</p>
<ul>
<li>Обучение за счет компании,
<li>Возможность переезда в другой город,
<li>Официальный доход.
<li>Премия, которая зависит только от твоей работы.
<li>Участие в корпоративных мероприятиях.
<li>Гибкий график работы.
<li>Полный социальный пакет (ДМС, оплата больничного, ежегодного отпуска, дополнительное страхование от несчастных случаев).
</ul>
]]></Description>
		<Salary>33000</Salary>
		<Images>
			<Image url="http://52.img.com/300x150/25719652_811142943.jpg" />
		</Images>		
    </Ad>
	<Ad>
        <Id>vacancy2016-07-25-2</Id>
        <Region>Москва</Region>
		<Street>ул. Ленина, д. 9</Street>
		<Latitude>53.485221</Latitude>
		<Longitude>41.840005</Longitude>
        <Category>Вакансии</Category>           
		<Industry>Юриспруденция</Industry>
		<Title>Старший помошник юриста</Title>
		<JobType>Неполный день</JobType>
		<Experience>Не имеет значения</Experience>
		<Description>Требования:
- желание работать
- умение работать в команде
- нацеленность на результат
- высшее юридическое образование
Условия:
- стабильная работа в крупной компании
- полный социальный пакет
- оплачиваемый отпуск и больничный
- Стремительный карьерный рост
- Стабильность - официальное трудоустройство с первого дня, «белая» заработная плата, оплачиваемые отпуска и больничные листы
</Description>	
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
