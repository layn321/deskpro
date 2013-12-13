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
// | - MySQLi database access class
// +-------------------------------------------------------------+

require_once(INC . 'classes/database/mysql.php');

/**
 * MySQLi Database Abstraction Layer
 *
 * This class extends the regular MySQL database abstraction
 * layer and implements MySQLi for version 4.1+
 *
 * @package	DeskPRO
 * @version	$Id$
 */
class DB_MySQLi extends DB_MySQL {

	var $driver_name = 'MySQLi';

	/**
	 * Function mapping from generic functions to MySQLi ones
	 * @var	array
	 * @access protected
	 */
	var $functions = array(
		'connect'		=> 'mysqli_connect',
		'select_db'		=> 'mysqli_select_db',
		'query'			=> 'mysqli_query',
		'fetch_array'	=> 'mysqli_fetch_array',
		'affected_rows'	=> 'mysqli_affected_rows',
		'insert_id'		=> 'mysqli_insert_id',
		'num_rows'		=> 'mysqli_num_rows',
		'num_fields'	=> 'mysqli_num_fields',
		'free'			=> 'mysqli_free_result',
		'escape'		=> 'mysqli_real_escape_string',
		'real_escape'	=> 'mysqli_real_escape_string',
		'errno'			=> 'mysqli_errno',
		'error'			=> 'mysqli_error',
	);





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
		return @$this->functions['connect']($host, $user, $password);
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
		return $this->functions['select_db']($this->link_id, $dbname);
	}





	/**
	 * Executes a MySQLi query in the current connection
	 *
	 * @access	public
	 *
	 * @param	string	The SQL query
	 *
	 * @return	mixed	Query-id result
	 */
	function wrapper_query($query_string) {
		return $this->functions['query']($this->link_id, $query_string);
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
		return $this->functions['escape']($this->link_id, $string);
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
	function wrapper_field_name($index = 0) {

		$queryid = $this->query_id;
		$field = mysqli_fetch_field_direct($queryid, $index);

		if ($field) {
			return $field->name;
		} else {
			return '';
		}

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

		$res = $this->query("SELECT * FROM $table LIMIT 1");

		$fielddata = mysqli_fetch_fields($res);
		$fields = array();

		foreach($fielddata as $f)
		{
			$fields[] = $f->name;
		}

		return $fields;
	}
}

