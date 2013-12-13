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

use Orb\Util\Arrays;

/**
 * This helps working with agent preferences
 */
class AgentPrefs implements \Orb\Helper\ShortCallableInterface
{
	protected $person;
	protected $prefs = array();

	protected $loaded_prefs = array();
	protected $loaded_pref_prefixes = array();

	protected $preload_ids = array('agent.ticket_signature', 'agent.ticket_signature_html');
	protected $preload_prefixes = array('agent.ui.flag.');

	public function __construct(Entity\Person $person)
	{
		$this->person = $person;
	}

	public function getShortCallableNames()
	{
		return array(
			'getAgentPref' => 'getAgentPref',
			'getAgentNamedPrefs' => 'getNamedPrefs',
		);
	}

	public function preload()
	{
		if (!$this->preload_ids && !$this->preload_prefixes) {
			return;
		}

		$params = array('p' => $this->person);
		$sql = "SELECT pref FROM DeskPRO:PersonPref pref INDEX BY pref.name WHERE pref.person = :p AND (";

		if ($this->preload_ids) {
			$sql .= "pref.name IN (:ids)";
			$params['ids'] = $this->preload_ids;
			foreach ($this->preload_ids as $id) {
				$this->loaded_prefs[] = $id;
			}
		}
		if ($this->preload_prefixes) {
			if ($this->preload_ids) $sql .= ' OR ';

			$x = 0;
			$parts = array();
			foreach ($this->preload_prefixes as $prefix) {
				$qname = 'p' . $x;
				$parts[] = 'pref.name LIKE :'.$qname;
				$params[$qname] = $prefix . '%';
				$this->loaded_pref_prefixes[] = $prefix;
			}

			$sql .= implode(' OR ', $parts);
		}

		$sql .= ')';

		$results = App::getOrm()->createQuery($sql)->execute($params);
		$this->prefs = array_merge($this->prefs, $results);

		$this->preload_ids = $this->preload_prefixes = array();
	}

	public function isPrefLoaded($name)
	{
		if (isset($this->loaded_prefs[$name])) {
			return true;
		}

		foreach ($this->loaded_pref_prefixes as $prefix) {
			if (strpos($name, $prefix) === 0) {
				return true;
			}
		}

		return false;
	}

	public function getPref($name)
	{
		$this->preload();
		if (!isset($this->prefs[$name]) && !$this->isPrefLoaded($name)) {
			$sql = "SELECT pref FROM DeskPRO:PersonPref pref WHERE pref.person = ?0 AND pref.name = ?1";
			$this->prefs[$name] = App::getOrm()->createQuery($sql)->execute(array($this->person, $name));
			$this->loaded_prefs[$name] = true;
		}

		if (!$this->prefs[$name]) {
			return null;
		}

		return $this->prefs[$name]->getValue();
	}

	public function getNamedPrefs()
	{
		$this->preload();

		if (func_num_args() == 1) {
			$names = array();
			$names[] = func_get_arg(0);
		} else {
			$names = func_get_args();
		}

		$not_found = array();

		foreach ($names as $n) {
			if (!$this->isPrefLoaded($n)) {
				$not_found[] = $n;
				$this->loaded_prefs[$name] = true; // loading it in a sec
			}
		}

		if ($not_found) {
			$sql = "SELECT pref FROM DeskPRO:PersonPref pref WHERE pref.person = ?0 AND pref.name IN (?2)";
			$results = App::getOrm()->createQuery($sql)->execute(array($this->person, $not_found));
			$this->prefs = array_merge($this->prefs, $results);
		}

		$ret = array();
		foreach ($names as $n) {
			if (isset($this->prefs[$n])) {
				$ret[$n] = $this->prefs[$n]->getValue();
			} else {
				$ret[$n] = null;
			}
		}

		return $ret;
	}
}
