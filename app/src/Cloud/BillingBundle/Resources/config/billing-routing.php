<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('billing', new Route(
	'/',
	array('_controller' => 'CloudBillingBundle:Main:index'),
	array(),
	array()
));

$collection->add('billing_sendq', new Route(
	'/billing-send-question',
	array('_controller' => 'CloudBillingBundle:SendFeedback:send'),
	array(),
	array()
));

$collection->add('billing_cancel', new Route(
	'/cancel-account/{authcode}',
	array('_controller' => 'CloudBillingBundle:Main:cancel'),
	array(),
	array()
));

################################################################################
# Login
################################################################################

$collection->add('billing_login_preload_sources', new Route(
	'/login/preload-sources',
	array('_controller' => 'CloudBillingBundle:Login:preloadSources'),
	array(),
	array()
));

$collection->add('billing_login', new Route(
	'/login',
	array('_controller' => 'CloudBillingBundle:Login:index'),
	array(),
	array()
));

$collection->add('billing_login_authenticate_local', new Route(
	'/login/authenticate-password',
	array('_controller' => 'CloudBillingBundle:Login:authenticateLocal', 'usersource_id' => 0),
	array(),
	array()
));

$collection->add('billing_send_lost', new Route(
	'/login/send-lost.json',
	array('_controller' => 'CloudBillingBundle:Login:sendResetPassword', '_format' => 'json'),
	array(),
	array()
));

return $collection;