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
 * @subpackage
 */

namespace Application\DeskPRO\Command;

namespace Application\DeskPRO\Command;

use DeskPRO\Kernel\KernelErrorHandler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Strings;

class SchemaCorrectionCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:schema-correction')
			->addOption('apply', null, InputOption::VALUE_NONE, 'Execute queries to sync the schema')
		     ->setHelp("This command compares the current database schema against the current codebase");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$do_apply = $input->getOption('apply');

		$schemadiff = \Application\DeskPRO\ORM\Util\Util::getUpdateSchemaSql();
		if ($schemadiff) {
			$output->writeln("<info>Correcting schema...</info>");
			foreach ($schemadiff as $line) {
				$output->writeln("-> " . $line);

				if ($do_apply) {
					try {
						App::getDb()->exec($line);
					} catch (\Exception $e) {
						KernelErrorHandler::handleException($e, false);

						$output->writeln("->\tFAILED: " . $e->getMessage());

						// If it failed, log the error and force it with FK checks off
						try {
							$output->writeln("->\tRetry with FK checks off...");
							App::getDb()->exec("SET FOREIGN_KEY_CHECKS = 0");
							App::getDb()->exec($line);
							App::getDb()->exec("SET FOREIGN_KEY_CHECKS = 1");
							$output->writeln("->\tSuccess");
						} catch (\Exception $e) {
							$output->writeln("->\tFAILED: " . $e->getMessage());
						}
					}
				}
			}
		} else {
			$output->writeln("<info>No corrections required</info>");
		}
	}
}