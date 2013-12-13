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

namespace Application\Migrations;

if (!defined('DP_ROOT')) exit('No access');

/**
 * Runs migrations
 */
class RunMigrations
{
	public function run()
	{
		if (isset($_REQUEST['run'])) {
			$this->runMigrations();
		} else {
			$this->runIntro();
		}
	}

	public function runIntro()
	{
		echo "<html><title>Run Migrations</title><body>";
		echo "<h1>Run Migrations</h1>";
		echo "<p>This will run through the developer migration scripts to bring your database up to the latest schema version.</p>";
		echo "<p>Running migrations may take some time. For simple changes, it will take around 10 seconds.</p>";
		echo "<p>You can also run migrations from the command-line:<br /><code>php app/cmd.php dpdev:do-migrations</code></p>";

		$url = $_SERVER['PHP_SELF'] . '?' . (!empty($_SERVER['QUERY_STRING']) ? str_replace('&', '&amp;', $_SERVER['QUERY_STRING']) : '') . '&amp;run';

		echo "<p>When you are ready to proceed, click the button below.<br /><a href=\"$url\">Click here to run the migration scripts now</a></p>";

		echo "</body></html>";
	}

	public function runMigrations()
	{
		global $DP_CONFIG;
		require DP_CONFIG_FILE;

		if (!isset($DP_CONFIG) || !is_array($DP_CONFIG)) {
			$DP_CONFIG = array();
		}

		if (!isset($DP_CONFIG['db'])) $DP_CONFIG['db'] = array();
		if (!isset($DP_CONFIG['db']['host']))      $DP_CONFIG['db']['host']      = DP_DATABASE_HOST;
		if (!isset($DP_CONFIG['db']['user']))      $DP_CONFIG['db']['user']      = DP_DATABASE_USER;
		if (!isset($DP_CONFIG['db']['password']))  $DP_CONFIG['db']['password']  = DP_DATABASE_PASSWORD;
		if (!isset($DP_CONFIG['db']['dbname']))    $DP_CONFIG['db']['dbname']    = DP_DATABASE_NAME;

		$env = 'dev';
		$debug = true;

		require DP_ROOT . '/sys/KernelBooter.php';
		$app = \DeskPRO\Kernel\KernelBooter::getCliApp($env, $debug);

		$argv = $_SERVER['argv'];
		array_shift($argv); // remove cron.php
		array_unshift($argv, 'cmd.php', 'dpdev:do-migration', '--no-interaction');
		$input = new \Symfony\Component\Console\Input\ArgvInput($argv);

		$output = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'w'), \Symfony\Component\Console\Output\StreamOutput::VERBOSITY_VERBOSE, null, $formatter = null);

		header('Content-Type: text/plain');
		header('Content-Disposition: inline; filename=migration.txt');

		$app->run($input, $output);
	}
}

$file_loader = new RunMigrations();
$file_loader->run();
