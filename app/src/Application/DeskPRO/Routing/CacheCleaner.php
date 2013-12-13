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

namespace Application\DeskPRO\Routing;

/**
 * A simple class to check routing files to see if they need to be regenerated.
 *
 * This is faster than the symfony way of checking for routing changes since we only check specific
 * files, and only check mod time.
 */
class CacheCleaner
{
	protected $routing_files;
	protected $gen_files;
	protected $oldest_cache_file;
	protected $newest_routing_file;

	public function isFresh()
	{
		if ($this->getRoutingTime() < $this->getCacheTime()) {
			return true;
		}

		return false;
	}

	public function clearCache()
	{
		foreach ($this->getCacheFiles() as $f) {
			if (is_file($f)) {
				unlink($f);
			}
		}
	}

	public function getRoutingTime()
	{
		if ($this->newest_routing_file) {
			return $this->newest_routing_file;
		}

		$this->newest_routing_file = 0;
		foreach ($this->getRoutingFiles() as $f) {
			if (file_exists($f) && filemtime($f) > $this->newest_routing_file) {
				$this->newest_routing_file = filemtime($f);
			}
		}

		return $this->newest_routing_file;
	}

	public function getCacheTime()
	{
		if ($this->oldest_cache_file) {
			$this->oldest_cache_file = 0;
		}

		$this->oldest_cache_file = time();
		foreach ($this->getCacheFiles() as $f) {
			if (file_exists($f) && filemtime($f) < $this->oldest_cache_file) {
				$this->oldest_cache_file = filemtime($f);
			}
		}

		return $this->oldest_cache_file;
	}

	public function getRoutingFiles()
	{
		if ($this->routing_files) {
			return $this->routing_files;
		}

		$this->routing_files = array(
			DP_ROOT  . '/src/Application/AdminBundle/Resources/config/admin-routing.php',
			DP_ROOT  . '/src/Application/AgentBundle/Resources/config/agent-routing.php',
			DP_ROOT  . '/src/Application/ApiBundle/Resources/config/api-routing.php',
			DP_ROOT  . '/src/Application/UserBundle/Resources/config/user-routing.php',
			DP_ROOT  . '/src/Application/ReportBundle/Resources/config/reports-routing.php',
			DP_ROOT  . '/src/Application/InstallBundle/Resources/config/install-routing.php',
			DP_ROOT  . '/src/Application/Billing/Resources/config/billing-routing.php',
			DP_ROOT  . '/src/Application/DeskPRO/Resources/config/dp-routing.php',
			DP_ROOT  . '/src/Cloud/AdminBundle/Resources/config/admin-routing.php',
			DP_ROOT  . '/src/Cloud/BillingBundle/Resources/config/billing-routing.php',
		);

		return $this->routing_files;
	}

	public function getCacheFiles()
	{
		if ($this->gen_files) {
			return $this->gen_files;
		}

		$this->gen_files = array();
		foreach (array('Admin','Agent','Api','User','Install','Billing','Cli','Report') as $k) {
			$this->gen_files[] = DP_ROOT.'/sys/cache/dev/'.$k.'KerneldevUrlGenerator.php';
			$this->gen_files[] = DP_ROOT.'/sys/cache/dev/'.$k.'KerneldevUrlMatcher.php';
			$this->gen_files[] = DP_ROOT.'/sys/cache/prod/'.$k.'KerneldevUrlGenerator.php';
			$this->gen_files[] = DP_ROOT.'/sys/cache/prod/'.$k.'KerneldevUrlMatcher.php';
			$this->gen_files[] = DP_ROOT.'/sys/cache/dev-cloud/'.$k.'KerneldevUrlGenerator.php';
			$this->gen_files[] = DP_ROOT.'/sys/cache/dev-cloud/'.$k.'KerneldevUrlMatcher.php';
			$this->gen_files[] = DP_ROOT.'/sys/cache/prod-cloud/'.$k.'KerneldevUrlGenerator.php';
			$this->gen_files[] = DP_ROOT.'/sys/cache/prod-cloud/'.$k.'KerneldevUrlMatcher.php';
		}

		return $this->gen_files;
	}
}