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

class CustomDefAbstract extends AbstractEntityRepository
{
	public static function getCacheId($id)
	{
		$str = 'customdef' . md5(get_called_class()) . '_' . $id;
		return $str;
	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		$q = $this->_em->createQuery("
			SELECT f
			FROM {$this->_entityName} f INDEX BY f.id
			ORDER BY f.display_order ASC, f.title
		");
		$q->useResultCache(true, null, static::getCacheId('getfields'));

		return $q->execute();
	}

	public function getEnabledFields()
	{
		$q = $this->_em->createQuery("
			SELECT f
			FROM {$this->_entityName} f INDEX BY f.id
			WHERE f.is_enabled = true
			ORDER BY f.display_order ASC, f.title
		");
		$q->useResultCache(true, null, static::getCacheId('getenabledfields'));

		return $q->execute();
	}

	public function getEnabledUserFields()
	{
		$q = $this->_em->createQuery("
			SELECT f
			FROM {$this->_entityName} f INDEX BY f.id
			WHERE f.is_enabled = true AND f.is_agent_field = false
			ORDER BY f.display_order ASC, f.title
		");
		$q->useResultCache(true, null, static::getCacheId('getenabledfields'));

		return $q->execute();
	}

	/**
	 * @return array
	 */
	public function getTopFields()
	{
		$q = $this->_em->createQuery("
			SELECT f
			FROM {$this->_entityName} f INDEX BY f.id
			WHERE f.parent IS NULL
			ORDER BY f.display_order ASC, f.title
		");

		$q->useResultCache(true, null, static::getCacheId('gettopfields'));

		return $q->execute();
	}

	public function getEnabledTopFields()
	{
		$q = $this->_em->createQuery("
			SELECT f
			FROM {$this->_entityName} f INDEX BY f.id
			WHERE f.parent IS NULL AND f.is_enabled = true
			ORDER BY f.display_order ASC, f.title
		");

		$q->useResultCache(true, null, static::getCacheId('gettopfields'));

		return $q->execute();
	}
}
