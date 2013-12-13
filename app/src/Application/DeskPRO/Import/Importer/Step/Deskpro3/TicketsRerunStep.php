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

use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\TicketAttachment;
use Application\DeskPRO\Entity\TicketParticipant;
use Application\DeskPRO\Import\Importer\Step\Deskpro3\Ticket\ImportTicket;

class TicketsRerunStep extends AbstractDeskpro3Step
{
	public $on_rerun = true;
	public $on_run = false;

	const PERPAGE = 1000;

	/**
	 * @var array
	 */
	public $thing_titles = array();

	/**
	 * @var array
	 */
	public $custom_field_info = array();

	/**
	 * @var \Application\DeskPRO\CustomFields\FieldManager
	 */
	public $fieldmanager;

	/**
	 * @var array
	 */
	public $personinfo_cache = array();

	/**
	 * @var int
	 */
	public $ticket_ids;

	/**
	 * @var array
	 */
	public $gateway_addresses = array();

	/**
	 * @var array
	 */
	public $old_gateway_addresses = array();

	/**
	 * @var array
	 */
	public $user_custom_field_info = array();

	/**
	 * @var \Application\DeskPRO\CustomFields\FieldManager
	 */
	public $user_fieldmanager;

	/**
	 * @var \Application\DeskPRO\Entity\Usersource[]
	 */
	public $usersources;

	public static function getTitle()
	{
		return 'Import Tickets (ReRun)';
	}

	public function getTicketIds()
	{
		if ($this->ticket_ids !== null) {
			return $this->ticket_ids;
		}

		$this->ticket_ids = $this->db->fetchColumn("
			SELECT data
			FROM import_datastore
			WHERE typename = 'dp3_tickets_rerun_ids'
		");

		if ($this->ticket_ids) {
			$this->ticket_ids = @unserialize($this->ticket_ids);
		}

		if (!$this->ticket_ids) {
			$this->ticket_ids = array();
		}

		return $this->ticket_ids;
	}

	public function countPages()
	{
		$count = count($this->getTicketIds());
		return ceil($count / self::PERPAGE);
	}

	public function run($page = 1)
	{
		$this->custom_field_info = $this->olddb->fetchAll("SELECT * FROM ticket_def");
		$this->fieldmanager = $this->getContainer()->getSystemService('ticket_fields_manager');

		$this->user_custom_field_info = $this->olddb->fetchAll("SELECT * FROM user_def");
		$this->user_fieldmanager = $this->getContainer()->getSystemService('person_fields_manager');
		$this->user_fieldmanager->getFields();
		$this->usersources = $this->getEm()->getRepository('DeskPRO:Usersource')->getAllUsersources(false);

		$this->old_gateway_addresses = $this->olddb->fetchAllKeyed("
			SELECT *
			FROM gateway_emails
		");

		$this->gateway_addresses = $this->db->fetchAllKeyed("
			SELECT *
			FROM email_gateway_addresses
			WHERE match_type = 'exact'
		");

		$sub_start_time = microtime(true);
		$this->logMessage("-- Processing batch {$page}");

		$batch = $this->getBatch($page);

		try {
			$this->db->beginTransaction();
			foreach ($batch as $tinfo) {
				$this->processTicketInfo($tinfo);
			}

			$this->importer->flushSaveMappedIdBuffer();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}


	/**
	 * Process a single ticket
	 * @param $ticket_id
	 */
	public function processTicketInfo($all_ticket_info)
	{
		$import_ticket = new ImportTicket();
		$import_ticket->importer = $this->importer;
		$import_ticket->step     = $this;

		$import_ticket->importOrUpdateTicket($all_ticket_info);
	}

	public function getThingTitle($thing, $id)
	{
		switch ($thing) {
			case 'department':
				if (!isset($this->thing_titles[$thing])) {
					$this->thing_titles[$thing] = $this->db->fetchAllKeyValue("SELECT id, title FROM departments");
				}
				return isset($this->thing_titles[$thing][$id]) ? $this->thing_titles[$thing][$id] : '';
			case 'ticket_category':
				if (!isset($this->thing_titles[$thing])) {
					$this->thing_titles[$thing] = $this->db->fetchAllKeyValue("SELECT id, title FROM ticket_categories");
				}
				return isset($this->thing_titles[$thing][$id]) ? $this->thing_titles[$thing][$id] : '';
			case 'ticket_workflow':
				if (!isset($this->thing_titles[$thing])) {
					$this->thing_titles[$thing] = $this->db->fetchAllKeyValue("SELECT id, title FROM ticket_workflows");
				}
				return isset($this->thing_titles[$thing][$id]) ? $this->thing_titles[$thing][$id] : '';
			case 'ticket_priority':
				if (!isset($this->thing_titles[$thing])) {
					$this->thing_titles[$thing] = $this->db->fetchAllKeyValue("SELECT id, title FROM ticket_priorities");
				}
				return isset($this->thing_titles[$thing][$id]) ? $this->thing_titles[$thing][$id] : '';
			case 'ticket_priority_pri':
				if (!isset($this->thing_titles[$thing])) {
					$this->thing_titles['ticket_priority_pri'] = $this->db->fetchAllKeyValue("SELECT id, priority FROM ticket_priorities");
				}
				return isset($this->thing_titles[$thing][$id]) ? $this->thing_titles[$thing][$id] : '';
		}

		return '';
	}

	/**
	 * @param $page
	 * @return array
	 */
	public function getBatch($page)
	{
		$counts = array(
			'ticket' => 0,
			'ticket_message' => 0,
			'ticket_logs' => 0,
		);

		$ticket_ids_batched = array_chunk($this->getTicketIds(), self::PERPAGE, false);

		if (!isset($ticket_ids_batched[$page-1])) {
			return array();
		}

		$ticket_ids_page = $ticket_ids_batched[$page-1];
		$ticket_ids_page = implode(',', $ticket_ids_page);

		$between_where = " IN($ticket_ids_page) ";

		#------------------------------
		# Fetch ticket
		#------------------------------

		$q = $this->olddb->query("SELECT * FROM ticket WHERE id $between_where");
		$q->execute();

		$batch = array();

		while ($ticket = $q->fetch(\PDO::FETCH_ASSOC)) {
			$batch[$ticket['id']] = array(
				'ticket' => $ticket,
				'ticket_notes' => array(),
				'ticket_message' => array(),
				'ticket_attachments' => array(),
				'ticket_participant' => array(),
				'tickets_logs' => array(),
				'tech_ticket_watch' => array()
			);
			$counts['ticket']++;
		}
		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch ticket_notes
		#------------------------------

		$q = $this->olddb->query("SELECT * FROM ticket_notes WHERE ticketid $between_where ORDER BY id ASC");
		$q->execute();

		while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch[$r['ticketid']])) continue;
			$batch[$r['ticketid']]['ticket_notes'][] = $r;
		}
		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch ticket_message
		#------------------------------

		$q = $this->olddb->query("SELECT * FROM ticket_message WHERE ticketid $between_where ORDER BY id ASC");
		$q->execute();

		while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch[$r['ticketid']])) continue;
			$batch[$r['ticketid']]['ticket_message'][] = $r;
			$counts['ticket_message']++;
		}
		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch ticket_attachments
		#------------------------------

		if (!dp_get_config('import.dev_ignore_attachments')) {
			$q = $this->olddb->query("SELECT * FROM ticket_attachments WHERE ticketid $between_where");
			$q->execute();

			while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
				if (!isset($batch[$r['ticketid']])) continue;
				$batch[$r['ticketid']]['ticket_attachments'][] = $r;
			}
			$q->closeCursor();
			unset($q);
		}

		#------------------------------
		# Fetch ticket_participant
		#------------------------------

		$q = $this->olddb->query("SELECT * FROM ticket_participant WHERE ticket $between_where");
		$q->execute();

		while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch[$r['ticket']])) continue;
			$batch[$r['ticket']]['ticket_participant'][] = $r;
		}
		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch tech_ticket_watch
		#------------------------------

		$q = $this->olddb->query("SELECT * FROM tech_ticket_watch WHERE ticketid $between_where");
		$q->execute();

		while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch[$r['ticketid']])) continue;
			$batch[$r['ticketid']]['tech_ticket_watch'][] = $r;
		}
		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch tickets_logs
		#------------------------------

		$q = $this->olddb->query("SELECT * FROM ticket_log WHERE ticketid $between_where ORDER BY id ASC");
		$q->execute();

		while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch[$r['ticketid']])) continue;
			$batch[$r['ticketid']]['tickets_logs'][] = $r;
			$counts['ticket_logs']++;
		}
		$q->closeCursor();
		unset($q);

		$this->logMessage(sprintf("-- Tickets: %d, Messages: %s, Logs: %s", $counts['ticket'], $counts['ticket_message'], $counts['ticket_logs']));

		return $batch;
	}

	public function getPersonInfo($new_id)
	{
		if (isset($this->personinfo_cache[$new_id])) {
			return $this->personinfo_cache[$new_id];
		}

		$info = $this->getDb()->fetchAssoc("
			SELECT p.id, p.first_name, p.last_name, p.name, pe.email AS primary_email_address
			FROM people p
			LEFT JOIN people_emails AS pe ON (pe.id = p.primary_email_id)
			WHERE p.id = ?
		", array($new_id));

		if ($info['first_name'] AND $info['last_name']) {
			$info['display_name'] = $info['first_name'] . ' ' . $info['last_name'];
		} elseif ($info['name']) {
			$info['display_name'] = $info['name'];
		} elseif ($info['last_name']) {
			$info['display_name'] = $info['last_name'];
		} elseif ($info['first_name']) {
			$info['display_name'] = $info['first_name'];
		} elseif ($info['primary_email_address']) {

			// try to get a nice name from the email address
			$email = $info['primary_email_address'];
			list ($name,) = explode('@', $email, 2);

			$name = str_replace('_', ' ', $name);
			$name = str_replace('.', ' ', $name);
			$name = preg_replace('#[ ]{2,}#', ' ', $name); //consec spaces to single space

			$info['display_name'] = ucfirst($name);
		} else {
			$info['display_name'] = 'ID-' . $info['id'];
		}

		$this->personinfo_cache[$new_id] = $info;

		return $this->personinfo_cache[$new_id];
	}
}