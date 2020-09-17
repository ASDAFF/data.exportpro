<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter;

Loc::loadMessages(__FILE__);

# Execute
if($bExecute) {
	Exporter::getInstance($strModuleId)->startTime();
	Exporter::getInstance($strModuleId)->includeModules();
	$bCanContinue = true;
	if($arPost['first']=='true'){
		# Для первого хита проверяем блокировку
		if($arProfile['LOCKED']=='Y'){
			$arJsonResult['Error'] = $bError;
			$arJsonResult['ShowError'] = true;
			print Helper::showNote(Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_PROFILE_IS_LOCKED', array(
				'#DATETIME#' => $arProfile['DATE_LOCKED']->toString(),
			)));
			$bCanContinue = false;
		}
		#Profile::clearSession($intProfileID);
		Helper::call($strModuleId, 'Profile', 'clearSession', [$intProfileID]);
	}
	if($bCanContinue) {
		try{
			$mResult = Exporter::getInstance($strModuleId)->executeProfile($intProfileID);
		}
		catch(\Exception $obException){
			$APPLICATION->restartBuffer();
			Helper::P($obException->getMessage());
			if(is_a($obException, 'Bitrix\Main\DB\SqlQueryException')){
				Helper::P($obException->getQuery());
			}
			$arTrace = $obException->getTrace();
			foreach($arTrace as $key => $value){
				unset($arTrace[$key]['args']);
			}
			Helper::P($arTrace);
			$mResult = Exporter::RESULT_ERROR;
		}
		catch(\Throwable $obException){
			$APPLICATION->restartBuffer();
			Helper::P($obException->getMessage());
			$arTrace = $obException->getTrace();
			foreach($arTrace as $key => $value){
				unset($arTrace[$key]['args']);
			}
			Helper::P($arTrace);
			$mResult = Exporter::RESULT_ERROR;
		}
		$bSuccess = $mResult === Exporter::RESULT_SUCCESS;
		$bError = $mResult === Exporter::RESULT_ERROR;
		$bContinue = $mResult === Exporter::RESULT_CONTINUE;
	}
	else {
		$bSuccess = false;
		$bError = true;
		$bContinue = false;
	}
	#
	$arJsonResult['Success'] = $bSuccess;
	$arJsonResult['Error'] = $bError;
	$arJsonResult['Repeat'] = $bContinue;
}

# Get session
#$arProfile = Profile::getProfiles($ModuleID, $intProfileID);
$arProfile = Helper::call($strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
$arSession = unserialize($arProfile['SESSION']);
$arSession = is_array($arSession) ? $arSession : array();

# Display progress
if(!$bError) {
	print Exporter::getInstance($strModuleId)->showProgress($intProfileID, $arSession, $obPlugin);
}

if($bSuccess) {
	#Profile::clearSession($intProfileID);
	Helper::call($strModuleId, 'Profile', 'clearSession', [$intProfileID]);
}


?>
