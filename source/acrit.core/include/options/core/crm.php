<?
namespace Acrit\Core\Crm;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Controller::setModuleId($this->strModuleId);

Loc::loadMessages(__FILE__);

$crm_conn_site = Helper::getOption($this->strModuleId, 'crm_conn_site');
$crm_conn_portal = Helper::getOption($this->strModuleId, 'crm_conn_portal');
$crm_conn_app_id = Helper::getOption($this->strModuleId, 'crm_conn_app_id');
$crm_conn_secret = Helper::getOption($this->strModuleId, 'crm_conn_secret');

$arTabOptions = [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CRM_NAME'),
	'OPTIONS' => []
];
$arTabOptions['OPTIONS']['crm_conn_site'] = [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CRM_SITE'),
	'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_CRM_SITE_HINT'),
	'ATTR' => 'size="40" placeholder="https://site.ru"',
	'TYPE' => 'text',
];
$arTabOptions['OPTIONS']['crm_conn_portal'] = [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CRM_PORTAL'),
	'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_CRM_PORTAL_HINT'),
	'ATTR' => 'size="40" placeholder="https://portal.bitrix24.ru"',
	'TYPE' => 'text',
];
if ($crm_conn_site) {
	if ($crm_conn_portal) {
		if (!$crm_conn_app_id || !$crm_conn_secret) {
			$arTabOptions['OPTIONS']['crm_conn_app_create'] = [
				'NAME'          => Loc::getMessage('ACRIT_CORE_OPTION_CRM_APP_CREATE'),
				'HINT'          => Loc::getMessage('ACRIT_CORE_OPTION_CRM_APP_CREATE_HINT'),
				'CALLBACK_MAIN' => function ($obOptions, $arTab) {
					$crm_conn_portal = Helper::getOption($this->strModuleId, 'crm_conn_portal');
					$link        = $crm_conn_portal . '/marketplace/local/list/';
					echo '<a href="' . $link . '" target="_blank">'.Loc::getMessage('ACRIT_CORE_OPTION_CRM_APP_CREATE_BTN').'</a>';
				},
			];
			$arTabOptions['OPTIONS']['crm_conn_app_link'] = [
				'NAME'          => Loc::getMessage('ACRIT_CORE_OPTION_CRM_APP_LINK'),
				'HINT'          => Loc::getMessage('ACRIT_CORE_OPTION_CRM_APP_LINK_HINT'),
				'CALLBACK_MAIN' => function ($obOptions, $arTab) {
					$crm_conn_site = Helper::getOption($this->strModuleId, 'crm_conn_site');
					$app_handler = Controller::getAppHandler();
					$link        = $crm_conn_site . $app_handler;
					echo $link;
				},
			];
		}
		$arTabOptions['OPTIONS']['crm_conn_app_id'] = [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CRM_APP_ID'),
			'HINT' => '',
			'ATTR' => 'size="60"',
			'TYPE' => 'text',
		];
		$arTabOptions['OPTIONS']['crm_conn_secret'] = [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CRM_SECRET'),
			'HINT' => '',
			'ATTR' => 'size="60"',
			'TYPE' => 'text',
		];
		$app_info = Rest::getAppInfo();
		$auth_info = Rest::getAuthInfo();
		if ($app_info) {
			if (!$auth_info) {
				$arTabOptions['OPTIONS']['crm_conn_auth'] = [
					'NAME'          => Loc::getMessage('ACRIT_CORE_OPTION_CRM_AUTH'),
					'HINT'          => Loc::getMessage('ACRIT_CORE_OPTION_CRM_AUTH_HINT'),
					'CALLBACK_MAIN' => function ($obOptions, $arTab) {
						$link = Rest::getAuthLink();
						echo '<a href="' . $link . '" class="adm-btn adm-btn-save">'.Loc::getMessage('ACRIT_CORE_OPTION_CRM_AUTH_BTN').'</a>';
					},
				];
			}
			else {
//				$arTabOptions['OPTIONS']['crm_conn_reset'] = [
//					'NAME'          => Loc::getMessage('ACRIT_CORE_OPTION_CRM_RESET')',
//					'HINT'          => Loc::getMessage('ACRIT_CORE_OPTION_CRM_RESET_HINT'),
//					'CALLBACK_MAIN' => function ($obOptions, $arTab) {
//						echo '<a href="#" class="adm-btn">'.Loc::getMessage('ACRIT_CORE_OPTION_CRM_RESET_BTN').'</a>';
//					},
//				];
			}
		}
		// Other parameters
		if ($app_info && $auth_info) {
//			$arTabOptions['OPTIONS'][] = Loc::getMessage('ACRIT_CORE_OPTION_CRM_OTHER_NAME')';
			$arTabOptions['OPTIONS']['crm_orderid_field'] = [
				'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CRM_ORDERID_FIELD'),
				'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_CRM_ORDERID_FIELD_HINT'),
				'TYPE' => 'LIST',
				'CALLBACK_MAIN' => function($obOptions, $arTab){
					$list = CrmPortal::getCRMOrderIDFields();
					$crm_orderid_field = Helper::getOption($this->strModuleId, 'crm_orderid_field');
					?>
					<select name="crm_orderid_field">
						<?foreach ($list as $id => $name):?>
						<option value="<?=$id;?>"<?=$id==$crm_orderid_field?' selected':'';?>><?=$name;?></option>
						<?endforeach;?>
					</select>
					<?
				},
			];
		}
	}
}
return $arTabOptions;

?>