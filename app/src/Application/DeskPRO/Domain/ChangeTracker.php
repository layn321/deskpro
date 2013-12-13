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

namespace Application\DeskPRO\Domain;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;

/**
 * This change tracker is meant to listen to changes on an object, and then after all changes
 * were committed, it calls listeners.
 *
 * So this is just an intermediary listener that records changes all, and then notifies other listeners
 * at the end. This allows those listeners to run deep inspections on all changes, rather than just know about
 * changes as they happen.
 */
abstract class ChangeTracker implements \Doctrine\Common\PropertyChangedListener
{
	protected $entity;
	protected $changes = array();
	public $extra = array();

	public function __construct($entity)
	{
		$this->entity = $entity;
	}


	/**
	 * Get the entity
	 */
	public function getEntity()
	{
		return $this->entity;
	}



	public function propertyChanged($sender, $prop, $old_val, $new_val)
	{
		$this->recordPropertyChanged($prop, $old_val, $new_val);
	}

	/**
	 * Log a property change
	 *
	 * @param  $prop
	 * @param  $old_val
	 * @param  $new_val
	 */
	public function recordPropertyChanged($prop, $old_val, $new_val)
	{
		// Detect fields that did not change
		if (is_null($new_val) && is_null($old_val)) {
			return;
		} elseif (is_scalar($new_val)) {
			if ($new_val == $old_val) {
				return;
			}
		} elseif ($new_val instanceof \DateTime) {
			if ($old_val instanceof \DateTime && $new_val->getTimestamp() == $old_val->getTimestamp()) {
				return;
			}
		} elseif (is_object($new_val) && isset($new_val->id) && is_object($old_val)) {
			if ($new_val->id == $old_val->id) {
				return;
			}
		}

		// It may have been reset more than once before being saved
		if (isset($this->changes[$prop])) {
			$old_val = $this->changes[$prop]['old'];
		}

		$this->changes[$prop] = $this->getChangeData($prop, $old_val, $new_val);
	}



	/**
	 * Log a property change where the value is multiple, such as additions to a collection
	 *
	 * @param  $prop
	 * @param  $old_val
	 * @param  $new_val
	 */
	public function recordMultiPropertyChanged($prop, $old_val, $new_val)
	{
		if (!isset($this->changes[$prop])) $this->changes[$prop] = array();

		$this->changes[$prop][] = $this->getChangeData($prop, $old_val, $new_val);
	}


	/**
	 * @param $prop
	 * @param $old_val
	 * @param $new_val
	 * @return array
	 */
	public function getChangeData($prop, $old_val, $new_val)
	{
		return array('old' => $old_val, 'new' => $new_val);
	}


	/**
	 * Get details of a property change
	 *
	 * @param  $prop
	 * @return array|null
	 */
	public function getChangedProperty($prop)
	{
		return isset($this->changes[$prop]) ? $this->changes[$prop] : null;
	}



	/**
	 * Get array of all property changes
	 *
	 * @return array
	 */
	public function getAllChangedProperties()
	{
		return $this->changes;
	}



	/**
	 * Get the names of all changed properties
	 *
	 * @return array
	 */
	public function getAllChangedPropertyNames()
	{
		return array_keys($this->changes);
	}



	/**
	 * Check if a specific property is changed
	 *
	 * @param  $prop
	 * @return bool
	 */
	public function isPropertyChanged($prop)
	{
		return isset($this->changes[$prop]);
	}



	/**
	 * Record some extra data about a ticket event that listeners might be interested in
	 *
	 * @param  $key
	 * @param  $value
	 */
	public function recordExtra($key, $value)
	{
		$this->extra[$key] = $value;
	}


	/**
	 * @param $key
	 * @param $value
	 */
	public function recordExtraMulti($key, $value)
	{
		if (!isset($this->extra[$key])) {
			$this->extra[$key] = array();
		}

		$this->extra[$key][] = $value;
	}



	/**
	 * Get extra data
	 *
	 * @param  $key
	 * @return array|null
	 */
	public function getExtra($key)
	{
		return isset($this->extra[$key]) ? $this->extra[$key] : null;
	}



	/**
	 * Get an array of all registered extra data
	 *
	 * @return array
	 */
	public function getAllExtra()
	{
		return $this->extra;
	}



	/**
	 * Check if some extra data item is set
	 *
	 * @param  $key
	 * @return bool
	 */
	public function isExtraSet($key)
	{
		return isset($this->extra[$key]);
	}



	/**
	 * Notify all listeners that changes to the entity have been committed
	 *
	 * @return void
	 */
	abstract public function done();
}
