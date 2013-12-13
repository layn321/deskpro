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

require_once(INC . 'classes/database/abstract.php');
require_once(INC . 'functions/email_functions.php');

/**
 * A database driver that uses PDO
 */
class DB_PdoMysql extends DB_Abstract
{
	var $driver_name = 'PdoMysql';

	var $_last_errno = null;
	var $_last_error = null;

	/**
	 * Keeps track of querys
	 * @see exec_query
	 */
	var $exec_row_counts = array();

	/**
	 * The PDO connection. Uses 'link_id' because the DB_Abstract
	 * uses it a lot for error testing etc. Easier to just use it.
	 * @var PDO
	 */
	var $link_id;

	function wrapper_connect($host, $user, $password) {
		try {
			$pdo = new PDO('mysql:dbname='.$this->database.';host='.$host, $user, $password, array(
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
			));
			$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			$pdo = false;
		}

		if ($pdo) {
			$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

			try {
				$pdo->exec('SET SESSION sql_mode=""');
			} catch (Exception $e) { }
		}

		return $pdo;
	}

	function wrapper_select_db($dbname) {
		// We have to specify the DB in the connection DSN for PDO...
		return true;
	}

	function wrapper_query($query_string) {

		$query_string = trim($query_string);

		// Need to use exec() for non-select type queries
		if (!preg_match('#^(SELECT|SHOW|REPAIR|OPTIMIZE|DESCRIBE)#', $query_string)) {
			return $this->exec_query($query_string);
		}

		$q = false;
		try {
			$q = $this->link_id->query($query_string);
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			return false;
		}

		if (!$q) {
			$e = $this->link_id->errorInfo();
			$this->_last_errno = $e[1];
			$this->_last_error = $e[2];
			return false;
		}

		return $q;
	}

	function exec_query($query_string)
	{
		// Because exec only returns a row count and not a
		// PDO object, we'll have to store the row counts.
		// We use an MD5 string key and return that as the "query id"
		// and then we can look it up again in wrapper_affected_rows

		$md5 = md5($query_string);

		try {
			$q = $this->link_id->exec($query_string);
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			return false;
		}

		$this->exec_row_counts[$md5] = $q;

		if ($q === false) {
			$e = $this->link_id->errorInfo();
			$this->_last_errno = $e[1];
			$this->_last_error = $e[2];
			return false;
		}

		// If this is an update query, we should take care of invalidating caches
		// for certain things
		global $cache2;
		if ($cache2 AND defined('DP_ENABLE_CACHE') AND DP_ENABLE_CACHE) {
			$match = null;
			if (strpos($query_string, 'DP_NOCLEAN_CACHE') === false AND preg_match('#^\s*(UPDATE|INSERT\s+INTO|DELETE\s+FROM)\s+`?(.*?)`?\s+#', $query_string, $match)) {
				$this->_cacheHandleUpdate($match[2]);
			}
		}

		return $md5;
	}

	protected function _cacheHandleUpdate($table)
	{
		global $cache2;

		// no cache? we cant really do anything then
		if (!$cache2) return;

		static $table_to_cacheid = array(
			'user_company' => array('company_names'),
			'tech' => array('techs'),
			'ticket_def' => array('ticket_def'),
			'ticket_def_cat' => array('ticket_def'),
			'user_def' => array('user_def'),
			'user_company_def' => array('user_company_def'),
			'languages' => array('languages'),
			'ticket_fielddisplay' => array('ticket_fielddisplay'),
			'user_source' => array('user_sources'),
			'ticket_cat' => array('basic_ticket_props'),
			'ticket_workflow' => array('basic_ticket_props'),
			'ticket_pri' => array('basic_ticket_props'),
			'user_groups' => array('basic_user_props'),
			'user_company_role' => array('basic_user_props'),
			'user_idea_categories' => array('useridea_categories'),
		);

		// We dont care about this table if its not in the map
		if (!isset($table_to_cacheid[$table])) {
			return;
		}

		// Otherwise we'll just delete the caches for each
		try {
			foreach ($table_to_cacheid[$table] as $cacheid) {
				$cache2->getCache()->remove($cacheid);
			}
		} catch (Exception $e) {
			trigger_error("Failed to update the $table cache", E_USER_WARNING);
		}
	}

	function wrapper_fetch_array($query_id, $type = DB_RETURN_ASSOC) {
		switch ($type) {
			case DB_RETURN_ASSOC:
				$type = PDO::FETCH_ASSOC;
				break;
			case DB_RETURN_NUM:
				$type = PDO::FETCH_NUM;
				break;
			case DB_RETURN_BOTH:
			default:
				$type = PDO::FETCH_BOTH;
				break;
		}

		if (!is_object($query_id)) {
			if ($this->error_halt) trigger_error('query_id is not a valid result object', E_USER_WARNING);
			return false;
		}

		try {
			return $query_id->fetch($type);
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			return false;
		}
	}

	function wrapper_affected_rows($query_id) {
		if (is_string($query_id)) {
			return isset($this->exec_row_counts[$query_id]) ? $this->exec_row_counts[$query_id] : 0;
		}

		if (!is_object($query_id)) {
			if ($this->error_halt) trigger_error('query_id is not a valid result object', E_USER_WARNING);
			return false;
		}

		try {
			return $query_id->rowCount();
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			return false;
		}
	}

	function wrapper_insert_id() {
		try {
			return $this->link_id->lastInsertId();
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			return false;
		}
	}

	function wrapper_num_rows($query_id) {

		if (!is_object($query_id)) {
			if ($this->error_halt) trigger_error('query_id is not a valid result object', E_USER_WARNING);
			return false;
		}

		try {
			$c = $query_id->rowCount();

			// not all db's report a correct row count (ie newer MySQL)
			// and dont listen to unbuffered attr
			// so for bc we'll just count an array on a cloned query
			if (!$c) {
				$clone = clone $query_id;
				$data = $clone->fetchAll(PDO::FETCH_COLUMN, 0);
				if ($data) {
					$c = count($clone->fetchAll(PDO::FETCH_COLUMN, 0));
				} else {
					$c = 0;
				}
			}

			return $c;
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			return false;
		}
	}

	function wrapper_num_fields($query_id) {

		if (!is_object($query_id)) {
			if ($this->error_halt) trigger_error('query_id is not a valid result object', E_USER_WARNING);
			return false;
		}

		try {
			return $query_id->columnCount();
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			return false;
		}
	}

	function wrapper_free(&$query_id) {
		$query_id = null;
	}

	function wrapper_escape($str) {

		// We need a link_id to call quote on
		$this->connect();

		try {
			$str = $this->link_id->quote($str);
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			return false;
		}

		// PDO adds single quotes around values, but all our code
		// inserts its own. In other words we get stuff like
		//     WHERE xxx = ''myquotedstring''
		// So we'll strip the start/end quotes if they are there
		$len = strlen($str);
		if ($str[0] == "'" AND $str[$len-1] == "'") {
			$str = substr($str, 1, -1);
		}

		// We also dont want to quote '%' and '_' by default, we do that ourselves
		// with another funciton in DB_Abstract
		if (strpos($str, "\\%") !== false OR strpos($str, "\\_") !== false) {
			$str = str_replace(array("\\%", "\\_"), array("%", "_"), $str);
		}

		return $str;
	}

	function wrapper_field_name($offset, $query_id) {

		if (!is_object($query_id)) {
			if ($this->error_halt) trigger_error('query_id is not a valid result object', E_USER_WARNING);
			return false;
		}

		try {
			$info = $query_id->getColumnMeta($offset);
		} catch (Exception $e) {
			$this->_last_errno = $e->getCode();
			$this->_last_error = $e->getMessage();
			return false;
		}

		if ($info AND isset($info['name']) AND $info['name']) {
			return $info['name'];
		}

		return false;
	}

	function wrapper_errno($link_id = null) {
		// Return the last error on the object (ie connection error)
		if (is_null($this->_last_errno) AND $link_id) {
			try {
				$e = $link_id->errorInfo();
				return $r[1];
			} catch (Exception $e) {
				return $e->getCode();
			}
		}

		// Otherwise form last query
		return $this->_last_errno;
	}

	function wrapper_error($link_id = null) {
		// Return the last error on the object (ie connection error)
		if (is_null($this->_last_error) AND $link_id) {
			try {
				$e = $link_id->errorInfo();
				return $r[2];
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}

		return $this->_last_error;
	}

	function wrapper_field_names($table) {
		$values = $this->query_return_array("SHOW COLUMNS FROM `$table`");
		$cols = array();
		foreach ($values as $v) {
			$cols[] = $v['Field'];
		}

		return $cols;
	}

	function wrapper_table_size($table) {
		$tmp = $this->query_return("SHOW TABLE STATUS LIKE '$table'");
		return (int)$tmp['Data_length'];
	}
}
