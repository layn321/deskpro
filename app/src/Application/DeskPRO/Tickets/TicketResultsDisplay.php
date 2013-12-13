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
 * @subpackage Tickets
 */
namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;
use Orb\Util\Arrays;
use Application\DeskPRO\People\PersonContextInterface;

class TicketResultsDisplay implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Ticket[]
	 */
	protected $tickets;

	/**
	 * @var array
	 */
	protected $ticket_ids;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var int
	 */
	protected $ticket_count;

	/**
	 * @var array
	 */
	protected $all_labels;

	/**
	 * @var array
	 */
	protected $all_ticket_slas;

	/**
	 * @var array
	 */
	protected $people;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	/**
	 * @var array
	 */
	protected $person_flagged;

	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}

	/**
	 * @param \Application\DeskPRO\Entity\Ticket[] $tickets
	 */
	public function __construct(array $tickets)
	{
		$this->tickets = $tickets;
		$this->ticket_count = count($tickets);
		$this->ticket_ids = Arrays::flattenToIndex($this->tickets, 'id');

		$this->em = App::getOrm();
		$this->db = $this->em->getConnection();

		$people_ids = array();
		foreach ($tickets as $ticket) {
			$people_ids[] = $ticket->person->getId();
			if ($ticket->agent) {
				$people_ids[] = $ticket->agent->getId();
			}
		}

		$this->dep_names = App::getDataService('Department')->getFullNames();
		$this->people = App::getDataService('Person')->getPeopleResultsFromIds($people_ids);
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->ticket_count;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Ticket[]
	 */
	public function getTickets()
	{
		return $this->tickets;
	}


	/**
	 * @return array
	 */
	public function getAllLabels()
	{
		if ($this->all_labels !== null) return $this->all_labels;

		if (!$this->ticket_count) {
			$this->all_labels = array();
			return $this->all_labels;
		}

		$ticket_ids = implode(',', $this->ticket_ids);

		$this->all_labels = $this->db->fetchAllGrouped("
			SELECT ticket_id, label
			FROM labels_tickets
			WHERE ticket_id IN ($ticket_ids)
		", array(), 'ticket_id', null, 'label');

		return $this->all_labels;
	}


	/**
	 * Get an array of labels applied to a ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return array
	 */
	public function getTicketLabels(Ticket $ticket)
	{
		$this->getAllLabels();
		return empty($this->all_labels[$ticket->id]) ? array() : $this->all_labels[$ticket->id];
	}


	/**
	 * Check if a ticket has labels
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return bool
	 */
	public function hasTicketLabels(Ticket $ticket)
	{
		$this->getAllLabels();
		return !empty($this->all_labels[$ticket->id]);
	}

	/**
	 * @return array
	 */
	public function getAllTicketSlas()
	{
		if ($this->all_ticket_slas !== null) return $this->all_ticket_slas;

		if (!$this->ticket_count) {
			$this->all_ticket_slas = array();
			return $this->all_ticket_slas;
		}

		$ticket_ids = implode(',', $this->ticket_ids);

		$this->all_ticket_slas = $this->db->fetchAllGrouped("
			SELECT ticket_slas.*, slas.title
			FROM ticket_slas
			INNER JOIN slas ON (ticket_slas.sla_id = slas.id)
			WHERE ticket_slas.ticket_id IN ($ticket_ids)
				AND ticket_slas.is_completed = 0
		", array(), 'ticket_id', 'id');

		return $this->all_ticket_slas;
	}


	/**
	 * Get an array of labels applied to a ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return array
	 */
	public function getTicketSlas(Ticket $ticket)
	{
		$this->getAllTicketSlas();
		return empty($this->all_ticket_slas[$ticket->id]) ? array() : $this->all_ticket_slas[$ticket->id];
	}

	/**
	 * Check if a ticket has an SLA
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return bool
	 */
	public function hasTicketSlas(Ticket $ticket)
	{
		$this->getAllTicketSlas();
		return !empty($this->all_ticket_slas[$ticket->id]);
	}

	public function getNextSlaTriggerDate(array $ticket_sla)
	{
		$times = array();

		if ($ticket_sla['sla_status'] == 'ok' && $ticket_sla['warn_date']) {
			$time = new \DateTime($ticket_sla['warn_date'], new \DateTimeZone('UTC'));
			if ($time->getTimestamp() > time() || !$ticket_sla['fail_date']) {
				$times[] = $time->getTimestamp();
			}
		}

		if ($ticket_sla['fail_date']) {
			$time = new \DateTime($ticket_sla['fail_date'], new \DateTimeZone('UTC'));
			$times[] = $time->getTimestamp();
		}

		if (!$times) {
			return null;
		}

		return new \DateTime('@' . min($times));
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getPerson(Ticket $ticket)
	{
		if (!$ticket->person) {
			return null;
		}

		return $this->people[$ticket->person->getId()];
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getAgent(Ticket $ticket)
	{
		if (!$ticket->agent) {
			return null;
		}

		return $this->people[$ticket->agent->getId()];
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return string
	 */
	public function getDepartmentName(Ticket $ticket)
	{
		if (!$ticket->department) {
			return null;
		}

		if (!isset($this->dep_names[$ticket->department->getId()])) {
			return '';
		}

		return $this->dep_names[$ticket->department->getId()];
	}


	/**
	 * @param mixed $ticket
	 * @return string
	 */
	public function getFlaggedColor($ticket)
	{
		if (!$this->person_context) {
			return null;
		}

		if ($this->person_flagged === null) {
			$this->person_flagged = App::getDb()->fetchAllKeyValue("
				SELECT ticket_id, color
				FROM tickets_flagged
				WHERE person_id = ? AND ticket_id IN (" . implode(',',$this->ticket_ids) . ")"
			, array($this->person_context->getId()));
		}

		$ticket_id = is_object($ticket) ? $ticket->getId() : $ticket;

		return isset($this->person_flagged[$ticket_id]) ? $this->person_flagged[$ticket_id] : null;
	}
}
