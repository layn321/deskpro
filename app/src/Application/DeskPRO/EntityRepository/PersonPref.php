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

class PersonPref extends AbstractEntityRepository
{
	/**
	 * Fetch all preferences in a related group. A group is defined by some common
	 * prefix and a dot. For example: some.group.mysetting, some.group.myothersetting
	 *
	 * Supply some.group to get an array of mysetting and myothersetting.
	 *
	 * @param string $pref_group The pref group
	 * @param int $person_id
	 * @param bool $trim_group_prefix Trim off the group prefix in the array keys
	 * @return array
	 */
	public function getPrefgroupForPersonId($pref_group, $person_id, $trim_group_prefix = true)
	{
		$pref_group = rtrim($pref_group, '.'); // incase it was supplied with dot
		$pref_group_len = strlen($pref_group) + 1; // used with trimming below

		$statement = $this->getEntityManager()->getConnection()->executeQuery("
			SELECT name, value_str, value_array
			FROM people_prefs
			WHERE person_id = ? AND name LIKE ?
		", array($person_id, "$pref_group.%"));

		$ret_prefs = array();

		while ($pref = $statement->fetch(\PDO::FETCH_ASSOC)) {
			$pref_name = $pref['name'];
			if ($trim_group_prefix) {
				$pref_name = substr($pref_name, $pref_group_len);
			}

			if ($pref['value_array']) {
				$pref['value_array'] = @unserialize($pref['value_array']);
			}

			$ret_prefs[$pref_name] = is_array($pref['value_array']) ? $pref['value_array'] : $pref['value_str'];
		}

		return $ret_prefs;
	}

	public function getForPerson($pref_name, $person)
	{
		try {
			return $this->getEntityManager()->createQuery("
				SELECT p
				FROM DeskPRO:PersonPref p
				WHERE p.name = ?1 AND p.person = ?2
			")->setParameter(1, $pref_name)->setParameter(2, $person)->getSingleResult();
		} catch (\Exception $e) {
			return null;
		}
	}


	/**
	 * Get the value of a specific setting.
	 *
	 * @param string|array $pref_name A pref name or array of names
	 * @param int $person_id
	 * @return mixed
	 */
	public function getPrefForPersonId($pref_name, $person_id)
	{
		if (is_array($pref_name)) {
			$is_single = false;
			$args = array($person_id);
			$args = array_merge($args, $pref_name);

			$in_str = implode(',', array_fill(0, count($pref_name), '?'));

			$prefs = App::getDb()->fetchAllKeyed("
				SELECT name, value_str, value_array
				FROM people_prefs
				WHERE person_id = ? AND name IN ($in_str)
			", $args, 'name');

			if (!$prefs) {
				return array();
			}
		} else {
			$is_single = true;
			$pref = $this->getEntityManager()->getConnection()->fetchAssoc("
				SELECT value_str, value_array
				FROM people_prefs
				WHERE person_id = ? AND name = ?
			", array($person_id, $pref_name));

			if (!$pref) {
				return null;
			}

			$prefs = array($pref_name => $pref);
		}

		$ret = array();
		foreach ($prefs as $pref_name => $pref) {
			if ($pref['value_array']) {
				$pref['value_array'] = @unserialize($pref['value_array']);
			}

			$pref = is_array($pref['value_array']) ? $pref['value_array'] : $pref['value_str'];

			$ret[$pref_name] = $pref;
		}

		if ($is_single) {
			return array_pop($ret);
		} else {
			return $ret;
		}
	}

	/**
	 * @param $pref_name
	 * @param $person_id
	 * @return void
	 */
	public function deletePrefForPersonId($pref_name, $person_id)
	{
		$pref = $this->getEntityManager()->getConnection()->executeUpdate("
			DELETE FROM people_prefs
			WHERE person_id = ? AND name LIKE ? LIMIT 1
		", array($person_id, $pref_name.'%'));
	}


	public function savePref($person, $pref_id, $value)
	{
		$pref = $person->setPreference($pref_id, $value);

		App::getDb()->replace('people_prefs', array(
			'person_id' => $person->getId(),
			'name' => $pref['name'],
			'value_str' => $pref['value_str'],
			'value_array' => $pref['value_array'],
			'date_expire' => $pref['date_expire']
		));

		return $pref;
	}
}
