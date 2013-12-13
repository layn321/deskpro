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

class DevCheckReservedWordsCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:check-reserved-words');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if (!dp_get_config('debug.dev')) {
			$output->write("Dev mode is not enabled");
			return 0;
		}

		echo "Checking database tables for fields named after reserved words ... ";

		$time_start = microtime(true);
		$reserved_check = new \Application\DeskPRO\DBAL\ReservedWords(App::getDb()->getSchemaManager());
		$bad = $reserved_check->getBadTables();
		$time_end = microtime(true);

		echo sprintf("Done (%.4fs)\n", $time_end-$time_start);

		if ($bad) {
			$output->writeln(sprintf("<error>There are %d tables using reserved words</error>", count($bad)));
			foreach ($bad as $table => $c) {
				echo "$table: " . implode(', ', $c) . "\n";
			}
		} else {
			$output->writeln(sprintf("<info>All tables check out fine</info>"));
		}
	}
}
