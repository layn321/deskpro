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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

use Application\DeskPRO\Entity\EmailGateway;
use Application\DeskPRO\Entity\EmailGatewayAddress;
use Orb\Util\Arrays;
use Orb\Util\Strings;

class PopAccountsStep extends AbstractDeskpro3Step
{
	public $default_email_address = null;
	public $email_addresses_map = array();
	public $keep_on_server = false;

	public static function getTitle()
	{
		return 'Import POP3 Accounts';
	}

	public function run($page = 1)
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM gateway_pop_accounts");
		$this->logMessage(sprintf("Importing %d POP3 accounts", $count));
		if (!$count) {
			return;
		}

		$this->keep_on_server = (bool)$this->getOldDb()->fetchColumn("SELECT value FROM settings WHERE name = 'gateway_no_del_msg'");

		#------------------------------
		# Map ticket accounts (email addresses) to their gateway accounts
		#------------------------------

		$this->default_email_address = $this->getOldDb()->fetchColumn("SELECT email FROM gateway_emails WHERE is_default = 1");
		$all_email_addresses = $this->getOldDb()->fetchAllKeyValue("SELECT id, email FROM gateway_emails");

		$all_rules = $this->getOldDb()->fetchAll("SELECT * FROM ticket_rules_mail");
		foreach ($all_rules as $rule) {
			$rule['criteria'] = @unserialize($rule['criteria']);

			if (!is_array($rule['criteria']) || !isset($rule['criteria']['pop']) || !$rule['accountid']) {
				continue;
			}

			$email_address = isset($all_email_addresses[$rule['accountid']]) ? $all_email_addresses[$rule['accountid']] : null;
			if (!$email_address) {
				continue;
			}

			$popid = (int)$rule['criteria']['pop'];
			if (!isset($this->email_addresses_map[$popid])) {
				$this->email_addresses_map[$popid] = array();
			}

			$this->email_addresses_map[$popid][] = $email_address;
		}

		#------------------------------
		# Process accounts
		#------------------------------

		$accounts = $this->getOldDb()->fetchAll("SELECT * FROM gateway_pop_accounts WHERE target = 'user' ORDER BY id ASC");

		$start_time = microtime(true);

		$this->getDb()->beginTransaction();

		try {
			foreach ($accounts as $account) {
				$this->processAccount($account);
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("Done all accounts. Took %.3f seconds.", $end_time-$start_time));
	}

	public function processAccount(array $account)
	{
		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('gateway_account', $account['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$account['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Create it
		#------------------------------

		$new_gateway = new EmailGateway();
		$new_gateway->title = "{$account['server']} :: {$account['username']}";
		$new_gateway->connection_type = EmailGateway::CONN_POP3;
		$new_gateway->connection_options = array(
			'port' => !empty($account['port']) ? $account['port'] : '110',
			'host' => $account['server'],
			'username' => $account['username'],
			'password' => $account['password'],
			'secure' => $account['usessl'] ? true : false
		);
		$new_gateway->gateway_type = 'tickets';
		$new_gateway->is_enabled = false;
		$new_gateway->keep_read  = $this->keep_on_server;

		$this->getEm()->persist($new_gateway);
		$this->getEm()->flush();

		$set_addresses = !empty($this->email_addresses_map[$account['id']]) ? $this->email_addresses_map[$account['id']] : array();
		if (\Orb\Validator\StringEmail::isValueValid($account['username'])) {
			$set_addresses[] = $account['username'];
		}
		if (!$set_addresses) {
			$set_addresses[] = $this->default_email_address;
		}

		array_walk($set_addresses, function (&$v, $k) { $v = strtolower($v); });
		$set_addresses = array_unique($set_addresses, \SORT_STRING);

		foreach ($set_addresses as $addr) {
			$new_address = EmailGatewayAddress::newEmailAddress($new_gateway, $addr);
			$this->getEm()->persist($new_address);
		}

		$this->getEm()->flush();

		$this->saveMappedId('gateway_account', $account['id'], $new_gateway->id);

		#------------------------------
		# Copy the imported transport to this account
		# because v3 uses the same account for everything
		#------------------------------

		$tr = $this->db->fetchAssoc("
			SELECT * FROM email_transports
			WHERE title = 'Imported Transport'
			LIMIT 1
		");

		unset($tr['id']);
		$tr['title'] = 'Transport for Email Account #' . $new_gateway->getId();
		$tr['match_type'] = 'exact';
		$primary_addr = Arrays::getFirstItem($set_addresses);
		$tr['match_pattern'] = $primary_addr;
		$this->db->insert('email_transports', $tr);
		$tr['id'] = $this->db->lastInsertId();

		$this->db->update('email_gateways', array(
			'linked_transport_id' => $tr['id']
		), array('id' => $new_gateway->getId()));

		#------------------------------
		# Copy email ids
		#------------------------------

		if ($this->keep_on_server) {
			$date = date('Y-m-d H:i:s');
			$email_uids = $this->getOldDb()->fetchAllCol("
				SELECT uid
				FROM gateway_email_uid
				WHERE account_id = ?
			", array($account['id']));

			foreach ($email_uids as $uid) {
				$this->getDb()->replace('email_uids', array(
					'id' => $uid,
					'gateway_id' => $new_gateway->id,
					'date_created' => $date
				));
			}
		}
	}
}
