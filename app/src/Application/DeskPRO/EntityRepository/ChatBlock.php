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

use \Doctrine\ORM\EntityRepository;
use Application\DeskPRO\Entity\Visitor as VisitorEntity;

class ChatBlock extends AbstractEntityRepository
{
	/**
	 * How long a block stays in place
	 */
	const BLOCK_TIMEOUT = 86400;

	/**
	 * @param \Application\DeskPRO\Entity\Visitor $visitor
	 * @return \Application\DeskPRO\Entity\ChatBlock
	 */
	public function getBlockForVisitor(VisitorEntity $visitor = null)
	{
		$datecut = new \DateTime('-' . self::BLOCK_TIMEOUT . ' seconds');

		if ($visitor) {
			$block = $this->_em->createQuery("
				SELECT b
				FROM DeskPRO:ChatBlock b
				WHERE (b.ip_address = ?0 OR b.visitor = ?1) AND b.date_created > ?2
			")->setParameters(array($visitor->ip_address, $visitor, $datecut))->setMaxResults(1)->getOneOrNullResult();
		} else {
			$block = $this->_em->createQuery("
				SELECT b
				FROM DeskPRO:ChatBlock b
				WHERE (b.ip_address = ?0) AND b.date_created > ?2
			")->setParameters(array(dp_get_user_ip_address(), $datecut))->setMaxResults(1)->getOneOrNullResult();
		}

		return $block;
	}


	/**
	 * @param string $ip_address
	 * @return \Application\DeskPRO\Entity\ChatBlock
	 */
	public function getBlockForIp($ip_address)
	{
		$datecut = new \DateTime('-' . self::BLOCK_TIMEOUT . ' seconds');

		$block = $this->_em->createQuery("
			SELECT b
			FROM DeskPRO:ChatBlock b
			WHERE b.ip_address = ?0 AND b.date_created > ?1
		")->setParameters(array($ip_address, $datecut))->setMaxResults(1)->getOneOrNullResult();

		return $block;
	}


	/**
	 * @param string $ip_address
	 * @param \Application\DeskPRO\Entity\Visitor $visitor
	 * @return bool
	 */
	public function isBlocked($ip_address, VisitorEntity $visitor = null)
	{
		$visitor_id = 0;
		if ($visitor) {
			$visitor_id = $visitor->getId();
		}

		$datecut = new \DateTime('-' . self::BLOCK_TIMEOUT . ' seconds');
		$blocked = $this->_em->getConnection()->fetchColumn("
			SELECT id FROM chat_blocks
			WHERE (visitor_id = ? OR ip_address = ?) AND date_created > ?
		", array($visitor_id, $ip_address, $datecut->format('Y-m-d H:i:s')));

		return $blocked ? true : false;
	}


	/**
	 * Deletes blocks older than BLOCK_TIMEOUT
	 *
	 * @return int
	 */
	public function cleanupBlocks()
	{
		$datecut = new \DateTime('-' . self::BLOCK_TIMEOUT . ' seconds');
		$count = $this->_em->getConnection()->executeUpdate("
			DELETE FROM chat_blocks
			WHERE date_created < ?
		", array($datecut->format('Y-m-d H:i:s')));

		return $count;
	}
}
