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

if (php_sapi_name() != 'cli') {
	echo "This script must only be run using the command line interface of PHP\n";
	echo "Contact support@deskpro.com if you require assistance.\n";
	exit(1);
}

ini_set('display_errors', true);
error_reporting(E_ALL | E_STRICT);
define('DP_ROOT', realpath(__DIR__ . '/../'));
define('DP_WEB_ROOT', realpath(__DIR__ . '/../../'));
define('DP_BOOT_MODE', 'cli');
if (!defined('DP_CONFIG_FILE')) define('DP_CONFIG_FILE', DP_WEB_ROOT . '/config.php');
setlocale(LC_CTYPE, 'C');
date_default_timezone_set('UTC');
ini_set('default_charset', 'UTF-8');
set_time_limit(0);

require DP_ROOT . '/sys/load_config.php';
dp_load_config();

if (!empty($argv[1])) {
	// need to be able to get settings
	require DP_ROOT . '/sys/KernelBooter.php';
	\DeskPRO\Kernel\KernelBooter::bootstrapLib(true);

	define('DP_INTERFACE', 'sys');

	$env = 'prod';
	$debug = false;

	if (isset($DP_CONFIG['debug']['dev']) && $DP_CONFIG['debug']['dev']) {
		$env = 'dev';
		$debug = true;
	}

	$kernel = new \DeskPRO\Kernel\UserKernel($env, $debug);
	$kernel->boot();

	if (session_id() != '') {
		session_write_close();
	}
} else {
	require DP_ROOT.'/vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
	require DP_ROOT.'/src/Orb/Util/ClassLoader.php';
	require DP_ROOT.'/sys/Kernel/KernelErrorHandler.php';
	require_once DP_ROOT.'/sys/autoload.php';

	set_error_handler('DeskPRO\\Kernel\\KernelErrorHandler::handleError', E_ALL | E_STRICT);
	set_exception_handler('DeskPRO\\Kernel\\KernelErrorHandler::handleException');
}

if (defined('DPC_IS_CLOUD')) {
	echo "Cannot run on a cloud install.\n";
	exit(3);
}

$db_conf = $DP_CONFIG['db'];
$db_conf['driver'] = 'pdo_mysql';

$pid_file = dp_get_data_dir() . '/twitter.pid';
$is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

$get_db = function() use ($db_conf) {
	return \Doctrine\DBAL\DriverManager::getConnection($db_conf);
};

$check_runner_active = function(&$pid = null) use($pid_file, $is_windows) {
	$running = false;
	$pid = null;

	if (file_exists($pid_file)) {
		$pid = intval(file_get_contents($pid_file));

		if ($pid) {
			if ($is_windows) {
				$running = false;
				exec('start "tasklist" /B tasklist.exe', $processes);
				foreach ($processes AS $process_line) {
					if (preg_match('/^.*\s(\d+)\s/U', $process_line, $match)) {
						if ($pid == intval($match[1])) {
							$running = true;
							break;
						}
					}
				}
			} else {
				$running = @file_exists("/proc/$pid");
			}
		}
	}

	if (!$running) {
		$pid = null;
	}

	return $running;
};

$log_file = dp_get_log_dir() . '/twitter.log';
$log_status = function($status, $print = true) use ($log_file, $DP_CONFIG) {
	$log = sprintf("[%s] %s\n", gmdate('Y-m-d H:i:s'), $status);
	if ($print) {
		echo $log;
	}

	if (!empty($DP_CONFIG['debug']['enable_twitter_log'])) {
		$fp = @fopen($log_file, 'a');
		if ($fp) {
			@fwrite($fp, $log);
			@fclose($fp);
		}
	}
};

$php_path = dp_get_php_path(true);
if ($is_windows) {
	// this prevents black boxes when started via twitter-windows.php
	$php_path = str_replace('php-win.exe', 'php.exe', $php_path);
}

$start_process = function($account_id, $rest = false) use ($php_path, $is_windows) {
	$rest_arg = ($rest ? ' rest' : '');
	$pipes = array();

	// todo: windows doesn't like the arguments being quoted for some reason...
	$file = basename(__FILE__);
	if (!$is_windows) {
		$file = escapeshellarg($file);
	}

	$account_id = intval($account_id);

	return proc_open("$php_path $file $account_id$rest_arg", array(), $pipes, __DIR__);
};

$runner_pid = null;
$runner_active = $check_runner_active($runner_pid);

if (!empty($argv[1])) {
	/** @var $db \Doctrine\DBAL\Connection */
	$db = $get_db();

	$account = $db->fetchAssoc('
		SELECT *
		FROM twitter_accounts
		WHERE id = ?
	', array($argv[1]));

	if (!$account) {
		$log_status("[Account] Invalid account $argv[1].");
		exit(1);
	}

	// needed for Phirehose
	define('TWITTER_CONSUMER_KEY', \Application\DeskPRO\Service\Twitter::getAgentConsumerKey());
	define('TWITTER_CONSUMER_SECRET', \Application\DeskPRO\Service\Twitter::getAgentConsumerSecret());

	$api = new EpiTwitter(
		\Application\DeskPRO\Service\Twitter::getAgentConsumerKey(),
		\Application\DeskPRO\Service\Twitter::getAgentConsumerSecret(),
		$account['oauth_token'],
		$account['oauth_token_secret']
	);
	$verified = false;
	try {
		$result = $api->get_applicationRate_limit_status();
		if ($result->rate_limit_context) {
			$verified = true;
		}
	} catch (\Exception $e) {}

	if (!$verified) {
		$log_status("[Account $account[id]] Twitter auth could not be verified. Waiting 60 seconds before exiting.");
		sleep(60);
		$log_status("[Account $account[id]] Twitter auth could not be verified. Exiting.");
		exit;
	}

	$consumer = new \Application\DeskPRO\Service\Phirehose\UserStream($account['oauth_token'], $account['oauth_token_secret']);
	$consumer->setWriteCallback(function($data) use ($get_db) {
		$db = $get_db();
		$db->insert('twitter_stream', $data);
		$db->close();
		$db = null;
	});
	$consumer->setAccount($account);

	if (!empty($argv[2]) && $argv[2] == 'rest') {
		$log_status("[Account $account[id] REST] Processor starting with PID " . getmypid() . ". Last processed: $account[last_processed_id]");

		if ($account['last_processed_id']) {
			$max_id = false;

			try {
				$results = $api->get('/statuses/home_timeline.json', array(
					'count' => 200,
					'since_id' => $account['last_processed_id']
				));
				$total = 0;
				foreach ($results AS $result) {
					if (!$max_id || $result->id_str > $max_id) {
						$max_id = $result->id_str;
					}

					$consumer->enqueueStatus(json_encode($result));
					$total++;
				}
				$log_status("[Account $account[id] REST] Missed home timeline: $total");
			} catch (EpiOAuthException $e) {
			} catch (EpiTwitterException $e) {}

			try {
				$results = $api->get('/statuses/mentions_timeline.json', array(
					'count' => 200,
					'include_rts' => 1,
					'since_id' => $account['last_processed_id']
				));
				$total = 0;
				foreach ($results AS $result) {
					if (!$max_id || $result->id_str > $max_id) {
						$max_id = $result->id_str;
					}

					$consumer->enqueueStatus(json_encode($result));
					$total++;
				}
				$log_status("[Account $account[id] REST] Missed mentions: $total");
			} catch (EpiOAuthException $e) {
			} catch (EpiTwitterException $e) {}

			try {
				$results = $api->get('/direct_messages.json', array(
					'count' => 200,
					'since_id' => $account['last_processed_id']
				));
				$total = 0;
				foreach ($results AS $result) {
					if (!$max_id || $result->id_str > $max_id) {
						$max_id = $result->id_str;
					}

					$res = new StdClass();
					$res->direct_message = $result;

					$consumer->enqueueStatus(json_encode($res));
					$total++;
				}
				$log_status("[Account $account[id] REST] Missed received DMs: $total");
			} catch (EpiOAuthException $e) {
			} catch (EpiTwitterException $e) {}

			try {
				$results = $api->get('/direct_messages/sent.json', array(
					'count' => 200,
					'since_id' => $account['last_processed_id']
				));
				$total = 0;
				foreach ($results AS $result) {
					if (!$max_id || $result->id_str > $max_id) {
						$max_id = $result->id_str;
					}

					$res = new StdClass();
					$res->direct_message = $result;

					$consumer->enqueueStatus(json_encode($res));
					$total++;
				}
				$log_status("[Account $account[id] REST] Missed sent DMs: $total");
			} catch (EpiOAuthException $e) {
			} catch (EpiTwitterException $e) {}

			if ($max_id) {
				$db->executeUpdate("
					UPDATE twitter_accounts
					SET last_processed_id = ?
					WHERE id = ? AND last_processed_id < ?
				", array($max_id, $account['id'], $max_id));
			}

			$log_status("[Account $account[id] REST] Completed.");
		} else {
			$log_status("[Account $account[id] REST] Completed (no work).");
		}
		exit;
	}

	$db->close();
	$db = null;
	\Application\DeskPRO\App::getDb()->close();

	$log_status("[Account $account[id]] Processor starting with PID " . getmypid() . ".");

	if (!$runner_active) {
		$log_status("[Account $account[id], PID " . getmypid() . "] Started without parent runner. Can only be terminated manually.");
	}

	$consumer->setCallback(function($status) use ($check_runner_active, $runner_active, $runner_pid, $log_status, $account, $get_db, $start_process) {
		$my_pid = getmypid();

		$log = sprintf(
			"Data received (length: %d), runner pid: %d, memory: %s KB (peak: %s KB)",
			strlen(trim($status)), $runner_pid,
			number_format((memory_get_usage(true) / 1024), 0, ',', '.'),
			number_format(memory_get_peak_usage(true) / 1024, 0, ',', '.')
		);
		$log_status("[Account $account[id], PID $my_pid] $log");
		if (trim($status)) {
			$log_status("\t\t" . trim($status), false);
		}

		/** @var $db \Doctrine\DBAL\Connection */
		$db = $get_db();

		$test_account = $db->fetchAssoc('
			SELECT *
			FROM twitter_accounts
			WHERE id = ?
		', array($account['id']));

		if (!$test_account || $test_account['oauth_token'] != $account['oauth_token']) {
			$log_status("[Account $account[id], PID $my_pid] Account removed or changed. Terminating.");
			exit;
		}

		if ($runner_active) {
			$new_pid = null;
			if (!$check_runner_active($new_pid)) {
				$log_status("[Account $account[id], PID $my_pid] Parent process no longer running. Terminating.");
				exit;
			}

			if ($runner_pid && $new_pid != $runner_pid) {
				$log_status("[Account $account[id], PID $my_pid] Parent process no longer running - new process running in its place. Terminating.");
				exit;
			}
		}

		$status_test = trim($status);
		$status_test = json_decode($status_test);

		if (isset($status_test->text)) {
			$new_id = $status_test->id_str;
		} else if (isset($status_test->direct_message)) {
			$new_id = $status_test->direct_message->id_str;
		} else {
			$new_id = false;
		}

		if ($new_id) {
			$db->executeUpdate("
				UPDATE twitter_accounts
				SET last_processed_id = ?
				WHERE id = ? AND last_processed_id < ?
			", array($new_id, $account['id'], $new_id));
		}

		if (isset($status_test->friends) && $test_account['last_processed_id']) {
			// this is the first message we see when we connect to a stream,
			// so check with the rest API for anything missing
			$start_process($account['id'], true);
		}

		$db->close();
		$db = null;

		gc_collect_cycles();

		return $status;
	});
	$consumer->consume();

	$my_pid = getmypid();

	try {
		$consumer->consume();
	} catch (PhirehoseConnectLimitExceeded $e) {
		$log_status("[Account $account[id], PID $my_pid] Connection limit exceeded: " . $e->getMessage() . ". Likely no permission.");
	} catch (Exception $e) {
		$log_status("[Account $account[id], PID $my_pid] General processor exception: " . $e->getMessage() . " at " . $e->getFile() . ':' . $e->getLine());
	}

	$log_status("[Account $account[id], PID $my_pid] Exiting Normally.");

	exit(0);
}

$timer = 0;
$max_timer = 0;
$sleep_length = 1; // needs to be divisible by 30
$children = array();
$my_pid = getmypid();

if ($runner_active) {
	$log_status("[Runner, PID $my_pid] PID file already exists ($pid_file) with PID $runner_pid. Cannot start new service until this is killed.");
	exit(1);
} else if (file_exists($pid_file)) {
	$log_status("[Runner, PID $my_pid] Previous process terminated unexpectedly. Restarting...");
	@unlink($pid_file);
}

file_put_contents($pid_file, getmypid());

$log_status("[Runner, PID $my_pid] Starting with PID " . getmypid() . ".");

if (function_exists('pcntl_signal')) {
	declare(ticks = 1);
	pcntl_signal(SIGTERM,  function() { exit; });
}

register_shutdown_function(function() use ($log_status, $my_pid) {
	global $children;

	foreach ($children AS $account_id => $process) {
		$info = proc_get_status($process);
		@proc_terminate($process);
		$log_status("[Runner, PID $my_pid] Terminated child PID $info[pid] for account $account_id during normal shutdown.");
	}
});
register_shutdown_function(function() use ($log_status, $my_pid) {
	global $pid_file;
	if (file_exists($pid_file) && intval(file_get_contents($pid_file)) != 0) {
		@unlink($pid_file);
	}

	$log_status("[Runner, PID $my_pid] Normal shutdown completed.");
});

while (true) {
	if ($timer % 30 == 0 && $timer > 0) {
		$running_pids = array();
		foreach ($children AS $account_id => $process) {
			$info = proc_get_status($process);
			if (!$info['running']) {
				@proc_close($process);
				unset($children[$account_id]);
			} else {
				$running_pids[] = $info['pid'];
			}
		}

		$log_status(sprintf("[Runner, PID $my_pid] Ping. Running: %d (PIDs: %s).", count($running_pids), implode(', ', $running_pids)));
	}

	if ($timer % 30 == 0) {
		/** @var $db \Doctrine\DBAL\Connection */
		$db = $get_db();

		$db->executeUpdate("
			INSERT INTO settings
				(name, value)
			VALUES
				('core.twitter_ping', ?)
			ON DUPLICATE KEY UPDATE value = VALUES(value)
		", array(time()));

		$kill = false;
		if (file_exists($pid_file)) {
			$pid = file_get_contents($pid_file);
			if ($pid != getmypid()) {
				$kill = true;
			}
		} else {
			$kill = true;
		}

		if ($kill) {
			foreach ($children AS $account_id => $process) {
				@proc_terminate($process);
			}

			$log_status("[Runner, PID $my_pid] PID file removed or has a different PID. Terminating.");
			exit;
		}

		$matched = array();

		foreach ($db->fetchAll('SELECT * FROM twitter_accounts') as $account) {
			$account_id = $account['id'];
			if (!isset($children[$account_id])) {
				if ($timer > 0) {
					$log_status("[Runner, PID $my_pid] Starting or restarting processor for account $account_id.");
				}
				$children[$account_id] = $start_process($account_id, false);
			}
			$matched[] = $account_id;
		}

		foreach ($children AS $account_id => $process) {
			if (!in_array($account_id, $matched)) {
				// has been deleted
				$log_status("[Runner, PID $my_pid] Terminating processor for account $account_id - account no longer to be processed.");
				@proc_terminate($process);
			}
		}

		$db->close();
		$db = null;

		gc_collect_cycles();
	}

	if (!count($children)) {
		$log_status("[Runner, PID $my_pid] No children running. Exiting.");
		exit;
	}

	if ($max_timer && $timer >= $max_timer) {
		$log_status("[Runner, PID $my_pid] Max timer ($max_timer) reached. Exiting.");
		exit;
	}

	sleep($sleep_length);
	$timer += $sleep_length;
}

$log_status("[Runner, PID $my_pid Exiting Normally.");