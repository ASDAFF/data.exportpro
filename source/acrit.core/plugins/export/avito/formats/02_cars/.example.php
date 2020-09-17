<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<Ads formatVersion="3" target="Avito.ru">
    <Ad>
        <Id>xjfdge4735404</Id>
        <DateBegin>2015-11-27</DateBegin>
        <DateEnd>2079-08-28</DateEnd>
        <Description>Автомобиль покупался новым в мае 2013 года, все ТО пройдены по регламенту в Тойота Центр Петровка. Машина на гарантии до мая 2016. Отличное внешнее и техническое состояние.
  
Возможен обмен на Ваш авто на выгодных условиях. Гарантия юридической чистоты.</Description>
        <AdStatus>TurboSale</AdStatus>
        <AllowEmail>Нет</AllowEmail>
        <CompanyName>ООО "Рога и копыта"</CompanyName>
        <ManagerName>Иван Петров-Водкин</ManagerName>
        <ContactPhone>+7 916 683-78-22</ContactPhone>
        <Region>Москва</Region>
        <Subway>Белорусская</Subway>
        <Category>Автомобили</Category>
        <CarType>С пробегом</CarType>
		<Price>250000</Price>
        <Kilometrage>53000</Kilometrage>
        <Accident>Не битый</Accident>
        <Make>Toyota</Make>
        <Model>Highlander</Model>
        <Year>2013</Year>
        <VIN>1FTWR72P1LVA41777</VIN>
        <CertificationNumber>77РМ193777</CertificationNumber>
        <BodyType>Внедорожник</BodyType>
        <Doors>5</Doors>
        <Color>Серый</Color>
        <FuelType>Бензин</FuelType>
        <EngineSize>3.5</EngineSize>
        <Power>249</Power>
        <Transmission>Автомат</Transmission>
        <DriveType>Полный</DriveType>
        <WheelType>Левый</WheelType>
				<GenerationId>336152</GenerationId>
				<ModificationId>112025</ModificationId>
				<ComplectationId />
        <PowerSteering>Электро-</PowerSteering>
        <ClimateControl>Климат-контроль многозонный</ClimateControl>
        <ClimateControlOptions>
            <Option>Атермальное остекление</Option>
             <Option>Управление на руле</Option>
        </ClimateControlOptions>
        <Interior>Кожа</Interior>
        <InteriorOptions>
            <Option>Люк</Option>
            <Option>Кожаный руль</Option>
        </InteriorOptions>
        <Heating>
            <Option>Передних сидений</Option>
            <Option>Зеркал</Option>
            <Option>Руля</Option>
        </Heating>       
        <PowerWindows>Только передние</PowerWindows>
        <ElectricDrive>
            <Option>Передних сидений</Option>
            <Option>Зеркал</Option>
            <Option>Складывания зеркал</Option>
        </ElectricDrive>     
        <MemorySettings>
            <Option>Передних сидений</Option>
            <Option>Зеркал</Option>
        </MemorySettings>
        <DrivingAssistance>
            <Option>Датчик дождя</Option>
            <Option>Датчик света</Option>
            <Option>Парктроник задний</Option>
        </DrivingAssistance>
        <AntitheftSystem>
            <Option>Сигнализация</Option>
            <Option>Иммобилайзер</Option>
        </AntitheftSystem>
        <Airbags>
            <Option>Фронтальные</Option>
            <Option>Боковые передние</Option>
        </Airbags>
        <ActiveSafety>
            <Option>Антиблокировка тормозов</Option>
            <Option>Антипробуксовка</Option>
            <Option>Курсовая устойчивость</Option>
        </ActiveSafety>
        <Multimedia>
            <Option>CD/DVD/Blu-ray</Option>
            <Option>MP3</Option>
            <Option>Управление на руле</Option>
        </Multimedia>
        <AudioSystem>8+ колонок</AudioSystem>
        <AudioSystemOptions>
            <Option>Сабвуфер</Option>
        </AudioSystemOptions>
        <Lights>Светодиодные</Lights>
        <LightsOptions>
            <Option>Омыватели фар</Option>
            <Option>Противотуманные</Option>
        </LightsOptions>
        <Wheels>19</Wheels>
        <WheelsOptions>
            <Option>Зимние шины в комплекте</Option>
        </WheelsOptions>
        <Owners>1</Owners>
        <Maintenance>
            <Option>Есть сервисная книжка</Option>
            <Option>На гарантии</Option>
        </Maintenance>       
        <Images>
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg" />
            <Image url="http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg" />
        </Images>
        <VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
    </Ad>
    <Ad>
        <Id>123</Id>
        <Description>Супер-мега-автомобиль, совсем свежий, только что с грядки. Подходи, не скупись, покупай живопись!..</Description>
        <Region>Владимирская область</Region>
        <City>Владимир</City>
		<District>Ленинский</District>
        <Category>Автомобили</Category>
        <CarType>Новые</CarType>
        <Make>Subaru</Make>
        <Model>Forester</Model>
        <Year>2015</Year>
        <BodyType>Кроссовер</BodyType>
        <Doors>5</Doors>
        <Color>Чёрный</Color>
        <FuelType>Бензин</FuelType>
        <EngineSize>2</EngineSize>
        <Power>150</Power>
        <Transmission>Вариатор</Transmission>
        <DriveType>Полный</DriveType>
        <WheelType>Левый</WheelType>
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
