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

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\Entity\TextSnippet;
use Application\DeskPRO\Entity\TextSnippetCategory;
use Orb\Util\Strings;

class TextSnippetsController extends AbstractController
{
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

		return $this->createApiResponse($data);
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
		return $this->createApiResponse($data);
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

		return $this->createApiCreateResponse(
			array('snippet_id' => $snippet->id),
			$this->generateUrl('api_ticketsnippets_get', array('id' => $snippet->id), true)
		);
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

		return $this->createApiResponse(array('success' => true, 'snippet_id' => $id));
	}

	####################################################################################################################
	# list-categories
	####################################################################################################################

	public function listCategoriesAction($typename)
	{
		$snippet_cats = $this->em->getRepository('DeskPRO:TextSnippetCategory')->getCatsForAgent($typename, $this->person);

		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$this->container->getObjectLangRepository()->preloadObjectCollection($lang, $snippet_cats);
		}

		$snippets_count = $this->em->getRepository('DeskPRO:TextSnippet')->countSnippetsForAgent($typename, $this->person);

		$data = array(
			'snippet_cats'   => array(),
		);

		foreach ($snippet_cats as $cat) {
			$data['snippet_cats'][] = $cat->toApiData();
		}

		return $this->createApiResponse($data);
	}

	####################################################################################################################
	# get-category
	####################################################################################################################

	public function getCategoryAction($typename, $id)
	{
		$cat = $this->em->find('DeskPRO:TextSnippetCategory', $id);

		if (!$cat || $cat->typename != $typename) {
			return $this->createNotFoundException();
		}

		$data = array('snippet_cat' => $cat->toApiData());

		return $this->createApiResponse($data);
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

		return $this->createApiCreateResponse(
			array('category_id' => $cat->id),
			$this->generateUrl('api_textsnippets_cats_get', array('id' => $cat->id), true)
		);
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
			return $this->createApiErrorResponse(409, 'The category is not empty. Delete existing snippets and try again.', 409);
		}

		$this->em->remove($cat);
		$this->em->flush();

		return $this->createApiResponse(array(
			'success' => true,
			'category_id' => $id
		));
	}
}