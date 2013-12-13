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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;

use Application\DeskPRO\Entity;
use \Doctrine\ORM\EntityRepository;
use Orb\Util\Util;

class Setting extends AbstractEntityRepository
{
	/**
	 * Update a database setting.
	 *
	 * This updates the database but not the currently loaded set of settings. If you need
	 * the value to take affect immediately (this process), then use the Settings service,
	 *
	 * <code>$this->container->get('settings')->setSetting($name, $value);</code>
	 *
	 * @param  string $name  The name of the setting
	 * @param  mixed  $value The value to set. Null means any existing value will be unset
	 * @return \Application\DeskPRO\Entity\Setting
	 */
	public function updateSetting($name, $value)
	{
		$db = $this->_em->getConnection();

		if ($value !== null) {
			$db->executeUpdate("
				INSERT INTO settings
					(name, value)
				VALUES
					(?, ?)
				ON DUPLICATE KEY UPDATE
					value = VALUES(value)
			", array($name, $value));
		} else {
			$db->delete('settings', array('name' => $name));
		}
	}
}
