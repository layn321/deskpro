#!/usr/bin/env php
<?php
if (php_sapi_name() != 'cli') {
	echo "This script must only be run from the CLI.\n";
	echo "Contact support@deskpro.com if you require assistance.\n";
	exit(1);
}


define('DP_BUILDING', true);
define('DP_ROOT', realpath(__DIR__ . '/../../'));
define('DP_WEB_ROOT', realpath(__DIR__ . '/../../../'));
define('DP_CONFIG_FILE', DP_WEB_ROOT . '/config.php');

require DP_ROOT . '/vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\ClassLoader\ClassCollectionLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array('Orb' => DP_ROOT.'/src'));
$loader->registerNamespaces(array('Symfony' => DP_ROOT.'/vendor/symfony/src'));
$loader->register();

$file = DP_ROOT.'/sys/bootstrap.php';
if (file_exists($file)) {
    unlink($file);
}

ClassCollectionLoader::load(array(
    'Symfony\\Component\\DependencyInjection\\ContainerAwareInterface',
    // Cannot be included because annotations will parse the big compiled class file
    //'Symfony\\Component\\DependencyInjection\\ContainerAware',
    'Symfony\\Component\\DependencyInjection\\ContainerInterface',
    'Symfony\\Component\\DependencyInjection\\Container',
    'Symfony\\Component\\HttpKernel\\HttpKernelInterface',
    'Symfony\\Component\\HttpKernel\\KernelInterface',
    'Symfony\\Component\\HttpKernel\\Kernel',
    'Symfony\\Component\\ClassLoader\\ClassCollectionLoader',
    'Symfony\\Component\\ClassLoader\\UniversalClassLoader',
    'Symfony\\Component\\HttpKernel\\Bundle\\Bundle',
    'Symfony\\Component\\HttpKernel\\Bundle\\BundleInterface',
    'Symfony\\Component\\Config\\ConfigCache',
    // cannot be included as commands are discovered based on the path to this class via Reflection
    //'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle',

	'Orb\Util\ClassLoader',
), dirname($file), basename($file, '.php'), false, false, '.php');

file_put_contents($file, "<?php\n\nnamespace { require DP_ROOT.'/sys/autoload.php'; }\n\n".substr(file_get_contents($file), 5));
