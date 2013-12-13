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

/**
 * These are the consumer key and secret for the app to use.
 *
 * The token and secret are for the user that owns the app. (Generated on dev.twitter.com)
 */
define('TWITTER_CONSUMER_KEY', '');
define('TWITTER_CONSUMER_SECRET', '');
define('TWITTER_OAUTH_TOKEN', '');
define('TWITTER_OAUTH_TOKEN_SECRET', '');

/**
 * If true, uses site streams; if false, uses user streams.
 */
define('TWITTER_SITE_STREAM', true);

/**
 * The DB information to connect to. The DB name will store a couple of its
 * own tables. The user must be able to push data to individual DBs storing
 * each account. It must be able to insert into the twitter_stream table.
 *
 * Tables that must be defined:
 *
CREATE TABLE `cloud_twitter_stream` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `date_created` datetime NOT NULL,
  `event` varchar(50) NOT NULL,
  `data` longblob NOT NULL COMMENT '(DC2Type:object)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE `cloud_twitter_associations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `db` varchar(75) not null,
  `user_id` bigint NOT NULL,
  `account_id` int not null,
  `oauth_token` varchar(255) not null,
  `oauth_token_secret` varchar(255) not null,
  PRIMARY KEY (`id`),
  KEY user_id (user_id)
);

CREATE TABLE `cloud_twitter_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_type` varchar(25) not null,
  `db` varchar(75) not null,
  `user_id` bigint NOT NULL,
  `account_id` int not null,
  `data` longblob NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`),
  KEY user_id (user_id)
);
 *
 * This needs to be paired with the following in config.php: (with correct values)
 *
$DP_CONFIG['twitter'] = array(
  'agent_consumer_key' => '',
  'agent_consumer_secret' => '',
  'user_consumer_key' => '',
  'user_consumer_secret' => '',
  'cloud_db_host' => '',
  'cloud_db_user' => '',
  'cloud_db_password' => '',
  'cloud_db_dbname' => '',
);
 */
define('CLOUD_DB_HOST', '');
define('CLOUD_DB_USER', '');
define('CLOUD_DB_PASSWORD', '');
define('CLOUD_DB_NAME', '');

/**
 * Control logging and general operations.
 *
 * Global logging is useful for debugging, but can get pretty big.
 */
define('PID_FILE', __DIR__  .'/twitter-cloud.pid');
define('CONTROL_FILE', __DIR__ . '/twitter-cloud-control.txt');
define('GLOBAL_LOGGING', true);
define('GLOBAL_LOG_FILE', __DIR__ . '/twitter-cloud.log');


/*****************************************************************************/

ini_set('display_errors', true);
error_reporting(E_ALL | E_STRICT);
setlocale(LC_CTYPE, 'C');
date_default_timezone_set('UTC');
ini_set('default_charset', 'UTF-8');
set_time_limit(0);
define('DP_ROOT', realpath(__DIR__ . '/../../../'));
define('DP_WEB_ROOT', realpath(__DIR__ . '/../../../../'));
if (!defined('DP_CONFIG_FILE')) define('DP_CONFIG_FILE', DP_WEB_ROOT . '/config.php');
require_once DP_ROOT.'/sys/load_config.php';
dp_load_config();

require DP_ROOT.'/vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require DP_ROOT.'/src/Orb/Util/ClassLoader.php';
require DP_ROOT.'/sys/Kernel/KernelErrorHandler.php';
require_once DP_ROOT.'/sys/autoload.php';

set_error_handler('DeskPRO\\Kernel\\KernelErrorHandler::handleError', E_ALL | E_STRICT);
set_exception_handler('DeskPRO\\Kernel\\KernelErrorHandler::handleException');

$db_conf = array(
	'host' => CLOUD_DB_HOST,
	'user' => CLOUD_DB_USER,
	'password' => CLOUD_DB_PASSWORD,
	'dbname' => CLOUD_DB_NAME,
	'driver' => 'pdo_mysql'
);

$pid_file = PID_FILE;
$is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

$get_db = function($db_name = null) use ($db_conf) {
	$conf = $db_conf;
	if ($db_name) {
		$conf['dbname'] = $db_name;
	}

	return \Doctrine\DBAL\DriverManager::getConnection($conf);
};

$check_runner_active = function(&$pid = null) use($is_windows) {
	$running = false;
	$pid = null;

	if (file_exists(PID_FILE)) {
		$pid = intval(file_get_contents(PID_FILE));

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

$log_status = function($status, $print = true) {
	$log = sprintf("[%s] %s\n", gmdate('Y-m-d H:i:s'), $status);
	if ($print) {
		echo $log;
	}

	if (GLOBAL_LOGGING) {
		$fp = @fopen(GLOBAL_LOG_FILE, 'a');
		if ($fp) {
			@fwrite($fp, $log);
			@fclose($fp);
		}
	}
};

/**************************************************************************************/

class CloudSiteStream extends \UserstreamPhirehose
{
	const URL_BASE         = 'https://sitestream.twitter.com/1.1/';
	const METHOD_SITE      = 'site';

	/**
	 * @var \Closure|null
	 */
	protected $write_callback;

	/**
	 * @var \Closure
	 */
	protected $callback;

	/**
	 * @var array
	 */
	protected $log = array();

	protected $log_callback;

	public function __construct($username, $password)
	{
		parent::__construct($username, $password, self::METHOD_SITE, self::FORMAT_JSON, self::CONNECT_OAUTH);
	}

	/**
	 * Suppress Phirehose @error_log output.
	 *
	 * @param string $message
	 * @return void
	 */
	protected function log($message)
	{
		$this->log[] = $message;

		if ($this->log_callback) {
			$callback = $this->log_callback;
			$callback($message);
		}
	}

	/**
	 * @param \Closure|null $callback
	 */
	public function setLogCallback(\Closure $callback = null)
	{
		$this->log_callback = $callback;
	}

	/**
	 * @return \Closure|null
	 */
	public function getLogCallback()
	{
		return $this->log_callback;
	}


	/**
	 * @param \Closure|null $callback
	 */
	public function setWriteCallback(\Closure $callback = null)
	{
		$this->write_callback = $callback;
	}

	/**
	 * @return \Closure|null
	 */
	public function getWriteCallback()
	{
		return $this->write_callback;
	}

	/**
	 * @return \Closure|null
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * @param \Closure|null $callback
	 */
	public function setCallback(\Closure $callback = null)
	{
		$this->callback = $callback;
	}

	/**
	 * Process raw streaming data.
	 *
	 * @param string $status
	 * @return void
	 */
	public function enqueueStatus($status)
	{
		try {
			if ($this->callback) {
				$callback = $this->callback;
				$status = $callback($status, $this);
			}

			// skip "ping -> pong"
			if (null === $status || !strlen(trim($status))) {
				return;
			}

			$status = trim($status);

			// decode json
			$status = json_decode($status);
			$event = 'unknown';

			if (!empty($status->control)) {
				$fp = @fopen(CONTROL_FILE, 'a');
				if ($fp) {
					@fwrite($fp, getmypid() . ':' . $status->control_uri . "\n");
					@fclose($fp);
				}
				return;
			}

			// unwrap
			$for_user_id = $status->for_user;
			$status = $status->message;

			// check if status is a tweet
			if (isset($status->text)) {
				$event = 'status';
			}

			// check direct message
			if (isset($status->direct_message)) {
				$event = 'message';
			}

			// check event
			if (isset($status->event)) {
				$event = 'event';
			}

			// check friend list
			if (isset($status->friends)) {
				$event = 'friends';
			}

			if (isset($status->delete)) {
				$event = 'delete';
			}

			$data = array(
				'user_id' => $for_user_id,
				'event' => $event,
				'data' => serialize($status),
				'date_created' => gmdate('Y-m-d H:i:s')
			);

			if ($this->write_callback) {
				$callback = $this->write_callback;
				$callback($data);
			} else {
				throw new \Exception("No write callback - can't process");
			}
		} catch (\Exception $e) {
			\DeskPRO\Kernel\KernelErrorHandler::handleException($e, false);
		}
	}
}

/**************************************************************************************/

class CloudUserStream extends \UserstreamPhirehose
{
	const URL_BASE         = 'https://userstream.twitter.com/1.1/';

	/**
	 * @var \Closure|null
	 */
	protected $write_callback;

	/**
	 * @var \Closure
	 */
	protected $callback;

	/**
	 * @var integer
	 */
	protected $user_id;

	/**
	 * @var array
	 */
	protected $log = array();

	protected $log_callback;


	/**
	 * Suppress Phirehose @error_log output.
	 *
	 * @param string $message
	 * @return void
	 */
	protected function log($message)
	{
		$this->log[] = $message;

		if ($this->log_callback) {
			$callback = $this->log_callback;
			$callback($message);
		}
	}

	/**
	 * @param \Closure|null $callback
	 */
	public function setLogCallback(\Closure $callback = null)
	{
		$this->log_callback = $callback;
	}

	/**
	 * @return \Closure|null
	 */
	public function getLogCallback()
	{
		return $this->log_callback;
	}

	public function setUserId($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getUserId()
	{
		return $this->user_id;
	}


	/**
	 * @param \Closure|null $callback
	 */
	public function setWriteCallback(\Closure $callback = null)
	{
		$this->write_callback = $callback;
	}

	/**
	 * @return \Closure|null
	 */
	public function getWriteCallback()
	{
		return $this->write_callback;
	}

	/**
	 * @return \Closure|null
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * @param \Closure|null $callback
	 */
	public function setCallback(\Closure $callback = null)
	{
		$this->callback = $callback;
	}

	/**
	 * Process raw streaming data.
	 *
	 * @param string $status
	 * @return void
	 */
	public function enqueueStatus($status)
	{
		try {
			if ($this->callback) {
				$callback = $this->callback;
				$status = $callback($status, $this);
			}

			// skip "ping -> pong"
			if (null === $status || !strlen(trim($status))) {
				return;
			}

			$status = trim($status);

			// decode json
			$status = json_decode($status);
			$event = 'unknown';

			// check if status is a tweet
			if (isset($status->text)) {
				$event = 'status';
			}

			// check direct message
			if (isset($status->direct_message)) {
				$event = 'message';
			}

			// check event
			if (isset($status->event)) {
				$event = 'event';
			}

			// check friend list
			if (isset($status->friends)) {
				$event = 'friends';
			}

			if (isset($status->delete)) {
				$event = 'delete';
			}

			$data = array(
				'user_id' => $this->user_id,
				'event' => $event,
				'data' => serialize($status),
				'date_created' => gmdate('Y-m-d H:i:s')
			);

			if ($this->write_callback) {
				$callback = $this->write_callback;
				$callback($data);
			} else {
				throw new \Exception("No write callback - can't process");
			}
		} catch (\Exception $e) {
			\DeskPRO\Kernel\KernelErrorHandler::handleException($e, false);
		}
	}
}

/**************************************************************************************/

class EpiSiteStreamTwitter extends EpiTwitter
{
	public function siteStreamRequest($method, $endpoint, $params = null)
	{
		$url = 'https://sitestream.twitter.com' . $endpoint;
		$resp= new EpiTwitterJson(call_user_func(array($this, 'httpRequest'), $method, $url, $params, $this->isMultipart($params)), $this->debug);
		if(!$this->isAsynchronous)
		  $resp->response;

		return $resp;
	}
}

/**************************************************************************************/

$runner_pid = null;
$runner_active = $check_runner_active($runner_pid);

if (!empty($argv[1])) {
	if (!$runner_active) {
		echo "Can only be run when the runner is active.";
		exit(1);
	}

	$user_ids = $argv;
	unset($argv[0]);
	$user_ids = array_map(function($i) { return strval($i + 0); }, $user_ids);
	foreach ($user_ids AS $k => $id) {
		if (!$id) {
			unset($user_ids[$k]);
		}
	}

	if (!$user_ids) {
		echo "No users to connect to.";
		exit(1);
	}

	$log_status("[Site Stream] Processor starting with PID " . getmypid() . " for users: " . implode(',', $user_ids) . ".");

	if (TWITTER_SITE_STREAM) {
		$consumer = new CloudSiteStream(TWITTER_OAUTH_TOKEN, TWITTER_OAUTH_TOKEN_SECRET);
		$consumer->setFollow($user_ids);
	} else {
		$user_id = reset($user_ids);
		$db = $get_db();
		$user = $db->fetchAssoc("
			SELECT *
			FROM cloud_twitter_associations
			WHERE user_id = ?
			ORDER BY id DESC
			LIMIT 1
		", array($user_id));
		$db->close();
		$db = null;

		if (!$user) {
			echo "User association for $user_id could not be found.\n";
			exit(2);
		}

		$consumer = new CloudUserStream($user['oauth_token'], $user['oauth_token_secret']);
		$consumer->setUserId($user['user_id']);
	}

	/*$consumer->setLogCallback(function($message) {
		echo "$message\n";
	});*/
	$consumer->setWriteCallback(function($data) use ($get_db) {
		$db = $get_db();
		$db->insert('cloud_twitter_stream', $data);
		$db->close();
		$db = null;
	});
	$consumer->setCallback(function($status, $consumer) use ($check_runner_active, $runner_pid, $log_status, $get_db) {
		$my_pid = getmypid();

		if ($consumer instanceof CloudUserStream) {
			$message_prefix = "[User Stream, PID $my_pid, User " . $consumer->getUserId() . "]";
		} else {
			$message_prefix = "[Site Stream, PID $my_pid]";
		}

		$log = sprintf(
			"Data received (length: %d), runner pid: %d, memory: %s KB (peak: %s KB)",
			strlen(trim($status)), $runner_pid,
			number_format((memory_get_usage(true) / 1024), 0, ',', '.'),
			number_format(memory_get_peak_usage(true) / 1024, 0, ',', '.')
		);
		$log_status("{ $log");
		if (trim($status)) {
			$log_status("\t\t" . trim($status), false);
		}

		gc_collect_cycles();

		$new_pid = null;
		if (!$check_runner_active($new_pid)) {
			$log_status("$message_prefix Parent process no longer running. Terminating.");
			exit;
		}

		if ($runner_pid && $new_pid != $runner_pid) {
			$log_status("$message_prefix Parent process no longer running - new process running in its place. Terminating.");
			exit;
		}

		if ($consumer instanceof CloudUserStream) {
			$db = $get_db();
			$user = $db->fetchAssoc("
				SELECT *
				FROM cloud_twitter_associations
				WHERE user_id = ?
				ORDER BY id DESC
				LIMIT 1
			", array($consumer->getUserId()));
			$db->close();
			$db = null;

			if (!$user) {
				$log_status("$message_prefix User ID " . $consumer->getUserId() . " is not longer being retrieved. Terminating.");
				exit;
			}
		}

		return $status;
	});

	$my_pid = getmypid();

	try {
		$consumer->consume();
	} catch (PhirehoseConnectLimitExceeded $e) {
		$log_status("[Site Stream, PID $my_pid] Connection limit exceeded: " . $e->getMessage() . ". Likely no permission.");
	} catch (Exception $e) {
		$log_status("[Site Stream, PID $my_pid] General processor exception: " . $e->getMessage() . " at " . $e->getFile() . ':' . $e->getLine());
	}

	$log_status("[Site Stream, PID $my_pid] Exiting Normally.");

	exit(0);
}

/**************************************************************************************/

$timer = 0;
$sleep_length = 1; // needs to be divisible by 15
$children = array();
$php_path = dp_get_php_path(true);

if ($is_windows) {
	// this prevents black boxes when started via twitter-windows.php
	$php_path = str_replace('php-win.exe', 'php.exe', $php_path);
}

$my_pid = getmypid();

if ($runner_active) {
	$log_status("[Runner, PID $my_pid] PID file already exists ($pid_file) with PID $runner_pid. Cannot start new service until this is killed.");
	exit(1);
} else if (file_exists(PID_FILE)) {
	$log_status("[Runner, PID $my_pid] Previous process terminated unexpectedly. Restarting...");
	@unlink(PID_FILE);
}

file_put_contents(PID_FILE, getmypid());
file_put_contents(CONTROL_FILE, '');

$log_status("[Runner, PID $my_pid] Starting with PID " . getmypid() . ".");

if (function_exists('pcntl_signal')) {
	declare(ticks = 1);
	pcntl_signal(SIGTERM,  function() { exit; });
}

register_shutdown_function(function() use ($log_status, $my_pid) {
	global $children;

	foreach ($children AS $process) {
		$info = proc_get_status($process);
		@proc_terminate($process);
		$log_status("[Runner, PID $my_pid] Terminated child PID $info[pid] during normal shutdown.");
	}
});
register_shutdown_function(function() use ($log_status, $my_pid) {
	if (file_exists(PID_FILE) && intval(file_get_contents(PID_FILE)) != 0) {
		@unlink(PID_FILE);
	}

	$log_status("[Runner, PID $my_pid] Normal shutdown completed.");
});

$associations = null;
$child_user_map = array();
$control_stream_map = array();
$add = array();
$remove = array();
$rest_api = new EpiSiteStreamTwitter(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_OAUTH_TOKEN, TWITTER_OAUTH_TOKEN_SECRET);

while (true) {
	if ($timer % 30 == 0 && $timer > 0) {
		$running_pids = array();
		foreach ($children AS $k => $process) {
			$info = proc_get_status($process);
			if (!$info['running']) {
				@proc_close($process);
				unset($children[$k]);
			} else {
				$running_pids[] = $info['pid'];
			}
		}

		$log_status(sprintf("[Runner, PID $my_pid] Ping. Running: %d (PIDs: %s).", count($running_pids), implode(', ', $running_pids)));
	}

	if ($timer % 15 == 0) {
		/** @var $db \Doctrine\DBAL\Connection */
		$db = $get_db();

		$updates = $db->fetchAll("
			SELECT stream.*, associations.db, associations.account_id
			FROM cloud_twitter_stream AS stream
			LEFT JOIN cloud_twitter_associations AS associations ON (stream.user_id = associations.user_id)
			ORDER BY stream.id
		");
		foreach ($updates AS $update) {
			if ($update['account_id']) {
				$db->executeUpdate("
					INSERT INTO `$update[db]`.twitter_stream
						(account_id, date_created, event, data)
					VALUES (?, ?, ?, ?)
				", array($update['account_id'], $update['date_created'], $update['event'], $update['data']));
			}

			// this may run multiple times if multiple accounts are associated with the same user,
			// but no big deal
			$db->delete('cloud_twitter_stream', array('id' => $update['id']));
		}

		$kill = false;
		if (file_exists(PID_FILE)) {
			$pid = file_get_contents(PID_FILE);
			if ($pid != getmypid()) {
				$kill = true;
			}
		} else {
			$kill = true;
		}

		if ($kill) {
			foreach ($children AS $process) {
				@proc_terminate($process);
			}

			$log_status("[Runner, PID $my_pid] PID file removed or has a different PID. Terminating.");
			exit;
		}

		if ($associations === null) {
			$results = $db->fetchAll("
				SELECT user_id, COUNT(*) AS total
				FROM cloud_twitter_associations
				GROUP BY user_id
			");
			$associations = array();
			foreach ($results AS $result) {
				$associations[$result['user_id']] = $result['total'];
			}
		}

		$control_stream_map = array();
		foreach (explode("\n", file_get_contents(CONTROL_FILE)) AS $line) {
			$line = trim($line);
			if ($line) {
				list($process_pid, $stream) = explode(':', $line);
				$control_stream_map[$process_pid] = $stream;
			}
		}

		foreach ($db->fetchAll('SELECT * FROM cloud_twitter_messages ORDER BY id') AS $message) {
			if ($message['message_type'] == 'add') {
				$data = @unserialize($message['data']);
				if ($data) {
					$affected = $db->executeUpdate("
						INSERT INTO cloud_twitter_associations
							(db, user_id, account_id, oauth_token, oauth_token_secret)
						VALUES
							(?, ?, ?, ?, ?)
						ON DUPLICATE KEY UPDATE
							oauth_token = VALUES(oauth_token),
							oauth_token_secret = VALUES(oauth_token_secret)
					", array($message['db'], $message['user_id'], $message['account_id'], $data['oauth_token'], $data['oauth_token_secret']));
					if ($affected == 1) {
						// an insert (2 affected is an update)
						if (isset($associations[$message['user_id']])) {
							$associations[$message['user_id']]++;
						} else {
							$associations[$message['user_id']] = 1;
							$add[] = $message['user_id'];
						}
					}
				}
			} else if ($message['message_type'] == 'remove') {
				$db->delete('cloud_twitter_associations', array(
					'db' => $message['db'],
					'user_id' => $message['user_id']
				));

				if (isset($associations[$message['user_id']])) {
					$associations[$message['user_id']]--;
					if ($associations[$message['user_id']] <= 0) {
						unset($associations[$message['user_id']]);
						$remove[] = $message['user_id'];
					}
				}
			}

			$db->delete('cloud_twitter_messages', array('id' => $message['id']));
		}

		$db->close();
		$db = null;

		if ($timer == 0) {
			$add = array_merge($add, array_keys($associations));
			$add = array_unique($add);
		}

		if ($remove) {
			foreach ($remove AS $user_id) {
				foreach ($child_user_map AS $child_pid => &$user_ids) {
					$key = array_search($user_ids, $user_id);
					if ($key !== false) {
						unset($user_ids[$key]);
						if (TWITTER_SITE_STREAM && isset($control_stream_map[$child_pid]) && isset($children[$child_pid])) {
							$control = $control_stream_map[$child_pid];
							try {
								$rest_api->siteStreamRequest('POST', "$control/remove_user.json", array(
									'user_id' => $user_id
								));
							} catch (\EpiTwitterException $e) {
							} catch (\EpiOAuthException $e) {}
						}
					}
				}
			}
		}

		foreach ($child_user_map AS $child_pid => $user_ids) {
			if (!isset($children[$child_pid])) {
				$add = array_merge($add, $user_ids);
				$add = array_unique($add);
				unset($child_user_map[$child_pid]);
			}
		}

		foreach ($add AS $k => $v) {
			if (!$v) {
				unset($add[$k]);
			}
		}

		if ($add) {
			if (TWITTER_SITE_STREAM) {
				// site stream implementation
				foreach ($child_user_map AS $child_pid => $user_ids) {
					while (count($user_ids) < 1000 && $add) {
						$slice_size = min(1000 - count($user_ids), 100);
						$user_ids = array_slice($add, 0, $slice_size);
						$add = array_slice($add, $slice_size);
						$child_user_map[$info['pid']] = array_merge($child_user_map[$info['pid']], $user_ids);

						$control = $control_stream_map[$child_pid];
						try {
							$rest_api->siteStreamRequest('POST', "$control/add_user.json", array(
								'user_id' => implode(',', $user_ids)
							));
						} catch (\EpiTwitterException $e) {
						} catch (\EpiOAuthException $e) {}
						break;
					}

					if (!$add) {
						break;
					}
				}

				if ($add) {
					// need to spawn a new process
					$user_ids = array_slice($add, 0, 100);
					$add = array_slice($add, 100);

					$log_status("[Runner, PID $my_pid] Starting new processor for users " . implode(', ', $user_ids) . ".");

					$pipes = array();
					// windows doesn't like the arguments being quoted for some reason...
					$process = proc_open("$php_path " . basename(__FILE__) . " " . implode(' ', $user_ids), array(), $pipes, __DIR__);
					$info = proc_get_status($process);
					$children[$info['pid']] = $process;
					$child_user_map[$info['pid']] = $user_ids;

					// can't add more here as we won't have the control stream yet
				}
			} else {
				// user stream implementation
				foreach ($add AS $user_id) {
					if ($timer > 0) {
						$log_status("[Runner, PID $my_pid] Starting new processor for user $user_id.");
					}

					$pipes = array();
					// windows doesn't like the arguments being quoted for some reason...
					$process = proc_open("$php_path " . basename(__FILE__) . " $user_id", array(), $pipes, __DIR__);
					$info = proc_get_status($process);
					$children[$info['pid']] = $process;
					$child_user_map[$info['pid']] = array($user_id);
				}

				$add = array();
			}
		}

		gc_collect_cycles();
	}

	sleep($sleep_length);
	$timer += $sleep_length;
}