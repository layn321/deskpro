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
use Application\DeskPRO\ORM\QueryPartial;
use Application\DeskPRO\Entity\Person as PersonEntity;
use Application\DeskPRO\Entity\Visitor as VisitorEntity;

use Doctrine\ORM\EntityRepository;

class CommentAbstract extends AbstractEntityRepository
{
	const FIELD = '';

	public function getByIds(array $ids, $keep_order = false)
	{
		if (!$ids) return array();

		$ids = implode(',', $ids);

		return $this->getEntityManager()->createQuery("
			SELECT c
			FROM " . $this->_entityName ." c INDEX BY c.id
			WHERE c.id IN ($ids)
		")->execute();
	}

	public function getComments($object, $show_validating = true)
	{
		if ($show_validating) {
			return $this->getEntityManager()->createQuery("
				SELECT c
				FROM " . $this->_entityName ." c
				WHERE c.status != ?1 AND c." . static::FIELD . " = ?2
				ORDER BY c.id DESC
			")->setParameter(1, 'deleted')->setParameter(2, $object)->execute();
		} else {
			return $this->getEntityManager()->createQuery("
				SELECT c
				FROM " . $this->_entityName ." c
				WHERE c.status = ?1 AND c." . static::FIELD . " = ?2
				ORDER BY c.id DESC
			")->setParameter(1, 'visible')->setParameter(2, $object)->execute();
		}
	}

	public function getDisplayComments($object, PersonEntity $person_context = null, VisitorEntity $visitor_context = null)
	{
		$params = array('obj_id' => $object->getId());
		$dql = "SELECT c FROM {$this->_entityName} c WHERE c.".static::FIELD." = :obj_id AND (c.status = 'visible'";
		if ($person_context && $person_context->getId()) {
			$dql .= ' OR c.person = :person_id';
			$params['person_id'] = $person_context->getId();
		}
		if ($visitor_context) {
			$dql .= ' OR c.visitor = :visitor_id';
			$params['visitor_id'] = $visitor_context->getId();
		}
		$dql .= ")";

		return $this->_em->createQuery($dql)->execute($params);
	}

	public function countAwaitingValidation()
	{
		$table = $this->getClassMetadata()->getTableName();
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM $table
			WHERE status = 'validating' OR (status = 'visible' AND is_reviewed = 0)
		");
	}

	public function getValidatingComments()
	{
		return $this->getEntityManager()->createQuery("
			SELECT c
			FROM " . $this->_entityName ." c
			LEFT JOIN c.person p
			WHERE c.status = ?1 OR c.is_reviewed = ?2
			ORDER BY c.id DESC
		")->setParameter(1, 'validating')
		  ->setParameter(2, false)
		  ->execute();
	}

	/**
	 * @param $content
	 * @param $person
	 * @param null $name
	 * @param null $email
	 */
	public function getDuplicate($content, $person = null, $name = null, $email = null)
	{
		$qb = $this->createQueryBuilder('c');

		if ($person && $person->getId()) {
			$qb->andWhere('c.person = :person');
			$qb->setParameter('person', $person);
		} else {
			if ($name) {
				$qb->andWhere('c.name = :name');
				$qb->setParameter('name', $name);
			}
			if ($email) {
				$qb->andWhere('c.email = :email');
				$qb->setParameter('email', $email);
			}
		}

		$qb->setMaxResults(5);
		$qb->orderBy('c.id', 'DESC');

		$comments = $qb->getQuery()->execute();

		foreach ($comments as $comment) {
			if ($comment->getContentReal() == $content) {
				return $comment;
			}
		}

		return null;
	}
}
