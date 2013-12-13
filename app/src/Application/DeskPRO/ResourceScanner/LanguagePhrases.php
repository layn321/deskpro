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

class LanguagePhrases
{
	/**
	 * @var string
	 */
	protected $lang_root;

	public function __construct($lang_root = null)
	{
		if ($lang_root === null) {
			$lang_root = DP_ROOT.'/languages/default';
		}

		$this->lang_root = $lang_root;
	}

	public function getGroups()
	{
		$groups = array();

		$lang_dir = dir($this->lang_root);
		while (($dir_name = $lang_dir->read()) !== false) {
			if ($dir_name == '.' || $dir_name == '..' || $dir_name == 'export') continue;

			$dir_path = $lang_dir->path . DIRECTORY_SEPARATOR . $dir_name;
			if (!is_dir($dir_path)) continue;

			$dir = dir($dir_path);

			while (($file = $dir->read()) != false) {
				if ($file == '.' || $file == '..' || $file == 'export') continue;

				if (!isset($groups[$dir_name])) $groups[$dir_name] = array();
				$groups[$dir_name][] = str_replace('.php', '', $file);
			}
		}

		return $groups;
	}

	public function getAllUserPhrases()
	{
		$groups = $this->getGroups();
		$groups = $groups['user'];

		$phrases = array();
		foreach ($groups as $group) {
			$phrases = array_merge($phrases, $this->getGroupPhrases('user.' . $group));
		}

		return $phrases;
	}

	public function getGroupPhrases($group)
	{
		$file = str_replace('.', DIRECTORY_SEPARATOR, $group) . '.php';
		$filepath = $this->lang_root . DIRECTORY_SEPARATOR . $file;

		if (!file_exists($filepath)) {
			return array();
		}

		return include($filepath);
	}

	public function getMasterPhrase($phrase_id)
	{
		$path = DP_ROOT.'/languages/default';
		$group_parts = explode('.', $phrase_id, 3);

		if (count($group_parts) == 3) {
			$file = $path . '/' . $group_parts[0] . '/' . $group_parts[1] . '.php';
		} else {
			$file = $path . '/' . $group_parts[0] . '/' . $group_parts[0] . '.php';
		}

		if (!is_file($file) || !isset($file_phrases[$phrase_id])) {
			return '';
		}

		$file_phrases = include($file);
		return $file_phrases[$phrase_id];
	}

	public function generatePhraseHash($phrase)
	{
		$phrase = preg_replace('#\s#', '', $phrase);
		return sha1($phrase);
	}
}
