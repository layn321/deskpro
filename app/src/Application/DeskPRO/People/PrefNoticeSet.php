<?php

namespace Application\DeskPRO\People;

use Application\DeskPRO\DBAL\Connection;
use Application\DeskPRO\Entity\Person;
use Orb\Util\Arrays;

class PrefNoticeSet implements \IteratorAggregate, \Countable
{
	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var string
	 */
	protected $pref_id;

	/**
	 * @var string
	 */
	protected $pref_data;

	/**
	 * @var string
	 */
	protected $set_file;

	/**
	 * @var array
	 */
	protected $set_data;

	/**
	 * @var array
	 */
	protected $waiting_data;

	/**
	 * @var string
	 */
	protected $next_waiting_id;

	public function __construct(Connection $db, Person $person, $pref_id, $set_file)
	{
		$this->person = $person;
		$this->pref_id = $pref_id;

		#------------------------------
		# Get pref
		#------------------------------

		$this->db = $db;
		$this->pref_data = $this->db->fetchColumn("
			SELECT value_array
			FROM people_prefs
			WHERE person_id = ? AND name = ?
		", array($person->getId(), $pref_id));

		if ($this->pref_data) {
			$this->pref_data = @unserialize($this->pref_data);
		}

		if (!$this->pref_data) {
			$this->pref_data = array('dismissed' => array());
		}

		#------------------------------
		# Load set data
		#------------------------------

		$this->set_file = $set_file;
		$this->set_data = require($set_file);
		$this->waiting_data = array();

		foreach ($this->set_data as $id => $item) {
			if (isset($this->pref_data['dismissed'][$id])) {
				continue;
			}
			if (isset($item['date'])) {
				$d = \DateTime::createFromFormat('Y-m-d H:i:s', $item['date']);
				if ($d < $person->date_created) {
					continue;
				}
			}
			if (isset($item['target'])) {
				if ($item['target'] == 'admin' && !$this->person->can_admin) {
					continue;
				}
				if ($item['target'] == 'agent' && !$this->person->is_agent) {
					continue;
				}
			}

			if (!$this->next_waiting_id) {
				$this->next_waiting_id = $id;
			}
			$this->waiting_data[$id] = $item;
		}
	}


	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->waiting_data);
	}

	/**
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->waiting_data);
	}


	/**
	 * @param string $id
	 */
	public function dismiss($id)
	{
		$this->pref_data['dismissed'][$id] = array('date' => date('Y-m-d H:i:s'));
		unset($this->waiting_data[$id]);

		$this->next_waiting_id = Arrays::getFirstKey($this->waiting_data);
	}


	/**
	 * @return string
	 */
	public function getNextId()
	{
		return $this->next_waiting_id;
	}


	/**
	 * @return array
	 */
	public function getWaitingIds()
	{
		return array_keys($this->waiting_data);
	}


	/**
	 * @return void
	 */
	public function save()
	{
		$data = serialize($this->pref_data);
		$this->db->replace('people_prefs', array(
			'name'        => $this->pref_id,
			'person_id'   => $this->person->getId(),
			'value_array' => $data,
			'value_str'   => null,
			'date_expire' => null
		));
	}
}