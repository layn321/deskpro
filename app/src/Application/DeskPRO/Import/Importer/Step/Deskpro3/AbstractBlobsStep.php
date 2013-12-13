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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

use Orb\Util\Strings;
use Orb\Data\ContentTypes;

// DP3's blob table doesnt store any info about the type of file, filesize etc
// But the individual content tables (ie ticket_attachments) does.
// So we'll just go through them one by one and process blobs in each
// - This has the nifty sideeffect of us easily ignoring abandoned blobs and that makes everyone smile

// Note this is JUST about importing blobs.
// Actually re-connecting these content tables is up to the steps that import that type of content
// I.e., we import blobs from ticket attachments into our blobs table, but the tickets step will actually make the relevant
// ticket attachment records.
abstract class AbstractBlobsStep extends AbstractDeskpro3Step
{
	abstract public function getTable();

	public function countPages()
	{
		$table = $this->getTable();

		$table_exists = $this->getOldDb()->fetchColumn("SHOW TABLES LIKE '$table'");
		if (!$table_exists) {
			return 1;
		}

		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM $table");
		if (!$count) {
			return 1;
		}

		$pages = ceil($count / 250);
		return $pages;
	}

	public function run($page = 1)
	{
		$table = $this->getTable();

		$table_exists = $this->getOldDb()->fetchColumn("SHOW TABLES LIKE '$table'");
		if (!$table_exists) {
			return;
		}

		$batch = $this->getBatch($table, $page - 1);

		$batch_ids = array();
		foreach ($batch as $b) {
			$batch_ids[] = $b['id'];
		}

		$get_existing = array();
		if ($batch_ids) {
			$batch_ids = implode(',', $batch_ids);
			$get_existing = $this->db->fetchAllCol("
				SELECT old_id
				FROM import_map
				WHERE typename = '$table-blob' AND old_id IN ($batch_ids)
			");

			if ($get_existing) {
				$get_existing = array_combine($get_existing, $get_existing);
			}
		}

		$this->getDb()->beginTransaction();
		try {
			foreach ($batch as $r) {
				if (isset($get_existing[$r['id']])) {
					continue;
				}

				$this->processBlob($table, $r);
			}

			$this->importer->flushSaveMappedIdBuffer();
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}

	public function processBlob($table, $record)
	{
		$record_id = $record['id'];
		$blob = $this->getOldDb()->fetchAssoc("SELECT * FROM blobs WHERE id = ?", array($record['blobid']));

		if (!$blob) {
			return;
		}

		$filetype = ContentTypes::getContentTypeFromExtension($record['extension']);
		if (!$filetype) {
			$filetype = 'application/octet-stream';
		}

		if (isset($blob['filepath']) && $blob['filepath']) {
			$file = @file_get_contents($this->importer->getConfig('existing_attachment_files') . '/' . $blob['filepath']);
			if (!$file) {
				$this->logMessage("Blob {$blob['id']} is missing from filesystem: " . $blob['filepath']);
				$file = '';
			}
		} else {
			$file = $this->getOldDb()->fetchAllCol("
				SELECT blobdata
				FROM blob_parts
				WHERE blobid = ?
				ORDER BY displayorder ASC
			", array($record['blobid']));
			$file = implode('', $file);
		}

		$dim_w = $dim_h = 0;
		if (in_array($filetype, ContentTypes::getImageContentTypes())) {
			$tmpfname = @tempnam(sys_get_temp_dir(), "dpblob_");
			if ($tmpfname && @file_put_contents($tmpfname, $file)) {
				$imageinfo = @getimagesize($tmpfname);
				if ($imageinfo) {
					$dim_w = $imageinfo[0];
					$dim_h = $imageinfo[1];
				}
			}
			@unlink($tmpfname);
		}

		$blob = $this->getContainer()->getBlobStorage()->createBlobRecordFromString(
			$file,
			\Orb\Util\Util::coalesce($record['filename'], 'file'),
			$filetype
		);
		$new_blob_id = $blob->getId();

		$this->getDb()->update('blobs', array(
			'filename' => $record['filename'],
			'filesize' => $record['filesize'],
			'dim_w' => $dim_w,
			'dim_h' => $dim_h,
			'date_created' => date('Y-m-d H:i:s', $record['timestamp']),
		), array('id' => $new_blob_id));

		$this->saveMappedId("$table-blob", $record['id'], $new_blob_id, true);
	}


	/**
	 * @param $page
	 * @return array
	 */
	public function getBatch($table, $page)
	{
		$start = $page * 250;
		$ids = $this->getOldDb()->fetchAll("SELECT * FROM $table ORDER BY id ASC LIMIT $start, 250");

		return $ids;
	}
}
