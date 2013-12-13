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
 * @category Tickets
 */

namespace Application\DeskPRO\People\PermissionLoader;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\People\PersonContextInterface;

use Orb\Util\Arrays;

class Departments extends AbstractLoader implements NoCache, PersonContextInterface
{
	protected $has_init = false;

	/**
	 * An array of categories allowed for real, that we get by computing
	 * inheritance.
	 * @var array
	 */
	protected $allowed_cats = array('tickets' => array(), 'chat' => array());

	public function _init()
	{
		if ($this->has_init) {
			return;
		}
		$this->has_init = true;

		$in = implode(',', $this->getUsergroupIds());

		if (DP_INTERFACE == 'agent' || ($this->person->is_agent && DP_INTERFACE != 'user')) {
			$res = App::getDb()->fetchAll("
				SELECT department_id, app, name, value
				FROM department_permissions
				WHERE person_id = {$this->person->getId()}
			");
		} else {
			$res = App::getDb()->fetchAll("
				SELECT department_id, app, name, value
				FROM department_permissions
				WHERE usergroup_id IN($in)
			");
		}

		$parent_with_allowed_child = array(
			'tickets' => array(),
			'chat'    => array()
		);

		foreach ($res as $d) {
			$dep = App::getDataService('Department')->get($d['department_id']);

			$check = 'is_' . $d['app'] . '_enabled';
			if (!isset($dep[$check]) || !$dep[$check]) {
				continue;
			}

			$this->allowed_cats[$d['app']][$d['department_id']][$d['name']] = $d['value'];

			// With departments, if a child is allowed, then the parent is too since its just a wrapper
			if ($dep && $dep->parent) {
				$parent_with_allowed_child[$d['app']][$dep->parent->getId()] = true;

				$this->allowed_cats[$d['app']][$dep->parent->getId()][$d['name']] = 1;
			}
		}

		// Now for each parent, we need to make sure at least one child is allowed
		// because you can never use a parent without a child (e.g, dont want to be able to assign to a parent)
		foreach (App::getDataService('Department')->getParentNodes() as $dep) {
			if (!isset($parent_with_allowed_child['chat'][$dep->getId()])) {
				unset($this->allowed_cats['chat'][$dep->getId()]);
			}
			if (!isset($parent_with_allowed_child['tickets'][$dep->getId()])) {
				unset($this->allowed_cats['tickets'][$dep->getId()]);
			}
		}
	}

	/**
	 * Is a dep allowed?
	 *
	 * @return bool
	 */
	public function isAllowed($id, $app, $permission = 'full')
	{
		$this->_init();

		if (!empty($this->allowed_cats[$app][$id]['full'])) {
			return true;
		};

		return !empty($this->allowed_cats[$app][$id][$permission]);
	}


	/**
	 * Get an array of all allowed categories.
	 *
	 * @return array
	 */
	public function getAllowed($app, $permission = 'full')
	{
		$this->_init();

		$ids = array();
		foreach ($this->allowed_cats[$app] AS $id => $perms) {
			if (!empty($perms[$permission]) || !empty($perms['full'])) {
				$ids[$id] = $id;
			}
		}

		return $ids;
	}


	/**
	 * @param string $app
	 * @param string $permission
	 * @return int[]
	 */
	public function getAllowedIds($app, $permission = 'full')
	{
		$this->_init();

		if (empty($this->allowed_cats[$app])) {
			return array();
		}

		$ids = array();

		foreach ($this->allowed_cats[$app] as $id => $perms) {
			if (isset($perms[$permission]) && $perms[$permission]) {
				$ids[] = $id;
			}
		}

		return $ids;
	}


	/**
	 * Get an array of data we'll serialize
	 *
	 * @return array
	 */
	protected function serializeData()
	{
		$this->_init();
		return array(
			'allowed_cats'    => $this->allowed_cats,
		);
	}


	/**
	 * Initialize this object with an array of saved data
	 *
	 * @param array $data
	 */
	protected function unserializeData(array $data)
	{
		$this->allowed_cats     = $data['allowed_cats'];
	}
}
