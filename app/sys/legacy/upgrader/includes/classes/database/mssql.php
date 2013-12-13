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
// | - MsSQL database access class
// +-------------------------------------------------------------+

require_once(INC . 'classes/database/abstract.php');





/**
 * MsSQL Database Abstraction Layer
 *
 * This class is a wrapper for PHP's MsSQL functions and
 * additionally has functions that perform common result
 * processing.
 *
 * @package	DeskPRO
 * @version	$Id$
 */
class DB_MsSQL extends DB_Abstract {

	var $driver_name = 'MsSQL';
	var $nodebug = true; // dont debug

	/**
	 * Function mapping from generic functions to MySQL ones
	 * @var	array
	 * @access protected
	 */
	var $functions = array(
		'connect'		=> 'mssql_connect',
		'select_db'		=> 'mssql_select_db',
		'query'			=> 'mssql_query',
		'fetch_array'	=> 'mssql_fetch_array',
		'affected_rows'	=> 'mssql_rows_affected',
		'insert_id'		=> 'mssql_insert_id',
		'num_rows'		=> 'mssql_num_rows',
		'num_fields'	=> 'mssql_num_fields',
		'free'			=> 'mssql_free_result',
		'escape'		=> 'mssql_escape_string',
		'field_name'	=> 'mssql_field_name',
		'errno'			=> 'mssql_errno',
		'error'			=> 'mssql_get_last_message',
		'list_fields'	=> 'mssql_list_fields',
	);





	/**
	 * Find a match using SELECT TOP 1 * FROM table
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
			$where = " WHERE $where";
		}

		$this->query("SELECT TOP 1 * FROM $table $where");
		return $this->num_rows();
	}





	/**
	 * Finds the MySQL max packet size
	 *
	 * @access	public
	 *
	 * @return	integer	The largest MySQL packet size
	 */
	function max_allowed_packet() {
		return 850000;
	}





	/**
	 * Makes a new database connection
	 *
	 * @access public
	 *
	 * @param string $host The server
	 * @param string $user The database username
	 * @param string $password The password to login with
	 * @return mixed The link id for MsSQL
	 */
	function wrapper_connect($host, $user, $password) {
		return @$this->functions['connect']($host, $user, $password, $this->force_new);
	}





	/**
	 * Selects the database through the open link-id
	 *
	 * @access	public
	 *
	 * @param	string	The name of the database
	 *
	 * @return	boolean True on success, false on failure
	 */
	function wrapper_select_db($dbname) {
		return $this->functions['select_db']($dbname, $this->link_id);
	}





	/**
	 * Executes a MsSQL query in the current connection
	 *
	 * @access	public
	 *
	 * @param	string	The SQL query
	 *
	 * @return	mixed	MsSQL query-id result
	 */
	function wrapper_query($query_string) {
		return $this->functions['query']($query_string, $this->link_id);
	}






	/**
	 * Escapes a string. In MsSQL the only string that needs escaping
	 * is the single quote. String values MUST be in single quotes because
	 * there is no way to escape double quotes.
	 *
	 * @access	public
	 *
	 * @param	string	The unescaped string
	 *
	 * @return	string	The escaped string
	 */
	function wrapper_escape($string) {
		return str_replace("'", "''", $string);
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
	function wrapper_field_name($columnnum) {
		return $this->functions['field_name']($this->query_id, $columnnum);
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
	function wrapper_field_names($table) {

		$rowdata = $this->query_return("SELECT TOP 1 * FROM $table");
		$fields = array();

		foreach ($rowdata as $f => $x) {
			$fields[] = $f;
		}

		return $fields;
	}




	/**
	 * Returns the number of affected rows. There is mssql_rows_affacted,
	 * but there have been reports of errors in php5.
	 *
	 * @return integer
	 */
	function wrapper_affected_rows($query_id) {
		$link_id = $this->link_id;

		if (function_exists($this->functions['affected_rows'])) {
			return @$this->functions['affected_rows']($link_id);
		} else {
			$res = @mssql_query("SELECT @@rowcount", $this->link_id);
			$rows = @mssql_result($res, 0, 0);
			return (int)$rows;
		}
	}





	/**
	 * Returns the last inserted ID.
	 *
	 * @return mixed
	 */
	function wrapper_insert_id() {
		$res = @mssql_query("SELECT @@IDENTITY", $this->link_id);
		$rows = @mssql_result($res, 0, 0);
		return (int)$rows;
	}





	/**
	 * There is no errno function in mssql. This will always return -1 when there is
	 * an error, and 0 when there is no error.
	 *
	 * @return integer
	 */
	function wrapper_errno($link_id = null) {
		if ($this->wrapper_error($link_id)) {
			return -1;
		}
		return 0;
	}




	/**
	 * Returns the last message from mssql.
	 *
	 * @param unknown_type $link_id
	 * @return unknown
	 */
	function wrapper_error($link_id = null) {
		$last_msg = trim(@$this->functions['error']());

		if (strpos(strtolower($last_msg), '(severity') !== false) {
			return $last_msg;
		}

		return '';
	}





	function error_email($msg) {

		$message = "Database Error:
$msg

MsSQL Error
" . $this->geterrdesc() .
"

Date
" . date("l dS \of F Y h:i:s A") .
"

Script
" . PATH .
"

Referer
" . $_SERVER['HTTP_REFERER'];

if (is_email(DATABASE_ERROR_MAIL)) {
	@dp_mail(DATABASE_ERROR_MAIL, '', DP_NAME . ' database Error', $message, '', '', '', '', 1, 1);
} else {
	echo "Couldn not send mail :: no address specified.<br />Check the DATABASE_ERROR_MAIL value in your includes/config.php file.";
}
	}
}

