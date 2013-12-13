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
 * @subpackage DBAL
 */

namespace Application\DeskPRO\DBAL\Logging;

use Application\DeskPRO\App;
use Orb\Util\Arrays;
use Orb\Util\Strings;

class SysQueryLogger extends \Symfony\Bridge\Doctrine\Logger\DbalLogger
{
	/**
	 * Max number of queries logged before we start dropping them.
	 * This is in case we get stuck in a loop somehwere, we dont want to fill the hdd or memory with
	 * a giant log.
	 */
	const SAFE_MAX = 1000;

	/**
	 * Is this logger enabled?
	 *
	 * @var bool
	 */
	private $_enabled = false;

	/**
	 * The time this object was instantiated. Most scripts have the DP_START_TIME constant
	 * defined during boot, so this is just a fallback used when determining when to log the
	 * slow page log.
	 *
	 * @var int
	 */
	private $_start_time = null;

	/**
	 * The last query started that we are currently loggin.
	 *
	 * @var array|null
	 */
	private $_last_query = null;

	/**
	 * Info about all the queries this current request
	 *
	 * @var array
	 */
	private $_queries = array();

	/**
	 * @var array
	 */
	private	$_query_id_count = array();

	/**
	 * @var array
	 */
	private	$_query_id_names = array();

	/**
	 * Number of queries executed
	 *
	 * @var int
	 */
	private $_query_count = 0;

	/**
	 * A count of DB time used so far
	 *
	 * @var int
	 */
	private $_db_time = 0;

	public function __construct()
	{
		$this->_start_time = microtime(true);

		if (!function_exists('dp_get_config')) return;
		if (!dp_get_config('debug.page_log.enabled') || isset($GLOBALS['DP_NOSQL_LOG'])) return;

		$this->_enabled = true;

		\DpShutdown::add(array($this, 'writeLogQuiet'));
	}

	public function writeLogQuiet()
	{
		if (!$this->_enabled) return;
		if (isset($GLOBALS['DP_NOSQL_LOG']) && $GLOBALS['DP_NOSQL_LOG']) return;

		try {
			$this->writeLog();
		} catch (\Exception $e) {}
	}

	public function startQuery($sql, array $params = null, array $types = null)
	{
		if (isset($GLOBALS['DP_DEV_ECHO_QUERY']) && $GLOBALS['DP_DEV_ECHO_QUERY']) {
			$sql_string = str_replace(array("\r\n", "\n", "\t"), ' ', $sql);
			$sql_string = preg_replace('# {2,}#', ' ', $sql_string);
			$sql_string = substr($sql_string, 0, 5000);
			echo "\n";
			echo "Query:  " . $sql_string;
			if ($params) {
				echo "\n";
				echo "Params: " . \DeskPRO\Kernel\KernelErrorHandler::varToString($params);
			}
			echo "\n";
		}

		if (!isset($GLOBALS['DP_QUERY_COUNT'])) {
			$GLOBALS['DP_QUERY_COUNT'] = 0;
		}
		$GLOBALS['DP_QUERY_COUNT']++;
		$this->_query_count++;

		if (!$this->_enabled) return;
		if ($this->_query_count > self::SAFE_MAX) return;

		$trace = null;
		if (dp_get_config('debug.page_log.save_trace')) {
			$e = new \Exception();
			$trace = $e->getTraceAsString();
		}

		$this->_last_query = array(
			'sql'            => trim($sql),
			'params'         => $params,
			'time_start'     => microtime(true),
			'time_end'       => 0,
			'time_taken'     => 0,
			'trans_level'    => 0,
			'trace'          => $trace
		);
	}

	public function stopQuery()
	{
		if (!$this->_enabled) return;
		if (!$this->_last_query) return;
		if (isset($GLOBALS['DP_NOSQL_LOG']) && $GLOBALS['DP_NOSQL_LOG']) return;

		$queryinfo = $this->_last_query;

		$queryinfo['params_string']  = \DeskPRO\Kernel\KernelErrorHandler::varToString($queryinfo['params']);
		$queryinfo['time_end']       = microtime(true);
		$queryinfo['time_taken']     = $queryinfo['time_end'] - $queryinfo['time_start'];

		$this->_queries[] = $queryinfo;
		$this->_db_time += $queryinfo['time_taken'];

		$this->_last_query = null;
	}

	public function writeLog()
	{
		if (!$this->_enabled) return;
		if (isset($GLOBALS['DP_NOSQL_LOG']) && $GLOBALS['DP_NOSQL_LOG']) return;

		$start_time = DP_START_TIME;

		$total_time = microtime(true) - $start_time;
		$db_time    = $this->_db_time;
		$php_time   = $total_time - $db_time;

		$opt_slow_query_time = dp_get_config('debug.page_log.slow_query_time');
		$opt_max_query_count = dp_get_config('debug.page_log.max_query_count');
		$opt_slow_db_time    = dp_get_config('debug.page_log.slow_db_time');
		$opt_slow_php_time   = dp_get_config('debug.page_log.slow_php_time');
		$opt_slow_page_time  = dp_get_config('debug.page_log.slow_page_time');

		$do_slow_query = false;
		if ($opt_slow_query_time) {
			foreach ($this->_queries as $queryinfo) {
				if ($queryinfo['time_taken'] >= $opt_slow_query_time) {
					$do_slow_query = true;
					break;
				}
			}
		}

		$do_max_query  = ($opt_max_query_count && $this->_query_count >= $opt_max_query_count) ? true : false;
		$do_slow_db    = ($opt_slow_db_time    && $db_time            >= $opt_slow_db_time)    ? true : false;
		$do_slow_php   = ($opt_slow_php_time   && $php_time           >= $opt_slow_php_time)   ? true : false;
		$do_slow_page  = ($opt_slow_page_time  && $total_time         >= $opt_slow_page_time)  ? true : false;

		if (!$do_slow_query && !$do_max_query && !$do_slow_db && !$do_slow_php && !$do_slow_page) {
			return;
		}

		$this->_procQueryArray();

		$unique_queries = null;
		$repeated_queries = array();

		foreach ($this->_queries as $queryinfo) {
			$query_name = $queryinfo['query_name'];
			if (!isset($unique_queries[$query_name])) {
				$unique_queries[$query_name] = $queryinfo;
				$repeated_queries[$query_name] = array(
					'count'      => 1,
					'total_time' => $queryinfo['time_taken'],
					'min_time'   => $queryinfo['time_taken'],
					'max_time'   => $queryinfo['time_taken'],
				);
			} else {
				$repeated_queries[$query_name]['count']++;
				$repeated_queries[$query_name]['total_time'] += $queryinfo['time_taken'];

				if ($queryinfo['time_taken'] < $repeated_queries[$query_name]['min_time']) {
					$repeated_queries[$query_name]['min_time'] = $queryinfo['time_taken'];
				}
				if ($queryinfo['time_taken'] > $repeated_queries[$query_name]['max_time']) {
					$repeated_queries[$query_name]['max_time'] = $queryinfo['time_taken'];
				}
			}
		}

		foreach ($repeated_queries as &$q) {
			if ($q['count'] == 1) {
				$q = null;
			} else {
				$q['avg_time'] = $q['total_time'] / $q['count'];
			}
		}
		unset($q);

		$repeated_queries = Arrays::removeFalsey($repeated_queries);

		$page_header = array();
		$page_header[] = "--- Page Log Begin ---";
		if (defined('DP_REQUEST_URL')) {
			$page_header[] = "=> URL: " . DP_REQUEST_URL;
		} elseif (php_sapi_name() == 'cli' && !empty($_SERVER['argv'])) {
			$page_header[] = "=> URL: (Command) " . implode(' ', $_SERVER['argv']);
		} elseif (!empty($_SERVER["REQUEST_URI"])) {
			$page_header[] = "=> URL: " . $_SERVER["REQUEST_URI"];
		}

		$page_header[] = sprintf("=> Time: %.4f    PHP_Time: %.4f    DB_Time: %.4f    Query_Count: %d    Peak_Memory: %d", $total_time, $php_time, $db_time, $this->_query_count, memory_get_peak_usage());

		#------------------------------
		# Slow Query Log
		#------------------------------

		if ($do_slow_query) {
			$slow_queries = array();
			foreach ($this->_queries as $queryinfo) {
				if ($queryinfo['time_taken'] >= $opt_slow_query_time) {
					$slow_queries[$queryinfo['query_name']] = $queryinfo;
				}
			}

			// Sort from highest to lowest
			uksort($slow_queries, function($a, $b) {
				if ($a['time_taken'] == $b['time_taken']) {
					return 0;
				}

				return $a['time_taken'] > $b['time_taken'] ? -1 : 1;
			});

			$repeated_lines = $this->_formatRepeatedQueries($repeated_queries);
			foreach ($repeated_lines as $k => &$l) {
				if ($k === 0) continue;
				$name = Strings::extractRegexMatch('/<(#[0-9]+)>/', $l);
				if (!isset($slow_queries[$name])) {
					$l = null;
				}
			}
			unset($l);

			$repeated_lines = Arrays::removeFalsey($repeated_lines);

			// Means just the 'Repeated Queries' header we want to get rid of
			if (count($repeated_lines) == 1) {
				$repeated_lines = array();
			}

			$write = array_merge($page_header, $this->_formatAllQueryRows($slow_queries), $repeated_lines);
			$this->_writeLogFile(
				dp_get_log_dir() . DIRECTORY_SEPARATOR . 'pagelog-slow-queries.log',
				$write
			);
		}

		#------------------------------
		# Max Query Count Log
		#------------------------------

		if ($do_max_query) {
			$write = array_merge($page_header, $this->_formatAllQueryRows($this->_queries), $this->_formatRepeatedQueries($repeated_queries));
			$this->_writeLogFile(
				dp_get_log_dir() . DIRECTORY_SEPARATOR . 'pagelog-query-count.log',
				$write
			);
		}

		#------------------------------
		# Slow DB Log
		#------------------------------

		if ($do_slow_db) {
			$write = array_merge($page_header, $this->_formatAllQueryRows($this->_queries), $this->_formatRepeatedQueries($repeated_queries));
			$this->_writeLogFile(
				dp_get_log_dir() . DIRECTORY_SEPARATOR . 'pagelog-slow-db.log',
				$write
			);
		}

		#------------------------------
		# Slow PHP Log
		#------------------------------

		if ($do_slow_php) {
			$this->_writeLogFile(
				dp_get_log_dir() . DIRECTORY_SEPARATOR . 'pagelog-slow-php.log',
				$page_header
			);
		}

		#------------------------------
		# Slow Page Log
		#------------------------------

		if ($do_slow_page) {
			$this->_writeLogFile(
				dp_get_log_dir() . DIRECTORY_SEPARATOR . 'pagelog-slow-page.log',
				$page_header
			);
		}
	}


	/**
	 * @return void
	 */
	private function _procQueryArray()
	{
		foreach ($this->_queries as $k => &$queryinfo) {
			if (stripos($queryinfo['sql'], 'SELECT') === 0) {
				$query_typename = 'SELECT';
			} elseif (stripos($queryinfo['sql'], 'UPDATE') === 0) {
				$query_typename = 'UPDATE';
			} elseif (stripos($queryinfo['sql'], 'INSERT') === 0) {
				$query_typename = 'INSERT';
			} elseif (stripos($queryinfo['sql'], 'DELETE') === 0) {
				$query_typename = 'DELETE';
			} else {
				$query_typename = 'OTHER';
			}

			if (preg_match('#\s+(FROM|INSERT INTO|UPDATE|DELETE FROM)\s+(.*?)\s+#', $queryinfo['sql'], $m)) {
				$query_table = $m[2];
			} else {
				$query_table = '?';
			}

			$sql_string = str_replace(array("\r\n", "\n", "\t"), ' ', $queryinfo['sql']);
			$sql_string = preg_replace('# {2,}#', ' ', $sql_string);
			$sql_string = substr($sql_string, 0, 5000);
			$queryinfo['sql_string'] = $sql_string;

			$query_id = md5($queryinfo['sql']);

			$queryinfo['query_typename'] = $query_typename;
			$queryinfo['query_table']    = $query_table;
			$queryinfo['query_id']       = $query_id;

			if (!isset($this->_query_id_count[$query_id])) {
				$this->_query_id_names[$query_id] = sprintf("#%04d", $k);
				$this->_query_id_count[$query_id] = 1;

				$queryinfo['query_name'] = $this->_query_id_names[$query_id];
				$queryinfo['count_of_id'] = 0;
			} else {
				$queryinfo['count_of_id'] = $this->_query_id_count[$query_id];
				$queryinfo['query_name'] = $this->_query_id_names[$query_id];

				$this->_query_id_count[$query_id]++;
			}

		}
	}


	/**
	 * @param array $queryinfo
	 * @return string
	 */
	private function _formatQueryRow(array $queryinfo)
	{
		// [2.3s] <Q123.2:tablename> query     <Params>

		$row = sprintf(
			"[%.4fs] <%s:%s> %s    <PARAMS> %s",
			$queryinfo['time_taken'],
			$queryinfo['query_name'] . ($queryinfo['count_of_id'] ? sprintf(".%03d", $queryinfo['count_of_id']) : ''),
			$queryinfo['query_table'],
			$queryinfo['sql_string'],
			$queryinfo['params_string']
		);

		if ($queryinfo['trace']) {
			$trace = Strings::modifyLines($queryinfo['trace'], "\t\t");
			$row .= "\tTrace:\n$trace";
		}

		return $row;
	}


	/**
	 * @param array $all_queryinfo
	 * @return string[]
	 */
	private function _formatAllQueryRows(array $all_queryinfo)
	{
		$write = array();

		foreach ($all_queryinfo as $queryinfo) {
			$write[] = $this->_formatQueryRow($queryinfo);
		}

		return $write;
	}


	/**
	 * @param array $repeated_queries
	 * @return string[]
	 */
	private function _formatRepeatedQueries(array $repeated_queries)
	{
		if (!$repeated_queries) {
			return array();
		}

		$write = array();
		$write[] = "Repeated Queries:";

		foreach ($repeated_queries as $name => $info) {
			$write[] = sprintf(
				"\t<%s> Count: %03d   Time: %.4f   MaxTime: %.4f   MinTime: %.4f   AvgTime: %.4f",
				$name,
				$info['count'],
				$info['total_time'],
				$info['max_time'],
				$info['min_time'],
				$info['avg_time']
			);
		}

		return $write;
	}


	/**
	 * @param string $path
	 * @param array $lines
	 * @return int
	 */
	private function _writeLogFile($path, array $lines)
	{
		$lines = implode("\n", $lines);

		$prefix = '[' . date('Y-m-d H:i:s') . '] ';
		$lines = Strings::modifyLines($lines, $prefix);
		$lines = trim($lines);
		$lines .= "\n";

		$ret = @file_put_contents($path, $lines, \FILE_APPEND | \LOCK_EX);

		// If we just created the file this will make it writable
		// in case the same file is being writ to by the CLI and web server both
		@chmod($path, 0777);

		return $ret;
	}
}
