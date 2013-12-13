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
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\FeedbackComment;

use Application\DeskPRO\Searcher\FeedbackSearch;
use Application\AgentBundle\Controller\Helper\FeedbackResults;
use Application\DeskPRO\UI\RuleBuilder;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Publish\RelatedContentUpdate;

use Application\DeskPRO\ContentRevision\Util as ContentRevisionUtil;

use Application\DeskPRO\Publish\Feedback\GroupingCounter;

use Application\DeskPRO\Feedback\FeedbackMerge;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

use FineDiff;

/**
 * Handles ticket searches
 */
class FeedbackController extends AbstractController
{
	############################################################################
	# get-section-data
	############################################################################

	public function getSectionDataAction()
	{
		$data = array();

		$counts = array();
		$counts['feedback_awaiting_validation'] = $this->em->getRepository('DeskPRO:Feedback')->countAwaitingValidation();
		$counts['comments_awaiting_validation'] = $this->em->getRepository('DeskPRO:FeedbackComment')->countAwaitingValidation();

		$status_counts = array();
		$status_counts['new']    = $this->em->getRepository('DeskPRO:Feedback')->countNew();
		$status_counts['active'] = $this->em->getRepository('DeskPRO:Feedback')->countActiveGrouped();
		$status_counts['closed'] = $this->em->getRepository('DeskPRO:Feedback')->countClosedGrouped();
		$status_counts['hidden'] = $this->em->getRepository('DeskPRO:Feedback')->countHiddenGrouped();

		$category_counts = $this->em->getRepository('DeskPRO:Feedback')->countAllCategoriesGrouped();

		$feedback_cats      = $this->em->getRepository('DeskPRO:FeedbackCategory')->getFlatHierarchy();
		$active_status_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getActiveCategories();
		$closed_status_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getClosedCategories();

		$label_lister = new \Application\DeskPRO\Labels\LabelLister('feedback');
		$feedback_tag_index = $label_lister->getIndexList();

		$data['section_html'] = $this->renderView('AgentBundle:Feedback:window-section.html.twig', array(
			'counts'             => $counts,
			'status_counts'      => $status_counts,
			'category_counts'    => $category_counts,
			'feedback_cats'      => $feedback_cats,
			'active_status_cats' => $active_status_cats,
			'closed_status_cats' => $closed_status_cats,
			'feedback_tag_index' => $feedback_tag_index
		));

		return $this->createJsonResponse($data);
	}

	############################################################################
	# view
	############################################################################

	public function viewAction($feedback_id)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		if (!$feedback) {
			throw $this->createNotFoundException();
		}

		#------------------------------
		# Custom fields
		#------------------------------

		$field_manager = $this->container->getSystemService('feedback_fields_manager');
		$custom_fields = $field_manager->getDisplayArrayForObject($feedback);

		#------------------------------
		# Article props
		#------------------------------

		$feedback_comments_raw = $feedback->comments;
		$feedback_comments = array();
		foreach ($feedback_comments_raw as $c) {
			if ($c->status != 'temp') {
				$feedback_comments[] = $c;
			}
		}

		$feedback_revisions = $feedback->getRevisions();
		$sticky_search_words = $this->em->getRepository('DeskPRO:SearchStickyResult')->getWordsForObject($feedback);

		$related_finder = new RelatedContentFinder($this->person, $feedback);
		$related_content = $related_finder->getRelatedEntities();

		$rated_searches = $this->em->getRepository('DeskPRO:SearchLog')->getRatedSearchesFor('feedback', $feedback['id'], 'counted');

		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.editfeedback', $this->person->id);

		$content_rating = new \Application\UserBundle\Controller\Helper\ContentRating($feedback, $this->person, $this->session->getVisitor());
		$my_vote = $content_rating->getRating();

		$feedback_categories = $this->em->getRepository('DeskPRO:FeedbackCategory')->getInHierarchy();
		$active_status_cats  = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getActiveCategories();
		$closed_status_cats  = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getClosedCategories();

		$category = $feedback->category;
		$category_path = $category->getTreeParents();

		$perms = array(
			'can_edit' => $this->person->PermissionsManager->PublishChecker->canEdit($feedback),
			'can_delete' => $this->person->PermissionsManager->PublishChecker->canDelete($feedback),
		);

		return $this->render('AgentBundle:Feedback:view.html.twig', array(
			'feedback'           => $feedback,
			'feedback_comments'  => $feedback_comments,
			'feedback_revisions' => $feedback_revisions,
			'state'          => $state,

			'category' => $category,
			'category_path' => $category_path,

			'custom_fields'  => $custom_fields,

			'my_vote' => $my_vote,

			'rated_searches'      => $rated_searches,
			'related_content'     => $related_content,
			'sticky_search_words' => $sticky_search_words,

			'feedback_categories'  => $feedback_categories,
			'active_status_cats'   => $active_status_cats,
			'closed_status_cats'   => $closed_status_cats,
			'perms'                => $perms
		));
	}

	public function whoVotedAction($feedback_id)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		$feedback_votes = $feedback->votes->toArray();

		return $this->render('AgentBundle:Feedback:view-who-voted.html.twig', array(
			'feedback' => $feedback,
			'feedback_votes' => $feedback_votes,
		));
	}

	public function ajaxSaveEditablesAction($feedback_id)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		if (!$feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if (!$this->person->PermissionsManager->PublishChecker->canEdit($feedback)) {
			return $this->createJsonResponse(array('success' => false));
		}

		$ret = '';

		switch ($this->in->getString('action')) {
			case 'title':
				$value = $this->in->getString('title');
				$feedback['title'] = $value;
				$ret = array('html' => htmlspecialchars($feedback['title']));
				break;
		}

		$this->em->transactional(function ($em) use ($feedback) {
			$em->persist($feedback);
			$em->flush();
		});

		return $this->createJsonResponse(array(
			'success' => true,
			'feedback_id' => $feedback['id'],
			'html' => $ret
		));
	}

	public function ajaxUpdateCategoryAction($feedback_id, $category_id)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		if (!$feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}



		$cat  = $this->em->find('DeskPRO:FeedbackCategory', $category_id);

		$feedback->category = $cat;

		$this->em->transactional(function ($em) use ($feedback) {
			$em->persist($feedback);
			$em->flush();
		});

		return $this->createJsonResponse(array(
			'success' => true,
			'feedback_id' => $feedback['id'],
		));
	}

	public function ajaxUpdateStatusAction($feedback_id, $status_code)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		if (!$feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}
		if (!$this->person->PermissionsManager->PublishChecker->canEdit($feedback)) {
			return $this->createJsonResponse(array('success' => false));
		}

		$feedback['status_code'] = $status_code;

		$this->em->transactional(function ($em) use ($feedback) {
			$em->persist($feedback);
			$em->flush();
		});

		return $this->createJsonResponse(array(
			'success' => true,
			'feedback_id' => $feedback['id'],
		));
	}

	public function ajaxSaveCustomFieldsAction($feedback_id)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		if (!$feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}
		if (!$this->person->PermissionsManager->PublishChecker->canEdit($feedback)) {
			return $this->createJsonResponse(array('success' => false));
		}

		$this->em->beginTransaction();

		try {
			$field_manager = $this->container->getSystemService('feedback_fields_manager');
			$post_custom_fields = $this->request->request->get('custom_fields', array());
			if (!empty($post_custom_fields)) {
				$field_manager->saveFormToObject($post_custom_fields, $feedback);
			}

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		$custom_fields = $field_manager->getDisplayArrayForObject($feedback);

		return $this->render('AgentBundle:Feedback:view-customfields-rendered-rows.html.twig', array(
			'feedback' => $feedback,
			'custom_fields' => $custom_fields,
		));
	}

	public function ajaxSaveLabelsAction($feedback_id)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		if (!$feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}
		if (!$this->person->PermissionsManager->PublishChecker->canEdit($feedback)) {
			return $this->createJsonResponse(array('success' => false));
		}

		$labels = $this->in->getCleanValueArray('labels', 'string', 'discard');

		$feedback->getLabelManager()->setLabelsArray($labels);

		$this->em->persist($feedback);
		$this->em->flush();

		return $this->createJsonResponse(array('success' => 1));
	}

	public function ajaxSaveCommentAction($feedback_id)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		$comment = new FeedbackComment();
		$comment->feedback = $feedback;
		$comment->is_reviewed = true;
		$comment['content'] = $this->in->getString('content');

		if ($this->in->getBool('agent_only')) {
			$comment['status'] = 'agent';
		} else {
			$comment['status'] = 'visible';
		}

		$commenting = new \Application\DeskPRO\Feedback\FeedbackCommenting($this->container, $this->person);
		$commenting->saveComment($feedback, $comment);
		$commenting->newCommentNotify($comment);

		return $this->render('AgentBundle:Feedback:view-comment.html.twig', array(
			'comment' => $comment
		));
	}

	public function ajaxSaveAction($feedback_id)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		if (!$feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$rev = null;

		$action = $this->in->getString('action');

		if ($action == 'delete') {
			if (!$this->person->PermissionsManager->PublishChecker->canDelete($feedback)) {
				return $this->createJsonResponse(array('success' => false));
			}
		} else {
			if (!$this->person->PermissionsManager->PublishChecker->canEdit($feedback)) {
				return $this->createJsonResponse(array('success' => false));
			}
		}

		$data = array('success' => 1);

		$this->em->beginTransaction();

		switch ($action) {

			case 'status':
			case 'delete':

				if ($action == 'delete') {
					$feedback['status_code'] = 'hidden.deleted';
				} else {
					$feedback['status_code'] = $this->in->getString('status');
				}
				break;

			case 'title':
				$feedback['title'] = $this->in->getString('title');

				$rev = ContentRevisionUtil::findOrCreate($feedback, 'title', $this->person);
				$rev['title'] = $feedback['title'];

				break;

			case 'add-related':
				$updater = new RelatedContentUpdate($feedback);
				$updater->addRelated(
					$this->in->getString('content_type'),
					$this->in->getString('content_id')
				);
				break;

			case 'remove-related':
				$updater = new RelatedContentUpdate($feedback);
				$updater->removeRelated(
					$this->in->getString('content_type'),
					$this->in->getString('content_id')
				);
				break;

			case 'remove-blob':
				foreach ($feedback->attachments as $k => $attach) {
					if ($attach->blob['id'] == $this->in->getUint('blob_id')) {
						$feedback->attachments->remove($k);
						$this->em->remove($attach);
						break;
					}
				}

				break;

			case 'content':

				$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.editfeedback', $this->person->id);

				$feedback['content'] = $this->in->getCleanValue('content', 'string', null, array('noclean' => true));

				$data['content_html'] = $this->renderView('AgentBundle:Feedback:view-content-tab.html.twig', array(
					'feedback' => $feedback
				));

				$rev = ContentRevisionUtil::findOrCreate($feedback, array('content'), $this->person);
				$rev['content'] = $feedback['content'];

				break;

			case 'category':
				$cat = $this->em->find('DeskPRO:FeedbackCategory', $this->in->getUint('category_id'));
				$feedback['category'] = $cat;
				$data['category_id'] = $cat['id'];
				break;

			case 'vote':

				$content_rating = new \Application\UserBundle\Controller\Helper\ContentRating($feedback, $this->person, $this->session->getVisitor());
				$content_rating->setRequest($this->request);
				$content_rating->setRating(1);

				break;

			case 'clear-vote':

				$content_rating = new \Application\UserBundle\Controller\Helper\ContentRating($feedback, $this->person, $this->session->getVisitor());
				$vote = $content_rating->getRating();

				if ($vote) {
					$feedback->removeRating($vote);

					$this->em->remove($vote);
				}

				break;
		}

		$this->em->persist($feedback);

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
	# merge
	############################################################################

	public function mergeOverlayAction($feedback_id, $other_feedback_id = 0)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);

		if ($other_feedback_id && $other_feedback_id != $feedback_id) {
			$other_feedback = $this->em->find('DeskPRO:Feedback', $other_feedback_id);
		} else {
			$other_feedback = false;
		}

		return $this->render('AgentBundle:Feedback:merge-overlay.html.twig', array(
			'feedback'          => $feedback,
			'other_feedback'    => $other_feedback,
		));
	}

	/**
	 * Merge a ticket interface
	 */
	public function mergeAction($feedback_id, $other_feedback_id)
	{
		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);
		$other_feedback = $this->em->find('DeskPRO:Feedback', $other_feedback_id);

		if (!$feedback || !$other_feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if (!$this->person->PermissionsManager->PublishChecker->canEdit($feedback)
			|| !$this->person->PermissionsManager->PublishChecker->canEdit($other_feedback)
			|| !$this->person->PermissionsManager->PublishChecker->canDelete($other_feedback)
		) {
			return $this->createJsonResponse(array('success' => false));
		}

		$old_feedback_id = $other_feedback['id'];

		try {
			$this->em->beginTransaction();
			$merge = new FeedbackMerge($this->person, $feedback, $other_feedback);
			$merge->merge();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		return $this->createJsonResponse(array(
			'success' => true,
			'id' => $feedback['id'],
			'old_id' => $old_feedback_id
		));
	}

	############################################################################
	# filters
	############################################################################

	/**
	 * Any general search. For example, status, category or label
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function filterListAction()
	{
		$vars = array('list_type' => 'filter');

		$result_helper = FeedbackResults::newFromRequest($this);

		$result_cache = $result_helper->getResultCache();

		return $this->renderList(
			$result_helper,
			null,
			array('list_type' => 'filter')
		);
	}


	/**
	 * A shortcut to run a filter on a category
	 *
	 * @param  $category_id
	 * @return
	 */
	public function categoryListAction($category_id)
	{
		$top_result_helper = FeedbackResults::newFromRequest($this, array(
			'specific_terms' => array(
				'category' => array('type' => 'category', 'op' => 'is', 'category' => $category_id),
				'status'   => array('type' => 'status', 'op' => 'not', 'status' => 'hidden')
			)
		));

		if ($this->in->getString('subgroup')) {
			$result_helper = FeedbackResults::newFromRequest($this, array(
				'specific_terms' => array(
					'category' => array('type' => 'category', 'op' => 'is', 'category' => $category_id),
					'status' => array('type' => 'status', 'op' => 'is', 'status' => $this->in->getString('subgroup')),
				)
			));
		} else {
			$result_helper = $top_result_helper;
		}

		$cat = $this->em->find('DeskPRO:FeedbackCategory', $category_id);

		$grouping = new GroupingCounter();
		$grouping->setGrouping('status');
		$grouping->setIds($top_result_helper->getFeedbackIds());
		$grouped = $grouping->getDisplayArray();

		if (!$cat->parent) {
			$grouped_key = $cat->getId();
			$grouped_info = array();
			$t = 0;
			if (isset($grouped['items'][$grouped_key])) {
				$grouped_info = Arrays::mergeAssoc($grouped_info, array($grouped_key => $grouped['items'][$grouped_key]));
				$t = $grouped['items'][$grouped_key]['total'];
			}

			$grouped_info[-1] = array('id' => -1, 'title' => 'TOTAL', 'total' => $t);
		} else {
			$grouped_key = $cat->getId();
			$t = 0;
			foreach ($cat->children as $c) {
				$k = $c['id'];
				if (isset($grouped['items'][$k])) {
					$grouped_info = Arrays::mergeAssoc($grouped_info, array($k => $grouped['items'][$k]));
					$t += $grouped['items'][$k]['total'];
				}
			}

			$grouped_info[-1] = array('id' => -1, 'title' => 'TOTAL', 'total' => $t);
		}

		return $this->renderList(
			$result_helper,
			null,
			array(
				'list_type' => 'category',
				'category_id' => $category_id,
				'page_title' => $cat->getFullTitle(),
				'grouped' => $grouped,
				'grouped_info' => $grouped_info,
				'grouped_key' => $grouped_key,
				'subgroup' => $this->in->getString('subgroup'),
			)
		);
	}


	/**
	 * A shortcut to run a filter on a label
	 *
	 * @param  $category_id
	 * @return
	 */
	public function labelListAction($label)
	{
		$result_helper = FeedbackResults::newFromRequest($this, array(
			'specific_terms' => array(
				array('type' => 'label', 'op' => 'is', 'label' => $label),
				array('type' => 'status', 'op' => 'not', 'status' => 'hidden'),
				array('type' => 'hidden_status', 'op' => 'not', 'hidden_status' => 'validating')
			),
		));

		return $this->renderList(
			$result_helper,
			null,
			array(
				'list_type' => 'label',
				'label' => $label,
				'page_title' => $label
			)
		);
	}


	/**
	 * A shortcut to run a filter on a status
	 *
	 * @param  $category_id
	 * @return
	 */
	public function statusListAction($status)
	{
		// $status can be either a top-level name like active, closed or hidden,
		// or an integer which will be treated as a status category (Active > Planned for example)

		if (strpos($status, '.') !== false) {
			list ($status, $v_status) = explode('.', $status);
			$top_result_helper = FeedbackResults::newFromRequest($this, array(
				'specific_terms' => array(
					'status' => array('type' => 'status', 'op' => 'is', 'status' => $status),
					'v_status' => array('type' => 'hidden_status', 'op' => 'is', 'hidden_status' => $v_status)
				)
			));
		} else {
			$top_result_helper = FeedbackResults::newFromRequest($this, array(
				'specific_terms' => array(
					'status' => array('type' => 'status', 'op' => 'is', 'status' => $status),
					'v_status' => array('type' => 'hidden_status', 'op' => 'not', 'hidden_status' => 'validating')
				)
			));
		}

		if ($this->in->getString('subgroup')) {
			$result_helper = FeedbackResults::newFromRequest($this, array(
				'specific_terms' => array(
					'status' => array('type' => 'status', 'op' => 'is', 'status' => $status),
					'category' => array('type' => 'category', 'op' => 'is', 'category' => $this->in->getString('subgroup')),
					'v_status' => array('type' => 'hidden_status', 'op' => 'not', 'hidden_status' => 'validating')
				)
			));
		} else {
			$result_helper = $top_result_helper;
		}

		$grouping = new GroupingCounter();
		$grouping->setGrouping('category_id');
		$grouping->setIds($top_result_helper->getFeedbackIds());
		$grouped = $grouping->getDisplayArray();

		return $this->renderList(
			$result_helper,
			null,
			array(
				'list_type' => 'status',
				'status' => $status,
				'grouped' => $grouped,
				'subgroup' => $this->in->getString('subgroup'),
			)
		);
	}


	/**
	 * This takes a result helper and just handles rendering it
	 *
	 * @param  $result_helper
	 * @param string $template
	 * @param array $template_vars
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function renderList($result_helper, $template = null, array $template_vars = array())
	{
		if (!$template) {
			$template = 'AgentBundle:Feedback:filter-list.html.twig';
		}

		$result_cache = $result_helper->getResultCache();

		$page = $this->in->getUint('p');
		if (!$page) $page = 1;

		$feedback = $result_helper->getFeedbackForPage($page);

		if ($this->in->getBool('is_partial')) {
			$template = str_replace('.html.twig', '-part.html.twig', $template);
		}

		// Options for the filter form
		$feedback_cats          = $this->em->getRepository('DeskPRO:FeedbackCategory')->getFlatHierarchy();
		$active_status_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getActiveCategories();
		$closed_status_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getClosedCategories();

		$display_fields = $this->person->getPref('agent.ui.feedback-filter-display-fields.0');
		if (!$display_fields) {
			$display_fields = $this->person->getPref('agent.ui.feedback-filter-display-fields.0');
		}
		if (!$display_fields) {
			$display_fields = array('date_created', 'category');
		}

		$user_cat_field = $this->container->getSystemService('FeedbackFieldsManager')->getUserCategoryField();

		$feedback_collection = new \Application\DeskPRO\Feedback\FeedbackCollection(
			$feedback,
			$this->container->getEm(),
			$this->container->getSystemService('FeedbackFieldsManager')
		);

		$display = $feedback_collection->getDisplayArray();

		return $this->render($template, array_merge(array(
			'display'      => $display,
			'cache'        => $result_cache,
			'cache_id'     => $result_cache['id'],
			'result_ids'   => $result_cache['results'],
			'feedback'        => $feedback,
			'num_results'  => $result_cache['num_results'],
			'per_page'     => 50,
			'criteria'     => $result_cache['criteria'],
			'user_cat_field' => $user_cat_field,
			'cur_page' => $page,

			'feedback_cats'          => $feedback_cats,
			'active_status_cats' => $active_status_cats,
			'closed_status_cats' => $closed_status_cats,

			'display_fields' => $display_fields,
		), $template_vars));
	}

	public function massActionsAction($action)
	{
		$this->em->beginTransaction();

		$feedback = $this->em->getRepository('DeskPRO:Feedback')->getByIds($this->in->getCleanValueArray('ids', 'uint', 'discard'));

		foreach ($feedback as $feedback) {
			switch ($action) {
				case 'set-status':
					$feedback->setStatusCode($this->in->getString('status'));
					break;

				case 'set-category':
					$cat = $this->em->find('DeskPRO:FeedbackCategory', $this->in->getUint('category_id'));
					if ($cat) {
						$feedback->category = $cat;
					}
					break;
			}
		}

		$this->em->flush();
		$this->em->commit();

		return $this->createJsonResponse(array(
			'success' => 1
		));
	}

	############################################################################
	# Compare revisions
	############################################################################

	public function compareRevisionsAction($rev_old_id, $rev_new_id)
	{
		$diff_info = ContentRevisionUtil::compareRevisions('DeskPRO:FeedbackRevision', $rev_old_id, $rev_new_id);

		return $this->render('AgentBundle:Feedback:compare-revs.html.twig', array(
			'rendered_content_diff' => $diff_info['rendered_content_diff'],
			'rendered_title_diff'   => $diff_info['rendered_title_diff'],
		));
	}

	############################################################################
	# newfeedback
	############################################################################

	public function newFeedbackAction()
	{
		$feedback_categories    = $this->em->getRepository('DeskPRO:FeedbackCategory')->getFlatHierarchy();
		$active_status_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getActiveCategories();
		$closed_status_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getClosedCategories();

		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.newfeedback', $this->person->id);

		return $this->render('AgentBundle:Feedback:newfeedback.html.twig', array(
			'feedback_categories'    => $feedback_categories,
			'active_status_cats' => $active_status_cats,
			'closed_status_cats' => $closed_status_cats,
			'state'              => $state
		));
	}

	public function newFeedbackSaveAction()
	{
		$newfeedback = new \Application\AgentBundle\Form\Model\NewFeedback($this->person);

		$formType = new \Application\AgentBundle\Form\Type\NewFeedback();
		$form = $this->get('form.factory')->create($formType, $newfeedback);

		if ($this->get('request')->getMethod() == 'POST') {
			$form->bindRequest($this->get('request'));
			$form->isValid();

			$validator = new \Application\AgentBundle\Validator\NewFeedbackValidator();
			if (!$validator->isValid($newfeedback)) {
				return $this->createJsonResponse(array(
					'error' => true,
					'error_codes' => $validator->getErrorGroups()
				));
			}
			$newfeedback->save();

			$feedback = $newfeedback->getFeedback();

			$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.newfeedback', $this->person->id);

			return $this->createJsonResponse(array(
				'success' => true,
				'feedback_id' => $feedback['id']
			));
		} else {
			return $this->createJsonResponse(array(
				'success' => false,
			));
		}
	}
}
