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

namespace Application\DeskPRO\Tickets\TicketActions;

use Application\DeskPRO\App;

use Application\DeskPRO\EmailGateway\PersonFromEmailProcessor;
use Application\DeskPRO\EmailGateway\Reader\Item\EmailAddress;
use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;
use Orb\Util\Arrays;
use Orb\Validator\StringEmail;

/**
 * Adds participants
 */
class AddCcAction extends AbstractAction
{
	/**
	 * @var string[]
	 */
	protected $add_emails;

	/**
	 * @var \Application\DeskPRO\Entity\Person[]
	 */
	protected $add_people;

	public function __construct($add_emails)
	{
		if (!is_array($add_emails)) {
			$add_emails = explode(',', $add_emails);
			$add_emails = Arrays::func($add_emails, 'trim');
		}

		$valid = array();
		foreach ($add_emails as $email) {
			if (StringEmail::isValueValid($email)) {
				$valid[] = $email;
			}
		}

		$valid = array_unique($valid);

		$this->add_emails = $valid;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Person[]
	 */
	public function getPeople()
	{
		if ($this->add_people) {
			return $this->add_people;
		}

		$this->add_people = array();

		foreach ($this->add_emails as $email) {
			$person = App::getEntityRepository('DeskPRO:Person')->findOneByEmail($email);
			if ($person) {
				$this->add_people[$person->getId()] = $person;
			} else {
				if (App::getContainer()->getSetting('core.user_mode') == 'closed') {
					continue;
				}
				$person_processor = new PersonFromEmailProcessor();

				$eml = new EmailAddress();
				$eml->email = $email;
				$person = $person_processor->createPerson($eml, false);

				if ($person) {
					$this->add_people[$person->getId()] = $person;
				}
			}
		}

		return $this->add_people;
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$people = $this->getPeople();
		foreach ($people as $person) {
			$ticket->addParticipantPerson($person);
		}
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		$actions = array();

		foreach ($this->getPeople() as $pid => $person) {
			$actions[] = array(
				'action' => 'add_participant',
				'person_id' => $pid
			);
		}

		return $actions;
	}


	/**
	 * @return string[]
	 */
	public function getEmailAddresses()
	{
		return $this->add_emails;
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		$email_addresses = $this->getEmailAddresses();
		$email_addresses = array_merge($email_addresses, $other_action->getEmailAddresses());
		$email_addresses = array_unique($email_addresses);

		$new = new self($email_addresses);

		return $new;
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		if (!$this->add_emails) {
			return '';
		}

		return "CC users: " . implode(', ', $this->add_emails);
	}
}
