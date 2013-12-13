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

class DepartmentDataService extends BaseRepositoryService
{
	protected $has_init = false;
	protected $cats;
	protected $cat_ids = array();
	protected $root_node_ids = array();
	protected $leaf_node_ids = array();
	protected $nodes_with_children = array();
	protected $filtered_nodes = array();
	protected $filtered_chat_nodes = array();

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

	public static function create(DeskproContainer $container, array $options = null)
	{
		if (!$options) $options = array();
		$options['entity'] = 'Application\\DeskPRO\\Entity\\Department';
		$options['translator'] = $container->getTranslator();
		$options['default_id'] = $container->getSetting('core.default_ticket_dep');
		$options['container']  = $container;

		$em = $container->getEm();
		$o = new static($em, $options);
		return $o;
	}

	protected function init()
	{
		$this->translator = $this->options['translator'];
		$this->default_id = $this->options['default_id'];
		$this->continer   = $this->options['container'];
	}

	public function get($dep_id)
	{
		$this->preload();
		return isset($this->cats[$dep_id]) ? $this->cats[$dep_id] : null;
	}

	public function getAll()
	{
		$this->preload();
		return $this->cats;
	}

	protected function preload()
	{
		if ($this->has_init) {
			return;
		}
		$this->has_init = true;

		$this->cats = $this->em->createQuery("
			SELECT d
			FROM DeskPRO:Department d INDEX BY d.id
			ORDER BY d.display_order ASC
		")->execute();
		$this->em->getUnitOfWork()->markAsPreloaded('DeskPRO:Department');

		$cats = array();

		// force hydration
		foreach ($this->cats as $c) {
			$this->cat_ids[] = $c->getId();
			$c->getTitle();
			$c->__dp_is_preloaded_repos = $this;

			$cats[$c->getId()] = array(
				'id' => $c->getId(),
				'parent_id' => $c->parent ? $c->parent->getId() : 0,
				'title' => $c->getTitle()
			);

			if (!$c->parent) {
				$this->root_node_ids[] = $c->getId();
			}
		}
		foreach ($this->cats as $c) {
			foreach ($c->children as $sc) {
				$this->nodes_with_children[$c->getId()] = true;
				$this->leaf_node_ids[] = $sc->getId();
			}
		}

		$this->repos->getInHierarchy($cats);
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
					$ret[$cid] = $this->translator->getPhraseObject($this->get($cid), 'title');
				}
			}
		} else {
			foreach ($this->cat_ids as $cid) {
				$ret[$cid] = $this->translator->getPhraseObject($this->get($cid), 'title');
			}
		}

		return $ret;
	}

	public function getByIds(array $ids, $keep_order = false)
	{
		$this->preload();
		$ret = array();

		foreach ($ids as $id) {
			if (isset($this->cats[$id])) {
				$ret[$id] = $this->cats[$id];
			}
		}

		return $ret;
	}

	public function getChildren($category = null, $direct = true)
	{
		$this->preload();
		$ids = $this->repos->getChildrenIds($category, $direct);
		if (!$ids) {
			return array();
		}

		return $this->getByIds($ids);
	}

	public function getPersonDepartments(\Application\DeskPRO\Entity\Person $person_context, $app, array $allow_ids = array(), $permission = 'full')
	{
		$key = md5($person_context->getId() . '.' . $app);

		if (isset($this->filtered_nodes[$key])) {
			return $this->filtered_nodes[$key];
		}

		if ($allow_ids) {
			foreach (array_values($allow_ids) as $id) {
				$d = $this->get($id);
				if ($d && $d->parent) {
					$allow_ids[] = $d->parent->getId();
				}
			}

			$allow_ids = array_unique($allow_ids);
			$allow_ids = array_combine(array_values($allow_ids), array_values($allow_ids));
		}

		$filter = function ($c) use ($person_context, $app, $allow_ids, $permission) {
			if (isset($allow_ids[$c->getId()])) {
				return true;
			}
			return $person_context->getPermissionsManager()->Departments->isAllowed($c->getId(), $app, $permission);
		};

		if (!$allow_ids) {
			$this->filtered_nodes[$key] = \Application\DeskPRO\Tree\TreeProxyHasPhraseName::makeTreeProxyArray($this->getRootNodes(), $filter);
			return $this->filtered_nodes[$key];
		} else {
			$nodes = \Application\DeskPRO\Tree\TreeProxyHasPhraseName::makeTreeProxyArray($this->getRootNodes(), $filter);
			return $nodes;
		}
	}

	public function getOnlineChatDepartments(\Application\DeskPRO\Entity\Person $person_context)
	{
		$key = $person_context->getId();

		if (isset($this->filtered_chat_nodes[$key])) {
			return $this->filtered_chat_nodes[$key];
		}

		$online_dep_ids = array();

		$agents_online_ids = $this->em->getRepository('DeskPRO:Session')->getAvailableAgentIds();
		foreach ($agents_online_ids as $aid) {
			$agent = $this->continer->getDataService('Agent')->get($aid);
			if (!$agent) continue;

			$agent->loadHelper('AgentPermissions');

			$online_dep_ids = array_merge(
				$online_dep_ids,
				$agent->getHelper('AgentPermissions')->getAllowedDepartments('chat')
			);
		}

		$online_dep_ids = array_unique($online_dep_ids, \SORT_NUMERIC);
		if ($online_dep_ids) {
			$online_dep_ids = array_combine($online_dep_ids, $online_dep_ids);
		}

		if (!$online_dep_ids) {
			$this->filtered_nodes[$key] = array();
			return array();
		}

		$filter = function ($c) use ($person_context, $online_dep_ids) {
			if (!isset($online_dep_ids[$c->getId()])) {
				return false;
			}
			return $person_context->getPermissionsManager()->Departments->isAllowed($c->getId(), 'chat', 'full');
		};

		$this->filtered_nodes[$key] = \Application\DeskPRO\Tree\TreeProxyHasPhraseName::makeTreeProxyArray($this->getRootNodes(), $filter);
		return $this->filtered_nodes[$key];
	}

	public function getRootNodes()
	{
		$this->preload();

		if (!$this->root_node_ids) {
			return array();
		}

		return $this->getByIds($this->root_node_ids);
	}

	public function getParentNodes()
	{
		$this->preload();
		return $this->getByIds(array_keys($this->nodes_with_children));
	}

	public function getParentNodeIds()
	{
		$this->preload();
		return array_keys($this->nodes_with_children);
	}

	public function getLeafNodeIds()
	{
		$this->preload();
		return $this->leaf_node_ids;
	}

	public function getLeafNodes()
	{
		$this->preload();
		return $this->getByIds($this->leaf_node_ids);
	}

	public function getPath($category)
	{
		$this->preload();
		$ids = $this->repos->getPathIds($category);

		if (!$ids) {
			return array();
		}

		return $this->getByIds($ids);
	}

	public function getDefaultTicketDepartment()
	{
		$this->preload();

		if (!$this->default_id || !isset($this->cats[$this->default_id]) || count($this->getChildren($this->default_id)) || !$this->cats[$this->default_id]->is_tickets_enabled) {
			$this->default_id = 0;
		}

		// Invalid default just chooses first
		if (!$this->default_id) {
			foreach ($this->cats as $c) {
				if ($c->is_tickets_enabled && !count($this->getChildren($c))) {
					$this->default_id = $c->getId();
					break;
				}
			}
		}

		return $this->cats[$this->default_id];
	}

	public function getFullNames($type = 'tickets', $include_parents = true)
	{
		$names = array();
		foreach ($this->getRootNodes() as $dep) {

			if (!$dep->isType($type)) {
				continue;
			}

			$children = $this->getChildren($dep);

			if ($include_parents || !$children) {
				$names[$dep->getId()] = $dep->title;
			}

			foreach ($children as $subdep) {
				if (!$subdep->isType($type)) {
					continue;
				}

				$names[$subdep->getId()] = $dep->title . ' > ' . $subdep->title;
			}
		}

		return $names;
	}

	public function __call($method, array $args = array())
	{
		$this->preload();
		return parent::__call($method, $args);
	}
}