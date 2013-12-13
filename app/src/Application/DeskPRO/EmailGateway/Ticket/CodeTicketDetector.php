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
use Orb\Log\Logger;
use Orb\Log\Loggable;

/**
 * Detects a ticket based off of codes in the subject or body.
 *
 * We look for (#AAAAA) in either the subject or body.
 * These are access codes that we can use to find a corresponding ticket and user.
 *
 * @see \Application\DeskPRO\Entity\TicketAccessCode
 */
class CodeTicketDetector implements TicketDetectorInterface, Loggable
{
	/**
	 * @var \Application\DeskPRO\Entity\TicketAccessCode
	 */
	protected $_found_tac = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $_found_person = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $_tac_person = null;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var bool
	 */
	protected $is_bounce_mode = false;

	/**
	 * Enable bounce mode if the message is or is suspected ot be a bounced message.
	 * This will look for PTAC/TAC 'headers' in the body text.
	 */
	public function enableBouncedMode()
	{
		$this->is_bounce_mode = true;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	public function findExistingTicket(AbstractReader $reader)
	{
		$this->getLogger()->logDebug("[CodeTicketDetector] Finding ticket");

		$this->_found_person = null;

		$search_text = array();
		$search_text[] = $reader->getSubject()->subject;
		$search_text[] = $reader->getBodyText()->getBody();
		$search_text[] = $reader->getBodyHtml()->getBody();

		$check_headers = array();
		if ($reader->getHeader('In-Reply-To')) {
			foreach ($reader->getHeader('In-Reply-To')->getAllParts() as $part) {
				$check_headers[] = $part;
			}
		}
		if ($reader->getHeader('References')) {
			foreach ($reader->getHeader('References')->getAllParts() as $part) {
				$check_headers[] = $part;
			}
		}

		// If its a bounced message, then the headers might be included in readable-text
		if ($this->is_bounce_mode) {
			$body = $reader->getBodyText()->getBody();
			if (!$body) {
				$body = strip_tags($reader->getBodyHtml()->getBody());
			}

			$m = null;
			if (preg_match_all('#(P?)TAC\-([A-Za-z0-9]+)\.#', $body, $m, \PREG_SET_ORDER)) {
				foreach ($m as $match) {
					if ($match[1]) {
						$this->getLogger()->logDebug("[CodeTicketDetector] Found PTAC in body-headers: " . $match[2]);
					} else {
						$this->getLogger()->logDebug("[CodeTicketDetector] Found TAC in body-headers: " . $match[2]);
					}

					$search_text[] = '(#' . $match[2] . ')';
				}
			}
		}

		// Add them to search text so below code will parse them out and treat them the same
		foreach ($check_headers as $header) {
			$m = null;
			if (preg_match('#(P?)TAC\-([A-Za-z0-9]+)\.#', $header, $m)) {
				if ($m[1]) {
					$this->getLogger()->logDebug("[CodeTicketDetector] Found PTAC in headers: " . $m[2]);
				} else {
					$this->getLogger()->logDebug("[CodeTicketDetector] Found TAC in headers: " . $m[2]);
				}

				$search_text[] = '(#' . $m[2] . ')';
			}
		}

		$search_text = array_unique($search_text);
		$search_text = implode(' ', $search_text);

		$auth_len = App::getSetting('core_tickets.ptac_auth_code_len');
		$authcode_min_len = $auth_len + 1;
		$authcode_max_len = $auth_len + 7;

		#------------------------------
		# PTAC
		#------------------------------

		$already_checked = array();

		$matches = null;
		if (preg_match_all('/\(#([A-Z0-9]{'.$authcode_min_len.','.$authcode_max_len.'})\)/', $search_text, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $m) {

				if (isset($already_checked[$m[1]])) {
					continue;
				}
				$already_checked[$m[1]] = true;

				$this->getLogger()->logDebug("[CodeTicketDetector] Checking code that looks like PTAC: {$m[1]}");

				$ticket = App::getEntityRepository('DeskPRO:Ticket')->getByAccessCode($m[1]);

				if ($ticket && !$ticket->isArchived()) {
					$this->_found_person = $ticket->findUserByEmail($reader->getFromAddress()->email);

					if ($this->_found_person) {
						$this->getLogger()->logDebug("[CodeTicketDetector] -- Matched ticket {$ticket->id} with person {$this->_found_person->id}");
					} else {
						$this->getLogger()->logDebug("[CodeTicketDetector] -- Matched ticket {$ticket->id} with new person");
					}

					return $ticket;
				}

				$this->getLogger()->logDebug("[CodeTicketDetector] -- Invalid code");
			}
		}

		#------------------------------
		# TAC
		#------------------------------

		// Reset the already checked array we build during tac checking,
		// we check the codes again for ptacs now
		$already_checked = array();

		$matches = null;
		if (preg_match_all('/\(#([A-Z0-9]{'.$authcode_min_len.','.$authcode_max_len.'})\)/', $search_text, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $m) {

				if (isset($already_checked[$m[1]])) {
					continue;
				}
				$already_checked[$m[1]] = true;

				$this->getLogger()->logDebug("[CodeTicketDetector] Checking code that looks like TAC: {$m[1]}");

				$tac = App::getEntityRepository('DeskPRO:TicketAccessCode')->getTacArrayFromAccessCode($m[1]);
				if (!$tac) {
					$this->getLogger()->logDebug("[CodeTicketDetector] -- Invalid code");
					continue;
				}

				$this->getLogger()->logDebug("[CodeTicketDetector] -- Valid code");

				$ticket = App::getEntityRepository('DeskPRO:Ticket')->find($tac['ticket_id']);
				if ($ticket && !$ticket->isArchived()) {
					// The person we have from the email address
					$this->_found_person = $ticket->findUserByEmail($reader->getFromAddress()->email);

					if (!$this->_found_person) {
						$this->_found_person = $ticket->findAgentByEmail($reader->getFromAddress()->email);
					}

					// The person who should own this tac
					$this->_tac_person = App::getEntityRepository('DeskPRO:Person')->find($tac['person_id']);

					if ($this->_found_person) {
						$this->getLogger()->logDebug("[CodeTicketDetector] -- Matched ticket {$ticket->id} with person {$this->_found_person->id}");
					} else {
						$this->getLogger()->logDebug("[CodeTicketDetector] -- Matched ticket {$ticket->id} with new person");
					}

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
	 * @return \Application\DeskPRO\Entity\Person|null
	 */
	public function getTacPerson()
	{
		return $this->_tac_person;
	}


	/**
	 * Unknown people are added as CC's. If you know the P/TAC then it's as good as a passowrd.
	 *
	 * @return bool
	 */
	public function canAddUnknownPerson()
	{
		return true;
	}


	/**
	 * Set the logger
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		if (!$this->logger) {
			$this->logger = new Logger();
		}

		return $this->logger;
	}
}
