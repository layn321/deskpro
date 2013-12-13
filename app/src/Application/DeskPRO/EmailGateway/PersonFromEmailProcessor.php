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
 */

namespace Application\DeskPRO\EmailGateway;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\EmailGateway\Reader\AbstractReader;
use Application\DeskPRO\EmailGateway\Reader\Item\EmailAddress;
use DeskPRO\Kernel\KernelErrorHandler;

/**
 * This finds a user based on the email sent, or creates a new user
 * from it.
 */
class PersonFromEmailProcessor
{
	/**
	 * When we have any email from a user, perform basic routines on the user its from.
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function passPerson(EmailAddress $from, Entity\Person $person)
	{
		if (!$person['first_name'] AND !$person['last_name']) {
			if ($from->getName()) {
				$person['name'] = $from->getName();
				App::getOrm()->persist($person);
			}
		}
	}



	/**
	 * Finds a person based on the From in the email address.
	 *
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function findPerson(EmailAddress $from)
	{
		$person = App::getEntityRepository('DeskPRO:Person')->findOneByEmail($from->getEmail());
		if ($person) {
			$this->passPerson($from, $person);
			return $person;
		} else {
			foreach (App::getDataService('Usersource')->getAllUsersources() as $us) {
				try {
					/** @var $adapter \Application\DeskPRO\Usersource\Adapter\AbstractAdapter */
					$adapter = $us->getAdapter();

					if (!$adapter->isCapable('find_identity')) {
						continue;
					}

					$identity = $adapter->findIdentityByInput($from->getEmail());
					if (!$identity) {
						continue;
					}

					$login_processor = new \Application\DeskPRO\Auth\LoginProcessor($us, $identity);
					$person = $login_processor->getPerson();

					$this->passPerson($from, $person);
					return $person;
				} catch (\Exception $e) {
					KernelErrorHandler::logException($e, false, 'gateway_usersource_error');
				}
			}
		}

		return null;
	}


	/**
	 * Finds a person based on the From in the email address.
	 *
	 * @param string $email_address The email address as a string
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function findPersonByEmailAddress($email_address)
	{
		$email = new EmailAddress();
		$email->email = $email_address;

		return $this->findPerson($email);
	}



	/**
	 * Creates a person based on the From email address.
	 *
	 * @param $from
	 * @param bool $do_validated True to validate user, false to use whatever is default
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function createPerson(EmailAddress $from, $do_validated = false)
	{
		$person = Entity\Person::newContactPerson();
		$person->creation_system = 'gateway.person';
		$person->name = $from->getNameUtf8();

		$email = new \Application\DeskPRO\Entity\PersonEmail();
		$email->setEmail($from->getEmail());
		$email->person = $person;

		if (!$do_validated) {
			// If not explicitly validated, then they arent valdiated
			// The validated flag is switched on NewTicketAction if validation
			// is not required. Its like this so triggers can affect the validation setting.
			$email->is_validated = false;
			$person->is_confirmed = false;
			$person->getChangeTracker()->recordExtra('email_validating', $from->getEmail());
		} else {
			$email->is_validated = true;
			$person->is_confirmed = true;
		}

		if (App::getSetting('core.user_mode') == 'require_reg_agent_validation') {
			$person->is_agent_confirmed = false;
		}

		App::getOrm()->persist($person);
		App::getOrm()->flush($person);

		$person->addEmailAddress($email);
		App::getOrm()->persist($person);
		App::getOrm()->persist($email);

		App::getOrm()->flush($person);
		App::getOrm()->flush($email);

		return $person;
	}
}
