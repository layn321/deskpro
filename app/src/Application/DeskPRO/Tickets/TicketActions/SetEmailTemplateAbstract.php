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
use Application\DeskPRO\Entity\Ticket;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\App;

abstract class SetEmailTemplateAbstract extends AbstractAction
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var string
	 */
	protected $template;

	public function __construct($template, TicketChangeTracker $tracker = null)
	{
		$this->tracker = $tracker;
		$this->template = $template;
	}

	/**
	 * Apply the action to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return void
	 */
	public function apply(Ticket $ticket)
	{
		if (!App::getDataService('Template')->customEmailExists($this->template)) {
			return;
		}

		$this->tracker->recordExtra('email_template_' . $this->getType(), $this->template);
	}


	abstract public function getType();


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		return array(
			array('action' => 'set_email_template', 'template' => $this->template)
		);
	}


	public function getDescriptionEmailName()
	{
		$parts = explode(':', $this->template);
		$name = array_pop($parts);
		$name = str_replace('.html.twig', '', $name);
		return $name;
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		return $other_action;
	}

	public function doPrepend()
	{
		return true;
	}
}
