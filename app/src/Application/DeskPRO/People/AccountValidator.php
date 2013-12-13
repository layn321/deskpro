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

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;

class AccountValidator
{
	/**
	 * @var \Application\DeskPRO\Entity\PersonEmail
	 */
	protected $email;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $ticket_ids = array();

	public function __construct(Person $person, PersonEmail $email)
	{
		$this->person = $person;
		$this->email  = $email;

		$this->em = App::getOrm();
		$this->db = $this->em->getConnection();
	}

	public function getPerson()
	{
		return $this->person;
	}

	/**
	 * Validate the email address and return the newly created PersonEmail
	 *
	 * @throws \Exception|\OutOfBoundsException
	 * @return \Application\DeskPRO\Entity\PersonEmail
	 */
	public function validate()
	{
		$this->ticket_ids = $this->em->getRepository('DeskPRO:Ticket')->getTicketIdsWithEmail($this->email);

		$this->em->getConnection()->beginTransaction();

		try {
			$this->email->is_validated = true;
			$this->em->flush();

			if (!$this->person->primary_email) {
				$this->person->primary_email = $this->email;
			}

			$is_newly_confirmed = false;
			if (!$this->person->is_confirmed) {
				$is_newly_confirmed = true;
			}

			$this->person->is_confirmed = true;

			$this->db->update('people', array(
				'is_confirmed' => 1,
				'primary_email_id' => $this->email->getId()
			), array('id' => $this->person->getId()));

			// Find tickets with this email awaiting validation
			if ($this->ticket_ids) {
				foreach ($this->ticket_ids as $ticket_id) {
					$ticket = $this->em->find('DeskPRO:Ticket', $ticket_id);

					$ticket->person_email_validating = null;
					$ticket->person_email = $this->email;

					if ($this->person->is_agent_confirmed) {
						$ticket->setStatus('awaiting_agent');
					}

					$ticket->_applySlas();

					$this->em->persist($ticket);
					$this->em->flush();
				}
			}

			if ($is_newly_confirmed) {
				$send_notify = new \Application\DeskPRO\Notifications\NewRegistrationNotification($this->person);
				$send_notify->send();
			}

			$this->em->getConnection()->commit();

			return $this->email;

		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}
	}

	public function getTicketIds()
	{
		return $this->ticket_ids;
	}

	public function getEmail()
	{
		return $this->email;
	}
}
