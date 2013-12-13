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
 * @category Search
 */

namespace Application\DeskPRO\Search\Adapter\Mysql;

use Orb\Util\CapabilityInformerInterface;

use Application\DeskPRO\App;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Application\DeskPRO\Search\SearcherResult\ResultSet;
use Application\DeskPRO\Search\SearcherResult\ResultInterface;
use Application\DeskPRO\People\PersonContextInterface;
use Orb\Util\Strings;
use Application\DeskPRO\Entity\Person;

/**
 * Strips out search results the user cant actually see
 */
class PermissionFilter implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	/**
	 * The types to generate the where for
	 *
	 * @var array
	 */
	protected $types = array('article', 'news', 'download', 'feedback');

	/**
	 * The 'where' clause
	 *
	 * @var string
	 */
	protected $perm_where = '';

	/**
	 * Any required joins
	 * @var null
	 */
	protected $perm_join = '';

	/**
	 * @var bool
	 */
	protected $has_gen = false;

	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}

	/**
	 * @param array $types
	 */
	public function setTypes(array $types)
	{
		$this->types = $types;
	}

	protected function _gen()
	{
		if (!$this->person_context) {
			throw new \RuntimeException("PermissionFilter requires you to set a person context");
		}

		if ($this->has_gen) {
			return;
		}
		$this->has_gen = true;

		$join = array();
		$where = array();
		$x = 0;

		if (in_array('article', $this->types)) {
			$x++;
			$jn = '_cs' . $x;
			$dis_ids = $this->person_context->PermissionsManager->ArticleCategories->getDisallowedCategories();
			if ($dis_ids) {
				$dis_ids = implode(',', $dis_ids);
				$join[] = "LEFT JOIN content_search_attribute AS $jn ON ($jn.object_type = 'article' AND $jn.object_type = content_search.object_type AND $jn.object_id = content_search.object_id AND $jn.attribute_id LIKE 'category_id%' AND $jn.content IN ($dis_ids))";
				$where[] = "$jn.object_id IS NULL";
			}
		}

		if (in_array('news', $this->types)) {
			$x++;
			$jn = '_cs' . $x;
			$dis_ids = $this->person_context->PermissionsManager->NewsCategories->getDisallowedCategories();
			if ($dis_ids) {
				$dis_ids = implode(',', $dis_ids);
				$join[] = "LEFT JOIN content_search_attribute AS $jn ON ($jn.object_type = 'news' AND $jn.object_type = content_search.object_type AND $jn.object_id = content_search.object_id AND $jn.attribute_id = 'category_id' AND $jn.content IN ($dis_ids))";
				$where[] = "$jn.object_id IS NULL";
			}
		}

		if (in_array('feedback', $this->types)) {
			$x++;
			$jn = '_cs' . $x;
			$dis_ids = $this->person_context->PermissionsManager->NewsCategories->getDisallowedCategories();
			if ($dis_ids) {
				$dis_ids = implode(',', $dis_ids);
				$join[] = "LEFT JOIN content_search_attribute AS $jn ON ($jn.object_type = 'feedback' AND $jn.object_type = content_search.object_type AND $jn.object_id = content_search.object_id AND $jn.attribute_id = 'category_id' AND $jn.content IN ($dis_ids))";
				$where[] = "$jn.object_id IS NULL";
			}
		}

		if (in_array('download', $this->types)) {
			$x++;
			$jn = '_cs' . $x;
			$dis_ids = $this->person_context->PermissionsManager->NewsCategories->getDisallowedCategories();
			if ($dis_ids) {
				$dis_ids = implode(',', $dis_ids);
				$join[] = "LEFT JOIN content_search_attribute AS $jn ON ($jn.object_type = 'download' AND $jn.object_type = content_search.object_type AND $jn.object_id = content_search.object_id AND $jn.attribute_id = 'category_id' AND $jn.content IN ($dis_ids))";
				$where[] = "$jn.object_id IS NULL";
			}
		}

		if (!$join && !$where) {
			return;
		}

		$this->perm_join = implode("\n", $join);
		$this->perm_where = "(" . implode(" AND ", $where) . ")";
	}


	/**
	 * Get the required joins for the check
	 *
	 * @return string|null
	 */
	public function getJoin()
	{
		$this->_gen();
		return $this->perm_join;
	}

	/**
	 * Get the required wheres for the check
	 *
	 * @return string|null
	 */
	public function getWhere()
	{
		$this->_gen();
		return $this->perm_where;
	}
}
