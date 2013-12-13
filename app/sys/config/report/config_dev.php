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
		'resource' => DP_ROOT.'/sys/config/report/routing.php'
	),
	'profiler' => array(
		'only_exceptions' => true
	)
));

$container->loadFromExtension('twig', array(
	'debug' => true
));
