<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('billing', new Route(
	'/',
	array('_controller' => 'BillingBundle:Main:index'),
	array(),
	array()
));

$collection->add('billing_login', new Route(
	'/login',
	array('_controller' => 'BillingBundle:Login:index'),
	array(),
	array()
));

$collection->add('billing_logout', new Route(
	'/logout/{auth}',
	array('_controller' => 'BillingBundle:Login:logout'),
	array(),
	array()
));

$collection->add('billing_login_authenticate_local', new Route(
	'/login/authenticate-password',
	array('_controller' => 'BillingBundle:Login:authenticateLocal', 'usersource_id' => 0),
	array(),
	array()
));

$collection->add('billing_login_ma_login', new Route(
	'/login/verity-ma-login/{license_id}/{code}',
	array('_controller' => 'BillingBundle:Login:verifyMaLoginRequest'),
	array(),
	array()
));

################################################################################
# License
################################################################################

$collection->add('billing_license_reqdemo', new Route(
	'/license/generate-demo',
	array('_controller' => 'BillingBundle:License:requestDemo'),
	array(),
	array()
));

$collection->add('billing_license_input_save', new Route(
	'/license/input/save',
	array('_controller' => 'BillingBundle:License:saveNewLicense'),
	array(),
	array()
));

$collection->add('billing_license_keyfile', new Route(
	'/license/download/deskpro-license-sign.key',
	array('_controller' => 'BillingBundle:License:keyFile'),
	array(),
	array()
));

return $collection;