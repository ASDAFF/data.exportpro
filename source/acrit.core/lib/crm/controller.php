<?php
/**
 * Controller
 */

namespace Acrit\Core\Crm;

\Bitrix\Main\Loader::includeModule("sale");

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Bitrix\Sale,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class Controller
{
	const APP_HANDLER = '/bitrix/acrit_#MODULE_ID#_crm_auth.php';
	const EVENTS_HANDLER = '/bitrix/acrit_#MODULE_ID#_crm_handler.php';
    public static $SERVER_ADDR;
    protected static $MANUAL_RUN = false;

	static $MODULE_ID = '';
	static $profile = false;

	public static function setModuleId($value) {
		self::$MODULE_ID = $value;
		Settings::setModuleId($value);
	}

	function setProfile(int $profile_id) {
		self::$profile = Helper::call(self::$MODULE_ID, 'CrmProfiles', 'getProfiles', [$profile_id]);
		//\Helper::Log('(getOrderProfile) selected profile "' . self::$profile['id'] . '"');
	}

	public static function getAppHandler() {
		$module_code = str_replace('acrit.', '', self::$MODULE_ID);
		$link = str_replace('#MODULE_ID#', $module_code, self::APP_HANDLER);
		return $link;
	}

	public static function setBulkRun() {
		self::$MANUAL_RUN = true;
		Rest::setBulkRun();
    }

	public static function isBulkRun() {
		return self::$MANUAL_RUN;
    }

	public static function setBaseParams() {
		if (!self::$SERVER_ADDR) {
			self::$SERVER_ADDR = Settings::get("crm_conn_site");
		}
        return true;
    }

	public static function getServerAddr() {
		return self::$SERVER_ADDR;
	}

	public static function checkConnection() {
        $res = false;
        if (Rest::getAppInfo() && Rest::getAuthInfo()) {
	        $res = true;
        }
        return $res;
    }

    public static function getOrderIDField() {
		$field = 'ORIGIN_ID';
		if (Settings::get('crm_orderid_field')) {
			$field = Settings::get('crm_orderid_field');
		}
		return $field;
    }


	/**
	 * Create new deal from order
	 */

	private static function createDealFromOrder($order_data, $deal_info, $deal_fields) {
		$deal = [];
		if (self::checkConnection()) {
			$order_id = $order_data['ID'];
			// Add deal
			$category_id = (int)self::$profile['CONNECT_DATA']['category'];
			$deal_title = self::getOrdTitleWithPrefix($order_data);
			$fields = [
				'TITLE'     => $deal_title,
				self::getOrderIDField() => $order_id,
				'CATEGORY_ID' => $category_id,
			];
			$fields = array_merge($deal_fields, $fields);
			// Source of deal
			$source_id = self::$profile['CONNECT_DATA']['source_id'];
			if ($source_id) {
				$fields['ORIGINATOR_ID'] = $source_id;
			}
			// Responsible user
			if (!$fields['ASSIGNED_BY_ID']) {
				$responsible_id = (int) self::$profile['CONNECT_DATA']['responsible'];
			}
			if ($responsible_id) {
				$fields['ASSIGNED_BY_ID'] = $responsible_id;
			}
			// Create deal
			//\Helper::Log('(createDealFromOrder) crm.deal.add for order '.$order_id.' '.print_r($fields, 1));
			$resp = Rest::execute('crm.deal.add', ['fields' => $fields]);
			if ($resp) {
				$deal_id = $resp;
				// Return deal details
				$deals = self::getDeal([$deal_id]);
				$deal = $deals[0];
				//\Helper::Log('(createDealFromOrder) order ' . $order_id . ' deal ' . $deal_id . ' created');
			}
		}
		return $deal;
	}


	/**
	 * Sync goods and delivery
	 */

	public static function updateDealProducts($deal_id, $order_data, $deal_info) {
		$result = false;
		if (self::checkConnection()) {
			$old_prod_rows = Rest::execute('crm.deal.productrows.get', [
				'id' => $deal_id
			]);
			$new_rows = [];
			// Products list of deal
			foreach ($order_data['PRODUCTS'] as $k => $item) {
				// Discount
				$price = $item['PRICE'];
				// Product fields
				$deal_prod = [
					'PRODUCT_NAME' => $item['PRODUCT_NAME'],
					'QUANTITY' => $item['QUANTITY'],
					'DISCOUNT_TYPE_ID' => 1,
					'DISCOUNT_SUM' => $item['DISCOUNT_SUM'],
					'MEASURE_CODE' => $item['MEASURE_CODE'],
					'TAX_RATE' => $item['TAX_RATE'],
					'TAX_INCLUDED' => $item['TAX_INCLUDED'],
				];
				if ($item['TAX_INCLUDED']) {
					$deal_prod['PRICE_EXCLUSIVE'] = $price;
					$deal_prod['PRICE'] = $price + $price * 0.01 * (int)$item['TAX_RATE'];
				}
				else {
					$deal_prod['PRICE'] = $price;
				}
				$new_rows[] = $deal_prod;
			}
//			// Delivery
//			$delivery_sync_type = Settings::get('products_delivery');
//			if (!$delivery_sync_type || ($delivery_sync_type == 'notnull' && $order_data['DELIVERY_PRICE'])) {
//				$new_rows[] = [
//					'PRODUCT_ID'   => 'delivery',
//					'PRODUCT_NAME' => Loc::getMessage("SP_CI_PRODUCTS_DELIVERY"),
//					'PRICE'        => $order_data['DELIVERY_PRICE'],
//					'QUANTITY'     => 1,
//				];
//			}
			// Check changes
			$new_rows = self::convEncForDeal($new_rows);
			$has_changes = false;
			if (count($new_rows) != count($old_prod_rows)) {
				$has_changes = true;
			}
			else {
				foreach ($new_rows as $j => $row) {
					foreach ($row as $k => $value) {
						if ($value != $old_prod_rows[$j][$k]) {
							$has_changes = true;
							continue 2;
						}
					}
				}
			}
			// Send request
			if ($has_changes) {
				//\Helper::Log('(updateDealProducts) deal '.$deal_id.' changed products '.print_r($new_rows, true));
				$resp = Rest::execute('crm.deal.productrows.set', [
					'id'   => $deal_id,
					'rows' => $new_rows
				]);
				if ($resp) {
					$result = true;
				}
			}
		}
		return $result;
	}


	/**
	 * Get contact data by profile
	 */

	public static function getDealContactDataByProfile(array $order_data, $contact) {
		$cont_fields = [];
		$comp_table = (array)self::$profile['CONTACTS']['table_compare'];
		$user_fields = $order_data['USER'];
		foreach ($comp_table as $deal_f_id => $order_f_id) {
			// User fields
			if ($order_f_id) {
				$value = $user_fields[$order_f_id];
				if ($value) {
					if (in_array($deal_f_id, ['EMAIL', 'PHONE'])) {
						$phonemail_mode = Settings::get('contacts_phonemail_mode');
						if ($phonemail_mode == 'replace' && ! empty($contact[$deal_f_id])) {
							foreach ($contact[$deal_f_id] as $i => $item) {
								if ($i == 0) {
									$cont_fields[$deal_f_id][] = ['ID'         => $item['ID'],
									                              'VALUE'      => $value,
									                              'VALUE_TYPE' => 'WORK'
									];
								} else {
									$cont_fields[$deal_f_id][] = ['ID' => $item['ID'], 'DELETE' => 'Y'];
								}
							}
						} else {
							$cont_fields[$deal_f_id][] = ['VALUE' => $value, 'VALUE_TYPE' => 'WORK'];
						}
					} else {
						$cont_fields[$deal_f_id] = $value;
					}
				} else {
					$cont_fields[$deal_f_id] = '';
				}
			}
		}
		return $cont_fields;
	}


	/**
	 * Sync the deal contact data
	 */

	public static function syncOrderToDealContact(array $order_data, $deal_info) {
		$result = false;
		if (self::checkConnection()) {
			$sync_new_type = (int) self::$profile['CONTACTS']['sync_new_type'];
			$deal = $deal_info['deal'];
			$contact = $deal_info['contact'];
			// Find contact
			if (!$contact['ID']) {
				$contact = self::findContact($order_data, $deal_info);
			}
			// Get contacts data
			$cont_fields = self::getDealContactDataByProfile($order_data, $contact);
			$cont_fields = self::convEncForDeal($cont_fields);
			//\Helper::Log('(syncOrderToDealContact) cont_fields '.print_r($cont_fields, true));
			// Add contact
			if (!$contact['ID']) {
				$responsible_id = (int)self::$profile['CONNECT_DATA']['responsible'];
				if ($responsible_id) {
					$cont_fields['ASSIGNED_BY_ID'] = $responsible_id;
				}
				$contact_id = Rest::execute('crm.contact.add', [
					'fields' => $cont_fields,
				]);
				if (!$contact_id) {
					$res = Rest::execute('crm.contact.add', [
						'fields' => $cont_fields,
					], false, true, false);
					//\Helper::Log('(syncOrderToDealContact) add contact error '.print_r($res, true));
				}
			}
			// Update contact
			else {
				$contact_id = $contact['ID'];
				// TODO: Checking for changes
				if ((!$deal['ID'] && $sync_new_type == 1) || $sync_new_type == 2) {
					Rest::execute('crm.contact.update', [
						'id'     => $contact_id,
						'fields' => $cont_fields,
					]);
				}
			}
			if ($contact_id) {
				$result = $contact_id;
			}
		}
		return $result;
	}


	/**
	 * Contacts search
	 */

	public static function findContact(array $order_data, $deal_info) {
		$contact = false;
		if (self::checkConnection()) {
			$cont_fields = self::getDealContactDataByProfile($order_data, []);
			$cont_s_field = self::$profile['CONTACTS']['contact_search_fields'];
			if ($cont_s_field) {
				if ($cont_fields[$cont_s_field]) {
					$filter = [
						$cont_s_field => $cont_fields[$cont_s_field],
					];
					$request = [
						'list' => [
							'method' => 'crm.contact.list',
							'params' => [
								'filter' => $filter,
							]
						],
						'get' => [
							'method' => 'crm.contact.get',
							'params' => [
								'id' => '$result[list][0][ID]',
							]
						]
					];
					$res = Rest::batch($request);
					if ($res['get']) {
						$contact = $res['get'];
					}
				}
			}
			else {
				if ($cont_fields['PHONE'] && $cont_fields['PHONE'][0]['VALUE']) {
					$search_phone = $cont_fields['PHONE'][0]['VALUE'];
				}
				if ($cont_fields['EMAIL'] && $cont_fields['EMAIL'][0]['VALUE']) {
					$search_email = $cont_fields['EMAIL'][0]['VALUE'];
				}
				// Find by phone
				if ($search_phone) {
					$phones = self::getPhonesFormats($search_phone);
					foreach ($phones as $phone) {
						$filter = [
							'PHONE' => $phone,
						];
						$request = [
							'list' => [
								'method' => 'crm.contact.list',
								'params' => [
									'filter' => $filter,
								]
							],
							'get' => [
								'method' => 'crm.contact.get',
								'params' => [
									'id' => '$result[list][0][ID]',
								]
							]
						];
						$res = Rest::batch($request);
						if ($res['get']) {
							$contact = $res['get'];
						}
					}
				}
				// Find by email
				if ( ! $contact && $search_email) {
					$filter = [
						'EMAIL' => $search_email,
					];
					$request = [
						'list' => [
							'method' => 'crm.contact.list',
							'params' => [
								'filter' => $filter,
							]
						],
						'get' => [
							'method' => 'crm.contact.get',
							'params' => [
								'id' => '$result[list][0][ID]',
							]
						]
					];
					$res = Rest::batch($request);
					if ($res['get']) {
						$contact = $res['get'];
					}
				}
			}
			//\Helper::Log('(findContact) finded contact "' . $contact['ID'] . '" by ' . print_r($filter, true));
		}
		return $contact;
	}


	/**
	 * Get changed fields of the deal
	 */

	public static function getDealChangedFields(array $order_data, array $deal_info) {
		$d_new_fields = [];
		// Changes of status
		$d_new_fields = array_merge($d_new_fields, self::getDealChangedStatus($order_data, $deal_info));
		// Changes of props
		$d_new_fields = array_merge($d_new_fields, self::getDealChangedProps($order_data, $deal_info));

		return $d_new_fields;
	}


	/**
	 * Updating of deal data
	 */

	public static function updateDealFields($deal_id, $order_id, $d_new_fields) {
		// Send changes
		if (!empty($d_new_fields)) {
			foreach ($d_new_fields as $k => $value) {
				if ($value === null) {
					$d_new_fields[$k] = '';
				}
			}
			//\Helper::Log('(updateDealFields) deal '.$deal_id.' update fields ' . print_r($d_new_fields, true));
			Rest::execute('crm.deal.update', [
				'id'     => $deal_id,
				'fields' => $d_new_fields,
			]);
		}
	}


	/**
	 * Information of status changes
	 */

	public static function getDealChangedStatus(array $order_data, array $deal_info) {
		$changed_fields = [];
		$status_table = (array)self::$profile['STAGES']['table_compare'];
		$cancel_table = (array)self::$profile['STAGES']['cancel_stages'];
		$reverse_disable = self::$profile['STAGES']['reverse_disable'] ? true : false;
		$deal = $deal_info['deal'];
		// Stage of canceled order
		$new_stage = false;
		if ($order_data['IS_CANCELED']) {
			if ( ! in_array($deal['STAGE_ID'], $cancel_table)) {
				$new_stage = $cancel_table[0];
			}
		}
		// Change stage if set conformity of status and statuses is different
		else {
			$sync_params = $status_table[$order_data['STATUS_ID']];
			$deal_stages = (array) $sync_params;
			$deal_stages = array_diff($deal_stages, ['']);
			if ( !empty($deal_stages) && !in_array($deal['STAGE_ID'], $deal_stages)) {
				$new_stage = $deal_stages[0];
			}
		}
		// Check if is reverse stage
		if ($new_stage && $reverse_disable) {
			$stages_list = [];
			foreach ($deal_info['stages'] as $item) {
				$stages_list[$item['STATUS_ID']] = count($stages_list);
			}
			if ($stages_list[$new_stage] <= $stages_list[$deal['STAGE_ID']]) {
				$new_stage = false;
			}
		}
		if ($new_stage) {
			$changed_fields['STAGE_ID'] = $new_stage;
		}
		return $changed_fields;
	}


	/**
	 * Information of properties changes
	 */

	public static function getDealChangedProps(array $order_data, array $deal_info) {
		$changed_fields = [];
		$deal = $deal_info['deal'];
		$deal_fields = $deal_info['fields'];
		$comp_table = (array)self::$profile['FIELDS']['table_compare'];
		foreach ($comp_table as $cp_o_f_code => $sync_params) {
			$d_f_code = $sync_params['value'];
			if ($deal_fields[$d_f_code]) {
				$new_value = false;
				$deal_value = $deal[$d_f_code];
				// Properties
				foreach ($order_data['FIELDS'] as $o_f_code => $o_field) {
					$value = false;
					if ($o_f_code == $cp_o_f_code) {
//						//\Helper::Log('(syncOrderToDeal) $prop: ' . print_r($prop, true));
						switch ($o_field['TYPE']) {
							case 'LIST':
								foreach ($o_field['VALUE'] as $value) {
									foreach ($deal_fields[$d_f_code]['items'] as $deal_f_value) {
										if ($deal_f_value['VALUE'] == self::convEncForDeal($value)) {
											$new_value[] = $deal_f_value['ID'];
										}
									}
								}
								break;
							case 'FILE':
								foreach ($o_field['VALUE'] as $file) {
									$data = file_get_contents($file['PATH']);
									$new_value[] = array(
										"fileData" => array(
											$file['NAME'],
											base64_encode($data)
										)
									);
								}
								break;
							case 'BOOLEAN':
								$new_value[] = $o_field['VALUE'][0];
								break;
							case 'DATE':
								if ($deal_fields[$d_f_code]['type'] == 'date') {
									$value[] = date(ProfileInfo::DATE_FORMAT_PORTAL_SHORT, strtotime($o_field['VALUE'][0]));
									$deal_value = date(ProfileInfo::DATE_FORMAT_PORTAL_SHORT, strtotime($deal_value));
								}
								else {
									$value[] = date(ProfileInfo::DATE_FORMAT_PORTAL, strtotime($o_field['VALUE'][0]));
									$deal_value = date(ProfileInfo::DATE_FORMAT_PORTAL, strtotime($deal_value));
								}
								$new_value = self::convEncForDeal($value);
								break;
							default:
								if (is_array($o_field['VALUE']) && count($o_field['VALUE']) === 1 && !$o_field['VALUE'][0]) {
									$o_field['VALUE'] = [];
								}
								$new_value = self::convEncForDeal($o_field['VALUE']);
						}
						break;
					}
				}

				if ($new_value !== false) {
//					//\Helper::Log('(syncOrderToDeal) $new_value: ' . print_r($new_value, true));
//					//\Helper::Log('(syncOrderToDeal) $deal_value: ' . print_r($deal_value, true));
					$deal_value = is_array($deal_value) ? $deal_value : (! $deal_value ? [] : [$deal_value]);
					if ( ! self::isEqual($new_value, $deal_value)) {
						if ($deal_fields[$d_f_code]['isMultiple']) {
							$changed_fields[$d_f_code] = $new_value;
						} else {
							$changed_fields[$d_f_code] = $new_value[0];
						}
					}
				}
			}
		}

		return $changed_fields;
	}


	/**
	 * Search of deal
	 */

	public static function findDeal(array $order_data, $wo_categ=false) {
		$deal_id = false;
		$filter = [
			self::getOrderIDField() => $order_data['ID'],
		];
		if (!$wo_categ) {
			$category_id = (int) self::$profile['CONNECT_DATA']['category'];
			$filter['CATEGORY_ID'] = $category_id;
		}
		$source_id = self::$profile['CONNECT_DATA']['source_id'];
		if ($source_id) {
			$filter['ORIGINATOR_ID'] = $source_id;
		}
		$i = 0;
		while (!$deal_id && $i < 3) {
			if ($i > 0) {
				usleep(500000);
			}
			$res = Rest::execute('crm.deal.list', [
				'filter' => $filter,
			]);
			if ($res) {
				$deal_id = (int) $res[0]['ID'];
			}
			$i++;
		}
		//\Helper::Log('(findDeal) order '.$order_data['ID'].' find deal '.$deal_id);
		return $deal_id;
	}

	/**
	 * Find CRM user
	 */

	public static function findCrmUser($store_user_id, array $deal_info=[]) {
		$crm_user_id = false;
		$res = \CUser::GetByID($store_user_id);
		$store_user = $res->Fetch();
		$user_email = $store_user['EMAIL'];
		if ($deal_info['assigned_user']['EMAIL'] && $user_email == $deal_info['assigned_user']['EMAIL']) {
			$crm_user_id = $deal_info['assigned_user']['ID'];
		}
		else {
			$res = Rest::execute('user.get', [
				'FILTER' => [
					'EMAIL' => $user_email,
				]
			]);
			if ($res[0]['ID']) {
				$crm_user_id = $res[0]['ID'];
			}
		}
		return $crm_user_id;
	}


	/**
	 * Sync order with deal
	 */

	public static function syncOrderToDeal(array $order_data) {
		$incl_res = \Bitrix\Main\Loader::includeSharewareModule(self::$MODULE_ID);
		if ($incl_res == \Bitrix\Main\Loader::MODULE_NOT_FOUND || $incl_res == \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED) {
			return;
		}
		if (!self::checkConnection()) {
			return;
		}
		// Check order data
		if (!$order_data) {
			return false;
		}
		// Check start date
		$start_date_ts = self::getStartDateTs();
		if ($start_date_ts && $order_data['DATE_INSERT'] < $start_date_ts) {
			return;
		}
		// Has synchronization active
		$sync_active = (self::$profile['ACTIVE'] == 'Y');
		if (!$sync_active) {
			return;
		}
		// Get deal
		$deal_id = self::findDeal($order_data);
		$order_id = $order_data['ID'];
		// Update fields of the deal
		if ($deal_id) {
			$deal_info = self::getDealInfo($deal_id);
			$deal = $deal_info['deal'];
			$deal_new_fields = self::getDealChangedFields($order_data, $deal_info);
			// Update contact
			try {
				$contact_id = self::syncOrderToDealContact($order_data, $deal_info);
				if ($deal_info['deal']['CONTACT_ID'] != $contact_id) {
					$deal_new_fields['CONTACT_ID'] = $contact_id;
				}
			}
			catch (\Exception $e) {
				//\Helper::Log('(syncOrderToDeal) can\'t sync of contact');
			}
			// Update deal
			self::updateDealFields($deal_id, $order_id, $deal_new_fields);
		}
		// Create a new deal
		else {
			// Check if deal of order doesn't exist on other categs
			if (!self::findDeal($order_data, true)) {
				$deal_info   = self::getDealInfo();
				$deal_fields = self::getDealChangedFields($order_data, $deal_info);
				// Add contact
				try {
					$contact_id = self::syncOrderToDealContact($order_data, $deal_info);
					if ($contact_id) {
						$deal_fields['CONTACT_ID'] = $contact_id;
					}
				}
				catch (\Exception $e) {
					//\Helper::Log('(syncOrderToDeal) can\'t sync of contact');
				}
				// Add deal
				$deal = self::createDealFromOrder($order_data, $deal_info, $deal_fields);
				$deal_id     = $deal['ID'];
				$deal_info   = self::getDealInfo($deal_id);
			}
		}
		if ($deal) {
			// Update products
			self::updateDealProducts($deal_id, $order_data, $deal_info);
		}
	}



	/**
	 * Deal data
	 */

	public static function getDeal($deals_ids) {
		$deals = [];
		if (is_array($deals_ids) && !empty($deals_ids)) {
			$req_list = [];
			foreach ($deals_ids as $i => $deals_id) {
				$req_list[$i] = 'crm.deal.get' . '?' . http_build_query([
						'id' => $deals_id,
					]);
			}
			$resp = Rest::execute('batch', [
				"halt"  => false,
				"cmd" => $req_list,
			]);
			if ($resp['result']) {
				foreach ($resp['result'] as $deal) {
					$deal['LINK'] = Settings::get("crm_conn_portal") . '/crm/deal/details/' . $deal['ID'] . '/';
					$deals[] = $deal;
				}
			}
		}
		return $deals;
	}

	/**
	 * CRM info for sync process
	 */

	public static function getDealInfo($deal_id=0) {
		$info = [
			'deal' => [],
			'fields' => [],
			'stages' => [],
			'contact' => [],
			'company' => [],
			'products' => [],
			'product_fields' => [],
			'assigned_user' => [],
		];
		$request = [];
		if ($deal_id) {
			$request['deal'] = [
				'method' => 'crm.deal.get',
				'params' => ['id' => $deal_id]
			];
			$request['contact'] = [
				'method' => 'crm.contact.get',
				'params' => [
					'id' => '$result[deal][CONTACT_ID]',
				]
			];
			$request['assigned_user'] = [
				'method' => 'user.get',
				'params' => [
					'id' => '$result[deal][ASSIGNED_BY_ID]',
				]
			];
			$request['products'] = [
				'method' => 'crm.deal.productrows.get',
				'params' => [
					'id' => $deal_id,
				]
			];
		}
		$request['fields'] = [
			'method' => 'crm.deal.fields',
		];
		$dealcateg_id = (int)self::$profile['CONNECT_DATA']['category'];
		if (!$dealcateg_id) {
			$request['stages'] = [
				'method' => 'crm.status.list',
				'params' => [
					'order' => ['SORT' => 'ASC'],
					'filter' => [
						'ENTITY_ID' => 'DEAL_STAGE',
					]
				]
			];
		}
		else {
			$request['stages'] = [
				'method' => 'crm.dealcategory.stage.list',
				'params' => [
					'id' => $dealcateg_id,
				]
			];
		}
		$request['product_fields'] = [
			'method' => 'crm.product.fields',
		];
		$info = array_merge($info, Rest::batch($request));
		if (!empty($info['assigned_user'])) {
			$info['assigned_user'] = $info['assigned_user'][0];
		}
		return $info;
	}


    /**
     * Sync all orders by period
     */

    function syncStoreToCRM($sync_interval=0) {
        global $DB;
	    if (self::checkConnection()) {
		    //\Helper::Log('(syncStoreToCRM) run period ' . $sync_period);
		    // Get plugin object
		    $plugin = false;
		    if (strlen(self::$profile['PLUGIN'])) {
			    $arProfilePlugin = Exporter::getInstance(self::$MODULE_ID)->getPluginInfo(self::$profile['PLUGIN']);
			    if (is_array($arProfilePlugin)) {
				    $strPluginClass = $arProfilePlugin['CLASS'];
				    if (strlen($strPluginClass) && class_exists($strPluginClass)) {
					    $plugin = new $strPluginClass(self::$MODULE_ID);
					    $plugin->setProfileArray(self::$profile);
				    }
			    }
		    }
		    // List of orders, changed by last period (if period is not set than get all orders)
		    if ($plugin) {
			    $filter = [];
			    if ($sync_interval > 0) {
				    $filter['change_date_from'] = time() - $sync_interval;
			    }
			    $orders_ids = $plugin->getOrdersIDsList($filter);
			    foreach ($orders_ids as $order_id) {
				    $order_data = $plugin->getOrder($order_id);
				    try {
					    self::syncOrderToDeal($order_data);
				    } catch (\Exception $e) {
					    //\Helper::Log('(syncStoreToCRM) can\'t sync of order ' . $order_data['ID']);
				    }
			    }
		    }
		    //\Helper::Log('(syncStoreToCRM) success');
	    }
    }


	/**
	 * Check system parameters
	 */

	function checkModuleStatus() {
		$res = [
			'auth_file' => false,
			'store_handler_file' => false,
			'crm_handler_file' => false,
			'app_info' => false,
			'auth_info' => false,
			'connect' => false,
			'store_events' => false,
			'crm_events' => false,
			'crm_events_uncheck' => false,
		];
		// Site base directory
		$site_default = \Helper::getSiteDef();
		$abs_root_path = $_SERVER['DOCUMENT_ROOT'] . $site_default['DIR'];
		// Check auth file
		if (file_exists($abs_root_path . 'bitrix/sprod_integr_auth.php')) {
			$res['auth_file'] = true;
		}
		// Check handler files
		if (file_exists($abs_root_path . 'bitrix/sprod_integr_bgr_run.php')) {
			$res['store_handler_file'] = true;
		}
		if (file_exists($abs_root_path . 'bitrix/sprod_integr_handler.php')) {
			$res['crm_handler_file'] = true;
		}
		// Availability of B24 application data
		if (Rest::getAppInfo()) {
			$res['app_info'] = true;
			// Availability of connection data
			if (Rest::getAuthInfo()) {
				$res['auth_info'] = true;
			}
		}
		if ($res['app_info'] && $res['auth_info']) {
			// Availability of an order change handler
			if (self::checkStoreHandlers()) {
				$res['store_events'] = true;
			}
			// Relevance of data for connecting to B24
			$resp = Rest::execute('app.info', [], false, true, false);
			if ($resp && !$resp['error']) {
				$res['connect'] = true;
				// Availability of a deal change handler
				if (self::checkCrmHandlers()) {
					$res['crm_events'] = true;
				}
				if (self::$profile['CONNECT_CRED']['direction'] == 'ctos') {
					$res['crm_events_uncheck'] = true;
				}
			}
		}

		return $res;
	}


    /**
     * Utilites
     */

    // Convert encoding
    function convEncForDeal($value) {
	    if (!Helper::isUtf()) {
		    $value = \Bitrix\Main\Text\Encoding::convertEncoding($value, "Windows-1251", "UTF-8");
	    }
        return $value;
    }

    // Convert encoding
    function convEncForOrder($value) {
	    if (!Helper::isUtf()) {
		    $value = \Bitrix\Main\Text\Encoding::convertEncoding($value, "UTF-8", "Windows-1251");
	    }
        return $value;
    }

    // Get prefix option
    public static function getPrefix() {
        $prefix = self::$profile['CONNECT_DATA']['prefix'];
        return $prefix;
    }

    // Get CRM order title
    function getOrdTitleWithPrefix(array $order_data) {
        $prefix = self::getPrefix();
        $order_num = $order_data['ID'];
	    $title = $prefix . $order_num;
        return $title;
    }

    // Values equal check
	public static function isEqual($order_value, $deal_value) {
    	$res = false;
    	if ($order_value == [false]) {
		    $order_value = [];
	    }
    	if ($deal_value == [false]) {
		    $deal_value = [];
	    }
    	if ( !is_array($order_value) && !is_array($deal_value)) {
    		if ($order_value == $deal_value) {
			    $res = true;
		    }
	    }
    	elseif (is_array($order_value) && is_array($deal_value)) {
    		if (count($order_value) == count($deal_value)) {
    			$res = true;
			    foreach ($order_value as $k => $value) {
				    if ($value != $deal_value[$k]) {
					    $res = false;
				    }
    			}
			    foreach ($deal_value as $k => $value) {
				    if ($value != $order_value[$k]) {
					    $res = false;
				    }
    			}
		    }
	    }
    	return $res;
	}

	public static function getStartDateTs() {
		$start_date_ts = false;
		$start_date = self::$profile['CONNECT_CRED']['start_date'];
		if ($start_date) {
			$start_date_ts = strtotime(date('d.m.Y 00:00:00', strtotime($start_date)));
		}
		return $start_date_ts;
	}

	private static function getPhonesFormats($phone){
		$phones = [];
		if(strlen($phone)){
			$phoneUnformatted = preg_replace('/[^+\d]/', '', $phone);
			$phoneFormatted = preg_replace(
				[
					'/^\+?7([\d]{3})([\d]{3})([\d]{2})([\d]{2})$/m',
					'/^\+?380([\d]{2})([\d]{3})([\d]{2})([\d]{2})$/m',
					'/^\+?996([\d]{3})([\d]{3})([\d]{3})$/m',
					'/^\+?998([\d]{2})([\d]{3})([\d]{4})$/m',
				],
				[
					'+7 (${1}) ${2}-${3}-${4}', // +7 (___) ___-__-__
					'+380 (${1}) ${2}-${3}-${4}', // +380 (__) ___-__-__
					'+996 (${1}) ${2}-${3}', // +996 (___) ___-___
					'+998-${1}-${2}-${3}', // +998-__- ___-____
				],
				$phoneUnformatted
			);
			$phones = array_unique([$phone, $phoneFormatted, $phoneUnformatted]);
		}
		return $phones;
	}

}