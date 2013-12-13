<?php if (!defined('DP_ROOT')) exit('No access');
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Resource\FileResource;

$loader->import(DP_ROOT.'/sys/config/config.php');
$container->loadFromExtension('framework', array(
	'router' => array(
		'resource' => DP_ROOT.'/sys/config-cloud/admin/routing.php'
	)
));

// twig.helpers.deskpro_user_templating
// We ned it in the admin because we need it for compiling when editing templates
$definition = new Definition();
$definition->setClass('Application\\UserBundle\\Twig\\Extension\\UserTemplatingExtension');
$definition->setArguments(array(
	new Reference('service_container')
));
$definition->addTag('twig.extension', array());
$container->setDefinition('twig.helpers.deskpro_user_templating', $definition);