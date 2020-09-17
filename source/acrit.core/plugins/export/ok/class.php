<?
/**
 * Acrit Core: Ok.ru plugin
 * @documentation https://apiok.ru/dev/methods/rest/market/
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Plugins\OdnoklassnikiSDK as OkSDK;

Loc::loadMessages(__FILE__);

class Ok extends Plugin {

	CONST DATE_UPDATED = '2019-06-27';

	protected $strFileExt;
	#protected static $intProfileID = 0;
	#protected static $strGroupID = "";
	protected $strGroupId;


	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}

	/* START OF BASE STATIC METHODS */

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return 'OK';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/**
	 *	Include classes
	 */
	public function includeClasses(){
		#require_once(__DIR__.'/../vk/lib/json.php');
		require_once(__DIR__.'/lib/odnoklassnikisdk.php');
	}

	/**
	 *	Get list of supported currencies
	 */
	public function getSupportedCurrencies(){
		return array('RUB');
	}

	/* END OF BASE STATIC METHODS */

	/**
	 *	Set available extension
	 */
	protected function setAvailableExtension($strExtension){
		$this->strFileExt = $strExtension;
	}

	/**
	 *	Show plugin settings
	 */
	public function showSettings(){
		$this->setAvailableExtension('xml');
		return $this->showDefaultSettings();
	}

	/**
	 *	Get EXPORT_FILE_NAME
	 */
	public function getExportFileName(){
		if(strlen($this->arProfile['PARAMS']['GROUP_LINK'])){
			return 'https://ok.ru/'.$this->arProfile['PARAMS']['GROUP_LINK'].'/market';
		}
		elseif(strlen($this->arProfile['PARAMS']['GROUP_ID'])){
			return 'https://ok.ru/group/'.$this->arProfile['PARAMS']['GROUP_ID'].'/market';
		}
		return false;
	}

	/**
	 *	Get EXPORT_FILE_NAME
	 */
	public function initConnection($intProfileID) {
		OkSDK::init(
			$this->arProfile['PARAMS']['APP_ID'],
			$this->arProfile['PARAMS']['APP_PUB_KEY'],
			$this->arProfile['PARAMS']['APP_SEC_KEY'],
			$this->arProfile['PARAMS']['ACCESS_TOKEN']
		);
	}

	/**
	 *	Show plugin default settings
	 */
	protected function showDefaultSettings(){
		ob_start();
			?>
			<table class="acrit-exp-plugin-settings" style="width:100%;">
				<tbody>
                    <tr class="heading" id="tr_HEADING_SYSTEM"><td colspan="2"><?=static::getMessage('SETTINGS_APP_DATA_TITLE');?></td></tr>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('SETTINGS_APP_ID_HINT'));?>
							<?=static::getMessage('SETTINGS_APP_ID');?>:
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input type="text" name="PROFILE[PARAMS][APP_ID]" id="acrit_exp_plugin_ok_app_id" value="<?=$this->arProfile['PARAMS']['APP_ID'];?>" size="90" />
						</td>
					</tr>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('SETTINGS_APP_PUB_KEY_HINT'));?>
							<?=static::getMessage('SETTINGS_APP_PUB_KEY');?>:
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input type="text" name="PROFILE[PARAMS][APP_PUB_KEY]" id="acrit_exp_plugin_ok_app_pub_key" value="<?=$this->arProfile['PARAMS']['APP_PUB_KEY'];?>" size="90" />
						</td>
					</tr>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('SETTINGS_APP_SEC_KEY_HINT'));?>
							<?=static::getMessage('SETTINGS_APP_SEC_KEY');?>:
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input type="text" name="PROFILE[PARAMS][APP_SEC_KEY]" id="acrit_exp_plugin_ok_app_sec_key" value="<?=$this->arProfile['PARAMS']['APP_SEC_KEY'];?>" size="90" />
						</td>
					</tr>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('SETTINGS_ACCESS_TOKEN_HINT'));?>
							<?=static::getMessage('SETTINGS_ACCESS_TOKEN');?>:
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input type="text" name="PROFILE[PARAMS][ACCESS_TOKEN]" id="acrit_exp_plugin_ok_access_token" value="<?=$this->arProfile['PARAMS']['ACCESS_TOKEN'];?>" size="90" />
						</td>
					</tr>
                    <tr class="heading" id="tr_HEADING_SYSTEM"><td colspan="2"><?=static::getMessage('SETTINGS_PROCESS_TITLE');?></td></tr>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('SETTINGS_GROUP_ID_HINT'));?>
							<?=static::getMessage('SETTINGS_GROUP_ID');?>:
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input type="text" name="PROFILE[PARAMS][GROUP_ID]" id="acrit_exp_plugin_ok_group_id" value="<?=$this->arProfile['PARAMS']['GROUP_ID'];?>" size="30" />
							<?if(strlen($this->arProfile['PARAMS']['GROUP_ID'])):?>
								&nbsp;
								<?=$this->showFileOpenLink($this->getExportFileName(), static::getMessage('SETTINGS_GROUP_ID_URL'));?>
							<?endif?>
						</td>
					</tr>
                    <tr>
						<td width="40%" class="adm-detail-content-cell-l">
							<?=Helper::ShowHint(static::getMessage('SETTINGS_GROUP_LINK_HINT'));?>
							<?=static::getMessage('SETTINGS_GROUP_LINK');?>:
						</td>
						<td width="60%" class="adm-detail-content-cell-r">
							<input type="text" name="PROFILE[PARAMS][GROUP_LINK]" id="acrit_exp_plugin_ok_group_link" value="<?=$this->arProfile['PARAMS']['GROUP_LINK'];?>" size="30" />
						</td>
					</tr>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l">
                            <?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_CREATE_CATALOGS_HINT'));?>
                            <?=static::getMessage('SETTINGS_PROCESS_CREATE_CATALOGS');?>:
                        </td>
                        <td width="60%" class="adm-detail-content-cell-r">
                            <input type="checkbox" name="PROFILE[PARAMS][PROCESS_CREATE_CATALOGS]" id="acrit_exp_plugin_ok_process_create_catalogs" value="Y"<?=$this->arProfile['PARAMS']['PROCESS_CREATE_CATALOGS']=='Y'?' checked':'';?> />
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l">
                            <?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_DELETE_OTHER_HINT'));?>
                            <?=static::getMessage('SETTINGS_PROCESS_DELETE_OTHER');?>:
                        </td>
                        <td width="60%" class="adm-detail-content-cell-r">
                            <input type="checkbox" name="PROFILE[PARAMS][PROCESS_DELETE_OTHER]" id="acrit_exp_plugin_ok_process_delete_other" value="Y"<?=$this->arProfile['PARAMS']['PROCESS_DELETE_OTHER']=='Y'?' checked':'';?> />
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l">
                            <?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_LIMIT_HINT'));?>
                            <?=static::getMessage('SETTINGS_PROCESS_LIMIT');?>:
                        </td>
                        <td width="60%" class="adm-detail-content-cell-r">
                            <input type="text" name="PROFILE[PARAMS][PROCESS_LIMIT]" id="acrit_exp_plugin_ok_process_run_limit" value="<?=$this->arProfile['PARAMS']['PROCESS_LIMIT']?$this->arProfile['PARAMS']['PROCESS_LIMIT']:0;?>" />
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l">
                            <?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_NEXT_POS_HINT'));?>
                            <?=static::getMessage('SETTINGS_PROCESS_NEXT_POS');?>:
                        </td>
                        <td width="60%" class="adm-detail-content-cell-r">
                            <span style="margin-right: 20px;" id="acrit_exp_plugin_ok_process_next_pos_view"><?=$this->arProfile['PARAMS']['PROCESS_NEXT_POS']?$this->arProfile['PARAMS']['PROCESS_NEXT_POS']:0;?></span>
                            <input type="hidden" name="PROFILE[PARAMS][PROCESS_NEXT_POS]" id="acrit_exp_plugin_ok_process_next_pos" value="<?=$this->arProfile['PARAMS']['PROCESS_NEXT_POS']?$this->arProfile['PARAMS']['PROCESS_NEXT_POS']:0;?>" />
                            <a href="#" class="adm-btn" id="acrit_exp_plugin_ok_process_next_pos_reset"><?=static::getMessage('SETTINGS_PROCESS_NEXT_POS_RESET');?></a>
                        </td>
                    </tr>
				</tbody>
			</table>
            <script>
                BX.message({'SETTINGS_PROCESS_NEXT_POS_RESET_ALERT': '<?=static::getMessage('SETTINGS_PROCESS_NEXT_POS_RESET_ALERT');?>'});
            </script>
			<?
		return ob_get_clean();
	}
	
	/**
	 *	Custom ajax actions
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult){
		#$intProfileID = &$arParams['PROFILE_ID'];
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		return array();
	}

	/**
	 *	Process single element
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
		// basically [in this class] do nothing, all business logic are in each format
	}

	/**
	 *	Show results
	 */
	public function showResults($arSession){
		ob_start();
		$intTime = $arSession['TIME_FINISHED']-$arSession['TIME_START'];
		if($intTime<=0){
			$intTime = 1;
		}
		?>
		<div><?=static::getMessage('RESULT_GENERATED');?>: <?=IntVal($arSession['GENERATE']['INDEX']);?></div>
		<div><?=static::getMessage('RESULT_EXPORTED');?>: <?=IntVal($arSession['EXPORT']['INDEX']);?></div>
		<div><?=static::getMessage('RESULT_ELAPSED_TIME');?>: <?=Helper::formatElapsedTime($intTime);?></div>
		<div><?=static::getMessage('RESULT_DATETIME');?>: <?=(new \Bitrix\Main\Type\DateTime())->toString();?></div>
		<?=$this->showFileOpenLink($this->getExportFileName(), static::getMessage('SETTINGS_GROUP_ID_URL'));?>
		<?
		return Helper::showSuccess(ob_get_clean());
	}

	/* START OF BASE METHODS FOR XML SUBCLASSES */

	/**
	 *	Send file to remote server
	 */
	protected function sendFileRemote($strFile, $strUrl) {
		Log::getInstance($this->strModuleId)->add('sendFileRemote is_file: '.is_file($strFile), $this->intProfileID);
		if(is_file($strFile)){
			$arFile = \CFile::MakeFileArray($strFile);
			Log::getInstance($this->strModuleId)->add('sendFileRemote $arFile: '.print_r($arFile, true), $this->intProfileID);
			if(is_array($arFile)){
				$strResult = HttpRequest::sendFile($arFile, $strUrl);
				Log::getInstance($this->strModuleId)->add('sendFileRemote $strResult: '.print_r($strResult, true), $this->intProfileID);
				usleep(350000);
				try {
					return Json::decode($strResult);
				}
				catch(\Exception $e){
				}
			}
		}
		return false;
	}

}

?>
