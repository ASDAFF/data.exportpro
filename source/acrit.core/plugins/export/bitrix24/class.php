<?
/**
 * Acrit Core: Bitrix24 plugin
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Plugins\Bitrix24Rest as BitrixRest;

Loc::loadMessages(__FILE__);

class Bitrix24 extends Plugin {

    CONST DATE_UPDATED = '2020-01-14';

	protected $strFileExt;

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
		return 'BITRIX24';
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
		require_once(__DIR__.'/lib/bitrix24rest.php');
	}

	/**
	 *	Get list of supported currencies
	 */
	public function getSupportedCurrencies(){
		return array('RUB');
	}

	/* END OF BASE STATIC METHODS */

	/**
	 *	Show plugin settings
	 */
	public function showSettings(){
		// Show settings
		return $this->showDefaultSettings();
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
	 *	Show plugin default settings
	 */
	protected function showDefaultSettings(){
		ob_start();
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_ACCESS_PORTAL_HINT'));?>
					<?=static::getMessage('SETTINGS_ACCESS_PORTAL');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][ACCESS_PORTAL]" id="acrit_exp_plugin_vk_access_portal" value="<?=$this->arProfile['PARAMS']['ACCESS_PORTAL'];?>" size="90" />
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_ACCESS_WEBHOOK_HINT'));?>
					<?=static::getMessage('SETTINGS_ACCESS_WEBHOOK');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][ACCESS_WEBHOOK]" id="acrit_exp_plugin_vk_access_webhook" value="<?=$this->arProfile['PARAMS']['ACCESS_WEBHOOK'];?>" size="90" />
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_ACCESS_USER_ID_HINT'));?>
					<?=static::getMessage('SETTINGS_ACCESS_USER_ID');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][ACCESS_USER_ID]" id="acrit_exp_plugin_vk_access_user_id" value="<?=$this->arProfile['PARAMS']['ACCESS_USER_ID'];?>" size="90" />
				</td>
			</tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_LIMIT_HINT'));?>
					<?=static::getMessage('SETTINGS_PROCESS_LIMIT');?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[PARAMS][PROCESS_LIMIT]" id="acrit_exp_plugin_vk_process_run_limit" value="<?=$this->arProfile['PARAMS']['PROCESS_LIMIT']?$this->arProfile['PARAMS']['PROCESS_LIMIT']:0;?>" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_PROCESS_NEXT_POS_HINT'));?>
					<?=static::getMessage('SETTINGS_PROCESS_NEXT_POS');?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <span style="margin-right: 20px;" id="acrit_exp_plugin_vk_process_next_pos_view"><?=$this->arProfile['PARAMS']['PROCESS_NEXT_POS']?$this->arProfile['PARAMS']['PROCESS_NEXT_POS']:0;?></span>
                    <input type="hidden" name="PROFILE[PARAMS][PROCESS_NEXT_POS]" id="acrit_exp_plugin_vk_process_next_pos" value="0" />
                    <a href="#" class="adm-btn" id="acrit_exp_plugin_vk_process_next_pos_reset"><?=static::getMessage('SETTINGS_PROCESS_NEXT_POS_RESET');?></a>
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
		$intProfileID = &$arParams['PROFILE_ID'];
//		switch($strAction){
//			case 'rest_test':
//				$arFilter = [
//					'order' => ["SORT" => "ASC"],
//					'filter' => [],
//				];
//				$arJsonResult = BitrixRest::executeMethod('crm.product.fields', $arFilter, $intProfileID, false);
//				break;
//		}
	}
}

?>
