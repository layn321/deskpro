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

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\Entity\TextSnippet;
use Application\DeskPRO\Entity\TextSnippetCategory;
use Orb\Util\Strings;

class TextSnippetsController extends AbstractController
{
	public function requireRequestToken($action, $arguments = null)
	{
		return false;
	}

	####################################################################################################################
	# get-widget-shell
	####################################################################################################################

	public function getWidgetShellAction($typename)
	{
		$snippet_cats = $this->em->getRepository('DeskPRO:TextSnippetCategory')->getCatsForAgent($typename, $this->person);

		if ($typename != 'tickets' && $typename != 'chat') {
			throw $this->createNotFoundException();
		}

		return $this->render("AgentBundle:TextSnippets:$typename-widget-shell.html.twig", array(
			'snippet_cats' => $snippet_cats
		));
	}

	####################################################################################################################
	# reload-client
	####################################################################################################################

	public function reloadClientAction($typename)
	{
		$snippet_cats = $this->em->getRepository('DeskPRO:TextSnippetCategory')->getCatsForAgent($typename, $this->person);

		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$this->container->getObjectLangRepository()->preloadObjectCollection($lang, $snippet_cats);
		}

		$snippets_count = $this->em->getRepository('DeskPRO:TextSnippet')->countSnippetsForAgent($typename, $this->person);
		$per_page       = 250;
		$num_pages      = ceil($snippets_count / $per_page);

		$data = array(
			'typename'       => $typename,
			'snippets_count' => $snippets_count,
			'num_pages'      => $num_pages,
			'snippet_cats'   => array(),
		);

		foreach ($snippet_cats as $cat) {
			$data['snippet_cats'][] = $cat->toApiData();
		}

		return $this->createJsonResponse($data);
	}

	####################################################################################################################
	# reload-client-batch
	####################################################################################################################

	public function reloadClientBatchAction($typename, $batch = 1)
	{
		$snippets = $this->em->getRepository('DeskPRO:TextSnippet')->getAllSnippetsForAgent($typename, $this->person, $batch, 250);
		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$this->container->getObjectLangRepository()->preloadObjectCollection($lang, $snippets);
		}

		$data = array('snippets' => array());
		foreach ($snippets as $snippet) {
			$data['snippets'][] = $snippet->toApiData();
		}

		return $this->createJsonResponse($data);
	}

	####################################################################################################################
	# filter-snippets
	####################################################################################################################

	public function filterSnippetsAction($typename)
	{
		$category_id   = $this->in->getUint('category_id') ?: null;
		$filter_string = $this->in->getString('filter_string');
		$language_id   = $this->in->getUint('language_id');

		$lang_repos = $this->container->getObjectLangRepository();

		$snippets = $this->em->getRepository('DeskPRO:TextSnippet')->getAllSnippetsForAgent($typename, $this->person, 1, 500, $category_id);
		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$lang_repos->preloadObjectCollection($lang, $snippets);
		}

		if ($filter_string || $language_id) {
			$snippets_all = $snippets;
			$snippets = array();

			$filter_string = Strings::utf8_strtolower($filter_string);


			foreach ($snippets_all as $snippet) {
				$match_lang   = false;
				$match_filter = false;

				if ($language_id) {
					foreach ($this->container->getLanguageData()->getAll() as $lang) {
						if ($lang->getId() == $language_id) {
							if ($snippet->getObjectTranslatable()->getObjectProp('title', $lang)) {
								$match_lang = true;
							}
							break;
						}
					}
				} else {
					$match_lang = true;
				}

				if ($filter_string) {
					foreach ($this->container->getLanguageData()->getAll() as $lang) {
						$test = $snippet->getObjectTranslatable()->getObjectProp('title', $lang);
						$test = Strings::utf8_strtolower($test);
						if (strpos($test, $filter_string) !== false) {
							$match_filter = true;
							break;
						}
					}

					if (!$match_filter) {
						foreach ($this->container->getLanguageData()->getAll() as $lang) {
							$test = $snippet->getObjectTranslatable()->getObjectProp('snippet', $lang);
							$test = Strings::utf8_strtolower($test);
							if (strpos($test, $filter_string) !== false) {
								$match_filter = true;
								break;
							}
						}
					}
				} else {
					$match_filter = true;
				}

				if ($match_lang && $match_filter) {
					$snippets[] = $snippet;
				}
			}
		}

		$data = array('snippets' => array());
		foreach ($snippets as $snippet) {
			$data['snippets'][] = $snippet->toApiData();
		}

		return $this->createJsonResponse($data);
	}

	####################################################################################################################
	# get-snippet
	####################################################################################################################

	public function getSnippetAction($typename, $id)
	{
		$snippet = $this->em->find('DeskPRO:TextSnippet', $id);
		if (!$snippet) {
			throw $this->createNotFoundException();
		}

		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$this->container->getObjectLangRepository()->preloadObject($lang, $snippet);
		}

		$data = array('snippet' => $snippet->toApiData());
		return $this->createJsonResponse($data);
	}

	####################################################################################################################
	# save-snippet
	####################################################################################################################

	public function saveSnippetAction($typename, $id)
	{
		if ($id) {
			$snippet = $this->em->find('DeskPRO:TextSnippet', $id);
			if (!$snippet) {
				throw $this->createNotFoundException();
			}
		} else {
			$snippet = new TextSnippet();
		}

		if ($category = $this->em->find('DeskPRO:TextSnippetCategory', $this->in->getUint('category_id'))) {
			$snippet->category = $category;
		}

		$snippet->setShortcutCode($this->in->getString('shortcut_code'));

		if ($snippet->shortcut_code) {
			// Prevent dupers
			$this->db->executeUpdate("
				UPDATE text_snippets
				LEFT JOIN text_snippet_categories ON (text_snippet_categories.id = text_snippets.category_id)
				SET text_snippets.shortcut_code = CONCAT(text_snippets.shortcut_code, '_', text_snippets.id)
				WHERE text_snippets.shortcut_code = ? AND text_snippet_categories.typename = ? AND text_snippets.id != ?
			", array($snippet->shortcut_code, $typename, $snippet->id));
		}

		$this->em->persist($snippet);
		$this->em->flush();

		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$this->container->getObjectLangRepository()->preloadObject($lang, $snippet);
		}

		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$lang_id = $lang->getId();

			$title   = $this->in->getString("title.$lang_id");
			$snippet_val = $this->in->getString("snippet.$lang_id");

			$rec = $this->container->getObjectLangRepository()->setRec($lang, $snippet, 'title', $title);
			$this->em->persist($rec);

			$rec = $this->container->getObjectLangRepository()->setRec($lang, $snippet, 'snippet', $snippet_val);
			$this->em->persist($rec);
		}

		$this->em->flush();

		return $this->createJsonResponse(array('success' => true, 'snippet' => $snippet->toApiData()));
	}

	####################################################################################################################
	# delete-snippet
	####################################################################################################################

	public function deleteSnippetAction($typename, $id)
	{
		$snippet = $this->em->find('DeskPRO:TextSnippet', $id);
		if (!$snippet) {
			throw $this->createNotFoundException();
		}

		$this->em->remove($snippet);
		$this->em->flush();

		return $this->createJsonResponse(array('success' => true, 'snippet_id' => $id));
	}

	####################################################################################################################
	# save-category
	####################################################################################################################

	public function saveCategoryAction($typename, $id)
	{
		if ($id) {
			$cat = $this->em->find('DeskPRO:TextSnippetCategory', $id);
			if (!$cat) {
				throw $this->createNotFoundException();
			}
		} else {
			$cat = new TextSnippetCategory();
			$cat->typename = $typename;
			$cat->person = $this->person;
		}

		$cat->is_global = ($this->in->getString('perm_type') == 'global');

		$this->em->persist($cat);
		$this->em->flush();

		$global_title = $this->in->getString('title');

		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$lang_id = $lang->getId();

			$title = $this->in->getString("title.$lang_id");
			if (!$title) {
				$title = $global_title;
			}

			$rec = $this->container->getObjectLangRepository()->setRec($lang, $cat, 'title', $title);
			$this->em->persist($rec);
		}

		$this->em->flush();

		return $this->createJsonResponse(array('success' => true, 'category' => $cat->toApiData()));
	}

	####################################################################################################################
	# delete-category
	####################################################################################################################

	public function deleteCategoryAction($typename, $id)
	{
		$cat = $this->em->find('DeskPRO:TextSnippetCategory', $id);
		if (!$cat) {
			throw $this->createNotFoundException();
		}

		$has_snippets = $this->db->fetchColumn("
			SELECT COUNT(*)
			FROM text_snippets
			WHERE category_id = ?
		", array($cat->getId()));

		if ($has_snippets) {
			return $this->createJsonResponse(array(
				'error' => true,
				'error_code' => 'not_empty'
			));
		}

		$this->em->remove($cat);
		$this->em->flush();

		return $this->createJsonResponse(array(
			'success' => true,
			'category_id' => $id
		));
	}
}