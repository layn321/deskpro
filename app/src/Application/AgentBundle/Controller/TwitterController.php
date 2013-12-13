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

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\TwitterAccountSearch;
use Application\DeskPRO\Entity\TwitterAccount;

/**
 * Handles creating/editing of Twitter Accounts
 */
class TwitterController extends AbstractController
{
	public function getSectionDataAction()
	{
		$data = array();

		$group_updates = $this->in->getCleanValueArray('group_updates');
		foreach ($group_updates AS $account_id => $groups) {
			if (!is_array($groups)) {
				continue;
			}

			foreach ($groups AS $type => $group) {
				$this->person->setPreference("agent.ui.twitter-group.$account_id.$type", $group);
			}

			App::getOrm()->persist($this->person);
			App::getOrm()->flush();
		}

		$accounts = $this->person->getTwitterAccounts();
		$counts = $this->em->getRepository('DeskPRO:TwitterAccountStatus')->getSectionCounts($accounts);

		$grouping_prefs = $this->em->getRepository('DeskPRO:PersonPref')->getPrefgroupForPersonId('agent.ui.twitter-group.', $this->person->getId());
		$groupings = array();
		foreach ($accounts AS $account) {
			$groupings[$account->id] = array();
			foreach (array('mine', 'team', 'unassigned', 'all') AS $group) {
				$value = isset($grouping_prefs[$account->id . '.' . $group]) ? $grouping_prefs[$account->id . '.' . $group] : '';
				$data = $this->em->getRepository('DeskPRO:TwitterAccountStatus')->getGroupedSectionCount($account, $group, $value);
				$groupings[$account->id][$group] = array('group' => $value, 'data' => $data);
			}
		}

		$data['section_html'] = $this->renderView('AgentBundle:Twitter:window-section.html.twig', array(
			'counts' => $counts,
			'groupings' => $groupings,
			'accounts' => $accounts,
			'agents' => $this->em->getRepository('DeskPRO:Person')->getAgents(),
			'teams' => $this->em->getRepository('DeskPRO:AgentTeam')->getTeams()
		));

		return $this->createJsonResponse($data);
	}

	public function updateGroupingAction()
	{
		$account = $this->getAccount($this->in->getUint('account_id'));
		$type = $this->in->getString('type');
		$group = $this->in->getString('group');

		$this->person->setPreference("agent.ui.twitter-group.$account->id.$type", $group);

		App::getOrm()->persist($this->person);
		App::getOrm()->flush();

		switch ($type) {
			case 'mine': $route = 'agent_twitter_mine_list'; break;
			case 'team': $route = 'agent_twitter_team_list'; break;
			case 'unassigned': $route = 'agent_twitter_unassigned_list'; break;
			case 'all': $route = 'agent_twitter_all_list'; break;
			default: $route = '';
		}

		$data = $this->em->getRepository('DeskPRO:TwitterAccountStatus')->getGroupedSectionCount($account, $type, $group);

		return $this->createJsonResponse(array(
			'account_id' => $account->id,
			'type' => $type,
			'group' => $group,
			'html' => $this->renderView('AgentBundle:Twitter:window-sub-grouping.html.twig', array(
				'account' => $account,
				'section_type' => $type,
				'group_by' => $group,
				'data' => $data,
				'route' => $route,
				'agents' => $this->em->getRepository('DeskPRO:Person')->getAgents(),
				'teams' => $this->em->getRepository('DeskPRO:AgentTeam')->getTeams()
			))
		));
	}

	public function newTweetAction()
	{
		$accounts = $this->person->getTwitterAccounts();
		if (!$accounts) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$account = count($accounts) == 1 ? $accounts[0] : false;

		return $this->render('AgentBundle:Twitter:new.html.twig', array(
			'accounts' => $accounts,
			'account' => $account
		));
	}

	public function newTweetSaveAction()
	{
		$accounts = $this->person->getTwitterAccounts();

		$text = $this->in->getString('text');
		$split = $this->in->getBool('split');
		$account_ids = $this->in->getCleanValueArray('account_ids', 'uint');

		$twitter_service = new \Application\DeskPRO\Service\Twitter();

		if (strlen($text)) {
			foreach ($accounts AS $account) {
				if (in_array($account->id, $account_ids)) {
					$twitter_service->sendAccountMessage('public', $text, $split, $account);
				}
			}
		}

		return $this->createJsonResponse(array('success' => true));
	}

	public function runSearchAction($account_id, $search_id)
	{
		$account = $this->getAccount($account_id);
		$search = $this->em->getRepository('DeskPRO:TwitterAccountSearch')->find($search_id);

		if (!$search) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(sprintf('There is no search with ID "%d"', $search_id));
		}

		$this->person->setPreference('agent.ui.last_twitter_account', $account->id);

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;
		$per_page = TwitterAccount::DEFAULT_LIMIT;

		if ($this->in->getBool('partial')) {
			$tpl = 'AgentBundle:Twitter:part-search.html.twig';
		} else {
			$tpl = 'AgentBundle:Twitter:run-search.html.twig';
		}

		$includeArchived = $this->in->getBool('include.archived');

		if ($this->in->getBool('since_id')) {
			$statuses = $search->updateSearch(true, $this->in->getString('since_id'));
			$added = count($statuses);
			$statuses = array_slice($statuses, 0, $per_page);
		} else {
			$statuses = $search->getAccountStatuses($includeArchived, $page, $per_page);
			$added = count($statuses);
		}
		$total_count = $search->countAccountStatuses($includeArchived);

		$max_id = 0;
		foreach ($statuses AS $status) {
			if ($status->status->id > $max_id) {
				$max_id = $status->status->id;
			}
		}

		return $this->render($tpl, array(
			'account'  => $account,
			'search'   => $search,
			'statuses' => $statuses,
			'total_count' => $total_count,
			'per_page' => $per_page,
			'page' => $page,
			'showing_to' => min($total_count, $page * $per_page),
			'max_id' => $max_id,
			'added' => $added
		));
	}

	public function deleteSearchAction($account_id, $security_token)
	{
		$account = $this->getAccount($account_id);

		$search_id = $this->in->getUint('search_id');
		if ($search_id) {
			$search = $this->em->getRepository('DeskPRO:TwitterAccountSearch')->find($search_id);
		} else {
			$search_term = $this->in->getString('search_term');
			$search = $this->em->getRepository('DeskPRO:TwitterAccountSearch')->getExistingSearch($search_term, $account);
		}

		if (!$search) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(sprintf('There is no search with ID "%d"', $search_id));
		}

		$this->ensureAuthToken('delete_search', $security_token);

		$this->em->remove($search);
		$this->em->flush();

		return $this->createJsonResponse(array('success' => true));
	}

	public function newSearchAction($account_id)
	{
		$account = $this->getAccount($account_id);
		$search_term = $this->in->getString('search_term');

		$search = $this->em->getRepository('DeskPRO:TwitterAccountSearch')->getExistingSearch($search_term, $account);

		if (!$search) {
			$search = new TwitterAccountSearch();
			$search->account = $account;
			$search->term = $search_term;

			$this->em->persist($search);
			$this->em->flush();
		}

		return $this->runSearchAction($account_id, $search->id);
	}

	/**
	 * Check account security.
	 *
	 * @param integer $id The account id.
	 * @return \Application\DeskPRO\Entity\TwitterAccount
	 * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function getAccount($id)
	{
		// check if account id is in persons account id list
		if (!in_array($id, $this->person->getTwitterAccountIds())) {
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
		}

		// check if account exists
		$account = $this->em->getRepository('DeskPRO:TwitterAccount')->find($id);
		if (!$account) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(sprintf('There is no account with ID "%d"', $id));
		}

		return $account;
	}

	/**
	 * @return string
	 */
	protected function getSortByDate()
	{
		// sort by date, ascending or descending
		$sortByDate = $this->in->getValue('sortbydate');
		if (!$sortByDate) {
			$sortByDate = 'desc';
		}

		return $sortByDate;
	}

	protected function adjustPage($count, $page = null, $per_page = null)
	{
		if (!$per_page) {
			$per_page = TwitterAccount::DEFAULT_LIMIT;
		}
		if ($page === null) {
			$page = $this->in->getUint('page');
		}
		if (!$page) {
			$page = 1;
		}

		$start = ($page - 1) * $per_page;
		if ($start >= $count) {
			$page = ($count ? ceil($count / $per_page) : 1);
		}

		return $page;
	}

	/**
	 * @param array $statuses
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function renderList(array $statuses, $template, $total_count, $page, $sort_by_date = null)
	{
		if ($sort_by_date === null) {
			$sort_by_date = $this->getSortByDate();
		}

		$per_page = TwitterAccount::DEFAULT_LIMIT;

		$parameters = array(
			'statuses' => $statuses,
			'person' => $this->getPerson(),
			'sort_by_date' => $sort_by_date,
			'total_count' => $total_count,
			'per_page' => $per_page,
			'page' => $page,
			'showing_to' => min($total_count, $page * $per_page),
			'agents' => $this->em->getRepository('DeskPRO:Person')->getAgents()
		);

		// check if is partial
		if ($this->in->getBool('partial')) {
			return $this->render('AgentBundle:TwitterStatus:part-status.html.twig', $parameters);
		}

		// render html response
		return $this->render($template, $parameters);
	}

}
