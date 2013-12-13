<?php if (!defined('DP_ROOT')) exit('No access');
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Resource\FileResource;


############################################################################
# Parameters
############################################################################

$container->setParameter('kernel.include_core_classes', false);
$container->setParameter('routing.file_locator.class', 'Application\\DeskPRO\\HttpKernel\\Config\\FileLocator');
$container->setParameter('templating.cache_warmer.template_paths.class', 'Application\\DeskPRO\\CacheWarmer\\TemplatePathsCacheWarmer');
$container->setParameter('doctrine.orm.proxy_dir', '%kernel.cache_dir%../doctrine-proxies');
$container->setParameter('doctrine.orm.entity_manager.class', 'Application\\DeskPRO\\ORM\\EntityManager');


############################################################################
# Services
############################################################################

// doctrine.dbal.connection_factory
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\DBAL\\ConnectionFactory');
$definition->setArguments(array(
	'%doctrine.dbal.connection_factory.types%'
));
$definition->addMethodCall('setContainer', array(new Reference('service_container')));
$container->setDefinition('doctrine.dbal.connection_factory', $definition);

// deskpro.interface_value
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\InterfaceValue');
$container->setDefinition('deskpro.interface_value', $definition);

// deskpro.profiler.request_matcher
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\Profiler\\RequestMatcher');
$container->setDefinition('deskpro.profiler.request_matcher', $definition);

// deskpro.service_urls
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\Settings\\ServiceUrls');
$definition->addMethodCall('loadPack', array('%kernel.root_dir%/config/service-urls.php'));
$container->setDefinition('deskpro.service_urls', $definition);

$definition = new Definition('Application\\DeskPRO\\Translate\\Loader\\SystemLoader', array(array(DP_ROOT . '/languages')));
$container->setDefinition('deskpro.core.translate_loader_system', $definition);

$definition = new Definition('Application\\DeskPRO\\Translate\\Loader\\DeskproLoader');
$definition->addMethodCall('setSystemLoader', array(new Reference('deskpro.core.translate_loader_system')));
$container->setDefinition('deskpro.core.translate_loader', $definition);

// Now create the translate object
$definition = new Definition('Application\\DeskPRO\\Translate\\Translate', array(
	new Reference('deskpro.core.translate_loader'),
	new Reference('event_dispatcher')
));
$container->setDefinition('deskpro.core.translate', $definition);


############################################################################
# Framework Configuration
############################################################################

$container->loadFromExtension('framework', array(
	'router' => array(
		'resource' => DP_ROOT.'/sys/config/install/routing.php'
	),
	'charset' => 'UTF-8',
	'secret' => 'mube224etsmhxky1gvwixc4b',
	'templating' => array(
		'engines' => array('php'),
		'assets_base_urls' => 'CONFIG_HTTP'
	),
	'validation' => array('enabled' => true),
	'session' => array(
		'default_locale' => 'en',
		'lifetime' => 3600,
	),
	'form' => array('enabled' => true)
));


############################################################################
# Doctrine Configuration
############################################################################

$container->loadFromExtension('doctrine', array(
	'orm' => array(
		'auto_generate_proxy_classes' => false,
		'default_entity_manager' => 'default',
		'entity_managers' => array(
			'default' => array('mappings' => array('DeskPRO' => array('type' => 'staticphp')), 'class_metadata_factory_name' => 'Orb\\Doctrine\\ORM\\Mapping\\StaticClassMetadataFactory')
		)
	),
	'dbal' => array(
		'default_connection' => 'default',
		'connections' => array(
			'default' => array('host' => 'from_user_config.db', 'logging' => true)
		)
	)
));


############################################################################
# DeskPRO Configuration
############################################################################

$container->loadFromExtension('install', array(

));

