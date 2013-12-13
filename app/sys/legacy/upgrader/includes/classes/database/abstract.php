<?php
// +-------------------------------------------------------------+
// | DeskPRO v3
// | Copyright (c) 2001 - 2012 DeskPRO Limited
// | http://www.deskpro.com    |     support@deskpro.com
// +-------------------------------------------------------------+
// | DESKPRO IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.deskpro.com or if you have otherwise received
// | this software from someone who is not a representative of
// | this organization you are involved in an illegal activity.
// | License agreement: http://www.deskpro.com/license
// +-------------------------------------------------------------+
// | File Details:
// | - Abstract database access class
// +-------------------------------------------------------------+

require_once(INC . 'functions/email_functions.php');

/**
* Equivelant of MYSQL(I)_BOTH
* @var	integer
*/
define('DB_RETURN_BOTH', 0);


/**
* Equivelant of MYSQL(I)_ASSOC
* @var	integer
*/
define('DB_RETURN_ASSOC', 1);


/**
* Equivelant of MYSQL(I)_NUM
* @var	integer
*/
define('DB_RETURN_NUM', 2);





/**
 * Database Abstraction Layer
 *
 * This class is a wrapper for the database-specific
 * functions that are found in the other classes within
 * this file.
 *
 * @package	DeskPRO
 * @version	$Id$
 */
class DB_Abstract {

	var $driver_name = 'Abstract';

	/**
	 * Database server location
	 * @var	string
	 * @access	public
	 */
	var $host = '';


	/**
	 * Name of the database to connect to
	 * @var	string
	 * @access	public
	 */
	var $database = '';


	/**
	 * Username of the MySQL user
	 * @var	string
	 * @access	public
	 */
	var $user = '';


	/**
	 * Password of the MySQL user
	 * @var	string
	 * @access	public
	 */
	var $password = '';


	/**
	 * Kill the current script on database errors
	 * @var	bool
	 * @access	public
	 */
	var $error_halt = true;


	/**
	 * Use a simple text error display instead of a HTML page
	 * @var bool
	 * @access public
	 */
	var $simple_error = false;


	/**
	 * Current MySQL error number
	 * @var	int
	 * @access	private
	 */
	var $errno = 0;


	/**
	 * Current MySQL error string
	 * @var	string
	 * @access	private
	 */
	var $error = '';


	/**
	 * Open MySQL link-id
	 * @var	mixed
	 * @access	private
	 */
	var $link_id = 0;


	/**
	 * Current query-id in MySQL
	 * @var	mixed
	 * @access	private
	 */
	var $query_id = 0;


	/**
	 * Name of the application using the DBAL
	 * @var	string
	 * @access	public
	 */
	var $appname = DP_NAME;


	/**
	 * Start time of query execution
	 * @var	float
	 * @access	private
	 */
	var $start;


	/**
	 * End time of query execution
	 * @var	float
	 * @access	private
	 */
	var $end;


	/**
	 * Total query execution time
	 * @var	float
	 * @access	private
	 */
	var $duration;


	/**
	 * An array of all the queries that were marked as slow
	 * @var	array
	 * @access	private
	 */
	var $querylog = array();


	/**
	 * Force a new connection
	 *
	 * @var bool
	 */
	var $force_new = false;


	/**
	 * Function mapping from generic functions to driver-specific ones
	 * @var	array
	 * @access protected
	 */
	var $functions = array();


	/**
	 * Constructor: assigns constant variables for database
	 * connection information
	 *
	 * @access private
	 */
	function DB_Abstract() {

		if (!is_array($this->functions)) {
			$this->halt('DB_Abstract cannot be instantiated directly, please use DB_MySQL or DB_MySQLi or DB_MsSQL');
		}

		if (defined('DATABASE_USER')) {
			$this->user = DATABASE_USER;
		}

		if (defined('DATABASE_PASSWORD')) {
			$this->password = DATABASE_PASSWORD;
		}

		if (defined('DATABASE_HOST')) {
			$this->host = DATABASE_HOST;
		}

		if (defined('DATABASE_NAME')) {
			$this->database = DATABASE_NAME;
		}

		register_shutdown_function(array(&$this, 'log_queries'));
	}






	/**
	 * Shutdown function that logs queries in db::querylog
	 *
	 * @access	public
	 */
	function log_queries() {

		foreach ($this->querylog AS $query) {
			$this->wrapper_query($query);
		}
	}





	/**
	 * Get the size of a table.
	 *
	 * @access	public
	 */
	function table_size($table) {
		return $this->wrapper_table_size($table);
	}



	/**
	 * Connects to a MySQL server using the set connection
	 * variables inside the class
	 *
	 * @access	public
	 *
	 * @return	mixed	The link-id for MySQL
	 */
	function connect() {

		if ($this->link_id) {
			return $this->link_id;
		}

		$this->link_id = $this->wrapper_connect($this->host, $this->user, $this->password);

		if (!$this->link_id) {
			$this->halt("connect($this->host, $this->user, \$this->password) failed. (Cannot connect to server)");
			return false;
		}

		if ($this->select_db($this->database)) {
			return true;
		}

		return false;
	}





	/**
	 * Wrapper for selecting the database
	 *
	 * @access	public
	 *
	 * @param	string	Name of the database
	 *
	 * @return	mixed	The link-id for MySQL
	 */
	function select_db($dbname) {

		if (!@$this->wrapper_select_db($dbname)) {
			$this->halt("cannot use database $dbname");
			return 0;
		} else {
			$this->database = $dbname;
			return $this->link_id;
		}
	}





	/**
	 * Create a new database
	 *
	 * @access public
	 *
	 * @param string $dbname The database name to create
	 */
	function create_db($dbname) {
		$this->wrapper_query("CREATE DATABASE $name");
	}





	/**
	 * Wrapper for executing a query
	 *
	 * @access	public
	 *
	 * @param	string	SQL query
	 *
	 * @return	mixed	Query-id result
	 */
	function query($query_string) {

		$query_string = trim($query_string);

		global $datastore;

		$mem = filesize_display(memory_get_usage());

		// check we have a string
		if (!$query_string) {
			return false;
		}

		// check we are connected
		if (!$this->connect()) {
			return false;
		}

		// get explain
		if (!$this->nodebug AND !defined('INSTALLER') AND (defined('DESKPRO_DEBUG_DISPLAYQUERIES') OR defined('DESKPRO_DEBUG_LOGQUERIES')) AND strpos($query_string, 'SELECT') !== false) {
			$explain = @$this->wrapper_query("EXPLAIN $query_string");
			while ($res = @$this->row_array($explain)) {
				$explain_log[] = $res;
			}
			$this->free($explain);
		}

		// start time
		list ($userc, $sec) = explode(' ', microtime());
		$this->start = ((float)$userc + (float)$sec);

		// run query; get certain variables
		$this->query_id = @$this->wrapper_query($query_string);

		// log to gateway debug
		if (defined('GATEWAYZONE') AND defined('GATEWAY_DEBUG_MYSQL')) {

			global $debug;
			if (is_object($debug)) {
				$debug->add("Query : $query_string");
			}
		}

		// if we have an error, deal with ith
		if (!$this->query_id) {
			$this->halt("Invalid SQL: $query_string");
		}

		// end time
		list ($userc, $sec) = explode(' ', microtime());
		$this->stop = ((float)$userc + (float)$sec);

		// duration
		$duration = $this->stop - $this->start;

		$results = $this->wrapper_affected_rows($this->query_id);

		// log query
		if (defined('DESKPRO_DEBUG_LOGQUERIES') AND !defined('INSTALLER')) {

			if (
			DESKPRO_DEBUG_LOGQUERIES == 1
			OR (DESKPRO_DEBUG_LOGQUERIES == 2 AND $duration > 0.1)
			OR (DESKPRO_DEBUG_LOGQUERIES == 3 AND $duration > 0.5)
			OR (DESKPRO_DEBUG_LOGQUERIES == 4 AND $duration > 5)
			)
			{

				if ($duration > 5) {
					$slow3 = 1;
				} elseif ($duration > 0.5) {
					$slow2 = 1;
				} elseif ($duration > 0.1) {
					$slow1 = 1;
				}

				$this->querylog[] = "
					INSERT INTO query_log
						(query, duration, matches, stamp, keytype,
						slow1, slow2, slow3, explain_log, filename)
					VALUES
						('" . $this->escape($query_string) . "',
						'$duration', '" . $this->escape($results) . "',
						" . TIMENOW . ", '$data[key]', '$slow1',
						'$slow2', '$slow3', '" . $this->escape(serialize($explain_log)) . "',
						'" . $this->escape($_SERVER['SCRIPT_NAME']) . "'
					)";
			}
		}

		if ((defined('DESKPRO_DEBUG_DISPLAYQUERIES') AND !defined('INSTALLER') AND !defined('NODISPLAYQUERIES')) OR defined('DESKPRO_DEBUG_DEVELOPERMODE_FOOTER')) {

			$datastore['query_count']++;
			$datastore['query_log'][] = array(
				'count' => $datastore['query_count'],
				'duration' => $duration,
				'query_string' => $query_string,
				'explain_log' => $explain_log,
				'memory' => $mem . ' => ' . filesize_display(memory_get_usage())
			);
			$datastore['query_time'] += $duration;
		}

		if (defined('DEVELOPERMODE')) {

			if (substr($query_string, 0, 6) == 'UPDATE') {

				// get the table
				$table = preg_match("#UPDATE ([a-zA-Z_]+) #", $query_string, $matches);
				$result = $this->query_return("SELECT COUNT(*) AS total FROM $matches[1]");
				$total = $result['total'];

				if ($total > 1 AND $total = $results) {
					echo "We just updated every row. Good idea?";
				}
			}
		}

		return $this->query_id;

	}





	/**
	 * Perform an UPDATE query.
	 *
	 * @param string $table The table to update
	 * @param array $fields Array of fields to set
	 * @param string $where Where clause to include
	 * @return resource
	 */
	function query_update($table, $fields, $where = false) {

		if (!$fields) {
			return false;
		}

		$where = $this->build_and_where($where);

		return $this->query("UPDATE $table SET " . array2sqlinsert($fields) . ($where ? " WHERE $where " : ''));
	}





	/**
	 * Perform an INSERT query and return the insert id.
	 *
	 * @param string $table The table to insert into
	 * @param array $fields Array of fields to set
	 * @return integer
	 */
	function query_insert($table, $fields = array()) {

		$this->query("INSERT INTO $table " . ($fields ? " SET " . array2sqlinsert($fields) : ''));

		return $this->insert_id();
	}





	/**
	 * Performs a REPLACE query and returns the insert id.
	 *
	 * @param unknown_type $table
	 * @param unknown_type $fields
	 */
	function query_replace($table, $fields = array()) {

	    $this->query("REPLACE INTO $table " . ($fields ? " SET " . array2sqlinsert($fields) : ''));

	    return $this->insert_id();
	}





	/**
	 * Perform a DELETE query
	 *
	 * @param string $table The table to delete from
	 * @param string $where Where clause to include
	 * @return int Number of affected rows
	 */
	function query_delete($table, $where = false) {

	    $where = $this->build_and_where($where);

		$this->query("DELETE FROM $table " . ($where ? " WHERE $where " : ''));
		return $this->affected_rows();
	}





	/**
	 * No errors
	 *
	 * @access	public
	 *
	 * @param	string	The SQL query
	 *
	 * @return	mixed	Query-id result
	 */
	function query_silent($query_string) {

		// check we have a string
		if (!trim($query_string)) {
			return false;
		}

		// check we are connected
		if (!$this->connect()) {
			return false;
		}

		$this->query_id = $this->wrapper_query($query_string);

		if (!$this->query_id) {
			$this->error = $this->geterrdesc();
			$this->errno = $this->geterrno();
		}

		return $this->query_id;

	}

	/**
	 * No errors (not even vlaue)
	 *
	 * @access	public
	 *
	 * @param	string	The SQL query
	 *
	 * @return	mixed	Query-id result
	 */
	function query_silent_extra($query_string) {

		// check we have a string
		if (!trim($query_string)) {
			return false;
		}

		// check we are connected
		if (!$this->connect()) {
			return false;
		}

		$this->query_id = $this->wrapper_query($query_string);

		return $this->query_id;

	}

	/**
	 * Sends a query that is not timed, logged, or EXPLAINed;
	 * certain queries like SHOW will not work in db::query()
	 * because of the EXPLAIN system.
	 *
	 * @access	public
	 *
	 * @param	string	The SQL query
	 *
	 * @return	mixed	Query-id result
	 */
	function query_quiet($query_string) {

		// check we have a string
		if (!trim($query_string)) {
			return false;
		}

		// check we are connected
		if (!$this->connect()) {
			return false;
		}

		$this->query_id = $this->wrapper_query($query_string);

		if (!$this->query_id) {
			$this->halt("Invalid SQL: $query_string");
		}

		return $this->query_id;
	}

	/**
	 * Fetches the next record in the database as an array
	 *
	 * @access	public
	 *
	 * @param	mixed	A query-id that isn't the latest
	 * @param	mixed	Type of array to return; either DB_RETURN_ASSOC, DB_RETURN_NUM, or DB_RETURN_BOTH
	 *
	 * @return	array	The next row in a result
	 */
	function row_array($query_id = '', $type = DB_RETURN_ASSOC) {

		$query_id = ifvalor($query_id, $this->query_id);

		if (!$query_id) {
			$this->halt("next_record called with no query pending.");
			return 0;
		}

		return $this->wrapper_fetch_array($query_id, $type);
	}




	function build_and_where($wheres) {
	    if (!is_array($wheres)) {
	        return $wheres;
	    }

	    $where_bits = array();

        foreach ($wheres as $field => $val) {
            if (is_array($val) AND $val[0] == 'NULL') {
                $val = 'NULL';
			} elseif (!is_int($val) AND (!ctype_digit(strval($val)) OR substr($val, 0, 1) === '0')) {
                $val = "'" . $this->escape($val) . "'";
            }
            $where_bits[] = "$field = $val";
        }

        $where = implode(' AND ', $where_bits);

        return $where;
	}





	/**
	 * Fetches the number of rows a query affected
	 *
	 * @access	public
	 *
	 * @return	integer	The number of affected rows
	 */
	function affected_rows() {
		return $this->wrapper_affected_rows($this->query_id);
	}






	/**
	 * Returns the ID of the last-inserted row
	 *
	 * @access	public
	 *
	 * @return	integer	The latest ID
	 */
	function insert_id() {
		return $this->wrapper_insert_id($this->link_id);
	}





	/**
	 * Fetches the number of rows a MySQL query gathered
	 *
	 * @access	public
	 *
	 * @return	integer	The number of rows
	 */
	function num_rows($query_id = false) {
		$query_id = ifvalor($query_id, $this->query_id);
		return $this->wrapper_num_rows($query_id);
	}





	/**
	 * Get the number of fields in a result
	 *
	 * @access	public
	 *
	 * @return	int	The number of fields
	 */
	function num_fields($query_id = false) {
		$query_id = ifvalor($query_id, $this->query_id);
		return $this->wrapper_num_fields($query_id);
	}





	/**
	 * Releases a MySQL result from the database server
	 * - only SELECT, SHOW, EXPLAIN, DESCRIBE queries return a resource
	 * @access	public
	 */
	function free($query_id = false) {
		$query_id = ifvalor($query_id, $this->query_id);
		return $this->wrapper_free($query_id);
	}





	/**
	 * Escapes a string using MySQL for language protection
	 *
	 * @access	public
	 *
	 * @param	string	The unescaped string
	 *
	 * @return	string	The escaped string
	 */
	function escape($string) {
		$this->connect();
		return @$this->wrapper_escape($string);
	}





	/**
	 * add escape sequence for special characters like % and _
	 * @access Public
	 * @param string $text	-	text to which escape sequence has to be added
	 * @return string		-	replaced string
	 */
	function escape_like($string) {
		$this->connect();
		return str_replace(array('%', '_'), array('\%', '\_'), @$this->wrapper_escape($string));
	}





	/**
	 * Returns the name of the specified field index
	 *
	 * @access	public
	 *
	 * @param	integer	The numerical accessor for the field
	 *
	 * @return	string	The name of the field
	 */
	function field_name($index, $query_id = false) {
		$query_id = ifvalor($query_id, $this->query_id);
		return $this->wrapper_field_name($index, $query_id);
	}





	/**
	 * Returns a list of field names for a certain table
	 *
	 * @access	public
	 *
	 * @param	string	The table name
	 *
	 * @return	array	List of field names in the table
	 */
	function field_names($table) {
		return $this->query_return_table_columns($table);
	}





	function error_email($msg) {

		global $request, $settings;

		$backtrace = debug_backtrace();

		ob_start();
		print_r($backtrace);
		$backtrace = ob_get_contents();
		ob_end_clean();

		// Blank out the password in the backtrace
		$backtrace = str_replace($this->password, '********', $backtrace);

		$message = "Database Error\n"
		         . "=================================================\n"
		         . "$msg\n"
		         . "\n\n"
		         . "(Error Number) Error\n"
   		         . "=================================================\n"
   		         . "({$this->errno}) {$this->error}\n"
   		         . "\n\n"
   		         . "Date\n"
   		         . "=================================================\n"
   		         . date("l dS \of F Y h:i:s A") . "\n"
   		         . "\n\n"
   		         . "Script\n"
   		         . "=================================================\n"
   		         . (PATH ? PATH : 'N/A') . "\n"
   		         . "\n\n"
   		         . DP_NAME."\n"
   		         . "=================================================\n"
   		         . "{$settings['deskpro_version']} ({$settings['deskpro_version_internal']})"
   		         . "\n\n"
   		         . "Referer\n"
   		         . "=================================================\n"
   		         . ($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : 'N/A') . "\n"
   		         . "\n\n"
   		         . "Debug Backtrace\n"
   		         . "=================================================\n"
   		         . ($backtrace ? $backtrace : 'N/A')
   		         . "\n\n"
   		         . "Request Object\n"
   		         . "=================================================\n"
   		         . (isset($request) ? print_r($request, true) : 'N/A') . "\n";

   		if ($this->getErrorEmailExplain()) {
   			$message = $this->getErrorEmailExplain() . "\n\n\n" . $message;
   		}

		if (is_email(DATABASE_ERROR_MAIL)) {
			@dp_mail(DATABASE_ERROR_MAIL, '', DP_NAME . ' Database Error', $message, '', '', '', '', '', array('X-debug-filepath' => 1));
		} else {
			echo "Couldn not send mail :: no address specified.<br />Check the DATABASE_ERROR_MAIL value in your includes/config.php file.";
		}

		if (defined('INSTALLER')) {
			$install_error_email = 'db_upgrade_error@deskpro.com';

			if (defined('DESKPRO_INSTALLER_DEBUG_EMAIL')) {
				$install_error_email = DESKPRO_INSTALLER_DEBUG_EMAIL;
			}

			if ($install_error_email AND is_email($install_error_email)) {
				@dp_mail($install_error_email, '', 'Installer Database Error', $message, '', '', '', '', '', array('X-debug-filepath' => 1));
			}
		}
	}





	function getErrorEmailExplain() {
		return '';
	}





	/**
	 * Show/get a simple error message
	 *
	 * @param string The error message
	 * @param bool Return the error message instead of showing it?
	 *
	 * @return mixed String if $return is true, nothing otherwise
	 */
	function error_simple($msg, $return = false) {
		global $user, $settings;

		$error = "There has been a Database Error :\n\n~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n\n";
		$error .= $msg . "\n\n";
		$error .= "Error  : " . $this->geterrdesc() . "\n\n";
		$error .= "Error Number : " . $this->geterrno() . "\n\n";
		$error .= "Date         : " . gmdate("M d Y H:i:s") . "\n\n";
		$error .= "Script       : " . PATH . "\n\n";
		$error .= "Referrer     : " . $_SERVER['HTTP_REFERER'] . "\n\n";
		$error .= "IP Address   : " . IPADDRESS . "\n\n";
		$error .= "Username     : " . $user['username'] . "\n\n";
		$error .= DP_NAME."      : {$settings['deskpro_version']} ({$settings['deskpro_version_internal']})\n\n";
		$error .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";

		if ($return) {
			return $error;
		} else {
			echo $error;
		}
	}





	/**
	 * Show an error page
	 *
	 * @param string The error message
	 */
	function error_show($msg) {
		global $user;

		if (defined('DATABASE_ERROR_SCRIPT')) {
			@include(DATABASE_ERROR_SCRIPT);
			return;
		}

		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html>
		<head>
			<meta http-equiv="X-UA-Compatible" content="IE=7" />
			<title>Database Error</title>
			<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
			<style type="text/css">
			<!--
			blockquote { margin-top: 75px; }
			p { font: 11px tahoma, verdana, arial, sans-serif; }
			-->
			</style>
		</head>
		<body>
			<blockquote>
				<blockquote>
					<p><strong>There seems to have been a problem with our database.</strong></p>
					<p>Please try again by clicking the <a href="#" onclick="window.location = window.location;">Refresh</a> button in your web browser.</p>
					<p>An e-mail has been sent to our <a href="mailto:<?php echo DATABASE_ERROR_MAIL?>">Technical Staff</a>, whom you should contact if the problem continues.</p>
					<p>We apologise for any inconvenience.</p>


		<?php

		echo "<!--\n\n";
		echo "Database error in " . DP_NAME . " :\n\n";

		echo "$msg\n\n";

		echo "Error  : " . $this->geterrdesc() . "\n";
		echo "Error Number : " . $this->geterrno() . "\n";
		echo "Date         : " . gmdate("M d Y H:i:s") . "\n";
		echo "Script       : " . htmlspecialchars(PATH) . "\n";
		echo "Referrer     : " . htmlspecialchars($_SERVER['HTTP_REFERER']) . "\n";
		echo "IP Address   : " . IPADDRESS . "\n";
		echo "Username     : " . $user['username'] . "\n";
		echo "\n\n";
		echo "-->";
		?>


				</blockquote>
			</blockquote>

			<?php
			if (defined('DESKPRO_DEBUG_DEVELOPERMODE')) {
				echo '<textarea rows="20" cols="40" style="width: 95%">';
				echo "Database error in " . DP_NAME . " :\n\n";

				echo "$msg\n\n";

				echo "Error  : " . $this->geterrdesc() . "\n";
				echo "Error Number : " . $this->geterrno() . "\n";
				echo "Date         : " . gmdate("M d Y H:i:s") . "\n";
				echo "Script       : " . htmlspecialchars(PATH) . "\n";
				echo "Referrer     : " . htmlspecialchars($_SERVER['HTTP_REFERER']) . "\n";
				echo "IP Address   : " . IPADDRESS . "\n";
				echo "Username     : " . $user['username'] . "\n";
				echo "\n\n";
				echo "\n~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
				echo "\n\n";
				if (function_exists('debug_print_backtrace')) {
					debug_print_backtrace();
				} elseif (function_exists('debug_backtrace')) {
					print_r(debug_backtrace());
				}
				echo '</textarea>';
			}
			?>
		</body>
		</html>
	<?php

	}





	/**
	 * Custom MySQL error handler
	 *
	 * @access	private
	 *
	 * @param	string	Custom halt message
	 */
	function halt($msg = '') {

		// we only want to do this once
		global $settings;

		$this->error = $this->geterrdesc();
		$this->errno = $this->geterrno();

		$this->nodebug = true;

		if (!$this->error_halt) {
			return;
		}

		// only do this once
		static $ran;
		if ($ran >= 1) {
			exit();
		}
		$ran++;

		/*************************
		* More serious, we are in email gateway. Handle back to the email gateway to sort out
		*************************/
		if (defined('GATEWAYZONE')) {

			$error = new gatewayCriticalError();
			$error->databaseError($this, $msg);


		/*************************
		* Installer: show error and
		* tell them to email DeskPRO
		*************************/
		} else if (defined('INSTALLER')) {

			if (defined('UPGRADE_TYPE') AND UPGRADE_TYPE == 'shell') {
				$html = "A database error has occurred\n"
				      . "=============================\n\n"
				      . "A database error at this point will most likely result in an incomplete install or upgrade, so you should stop the process and contact " . DP_NAME . " with these details.\n\n"
				      . "Visit the helpdesk (http://support.deskpro.com/) or email support@deskpro.com\n\n"
				      . $this->error_simple($msg, true);

			} else {
				$html = '<div style="padding:5px;border:2px solid #A20000;background:#F0F0F0;">';
				$html .= '<h3>A database error has occurred.</h3> A database error at this point will most likely
				result in an incomplete install or upgrade, so you should stop the process and contact ' . DP_NAME . ' with these details. ';
				$html .= 'Visit the <a href="http://support.deskpro.com/">' . DP_NAME . ' Helpdesk</a> or email <a href="mailto:support@deskpro.com">support@deskpro.com</a>.<br /><br />Please do <strong>NOT</strong> continue. You will need to restart your install/upgrade.';
				$html .= '<hr /><textarea cols="48" rows="12" style="width:100%;">';
				$html .= dp_html($this->error_simple($msg, true));
				$html .= '</textarea></div>';
			}

			echo $html;


		/*************************
		* Show an error
		*************************/
		} else  {

			if ($this->simple_error) {
				$this->error_simple($msg);
			} else {
				$this->error_show($msg);
			}

		}

		/*************************
		* Log the Error
		*************************/

		if (!defined('INSTALLER')) {
			log_error('mysql', $msg, $this->error_simple($msg, TRUE));
		}

		/*************************
		* Send Email
		*************************/

		$this->error_email($msg);

		/*************************
		* Stop?
		*************************/

		if (!defined('GATEWAYZONE')) {
			exit();
		}
	}





	/**
	 * Get the current MySQL error number
	 *
	 * @access	private
	 *
	 * @return	integer	The error number
	 */
	function geterrno() {

		if ($this->link_id) {
			$this->errno = $this->wrapper_errno($this->link_id);
		} else {
			$this->errno = $this->wrapper_errno();
		}
		return $this->errno;
	}





	/**
	 * Get a human-readable MySQL error
	 *
	 * @access	private
	 *
	 * @return	string	The error description
	 */
	function geterrdesc() {

		if ($this->link_id) {
			$this->error = $this->wrapper_error($this->link_id);
		} else {
			$this->error = $this->wrapper_error();
		}
		return $this->error;
	}





	/**
	 * Finds the MySQL max packet size
	 *
	 * @access	public
	 *
	 * @return	integer	The largest MySQL packet size
	 */
	function max_allowed_packet() {

		if ($this->max_packet) {
			return $this->max_packet;
		}

		// get max packet
		$result = $this->query_return("SHOW variables LIKE 'max_allowed_packet'");
		$this->max_packet = round(0.85 * $result['Value']);

		// give default of 1MB
		give_default($this->max_packet, 850000);

		return $this->max_packet;
	}





	function emptytable($table) {
		$this->query("TRUNCATE TABLE `$table`");
	}








	################################################################
	####################### DEFAULT WRAPPERS #######################
	################################################################

	function wrapper_connect($host, $user, $password) {
		return @$this->functions['connect']($host, $user, $password);
	}

	function wrapper_select_db($dbname) {
		return @$this->functions['select_db']($dbname);
	}

	function wrapper_query($query_string) {
		return @$this->functions['query']($query_string);
	}

	function wrapper_fetch_array($query_id, $type = DB_RETURN_ASSOC) {
		return @$this->functions['fetch_array']($query_id, $type);
	}

	function wrapper_affected_rows($query_id) {
		return @$this->functions['affected_rows']($this->link_id);
	}

	function wrapper_insert_id() {
		return @$this->functions['insert_id']($this->link_id);
	}

	function wrapper_num_rows($query_id) {
		return @$this->functions['num_rows']($query_id);
	}

	function wrapper_num_fields($query_id) {
		return @$this->functions['num_fields']($query_id);
	}

	function wrapper_free($query_id) {
		return @$this->functions['free']($query_id);
	}

	function wrapper_escape($str) {
		return @$this->functions['escape']($str);
	}

	function wrapper_field_name($query_id, $offset) {
		return @$this->functions['field_name']($query_id, $offset);
	}

	function wrapper_errno($link_id = null) {
		if (is_resource($link_id)) {
			return @$this->functions['errno']($link_id);
		} else {
			return @$this->functions['errno']();
		}
	}

	function wrapper_error($link_id = null) {
		if (is_resource($link_id)) {
			return @$this->functions['error']($link_id);
		} else {
			return @$this->functions['error']();
		}
	}

	function wrapper_field_names() {
		return @$this->functions['list_fields']();
	}

	function wrapper_table_size($table) {
		trigger_error('wrapper_table_size not implemented for database module', E_USER_WARNING);
		return 0;
	}










	################################################################
	####################### QUERY SHORT CUTS #######################
	################################################################

	/**
	 * Run a query and return the first row in the result set
	 *
	 * @access	public
	 *
	 * @param	string	The SQL query
	 * @param	integer	Type of array to be returned; either DB_RETURN_ASSOC, DB_RETURN_NUM, or DB_RETURN_BOTH
	 *
	 * @return	array	First row in the result set
	 */
	function query_return($query_string, $type = DB_RETURN_ASSOC) {

		$query_id = $this->query($query_string);
		return $this->row_array($query_id, $type);
	}





	/**
	 * Perform a SELECT COUNT(*) on a given table
	 *
	 * @access	public
	 *
	 * @param	string	The table name
	 * @param	string	Additional WHERE clause
	 *
	 * @return	integer	The COUNT(*) result
	 */
	function query_count($table, $where = '', $addwhere = TRUE) {

		if ($where) {
		    $where = $this->build_and_where($where);
			$where = iff($addwhere, " WHERE ") . $where;
		}

		$result = $this->query_return("SELECT COUNT(*) AS total FROM $table $where");
		return $result['total'];
	}





	/**
	 * Figure out how many matches
	 *
	 * @access	public
	 *
	 * @param	string	Table name
	 * @param	string	Additional WHERE clause
	 *
	 * @return	integer	Number of rows for the match
	 */
	function query_match($table, $where = '') {

		if ($where) {
		    $where = $this->build_and_where($where);
			$where = " WHERE $where";
		}

		$result = $this->query_return("SELECT COUNT(*) AS total FROM $table $where");

		return $result['total'];
	}





	/**
	 * Check to see if a given WHERE clause is true in a
	 * given table
	 *
	 * @access	public
	 *
	 * @param	string	The table name
	 * @param	string	Additional WHERE clause
	 *
	 * @return	bool	Whether or not there are any matching rows
	 */
	function query_amatch($table, $where = '') {

		if ($where) {
		    $where = $this->build_and_where($where);
			$where = " WHERE $where";
		}

		$result = $this->query_return("SELECT COUNT(*) AS total FROM $table $where LIMIT 1");

		if ($result['total'] > 0) {
			return true;
		} else {
			return false;
		}
	}





	/**
	 * Run a query and return the first column of the first row
	 *
	 * @param string $query_string The SQL query
	 * @return mixed The data
	 */
	function query_return_first($query_string) {

		$query_id = $this->query($query_string);

		if (!$query_id) {
			return false;
		}

		$row = $this->row_array($query_id, DB_RETURN_NUM);

		if (!$row) {
			return false;
		}

		return $row[0];
	}


	/**
	 * Run a query and return only a single column
	 *
	 * @param string $query_string The SQL query
	 * @param integer $col The column to return
	 * @return array
	 */
	function query_return_col($query_string, $col = 0) {

		$query_id = $this->query($query_string);

		if (!$query_id) {
			return false;
		}

		$data = array();

		while ($row = $this->row_array($query_id, DB_RETURN_NUM)) {
			$data[] = $row[$col];
		}

		if (!$data) {
			return false;
		}

		return $data;

	}





	/**
	 * Run a query, grouping the results by a given field
	 *
	 * @access	public
	 *
	 * @param	string	The SQL query
	 * @param	string	Name of the field to group by
	 * @param	string	Fieldname; if specified the result will only contain this
	 *
	 * @return	array	Grouped data results
	 */
	function query_return_group($query_string, $field, $fieldname = false) {

		$query_id = $this->query($query_string);

		while ($res = $this->row_array($query_id, DB_RETURN_ASSOC)) {
			if ($fieldname) {
				$data[ $res["$field"] ][] = $res[$fieldname];
			} else {
				$data[ $res["$field"] ][] = $res;
			}
		}

		return $data;
	}





	/**
	 * Run a query putting all the results into an array; if
	 * an index is specified, the returned array will be
	 * indexed by that key
	 *
	 * @access	public
	 *
	 * @param	string	The query string
	 * @param	string	An index in the results to be used as the new array indices
	 *
	 * @return	array	Result data array
	 */
	function query_return_array($query_string, $index = '') {

		$query_id = $this->query($query_string);

		while ($res = $this->row_array($query_id, DB_RETURN_ASSOC)) {
			if ($index) {
				$data[ $res["$index"] ] = $res;
			} else {
				$data[] = $res;
			}
		}

		return $data;
	}





	/**
	 * Run a query putting all the results into an array; if
	 * a fieldname is set it is the only data returned. Else
	 * it mimics query_return_array()
	 *
	 * @access	public
	 *
	 * @param	string	The query string
	 * @param	string	Fieldname; if specified the result will only contain this
	 * @param	string	Index in the results to be used as the new array indicies
	 *
	 * @return	array	Result data array
	 */
	function query_return_array_id($query_string, $fieldname = '', $index = 'id') {

		$query_id = $this->query($query_string);

		while ($res = $this->row_array($query_id, DB_RETURN_ASSOC)) {

			if ($fieldname) {
				if ($index) {
					$data[ $res["$index"] ] = $res[$fieldname];
				} else {
					$data[] = $res[$fieldname];
				}
			} else {
				if ($index) {
					$data[ $res["$index"] ] = $res;
				} else {
					$data[] = $res;
				}
			}
		}

		return $data;
	}



	/**
	 * Check a table to see if it has a particular column.
	 *
	 * @param string $table The table name to check
	 * @param string $check_col The column to check for
	 * @return bool
	 */
	function query_table_has_column($table, $check_col)
	{
		return in_array($check_col, $this->query_return_table_columns($table));
	}



	/**
	 * Fetch all columns that exist on a table.
	 *
	 * @param string $table The table to fetch info from
	 * @return array
	 */
	function query_return_table_columns($table)
	{
		$q = $this->query("DESCRIBE `$table`");

		$cols = array();
		while ($res = $this->row_array($q, DB_RETURN_ASSOC)) {
			$cols[] = $res['Field'];
		}

		return $cols;
	}



	/**
	 * Check to see if a table exists.
	 *
	 * @param string $table The table name ot check for
	 * @return bool
	 */
	function query_table_exists($table)
	{
		$q = $this->query("SHOW TABLES LIKE '$table'");

		if ($this->row_array($q)) {
			return true;
		}

		return false;
	}
}

