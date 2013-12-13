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

class Build1351871191 extends AbstractBuild
{
	public function run()
	{
		$this->out("Allow multiple glossary words per definition");
		$this->execMutateSql("CREATE TABLE glossary_word_definitions (id INT AUTO_INCREMENT NOT NULL, definition LONGTEXT NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE glossary_words ADD definition_id INT NOT NULL");
		$this->execMutateSql("INSERT INTO glossary_word_definitions SELECT id, content FROM glossary_words");
		$this->execMutateSql("UPDATE glossary_words SET definition_id = id");
		$this->execMutateSql("ALTER TABLE glossary_words DROP content");
		$this->execMutateSql("ALTER TABLE glossary_words ADD CONSTRAINT FK_1A8003DAD11EA911 FOREIGN KEY (definition_id) REFERENCES glossary_word_definitions (id) ON DELETE CASCADE");
		$this->execMutateSql("CREATE INDEX IDX_1A8003DAD11EA911 ON glossary_words (definition_id)");
		$this->execMutateSql("CREATE UNIQUE INDEX UNIQ_1A8003DAC3F17511 ON glossary_words (word)");
	}
}