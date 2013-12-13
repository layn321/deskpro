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

use Orb\Util\Arrays;

/**
 * Loads default phrases from filesystem-based lang packs
 */
class SystemLoader implements LoaderInterface
{
	/**
	 * Array of filepath => array
	 * @var array
	 */
	protected $loaded_files = array();

	public function load($groups, $language)
	{
		$lang_packs = array();

		// Always read from the default because it has the core phrases
		$lang_packs[] = DP_ROOT . '/languages/default';

		if ($language && $language->base_filepath) {
			$lang_packs[] = str_replace('%DP_ROOT%', DP_ROOT, $language->base_filepath);
		}

		$lang_packs = array_unique($lang_packs);
		$lang_packs = Arrays::removeFalsey($lang_packs);

		$phrases = array();

		foreach ($lang_packs as $path) {
			foreach ($groups as $group) {
				$group_parts = explode('.', $group, 2);

				// agent.something => agent/something.php
				if (count($group_parts) == 2) {
					$file = $path . '/' . $group_parts[0] . '/' . $group_parts[1] . '.php';
				// agent => agent/agent.php
				} else {
					$file = $path . '/' . $group_parts[0] . '/' . $group_parts[0] . '.php';
				}

				$file_phrases = $this->loadFile($file);
				if ($file_phrases) {
					$phrases = array_merge($phrases, $file_phrases);
				}
			}
		}

		return $phrases;
	}

	/**
	 * @param string $file
	 * @return array
	 */
	public function loadFile($file)
	{
		if (isset($this->loaded_files[$file])) {
			return $this->loaded_files[$file];
		}

		if (is_file($file)) {
			$file_phrases = include($file);
			if ($file_phrases && is_array($file_phrases)) {
				$this->loaded_files[$file] = $file_phrases;
			}
		}

		if (!isset($this->loaded_files[$file])) {
			$this->loaded_files[$file] = array();
		}

		return $this->loaded_files[$file];
	}
}
