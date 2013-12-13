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
 * @category Controller
 */

namespace Application\DeskPRO\ResourceScanner;

use Application\DeskPRO\App;
use Orb\Util\Arrays;

/**
 * Scans the filesystem for an array of all templates
 */
class TemplateFiles
{
	protected $use_map_file = true;


	/**
	 * @param bool $use_map_file
	 */
	public function __construct($use_map_file = true)
	{
		$this->use_map_file = $use_map_file;
	}


	/**
	 * Get the map array
	 *
	 * @return array
	 */
	public function getTemplateMap()
	{
		$map_file_path = DP_ROOT.'/sys/config/template-map.php';

		if (!$this->use_map_file || !is_file($map_file_path)) {
			return $this->genTemplateMap();
		}

		$map = require $map_file_path;
		return $map;
	}


	/**
	 * Scans the filesystem to generate the map on-demand
	 *
	 * @return array
	 */
	public function genTemplateMap()
	{
		$paths = array(
			'AdminBundle'   => DP_ROOT.'/src/Application/AdminBundle/Resources/views',
			'AgentBundle'   => DP_ROOT.'/src/Application/AgentBundle/Resources/views',
			'DeskPRO'       => DP_ROOT.'/src/Application/DeskPRO/Resources/views',
			'ReportBundle'  => DP_ROOT.'/src/Application/ReportBundle/Resources/views',
			'UserBundle'    => DP_ROOT.'/src/Application/UserBundle/Resources/views',
		);

		$tpl_info = array();

		foreach ($paths as $bundle => $dir) {
			$finder = new \Symfony\Component\Finder\Finder();
			$finder->files()->name('*.twig')->in($dir);

			foreach ($finder as $file) {
				/** @var \Symfony\Component\Finder\SplFileinfo $file */

				$filepath = $file->getRealPath();
				$filepath = str_replace('\\', '/', $filepath);
				$dir = str_replace('\\', '/', $dir);

				$tplname = str_replace($dir . '/', ':', $filepath);
				$tplname = str_replace('/', ':', $tplname);
				if (substr_count($tplname, ':') < 2) {
					$tplname = ':' . $tplname; // for layouts that are in top dir, MyBundle::layout
				}
				$tplname = $bundle . $tplname;

				// Dev templates arent included
				if (strpos($tplname, ':Dev:') !== false) {
					continue;
				}

				$tpl_info[$tplname] = array(
					'path' => $file->getRealPath(),
					'last_updated' => 0,
				);
			}
		}

		return $tpl_info;
	}


	/**
	 * Templates that should be categorized as "user portal" type templates.
	 */
	public function getUserTemplates()
	{
		$raw_map = $this->getTemplateMap();

		$map = array();

		foreach ($raw_map as $k => $info) {
			if (strpos($k, 'UserBundle:') !== false || strpos($k, 'DeskPRO:custom_fields:') !== false) {
				$map[$k] = $info;
			}
		}

		return $map;
	}


	/**
	 * Non-user portal templates
	 */
	public function getEmailTemplates()
	{
		$raw_map = $this->getTemplateMap();

		$map = array();

		foreach ($raw_map as $k => $info) {
			if (strpos($k, 'DeskPRO:emails_agent:') !== false || strpos($k, 'DeskPRO:emails_common:') !== false || strpos($k, 'DeskPRO:emails_user:') !== false) {
				$map[$k] = $info;
			}
		}

		return $map;
	}


	/**
	 * Email templates
	 */
	public function getOtherTemplates()
	{
		$raw_map = $this->getTemplateMap();

		$map = array();

		foreach ($raw_map as $k => $info) {
			if (strpos($k, 'AdminBundle:') !== false || strpos($k, 'AgentBundle:') !== false || strpos($k, 'DeskPRO:') !== false) {
				$map[$k] = $info;
			}
		}

		return $map;
	}


	/**
	 * Group the map into [bundle][dir][tplname]
	 *
	 * @param array $map
	 * @return array
	 */
	public function groupPrefixes(array $map)
	{
		$grouped = array();
		foreach ($map as $k => $v) {
			preg_match('#^(.*?):(.*?):(.*?)$#', $k, $m);
			$bundle = $m[1];
			$dir = $m[2];
			if ($dir) {
				$dir = 'TOP';
			}

			if (!isset($grouped[$bundle])) $grouped[$bundle] = array();
			if (!isset($grouped[$bundle][$dir])) $grouped[$bundle][$dir] = array();

			$grouped[$bundle][$dir][$k] = $v;
			$grouped[$bundle][$dir][$k]['shortname'] = str_replace('.twig', '', $m[3]);
		}

		return $grouped;
	}
}
