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

namespace DeskPRO\Kernel;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

use Application\DeskPRO\App;

class CliKernel extends AgentKernel
{
	protected function registerAdditionalBundles()
	{
		$bundles = parent::registerAdditionalBundles();
		$bundles[] = new \Symfony\Bundle\DoctrineMigrationsBundle\DoctrineMigrationsBundle();

		return $bundles;
	}

	public function boot($mode = 'cli')
	{
		parent::boot();

		if ($mode == 'cron' || in_array('dp:upgrade', $_SERVER['argv']) || in_array('dp:import', $_SERVER['argv'])) {
			$this->runCronBootChecks();
		}
	}

	protected function runCronBootChecks()
	{
		$server_check = new \Application\InstallBundle\Install\ServerChecks();
		$server_check->setMode('cron');
		$server_check->checkServer();

		try {
			$server_check->checkDatabase(null);
		} catch (\Exception $e) {}

		if ($server_check->hasFatalErrors()) {
			$this->handleCronBootErrors($server_check);
			exit(1);
		}
	}

	protected function handleCronBootErrors(\Application\InstallBundle\Install\ServerChecks $server_check)
	{
		$errors = $server_check->getFatalErrors();

		$error_messages = array();
		$error_codes = array();
		foreach ($errors as $k => $e) {
			$error_messages[] = $e['message'];
			$error_codes[] = $k;
		}

		$msg = "There are problems with your server that prevent DeskPRO from executing this command:\n\n";
		$msg .= '- ' . implode("\n- ", $error_messages);
		$msg .= "\n\n###\n\n";

		foreach ($error_codes as $code) {
			$msg .= "error:$code\n";
		}

		$ini_path = deskpro_install_guess_phpini_path();
		if ($ini_path) {
			$msg .= "ini_path: $ini_path\n";
		}

		echo $msg;

		$db_write = false;
		if (!$server_check->hasErrorType('pdo_ext') && !$server_check->hasErrorType('pdo_mysql_ext') && !$server_check->hasDbErrors()) {
			try {
				$db = App::getDb();
				$db->replace('install_data', array(
					'build' => 1,
					'name'  => 'cron_run_errors',
					'data' => $msg
				));
				$db_write = true;
			} catch (\Exception $e) {
				$db_write = false;
			}
		}

		if (!$db_write) {
			@file_put_contents($this->getLogDir() .'/cron-boot-errors.log', $msg);
		}
	}
}