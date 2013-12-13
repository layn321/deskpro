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

namespace Application\DeskPRO\CustomFields;

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\CustomDefAbstract;
use Doctrine\ORM\EntityManager;
use Orb\Auth\Identity;
use Application\DeskPRO\Entity\Usersource;
use Application\DeskPRO\Entity\Person;
use Orb\Util\Arrays;

class PersonFieldManager extends FieldManager
{
	public function copyUsersourceData(Person $person, Identity $identity, Usersource $usersource)
	{
		$save_data = array();

		foreach ($this->getDefinedFields() as $field) {
			if ($field->handler_class != 'Application\\DeskPRO\\CustomFields\\Handler\\Data') {
				continue;
			}

			if ($field->getOption('usersource_id') && $field->getOption('usersource_id') != $usersource->getId()) {
				continue;
			}

			$field_name = $field->getOption('field_name');
			$raw_data   = $identity->getRawData();

			$val = Arrays::keyAsPath($raw_data, $field_name, '/', null);

			if ($val === null) {
				continue;
			}

			$save_data['field_' . $field->getId()] = $val;
		}

		if ($save_data) {
			$this->saveFormToObject($save_data, $person, true);
		}
	}
}
