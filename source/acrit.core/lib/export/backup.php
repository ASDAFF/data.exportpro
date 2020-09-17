<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\ProfileIBlockTable as ProfileIBlock,
	\Acrit\Core\Export\ProfileFieldTable as ProfileField,
	\Acrit\Core\Export\ProfileValueTable as ProfileValue,
	\Acrit\Core\Export\AdditionalFieldTable as AdditionalField,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition,
	\Acrit\Core\Export\ExportDataTable as ExportData,
	\Acrit\Core\Export\HistoryTable as History;

Loc::loadMessages(__FILE__);

/**
 * Class Backup
 * @package Acrit\Core\Export
 */

abstract class Backup {
	
	const MODULE_ID = ''; // Must be overriden! Value must contain module id! Value cannot be null!
	
	CONST MYSQL_DATETIME = 'Y-m-d H:i:s';
	CONST FILE_EXT = 'txt';
	CONST AUTOBACKUP_COUNT = 30;
	
	CONST MODE_DEFAULT = 'default';
	CONST MODE_EXACT = 'exact';
	
	#static $strModuleId;
	
	/**
	 *	Set $strModuleId (statically)
	 */
	/*
	public static function setModuleId($strModuleId){
		static::MODULE_ID = $strModuleId;
	}
	*/
	
	/**
	 *	Get modes
	 */
	public static function getModes(){
		return array(
			static::MODE_DEFAULT => Loc::getMessage('ACRIT_EXP_BACKUP_MODE_'.ToUpper(static::MODE_DEFAULT)),
			static::MODE_EXACT => Loc::getMessage('ACRIT_EXP_BACKUP_MODE_'.ToUpper(static::MODE_EXACT)),
		);
	}
	
	/**
	 *	Get filename for backup file
	 */
	protected static function getBackupFilename($strSuffix=null){
		$strSuffix = (is_string($strSuffix) || is_numeric($strSuffix)) && strlen($strSuffix) ? $strSuffix : '';
		$strResult = static::MODULE_ID.'_backup_'.$strSuffix.'__'.date('Y-m-d_H-i-s').'.'.static::FILE_EXT;
		$strHost = Helper::getCurrentHost();
		$strHost = preg_replace('#^www\.#', '', $strHost);
		if(strlen($strHost)){
			$strResult = $strHost.'_'.$strResult;
		}
		return $strResult;
	}
	
	/**
	 *	Create temp file
	 */
	public static function createBackupFile($strID=false){
		$bAll = $strID == 'all';
		#
		$strTmpFile = Helper::getTmpDir().'/'.static::getBackupFilename($bAll ? '' : $strID);
		if(is_file($strTmpFile)){
			unlink($strTmpFile);
		}
		#
		$arID = array();
		if($bAll){
			$arQuery = [
				'select' => array(
					'ID',
				),
			];
			#$resProfiles = Profile::GetList($arQuery);
			$resProfiles = Helper::call(static::MODULE_ID, 'Profile', 'getList', [$arQuery]);
			while($arProfile = $resProfiles->fetch()) {
				$arID[] = $arProfile['ID'];
			}
		}
		else{
			$arID = explode(',', $strID);
			foreach($arID as $key => $value){
				$value = IntVal($value);
				if($value<=0) {
					unset($arID[$key]);
				}
			}
		}
		#
		if(!empty($arID)){
			$mProfiles = array();
			foreach($arID as $intProfileID){
				$arProfile = static::getProfileData($intProfileID);
				$mProfiles[] = $arProfile;
			}
			$mProfiles = serialize($mProfiles);
			if(!Helper::isUtf()){
				$mProfiles = Helper::convertEncoding($mProfiles, 'CP1251', 'UTF-8'); // Always in UTF-8 !!!
			}
			file_put_contents($strTmpFile, $mProfiles);
			unset($mProfiles, $arProfile);
		}
		return $strTmpFile;
	}
	
	/**
	 *	Archive tmp file to zip
	 */
	public static function fileToZip($strTmpFile){
		$strZipFile = Helper::changeFileExt($strTmpFile, 'zip');
		if(is_file($strZipFile)){
			unlink($strZipFile);
		}
		$obAchiver = \CBXArchive::GetArchive($strZipFile);
		$obAchiver->SetOptions(array(
			'REMOVE_PATH' => pathinfo($strTmpFile, PATHINFO_DIRNAME),
		));
		$arZipFiles = array(
			$strTmpFile,
		);
		$intResult = $obAchiver->Pack($arZipFiles);
		unset($obAchiver);
		return $strZipFile;
	}
	
	/**
	 *	Download file
	 */
	public static function downloadFile($strZipFile){
		if(is_file($strZipFile)) {
			$strFilename = sprintf('"%s"', addcslashes(basename($strZipFile), '"\\'));
			$intSize = filesize($strZipFile);
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$strFilename); 
			header('Content-Transfer-Encoding: binary');
			header('Connection: Keep-Alive');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: '.$intSize);
			readfile($strZipFile);
			return true;
		}
		return false;
	}
	
	/**
	 *	Copy loaded file, unzip it, get file contents and remove file
	 */
	public static function getZipContents($strFileTmp){
		$strTmpDir = Helper::getTmpDir();
		$strDestinationFile = $strTmpDir.'/restore.zip';
		if(is_file($strDestinationFile)){
			unlink($strDestinationFile);
		}
		if(move_uploaded_file($strFileTmp, $strDestinationFile)){
			$strContents = '';
			$obAchiver = \CBXArchive::GetArchive($strDestinationFile);
			$arFiles = $obAchiver->GetContent();
			if(count($arFiles) === 1){
				$strFilename = $arFiles[0]['filename'];
				$obAchiver->UnPack($strTmpDir);
				if(is_file($strTmpDir.'/'.$strFilename)){
					$strContents = file_get_contents($strTmpDir.'/'.$strFilename);
					@unlink($strTmpDir.'/'.$strFilename);
				}
			}
			@unlink($strDestinationFile);
			if(strlen($strContents)){
				return $strContents;
			}
		}
		return false;
	}
	
	/**
	 *	Restore from uploaded file
	 */
	public static function restoreFromBackupFile($strFileTmp, $strMode=self::MODE_DEFAULT){
		$strContents = static::getZipContents($strFileTmp);
		if(strlen($strContents)){
			if(!Helper::isUtf()){
				$strContents = Helper::convertEncoding($strContents, 'UTF-8', 'CP1251');
			}
			$arProfiles = unserialize($strContents);
			if(is_array($arProfiles)) {
				#
				$bExact = $strMode==static::MODE_EXACT;
				if($bExact){
					foreach($arProfiles as $arProfile){
						if(is_numeric($arProfile['ID'])){
							Helper::call(static::MODULE_ID, 'Profile', 'delete', [$arProfile['ID']]);
						}
					}
				}
				$bSuccess = true;
				foreach($arProfiles as $arProfile){
					if (!static::setProfileData($arProfile, $strMode)){
						$bSuccess = false;
					}
				}
				Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
				return $bSuccess;
				#
			}
		}
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
		return false;
	}
	
	/**
	 *	Get all profile data (profile params, iblock, fields, values, category redefinitions, additional fields)
	 */
	public static function getProfileData($intProfileID){
		$arResult = false;
		# Get profile
		$arQuery = [
			'filter' => array(
				'ID' => $intProfileID,
			),
		];
		#$resProfile = Profile::getList($arQuery);
		$resProfile = Helper::call(static::MODULE_ID, 'Profile', 'getList', [$arQuery]);
		if($arProfile = $resProfile->fetch()){
			# Transform datetimes
			static::arrayDatetimeToMySql($arProfile);
			# Get IBlocks
			$arProfile['IBLOCKS'] = array();
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
				),
			];
			#$resProfileIBlocks = ProfileIBlock::getList($arQuery);
			$resProfileIBlocks = Helper::call(static::MODULE_ID, 'ProfileIBlock', 'getList', [$arQuery]);
			while($arProfileIBlock = $resProfileIBlocks->fetch()){
				# Transform datetimes
				static::arrayDatetimeToMySql($arProfileIBlock);
				#
				unset($arProfileIBlock['PROFILE_ID']);
				#
				$intIBlockID = $arProfileIBlock['IBLOCK_ID'];
				# Get fields
				$arProfileIBlock['FIELDS'] = array();
				$arQuery = [
					'filter' => array(
						'PROFILE_ID' => $intProfileID,
						'IBLOCK_ID' => $intIBlockID,
					),
				];
				#$resProfileFields = ProfileField::getList($arQuery);
				$resProfileFields = Helper::call(static::MODULE_ID, 'ProfileField', 'getList', [$arQuery]);
				while($arProfileField = $resProfileFields->fetch()){
					$strFieldCode = $arProfileField['FIELD'];
					# Transform datetimes
					static::arrayDatetimeToMySql($arProfileField);
					#
					unset($arProfileField['PROFILE_ID']);
					# Get values
					$arProfileField['VALUES'] = array();
					$arQuery = [
						'filter' => array(
							'PROFILE_ID' => $intProfileID,
							'IBLOCK_ID' => $intIBlockID,
							'FIELD' => $strFieldCode,
						),
					];
					#$resProfileValues = ProfileValue::getList($arQuery);
					$resProfileValues = Helper::call(static::MODULE_ID, 'ProfileValue', 'getList', [$arQuery]);
					while($arProfileValue = $resProfileValues->fetch()){
						# Transform datetimes
						static::arrayDatetimeToMySql($arProfileValue);
						#
						unset($arProfileValue['PROFILE_ID']);
						#
						$arProfileField['VALUES'][] = $arProfileValue;
					}
					#
					$arProfileIBlock['FIELDS'][$strFieldCode] = $arProfileField;
				}
				# Get additional fields
				$arProfileIBlock['ADDITIONAL_FIELDS'] = array();
				$arQuery = [
					'filter' => array(
						'PROFILE_ID' => $intProfileID,
						'IBLOCK_ID' => $intIBlockID,
					),
				];
				#$resAdditionalFields = AdditionalField::getList($arQuery);
				$resAdditionalFields = Helper::call(static::MODULE_ID, 'AdditionalField', 'getList', [$arQuery]);
				while($arAdditionalField = $resAdditionalFields->fetch()){
					#
					unset($arAdditionalField['PROFILE_ID']);
					#
					$arProfileIBlock['ADDITIONAL_FIELDS'][$arAdditionalField['ID']] = $arAdditionalField;
				}
				# Get category redefinitions
				$arProfileIBlock['CATEGORY_REDEFINITIONS'] = array();
				$arQuery = [
					'filter' => array(
						'PROFILE_ID' => $intProfileID,
						'IBLOCK_ID' => $intIBlockID,
					),
				];
				#$resCategoryRedefinitions = CategoryRedefinition::getList($arQuery);
				$resCategoryRedefinitions = Helper::call(static::MODULE_ID, 'CategoryRedefinition', 'getList', [$arQuery]);
				while($arCategoryRedefinition = $resCategoryRedefinitions->fetch()){
					unset($arCategoryRedefinition['PROFILE_ID']);
					#
					$arProfileIBlock['CATEGORY_REDEFINITIONS'][$arCategoryRedefinition['ID']] = $arCategoryRedefinition;
				}
				#
				$arProfile['IBLOCKS'][$intIBlockID] = $arProfileIBlock;
			}
			#
			$arResult = $arProfile;
		}
		return $arResult;
	}
	
	/**
	 *	Set profile data (if profile $arProfile['ID'] exists, save to him, else create new [ => copy])
	 */
	public static function setProfileData($arProfile, $strMode=self::MODE_DEFAULT){
		$bExact = $strMode==static::MODE_EXACT;
		#
		$arProfileFields = $arProfile;
		if(!$bExact){
			unset($arProfileFields['ID']);
		}
		unset($arProfileFields['IBLOCKS']);
		unset($arProfileFields['LOCKED']);
		unset($arProfileFields['DATE_STARTED']);
		unset($arProfileFields['DATE_LOCKED']);
		unset($arProfileFields['SESSION']);
		static::arrayDatetimeFromMySql($arProfileFields);
		# Replace EXPORT_FILE_NAME if restoring from other export module
		$arParams = unserialize($arProfileFields['PARAMS']);
		if(is_array($arParams) && !empty($arParams)){
			if(stripos($arParams['EXPORT_FILE_NAME'], static::MODULE_ID) === false){ // if found substring, skip replacing!!
				$strFilename = &$arParams['EXPORT_FILE_NAME'];
				$strFilename = preg_replace('#acrit\.([a-z0-9]+)#', static::MODULE_ID, $strFilename);
				$arProfileFields['PARAMS'] = serialize($arParams);
			}
		}
		unset($arParams);
		#
		$obResult = Helper::call(static::MODULE_ID, 'Profile', 'add', [$arProfileFields]);
		if($obResult->isSuccess()){
			$intProfileID = $obResult->getID();
		}
		unset($arProfileFields);
		if($intProfileID) {
			# Import iblocks
			if(is_array($arProfile['IBLOCKS'])){
				foreach($arProfile['IBLOCKS'] as $intIBlockID => $arIBlock){
					$arProfileIBlock = $arIBlock;
					$arProfileIBlock['PROFILE_ID'] = $intProfileID;
					if(!$bExact){
						unset($arProfileIBlock['ID']);
					}
					unset($arProfileIBlock['FIELDS']);
					unset($arProfileIBlock['ADDITIONAL_FIELDS']);
					unset($arProfileIBlock['CATEGORY_REDEFINITIONS']);
					static::arrayDatetimeFromMySql($arProfileIBlock);
					$obResult = Helper::call(static::MODULE_ID, 'ProfileIBlock', 'add', [$arProfileIBlock]);
					if($obResult->isSuccess()){
						# Import additional fields
						$arAdditionalFields = array();
						if(is_array($arIBlock['ADDITIONAL_FIELDS'])){
							foreach($arIBlock['ADDITIONAL_FIELDS'] as $intAdditionalFieldID => $arAdditionalField){
								if(!$bExact){
									unset($arAdditionalField['ID']);
								}
								static::arrayDatetimeFromMySql($arAdditionalField);
								$arAdditionalField['PROFILE_ID'] = $intProfileID;
								$obResult = Helper::call(static::MODULE_ID, 'AdditionalField', 'add', [$arAdditionalField]);
								if($obResult->isSuccess()){
									$intAdditionalFieldNewID = $obResult->getID();
								}
								$arIBlock['ADDITIONAL_FIELDS'][$intAdditionalFieldID]['NEW_ID'] = $intAdditionalFieldNewID;
							}
						}
						# Import fields
						if(is_array($arIBlock['FIELDS'])){
							foreach($arIBlock['FIELDS'] as $intFieldID => $arField){
								$arProfileField = $arField;
								#
								if(!$bExact){
									$intAdditionalFieldID = AdditionalField::getIdFromCode($arProfileField['FIELD']);
									if($intAdditionalFieldID){
										$intAdditionalFieldNewID = $arIBlock['ADDITIONAL_FIELDS'][$intAdditionalFieldID]['NEW_ID'];
										$arProfileField['FIELD'] = AdditionalField::getFieldCode($intAdditionalFieldNewID);
									}
								}
								#
								$arProfileField['PROFILE_ID'] = $intProfileID;
								if(!$bExact){
									unset($arProfileField['ID']);
								}
								unset($arProfileField['VALUES']);
								static::arrayDatetimeFromMySql($arProfileField);
								$obResult = Helper::call(static::MODULE_ID, 'ProfileField', 'add', [$arProfileField]);
								if($obResult->isSuccess()){
									# Import values
									if(is_array($arField['VALUES'])){
										foreach($arField['VALUES'] as $arValue){
											$arProfileValue = $arValue;
											#
											if(!$bExact){
												$intAdditionalFieldID = AdditionalField::getIdFromCode($arProfileValue['FIELD']);
												if($intAdditionalFieldID){
													$intAdditionalFieldNewID = $arIBlock['ADDITIONAL_FIELDS'][$intAdditionalFieldID]['NEW_ID'];
													$arProfileValue['FIELD'] = AdditionalField::getFieldCode($intAdditionalFieldNewID);
												}
											}
											#
											$arProfileValue['PROFILE_ID'] = $intProfileID;
											unset($arProfileValue['ID']);
											static::arrayDatetimeFromMySql($arProfileValue);
											Helper::call(static::MODULE_ID, 'ProfileValue', 'add', [$arProfileValue]);
										}
									}
								}
							}
						}
						# Import category redefinitions
						if(is_array($arIBlock['CATEGORY_REDEFINITIONS'])){
							foreach($arIBlock['CATEGORY_REDEFINITIONS'] as $intCategoryRedefinitionID => $arCategoryRedefinition){
								if(!$bExact){
									unset($arCategoryRedefinition['ID']);
								}
								static::arrayDatetimeFromMySql($arCategoryRedefinition);
								$arCategoryRedefinition['PROFILE_ID'] = $intProfileID;
								Helper::call(static::MODULE_ID, 'CategoryRedefinition', 'add', [$arCategoryRedefinition]);
							}
						}
					}
				}
			}
			#
			return $intProfileID;
		}
		return false;
	}
	
	/**
	 *	Convert all Bitrix\Main\Type\DateTime to string with MySQL datetime format
	 */
	private static function arrayDatetimeToMySql(&$arData){
		foreach($arData as $key => $value){
			if(is_object($value) && get_class($value) == 'Bitrix\Main\Type\DateTime') {
				$arData[$key] = 'DATETIME <'.$value->format(static::MYSQL_DATETIME).'>';
			}
		}
	}
	
	/**
	 *	Convert all datetimes to Bitrix\Main\Type\DateTime
	 */
	private static function arrayDatetimeFromMySql(&$arData){
		$strPattern = '#^DATETIME <(.*?)>$#';
		foreach($arData as $key => $value){
			if(is_string($value) && preg_match($strPattern, $value, $arMatch)) {
				$arData[$key] = new \Bitrix\Main\Type\DateTime($arMatch[1], static::MYSQL_DATETIME);
			}
		}
	}
	
	/**
	 *	Trancate profiles table
	 */
	public static function deleteProfilesDataAll(){
		$arTables = array(
			#Profile::getTableName(),
			Helper::call(static::MODULE_ID, 'Profile', 'getTableName'),
			#ProfileIBlock::getTableName(),
			Helper::call(static::MODULE_ID, 'ProfileIBlock', 'getTableName'),
			#ProfileField::getTableName(),
			Helper::call(static::MODULE_ID, 'ProfileField', 'getTableName'),
			#ProfileValue::getTableName(),
			Helper::call(static::MODULE_ID, 'ProfileValue', 'getTableName'),
			#AdditionalField::getTableName(),
			Helper::call(static::MODULE_ID, 'AdditionalField', 'getTableName'),
			#CategoryRedefinition::getTableName(),
			Helper::call(static::MODULE_ID, 'CategoryRedefinition', 'getTableName'),
			#ExportData::getTableName(),
			Helper::call(static::MODULE_ID, 'ExportData', 'getTableName'),
			#History::getTableName(),
			Helper::call(static::MODULE_ID, 'History', 'getTableName'),
		);
		foreach($arTables as $strTable){
			\Bitrix\Main\Application::getConnection()->query("TRUNCATE `{$strTable}`;");
		}
		#Profile::clearProfilesCache();
		Helper::call(static::MODULE_ID, 'Profile', 'clearProfilesCache');
	}
	
	/**
	 *	Autobackup: get tmp dir
	 */
	protected static function getAutobackupDir($bAutoCreate=true){
		$strUploadDir = Helper::getOption('main', 'upload_dir');
		if(!strlen($strUploadDir)){
			$strUploadDir = 'upload';
		}
		$strResult = $_SERVER['DOCUMENT_ROOT'].'/'.$strUploadDir.'/'.static::MODULE_ID.'/'.'autobackup';
		if($bAutoCreate && !is_dir($strResult)){
			mkdir($strResult, BX_DIR_PERMISSIONS, true);
		}
		return $strResult;
	}
	
	/**
	 *	Automatically daily backup (by Agent)
	 */
	public static function autobackup($bForce=false){
		$bCanBackup = true;
		$strNewHash = static::getProfilesBackupHash();
		if(!$bForce) {
			$bCanBackup = $strNewHash != Helper::getOption(static::MODULE_ID, 'autobackup_hash');
		}
		if($bCanBackup) {
			$strDir = static::getAutobackupDir();
			$strTmpFile = static::createBackupFile('all');
			if(strlen($strTmpFile) && is_file($strTmpFile)) {
				$strZipFile = static::fileToZip($strTmpFile);
				if(is_file($strZipFile)){
					$strNewFile = $strDir.'/'.pathinfo($strZipFile, PATHINFO_BASENAME);
					rename($strZipFile, $strNewFile);
					Helper::setOption(static::MODULE_ID, 'autobackup_hash', $strNewHash);
				}
				@unlink($strTmpFile);
			}
			static::autobackupRemoveExcessFiles();
		}
		$strResult = get_called_class().'::'.__FUNCTION__.'();';
		return $strResult;
	}
	
	/**
	 *	Autobackup: remove excess backup files
	 */
	protected static function autobackupRemoveExcessFiles(){
		$strDir = static::getAutobackupDir();
		#
		$arBackupFiles = array();
		$resHandle = opendir($strDir);
		while ($strFile = readdir($resHandle)) {
			if($strFile != '.' && $strFile != '..') {
				$arBackupFiles[$strDir.'/'.$strFile] = filemtime($strDir.'/'.$strFile);
			}
		}
		closedir($resHandle);
		arsort($arBackupFiles);
		$intAutobackupCount = IntVal(Helper::getOption(static::MODULE_ID, 'autobackup_count'));
		if($intAutobackupCount <= 0) {
			$intAutobackupCount = static::AUTOBACKUP_COUNT;
		}
		$arExcessFiles = array_slice($arBackupFiles, $intAutobackupCount);
		foreach($arExcessFiles as $strFile => $intFilemtime){
			@unlink($strFile);
		}
		unset($arBackupFiles, $resHandle, $strDir, $strFile, $intFilemtime, $intAutobackupCount, $arExcessFiles);
	}
	
	/**
	 *
	 */
	protected static function getProfilesBackupHash(){
		#return MD5(serialize(Profile::getProfiles()));
		return MD5(serialize(Helper::call(static::MODULE_ID, 'Profile', 'getProfiles')));
	}

}
?>