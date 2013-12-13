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

namespace Application\DeskPRO\EntityRepository\Helper;

use Application\DeskPRO\App;
use Application\DeskPRO\EntityRepository\AbstractEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;

use Orb\Util\Arrays;
use Orb\Util\Strings;
use Orb\Util\Numbers;

class CommentHelper
{
	/**
	 * @var \Application\DeskPRO\EntityRepository\AbstractEntityRepository
	 */
	protected $repos;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Doctrine\ORM\Mapping\ClassMetadata
	 */
	protected $class;

	/**
	 * @var string
	 */
	protected $entity_name;

	/**
	 * @var string
	 */
	protected $table_name;

	/**
	 * @var string
	 */
	protected $comment_entity_name;

	/**
	 * @var string
	 */
	protected $comment_table_name;

	/**
	 * @var string
	 */
	protected $comment_join_field;

	public function __construct(
		EntityManager $em,
		AbstractEntityRepository $repos,
		$entity_name,
		ClassMetadata $class,
		$comment_entity_name,
		$comment_table_name,
		$comment_join_field
	)
	{
		$this->repos       = $repos;
		$this->em          = $em;
		$this->class       = $class;
		$this->entity_name = $entity_name;
		$this->table_name  = $class->getTableName();

		$this->comment_entity_name = $comment_entity_name;
		$this->comment_table_name  = $comment_table_name;
		$this->comment_join_field  = $comment_join_field;
	}


	/**
	 * Count the number of comments on a record
	 *
	 * @param $record
	 * @param bool $user_visible
	 * @return int
	 */
	public function countOn($record, $user_visible = true)
	{
		if ($user_visible) {
			$user_visible = " AND status = 'visible'";
		} else {
			$user_visible = '';
		}

		$sql = "
			SELECT COUNT(*)
			FROM {$this->comment_table_name}
			WHERE
				{$this->comment_join_field} = ?
				$user_visible
		";

		return $this->em->getConnection()->fetchColumn($sql, array($record->getId()));
	}


	/**
	 * Count the number of comments on a number of records
	 *
	 * @param array $records
	 * @param bool $user_visible
	 * @return array
	 */
	public function countsOnCollection(array $records, $user_visible = true)
	{
		if ($user_visible) {
			$user_visible = " AND status = 'visible'";
		} else {
			$user_visible = '';
		}

		$ids = array();

		foreach ($records as $r) {
			// Ids provided
			if (Numbers::isInteger($r)) {
				$ids[] = $r;

			// Objects provided
			} else {
				$ids[] = $r->getId();
			}
		}

		$ids = Arrays::removeFalsey($ids);
		$ids = array_unique($ids);

		if (!$ids) {
			return array();
		}

		$ids = implode(',', $ids);

		$sql = "
			SELECT {$this->comment_join_field}, COUNT(*)
			FROM {$this->comment_table_name}
			WHERE
				{$this->comment_join_field} IN ($ids)
				$user_visible
			GROUP BY {$this->comment_join_field}
		";

		$counts = $this->em->getConnection()->fetchAllKeyValue($sql);

		return $counts;
	}
}
