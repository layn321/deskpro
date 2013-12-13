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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

class TicketFiltersController extends AbstractController
{
	############################################################################
	# index
	############################################################################

	public function indexAction()
	{
		$global_filters = $this->em->getRepository('DeskPRO:TicketFilter')->getAllGlobalFilters();
		$team_filters   = $this->em->getRepository('DeskPRO:TicketFilter')->getAllTeamFilters();
		$agent_filters  = $this->em->getRepository('DeskPRO:TicketFilter')->getAllAgentFilters();

		$access_tester = new \Application\DeskPRO\Tickets\FilterAccessResolver($this->em);
		$agents = $this->em->getRepository('DeskPRO:Person')->getAgents();

 		return $this->render('AdminBundle:TicketFilters:index.html.twig', array(
			'global_filters' => $global_filters,
			'team_filters'   => $team_filters,
			'agent_filters'  => $agent_filters,
			'agents'         => $agents,
			'access_tester'  => $access_tester,
		));
	}

	############################################################################
	# edit
	############################################################################

	public function editAction($filter_id)
	{
		if (!$filter_id) {
			$filter = new Entity\TicketFilter();

			$filter_users = null;
			$filter_users_ignore = null;
		} else {
			$filter = $this->em->getRepository('DeskPRO:TicketFilter')->find($filter_id);
			if (!$filter) {
				throw $this->createNotFoundException();
			}

			$access_tester = new \Application\DeskPRO\Tickets\FilterAccessResolver($this->em);

			$agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
			$filter_users = $access_tester->getUsers($filter, $agents);
			$filter_users_ignore = $access_tester->getIgnoreUsers($filter, $agents);
		}

		if ($this->in->getBool('process')) {
			switch ($this->in->getString('filter_visibility')) {
				case 'filter_agent_team':
					$filter['agent_team_id'] = $this->in->getUint('filter.agent_team_id');
					$filter['person']        = null;
					$filter['is_global']     = false;
					break;

				case 'filter_agent':
					$filter['agent_team'] = null;
					$filter['person_id']  = $this->in->getUint('filter.person_id');
					$filter['is_global']  = false;
					break;

				case 'filter_global':
				default:
					$filter['agent_team'] = null;
					$filter['person']     = null;
					$filter['is_global']  = true;
					break;
			}

			$filter['title'] = $this->in->getString('filter.title');
			$term_rules = \Application\DeskPRO\UI\RuleBuilder::newTermsBuilder();
			$filter['terms'] = $term_rules->readForm($this->in->getCleanValueArray('terms', 'raw' , 'discard'));

			APp::getOrm()->transactional(function($em) use ($filter) {
				$em->persist($filter);
				$em->flush();
			});

			return $this->redirectRoute('admin_tickets_filters');
		}

		$term_options = App::getApi('tickets')->getTicketOptions($this->person);

		return $this->render('AdminBundle:TicketFilters:edit.html.twig', array(
			'filter' => $filter,
			'term_options' => $term_options,
			'filter_users' => $filter_users,
			'filter_users_ignore' => $filter_users_ignore,
		));
	}

	############################################################################
	# delete
	############################################################################

	public function deleteAction($filter_id, $security_token)
	{
		$this->ensureAuthToken('delete_ticket_filter', $security_token);

		$filter = $this->em->getRepository('DeskPRO:TicketFilter')->find($filter_id);
		if (!$filter) {
			throw $this->createNotFoundException();
		}

		$this->db->beginTransaction();

		try {
			$this->em->remove($filter);
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_tickets_filters');
	}
}
