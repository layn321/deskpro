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

namespace Application\DeskPRO\ORM\EventListener;

use \Doctrine\ORM\Events;
use \Doctrine\ORM\Event\LifecycleEventArgs;
use \Doctrine\Common\EventSubscriber;

/**
 * This listener automatically sets the container once an ORM entity has been laoded.
 */
class SetContainerListener implements EventSubscriber
{
	protected $container;

	public function __construct(\Symfony\Component\DependencyInjection\Container $container)
	{
		$this->container = $container;
	}

	public function getSubscribedEvents()
	{
	   return array(Events::postLoad);
	}

	public function postLoad(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if ($entity instanceof Entity) {
			if (!$entity->hasContainer()) {
				$entity->setContainer($container);
			}
		}
	}
}
