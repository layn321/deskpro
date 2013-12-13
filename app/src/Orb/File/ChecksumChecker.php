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

namespace Orb\File;

use Symfony\Component\Finder\Finder;

class ChecksumChecker
{
	/**
	 * @var array
	 */
	protected $checksums = null;

	/**
	 * @var string
	 */
	protected $base_dir;

	/**
	 * @var \Symfony\Component\Finder\Finder
	 */
	protected $finder;

	/**
	 * @var array
	 */
	protected $file_list = null;


	public function __construct($base_dir)
	{
		$this->base_dir = $base_dir;

		$this->finder = new Finder();
		$this->finder->files();
		$this->finder->in($this->base_dir);
		$this->finder->ignoreVCS(true);
	}


	/**
	 * Instead of recursively scanning and finding files, you can optionally
	 * set an array of specific paths.
	 *
	 * @param array $files
	 */
	public function setFileList(array $files)
	{
		$this->file_list = $files;
	}


	/**
	 * Ignore a filename
	 *
	 * @param string $file
	 */
	public function ignoreFilename($file)
	{
		$this->finder->notName($file);
	}


	/**
	 * Ignore a directory
	 *
	 * @param string $dir
	 */
	public function ignoreDirectory($dir)
	{
		$this->finder->exclude($dir);
	}


	/**
	 * Go through and load checks
	 *
	 * @param callback $progress_callback
	 */
	public function load($progress_callback = null)
	{
		$this->checksums = array();

		$count = 0;
		foreach ($this->getIterator() as $file) {
			$count++;
			$path = str_replace($this->base_dir, '', $file->getRealPath());

			$file_contents = $this->normalizeFileString(file_get_contents($file->getRealPath()));

			$hash = md5($file_contents);
			$this->checksums[$path] = $hash;

			if ($progress_callback) {
				$progress_callback($count, $file, $hash);
			}
		}
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
	 * @return int
	 */
	public function count()
	{
		return count($this->checksums);
	}


	/**
	 * Get an array of filename => checksum for all found files
	 *
	 * @return array
	 */
	public function getChecksums()
	{
		if ($this->checksums === null) {
			$this->load();
		}

		return $this->checksums;
	}


	/**
	 * Get an array of files
	 *
	 * @return array
	 */
	public function getFilesArray()
	{
		return array_keys($this->checksums);
	}


	/**
	 * Compare newly generated checksums (generated right now) with those in an array
	 *
	 * @param array $with_checksums
	 */
	public function compare(array $with_checksums)
	{
		$this->getChecksums();

		$results = array(
			'added' => array(),
			'removed' => array(),
			'changed' => array()
		);

		foreach ($this->checksums as $path => $checksum) {
			if (!isset($with_checksums[$path])) {
				$results['added'][] = $path;
			} elseif ($checksum != $with_checksums[$path]) {
				$results['changed'][] = $path;
			}
		}

		$results['removed'] = array_diff(array_keys($with_checksums), array_keys($this->checksums));

		return $results;
	}


	/**
	 * Same as compare() except it fetches checksums from a file
	 *
	 * @param $file
	 * @throws \InvalidArgumentException
	 */
	public function compareWithDump($file)
	{
		if (!is_file($file)) {
			throw new \InvalidArgumentException("File does not exist: `$file`");
		}

		$checksums = require $file;

		if (!is_array($checksums)) {
			throw new \InvalidArgumentException("Dump file did not return checksum array");
		}

		return $this->compare($checksums);
	}


	/**
	 * @param $file
	 */
	public function dumpToFile($file)
	{
		$php = '<?php return ' . var_export($this->getChecksums(), true) . ";\n";
		file_put_contents($file, $php);
	}


	/**
	 * @return \ArrayIterator|\Symfony\Component\Finder\Finder
	 */
	public function getIterator()
	{
		if ($this->file_list) {
			$array = array();
			foreach ($this->file_list as $f) {
				$file = new \SplFileInfo($this->base_dir . $f);
			}

			return new \ArrayIterator($array);
		} else {
			return $this->finder;
		}
	}
}
