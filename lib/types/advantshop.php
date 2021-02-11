<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["advantshop"] = array(
    "CODE" => "advantshop",
    "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_NAME" ),
    "DESCRIPTION" => GetMessage( "DATA_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
    "REG" => "http://www.advantshop.net/",
    "HELP" => "http://www.advantshop.net/help/pages/export-csv-columns?name=csv",
    "FIELDS" => array(
        array(
            "CODE" => "ARTICUL",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_ARTICUL" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "NAME",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_NAME" ),
            "VALUE" => "NAME",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "CODE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_CODE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "CATEGORY",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_CATEGORY" ),
            "VALUE" => "IBLOCK_SECTION_ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "ENABLED",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_ENABLED" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "CURRENCY",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_CURRENCY" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB"
        ),
        array(
            "CODE" => "PRICE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_PRICE" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "PURCHASE_PRICE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_PURCHASE_PRICE" ),
        ),
        array(
            "CODE" => "AMOUNT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_AMOUNT" ),
        ),
        array(
            "CODE" => "SKU_VENDOR_CODE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_SKU_VENDOR_CODE" ),
        ),
        array(
            "CODE" => "SKU_SIZE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_SKU_SIZE" ),
        ),
        array(
            "CODE" => "SKU_COLOR",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_SKU_COLOR" ),
        ),
        array(
            "CODE" => "SKU_PRICE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_SKU_PRICE" ),
        ),
        array(
            "CODE" => "SKU_PURCHASEPRICE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_SKU_PURCHASEPRICE" ),
        ),
        array(
            "CODE" => "SKU_AMOUNT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_SKU_AMOUNT" ),
        ),        
        array(
            "CODE" => "UNIT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_UNIT" ),
        ),
        array(
            "CODE" => "DISCOUNT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_DISCOUNT" ),
        ),
        array(
            "CODE" => "SHIPPINGPRICE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_SHIPPINGPRICE" ),
        ),
        array(
            "CODE" => "WEIGHT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_WEIGHT" ),
        ),
        array(
            "CODE" => "WIDTH",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_WIDTH" ),
        ),
        array(
            "CODE" => "HEIGHT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_HEIGHT" ),
        ),
        array(
            "CODE" => "LENGHT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_LENGHT" ),
        ),
        array(
            "CODE" => "BRIEFDESCRIPTION",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_BRIEFDESCRIPTION" ),
        ),
        array(
            "CODE" => "DESCRIPTION",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_DESCRIPTION" ),
        ),
        array(
            "CODE" => "TITLE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_TITLE" ),
        ),
        array(
            "CODE" => "METAKEYWORDS",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_METAKEYWORDS" ),
        ),
        array(
            "CODE" => "METADESCRIPTION",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_METADESCRIPTION" ),
        ),
        array(
            "CODE" => "PHOTOS",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_PHOTOS" ),
        ),
        array(
            "CODE" => "MARKERS_NEWS",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_MARKERS_NEWS" ),
        ),
        array(
            "CODE" => "MARKERS_HITS",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_MARKERS_HITS" ),
        ),
        array(
            "CODE" => "MARKERS_DISCOUNT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_MARKERS_DISCOUNT" ),
        ),
        array(
            "CODE" => "PRODUCER",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_PRODUCER" ),
        ),
        array(
            "CODE" => "PREORDER",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_PREORDER" ),
        ),
        array(
            "CODE" => "SALESNOTE",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_SALESNOTE" ),
        ),
        array(
            "CODE" => "RELATED_SKU",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_RELATED_SKU" ),
        ),
        array(
            "CODE" => "ALTERNATIVE_SKU",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_ALTERNATIVE_SKU" ),
        ),
        array(
            "CODE" => "CUSTOMOPTION",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_CUSTOMOPTION" ),
        ),
        array(
            "CODE" => "GTIN",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_GTIN" ),
        ),
        array(
            "CODE" => "GOOGLEPRODUCTCATEGORY",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_GOOGLEPRODUCTCATEGORY" ),
        ),
        array(
            "CODE" => "ADULT",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_ADULT" ),
        ),
        array(
            "CODE" => "MANUFACTURER_WARRANTY",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_MANUFACTURER_WARRANTY" ),
        ),
        array(
            "CODE" => "TAGS",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_TAGS" ),
        ),
        array(
            "CODE" => "GIFTS",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_GIFTS" ),
        ),
        array(
            "CODE" => "PRODUCTSETS",
            "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_PRODUCTSETS" ),
        ),
    ),
);

$profileTypes["advantshop"]["PORTAL_REQUIREMENTS"] = GetMessage( "DATA_EXPORTPRO_TYPE_ADVANTSHOP_PORTAL_REQUIREMENTS" );

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes["advantshop"]["FIELDS"][6] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage( "DATA_EXPORTPRO_ADVANTSHOP_FIELD_PRICE" ),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}