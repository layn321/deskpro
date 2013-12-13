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

class Build1348573081 extends AbstractBuild
{
	public function run()
	{
		$this->out("Processing bad people_emails_validating");

		$validating = $this->container->getDb()->fetchAll("
			SELECT
				people_emails_validating.id as validating_id, people_emails_validating.email as validating_email,
				people.id AS person_id, people.primary_email_id AS person_primary_email_id
			FROM people_emails_validating
			LEFT JOIN people ON (people.id = people_emails_validating.person_id)
			WHERE person_id IS NOT NULL
		");

		foreach ($validating as $valid) {
			$email_address = strtolower($valid['validating_email']);

			$email_exist = $this->container->getDb()->fetchAssoc("
				SELECT *
				FROM people_emails
				WHERE email = ?
			", array($email_address));

			if ($email_exist) {
				continue;
			}

			$this->container->getDb()->insert('people_emails', array(
				'person_id' => $valid['person_id'],
				'email' => $email_address,
				'email_domain' => \Orb\Util\Strings::extractRegexMatch('#@(.*?)$#', $email_address),
				'is_validated' => true,
				'date_created' => date('Y-m-d H:i:s'),
				'date_validated' => date('Y-m-d H:i:s'),
			));

			$email_id = $this->container->getDb()->lastInsertId();

			if (!$valid['person_primary_email_id']) {
				$this->container->getDb()->insert('people', array(
					'primary_email_id' => $email_id,
				));
			}

			$this->container->getDb()->executeUpdate("
				UPDATE tickets
				SET person_email_validating_id = NULL
				WHERE person_email_validating_id = ?
			", array($valid['validating_id']));

			$this->container->getDb()->executeUpdate("
				DELETE FROM people_emails_validating
				WHERE id = ?
			", array($valid['validating_id']));
		}
	}
}