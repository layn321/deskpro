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

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Symfony\Component\DependencyInjection\ContainerAware;

class Tickets
{
	/**
	 * Get an array of tickets from the passed IDs.
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getTicketsFromIds(array $ids)
	{
		return App::getOrm()
			->getRepository('DeskPRO:Ticket')
			->getTicketsFromIds($ids);
	}



	/**
	 * @param Ticket $ticket
	 * @return TicketEdit
	 */
	public function getTicketEditor(Entity\Ticket $ticket)
	{
		$ticket_edit = new TicketEdit($ticket);
		return $ticket_edit;
	}



	/**
	 * Get an array of various options used on the new ticket page.
	 *
	 * @param mixed $person The person we're fetching for. This will define the permissions/context.
	 * @return array
	 */
	public function getTicketOptions($person)
	{
		$options = array();

		if ($person['is_agent']) {
			$options['agents'] = App::getDataService('Person')->getAgentNames();

			if (App::getSetting('core.use_agent_team')) {
				$options['agent_teams'] = App::getDataService('AgentTeam')->getTeamNames();
			} else {
				$options['agent_teams'] = array();
			}
		}

		$options['departments'] = App::getDataService('Department')->getNames(null, false);
		$options['gateway_addresses'] = App::getDataService('EmailGatewayAddress')->getOptions();
		$options['gateway_accounts'] = App::getDataService('EmailGateway')->getGatewayNames();

		if (App::getSetting('core.use_ticket_category')) {
			$options['ticket_categories_hierarchy'] = App::getDataService('TicketCategory')->getInHierarchy();
			$options['ticket_categories_full'] = App::getDataService('TicketCategory')->getFullNames(null, false);
			$options['ticket_categories'] = App::getDataService('TicketCategory')->getNames(null, false);
		} else {
			$options['ticket_categories_hierarchy'] = array();
			$options['ticket_categories_full'] = array();
			$options['ticket_categories'] = array();
		}

		if (App::getSetting('core.use_ticket_workflow')) {
			$options['ticket_workflows'] = App::getDataService('TicketWorkflow')->getNames();
		} else {
			$options['ticket_workflows'] = array();
		}

		if (App::getSetting('core.use_product')) {
			$options['products'] = App::getDataService('Product')->getNames();
			$options['products_hierarchy']  = App::getDataService('Product')->getInHierarchy();
		} else {
			$options['products'] = array();
			$options['products_hierarchy']  = array();
		}

		$options['slas']  = App::getDataService('Sla')->getSlaTitles();

		if (App::getSetting('core.use_ticket_priority')) {
			$options['priorities']  = App::getDataService('TicketPriority')->getNames();
		} else {
			$options['priorities'] = array();
		}
		$options['ticket_priorities']  = $options['priorities'];

		return $options;
	}
}
