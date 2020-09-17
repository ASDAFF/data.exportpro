<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8" ?>
<price date="2008-09-19 12:55">
<name>Интернет-магазин</name>
<currency id="USD" rate="76.50"/>
<catalog>
	<category id="1">Мобильные телефоны</category>
	<category id="1026" parentID="1">Мобильные телефоны iPhone</category>
	<category id="1042" parentID="1">Мобильные телефоны Б/У</category>
	<category id="1055" parentID="1">Мобильные телефоны SerteC</category>
	<category id="1001" parentID="1">Мобильные телефоны Motorola</category>
	<category id="1002" parentID="1">Мобильные телефоны Nokia</category>
	<category id="1003" parentID="1">Мобильные телефоны Samsung</category>
	<category id="1004" parentID="1">Мобильные телефоны Sony Ericsson</category>
	<category id="1027" parentID="1">Мобильные телефоны Alcatel</category>
	<category id="1033" parentID="1">Мобильные телефоны Fly</category>
	<category id="1034" parentID="1">Мобильные телефоны LG</category>
	<category id="1021" parentID="1">Мобильные телефоны Sitronics</category>
	<category id="2">Карты памяти</category>
	<category id="1005" parentID="2">Карты памяти Memory Stick Micro M2</category>
	<category id="1006" parentID="2">Карты памяти MicroSD</category>
	<category id="1007" parentID="2">Карты памяти MiniSD</category>
	<category id="1009" parentID="2">Карты памяти Memory Stick Pro Duo</category>
	<category id="1010" parentID="2">Карты памяти RS MMC</category>
	<category id="1032" parentID="2">Карты памяти Transcend SD</category>
	<category id="1040" parentID="2">Карты памяти Compact Flash</category>
	<category id="1041" parentID="2">Карты памяти Secure Digital</category>
	<category id="1018" parentID="2">Карты памяти xD-picture Card</category>
	<category id="1024" parentID="2">Карты памяти USB FlashCard</category>
	<category id="3">Цифровые фотоаппараты</category>
	<category id="1011" parentID="3">Цифровые фотоаппараты Canon</category>
	<category id="1047" parentID="3">Цифровые фотоаппараты Casio</category>
	<category id="1017" parentID="3">Цифровые фотоаппараты Ergo</category>
	<category id="1029" parentID="3">Цифровые фотоаппараты Kodak</category>
	<category id="1028" parentID="3">Цифровые фотоаппараты Nikon</category>
	<category id="1012" parentID="3">Цифровые фотоаппараты Olympus</category>
	<category id="1025" parentID="3">Цифровые фотоаппараты Panasonic</category>
	<category id="1013" parentID="3">Цифровые фотоаппараты Samsung</category>
	<category id="1014" parentID="3">Цифровые фотоаппараты Sony CyberShot</category>
	<category id="4">MP3 Плееры (Flash)</category>
	<category id="1015" parentID="4">MP3 Плееры (Flash) Transcend T-Sonic</category>
	<category id="1023" parentID="4">MP3 Плееры (Flash) Ergo</category>
	<category id="1016" parentID="4">MP3 Плееры (Flash) Apple ipod</category>
	<category id="1053" parentID="4">MP3 Плееры (Flash) Wokster</category>
	<category id="1061" parentID="4">MP3 Плееры (Flash) Apacer</category>
	<category id="5">Радиотелефоны</category>
	<category id="1019" parentID="5">Радиотелефоны Ergo</category>
	<category id="1020" parentID="5">Радиотелефоны Philips</category>
	<category id="1022" parentID="5">Радиотелефоны Panasonic</category>
	<category id="1039" parentID="5">Радиотелефоны Siemens</category>
	<category id="6">DVD плееры</category>
	<category id="1030" parentID="6">DVD плееры Ergo</category>
	<category id="1031" parentID="6">DVD плееры X-DIGITAL</category>
	<category id="7">Видеокамеры</category>
	<category id="1035" parentID="7">Видеокамеры Canon</category>
	<category id="1036" parentID="7">Видеокамеры Panasonic</category>
	<category id="1037" parentID="7">Видеокамеры Samsung</category>
	<category id="1038" parentID="7">Видеокамеры Sony</category>
	<category id="8">Карманные компьютеры</category>
	<category id="1043" parentID="8">Карманные компьютеры ASUS</category>
	<category id="1046" parentID="8">Карманные компьютеры HTC</category>
	<category id="9">Ноутбуки</category>
	<category id="1044" parentID="9">Ноутбуки Acer</category>
	<category id="1045" parentID="9">Ноутбуки ASUS</category>
	<category id="1048" parentID="9">Ноутбуки Toshiba</category>
	<category id="1052" parentID="9">Ноутбуки LG</category>
	<category id="10">Гарнитуры Bluetooth</category>
	<category id="1049" parentID="10">Гарнитуры Bluetooth Nokia</category>
	<category id="1050" parentID="10">Гарнитуры Bluetooth Samsung</category>
	<category id="1060" parentID="10">Гарнитуры Bluetooth Southwing</category>
	<category id="1051" parentID="10">Гарнитуры Bluetooth Jabra</category>
	<category id="11">GPS-навигаторы</category>
	<category id="1054" parentID="11">GPS-навигаторы Ergo</category>
	<category id="1056" parentID="11">GPS-навигаторы Eagle</category>
	<category id="1057" parentID="11">GPS-навигаторы Lowrance</category>
	<category id="12">Эхолоты</category>
	<category id="1058" parentID="12">Эхолоты Eagle</category>
	<category id="13">MPEG4-плеер</category>
	<category id="1059" parentID="13">MPEG4-плеер Wokster</category>
</catalog>
<items>
	<item id="330">
	<name>Motorola A1200</name>
	<categoryId>1001</categoryId>
	<price>1260</price>
	<bnprice>1300</bnprice>
	<url>http://url/catalog/1/1/330/</url>
	<image>http://url/images/image7402727981188804741.jpg</image>
	<vendor>Motorola</vendor>
	<description>GSM 900/1800/1900. Тип корпуса: раскладушка. Аккумулятор: 850 мАч. цветной TFT экран, Цветной сенсорный TFT экран, 262144 цветов, 240х320, 2.4, дополнительный экран 65536 цветов, 120x160 пикс. Интерфейс USB. Встроенный MP3 плеер. Голосовое управление. Поддержка GPRS, Bluetooth, MMS. Запись видео. Java-приложения. Камера 2 Мп, 1600x1200, режим макросъемки, запись видео Вес: 122 г. Размер 95.7 х 51.7 х 21.5 мм 
	</description>
    <guarantee type="manufacturer">12</guarantee>
    <param name="Страна изготовления">Китай</param>
	</item>
	<item id="400">
	<name>Motorola C123</name>
	<categoryId>1001</categoryId>
	<price>240</price>
	<bnprice>250</bnprice>
	<url>http://url/catalog/1/1/400/</url>
	<image>http://url/images/image8165287011197744112.jpg</image>
	<vendor>Motorola</vendor>
	<description>Аппарат начального класса выполнен в классическом моноблочном форм-факторе. Телефон оснащен минимальным количеством функций и предназначен только для осуществления звонков и отправки SMS, аккумулятор большой емкости позволяет аппарату работать до 11 часов в режиме разговора. Функционал: GSM 900/1800, LCD-монохромный (96x64 pix), SMS, iTAP, монофонические сигналы вызова, редактор мелодий, секундомер, калькулятор, будильник, телефонная книга ограничена объемом SIM карты</description>
    <guarantee unit="days" type="shop">7</guarantee>
	</item>
	<item id="1192">
	<name>Motorola E8</name>
	<categoryId>1001</categoryId>
	<price>1460</price>
	<bnprice>1505</bnprice>
	<url>http://url/catalog/1/1/1192/</url>
	<image>http://url/images/image6522896241216847367.jpg</image>
	<vendor>Motorola</vendor>
	<description/>
	</item>
	<item id="5">
	<name>Motorola K1</name>
	<categoryId>1001</categoryId>
	<price>795</price>
	<bnprice>815</bnprice>
	<url>http://url/catalog/1/1/5/</url>
	<image>http://url/images/image3906117671182176494.jpg</image>
	<vendor>Motorola</vendor>
	<description>Стандарты: GSM 850/900/1800/1900. Корпус: раскладушка. Антенна: встроенная. Дисплей TFT, 262144 цветов. Размер дисплея: 176 x 220 пикселей. Тел. книжка: 1000 записей. Фотокамера встроенная, возможность воспроизведения видео. Голосовой набор. Bluetooth. MMS. Органайзер. Полифонические мелодии вызова. Размер 103 х 42 х 16 мм. Вес 102 г
	</description>
	</item>	
</items>
</price>
XML;
if (!Helper::isUtf())
{
	$strExample = Helper::convertEncoding($strExample, 'UTF-8', 'CP1251');
}
?>
<div class="acrit-exp-plugin-example">
	<pre><code class="xml"><?= htmlspecialcharsbx($strExample); ?></code></pre>
</div>
<script>
	$('.acrit-exp-plugin-example pre code.xml').each(function (i, block) {
		highlighElement(block);
	});
</script>
