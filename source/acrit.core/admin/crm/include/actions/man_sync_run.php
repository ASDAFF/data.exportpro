<?
namespace Acrit\Core\Crm;

// Prepare
//$next_item = $_REQUEST['next_item']?$_REQUEST['next_item']:0;
//$limit = $_REQUEST['limit']?$_REQUEST['limit']:10;
//$imported_count = (int)$_REQUEST['imported_count'];
//$next_item_new = 0;

$next_item = $_REQUEST['next_item'] ? $_REQUEST['next_item'] : 0;
$cnt = $_REQUEST['count'] ? $_REQUEST['count'] : 0;
//Helper::Log('(sync) next_item '.$next_item);
$step_time = 20;
$start_time = time();

$filter = [];
$start_sync_ts = false;
$sync_period_opt = $arProfile['SYNC']['man']['period'];
if ($sync_period_opt == '1d') {
	$sync_period = 3600 * 24;
}
elseif ($sync_period_opt == '1w') {
	$sync_period = 3600 * 24 * 7;
}
elseif ($sync_period_opt == '1m') {
	$sync_period = 3600 * 24 * 31;
}
elseif ($sync_period_opt == '3m') {
	$sync_period = 3600 * 24 * 31 * 3;
}
if ($sync_period) {
	$start_sync_ts = time() - $sync_period;
}
$start_date_ts = Controller::getStartDateTs();
if ($start_date_ts) {
	if ($start_date_ts > $start_sync_ts) {
		$start_sync_ts = $start_date_ts;
	}
}
if ($start_sync_ts) {
	$filter['create_date_from'] = $start_sync_ts;
}

// Process
//Helper::Log('(sync) cnt '.$cnt);
if ($next_item < $cnt) {
	$orders_ids = $obPlugin->getOrdersIDsList($filter);
	$i = 0;
	foreach($orders_ids as $order_id) {
		if ($i < $next_item) {
			$i++;
			continue;
		}
		$exec_time = time() - $start_time;
		if ($exec_time >= $step_time) {
//			Helper::Log('(sync) break on '.$i);
			break;
		}
		$order_data = $obPlugin->getOrder($order_id);
		if ($arProfile['SYNC']['man']['only_new']) {
			$deal_id = Controller::findDeal($order_data);
			if (!$deal_id) {
				try {
					Controller::syncOrderToDeal($order_data);
				}
				catch (\Exception $e) {
//					\Helper::Log('(sync) can\'t sync of order ' . $order_data['ID']);
				}
			}
		}
		else {
			try {
				Controller::syncOrderToDeal($order_data);
			}
			catch (\Exception $e) {
//				\Helper::Log('(sync) can\'t sync of order ' . $order_data['ID']);
			}
		}
		$i++;
	}
}
$next_item   = $i;

// Result
$arJsonResult['result'] = 'ok';
$arJsonResult['next_item'] = (int)$next_item;
$arJsonResult['errors'] = [];
$arJsonResult['report'] = [];
$arJsonResult['sync_period_opt'] = $sync_period_opt;
