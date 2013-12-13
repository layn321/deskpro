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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\News;
use Application\DeskPRO\Entity\NewsComment;
use Application\DeskPRO\Searcher\NewsSearch;
use Application\DeskPRO\UI\RuleBuilder;

use Application\AgentBundle\Controller\Helper\NewsResults;
use Application\DeskPRO\ContentRevision\Util as ContentRevisionUtil;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Publish\RelatedContentUpdate;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;
use Orb\Util\Numbers;

use FineDiff;

/**
 * Handles listing and editing of news
 */
class NewsController extends AbstractController
{
	############################################################################
	# view
	############################################################################

	public function viewAction($news_id)
	{
		$news = $this->em->find('DeskPRO:News', $news_id);

		if (!$news) {
			throw $this->createNotFoundException();
		}

		$news_comments = $this->em->getRepository('DeskPRO:NewsComment')->getComments($news);

		$related_finder = new RelatedContentFinder($this->person, $news);
		$related_content = $related_finder->getRelatedEntities();

		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.editarticle', $this->person->id);

		$sticky_search_words = $this->em->getRepository('DeskPRO:SearchStickyResult')->getWordsForObject($news);
		$rated_searches = $this->em->getRepository('DeskPRO:SearchLog')->getRatedSearchesFor('news', $news['id'], 'counted');

		$news_categories = $this->em->getRepository('DeskPRO:NewsCategory')->getInHierarchy();

		$perms = array(
			'can_edit' => $this->person->PermissionsManager->PublishChecker->canEdit($news),
			'can_delete' => $this->person->PermissionsManager->PublishChecker->canDelete($news),
		);

		return $this->render('AgentBundle:News:view.html.twig', array(
			'news'                 => $news,
			'news_comments'        => $news_comments,
			'news_categories'      => $news_categories,
			'related_content'      => $related_content,
			'state'                => $state,
			'sticky_search_words'  => $sticky_search_words,
			'rated_searches'       => $rated_searches,
			'perms'                => $perms,
		));
	}

	public function viewRevisionsAction($news_id)
	{
		$news = $this->em->find('DeskPRO:News', $news_id);

		return $this->render('AgentBundle:News:view-revisions-tab.html.twig', array(
			'news' => $news,
		));
	}

	public function ajaxSaveLabelsAction($news_id)
	{
		$news = $this->em->find('DeskPRO:News', $news_id);

		if (!$news) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}
		if (!$this->person->PermissionsManager->PublishChecker->canEdit($news)) {
			return $this->createJsonResponse(array('success' => 0));
		}

		$labels = $this->in->getCleanValueArray('labels', 'string', 'discard');

		$news->getLabelManager()->setLabelsArray($labels);

		$this->em->persist($news);
		$this->em->flush();

		return $this->createJsonResponse(array('success' => 1));
	}

	public function ajaxSaveCommentAction($news_id)
	{
		$news = $this->em->find('DeskPRO:News', $news_id);

		$comment = new NewsComment();
		$comment->news = $news;
		$comment->person = $this->person;
		$comment['content'] = $this->in->getString('content');
		$comment['status'] = 'visible';
		$comment['date_created']  = new \DateTime();

		if ($this->person->hasPerm('agent_publish.validate')) {
			$comment->is_reviewed = true;
		}

		$this->em->persist($comment);
		$this->em->flush();

		return $this->render('AgentBundle:News:view-comment.html.twig', array(
			'comment' => $comment
		));
	}

	public function ajaxSaveAction($news_id)
	{
		$news = $this->em->find('DeskPRO:News', $news_id);
		$rev = null;

		if (!$news) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$action = $this->in->getString('action');

		if ($action == 'delete') {
			if (!$this->person->PermissionsManager->PublishChecker->canDelete($news)) {
				return $this->createJsonResponse(array('success' => false));
			}
		} else {
			if (!$this->person->PermissionsManager->PublishChecker->canEdit($news)) {
				return $this->createJsonResponse(array('success' => false));
			}
		}

		$data = array('success' => 1);

		$this->em->beginTransaction();

		switch ($action) {
			case 'status':
				$news['status_code'] = $this->in->getString('status');
				if ($news['status_code'] == 'published' && !$this->person->hasPerm('agent_publish.validate')) {
					$news['status_code'] = 'hidden.validating';
				}
				break;

			case 'title':
				$news['title'] = $this->in->getString('title');

				$rev = ContentRevisionUtil::findOrCreate($news, 'title', $this->person);
				$rev['title'] = $news['title'];

				break;

			case 'add-related':
				$updater = new RelatedContentUpdate($news);
				$updater->addRelated(
					$this->in->getString('content_type'),
					$this->in->getString('content_id')
				);
				break;

			case 'remove-related':
				$updater = new RelatedContentUpdate($news);
				$updater->removeRelated(
					$this->in->getString('content_type'),
					$this->in->getString('content_id')
				);
				break;

			case 'content':

				$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.editcontent', $this->person->id);

				$news['content'] = $this->in->getCleanValue('content', 'string', null, array('noclean' => true));
				$data['content_html'] = $this->renderView('AgentBundle:News:view-content-tab.html.twig', array(
					'news' => $news
				));

				$rev = ContentRevisionUtil::findOrCreate($news, 'content', $this->person);
				$rev['content'] = $news['content'];

				break;

			case 'category':
				$cat = $this->em->find('DeskPRO:NewsCategory', $this->in->getUint('category_id'));
				$news['category'] = $cat;
				$data['category_id'] = $cat['id'];
				break;

			case 'delete':
				$news->status_code = 'hidden.deleted';
				break;
		}

		$this->em->persist($news);

		if ($rev) {
			$this->em->persist($rev);
		}

		$this->em->flush();
		$this->em->commit();

		if ($rev) {
			$data['revision_id'] = $rev['id'];
		} else {
			$data['revision_id'] = null;
		}

		return $this->createJsonResponse($data);
	}

	############################################################################
	# Compare revisions
	############################################################################

	public function compareRevisionsAction($rev_old_id, $rev_new_id)
	{
		$diff_info = ContentRevisionUtil::compareRevisions('DeskPRO:NewsRevision', $rev_old_id, $rev_new_id);

		return $this->render('AgentBundle:News:compare-revs.html.twig', array(
			'rendered_content_diff' => $diff_info['rendered_content_diff'],
			'rendered_title_diff'   => $diff_info['rendered_title_diff'],
		));
	}

	############################################################################
	# list
	############################################################################

	/**
	 * View a list of feedback
	 */
	public function listAction($category_id = 0)
	{
		$category = null;
		if ($category_id) {
			$category = $this->em->find('DeskPRO:NewsCategory', $category_id);
		}

		$show_all = false;
		if (!$category) {
			$show_all = $this->in->getBool('all');
		}

		$result_helper = NewsResults::newFromRequest($this, array(
			'category' => $category,
			'show_all' => $show_all
		));

		$page = $this->in->getUint('p');
		if (!$page) $page = 1;

		$results = $result_helper->getNewsForPage($page);
		$result_cache = $result_helper->getResultCache();

		$total_results = count($result_helper->getNewsIds());
		$num_pages = ceil($total_results / 50);
		$showing_to = min(($page) * 50, $total_results);

		$display_fields = $this->person->getPref('agent.ui.news-filter-display-fields.0');
		if (!$display_fields) {
			$display_fields = array('author', 'date_created');
		}

		$tpl = 'AgentBundle:News:filter.html.twig';
		if ($this->request->isPartialRequest()) {
			$tpl = 'AgentBundle:News:filter-page.html.twig';
		}

		$comment_counts = array();
		if ($results) {
			$comment_counts = $this->db->fetchAllKeyValue("
				SELECT article_id, COUNT(*)
				FROM article_comments
				WHERE article_id IN (" . implode(',', array_keys($results)) . ")
				GROUP BY article_id
			");
		}

		$cat_usergroups = array();
		$cat_structure_data = array();
		if ($category) {
			$cat_usergroups = $this->db->fetchAllCol("
				SELECT usergroup_id
				FROM news_category2usergroup
				WHERE category_id = ?
			", array($category->getId()));

			$cat_structure_data = $this->em->getRepository('DeskPRO:NewsCategory')->getInHierarchy();;
			$cat_structure_data = Arrays::removeButKey($cat_structure_data, array('id' , 'title', 'children'), true, true);
			$cat_structure_data = Arrays::multiRenameKey($cat_structure_data, 'title', 'label');
			$cat_structure_data = Arrays::assocToNumericArary($cat_structure_data, 'children');
		}

		return $this->render($tpl, array(
			'results'            => $results,
			'result_id'          => $result_cache['id'],
			'comment_counts'     => $comment_counts,
			'display_fields'     => $display_fields,
			'category'           => $category,
			'cat_usergroups'     => $cat_usergroups,
			'cat_structure_data' => $cat_structure_data,
			'total_results'      => $total_results,
			'num_pages'          => $num_pages,
			'cur_page'           => $page,
			'showing_to'         => $showing_to,
		));
	}

	############################################################################
	# New news
	############################################################################

	public function newNewsAction()
	{
		$news_categories = $this->em->getRepository('DeskPRO:NewsCategory')->getFlatHierarchy();

		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.newnews', $this->person->id);

		return $this->render('AgentBundle:News:newnews.html.twig', array(
			'news_categories' => $news_categories,
			'state' => $state
		));
	}

	public function newNewsSaveAction()
	{
		$newnews = new \Application\AgentBundle\Form\Model\NewNews($this->person);

		$formType = new \Application\AgentBundle\Form\Type\NewNews();
		$form = $this->get('form.factory')->create($formType, $newnews);

		$this->db->executeUpdate("DELETE FROM people_prefs WHERE name = 'agent.ui.state.newnews' AND person_id = ?", array($this->person->id));

		if ($this->get('request')->getMethod() == 'POST') {
			$form->bindRequest($this->get('request'));
			$form->isValid();

			$validator = new \Application\AgentBundle\Validator\NewNewsValidator();
			if (!$validator->isValid($newnews)) {
				return $this->createJsonResponse(array(
					'error' => true,
					'error_codes' => $validator->getErrorGroups()
				));
			}
			$newnews->save();

			$news = $newnews->getNews();

			$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.newnews', $this->person->id);

			return $this->createJsonResponse(array(
				'success' => true,
				'news_id' => $news['id']
			));
		} else {
			return $this->createJsonResponse(array(
				'success' => false,
			));
		}
	}
}
