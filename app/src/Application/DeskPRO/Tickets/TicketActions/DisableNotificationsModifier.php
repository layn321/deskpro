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

class DisableNotificationsModifier implements CollectionModifierInterface
{
	public function __construct()
	{

	}

	public function modifyCollection(ActionsCollection $collection)
	{
		$notify_types = array();
		$notify_types[] = 'AgentNotification';
		$notify_types[] = 'AgentAlertNotification';
		$notify_types[] = 'UserNotificationNewReply';
		$notify_types[] = 'UserNotificationNewReplyUser';
		$notify_types[] = 'UserNotificationNewReplyUserOther';
		$notify_types[] = 'UserNotificationNewReplyAgent';

		foreach ($notify_types as $type) {
			if ($collection->hasActionType($type)) {
				$collection->removeActionType($type);
			}
		}

		if ($collection->hasActionType('NewTicket')) {
			$collection->getActionType('NewTicket')->disableNotifications();
		}
	}

	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		$tr = App::getTranslator();
		return $tr->phrase('agent.tickets.disable_all_notifs_action');
	}
}
