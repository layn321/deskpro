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
use Application\DeskPRO\Entity\News;
use Application\DeskPRO\Entity\NewsComment;
use Application\DeskPRO\Searcher\NewsSearch;
use Application\DeskPRO\UI\RuleBuilder;
use Orb\Util\Numbers;

use Application\AgentBundle\Controller\Helper\NewsResults;
use Application\DeskPRO\ContentRevision\Util as ContentRevisionUtil;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Publish\RelatedContentUpdate;

class NewsController extends AbstractController
{
	public function searchAction()
	{
		$search_map = array(
			'category_id' => NewsSearch::TERM_CATEGORY,
			'category_id_specific' => NewsSearch::TERM_CATEGORY_SPECIFIC,
			'label' => NewsSearch::TERM_LABEL,
			'status' => NewsSearch::TERM_STATUS
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
			$terms[] = array('type' => NewsSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
				'date1' => $date_created_start,
				'date2' => $date_created_end
			));
		} else if ($date_created_start) {
			$terms[] = array('type' => NewsSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
				'date1' => $date_created_start
			));
		}

		$order_by = $this->in->getString('order');
		if (!$order_by) {
			$order_by = 'date:desc';
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('news', $terms, $extra, $this->in->getUint('cache_id'), new NewsSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($ids, $page, $per_page);
		$news = App::getEntityRepository('DeskPRO:News')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($ids),
			'cache_id' => $result_cache->id,
			'news' => $this->getApiData($news)
		));
	}

	public function newNewsAction()
	{
		$errors = array();
		$news = new News();

		$title = $this->in->getString('title');
		if ($title) {
			$news->title = $title;
		} else {
			$errors['title'] = array('required_field.title', 'title is required');
		}

		$content = $this->in->getHtml('content');
		if ($content) {
			$news->content = $content;
		} else {
			$errors['content'] = array('required_field.content', 'content is required');
		}

		$status = $this->in->getString('status');
		if (!$status) {
			$status = 'published';
		}
		$news->setStatusCode($status);

		$date = $this->in->getUint('date');
		if ($date) {
			$news->date_created = new \DateTime('@' . $date);
			if ($status == 'published') {
				$news->date_published = new \DateTime('@' . $date);
			}
		}

		$cat = $this->em->find('DeskPRO:NewsCategory', $this->in->getUint('category_id'));
		if (!$cat) {
			$errors['category_id'] = array('invalid_argument.category_id', 'category_id not found');
		} else {
			$news->category = $cat;
		}

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		$news->person = $this->person;

		$this->em->persist($news);
		$this->em->flush();

		$labels = $this->in->getCleanValueArray('label', 'string', 'discard');
		if ($labels) {
			$news->getLabelManager()->setLabelsArray($labels, $this->em);
			$this->em->flush();
		}

		return $this->createApiCreateResponse(
			array('id' => $news->id),
			$this->generateUrl('api_news_news', array('news_id' => $news->id), true)
		);
	}

	public function getNewsAction($news_id)
	{
		$news = $this->_getNewsOr404($news_id);

		return $this->createApiResponse(array('news' => $news->toApiData()));
	}

	public function postNewsAction($news_id)
	{
		$news = $this->_getNewsOr404($news_id, 'edit');

		$revs = array();

		$title = $this->in->getString('title');
		if ($title) {
			$news->title = $title;

			$rev = ContentRevisionUtil::findOrCreate($news, 'title', $this->person);
			$rev->title = $news->title;

			$revs['title'] = $rev;
		}

		$status = $this->in->getString('status');
		if ($status) {
			$news->status = $status;
		}

		$date = $this->in->getUint('date_published');
		if ($date && $news->status == 'published') {
			$news->date_published = new \DateTime('@' . $date);
		}

		$content = $this->in->getString('content');
		if ($content && $content != $news->content) {
			$news->content = $this->in->getHtml('content');

			$rev = ContentRevisionUtil::findOrCreate($news, array('content'), $this->person);
			$rev->content = $news->content;

			$revs['content'] = $rev;
		}

		$category_id = $this->in->getUint('category_id');
		if ($category_id) {
			$cat = $this->em->find('DeskPRO:NewsCategory', $category_id);
			if ($cat) {
				$news->category = $cat;
			}
		}

		foreach ($revs AS $rev) {
			$this->em->persist($rev);
		}
		$this->em->persist($news);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function deleteNewsAction($news_id)
	{
		$news = $this->_getNewsOr404($news_id, 'delete');

		$news->status_code = 'hidden.deleted';
		$this->em->persist($news);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getNewsCommentsAction($news_id)
	{
		$news = $this->_getNewsOr404($news_id);
		$comments = $this->em->getRepository('DeskPRO:NewsComment')->getComments($news);

		return $this->createApiResponse(array('comments' => $this->getApiData($comments)));
	}

	public function newNewsCommentAction($news_id)
	{
		$news = $this->_getNewsOr404($news_id);

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

		$comment = new NewsComment();
		$comment->news = $news;
		$comment->person = $person ?: $this->person;
		$comment['content'] = $content;
		$comment['status'] = $status ?: 'visible';
		$comment['is_reviewed'] = ($comment['status'] == 'visible' && !$person);
		$comment['date_created']  = new \DateTime();

		$this->em->persist($comment);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $comment->id),
			$this->generateUrl('api_news_news_comments_comment', array('news_id' => $news->id, 'comment_id' => $comment->id), true)
		);
	}

	public function getNewsCommentAction($news_id, $comment_id)
	{
		$news = $this->_getNewsOr404($news_id);
		$comment = $this->em->getRepository('DeskPRO:NewsComment')->find($comment_id);
		if (!$comment || $comment->news->id != $news->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		return $this->createApiResponse(array('comment' => $comment->toApiData()));
	}

	public function postNewsCommentAction($news_id, $comment_id)
	{
		$news = $this->_getNewsOr404($news_id);
		$comment = $this->em->getRepository('DeskPRO:NewsComment')->find($comment_id);
		if (!$comment || $comment->news->id != $news->id) {
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

	public function deleteNewsCommentAction($news_id, $comment_id)
	{
		$news = $this->_getNewsOr404($news_id);
		$comment = $this->em->getRepository('DeskPRO:NewsComment')->find($comment_id);
		if (!$comment || $comment->news->id != $news->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->remove($comment);
		$this->em->flush();

		$this->_sendCommentDeletedNotification($comment);

		return $this->createSuccessResponse();
	}

	public function getNewsLabelsAction($news_id)
	{
		$news = $this->_getNewsOr404($news_id);

		return $this->createApiResponse(array('labels' => $this->getApiData($news->labels)));
	}

	public function postNewsLabelsAction($news_id)
	{
		$news = $this->_getNewsOr404($news_id, 'edit');
		$label = $this->in->getString('label');

		if ($label === '') {
			return $this->createApiErrorResponse('required_field', "Field 'label' missing or empty");
		}

		$news->getLabelManager()->addLabel($label);
		$this->em->persist($news);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('label' => $label),
			$this->generateUrl('api_news_news_label', array('news_id' => $news->id, 'label' => $label), true)
		);
	}

	public function getNewsLabelAction($news_id, $label)
	{
		$news = $this->_getNewsOr404($news_id);

		if ($news->getLabelManager()->hasLabel($label)) {
			return $this->createApiResponse(array('exists' => true));
		} else {
			return $this->createApiResponse(array('exists' => false));
		}
	}

	public function deleteNewsLabelAction($news_id, $label)
	{
		$news = $this->_getNewsOr404($news_id, 'edit');

		$news->getLabelManager()->removeLabel($label);
		$this->em->persist($news);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getValidatingCommentsAction()
	{
		$comments = $this->em->getRepository('DeskPRO:NewsComment')->getValidatingComments();
		$entity_key = 'news';
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
		$categories = $this->em->getRepository('DeskPRO:NewsCategory')->getFlatHierarchy();

		return $this->createApiResponse(array('categories' => $categories));
	}

	public function postCategoriesAction()
	{
		$errors = array();

		$title = $this->in->getString('title');
		if (!$title) {
			$errors['title'] = array('required_field.title', 'title empty or missing');
		}

		$category = new \Application\DeskPRO\Entity\NewsCategory();

		$category->title = $title;

		$parent_id = $this->in->getUint('parent_id');
		if ($parent_id) {
			$parent = $this->em->getRepository('DeskPRO:NewsCategory')->find($parent_id);
			if ($parent) {
				$category->setParent($parent);
			}
		}

		$category->display_order = $this->in->getUint('display_order');

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		if ($this->in->checkIsset('usergroup_id')) {
			$usergroup_ids = $this->in->getCleanValueArray('usergroup_id', 'uint');
		} else {
			$usergroup_ids = array(1);
		}

		$this->db->beginTransaction();

		try {
			$this->em->persist($category);
			$this->em->flush();

			foreach ($usergroup_ids AS $usergroup_id) {
				if (!$usergroup_id) {
					continue;
				}
				App::getDb()->insert('news_category2usergroup', array(
					'category_id'  => $category->getId(),
					'usergroup_id' => $usergroup_id
				));
			}

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createApiCreateResponse(
			array('id' => $category->id),
			$this->generateUrl('api_news_category', array('category_id' => $category->id), true)
		);
	}

	public function getCategoryAction($category_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		return $this->createApiResponse(array('category' => $category->toApiData()));
	}

	public function postCategoryAction($category_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		$errors = array();

		if ($this->in->checkIsset('title')) {
			$title = $this->in->getString('title');
			if (!$title) {
				$errors['title'] = array('required_field.title', 'title empty or missing');
			}
			$category->title = $title;
		}


		if ($this->in->checkIsset('parent_id')) {
			$parent_id = $this->in->getUint('parent_id');
			if ($parent_id) {
				$parent = $this->em->getRepository('DeskPRO:NewsCategory')->find($parent_id);
				if ($parent) {
					$category->setParent($parent);
				}
			} else {
				$category->setParent(null);
			}
		}

		if ($this->in->checkIsset('display_order')) {
			$category->display_order = $this->in->getUint('display_order');
		}

		$this->em->persist($category);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function deleteCategoryAction($category_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		try {
			\Application\DeskPRO\Publish\CategoryEdit::deleteCategory('news', $category_id);
		} catch (\OutOfBoundsException $e) {
			return $this->createApiErrorResponse('invalid_argument.category_id', 'category is not empty');
		}

		return $this->createSuccessResponse();
	}

	public function getCategoryNewsAction($category_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		$terms = array(
			array('type' => NewsSearch::TERM_CATEGORY_SPECIFIC, 'op' => 'contains', 'options' => array($category->id))
		);

		$order_by = $this->in->getString('order');
		if (!$order_by) {
			$order_by = 'date:desc';
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('news', $terms, $extra, $this->in->getUint('cache_id'), new NewsSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($ids, $page, $per_page);
		$news = App::getEntityRepository('DeskPRO:News')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($ids),
			'cache_id' => $result_cache->id,
			'news' => $this->getApiData($news)
		));
	}

	public function getCategoryGroupsAction($category_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		return $this->createApiResponse(array('groups' => $this->getApiData($category->usergroups)));
	}

	public function postCategoryGroupsAction($category_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		$group_id = $this->in->getUint('id');

		$group = $this->em->getRepository('DeskPRO:Usergroup')->find($group_id);
		if (!$group || $group->is_agent_group) {
			return $this->createApiErrorResponse('invalid_argument.id', 'group cannot be found or is not available');
		}

		$exists = false;
		foreach ($category->usergroups AS $group) {
			if ($group->id == $group_id) {
				$exists = true;
				break;
			}
		}

		if (!$exists) {
			$this->db->insert('news_category2usergroup', array(
				'category_id' => $category->id,
				'usergroup_id' => $group_id
			));
		}

		return $this->createApiCreateResponse(
			array('id' => $group_id),
			$this->generateUrl('api_news_category_group', array('category_id' => $category->id, 'group_id' => $group_id), true)
		);
	}

	public function getCategoryGroupAction($category_id, $group_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		$exists = false;
		foreach ($category->usergroups AS $group) {
			if ($group->id == $group_id) {
				$exists = true;
				break;
			}
		}

		return $this->createApiResponse(array('exists' => $exists));
	}

	public function deleteCategoryGroupAction($category_id, $group_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		foreach ($category->usergroups AS $key => $group) {
			if ($group->id == $group_id) {
				$category->usergroups->remove($key);
				$this->em->persist($category);
				$this->em->flush();
				break;
			}
		}

		return $this->createSuccessResponse();
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\News
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getNewsOr404($id, $check_perm = false)
	{
		$news = $this->em->getRepository('DeskPRO:News')->findOneById($id);

		if (!$news) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no news with ID $id");
		}

		if ($check_perm) {
			if ($check_perm == 'edit' && !$this->person->PermissionsManager->PublishChecker->canEdit($news)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}

			if ($check_perm == 'delete' && !$this->person->PermissionsManager->PublishChecker->canDelete($news)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
		}

		return $news;
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\NewsCategory
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getCategoryOr404($id)
	{
		$category = $this->em->getRepository('DeskPRO:NewsCategory')->find($id);

		if (!$category) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no category with ID $id");
		}

		return $category;
	}
}
