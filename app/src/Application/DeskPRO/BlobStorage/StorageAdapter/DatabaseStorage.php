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
use Doctrine\DBAL\Connection;
use Orb\Util\Numbers;

class DatabaseStorage extends AbstractStorageAdapter
{
	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var string
	 */
	protected $path_field;

	/**
	 * @var string
	 */
	protected $order_field;

	/**
	 * @var string
	 */
	protected $data_field;

	/**
	 * @var string
	 */
	protected $metadata_id_property;

	/**
	 * @var bool
	 */
	protected $manual_order = false;

	/**
	 * @var int
	 */
	protected $seg_size = 256000;

	protected function init()
	{
		$this->db           = $this->options->get('db');
		$this->table        = $this->options->get('table');
		$this->data_field   = $this->options->get('field_name.data');
		$this->path_field   = $this->options->get('field_name.path');
		$this->order_field  = $this->options->get('field_name.order');
		$this->seg_size     = $this->options->get('segment_size', 256000);
		$this->manual_order = $this->options->get('manual_order', false);
		$this->metadata_id_property = $this->options->get('metadata_id_property', null);
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @return bool
	 */
	public function deleteBlob(Blob $blob)
	{
		$path_field = $this->path_field;

		$id = $this->getDbPathId($blob);
		$this->logger->logInfo("[DatabaseStorage] (deleteBlob) Delete $id");

		try {
			$num = $this->db->delete($this->table, array(
				$path_field => $id
			));
		} catch (\Exception $e) {
			$this->logger->logError("[DatabaseStorage] (deleteBlob) Failed: {$e->getCode()} {$e->getMessage()}");
			throw $e;
		}

		$this->logger->logInfo("[DatabaseStorage] (deleteBlob) Affected rows: $num");

		return true;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @return string
	 */
	public function getDbPathId(Blob $blob)
	{
		if ($this->metadata_id_property) {
			return $blob->getMeta($this->metadata_id_property);
		}

		return $blob->getPath();
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @return bool
	 */
	public function checkBlobExists(Blob $blob)
	{
		$id = $this->getDbPathId($blob);
		$x = $this->db->fetchColumn("
			SELECT COUNT(*)
			FROM `{$this->table}`
			WHERE `{$this->path_field}` = ?
			LIMIT 1
		", array($this->getDbPathId($blob)));

		$this->logger->logInfo("[DatabaseStorage] (checkBlobExists) Blob $id " . ($x ? 'exists' : 'no exist'));

		return $x ? true : false;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param $data
	 * @return mixed
	 */
	public function writeBlobString(Blob $blob, $data)
	{
		$path = $this->getDbPathId($blob);

		$path_field  = $this->path_field;
		$data_field  = $this->data_field;
		$order_field = $this->order_field;

		$size = strlen($data);

		$data = str_split($data, $this->seg_size);
		foreach ($data as $k => $d) {
			if ($this->manual_order) {
				$this->db->insert($this->table, array(
					$path_field => $path,
					$data_field => $d,
					$order_field => $k,
				));
			} else {
				$this->db->insert($this->table, array(
					$path_field => $path,
					$data_field => $d,
				));
			}
		}

		$this->logger->logInfo("[DatabaseStorage] (writeBlobString) Blob $path: Wrote " . Numbers::filesizeDisplay($size) . " in " . count($data) . " segments");

		return $size;
	}


	/**
	 * @param \Application\DeskPRO\BlobStorage\Blob $blob
	 * @param resource $data
	 * @return int
	 */
	public function writeBlobFromStream(Blob $blob, $fp_source)
	{
		return $this->writeBlobFromStream($blob, stream_get_contents($fp_source));
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
		$id = $this->getDbPathId($blob);

		$q = $this->db->prepare("
			SELECT `{$this->data_field}`
			FROM `{$this->table}`
			WHERE `{$this->path_field}` = ?
			ORDER BY `{$this->order_field}` ASC
		");
		$q->execute(array($id));

		$data = '';
		$count = 0;
		while ($d = $q->fetchColumn(0)) {
			$count++;
			$data .= $d;
		}

		$q->closeCursor();

		$this->logger->logInfo("[DatabaseStorage] (readBlobString) Blob $id: Read " . Numbers::filesizeDisplay(strlen($data)) . " in " . $count . " segments");

		return $data;
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
}