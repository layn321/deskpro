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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Searcher\FeedbackSearch;
use Orb\Util\Numbers;

use Application\DeskPRO\ContentRevision\Util as ContentRevisionUtil;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Publish\RelatedContentUpdate;

class FeedbackController extends AbstractController
{
	public function searchAction()
	{
		$search_map = array(
			'category_id' => FeedbackSearch::TERM_CATEGORY,
			'category_id_specific' => FeedbackSearch::TERM_CATEGORY_SPECIFIC,
			'label' => FeedbackSearch::TERM_LABEL,
			'status' => FeedbackSearch::TERM_STATUS,
			'status_category_id' => FeedbackSearch::TERM_STATUS_CATEGORY
		);

		$terms = array();

		foreach ($search_map AS $input => $search_key) {
			$value = $this->in->getCleanValueArray($input, 'raw', 'discard');
			if ($value) {
				$terms[] = array('type' => $search_key, 'op' => 'contains', 'options' => $value);
			}
		}

		$date_created_start = $this->in->getUint('date_created_start');
		$date_created_end = $this->in->getUint('date_created_end');
		if ($date_created_end) {
			$terms[] = array('type' => FeedbackSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
				'date1' => $date_created_start,
				'date2' => $date_created_end
			));
		} else if ($date_created_start) {
			$terms[] = array('type' => FeedbackSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
				'date1' => $date_created_start
			));
		}

		$order_by = $this->in->getString('order');
		if (!$order_by) {
			$order_by = 'date_created:desc';
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('feedback', $terms, $extra, $this->in->getUint('cache_id'), new FeedbackSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($ids, $page, $per_page);
		$feedback = App::getEntityRepository('DeskPRO:Feedback')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($ids),
			'cache_id' => $result_cache->id,
			'feedback' => $this->getApiData($feedback)
		));
	}

	public function newFeedbackAction()
	{
		$errors = array();
		$feedback = new Feedback();

		$title = $this->in->getString('title');
		if ($title) {
			$feedback->title = $title;
		} else {
			$errors['title'] = array('required_field.title', 'title is required');
		}

		$content = $this->in->getHtml('content');
		if ($content) {
			$feedback->content = $content;
		} else {
			$errors['content'] = array('required_field.content', 'content is required');
		}

		$status_cat = $this->em->find('DeskPRO:FeedbackStatusCategory', $this->in->getUint('status_category_id'));
		if ($status_cat) {
			$feedback->setStatusCode($status_cat->status_type . '.' . $status_cat->id);
		} else {
			$status = $this->in->getString('status');
			if (!$status) {
				$status = 'new';
			}
			$feedback->setStatusCode($status);
		}

		$cat = $this->em->find('DeskPRO:FeedbackCategory', $this->in->getUint('category_id'));
		if ($cat) {
			$feedback->category = $cat;
		}

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		$feedback->person = $this->person;

		$this->_insertFeedbackAttachments($feedback);

		$this->em->persist($feedback);
		$this->em->flush();

		$labels = $this->in->getCleanValueArray('label', 'string', 'discard');
		if ($labels) {
			$feedback->getLabelManager()->setLabelsArray($labels, $this->em);
			$this->em->flush();
		}

		$user_category_id = $this->in->getUint('user_category_id');
		if ($user_category_id) {
			$field = $this->_getUserCategoryField();
			$field_manager = $this->container->getSystemService('feedback_fields_manager');
			$field_manager->saveFormToObject(array('field_' . $field->id => $user_category_id), $feedback, true);
		}

		return $this->createApiCreateResponse(
			array('id' => $feedback->id),
			$this->generateUrl('api_feedback_feedback', array('feedback_id' => $feedback->id), true)
		);
	}

	public function getFeedbackAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);

		return $this->createApiResponse(array('feedback' => $feedback->toApiData()));
	}

	public function postFeedbackAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id, 'edit');

		$revs = array();

		$title = $this->in->getString('title');
		if ($title) {
			$feedback->title = $title;

			$rev = ContentRevisionUtil::findOrCreate($feedback, 'title', $this->person);
			$rev->title = $feedback->title;

			$revs['title'] = $rev;
		}

		$content = $this->in->getString('content');
		if ($content && $content != $feedback->content) {
			$feedback->content = $this->in->getHtml('content');

			$rev = ContentRevisionUtil::findOrCreate($feedback, array('content'), $this->person);
			$rev->content = $feedback->content;

			$revs['content'] = $rev;
		}

		$category_id = $this->in->getUint('category_id');
		if ($category_id) {
			$cat = $this->em->find('DeskPRO:FeedbackCategory', $category_id);
			if ($cat) {
				$feedback->category = $cat;
			}
		}

		$status_category_id = $this->in->getUint('status_category_id');
		if ($status_category_id) {
			$status_cat = $this->em->find('DeskPRO:FeedbackStatusCategory', $this->in->getUint('status_category_id'));
			if ($status_cat) {
				$feedback->setStatusCode($status_cat->status_type . '.' . $status_cat->id);
			}
		} else {
			$status = $this->in->getString('status');
			if ($status) {
				$feedback->setStatusCode($status);
			}
		}

		$this->_insertFeedbackAttachments($feedback);

		foreach ($revs AS $rev) {
			$this->em->persist($rev);
		}
		$this->em->persist($feedback);
		$this->em->flush();

		$user_category_id = $this->in->getUint('user_category_id');
		if ($user_category_id) {
			$field = $this->_getUserCategoryField();
			$field_manager = $this->container->getSystemService('feedback_fields_manager');
			$field_manager->saveFormToObject(array('field_' . $field->id => $user_category_id), $feedback, true);
		}

		return $this->createSuccessResponse();
	}

	public function deleteFeedbackAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id, 'delete');

		$feedback->status_code = 'hidden.deleted';
		$this->em->persist($feedback);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getFeedbackVotesAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);
		$votes = App::getEntityRepository('DeskPRO:Rating')->getRatingsFor('feedback', $feedback->id);

		return $this->createApiResponse(array('votes' => $this->getApiData($votes)));
	}

	public function getFeedbackCommentsAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);
		$comments = $this->em->getRepository('DeskPRO:FeedbackComment')->getComments($feedback);

		return $this->createApiResponse(array('comments' => $this->getApiData($comments)));
	}

	public function newFeedbackCommentAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);

		$content = $this->in->getString('content');
		if (!$content) {
			return $this->createApiErrorResponse('required_field.content', 'Missing content');
		}

		$person_id = $this->in->getUint('person_id');
		$person = null;
		if ($person_id) {
			$person = $this->em->getRepository('DeskPRO:Person')->find($person_id);
		}

		$status = $this->in->getString('status');

		$comment = new \Application\DeskPRO\Entity\FeedbackComment();
		$comment->feedback = $feedback;
		$comment->person = $person ?: $this->person;
		$comment['content'] = $content;
		$comment['status'] = $status ?: 'visible';
		$comment['is_reviewed'] = ($comment['status'] == 'visible' && !$person);
		$comment['date_created']  = new \DateTime();

		$this->em->persist($comment);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $comment->id),
			$this->generateUrl('api_feedback_feedback_comments_comment', array('feedback_id' => $feedback->id, 'comment_id' => $comment->id), true)
		);
	}

	public function getFeedbackCommentAction($feedback_id, $comment_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);
		$comment = $this->em->getRepository('DeskPRO:FeedbackComment')->find($comment_id);
		if (!$comment || $comment->feedback->id != $feedback->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		return $this->createApiResponse(array('comment' => $comment->toApiData()));
	}

	public function postFeedbackCommentAction($feedback_id, $comment_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);
		$comment = $this->em->getRepository('DeskPRO:FeedbackComment')->find($comment_id);
		if (!$comment || $comment->feedback->id != $feedback->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$approved = false;
		$status = $this->in->getString('status');
		if ($status) {
			$approved = ($status == 'visible' && $comment->status != 'visible');
			$comment->status = $status;
		}

		$content = $this->in->getString('content');
		if ($content) {
			$comment->content = $content;
		}

		$this->em->persist($comment);
		$this->em->flush();

		if ($approved) {
			$this->_sendCommentApprovedNotification($comment);
		}

		return $this->createSuccessResponse();
	}

	public function deleteFeedbackCommentAction($feedback_id, $comment_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);
		$comment = $this->em->getRepository('DeskPRO:FeedbackComment')->find($comment_id);
		if (!$comment || $comment->feedback->id != $feedback->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->remove($comment);
		$this->em->flush();

		$this->_sendCommentDeletedNotification($comment);

		return $this->createSuccessResponse();
	}

	public function mergeFeedbackAction($feedback_id, $other_feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id, 'edit');
		$other_feedback = $this->_getFeedbackOr404($other_feedback_id, 'edit');

		if (!$this->person->PermissionsManager->PublishChecker->canEdit($feedback)
			|| !$this->person->PermissionsManager->PublishChecker->canEdit($other_feedback)
			|| !$this->person->PermissionsManager->PublishChecker->canDelete($other_feedback)
		) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		try {
			$this->em->beginTransaction();
			$merge = new \Application\DeskPRO\Feedback\FeedbackMerge($this->person, $feedback, $other_feedback);
			$merge->merge();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		return $this->createSuccessResponse();
	}

	public function getFeedbackAttachmentsAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);

		return $this->createApiResponse(array('attachments' => $this->getApiData($feedback->attachments)));
	}

	public function newFeedbackAttachmentAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id, 'edit');

		$file = $this->request->files->get('attach');
		if (is_array($file)) {
			$file = reset($file);
		}

		$blob = false;

		if ($file) {
			$accept = $this->container->getAttachmentAccepter();

			$error = $accept->getError($file, 'agent');
			if (!$error) {
				$blob = $accept->accept($file);
			} else {
				$message = $this->container->getTranslator()->phrase('agent.general.attach_error_' . $error['error_code'], $error);
				return $this->createApiErrorResponse($error['error_code'], $message);
			}
		} else {
			$blob_id = $this->in->getUint('attach_id');
			$blob = $this->em->find('DeskPRO:Blob', $blob_id);
			if (!$blob) {
				return $this->createApiErrorResponse('invalid_argument.attach_id', 'attach_id not found');
			}
		}

		$attach = $this->_addFeedbackAttachment($blob, $feedback);

		$this->em->persist($feedback);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $attach->id),
			$this->generateUrl('api_feedback_feedback_attachment', array('feedback_id' => $feedback->id, 'attachment_id' => $attach->id), true)
		);
	}

	public function getFeedbackAttachmentAction($feedback_id, $attachment_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);
		$exists = false;
		foreach ($feedback->attachments AS $attachment) {
			if ($attachment->id == $attachment_id) {
				$exists = true;
				break;
			}
		}

		return $this->createApiResponse(array('exists' => $exists));
	}

	public function deleteFeedbackAttachmentAction($feedback_id, $attachment_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);
		foreach ($feedback->attachments AS $k => $attachment) {
			if ($attachment->id == $attachment_id) {
				$feedback->attachments->remove($k);
				$this->em->remove($attachment);
				break;
			}
		}

		$this->em->persist($feedback);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getFeedbackLabelsAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);

		return $this->createApiResponse(array('labels' => $this->getApiData($feedback->labels)));
	}

	public function postFeedbackLabelsAction($feedback_id)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id, 'edit');
		$label = $this->in->getString('label');

		if ($label === '') {
			return $this->createApiErrorResponse('required_field', "Field 'label' missing or empty");
		}

		$feedback->getLabelManager()->addLabel($label);
		$this->em->persist($feedback);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('label' => $label),
			$this->generateUrl('api_feedback_feedback_label', array('feedback_id' => $feedback->id, 'label' => $label), true)
		);
	}

	public function getFeedbackLabelAction($feedback_id, $label)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id);

		if ($feedback->getLabelManager()->hasLabel($label)) {
			return $this->createApiResponse(array('exists' => true));
		} else {
			return $this->createApiResponse(array('exists' => false));
		}
	}

	public function deleteFeedbackLabelAction($feedback_id, $label)
	{
		$feedback = $this->_getFeedbackOr404($feedback_id, 'edit');

		$feedback->getLabelManager()->removeLabel($label);
		$this->em->persist($feedback);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getValidatingCommentsAction()
	{
		$comments = $this->em->getRepository('DeskPRO:FeedbackComment')->getValidatingComments();
		$entity_key = 'feedback';
		$output = array();
		foreach ($comments AS $key => $value) {
			$output[$key] = $value->toApiData(false, true);
			if ($value->$entity_key) {
				$output[$key][$entity_key] = $value->$entity_key->toApiData(false, false);
			}
		}

		return $this->createApiResponse(array('comments' => $output));
	}

	public function getCategoriesAction()
	{
		$categories = $this->em->getRepository('DeskPRO:FeedbackCategory')->getFlatHierarchy();

		return $this->createApiResponse(array('categories' => $categories));
	}

	public function getStatusCategoriesAction()
	{
		$categories = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->findAll();

		return $this->createApiResponse(array('categories' => $this->getApiData($categories)));
	}

	public function getUserCategoriesAction()
	{
		$field = $this->_getUserCategoryField();
		$children = $field->getAllChildren();

		return $this->createApiResponse(array('categories' => $this->getApiData($children)));
	}

	protected function _insertFeedbackAttachments(Feedback $feedback)
	{
		$attachments = $this->request->files->get('attach');
		if (!is_array($attachments)) {
			$attachments = array($attachments);
		}
		$accept = $this->container->getAttachmentAccepter();

		foreach ($attachments AS $file) {
			$error = $accept->getError($file, 'agent');
			if (!$error) {
				$blob = $accept->accept($file);
				$this->_addFeedbackAttachment($blob, $feedback);
			}
		}

		foreach ($this->in->getCleanValueArray('attach_id') as $blob_id) {
			$this->_addFeedbackAttachment($blob_id, $feedback);
		}
	}

	protected function _addFeedbackAttachment($blob_id, Feedback $feedback)
	{
		if ($blob_id instanceof \Application\DeskPRO\Entity\Blob) {
			$blob = $blob_id;
		} else {
			$blob = $this->em->getRepository('DeskPRO:Blob')->find($blob_id);
		}

		if ($blob) {
			$attach = new \Application\DeskPRO\Entity\FeedbackAttachment();
			$attach['blob'] = $blob;
			$attach['person'] = $this->person;

			$feedback->addAttachment($attach);

			return $attach;
		} else {
			return false;
		}
	}

	/**
	 * @return \Application\DeskPRO\Entity\CustomDefFeedback
	 */
	protected function _getUserCategoryField()
	{
		$field = $this->container->getDataService('CustomDefFeedback')->getCategoryField();

		if (!$field) {
			$field = new \Application\DeskPRO\Entity\CustomDefFeedback();
			$field->handler_class = 'Application\\DeskPRO\\CustomFields\\Handler\\Choice';
			$field->title = 'Category';
			$field->sys_name = 'cat';
			$field->description = 'Category';
			$this->em->persist($field);
			$this->em->flush();
		}

		return $field;
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\Feedback
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getFeedbackOr404($id, $check_perm = false)
	{
		$feedback = $this->em->getRepository('DeskPRO:Feedback')->findOneById($id);

		if (!$feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no feedback with ID $id");
		}

		if ($check_perm) {
			if ($check_perm == 'edit' && !$this->person->PermissionsManager->PublishChecker->canEdit($feedback)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}

			if ($check_perm == 'delete' && !$this->person->PermissionsManager->PublishChecker->canDelete($feedback)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
		}

		return $feedback;
	}
}
