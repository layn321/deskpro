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
 * @category DBAL
 */

namespace Application\DeskPRO\DBAL;

use Symfony\Bundle\FrameworkBundle\ContainerAwareEventDispatcher;

use \Doctrine\ORM\Event\LifecycleEventArgs;
use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use \Doctrine\ORM\Event\PreUpdateEventArgs;
use \Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * This connects some of the Doctrine events to the symfony event dispatcher
 */
class DoctrineEvent extends \Symfony\Component\EventDispatcher\Event
{
	/**
	 * @var mixed
	 */
	protected $doctrine_event;

	/**
	 * @var string
	 */
	protected $event_type;

	/**
	 * @var mixed
	 */
	protected $entity;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $entity_manager;

	public function __construct($event_type, $doctrine_event)
	{
		$this->event_type     = $event_type;
		$this->doctrine_event = $doctrine_event;
		$this->entity_manager = $doctrine_event->getEntityManager();
		$this->entity         = null;

		if ($doctrine_event instanceof LifecycleEventArgs OR $doctrine_event instanceof PreUpdateEventArgs) {
			$this->entity = $doctrine_event->getEntity();
		}
	}


	/**
	 * The entity, or null if the event type doesnt have an entity
	 *
	 * @return mixed
	 */
	public function getEntity()
	{
		return $this->entity;
	}


	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entity_manager;
	}


	/**
	 * @return string
	 */
	public function getEventType()
	{
		return $this->event_type;
	}


	/**
	 * @return mixed
	 */
	public function getDoctrineEvent()
	{
		return $this->doctrine_event;
	}
}
