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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;

use \Doctrine\ORM\EntityRepository;
use Application\DeskPRO\Languages\LangPackInfo;

class Language extends AbstractEntityRepository
{
	protected $lang_titles = null;
	protected $default_lang = null;

	/**
	 * @return array
	 */
	public function getTitles($for_ids = null)
	{
		if ($this->lang_titles === null) {
            $db = App::getDb();
            $this->lang_titles = $db->fetchAllKeyValue("
                SELECT id, title
                FROM languages
                ORDER BY title ASC
            ");
        }

        if (!$for_ids) {
            return $this->lang_titles;
        }

        $ret = array();
        foreach ((array)$for_ids as $id) {
            $ret[$id] = $this->lang_titles[$id];
        }

        return $ret;
	}



	/**
	 * @return \Application\DeskPRO\Entity\Language
	 */
	public function getDefault()
	{
		if ($this->default_lang !== null) {
			return $this->default_lang;
		}

		$lang_id = App::getSetting('core.default_language_id');
		if (!$lang_id) {
			$lang_id = 1;
		}

		$this->default_lang = $this->find($lang_id);

		return $this->default_lang;
	}


	/**
	 * Install all lang packs form $langpacks that arent already installed.
	 *
	 * @param \Application\DeskPRO\Languages\LangPackInfo $langpacks
	 * @throws \Exception
	 */
	public function installAll(LangPackInfo $langpacks)
	{
		$em = $this->_em;
		$db = $em->getConnection();

		$installed = $db->fetchAllCol("
			SELECT sys_name
			FROM languages
		");

		$installed = array_flip($installed);

		foreach ($langpacks->getLangIds() as $id) {
			if (isset($installed[$id])) {
				continue;
			}

			$lang = $langpacks->newLanguageEntity($id);
			$em->persist($lang);
		}

		$db->beginTransaction();
		try {
			$em->flush();
			$db->commit();
		} catch (\Exception $e) {
			$db->rollback();
			throw $e;
		}
	}
}
