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
 * @subpackage
 */

namespace Application\InstallBundle\Upgrade\Build;

class Build1340053454 extends AbstractBuild
{
	public function run()
	{
		$style_ids = $this->container->getDb()->fetchAllCol("SELECT id FROM styles");

		if (count($style_ids) != 1 || !in_array(1, $style_ids)) {
			$this->out("Resetting styles");
			$this->execMutateSql("DELETE FROM styles");
			$this->execMutateSql("
				INSERT INTO `styles` (`id`, `parent_id`, `logo_blob_id`, `css_blob_id`, `title`, `note`, `css_dir`, `css_updated`, `options`, `created_at`)
				VALUES (1, NULL, NULL, NULL, 'Default Style', 'Default Style', 'stylesheets/user', '2012-06-18 20:20:20', X'613A303A7B7D', '2012-06-18 20:20:20');
			");
		} else {
			$this->out("Style record okay: " . implode(',', $style_ids));
		}
	}
}