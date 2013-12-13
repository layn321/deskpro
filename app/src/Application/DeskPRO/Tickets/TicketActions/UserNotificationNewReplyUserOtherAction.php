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

use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\Translate\DelegatePhrase;
use Application\DeskPRO\App;

class UserNotificationNewReplyUserOtherAction extends AbstractUserNotificationAction
{
	protected $skip_person_id;

	public function __construct(TicketChangeTracker $tracker, $skip_person_id)
	{
		$this->tracker = $tracker;
		$this->skip_person_id = $skip_person_id;
	}

	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		if (!count($ticket->getUserParticipants())) {
			return;
		}

		$tpl = $this->getTemplate('user_new_reply_user', 'DeskPRO:emails_user:new-reply-user-other.html.twig');

		$change_info = array(
			'type' => 'user_notify',
			'notify_type' => 'newreply',
			'emailed' => array(),
			'cced' => array()
		);

		$vars = array(
			'action' => 'new_user_reply',
			'skip_person_id' => $this->skip_person_id
		);

		$this->doSend($tpl, $vars, $ticket, $change_info);

		$this->tracker->recordMultiPropertyChanged('log_actions', null, $change_info);
	}

	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		return '';
	}
}
