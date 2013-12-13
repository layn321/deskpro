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

namespace Application\DeskPRO\DependencyInjection\SystemServices;

use Application\DeskPRO\DependencyInjection\DeskproContainer;

class UsergroupDataService extends BaseRepositoryService
{
	protected $has_init = false;
	protected $ugs;
	protected $agent_ugs;
	protected $user_ugs;
	protected $ug_ids = array();

	/**
	 * @var \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	protected $continer;

	public static function create(DeskproContainer $container, array $options = null)
	{
		if (!$options) $options = array();
		$options['entity'] = 'Application\\DeskPRO\\Entity\\Usergroup';
		$options['container']  = $container;

		$em = $container->getEm();
		$o = new static($em, $options);
		return $o;
	}

	protected function init()
	{
		$this->continer   = $this->options['container'];
	}

	public function get($ug_id)
	{
		$this->preload();
		return isset($this->ugs[$ug_id]) ? $this->ugs[$ug_id] : null;
	}

	public function getAll()
	{
		$this->preload();
		return $this->ugs;
	}

	public function getUserUsergroups()
	{
		$this->preload();
		return $this->user_ugs;
	}

	public function getAgentUsergroups()
	{
		$this->preload();
		$this->agent_ugs;
	}

	protected function preload()
	{
		if ($this->has_init) {
			return;
		}
		$this->has_init = true;

		$this->ugs = $this->em->createQuery("
			SELECT ug
			FROM DeskPRO:Usergroup ug INDEX BY ug.id
			ORDER BY ug.id ASC
		")->execute();
		$this->em->getUnitOfWork()->markAsPreloaded('DeskPRO:Usergroup');

		foreach ($this->ugs as $ug) {
			$this->ug_ids[] = $ug->getId();
			$ug->getTitle();

			if ($ug->is_agent_group) {
				$this->agent_ugs[$ug->getId()] = $ug;
			} else {
				$this->user_ugs[$ug->getId()] = $ug;
			}
		}
	}

	public function getNames($for_ids = null)
	{
		$this->preload();

		$ret = array();

		if ($for_ids) {
			foreach ($for_ids as $cid) {
				if (!$this->get($cid)) {
					$ret[$cid] = "Unknown #$cid";
				} else {
					$ret[$cid] = $this->get($cid)->title;
				}
			}
		} else {
			foreach ($this->ug_ids as $cid) {
				$ret[$cid] = $this->get($cid)->title;
			}
		}

		return $ret;
	}

	public function getUsergroupNames()
	{
		$this->preload();
		$names = $this->getNames(array_keys($this->user_ugs));
		return $names;
	}

	public function getAgentUsergroupNames()
	{
		return $this->getUsergroupNames(array_keys($this->agent_ugs));
	}

	public function getByIds(array $ids, $keep_order = false)
	{
		$this->preload();
		$ret = array();

		foreach ($ids as $id) {
			if (isset($this->ugs[$id])) {
				$ret[$id] = $this->ugs[$id];
			}
		}

		return $ret;
	}

	public function __call($method, array $args = array())
	{
		$this->preload();
		return parent::__call($method, $args);
	}
}