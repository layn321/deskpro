<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id$
// +-------------------------------------------------------------+
// | File Details:
// | - Upgrade to 3.1.2 gold
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_3010201 extends upgrade_base_v3 {

	var $version = '3.1.2';

	var $version_number = 3010201;

	var $pages = array(
		array('Main Database Changes', 'options.gif'),
		array('Fixed Unlinked Tickets', 'options.gif'),
		array('Add Index', 'options.gif'),
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db;

		$this->start('Add messagehash to temp ticket table');
		$db->query("ALTER TABLE  `ticket_temp` ADD  `messagehash` VARCHAR( 32 ) NOT NULL AFTER  `validate_key`");
		$this->yes();
	}


	/***************************************************
	* Fix any tickets with userid 0 caused by taht bug
	***************************************************/

	function step2($page) {

		global $db;

		$this->start('Fixing unlinked tickets - Page ' . $page);

		$tickets = $db->query_return_array_id("
			SELECT id
			FROM ticket
			WHERE userid = 0
			LIMIT 100
		", 'id', '');

		/********************
		* There are tickets that need updating
		********************/

		if ($tickets) {
			$messages = $db->query_return_array_id("
				SELECT ticketid, userid
				FROM ticket_message
				WHERE
					ticketid IN " . array2sql($tickets) . "
					AND userid != 0
				GROUP BY ticketid
			", 'userid', 'ticketid');

			foreach ($tickets as $ticketid) {

				$userid = $messages[$ticketid];

				// If for some reason we dont have an id,
				// something else is wrong. Set to -1 to prevent
				// DB error here, though.
				if (!$userid) {
					$userid = -1;
				}

				$db->query("
					UPDATE ticket
					SET userid = $userid
					WHERE id = $ticketid
				");
			}

			$this->yes();

			// Just keep going until theres nothing left
			$this->redoStep(2, ++$page);

		/********************
		* No more left
		********************/

		} else {
			$this->yes();
		}
	}


	/***************************************************
	* Add index to ticket_message
	***************************************************/

	function step3() {

		global $db;

		$this->start('Add index on message hash to ticket_message');
		$db->query("ALTER TABLE  `ticket_message` ADD INDEX (  `messagehash` )");
		$this->yes();
	}
}



/***************************************************
* - RUN CLASS
***************************************************/

// check we are in correct location
install_check();

// display header
$header->build();

// create the installer
$upgrade = new upgrade_3010201();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));