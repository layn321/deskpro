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

use \Doctrine\ORM\EntityRepository;

class AbstractRevisionRepository extends AbstractEntityRepository
{
    public function getRevisionsForAgent(PersonEntity $agent, array $options = array())
    {
        $class_parts = explode('\\',get_class($this));
        $class_name = array_pop($class_parts);

        if(isset($options['date_range'])) {
            $query = $this->_em->createQuery("
				SELECT rev
				FROM DeskPRO:{$class_name} rev INDEX BY rev.id
				WHERE rev.person = ?1
				AND rev.date_created BETWEEN ?2 AND ?3
				ORDER BY rev.date_created ASC
			")
                ->setParameter(1, $agent)
                ->setParameter(2, $options['date_range']['start'])
                ->setParameter(3, $options['date_range']['end'])
            ;
        } else {
            $query = $this->_em->createQuery("
				SELECT rev
				FROM DeskPRO:{$class_name} rev INDEX BY log.id
				WHERE rev.person = ?1
				ORDER BY rev.date_created ASC
			")
                ->setParameter(1, $agent);
        }

        return $query->execute();
    }
}
