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

class SetFromAddressAgentModifier implements CollectionModifierInterface
{
	protected $email_address;

	public function __construct($email_address)
	{
		$this->email_address = $email_address;
	}

	public function modifyCollection(ActionsCollection $collection)
	{
		if (!\Orb\Validator\StringEmail::isValueValid($this->email_address)) {
			return;
		}

		if ($collection->hasActionType('SetTicketEmailAgent')) {
			$collection->getActionType('SetTicketEmailAgent')->setEmail($this->email_address);
		} else {
			$status_action = new SetTicketEmailAgentAction($this->email_address);
			$collection->addAction($status_action, array(), true);
		}
	}

	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		$tr = App::getTranslator();
		return $tr->phrase('agent.tickets.send_agent_notifs_from_email_action', array('email' => $this->email_address));
	}
}
