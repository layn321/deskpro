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
 * @category ORM
 */

namespace Application\DeskPRO\Entity\EventListener;

use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;

/**
 * This listens for Doctrine events on objects we care about
 */
class SearchUpdater implements EventSubscriber
{
	protected $container;
	protected $is_running = false;

	public function __construct(\Symfony\Component\DependencyInjection\Container $container)
	{
		$this->container = $container;
	}

	public function getSubscribedEvents()
	{
		return array(
			Events::postPersist,
			Events::postUpdate,
			Events::postRemove,
		);
	}

	public function postPersist(LifecycleEventArgs $eventArgs)
	{
		if ($this->is_running) return;
		$this->is_running = true;

		$entity = $eventArgs->getEntity();
		if (
			$entity instanceof \Application\DeskPRO\Entity\Article ||
			$entity instanceof \Application\DeskPRO\Entity\Download ||
			$entity instanceof \Application\DeskPRO\Entity\Feedback ||
			$entity instanceof \Application\DeskPRO\Entity\News
		) {
			$this->container->getSystemService('search_indexer')->update($entity, 'update');
		}

		$this->is_running = false;
	}

	public function postUpdate(LifecycleEventArgs $eventArgs)
	{
		if ($this->is_running) return;
		$this->is_running = true;

		$entity = $eventArgs->getEntity();
		if (
			$entity instanceof \Application\DeskPRO\Entity\Article ||
			$entity instanceof \Application\DeskPRO\Entity\Download ||
			$entity instanceof \Application\DeskPRO\Entity\Feedback ||
			$entity instanceof \Application\DeskPRO\Entity\News
		) {
			$this->container->getSystemService('search_indexer')->update($entity, 'update');
		}

		$this->is_running = false;
	}

	public function postRemove(LifecycleEventArgs $eventArgs)
	{
		if ($this->is_running) return;
		$this->is_running = true;

		$entity = $eventArgs->getEntity();
		if (
			$entity instanceof \Application\DeskPRO\Entity\Article ||
			$entity instanceof \Application\DeskPRO\Entity\Download ||
			$entity instanceof \Application\DeskPRO\Entity\Feedback ||
			$entity instanceof \Application\DeskPRO\Entity\News
		) {
			$this->container->getSystemService('search_indexer')->update($entity, 'delete');
		}

		$this->is_running = false;
	}
}
