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

class RecountRatingsCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:recount-ratings');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$recount_ratings = new \Application\DeskPRO\Publish\RecountRatings($this->getContainer()->getEm());

		$status_fn = function($type, $info) use ($output) {
			switch ($type) {
				case 'start':
					$output->writeln("Recounting ratings. Processing {$info['batch_size']} batches.");
					break;
				case 'batch_start':
					$output->write("Processing batch {$info['batch']} ... ");
					break;
				case 'batch_end':
					$output->writeln("Done");
					break;
				case 'end':
					$output->writeln("Done all.");
					break;
			}
		};
		$recount_ratings->recountAll($status_fn);

		return 0;
	}
}
