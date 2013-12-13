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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\SearchLog;

use Application\DeskPRO\Labels\ContentLabelCloud;

use Application\DeskPRO\Search\StickyWordSearch;

use Orb\Util\Arrays;
use Orb\Util\Numbers;

class SearchController extends AbstractController
{
	public function searchAction()
	{
		$q = $this->in->getString('q');

		if ($this->in->getString('gourl')) {
			$gourl = $this->in->getString('gourl');
			$count = $this->in->getUint('c');

			$validate = $this->checkRequestToken($gourl . $count, 't');
			if ($validate || 1) {
				$searchlog = SearchLog::create($q, $count, true);
				$this->em->persist($searchlog);
				$this->em->flush();

				$this->session->set('from_search', true);
				$this->session->set('last_searchlog_id', $searchlog->id);
				$this->session->save();
			}
			return $this->redirect($this->in->getString('gourl'));
		}

		$is_search = false;
		$results = false;
		$sticky_results = false;

		$total = 0;
		$per_page = 25;
		$cur_page = 1;
		if ($this->in->getUint('p')) {
			$cur_page = $this->in->getUint('p');
		}

		if ($q) {
			$is_search  = true;
			$search     = App::getSearchAdapter();
			$search->setPersonContext($this->person);

			$result_set = $search->getContentSearcher()->omnisearch($q, null, $per_page, $cur_page);
			$total      = $result_set->totalCount();
			$results    = $search->getResultSetObjects($result_set, true);

			$sticky_search  = new StickyWordSearch($this->em);
			$sticky_results = $sticky_search->getResults($q, 5);

			if ($sticky_results) {
				foreach ($sticky_results as $key => $x) {
					if (isset($results[$key])) {
						unset($results[$key]);
					}
				}
			}

			$searchlog = SearchLog::create($q, count($results) + count($sticky_results), true);
			$this->em->transactional(function($em) use ($searchlog) {
				$em->persist($searchlog);
				$em->flush();
			});

			$this->session->set('last_searchlog_id', $searchlog->id);
			$this->session->save();
		}

		$pageinfo = Numbers::getPaginationPages($total, $cur_page, $per_page);

		return $this->render('UserBundle:Search:search.html.twig', array(
			'is_search'         => $is_search,
			'results'           => $results,
			'sticky_results'    => $sticky_results,
			'query'             => $q,
			'pageinfo'          => $pageinfo,
			'num_results'       => $total,
		));
	}

	public function labelSearchAction($label = '', $type = 'all')
	{
		if ($this->in->getString('label') || $this->in->getString('type')) {
			if (!$label) {
				$label = $this->in->getString('label');
			}
			if ($this->in->getString('type')) {
				$type = $this->in->getString('type');
			}

			if ($label) {
				if (!$type OR !in_array($type, array('all', 'articles', 'feedback', 'downloads', 'news'))) {
					$type = 'all';
				}

				// Redirect label in query string (ie from form) to proper URL
				return $this->redirectRoute('user_search_labels', array('label' => $label, 'type' => $type));
			}
		}

		if (!$type OR !in_array($type, array('all', 'articles', 'feedback', 'downloads', 'news'))) {
			$type = 'all';
		}

		#------------------------------
		# Find content with label
		#------------------------------

		$total = 0;
		$per_page = 25;
		$cur_page = 1;
		if ($this->in->getUint('p')) {
			$cur_page = $this->in->getUint('p');
		}

		$search_types = array();
		if ($type == 'all') {
			$search_types = array('article', 'feedback', 'download', 'news');
		} else {
			if ($type == 'articles')   $search_types = array('article');
			if ($type == 'feedback')   $search_types = array('feedback');
			if ($type == 'downloads')  $search_types = array('download');
			if ($type == 'news')       $search_types = array('news');
		}

		$results = null;
		$pageinfo = null;
		if ($label) {
			$search      = App::getSearchAdapter();
			$result_set  = $search->getContentSearcher()->labelled(array($label), $per_page, $cur_page, $search_types);
			$results     = $search->getResultSetObjects($result_set, true);

			$total    = $result_set->totalCount();
			$pageinfo = Numbers::getPaginationPages($total, $cur_page, $per_page);
		}

		#------------------------------
		# Make combined search cloud
		#------------------------------

		$content_cloud = new ContentLabelCloud();
		$cloud = $content_cloud->getCloud();

		return $this->render('UserBundle:Search:label-search.html.twig', array(
			'cloud'    => $cloud,
			'label'    => $label,
			'results'  => $results,
			'type'     => $type,
			'pageinfo' => $pageinfo,
			'num_results' => $total
		));
	}

	public function omnisearchAction($query)
	{
		$search = App::getSearchAdapter();
		$result_set = $search->getContentSearcher()->omnisearch($query);
		$results = $search->getResultSetObjects($result_set, true);

		$format = $this->in->getString('format');

		if ($format == 'json') {
			$data = array('results' => array());

			foreach ($results as $res) {
				$item = $res['object'];
				$data['results'][] = array(
					'url' => $item->getLink(),
					'title' => $item->getTitle()
				);
			}

			if ($this->in->getString('callback')) {
				return $this->createJsonpResponse($data);
			} else {
				return $this->createJsonResponse($data);
			}
		} else {
			return $this->render('UserBundle:Search:omnisearch.html.twig', array(
				'results' => $results,
				'query'   => $query,
			));
		}
	}

	public function similarToAction($content_type)
	{
		 $content = isset($_REQUEST['content']) ? (string)$_REQUEST['content'] : '';

		$search = App::getSearchAdapter();
		$result_set = $search->getContentSearcher()->omnisearch($content, array($content_type));
		$results = $search->getResultSetObjects($result_set, true);

		return $this->render('UserBundle:Search:similar-to.html.twig', array(
			'results' => $results,
		));
	}
}
