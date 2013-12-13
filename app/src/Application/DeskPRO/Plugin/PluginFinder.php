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
 * @subpackage Addons
 */

namespace Application\DeskPRO\Plugin;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Plugin;
use Application\DeskPRO\Plugin\PluginPackage\AbstractPluginPackage;

use Symfony\Component\Finder\Finder;

/**
 * This finds plugins that exist in the DeskPRO file structure
 */
class PluginFinder
{
	protected $base_path;
	protected $max_depth = 3;

	protected $found = null;

	public function __construct($base_path = null, $max_depth = 3)
	{
		if ($base_path === null) {
			$base_path = AbstractPluginPackage::getBasePluginPath();
		}

		$this->base_path = str_replace('/', DIRECTORY_SEPARATOR, $base_path);
		$this->max_depth = $max_depth;
	}

	
	/**
	 * Find all available plugins
	 *
	 * @return array
	 */
	public function findPlugins()
	{
		if ($this->found !== null) return $this->found;

		$finder = new Finder();
		$finder->files()
			   ->depth('< ' . $this->max_depth)
			   ->name('PluginPackage.php')
			   ->in($this->base_path);

		$this->found = array();

		//SplFileInfo
		foreach ($finder as $file) {
			require_once($file->getRealPath());
			$classname = $this->getClassnameFromFile($file->getRealPath());

			$info = new $classname();
			$this->found[$info->getName()] = $info;
		}

		return $this->found;
	}

	
	/**
	 * Get info of one speciifc plugin
	 */
	public function getPluginInfo($name)
	{
		$this->findPlugins();
		if (!isset($this->found[$name])) return null;

		return $this->found[$name];
	}

	
	/**
	 * Get the classname for a file given the standard naming convention
	 *
	 * @param  $filename
	 * @return mixed
	 */
	public function getClassnameFromFile($filename)
	{
		$classname = str_replace($this->base_path . DIRECTORY_SEPARATOR, '', $filename);
		$classname = str_replace(DIRECTORY_SEPARATOR, '\\', $classname);
		$classname = \preg_replace('#\.php$#', '', $classname);

		return $classname;
	}
}
