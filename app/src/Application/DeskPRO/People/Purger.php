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

namespace Application\DeskPRO\People;

use Doctrine\ORM\EntityManager;
use Application\DeskPRO\Entity\Person;

class Purger implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * Who is performing the delete
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	public function __construct(Person $person, EntityManager $em)
	{
		$this->person = $person;
		$this->em     = $em;
		$this->db     = $em->getConnection();
	}


	/**
	 * @return void
	 */
	public function purge()
	{
		$this->db->beginTransaction();
		try {
			$this->purgeTickets();

			$this->db->delete('people', array('id' => $this->person->getId()));
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}
	}


	/**
	 * Purge all the tickets belonging to a user
	 *
	 * @return void
	 */
	public function purgeTickets()
	{
		$ticket_ids = $this->db->fetchAllCol("
			SELECT id FROM tickets WHERE person_id = ? AND hidden_status != 'deleted'
		", array($this->person->getId()));

		if (!$ticket_ids) {
			return;
		}

		#------------------------------
		# Insert delete logs
		#------------------------------

		$by_person_id = null;
		if ($this->person_context) {
			$by_person_id = $this->person_context->getId();
		}

		$date_str   = date('Y-m-d H:i:s');
		$reason_str =  'User was deleted';

		$inserts = array();

		foreach ($ticket_ids as $ticket_id) {
			$inserts[] = array('ticket_id' => $ticket_id, 'by_person_id' => $by_person_id, 'new_ticket_id' => 0, 'date_created' => $date_str, 'reason' => $reason_str);
		}

		$this->db->batchInsert('tickets_deleted', $inserts);

		#------------------------------
		# Clear out the search tables
		#------------------------------

		$this->db->delete('tickets_search_active', array('person_id' => $this->person->getId()));
		$this->db->delete('tickets_search_message', array('person_id' => $this->person->getId()));
		$this->db->delete('tickets_search_message_active', array('person_id' => $this->person->getId()));
		$this->db->executeUpdate("DELETE FROM tickets_search_subject WHERE id IN (" . implode(',', $ticket_ids) . ")");
	}


	/**
	 * Set the context (who is making these edits)
	 *
	 * @param Person $person
	 */
	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getPersonContext()
	{
		return $this->person_context;
	}
}