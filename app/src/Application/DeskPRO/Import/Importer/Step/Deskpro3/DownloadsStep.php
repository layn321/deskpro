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

use Application\DeskPRO\Entity\DownloadCategory;
use Application\DeskPRO\Entity\Download;
use Application\DeskPRO\Entity\DownloadRevision;

class DownloadsStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Downloads';
	}

	public function run($page = 1)
	{
		$download_ids = $this->getOldDb()->fetchAllCol("SELECT id FROM files ORDER BY id ASC");
		if ($download_ids) {
			$this->logMessage(sprintf("Importing %d downloads", count($download_ids)));

			$start_time = microtime(true);

			$this->getDb()->beginTransaction();
			try {
				foreach ($download_ids as $did) {
					$this->processDownload($did);
				}
				$this->getDb()->commit();
			} catch (\Exception $e) {
				$this->getDb()->rollback();
				throw $e;
			}

			$end_time = microtime(true);
			$this->logMessage(sprintf("Done all downloads. Took %.3f seconds.", $end_time-$start_time));
		}
	}


	/**
	 * Process a download
	 */
	public function processDownload($download_id)
	{
		$download = $this->getOldDb()->fetchAssoc("SELECT * FROM files WHERE id = ?", array($download_id));

		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('file', $download['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$download['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Create it
		#------------------------------

		$new_category = $this->getEm()->find('DeskPRO:DownloadCategory', $this->getMappedNewId('file_cat', $download['category']));
		if (!$new_category) {
			$this->logMessage("{$download['id']} has an invalid category {$download['category']}, skipping");
			return;
		}

		$new_person = null;
		if ($download['techid']) {
			$new_person = $this->getEm()->find('DeskPRO:Person', $this->getMappedNewId('tech', $download['techid']));
		}
		if (!$new_person) {
			$new_person = $this->getEm()->getRepository('DeskPRO:Person')->findOneBy(array('can_admin' => true));
		}

		$new_blob = $this->getEm()->find('DeskPRO:Blob', $this->getMappedNewId('files-blob', $download['id']));
		if (!$new_category) {
			$this->logMessage("{$download['id']} has an invalid blob, skipping");
			return;
		}

		$new_download = new Download();
		$new_download->setStatusCode(Download::STATUS_PUBLISHED);
		$new_download->blob = $new_blob;
		$new_download->category = $new_category;
		$new_download->person = $new_person;
		$new_download->title = $download['filename'] ?: 'untitled';
		$new_download->content = $download['filename'] ?: 'untitled';
		$new_download->date_created = new \DateTime('@' . $download['timestamp']);
		$new_download->date_published = new \DateTime('@' . $download['timestamp']);

		$this->getEm()->persist($new_download);
		$this->getEm()->flush();

		$this->saveMappedId('file', $download['id'], $new_download->id);

		#------------------------------
		# Create the first revision
		#------------------------------

		$revision = new DownloadRevision();
		$revision->download = $new_download;
		$revision->blob = $new_download->blob;
		$revision->title = $new_download->title;
		$revision->content = $new_download->content;
		$revision->person = $new_person;
		$revision->date_created = $new_download->date_created;

		$this->getEm()->persist($revision);
		$this->getEm()->flush();
	}
}
