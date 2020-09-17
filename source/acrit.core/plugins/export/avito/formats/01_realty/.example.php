<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<Ads formatVersion="3" target="Avito.ru">
    <Ad>
		<Id>xjfdge4735202</Id>
		<Category>Квартиры</Category>
		<OperationType>Продам</OperationType>
		<DateBegin>2015-11-27</DateBegin>
		<DateEnd>2079-08-28</DateEnd>
		<Address>Россия, Алтайский край, Барнаул, Парашютная улица, 49</Address>
		<Description>
			Новая, просторная, светлая и уютная квартира с типовым косметическим ремонтом в новом доме серии "П-44Т". 
 
			Комнаты изолированные, 19 и 15 метров, кухня 13 метров с эркером, большая ванная, с/у раздельный, две застекленные лоджии. 
 
			А также:
				* стеклопакеты, 
				* паркетная доска, 
				* свободна, 
				* никто не прописан, 
				* прямая продажа.
		</Description>
		<Price>123000</Price>
		<CompanyName>ООО "Рога и копыта"</CompanyName>
		<ManagerName>Иван Петров-Водкин</ManagerName>
		<AllowEmail>Нет</AllowEmail>
		<ContactPhone>+7 916 683-78-22</ContactPhone>
		<Images>
			<Image url="http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg" />
			<Image url="http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg" />
		</Images>
		<VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
		<AdStatus>PushUp</AdStatus>
		<Rooms>2</Rooms>
		<Square>61</Square>
		<Floor>13</Floor>
		<Floors>16</Floors>
		<HouseType>Деревянный</HouseType>
		<MarketType>Новостройка</MarketType>
		<NewDevelopmentId>28212</NewDevelopmentId>
		<CadastralNumber>77:04:0004011:3882</CadastralNumber>
	</Ad>
    <Ad>
		<Id>xjfdge4735204</Id>
		<Category>Комнаты</Category>
		<Address>Тамбовская область, Моршанск, Моршанский р-н, с. Устьи, ул. Лесная, 7</Address>
		<Latitude>53.485221</Latitude>
		<Longitude>41.840005</Longitude>
		<Description><![CDATA[
<p>Новая, просторная, <strong>светлая и уютная квартира<strong> с ремонтом и большим холодильником,<br />
комнаты изолированные, 11 и 10 метров, кухня 3 метра с балконом.</p>
<p><em>Важно:</em></p>
<ul>
<li>маленькая ванная, 
<li>с/у совмещенный, 
<li>3 застекленные лоджии, 
<li>стеклопакеты, 
<li>паркет, 
<li>свободна.
</ul>
]]>
		</Description>
		<Price>102000</Price>
		<Rooms>2</Rooms>
		<Square>61.3</Square>
		<Floor>14</Floor>
		<Floors>16</Floors>
		<HouseType>Деревянный</HouseType>
		<OperationType>Сдам</OperationType>
		<LeaseType>На длительный срок</LeaseType>
		<PropertyRights>Посредник</PropertyRights>
		<LeaseCommissionSize>50</LeaseCommissionSize>
		<LeaseDeposit>2,5 месяца</LeaseDeposit>
		<LeaseBeds>3</LeaseBeds>
		<LeaseSleepingPlaces>5</LeaseSleepingPlaces>
		<LeaseMultimedia>
			<Option>Wi-Fi</Option>
			<Option>Кабельное / цифровое ТВ</Option>
		</LeaseMultimedia>
		<LeaseAppliances>
			<Option>Плита</Option>
			<Option>Стиральная машина</Option>
			<Option>Фен</Option>
		</LeaseAppliances>
		<LeaseComfort>
			<Option>Кондиционер</Option>
			<Option>Балкон / лоджия</Option>
		</LeaseComfort>
		<LeaseAdditionally>
			<Option>Можно с питомцами</Option>
			<Option>Можно для мероприятий</Option>
			<Option>Можно курить</Option>
		</LeaseAdditionally>
		<Images>
			<Image name="a1.jpg"/>
			<Image name="a2.jpg"/>
			<Image name="a3.jpg"/>
		</Images>
		<AllowEmail>Нет</AllowEmail>		
	</Ad>
    <Ad>
		<Id>47352067890</Id>
		<Category>Дома, дачи, коттеджи</Category>
		<OperationType>Продам</OperationType>
		<ObjectType>Дом</ObjectType>
		<Address>Владимирская область, Владимир, Судогодское шоссе</Address>
		<DistanceToCity>5</DistanceToCity>
		<Latitude>56.046606</Latitude>
		<Longitude>40.445891</Longitude>
		<Square>85</Square>
		<LandArea>6.5</LandArea>
		<Floors>2</Floors>
		<WallsType>Брус</WallsType>
		<Description>Тестовое объявление — тест Автозагрузки - Дома, дачи, коттеджи.</Description>
		<Price>2000000</Price>
		<AdStatus>Free</AdStatus>
	</Ad>
    <Ad>
		<Id>зем47352067891</Id>
		<Category>Земельные участки</Category>
		<OperationType>Сдам</OperationType>
		<ObjectType>Сельхозназначения (СНТ, ДНП)</ObjectType>
		<Address>Владимирская область, Владимир, Ленинский район, микрорайон Коммунар, Набережная улица</Address>
		<District>Октябрьский</District>
		<DistanceToCity>0</DistanceToCity>
		<Latitude>56.108573</Latitude>
		<Longitude>40.42247</Longitude>
		<LandArea>11.7</LandArea>
		<LeaseDeposit>2,5 месяца</LeaseDeposit>
		<Description>Тестовое объявление — тест Автозагрузки - Земельные участки.</Description>
		<Price>2000</Price>
	</Ad>	
    <Ad>
		<Id>гараж345353</Id>
		<Category>Гаражи и машиноместа</Category>
		<OperationType>Продам</OperationType>
		<ObjectType>Машиноместо</ObjectType>
		<ObjectSubtype>Многоуровневый паркинг</ObjectSubtype>
		<Secured>Нет</Secured>
		<Address>Санкт-Петербург, Роменская улица, 2</Address>
		<Square>10</Square>
		<Description>Тестовое объявление — тест Автозагрузки - Гаражи и машиноместа.</Description>
		<Price>1500000</Price>
		<AdStatus>Free</AdStatus>
	</Ad>
    <Ad>
		<Id>xjfdge4735205</Id>
		<Category>Коммерческая недвижимость</Category>
		<OperationType>Сдам</OperationType>
		<ObjectType>Офисное помещение</ObjectType>
		<BuildingClass>A</BuildingClass>
		<Address>Москва, ул. Лесная, д. 7</Address>
		<Square>300</Square>
		<LeaseDeposit>1,5 месяца</LeaseDeposit>
		<Title>Офис 300 м², бизнес-центр "Белые сады"</Title>
		<Description>Тестовое объявление — тест Автозагрузки - Коммерческая недвижимость.</Description>
		<Price>12000</Price>
		<PriceType>в год за м2</PriceType>
	</Ad>
    <Ad>
		<Id>нзр4735207</Id>
		<Category>Недвижимость за рубежом</Category>
		<ObjectType>Квартира, апартаменты</ObjectType>
		<OperationType>Продам</OperationType>
		<Address>Москва, ул. Лесная, д. 5</Address>
		<Country>Уругвай</Country>		
		<Description>Тестовое объявление — тест Автозагрузки - Недвижимость за рубежом.</Description>
		<Price>2100000</Price>
		<AdStatus>TurboSale</AdStatus>
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
