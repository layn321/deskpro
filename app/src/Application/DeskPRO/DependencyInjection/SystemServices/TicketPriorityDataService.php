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

class TicketPriorityDataService extends BaseRepositoryService
{
	/**
	 * @var bool
	 */
	protected $has_init = false;

	/**
	 * @var array
	 */
	protected $pris;

	/**
	 * @var array
	 */
	protected $pri_ids;

	/**
	 * @var array
	 */
	protected $pri_map;

	/**
	 * @var \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	protected $continer;

	/**
	 * @var \Application\DeskPRO\Translate\Translate
	 */
	protected $translator;

	/**
	 * @var int
	 */
	protected $default_id;


	/**
	 * @param \Application\DeskPRO\DependencyInjection\DeskproContainer $container
	 * @param array $options
	 * @return BaseRepositoryService|TicketPriorityDataService
	 */
	public static function create(DeskproContainer $container, array $options = null)
	{
		if (!$options) $options = array();
		$options['entity'] = 'Application\\DeskPRO\\Entity\\TicketPriority';
		$options['translator'] = $container->getTranslator();
		$options['default_id'] = $container->getSetting('core.default_ticket_pri');
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
		$this->translator = $this->options['translator'];
		$this->default_id = $this->options['default_id'];
		$this->continer   = $this->options['container'];
	}


	/**
	 * @param int $pri_id
	 * @return \Application\DeskPRO\Entity\TicketPriority
	 */
	public function get($pri_id)
	{
		$this->preload();
		return isset($this->pris[$pri_id]) ? $this->pris[$pri_id] : null;
	}


	/**
	 * @return \Application\DeskPRO\Entity\TicketPriority[]
	 */
	public function getAll()
	{
		$this->preload();
		return $this->pris;
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

		$this->pris = $this->em->createQuery("
			SELECT p
			FROM DeskPRO:TicketPriority p INDEX BY p.id
			ORDER BY p.priority ASC
		")->execute();
		$this->em->getUnitOfWork()->markAsPreloaded('DeskPRO:TicketPriority');

		$this->pri_ids = array();
		$this->pri_map = array();

		// force hydration
		foreach ($this->pris as $p) {
			$this->pri_ids[] = $p->getId();
			$this->pri_map[$p->getId()] = $p->getPriority();
			$p->getTitle();
		}
	}


	/**
	 * @param int[] $for_ids
	 * @return string[]
	 */
	public function getNames($for_ids = null)
	{
		$this->preload();

		$ret = array();

		if ($for_ids) {
			foreach ($for_ids as $pid) {
				$ret[$pid] = $this->translator->getPhraseObject($this->get($pid), 'title');
			}
		} else {
			foreach ($this->pri_ids as $pid) {
				$ret[$pid] = $this->translator->getPhraseObject($this->get($pid), 'title');
			}
		}

		return $ret;
	}


	/**
	 * Gets a map of id=>priority
	 *
	 * @return array
	 */
	public function getIdToPriorityMap()
	{
		$this->preload();
		return $this->pri_map;
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
			if (isset($this->pris[$id])) {
				$ret[$id] = $this->pris[$id];
			}
		}

		return $ret;
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