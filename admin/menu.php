<?php

IncludeModuleLangFile( __FILE__ );

if( $APPLICATION->GetGroupRight( "data.exportpro" ) != "D" ){
	$aMenu = array(
		"parent_menu" => "global_menu_data",
		"section" => GetMessage( "DATA_EXPORTPRO_SECTION" ),
		"sort" => 100,
		"text" => GetMessage( "DATA_EXPORTPRO_SECTION" ),
		"title" => GetMessage( "DATA_EXPORTPRO_MENU_TEXT" ),
		"url" => "",
		"icon" => "data_exportpro_menu_icon",
		"page_icon" => "",
		"items_id" => "menu_data.exportpro",
		"items" => array(
			array(
				"text" => GetMessage( "DATA_EXPORTPRO_MENU_TITLE" ),
				"url" => "data_exportpro_list.php?lang=".LANGUAGE_ID,
				"more_url" => array(
                    "data_exportpro_list.php",
                    "data_exportpro_edit.php"
                ),
				"title" => GetMessage( "DATA_EXPORTPRO_MENU_TITLE" ),
			),
			array(
				"text" => GetMessage( "DATA_EXPORTPRO_MENU_PROFILE_EXPORT" ),
				"url" => "data_exportpro_export.php",
				"more_url" => array( "data_exportpro_export.php" ),
				"title" => GetMessage( "DATA_EXPORTPRO_MENU_PROFILE_EXPORT" )
			),
		)
	);
	return $aMenu;
}
return false;