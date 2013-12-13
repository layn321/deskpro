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

namespace Application\DeskPRO\EmailGateway\Ticket;

use Application\DeskPRO\App;
use Application\DeskPRO\EmailGateway\Reader\AbstractReader;
use Application\DeskPRO\Entity\Ticket;

use Orb\Util\Strings;

/**
 * Detects a ticket based off of REF codes in the subject
 */
class SubjectRefMatchDetector implements TicketDetectorInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $_found_person = null;

	protected $_time_cutoff = 0;

	/**
	 * @param int $time_cutoff Max age of a ticket before the subject match wont work
	 */
	public function __construct($time_cutoff = 604800 /* 7 days */)
	{
		$this->_time_cutoff = date('Y-m-d H:i:s', time()-$time_cutoff);
	}

	/**
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	public function findExistingTicket(AbstractReader $reader)
	{
		$this->_found_person = null;

		$subject = trim($reader->getSubject()->subject);

		$ticket_refs = App::getSystemService('RefGenerator')->extractRefs($subject);
		if (!$ticket_refs) return null;

		foreach ($ticket_refs as $ref) {
			try {
				$ticket = App::getEntityRepository('DeskPRO:Ticket')->findOneByRef($ref);
			} catch (\Exception $e) {
				continue;
			}

			if (!$ticket) {
				continue;
			}

			if (!$ticket->isArchived() && $p = $ticket->findUserByEmail($reader->getFromAddress()->getEmail())) {
				$this->_found_person = $p;
				return $ticket;
			}
		}

		return null;
	}

	/**
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function findExistingPerson(Ticket $ticket, AbstractReader $reader)
	{
		if ($this->_found_person) {
			return $this->_found_person;
		}

		return null;
	}

	/**
	 * Unknown users cant be added based just on subject
	 *
	 * @return void
	 */
	public function canAddUnknownPerson()
	{
		return false;
	}
}
