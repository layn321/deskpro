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

use Orb\Util\Strings;

class Build1375958085 extends AbstractBuild
{
	public function run()
	{
		// A previous build has object_lang created with bad charset
		// This was fixed on the table, but its possible the fields need updating too
		$show = $this->container->getDb()->fetchColumn("SHOW CREATE TABLE `object_lang`", array(), 1);
		$show = str_replace('`', '', $show);
		$show = strtolower($show);

		// Check if the value part has 'character set' bit which will override the table
		$value_middle = Strings::extractRegexMatch('#value\s+longtext(.*?)not null#', $show);
		if (strpos($value_middle, 'character set') !== false) {
			$this->out("Correct charset on object_lang");
			$this->execMutateSql("ALTER TABLE `object_lang` CHANGE `value` `value` LONGTEXT NOT NULL");
		}
	}
}