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

class SetEmailTemplateModifier implements CollectionModifierInterface
{
	protected $tpl;
	protected $tpl_type = '';

	public function __construct($tpl, $tpl_type)
	{
		$this->tpl  = $tpl;
		$this->tpl_type = $tpl_type;
	}

	public function modifyCollection(ActionsCollection $collection)
	{
		$notify_types = array();
		$notify_types[] = 'NewTicket';
		$notify_types[] = 'UserNotificationNewReply';
		$notify_types[] = 'UserNotificationNewAgent';
		$notify_types[] = 'AgentNotification';

		foreach ($notify_types as $type) {
			if ($collection->hasActionType($type)) {
				$action = $collection->getActionType($type);
				$action->setEmailTemplate($this->tpl, $this->tpl_type);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		$tr = App::getTranslator();

		$tpl = $this->tpl;
		$tpl = str_replace('DeskPRO:custom_emails_user:', '', $tpl);
		$tpl = str_replace('DeskPRO:custom_emails_agent:', '', $tpl);
		$tpl = str_replace('DeskPRO:emails_user:', '', $tpl);
		$tpl = str_replace('DeskPRO:emails_agent:', '', $tpl);

		return $tr->phrase('agent.tickets.use_email_tpl_action', array('tpl' => $tpl));
	}
}
