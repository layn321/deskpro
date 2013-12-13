<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('api_dpc_call_ping', new Route(
	'/dpc-call/ping',
	array('_controller' => 'CloudApiBundle:CloudCall:ping'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_dpc_call_resetpass', new Route(
	'/dpc-call/reset-password/{person_id}',
	array('_controller' => 'CloudApiBundle:CloudCall:resetPassword'),
	array(),
	array()
));

return $collection;