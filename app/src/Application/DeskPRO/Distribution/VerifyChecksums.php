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

class VerifyChecksums
{
	protected $standard_hashes;
	protected $count_all;

	public function __construct($chunk_size = 350)
	{
		if (!is_file(DP_ROOT.'/sys/Resources/distro-checksums.php')) {
			// Get it to appear in the missing list in admin
			$standard_hashes = array('/app/sys/Resources/distro-checksums.php' => 'missing');
		} else {
			$standard_hashes = require DP_ROOT.'/sys/Resources/distro-checksums.php';
		}
		$this->count_all = count($standard_hashes);
		$standard_hashes = array_chunk($standard_hashes, $chunk_size, true);
		$this->standard_hashes = $standard_hashes;
	}

	/**
	 * Gets the files in a specific chunk as defined in the standard distro checksum file,
	 * and then compares those hashes.
	 *
	 * @param int $chunk
	 */
	public function compareChunk($chunk = 0)
	{
		$standard_chunk_hashes = $this->getStandardChunk($chunk);
		$chunk_files = array_keys($standard_chunk_hashes);
		$chunk_hashes = array();

		$uproot = realpath(DP_ROOT.'/../');

		foreach ($chunk_files as $f) {
			$filepath = $uproot.$f;
			if (file_exists($filepath)) {
				$file_contents = $this->normalizeFileString(file_get_contents($filepath));
				$chunk_hashes[$f] = md5($file_contents);
			} else {
				$chunk_hashes[$f] = null;
			}
		}

		$results = array(
			'added' => array(),
			'removed' => array(),
			'changed' => array(),
			'okay' => array()
		);

		foreach ($chunk_hashes as $path => $checksum) {
			if (!isset($standard_chunk_hashes[$path])) {
				$results['added'][] = $path;
			} elseif ($checksum === null) {
				$results['removed'][] = $path;
			} elseif ($checksum != $standard_chunk_hashes[$path]) {
				$results['changed'][] = $path;
			} else {
				$results['okay'][] = $path;
			}
		}

		return $results;
	}

	/**
	 * @param string $file_contents
	 * @return string
	 */
	protected function normalizeFileString($file_contents)
	{
		static $bom = null;

		if ($bom === null) {
			$bom = pack('CCC', 0xEF, 0xBB, 0xBF);
		}

		if (substr($file_contents, 0, 3) === $bom) {
			$file_contents = substr($file_contents, 3);
		}

		$file_contents = trim(str_replace(array("\r", "\n"), '', $file_contents));

		return $file_contents;
	}


	/**
	 * Get a chunk
	 *
	 * @param int $chunk
	 * @param int $chunk_size
	 * @return array
	 */
	public function getStandardChunk($chunk)
	{
		if (!isset($this->standard_hashes[$chunk])) {
			return array();
		}

		return $this->standard_hashes[$chunk];
	}

	/**
	 * Count how many chunks there are
	 * @return int
	 */
	public function countChunks()
	{
		return count($this->standard_hashes);
	}

	public function countFiles()
	{
		return $this->count_all;
	}
}
