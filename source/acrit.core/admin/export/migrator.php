<?
namespace Acrit\Core\Export;

use 
	\Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Migrator\Manager as MigratorManager;

// Core (part 1)
$strCoreId = 'acrit.core';
$strModuleId = $ModuleID = preg_replace('#^.*?/([a-z0-9]+)_([a-z0-9]+).*?$#', '$1.$2', $_SERVER['REQUEST_URI']);
$strModuleCode = preg_replace('#^(.*?)\.(.*?)$#', '$2', $strModuleId);
$strModuleUnderscore = preg_replace('#^(.*?)\.(.*?)$#', '$1_$2', $strModuleId);
define('ADMIN_MODULE_NAME', $strModuleId);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
IncludeModuleLangFile(__FILE__);

// Check rights
if($APPLICATION->GetGroupRight($strModuleId) == 'D'){
	$APPLICATION->authForm(Loc::getMessage('ACCESS_DENIED'));
}

//
\CJSCore::Init('jquery2');
\CAjax::Init();

// Input data
$arGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray();
$arPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->toArray();

// Page title
$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE');

// Core notice
if(!\Bitrix\Main\Loader::includeModule($strCoreId)){
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	?><div id="acrit-exp-core-notifier"><?
		print '<div style="margin-top:15px;"></div>';
		print \CAdminMessage::ShowMessage(array(
			'MESSAGE' => Loc::getMessage('ACRIT_EXP_CORE_NOTICE', [
				'#CORE_ID#' => $strCoreId,
				'#LANG#' => LANGUAGE_ID,
			]),
			'HTML' => true,
		));
	?></div><?
	$APPLICATION->SetTitle($strPageTitle);
	die();
}

// Module
\Bitrix\Main\Loader::includeModule($strModuleId);
$arPluginsPlain = Exporter::getInstance($strModuleId)->findPlugins(false);

// Set page title
$strPageTitle .= ' (&laquo;'.Helper::getModuleName($strModuleId).'&raquo;, '.$strModuleId.')';
$APPLICATION->SetTitle($strPageTitle);

// Start output
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strCoreId.'/install/demo.php');

// Text definitions for popup
ob_start();
?><script>
BX.message({
	// General
	ACRIT_EXP_POPUP_LOADING: '<?=Loc::getMessage('ACRIT_EXP_POPUP_LOADING');?>',
	ACRIT_EXP_POPUP_RESTORE_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_TITLE');?>',
	ACRIT_EXP_POPUP_RESTORE_SAVE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_SAVE');?>',
	ACRIT_EXP_POPUP_RESTORE_CLOSE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_CLOSE');?>',
	ACRIT_EXP_POPUP_RESTORE_WRONG_FILE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_WRONG_FILE');?>',
	ACRIT_EXP_POPUP_RESTORE_NO_FILE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_NO_FILE');?>',
	ACRIT_EXP_POPUP_RESTORE_RESTORE_ERROR: '<?=Loc::getMessage('ACRIT_EXP_POPUP_RESTORE_RESTORE_ERROR');?>'
});
</script><?
$strJs = ob_get_clean();
\Bitrix\Main\Page\Asset::GetInstance()->AddString($strJs, true, \Bitrix\Main\Page\AssetLocation::AFTER_CSS);

// Create migrator manager, get profiles
$obMigratorManager = new MigratorManager($strModuleId);
$arMigratableProfiles = $obMigratorManager->getMigratableProfiles();

// Get exists profiles
$arExistsProfiles = $obMigratorManager->getNewProfiles();
$arExternalMigratedIDs = array_map(function($arProfile){
	return $arProfile['EXTERNAL_ID'];
}, $arExistsProfiles);
$arExternalMigratedIDs = array_filter($arExternalMigratedIDs, function($strValue) {
	return $strValue !== '';
});
$arExternalMigratedIDs = array_reverse($arExternalMigratedIDs, true);

// Get site list
$arSites = Helper::getSitesList();
$arMigrateFromSites = array();
foreach($arMigratableProfiles as $key => $arProfile){
	if(!in_array($arProfile['LID'], $arMigratableProfiles)){
		$arMigrateFromSites[] = $arProfile['LID'];
	}
}
$arMigrateFromSites = array_unique($arMigrateFromSites);

//
$arPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->toArray();
$bSave = isset($arPost['save']) && strlen($arPost['save']);
if($bSave) {
	$arProfilesID = $arPost['profiles'];
	if(!is_array($arProfilesID)){
		$arProfilesID = array();
	}
	if(!empty($arProfilesID)){
		foreach($arProfilesID as $intProfileID){
			$arProfile = $arMigratableProfiles[$intProfileID];
			$arProfile = $obMigratorManager->compileProfileArray($arMigratableProfiles[$intProfileID]);
			if(is_array($arProfile) && !empty($arProfile)){
				$obMigratorManager->saveProfile($arProfile);
			}
		}
		LocalRedirect($_SERVER['REQUEST_URI']);
	}
}

// Prepare tab control
$arTabs = array();
$arTabs[] = array(
	'DIV' => 'general',
	'TAB' => Loc::getMessage('ACRIT_EXP_TAB_GENERAL_NAME'),
	'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_GENERAL_DESC'),
);

// Warning
print Helper::showNote(Loc::getMessage('ACRIT_EXP_NOTICE', [
	'#MODULE_UNDERSCORE#' => $strModuleUnderscore,
	'#LANGUAGE_ID#' => LANGUAGE_ID,
]));

// Start tab control
$strAdminFormName = 'AcritExpMigrator';
$obTabControl = new \CAdminTabControl($strAdminFormName, $arTabs);
$obTabControl->Begin();
$obTabControl->BeginNextTab();
//----------------------------------------------------------------------------------------------------------------------
?>

<form action="<?=POST_FORM_ACTION_URI;?>" method="post">

	<div class="acrit-exp-migrator" data-role="migrator">
		<?if(!empty($arMigratableProfiles)):?>
			<ul>
				<?foreach($arMigrateFromSites as $strSiteID):?>
					<li>
						<?if(count($arMigrateFromSites) > 1):?>
							<h4>
								<?=Loc::getMessage('ACRIT_EXP_SITE_PREFIX');?> <?=$arSites[$strSiteID]['NAME'];?>
								<?
								$arInfo = array();
								if(strlen($arSites[$strSiteID]['SERVER_NAME'])){
									$arInfo[] = $arSites[$strSiteID]['SERVER_NAME'];
								}
								$arInfo[] = $strSiteID;
								?>
								(<?=implode(', ', $arInfo);?>)
							</h4>
						<?endif?>
						<ul>
							<?foreach($arMigratableProfiles as $intProfileID => $arProfile):?>
								<?
								$strMigratedProfileExternalID = $obMigratorManager->compileExternalID($intProfileID);
								$intMigratedProfile = array_search($strMigratedProfileExternalID, $arExternalMigratedIDs);
								?>
								<?if($arProfile['LID'] == $strSiteID):?>
									<li>
										<label>
											<input type="checkbox" name="profiles[]" value="<?=$intProfileID;?>" />
											<span>
												<?=$arProfile['NAME'];?> [<?=$arProfile['CODE'];?>, ID=<?=$intProfileID;?>]
											</span>
											<?if($intMigratedProfile !== false):?>
											<span>
												<?=Loc::getMessage('ACRIT_EXP_ALREADY_MIGRATED', array(
													'#URL#' => '/bitrix/admin/'.$strModuleUnderscore.'_new_edit.php?ID='.$intMigratedProfile
														.'&lang='.LANGUAGE_ID,
													'#ID#' => $intMigratedProfile,
												));?>
											</span>
											<?endif?>
										</label>
									</li>
								<?endif?>
							<?endforeach?>
						</ul>
						<div class="select-control">
							<a href="#" data-role="select-all"><?=Loc::getMessage('ACRIT_EXP_SELECT_ALL');?></a>
							<a href="#" data-role="select-none"><?=Loc::getMessage('ACRIT_EXP_SELECT_NONE');?></a>
							<a href="#" data-role="select-invert"><?=Loc::getMessage('ACRIT_EXP_SELECT_INVERT');?></a>
						</div>
					</li>
				<?endforeach?>
			</ul>
		<?else:?>
			<p><?=Loc::getMessage('ACRIT_EXP_NO_MIGRATABLE_PROFILES');?></p>
		<?endif?>
	</div>
	<br/>

	<style>
		.acrit-exp-migrator ul {
			list-style:none;
			margin:0 0 0 20px;
			padding:0;
		}
		.acrit-exp-migrator ul li li {
			margin-bottom:3px;
		}
		.acrit-exp-migrator .select-control {
			margin:10px 0 0 20px;
			padding:2px 0 4px;
		}
		.acrit-exp-migrator .select-control a {
			border-bottom:1px dashed #2675d7;
			color:#2675d7;
			display:inline-block;
			line-height:110%;
			margin:0 4px 0 0;
			text-decoration:none;
		}
		.acrit-exp-migrator .select-control a:hover {
			border-bottom-color:transparent;
		}
	</style>
	<script>
	$('input[type=checkbox]', $('[data-role="migrator"]')).each(function(){
		BX.adminFormTools.modifyCheckbox(this);
	});
	$('[data-role="select-all"]', $('[data-role="migrator"]')).bind('click', function(e){
		e.preventDefault();
		$(this).closest('li').find('input[type=checkbox]').prop('checked', true);
	});
	$('[data-role="select-none"]', $('[data-role="migrator"]')).bind('click', function(e){
		e.preventDefault();
		$(this).closest('li').find('input[type=checkbox]').prop('checked', false);
	});
	$('[data-role="select-invert"]', $('[data-role="migrator"]')).bind('click', function(e){
		e.preventDefault();
		$(this).closest('li').find('input[type=checkbox]').each(function(){
			$(this).prop('checked', !$(this).prop('checked'));
		});
	});
	</script>

	<?
	//----------------------------------------------------------------------------------------------------------------------
	// End tab control
	$obTabControl->Buttons();
		if(!empty($arMigratableProfiles)){
			?><input type="submit" name="save" data-role="migrate-button" class="adm-btn-green" value="<?=GetMessage('ACRIT_EXP_MIGRATE_BUTTON');?>" /><?
		}
	$obTabControl->End();
	?>
	
</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>