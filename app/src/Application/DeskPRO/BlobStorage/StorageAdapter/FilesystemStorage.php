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

namespace Application\DeskPRO\BlobStorage\StorageAdapter;

use Application\DeskPRO\BlobStorage\Blob;
use Orb\Util\Numbers;

class FilesystemStorage extends AbstractStorageAdapter implements ReadStreamInterface, WriteStreamInterface
{
	/**
	 * @var string
	 */
	protected $base_path;

	/**
	 * @var int
	 */
	protected $file_mode = 0777;

	/**
	 * @var int
	 */
	protected $dir_mode = 0777;

	protected function init()
	{
		$this->base_path = rtrim($this->options->get('base_path'), '/\\');

		if ($this->options->has('file_mode')) {
			$this->file_mode = (int)$this->options->get('file_mode');
		}
		if ($this->options->has('dir_mode')) {
			$this->file_mode = (int)$this->options->get('dir_mode');
		}
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @return bool
	 */
	public function checkBlobExists(Blob $blob)
	{
		$path = $this->resolvePath($blob->getPath());

		$exists = is_file($path);

		$this->logger->logInfo("[FilesystemStorage] (checkBlobExists) $path " . ($exists ? 'exists' : 'no exist'));

		return $exists;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @return bool
	 */
	public function deleteBlob(Blob $blob)
	{
		$path = $this->resolvePath($blob->getPath());

		if (!file_exists($path)) {
			$this->logger->logInfo("[FilesystemStorage] (deleteBlob) Path does not exist, nothing to delete: $path");
			return true;
		}

		$res = @unlink($path);

		if ($res) {
			$this->logger->logInfo("[FilesystemStorage] (deleteBlob) Deleted path: $path");
		} else {
			$this->logger->logInfo("[FilesystemStorage] (deleteBlob) Failed to delete path: $path");
		}

		return $res;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param $data
	 * @return int
	 */
	public function writeBlobString(Blob $blob, $data)
	{
		$fp = $this->getBlobWriteStream($blob);
		$ret = @fwrite($fp, $data);
		@fclose($fp);

		$this->logger->logInfo("[FilesystemStorage] (writeBlobString) Wrote " . Numbers::filesizeDisplay($ret) . " from string to " . $this->resolvePath($blob->getPath()));

		return $ret;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param string $source_path
	 * @return int
	 */
	public function writeBlobFromFile(Blob $blob, $source_path)
	{
		$fp_source = @fopen($source_path, 'r');

		if (!$fp_source) {
			@fclose($fp_source);
			$this->logger->logError("[FilesystemStorage] (writeBlobFromFile) Could not open $source_path for reading");
			throw new \RuntimeException("Could not open source_path for reading: $source_path");
		}

		try {
			$ret = $this->writeBlobFromStream($blob, $fp_source);
		} catch (\Exception $e) {
			@fclose($fp_source);
			throw $e;
		}

		@fclose($fp_source);

		$this->logger->logInfo("[FilesystemStorage] (writeBlobFromFile) Wrote " . Numbers::filesizeDisplay($ret) . " from $source_path to " . $this->resolvePath($blob->getPath()));

		return $ret;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param resource $data
	 * @return int
	 */
	public function writeBlobFromStream(Blob $blob, $fp_source)
	{
		$fp = $this->getBlobWriteStream($blob);
		$ret = $this->_copyStream($fp_source, $fp);
		@fclose($fp);

		$this->logger->logInfo("[FilesystemStorage] (writeBlobFromStream) Wrote " . Numbers::filesizeDisplay($ret) . " from stream to " . $this->resolvePath($blob->getPath()));

		$path = $this->resolvePath($blob->getPath());
		if (file_exists($path)) {
			$this->_chmod($path, $this->file_mode);
		}

		return $ret;
	}


	/**
	 * Loads the entire blob into a string
	 *
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @return string
	 */
	public function readBlobString(Blob $blob)
	{
		$fp = $this->getBlobReadStream($blob);

		$str = '';
		while (!feof($fp)) {
			$str .= @fread($fp, 1000);
		}

		@fclose($fp);

		$this->logger->logInfo("[FilesystemStorage] (readBlobString) Read " . Numbers::filesizeDisplay(strlen($str)) . " from " . $this->resolvePath($blob->getPath()));

		return $str;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param string $target_path
	 * @return int
	 */
	public function readBlobToFile(Blob $blob, $target_path)
	{
		$fp_target = @fopen($target_path, 'w');

		if (!$fp_target) {
			@fclose($fp_target);
			$this->logger->logError("[FilesystemStorage] (readBlobToFile) Could not open $target_path for writing");
			throw new \RuntimeException("Could not open target_path for writing: $target_path");
		}

		try {
			$ret = $this->readBlobToStream($blob, $fp_target);
		} catch (\Exception $e) {
			@fclose($fp_target);
			throw $e;
		}

		$this->logger->logInfo("[FilesystemStorage] (readBlobToFile) Read " . Numbers::filesizeDisplay($ret) . " to $target_path from " . $this->resolvePath($blob->getPath()));

		return $ret;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param resource $fp_target
	 * @return int
	 */
	public function readBlobToStream(Blob $blob, $fp_target)
	{
		$fp = $this->getBlobReadStream($blob);
		$ret = $this->_copyStream($fp, $fp_target);
		fclose($fp);

		$this->logger->logInfo("[FilesystemStorage] (readBlobToStream) Read " . Numbers::filesizeDisplay($ret) . " to stream from " . $this->resolvePath($blob->getPath()));

		return $ret;
	}


	/**
	 * @return resource
	 */
	public function getBlobWriteStream(Blob $blob)
	{
		$path = $this->resolvePath($blob->getPath());
		$dir = dirname($path);

		if (!is_dir($dir)) {
			@mkdir($dir, 0777, true);
			$this->_chmod($dir, $this->dir_mode);
		}

		$fp = @fopen($path, 'w');

		if (!$fp) {
			$this->logger->logError("[FilesystemStorage] (getBlobWriteStream) Failed to open path for writing: $path");
			throw new \RuntimeException("Could not open blob for writing: $path");
		}

		return $fp;
	}


	/**
	 * @return resource
	 */
	public function getBlobReadStream(Blob $blob)
	{
		$path = $this->resolvePath($blob->getPath());

		$fp = @fopen($path, 'r');

		if (!$fp) {
			$this->logger->logError("[FilesystemStorage] (getBlobReadStream) Failed to open path for reading: $path");
			throw new \RuntimeException("Could not open blob for reading: $path");
		}

		return $fp;
	}


	/**
	 * Get the full path from a path string
	 *
	 * @param string $path
	 * @return string
	 */
	public function resolvePath($path)
	{
		$path = trim($path, '/\\');
		return $this->base_path . DIRECTORY_SEPARATOR . $path;
	}

	/**
	 * @param $fp_from
	 * @param $fp_to
	 * @return int
	 */
	protected function _copyStream($fp_from, $fp_to)
	{
		$size = 0;
        while (!feof($fp_from)) {
			$size += @fwrite($fp_to, fread($fp_from, 8192));
		}

        return $size;
	}

	/**
	 * chmod's a file to $mode. Resets current umask in case it is set.
	 *
	 * @param string $file
	 * @param int $mode
	 */
	private function _chmod($file, $mode)
	{
		$current_umask = umask();
        @umask(0000);
		@chmod($file, $mode);
        @umask($current_umask);
	}
}