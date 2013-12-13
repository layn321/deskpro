<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('install_checks', new Route(
	'/server-checks',
	array('_controller' => 'InstallBundle:Install:index'),
	array(),
	array()
));

$collection->add('install_check_urls', new Route(
	'/url-rewriting-check',
	array('_controller' => 'InstallBundle:Install:installRewriteCheck'),
	array(),
	array()
));

$collection->add('install_license', new Route(
	'/',
	array('_controller' => 'InstallBundle:Install:license'),
	array(),
	array()
));

$collection->add('install_configedit', new Route(
	'/config-editor',
	array('_controller' => 'InstallBundle:Install:configEditor'),
	array(),
	array()
));

$collection->add('install', new Route(
	'/',
	array('_controller' => 'InstallBundle:Install:license'),
	array(),
	array()
));

$collection->add('install_verify_files', new Route(
	'/verify-files',
	array('_controller' => 'InstallBundle:Install:verifyFiles'),
	array(),
	array()
));

$collection->add('install_verify_files_do', new Route(
	'/verify-files/do/{batch}',
	array('_controller' => 'InstallBundle:Install:doVerifyFiles', 'batch' => 0),
	array(),
	array()
));

$collection->add('install_create_tables', new Route(
	'/install-database',
	array('_controller' => 'InstallBundle:Install:createTables'),
	array(),
	array()
));

$collection->add('install_create_tables_do', new Route(
	'/install-database/do/{batch}',
	array('_controller' => 'InstallBundle:Install:doCreateTables', 'batch' => 0),
	array(),
	array()
));

$collection->add('install_install_data', new Route(
	'/install-data',
	array('_controller' => 'InstallBundle:Install:installData'),
	array(),
	array()
));

$collection->add('install_install_data_save', new Route(
	'/install-data/save',
	array('_controller' => 'InstallBundle:Install:installDataSave'),
	array(),
	array()
));

$collection->add('install_install_done', new Route(
	'/install-done',
	array('_controller' => 'InstallBundle:Install:installDone'),
	array(),
	array()
));

$collection->add('install_send_install_report_error', new Route(
	'/install-report-error',
	array('_controller' => 'InstallBundle:Install:sendInstallReportError'),
	array(),
	array()
));

return $collection;
