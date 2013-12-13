<?php

/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/


/**
 * DeskPRO
 *
 * @package DeskPRO
 */

namespace Application\DevBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;

use Orb\Util\Arrays;
use Orb\Util\Strings;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\Route;

class FindBadRoutesCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setDefinition(array(
		))->setName('dpdev:find-bad-routes');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$route_files = array(
			DP_ROOT  . '/src/Application/AdminBundle/Resources/config/admin-routing.php',
			DP_ROOT  . '/src/Application/AgentBundle/Resources/config/agent-routing.php',
			DP_ROOT  . '/src/Application/ApiBundle/Resources/config/api-routing.php',
			DP_ROOT  . '/src/Application/UserBundle/Resources/config/user-routing.php',
			DP_ROOT  . '/src/Application/ReportBundle/Resources/config/reports-routing.php',
			DP_ROOT  . '/src/Application/InstallBundle/Resources/config/install-routing.php',
			DP_ROOT  . '/src/Cloud/AdminBundle/Resources/config/admin-routing.php',
		);

		$bundles = array(
			'AdminBundle' => 'Application\\AdminBundle\\Controller',
			'AgentBundle' => 'Application\\AgentBundle\\Controller',
			'ApiBundle' => 'Application\\ApiBundle\\Controller',
			'UserBundle' => 'Application\\UserBundle\\Controller',
			'ReportBundle' => 'Application\\ReportBundle\\Controller',
			'InstallBundle' => 'Application\\InstallBundle\\Controller',
			'BillingBundle' => 'Application\\BillingBundle\\Controller',
			'DeskPRO' => 'Application\\DeskPRO\\Controller',
			'CloudAdminBundle' => 'Cloud\\AdminBundle\\Controller',
			'CloudBillingBundle' => 'Cloud\\BillingBundle\\Controller'
		);

		#----------------------------------------
		# Bad route to controller refs
		#----------------------------------------

		foreach ($route_files as $f) {
			/** @var $coll \Symfony\Component\Routing\RouteCollection */
			$coll = require($f);

			foreach ($coll as $name => $route) {

				if ($name == 'user_chat_widgetisavail') {
					continue;
				}

				/** @var $route \Symfony\Component\Routing\Route */
				$controller = $route->getDefault('_controller');

				$dead_str = basename($f) . ": " . $name . "\n";

				if (!$controller) {
					echo "No controller key: " . $dead_str;
					continue;
				}

				$controller = explode(':', $controller);
				if (count($controller) != 3) {
					echo "Bad controller key: " . $dead_str;
					continue;
				}

				if (!isset($bundles[$controller[0]])) {
					echo "Unknown bundle: " . $dead_str;
					continue;
				}

				$class = $bundles[$controller[0]] . '\\' . $controller[1] . 'Controller';

				if (!class_exists($class)) {
					echo "Bad class $class: " . $dead_str;
					continue;
				}

				try {
					$refl = new \ReflectionClass($class);
				} catch (\Exception $e) {
					echo "Error in source file: {$e->getMessage()}: " . $dead_str;
					continue;
				}

				if (!$refl->hasMethod($controller[2] . 'Action')) {
					echo "Bad method: " . $dead_str;
					continue;
				}
			}
		}

		#----------------------------------------
		# Missing routes for action methods
		#----------------------------------------

		$route_file_contents = array();
		foreach ($route_files as $f) {
			$route_file_contents[] = file_get_contents($f);
		}

		$controller_files = array(
			DP_ROOT  . '/src/Application/AdminBundle/Controller',
			DP_ROOT  . '/src/Application/AgentBundle/Controller',
			DP_ROOT  . '/src/Application/ApiBundle/Controller',
			DP_ROOT  . '/src/Application/UserBundle/Controller',
			DP_ROOT  . '/src/Application/ReportBundle/Controller',
			DP_ROOT  . '/src/Application/InstallBundle/Controller',
		);

		foreach ($controller_files as $c_dir) {
			$files = \Symfony\Component\Finder\Finder::create()->in($c_dir)->name("*.php")->files();

			foreach ($files as $f) {
				$class = Strings::extractRegexMatch("#((Application)/(.*?)/Controller/(.*?)).php$#", $f);
				$class = str_replace('/', '\\', $class);

				$symfony_name = str_replace('Application\\', '', $class);
				$symfony_name = str_replace('\\Controller\\', '\\', $symfony_name);
				$symfony_name = str_replace('\\', ':', $symfony_name);
				$symfony_name = preg_replace('#Controller$#', '', $symfony_name);

				try {
					$refl = new \ReflectionClass($class);
				} catch (\Exception $e) {
					echo "Bad source file {$e->getMessage()}: " . $f;
					echo "\n";
					continue;
				}

				if ($refl->isAbstract()) {
					continue;
				}

				$methods = $refl->getMethods(\ReflectionMethod::IS_PUBLIC);
				foreach ($methods as $method) {

					if ($method->name == 'DeskPRO_onControllerPreAction' || $method->name == 'DeskPRO_onControllerPostAction' || $method->name == 'preAction' || $method->name == 'postAction') {
						continue;
					}

					$name = Strings::extractRegexMatch('#^(.*?)Action$#', $method->name);
					if (!$name) {
						continue;
					}

					$symfony_action_name = $symfony_name . ':' . $name;
					$found = false;

					foreach ($route_file_contents as $f) {
						if (strpos($f, $symfony_action_name) !== false) {
							$found = true;
							break;
						}
					}

					if (!$found) {
						echo "Warning: Not found $class::{$method->name}: $symfony_action_name\n";
					}
				}
			}
		}
	}
}
