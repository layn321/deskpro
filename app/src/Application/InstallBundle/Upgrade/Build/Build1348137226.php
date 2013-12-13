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

class Build1348137226 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add support for ticket charges/billing");
		$this->execMutateSql("CREATE TABLE ticket_charges (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, person_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, agent_id INT DEFAULT NULL, charge_time INT DEFAULT NULL, charge NUMERIC(10, 2) DEFAULT NULL, comment VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_36230948700047D2 (ticket_id), INDEX IDX_36230948217BBB47 (person_id), INDEX IDX_3623094832C8A3DE (organization_id), INDEX IDX_362309483414710B (agent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE ticket_charges ADD CONSTRAINT FK_36230948700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE ticket_charges ADD CONSTRAINT FK_36230948217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE ticket_charges ADD CONSTRAINT FK_3623094832C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE ticket_charges ADD CONSTRAINT FK_362309483414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL");
	}
}