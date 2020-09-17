<?
use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<yml_catalog date="2013-06-20 10:09:18">
    <shop>
        <name>Интернет-магазин</name>
        <company>Интернет-магазин</company>
        <categories>
            <category id="2">Офисная мебель</category>
            <category id="3" parentId="2">Стеллажи</category>
            <category id="4" parentId="2">Рабочие места</category>
            <category id="5" parentId="2">Стулья и кресла</category>
            <category id="6">Мягкая мебель</category>
            <category id="7" parentId="6">Диваны</category>
            <category id="8" parentId="6">Кровати</category>
            <category id="9">Мебель для сада</category>
            <category id="10">Зеркала</category>
            <category id="11">Освещение</category>
            <category id="12">Текстиль</category>
        </categories>
        <offers>
            <offer id="115" productId="43" quantity="16">
                <url>http://testbitrix.test/catalog/shelves/rack_2_sectional/</url>
                <price>14000.00</price>
                <purchasePrice>13200.00</purchasePrice>
                <categoryId>3</categoryId>
                <picture>http://testbitrix.test/upload/iblock/d2b/d2b25cbdc1f76b8b1672f5e8d1ec6589.JPG</picture>
                <name>Стеллаж 2-х секционный</name>
                <xmlId>82</xmlId>
                <productName>Стеллаж 2-х секционный</productName>
                <param name="Артикул" code="article">789789</param>
                <param name="Размер" code="size">двухъярусный</param>
                <param name="Цвет" code="color">белый</param>
                <vendor>Abagure</vendor>
                <param name="Вес" code="weight">50 кг</param>
                <unit code="pcs" name="Штука" sym="шт." />
                <vatRate>18</vatRate>
                <dimensions>100/50.8/150</dimensions>
                <barcode>012485ab</barcode>
            </offer>

            <offer id="116" productId="43" quantity="25">
                <url>http://testbitrix.test/catalog/shelves/rack_2_sectional/</url>
                <price>14500.00</price>
                <purchasePrice>11000.00</purchasePrice>
                <categoryId>3</categoryId>
                <picture>http://testbitrix.test/upload/iblock/d2b/d2b25cbdc1f76b8b1672f5e8d1ec1501.JPG</picture>
                <name>Стеллаж 2-х секционный (оранжевый)</name>
                <xmlId>83</xmlId>
                <productName>Стеллаж 2-х секционный</productName>
                <param name="Артикул" code="article">789789</param>
                <param name="Размер" code="size">двухъярусная</param>
                <param name="Цвет" code="color">черный</param>
                <vendor>Cologio</vendor>
                <param name="Вес" code="weight">60 кг</param>
                <unit code="pcs" name="Штука" sym="шт." />
                <vatRate>none</vatRate>
                <dimensions>100/50.8/150</dimensions>
                <barcode>012485ab</barcode>
            </offer>

            <offer id="253" productId="155" quantity="20">
                <url>http://testbitrix.loc/catalog/textile/sheet_beige/</url>
                <price>200.00</price>
                <purchasePrice>175.00</purchasePrice>
                <categoryId>12</categoryId>
                <picture>http://testbitrix.loc/upload/iblock/be7/be7139e39cda62e8c032f3b2ed0106e4.JPG</picture>
                <name>Ткань льняная бежевая</name>
                <xmlId>66</xmlId>
                <productName>Ткань льняная бежевая</productName>
                <param name="Артикул" code="article">151642</param>
                <param name="Ширина" code="width">150</param>
                <param name="Цвет" code="color">бежевый</param>
                <unit code="meter" name="Метр" sym="м" />
                <vatRate>10</vatRate>
                <weight>2.05</weight>
            </offer>

            <offer id="56" productId="56" quantity="30">
                <productActivity>N</productActivity>
                <url>http://testbitrix.loc/catalog/summer_collection/rocker/</url>
                <price>4250.00</price>
                <categoryId>9</categoryId>
                <picture>http://testbitrix.loc/upload/iblock/68b/68b955690e0f1f9dacb96cc4248e9c44.jpg</picture>
                <name>Кресло-качалка</name>
                <xmlId>104</xmlId>
                <productName>Кресло-качалка</productName>
                <param name="Артикул" code="article">891081</param>
                <vendor>Riotto</vendor>
                <unit code="pcs" name="Штука" sym="шт." />
            </offer>

        </offers>
    </shop>
</yml_catalog>
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