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

class BanningStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Banned Emails and IPs';
	}

	public function run($page = 1)
	{
		$this->getDb()->beginTransaction();

		try {
			$this->importIpBans();
			$this->importEmailBans();
			$this->importEmailGroupBans();

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}


	/**
	 * Imports IP bans from the serialized 'ip_ban' data record.
	 */
	public function importIpBans()
	{
		$this->logMessage("Processing IP bans");
		$ip_bans = $this->getOldDb()->fetchColumn("SELECT data FROM data WHERE name = 'ip_ban'");
		if (!$ip_bans) {
			$this->logMessage("-- None (no data record)");
			return;
		}

		$ip_bans = @unserialize($ip_bans);
		if (!$ip_bans) {
			$this->logMessage("-- None (empty or invalid)");
			return;
		}

		$this->logMessage(sprintf("-- Importing %d bans", count($ip_bans)));

		$start_time = microtime(true);

		foreach ($ip_bans as $ip) {
			$banip = new \Application\DeskPRO\Entity\BanIp();
			$banip->setBannedIp($ip);
			$this->getEm()->persist($banip);
		}
		$this->getEm()->flush();

		$end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $end_time-$start_time));
	}


	/**
	 * Imports email bans from the 'ban_email' table for specific bans.
	 */
	public function importEmailBans()
	{
		$this->logMessage("Processing banned email addresses");
		$email_bans = $this->getOldDb()->fetchAllCol("SELECT email FROM ban_email");

		if (!$email_bans) {
			$this->logMessage("-- None");
			return;
		}

		$this->logMessage(sprintf("-- Importing %d addresses", count($email_bans)));

		$start_time = microtime(true);

		foreach ($email_bans as $email) {
			$this->getDb()->replace('ban_emails', array('banned_email' => $email));
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $end_time-$start_time));
	}


	/**
	 * Imports email bans from the serialized 'email_group' data record for wildcard bans
	 */
	public function importEmailGroupBans()
	{
		$this->logMessage("Processing banned email addresses with wildcards");
		$email_bans = $this->getOldDb()->fetchColumn("SELECT data FROM data WHERE name = 'ip_ban'");
		if (!$email_bans) {
			$this->logMessage("-- None (no data record)");
			return;
		}

		$email_bans = @unserialize($email_bans);
		if (!$email_bans) {
			$this->logMessage("-- None (empty or invalid)");
			return;
		}

		$this->logMessage(sprintf("-- Importing %d bans", count($email_bans)));

		$start_time = microtime(true);

		foreach ($email_bans as $email) {
			$this->getDb()->replace('ban_emails', array('banned_email' => $email));
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $end_time-$start_time));
	}
}
