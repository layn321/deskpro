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

use \Doctrine\ORM\EntityRepository;

class Cache extends AbstractEntityRepository
{
	public function load($id)
	{
		return false;
		$data = App::getDb()->fetchColumn("SELECT data FROM cache WHERE id = ?", array($id));

		if (!$data) {
			return false;
		}

		$data = @unserialize($data);

		if (isset($data['VALUE'])) {
			return $data['VALUE'];
		}

		return $data;
	}

	public function save($id, $data, $lifetime = null)
	{
		if (!is_array($data)) {
			$data = array('VALUE' => $data);
		}

		$data = serialize($data);

		$expire = null;
		if ($lifetime) {
			$expire = date('Y-m-d H:i:s', time()+$lifetime);
		}

		App::getDb()->executeUpdate(
			"REPLACE INTO cache SET id = ?, data = ?, date_expire = ?", array(
			$id, $data, $expire
		));

		return true;
	}

	public function delete($id)
	{
		return App::getDb()->executeUpdate("DELETE FROM cache WHERE id LIKE ?", array($id . '%'));
	}

	/**
	 * Clean up all expired cache entries
	 */
	public function cleanExpired()
	{
		return App::getDb()->executeUpdate("DELETE FROM cache WHERE date_expire < ?", array(date('Y-m-d H:i:s')));
	}
}
