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
use Aws\S3\Enum\CannedAcl;
use Aws\S3\S3Client;

class AmazonS3Storage extends AbstractStorageAdapter
{
	/**
	 * @var \Aws\S3\S3Client
	 */
	protected $s3;

	/**
	 * @var string
	 */
	protected $bucket;

	/**
	 * @var string
	 */
	protected $file_url_domain;

	/**
	 * @var string
	 */
	protected $base_path;

	/**
	 * @var int
	 */
	protected $attempts = 2;

	/**
	 * @var int
	 */
	protected $retry_sleep = 1;

	protected function init()
	{
		$this->s3              = $this->options->get('s3_client');
		$this->bucket          = $this->options->get('bucket');
		$this->file_url_domain = $this->options->get('file_url_domain');
		$this->base_path       = rtrim($this->options->get('base_path', ''), '/\\');
		$this->attempts        = $this->options->get('attempts', 1);
		$this->retry_sleep     = $this->options->get('retry_sleep', 1);

		if (!$this->s3 || !($this->s3 instanceof S3Client)) {
			throw new \InvalidArgumentException("s3_client must be an instance of Aws\\S3\\S3Client");
		}

		if (!$this->bucket) {
			throw new \InvalidArgumentException("bucket is a required option");
		}
	}


	/**
	 * @param Blob $blob
	 * @return string
	 */
	public function makePathForBlob(Blob $blob)
	{
		$path = array();
		if ($blob->getMeta('batch')) {
			$path[] = $blob->getMeta('batch');
		}
		if ($blob->getMeta('authcode')) {
			$path[] = $blob->getMeta('authcode');
		} else {
			$path[] = md5(uniqid('', true));
		}

		return implode('/', $path) . '-' . $blob->getFilenameSafe();
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
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @return bool
	 */
	public function checkBlobExists(Blob $blob)
	{
		return $this->s3->doesObjectExist(
			$this->bucket,
			$this->resolvePath($blob->getPath())
		);
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @return bool
	 */
	public function deleteBlob(Blob $blob)
	{
		$path = $this->resolvePath($blob->getPath());

		$this->s3->deleteObject(array(
			'Bucket' => $this->bucket,
			'Key' => $path
		));

		return true;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param $data
	 * @return mixed
	 */
	public function writeBlobString(Blob $blob, $data)
	{
		$path = $this->resolvePath($blob->getPath());

		$disposition = $blob->getMeta('content_disposition') ?: 'attachment';
		$disposition .= '; filename="' . str_replace(array('\'', '"'), '-', $blob->getFilename()) . '"';

		$try = $this->attempts;
		while (--$try >= 0) {
			try {
				$this->s3->putObject(array(
					'Bucket'             => $this->bucket,
					'Body'               => $data,
					'Key'                => $this->resolvePath($blob->getPath()),
					'ContentType'        => $blob->getContentType(),
					'ContentDisposition' => $disposition,
					'ACL'                => CannedAcl::PUBLIC_READ,
				));
				break;
			} catch (\Exception $e) {
				if ($try == 0) {
					throw $e;
				}
				if ($this->retry_sleep) {
					sleep($this->retry_sleep);
				}
			}
		}

		if (!$this->file_url_domain) {
			$blob->setMeta('file_url', 'https://'. $this->bucket . '.s3.amazonaws.com' . $path);
		} else {
			$blob->setMeta('file_url', 'https://'. $this->file_url_domain . $path);
		}

		return strlen($data);
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param resource $data
	 * @return int
	 */
	public function writeBlobFromStream(Blob $blob, $fp_source)
	{
		return $this->writeBlobString($blob, stream_get_contents($fp_source));
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param string $source_path
	 * @return int
	 */
	public function writeBlobFromFile(Blob $blob, $source_path)
	{
		return $this->writeBlobString($blob, file_get_contents($source_path));
	}


	/**
	 * Loads the entire blob into a string
	 *
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @return string
	 */
	public function readBlobString(Blob $blob)
	{
		$try = $this->attempts;
		while (--$try >= 0) {
			try {
				/** @var $model \Guzzle\Service\Resource\Model */
				$model = $this->s3->getObject(array(
					'Bucket' => $this->bucket,
					'Key'    => $this->resolvePath($blob->getPath()),
				));
				break;
			} catch (\Exception $e) {
				if ($try == 0) {
					throw $e;
				}
				if ($this->retry_sleep) {
					sleep($this->retry_sleep);
				}
			}
		}

		return $model->get('body');
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param $target_path
	 * @return int
	 */
	public function readBlobToFile(Blob $blob, $target_path)
	{
		return file_put_contents($target_path, $this->readBlobString($blob));
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param resource $data
	 * @return int
	 */
	public function readBlobToStream(Blob $blob, $fp_target)
	{
		return fwrite($fp_target, $this->readBlobString($blob));
	}


	/**
	 * @param Blob $blob
	 */
	public function getFileUrlLink(Blob $blob)
	{

	}
}