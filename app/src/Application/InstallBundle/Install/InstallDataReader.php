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
*/

namespace Application\InstallBundle\Install;

use Orb\Util\Strings;

class InstallDataReader implements \IteratorAggregate, \Countable
{
	protected $filename;
	protected $filepath;
	protected $filetype;

	protected $tags = array();
	protected $data = null;

	public function __construct($filepath)
	{
		$this->filepath = $filepath;
		$this->filetype = pathinfo($this->filename, \PATHINFO_EXTENSION);

		if (!is_file($this->filepath)) {
			throw new \InvalidArgumentException("Invalid file `$filename`");
		}
	}

	public function _read()
	{
		// Already read
		if ($this->data !== null) return;

		$this->tags = array();
		$this->data = array();

		// prefix here so the array_shift below gets rid of junk,
		// but doesnt bug out if theres a BEGIN right on the first line
		$file = "\n\nxxx\n\n" . file_get_contents($this->filepath);

		$parts = preg_split('/^##BEGIN:(.*?)##\s*$/m', $file, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		if (!$parts) {
			return;
		}

		// remove first part of the file because its not part of any section
		array_shift($parts);

		$_desc_str = null;
		foreach ($parts as $part) {

			$part = trim($part);
			if (!$part) continue;

			// The name of the part is before each part itself,
			// so we read it first and next time around we have the real content
			if ($_desc_str === null) {
				$_desc_str = $part;
				continue;
			}

			$desc_str = $_desc_str;
			$_desc_str = null;

			// something.some_name
			// Tag: something, name: some_name
			$desc_parts = explode('.', $desc_str, 2);
			if (count($desc_parts) == 1) {
				$tag = 'default';
				$name = $desc_parts;
			} else {
				list ($tag, $name) = $desc_parts;
			}

			if (!isset($this->tags[$tag])) $this->tags[$tag] = array();
			$this->tags[$tag][] = "$tag.$name";

			$part = trim($part);
			if ($this->filetype == 'sql') {
				$part = rtrim($part, ';'); // trailing ;'s
			}

			$this->data[$tag . '.' . $name] = $part;
		}
	}

	/**
	 * @return array
	 */
	public function getAllForTag($tag)
	{
		$ret = array();

		$this->_read();
		if (!isset($this->tags[$tag])) {
			throw new \InvalidArgumentException("No such tag `$tag`");
		}

		foreach ($this->tags[$tag] as $name) {
			$ret[$name] = $this->get($name);
		}

		return $ret;
	}

	/**
	 * @return string
	 */
	public function get($name)
	{
		$this->_read();
		if (!isset($this->data[$name])) {
			throw new \InvalidArgumentException("No such name `$name`");
		}

		return $this->data[$name];
	}

	/**
	 * @return array
	 */
	public function getAll()
	{
		$this->_read();
		return $this->data;
	}

	/**
	 * @return bool
	 */
	public function has($name)
	{
		$this->_read();
		return isset($this->data[$name]);
	}

	/**
	 * @return array
	 */
	public function getTags()
	{
		$this->_read();
		return array_keys($this->tags);
	}

	/**
	 * @return \ArrayObject
	 */
	public function getIterator()
	{
		$this->_read();
		return new \ArrayObject($this->data);
	}


	/**
	 * @return int
	 */
	public function count()
	{
		$this->_read();
		return count($this->data);
	}
}
