<?

namespace Acrit\Core\Crm;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class CrmPortal {

	const PROPS_AVAILABLE = ['TEXT', 'TEXTAREA', 'CHECKBOX', 'RADIO', 'SELECT', 'DATE', 'FILE', 'MULTISELECT', 'LOCATION'];
	const SYNC_NONE = 0;
	const SYNC_STOC = 1;
	const SYNC_CTOS = 2;
	const SYNC_ALL = 3;
	const DATE_FORMAT_PORTAL = 'Y-m-d\TH:i:sO';
	const DATE_FORMAT_PORTAL_SHORT = 'Y-m-d';


	/**
	 * Data from portal
	 */

	// CRM products fields for storing of order ID
	public static function getCRMOrderIDFields() {
		$result = [
			'' => Loc::getMessage("CRM_PORTAL_CRM_ORDERID_FIELD_ORIGIN_ID"),
		];
		$list = Rest::getList('crm.deal.userfield.list');
		if (is_array($list) && !empty($list)) {
			$new_list = [];
			foreach ($list as $item) {
				if (in_array($item['USER_TYPE_ID'], ['string','double'])) {
					$new_list[] = $item;
				}
			}
			$req_count = ceil(count($new_list) / 50);
			for ($r=0; $r<$req_count; $r++) {
				$next = $r * 50;
				$list_part = [];
				for ($j=$next; $j<$next+50 && $j<count($new_list); $j++) {
					$list_part[] = $new_list[$j];
				}
				// Get name from lang info
				$req_list = [];
				foreach ($list_part as $i => $field) {
					$req_list[$i] = 'crm.deal.userfield.get' . '?' . http_build_query([
							'id' => $field['ID'],
						]);
				}
				$resp = Rest::execute('batch', [
					"halt"  => false,
					"cmd" => $req_list,
				]);
				if ($resp['result']) {
					foreach ($list_part as $i => $field) {
						$field_details = $resp['result'][$i];
						if ( ! empty($field_details)) {
							$result[$field_details['FIELD_NAME']] = $field_details['EDIT_FORM_LABEL']['ru'];
						}
					}
				}
			}
		}
		return $result;
	}

	// Deals directions
	public static function getDirections() {
		global $APPLICATION;
		$result = [
			0 => Loc::getMessage("CRM_PORTAL_MAIN_CATEGORY"),
		];
		$list = Rest::execute('crm.dealcategory.list', [
			'IS_LOCKED' => 'N',
		]);
		if (is_array($list)) {
			foreach ($list as $item) {
				$name = $item['NAME'];
				if (!Helper::isUtf()) {
					$name = \Bitrix\Main\Text\Encoding::convertEncoding($name, "UTF-8", "Windows-1251");
				}
				$result[$item['ID']] = $name;
			}
		}
		return $result;
	}

	// Portal users
	public static function getUsers() {
		$result = [];
		$params = [
			'sort' => 'LAST_NAME',
			'order' => 'asc',
			'FILTER' => [
				'ACTIVE' => 'Y',
				'USER_TYPE' => 'employee',
			],
		];
		$resp = Rest::getList('user.get', '', $params);
		if (!empty($resp)) {
			foreach ($resp as $item) {
				$result[$item['ID']] = $item['LAST_NAME'].' '.$item['NAME'];
			}
		}
		if (!Helper::isUtf()) {
			$result = \Bitrix\Main\Text\Encoding::convertEncoding($result, "UTF-8", "Windows-1251");
		}
		return $result;
	}

	// Deal stages
	public static function getStages($dealcateg_id) {
		global $APPLICATION;
		$result = [];
		if (!$dealcateg_id) {
			$list = Rest::execute('crm.status.list', [
				'order' => ['SORT' => 'ASC'],
				'filter' => [
					'ENTITY_ID' => 'DEAL_STAGE',
				]
			]);
		}
		else {
			$list = Rest::execute('crm.dealcategory.stage.list', [
				'id' => $dealcateg_id,
			]);
		}
		if (is_array($list)) {
			foreach ($list as $item) {
				$result[] = [
					'id' => $item['STATUS_ID'],
					'name' => $item['NAME'],
				];
			}
		}
		if (!Helper::isUtf()) {
			$result = \Bitrix\Main\Text\Encoding::convertEncoding($result, "UTF-8", "Windows-1251");
		}
		return $result;
	}

	// Deal fields
	public static function getFields() {
		global $APPLICATION;
		$result = [];
		// Main
		$result[] = [
			'id' => 'ID',
			'name' => Loc::getMessage("CRM_PORTAL_MAIN_CRM_FIELDS_ID"),
		];
		$result[] = [
			'id' => 'LINK',
			'name' => Loc::getMessage("CRM_PORTAL_MAIN_CRM_FIELDS_LINK"),
		];
		// UTM
		$list = Rest::execute('crm.deal.fields');
		if (!empty($list)) {
			foreach ($list as $f_code => $item) {
				if (strpos($f_code, 'UTM_') === 0 || in_array($f_code, ['COMMENTS'])) {
					$result[] = [
						'id' => $f_code,
						'name' => $item['title'],
					];
				}
			}
		}
		// User fields
		// TODO: Remake to getList
		$list = Rest::execute('crm.deal.userfield.list');
		if (is_array($list) && !empty($list)) {
			$req_list = [];
			foreach ($list as $i => $field) {
				$req_list[$i] = 'crm.deal.userfield.get' . '?' . http_build_query([
						'id' => $field['ID'],
					]);
			}
			$resp = Rest::execute('batch', [
				"halt"  => false,
				"cmd" => $req_list,
			]);
			if ($resp['result']) {
				foreach ($list as $i => $field) {
					$field_details = $resp['result'][$i];
					if ( ! empty($field_details)) {
						$result[] = [
							'id' => $field_details['FIELD_NAME'],
							'name' => $field_details['EDIT_FORM_LABEL']['ru'],
						];
					}
				}
			}
		}
		if (!Helper::isUtf()) {
			$result = \Bitrix\Main\Text\Encoding::convertEncoding($result, "UTF-8", "Windows-1251");
		}
		return $result;
	}

	// Fields for the contact
	public static function getContactFields() {
		$result = [
			'LAST_NAME' => [
				'name' => Loc::getMessage("CRM_PORTAL_LAST_NAME"),
				'direction' => self::SYNC_STOC,
				'default' => 'LAST_NAME',
				'hint' => Loc::getMessage("CRM_PORTAL_CONTACT_LAST_NAME_HINT"),
			],
			'NAME' => [
				'name' => Loc::getMessage("CRM_PORTAL_NAME"),
				'direction' => self::SYNC_STOC,
				'default' => 'NAME',
				'hint' => Loc::getMessage("CRM_PORTAL_CONTACT_NAME_HINT"),
			],
			'SECOND_NAME' => [
				'name' => Loc::getMessage("CRM_PORTAL_SECOND_NAME"),
				'direction' => self::SYNC_STOC,
				'default' => 'SECOND_NAME',
				'hint' => Loc::getMessage("CRM_PORTAL_CONTACT_SECOND_NAME_HINT"),
			],
			'EMAIL' =>[
				'name' => Loc::getMessage("CRM_PORTAL_EMAIL"),
				'direction' => self::SYNC_STOC,
				'default' => 'EMAIL',
				'hint' => Loc::getMessage("CRM_PORTAL_CONTACT_EMAIL_HINT"),
			],
			'PHONE' => [
				'name' => Loc::getMessage("CRM_PORTAL_PHONE"),
				'direction' => self::SYNC_STOC,
				'default' => '',
				'hint' => Loc::getMessage("CRM_PORTAL_CONTACT_PHONE_HINT"),
			],
		];
		$list = Rest::execute('crm.contact.fields');
		if (!empty($list)) {
			foreach ($list as $f_code => $item) {
				if (strpos($f_code, 'UF_') === 0) {
					$name = $item['formLabel'];
					if (!Helper::isUtf()) {
						$name = \Bitrix\Main\Text\Encoding::convertEncoding($name, "UTF-8", "Windows-1251");
					}
					$result[$f_code] = [
						'name' => $name,
						'direction' => self::SYNC_STOC,
						'default' => '',
						'hint' => Loc::getMessage("CRM_PORTAL_CONTACT_".$f_code."_HINT"),
					];
				}
			}
		}

		return $result;
	}

	// Fields for the contact
	public static function getContactSFields() {
		$result = [
			'' => Loc::getMessage("CRM_PORTAL_CCSF_PHONEMAIL"),
		];
		$list = Rest::execute('crm.contact.fields');
		if (!empty($list)) {
			foreach ($list as $f_code => $item) {
				if (strpos($f_code, 'UF_') === 0) {
					$name = $item['formLabel'];
					if (!Helper::isUtf()) {
						$name = \Bitrix\Main\Text\Encoding::convertEncoding($name, "UTF-8", "Windows-1251");
					}
					$result[$f_code] = $name;
				}
			}
		}

		return $result;
	}

	// Fields for the company
	public static function getCompanyFields() {
		$presets = self::getCompanyPresets();
		$result = [
			'company' => [
				'title' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_COMPANY_TITLE"),
				'items' => [
					'NAME' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_COMPANY_NAME"),
					'PHONE' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_COMPANY_PHONE"),
					'EMAIL' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_COMPANY_EMAIL"),
				],
			],
			'requisite' => [
				'title' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_TITLE"),
				'items' => [
					'PRESET_ID' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_PRESET_ID"),
					'RQ_FIRST_NAME' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_FIRST_NAME"),
					'RQ_LAST_NAME' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_LAST_NAME"),
					'RQ_SECOND_NAME' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_SECOND_NAME"),
					'RQ_COMPANY_NAME' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_COMPANY_NAME"),
					'RQ_COMPANY_FULL_NAME' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_COMPANY_FULL_NAME"),
					'RQ_DIRECTOR' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_DIRECTOR"),
					'RQ_INN' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_INN"),
					'RQ_KPP' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_KPP"),
					'RQ_OGRN' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_OGRN"),
					'RQ_OGRNIP' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_OGRNIP"),
					'RQ_OKPO' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_OKPO"),
					'RQ_OKTMO' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_OKTMO"),
					'RQ_OKVED' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_REQUISITE_RQ_OKVED"),
				],
				'values' => [
					'PRESET_ID' => $presets,
				],
				'value_def' => [
					'PRESET_ID' => 1,
				],
			],
			'bankdetail' => [
				'title' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_BANKDETAIL_TITLE"),
				'items' => [
					'RQ_BANK_NAME' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_BANKDETAIL_RQ_BANK_NAME"),
					'RQ_BANK_ADDR' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_BANKDETAIL_RQ_BANK_ADDR"),
					'RQ_BIK' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_BANKDETAIL_RQ_BIK"),
					'RQ_ACC_NUM' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_BANKDETAIL_RQ_ACC_NUM"),
					'RQ_ACC_CURRENCY' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_BANKDETAIL_RQ_ACC_CURRENCY"),
					'RQ_COR_ACC_NUM' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_BANKDETAIL_RQ_COR_ACC_NUM"),
				],
			],
			'address_jur' => [
				'title' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_JUR_TITLE"),
				'items' => [
					'ADDRESS_1' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_ADDRESS_1"),
					'ADDRESS_2' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_ADDRESS_2"),
					'CITY' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_CITY"),
					'POSTAL_CODE' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_POSTAL_CODE"),
					'REGION' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_REGION"),
					'PROVINCE' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_PROVINCE"),
					'COUNTRY' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_COUNTRY"),
				],
			],
			'address_fact' => [
				'title' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_FACT_TITLE"),
				'items' => [
					'ADDRESS_1' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_ADDRESS_1"),
					'ADDRESS_2' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_ADDRESS_2"),
					'CITY' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_CITY"),
					'POSTAL_CODE' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_POSTAL_CODE"),
					'REGION' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_REGION"),
					'PROVINCE' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_PROVINCE"),
					'COUNTRY' => Loc::getMessage("CRM_PORTAL_INFO_COMPANY_ADDRESS_COUNTRY"),
				],
			],
		];
		return $result;
	}

	// Presets of org types for the company
	public static function getCompanyPresets() {
		$list = Rest::execute('crm.requisite.preset.list');
		if (!empty($list)) {
			foreach ($list as $item) {
				$name = $item['NAME'];
				if (!Helper::isUtf()) {
					$name = \Bitrix\Main\Text\Encoding::convertEncoding($name, "UTF-8", "Windows-1251");
				}
				$result[$item['ID']] = $name;
			}
		}
		return $result;
	}

}
