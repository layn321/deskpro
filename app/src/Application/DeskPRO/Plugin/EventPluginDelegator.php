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
 * @subpackage Plugin
 */

namespace Application\DeskPRO\Plugin;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Plugin;

use Application\DeskPRO\EventDispatcher\FilterPluginInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * This attaches itself as a listener to all events used by plugins. When an event is fired,
 * plugins listener classes are lazy-initialized and run.
 */
class EventPluginDelegator
{
	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcher
	 */
	protected $event_dispatcher;

	/**
	 * @var string[]
	 */
	protected $listened_events = array();

	/**
	 * @var \Application\DeskPRO\Plugin\PluginListenerFactory
	 */
	protected $plugin_listener_factory;

	/**
	 * @var \Application\DeskPRO\Entity\PluginListener[]
	 */
	protected $plugin_listeners = array();

	/**
	 * These are instantiaed plugin listener classes
	 * @var array
	 */
	protected $plugin_listener_objs = array();

	public static function newWithInstalledPlugins()
	{
		$ead = new self();

		
	}

	public function __construct(EventDispatcher $event_dispatcher = null)
	{
		if ($event_dispatcher === null) {
			$event_dispatcher = App::getEventDispatcher();
		}
		
		$this->event_dispatcher = $event_dispatcher;

		$this->plugin_listener_factory = new PluginListenerFactory();
	}


	/**
	 * Add plugins to the listener
	 * 
	 * @param  $plugins
	 * @return void
	 */
	public function addPluginListeners(array $plugin_listeners)
	{
		$events = array();
		foreach ($plugin_listeners as $plugin_listener) {
			$event_name = $plugin_listener['event_name'];

			if (!isset($this->plugin_listeners[$event_name])) $this->plugin_listeners[$event_name] = array();
			$this->plugin_listeners[$event_name][] = $plugin_listener;

			$events[] = $event_name;
		}

		$this->listenForEvents($events);
	}

	/**
	 * Listen on these event names
	 *
	 * @param array $events
	 * @return void
	 */
	public function listenForEvents(array $events)
	{
		$this->listened_events = array_merge($this->listened_events, $events);
		$this->listened_events = array_unique($this->listened_events);

		foreach ($events as $event) {
			$this->event_dispatcher->addListener($events, $this);
		}
	}
	

	/**
	 * Gets all the event handler classes
	 * 
	 * @param  $event_name
	 * @return array
	 */
	public function getEventRunners($event_name, $event)
	{
		if (empty($this->plugin_listeners[$event_name])) return array();

		$runners = array();

		//FilterPluginInterface
		$plugin_listeners = $this->plugin_listeners[$event_name];
		if ($event AND $event instanceof FilterPluginInterface) {
			$plugin_listeners = array_filter($plugin_listeners, array($event, 'filterPlugins'));
		}

		foreach ($plugin_listeners as $plugin_listener) {
			$id = $plugin_listener['id'];
			if (isset($this->plugin_listener_objs[$id])) {
				$runner = $this->plugin_listener_objs[$id];
			} else {
				$runner = $this->plugin_listener_factory->create($plugin_listener);
				$this->plugin_listener_objs[$id] = $runner;
			}

			$runners[] = $runner;
		}
		
		return $runners;
	}

	
	/**
	 * Handles all event calls and delegates them to the plugins
	 *
	 * @throws \BadMethodCallException
	 * @param  $method
	 * @param  $args
	 * @return void
	 */
	public function __call($method, $args)
	{
		if (!in_array($method, $this->listened_events)) {
			throw new \BadMethodCallException("`$method` is an invlaid event name for this listener");
		}

		$event = isset($args[0]) ? $args[0] : null;
		$runners = $this->getEventRunners($method, $event);

		foreach ($runners as $runner) {
			call_user_func_array(array($runner, $method), $args);
		}
	}
}
