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

namespace Application\DeskPRO\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

class DevPagelogCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:pagelog');
		$this->addArgument('action', InputArgument::OPTIONAL, 'What to do?', 'help');
		$this->addOption('file', null, InputOption::VALUE_REQUIRED,  '[load] The file to load');
		$this->addOption('type', null, InputOption::VALUE_REQUIRED,  '[group] The type of URL to group on');
		$this->addOption('var', null, InputOption::VALUE_REQUIRED,  '[group] The variable to group on ');
	}

	protected $_data_cache = array();

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		set_time_limit(0);

		switch ($input->getArgument('action')) {
			case 'help':
				$output->write($this->getHelp());
				return 0;

			case 'count':
				$count = $this->getSqliteConnection()->fetchColumn("SELECT COUNT(*) FROM pagelog");
				echo "There are $count loaded page logs\n";
				return 0;

			case 'group':
				return $this->viewGroupedAction($input, $output);

			case 'load':
				return $this->loadAction($input, $output);

			default:
				echo "Unknown action";
				return 1;
		}
	}

	/**
	 * @return \Application\DeskPRO\DBAL\Connection
	 */
	protected function getSqliteConnection()
	{
		static $conn;

		if (!$conn) {
			/** @var $conn \Application\DeskPRO\DBAL\Connection */
			$conn = \Doctrine\DBAL\DriverManager::getConnection(array(
				'driver'       => 'pdo_sqlite',
				'path'         => dp_get_data_dir() . '/log-analytics.sqlite'
			));

			if (!$conn->getSchemaManager()->tablesExist('pagelog')) {
				$conn->exec('
					CREATE  TABLE "pagelog" (
						"id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL ,
						"url" VARCHAR NOT NULL ,
						"url_noparams" VARCHAR NOT NULL ,
						"url_noaccount" VARCHAR NOT NULL ,
						"url_noaccount_noparams" VARCHAR NOT NULL ,
						"time_total" FLOAT NOT NULL ,
						"time_php" FLOAT NOT NULL ,
						"time_db" FLOAT NOT NULL ,
						"query_count" FLOAT NOT NULL ,
						"peak_memory" INTEGER NOT NULL ,
						"hit_at" DATETIME NOT NULL
					);
				');
			}
		}

		return $conn;
	}

	protected function viewGroupedAction(InputInterface $input, OutputInterface $output)
	{
		$url_type = $input->getOption('type');
		if (!$url_type) {
			$url_type = 'raw';
		}

		$url_field = '';

		switch ($url_type) {
			case 'raw': $url_field = 'url'; break;
			case 'noparams': $url_field = 'url_noparams'; break;
			case 'noaccount': $url_field = 'url_noaccount'; break;
			case 'noaccount_noparams': $url_field = 'url_noaccount_noparams'; break;
			default:
				echo "Invalid type";
				return 1;
		}

		$group_var = $input->getOption('var');
		if (!$group_var) {
			$group_var = 'time';
		}

		$group_field = '';

		switch ($group_var) {
			case 'time': $group_field = 'time_total'; break;
			case 'time_php': $group_field = 'time_php'; break;
			case 'time_db': $group_field = 'time_db'; break;
			case 'queries': $group_field = 'query_count'; break;
			case 'memory': $group_field = 'peak_memory'; break;
			case 'count': $group_field = 'COUNT(*)'; break;
			default: echo "Invalid var."; return 1;
		}

		$query = "SELECT $url_field AS urlfield, $group_field AS groupfield FROM pagelog GROUP BY urlfield ORDER BY groupfield DESC LIMIT 2000";
		$data = $this->getSqliteConnection()->fetchAll($query);

		foreach ($data as $r) {
			echo sprintf("%-10s %s\n", $r['groupfield'], $r['urlfield']);
		}

		echo "\n";
		return 0;
	}

	protected function loadAction(InputInterface $input, OutputInterface $output)
	{
		$log_path = $input->getOption('file');
		if (!is_file($log_path)) {
			$output->writeln('<error>Unknown file</error>');
			return 1;
		}

		$fh = fopen($log_path, 'r');

		$count = 0;
		while (!feof($fh)) {
			$count++;
			$line = fgets($fh);
			$m = null;

			if (!preg_match('#^\[(.*?)\]\s+Time: (\d+\.\d+)\s+PHP_Time: (\d+\.\d+)\s+DB_Time: (\d+\.\d+)\s+Query_Count: (\d+)\s+Peak_Memory: (\d+)\s+URL: (.*?)$#', $line, $m)) {
				continue;
			}

			$date        = $m[1];
			$time_total  = $m[2];
			$time_php    = $m[3];
			$time_db     = $m[4];
			$query_count = $m[4];
			$peak_memory = $m[4];
			$url         = $m[7];

			$url = preg_replace('#\?v=[0-9]+#', '', $url);

			if (strpos($url, 'chat/poll') !== false) {
				$url_no_nums = preg_replace('#chat/poll/.*?$#', 'chat/poll', $url);
			} elseif (strpos($url, 'similar-to/articlecontent') !== null) {
				$url_no_nums = preg_replace('#similar-to/articlecontent/.*?$#', 'similar-to/articlecontent', $url);
			} elseif (strpos($url, 'search/articlecontent') !== null) {
				$url_no_nums = preg_replace('#search/articlecontent/.*?$#', 'search/articlecontent', $url);
			} else {
				$url_no_nums = preg_replace('#/[0-9]+$#', '', $url);
				$url_no_nums = preg_replace('#/[0-9]+\-[a-zA-Z0-9_\-]+$#', '', $url_no_nums);
			}

			$url_noaccount = preg_replace('#^https?://(.*?)/(.*?)$#', '$2', $url);
			$url_noaccount_nonums = preg_replace('#^https?://(.*?)/(.*?)$#', '$2', $url_no_nums);

			$this->getSqliteConnection()->insert('pagelog', array(
				'url'                    => $url,
				'url_noparams'           => $url_no_nums,
				'url_noaccount'          => $url_noaccount,
				'url_noaccount_noparams' => $url_noaccount_nonums,
				'time_total'             => $time_total,
				'time_php'               => $time_php,
				'time_db'                => $time_db,
				'query_count'            => $query_count,
				'peak_memory'            => $peak_memory,
				'hit_at'                 => $date
			));

			if ($count % 1000 == 0) {
				echo ".";
			}
		}

		echo "\n";
		echo "Insert $count logs.\n";
	}
}
