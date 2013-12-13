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

namespace Application\DeskPRO\People\Helpers;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Usergroup;
use Application\DeskPRO\Entity\PermissionCache;
use Application\DeskPRO\People\PersonContextInterface;

use Orb\Util\Arrays;
use Orb\Util\Util;

/**
 * The permission manager takes care of loading effective permissions for a user.
 */
class PermissionsManager implements \Orb\Helper\ShortCallableInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * All the users usergroups
	 * @string array
	 */
	protected $usergroup_ids;

	/**
	 * Groups we got inherited from an org
	 *
	 * @string array
	 */
	protected $org_usergroup_ids;

	/**
	 * The usergroups key for all the users groups
	 * @var string
	 */
	protected $usergroups_key;

	/**
	 * Types that we know we want, but havent been loaded yet
	 * @param array
	 */
	protected $queued_types = array();

	/**
	 * Initialized loaders
	 * @var \Application\DeskPRO\People\PermissionLoader\AbstractLoader[]
	 */
	protected $loaders = array();

	/**
	 * Initialized checkers
	 * @var \Application\DeskPRO\People\PermissionChecker\AbstractChecker[]
	 */
	protected $checkers = array();

	/**
	 * @var array
	 */
	protected $dirty_caches = array();

	/**
	 * @var bool
	 */
	protected $admin_god_mode = false;

	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function __construct(Person $person)
	{
		$this->person = $person;

		$this->usergroup_ids = App::getDb()->fetchAllCol("
			SELECT person2usergroups.usergroup_id
			FROM person2usergroups
			LEFT JOIN usergroups ON usergroups.id = person2usergroups.usergroup_id
			WHERE person2usergroups.person_id = ? AND usergroups.is_enabled = 1
		", array($this->person['id']));

		$everyone_ug = App::getDataService('Usergroup')->find(Usergroup::EVERYONE_ID);
		if ($everyone_ug && $everyone_ug->is_enabled) {
			$this->usergroup_ids[] = Usergroup::EVERYONE_ID;
		} else {
			$this->usergroup_ids[] = 0;
		}

		$reg_ug = App::getDataService('Usergroup')->find(Usergroup::REG_ID);
		if ($person->getId() && $person->is_agent_confirmed && $reg_ug->is_enabled) {
			$this->usergroup_ids[] = Usergroup::REG_ID;
		}

		// And org ones...
		$this->org_usergroup_ids = array();
		if ($this->person->organization) {
			$this->org_usergroup_ids = App::getDb()->fetchAllCol("
				SELECT organization2usergroups.usergroup_id
				FROM organization2usergroups
				LEFT JOIN usergroups ON usergroups.id = organization2usergroups.usergroup_id
				WHERE organization2usergroups.organization_id = ? AND usergroups.is_enabled = 1
			", array($this->person->organization['id']));

			if ($this->org_usergroup_ids) {
				$this->usergroup_ids = array_merge($this->usergroup_ids, $this->org_usergroup_ids);
				$this->usergroup_ids = array_unique($this->usergroup_ids);
			}
		}

		sort($this->usergroup_ids, SORT_NUMERIC);

		$this->usergroups_key = PermissionCache::generateUsergroupSetKey($this->usergroup_ids);

		if ($this->person->is_agent) {
			$this->usergroups_key = md5($this->usergroups_key . 'agent-' . $this->person->id);
		}

		\DpShutdown::add(array($this, 'flushCache'));
	}


	/**
	 * Admin mode enables all permissions when viewing the user interface (usually through the portal editor).
	 *
	 * @return bool
	 */
	public function enableAdminMode()
	{
		$this->admin_god_mode = true;
	}


	/**
	 * Get an array of usergroups this user has applied to them
	 *
	 * @return array
	 */
	public function getUsergroupIds()
	{
		return $this->usergroup_ids;
	}



	/**
	 * Of the groups we belong to, get the ones we inherited from our organization.
	 *
	 * @return array
	 */
	public function getOrganizationUsergroupIds()
	{
		return $this->org_usergroup_ids;
	}



	/**
	 * Get the usergroups set key
	 *
	 * @return string
	 */
	public function getUsergroupSetKey()
	{
		return $this->usergroups_key;
	}



	/**
	 * Load permissions of a particular type.
	 *
	 * @param $name
	 */
	public function loadPermissions($name)
	{
		$args = func_get_args();

		foreach ($args as $name) {
			if (isset($this->loaders[strtolower($name)])) {
				continue;
			}

			if (!class_exists($this->getLoaderClass($name))) {
				throw new \InvalidArgumentException("No loader for permission type `$name`");
			}

			$this->queued_types[] = $name;
		}
	}



	/**
	 * This loads up the queued permission types. The reason they're queued is so we
	 * can fetch multiple records from the cache at once, which is helpful when
	 * the cache is a slow-cache such as the db.
	 *
	 * @return void
	 */
	public function _loadQueued()
	{
		#-------------------------
		# Fetch from the cache first
		#-------------------------

		$caches = App::getEntityRepository('DeskPRO:PermissionCache')->loadPermissionTypes($this->usergroups_key, $this->person->getId(), $this->queued_types);

		foreach ($caches as $cache) {
			$loader = $cache->perms;
			$name = Util::getBaseClassname($loader);

			if ($loader instanceof PersonContextInterface) {
				$loader->setPersonContext($this->person);
			}

			$this->loaders[strtolower($name)] = $loader;
		}

		#-------------------------
		# Load the rest for the first time
		#-------------------------

		$do_cache = array();
		$queued_types = $this->queued_types;
		$this->queued_types = array();

		foreach ($queued_types as $name) {

			if (isset($this->loaders[strtolower($name)])) {
				continue;
			}

			$class = $this->getLoaderClass($name);
			$loader = new $class($this->usergroup_ids);

			if ($loader instanceof PersonContextInterface) {
				$loader->setPersonContext($this->person);
			}

			$this->loaders[strtolower($name)] = $loader;

			if (!($loader instanceof \Application\DeskPRO\People\PermissionLoader\NoCache)) {
				$this->dirty_caches[] = PermissionCache::newFromLoader($loader, $this->person->getId());
			}
		}
	}



	/**
	 * Using property overloading to give direct access to individual loaders.
	 *
	 * @param  $name
	 * @return \Application\DeskPRO\People\PermissionLoader\AbstractLoader
	 */
	public function __get($name)
	{
		return $this->get($name);
	}



	/**
	 * Get a loader
	 *
	 * @param  $name
	 * @return \Application\DeskPRO\People\PermissionLoader\AbstractLoader
	 */
	public function get($name)
	{
		$namel = strtolower($name);

		if (preg_match('#Checker$#', $name)) {
			if (!isset($this->checkers[$namel])) {
				$class = 'Application\\DeskPRO\\People\\PermissionChecker\\' . $name;
				if (!$class) {
					throw new \InvalidArgumentException("Unknown permission checker `{$name}`");
				}

				$this->checkers[$namel] = new $class($this->person);
			}

			return $this->checkers[$namel];
		}

		if (!isset($this->loaders[$namel])) {
			$this->loadPermissions($name);
			$this->_loadQueued();
		}

		return $this->loaders[$namel];
	}


	/**
	 * Get a normal usergruop permission.
	 * This is a shortcut for the usergroups loader.
	 *
	 * @param $name
	 * @return bool
	 */
	public function hasPerm($name)
	{
		if ($this->admin_god_mode && in_array($name, array('articles.use', 'feedback.use', 'downloads.use', 'news.use', 'chat.use'))) {
			return true;
		}

		if ($name == 'agent_tickets.create') {
			if (!App::getDataService('Department')->getPersonDepartments($this->person, 'tickets', array(), 'assign')) {
				return false;
			}
		}

		if ($name == 'articles.use' && !App::getSetting('core.apps_kb')) {
			return false;
		}
		if ($name == 'feedback.use' && !App::getSetting('core.apps_feedback')) {
			return false;
		}
		if ($name == 'downloads.use' && !App::getSetting('core.apps_downloads')) {
			return false;
		}
		if ($name == 'news.use' && !App::getSetting('core.apps_news')) {
			return false;
		}
		if ($name == 'chat.use' || $name == 'agent_chat.use') {
			if (!App::getSetting('core.apps_chat')) {
				return false;
			}

			if (!$this->get('Departments')->getAllowed('chat')) {
				return false;
			}
		}
		if ($name == 'articles.comment' || $name == 'downloads.comment' || $name == 'news.comment') {
			if (!App::getSetting('user.publish_comments')) {
				return false;
			}
		}

		return $this->get('Usergroups')->getPermission($name) ? true : false;
	}


	/**
	 * Flush any pending permission group caches that need to be written
	 */
	public function flushCache()
	{
		if (!$this->dirty_caches) {
			return;
		}

		try {
			foreach ($this->dirty_caches as $c) {
				$insert_cache = array(
					'name'            => $c->getName(),
					'usergroup_key'   => $c->getUsergroupKey(),
					'usergroup_ids'   => implode(',', $c->getUsergroupIds()),
					'perms'           => serialize($c->getPerms())
				);

				App::getDb()->replace('permissions_cache', $insert_cache);
			}
		} catch (\Exception $e) {
			$info = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
			\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($info);
		}

		$this->dirty_caches = array();
	}


	/**
	 * Get the full name of the loader for a given permission type name.
	 *
	 * @param  $name
	 * @return string
	 */
	public function getLoaderClass($name)
	{
		return 'Application\\DeskPRO\\People\\PermissionLoader\\' . $name;
	}


	public function getShortCallableNames()
	{
		return array(
			'getPermissionsManager' => '_getthis',
			'getPermsLoader'        => 'get',
			'hasPerm'               => 'hasPerm',
			'has_perm'               => 'hasPerm',
		);
	}

	public function _getthis() { return $this; }
}
