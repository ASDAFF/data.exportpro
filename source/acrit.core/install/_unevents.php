<?
use \Bitrix\Main\EventManager;

$strModuleId = 'acrit.core';

/*** CORE ***/

// Add menu section
EventManager::getInstance()->unRegisterEventHandler(
	'main',
	'OnBuildGlobalMenu',
	$strModuleId,
	'\Acrit\Core\EventHandler',
	'OnBuildGlobalMenu'
);

// Auto check updates
EventManager::getInstance()->unRegisterEventHandler(
	'main',
	'OnAfterEpilog',
	$strModuleId,
	'\Acrit\Core\EventHandler',
	'OnAfterEpilog'
);

// Update modules
EventManager::getInstance()->unRegisterEventHandler(
	'main',
	'OnModuleUpdate',
	$strModuleId,
	'\Acrit\Core\EventHandler',
	'OnModuleUpdate'
);

// Handler for GoogleTagManager
EventManager::getInstance()->unRegisterEventHandler(
	'main',
	'OnEndBufferContent',
	$strModuleId,
	'\Acrit\Core\GoogleTagManager',
	'OnEndBufferContent'
);

// Handler for DynamicRemarketing
EventManager::getInstance()->unRegisterEventHandler(
	'main',
	'OnEndBufferContent',
	$strModuleId,
	'\Acrit\Core\DynamicRemarketing',
	'OnEndBufferContent'
);

/*** EXPORT ***/

// Show 'Preview' on context panel in element edit page
EventManager::getInstance()->unRegisterEventHandler(
	'main',
	'OnAdminContextMenuShow',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnAdminContextMenuShow'
);

// Handler element save for autogenerate
EventManager::getInstance()->unRegisterEventHandler(
	'iblock',
	'OnAfterIBlockElementAdd',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnAfterIBlockElementAddUpdate'
);
EventManager::getInstance()->unRegisterEventHandler(
	'iblock',
	'OnAfterIBlockElementUpdate',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnAfterIBlockElementAddUpdate'
);

// Handler properties save
EventManager::getInstance()->unRegisterEventHandler(
	'iblock',
	'OnAfterIBlockElementSetPropertyValues',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnAfterIBlockElementSetPropertyValues'
);
EventManager::getInstance()->unRegisterEventHandler(
	'iblock',
	'OnAfterIBlockElementSetPropertyValuesEx',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnAfterIBlockElementSetPropertyValues'
);

//Handler for save element in admin page /bitrix/admin/iblock_element_edit.php (there is the redirect)
EventManager::getInstance()->unRegisterEventHandler(
	'main',
	'OnBeforeLocalRedirect',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnBeforeLocalRedirect'
);

// Handler for save product
EventManager::getInstance()->unRegisterEventHandler(
	'catalog',
	'OnProductAdd',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnProductAddUpdate'
);
EventManager::getInstance()->unRegisterEventHandler(
	'catalog',
	'OnProductUpdate',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnProductAddUpdate'
);

// Handler for save price
EventManager::getInstance()->unRegisterEventHandler(
	'catalog',
	'OnPriceAdd',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnPriceAddUpdate'
);
EventManager::getInstance()->unRegisterEventHandler(
	'catalog',
	'OnPriceUpdate',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnPriceAddUpdate'
);

// Handler for save product store data
EventManager::getInstance()->unRegisterEventHandler(
	'catalog',
	'OnStoreProductAdd',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnStoreProductAddUpdate'
);
EventManager::getInstance()->unRegisterEventHandler(
	'catalog',
	'OnStoreProductUpdate',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnStoreProductAddUpdate'
);

// Handler for delete element
EventManager::getInstance()->unRegisterEventHandler(
	'iblock',
	'OnAfterIBlockElementDelete',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnAfterIBlockElementDelete'
);

// Handler for epilog, process all queue
EventManager::getInstance()->unRegisterEventHandler(
	'main',
	'OnEpilog',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnEpilog'
);

// Handler section save for autogenerate
EventManager::getInstance()->unRegisterEventHandler(
	'iblock',
	'OnAfterIBlockSectionUpdate',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnAfterIBlockSectionUpdate'
);

// Handler iblock save for autogenerate
EventManager::getInstance()->unRegisterEventHandler(
	'iblock',
	'OnAfterIBlockUpdate',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnAfterIBlockUpdate'
);

// Handler iblock delete
EventManager::getInstance()->unRegisterEventHandler(
	'iblock',
	'OnAfterIBlockDelete',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnAfterIBlockDelete'
);

// Handler for add discount (for discount recalculation)
EventManager::getInstance()->unRegisterEventHandler(
	'sale',
	'DiscountOnAfterAdd',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'DiscountOnAfterAdd'
);

// Handler for update discount (for discount recalculation)
EventManager::getInstance()->unRegisterEventHandler(
	'sale',
	'DiscountOnAfterUpdate',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'DiscountOnAfterUpdate'
);

// Handler for delete discount (for discount recalculation)
EventManager::getInstance()->unRegisterEventHandler(
	'sale',
	'DiscountOnAfterDelete',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'DiscountOnAfterDelete'
);

// Handler for price add
EventManager::getInstance()->unRegisterEventHandler(
	'catalog',
	'OnGroupAdd',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnGroupAdd'
);

// Handler for price update
EventManager::getInstance()->unRegisterEventHandler(
	'catalog',
	'OnGroupUpdate',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnGroupUpdate'
);

// Handler for price delete
EventManager::getInstance()->unRegisterEventHandler(
	'catalog',
	'OnGroupDelete',
	$strModuleId,
	'\Acrit\Core\Export\EventHandlerExport',
	'OnGroupDelete'
);

?>