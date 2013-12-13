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

namespace DeskPRO\Kernel;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

use Application\DeskPRO\App;

class KernelErrorHandler
{
	public static $is_logging = false;
	public static $is_handling_exception = false;
	public static $wrote_log_file = false;
	public static $wrote_php_log = false;
	protected static $process_log = array();


	/**
	 * Add a log line that'll be saved with an error. This is used with things like the gateway, where
	 * if there's an error we'll want to know everything that happened up to the error point.
	 *
	 * @param $line
	 */
	public static function addProcessLog($line)
	{
		self::$process_log[] = $line;
	}


	/**
	 * Clears the process log. For example, with the gateway, if a new email is started then the last log
	 * might be cleared.
	 */
	public static function clearProcessLog()
	{
		self::$process_log = array();
	}


	/**
	 * Handle an error. Typically used as the error handler with set_error_handler()
	 *
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 * @return void
	 */
	public static function handleError($errno, $errstr, $errfile, $errline)
	{
		$GLOBALS['DP_LAST_ERROR'] = array('type' => $errno, 'message' => $errstr, 'file' => $errfile, 'line' => $errline);

		if (!(error_reporting() & $errno)) {
			return;
		}

		$errinfo = self::getErrorInfo($errno, $errstr, $errfile, $errline);

		// PDO::__construct on Windows sometimes doesnt listen to the PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		// option. So it'll generate warnings instead of exceptions.
		// This tries to catch those cases, and turn them into exceptions.
		if (strpos($errinfo['errstr'], 'PDO::__construct') !== false) {
			$code = $errinfo['errno'];
			if (preg_match('#PDO::__construct\\(\\): \\[(.*?)\\]#', $errinfo['errstr'], $m)) {
				$code = $m[1];
			}
			$pdo_e = new \PDOException($errinfo['errstr'], $code);
			throw $pdo_e;
		}


		self::logErrorInfo($errinfo);

		if ($errinfo['display']) {
			$display_errors = @ini_get('display_errors');
			if (isset($GLOBALS['DP_IS_IN_CLI']) || $display_errors == "1" || strtolower($display_errors) == "on" || strtolower($display_errors) == "true" || strtolower($display_errors) == "yes") {
				// Prevent outputting of APC warnings
				// These are logged and a warning about APC is displayed to the admin,
				// but until that is fixed these warnings themselves can cause issues (e.g., cause JSON results to become invalid)
				if (strpos($errinfo['summary'], 'Unable to allocate memory for pool') === false) {
					echo $errinfo['summary'];
				}
			}

			if (isset($GLOBALS['DP_IS_IN_CLI'])) {
				if (self::$wrote_log_file) echo "\n(Refer to " . self::$wrote_log_file . " for details)\n";
				if (self::$wrote_php_log) echo "\n(Refer to the PHP erorr log for details)\n";
			}
		}

		try {
			if (!empty($GLOBALS['DP_ERR_LOGGER'])) {
				$logger = $GLOBALS['DP_ERR_LOGGER'];
				$logger->log($errinfo['summary'] . "\n" . $errinfo['trace'], 'ERR', array('errinfo' => $errinfo));
			}
		} catch (\Exception $e) {}

		if ($errinfo['die']) {
			self::tryCleanup();
			exit(1);
		}
	}


	/**
	 * Handle logging of an exception.
	 *
	 * @param \Exception $exception
	 * @return void
	 */
	public static function handleException(\Exception $exception, $exit = true)
	{
		if (self::$is_handling_exception) {
			return;
		}

		// Dont log 404's
		if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
			return;
		}
		if ($exception instanceof \Symfony\Component\Routing\Exception\MethodNotAllowedException) {
			return;
		}
		if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
			return;
		}
		if ($exception instanceof \Application\DeskPRO\HttpKernel\Exception\NoPermissionException) {
			return;
		}

		$GLOBALS['DP_LAST_ERROR'] = array('type' => 'exception', 'exception' => get_class($exception), 'message' => $exception->getMessage(), 'file' => $exception->getFile(), 'line' => $exception->getFile());

		self::$is_handling_exception = true;

		$errinfo = self::getExceptionInfo($exception);
		self::logErrorInfo($errinfo);

		if ($errinfo['display']) {
			echo $errinfo['summary'];

			if (isset($GLOBALS['DP_IS_IN_CLI'])) {
				if (self::$wrote_log_file) echo "\n(Refer to " . self::$wrote_log_file . " for details)\n";
				else echo "\n(Refer to the PHP erorr log for details)\n";
			}
		}

		try {
			if (!empty($GLOBALS['DP_ERR_LOGGER'])) {
				$logger = $GLOBALS['DP_ERR_LOGGER'];
				$logger->log($errinfo['summary'] . "\n" . $errinfo['trace'], 'ERR', array('errinfo' => $errinfo));
			}
		} catch (\Exception $e) {}

		if ($errinfo['die'] && $exit) {

			self::tryCleanup();

			$code = (int)$errinfo['exception']->getCode();
			if ($code > 255) $code = 255;
			if ($code == 0) $code = 1;
			exit($code);
		}

		self::$is_handling_exception = false;
	}


	/**
	 * @param \Exception $exception   The exception to log
	 * @param bool $send              True to send a report to deskpro
	 * @param string $unique_id       An error ID. if this error has been reported before, it will not be reported again
	 */
	public static function logException(\Exception $exception, $send = false, $unique_id = null)
	{
		static $got_unique_ids = array();

		if ($unique_id && defined('DP_BUILD_TIME') && !defined('DP_BUILDING')) {
			if (isset($got_unique_ids[$unique_id])) return;
			$got_unique_ids[$unique_id] = true;

			try {
				$got = App::getDb()->fetchColumn("
					SELECT data
					FROM install_data
					WHERE build = ? AND name = ?
				", array(DP_BUILD_TIME, 'err_' . $unique_id));
				if ($got) {
					return;
				}

				App::getDb()->replace('install_data', array(
					'data'  => '1',
					'build' => DP_BUILD_TIME,
					'name'  => 'err_' . $unique_id
				));
			} catch (\Exception $e) {}
		}

		$einfo = self::getExceptionInfo($exception);
		if (!$send) {
			$einfo['no_send_error'] = true;
		}
		self::logErrorInfo($einfo);
	}


	/**
	 * Tries to clean up before dieing after a fatal error.
	 */
	public static function tryCleanup()
	{
		if (class_exists('Application\DeskPRO\App')) {
			try {
				$db = App::getDb();
				if ($db->isTransactionActive()) {
					$db->rollback();
				}
			} catch (\Exception $e) {}
		}
	}


	/**
	 * @static
	 *
	 */
	public static function genSessionName()
	{
		static $counter = 0;

		list($time, $ms) = explode(' ', microtime());

		return self::_encodeNum($time) . self::_encodeNum($ms) . self::_encodeNum(++$counter);
	}

	protected static function _encodeNum($num)
	{
		$alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$arr = array();
		$base = strlen($alphabet);

		while ($num) {
			$rem = $num % $base;
			$num = (int)($num / $base);
			$arr[] = $alphabet[$rem];
		}

		$arr = array_reverse($arr);
		return implode('', $arr);
	}


	/**
	 * Takes care of logging an error. $errinfo is an info array from getExceptionInfo or getErrorInfo.
	 *
	 * @param array $errinfo
	 * @return void
	 */
	public static function logErrorInfo(array $errinfo)
	{
		if (self::$is_logging) return;
		self::$is_logging = true;

		if (!class_exists('Application\DeskPRO\App')) {
			return;
		}

		if (isset($errinfo['exception']) && $errinfo['exception'] instanceof \PDOException) {
			$errinfo['email'] = true;
		}

		if (!empty($errinfo['set_setting'])) {
			try {
				App::getContainer()->getSettingsHandler()->setSetting($errinfo['set_setting'], 1);
			} catch (\Exception $e) {}
		}

		self::logToFile($errinfo);
		unset($errinfo['exception']);

		if (isset($GLOBALS['DP_CRON_LOGGER'])) {
			$GLOBALS['DP_CRON_LOGGER']->log("ERROR {$errinfo['session_name']}: {$errinfo['summary']}", 'ERR', array('flag' => 'job_error'));
		}

		if (class_exists('\Application\DeskPRO\App') && !\Application\DeskPRO\App::getConfig('debug.no_report_errors')) {
			if (!(isset($errinfo['no_send_error']) && $errinfo['no_send_error'])) {
				\Application\DeskPRO\Service\ErrorReporter::reportPhpError($errinfo);
			}
		}

		self::$is_logging = false;
	}


	/**
	 * Logs error info to the data/error.log file and the summary to the PHP error log.
	 *
	 * @param array $errinfo
	 */
	public static function logToFile(array $errinfo)
	{
		self::$wrote_log_file = false;

		$str = array();
		if ($errinfo['type'] == 'exception') {
			$e = $errinfo['exception'];
			$line = sprintf("DeskPRO Exception: %s:%s (%s line %s): %s", $errinfo['exception_type'], $e->getCode(), $errinfo['errfile'], $errinfo['errline'], $e->getMessage());
			$str[] = sprintf("Exception: %s %s\n", $e->getCode(), $e->getMessage());
			$str[] = sprintf("\tType: %s\n", $errinfo['exception_type']);
			$str[] = sprintf("\tDate: %s (Running time to error: %s)\n", date('Y-m-d H:i:s'), $errinfo['time_to_error']);
			$str[] = sprintf("\tBuild: %s\n", defined('DP_BUILD_NUM') ? DP_BUILD_NUM : defined('DP_BUILD_TIME') ? DP_BUILD_TIME : '0');
			if (!empty($errinfo['url'])) {
				$str[] = sprintf("\tURL: %s\n", $errinfo['url']);
				$str[] = sprintf("\tUserAgent: %s\n", $errinfo['client_user_agent']);
			}
			$str[] = sprintf("\tLine %d of %s\n", $errinfo['errline'], $errinfo['errfile']);
		} else {
			$line = sprintf("DeskPRO Error: %s (%s line %s): %s", $errinfo['errname'], $errinfo['errfile'], $errinfo['errline'], $errinfo['errstr']);
			$str[] = sprintf("Error: %s\n", $errinfo['errstr']);
			$str[] = sprintf("\tType: %s\n", $errinfo['errname']);
			$str[] = sprintf("\tDate: %s (Running time to error: %s)\n", date('Y-m-d H:i:s'), $errinfo['time_to_error']);
			$str[] = sprintf("\tBuild: %s\n", defined('DP_BUILD_NUM') ? DP_BUILD_NUM : defined('DP_BUILD_TIME') ? DP_BUILD_TIME : '0');
			if (!empty($errinfo['url'])) {
				$str[] = sprintf("\tURL: %s\n", $errinfo['url']);
				$str[] = sprintf("\tUserAgent: %s\n", $errinfo['client_user_agent']);
			}
			$str[] = sprintf("\tLine %d of %s\n", $errinfo['errline'], $errinfo['errfile']);
		}

		$errinfo['trace'] = trim($errinfo['trace']);
		if ($errinfo['trace']) {
			$lines = explode("\n", $errinfo['trace']);
			foreach ($lines as $l) {
				$str[] = sprintf("\t-> %s\n", trim($l));
			}
		}

		if (!empty($errinfo['context_data'])) {
			$str[] = "Context Data:\n";
			$str[] = $errinfo['context_data'];
			$str[] = "\n\n";
		}

		$str = trim(implode('', $str));
		$str .= "\n";

		// Prefix each line for easier parsing
		$str = preg_replace('#^#m', "<DP_LOG:{$errinfo['session_name']}> ", $str);
		$line = preg_replace('#^#m', "<DP_LOG:{$errinfo['session_name']}> ", $line);

		// Always write error line to standard error log
		@error_log($line, 0);

		if (function_exists('dp_get_log_dir') && dp_get_log_dir() && ($fh = @fopen(dp_get_log_dir() . '/error.log', 'a')) !== false) {

			$written = @fwrite($fh, $str);

			if ($written) {
				self::$wrote_log_file = dp_get_log_dir() . '/error.log';

				// Max 30MB
				$stat = @fstat($fh);
				if ($stat && $stat['size'] && $stat['size'] > 31457280) {
					@ftruncate($fh, 31457280);
				}
			}

			@fclose($fh);
		}

		$throttle_id = 'email_error';
		if (isset($errinfo['email_throttle_id'])) {
			$throttle_id = $errinfo['email_throttle_id'];
		}
		if (
			isset($errinfo['email'])
			&& $errinfo['email']
			&& defined('DP_TECHNICAL_EMAIL')
			&& DP_TECHNICAL_EMAIL
			&& !isset($GLOBALS['DP_CONFIG']['debug']['no_report_errors'])
			&& function_exists('dp_should_throttle_action')
			&& !dp_should_throttle_action($throttle_id, 300)
		) {

			if (isset($errinfo['exception']) && $errinfo['exception'] instanceof \PDOException) {
				$line = "There has been a MySQL error: " . $errinfo['exception']->getMessage();
			}

			$fallback_send = true;

			if (isset($errinfo['email_body'])) {
				$email_str = $errinfo['email_body'];
			} else {
				$email_str = $str;
			}

			$email_subject = $line;
			if (isset($errinfo['email_subject'])) {
				$email_subject = $errinfo['email_subject'];
			}

			if (class_exists('Application\DeskPRO\App')) {
				try {
					$message = App::getMailer()->createMessage();
					$message->setTo(DP_TECHNICAL_EMAIL);
					$message->setSubject($email_subject);
					$message->disableQueueHint();

					$email_str = nl2br(htmlspecialchars($email_str, \ENT_QUOTES, 'UTF-8'));
					$message->setBody($email_str, 'text/html');

					if (isset($errinfo['attach_logs']) && $errinfo['attach_logs']) {
						if (is_file(dp_get_log_dir() . '/error.log')) {
							$file = @file_get_contents(dp_get_log_dir() . '/error.log');
							if (isset($file[3670016])) {
								$file = substr($file, -3670016);
							}
							$filename = 'error.log';
							$filetype = 'text/plain';

							if (function_exists('gzencode')) {
								$file = gzencode($file);
								$filename = 'error.log.gz';
								$filetype = 'application/gzip';
							}

							$message->attach(\Swift_Attachment::newInstance(
								$file,
								$filename,
								$filetype
							));
						}

						if (is_file(dp_get_log_dir() . '/cli-phperr.log')) {
							$file = @file_get_contents(dp_get_log_dir() . '/cli-phperr.log');
							if (isset($file[3670016])) {
								$file = substr($file, -3670016);
							}
							$filename = 'cli-phperr.log';
							$filetype = 'text/plain';

							if (function_exists('gzencode')) {
								$file = gzencode($file);
								$filename = 'cli-phperr.log.gz';
								$filetype = 'application/gzip';
							}

							$message->attach(\Swift_Attachment::newInstance(
								$file,
								$filename,
								$filetype
							));
						}
					}

					if (App::getMailer()->sendNow($message)) {
						$fallback_send = false;
					}
				} catch (\Exception $e) {}
			}

			if ($fallback_send) {
				@mail(DP_TECHNICAL_EMAIL, $line, $str);
			}
		}
	}

	/**
	 * Gets a standard error info array from an exception.
	 *
	 * @param \Exception $exception
	 * @return array
	 */
	public static function getExceptionInfo(\Exception $exception)
	{
		$errno   = $exception->getCode();
		$errstr  = self::stripPathPrefix($exception->getMessage());
		$errfile = self::stripPathPrefix($exception->getFile());
		$errline = $exception->getLine();

		$errfile_hash     = self::getFilehash($exception->getFile());
		$errfile_modified = self::isFileModified($exception->getFile(), $errfile_hash);

		$backtrace = $exception->getTrace();
		$trace = self::formatBacktrace($backtrace);
		$context_data = '';

		if (isset($exception->_dp_query)) {
			$errstr .= ' -- Query: ' . substr($exception->_dp_query, 0, 2000);

			$context_data .= 'Query: ' . substr($exception->_dp_query, 0, 2000);

			if (!empty($exception->_dp_query_params)) {
				$context_data .= "\n\n" . self::varToString($exception->_dp_query_params);
			}
		}

		if (!$context_data && isset($exception->_dp_context_data)) {
			$context_data = $exception->_dp_context_data;
		}

		$type = get_class($exception);
		$summary = "[EXCEPTION] $type:$errno $errstr ($errfile:$errline)";

		$display = true;
		if (!(error_reporting() & E_ERROR)) {
			$display = false;
		}

		$prev = $exception->getPrevious();
		if ($prev) {
			$previnfo = self::getExceptionInfo($prev);
			$summary .= ", " . $previnfo['summary'];
			$trace .= "\n\n(Alt Exception)\n{$previnfo['summary']}\n" . $previnfo['trace'];
		}

		$last_e = null;
		if (isset($GLOBALS['DP_LAST_ERROR'])) {
			$last_e = sprintf("[%d] %s (%s line %d)", $GLOBALS['DP_LAST_ERROR']['type'], $GLOBALS['DP_LAST_ERROR']['message'], $GLOBALS['DP_LAST_ERROR']['file'], $GLOBALS['DP_LAST_ERROR']['line']);
		} elseif ($last_e_info = @error_get_last()) {
			$last_e = sprintf("[%d] %s (%s line %d)", $last_e_info['type'], $last_e_info['message'], $last_e_info['file'], $last_e_info['line']);
		}

		$errinfo = array(
			'type'              => 'exception',
			'session_name'      => isset($exception->_dp_sn) ? $exception->_dp_sn : self::genSessionName(),
			'exception'         => $exception,
			'exception_type'    => get_class($exception),
			'die'               => true,
			'pri'               => 'ERR',
			'trace'             => $trace,
			'summary'           => $summary,
			'errstr'            => $errstr,
			'errname'           => 'EXCEPTION',
			'errno'             => $errno,
			'errfile'           => $errfile,
			'errfile_hash'      => $errfile_hash,
			'errfile_modified'  => $errfile_modified,
			'errline'           => $errline,
			'last_error'        => $last_e,
			'display'           => $display,
			'build'             => defined('DP_BUILD_TIME') ? DP_BUILD_TIME : 0,
			'process_log'       => implode("\n", self::$process_log),
			'context_data'      => $context_data,
			'error_time'        => microtime(true),
			'time_to_error'     => defined('DP_START_TIME') ? sprintf("%0.4f", microtime(true) - DP_START_TIME) : 0,
			'client_user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
		);

		$url = '';
		if (defined('DP_REQUEST_URL')) {
			$url = DP_REQUEST_URL;
		} elseif (defined('DP_INTERFACE')) {
			$url = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
			if (class_exists('Application\\DeskPRO\\App')) {
				try {
					$url = App::getRequest()->getUri();
				} catch (\Exception $e) {}
			}
		}
		if (php_sapi_name() == 'cli' && !empty($_SERVER['argv'])) {
			$url = 'Command: ' . implode(' ', $_SERVER['argv']);
		}
		$errinfo['url'] = $url;

		if (self::isNoReportException($exception)) {
			$errinfo['no_send_error'] = true;
		}

		return $errinfo;
	}


	/**
	 * @param \Exception $exception
	 * @return bool
	 */
	public static function isNoReportException(\Exception $exception)
	{
		static $ignore = array(
			'Swift_TransportException',
			'Swift_IoException',
			'Zend\\Mail\\Protocol\\Exception\\RuntimeException',
		);

		foreach ($ignore as $cls) {
			if ($exception instanceof $cls) {
				return true;
			}
		}

		if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
			return true;
		}

		if ($exception instanceof \Symfony\Component\Routing\Exception\MethodNotAllowedException) {
			return true;
		}

		if ($exception instanceof \Doctrine\DBAL\Types\ConversionException && strpos($exception->getMessage(), 'Doctrine Type array') !== false) {
			return true;
		}

		if ($exception instanceof \Doctrine\DBAL\ConnectionException && strpos($exception->getMessage(), 'There is no active transaction') !== false) {
			return true;
		}

		if ($exception instanceof \Imagine\Exception\RuntimeException && strpos($exception->getMessage(), 'Unable to open temporary file') !== false) {
			return true;
		}

		if ($exception instanceof \PDOException) {
			if (strpos($exception->getFile(), 'DbTablePhpPasswordCheck.php') !== false) {
				return true;
			}

			if (strpos($exception->getMessage(), 'Incorrect key file for table') !== false) {
				return true;
			}

			if (strpos($exception->getMessage(), 'A connection attempt failed') !== false) {
				return true;
			}

			if (strpos($exception->getMessage(), 'No connection could be made') !== false) {
				return true;
			}

			// disk is full
			if (strpos($exception->getMessage(), 'Got error 28 from storage engine') !== false) {
				return true;
			}

			// innodb error, probably during recovery of disk issue
			if (strpos($exception->getMessage(), 'Got error -1 from storage engine') !== false) {
				return true;
			}

			// table is full
			if (strpos($exception->getMessage(), 'General error: 1114 The table') !== false) {
				return true;
			}

			if (strpos($exception->getMessage(), 'MySQL server has gone away') !== false) {
				return true;
			}

			if (strpos($exception->getMessage(), 'Too many connections') !== false) {
				return true;
			}

			if (strpos($exception->getMessage(), 'has more than \'max_user_connections\' active connections') !== false) {
				return true;
			}

			if (strpos($exception->getMessage(), 'Can\'t connect to MySQL server on') !== false) {
				return true;
			}
		}

		if ($exception instanceof \InvalidArgumentException && preg_match('#Command ".*?" is not defined#', $exception->getMessage())) {
			return true;
		}

		if ($exception instanceof \InvalidArgumentException && preg_match('#The command ".*?" does not exist#', $exception->getMessage())) {
			return true;
		}

		// For commands run with bad options
		if ($exception instanceof \RuntimeException && strpos($exception->getMessage(), 'option does not exist') !== false && strpos($exception->getFile(), 'ArgvInput.php') !== false) {
			return true;
		}

		if ($exception instanceof \RuntimeException && strpos($exception->getMessage(), 'Could not open blob for reading') !== false) {
			return true;
		}

		if ($exception instanceof \RuntimeException && strpos($exception->getMessage(), 'Cannot create Imagine instance') !== false) {
			return true;
		}

		if ($exception instanceof \Zend\Ldap\Exception && strpos($exception->getMessage(), 'LDAP extension not loaded') !== false) {
			return true;
		}

		return false;
	}


	/**
	 * Gets a standard error info array from an error.
	 *
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 * @return array
	 */
	public static function getErrorInfo($errno, $errstr, $errfile, $errline)
	{
		$die = false;
		switch ($errno) {
			case E_ERROR:
				$die = true;
				$pri = 'ERR';
				$errname = "E_ERROR";
				break;

			case E_WARNING:
			case E_USER_WARNING:
				$pri = 'WARN';
				$errname = "E_WARNING";
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
				$pri = 'NOTICE';
				$errname = "E_NOTICE";
				break;

			case E_STRICT:
				$pri = 'STRICT';
				$errname = "E_STRICT";
				break;

			case E_RECOVERABLE_ERROR:
				$pri = 'ERR';
				$errname = "E_RECOVERABLE_ERROR";
				break;

			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$pri = 'NOTICE';
				$errname = "E_DEPRECATED";
				break;

			default:
				$pri = 'ERR';
				$errname = 'UNKNOWN';
		}

		$context_data = '';
		$set_setting = '';

		$display = true;
		$no_send_error = false;
		if (!(error_reporting() & $errno)) {
			$display = false;
		}

		// Dont output apc warnings (but still log them)
		if ($display && strpos($errstr, 'Unable to allocate memory for pool') !== false) {
			$display = false;
			$no_send_error = true;
			$set_setting = 'core.error_unable_allocate_memory';
		}

		$errfile_hash     = self::getFilehash($errfile);
		$errfile_modified = self::isFileModified($errfile, $errfile_hash);

		$errstr  = self::stripPathPrefix($errstr);
		$errfile = self::stripPathPrefix($errfile);

		$backtrace = debug_backtrace();
		$trace = self::formatBacktrace($backtrace);

		// Dont send in general perm errors or things to do with the fs storage
		if ((strpos($errstr, 'failed to open stream: Permission denied') !== false || strpos($errstr, 'failed to open stream: No such file or directory') !== false) && strpos($trace, 'FileDescriptor') !== false) {
			$no_send_error = true;
		}

		// Dont send connection errors with smtp
		if (strpos($errfile, 'StreamBuffer.php') !== false && strpos($errstr, 'bytes failed with errno') !== false) {
			$no_send_error = true;
		}

		if (strpos($errstr, 'htmlspecialchars(): Invalid multibyte sequence in argument') !== false) {
			$no_send_error = true;
		}

		// Dont send logs about bad file attachments
		if (strpos($errstr, 'failed to open stream') !== false && (strpos($errstr, '/FileDescriptor/Filesystem.php') !== false || strpos($errstr, '\\FileDescriptor\\Filesystem.php') !== false)) {
			$no_send_error = true;
		}

		// Windows PHP <5.3.6 https://bugs.php.net/bug.php?id=51894
		if (strpos($errstr, 'range(): step exceeds the specified range') !== false) {
			$no_send_error = true;
		}

		// Log but dont report erorrs about writing chat available trigger
		if (strpos($errstr, 'chat_is_available.trigger') !== false) {
			$no_send_error = true;
		}

		// Ignore range() warning caused by PHP bug https://bugs.php.net/bug.php?id=51894 (fixed in PHP >= 5.3.6)
		if (strpos($errstr, 'step exceeds the specified range') !== false) {
			$no_send_error = true;
		}

		if (strpos($errstr, 'set_time_limit() has been disabled for security reasons') !== false) {
			$no_send_error = true;
		}

		if (strpos($errstr, 'passthru() has been disabled for security reasons') !== false) {
			$no_send_error = true;
		}

		if (strpos($errstr, 'possibly out of disk space') !== false) {
			$no_send_error = true;
		}

		// Socket/network errors
		if (
			strpos($errstr, 'stream_socket_enable_crypto():') !== false
			|| strpos($errstr, 'SSL: Broken pipe') !== false
			|| strpos($errstr, 'SSL operation failed') !== false
			|| strpos($errstr, 'errno=32 Broken pipe')
			|| strpos($errstr, 'SSL: An established connection was aborted') !== false
			|| strpos($errstr, 'fsockopen()') !== false
		) {
			$no_send_error = true;
		}

		$summary = "[$errname:$errno] $errstr ($errfile:$errline)";

		$url = '';
		if (defined('DP_REQUEST_URL')) {
			$url = DP_REQUEST_URL;
		} elseif (defined('DP_INTERFACE')) {
			$url = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
			if (class_exists('Application\\DeskPRO\\App')) {
				try {
					$url = App::getRequest()->getUri();
				} catch (\Exception $e) {}
			}
		}
		if (php_sapi_name() == 'cli' && !empty($_SERVER['argv'])) {
			$url = 'Command: ' . implode(' ', $_SERVER['argv']);
		}

		$last_e = null;
		if (isset($GLOBALS['DP_LAST_ERROR'])) {
			$last_e = sprintf("[%d] %s (%s line %d)", $GLOBALS['DP_LAST_ERROR']['type'], $GLOBALS['DP_LAST_ERROR']['message'], $GLOBALS['DP_LAST_ERROR']['file'], $GLOBALS['DP_LAST_ERROR']['line']);
		} elseif ($last_e_info = @error_get_last()) {
			$last_e = sprintf("[%d] %s (%s line %d)", $last_e_info['type'], $last_e_info['message'], $last_e_info['file'], $last_e_info['line']);
		}

		return array(
			'type'               => 'error',
			'session_name'       => self::genSessionName(),
			'die'                => $die,
			'pri'                => $pri,
			'trace'              => $trace,
			'summary'            => $summary,
			'errstr'             => $errstr,
			'errname'            => $errname,
			'errno'              => $errno,
			'errfile'            => $errfile,
			'errfile_hash'       => $errfile_hash,
			'errfile_modified'   => $errfile_modified,
			'errline'            => $errline,
			'last_error'         => $last_e,
			'display'            => $display,
			'build'              => defined('DP_BUILD_TIME') ? DP_BUILD_TIME : 0,
			'process_log'        => implode("\n", self::$process_log),
			'context_data'       => $context_data,
			'error_time'        => microtime(true),
			'time_to_error'     => defined('DP_START_TIME') ? sprintf("%0.4f", microtime(true) - DP_START_TIME) : 0,
			'no_send_error'     => $no_send_error,
			'client_user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
			'set_setting'       => $set_setting,
			'url'               => $url,
		);
	}


	/**
	 * Strips the full path prefix from $content. This makes all paths relative to the root of DeskRPO install.
	 *
	 * @param string $content
	 * @return string
	 */
	public static function stripPathPrefix($content)
	{
		$content = str_replace('\\', '/', $content);

		$prefix = str_replace('\\', '/', DP_ROOT) . '/';
		$content = str_replace($prefix, '/app/', $content);

		$prefix = str_replace('\\', '/', DP_WEB_ROOT) . '/';
		$content = str_replace($prefix, '/', $content);

		return $content;
	}


	/**
	 * Formats a backtrace.
	 *
	 * @param array $backtrace
	 * @return string
	 */
	public static function formatBacktrace(array $backtrace)
	{
		$trace = '';

		foreach($backtrace as $k=>$v){

			$prefix = "#$k ";
			$line = '';

			if (!empty($v['file'])) {
				$v['file'] = self::stripPathPrefix($v['file']);
				$prefix .= "[{$v['file']}:{$v['line']}] ";
			}

			if (isset($v['object'])) {
				$line .= get_class($v['object']) . "::";
			} elseif (isset($v['class'])) {
				$line .= $v['class'] . "::";
			}

			$line .= "{$v['function']}(";

			if (!empty($v['args'])) {
				$line .= self::varToString($v['args']);
			}

			$line .= ")";

			$trace .= $prefix . ' ' . trim($line) . "\n";
		}

		$trace = preg_replace('#PDO::__construct(.*?)$#m', 'PDO::__construct(...)', $trace);
		$trace = preg_replace_callback('#Pop3::__construct(.*?)$#m', function ($m) {
			$ret = $m[0];
			$ret = preg_replace('#password => .*?, ssl#', 'password => \'***\', ssl', $ret);
			return $ret;
		}, $trace);

		return trim($trace);
	}


	/**
	 * Used with formatBacktrace to format an array (usually parameters) to a string, being sure not to recurse
	 * too deep.
	 *
	 * @param mixed $var
	 * @param int $_depth
	 * @return string
	 */
	public static function varToString($var, $_depth = 0)
    {
        if (is_object($var)) {
            return sprintf('[object](%s)', get_class($var));
        }
        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
				if ($_depth > 8) {
					$a[] = sprintf('%s => %s', $k, '(string)');
				} else {
					$a[] = sprintf('%s => %s', $k, self::varToString($v, $_depth+1));
				}
            }
            return sprintf("[array](%s)", implode(', ', $a));
        }
        if (is_resource($var)) {
            return '[resource]';
        }
		$str = (string)$var;
		if (strlen($str) > 1000) {
			$str = substr($str, 0, 1000) . "...(clipped)";
		}
        return str_replace("\n", '', var_export(self::stripPathPrefix($str), true));
    }


	/**
	 * Gets a filehash
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getFilehash($path)
	{
		if (!is_file($path)) {
			return null;
		}

		$file_contents = @file_get_contents($path);

		$bom = pack('CCC', 0xEF, 0xBB, 0xBF);
		if (substr($file_contents, 0, 3) === $bom) {
			$file_contents = substr($file_contents, 3);
		}

		$file_contents = trim(str_replace(array("\r", "\n"), '', $file_contents));

		return $file_contents;
	}


	/**
	 * Compare a file hash versus the original stored in the distro checksums file.
	 *
	 * @param string $path
	 */
	public static function isFileModified($path, $hash = null)
	{
		if ($hash === null) {
			$hash = self::getFilehash($path);
		}

		if (!is_file(DP_ROOT . 'app/sys/Resources/distro-checksums.php')) {
			return true;
		}

		$checksums = require(DP_ROOT . 'app/sys/Resources/distro-checksums.php');
		$key = str_replace(DP_ROOT, '', $path);

		if (!isset($checksums[$key]) || $hash != $checksums[$key]) {
			return false;
		}

		return true;
	}
}