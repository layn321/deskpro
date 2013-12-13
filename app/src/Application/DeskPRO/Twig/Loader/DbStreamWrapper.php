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

namespace Application\DeskPRO\Twig\Loader;

use Application\DeskPRO\App;

/**
 * Twig loaders expect a path, and to read templates from the database means
 */
class DbStreamWrapper
{
	protected $position;
	protected $name;
	protected $php_code;
	protected $size = 0;
	protected $date_updated;

	public static function getTemplateInfo($name)
	{
		static $templates = array();
		static $not_set = array();

		// Already loaded the template
		if (isset($templates[$name])) {
			if (!isset($templates[$name]['size'])) {
				$templates[$name]['size'] = strlen($templates[$name]['template_compiled']);
			}
			return $templates[$name];
		} elseif (isset($not_set[$name])) {
			return null;
		}

		#------------------------------
		# Fetch template info, and try to
		# guess which other templates will be used as well
		#------------------------------

		$parts = explode(':', $name, 3);
		$bundle = $parts[0];
		$subdir = $parts[1];

		$params = array($name);
		$params[] = 'DeskPRO:%';
		$params[] = "$bundle:Common:%";
		$params[] = "$bundle:Main:%";

		if ($bundle == 'UserBundle') {
			$params[] = 'UserBundle:Portal:%';
		}

		if ($subdir) {
			$params[] = "$bundle:$subdir:%";
		}

		$where = array('name = ?');
		for ($i = 1, $c = count($params); $i < $c; $i++) {
			$where[] = "name LIKE ?";
		}
		$where = implode(" OR ", $where);

		$results = App::getDb()->fetchAllKeyed("
			SELECT name, template_compiled, UNIX_TIMESTAMP(date_updated) AS date_updated
			FROM templates
			WHERE $where
		", $params, 'name');

		$templates = array_merge($templates, $results);

		if (!isset($templates[$name])) {
			$not_set[$name] = true;
			return null;
		}

		$templates[$name]['size'] = strlen($templates[$name]['template_compiled']);

		return $templates[$name];
	}

	public function stream_open($path, $mode, $options, &$opened_path)
	{
		$this->position = 0;

		if (!preg_match('#/([^/]+)$#', $path, $m)) {
			return false;
		}

		$this->name = $m[1];

		$info = self::getTemplateInfo($this->name);
		if (!$info) {
			return false;
		}

		$this->php_code = $info['template_compiled'];
		$this->date_updated = $info['date_updated'];
		$this->size = $info['size'];

		return true;
	}

	public function stream_read($count)
	{
		$ret = substr($this->php_code, $this->position, $count);
		$this->position += strlen($ret);

		return $ret;
	}

	public function stream_write($data)
	{
		$left = substr($this->php_code, 0, $this->position);
		$right = substr($this->php_code, $this->position + strlen($data));
		$this->php_code = $left . $data . $right;
		$this->position += strlen($data);
		return strlen($data);
	}

	public function stream_tell()
	{
		return $this->position;
	}

	public function stream_eof()
	{
		return $this->position >= strlen($this->php_code);
	}

	public function stream_seek($offset, $whence)
	{
		switch ($whence) {
			case SEEK_SET:
				if ($offset < strlen($this->php_code) && $offset >= 0) {
					$this->position = $offset;
					return true;
				} else {
					return false;
				}
				break;

			case SEEK_CUR:
				if ($offset >= 0) {
					$this->position += $offset;
					return true;
				} else {
					return false;
				}
				break;

			case SEEK_END:
				if (strlen($this->php_code) + $offset >= 0) {
					$this->position = strlen($this->php_code) + $offset;
					return true;
				} else {
					return false;
				}
				break;

			default:
				return false;
		}
	}

	public function url_stat($path)
	{
		if ($path == 'dptpl://load') {
			return array(
				'dev' => 0,
				'ino' => 0,
				'mode' => 040777,
				'nlink' => 0,
				'uid' => 0,
				'gid' => 0,
				'rdev' => 0,
				'size' => 1,
				'atime' => time(),
				'mtime' => time(),
				'ctime' => time(),
				'blksize' => 0,
				'blocks' => -1,
			);
		}

		if (!preg_match('#/([^/]+)$#', $path, $m)) {
			return false;
		}

		$name = $m[1];
		$info = self::getTemplateInfo($name);

		if (!$info) {
			return false;
		}

		return array(
			'dev' => 0,
			'ino' => 0,
			'mode' => 0100555,
			'nlink' => 0,
			'uid' => 0,
			'gid' => 0,
			'rdev' => 0,
			'size' => $info['size'],
			'atime' => time(),
			'mtime' => $info['date_updated'],
			'ctime' => $info['date_updated'],
			'blksize' => 0,
			'blocks' => -1,
		);
	}

	public function stream_stat()
	{
		return array(
			'dev' => 0,
			'ino' => 0,
			'mode' => 0100555,
			'nlink' => 0,
			'uid' => 0,
			'gid' => 0,
			'rdev' => 0,
			'size' => $this->size,
			'atime' => time(),
			'mtime' => $this->date_updated,
			'ctime' => $this->date_updated,
			'blksize' => 0,
			'blocks' => -1,
		);
	}

	public function stream_metadata($path, $option, $var)
	{
		return true;
	}


	/**
	 * Signal that stream_select is not supported by returning false
	 *
	 * @param int $cast_as
	 * @return bool
	 */
	public function stream_cast($cast_as)
	{
		return false;
	}
}