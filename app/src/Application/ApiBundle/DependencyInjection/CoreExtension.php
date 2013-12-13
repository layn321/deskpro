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

namespace Application\ApiBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Application\DeskPRO\App;

/**
 * Registers basic core stuff
 */
class CoreExtension extends Extension
{
	public function load(array $config, ContainerBuilder $container)
    {
		$definition = new Definition('Application\\ApiBundle\\StaticLoader\\RequestKey');
	    $definition->setFactoryClass('Application\\ApiBundle\\StaticLoader\\RequestKey');
		$definition->setFactoryMethod('getApiKeyFromRequest');
		$container->setDefinition('deskpro.api.request_key', $definition);

		$definition = new Definition('Application\\ApiBundle\\StaticLoader\\RequestKey');
	    $definition->setFactoryClass('Application\\ApiBundle\\StaticLoader\\RequestKey');
		$definition->setFactoryMethod('getApiTokenFromRequest');
		$container->setDefinition('deskpro.api.request_token', $definition);
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
        return 'deskpro_api_core';
    }
}
