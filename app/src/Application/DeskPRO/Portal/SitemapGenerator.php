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
 * @subpackage
 */

namespace Application\DeskPRO\Portal;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Application\DeskPRO\Publish\Structure as PublishStructure;
use Application\DeskPRO\People\PersonGuest;

class SitemapGenerator
{
	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
	 */
	protected $router;

	/**
	 * @var \Application\DeskPRO\Publish\Structure
	 */
	protected $structure;

	/**
	 * @var string
	 */
	protected $base_url;

	/**
	 * @var array
	 */
	protected $items = null;

	public function __construct($base_url, EntityManager $em, Router $router)
	{
		$this->base_url   = rtrim($base_url, '/');
		$this->em         = $em;
		$this->db         = $em->getConnection();
		$this->router     = $router;

		$person = new PersonGuest();
		$this->structure = new PublishStructure(
			$person,
			$this->em,
			new \Doctrine\Common\Cache\ArrayCache()
		);
	}


	/**
	 * Get sitemap.xml
	 *
	 * @return string
	 */
	public function getXml()
	{
		$xml = array();
		$xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$attributes = array('loc', 'changefreq', 'lastmod', 'priority');

		foreach ($this->getItems() as $item) {
			$xml[] = "<url>";
			foreach ($attributes as $attr) {
				if (!empty($item[$attr])) {
					$val = $item[$attr];
					if ($attr == 'loc') {
						$val = $this->base_url . $val;
					}

					$xml[] = "\t<$attr>$val</$attr>";
				}
			}
			$xml[] = "</url>";
		}

		$xml[] = "</urlset>";
		$xml[] = '';

		$xml = implode("\n", $xml);

		return $xml;
	}


	/**
	 * Get items
	 *
	 * @return array
	 */
	public function getItems()
	{
		if ($this->items !== null) return $this->items;

		$this->items = array_merge(
			$this->getSiteItems(),
			$this->getArticleItems(),
			$this->getFeedbackItems(),
			$this->getDownloadItems(),
			$this->getNewsItems()
		);

		return $this->items;
	}

	/**
	 * @return array
	 */
	protected function getSiteItems()
	{
		$items = array();
		$items[] = array(
			'loc' => $this->router->generate('user', array()),
			'changefreq' => 'daily',
		);

		$items[] = array(
			'loc' => $this->router->generate('user_tickets_new', array()),
			'changefreq' => 'monthly',
		);

		$items[] = array(
			'loc' => $this->router->generate('user_feedback_new', array()),
			'changefreq' => 'monthly',
		);

		return $items;
	}

	/**
	 * @return array
	 */
	protected function getArticleItems()
	{
		$cat_ids = $this->structure->getArticleCategoryIds();
		if (!$cat_ids) {
			return array();
		}

		$items = array();

		$items[] = array(
			'loc' => $this->router->generate('user_articles', array()),
			'changefreq' => 'daily'
		);

		#------------------------------
		# Categories
		#------------------------------

		$cats = $this->structure->getArticleCategories();

		foreach ($cats as $cat) {
			$items[] = array(
				'loc' => $this->router->generate('user_articles', array('slug' => $cat->getUrlSlug())),
				'changefreq' => 'daily'
			);
		}

		#------------------------------
		# Articles
		#------------------------------

		$articles = $this->em->createQuery("
			SELECT PARTIAL art.{id,slug,title}
			FROM DeskPRO:Article art
			LEFT JOIN art.categories cat
			WHERE art.status = 'published' AND cat.id IN (?0)
		")->execute(array($cat_ids));

		foreach ($articles as $a) {
			$items[] = array(
				'loc' => $this->router->generate('user_articles_article', array('slug' => $a->getUrlSlug())),
				'changefreq' => 'weekly'
			);
		}

		return $items;
	}

	/**
	 * @return array
	 */
	protected function getNewsItems()
	{
		$cat_ids = $this->structure->getNewsCategoryIds();
		if (!$cat_ids) {
			return array();
		}

		$items = array();

		$items[] = array(
			'loc' => $this->router->generate('user_news', array()),
			'changefreq' => 'daily'
		);

		#------------------------------
		# Categories
		#------------------------------

		$cats = $this->structure->getNewsCategories();

		foreach ($cats as $cat) {
			$items[] = array(
				'loc' => $this->router->generate('user_news', array('slug' => $cat->getUrlSlug())),
				'changefreq' => 'daily'
			);
		}

		#------------------------------
		# News
		#------------------------------

		if ($cat_ids) {
			$news = $this->em->createQuery("
				SELECT PARTIAL news.{id,slug,title}
				FROM DeskPRO:News news
				WHERE news.status = 'published' AND news.category IN (?0)
			")->execute(array($cat_ids));

			foreach ($news as $n) {
				$items[] = array(
					'loc' => $this->router->generate('user_news_view', array('slug' => $n->getUrlSlug())),
					'changefreq' => 'weekly'
				);
			}
		}

		return $items;
	}

	/**
	 * @return array
	 */
	protected function getDownloadItems()
	{
		$cat_ids = $this->structure->getDownloadCategoryIds();
		if (!$cat_ids) {
			return array();
		}

		$items = array();

		$items[] = array(
			'loc' => $this->router->generate('user_downloads', array()),
			'changefreq' => 'daily'
		);

		#------------------------------
		# Categories
		#------------------------------

		$cats = $this->structure->getDownloadCategories();

		foreach ($cats as $cat) {
			$items[] = array(
				'loc' => $this->router->generate('user_downloads', array('slug' => $cat->getUrlSlug())),
				'changefreq' => 'daily'
			);
		}

		#------------------------------
		# Downloads
		#------------------------------

		$downloads = $this->em->createQuery("
			SELECT PARTIAL download.{id,slug,title}
			FROM DeskPRO:Download download
			WHERE download.status = 'published' AND download.category IN (?0)
		")->execute(array($cat_ids));

		foreach ($downloads as $d) {
			$items[] = array(
				'loc' => $this->router->generate('user_downloads_file', array('slug' => $d->getUrlSlug())),
				'changefreq' => 'weekly'
			);
		}

		return $items;
	}

	/**
	 * @return array
	 */
	protected function getFeedbackItems()
	{
		$cat_ids = $this->structure->getFeedbackCategoryIds();
		if (!$cat_ids) {
			return array();
		}

		$items = array();

		$items[] = array(
			'loc' => $this->router->generate('user_feedback', array()),
			'changefreq' => 'daily'
		);

		#------------------------------
		# Categories
		#------------------------------

		$cats = $this->structure->getFeedbackCategories();

		foreach ($cats as $cat) {
			$items[] = array(
				'loc' => $this->router->generate('user_feedback', array('slug' => $cat->getUrlSlug())),
				'changefreq' => 'daily'
			);
		}

		#------------------------------
		# Downloads
		#------------------------------

		$feedback = $this->em->createQuery("
			SELECT PARTIAL feedback.{id,slug,title}
			FROM DeskPRO:Feedback feedback
			WHERE feedback.hidden_status IS NULL AND feedback.category IN (?0)
		")->execute(array($cat_ids));

		foreach ($feedback as $f) {
			$items[] = array(
				'loc' => $this->router->generate('user_feedback_view', array('slug' => $f->getUrlSlug())),
				'changefreq' => 'weekly'
			);
		}

		return $items;
	}
}