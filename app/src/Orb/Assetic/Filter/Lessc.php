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
 * @category Auth
 */

namespace Orb\Assetic\Filter;

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Util\ProcessBuilder;

class Lessc implements FilterInterface
{
	protected $lessc_bin;

	public function __construct($lessc_bin)
	{
		$this->lessc_bin = $lessc_bin;
	}

	public function filterDump(AssetInterface $asset)
    {

    }

	public function filterLoad(AssetInterface $asset)
	{
		$tempDir = realpath(sys_get_temp_dir());
		$madefiles = array();

		$source_files = array();
		if ($asset instanceof \Assetic\Asset\FileAsset) {
			$source_files[] = $asset->getSourceRoot() . '/' . $asset->getSourcePath();
		} else if ($asset instanceof \Assetic\Asset\AssetCollection) {
			foreach ($asset->getIterator() as $sub_asset) {
				$source_files[] = $asset->getSourceRoot() . '/' . $sub_asset->getSourcePath();
			}
		} else {
			throw new \RunTimeException("Cannot handle asset type `" . get_class($asset) . "`");
		}

		foreach ($source_files as $source_file) {
			$hash = substr(sha1(time().rand(11111, 99999)), 0, 7);
			$output = $tempDir.DIRECTORY_SEPARATOR.$hash.'.css';

			$pb = new ProcessBuilder();
			$pb->add($this->lessc_bin);

			$pb->add($source_file)->add($output);
			$proc = $pb->getProcess();
        	$code = $proc->run();

			if (0 < $code) {
				if (file_exists($output)) {
					unlink($output);
				}

				foreach ($madefiles as $tmpfile) {
					if (file_exists($tmpfile)) {
						unlink($tmpfile);
					}
				}

				throw new \RuntimeException("[Lessc] " . $pb->getProcess()->getCommandLine() . " " . $proc->getOutput() . "\n\n" . $proc->getErrorOutput());
			} elseif (!file_exists($output)) {
				throw new \RuntimeException('Error creating output file.');
			}

			$madefiles[] = $output;
		}

		$complete_file = array();
		foreach ($madefiles as $f) {
			$complete_file[] = file_get_contents($f);
			unset($f);
		}

		$complete_file = implode("\n", $complete_file);

        $asset->setContent($complete_file);
	}
}
