<?
IncludeModuleLangFile(__FILE__);

class acrit_core extends CModule{
	const MONITORING_EMAIL = 'admin@acrit.ru';
	const MODULE_ID = 'acrit.core';
	var $MODULE_ID = 'acrit.core';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function __construct(){
		require(__DIR__.'/version.php');
		if(is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)){
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}
		$this->MODULE_NAME = getMessage('ACRIT_CORE_MODULE_NAME');
		$this->MODULE_DESCRIPTION = getMessage('ACRIT_CORE_MODULE_DESC');
		$this->PARTNER_NAME = getMessage('ACRIT_CORE_PARTNER_NAME');
		$this->PARTNER_URI = getMessage('ACRIT_CORE_PARTNER_URI');
	}

	function DoInstall(){
		global $APPLICATION, $DB, $DBType;
		if($APPLICATION->GetGroupRight('main') < 'W'){
			return;
		}
		RegisterModule($this->MODULE_ID);
		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallEvents();
		$this->ResetDemo();
	}

	function DoUninstall(){
		global $APPLICATION, $DB, $DBType;
		if($APPLICATION->GetGroupRight('main') < 'W'){
			return;
		}
		$bCanUninstall = true;
		foreach(\Bitrix\Main\ModuleManager::getInstalledModules() as $strModuleId => $arModule){
			if(preg_match('#^acrit\.(.*?)$#i', $strModuleId) && $strModuleId != $this->MODULE_ID){
				$bCanUninstall = false;
				$GLOBALS['ACRIT_MODULE_OTHER_MODULES'][] = $strModuleId;
			}
		}
		$GLOBALS['ACRIT_MODULE_ID'] = $this->MODULE_ID;
		$GLOBALS['ACRIT_MODULE_NAME'] = $this->MODULE_NAME;
		if($_REQUEST['step'] < 2){
			if($bCanUninstall){
				$APPLICATION->IncludeAdminFile(getMessage('ACRIT_CORE_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/uninst_form.php');
			}
			else{
        $APPLICATION->IncludeAdminFile(getMessage('ACRIT_CORE_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/cannot_uninstall.php');
			}
		}
		elseif($_REQUEST['step'] == 2 && $bCanUninstall){
			$this->UnInstallEvents();
			$this->UnInstallDB();
			$this->UnInstallFiles();
			\CAdminNotify::DeleteByModule($this->MODULE_ID);
			UnRegisterModule($this->MODULE_ID);
			$this->ResetDemo();
			if(\Bitrix\Main\Loader::includeModule($this->MODULE_ID)){
				if(class_exists('Acrit\Core\Helper')){
					$bBitrixCloudMonitoring = Acrit\Core\Helper::startBitrixCloudMonitoring(self::MONITORING_EMAIL);
				}
				if(class_exists('Acrit\Core\Cli')){
					$strDefaultPhp = \Acrit\Core\Cli::getDefaultPhpPath();
					$strPhpPath = \Acrit\Core\Cli::getPhpPath();
					if(strlen($strPhpPath) && $strPhpPath != $strDefaultPhp){
						\Bitrix\Main\Config\Option::set($this->MODULE_ID, 'php_path', $strPhpPath);
					}
				}
			}
			$APPLICATION->IncludeAdminFile(getMessage('ACRIT_CORE_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/uninst_mail.php');
		}
	}

	function InstallDB($arParams = array()){
		return true;
	}

	function UninstallDB($arParams = array()){
		return true;
	}

	function InstallFiles($arParams = array()){
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/js', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/', true, true);
		return true;
	}

	function UnInstallFiles(){
		DeleteDirFilesEx('/bitrix/js/'.$this->MODULE_ID.'/');
		DeleteDirFilesEx('/bitrix/themes/.default/images/'.$this->MODULE_ID.'/');
		DeleteDirFilesEx('/bitrix/themes/.default/icons/'.$this->MODULE_ID.'/');
		@unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default/'.$this->MODULE_ID.'.css');
		DeleteDirFilesEx('/upload/'.$this->MODULE_ID.'/');
		return true;
	}

	function InstallEvents(){
		require __DIR__.'/_events.php';
		return true;
	}

	function UnInstallEvents(){
		require __DIR__.'/_unevents.php';
		return true;
	}
	
	function ResetDemo(){
		global $DB;
		$DB->Query("DELETE FROM `b_option` WHERE `MODULE_ID`='{$this->MODULE_ID}' AND `NAME`='~bsm_stop_date';");
		return true;
	}
		
}