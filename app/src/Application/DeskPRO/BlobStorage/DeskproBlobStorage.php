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

namespace Application\DeskPRO\BlobStorage;

use Application\DeskPRO\BlobStorage\StorageAdapter\AbstractStorageAdapter;
use DeskPRO\Kernel\KernelErrorHandler;
use Doctrine\ORM\EntityManager;
use Application\DeskPRO\BlobStorage\Blob;
use Application\DeskPRO\Entity\Blob as BlobEntity;
use Orb\Data\ContentTypes;
use Orb\Log\Loggable;
use Orb\Util\Numbers;
use Orb\Util\Strings;
use Orb\Log\Logger;

class DeskproBlobStorage implements Loggable
{
	/**
	 * @var string
	 */
	protected $preferred_adapter_id = null;

	/**
	 * @var \Application\DeskPRO\BlobStorage\StorageAdapter\AbstractStorageAdapter[]
	 */
	protected $adapters = array();

	/**
	 * @var string[]
	 */
	protected $disabled_adapters = array();

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var string
	 */
	protected $save_copy_path;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->adapters = array();
		$this->em = $em;
		$this->db = $em->getConnection();
		$this->logger = new Logger();
	}


	/**
	 * After a file is saved, copy a second copy to this filepath
	 *
	 * The filepath is represented as a full path that a file will be written to. Include
	 * any of these variables in the path: %ID%, %AUTH%, %BATCH%, %FILENAME%, %DATETIME%
	 *
	 * @param string $url
	 */
	public function setSaveCopyPath($path)
	{
		$this->save_copy_path = $path;
	}


	/**
	 * @param Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @return Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}


	/**
	 * @param StorageAdapter\AbstractStorageAdapter $adapter
	 * @param int $priority
	 */
	public function addAdapter($id, AbstractStorageAdapter $adapter, $priority = 0)
	{
		if ($this->preferred_adapter_id === null) {
			$this->preferred_adapter_id = $id;
		}

		$this->adapters[$id] = $adapter;
	}


	/**
	 * @param string $id
	 * @return AbstractStorageAdapter
	 * @throws \InvalidArgumentException
	 */
	public function getAdapter($id)
	{
		if (!isset($this->adapters[$id])) {
			$this->logger->logError("[DeskproBlobStorage] (getAdapter) No adapter by id: $id");
			throw new \InvalidArgumentException("No adapter by id `$id`");
		}

		return $this->adapters[$id];
	}


	/**
	 * @return string[]
	 */
	public function getAdapterIds()
	{
		return array_keys($this->adapters);
	}


	/**
	 * @return AbstractStorageAdapter
	 */
	public function getPreferredAdapter()
	{
		return $this->getAdapter($this->preferred_adapter_id);
	}


	/**
	 * @return string
	 */
	public function getPreferredAdapterId()
	{
		return $this->preferred_adapter_id;
	}


	/**
	 * @param string $id
	 * @throws \InvalidArgumentException
	 */
	public function setPreferredAdapterId($id)
	{
		if (!isset($this->adapters[$id])) {
			throw new \InvalidArgumentException("No adapter by id `$id`");
		}

		$this->preferred_adapter_id = $id;
	}


	/**
	 * Mark an adapter as disabled. Means it can still be used to read
	 * blobs, but it wont be used when persisting.
	 *
	 * @param string $id
	 */
	public function disableAdapter($id)
	{
		$this->disabled_adapters[$id] = true;
	}


	/**
	 * @param string $filename
	 * @param string $content_type
	 * @param array $props
	 * @return \Application\DeskPRO\Entity\Blob
	 */
	private function _createBlobEntity($filename, $content_type, array $props = null)
	{
		$this->logger->logDebug("[DeskproBlobStorage] (_createBlobEntity) Filename: $filename   ContentType: $content_type");

		$blob_entity = new BlobEntity();
		$blob_entity->filename     = $filename;
		$blob_entity->content_type = $content_type;

		if ($props) {
			if (isset($props['original_blob'])) {
				$blob_entity->original_blob = $props['original_blob'];
			}
			if (isset($props['is_temp']) && $props['is_temp']) {
				$blob_entity->is_temp = true;
			}
			if (isset($props['date_cleanup']) && $props['date_cleanup']) {
				$blob_entity->date_cleanup = $props['date_cleanup'];
			}
			if (isset($props['sys_name']) && $props['sys_name']) {
				$blob_entity->sys_name = $props['sys_name'];
			}
		}

		return $blob_entity;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Blob
	 */
	public function createBlobRecordFromFile($source_path, $filename, $content_type, array $props = null)
	{
		$this->logger->logDebug("[DeskproBlobStorage] BEGIN (saveBlobRecordFromFile) From path: $source_path");

		$blob_entity = $this->_createBlobEntity($filename, $content_type, $props);
		$blob_entity->filesize     = filesize($source_path);
		$blob_entity->blob_hash    = md5_file($source_path);

		if (ContentTypes::isImageContentType($content_type)) {
			$imageinfo = @getimagesize($source_path);
			if ($imageinfo) {
				$blob_entity->dim_w = $imageinfo[0];
				$blob_entity->dim_h = $imageinfo[1];
			}
		}

		$this->em->persist($blob_entity);
		$this->em->flush();

		// We need the ID first to generate a proper unique filename/auth
		$batch = (int)(($blob_entity->id-1) / 1000) + 1;
		$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromFile) Blob ID: {$blob_entity->id}");

		// Now call the blob storages
		$blob = new Blob(
			$blob_entity->filename,
			$blob_entity->content_type,
			array(
				'blob_id' => $blob_entity->id
			)
		);

		$blob->setMeta('batch', $batch);

		$prev_e = null;
		foreach ($this->adapters as $adapter_id => $adapter) {
			if (isset($this->disabled_adapters[$adapter_id])) {
				continue;
			}

			$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromFile) Attempting adapter: $adapter_id");

			/** @var $adapter \Application\DeskPRO\BlobStorage\StorageAdapter\AbstractStorageAdapter */
			try {
				if ($adapter_id == 'fs') {
					$authcode = $batch  . Strings::random(10, Strings::CHARS_KEY_ALPHA) . $blob_entity->getId() . $blob_entity->getNameHash();
				} else {
					$authcode = $blob_entity->getId() . Strings::random(15, Strings::CHARS_KEY_ALPHA) . '0';
				}

				$blob->setMeta('authcode', $authcode);
				$path = $adapter->makePathForBlob($blob);
				$blob->setPath($path);
				$adapter->writeBlobFromFile($blob, $source_path);

				$blob_entity->save_path = $path;
				$blob_entity->storage_loc = $adapter_id;

				// Success, dont try others
				break;
			} catch (\Exception $e) {
				$this->logger->logWarn("[DeskproBlobStorage] (saveBlobRecordFromFile) $adapter_id failed: {$e->getCode()} {$e->getMessage()}");
				KernelErrorHandler::logException($e);
				$prev_e = $e;
			}
		}

		// None of the succeeded, try to delete this half-inserted blob and then throw an error
		if (!$blob_entity->storage_loc) {
			$this->logger->logError("[DeskproBlobStorage] (saveBlobRecordFromFile) All adapters failed");

			$this->em->remove($blob_entity);
			$this->em->flush();

			throw new \RuntimeException("Failed to store blob, no adapters succeeded", 1, $prev_e);
		}

		$blob_entity->authcode  = $blob->getMeta('authcode');

		if ($blob->getMeta('file_url')) {
			$blob_entity->file_url = $blob->getMeta('file_url');
		}

		if ($blob_entity->storage_loc != $this->preferred_adapter_id) {
			$blob_entity->storage_loc_pref = $this->preferred_adapter_id;
		}

		$this->em->persist($blob_entity);
		$this->em->flush();

		$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromFile) Save success");

		if ($this->save_copy_path) {
			$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromFile) Saving copy");

			$batch = (int)(($blob_entity->id-1) / 1000) + 1;
			$copy_path = str_replace(
				array('%ID%', '%AUTH%', '%DATETIME%', '%FILENAME%', '%BATCH%'),
				array($blob_entity->id, $blob_entity->authcode, $blob_entity->date_created->format('YmdHis'), $blob_entity->filename, $batch),
				$this->save_copy_path
			);
			$meta_path = $copy_path . '.meta';

			$dirname = dirname($copy_path);

			if (!is_dir($dirname)) {
				if (!@mkdir($dirname, 0777, true)) {
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromFile) Failed to create copy dir: $dirname");
				}
			}

			if (is_dir($dirname)) {
				if (@copy($source_path, $copy_path)) {
					@chmod($copy_path, 0777);
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromFile) Wrote file: $copy_path");
				} else {
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromFile) Failed to write copy file: $copy_path");
					error_log("Failed to write copy file: $copy_path");
				}

				if (@file_put_contents($meta_path, json_encode($blob_entity->toArray(BlobEntity::TOARRAY_ONLY_PRIMATIVES)))) {
					@chmod($meta_path, 0777);
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromFile) Wrote metadata file: $meta_path");
				} else {
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromFile) Failed to write copy file metadata: $meta_path");
					error_log("Failed to write copy file metadata: $copy_path");
				}
			}
		}

		return $blob_entity;
	}



	/**
	 * @return \Application\DeskPRO\Entity\Blob
	 */
	public function createBlobRecordFromString($source_data, $filename, $content_type, array $props = null)
	{
		$this->logger->logDebug("[DeskproBlobStorage] BEGIN (saveBlobRecordFromString) From data string " . Numbers::filesizeDisplay(strlen($source_data)));

		$blob_entity = $this->_createBlobEntity($filename, $content_type, $props);
		$blob_entity->filesize  = strlen($source_data);
		$blob_entity->blob_hash = md5($source_data);

		if (ContentTypes::isImageContentType($content_type)) {
			$tmpfname = @tempnam(sys_get_temp_dir(), "dpblob_");
			if ($tmpfname && @file_put_contents($tmpfname, $source_data)) {
				$imageinfo = @getimagesize($tmpfname);
				if ($imageinfo) {
					$blob_entity->dim_w = $imageinfo[0];
					$blob_entity->dim_h = $imageinfo[1];
				}
			}
			@unlink($tmpfname);
		}

		$this->em->persist($blob_entity);
		$this->em->flush();

		// We need the ID first to generate a proper unique filename/auth
		$batch = (int)(($blob_entity->id-1) / 1000) + 1;
		$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromString) Blob ID: {$blob_entity->id}");

		// Now call the blob storages
		$blob = new Blob(
			$blob_entity->filename,
			$blob_entity->content_type,
			array(
				'blob_id' => $blob_entity->id
			)
		);

		$blob->setMeta('batch', $batch);

		$prev_e = null;
		foreach ($this->adapters as $adapter_id => $adapter) {
			if (isset($this->disabled_adapters[$adapter_id])) {
				continue;
			}

			$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromString) Attempting adapter: $adapter_id");

			/** @var $adapter \Application\DeskPRO\BlobStorage\StorageAdapter\AbstractStorageAdapter */
			try {
				if ($adapter_id == 'fs') {
					$authcode = $batch  . Strings::random(10, Strings::CHARS_KEY_ALPHA) . $blob_entity->getId() . $blob_entity->getNameHash();
				} else {
					$authcode = $blob_entity->getId() . Strings::random(15, Strings::CHARS_KEY_ALPHA) . '0';
				}

				$blob->setMeta('authcode', $authcode);
				$path = $adapter->makePathForBlob($blob);
				$blob->setPath($path);
				$adapter->writeBlobString($blob, $source_data);

				$blob_entity->save_path = $path;
				$blob_entity->storage_loc = $adapter_id;

				// Success, dont try others
				break;
			} catch (\Exception $e) {
				$this->logger->logWarn("[DeskproBlobStorage] (saveBlobRecordFromString) $adapter_id failed: {$e->getCode()} {$e->getMessage()}");
				KernelErrorHandler::logException($e);
				$prev_e = $e;
			}
		}

		// None of the succeeded, try to delete this half-inserted blob and then throw an error
		if (!$blob_entity->storage_loc) {
			$this->logger->logError("[DeskproBlobStorage] (saveBlobRecordFromString) All adapters failed");

			$this->em->remove($blob_entity);
			$this->em->flush();

			throw new \RuntimeException("Failed to store blob, no adapters succeeded", 1, $prev_e);
		}

		$blob_entity->authcode  = $blob->getMeta('authcode');

		if ($blob->getMeta('file_url')) {
			$blob_entity->file_url = $blob->getMeta('file_url');
		}

		if ($blob_entity->storage_loc != $this->preferred_adapter_id) {
			$blob_entity->storage_loc_pref = $this->preferred_adapter_id;
		}

		$this->em->persist($blob_entity);
		$this->em->flush();

		$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromString) Save success");

		if ($this->save_copy_path) {
			$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromString) Saving copy");

			$batch = (int)(($blob_entity->id-1) / 1000) + 1;
			$copy_path = str_replace(
				array('%ID%', '%AUTH%', '%DATETIME%', '%FILENAME%', '%BATCH%'),
				array($blob_entity->id, $blob_entity->authcode, $blob_entity->date_created->format('YmdHis'), $blob_entity->filename, $batch),
				$this->save_copy_path
			);
			$meta_path = $copy_path . '.meta';

			$dirname = dirname($copy_path);

			if (!is_dir($dirname)) {
				if (!@mkdir($dirname, 0777, true)) {
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromString) Failed to create copy dir: $dirname");
				}
			}

			if (is_dir($dirname)) {
				if (@file_put_contents($copy_path, $source_data)) {
					@chmod($copy_path, 0777);
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromString) Wrote file: $copy_path");
				} else {
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromString) Failed to write copy file: $copy_path");
					error_log("Failed to write copy file: $copy_path");
				}

				if (@file_put_contents($meta_path, json_encode($blob_entity->toArray(BlobEntity::TOARRAY_ONLY_PRIMATIVES)))) {
					@chmod($meta_path, 0777);
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromString) Wrote metadata file: $meta_path");
				} else {
					$this->logger->logDebug("[DeskproBlobStorage] (saveBlobRecordFromString) Failed to write copy file metadata: $meta_path");
					error_log("Failed to write copy file metadata: $copy_path");
				}
			}
		}

		return $blob_entity;
	}


	/**
	 * Read a blob into a string
	 *
	 * @param Blob $blob
	 * @param string $adapter_id
	 * @return string
	 * @throws \Exception
	 */
	public function copyBlobToString(Blob $blob, $adapter_id)
	{
		$this->logger->logDebug("[DeskproBlobStorage] (getBlobString) Reading {$blob->getPath()} from $adapter_id");

		$adapter = $this->getAdapter($adapter_id);

		try {
			$data = $adapter->readBlobString($blob);
		} catch (\Exception $e) {
			$this->logger->logDebug("[DeskproBlobStorage] (getBlobString) Read failed: {$e->getCode()} {$e->getMessage()}");
			throw $e;
		}

		$this->logger->logDebug("[DeskproBlobStorage] (getBlobString) Read success: " . Numbers::filesizeDisplay(strlen($data)));

		return $data;
	}


	/**
	 * Read a blob to a file
	 *
	 * @param string $target_path
	 * @param Blob $blob
	 * @param string $adapter_id
	 * @return int
	 * @throws \Exception
	 */
	public function copyBlobToFile($target_path, Blob $blob, $adapter_id)
	{
		$this->logger->logDebug("[DeskproBlobStorage] (saveBlobToFile) Saving {$blob->getPath()} from $adapter_id to $target_path");

		$adapter = $this->getAdapter($adapter_id);

		try {
			$data = $adapter->readBlobToFile($blob, $target_path);
		} catch (\Exception $e) {
			$this->logger->logDebug("[DeskproBlobStorage] (saveBlobToFile) Save failed: {$e->getCode()} {$e->getMessage()}");
			throw $e;
		}

		$this->logger->logDebug("[DeskproBlobStorage] (getBlobString) Save success: " . Numbers::filesizeDisplay($data));

		return $data;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Blob $blob_entity
	 */
	public function copyBlobRecordToString(BlobEntity $blob_entity)
	{
		$this->logger->logDebug("[DeskproBlobStorage] (readBlobStringFromRecord) Read blob record {$blob_entity->id} from {$blob_entity->storage_loc}");

		$data = null;

		// Can just use the public URL
		if ($blob_entity->file_url) {
			$this->logger->logDebug("[DeskproBlobStorage] (readBlobStringFromRecord) Attempting to fetch via URL: {$blob_entity->file_url}");
			$data = @file_get_contents($blob_entity->file_url);
			if (!$data || strlen($data) != $blob_entity->filesize) {
				$this->logger->logDebug("[DeskproBlobStorage] (readBlobStringFromRecord) Failed");
				$data = null;
			} else {
				$this->logger->logDebug("[DeskproBlobStorage] (readBlobStringFromRecord) Successfully read {$blob_entity->filesize} bytes");
			}
		}

		if (!$data) {
			$blob = $this->getBlobFromBlobRecord($blob_entity);
			$data = $this->copyBlobToString($blob, $blob_entity->storage_loc);
		}

		return $data;
	}


	/**
	 * @param $target_path
	 * @param BlobEntity $blob_entity
	 */
	public function copyBlobRecordToFile($target_path, BlobEntity $blob_entity)
	{
		$this->logger->logDebug("[DeskproBlobStorage] (saveBlobStringToFile) Read blob record {$blob_entity->id} from {$blob_entity->storage_loc} to $target_path");

		$data = null;

		// Can just use the public URL
		if ($blob_entity->file_url) {
			$this->logger->logDebug("[DeskproBlobStorage] (saveBlobStringToFile) Attempting to fetch via URL: {$blob_entity->file_url}");

			$failed = false;
			if (!@copy($blob_entity->file_url, $target_path)) {
				$failed = true;
			}
			if (!$failed && filesize($target_path) != $blob_entity->filesize) {
				$failed = true;
			}

			if ($failed) {
				$this->logger->logDebug("[DeskproBlobStorage] (saveBlobStringToFile) Failed");
				$data = null;
			} else {
				$this->logger->logDebug("[DeskproBlobStorage] (saveBlobStringToFile) Successfully saved {$blob_entity->filesize} bytes");
			}
		}

		if (!$data) {
			$blob = $this->getBlobFromBlobRecord($blob_entity);
			$data = $this->copyBlobToFile($target_path, $blob, $blob_entity->storage_loc);
		}

		return $data;
	}


	/**
	 * @param Blob $blob
	 * @param string $adapter_id
	 * @return void
	 * @throws \Exception
	 */
	public function deleteBlob(Blob $blob, $adapter_id)
	{
		$this->logger->logDebug("[DeskproBlobStorage] (deleteBlob) Deleting {$blob->getPath()} from $adapter_id");

		$adapter = $this->getAdapter($adapter_id);

		try {
			$data = $adapter->deleteBlob($blob);
		} catch (\Exception $e) {
			$this->logger->logDebug("[DeskproBlobStorage] (deleteBlob) Delete failed: {$e->getCode()} {$e->getMessage()}");
			throw $e;
		}

		$this->logger->logDebug("[DeskproBlobStorage] (deleteBlob) Delete success");
	}


	/**
	 * @param BlobEntity $blob_entity
	 * @return void
	 */
	public function deleteBlobRecord(BlobEntity $blob_entity)
	{
		$this->logger->logDebug("[DeskproBlobStorage] (deleteBlobRecord) Deleting {$blob_entity->getId()} from {$blob_entity->storage_loc}");

		$blob = $this->getBlobFromBlobRecord($blob_entity);

		try {
			$this->deleteBlob($blob, $blob_entity->storage_loc);
			$this->em->remove($blob_entity);
			$this->em->flush();
		} catch (\Exception $e) {
			$this->logger->logDebug("[DeskproBlobStorage] (deleteBlobRecord) Delete failed: {$e->getCode()} {$e->getMessage()}");
			throw $e;
		}

		$this->logger->logDebug("[DeskproBlobStorage] (deleteBlobRecord) Delete success");
	}


	/**
	 * @param BlobEntity $blob_entity
	 * @return Blob
	 */
	public function getBlobFromBlobRecord(BlobEntity $blob_entity)
	{
		$blob = new Blob(
			$blob_entity->filename,
			$blob_entity->content_type,
			array(
				'blob_id' => $blob_entity->id
			)
		);
		$blob->setPath($blob_entity->save_path);
		if ($blob_entity->file_url) {
			$blob->setMeta('file_url', $blob_entity->file_url);
		}

		return $blob;
	}


	/**
	 * @param BlobEntity $blob_entity
	 * @param string $adapter_id
	 */
	public function moveBlobRecordToAdapter(BlobEntity $blob_entity, $adapter_id)
	{
		$old_blob = $this->getBlobFromBlobRecord($blob_entity);
		$old_adapter_id = $blob_entity->storage_loc;

		$blob = $this->getBlobFromBlobRecord($blob_entity);
		$file_data = $this->copyBlobRecordToString($blob_entity);

		$batch = (int)(($blob_entity->id-1) / 1000) + 1;
		if ($adapter_id == 'fs') {
			$authcode = $batch  . Strings::random(10, Strings::CHARS_KEY_ALPHA) . $blob_entity->getId() . $blob_entity->getNameHash();
		} else {
			$authcode = $blob_entity->getId() . Strings::random(15, Strings::CHARS_KEY_ALPHA) . '0';
		}

		$blob->setMeta('authcode', $authcode);
		$blob->setMeta('batch', $batch);

		$adapter = $this->getAdapter($adapter_id);
		$path = $adapter->makePathForBlob($blob);
		$blob->setPath($path);
		$adapter->writeBlobString($blob, $file_data);

		$blob_entity->save_path = $path;
		$blob_entity->storage_loc = $adapter_id;

		$blob_entity->authcode  = $blob->getMeta('authcode');

		if ($blob->getMeta('file_url')) {
			$blob_entity->file_url = $blob->getMeta('file_url');
		}

		$this->em->persist($blob_entity);
		$this->em->flush();

		// Delete the old one
		$this->deleteBlob($old_blob, $old_adapter_id);
	}
}