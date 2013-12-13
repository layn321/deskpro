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
 * @subpackage PageDisplay
 */

namespace Application\DeskPRO\PageDisplay\Page;

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\PageDisplayAbstract;
use Application\DeskPRO\Entity\PortalPageDisplay;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\PageDisplay\Item\Portal\PortalItemAbstract;
use Application\DeskPRO\PageDisplay\Item\Portal\CacheableItem;
use Application\DeskPRO\DependencyInjection\DeskproContainer;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Orb\Util\Strings;

class PortalPage extends BasicPage implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\PageDisplay\Item\Portal\PortalItemAbstract[]
	 */
	protected $page_display_items = array();

	/**
	 * The controller requesting the portal item
	 *
	 * @var \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	protected $container;

	/**
	 * The user who is viewing the item
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	/**
	 * If provided, this will lazy-load a PortalPageDisplay for a section if it doesnt exist
	 * yet in this object.
	 * @var callback
	 */
	protected $lazy_loader = null;

	/**
	 * @var bool
	 */
	protected $is_admin_mode = false;

	public function __construct(DeskproContainer $container, Person $person_context)
	{
		$this->container = $container;
		$this->person_context = $person_context;

		if (isset($_GET['admin_portal_controls']) && $person_context->can_admin) {
			$portal_items = $container->getEm()->getRepository('DeskPRO:PortalPageDisplay')->getAllBlocks();
			$this->is_admin_mode = true;
			App::get('templating.globals')->setVariable('admin_mode', true);
		} else {
			$portal_items = $container->getEm()->getRepository('DeskPRO:PortalPageDisplay')->getEnabledBlocks();
			App::get('templating.globals')->setVariable('admin_mode', false);
		}

		/*
		 * The details for each portal block is in its own PortalPageDisplay entity. The PortalPage system works
		 * with a single page display for each section, so we're creating a wrapper page display that just contains
		 * all the info for the others. The actual items are initiated later.
		 */

		$group_displays = array();

		foreach ($portal_items as $item) {
			if (!isset($group_displays[$item->section])) {
				$group_displays[$item->section] =  new PortalPageDisplay();
				$group_displays[$item->section]->section = $item->section;
				$group_displays[$item->section]->data = array();
			}

			$data = $group_displays[$item->section]->data;
			$data[] = $item;

			$group_displays[$item->section]->data = $data;
		}

		foreach ($group_displays as $d) {
			$this->addPageDisplay($d);
		}
	}


	/**
	 * @param callback $lazy_loader
	 * @return void
	 */
	public function setLazyLoader($lazy_loader)
	{
		$this->lazy_loader = $lazy_loader;
	}


	protected function _loadSection($section)
	{
		if ($this->lazy_loader AND !isset($this->page_displays[$section])) {
			$lazy_loader = $this->lazy_loader;
			$page_display = $lazy_loader($section, $this);
			if ($page_display) {
				$this->addPageDisplay($page_display);
			}
		}
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return void
	 */
	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}

	/**
	 * @param \Application\DeskPRO\Entity\PageDisplayAbstract $page_display
	 * @return void
	 */
	public function addPageDisplay(PageDisplayAbstract $page_display)
	{
		parent::addPageDisplay($page_display);
		$this->_initItems($page_display);
	}


	/**
	 * Init all items defined in the PortalPageDisplay and add it to
	 * this object.
	 *
	 * @param \Application\DeskPRO\Entity\PortalPageDisplay $page_display
	 * @return void
	 */
	protected function _initItems(PortalPageDisplay $page_display)
	{
		$section = $page_display['section'];
		if (!isset($this->page_display_items[$section])) {
			$this->page_display_items[$section] = array();
		}

		$data = $page_display['data'];

		foreach ($data as $item) {
			$this->page_display_items[$section][] = $this->_createPortalItem($section, $item);
		}

	}


	/**
	 * Creates a PortalItem object given the item info array
	 *
	 * @param $section
	 * @param $item
	 * @return \Application\DeskPRO\PageDisplay\Item\Portal\PortalItemAbstract
	 */
	public function _createPortalItem($section, $item)
	{
		$type = $item->type;
		if (strpos($type, '\\') === false) {
			$type_class = ucfirst(Strings::underscoreToCamelCase($type));
			$type_class = "Application\\DeskPRO\\PageDisplay\\Item\\Portal\\$type_class";
		} else {
			$type_class = $type;
		}

		$data = $item->data;
		$data['pid'] = $item->id;
		$data['is_enabled'] = $item->is_enabled;
		$data['display_order'] = $item->display_order;
		$data['admin_mode'] = $this->is_admin_mode;

		$obj = new $type_class($section, $data, $this->container, $this->person_context);

		return $obj;
	}


	/**
	 * Get an array of all CSS assets used by all portal items
	 *
	 * @return array
	 */
	public function getCssAssets($sections)
	{
		if ($sections == 'all') {
			$sections = array_keys($this->page_displays);
		} else {
			$sections = (array)$sections;
		}

		$assets = array();

		foreach ($sections as $section) {
			$this->_loadSection($section);
			if (isset($this->page_display_items[$section])) {
				foreach ($this->page_display_items[$section] as $item) {
					$assets = array_merge($assets, $item->getCssAssets());
				}
			}
		}

		return $assets;
	}


	/**
	 * Get an array of all JS assets used by all portal items
	 *
	 * @return array
	 */
	public function getJsAssets($sections)
	{
		if ($sections == 'all') {
			$sections = array_keys($this->page_displays);
		} else {
			$sections = (array)$sections;
		}

		$assets = array();

		foreach ($sections as $section) {
			$this->_loadSection($section);
			if (isset($this->page_display_items[$section])) {
				foreach ($this->page_display_items[$section] as $item) {
					$assets = array_merge($assets, $item->getJsAssets());
				}
			}
		}

		return $assets;
	}


	/**
	 * @param $section
	 */
	public function getSectionDisplayItems($section)
	{
		$this->_loadSection($section);

		if (!isset($this->page_display_items[$section])) {
			return array();
		}

		return $this->page_display_items[$section];
	}


	/**
	 * @param string $section
	 * @param string $type
	 * @return bool
	 */
	public function hasBlock($section, $type)
	{
		if (!isset($this->page_display_items[$section])) {
			return false;
		}

		foreach ($this->page_display_items[$section] as $item) {
			$item_type = strtolower(\Orb\Util\Util::getBaseClassname($item));
			if ($item_type == $type) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Get the renderable HTML for a section.
	 *
	 * @param $section
	 * @return string
	 */
	public function getSectionHtml($section)
	{
		$this->_loadSection($section);

		if (!isset($this->page_display_items[$section])) {
			return '';
		}

		/** @var $cache \Orb\Doctrine\Common\Cache\PreloadedMysqlCache */
		$cache = App::getContainer()->getSystemService('portal_block_cache');
		$cache->preloadPrefix('block.');

		$html = array();
		foreach ($this->page_display_items[$section] as $item) {
			if (!$item->checkPermission()) {
				continue;
			}

			$block_html = null;
			$cache_info = false;

			if (!$this->is_admin_mode && $item instanceof CacheableItem) {
				$cache_info = $item->getCacheOptions();
			}

			if ($cache_info) {

				if (!is_array($cache_info)) {
					$cache_info = array();
				}

				if (empty($cache_info['lifetime'])) $cache_info['lifetime'] = false;
				if (empty($cache_info['tags'])) $cache_info['tags'] = array();

				$cache_id = "block.portal_{$section}_" . str_replace('\\', '', get_class($item));
				$cache_lifetime = null;

				if (!isset($cache_info['user_indifferent']) OR !$cache_info['user_indifferent']) {
					$cache_id .= '_' . $this->person_context->getUsergroupSetKey();
				}

				if (($block_html = $cache->fetch($cache_id)) === false || $this->is_admin_mode) {
					$block_html = $item->getHtml();
					$cache->save($cache_id, $block_html, $cache_info['lifetime']);
				}

			} else {
				$block_html = $item->getHtml();
			}

			if (!$block_html && $this->is_admin_mode && $item instanceof \Application\DeskPRO\PageDisplay\Item\Portal\Template) {
				$block_html = '<div style="margin: 8px 0 8px 0; font-size: 11px; background-color: #fff; padding: 6px; border-radius: 4px;">(This block has no content)</div>';
			}

			if ($block_html) {
				$pid = $item->getOption('pid');
				$type = strtolower(\Orb\Util\Util::getBaseClassname($item));
				$block_html = "<div class=\"dp-p dp-{$type} dp-pid-{$pid}" . ($item->getOption('is_enabled') ? '' : ' dp-p-disabled disabled') . "\" data-dp-pid=\"{$pid}\">{$block_html}</div>";
				$html[] = $block_html;
			}
		}

		return implode("\n\n", $html);
	}
}
