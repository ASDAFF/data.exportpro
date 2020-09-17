<?
use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="2019-12-02 15:15">
    <shop>
        <name>Моя компания</name>
        <company>Моя компания</company>
        <url>https://dinretest.nethouse.ru</url>
        <currencies>
            <currency id="RUR" rate="1"/>
        </currencies>
        <categories>
            <category id="3219324" >Без названия</category>
        </categories>
        <offers>
            <offer id="32444228" available="true">
                <url>https://dinretest.nethouse.ru/products/polypeptid</url>
                <price>2000</price>
                <currencyId>RUR</currencyId>
                <categoryId>3219324</categoryId>
                <picture>https://i.siteapi.org/__MTIGUnLuELLTiv3hmyWOlpsMk=/fit-in/1024x768/4b342c92c5ed468.s2.siteapi.org/img/rnla434gsb4ck8o4ockok0gk4gs444</picture>
                <name>Полипептид</name>
                <description>Дизоксирибонуклеиновый, цитоплазматический. Локусная трёхкарбоновая гибридизация центральных атомных орбиталей, приятный внешний вид.</description>
                <sales_notes>1000</sales_notes>
            </offer>
            <offer id="32444229" available="true">
                <url>https://dinretest.nethouse.ru/products/kotik</url>
                <price>4599</price>
                <oldprice>4900</oldprice>
                <currencyId>RUR</currencyId>
                <categoryId>3219324</categoryId>
                <picture>https://i.siteapi.org/7LQAuAiiBtl-qkcSdEU67xd0Yx0=/fit-in/1024x768/4b342c92c5ed468.s2.siteapi.org/img/rzz6zhiz4cgw408c0og0kc4sk800cg</picture>
                <name>Котик</name>
                <description>Маленький. Белый. Предоставляется бессрочная гарантия на пушистость. Обучен делать кусь. </description>
                <sales_notes>1000</sales_notes>
            </offer>
            <offer id="32444665" available="true">
                <url>https://dinretest.nethouse.ru/products/rocket</url>
                <price>1000</price>
                <currencyId>RUR</currencyId>
                <categoryId>3219324</categoryId>
                <picture>https://i.siteapi.org/WefrUwOCApgApOt4XpjTZy0WSsE=/fit-in/1024x768/4b342c92c5ed468.s2.siteapi.org/img/luynynmzt9ck0w0wk0ww448o4cgc80</picture>
                <name>Ракета союз-м</name>
                <description>Ракета союз-м. Пробег 768 800 км. Летала до луны, сейчас не ездим, хранится в тёплом боксе. ТО пройдены, документы оригинал. 
                Неудачных запусков не было. Первая и вторые ступени новые, ещё не отбегали. Отдаю бесплатно. Илону Маску просьба не беспокоить.</description>
                <sales_notes>1000</sales_notes>
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