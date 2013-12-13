<?php if (!defined('DP_ROOT')) exit('No access');
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Resource\FileResource;

$loader->import(DP_ROOT.'/sys/config/config.php');

$container->setParameter('kernel.debug', true);

$container->loadFromExtension('framework', array(
	'router' => array(
		'resource' => DP_ROOT.'/sys/config/user/routing_dev.php'
	),
	'profiler' => array(
		'only_exceptions' => false,
		'matcher' => array('service' => 'deskpro.profiler.request_matcher')
	),
));

$container->loadFromExtension('web_profiler', array(
	'toolbar' => true,
	'intercept_redirects' => false,
	'verbose' => true
));

// twig.helpers.deskpro_user_templating
$definition = new Definition();
$definition->setClass('Application\\UserBundle\\Twig\\Extension\\UserTemplatingExtension');
$definition->setArguments(array(
	new Reference('service_container')
));
$definition->addTag('twig.extension', array());
$container->setDefinition('twig.helpers.deskpro_user_templating', $definition);

$container->loadFromExtension('twig', array(
	'debug' => true
));

$container->loadFromExtension('monolog', array(
	'handlers' => array(
		'main' => array(
			'type' => 'stream',
			'path' => '%kernel.logs_dir%/%kernel.environment%.user.log',
			'level' => 'WARNING'
		)
	)
));