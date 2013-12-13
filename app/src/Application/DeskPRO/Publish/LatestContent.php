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
 * @subpackage Publish
 */

namespace Application\DeskPRO\Publish;

use Doctrine\ORM\EntityManager;

class LatestContent
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var int
	 */
	protected $count = 10;

	/**
	 * @var int
	 */
	protected $max_article = 10;

	/**
	 * @var int
	 */
	protected $max_feedback = 10;

	/**
	 * @var int
	 */
	protected $max_download = 10;

	/**
	 * @var int
	 */
	protected $max_news = 10;

	/**
	 * @var
	 */
	protected $use_selections;


	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


	/**
	 * @param array $use_selections
	 */
	public function useSelections(array $use_selections)
	{
		$this->use_selections = $use_selections;
		$this->max_article = 100;
		$this->max_download = 100;
		$this->max_news = 100;
		$this->max_feedback = 100;
		$this->count = 100;
	}


	/**
	 * @param $x
	 * @return LatestContent
	 */
	public function setMaxCount($x)
	{
		$this->count = $x;

		// They all have equal weight
		$this->max_article = $this->max_feedback = $this->max_download = $this->max_news = $x;

		return $this;
	}


	/**
	 * @param $x
	 * @return LatestContent
	 */
	public function setMaxArticles($x)
	{
		$this->max_article = $x;
		return $this;
	}


	/**
	 * @param $x
	 * @return LatestContent
	 */
	public function setMaxFeedback($x)
	{
		$this->max_feedback = $x;
		return $this;
	}


	/**
	 * @param $x
	 * @return LatestContent
	 */
	public function setMaxDownloads($x)
	{
		$this->max_download = $x;
		return $this;
	}


	/**
	 * @param $x
	 * @return LatestContent
	 */
	public function setMaxNews($x)
	{
		$this->max_news = $x;
		return $this;
	}


	/**
	 * @return array
	 */
	public function getResults()
	{
		$results = array();

		if ($this->use_selections) {
			if (!empty($this->use_selections['articles'])) {
				$res = $this->em->getRepository('DeskPRO:Article')->getByIds($this->use_selections['articles']);
				foreach ($res as $r) {
					$results[] = array('type' => 'article', 'item' => $r);
				}
			}

			if (!empty($this->use_selections['downloads'])) {
				$res = $this->em->getRepository('DeskPRO:Download')->getByIds($this->use_selections['downloads']);
				foreach ($res as $r) {
					$results[] = array('type' => 'download', 'item' => $r);
				}
			}

			if (!empty($this->use_selections['news'])) {
				$res = $this->em->getRepository('DeskPRO:News')->getByIds($this->use_selections['news']);
				foreach ($res as $r) {
					$results[] = array('type' => 'news', 'item' => $r);
				}
			}
		} else {
			if ($this->max_article) {
				$res = $this->em->getRepository('DeskPRO:Article')->getNewest($this->max_article);
				foreach ($res as $r) {
					$results[] = array('type' => 'article', 'item' => $r);
				}
			}
			if ($this->max_feedback) {
				$res = $this->em->getRepository('DeskPRO:Feedback')->getNewest(null, $this->max_feedback);
				foreach ($res as $r) {
					$results[] = array('type' => 'feedback', 'item' => $r);
				}
			}
			if ($this->max_download) {
				$res = $this->em->getRepository('DeskPRO:Download')->getNewest($this->max_download);
				foreach ($res as $r) {
					$results[] = array('type' => 'download', 'item' => $r);
				}
			}
			if ($this->max_news) {
				$res = $this->em->getRepository('DeskPRO:News')->getNewest($this->max_news);
				foreach ($res as $r) {
					$results[] = array('type' => 'news', 'item' => $r);
				}
			}
		}

		usort($results, function($a, $b) {
			return ($a['item']->date_created < $b['item']->date_created) ? -1 : 1;
		});

		if (count($results) <= $this->count) {
			$final_results = $results;
		} else {
			$final_results = array();
			$counts = array();

			foreach ($results as $r) {
				if (!isset($counts[$r['type']])) {
					$counts[$r['type']] = 0;
				}

				$prop = 'max_' . $r['type'];

				if ($counts[$r['type']] >= $this->$prop) {
					continue;
				}

				$final_results[] = $r;
				$counts[$r['type']]++;

				if (count($final_results) >= $this->count) {
					break;
				}
			}
		}

		return $final_results;
	}
}
