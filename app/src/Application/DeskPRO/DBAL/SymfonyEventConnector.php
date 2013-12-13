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
class SymfonyEventConnector implements \Doctrine\Common\EventSubscriber
{
	/**
	 * @var \Symfony\Bundle\FrameworkBundle\ContainerAwareEventDispatcher
	 */
	protected $event_dispatcher;

	/**
	 * @param \Symfony\Bundle\FrameworkBundle\ContainerAwareEventDispatcher $event_dispatcher
	 */
	public function __construct(ContainerAwareEventDispatcher $event_dispatcher)
	{
		$this->event_dispatcher = $event_dispatcher;
	}

	public function preRemove($event)
	{
		$event = new DoctrineEvent('preRemove', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPreRemove', $event);
	}

	public function postRemove($event)
	{
		$event = new DoctrineEvent('postRemove', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPostRemove', $event);
	}

	public function prePersist($event)
	{
		$event = new DoctrineEvent('prePersist', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPrePersist', $event);
	}

	public function postPersist($event)
	{
		$event = new DoctrineEvent('postPersist', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPostPersist', $event);
	}

	public function preUpdate($event)
	{
		$event = new DoctrineEvent('preUpdate', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPreUpdate', $event);
	}

	public function postUpdate($event)
	{
		$event = new DoctrineEvent('postUpdate', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPostUpdate', $event);
	}

	public function postLoad($event)
	{
		$event = new DoctrineEvent('postLoad', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPostLoad', $event);
	}

	public function loadClassMetadata($event)
	{
		$event = new DoctrineEvent('loadClassMetadata', $event);
		$this->event_dispatcher->dispatch('Doctrine_onLoadClassMetadata', $event);
	}

	public function onFlush($event)
	{
		$event = new DoctrineEvent('onFlush', $event);
		$this->event_dispatcher->dispatch('Doctrine_onFlush', $event);
	}

	public function getSubscribedEvents()
	{
		return array(
			'preRemove',
			'postRemove',
			'prePersist',
			'postPersist',
			'preUpdate',
			'postUpdate',
			'postLoad',
			'loadClassMetadata',
			'onFlush'
		);
	}
}
