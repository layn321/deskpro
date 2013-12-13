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
 * @subpackage WorkerProcess
 */

namespace Application\DeskPRO\WorkerProcess\Job;

use Application\DeskPRO\Mail\QueueProcessor\Database as DatabaseQueueProcessor;

use Application\DeskPRO\App;
use Application\DeskPRO\Log\Logger;
use Application\DeskPRO\Mail\Transport\DelegatingTransport;

/**
 * Updates the sitemap file
 */
class SitemapFile extends AbstractJob
{
	const DEFAULT_INTERVAL = 604800; // 7 days

	public function run()
	{
		$old_sitemap_file = App::getSetting('core.sitemap_blob_id');

		if ($old_sitemap_file) {
			try {
				$blob = App::getOrm()->find('DeskPRO:Blob', $old_sitemap_file);
				if ($blob) {
					App::getContainer()->getBlobStorage()->deleteBlobRecord($blob);
				}
			} catch (\Exception $e) {}
		}

		$gen = new \Application\DeskPRO\Portal\SitemapGenerator(App::getSetting('core.deskpro_url'), App::getOrm(), App::getRouter());
		$file = $gen->getXml();

		$blob = App::getContainer()->getBlobStorage()->createBlobRecordFromString(
			$file,
			'sitemap.xml',
			'text/xml',
			array('sys_name' => 'sitemap_xml')
		);
		$blob_id = $blob->getId();
	}
}
