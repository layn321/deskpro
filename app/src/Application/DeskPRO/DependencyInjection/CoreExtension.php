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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Application\DeskPRO\App;

/**
 * Registers basic core stuff
 */
class CoreExtension extends Extension
{
	public function load(array $config, ContainerBuilder $container)
    {
		$definition = new Definition('Application\\DeskPRO\\StaticLoader\\SystemEvents');
		$definition->addArgument(new Reference('event_dispatcher'));
		$container->setDefinition('deskpro.sys_events_loader', $definition);

		$definition = new Definition('Application\\DeskPRO\\ConfigServiceLoader');
		$container->setDefinition('deskpro.config_service_loader', $definition);

		$definition = new Definition('Symfony\\Component\\HttpFoundation\\Response');
		$container->setDefinition('response', $definition);

		$definition = new Definition('Application\\DeskPRO\\DBAL\\Logging\\SysQueryLogger');
		$container->setDefinition('deskpro.dbal.logger.query_logger', $definition);

		$definition = new Definition('Symfony\\Bridge\\Doctrine\\Logger\\DbalLogger', array(new Reference('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE)));
		$container->setDefinition('doctrine.query_logger', $definition);

		$definition = new Definition('Application\\DeskPRO\\CacheInvalidator\\QueryListener');
		$container->setDefinition('deskpro.cache.query_listener', $definition);

		$definition = new Definition('Application\\DeskPRO\\DBAL\\Logging\\CacheExec', array(new Reference('deskpro.cache.query_listener')));
		$container->setDefinition('deskpro.dbal.logger.cache_query_listener', $definition);

		$definition = new Definition('Application\\DeskPRO\\DBAL\\Logging\\DelegateLogger');
		$definition->addMethodCall('addLogger', array(new Reference('deskpro.dbal.logger.cache_query_listener'), 'cache_query_listener'));
		$definition->addMethodCall('addLogger', array(new Reference('deskpro.dbal.logger.query_logger'), 'query_logger'));
		$container->setDefinition('doctrine.dbal.logger', $definition);

		$definition = new Definition('Application\\DeskPRO\\Plugin\\PluginManager', array(new Reference('doctrine.orm.entity_manager')));
		$container->setDefinition('deskpro.plugin_manager', $definition);

		$definition = new Definition('Application\\DeskPRO\\Entity\\Person');
		$definition->setFactoryService('session')->setFactoryMethod('getPerson');
		$container->setDefinition('deskpro.session_person', $definition);

		$definition = new Definition('Application\\DeskPRO\\People\\ActivityLogger\\ActivityLogger', array(
			new Reference('doctrine.orm.entity_manager')
		));
		$container->setDefinition('deskpro.person_activity_logger', $definition);

		$this->loadInputReader($container);
		$this->loadTranslation($container);
		$this->loadSettings($container);
    }

	/**
	 * Sets up the translater
	 */
	protected function loadTranslation(ContainerBuilder $container)
	{
		// BundleLoader
		$definition = new Definition('Application\\DeskPRO\\Translate\\Loader\\SystemLoader', array(array(
			DP_ROOT . '/languages'
		)));
		$container->setDefinition('deskpro.core.translate_loader_system', $definition);

		// DbLoader
		$definition = new Definition('Application\\DeskPRO\\Translate\\Loader\\DbLoader', array(
			new Reference('database_connection')
		));
		$container->setDefinition('deskpro.core.translate_loader_db', $definition);

		// CombinationLoader
		$definition = new Definition('Application\\DeskPRO\\Translate\\Loader\\DeskproLoader');
		$definition->addMethodCall('setSystemLoader', array(new Reference('deskpro.core.translate_loader_system')));
		$definition->addMethodCall('setDbLoader', array(new Reference('deskpro.core.translate_loader_db')));
		$container->setDefinition('deskpro.core.translate_loader', $definition);

		// Add the cacher to the CombinationLoader if we want
		$definition->addMethodCall('setCache', array(new Reference('deskpro.cache.phrases', ContainerBuilder::IGNORE_ON_INVALID_REFERENCE)));

		// Now create the translate object
		$definition = new Definition('Application\\DeskPRO\\Translate\\Translate', array(
			new Reference('deskpro.core.translate_loader'),
			new Reference('event_dispatcher')
		));
		$definition->addMethodCall('setSession', array(new Reference('session')));
		$container->setDefinition('deskpro.core.translate', $definition);

		// Attach listener for no phrase
		$definition = $container->getDefinition('deskpro.sys_events_loader');
		$definition->addMethodCall('addNoPhraseEventListener');
	}


	/**
	 * Sets up the input reader
	 */
	protected function loadInputReader(ContainerBuilder $container)
	{
		// Init readers
		$definition = new Definition('Orb\Input\Reader\Source\Superglobal', array('_REQUEST'));
		$container->setDefinition('deskpro.core.input_reader_req', $definition);

		$definition = new Definition('Orb\Input\Reader\Source\Superglobal', array('_POST'));
		$container->setDefinition('deskpro.core.input_reader_post', $definition);

		$definition = new Definition('Orb\Input\Reader\Source\Superglobal', array('_GET'));
		$container->setDefinition('deskpro.core.input_reader_get', $definition);

		$definition = new Definition('Orb\Input\Reader\Source\Superglobal', array('_COOKIE'));
		$container->setDefinition('deskpro.core.input_reader_cookie', $definition);

		// Cleaner plugin: XssCleaner
		$definition = new Definition('Orb\Input\Cleaner\CleanerPlugin\BasicXss');
		$container->setDefinition('deskpro.core.input_cleaner_plugin_xss', $definition);

		// Cleaner plugin: HTML Purifier
		$definition = new Definition('Orb\Input\Cleaner\CleanerPlugin\HtmlPurifier');
		$container->setDefinition('deskpro.core.input_cleaner_plugin_html_purifier', $definition);

		// Init cleaner
		$definition = new Definition('Orb\Input\Cleaner\Cleaner');
		$definition->addMethodCall('addCleaner', array(new Reference('deskpro.core.input_cleaner_plugin_xss')));
		$definition->addMethodCall('addCleaner', array(new Reference('deskpro.core.input_cleaner_plugin_html_purifier')));
		$container->setDefinition('deskpro.core.input_cleaner', $definition);

		// Init reader
		$definition = new Definition('Orb\Input\Reader\Reader', array(new Reference('deskpro.core.input_cleaner')));
		$definition->addMethodCall('addSource', array('req', new Reference('deskpro.core.input_reader_req')));
		$definition->addMethodCall('addSource', array('post', new Reference('deskpro.core.input_reader_post')));
		$definition->addMethodCall('addSource',array('get', new Reference('deskpro.core.input_reader_get')));
		$definition->addMethodCall('addSource', array('cookie', new Reference('deskpro.core.input_reader_cookie')));
		$definition->addMethodCall('setArrayStringSeparator', array('.'));
		$container->setDefinition('deskpro.core.input_reader', $definition);
	}



	/**
	 * Sets up the settings loader
	 */
	protected function loadSettings(ContainerBuilder $container)
	{
		$definition = new Definition('Application\\DeskPRO\\Settings\\Settings', array(
			array(
				'core'  => DP_ROOT . '/src/Application/DeskPRO/Resources/settings',
				'agent' => DP_ROOT . '/src/Application/AgentBundle/Resources/settings',
				'user'  => DP_ROOT . '/src/Application/UserBundle/Resources/settings',
				'dev'   => DP_ROOT . '/src/Application/DevBundle/Resources/settings',
			),
			new Reference('database_connection')
		));
		$definition->addMethodCall('loadGroups', array('core'));
		$container->setDefinition('deskpro.core.settings', $definition);
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
        return 'deskpro_core';
    }
}
