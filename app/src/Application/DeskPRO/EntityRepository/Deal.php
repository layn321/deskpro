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
 * @copyright Copyright (c) 2011 DeskPRO (http://www.deskpro.com/)
 */

namespace Application\DeskPRO\EntityRepository;

use Symfony\Component\Validator\Constraints\DateTime;
use Application\DeskPRO\App;
use \Doctrine\ORM\EntityRepository;
use Application\DeskPRO\Entity;

class Deal extends AbstractEntityRepository
{

    /**
     * Find pending tasks assigned to the person.
     *
     * @param Person $person The person
     * @return Array
     */
    public function findDealsForPerson(Entity\Person $person, $status = 0) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(d) types, dt.name, dt.id')
                ->from('DeskPRO:Deal', 'd')
                ->innerJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->where('p.id = :person_id')
                ->andWhere('d.status = :status')
                ->groupBy('d.deal_type')
                ->setParameters(array('person_id' => $person['id'], 'status' => $status))
        ;
        $query = $qb->getQuery(); //print $query->getSQL();
        return $query->getScalarResult();
    }

    /**
     * Find pending tasks assigned to the person.
     *
     * @param Person $person The person
     * @return Array
     */
    public function findDealsForOther(Entity\Person $person, $status = 0) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(d) types, dt.name, dt.id')
                ->from('DeskPRO:Deal', 'd')
                ->leftJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->where('p.id IS NULL OR p.id != :person_id')
                ->andWhere('d.status = :status')
                ->andWhere('d.visibility > :visibility')
                ->setParameter('visibility', 0)
                ->groupBy('d.deal_type')
                ->setParameters(array('person_id' => $person['id'], 'status' => $status))
        ;
        $query = $qb->getQuery(); //print $query->getSQL();
        return $query->getScalarResult();
    }

    /**
     * Find pending tasks assigned to the person.
     *
     * @param Person $person The person
     * @return Array
     */
    public function countDealsForPerson(Entity\Person $person, $status = 0) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(d)')
                ->from('DeskPRO:Deal', 'd')
                ->innerJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->where('p.id = :person_id')
                ->andWhere('d.status = :status')
                //->groupBy('d.deal_type')
                ->setParameters(array('person_id' => $person['id'], 'status' => $status))
        ;
        $query = $qb->getQuery(); //print $query->getSQL();exit;
        return $query->getSingleScalarResult();
    }

    /**
     * Find pending tasks assigned to the person.
     *
     * @param Person $person The person
     * @return Array
     */
    public function countDealsForOther(Entity\Person $person, $status = 0) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(d)')
                ->from('DeskPRO:Deal', 'd')
                ->leftJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->where('p.id IS NULL OR p.id != :person_id')
                ->andWhere('d.status = :status')
                ->andWhere('d.visibility > :visibility')
                ->setParameter('visibility', 0)
                ->setParameters(array('person_id' => $person['id'], 'status' => $status))
        ;
        $query = $qb->getQuery(); //print $query->getSQL();
        return $query->getSingleScalarResult();
    }

    /**
     * Find pending tasks assigned to the person.
     *
     * @param Person $person The person
     * @return Array
     */
    public function filterDealsForPerson(Entity\Person $person, $status = 0, $deal_type_id = null, $order_by = 'date_created') {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
                ->from('DeskPRO:Deal', 'd')
                ->innerJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->leftJoin('d.deal_stage', 'ds')
                ->where('p.id = :person_id');
        $qb->setParameter('person_id', $person['id']);

        if ($status >= 0) {
            $qb->andWhere('d.status = :status');
            $qb->setParameter('status', $status);
        } else {
            $qb->andWhere('d.status > :status');
            $qb->setParameter('status', 0);
        }

        if ($deal_type_id) {
            $qb->andWhere('dt.id = :deal_type_id');
            $qb->setParameter('deal_type_id', $deal_type_id);
        }

        switch ($order_by) {
            case 'date_created':
                $qb->orderBy('d.date_created');
                break;
            case 'title':
                $qb->orderBy('d.title');
                break;
            case 'deal_size':
                $qb->orderBy('d.deal_value');
                break;
            case 'deal_type':
                $qb->orderBy('dt.name');
                break;
        }

        $query = $qb->getQuery(); //print $query->getSQL(); exit;
        return $query->getResult();
    }

    /**
     * Find pending tasks assigned to the person.
     *
     * @param Person $person The person
     * @return Array
     */
    public function filterDealsForOther(Entity\Person $person, $status = 0, $deal_type_id = null, $order_by = 'date_created') {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
                ->from('DeskPRO:Deal', 'd')
                ->leftJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->leftJoin('d.deal_stage', 'ds')
                ->where('p.id IS NULL OR p.id != :person_id')
                ->andWhere('d.visibility > :visibility')
                ->setParameter('visibility', 0)
                ->setParameter('person_id', $person['id'])               
                ;

        if ($status >= 0) {
            $qb->andWhere('d.status = :status');
            $qb->setParameter('status', $status);
        } else {
            $qb->andWhere('d.status > :status');
            $qb->setParameter('status', 0);
        }

        if ($deal_type_id) {
            $qb->andWhere('dt.id = :deal_type_id');
            $qb->setParameter('deal_type_id', $deal_type_id);
        }

        switch ($order_by) {
            case 'date_created':
                $qb->orderBy('d.date_created');
                break;
            case 'title':
                $qb->orderBy('d.title');
                break;
            case 'deal_size':
                $qb->orderBy('d.deal_value');
                break;
            case 'deal_type':
                $qb->orderBy('dt.name');
                break;
        }

        $query = $qb->getQuery(); //print $query->getSQL();
        return $query->getResult();
    }

    public function findPersonInDeal(Entity\Person $person, $deal_id = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(p)')
                ->from('DeskPRO:Deal', 'd')
                ->innerJoin('d.peoples', 'p')
                ->where('p.id = :person_id')
                ->andWhere('d.id = :deal_id')
                ->setParameters(array('person_id' => $person['id'], 'deal_id' => $deal_id))
        ;
        $query = $qb->getQuery(); //print $query->getSQL();exit;
        return $query->getSingleScalarResult();
    }

    public function findOrganizationInDeal(Entity\Organization $organization, $deal_id = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(o)')
                ->from('DeskPRO:Deal', 'd')
                ->innerJoin('d.organizations', 'o')
                ->where('o.id = :org_id')
                ->andWhere('d.id = :deal_id')
                ->setParameters(array('org_id' => $organization['id'], 'deal_id' => $deal_id))
        ;
        $query = $qb->getQuery(); 
        return $query->getSingleScalarResult();
    }

    /**
     * Group By deal accor to the filter.
     *
     * @param Entity\Person $person
     * @param <type> $status
     * @param <type> $deal_type_id
     * @param <type> $group_by
     * @return Collection
     */
    public function groupByDealsForPerson(Entity\Person $person, $status = 0, $deal_type_id = null, $group_by = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->from('DeskPRO:Deal', 'd')
                ->innerJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->leftJoin('d.deal_stage', 'ds')
                ->where('p.id = :person_id');

        $qb->setParameter('person_id', $person['id']);

        if ($status >= 0) {
            $qb->andWhere('d.status = :status');
            $qb->setParameter('status', $status);
        } else {
            $qb->andWhere('d.status > :status');
            $qb->setParameter('status', 0);
        }

        if ($deal_type_id) {
            $qb->andWhere('dt.id = :deal_type_id');
            $qb->setParameter('deal_type_id', $deal_type_id);
        }

        switch ($group_by) {
            case 'deal_stage':
                $qb->select('COUNT(d), ds.name AS name, ds.id AS id');
                $qb->groupBy('d.deal_stage');
                break;
            case 'deal_type':
                $qb->select('COUNT(d), dt.name AS name, dt.id AS id');
                $qb->groupBy('d.deal_type');
                break;
            default:
                $qb->select('COUNT(d)');
                break;
        }

        $query = $qb->getQuery(); //print $query->getSQL(); exit;
        return $query->getScalarResult();
    }

    /**
     * Find pending tasks assigned to the person.
     *
     * @param Person $person The person
     * @return Array
     */
    public function groupByDealsForOther(Entity\Person $person, $status = 0, $deal_type_id = null, $group_by = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
                ->from('DeskPRO:Deal', 'd')
                ->leftJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->leftJoin('d.deal_stage', 'ds')
                ->where('p.id IS NULL OR p.id != :person_id')
                ->andWhere('d.visibility > :visibility')
                ->setParameter('visibility', 0)
                ->setParameter('person_id', $person['id']);

        if ($status >= 0) {
            $qb->andWhere('d.status = :status');
            $qb->setParameter('status', $status);
        } else {
            $qb->andWhere('d.status > :status');
            $qb->setParameter('status', 0);
        }

        if ($deal_type_id) {
            $qb->andWhere('dt.id = :deal_type_id');
            $qb->setParameter('deal_type_id', $deal_type_id);
        }

        switch ($group_by) {
            case 'deal_stage':
                $qb->select('COUNT(d), ds.name AS name, ds.id AS id');
                $qb->groupBy('d.deal_stage');
                break;
            case 'deal_type':
                $qb->select('COUNT(d), dt.name AS name, dt.id AS id');
                $qb->groupBy('d.deal_type');
                break;
            case 'assigned_agent':
                $qb->select('COUNT(d), p.name AS name, p.id AS id');
                $qb->groupBy('d.assigned_agent');
                break;
            default:
                $qb->select('COUNT(d)');
                break;
        }

        $query = $qb->getQuery(); //print $query->getSQL(); exit;
        return $query->getScalarResult();
    }

    /**
     * Find pending tasks assigned to the person.
     *
     * @param Person $person The person
     * @return Array
     */
    public function filterGroupByDealsForPerson(Entity\Person $person, $status = 0, $deal_type_id = null, $group_by = null, $set_group_option = 0) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
                ->from('DeskPRO:Deal', 'd')
                ->innerJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->leftJoin('d.deal_stage', 'ds')
                ->where('p.id = :person_id');
        $qb->setParameter('person_id', $person['id']);
        $qb->orderBy('d.date_created');

        if ($status >= 0) {
            $qb->andWhere('d.status = :status');
            $qb->setParameter('status', $status);
        } else {
            $qb->andWhere('d.status > :status');
            $qb->setParameter('status', 0);
        }

        if ($deal_type_id) {
            $qb->andWhere('dt.id = :deal_type_id');
            $qb->setParameter('deal_type_id', $deal_type_id);
        }

        if ($set_group_option) {
            switch ($group_by) {
                case 'deal_stage':
                    $qb->andWhere('ds.id = :deal_stage');
                    $qb->setParameter('deal_stage', $set_group_option);
                    break;
                case 'deal_type':
                    $qb->andWhere('dt.id = :deal_type');
                    $qb->setParameter('deal_type', $set_group_option);
                    break;
            }
        }

        $query = $qb->getQuery(); //print $query->getSQL(); exit;
        return $query->getResult();
    }


    /**
     * Find pending tasks assigned to the person.
     *
     * @param Person $person The person
     * @return Array
     */
    public function filterGroupByDealsForOther(Entity\Person $person, $status = 0, $deal_type_id = null, $group_by = null, $set_group_option = 0) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
                ->from('DeskPRO:Deal', 'd')
                ->leftJoin('d.assigned_agent', 'p')
                ->innerJoin('d.deal_type', 'dt')
                ->leftJoin('d.deal_stage', 'ds')
                ->where('p.id IS NULL OR p.id != :person_id')
                ->andWhere('d.visibility > :visibility')
                ->setParameter('visibility', 0)
                ->setParameter('person_id', $person['id']);
        $qb->orderBy('d.date_created');

        if ($status >= 0) {
            $qb->andWhere('d.status = :status');
            $qb->setParameter('status', $status);
        } else {
            $qb->andWhere('d.status > :status');
            $qb->setParameter('status', 0);
        }

        if ($deal_type_id) {
            $qb->andWhere('dt.id = :deal_type_id');
            $qb->setParameter('deal_type_id', $deal_type_id);
        }

        if ($set_group_option) {
            switch ($group_by) {
                case 'deal_stage':
                        $qb->andWhere('ds.id = :deal_stage');
                        $qb->setParameter('deal_stage', $set_group_option);
                        break;
                case 'deal_type':
                    $qb->andWhere('dt.id = :deal_type');
                    $qb->setParameter('deal_type', $set_group_option);
                    break;
                case 'assigned_agent':
                   $qb->andWhere('p.id = :assigned_agent');
                    $qb->setParameter('assigned_agent', $set_group_option);
                    break;
            }
        }


        $query = $qb->getQuery(); //print $query->getSQL();
        return $query->getResult();
    }

}
