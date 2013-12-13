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

class Build1370440436 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add object_lang.ref_type and object_lang.ref_id");
		$this->execMutateSql("ALTER TABLE object_lang ADD ref_type VARCHAR(100) DEFAULT NULL, ADD ref_id INT DEFAULT NULL");
		$this->execMutateSql("CREATE INDEX prop_ref_type ON object_lang (ref_type, ref_id)");

		$default_lang_id = $this->container->getDb()->fetchColumn("
			SELECT value
			FROM settings
			WHERE name = 'core.default_language_id'
		");
		if (!$default_lang_id) {
			$default_lang_id = 1;
		}

		$this->out("Setting language_id to $default_lang_id on articles");
		$this->execMutateSql("UPDATE articles SET language_id = $default_lang_id WHERE language_id IS NULL");
	}
}