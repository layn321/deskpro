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
 * Orb
 *
 * @package Orb
 * @category File
 */

namespace Application\DeskPRO\Distribution;

use Symfony\Component\Finder\Finder;

class ChecksumChecker extends \Orb\File\ChecksumChecker
{
	public function __construct($chunk_size = 200)
	{
		parent::__construct(realpath(DP_ROOT.'/../'));
		$this->finder->notName('distro-checksums.php')
			->notName('.gitignore')
			->notName('.gitmodules')
			->notName('.buildpath')
			->notName('.project')
			->notName('.DS_Store')
			->notName('dev_debug.php')
			->notName('config.php')
			->notName('config.new.php')
			->notName('classes.map')
			->notName('.htaccess')
			->notName('web.config')
			->ignoreVCS(true)
			->exclude('sys/cache/dev')
			->exclude('.settings')
			->exclude('.idea')
			->notName('.travis.yml')
			->exclude('data')
			->exclude('.feedback');
	}

	/**
	 * Compare the current fileset with the distributed list
	 *
	 * @return array
	 */
	public function compareWithStandard()
	{
		return $this->compareWithDump(DP_ROOT.'/sys/Resources/distro-checksums.php');
	}


	/**
	 * Dump current hashes to standard checksum file for deskpro
	 */
	public function dumpToStardnardFile()
	{
		return $this->dumpToFile(DP_ROOT.'/sys/Resources/distro-checksums.php');
	}
}
