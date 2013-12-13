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

namespace Application\InstallBundle\Upgrade;

use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Orb\Util\Arrays;
use Orb\Util\Strings;

/**
 * This manages database upgrade scripts. An upgrade script is just a class with queries to process
 * the database from a previous version into the current version. It brings the database up to date
 * with whatever the current filesystem version is.
 *
 * Database upgrade scripts are timestamps just like the deskpro build is. But although we call these
 * upgrade scripts "Build scripts," they do not directly correlate with official builds. That is,
 * a build scripts timestamp is typically the timestamp at which the dev implemented it, while a
 * deskpro package (zip file) build time is when it was actually generated and packaged.
 *
 * The database contains a setting `core.deskpro_build`. We call this the "database version".
 * The filesystem contains a file /app/sys/config/build-time.php that defines DP_BUILD_TIME.
 * We call this the "filesystem version"
 *
 * This manager simply detects when the database version is older than the filesystem time,
 * and then runs all upgrade classes between the two points.
 */
class Manager
{
	/**
	 * @var \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	protected $container;

	/**
	 * @var int
	 */
	protected $db_version;

	/**
	 * @var array
	 */
	protected $build_list;

	/**
	 * @param \Application\DeskPRO\DependencyInjection\DeskproContainer $container
	 */
	public function __construct(DeskproContainer $container)
	{
		$this->container = $container;
		$this->reset();
	}


	/**
	 * @return int
	 */
	public function getCurrentBuild()
	{
		return $this->db_version;
	}


	/**
	 * When build info might've changed outside of this request, this rebuilds internal structures.
	 */
	public function reset()
	{
		$this->db_version = $this->container->getDb()->fetchColumn("SELECT value FROM settings WHERE name = 'core.deskpro_build'");
	}


	/**
	 * Runs the next build script
	 *
	 * @return void
	 */
	public function runBuild($build_id)
	{
		$class = $this->getBuildClass($build_id);
		$build = new $class($this->container);
		$build->run();

		if ($build->shouldRerun()) {
			$current_run = $build->getStatus('runcount', 0);
			$build->saveStatus('runcount', $current_run+1);
		} else {
			$this->db_version = $build_id;
			$this->container->getDb()->update('settings', array('value' => $build_id), array('name' => 'core.deskpro_build'));
			$this->container->getDb()->executeUpdate("DELETE FROM import_datastore WHERE typename LIKE ?", array(
				'up.' . $build->getBuildId() . '.%'
			));
		}
	}


	/**
	 * Any code to be run after all upgrade scripts are complete (and we're on the latest DB).
	 */
	public function postUpgrade()
	{
		\Application\DeskPRO\DataSync\AbstractDataSync::syncAllBaseToLive();

		// Clear old CSS blob so it's regenerated
		$this->container->getDb()->executeUpdate("UPDATE styles SET css_blob_id = NULL, css_blob_rtl_id = NULL");

		// Update lang titles
		$langpacks = new \Application\DeskPRO\Languages\LangPackInfo();

		foreach ($langpacks->getLangTitles(true) as $id => $title) {
			$this->container->getDb()->executeUpdate("UPDATE languages SET title = ? WHERE sys_name = ? AND title = ''", array($title, $id));
		}

		// Update flags if theyre blank
		$blank_flags = $this->container->getDb()->fetchAllCol("SELECT sys_name FROM languages WHERE flag_image = ''");
		foreach ($blank_flags as $sys_name) {
			if (!$langpacks->hasLang($sys_name)) continue;

			$flag = $langpacks->getLangInfo($sys_name, 'flag_image');
			if ($flag) {
				$this->container->getDb()->executeUpdate("UPDATE languages SET flag_image = ? WHERE sys_name = ?", array($flag, $sys_name));
			}
		}

		// Auto-install any new langs
		$auto_install = $this->container->getDb()->fetchColumn("SELECT value FROM settings WHERE name = 'core.lang_auto_install'");
		if ($auto_install) {
			$this->container->getEm()->getRepository('DeskPRO:Language')->installAll($langpacks);
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateLanguageCache();

		$cache = new \Application\DeskPRO\CacheInvalidator\LanguageJsCache();
		$cache->invalidateAll();

		// need to restart the Twitter daemon (pid of 0 means to not run)
		if (file_exists(dp_get_data_dir() . '/twitter.pid')) {
			$twitter_pid = intval(file_get_contents(dp_get_data_dir() . '/twitter.pid'));
		} else {
			$twitter_pid = null;
		}

		if ($twitter_pid !== 0) {
			@unlink(dp_get_data_dir() . '/twitter.pid');
		}
	}


	/**
	 * Is there another build script to run?
	 *
	 * @return bool
	 */
	public function hasNext()
	{
		$next_id = $this->getNextBuildId();
		return (bool)$next_id;
	}


	/**
	 * Get the build class for a build ID
	 *
	 * @param int $build_id
	 * @return string
	 */
	public function getBuildClass($build_id)
	{
		return 'Application\\InstallBundle\\Upgrade\\Build\\Build' . $build_id;
	}


	/**
	 * Get an array of build IDs that are waiting to be performed.
	 * The array is ordered.
	 *
	 * @return array
	 */
	public function getWaitingBuildIds()
	{
		$ret = array();

		foreach ($this->getAllBuildIds() as $build_id) {
			if ($this->db_version < $build_id) {
				$ret[] = $build_id;
			}
		}

		return $ret;
	}


	/**
	 * Get a list of all upgrade build script available
	 *
	 * @return array
	 */
	public function getAllBuildIds()
	{
		if ($this->build_list !== null) {
			return $this->build_list;
		}

		$finder = new \Symfony\Component\Finder\Finder();
		$finder->in(DP_ROOT.'/src/Application/InstallBundle/Upgrade/Build')->files()->name('/Build(.*?)\.php/');

		$this->build_list = array();
		foreach ($finder as $f) {
			$build_id    = Strings::extractRegexMatch('/Build(.*?)\.php/', $f->getFilename(), 1);
			$this->build_list[] = $build_id;
		}

		sort($this->build_list, \SORT_NUMERIC);

		return $this->build_list;
	}


	/**
	 * Gets the next build ID or 0 if the db is up to date
	 *
	 * @return int
	 */
	public function getNextBuildId()
	{
		foreach ($this->getAllBuildIds() as $build_id) {
			if ($this->db_version < $build_id) {
				return $build_id;
			}
		}

		return 0;
	}


	/**
	 * Get the latest build id
	 *
	 * @return int
	 */
	public function getLatestBuildId()
	{
		return Arrays::getLastItem($this->getAllBuildIds());
	}


	/**
	 * Formats a build ID
	 *
	 * @param int $build_id
	 * @return string
	 */
	public function formatBuildId($build_id)
	{
		return date('Y-m-d H:i:s', $build_id);
	}
}