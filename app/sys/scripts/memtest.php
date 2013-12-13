<?php

/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * DeskPRO
 *
 * @package DeskPRO
 */
 
// no direct access
if (!defined('DP_ROOT')) exit('No access');

$memlimit = ini_get('memory_limit');
if (!$memlimit || $memlimit == -1) {
	$memlimit = 0;
	$memlimit_mb = 0;
} else {
	$last = strtolower($memlimit[strlen($memlimit)-1]);
	$memlimit = (int)$memlimit;
	switch($last) {
		case 'g': $memlimit *= 1024;
		case 'm': $memlimit *= 1024;
		case 'k': $memlimit *= 1024;
	}

	$memlimit_mb = round($memlimit / 1024 / 1024, 2);
}

if ($memlimit) {
	echo "Expected memory limit of {$memlimit} ({$memlimit_mb} MB)<br />";
} else {
	echo "No memory limit defined in PHP configuration. Testing with 500 MB.<br />";
	$memlimit = 500 * 1024 * 1024;
	$memlimit_mb = 500;
}

$mem = memory_get_usage();
$mem_mb = round($mem / 1024 / 1024, 2);
echo "Start: {$mem} ($mem_mb <B)<br />";

$counter = 0;
while (true) {
	$counter += 102400;

	$fill = str_repeat('x', $counter * 100);
	$mem = memory_get_usage();
	$mem_mb = round($mem / 1024 / 1024, 2);
	echo "Reached: {$mem} ($mem_mb MB)<br />";
	unset($fill);

	if ($mem_mb + 8 >= $memlimit_mb) {
		break;
	}
}

echo "<br />";
echo "If you can see this, then the server can allocate something close to " . $memlimit_mb . " MB";
