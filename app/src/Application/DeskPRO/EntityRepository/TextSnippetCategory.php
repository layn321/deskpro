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

class TextSnippetCategory extends AbstractEntityRepository
{
	public function getCatsForAgent($typename, PersonEntity $agent)
	{
		$agent->loadHelper('AgentTeam');

		$dql = "
			SELECT c
			FROM DeskPRO:TextSnippetCategory c
			WHERE
				c.typename = ?1
				AND (c.person = ?2 OR c.is_global = true)
		";

		$coll = $this->getEntityManager()->createQuery($dql)
			->setParameter(1, $typename)
			->setParameter(2, $agent)
			->execute();

		return $coll;
	}

	public function getAllByType($typename)
	{
		return $this->getEntityManager()->createQuery("
			SELECT c
			FROM DeskPRO:TextSnippetCategory c INDEX BY c.id
			WHERE c.typename = ?0

		")->execute(array($typename));
	}
}
