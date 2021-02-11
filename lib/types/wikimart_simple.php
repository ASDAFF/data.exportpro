<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["wikimart_simple"] = array(
	"CODE" => "wikimart_simple",
    "NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_NAME" ),
	"DESCRIPTION" => GetMessage( "DATA_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_ID" ),
            "VALUE" => "ID",
			"REQUIRED" => "Y",
            "TYPE" => "field",
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_AVAILABLE" ),
			"VALUE" => "",
            "TYPE" => "const",
            "CONDITION" => array(
                "CLASS_ID" => "CondGroup",
                "DATA" => array(
                    "All" => "AND",
                    "True" => "True"
                ),
                "CHILDREN" => array(
                    array(
                        "CLASS_ID" => "CondCatQuantity",
                        "DATA" => array(
                                "logic" => "EqGr",
                                "value" => "1"
                        )
                    )
                )
            ),
            "USE_CONDITION" => "Y",
            "CONTVALUE_TRUE" => "true",
            "CONTVALUE_FALSE" => "false",
		),
		array(
			"CODE" => "BID",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_BID" ),
			"VALUE" => "",
		),
		array(
			"CODE" => "URL",
			"NAME" => "URL ".GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_URL" ),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
		),
		array(
			"CODE" => "PRICE",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_PRICE" ),
			"REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_CURRENCY" ),
			"REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_CATEGORY" ),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => "Y",
            "TYPE" => "field",
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_PICTURE" ),
		),
        array(
			"CODE" => "STORE",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_STORE" ),
		),
        array(
			"CODE" => "NAME",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_NAME" ),
			"VALUE" => "NAME",
            "TYPE" => "field",
		),
        array(
			"CODE" => "VENDOR",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_VENDOR" ),
		),
		array(
			"CODE" => "VENDORCODE",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_VENDORCODE" ),
		),
		
		array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_DESCRIPTION" ),
		),
		array(
			"CODE" => "ADULT",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_ADULT" ),
		),
        array(
			"CODE" => "AGE",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_AGE" ),
		),
		array(
			"CODE" => "CPA",
			"NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_CPA" ),
		),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
	),
	"FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="#DATE#">
    <shop>
        <name>#SHOP_NAME#</name>
        <company>#COMPANY_NAME#</company>
        <url>#SITE_URL#</url>
        <currencies>#CURRENCY#</currencies>
        <categories>#CATEGORY#</categories>
        <offers>
            #ITEMS#
        </offers>
    </shop>
</yml_catalog>',
    
	"DATEFORMAT" => "Y-m-d_h:i",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes["wikimart_simple"]["FIELDS"][4] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage( "DATA_EXPORTPRO_WIKIMART_SIMPLE_FIELD_PRICE" ),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["wikimart_simple"]["PORTAL_REQUIREMENTS"] = GetMessage( "DATA_EXPORTPRO_TYPE_WIKIMART_SIMPLE_PORTAL_REQUIREMENTS" );
$profileTypes["wikimart_simple"]["EXAMPLE"] = GetMessage( "DATA_EXPORTPRO_TYPE_WIKIMART_SIMPLE_EXAMPLE" );

$profileTypes["wikimart_simple"]["CURRENCIES"] =
    "<currency id='#CURRENCY#' rate='#RATE#' />" . PHP_EOL;

$profileTypes["wikimart_simple"]["SECTIONS"] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes["wikimart_simple"]["ITEMS_FORMAT"] = "
<offer id=\"#ID#\" available=\"#AVAILABLE#\" bid=\"#BID#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <market_category>#MARKET_CATEGORY#</market_category>
    <picture>#SITE_URL##PICTURE#</picture>
    <store>#STORE#</store>
    <name>#NAME#</name>
    <vendor>#VENDOR#</vendor>
    <vendorCode>#VENDORCODE#</vendorCode>
    <description>#DESCRIPTION#</description>
    <adult>#ADULT#</adult>
    <age>#AGE#</age>
    <cpa>#CPA#</cpa>
</offer>
";
    
$profileTypes["wikimart_simple"]["LOCATION"] = array(
	"wikimart" => array(
		"name" => GetMessage( "DATA_EXPORTPRO_WIKIMART" ),
		"sub" => array(
		)
	),
);