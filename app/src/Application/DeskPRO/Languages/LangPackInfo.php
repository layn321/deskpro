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

namespace Application\DeskPRO\Languages;

class LangPackInfo
{
	/**
	 * @var string
	 */
	protected $langs_dir;

	/**
	 * @var array
	 */
	protected $manifest;

	public function __construct()
	{
		$this->langs_dir = DP_ROOT.'/languages';

		$this->manifest = include($this->langs_dir . '/manifest.php');

		if (dp_get_config('debug.lang_manifest')) {
			$this->manifest = array_merge($this->manifest, dp_get_config('debug.lang_manifest'));
		}
	}


	/**
	 * @return string
	 */
	public function getLangDir()
	{
		return $this->langs_dir;
	}


	/**
	 * @return array
	 */
	public function getLangIds()
	{
		return array_keys($this->manifest);
	}


	/**
	 * @param string $id
	 * @return bool
	 */
	public function hasLang($id)
	{
		return isset($this->manifest[$id]);
	}


	/**
	 * Fetches info about a language.
	 *
	 * $key can be:
	 * - null: Array of all info
	 * - id: The lang id
	 * - lang_code: The three-letter language code (ISO 639-2)
	 * - title: Readable English title of the language
	 * - locale: The locale
	 * - has_user: Is the pack considered user interface complete?
	 * - has_agent: Is the pack considered agent interface complete?
	 * - has_admin: Is the pack considered admin interface complete?
	 *
	 * @param string $id
	 * @param string|null $key
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public function getLangInfo($id, $key = null)
	{
		if (!isset($this->manifest[$id])) {
			throw new \InvalidArgumentException("Unknown language $id");
		}

		$info = $this->manifest[$id];

		if ($key) {
			if (!isset($info[$key])) {
				return null;
			}

			return $info[$key];
		}

		return $info;
	}


	/**
	 * Get lang titles as id=>title
	 *
	 * @return array
	 */
	public function getLangTitles($local = false)
	{
		$ret = array();

		if ($local) {
			foreach ($this->manifest as $id => $info){
				$lang_file = $this->langs_dir . "/$id/user/lang.php";

				$lang = array();
				if (is_file($lang_file)) {
					$lang = require($lang_file);
				}

				if (isset($lang['user.lang.lang_title'])) {
					$ret[$id] = $lang['user.lang.lang_title'];
				} else {
					$ret[$id] = $info['title'];
				}
			}
		} else {
			foreach ($this->manifest as $id => $info) {
				$ret[$id] = $info['title'];
			}
		}

		return $ret;
	}


	/**
	 * @return array
	 */
	public function getDefaultSections()
	{
		return array('user', 'agent');
		return array('admin', 'agent', 'user');
	}


	/**
	 * @param string $section
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function getDefaultCategories($section)
	{
		switch ($section) {
			case 'user':  return array('chat', 'defaults', 'downloads', 'emails', 'email_subjects', 'error', 'feedback', 'general', 'knowledgebase', 'lang', 'news', 'portal', 'profile', 'tickets', 'time', 'widget');
			case 'agent': return array('chat', 'chrome', 'deal', 'defaults', 'emails', 'feedback', 'general', 'interface', 'login', 'media', 'organizations', 'people', 'publish', 'report', 'search', 'settings', 'snippets', 'tasks', 'tickets', 'time', 'twitter', 'userchat', 'usertrack');
			case 'admin': return array('agents', 'api', 'banning', 'billing', 'custom_fields', 'departments', 'designer', 'emailtpl_desc', 'feedback', 'gateway', 'general', 'languages', 'license', 'logs', 'menu', 'plugins', 'portal', 'products', 'server', 'settings', 'setup', 'templates', 'tickets', 'twitter', 'user_groups', 'user_registration', 'user_rules');
		}

		throw new \InvalidArgumentException("Invalid section $section");
	}


	/**
	 * @param string $id
	 * @return \Application\DeskPRO\Entity\Language
	 */
	public function newLanguageEntity($id)
	{
		if (!$this->hasLang($id)) {
			throw new \InvalidArgumentException();
		}

		$lang = new \Application\DeskPRO\Entity\Language();
		$lang->sys_name      = $this->getLangInfo($id, 'id');
		$lang->title         = $this->getLangInfo($id, 'title');
		$lang->lang_code     = $this->getLangInfo($id, 'lang_code');
		$lang->flag_image    = $this->getLangInfo($id, 'flag_image') ?: '';
		$lang->locale        = $this->getLangInfo($id, 'locale');
		$lang->is_rtl        = $this->getLangInfo($id, 'is_rtl');
		$lang->has_user      = $this->getLangInfo($id, 'has_user');
		$lang->has_agent     = $this->getLangInfo($id, 'has_agent');
		$lang->has_admin     = $this->getLangInfo($id, 'has_admin');
		$lang->base_filepath = '%DP_ROOT%/languages/' . $id;

		// Get the title from the lang itself
		$title_file = DP_ROOT . '/languages/' . $id . '/user/lang.php';
		if (file_exists($title_file)) {
			$tmp = require($title_file);
			if (isset($tmp['user.lang.lang_title'])) {
				$lang->title = $tmp['user.lang.lang_title'];
			}
		}

		return $lang;
	}
}