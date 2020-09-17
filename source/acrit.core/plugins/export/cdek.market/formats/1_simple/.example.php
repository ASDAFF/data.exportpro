<?
use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<yml_catalog date="2019-09-19 10:57">
    <shop>
        <name>akrit</name>
        <company>akrittest</company>
        <platform>1С-Битрикс: Управление сайтом</platform>
        <version>19.0.250</version>
        <url>http://akrittest</url>
        <categories>
            <category id="14">Одежда, обувь и аксессуары///Аксессуары///Ремни, пояса и подтяжки///Ремни</category>
            <category id="15">Одежда, обувь и аксессуары///Аксессуары///Ремни, пояса и подтяжки///Ремни</category>
        </categories>
        <offers>
            <offer>
                <Product_code>1111737</Product_code>
                <Language>ru</Language>
                <Category>Одежда, обувь и аксессуары///Аксессуары///Ремни, пояса и подтяжки///Ремни</Category>
                <Price>1190</Price>
                <Weight>123</Weight>
                <Downloadable>false</Downloadable>
                <Detailed_image>http://akrittest/upload/iblock/c06/c06522c9318c78465a6f933e981c0cf5.jpg</Detailed_image>
                <Product_name>Ремень Классика</Product_name>
                <Description><![CDATA[Стильный и качественный ремень станет прекрасным завершение Вашего образа. Ширина ремня ок. 3,5 см, длина регулируется на уменьшение. Уход за изделием: натереть пряжку войлоком, кожу влажной тряпочкой.
                    <div>
                        <br />
                    </div>

                    <div>
                        <div><b>Дополнительное описание:</b></div>

                        <div>
                            <ul>
                                <li><b>Габариты предметов:</b> Ширина, 3.5 см</li>

                                <li><b>Габариты предметов:</b> Длина, 105.0 см</li>

                                <li><b>Размер пряжки:</b> Крупная, 7.0 см</li>

                                <li><b>Сезон:</b> круглогодичный</li>

                                <li><b>Пол:</b> Мужской</li>

                                <li><b>Страна бренда:</b> Италия</li>

                                <li><b>Страна производитель:</b> Италия</li>
                            </ul>
                        </div>
                    </div>
                    ]]></Description>
                <Short_description><![CDATA[]]></Short_description>
                <Quantity>4</Quantity>
            </offer>
            <offer>
                <Product_code>11118168</Product_code>
                <Language>ru</Language>
                <Category>Одежда, обувь и аксессуары///Аксессуары///Ремни, пояса и подтяжки///Ремни</Category>
                <Price>750</Price>
                <Weight>324</Weight>
                <Downloadable>false</Downloadable>
                <Detailed_image>http://akrittest/upload/iblock/473/473e5d283e2776978b05cd6cc49c8322.jpg</Detailed_image>
                <Product_name>МАДРИД Диван Olivia коричневый кожзам бежевый</Product_name>
                <Description><![CDATA[Диван Мадрид - мебель, на которой могут одновременно сидеть три человека. В его основе усиленный металлический каркас, из трубы сечением 20-30 мм с толщиной стенки 2 мм. Раскладывается диван вперед с помощью механизма трансформации аккордеон,образуя широкое бесшовное спальное место динной 200 см и шириной 150 см .Это полноценная кровать для ежедневного сна для двух человек. Имеет съемный чехол,который может быть заменен на новый или подвергнут чистке специальными средствами.]]></Description>
                <Quantity>2</Quantity>
            </offer>
        </offers>
    </shop>
</yml_catalog>
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