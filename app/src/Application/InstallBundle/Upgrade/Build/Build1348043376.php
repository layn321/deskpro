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

class Build1348043376 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add root field information to custom fields");

		$this->execMutateSql("ALTER TABLE custom_data_article ADD root_field_id INT DEFAULT NULL");
		$this->execMutateSql("ALTER TABLE custom_data_article ADD CONSTRAINT FK_1DB64F8C3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_article (id) ON DELETE CASCADE");
		$this->execMutateSql("CREATE INDEX IDX_1DB64F8C3F6A6D56 ON custom_data_article (root_field_id)");
		$this->execMutateSql("
			UPDATE custom_data_article
			INNER JOIN custom_def_article ON (custom_def_article.id = custom_data_article.field_id)
			SET custom_data_article.root_field_id = IF(custom_def_article.parent_id, custom_def_article.parent_id, custom_def_article.id)
		");

		$this->execMutateSql("ALTER TABLE custom_data_feedback ADD root_field_id INT DEFAULT NULL");
		$this->execMutateSql("ALTER TABLE custom_data_feedback ADD CONSTRAINT FK_92E9C37F3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_feedback (id) ON DELETE CASCADE");
		$this->execMutateSql("CREATE INDEX IDX_92E9C37F3F6A6D56 ON custom_data_feedback (root_field_id)");
		$this->execMutateSql("
			UPDATE custom_data_feedback
			INNER JOIN custom_def_feedback ON (custom_def_feedback.id = custom_data_feedback.field_id)
			SET custom_data_feedback.root_field_id = IF(custom_def_feedback.parent_id, custom_def_feedback.parent_id, custom_def_feedback.id)
		");

		$this->execMutateSql("ALTER TABLE custom_data_organizations ADD root_field_id INT DEFAULT NULL");
		$this->execMutateSql("ALTER TABLE custom_data_organizations ADD CONSTRAINT FK_20C5B8AC3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_organizations (id) ON DELETE CASCADE");
		$this->execMutateSql("CREATE INDEX IDX_20C5B8AC3F6A6D56 ON custom_data_organizations (root_field_id)");
		$this->execMutateSql("
			UPDATE custom_data_organizations
			INNER JOIN custom_def_organizations ON (custom_def_organizations.id = custom_data_organizations.field_id)
			SET custom_data_organizations.root_field_id = IF(custom_def_organizations.parent_id, custom_def_organizations.parent_id, custom_def_organizations.id)
		");

		$this->execMutateSql("ALTER TABLE custom_data_person ADD root_field_id INT DEFAULT NULL");
		$this->execMutateSql("ALTER TABLE custom_data_person ADD CONSTRAINT FK_621E55A53F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_people (id) ON DELETE CASCADE");
		$this->execMutateSql("CREATE INDEX IDX_621E55A53F6A6D56 ON custom_data_person (root_field_id)");
		$this->execMutateSql("
			UPDATE custom_data_person
			INNER JOIN custom_def_people ON (custom_def_people.id = custom_data_person.field_id)
			SET custom_data_person.root_field_id = IF(custom_def_people.parent_id, custom_def_people.parent_id, custom_def_people.id)
		");

		$this->execMutateSql("ALTER TABLE custom_data_ticket ADD root_field_id INT DEFAULT NULL");
		$this->execMutateSql("ALTER TABLE custom_data_ticket ADD CONSTRAINT FK_C16229703F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_ticket (id) ON DELETE CASCADE");
		$this->execMutateSql("CREATE INDEX IDX_C16229703F6A6D56 ON custom_data_ticket (root_field_id)");
		$this->execMutateSql("
			UPDATE custom_data_ticket
			INNER JOIN custom_def_ticket ON (custom_def_ticket.id = custom_data_ticket.field_id)
			SET custom_data_ticket.root_field_id = IF(custom_def_ticket.parent_id, custom_def_ticket.parent_id, custom_def_ticket.id)
		");
	}
}