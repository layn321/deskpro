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
 * Orb
 *
 * @package Orb
 * @category Input
 */

namespace Orb\Input\Cleaner;

use Orb\Input\Cleaner\CleanerPlugin\CleanerPlugin;

/**
 * A cleaner class that cleans any input to fit a data type.
 */
class Cleaner
{
	/**
	 * A map of types to their cleaner
	 * @var array
	 */
	protected $cleaner_type_map = array();

	/**
	 * @var \Orb\Input\Cleaner\CleanerPlugin\CleanerPlugin[]
	 */
	protected $cleaners = array();

	public function __construct()
	{
		$basic = new \Orb\Input\Cleaner\CleanerPlugin\Basic();
		$basic->enableUtfHandling();

		$this->addCleaner($basic);
	}


	/**
	 * Add a cleaner
	 *
	 * @param CleanerPlugin\CleanerPlugin $cleaner
	 */
	public function addCleaner(CleanerPlugin $cleaner)
	{
		$this->cleaners[$cleaner->getCleanerId()] = $cleaner;

		foreach ($cleaner->getCleanerTypes() as $t) {
			$this->cleaner_type_map[$t] = $cleaner->getCleanerId();
		}
	}


	/**
	 * Get a  cleaner
	 *
	 * @param $id
	 * @return CleanerPlugin\CleanerPlugin
	 */
	public function getCleaner($id)
	{
		return $this->cleaners[$id];
	}


	/**
	 * Check if a cleaner has been added
	 *
	 * @param $id
	 * @return bool
	 */
	public function hasCleaner($id)
	{
		return isset($this->cleaners[$id]);
	}


	/**
	 * See if we support a type of cleaner
	 *
	 * @param $type
	 * @return bool
	 */
	public function supportsType($type)
	{
		return isset($this->cleaner_type_map[$type]);
	}


	/**
	 * Get the cleaner for a particular input request type
	 *
	 * @param string $type
	 * @return CleanerPlugin\CleanerPlugin
	 */
	public function getCleanerForType($type)
	{
		if (!isset($this->cleaner_type_map[$type])) {
			return null;
		}

		$id = $this->cleaner_type_map[$type];
		return $this->getCleaner($id);
	}


	/**
	 * Clean a value.
	 *
	 * @param   mixed  $value    The value to clean
	 * @param   int    $type     The type to cast to
	 * @param   mixed  $options  Options for the type
	 * @return  mixed  The cleaned value
	 */
	public function clean($value, $type = 'raw', $options = null)
	{
		if (!$options) $options = array();

		if (!isset($this->cleaner_type_map[$type])) {
			throw new \InvalidArgumentException("Invalid cleaner type `$type`");
		}

		return $this->getCleanerForType($type)->cleanValue($value, $type, $options, $this);
	}



	/**
	 * Clean an array of values. $type_key can be TYPE_DISCARD if you dont want to keep the keys. In such cases,
	 * the array indecies will be integers (i.e., array will be build via $array[]=$val).
	 *
	 * @param   array    $array        The array to clean
	 * @param   integer  $type_val     The type to cast values to
	 * @param   integer  $type_key     The type to cast keys to
	 * @param   mixed    $options_val  Options for the val type
	 * @param   mixed    $options_key  Options for the key type
	 * @return  array
	 */
	public function cleanArray($array, $type_val = 'raw', $type_key = 'raw', $options_val = null, $options_key = null)
	{
	    if (!is_array($array)) {
	        $array = (array)$array;
	    }

	    $ret_array = array();

		foreach ($array as $k => $v) {
			$k = $this->clean($k, $type_key, $options_key);
			$v = $this->clean($v, $type_val, $options_val);

			if ($type_key == 'discard') {
				$ret_array[] = $v;
			} else {
				$ret_array[$k] = $v;
			}
		}

		return $ret_array;
	}
}
