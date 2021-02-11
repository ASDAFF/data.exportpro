<?php
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php" );

global $ID;
$ID = intval( $ID );
$moduleId =  "data.exportpro";
$POST_RIGHT = $APPLICATION->GetGroupRight( $moduleId );

if( $POST_RIGHT >= "R" ){
    CModule::IncludeModule( $moduleId );
    $dataExport = new CDataExportproExport( $ID );
    $dataExport->Export();
}

require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php" );