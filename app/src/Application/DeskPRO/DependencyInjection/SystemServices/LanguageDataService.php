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

namespace Application\DeskPRO\DependencyInjection\SystemServices;

use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Application\DeskPRO\Entity\Language;
use Orb\Util\Arrays;

class LanguageDataService extends BaseRepositoryService
{
	protected $has_init = false;

	/**
	 * @var int
	 */
	protected $default_lang_id = 1;

	/**
	 * Loaded langs
	 * @var array
	 */
	protected $languages = array();

	/**
	 * @var int
	 */
	protected $count = 1;

	public static function create(DeskproContainer $container, array $options = null)
	{
		if (!$options) $options = array();
		$options['entity'] = 'Application\\DeskPRO\\Entity\\Language';
		$options['default_lang_id'] = $container->getSetting('core.default_language_id');

		$em = $container->getEm();
		$o = new static($em, $options);
		return $o;
	}

	public function init()
	{
		$this->default_lang_id = $this->options->get('default_lang_id');
	}


	/**
	 * @return bool
	 */
	public function isLangSystemEnabled()
	{
		return $this->isMultiLang();
	}


	/**
	 * True to enable multi-language interfaces. Languages might be enabled, but if only one
	 * lang exists then it effectively means that the interface should still act as though
	 * its disabled.
	 *
	 * @return bool
	 */
	public function isMultiLang()
	{
		$this->preload();
		return $this->count > 1;
	}


	/**
	 * Find a language by a lang code
	 *
	 * @param string $code
	 * @return \Application\DeskPRO\Entity\Language|null
	 */
	public function findLangCode($code)
	{
		$this->preload();
		foreach ($this->languages as $lang) {
			if ($lang->lang_code == $code) {
				return $lang;
			}
		}

		return null;
	}


	/**
	 * Get an array of lang codes
	 *
	 * @return string[]
	 */
	public function getLangCodes()
	{
		$this->preload();
		$codes = array();

		foreach ($this->languages as $lang) {
			$codes[] = $lang->lang_code;
		}

		return $codes;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Language
	 */
	public function getDefault()
	{
		$this->preload();
		return $this->get($this->default_lang_id);
	}


	/**
	 * @return int
	 */
	public function getDefaultId()
	{
		return $this->default_lang_id;
	}


	/**
	 * @param $id
	 * @return \Application\DeskPRO\Entity\Language
	 */
	public function get($id)
	{
		$this->preload();
		return isset($this->languages[$id]) ? $this->languages[$id] : null;
	}


	/**
	 * @return int
	 */
	public function count()
	{
		$this->preload();
		return $this->count;
	}


	/**
	 * @param int $id
	 * @return bool
	 */
	public function has($id)
	{
		$this->preload();
		return isset($this->languages[$id]);
	}


	/**
	 * Loads the required data
	 *
	 * @return mixed
	 */
	protected function preload()
	{
		if ($this->has_init) {
			return;
		}
		$this->has_init = true;

		$this->languages = $this->em->createQuery("
			SELECT l
			FROM DeskPRO:Language l INDEX BY l.id
			ORDER BY l.title ASC
		")->execute();

		$this->count = count($this->languages);
	}


	/**
	 * Get languages by ID
	 *
	 * @param array $ids
	 * @param bool $keep_order
	 * @return \Application\DeskPRO\Entity\Language[]
	 */
	public function getByIds(array $ids, $keep_order = false)
	{
		$this->preload();
		$ret = array();

		foreach ($ids as $id) {
			if (isset($this->languages[$id])) {
				$ret[$id] = $this->languages[$id];
			}
		}

		if ($keep_order) {
			Arrays::orderIdArray($ids, $ret);
		}

		return $ret;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Language[]
	 */
	public function getAll()
	{
		$this->preload();
		return $this->languages;
	}


	/**
	 * Get names of langs
	 *
	 * @param array|null $for_ids
	 * @return string[]
	 */
	public function getTitles(array $for_ids = null)
	{
		$this->preload();
		$ret = array();

		if (!$for_ids) {
			$for_ids = array_keys($this->languages);
		}

		foreach ($for_ids as $id) {
			$ret[$id] = $this->languages[$id]->getTitle();
		}

		return $ret;
	}
}