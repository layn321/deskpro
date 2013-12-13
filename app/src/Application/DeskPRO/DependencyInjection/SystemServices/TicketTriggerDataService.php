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

class TicketTriggerDataService extends BaseRepositoryService
{
	/**
	 * @var bool
	 */
	protected $has_init = false;

	/**
	 * @var \Application\DeskPRO\Entity\TicketTrigger[]
	 */
	protected $triggers;

	/**
	 * @var \Application\DeskPRO\Entity\TicketTrigger[]
	 */
	protected $escalations;

	/**
	 * @var \Application\DeskPRO\Entity\TicketTrigger[]
	 */
	protected $all;


	/**
	 * @param \Application\DeskPRO\DependencyInjection\DeskproContainer $container
	 * @param array $options
	 */
	public static function create(DeskproContainer $container, array $options = null)
	{
		if (!$options) $options = array();
		$options['entity'] = 'Application\\DeskPRO\\Entity\\TicketTrigger';
		$options['container']  = $container;

		$em = $container->getEm();
		$o = new static($em, $options);
		return $o;
	}


	/**
	 * Sets some useful objects from options
	 */
	protected function init()
	{
		$this->continer   = $this->options['container'];
	}


	/**
	 * Get a trigger or escalation
	 *
	 * @param int $trigger_id
	 * @return \Application\DeskPRO\Entity\TicketPriority
	 */
	public function get($trigger_id)
	{
		$this->preload();
		return isset($this->all[$trigger_id]) ? $this->all[$trigger_id] : null;
	}


	/**
	 * Get all triggers and escalations
	 *
	 * @return \Application\DeskPRO\Entity\TicketPriority[]
	 */
	public function getAll()
	{
		$this->preload();
		return $this->all;
	}


	/**
	 * Get all triggers
	 *
	 * @return \Application\DeskPRO\Entity\TicketTrigger[]
	 */
	public function getAllTriggers()
	{
		$this->preload();
		return $this->triggers;
	}


	/**
	 * Get all escalations
	 *
	 * @return \Application\DeskPRO\Entity\TicketTrigger[]
	 */
	public function getAllEscalations()
	{
		$this->preload();
		return $this->escalations;
	}


	/**
	 * Loads all tikcet priorities into this object
	 */
	protected function preload()
	{
		if ($this->has_init) {
			return;
		}
		$this->has_init = true;

		$this->all = $this->em->createQuery("
			SELECT t
			FROM DeskPRO:TicketTrigger t INDEX BY t.id
			WHERE t.is_enabled = true
		")->execute();

		foreach ($this->all as $t) {
			// Force hydration
			$t->getTitle();

			if (strpos($t->event_trigger, 'time.') === 0) {
				$this->escalations[$t->id] = $t;
			} else {
				$this->triggers[$t->id] = $t;
			}
		}
	}



	/**
	 * @param array $ids
	 * @return \Application\DeskPRO\Entity\TicketPriority[]
	 */
	public function getByIds(array $ids)
	{
		$this->preload();
		$ret = array();

		foreach ($ids as $id) {
			if (isset($this->all[$id])) {
				$ret[$id] = $this->all[$id];
			}
		}

		return $ret;
	}


	/**
	 * Check if a specific trigger exists and is a trigger
	 *
	 * @param int $trigger_id
	 * @return bool
	 */
	public function hasTriggerId($trigger_id)
	{
		$this->preload();
		return isset($this->triggers[$trigger_id]);
	}


	/**
	 * Check if a specific trigger exists and is an escalation
	 *
	 * @param int $trigger_id
	 * @return bool
	 */
	public function hasEscalationId($trigger_id)
	{
		$this->preload();
		return isset($this->escalations[$trigger_id]);
	}


	/**
	 * Calls a method on the repository class and caches the result.
	 *
	 * @param $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, array $args = array())
	{
		$this->preload();
		return parent::__call($method, $args);
	}
}