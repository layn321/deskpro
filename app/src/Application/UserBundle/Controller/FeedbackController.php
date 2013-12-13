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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\EmailGateway\PersonFromEmailProcessor;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;
use Orb\Util\Numbers;

use Application\UserBundle\Form\NewFeedbackType;
use Application\DeskPRO\Comments\NewCommentFormType;

use Application\UserBundle\Controller\Helper\Comments;
use Application\UserBundle\Controller\Helper\FacebookLike;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;

use Application\DeskPRO\Feedback\FeedbackCollection;

class FeedbackController extends AbstractController
{
	public function sectionPermissionCheck()
	{
		return $this->person->hasPerm('feedback.use');
	}

	/**
	 * Main index shows initial category listing
	 */
	public function filterAction($status = 'open', $slug = 'all-categories', $order_by = 'popular', $just_form = false)
	{
		if ($just_form && !$this->person->hasPerm('feedback.submit')) {
			return $this->redirectRoute('user_feedback_home');
		}

		/** @var $structure \Application\DeskPRO\Publish\Structure */
		$structure = $this->container->getSystemService('publish_structure');

		$page = $this->in->getUint('p');
		$page = max(1, $page);

		$per_page = 20;
		if ($this->request->isPartialRequest() == 'portal') {
			$per_page = 10;
		}

		$parent_status = $status;
		$sub_status_id = 0;

		if (!$status) {
			$status = 'open';
		}

		if (!$slug) {
			$slug = 'all-categories';
		}

		if ($order_by != 'popular' && $order_by != 'newest' && $order_by != 'most-voted' && $order_by != 'i-voted') {
			$order_by = 'popular';
		}

		$search_options = array(
			'order_by' => $order_by,
		);

		if ($slug && $slug != 'all-categories') {
			$category_id = $this->container->getRouter()->getIdFromSlug($slug);
			$category = null;

			if ($category_id && $structure->hasFeedbackCategory($category_id)) {
				$category = $structure->getFeedbackCategory($category_id);
			}

			if (!$category) {
				return $this->renderStandardError('@user.error.not-found-title', '@user.error.not-found', 404);
			}

			$cat_id = $category['id'];

			// Auto-correct URL
			if ($slug != $category->getUrlSlug()) {
				return $this->redirectRoute('user_feedback', array('slug' => $category->getUrlSlug()), 301);
			}

			$category_path = $category->getTreeParents();

		} else {
			$category = null;
			$cat_id = 0;
			$category_path = array();
		}

		$captcha = null;
		$captcha_html = '';
		if ($this->container->getSetting('user.publish_captcha') && ($this->container->getSetting('user.always_show_captcha') || !$this->person->getId())) {
			$captcha = $this->container->getSystemObject('form_captcha', array('type' => 'user_newfeedback'));
			$captcha_html = $captcha->getHtml();
		}

		$feedback_cats  = $structure->getFeedbackRootCategories();
		$active_status_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getActiveCategories();
		$closed_status_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getClosedCategories();
		$status_subcats = Arrays::mergeAssoc($active_status_cats, $closed_status_cats);

		$searcher = new \Application\DeskPRO\Searcher\FeedbackSearch();
		$searcher->setPersonContext($this->person);
		$searcher->setVisitor($this->session->getVisitor());
		if ($status == 'any-status') {
			$searcher->addTerm('status', 'not', 'hidden');
		} elseif ($status == 'open') {
			$searcher->addTerm('status', 'is', array('new', 'active'));
		} else {
			$set_status = $status;
			if ($set_status == 'gathering-feedback') {
				$set_status = 'new';
			}
			$searcher->addTerm('status', 'is', $set_status);
		}

		$status_cat = null;
		if (strpos($status, '.') !== false) {
			list($parent_status, $sub_status_id) = explode('.', $status, 2);
			$status_cat = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->find($sub_status_id);
		}

		$searcher->setOrderByCode($search_options['order_by']);

		if ($category) {
			$searcher->addTerm('category', 'is', $category['id']);
		}

		$total = $searcher->getCount();
		$pageinfo = Numbers::getPaginationPages($total, $page, $per_page, 3);
		$limit = array(
			'offset' => ($pageinfo['curpage']-1) * $per_page,
			'max' => $per_page
		);

		$feedback_ids = array();
		$feedback = array();

		if (!$just_form) {
			$feedback_ids = $searcher->getMatches($limit);
			$feedback = $this->em->getRepository('DeskPRO:Feedback')->getByResultIds($feedback_ids);
		}

		$category_counts = $structure->getFeedbackCategoryCounts($this->person);
		$status_counts   = $structure->getFeedbackStatusCounts($category, $this->person);
		$has_voted_ids = $this->person->FeedbackVotes->getVotesOnFeedbackCollection($feedback_ids);

		$comment_counts = array();
		if ($feedback) {
			$comment_counts = $this->em->getRepository('DeskPRO:FeedbackCategory')
				->getCommentHelper()
				->countsOnCollection($feedback);
		}

		#------------------------------
		# We have the submit form on the same pag
		#------------------------------

		$newfeedback = new \Application\DeskPRO\Feedback\NewFeedback(
			$this->session->getVisitor()
		);
		$newfeedback->setPersonContext($this->person);

		// Initial value from coming from a category
		if ($category) {
			$newfeedback->category_id = $category->getId();
		}

		if($this->person) {
			$newfeedback->person_name = $this->person->name;
		}

		$form = $this->get('form.factory')->create(new NewFeedbackType($this->person), $newfeedback);

		$cf_man = $this->container->getSystemService('FeedbackFieldsManager');
		$newfeedback_cat_field = $cf_man->getSystemField('cat');

		if (!$newfeedback_cat_field || !$cf_man->getFieldChildren($newfeedback_cat_field)) {
			$newfeedback_cat_field = null;
		} else {
			$custom_fields = $cf_man->getDisplayArray();
			$newfeedback_cat_field = $custom_fields[$newfeedback_cat_field->getId()];
		}

		#------------------------------
		# New feedback submitted
		#------------------------------

		$errors = $error_fields = null;
		$is_submitted = false;
		if ($this->in->getBool('process_new') && $this->person->hasPerm('feedback.submit')) {

			$this->ensureStandardRequestToken();

			$is_submitted = true;
			$validator = new \Application\UserBundle\Validator\NewFeedbackValidator();

			if ($captcha) {
				$validator->setCaptcha($captcha);
			}

			$newfeedback->custom_fields = $this->in->getRaw('feedback.custom_fields');
			$form->bindRequest($this->get('request'));

			// Try to set a default name from usersource
			// This allows sites that user usersources to edit the template to remove the 'name' field
			if (!$newfeedback->person_name && $newfeedback->person_email) {
				$person_processor = new PersonFromEmailProcessor();
				$person = $person_processor->findPersonByEmailAddress($newfeedback->person_email);
				if ($person) {
					$newfeedback->person_name = $person->getDisplayName();
				}
			}

			$newfeedback->setAttachBlobs($this->in->getCleanValueArray('attach_ids', 'str_simple', 'discard'));

			$trap_fail = false;
			if (!empty($_POST['first_name']) || !empty($_POST['last_name']) || !empty($_POST['email'])) {
				$trap_fail = true;
			}

			if ($validator->isValid($newfeedback) && !$trap_fail) {
				$feedback = $newfeedback->save();

				$notify_send = new \Application\DeskPRO\Notifications\NewFeedbackNotification($feedback);
				$notify_send->send();

				$submitted_feedback = App::getSession()->get('submitted_feedback') ?: array();
				$submitted_feedback[] = $feedback->getId();
				App::getSession()->set('submitted_feedback', $submitted_feedback);
				App::getSession()->save();

				App::setSkipCache(true);

				if ($newfeedback->require_login) {
					return $this->redirectRoute('user_login', array('return' => $this->generateUrl('user_feedback_newfeedback_finishlogin', array('feedback_id' => $feedback->id))));
				} elseif ($feedback->getStatusCode() == 'hidden.user_validating') {
					return $this->redirectRoute('user_feedback_view', array('slug' => $feedback->getUrlSlug()));
				} else {
					return $this->redirectRoute('user_feedback_view', array('slug' => $feedback->getUrlSlug()));
				}
			} else {
				$errors = $validator->getErrors(true);
				$error_fields = $validator->getErrorGroups(true);
			}
		}

		$feedback_collection = new FeedbackCollection($feedback, $this->container->getEm(), $cf_man);
		$display = $feedback_collection->getDisplayArray();

		return $this->render('UserBundle:Feedback:filter.html.twig', array(
			'display'               => $display,
			'feedback_cats'         => $feedback_cats,
			'active_status_cats'    => $active_status_cats,
			'closed_status_cats'    => $closed_status_cats,
			'status_subcats'        => $status_subcats,
			'sub_status_id'         => $sub_status_id,
			'category'              => $category,
			'cat_id'                => 0,
			'category_path'         => $category_path,
			'category_counts'       => $category_counts,
			'status_counts'         => $status_counts,
			'status'                => $status,
			'parent_status'         => $parent_status,
			'feedback'              => $feedback,
			'comment_counts'        => $comment_counts,
			'pageinfo'              => $pageinfo,
			'num_results'           => $total,
			'search_options'        => $search_options,
			'has_voted_ids'         => $has_voted_ids,
			'status_cat'            => $status_cat,

			'just_form'             => $just_form,
			'is_submitted'          => $is_submitted,
			'newfeedback'           => $newfeedback,
			'newfeedback_cat_field' => $newfeedback_cat_field,
			'captcha_html'          => $captcha_html,
			'form'                  => $form->createView(),
			'errors'                => $errors,
			'error_fields'          => $error_fields,
		));
	}

		/**
	 * View an feedback
	 *
	 * @param  $feedback_id
	 */
	public function voteAction($feedback_id)
	{
		$feedback = $this->em->getRepository('DeskPRO:Feedback')->find($feedback_id);
		if (!$feedback) {
			return $this->renderStandardError('@user.feedback.feedback_not_found', '@user.error.not-found', 404);
		}

		// Perm check
		if (!$this->person->PermissionsManager->UserPublishChecker->canViewFeedback($feedback)) {
			return $this->renderLoginOrPermissionError();
		}

		if ($this->person['id']) {
			$r = $this->em->getRepository('DeskPRO:Rating')->getRatingByPersonOnObject('Feedback', $feedback_id, $this->person, $this->session->getVisitor());
		} else {
			$r = $this->em->getRepository('DeskPRO:Rating')->getRatingByPersonOnObject('Feedback', $feedback_id, null, $this->session->getVisitor());
		}

		if ($r) {
			$feedback->removeRating($r);
			$this->em->remove($r);
			$this->em->flush();
		}

		if ($this->in->getInt('rating')) {
			$content_rating = new \Application\UserBundle\Controller\Helper\ContentRating($feedback, $this->person, $this->session->getVisitor());
			$content_rating->setRequest($this->request);

			$this->em->beginTransaction();
			$content_rating->setRating(
				$this->in->getInt('rating'),
				$this->in->getUint('log_search_id')
			);
			$this->em->flush();
			$this->em->commit();

			App::setSkipCache(true);
		}

		if ($this->request->isXmlHttpRequest()) {
			return $this->createJsonResponse(array(
				'success' => true,
				'voted' => $this->in->getInt('rating'),
				'total_rating' => $feedback->total_rating,
			));
		}

		return $this->redirectRoute('user_feedback_view', array('slug' => $feedback->getUrlSlug()));
	}

	public function newFinishLoginAction($feedback_id)
	{
		if ($this->person->isGuest()) {
			$return_url = $this->generateUrl('user_feedback_newfeedback_finishlogin', array('feedback_id' => $feedback_id));
			return $this->redirectRoute('user_login', array('return' => $return_url));
		}

		$feedback = $this->em->find('DeskPRO:Feedback', $feedback_id);
		if (!$feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if ($feedback->person->id != $this->person->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if (!$this->person->hasPerm('feedback.no_submit_validate')) {
			$feedback->setStatusCode('hidden.validating');
		} else {
			$feedback['status'] = Entity\Feedback::STATUS_NEW;
			$feedback['validating'] = null;
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($feedback);
			$this->em->flush();

			$this->em->getConnection()->commit();

			App::setSkipCache(true);
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$person = $this->person;
		App::getTranslator()->setTemporaryLanguage($person->getLanguage(), function($tr, $lang) use ($feedback, $person) {

			$vars = array(
				'feedback' => $feedback,
				'person' => $person,
				'email' => $person->primary_email,
				'validating' => $feedback['validating'],
			);

			$message = App::getMailer()->createMessage();
			$message->setTo($person->primary_email_address, $person->getDisplayName());
			$message->setTemplate('DeskPRO:emails_user:feedback-new.html.twig', $vars);
			$message->enableQueueHint();

			App::getMailer()->send($message);
		});

		return $this->redirectRoute('user_feedback_view', array('slug' => $feedback->getUrlSlug()));
	}



	/**
	 * View an feedback
	 *
	 * @param  $feedback_id
	 */
	public function viewAction($slug)
	{
		$feedback = $this->em->getRepository('DeskPRO:Feedback')->getBySlug($slug);
		if (!$feedback) {
			return $this->renderStandardError('@user.feedback.feedback_not_found', '@user.error.not-found', 404);
		}

		// Perm check
		if (!$this->person->PermissionsManager->UserPublishChecker->canViewFeedback($feedback, App::getSession())) {
			return $this->renderLoginOrPermissionError();
		}

		// Auto-correct URL
		if ($slug != $feedback->getUrlSlug()) {
			return $this->redirectRoute('user_feedback_view', array('slug' => $feedback->getUrlSlug()), 301);
		}

		return $this->viewFeedback($feedback);
	}

	private function viewFeedback($feedback, $errors = null)
	{
		$categories = $this->em->getRepository('DeskPRO:FeedbackCategory')->getRootNodes();

		$category = $feedback->category;
		$category_path = $category->getTreeParents();

		$has_voted_this   = $this->person->FeedbackVotes->getVotesOnFeedback($feedback);

		$comments = null;
		$comments_widget = null;
		$comments_helper = Comments::create($feedback);
		if ($comments_helper) {
			$comments_widget = $comments_helper->getHtml();
		} else {
			$comments = $this->em->getRepository('DeskPRO:FeedbackComment')->getDisplayComments($feedback, $this->person, $this->session->getVisitor());
		}

		if ($this->container->getSetting('core.facebook_like')) {
			$like_helper = FacebookLike::create($feedback);
			$facebook_like = $like_helper->getHtml();
		}

		$related_finder = new RelatedContentFinder($this->person, $feedback);
		$related_content = $related_finder->getRelatedEntities();

		$tpl = 'UserBundle:Feedback:view.html.twig';
		if ($this->in->getString('_partial') == 'overlayWidget' || $this->in->getString('_partial') == 'overlaySuggest') {
			$tpl = 'UserBundle:Feedback:view-overlay.html.twig';
		}

		$this->container->getSystemService('view_log')->view($feedback);

		$feedback_collection = new FeedbackCollection(
			array($feedback),
			$this->container->getEm(),
			$this->container->getSystemService('FeedbackFieldsManager')
		);
		$display = $feedback_collection->getDisplayArrayForFeedback($feedback);

		return $this->render($tpl, array(
			'display'           => $display,
			'has_voted_this'    => $has_voted_this,

			'feedback'          => $feedback,
			'category_path'     => $category_path,
			'category'          => $category,
			'categories'        => $categories,

			'comments'          => $comments,
			'comments_widget'   => $comments_widget,

			'facebook_like'     => isset($facebook_like) ? $facebook_like : null,

			'related_content'   => $related_content
		));
	}

	/**
	 * Submit a new comment
	 *
	 * @param  $article_id
	 */
	public function newCommentAction($feedback_id)
	{
		$feedback = $this->em->getRepository('DeskPRO:Feedback')->find($feedback_id);
		if (!$feedback) {
			return $this->renderStandardError('@user.feedback.feedback_not_found', '@user.error.not-found', 404);
		}

		// Perm check
		if (!$this->person->PermissionsManager->UserPublishChecker->canViewFeedback($feedback)) {
			return $this->renderLoginOrPermissionError();
		}

		if ($feedback->getStatus() == 'closed') {
			return $this->renderStandardError('@user.feedback.voting_closed', '@user.feedback.voting_closed-explain');
		}

		$new_comment = new \Application\DeskPRO\Comments\NewComment(
			'Application\\DeskPRO\\Entity\\FeedbackComment',
			$this->person,
			array('feedback' => $feedback)
		);

		$newcomment_formtype = new NewCommentFormType($this->person);
		$form = $this->get('form.factory')->create($newcomment_formtype, $new_comment);
		$validator = new \Application\UserBundle\Validator\NewCommentValidator();
		$validator->setPersonContext($this->person);

		if ($this->get('request')->getMethod() == 'POST') {

			$trap_fail = false;
			if (!empty($_POST['first_name']) || !empty($_POST['last_name']) || !empty($_POST['email'])) {
				$trap_fail = true;
			}

			if (!$this->consumeRequest('newcomment_feedback') || $trap_fail) {
				return $this->redirectRoute('user_feedback_view', array(
					'slug' => $feedback->getUrlSlug(),
				));
			}

			$form->bindRequest($this->get('request'));

			if (!$validator->isValid($new_comment)) {
				$this->session->setFlash('comment_error', $validator->getErrors(true));
				return $this->redirectRoute('user_feedback_view', array(
					'slug' => $feedback->getUrlSlug(),
				));
			}

			if ($form->isValid() && !$validator->checkDupe($new_comment)) {
				$comment = $new_comment->save();

				App::setSkipCache(true);

				if ($new_comment->require_login) {
					return $this->redirectRoute('user_newcomment_finishlogin', array(
						'comment_type' => 'feedback',
						'comment_id' => $comment->id,
					));
				}
			}

			return $this->viewFeedback($feedback);
		}

		return $this->redirectRoute('user_feedback_view', array(
			'slug' => $feedback->getUrlSlug(),
		));
	}
}
