<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->addCollection($loader->import(DP_ROOT.'/sys/config/agent/routing.php'));
$collection->addCollection($loader->import(DP_ROOT.'/src/Application/DevBundle/Resources/config/dev-routing.php'), '/dev');
$collection->addCollection($loader->import(DP_ROOT.'/vendor/symfony/src/Symfony/Bundle/WebProfilerBundle/Resources/config/routing/profiler.xml'), '_profiler');
$collection->addCollection($loader->import(DP_ROOT.'/vendor/symfony/src/Symfony/Bundle/WebProfilerBundle/Resources/config/routing/wdt.xml'), '_wdt');

return $collection;
