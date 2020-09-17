<?
/**
 * Acrit Core: Wildberries plugin
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Log,
	\Acrit\Core\Json;

Loc::loadMessages(__FILE__);

class Wildberries extends Plugin {

    CONST DATE_UPDATED = '2019-05-01';

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
		return 'WILDBERRIES';
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
	 *	Get WB fields for identification
	 */

	public function getWBIDFields($intOrderId) {
		$arList = [];
		if ($intOrderId) {
			$arOrderRes = $this->request('new/' . $intOrderId);
			foreach ($arOrderRes['Data'][0]['Fields'] as $arItem) {
				$arList[$arItem['Id']] = $arItem['Name'].' ('.$arItem['Id'].')';
			}
		}
		return $arList;
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
					<?=Helper::ShowHint(static::getMessage('SETTINGS_TOKEN_HINT'));?>
					<?=static::getMessage('SETTINGS_TOKEN');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][TOKEN]" id="acrit_exp_plugin_wildberries_token" value="<?=$this->arProfile['PARAMS']['TOKEN'];?>" size="90" />
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_ORDER_HINT'));?>
					<?=static::getMessage('SETTINGS_ORDER');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][ORDER]" id="acrit_exp_plugin_wildberries_order" value="<?=$this->arProfile['PARAMS']['ORDER'];?>" size="90" />
				</td>
			</tr>
            <?if ($this->arProfile['PARAMS']['ORDER']):?>
            <?
            $arFList = $this->getWBIDFields($this->arProfile['PARAMS']['ORDER']);
            ?>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(static::getMessage('SETTINGS_WB_ID_HINT'));?>
					<?=static::getMessage('SETTINGS_WB_ID');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
					<select name="PROFILE[PARAMS][WB_ID]">
                        <?foreach ($arFList as $id => $name):?>
                        <option value="<?=$id;?>"<?=$this->arProfile['PARAMS']['WB_ID']==$id?' selected':''?>><?=$name;?></option>
                        <?endforeach;?>
                    </select>
				</td>
			</tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
                    <?=Helper::ShowHint(static::getMessage('SETTINGS_WB_DICT_LOAD_HINT'));?>
                    <?=static::getMessage('SETTINGS_WB_DICT_LOAD');?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="checkbox" name="PROFILE[PARAMS][WB_DICT_LOAD]" <?=$this->arProfile['PARAMS']['WB_DICT_LOAD']=='Y'?' checked':''?> value="Y" />
                </td>
            </tr>
            <?endif;?>
			</tbody>
		</table>
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


	protected function request($method, $params=[], $type='get', $json=false) {
		$result = false;
		$token = $this->arProfile['PARAMS']['TOKEN'];
		if ($token) {
			$curl        = curl_init();
			$url         = "https://specifications.wildberries.ru/api/v1/Specification/" . $method;
			$headers[]   = 'Content-Type: application/json';
			$headers[]   = "X-Supplier-Cert-Serial:" . $token;
			$curl_params = [
				CURLOPT_HEADER         => 0,
				CURLOPT_HTTPHEADER     => $headers,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL            => $url,
			];
			$query_data  = http_build_query($params);
			if ($type == 'post') {
				$curl_params[CURLOPT_POST]       = 1;
				$curl_params[CURLOPT_POSTFIELDS] = $query_data;
			} else {
				$curl_params[CURLOPT_URL] = $url . '?' . $query_data;
			}
			if ($json) {
				$curl_params[CURLOPT_POST]       = 1;
				$curl_params[CURLOPT_POSTFIELDS] = $json;
			}
			curl_setopt_array($curl, $curl_params);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			$response = curl_exec($curl);
			curl_close($curl);
			$result = json_decode($response, true);
		}
		return $result;
	}

}

?>
