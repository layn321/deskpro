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
 * Detects a ticket based off of the code in the TO address that
 * the email was sent to.
 *
 * This is used with a catch-all email address. When a user replies to a notification,
 * we automatically detect the ticket based on the address: ticket-ABIEUJF@example.com
 *
 * @see \Application\DeskPRO\Entity\TicketAccessCode
 */
class ToEmailTicketDetector implements TicketDetectorInterface
{
	/**
	 * The regex to match
	 * @var string
	 */
	protected $account_pattern;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $_found_person = null;

	/**
	 * $account_pattern needs to be an email address with the special token TICKET_CODE
	 * in it to denote the position of the ticket code.
	 *
	 * For example:
	 * <code>
	 * $detector = new ToEmailTicketDetector('ticket-TAC@example.com');
	 * </code>
	 *
	 * @param string The pattern with the special token TICKET_CODE in it.
	 */
	public function __construct($account_pattern)
	{
		$account_pattern = preg_quote($account_pattern, '#');

		$auth_len = App::getSetting('core_tickets.ptac_auth_code_len');
		$authcode_min_len = $auth_len + 1;
		$authcode_max_len = $auth_len + 7;

		$account_pattern = str_replace('TAC', '(?P<auth>[A-Z0-9]{'.$authcode_min_len.','.$authcode_max_len.'})', $account_pattern);

		$this->account_pattern = '#^' . $account_pattern . '#$';
	}


	/**
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	public function findExistingTicket(AbstractReader $reader)
	{
		$search_addr = array();
		foreach ($reader->getToAddresses() as $addr) {
			$search_addr[] = $addr->email;
		}
		foreach ($reader->getCcAddresses() as $addr) {
			$search_addr[] = $addr->email;
		}

		// Easier to run regex on all at once
		$search_addr = ' ' . implode(' ', $search_addr) . ' ';

		$match_ptac = Strings::extractRegexMatch($this->account_pattern, $search_addr, 'auth');
		if (!$match_ptac) return null;

		#------------------------------
		# Try to find the ticket and user now
		#------------------------------

		$ticket = App::getEntityRepository('DeskPRO:Ticket')->getByAccessCode($match_ptac);

		if ($ticket && !$ticket->isArchived()) {

			$this->_found_person = $ticket->findUserByEmail($reader->getFromAddress()->email);

			return $ticket;
		}

		return null;
	}

	/**
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function findExistingPerson(Ticket $ticket, AbstractReader $reader)
	{
		if ($this->_found_tac) {
			return $this->_found_tac->person;
		}

		return null;
	}

	public function canAddUnknownPerson()
	{
		return true;
	}
}
