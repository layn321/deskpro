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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\FormModel;

use Application\DeskPRO\App;

use Orb\Util\Arrays;

class EditAgent
{
	protected $person;

	protected $_set_teams;
	protected $_set_deps;
	protected $_set_groups;

	public function __construct($person)
	{
		$this->person = $person;
	}

	############################################################################
	# getters
	############################################################################

	public function getName() { return $this->person['name']; }
	public function getPassword() { return ''; }

	public function getEmail()
	{
		if ($this->person->primary_email) {
			return $this->person->primary_email['email'];
		}
		return '';
	}

	public function getAccessZones()
	{
		$access_zones = array();

		$access = $this->person->agent->getAccess();
		$props = $access->getFieldKeys();

		foreach ($props as $p) {
			// The property is an access_ property
			if (strpos($p, 'access_') === 0) {
				// The user has access, so add it to the array
				if ($access[$p]) {
					$access_zones[] = $p;
				}
			}
		}

		return $access_zones;
	}

	public function getUsergroups()
	{
		$usergroups = array();
		foreach ($this->person->usergroups as $ug) {
			$usergroups[] = $ug['id'];
		}

		return $usergroups;
	}

	public function getAgentTeams()
	{
		return $this->person->agent->getTeamIds();
	}

	public function getAllowedDepartments()
	{
		return $this->person->agent->getAllowedDepartmentIds();
	}

	############################################################################
	# setters
	############################################################################

	public function setName($v) { $this->person['name'] = $v; }
	public function setPassword($v)
	{
		if ($v) {
			$this->person['password'] = $v;
		}
	}

	public function setEmail($v)
	{
		if ($this->person->primary_email) {
			$this->person->primary_email['email'] = $v;
		} else {
			$this->person->addEmailAddress($v);
		}
	}

	public function setAccessZones($zones)
	{
		$access = $this->person->agent->getAccess();

		$access = $this->person->agent->getAccess();
		$props = $access->getFieldKeys();

		foreach ($props as $p) {
			// The property is an access_ property
			if (strpos($p, 'access_') === 0) {
				// The user has access, so add it to the array
				if (in_array($p, $zones)) {
					$access[$p] = true;
				} else {
					$access[$p] = false;
				}
			}
		}
	}

	public function setAgentTeams($teams)
	{
		$this->_set_teams = $teams;
	}

	public function setUsergroups($usergroups)
	{
		$this->_set_groups = $usergroups;
	}

	public function setAllowedDepartments($departments)
	{
		$access = $this->person->agent->getAccess();
		$access->departments->clear();

		foreach ($departments as $dep) {
			$dep = App::getEntityRepository('DeskPRO:Department')->find($dep);
			if ($dep) {
				$access->departments->add($dep);
			}
		}
	}

	############################################################################
	# persit
	############################################################################

	public function persist()
	{
		App::getOrm()->persist($this->person);
		App::getOrm()->persist($this->person->agent->getAccess());

		$this->_persistTeams();
		$this->_persistGroups();
	}

	protected function _persistTeams()
	{
		$db = App::getDb();
		$db->delete('agent_team_members', array('person_id' => $this->person['id']));

		$teams = $this->_set_teams;
		if (!$teams) return;

		foreach ($teams as $t) {
			$db->insert('agent_team_members', array(
				'person_id' => $this->person['id'],
				'team_id' => $t
			));
		}
	}

	public function _persistGroups()
	{
		$db = App::getDb();
		$db->delete('person2usergroups', array('person_id' => $this->person['id']));

		$usergroups = $this->_set_groups;
		if (!$usergroups) return;

		foreach ($usergroups as $u) {
			$db->insert('person2usergroups', array(
				'person_id' => $this->person['id'],
				'usergroup_id' => $u
			));
		}
	}
}
