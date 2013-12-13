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
use Application\DeskPRO\Log\Logger;

class SearchReindexCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:search-reindex')->addArgument('content-type', InputArgument::REQUIRED, 'The type of content you want to reindex: article, download, feedback, news, ticket');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$content_type = $input->getArgument('content-type');

		$table = null;
		$entity = null;
		$ids = null;

		switch ($content_type) {
			case 'article':
				$entity = 'DeskPRO:Article';
				$table = 'articles';
				break;

			case 'download':
				$entity = 'DeskPRO:Download';
				$table = 'downloads';
				break;

			case 'feedback':
				$entity = 'DeskPRO:Feedback';
				$table = 'feedback';
				break;

			case 'news':
				$entity = 'DeskPRO:News';
				$table = 'news';
				break;

			case 'ticket':
				$entity = 'DeskPRO:Ticket';
				$table = 'tickets';
				break;

			default:
				$output->writeln("<warn>Unsupported content type `$content_type`</warn>");
				return 1;
				break;
		}

		$this->getContainer()->getSearchAdapter()->deleteContentTypeFromIndex($content_type);

		$all_ids = $this->getContainer()->getDb()->fetchAllCol("SELECT id FROM $table ORDER BY id ASC");
		if (!$all_ids) {
			$output->writeln("No objects to update.");
			return 0;
		}

		$all_batch_ids = array_chunk($all_ids, 20);

		$output->writeln(sprintf("%d objects will be processed in %d batches", count($all_ids), count($all_batch_ids)));

		#------------------------------
		# Process each
		#------------------------------

		$x = 0;
		foreach ($all_batch_ids as $batch_ids) {
			$x++;
			$batch = $this->getContainer()->getEm()->getRepository($entity)->getByIds($batch_ids);
			if ($batch) {
				$this->getContainer()->getSearchAdapter()->updateObjectsInIndex($batch);
			}

			$this->getContainer()->getEm()->clear();

			$output->write('.');

			if ($x % 50 === 0) {
				set_time_limit(40);
				$this->getContainer()->getEm()->clear();
			}
		}
		$output->writeln('');

		return 0;
	}
}
