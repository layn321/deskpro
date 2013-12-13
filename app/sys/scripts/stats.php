<?php if (!defined('DP_ROOT')) exit('No access');

require DP_ROOT.'/sys/load_config.php';
dp_load_config();

#------------------------------
# Verify auth
#------------------------------

if (!defined('DP_APC_STATS_INFO_AUTH')) {
	echo "DP_APC_STATS_INFO_AUTH_UNDEFINED";
	exit(1);
}

if (!isset($_GET['auth']) || $_GET['auth'] != DP_APC_STATS_INFO_AUTH) {
	echo "DP_APC_STATS_INFO_AUTH_INVALID";
	exit(1);
}

#------------------------------
# Print stats
#------------------------------

$data = array();

$val = @apc_fetch(DP_APC_STATS_KEY.'.query_count');
if (!$val) {
	$data['query_count.total'] = 0;
	$data['query_count.hour_avg'] = 0;
} else {
	$total = 0;
	foreach ($val as $hour => $count) {
		$total += $count;
		$data['query_count.hour.' . $hour] = $count;
	}

	if ($val) {
		$avg = ceil($total / count($val));
	} else {
		$avg = 0;
	}

	$data['query_count.total'] = $total;
	$data['query_count.hour_avg'] = $avg;
}

header('Content-Type: application/json');
echo json_encode($data);