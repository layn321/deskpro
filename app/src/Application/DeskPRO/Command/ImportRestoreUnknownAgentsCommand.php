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
*/

namespace Application\DeskPRO\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Application\DeskPRO\DBAL\Connection;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Log\Logger;

use Orb\Util\Util;
use Orb\Util\Numbers;
use Orb\Util\Strings;

class ImportRestoreUnknownAgentsCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	/**
	 * @var \Application\DeskPRO\Log\Logger
	 */
	protected $logger;

	/**
	 * @var float
	 */
	protected $cmd_start_time;

	protected $total_time;

	protected function configure()
	{
		$this->setName('dp:import-restore-unknown-agents');
		$this->addOption('run', null, InputOption::VALUE_NONE, 'Run the tool. Without this switch, just info is displayed');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		global $DP_CONFIG;

		$time_start = microtime(true);

		#----------------------------------------
		# Show info
		#----------------------------------------

		/** @var $old_db \Application\DeskPRO\DBAL\Connection */
		$old_db = $this->getContainer()->get('doctrine.dbal.connection_factory')->createConnection(array(
			'driver'        => 'pdo_mysql',
			'host'          => $DP_CONFIG['import']['db_host'],
			'user'          => $DP_CONFIG['import']['db_user'],
			'password'      => $DP_CONFIG['import']['db_password'],
			'dbname'        => $DP_CONFIG['import']['db_name'],
			'names_charset' => 'latin1'
		));
		$old_db->connect();

		// Find messages by techs that dont exist
		$have_tech_ids = $old_db->fetchAllCol("
			SELECT id
			FROM tech
		");

		$missing_tech_ids = $old_db->fetchAllCol("
			SELECT DISTINCT(techid)
			FROM ticket_message
			WHERE techid NOT IN (" . implode(',', $have_tech_ids) . ") AND techid != 0
		");

		if (!$missing_tech_ids) {
			$output->writeln("No missing tech IDs were found");
			return 0;
		}

		$count_messages = $old_db->fetchColumn("
			SELECT COUNT(*)
			FROM ticket_message
			WHERE techid IN (" . implode(',', $missing_tech_ids) . ")
		");

		$output->writeln(sprintf("Found %d missing tech IDs that wrote a total of %d messages", count($missing_tech_ids), $count_messages));

		if (!$input->getOption('run')) {
			$output->writeln("Use the --run switch with this command to restore these missing agents as unknown deleted agents and then restore their messages.");
			return 0;
		}

		#----------------------------------------
		# Run the changes
		#----------------------------------------

		$email_domain = php_uname('n');
		if (!$email_domain) {
			$email_domain = 'deskpro-dummy.example.com';
		}

		$site_url = @parse_url($this->getContainer()->getDb()->fetchColumn("SELECT value FROM settings WHERE name = 'core.deskpro_url'"));
		if ($site_url && !empty($site_url['host'])) {
			$email_domain = $site_url['host'];
		}

		foreach ($missing_tech_ids as $tech_id) {

			$set_email = "deleted-agent-$tech_id@$email_domain";
			$agent = $this->getContainer()->getEm()->getRepository('DeskPRO:Person')->findOneByEmail($set_email);

			if ($agent) {
				$output->writeln("<error>Tech #$tech_id already processed into agent $set_email -- Skipping</error>");
				continue;
			}

			$agent = new \Application\DeskPRO\Entity\Person();
			$agent->setEmail($set_email, true);
			$agent->setPassword(uniqid('', true) . mt_rand(1000,9999));
			$agent->is_agent    = true;
			$agent->salt        = 'xxx';
			$agent->can_agent   = true;
			$agent->can_admin   = false;
			$agent->can_billing = false;
			$agent->can_reports = false;
			$agent->is_deleted  = true;
			$agent->first_name  = "Deleted";
			$agent->last_name   = "Deleted";

			$this->getContainer()->getDb()->beginTransaction();

			try {
				$this->getContainer()->getEm()->persist($agent);
				$this->getContainer()->getEm()->flush();

				$ids = $old_db->fetchAllCol("SELECT id FROM ticket_message WHERE techid = ?", array($tech_id));
				$output->writeln(sprintf("<info>Tech #%d processed into agent #%d %s :: %d messages to insert</info>", $tech_id, $agent->getId(), $set_email, count($ids)));

				$batch_ids = array_chunk($ids, 1000);
				while ($batch = array_shift($batch_ids)) {
					$all_message_info = $old_db->fetchAll("
						SELECT *
						FROM ticket_message
						WHERE id IN (" . implode(',', $batch) . ")
					");

					$check_ticket_ids = array();
					foreach ($all_message_info as $message_info) {
						$check_ticket_ids[$message_info['ticketid']] = $message_info['ticketid'];
					}

					$has_ticket_ids = $old_db->fetchAllKeyValue("
						SELECT id
						FROM ticket
						WHERE id IN (" . implode(',', $check_ticket_ids) . ")
					", array(), 0, 0);

					foreach ($all_message_info as $message_info) {

						if (!isset($has_ticket_ids[$message_info['ticketid']])) {
							continue;
						}

						$insert_message = array();
						$insert_message['message_hash'] = sha1(microtime(true) . mt_rand(1000,99999)); // bogus hash
						$insert_message['message'] = $message_info['message'];
						$insert_message['person_id'] = $agent->getId();
						$insert_message['ticket_id'] = $message_info['ticketid'];
						$insert_message['creation_system'] = 'web';
						$insert_message['date_created'] = date('Y-m-d H:i:s', $message_info['timestamp']);
						$insert_message['ip_address'] = $message_info['ipaddress'];

						if ($message_info['charset'] && strtoupper($message_info['charset']) != 'UTF-8') {
							$new_msg = \Orb\Util\Strings::convertToUtf8($message_info['message'], $message_info['charset']);
							if ($new_msg) {
								$message_info['message'] = $new_msg;
							}
						}

						$insert_message['message'] = \Orb\Util\Strings::htmlEntityDecodeUtf8($insert_message['message']);
						$insert_message['message'] = trim(\Orb\Util\Strings::utf8_bad_strip($insert_message['message']));
						$insert_message['message'] = nl2br(htmlspecialchars($insert_message['message'], \ENT_QUOTES, 'UTF-8'));

						$this->getContainer()->getDb()->insert('tickets_messages', $insert_message);
					}
				}

				$this->getContainer()->getDb()->commit();
			} catch (\Exception $e) {
				$this->getContainer()->getDb()->rollback();
				throw $e;
			}
		}

		$time_end = microtime(true);

		$output->writeln(sprintf("<info>All done in %.2f seconds</info>", $time_end-$time_start));

		return 0;
	}
}