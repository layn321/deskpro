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
$container->setParameter('http_kernel.class', 'Application\\DeskPRO\\HttpKernel\\HttpKernel');
$container->setParameter('controller_resolver.class', 'Application\\DeskPRO\\HttpKernel\\Controller\\ControllerResolver');
$container->setParameter('session.class', 'Application\\DeskPRO\\HttpFoundation\\Session');
$container->setParameter('swiftmailer.class', 'Application\\DeskPRO\\Mail\\Mailer');
$container->setParameter('twig.loader.class', 'Application\\DeskPRO\\Twig\\Loader\\HybridLoader');
$container->setParameter('twig.class', 'Application\\DeskPRO\\Twig\\Environment');
$container->setParameter('file_locator.class', 'Application\\DeskPRO\\HttpKernel\\Config\\FileLocator');
$container->setParameter('routing.file_locator.class', 'Application\\DeskPRO\\HttpKernel\\Config\\FileLocator');
$container->setParameter('router.options.generator_class', 'Application\\DeskPRO\\Routing\\Generator\\UrlGenerator');
$container->setParameter('router.options.generator_base_class', 'Application\\DeskPRO\\Routing\\Generator\\UrlGenerator');
$container->setParameter('templating.globals.class', 'Application\\DeskPRO\\Templating\\GlobalVariables');
$container->setParameter('templating.asset.url_package.class', 'Application\\DeskPRO\\Templating\\Asset\\UrlPackage');
$container->setParameter('templating.asset.path_package.class', 'Application\\DeskPRO\\Templating\\Asset\\PathPackage');
$container->setParameter('templating.cache_warmer.template_paths.class', 'Application\\DeskPRO\\CacheWarmer\\TemplatePathsCacheWarmer');
$container->setParameter('router.class', 'Application\\DeskPRO\\Routing\\Router');
$container->setParameter('router.options.generator_dumper_class', 'Application\\DeskPRO\\Routing\\Generator\\Dumper\\PhpGeneratorDumper');
$container->setParameter('router.options.matcher_dumper_class', 'Application\\DeskPRO\\Routing\\Matcher\\Dumper\\PhpMatcherDumper');
$container->setParameter('router.options.generator_class', 'Application\\DeskPRO\\Routing\\Generator\\UrlGenerator');
$container->setParameter('router.options.generator_base_class', 'Application\\DeskPRO\\Routing\\Generator\\UrlGenerator');
$container->setParameter('doctrine.data_collector.class', 'Application\\DeskPRO\\Profiler\\DataCollector\\DoctrineDataCollector');
$container->setParameter('doctrine.orm.proxy_dir', '%kernel.cache_dir%../doctrine-proxies');
$container->setParameter('twig.options', array('cache' => '%kernel.cache_dir%../twig-compiled', 'charset' => 'UTF-8', 'debug' => '%kernel.debug%', 'auto_reload' => '%kernel.debug%'));
$container->setParameter('doctrine_migrations.dir_name', '%kernel.root_dir%/Resources/DoctrineMigrations');
$container->setParameter('doctrine_migrations.table_name', 'dev_migration_versions');
$container->setParameter('twig.extension.form.class', 'Application\\DeskPRO\\Twig\\Extension\\FormExtension');
$container->setParameter('doctrine.orm.entity_manager.class', 'Application\\DeskPRO\\ORM\\EntityManager');
$container->setParameter('templating.locator.class', 'Application\\DeskPRO\\Templating\\Loader\\TemplateLocator');
$container->setParameter('templating.engine.twig.class', 'Application\\DeskPRO\\Twig\\TwigEngine');
$container->setParameter('twig.cache_warmer.class', 'Application\\DeskPRO\\Twig\\CacheWarmer\\TemplateCacheCacheWarmer');
$container->setParameter('twig.extension.trans.class', 'Application\\DeskPRO\\Twig\\Extension\\TranslationExtension');
$container->setParameter('templating.engine.delegating.class', 'Application\\DeskPRO\\Templating\\Engine');

############################################################################
# Services
############################################################################

// templating.engine.jsonphp
$definition = new Definition();
$definition->setClass('Orb\\Templating\\Engine\\PhpVarJsonEngine');
$definition->setArguments(array(
	new Reference('templating.name_parser'),
	new Reference('service_container'),
	new Reference('templating.loader'),
	new Reference('templating.globals'),
));
$definition->addTag('templating.engine', array('alias' => 'jsonphp'));
$container->setDefinition('templating.engine.jsonphp', $definition);

// twig.helpers.deskpro_templating
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\Twig\\Extension\\TemplatingExtension');
$definition->setArguments(array(
	new Reference('service_container')
));
$definition->addTag('twig.extension', array());
$container->setDefinition('twig.helpers.deskpro_templating', $definition);

// twig.helpers.deskpro_user_templating
$definition = new Definition();
$definition->setClass('Application\\UserBundle\\Twig\\Extension\\UserTemplatingExtension');
$definition->setArguments(array(
	new Reference('service_container')
));
$definition->addTag('twig.extension', array());
$container->setDefinition('twig.helpers.deskpro_user_templating', $definition);

// session.storage
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\HttpFoundation\\SessionStorage\\SessionEntityStorage');
$definition->setArguments(array(
	new Reference('doctrine.orm.entity_manager'),
	'%session.storage.options%'
));
$container->setDefinition('session.storage', $definition);

// deskpro.mail_logger
$definition = new Definition();
$definition->setClass('Orb\\Log\\Logger');
$definition->setFactoryService(new Reference('service_container'));
$definition->setFactoryMethod('getSystemService');
$definition->setArguments(array('mail_logger'));
$container->setDefinition('deskpro.mail_logger', $definition);

// swiftmailer.mailer
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\Mail\\Mailer');
$definition->setArguments(array(
	new Reference('swiftmailer.transport'),
	new Reference('templating'),
	new Reference('deskpro.mail_logger')
));
$container->setDefinition('swiftmailer.mailer', $definition);

// swiftmailer.transport.dp_delegating
$definition = new Definition();
if (defined('DPC_IS_CLOUD')) {
	$definition->setClass('Cloud\\Mail\\Transport\\DelegatingTransport');
} else {
	$definition->setClass('Application\\DeskPRO\\Mail\\Transport\\DelegatingTransport');
}
$definition->setArguments(array(
	new Reference('swiftmailer.transport.eventdispatcher')
));
$container->setDefinition('swiftmailer.transport.dp_delegating', $definition);

// doctrine.dbal.connection_factory
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\DBAL\\ConnectionFactory');
$definition->setArguments(array(
	'%doctrine.dbal.connection_factory.types%'
));
$definition->addMethodCall('setContainer', array(new Reference('service_container')));
$container->setDefinition('doctrine.dbal.connection_factory', $definition);

// doctrine.orm.default_query_cache
$definition = new Definition();
$definition->setClass('Orb\\Doctrine\\Common\\Cache\\ArrayFileCache');
$definition->setFactoryClass('Application\\DeskPRO\\DependencyInjection\\SystemServices\\ArrayFileCacheFactory');
$definition->setFactoryMethod('create');
$definition->setArguments(array('dql'));
$definition->addMethodCall('registerShutdownCommit');
$container->setDefinition('doctrine.orm.default_query_cache', $definition);

// deskpro.exception_logger
$definition = new Definition();
$definition->setClass('Application\DeskPRO\HttpKernel\ExceptionListener');
$definition->addTag('kernel.event_listener', array('event' => 'kernel.exception', 'method' => 'onKernelException', 'priority' => -128));
$container->setDefinition('deskpro.exception_logger', $definition);

// deskpro.profiler.request_matcher
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\Profiler\\RequestMatcher');
$container->setDefinition('deskpro.profiler.request_matcher', $definition);

// deskpro.interface_value
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\InterfaceValue');
$container->setDefinition('deskpro.interface_value', $definition);

// doctrine.orm.default_result_cache
$definition = new Definition();
$definition->setClass('Orb\\Doctrine\\Common\\Cache\\PreloadedMysqlCache');
$definition->setArguments(array(
	new Reference('database_connection')
));
$definition->addMethodCall('setPrefix', array('dres', new Reference('deskpro.interface_value')));
$container->setDefinition('default_result_cache', $definition);

// validator.mapping.loader.loader_chain
$definition = new Definition();
$definition->setClass('%validator.mapping.loader.loader_chain.class%');
$definition->setArguments(array(array(
	new Reference('validator.mapping.loader.static_method_loader')
)));
$container->setDefinition('validator.mapping.loader.loader_chain', $definition);

// deskpro.search_index.entity_updater_listener
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\Entity\\EventListener\\SearchUpdater');
$definition->setArguments(array(
	new Reference('service_container')
));
$definition->addTag('doctrine.event_subscriber', array(
	'connection' => 'default',
));
$container->setDefinition('deskpro.search_index.entity_updater_listener', $definition);

// browser_sniffer
$definition = new Definition();
$definition->setClass('Browser');
$container->setDefinition('browser_sniffer', $definition);

// deskpro.service_urls
$definition = new Definition();
$definition->setClass('Application\\DeskPRO\\Settings\\ServiceUrls');
$definition->addMethodCall('loadPack', array('%kernel.root_dir%/config/service-urls.php'));
$container->setDefinition('deskpro.service_urls', $definition);

############################################################################
# Framework Configuration
############################################################################

$container->loadFromExtension('framework', array(
	'charset' => 'UTF-8',
	'secret' => 'mube224etsmhxky1gvwixc4b',
	'templating' => array(
		'engines' => array('twig', 'php', 'jsonphp'),
		'assets_base_urls' => 'CONFIG_HTTP'
	),
	'validation' => array('enabled' => true),
	'session' => array(
		'default_locale' => 'en',
		'lifetime' => 3600,
		'auto_start' => true
	),
	'form' => array('enabled' => true)
));

// Monolog default logging, turn off unless specifically enabled (eg in some _dev configs)
$container->loadFromExtension('monolog', array(
	'handlers' => array(
		'main' => array(
			'type' => 'null',
		)
	)
));


############################################################################
# Twig Configuration
############################################################################

$container->loadFromExtension('twig', array(
	'form' => array(
		'resources' => array(
			'DeskPRO:Form:form_div_layout.html.twig'
		)
	)
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
			'default' => array('host' => 'from_user_config.db', 'logging' => true),
			'read' => array('host' => 'from_user_config.db_read', 'logging' => true)
		)
	)
));


############################################################################
# Swiftmailer Configuration
############################################################################

$container->loadFromExtension('swiftmailer', array(
	'transport' => 'swiftmailer.transport.dp_delegating'
));


############################################################################
# DeskPRO Configuration
############################################################################

$container->loadFromExtension('deskpro_core', array());
$container->loadFromExtension('deskpro_search', array());
