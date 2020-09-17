<?
use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<yml_catalog date="2019-09-19 10:57">
    <shop>
        <name>akrit</name>
        <company>akrittest</company>
        <platform>1�-�������: ���������� ������</platform>
        <version>19.0.250</version>
        <url>http://akrittest</url>
        <categories>
            <category id="14">������, ����� � ����������///����������///�����, ����� � ��������///�����</category>
            <category id="15">������, ����� � ����������///����������///�����, ����� � ��������///�����</category>
        </categories>
        <offers>
            <offer id="12346" bid="80">
                <name>���������� First FA-5300</name>
                <vendor>First</vendor>
                <vendorCode>A1234567B</vendorCode>
                <url>http://best.seller.ru/product_page.asp?pid=12348</url>
                <price>1490</price>
                <oldprice>1620</oldprice>
                <currencyId>RUR</currencyId>
                <categoryId>101</categoryId>
                <picture>http://best.seller.ru/img/large_12348.jpg</picture>
                <store>false</store>
                <pickup>true</pickup>
                <delivery>true</delivery>
                <delivery-options>
                  <option cost="300" days="0" order-before="12"/>
                </delivery-options>
                <description>
                <![CDATA[
                  <p>�������� ������� ��� ��������� ������� ������.</p>
                ]]>
                </description>
                <sales_notes>���������� ����������.</sales_notes>
                <manufacturer_warranty>true</manufacturer_warranty>
                <country_of_origin>������</country_of_origin>
                <barcode>0156789012</barcode>
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