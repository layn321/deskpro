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
 * @subpackage
 */

namespace Application\DeskPRO\Service;
use Application\DeskPRO\App;

class ErrorReporter
{
	public static function getBasicData($send_all_stats = false)
	{
		if (isset($GLOBALS['DP_DISABLE_SENDREPORTS'])) {
			return array();
		}

		if (!defined('DP_BUILD_NUM')) {
			return array();
		}

		$reduced_lic_reports = false;
		if (class_exists('Application\\DeskPRO\\App')) {
			try {
				if (App::getSetting('core.enable_reduced_lic_reports')) {
					$reduced_lic_reports = true;
				}
			} catch (\Exception $e) {}
		}

		if ($send_all_stats) {
			$reduced_lic_reports = false;
		}

		if ($reduced_lic_reports) {
			$info = array(
				'client_user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
				'build'             => DP_BUILD_TIME,
				'build_num'         => DP_BUILD_NUM,
			);
		} else {
			if (class_exists('Application\\DeskPRO\\App')) {
				try {
					$db = App::getDb();
				} catch (\Exception $e) {
					$db = null;
				}
				$stats_fetcher = new \Application\InstallBundle\Data\ServerStats($db);
				$all_stats = $stats_fetcher->getStats();
			} else {
				$all_stats = array();
			}

			$info = array(
				'root'              => defined('DP_ROOT')                 ? DP_ROOT : '',
				'os'                => isset($all_stats['server_os'])     ? $all_stats['server_os'] : '',
				'web_server'        => isset($all_stats['web_server'])    ? $all_stats['web_server'] : '',
				'php_version'       => isset($all_stats['php_version'])   ? $all_stats['php_version'] : '',
				'apc_version'       => isset($all_stats['apc_version'])   ? $all_stats['apc_version'] : '',
				'mysql_version'     => isset($all_stats['mysql_version']) ? $all_stats['mysql_version'] : '',
				'server_ip'         => isset($_SERVER['SERVER_ADDR'])     ? $_SERVER['SERVER_ADDR'] : '',
				'client_ip'         => isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR'] : '',
				'client_referrer'   => isset($_SERVER['HTTP_REFERER'])    ? $_SERVER['HTTP_REFERER'] : '',
				'client_user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
				'client_request'    => isset($_REQUEST)                   ? implode(', ', array_keys($_REQUEST)) : '',
				'build'             => DP_BUILD_TIME,
				'build_num'         => DP_BUILD_NUM,
			);

			if ($send_all_stats && $all_stats) {
				$info = array_merge($info, $all_stats);
			}

			$info['hostname'] = @gethostname();
		}

		if (defined('DP_REQUEST_URL')) {
			$url = DP_REQUEST_URL;
		} elseif (defined('DP_INTERFACE')) {
			$url = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
			if (class_exists('Application\\DeskPRO\\App')) {
				try {
					$url = App::getRequest()->getUri();
				} catch (\Exception $e) {}
			}
		} else {
			$url = '';
		}

		if (php_sapi_name() == 'cli') {
			$url = 'Command: ' . implode(' ', $_SERVER['argv']);
		}

		$info['url'] = $url;

		if (isset($GLOBALS['DP_CONFIG']['debug']['dev']) && $GLOBALS['DP_CONFIG']['debug']['dev']) {
			$info['DEV_MODE'] = 1;
		}

		if (class_exists('Application\\DeskPRO\\App', false)) {
			try {
				$kernel = App::getKernel();
				$info['Kernel::getEnvironment'] = $kernel->getEnvironment();
				$info['Kernel::isDebug']        = $kernel->isDebug() ? 'true' : 'false';
			} catch (\Exception $e) {}
		}

		if ((defined('DP_INTERFACE') && DP_INTERFACE != 'install') || (!isset($GLOBALS['DP_IS_INSTALL']) || !$GLOBALS['DP_IS_INSTALL'])) {
			try {
				$info['license_id'] = \DeskPRO\Kernel\License::getLicense()->getLicenseId();
				$info['is_demo']    = \DeskPRO\Kernel\License::getLicense()->isDemo();
			} catch (\Exception $e) {
				$info['license_id'] = '';
				$info['is_demo'] = false;
			}
		}

		$instance_data = dp_get_config('instance_data.report');
		if ($instance_data) {
			$info = array_merge($instance_data, $info);
		}

		return $info;
	}


	/**
	 * Checks a hash against the db to see if we should avoid sending the error report
	 * too many times. The system sends at most one report a day.
	 *
	 * @static
	 * @param $hash
	 */
	public static function shouldThrottleReport($hash)
	{
		if (!class_exists('Application\\DeskPRO\\App')) {
			return false;
		}

		try {
			$db = App::getDb();
			$exist_date = $db->fetchColumn("
				SELECT date_expire
				FROM tmp_data
				WHERE name = ?
				LIMIT 1
			", array('submitreport_' . $hash));

			if ($exist_date) {
				$date = \DateTime::createFromFormat('Y-m-d H:i:s', $exist_date);

				// If its under 24 hours, then we dont send the report
				if (time() - $date->getTimestamp() < 86400) {
					return true;
				}
			}
		} catch (\Exception $e) {};


		return false;
	}


	/**
	 * Submits a PHP error. $errinfo is a standard error info array, see KernelErrorHandler::getExceptionInfo
	 * and KernelErrorHandler::getErrorInfo.
	 *
	 * @static
	 * @param array $errinfo
	 */
	public static function reportPhpError(array $errinfo)
	{
		if (isset($GLOBALS['DP_DISABLE_SENDREPORTS'])) {
			return array();
		}

		if (!defined('DP_BUILD_NUM')) {
			return array();
		}

		$info = self::getBasicData();

		if ($errinfo['type'] == 'exception') {

			$ignore_types = array(
				'Application\\DeskPRO\\Command\\Exception\\CronRunningException',
				'Application\\DeskPRO\\FileStorage\\Exception\\PermissionException',
				'Application\\DeskPRO\\HttpKernel\\Exception\\NoPermissionException',
				'Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException',
			);

			if (in_array($errinfo['exception_type'], $ignore_types)) {
				return;
			}

			if (isset($errinfo['exception']) && $errinfo['exception'] instanceof \PDOException) {
				$ignore_codes = array(
					'HY000', // MySQL server has gone away
					'1203',  // more than 'max_user_connections' active connections
				);

				if (in_array($errinfo['exception']->getCode(), $ignore_codes)) {
					return;
				}
			}

			$copy_keys = array(
				'type', 'session_name', 'exception_type', 'die', 'pri',
				'trace', 'summary', 'errstr', 'errname', 'errno', 'errfile', 'errline',
				'display', 'process_log'
			);
			$info['local_hash'] = md5('php' . $errinfo['exception_type'] . $errinfo['errfile'] . $errinfo['errline']);
		} else {
			$copy_keys = array(
				'type', 'session_name', 'die', 'pri',
				'trace', 'summary', 'errstr', 'errname', 'errno', 'errfile', 'errline',
				'display', 'process_log'
			);
			$info['local_hash'] = md5('php' . $errinfo['errname'] . $errinfo['errfile'] . $errinfo['errline']);
		}

		$send_info = array();
		foreach ($copy_keys as $k) {
			$send_info[$k] = isset($errinfo[$k]) ? $errinfo[$k] : null;
		}

		$info['error_type'] = 'php';
		$info['error_info'] = $send_info;

		// Attempt to attach trailing errors from server log files
		foreach (array('server-phperr-web.log', 'cli-phperr.log') as $logfile) {
			$logpath = dp_get_log_dir() . '/' . $logfile;
			if (!file_exists($logpath)) {
				continue;
			}

			$log = file_get_contents($logpath, false, null, max(0, filesize($logpath) - 40960));

			$info[$logfile] = $log;
		}

		if (!self::shouldThrottleReport($info['local_hash'])) {
			self::sendReport('report-error', $info, 10);
		}
	}


	/**
	 * Submits a JS error. $errinfo is a standard error info array from \Application\DeskPRO\Controller\DataController::logJsErrorAction
	 *
	 * @static
	 * @param array $errinfo
	 */
	public static function reportJsError(array $errinfo)
	{
		if (isset($GLOBALS['DP_DISABLE_SENDREPORTS'])) {
			return;
		}

		$info = array();

		if (isset($errinfo['script']) && isset($errinfo['line'])) {
			$info['local_hash'] = md5('js' . $errinfo['script'] . $errinfo['line']);
		} else {
			$info['local_hash'] = md5('js' . $errinfo['message']);
		}

		$info['error_type'] = 'js';
		$info['error_info'] = $errinfo;

		if (!self::shouldThrottleReport($info['local_hash'])) {
			self::sendReport('report-error', $info, 10);
		}
	}


	public static function sendInstallReport($data)
	{
		$info = $data;

		if (isset($info['errinfo'])) {

			$errinfo = $info['errinfo'];
			unset($info['errinfo']);

			if ($errinfo['type'] == 'exception') {
				$copy_keys = array(
					'type', 'session_name', 'exception_type', 'die', 'pri',
					'trace', 'summary', 'errstr', 'errname', 'errno', 'errfile', 'errline',
					'display'
				);
				$info['local_hash'] = md5('php' . $errinfo['exception_type'] . $errinfo['errfile'] . $errinfo['errline']);
			} else {
				$copy_keys = array(
					'type', 'session_name', 'die', 'pri',
					'trace', 'summary', 'errstr', 'errname', 'errno', 'errfile', 'errline',
					'display'
				);
				$info['local_hash'] = md5('php' . $errinfo['errname'] . $errinfo['errfile'] . $errinfo['errline']);
			}

			$send_info = array();
			foreach ($copy_keys as $k) {
				$send_info[$k] = isset($errinfo[$k]) ? $errinfo[$k] : null;
			}

			$info['error_type'] = 'php';
			$info['error_info'] = $send_info;
		}

		unset($info['local_hash']);
		self::sendReport('report-install', $info, 12);
	}


	/**
	 * Sends a report to the logging server if local_hash exists in $data, it'll save the hash
	 * to ensure the report isn't re-sent too many times (once every 24 hours).
	 *
	 * @static
	 * @param $service
	 * @param array $data
	 * @param int $timeout
	 */
	public static function sendReport($service, array $data = array(), $timeout = 8)
	{
		if (isset($GLOBALS['DP_DISABLE_SENDREPORTS'])) {
			return;
		}

		$data = array_merge(self::getBasicData(), $data);

		if (isset($data['local_hash'])) {
			try {
				App::getDb()->replace('tmp_data', array(
					'name'         => 'submitreport_' . $data['local_hash'],
					'auth'         => substr(md5(microtime()) . mt_rand(1,999), 0, 15),
					'data'         => serialize(array()),
					'date_created' => date('Y-m-d H:i:s'),
					'date_expire'  => date('Y-m-d H:i:s', strtotime('+24 hours')),
				));
			} catch (\Exception $e) {
				return;
			}
		}

		// Make sure payload isnt too big
		foreach ($data as &$d) {
			if (!is_string($d)) continue;
			if (isset($d[512001])) {
				$d = substr($d, 0, 512000);
				$d .= ' (Truncated)';
			}
		}
		unset($d);

		try {
			$client = new \Zend\Http\Client(null, array('timeout' => $timeout, 'strictredirects' => true));
			$client->setMethod(\Zend\Http\Request::METHOD_POST);

			$url = \DeskPRO\Kernel\License::getLicServer() . '/api/data-submit/' . $service . '.json';
			$client->setUri($url);
			$client->getRequest()->post()->fromArray($data);
			$r = $client->send();

			if (!$r->isSuccess()) {
				error_log("URL retrned code " . $r->getStatusCode() . ": " . $url);
			}
		} catch (\Exception $e) {}
	}


	/**
	 * Sends a heartbeat
	 */
	public static function sendHeartbeat(&$result = null)
	{
		if (isset($GLOBALS['DP_DISABLE_SENDREPORTS'])) {
			return '';
		}

		$data = self::getBasicData();

		if (!App::getSetting('core.enable_reduced_lic_reports')) {
			$database_stats = new \Application\DeskPRO\DBAL\DatabaseStats(App::getDb());
			$data = array_merge($data, $database_stats->getStats());

			$data['setting_core_user_mode'] = App::getSetting('core.user_mode');
			$data['setting_core_rewrite_urls'] = App::getSetting('core.rewrite_urls');
			$data['setting_core_site_url'] = App::getSetting('core.site_url');
			$data['setting_core_install_time'] = App::getSetting('core.install_time');
			$data['setting_core_filestorage_method'] = App::getSetting('core.filestorage_method');
		}

		$data['setting_core_deskpro_url'] = App::getSetting('core.deskpro_url');
		$data['db_id_hash'] = md5(DP_DATABASE_HOST . DP_DATABASE_NAME . DP_DATABASE_USER);
		$data['license_code'] = App::getSetting('core.license');

		try {
			$client = new \Zend\Http\Client(null, array('timeout' => 20, 'strictredirects' => true));
			$client->setMethod(\Zend\Http\Request::METHOD_POST);
			$client->setUri(\DeskPRO\Kernel\License::getLicServer() . '/api/heartbeat.json');
			$client->getRequest()->post()->fromArray($data);
			$r = $client->send();
			return $r->getBody();
		} catch (\Exception $e) {
			error_log(sprintf("sendHeartbeat %s %s", $e->getCode(), $e->getMessage()));
			return null;
		}
	}


	/**
	 * Sends a ping to the install log server about status of an installation.
	 *
	 * @param $step
	 */
	public static function sendInstallStatusPing($step)
	{
		$data = array(
			'install_token' => App::getSetting('core.install_token'),
			'step' => $step
		);

		try {
			$client = new \Zend\Http\Client(null, array('timeout' => 5, 'strictredirects' => true));
			$client->setMethod(\Zend\Http\Request::METHOD_POST);
			$client->setUri(\DeskPRO\Kernel\License::getLicServer() . '/api/data-submit/ping-install.json');
			$client->getRequest()->post()->fromArray($data);
			$r = $client->send();
		} catch (\Exception $e) {
			error_log(sprintf("sendInstallStatusPing %s %s", $e->getCode(), $e->getMessage()));
		}
	}


	/**
	 * @static
	 * @param $person
	 * @param $message
	 */
	public static function sendFeedback($person, $message, $email_address = null, $type = null)
	{
		if (!$email_address || !\Orb\Validator\StringEmail::isValueValid($email_address)) {
			$email_address = $person->email_address;
		}

		$data = array(
			'message' => $message,
			'name' => $person->getDisplayName(),
			'email' => $email_address,
			'url' => App::getSetting('core.deskpro_url'),
			'type' => $type,
		);

		try {
			$client = new \Zend\Http\Client(null, array('timeout' => 5, 'strictredirects' => true));
			$client->setMethod(\Zend\Http\Request::METHOD_POST);
			$client->setUri(\DeskPRO\Kernel\License::getLicServer() . '/api/data-submit/submit-feedback.json');
			$client->getRequest()->post()->fromArray($data);
			$client->setEncType('application/x-www-form-urlencoded; charset=UTF-8');
			$r = $client->send();
		} catch (\Exception $e) {}
	}
}