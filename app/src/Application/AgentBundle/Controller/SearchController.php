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
use Orb\Util\Numbers;
use Application\DeskPRO\Search\SearcherResult\ResultSet;
use Application\DeskPRO\Search\SearcherResult\Result;

class SearchController extends AbstractController
{
    public function searchAction()
    {
		$q = $this->in->getString('q');
		$search = App::getSearchAdapter();

		$results = array();
		$results_raw['ticket']    = $search->getTicketSearcher()->query($q, 25, 1, true);
		$results_raw['chat_conversation'] = $search->getChatConversationSearcher()->query($q, 25, 1, true);
		$results_raw['article']   = $search->getContentSearcher()->query($q, 25, 1, array('article'), true);
		$results_raw['download']  = $search->getContentSearcher()->query($q, 25, 1, array('download'), true);
		$results_raw['feedback']  = $search->getContentSearcher()->query($q, 25, 1, array('feedback'), true);
		$results_raw['news']      = $search->getContentSearcher()->query($q, 25, 1, array('news'), true);

		$results = array();
		foreach ($results_raw as $type => $set) {
			if ($set->count()) {
				$results[$type] = $search->getResultSetObjects($set, true);
			}
		}

		return $this->render('AgentBundle:Search:search.html.twig', array(
			'results' => $results
		));
	}

	public function searchResultsAction()
    {
		$q = $this->in->getString('q');
		$search = App::getSearchAdapter();

		$results_raw['ticket']    = $search->getTicketSearcher()->query($q, 25, 1, true);
		$results_raw['chat_conversation'] = $search->getChatConversationSearcher()->query($q, 25, 1, true);
		$results_raw['article']   = $search->getContentSearcher()->query($q, 25, 1, array('article'), true);
		$results_raw['download']  = $search->getContentSearcher()->query($q, 25, 1, array('download'), true);
		$results_raw['feedback']  = $search->getContentSearcher()->query($q, 25, 1, array('feedback'), true);
		$results_raw['news']      = $search->getContentSearcher()->query($q, 25, 1, array('news'), true);

		$results = array();
		foreach ($results_raw as $type => $set) {
			if ($set->count()) {
				$results[$type] = $search->getResultSetObjects($set, false);
			}
		}

		// Check for IDs too
		if (is_numeric($q)) {
			$exact = array(
				'ticket'       => $this->em->find('DeskPRO:Ticket', $q),
				'person'       => $this->em->find('DeskPRO:Person', $q),
				'organization' => $this->em->find('DeskPRO:Organization', $q),
				'article'      => $this->em->find('DeskPRO:Article', $q),
				'news'         => $this->em->find('DeskPRO:News', $q),
				'download'     => $this->em->find('DeskPRO:Download', $q),
				'feedback'     => $this->em->find('DeskPRO:Feedback', $q)
			);
			foreach ($exact as $type => $obj) {
				if ($obj) {
					if (!isset($results[$type])) {
						$results[$type] = array();
					}
					$results[$type][] = $obj;
				}
			}
		}

		return $this->render('AgentBundle:Search:search.json.jsonphp', array(
			'router' => App::getRouter(),
			'results' => $results,
		));
	}
}
