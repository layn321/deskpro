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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\Person as PersonEntity;

use Orb\Util\Arrays;

class TextSnippet extends AbstractEntityRepository
{
	/**
	 * Get snippets for agent grouped by category (@see groupSnippetCollection)
	 *
	 * @param $typename
	 * @param PersonEntity $agent
	 * @return array
	 */
	public function getSnippetsForAgent($typename, PersonEntity $agent)
	{
		$agent->loadHelper('AgentTeam');

		$dql = "
			SELECT s, c
			FROM DeskPRO:TextSnippet s
			LEFT JOIN s.category c
			WHERE
				c.typename = ?1
				AND (c.person = ?2 OR c.is_global = true)
		";

		$coll = $this->getEntityManager()->createQuery($dql)
			->setParameter(1, $typename)
			->setParameter(2, $agent)
			->execute();

		if (!$coll) return array();

		return $this->groupSnippetCollection($coll);
	}


	/**
	 * Get all snippets for an agent with limits
	 *
	 * @param $typename
	 * @param PersonEntity $agent
	 * @param int $page
	 * @param int $per_page
	 * @param int $in_category
	 * @return mixed
	 */
	public function getAllSnippetsForAgent($typename, PersonEntity $agent, $page = 1, $per_page = 250, $in_category = null)
	{
		$dql = "
			SELECT s, c
			FROM DeskPRO:TextSnippet s
			LEFT JOIN s.category c
			WHERE
				c.typename = ?1
				AND (c.person = ?2 OR c.is_global = true)
		";

		if ($in_category) {
			$dql .= ' AND c = ?3 ';
		}

		$q = $this->getEntityManager()->createQuery($dql)
			->setMaxResults($per_page)
			->setFirstResult(($page-1) * $per_page)
			->setParameter(1, $typename)
			->setParameter(2, $agent);

		if ($in_category) {
			$q->setParameter(3, $in_category);
		}

		$coll = $q->execute();

		return $coll;
	}


	/**
	 * Count all of an agents snippets
	 *
	 * @param $typename
	 * @param PersonEntity $agent
	 * @return mixed
	 */
	public function countSnippetsForAgent($typename, PersonEntity $agent)
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM text_snippets
			LEFT JOIN text_snippet_categories ON (text_snippet_categories.id = text_snippets.category_id)
			WHERE
				text_snippet_categories.typename = ?
				AND (text_snippets.person_id = ? OR text_snippet_categories.is_global = 1)
		", array($typename, $agent->getId()));
	}


	/**
	 * Group a collection of snippets
	 *
	 * @param $collection
	 * @return array
	 */
	public function groupSnippetCollection($collection)
	{
		$ret = array();

		foreach ($collection as $snippet) {
			if (!isset($ret[$snippet->category['id']])) {
				$ret[$snippet->category['id']] = array('category' => $snippet->category, 'snippets' => array());
			}

			$ret[$snippet->category['id']]['snippets'][] = $snippet;
		}

		return $ret;
	}
}
