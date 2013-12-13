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

namespace Application\DeskPRO\People\Helpers;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

/**
 * Figures out agent permissions
 */
class AgentPermissions implements \ArrayAccess, \Orb\Helper\ShortCallableInterface
{
	protected $person;

	protected $_allowed_ids = null;
	protected $_disallowed_ids = array();

	public function __construct(Entity\Person $person)
	{
		$this->person = $person;
	}

	public function getShortCallableNames()
	{
		return array(
			'getAgentPermissions' => '_getthis',
			'getDisallowedDepartments' => 'getDisallowedDepartments',
			'getAllowedDepartments' => 'getAllowedDepartments',
		);
	}

	// we use this because we implement arrayaccess
	// so the caller gets this, and can use it as an array.
	// So if the caller gets it through a another array access, it means
	// we support $whatever['thishelper']['thisobject'];
	public function _getthis() { return $this; }



	/**
	 * Check if the user is allowed to use a particular department
	 *
	 * @param int|Department $dep
	 * @return bool
	 */
	public function isDepartmentAllowed($dep, $context = 'tickets')
	{
		if ($dep instanceof Entity\Department) {
			$dep = $dep['id'];
		}

		return in_array($dep, $this->getAllowedDepartments($context));
	}



	/**
	 * Get an array of departments the user isn't allowed to see
	 *
	 * @return array
	 */
	public function getDisallowedDepartments($context = 'tickets')
	{
		if (isset($this->_disallowed_ids[$context])) {
			return $this->_disallowed_ids[$context];
		}

		$all_ids = App::getDataService('Department')->getIds();

		$allowed_ids = $this->getAllowedDepartments($context);

		$disallowed_ids = array_diff($all_ids, $allowed_ids);

		$this->_disallowed_ids[$context] = $disallowed_ids;

		return $this->_disallowed_ids[$context];
	}



	/**
	 * Get an array of departments the user is allowed to see
	 *
	 * @return array
	 */
	public function getAllowedDepartments($context = 'tickets')
	{
		if ($this->_allowed_ids !== null) {
			if (!isset($this->_allowed_ids[$context])) {
				return array();
			}

			return $this->_allowed_ids[$context];
		}

		$raw = App::getDb()->fetchAll("
			SELECT app, department_id
			FROM department_permissions
			WHERE person_id = ?
				AND name = 'full' AND value = 1
		", array($this->person->id));

		$this->_allowed_ids = array();
		foreach ($raw as $r) {
			if (!isset($this->_allowed_ids[$r['app']])) {
				$this->_allowed_ids[$r['app']] = array();
			}
			$this->_allowed_ids[$r['app']][] = $r['department_id'];

			$dep = App::getContainer()->getDataService('Department')->get($r['department_id']);
			if ($dep && $dep->parent) {
				$this->_allowed_ids[$r['app']][] = $dep->parent->getId();
			}
		}

		if (!isset($this->_allowed_ids[$context])) {
			return array();
		}

		return $this->_allowed_ids[$context];
	}


	public function offsetExists($offset)
	{
		$o = array('allowed_dep_ids', 'disallowed_dep_ids');
		return in_array($offset, $o);
	}
	public function offsetGet($offset)
	{
		if ($offset == 'allowed_dep_ids') {
			return $this->getAllowedDepartments();
		} else {
			return $this->getDisallowedDepartments();
		}
	}
	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException('offsetSet not supported');
	}
	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException('offsetUnset not supported');
	}
}
