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
// | - MySQL database access class
// +-------------------------------------------------------------+

require_once(INC . 'classes/database/abstract.php');





/**
 * MySQL Database Abstraction Layer
 *
 * This class is a wrapper for PHP's MySQL functions and
 * additionally has functions that perform common result
 * processing.
 *
 * @package	DeskPRO
 * @version	$Id: mysql.php 7176 2012-07-13 14:48:34Z chroder $
 */
class DB_MySQL extends DB_Abstract {

	var $driver_name = 'MySQL';

	/**
	 * Function mapping from generic functions to MySQL ones
	 * @var	array
	 * @access protected
	 */
	var $functions = array(
		'connect'		=> 'mysql_connect',
		'select_db'		=> 'mysql_select_db',
		'query'			=> 'mysql_query',
		'fetch_array'	=> 'mysql_fetch_array',
		'affected_rows'	=> 'mysql_affected_rows',
		'insert_id'		=> 'mysql_insert_id',
		'num_rows'		=> 'mysql_num_rows',
		'num_fields'	=> 'mysql_num_fields',
		'free'			=> 'mysql_free_result',
		'escape'		=> 'mysql_escape_string',
		'real_escape'	=> 'mysql_real_escape_string',
		'field_name'	=> 'mysql_field_name',
		'errno'			=> 'mysql_errno',
		'error'			=> 'mysql_error',
		'list_fields'	=> 'mysql_list_fields',
	);





	/**
	 * Connect to a MySQL database. Same as DB_abstract except it checks which
	 * version the server is and sets sql_mode accordingly.
	 *
	 * @return unknown
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

			$version = $this->query_return("SELECT VERSION() AS v");
			$version = preg_replace('#[^\.0-9]#', '', $version['v']);

			if(version_compare($version, '4.1', '>=')) {
				$this->query_silent("SET SESSION sql_mode=''");
			}

			return true;
		}

		return false;
	}





	/**
	 * Makes a new database connection
	 *
	 * @access public
	 *
	 * @param string $host The server
	 * @param string $user The database username
	 * @param string $password The password to login with
	 * @return mixed The link id for MySQL
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
	 * @return	mixed	The link-id for MySQL
	 */
	function wrapper_select_db($dbname) {
		return $this->functions['select_db']($dbname, $this->link_id);
	}





	/**
	 * Executes a MySQL query in the current connection
	 *
	 * @access	public
	 *
	 * @param	string	The SQL query
	 *
	 * @return	mixed	MySQL query-id result
	 */
	function wrapper_query($query_string) {
		return $this->functions['query']($query_string, $this->link_id);
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
	function wrapper_escape($string) {

		$this->connect();

		if (version_compare(phpversion(), '4.3.0') == '-1') {
			$string = $this->functions['escape']($string);
		} else {
			$string = $this->functions['real_escape']($string);
		}

		return $string;
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
	function wrapper_field_name($columnnum, $query_id) {
		return $this->functions['field_name']($query_id, $columnnum);
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

		$fields = $this->functions['list_fields']($this->database, $table, $this->link_id);
		$columns = $this->functions['num_fields']($fields);

		for ($i = 0; $i < $columns; $i++) {
			$array[] = $this->functions['field_name']($fields, $i);
		}

		return $array;
	}





	/**
	 * Get the size of a table.
	 *
	 * @param string $table
	 * @return integer
	 */
	function wrapper_table_size($table) {
		$tmp = $this->query_return("SHOW TABLE STATUS LIKE '$table'");
		return (int)$tmp['Data_length'];
	}





	function getErrorEmailExplain() {
		return "There has been an SQL error with your " . DP_NAME . " installation. The guide below
should help you solve this.

i) If the error is a message suggests that 'the mysql server has gone away'
then this means that MySQL server was not available to the PHP script. MySQL
may have crashed or been restarted during the connection. You should ensure
that mySQL is operational.

ii) If you get error 145 (can't open file) then this probably means you have
table corruption. You should run the query REPAIR table x where x is the table
with corruption.

iii) Any other database error should immediately be forwarded to
support@deskpro.com. Please include steps to repeat the generation of the MySQL
error in your report.";
	}
}

