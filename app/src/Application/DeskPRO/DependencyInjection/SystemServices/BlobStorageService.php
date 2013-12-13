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
 * @category DependencyInjection
 */

namespace Application\DeskPRO\DependencyInjection\SystemServices;

use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Application\DeskPRO\BlobStorage\DeskproBlobStorage;
use Application\DeskPRO\BlobStorage\StorageAdapter\AmazonS3Storage;
use Application\DeskPRO\BlobStorage\StorageAdapter\FilesystemStorage;
use Application\DeskPRO\BlobStorage\StorageAdapter\DatabaseStorage;
use Aws\S3\S3Client;
use Orb\Log\Logger;

class BlobStorageService
{
	public static function create(DeskproContainer $container)
	{
		#------------------------------
		# Create a logger
		#------------------------------

		$logger = new Logger();

		if (!dp_get_config('enable_blobstorage_log')) {
			$logger->addFilter(new \Orb\Log\Filter\PriorityFilter(Logger::WARN));
		}

		$wr = new \Orb\Log\Writer\Stream($container->getLogDir() . DIRECTORY_SEPARATOR . 'blob_storage.log');
		$logger->addWriter($wr);

		#------------------------------
		# Filesystem adapter
		#------------------------------

		$opts = array('base_path' => $container->getBlobDir());
		if ($container->getSetting('core.filestorage_file_mode')) {
			$opts['file_mode'] = $container->getSetting('core.filestorage_file_mode');
		}
		if ($container->getSetting('core.filestorage_dir_mode')) {
			$opts['dir_mode'] = $container->getSetting('core.filestorage_dir_mode');
		}

		$fs_adapter = new FilesystemStorage($opts);
		$fs_adapter->setLogger($logger);

		#------------------------------
		# S3 Adapter
		#------------------------------

		$s3_adapter = null;
		if ($container->getSetting('core.filestorage_s3_key') && $container->getSetting('core.filestorage_s3_secret') && $container->getSetting('core.filestorage_s3_bucket')) {
			if (!defined('CURLOPT_CONNECTTIMEOUT')) define(CURLOPT_CONNECTTIMEOUT, 78);
			if (!defined('CURLOPT_TIMEOUT')) define(CURLOPT_TIMEOUT, 13);

			$client = S3Client::factory(array(
				'key'          => $container->getSetting('core.filestorage_s3_key'),
				'secret'       => $container->getSetting('core.filestorage_s3_secret'),
				'curl.options' => array(
					CURLOPT_CONNECTTIMEOUT => 40,
					CURLOPT_TIMEOUT        => 120,
				)
			));
			$s3_adapter = new AmazonS3Storage(array(
				's3_client'       => $client,
				'bucket'          => $container->getSetting('core.filestorage_s3_bucket'),
				'file_url_domain' => $container->getSetting('core.filestorage_s3_file_url_domain'),
				'base_path'       => $container->getSetting('core.filestorage_s3_basepath'),
			));
			$s3_adapter->setLogger($logger);
		}

		#------------------------------
		# Database adapter
		#------------------------------

		$db_adapter = new DatabaseStorage(array(
			'db'                   => $container->getDb(),
			'table'                => 'blobs_storage',
			'field_name.data'      => 'data',
			'field_name.path'      => 'blob_id',
			'field_name.order'     => 'id',
			'metadata_id_property' => 'blob_id'
		));
		$db_adapter->setLogger($logger);

		#------------------------------
		# Create the storage
		#------------------------------

		$bs = new DeskproBlobStorage($container->getEm());
		$bs->setLogger($logger);

		if ($s3_adapter && $container->getSetting('core.filestorage_method') == 's3') {
			$bs->addAdapter('s3', $s3_adapter);
			$bs->addAdapter('fs', $fs_adapter);
			$bs->addAdapter('db', $db_adapter);
			$bs->disableAdapter('fs');
		} elseif ($container->getSetting('core.filestorage_method') == 'fs') {
			$bs->addAdapter('fs', $fs_adapter);
			if ($s3_adapter) {
				$bs->addAdapter('s3', $s3_adapter);
				$bs->disableAdapter('s3');
			}
			$bs->addAdapter('db', $db_adapter);
		} else {
			$bs->addAdapter('db', $db_adapter);
			$bs->addAdapter('fs', $fs_adapter);
			$bs->disableAdapter('fs', $fs_adapter);
			if ($s3_adapter) {
				$bs->addAdapter('s3', $s3_adapter);
				$bs->disableAdapter('s3');
			}
		}

		if (defined('DP_BLOBSTORAGE_SAVECOPY_PATH')) {
			$bs->setSaveCopyPath(DP_BLOBSTORAGE_SAVECOPY_PATH);
		}

		return $bs;
	}
}
