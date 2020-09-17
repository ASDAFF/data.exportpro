<?
namespace Acrit\Core\Crm;

// Process
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
$cnt = $obPlugin->getOrdersCount($filter);
// Result
$count = 0;
$arJsonResult['result'] = 'ok';
$arJsonResult['count'] = (int)$cnt;
$arJsonResult['errors'] = [];
