<?php
IncludeModuleLangFile(__FILE__);
$profileTypes["1c_trade"] = array(
    "CODE" => "1c_trade",
    "NAME" => GetMessage( "DATA_EXPORTPRO_1C_TRADE_NAME" ),
    "DESCRIPTION" => GetMessage( "DATA_EXPORTPRO_1C_TRADE_DESCRIPTION" ),
    "DATEFORMAT" => "c",
    "ENCODING" => "utf8",
    "EXAMPLE" => GetMessage( "DATA_EXPORTPRO_1C_TRADE_EXAMPLE" ),
    "NAMESCHEMA" => array( "CATALOG_QUANTITY" => "CATALOG_QUANTITY_SKU" )
);