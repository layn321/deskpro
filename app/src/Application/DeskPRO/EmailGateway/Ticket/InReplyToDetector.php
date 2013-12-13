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
 * Detects a ticket based off of In-Reply-To field and the From: must
 * be from a user we know about.
 *
 * @see \Application\DeskPRO\Entity\TicketAccessCode
 */
class InReplyToDetector implements TicketDetectorInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\TicketAccessCode
	 */
	protected $_found_person = null;

	/**
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	public function findExistingTicket(AbstractReader $reader)
	{
		$this->_found_person = null;

		#------------------------------
		# Fetch message Ids from headers
		#------------------------------

		$search_text = array();

		// In-Reply-To should have the direct message
		// being replied to
		$in_reply_to = $reader->getHeader('In-Reply-To');
		if ($in_reply_to) {
			foreach ($in_reply_to->getAllParts() as $part) {
				$search_text[] = $part;
			}
		}

		// References may have other messages in a thread,
		// so also a good place to look for the TAC
		$references = $reader->getHeader('References');
		if ($references) {
			foreach ($references->getAllParts() as $part) {
				$search_text[] = $part;
			}
		}

		$search_text = implode(' ', $search_text);

		#------------------------------
		# Try to find TAC
		#------------------------------

		$auth_len = App::getSetting('core_tickets.ptac_auth_code_len');
		$authcode_min_len = $auth_len + 1;
		$authcode_max_len = $auth_len + 7;

		$matches = null;
		if (preg_match_all('#(?<!P)TAC\-([A-Z0-9]{'.$authcode_min_len.','.$authcode_max_len.'})\.#i', $search_text, $matches, PREG_SET_ORDER)) {

			foreach ($matches as $m) {
				$tac = App::getEntityRepository('DeskPRO:TicketAccessCode')->findByAccessCode($m[1]);
				if (!$tac) continue;

				$ticket = $tac->ticket;
				if (!$ticket->isArchived()) {
					$this->_found_person = $tac->person;
					return $ticket;
				}

			}
		}

		#------------------------------
		# Try to find PTAC
		#------------------------------

		$matches = null;
		if (preg_match_all('#PTAC\-([A-Z0-9]{'.$authcode_min_len.','.$authcode_max_len.'})\.#i', $search_text, $matches, PREG_SET_ORDER)) {

			foreach ($matches as $m) {
				$ticket = App::getEntityRepository('DeskPRO:Ticket')->getByAccessCode($m[1]);

				if ($ticket && !$ticket->isArchived()) {

					$this->_found_person = $ticket->findUserByEmail($reader->getFromAddress()->email);

					return $ticket;
				}
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
	 * Add unknown users, the reply code in the address is the PTAC
	 * so basically a passowrd
	 *
	 * @return void
	 */
	public function canAddUnknownPerson()
	{
		return true;
	}
}
