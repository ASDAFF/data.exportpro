<?php
/**
 * Copyright (c) 15/9/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile( __FILE__ );

if( $APPLICATION->GetGroupRight( "kit.exportpro" ) != "D" ){
	$aMenu = array(
		"parent_menu" => "global_menu_kit",
		"section" => GetMessage( "ACRIT_EXPORTPRO_SECTION" ),
		"sort" => 100,
		"text" => GetMessage( "ACRIT_EXPORTPRO_SECTION" ),
		"title" => GetMessage( "ACRIT_EXPORTPRO_MENU_TEXT" ),
		"url" => "",
		"icon" => "kit_exportpro_menu_icon",
		"page_icon" => "",
		"items_id" => "menu_kit.exportpro",
		"items" => array(
			array(
				"text" => GetMessage( "ACRIT_EXPORTPRO_MENU_TITLE" ),
				"url" => "kit_exportpro_list.php?lang=".LANGUAGE_ID,
				"more_url" => array(
                    "kit_exportpro_list.php",
                    "kit_exportpro_edit.php"
                ),
				"title" => GetMessage( "ACRIT_EXPORTPRO_MENU_TITLE" ),
			),
			array(
				"text" => GetMessage( "ACRIT_EXPORTPRO_MENU_PROFILE_EXPORT" ),
				"url" => "kit_exportpro_export.php",
				"more_url" => array( "kit_exportpro_export.php" ),
				"title" => GetMessage( "ACRIT_EXPORTPRO_MENU_PROFILE_EXPORT" )
			),
		)
	);
	return $aMenu;
}
return false;