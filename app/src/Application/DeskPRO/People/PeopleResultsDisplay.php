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
 * @subpackage People
 */
namespace Application\DeskPRO\People;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Orb\Util\Arrays;

class PeopleResultsDisplay
{
	/**
	 * @var \Application\DeskPRO\Entity\Person[]
	 */
	protected $people;

	/**
	 * @var array
	 */
	protected $people_ids;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Application\DeskPRO\CustomFields\FieldManager
	 */
	protected $field_manager;

	/**
	 * @var int
	 */
	protected $people_count;

	/**
	 * @var array
	 */
	protected $all_labels;

	/**
	 * @var array
	 */
	protected $people_ticket_counts;

	/**
	 * @var array
	 */
	protected $primary_emails;

	/**
	 * @var array
	 */
	protected $people_fields;

	/**
	 * @var array
	 */
	protected $people_usernames;

	/**
	 * @param \Application\DeskPRO\Entity\People[] $people
	 */
	public function __construct(array $people)
	{
		$this->people = $people;
		$this->people_count = count($people);
		$this->people_ids = array();
		foreach ($this->people as $p) {
			$this->people_ids[] = $p->id;
		}

		$this->em = App::getOrm();
		$this->db = $this->em->getConnection();
		$this->field_manager = App::getSystemService('person_fields_manager');
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->people_count;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Person[]
	 */
	public function getPeople()
	{
		return $this->people;
	}


	/**
	 * @return array
	 */
	public function getAllLabels()
	{
		if ($this->all_labels !== null) return $this->all_labels;

		if (!$this->people_count) {
			$this->all_labels = array();
			return $this->all_labels;
		}

		$people_ids = implode(',', $this->people_ids);

		$this->all_labels = $this->db->fetchAllGrouped("
			SELECT person_id, label
			FROM labels_people
			WHERE person_id IN ($people_ids)
		", array(), 'person_id', null, 'label');

		return $this->all_labels;
	}

	/**
	 * @return array
	 */
	public function getAllUsernames()
	{
		if ($this->people_usernames !== null) return $this->people_usernames;

		if (!$this->people_count) {
			$this->people_usernames = array();
			return $this->people_usernames;
		}

		$people_ids = implode(',', $this->people_ids);

		$this->people_usernames = $this->db->fetchAllGrouped("
			SELECT person_id, identity_friendly
			FROM person_usersource_assoc
			WHERE person_id IN ($people_ids) AND identity_friendly != ''
		", array(), 'person_id', null, 'identity_friendly');

		return $this->people_usernames;
	}

	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function getEmail(Person $person)
	{
		if (!$person->primary_email) {
			return null;
		}

		if ($this->primary_emails === null) {
			$primary_email_ids = array();
			foreach ($this->people as $p) {
				if ($p->primary_email) {
					$primary_email_ids[] = $p->primary_email->getId();
				}
			}

			$this->primary_emails = $this->em->getRepository('DeskPRO:PersonEmail')->getByIds($primary_email_ids);
		}

		return $this->primary_emails[$person->primary_email->getId()];
	}

	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function getCustomFields(Person $person)
	{
		if ($this->people_fields === null) {
			$field_data = $this->em->createQuery("
				SELECT cp FROM DeskPRO:CustomDataPerson cp
				WHERE cp.person IN (?0)
			")->execute(array($this->people_ids));

			$person_data = array();

			foreach ($field_data as $data) {
				$pid = $data->person->getId();
				if (!isset($person_data[$pid])) {
					$person_data[$pid] = array();
				}

				$person_data[$pid][] = $data;
			}

			$this->people_fields = array();
			foreach ($person_data as $pid => $custom_data) {
				$this->people_fields[$pid] = $this->field_manager->getDisplayArray($this->field_manager->createFieldDataFromArray($custom_data), null);
			}
		}

		if (isset($this->people_fields[$person->id])) {
			return $this->people_fields[$person->id];
		} else {
			return array();
		}
	}


	/**
	 * Get an array of labels applied to a person
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return array
	 */
	public function getPersonLabels(Person $person)
	{
		$this->getAllLabels();
		return empty($this->all_labels[$person->id]) ? array() : $this->all_labels[$person->id];
	}


	/**
	 * Get an array of usernames from usersources applied to a person
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return array
	 */
	public function getPersonUsernames(Person $person)
	{
		$this->getAllUsernames();
		return empty($this->people_usernames[$person->id]) ? array() : $this->people_usernames[$person->id];
	}


	/**
	 * Check if a person has labels
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return bool
	 */
	public function hasPersonLabels(Person $person)
	{
		$this->getAllLabels();
		return !empty($this->all_labels[$person->id]);
	}


	/**
	 * @return array
	 */
	public function getAllPeopleTicketCounts()
	{
		if ($this->people_ticket_counts !== null) return $this->people_ticket_counts;

		$this->people_ticket_counts = $this->em->getRepository('DeskPRO:Ticket')->getTicketCountsForPeople($this->people);

		return $this->people_ticket_counts;
	}


	/**
	 * Get the number of tickets submitted by a user.
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return int
	 */
	public function getPersonTicketCount(Person $person)
	{
		$this->getAllPeopleTicketCounts();
		return isset($this->people_ticket_counts[$person->id]) ? $this->people_ticket_counts[$person->id] : 0;
	}
}
