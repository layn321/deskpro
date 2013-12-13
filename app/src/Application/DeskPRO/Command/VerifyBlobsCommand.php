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

use Application\DeskPRO\BlobStorage\MoveBlobsUtil;
use Orb\Log\Logger;
use Orb\Log\Writer\ConsoleOutputWriter;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;

use Orb\Util\Arrays;
use Orb\Util\Strings;


class VerifyBlobsCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:verify-blobs');
		$this->setHelp("This checks all blobs stored on the filesystem to make sure they exist and that they are the correct");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$bs = App::getContainer()->getBlobStorage();

		/** @var \Application\DeskPRO\BlobStorage\StorageAdapter\FilesystemStorage $fs */
		$fs = $bs->getAdapter('fs');

		$is_verbose = $output->getVerbosity() > 1;

		$page = 0;
		do {
			$limit = $page++ * 1000;
			$blob_batch = App::getDb()->fetchAll("SELECT * FROM blobs WHERE storage_loc = 'fs' LIMIT $limit, 1000");

			foreach ($blob_batch as $blob) {
				$file_path = $fs->resolvePath($blob['save_path']);

				if (!file_exists($file_path)) {
					printf("Blob #%d is MISSING. Expected: %s\n", $blob['id'], $file_path);
				} else {
					$md5 = md5_file($file_path);
					if ($md5 != $blob['blob_hash']) {
						printf("Blob #%d is INVALID. Hash mismatch with file: %s\n\t$md5 != {$blob['blob_hash']}\n", $blob['id'], $file_path);
					} else {
						if ($is_verbose) printf("Blob #%d is OKAY. File: %s\n", $blob['id'], $file_path);
					}
				}
			}

			if ($blob_batch) {
				printf("Checked %d files\n", count($blob_batch));
			}
		} while ($blob_batch);

		echo "\nDone\n";
		return 0;
	}
}