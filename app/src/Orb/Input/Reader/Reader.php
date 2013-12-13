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

namespace Orb\Input\Reader;

use Orb\Input\Reader\Source\SourceInterface;
use Orb\Input\Cleaner\Cleaner;
use Orb\Util\Strings;

/**
 * The reader lets you easily read input from a number of defined sources, and makes it easy to
 * clean/sanitize data at the same time.
 */
class Reader
{
	/**
	 * Array of Orb\Input\Reader\Source\SourceInterface
	 * @var array
	 */
	protected $sources = array();

	/**
	 * Other names to refer to a source.
	 * @var array
	 */
	protected $source_aliases = array();

	/**
	 * The default source to use when none provided.
	 * @var string
	 */
	protected $default_source_name = null;

	/**
	 * An array of dynamic call names trapped with __call().
	 * So we dont have to run regex all the time.
	 *
	 * @var array
	 */
	protected $call_cache = array();

	/**
	 * The cleaner object.
	 *
	 * @var Cleaner
	 */
	protected $cleaner;

	/**
	 * When non-null, this character is used to turn a string $name into an array
	 * for easy array reading.
	 *
	 * @var string
	 */
	protected $array_name_sep = null;



	/**
	 * Create the reader.
	 *
	 * @param Cleaner $cleaner A cleaner object.
	 */
	public function __construct(Cleaner $cleaner = null)
	{
		if (!$cleaner) {
			$cleaner = new Cleaner();
		}

		$this->cleaner = $cleaner;
	}



	/**
	 * Get the raw value from a source.
	 *
	 * @param   string   $name         The name of the value to fetch
	 * @param   string   $source_name  The name of the source
	 * @return  mixed
	 */
	public function getValue($name, $source_name = null)
	{
		$source = $this->getSource($source_name);

		if ($this->array_name_sep !== null AND is_string($name) AND Strings::isIn($this->array_name_sep, $name)) {
			$name = explode($this->array_name_sep, $name);
		}

		return $source->getValue($name);
	}



	/**
	 * Get an array value. This is just a shortcut to get a raw value, and then
	 * cast it to an array if it isn't already.
	 *
	 * @param   string   $name         The name of the value to fetch
	 * @param   string   $source_name  The name of the source
	 * @return  mixed
	 */
	public function getArrayValue($name, $source_name = null)
	{
		$value = $this->getValue($name, $source_name);

		if (!is_array($value)) {
	        $value = (array)$value;
	    }

		return $value;
	}



	/**
	 * Get a value from a source then clean it.
	 *
	 * @param   string      $name           The name of the value to fetch
	 * @param   string|int  $clean_type     How to clean the value
	 * @param   string      $source_name    Where to get the value from
	 * @param   mixed       $clean_options  Any options to pass to the cleaner
	 * @return  mixed
	 */
	public function getCleanValue($name, $clean_type = 'raw', $source_name = null, $clean_options = null)
	{
		$value = $this->getValue($name, $source_name);

		return $this->cleaner->clean($value, $clean_type, $clean_options);
	}



	/**
	 * Get an array value from a source then clean it.
	 *
	 * @param   string      $name               The name of the value to fetch
	 * @param   string|int  $clean_val_type     How to clean the value
	 * @param   string|int  $clean_key_type     How to clean the key
	 * @param   string      $source_name        Where to get the value from
	 * @param   mixed       $clean_val_options  Any options to pass to the cleaner for value cleaning
	 * @param   mixed       $clean_key_options  Any options to pass to the cleaner for key cleaning
	 * @return  mixed
	 */
	public function getCleanValueArray($name, $clean_val_type = 'raw', $clean_key_type = 'raw', $source_name = null, $clean_val_options = null, $clean_key_options = null)
	{
		$value = $this->getValue($name, $source_name);

		return $this->cleaner->cleanArray($value, $clean_val_type, $clean_key_type, $clean_val_options, $clean_key_options);
	}



	/**
	 * Check if a variable is set.
	 *
	 * @param   string   $name         The name of the value to fetch
	 * @param   string   $source_name  The name of the source
	 * @return  bool
	 */
	public function checkIsset($name, $source_name = null)
	{
		$source = $this->getSource($source_name);

		if ($this->array_name_sep !== null AND is_string($name) AND Strings::isIn($this->array_name_sep, $name)) {
			$name = explode($this->array_name_sep, $name);
		}

		return $source->checkIsset($name);
	}



	/**
	 * Add a new source to this reader.
	 *
	 * @param   string                    $name    The name to reference this source by
	 * @param   Orb_Input_Reader_ISource  $source  The source object
	 * @return  Orb_Input_Reader
	 */
	public function addSource($name, SourceInterface $source)
	{
		if (is_array($name)) {
			$names = $name;
			$name = $names[0];
		} else {
			$names = array($name);
		}

		$this->sources[$name] = $source;

		foreach ($names as $n) {
			$this->source_aliases[$n] = $this->sources[$name];
		}

		if (!$this->default_source_name) {
			$this->default_source_name = $name;
		}

		return $this;
	}



	/**
	 * Has a source been registered?
	 *
	 * @param   string  $name  The name to check
	 * @return  bool
	 */
	public function hasSource($name)
	{
		return isset($this->source_aliases);
	}



	/**
	 * Set the default source to use when you don't supply one.
	 *
	 * @param  string  $name  The name of the source
	 */
	public function setDefaultSourceName($name)
	{
		if (!isset($this->sources[$name])) {
			throw new Exception('Source has not been added to this reader: '.$name);
		}

		$this->default_source_name = $name;
	}



	/**
	 * Get the currently set default source.
	 *
	 * @return  string
	 */
	public function getDefaultSourceName()
	{
		return $this->default_source_name;
	}



	/**
	 * Get a source object from the name.
	 *
	 * @param   string  $name  The name of the source to get
	 * @return  SourceInterface
	 */
	public function getSource($name = null)
	{
		if ($name === null) $name = $this->default_source_name;

		if (!isset($this->source_aliases[$name])) {
			throw new \Exception('Unknown source: ' . $name);
		}

		return $this->source_aliases[$name];
	}



	/**
	 * Dynamic method calls for easy fetching of data types from a source. There are two styleS:
	 * - getTypeFromSource($name, $options = null)
	 * - getType($name, $source = null, $options = null);
	 *
	 * Exampes:
	 * <code>
	 * $user_id = $in->getIntFromPost('user_id');
	 * $content = $in->getString('content');
	 * $published = $in->getBool('published');
	 * </code>
	 *
	 * @param $method_name
	 * @param $method_args
	 * @return unknown_type
	 */
	public function __call($method_name, $method_args)
	{
		$match = null;

		$name = null;
		$type = null;
		$from = null;
		$options = null;

		#----------------------------------------
		# We cache method names so we dont run regex
		# and string mutations all the time. If its set,
		# then we can get info from the cache
		#----------------------------------------

		if (isset($this->call_cache[$method_name])) {
			$call_info = $this->call_cache[$method_name];

			// getTypeFromSource
			if ($call_info['call_type'] == 'getTypeFromSource') {
				$name = $method_args[0];
				$type = $call_info['type'];
				$from = $call_info['from'];
				if (isset($method_args[1])) {
					$options = $method_args[1];
				}

			// getType
			} else {
				$name = $method_args[0];
				$type = $call_info['type'];
				if (isset($method_args[1])) {
					$from = $method_args[1];
				}
				if (isset($method_args[2])) {
					$options = $method_args[2];
				}
			}

		#----------------------------------------
		# First time calling this method
		#----------------------------------------

		} else {

			#----------------------------------------
			# getTypeFromSource(name, options);
			#----------------------------------------

			if (preg_match('#^get(.*?)From(.*?)$#', $method_name, $match)) {

				$name = $method_args[0];

				$type = $match[1];
				$type = Strings::camelCaseToDash($type);
				$type = str_replace('-', '_', $type);

				$from = strtolower($match[2]);

				if (isset($method_args[1])) {
					$options = $method_args[1];
				}

				$this->call_cache[$method_name] = array(
					'call_type' => 'getTypeFromSource',
					'type' => $type,
					'from' => $from
				);


			#----------------------------------------
			# getType(name, source, options);
			#----------------------------------------

			} elseif (preg_match('#^get(.*?)$#', $method_name, $match)) {

				$name = $method_args[0];

				$type = $match[1];
				$type = Strings::camelCaseToDash($type);
				$type = str_replace('-', '_', $type);

				if (isset($method_args[1])) {
					$from = $method_args[1];
				}

				if (isset($method_args[2])) {
					$options = $method_args[2];
				}

				$this->call_cache[$method_name] = array(
					'call_type' => 'getType',
					'type' => $type
				);

			#----------------------------------------
			# Invalid
			#----------------------------------------

			} else {
				throw new \BadMethodCallException("Unknown method $method_name");
			}
		}

		return $this->getCleanValue($name, $type, $from, $options);
	}



	/**
	 * Get the cleaner object.
	 *
	 * @return Cleaner
	 */
	public function getCleaner()
	{
		return $this->cleaner;
	}



	/**
	 * Setting an array string separator will allow you to provide $name's that represent
	 * nested arrays. For example:
	 *
	 * <code>
	 * $in->getValue(array('user', 'profile', 'name'));
	 *
	 * // same as..
	 * $in->setArrayStringSeparator('.');
	 * $in->getValue('user.profile.name');
	 * </code>
	 *
	 * @param string $sep
	 */
	public function setArrayStringSeparator($sep = '.')
	{
		$this->array_name_sep = $sep;
	}
}
