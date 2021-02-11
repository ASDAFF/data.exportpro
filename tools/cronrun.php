<?php
$profileId = intval( $argv[1] );
$documentRoot = $argv[2];
global $cronpage;
$cronpage = $argv[3];
$_REQUEST["unlock"] = "Y";
set_time_limit( 0 );


$_SERVER["DOCUMENT_ROOT"] = $DOCUMENT_ROOT = $documentRoot;
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php" );

CModule::IncludeModule( "data.exportpro" );
DataExportproSession::Init( 0 );
DataExportproSession::DeleteSession( $profileId );
CExportproCron::StartExport( $profileId );