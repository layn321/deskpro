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

namespace Application\DeskPRO\Import\Importer\Step\Zendesk;

use Orb\Data\ContentTypes;

class UserPicturesStep extends AbstractZendeskStep
{
	const PERPAGE = 250;

	public static function getTitle()
	{
		return 'Download User Pictures';
	}

	public function countPages()
	{
		if ($this->importer->getConfig('fast_import')) {
			return 0;
		}

		$count = $this->db->fetchColumn("
			SELECT COUNT(*) FROM import_datastore
			WHERE typename LIKE 'attach.person_picture.%'
		");

		if (!$count) {
			return 1;
		}

		return ceil($count / self::PERPAGE);
	}

	public function run($page = 1)
	{
		if ($this->importer->getConfig('fast_import')) {
			return 0;
		}

		$perpage = self::PERPAGE;
		$start = ($page - 1) * $perpage;
		$batch = $this->db->fetchAllCol("
			SELECT data FROM import_datastore
			WHERE typename LIKE 'attach.person_picture.%'
			ORDER BY typename ASC
			LIMIT $start, $perpage
		");

		foreach ($batch as $n) {
			$n = unserialize($n);
			if ($n) {
				$this->getDb()->beginTransaction();
				try {
					$this->processBlob($n);
					$this->getDb()->commit();
				} catch (\Exception $e) {
					$this->getDb()->rollback();
					throw $e;
				}
			}
		}
	}

	/**
	 * @param array $blob_info
	 */
	public function processBlob($blob_info)
	{
		$tmpfile = tempnam(sys_get_temp_dir(), 'dp');

		$context = stream_context_create(array(
			'http' => array(
				'header' => 'Authorization: Basic ' . base64_encode($this->zd->getZendeskApiUserId() . '/token:' . $this->zd->getZendeskApiKey(false))
			)
		));

		$tmpcontent = file_get_contents($blob_info['url'], null, $context);
		if (!$tmpcontent) {
			sleep(2);
			$tmpcontent = file_get_contents($blob_info['url'], null, $context);
			if ($tmpcontent) {
				sleep(2);
				$tmpcontent = file_get_contents($blob_info['url'], null, $context);
				if (!$tmpcontent) {
					$this->logMessage("Failed copy blob: " . print_r($blob_info,1));
				}
			}
			return;
		}
		file_put_contents($tmpfile, $tmpcontent);
		unset($tmpcontent);

		$dim_w = $dim_h = 0;
		if (in_array($blob_info['content_type'], ContentTypes::getImageContentTypes())) {
			$imageinfo = @getimagesize($tmpfile);
			if ($imageinfo) {
				$dim_w = $imageinfo[0];
				$dim_h = $imageinfo[1];
			}
		}

		$blob = $this->getContainer()->getBlobStorage()->createBlobRecordFromFile(
			$tmpfile,
			$blob_info['filename'],
			$blob_info['content_type']
		);
		$new_blob_id = $blob->getId();

		$this->db->update('blobs', array(
			'filename' => $blob_info['filename'],
			'filesize' => filesize($tmpfile),
			'dim_w' => $dim_w,
			'dim_h' => $dim_h,
			'date_created' => date('Y-m-d H:i:s'),
		), array('id' => $new_blob_id));

		$this->db->update('people', array(
			'picture_blob_id' => $new_blob_id
		), array('id' => $blob_info['person_id']));

		@unlink($tmpfile);
	}
}
