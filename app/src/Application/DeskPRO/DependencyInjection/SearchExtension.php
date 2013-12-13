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
 * @category DependencyInjection
 */

namespace Application\DeskPRO\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Application\DeskPRO\App;

class SearchExtension extends Extension
{
	public function load(array $config, ContainerBuilder $container)
    {
		$definition = new Definition('Application\\DeskPRO\\Search\\Adapter\\AbstractAdapter');
		$definition->setFactoryClass('Application\\DeskPRO\\StaticLoader\\SearchAdapter');
		$definition->setFactoryMethod('getSearchAdapter');
		$container->setDefinition('deskpro.search_adapter', $definition);

		$definition = new Definition('Application\\DeskPRO\\Search\\EntityListener', array(new Reference('deskpro.search_adapter')));
		$definition->addTag('kernel.listener', array('event' => 'Doctrine_onPostUpdate'));
		$definition->addTag('kernel.listener', array('event' => 'Doctrine_onPostPersist'));
		$definition->addTag('kernel.listener', array('event' => 'Doctrine_onPostRemove'));
		$container->setDefinition('deskpro.search_adapter_entity_listener', $definition);

		// Doctrine listener to support search engine
		$definition = new Definition('Application\\DeskPRO\\Search\\EntityWatcher\\EntityWatcher', array(new Reference('service_container')));
		$definition->addTag('doctrine.event_subscriber');
		$container->setDefinition('deskpro.search.entity_listener', $definition);
	}

	public function getXsdValidationBasePath()
	{
		return null;
	}

	public function getNamespace()
	{
		return null;
	}

	public function getAlias()
    {
        return 'deskpro_search';
    }
}
