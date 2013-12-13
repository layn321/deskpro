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

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\AdminBundle\Form\EditLanguageType;
use Orb\Util\Arrays;
use Symfony\Component\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Application\DeskPRO\Languages\LanguagePackFile;
use Application\DeskPRO\Languages\LanguagePack;
use Application\DeskPRO\Languages\LanguageInstaller;

class LanguagesController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	public function indexAction()
    {
		$langpacks = new \Application\DeskPRO\Languages\LangPackInfo();
		$packs = $langpacks->getLangTitles();
		$packs_flags = array();

		foreach ($packs as $id => $title) {
			$flag = $langpacks->getLangInfo($id, 'flag_image');
			$packs_flags[$id] = $flag;
		}

		$packs_local = $langpacks->getLangTitles(true);

		$langs = $this->container->getDataService('Language')->getAll();
		$installed_packs = array();
		foreach ($langs as $l) {
			$installed_packs[$l->getSysName()] = $l;
		}

        return $this->render('AdminBundle:Languages:index.html.twig', array(
			'packs' => $packs,
			'packs_local' => $packs_local,
			'installed_packs' => $installed_packs,
			'packs_flags' => $packs_flags,
		));
	}

	############################################################################
	# install
	############################################################################

	public function installPackAction($id)
	{
		$langpacks = new \Application\DeskPRO\Languages\LangPackInfo();

		if (!$langpacks->hasLang($id)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$lang = $langpacks->newLanguageEntity($id);

		$this->db->beginTransaction();
		try {
			$this->em->persist($lang);
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
		}

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_langs', array('language_id' => $lang->getId()));
	}

	############################################################################
	# edit-departments
	############################################################################

	public function departmentsAction($language_id, $type)
	{
		$vars = $this->getLangInfo($language_id);

		if ($type == 'tickets') {
			$all_departments = $this->em->createQuery("
				SELECT dep
				FROM DeskPRO:Department dep
				WHERE dep.parent IS NULL AND dep.is_tickets_enabled = true
				ORDER BY dep.display_order ASC
			")->getResult();
		} else {
			$all_departments = $this->em->createQuery("
				SELECT dep
				FROM DeskPRO:Department dep
				WHERE dep.parent IS NULL AND dep.is_chat_enabled = true
				ORDER BY dep.display_order ASC
			")->getResult();
		}

		$vars['all_departments'] = $all_departments;

		$group = 'obj_department';
		$vars['lang_phrases'] = $this->em->getRepository('DeskPRO:Phrase')->getLanguagePhrasesInGroup($vars['language'], $group);

		return $this->render('AdminBundle:Languages:lang-phrases-departments.html.twig', $vars);
	}

	############################################################################
	# edit-products
	############################################################################

	public function productsAction($language_id)
	{
		$vars = $this->getLangInfo($language_id);

		$all_products = $this->em->createQuery("
			SELECT prod
			FROM DeskPRO:Product prod
			WHERE prod.parent IS NULL
			ORDER BY prod.display_order ASC
		")->getResult();

		$vars['all_products'] = $all_products;

		$group = 'obj_product';
		$vars['lang_phrases'] = $this->em->getRepository('DeskPRO:Phrase')->getLanguagePhrasesInGroup($vars['language'], $group);

		return $this->render('AdminBundle:Languages:lang-phrases-products.html.twig', $vars);
	}

	############################################################################
	# edit-ticket-categories
	############################################################################

	public function ticketCategoriesAction($language_id)
	{
		$vars = $this->getLangInfo($language_id);

		$all_categories = $this->em->createQuery("
			SELECT cat
			FROM DeskPRO:TicketCategory cat
			WHERE cat.parent IS NULL
			ORDER BY cat.display_order ASC
		")->getResult();

		$vars['all_categories'] = $all_categories;

		$group = 'obj_department';
		$vars['lang_phrases'] = $this->em->getRepository('DeskPRO:Phrase')->getLanguagePhrasesInGroup($vars['language'], $group);

		return $this->render('AdminBundle:Languages:lang-phrases-ticket-categories.html.twig', $vars);
	}

	############################################################################
	# edit-ticket-priorities
	############################################################################

	public function ticketPrioritiesAction($language_id)
	{
		$vars = $this->getLangInfo($language_id);

		$all_priorities = $this->em->createQuery("
			SELECT pri
			FROM DeskPRO:TicketPriority pri
			ORDER BY pri.priority ASC
		")->getResult();

		$vars['all_priorities'] = $all_priorities;

		$group = 'obj_ticketpriority';
		$vars['lang_phrases'] = $this->em->getRepository('DeskPRO:Phrase')->getLanguagePhrasesInGroup($vars['language'], $group);

		return $this->render('AdminBundle:Languages:lang-phrases-ticket-priorities.html.twig', $vars);
	}

	############################################################################
	# edit-ticket-workflows
	############################################################################

	public function ticketWorkflowsAction($language_id)
	{
		$vars = $this->getLangInfo($language_id);

		$all_priorities = $this->em->createQuery("
			SELECT work
			FROM DeskPRO:TicketWorkflow work
			ORDER BY work.display_order ASC
		")->getResult();

		$vars['all_priorities'] = $all_priorities;

		$group = 'obj_ticketworkflow';
		$vars['lang_phrases'] = $this->em->getRepository('DeskPRO:Phrase')->getLanguagePhrasesInGroup($vars['language'], $group);

		return $this->render('AdminBundle:Languages:lang-phrases-ticket-workflows.html.twig', $vars);
	}

	############################################################################
	# custom-ticket-fields
	############################################################################

	public function customFieldsAction($language_id, $field_type)
	{
		switch ($field_type) {
			case 'tickets':
				$ent = 'DeskPRO:CustomDefTicket';
				$group = 'obj_customdefticket';
				break;

			case 'people':
				$ent = 'DeskPRO:CustomDefPerson';
				$group = 'obj_customdefperson';
				break;

			case 'organizations':
				$ent = 'DeskPRO:CustomDefOrganization';
				$group = 'obj_customdeforganization';
				break;

			default: throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$vars = $this->getLangInfo($language_id);

		$all_fields = $this->em->createQuery("
			SELECT f
			FROM $ent f
			WHERE f.parent IS NULL
			ORDER BY f.display_order ASC
		")->getResult();

		$vars['all_fields'] = $all_fields;
		$vars['lang_group'] = $group;

		$vars['lang_phrases'] = $this->em->getRepository('DeskPRO:Phrase')->getLanguagePhrasesInGroup($vars['language'], $group);

		return $this->render('AdminBundle:Languages:lang-phrases-fields.html.twig', $vars);
	}

	############################################################################
	# edit-feedback
	############################################################################

	public function feedbackAction($language_id)
	{
		$vars = $this->getLangInfo($language_id);

		$all_statuses = $this->em->createQuery("
			SELECT s
			FROM DeskPRO:FeedbackStatusCategory s
			ORDER BY s.display_order ASC
		")->getResult();
		$vars['all_statuses'] = $all_statuses;

		$all_types = $this->em->createQuery("
			SELECT s
			FROM DeskPRO:FeedbackCategory s
			ORDER BY s.display_order ASC
		")->getResult();
		$vars['all_types'] = $all_types;

		$vars['lang_phrases'] = $this->em->getRepository('DeskPRO:Phrase')->getLanguagePhrasesInGroup($vars['language'], 'obj_feedbackstatuscategory');
		$vars['lang_phrases'] = array_merge($this->em->getRepository('DeskPRO:Phrase')->getLanguagePhrasesInGroup($vars['language'], 'obj_feedbackcategory'), $vars['lang_phrases']);

		return $this->render('AdminBundle:Languages:lang-phrases-feedback.html.twig', $vars);
	}

	############################################################################
	# kb-cats
	############################################################################

	public function kbCatsAction($language_id)
	{
		$vars = $this->getLangInfo($language_id);

		$all_categories = $this->em->createQuery("
			SELECT cat
			FROM DeskPRO:ArticleCategory cat INDEX BY cat.id
			ORDER BY cat.display_order ASC
		")->getResult();

		$vars['all_categories'] = $all_categories;

		$vars['flat_hierarchy'] = $this->em->getRepository('DeskPRO:ArticleCategory')->getFlatHierarchy();

		$group = 'obj_articlecategory';
		$vars['lang_phrases'] = $this->em->getRepository('DeskPRO:Phrase')->getLanguagePhrasesInGroup($vars['language'], $group);

		return $this->render('AdminBundle:Languages:lang-phrases-kbcats.html.twig', $vars);
	}

	############################################################################
	# edit-language
	############################################################################

	public function editLanguageAction($language_id)
	{
		$vars = $this->getLangInfo($language_id);

		$files = \Symfony\Component\Finder\Finder::create()->files()->in(DP_WEB_ROOT.'/web/images/flags')->name('*.png');
		$flags = array();

		foreach ($files as $f) {
			$flags[] = $f->getFileName();
		}

		if ($this->in->getBool('process')) {
			$lang = $vars['language'];
			$lang->title = $this->in->getString('language.title');
			$lang->locale = $this->in->getString('language.locale');

			$flag = $this->in->getString('language.flag');
			if ($flag && in_array($flag, $flags)) {
				$lang->flag_image = $flag;
			} else {
				$lang->flag_image = '';
			}

			$this->em->persist($lang);
			$this->em->flush();
		}

		$form = $this->get('form.factory')->create(new EditLanguageType(), $vars['language']);
		$vars['form'] = $form->createView();
		$vars['flags'] = $flags;

		return $this->render('AdminBundle:Languages:lang-edit.html.twig', $vars);
	}

	public function deleteLanguageAction($language_id, $security_token)
	{
		if (!$this->session->getEntity()->checkSecurityToken('delete_lang', $security_token)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$language = $this->getLanguageOr404($language_id);

		if ($this->container->getDataService('Language')->getDefault()->getId() == $language->getId()) {
			$url = $this->generateUrl('admin_langs');
			return $this->renderStandardError("You cannot delete the default lanugage. You may change the default language from <a href='$url'>Admin &rarr; Settings &rarr; Languages</a>.");
		}

		$this->em->beginTransaction();
		try {
			$this->em->remove($language);
			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_langs');
	}

	public function editPhrasesAction($language_id, $group)
	{
		$vars = $this->getLangInfo($language_id);
		$vars['group'] = $group;
		$vars['showinggroup'] = $group;

		$vars['lang_phrases'] = array('custom' => array(), 'original' => array());
		$vars['lang_phrases']['custom'] = $this->em->getRepository('DeskPRO:Phrase')->getCustomPhrases($vars['language']);

		if ($group == 'CUSTOM') {
			$groups = array();
			foreach ($vars['lang_phrases']['custom'] as $phrase) {
				$groups[] = $phrase->groupname;
			}
			$groups[] = 'custom';
			$groups = array_unique($groups);
		} else {
			$groups = array($group);
		}

		$vars['master_phrases'] = array();
		if ($groups) {
			foreach ($groups as $g) {
				$groups_reader = new \Application\DeskPRO\ResourceScanner\LanguagePhrases();
				$master_phrases = $groups_reader->getGroupPhrases($g);
				$vars['master_phrases'] = array_merge($vars['master_phrases'], $master_phrases);
			}
			$vars['lang_phrases']['original'] = $vars['master_phrases'];

			foreach ($groups as $g) {
				$groups_reader = new \Application\DeskPRO\ResourceScanner\LanguagePhrases(str_replace('%DP_ROOT%', DP_ROOT, $vars['language']->base_filepath));
				$master_phrases = $groups_reader->getGroupPhrases($g);
				$vars['lang_phrases']['original'] = array_merge($vars['lang_phrases']['original'], $master_phrases);
			}
		}

		if ($group == 'CUSTOM') {
			$custom_ids = App::getDb()->fetchAllKeyValue("
				SELECT name
				FROM phrases
				WHERE groupname = 'custom' OR language_id = $language_id
			", array(), 0, 0);

			foreach ($vars['master_phrases'] as $k => $v) {
				if (!isset($custom_ids[$k])) {
					unset($vars['master_phrases'][$k]);
				}
			}
			foreach ($custom_ids as $k) {
				if (!isset($vars['master_phrases'][$k]) && isset($vars['lang_phrases']['custom'][$k])) {
					$vars['master_phrases'][$k] = $vars['lang_phrases']['custom'][$k];
				}
			}
		}

		if (App::getRequest()->isPartialRequest() == 'overlay') {
			return $this->render('AdminBundle:Languages:lang-phrases-overlay.html.twig', $vars);
		}

		return $this->render('AdminBundle:Languages:lang-phrases.html.twig', $vars);
	}

	public function addCustomPhraseAction($language_id)
	{
		$language = $this->getLanguageOr404($language_id);

		$phrase_id   = $this->in->getString('phrase_id');
		$phrase_id = preg_replace('#[^a-zA-Z0-9_\-]#', '_', $phrase_id);
		$phrase_id = 'custom.' . $phrase_id;

		$phrase_text = $this->in->getString('custom_phrase');

		App::getDb()->replace('phrases', array(
			'language_id'   => $language_id,
			'name'          => $phrase_id,
			'groupname'     => 'custom',
			'phrase'        => $phrase_text,
			'created_at'    => date('Y-m-d H:i:s'),
			'updated_at'    => date('Y-m-d H:i:s')
		));

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		$cache = new \Application\DeskPRO\CacheInvalidator\LanguageJsCache();
		$cache->invalidateLanguage($language_id);

		return $this->createJsonResponse(array(
			'success'   => true,
			'phrase_id' => $phrase_id,
		));
	}

	public function savePhrasesAction($language_id)
	{
		$language = $this->getLanguageOr404($language_id);

		$phrases = $this->in->getCleanValueArray('phrases', 'string', 'string');

		$phrase_reader = new \Application\DeskPRO\ResourceScanner\LanguagePhrases();

		$this->em->beginTransaction();
		try {
			foreach ($phrases as $phrase_id => $phrase_text) {
				$phrase_text = trim($phrase_text);

				$phrase = $this->em->getRepository('DeskPRO:Phrase')->getPhraseForLanguage($phrase_id, $language);
				if (!$phrase) {
					$phrase = new \Application\DeskPRO\Entity\Phrase();
					$phrase->language = $language;
					$phrase->name = $phrase_id;
					$master_phrase = $phrase_reader->getMasterPhrase($phrase_id);
					if (!$master_phrase) {
						$master_phrase = '';
					}
					$phrase->original_phrase = $master_phrase;
					$phrase->original_hash = $phrase_reader->generatePhraseHash($master_phrase);
				}

				if ($phrase_text == $phrase->original_phrase || !$phrase_text) {
					if ($phrase->id) {
						$this->em->remove($phrase);
					}
					continue;
				}

				if ($phrase_text) {
					$phrase->phrase = $phrase_text;
					$this->em->persist($phrase);
				}
			}

			$this->em->flush();
			$this->em->commit();

			$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
			$cache->invalidateAll();

			$cache = new \Application\DeskPRO\CacheInvalidator\LanguageJsCache();
			$cache->invalidateLanguage($language_id);
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		return $this->createJsonResponse(array('success' => true));
	}

	public function savePhraseArrayAction()
	{
		$phrases = $this->container->getIn()->getCleanValueArray('lang_phrase', 'raw', 'string');
		$phrase_reader = new \Application\DeskPRO\ResourceScanner\LanguagePhrases();

		foreach ($phrases as $phrase_id => $lang_phrase) {
			$lang_phrase = (array)$lang_phrase;
			foreach ($lang_phrase as $lang_id => $phrase_text) {
				$language = App::getDataService('Language')->get($lang_id);
				if (!$language) {
					continue;
				}

				$phrase = $this->em->getRepository('DeskPRO:Phrase')->getPhraseForLanguage($phrase_id, $language);
				if (!$phrase) {
					$phrase = new \Application\DeskPRO\Entity\Phrase();
					$phrase->language = $language;
					$phrase->name = $phrase_id;
					$master_phrase = $phrase_reader->getMasterPhrase($phrase_id);
					if (!$master_phrase) {
						$master_phrase = '';
					}
					$phrase->original_phrase = $master_phrase;
					$phrase->original_hash = $phrase_reader->generatePhraseHash($master_phrase);
				}

				if ($phrase_text == $phrase->original_phrase || !$phrase_text) {
					if ($phrase->id) {
						$this->em->remove($phrase);
					}
					continue;
				}

				$phrase->phrase = $phrase_text;

				$this->em->persist($phrase);
			}

			$this->em->flush();
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		$cache = new \Application\DeskPRO\CacheInvalidator\LanguageJsCache();
		$cache->invalidateAll();

		return $this->createJsonResponse(array('success' => true));
	}

	public function getPhraseTextAction()
	{
		$phrase_id = $this->in->getString('phrase_id');

		$languages = App::getDataService('Language')->getAll();

		$data = array();
		$data['phrase_id'] = $phrase_id;

		foreach ($languages as $lang) {
			$lang_row = array();
			$lang_row['language_id']     = $lang->getId();
			$lang_row['language_title']  = $lang->getTitle();
			$lang_row['phrase']          = App::getTranslator()->getPhraseText($phrase_id, $lang);

			$data['langs'][] = $lang_row;
		}

		return $this->createJsonResponse($data);
	}

	############################################################################
	# settings
	############################################################################

	public function toggleAutoInstallAction()
	{
		if ($this->container->getSetting('core.lang_auto_install')) {
			$set = false;
		} else {
			$set = true;
		}

		$this->container->getSettingsHandler()->setSetting('core.lang_auto_install', $set);

		if ($set) {
			$langpacks = new \Application\DeskPRO\Languages\LangPackInfo();
			$this->em->getRepository('DeskPRO:Language')->installAll($langpacks);
		}

		return $this->redirectRoute('admin_langs');
	}

	############################################################################
	# mass-update-tickets
	############################################################################

	public function massUpdateTicketsAction()
    {
		$done = false;
		$count = 0;

		$from_lang_id = -1;
		$to_lang_id   = -1;

		if ($this->in->getBool('process')) {
			$from_lang_id = $this->in->getUint('from_lang');
			$to_lang_id   = $this->in->getUint('to_lang');

			if (
				($from_lang_id && !$this->container->getLanguageData()->get($from_lang_id))
				|| ($to_lang_id && !$this->container->getLanguageData()->get($to_lang_id))
			) {
				throw $this->createNotFoundException();
			}

			$use_to_id = $to_lang_id;
			if (!$use_to_id) {
				$use_to_id = 'NULL';
			}

			$sql = "UPDATE tickets SET language_id = $use_to_id";
			if ($from_lang_id) $sql .= " WHERE language_id = $from_lang_id";
			$count = $this->db->executeUpdate($sql);

			$sql = "UPDATE tickets_search_active SET language_id = $use_to_id";
			if ($from_lang_id) $sql .= " WHERE language_id = $from_lang_id";
			$this->db->executeUpdate($sql);

			$done = true;
		}

		return $this->render('AdminBundle:Languages:mass-set-tickets.html.twig', array(
			'done' => $done,
			'count' => $count,
			'to_lang_id' => $to_lang_id,
			'from_lang_id' => $from_lang_id,
		));
	}

	############################################################################
	# mass-update-people
	############################################################################

	public function massUpdatePeopleAction()
    {
		$done = false;
		$count = 0;

		$from_lang_id = -1;
		$to_lang_id   = -1;

		if ($this->in->getBool('process')) {
			$from_lang_id = $this->in->getUint('from_lang');
			$to_lang_id   = $this->in->getUint('to_lang');

			if (
				($from_lang_id && !$this->container->getLanguageData()->get($from_lang_id))
				|| ($to_lang_id && !$this->container->getLanguageData()->get($to_lang_id))
			) {
				throw $this->createNotFoundException();
			}

			$use_to_id = $to_lang_id;
			if (!$use_to_id) {
				$use_to_id = 'NULL';
			}

			$sql = "UPDATE people SET language_id = $use_to_id";
			if ($from_lang_id) $sql .= " WHERE language_id = $from_lang_id";
			$count = $this->db->executeUpdate($sql);

			$done = true;
		}

		return $this->render('AdminBundle:Languages:mass-set-people.html.twig', array(
			'done' => $done,
			'count' => $count,
			'to_lang_id' => $to_lang_id,
			'from_lang_id' => $from_lang_id,
		));
	}

	############################################################################

	protected function getLangInfo($language_id)
	{
		if (is_object($language_id)) {
			$language = $language_id;
		} elseif ($language_id) {
			$language = $this->getLanguageOr404($language_id);
		} else {
			$language = new \Application\DeskPRO\Entity\Language();
		}

		$groups_reader = new \Application\DeskPRO\ResourceScanner\LanguagePhrases();

		$vars = array();
		$vars['language'] = $language;
		$phrase_groups = $groups_reader->getGroups();

		// Order so user, agent, admin
		$vars['phrase_groups'] = array(
			'user'  => $phrase_groups['user'],
			'agent' => $phrase_groups['agent'],
			'admin' => $phrase_groups['admin'],
		);

		return $vars;
	}

	/**
	 * @return \Application\DeskPRO\Entity\Language
	 */
	protected function getLanguageOr404($language_id)
	{
		$language = $this->em->find('DeskPRO:Language', $language_id);
		if (!$language) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no language with ID $language_id");
		}

		return $language;
	}
}
