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
 * @category Translate
 */

namespace Application\DeskPRO\Translate\Loader;

/**
 * Loads core from DeskPRO core.php, and agent_whatever as AgentBundle whatever.php etc.
 */
class BundleLoader implements LoaderInterface
{
	protected $bundle_paths;

	/**
	 * @param array $bundle_paths BundleName=>Path
	 */
	public function __construct(array $bundle_paths = array())
	{
		$this->bundle_paths = array();

		foreach ($bundle_paths as $name => $path) {

			// Conver SomeNamedBundle to somenamed
			$name = strtolower($name);
			$name = str_replace('bundle', '', $name);

			$this->bundle_paths[$name] = $path;
		}
	}



	public function load($groups, $language)
	{
		// We dont actually use lang here. the bundle loader
		// is always english, used as the default.

		if (!is_array($groups)) $groups = array($groups);

		$phrases = array();

		foreach ($groups as $group) {

			$group = strtolower($group);

			$bundle_parts = explode('_', $group, 2);
			if (!isset($bundle_parts[1])) $bundle_parts[1] = $bundle_parts[0];

			list($bundle_name, $name) = $bundle_parts;

			if (!isset($this->bundle_paths[$bundle_name])) {
				continue;
			}

			$filepath = $this->bundle_paths[$bundle_name] . '/' . $name . '.php';

			if (!is_file($filepath)) {
				return array();
			}

			$phrases[$group] = include($filepath);
		}

		return $phrases;
	}
}
