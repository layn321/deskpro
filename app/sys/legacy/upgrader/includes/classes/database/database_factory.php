<?php

error_reporting(E_ALL & ~E_NOTICE & ~8192);

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
// | - Database factory
// +-------------------------------------------------------------+

/**
 * This file contains the database factory responsible
 * for including the correct database class, and initiating it.
 *
 * @package	DeskPRO
 */

/**
 * Factory for creating database abstraction layer.
 *
 * @access	public
 *
 * @return	DB_Abstract	A database object
 */
function &database_factory($type = 'pdomysql', $forcenew = false) {

	static $object = null;

	if(!$object OR $forcenew) {

		$type = strtolower($type);
		$db = null;

		switch($type) {
			case 'mssql':
				require_once(INC . 'classes/database/mssql.php');
				$db = new DB_MsSQL();
				break;

			case 'mysql':
			case 'mysqli':
			case 'pdomysql':
				require_once(INC . 'classes/database/PdoMysql.php');
				$db = new DB_PdoMysql();
				break;


			default:
				$db = null;
				break;
		}

		if ($forcenew) {
			$db->force_new = true;
			return $db;
		} else {
			$object = $db;
		}
	}

	return $object;
}

function init_doctrine() {
	global $db;
	if ($db) {
		try {
			Doctrine_Manager::connection($db->link_id, 'main');
		} catch (Exception $e) {}
	}
}





/**
 * For backwards compat., some places
 * still call it instead of using $db
 *
 * @return DB_Abstract
 */
function &database_object_factory() {
	return database_factory('pdomysql');
}

