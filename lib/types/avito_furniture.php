<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["avito_furniture"] = array(
	"CODE" => "avito_furniture",
    "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_NAME" ),
	"DESCRIPTION" => GetMessage( "DATA_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "Id",
			"NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_ID" ),
            "VALUE" => "ID",
			"REQUIRED" => "Y",
            "TYPE" => "field",
		),
		array(
            "CODE" => "DateBegin",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_DATEBEGIN" ),
        ),
        array(
            "CODE" => "DateEnd",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_DATEEND" ),
        ),
        array(
            "CODE" => "AdStatus",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_ADSTATUS" ),
        ),
        array(
            "CODE" => "AllowEmail",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_ALLOWEMAIL" ),
        ),
        array(
            "CODE" => "ManagerName",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_MANAGERNAME" ),
        ),
        array(
            "CODE" => "ContactPhone",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_CONTACTPHONE" ),
        ),
        array(
            "CODE" => "Region",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_REGION" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "City",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_CITY" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "District",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_AVTO_FIELD_DISTRICT" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "Category",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_CATEGORY" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_CATEGORY_VALUE" ),
        ),
        array(
            "CODE" => "GoodsType",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_GOODSTYPE" ),
        ),
        array(
			"CODE" => "Title",
			"NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_TITLE" ),
		),
        array(
            "CODE" => "Description",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_DESCRIPTION" ),
        ),
        array(
            "CODE" => "Price",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_PRICE" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "Image",
            "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_IMAGE" ),
        ),
	),
	"FORMAT" => '<?xml version="1.0"?>
<Ads target="Avito.ru" formatVersion="1">
    #ITEMS#
</Ads>',
    
	"DATEFORMAT" => "Y-m-d",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes["avito_furniture"]["FIELDS"][14] = array(
        "CODE" => "Price",
        "NAME" => GetMessage( "DATA_EXPORTPRO_AVITO_FURNITURE_FIELD_PRICE" ),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["avito_furniture"]["PORTAL_REQUIREMENTS"] = GetMessage( "DATA_EXPORTPRO_TYPE_AVITO_FURNITURE_PORTAL_REQUIREMENTS" );
$profileTypes["avito_furniture"]["PORTAL_VALIDATOR"] = GetMessage( "DATA_EXPORTPRO_TYPE_AVITO_FURNITURE_PORTAL_VALIDATOR" );
$profileTypes["avito_furniture"]["EXAMPLE"] = GetMessage( "DATA_EXPORTPRO_TYPE_AVITO_FURNITURE_EXAMPLE" );

$profileTypes["avito_furniture"]["CURRENCIES"] = "";

$profileTypes["avito_furniture"]["SECTIONS"] = "";

$profileTypes["avito_furniture"]["ITEMS_FORMAT"] = "
<Ad>
    <Id>#Id#</Id>
    <DateBegin>#DateBegin#</DateBegin>
    <DateEnd>#DateEnd#</DateEnd>
    <AdStatus>#AdStatus#</AdStatus>
    <AllowEmail>#AllowEmail#</AllowEmail>
    <ManagerName>#ManagerName#</ManagerName>
    <ContactPhone>#ContactPhone#</ContactPhone>
    <Region>#Region#</Region>
    <City>#City#</City>
    <District>#District#</District>
    <Category>#Category#</Category>
    <GoodsType>#GoodsType#</GoodsType>
    <Title>#Title#</Title>
    <Description>#Description#</Description>
    <Price>#Price#</Price>
    <Images>
        <Image url=\"#SITE_URL##Image#\"></Image>
    </Images>
</Ad>
";
    
$profileTypes["avito_furniture"]["LOCATION"] = array(
	"avito" => array(
		"name" => GetMessage( "DATA_EXPORTPRO_AVITO" ),
		"sub" => array(
		)
	),
);