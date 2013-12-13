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

class Build1345106506 extends AbstractBuild
{
	public function run()
	{
		$this->out("Support for favoriting reports");
		$this->execMutateSql("CREATE TABLE report_builder_favorite (report_builder_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_CCD5CB1186DD4ADF (report_builder_id), INDEX IDX_CCD5CB11217BBB47 (person_id), PRIMARY KEY(report_builder_id, person_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE report_builder_favorite ADD CONSTRAINT FK_CCD5CB1186DD4ADF FOREIGN KEY (report_builder_id) REFERENCES report_builder (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE report_builder_favorite ADD CONSTRAINT FK_CCD5CB11217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE");
	}
}