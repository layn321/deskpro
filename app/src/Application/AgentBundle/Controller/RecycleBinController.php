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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller;

use Orb\Util\Arrays;

use Application\DeskPRO\Entity;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Entity\PersonContactData;
use Application\DeskPRO\Entity\PersonNote;
use Application\DeskPRO\Entity\Organization;

use Application\DeskPRO\App;

/**
 * Handles viewing of deleted items
 */
class RecycleBinController extends AbstractController
{
	public function listAction()
	{
		$tickets = $this->_getTickets();
		$ticket_html = false;
		if (!empty($tickets['html'])) {
			$ticket_html = $tickets['html'];
		}

		return $this->render('AgentBundle:RecycleBin:list.html.twig', array(
			'tickets_html' => $ticket_html,
			'tickets_no_more_results' => $tickets['no_more_results'],
		));
	}

	public function listMoreAction($type, $page)
	{
		$method = '_get' . ucfirst($type);
		$res = $this->$method($page);

		$return_res = array(
			'html' => $res['html'],
			'count' => $res['count'],
			'no_more_results' => $res['no_more_results']
		);

		return $this->createJsonResponse($res);
	}


	############################################################################
	# fetcher methods for different types
	############################################################################

	protected function _getTickets($page = 1)
	{
		$per_page = 10;
		$pageinfo = array(
			'limit'  => $per_page,
			'offset' => ($page - 1) * $per_page
		);

		$searcher = new \Application\DeskPRO\Searcher\TicketSearch();
		$searcher->addTerm('deleted', 'is', 1);

		$results = $searcher->getMatches($pageinfo);

		if (!$results) {
			return array('no_more_results' => true);
		}

		$no_more = false;
		if (count($results < $per_page)) {
			$no_more = true;
		}

		$deleted_tickets = $this->em->createQuery("
			SELECT d
			FROM DeskPRO:TicketDeleted d INDEX BY d.ticket_id
			LEFT JOIN d.by_person p
			WHERE d.ticket_id IN (" . implode(',', $results) . ")
		");
		$tickets = $this->em->getRepository('DeskPRO:Ticket')->getTicketsFromIds($results);


		$vars = array(
			'tickets' => $tickets,
			'count' => count($tickets),
			'deleted_tickets' => $deleted_tickets,
			'page' => $page,
			'no_more_results' => $no_more
		);

		$vars['html'] = $this->renderView('AgentBundle:RecycleBin:list-tickets.html.twig', $vars);

		return $vars;
	}
}
