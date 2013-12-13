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

use Doctrine\ORM\EntityManager;
use Application\DeskPRO\Entity\Language;

class LanguageInstaller
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\Entity\Language
	 */
	protected $upgrade_language;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


	/**
	 * This will insert the lang pack into an existing language,
	 * upgrading it instead of installing a brand new one.
	 *
	 * @param \Application\DeskPRO\Entity\Language $language
	 */
	public function setUpgradeLanguage(Language $language)
	{
		$this->upgrade_language = $language;
	}


	/**
	 * Install a language pack from a pack file located at $pack_path.
	 *
	 * @param string $pack_path
	 */
	public function insatllFromPackFilePath($pack_path)
	{
		$pack_file = LanguagePackFile::newFromFile($pack_path);
		return $this->installPack($pack_file->getPack());
	}


	/**
	 * Install a language pack from a pack file loaded into a string.
	 *
	 * @param string $pack_string
	 */
	public function installFromPackFileString($pack_string)
	{
		$pack_file = LanguagePackFile::newFromString($pack_string);
		return $this->installPack($pack_file->getPack());
	}


	/**
	 * Install a language pack from an already loaded LanguagePackFile.
	 *
	 * @param \Application\DeskPRO\Languages\LanguagePackFile $pack_file
	 */
	public function installFromPackFile(LanguagePackFile $pack_file)
	{
		return $this->installPack($pack_file->getPack());
	}


	/**
	 * Install a new language pack
	 *
	 * @param \Application\DeskPRO\Languages\LanguagePack $pack
	 * @return \Application\DeskPRO\Entity\Language
	 * @throws \Exception
	 */
	public function installPack(LanguagePack $pack)
	{
		$this->em->getConnection()->beginTransaction();
		try {

			if ($this->upgrade_language) {

				$lang = $this->upgrade_language;
				$lang->sys_name = $pack->sys_name;
				$lang->lang_code = $pack->lang_code;

				// Delete all phrases that arent customized
				$this->em->getConnection()->executeUpdate("
					DELETE FROM phrases
					WHERE language_id = ? AND phrase IS NULL OR phrase = ''
				", array($lang->getId()));

				// Figure out obsolete phrases to delete
				$custom_phrase_ids = $this->em->getConnection()->fetchAllCol("
					SELECT name FROM phrases
					WHERE language_id = ?
				", array($lang->getId()));

				$delete_ids = array();

				foreach ($custom_phrase_ids as $phrase_id) {
					if (!isset($pack->phrases[$phrase_id])) {
						$delete_ids[] = $phrase_id;
					}
				}

				if ($delete_ids) {
					$delete_ids = '"' . implode('","', $delete_ids) . '"';

					$this->em->getConnection()->executeUpdate("
						DELETE FROM phrases
						WHERE language_id = ? AND name IN ($delete_ids)
					", $lang->getId());
				}

			} else {
				$lang = new Language();
				$lang->title     = $pack->title;
				$lang->locale    = $pack->locale;
				$lang->sys_name  = $pack->sys_name;
				$lang->lang_code = $pack->lang_code;

				$this->em->persist($lang);
				$this->em->flush();
			}

			$lang_id = $lang->getId();
			$created_at = date('Y-m-d H:i:s');

			$insert_phrases = array();
			foreach ($pack->phrases as $id => $phrase) {

				$groupname = \Orb\Util\Strings::rexplode('.', $id);
				$groupname = array_shift($groupname);

				$insert_phrases[] = array(
					'language_id'       => $lang_id,
					'name'              => $id,
					'groupname'         => $groupname,
					'original_phrase'   => $phrase,
					'original_hash'     => sha1($phrase),
					'created_at'        => $created_at,
					'updated_at'        => $created_at,
				);
			}

			$batch = array_chunk($insert_phrases, 150);
			foreach ($batch as $b) {
				$this->em->getConnection()->batchInsert('phrases', $b);
			}

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $lang;
	}
}