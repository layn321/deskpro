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

namespace Orb\File;

/**
* Orb
*
* @package Orb
* @category File
*/

/**
 * Will attempt to compress a file using whatever tools are available
 */
class CompressFile
{
	/**
	 * @var string
	 */
	protected $file_contents = '';

	/**
	 * @var string
	 */
	protected $tmpfile;

	/**
	 * The type of file created
	 * @var string
	 */
	protected $type;

	public function __construct($file_contents = '')
	{
		$this->file_contents = $file_contents;
	}

	/**
	 * Get the temp file that the contents were saved into
	 *
	 * @return string
	 */
	public function getTmpFile()
	{
		return $this->tmpfile;
	}

	/**
	 * get the type of compression algo used
	 *
	 * @return string
	 */
	public function getCompressedType()
	{
		return $this->type;
	}


	/**
	 * Check to see which methods of compression we can use, and choose one.
	 *
	 * @return bool
	 */
	public function compress()
	{
		if (function_exists('gzcompress')) {
			$this->compressGz();
			return true;
		} elseif (function_exists('bzcompress')) {
			$this->compressBzip();
			return true;
		} elseif (class_exists('ZipArchive')) {
			$this->compressZip();
			return true;
		} elseif (false && function_exists('shell_exec')) {
			$path = @shell_exec('whereis gzip');
			if (strpos($path, '/gzip') !== false) {
				$this->compressGzCommand(\Orb\Util\Strings::getFirstLine($path));
				return true;
			}
		}

		return false;
	}


	/**
	 * Compress using the GZ extension
	 */
	public function compressGz()
	{
		$this->type = 'gz';
		$this->tmpfile = tempnam(sys_get_temp_dir(), 'gzfile' . mt_rand(1000,9999));
		$fp = @fopen($this->tmpfile, 'w');

		if (!$fp) {
			throw new \RuntimeException("Could not create temp file", 1);
		}

		fwrite($fp, gzencode($this->file_contents));
		fclose($fp);
	}


	/**
	 * Compress using the Bzip extension
	 */
	public function compressBzip()
	{
		$this->type = 'bzip2';
		$this->tmpfile = tempnam(sys_get_temp_dir(), 'bzipfile' . mt_rand(1000,9999));
		$fp = @fopen($this->tmpfile, 'w');

		if (!$fp) {
			throw new \RuntimeException("Could not create temp file", 1);
		}

		fwrite($fp, bzcompress($this->file_contents));
		fclose($fp);
	}


	/**
	 * Compress using the Zip extension
	 */
	public function compressZip()
	{
		$this->type = 'zip';
		$this->tmpfile = tempnam(sys_get_temp_dir(), 'zipfile' . mt_rand(1000,9999));

		$zip = new \ZipArchive();
		$zip->open($this->tmpfile, \ZipArchive::CREATE);
		$zip->addFromString('file', $this->file_contents);
		$zip->close();
	}


	/**
	 * Compress using the command-line by executing $gzip_path as the gzip binary
	 *
	 * @param string $gzip_path
	 */
	public function compressGzCommand($gzip_path)
	{
		$this->type = 'gz';
		$this->tmpfile = tempnam(sys_get_temp_dir(), 'zipfile' . mt_rand(1000,9999));
		file_put_contents($this->tmpfile, $this->file_contents);

		$cmd = $gzip_path . ' ' . $this->tmpfile;
		shell_exec($cmd);

		$this->tmpfile .= '.gz';
	}
}