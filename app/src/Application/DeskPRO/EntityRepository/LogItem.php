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

use Doctrine\ORM\EntityRepository;
use Application\DeskPRO\App;

class LogItem extends AbstractEntityRepository
{
	public function findBySn($log_sn)
	{
		try {
			return $this->_em->createQuery("
				SELECT l
				FROM DeskPRO:LogItem l
				WHERE l.session_name = ?1
				ORDER BY l.id DESC
			")->setParameter(1, $log_sn)
			  ->setMaxResults(1)
			  ->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}


	/**
	 * Count all error log items
	 *
	 * @return int
	 */
	public function getErrorLogsCount()
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM log_items
			WHERE log_name = ?
		", array('error_log'));
	}



	/**
	 * Get an array of error logs
	 *
	 * @param int $page
	 * @param int $per_page
	 * @return array
	 */
	public function getErrorLogs($page = 1, $per_page = 25)
	{
		$offset = max(0, $page - 1) * $per_page;

		return $this->_em->createQuery("
			SELECT l
			FROM DeskPRO:LogItem l
			WHERE l.log_name = ?1
			ORDER BY l.id DESC
		")->setParameter(1, 'error_log')
		  ->setFirstResult($offset)
		  ->setMaxResults($per_page)
		  ->execute();
	}



	/**
	 * Deletes all error logs
	 *
	 * @return bool
	 */
	public function clearAllErrorLogs()
	{
		App::getDb()->executeUpdate("DELETE FROM log_items WHERE log_name = ?", array('error_log'));
		return true;
	}
}
