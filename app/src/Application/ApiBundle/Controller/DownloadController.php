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
use Application\DeskPRO\Entity\Download;
use Application\DeskPRO\Entity\DownloadComment;
use Application\DeskPRO\Searcher\DownloadSearch;
use Application\DeskPRO\UI\RuleBuilder;

use Application\DeskPRO\ContentRevision\Util as ContentRevisionUtil;
use Application\AgentBundle\Controller\Helper\DownloadResults;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Publish\RelatedContentUpdate;
use Orb\Util\Numbers;

class DownloadController extends AbstractController
{
	public function searchAction()
	{
		$search_map = array(
			'category_id' => DownloadSearch::TERM_CATEGORY,
			'category_id_specific' => DownloadSearch::TERM_CATEGORY_SPECIFIC,
			'label' => DownloadSearch::TERM_LABEL,
			'new' => DownloadSearch::TERM_NEW,
			'popular' => DownloadSearch::TERM_POPULAR,
			'status' => DownloadSearch::TERM_STATUS
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
			$terms[] = array('type' => DownloadSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
				'date1' => $date_created_start,
				'date2' => $date_created_end
			));
		} else if ($date_created_start) {
			$terms[] = array('type' => DownloadSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
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

		$result_cache = $this->getApiSearchResult('download', $terms, $extra, $this->in->getUint('cache_id'), new DownloadSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($ids, $page, $per_page);
		$downloads = App::getEntityRepository('DeskPRO:Download')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($ids),
			'cache_id' => $result_cache->id,
			'downloads' => $this->getApiData($downloads)
		));
	}

	public function newDownloadAction()
	{
		$errors = array();
		$download = new Download();

		$title = $this->in->getString('title');
		if ($title) {
			$download->title = $title;
		}

		$download->content = $this->in->getHtml('content');

		$status = $this->in->getString('status');
		if (!$status) {
			$status = 'published';
		}
		$download->setStatusCode($status);

		$cat = $this->em->find('DeskPRO:DownloadCategory', $this->in->getUint('category_id'));
		if (!$cat) {
			$errors['category_id'] = array('invalid_argument.category_id', 'category_id not found');
			} else {
			$download->category = $cat;
		}

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		$file = $this->request->files->get('attach');
		if (is_array($file)) {
			$file = reset($file);
		}

		if ($file) {
			$accept = $this->container->getAttachmentAccepter();

			$error = $accept->getError($file, 'agent');
			if (!$error) {
				$blob = $accept->accept($file);
			} else {
				$message = $this->container->getTranslator()->phrase('agent.general.attach_error_' . $error['error_code'], $error);
				$errors['attach'] = array($error['error_code'] . '.attach', $message);
			}
		} else {
			$blob_id = $this->in->getUint('attach_id');
			$blob = $this->em->find('DeskPRO:Blob', $blob_id);
			if (!$blob) {
				$errors['attach_id'] = array('invalid_argument.attach_id', 'attach_id not found');
			}
		}

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		$download->blob = $blob;
		if (!$download->title) {
			$download->title = $blob->filename;
		}
		$download->person = $this->person;

		$this->em->persist($blob);
		$this->em->persist($download);
		$this->em->flush();

		$labels = $this->in->getCleanValueArray('label', 'string', 'discard');
		if ($labels) {
			$download->getLabelManager()->setLabelsArray($labels, $this->em);
			$this->em->flush();
		}

		return $this->createApiCreateResponse(
			array('id' => $download->id),
			$this->generateUrl('api_downloads_download', array('download_id' => $download->id), true)
		);
	}

	public function getDownloadAction($download_id)
	{
		$download = $this->_getDownloadOr404($download_id);

		return $this->createApiResponse(array('download' => $download->toApiData()));
	}

	public function postDownloadAction($download_id)
	{
		$download = $this->_getDownloadOr404($download_id, 'edit');

		$revs = array();

		$title = $this->in->getString('title');
		if ($title) {
			$download->title = $title;

			$rev = ContentRevisionUtil::findOrCreate($download, 'title', $this->person);
			$rev->title = $download->title;

			$revs['title'] = $rev;
		}

		$status = $this->in->getString('status');
		if ($status) {
			$download->status = $status;
		}

		$content = $this->in->getString('content');
		if ($content && $content != $download->content) {
			$download->content = $this->in->getHtml('content');

			$rev = ContentRevisionUtil::findOrCreate($download, array('content'), $this->person);
			$rev->content = $download->content;

			$revs['content'] = $rev;
		}

		$file = $this->request->files->get('attach');
		if (is_array($file)) {
			$file = reset($file);
		}

		$blob = false;
		if ($file) {
			$accept = $this->container->getAttachmentAccepter();
			if (!$accept->getError($file, 'agent')) {
				$blob = $accept->accept($file);
			}
		} else if ($this->in->getUint('attach_id')) {
			$blob = $this->em->find('DeskPRO:Blob', $this->in->getUint('attach_id'));
		}

		if ($blob) {
			if (!$title) {
				$download->title = $blob->filename;
			} else {
				// replace this revision below
				unset($revs['title']);
			}

			$download->blob = $blob;

			$rev = ContentRevisionUtil::findOrCreate($download, array('blob', 'title'), $this->person);
			$rev->title = $download->title;
			$rev->blob = $download->blob;

			$revs['file'] = $rev;
		}

		$category_id = $this->in->getUint('category_id');
		if ($category_id) {
			$cat = $this->em->find('DeskPRO:DownloadCategory', $category_id);
			if ($cat) {
				$download->category = $cat;
			}
		}

		foreach ($revs AS $rev) {
			$this->em->persist($rev);
		}
		$this->em->persist($download);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function deleteDownloadAction($download_id)
	{
		$download = $this->_getDownloadOr404($download_id, 'delete');

		$download->status_code = 'hidden.deleted';
		$this->em->persist($download);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getDownloadCommentsAction($download_id)
	{
		$download = $this->_getDownloadOr404($download_id);
		$comments = $this->em->getRepository('DeskPRO:DownloadComment')->getComments($download);

		return $this->createApiResponse(array('comments' => $this->getApiData($comments)));
	}

	public function newDownloadCommentAction($download_id)
	{
		$download = $this->_getDownloadOr404($download_id);

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

		$comment = new DownloadComment();
		$comment->download = $download;
		$comment->person = $person ?: $this->person;
		$comment['content'] = $content;
		$comment['status'] = $status ?: 'visible';
		$comment['is_reviewed'] = ($comment['status'] == 'visible' && !$person);
		$comment['date_created']  = new \DateTime();

		$this->em->persist($comment);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $comment->id),
			$this->generateUrl('api_downloads_download_comments_comment', array('download_id' => $download->id, 'comment_id' => $comment->id), true)
		);
	}

	public function getDownloadCommentAction($download_id, $comment_id)
	{
		$download = $this->_getDownloadOr404($download_id);
		$comment = $this->em->getRepository('DeskPRO:DownloadComment')->find($comment_id);
		if (!$comment || $comment->download->id != $download->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		return $this->createApiResponse(array('comment' => $comment->toApiData()));
	}

	public function postDownloadCommentAction($download_id, $comment_id)
	{
		$download = $this->_getDownloadOr404($download_id);
		$comment = $this->em->getRepository('DeskPRO:DownloadComment')->find($comment_id);
		if (!$comment || $comment->download->id != $download->id) {
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

	public function deleteDownloadCommentAction($download_id, $comment_id)
	{
		$download = $this->_getDownloadOr404($download_id);
		$comment = $this->em->getRepository('DeskPRO:DownloadComment')->find($comment_id);
		if (!$comment || $comment->download->id != $download->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->remove($comment);
		$this->em->flush();

		$this->_sendCommentDeletedNotification($comment);

		return $this->createSuccessResponse();
	}

	public function getDownloadLabelsAction($download_id)
	{
		$download = $this->_getDownloadOr404($download_id);

		return $this->createApiResponse(array('labels' => $this->getApiData($download->labels)));
	}

	public function postDownloadLabelsAction($download_id)
	{
		$download = $this->_getDownloadOr404($download_id, 'edit');
		$label = $this->in->getString('label');

		if ($label === '') {
			return $this->createApiErrorResponse('required_field', "Field 'label' missing or empty");
		}

		$download->getLabelManager()->addLabel($label);
		$this->em->persist($download);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('label' => $label),
			$this->generateUrl('api_downloads_download_label', array('download_id' => $download->id, 'label' => $label), true)
		);
	}

	public function getDownloadLabelAction($download_id, $label)
	{
		$download = $this->_getDownloadOr404($download_id);

		if ($download->getLabelManager()->hasLabel($label)) {
			return $this->createApiResponse(array('exists' => true));
		} else {
			return $this->createApiResponse(array('exists' => false));
		}
	}

	public function deleteDownloadLabelAction($download_id, $label)
	{
		$download = $this->_getDownloadOr404($download_id, 'edit');

		$download->getLabelManager()->removeLabel($label);
		$this->em->persist($download);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getValidatingCommentsAction()
	{
		$comments = $this->em->getRepository('DeskPRO:DownloadComment')->getValidatingComments();
		$entity_key = 'download';
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
		$categories = $this->em->getRepository('DeskPRO:DownloadCategory')->getFlatHierarchy();

		return $this->createApiResponse(array('categories' => $categories));
	}

	public function postCategoriesAction()
	{
		$errors = array();

		$title = $this->in->getString('title');
		if (!$title) {
			$errors['title'] = array('required_field.title', 'title empty or missing');
		}

		$category = new \Application\DeskPRO\Entity\DownloadCategory();

		$category->title = $title;

		$parent_id = $this->in->getUint('parent_id');
		if ($parent_id) {
			$parent = $this->em->getRepository('DeskPRO:DownloadCategory')->find($parent_id);
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
				App::getDb()->insert('download_category2usergroup', array(
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
			$this->generateUrl('api_downloads_category', array('category_id' => $category->id), true)
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
				$parent = $this->em->getRepository('DeskPRO:DownloadCategory')->find($parent_id);
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
			\Application\DeskPRO\Publish\CategoryEdit::deleteCategory('downloads', $category_id);
		} catch (\OutOfBoundsException $e) {
			return $this->createApiErrorResponse('invalid_argument.category_id', 'category is not empty');
		}

		return $this->createSuccessResponse();
	}

	public function getCategoryDownloadsAction($category_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		$terms = array(
			array('type' => DownloadSearch::TERM_CATEGORY_SPECIFIC, 'op' => 'contains', 'options' => array($category->id))
		);

		$order_by = $this->in->getString('order');
		if (!$order_by) {
			$order_by = 'date:desc';
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('download', $terms, $extra, $this->in->getUint('cache_id'), new DownloadSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($ids, $page, $per_page);
		$downloads = App::getEntityRepository('DeskPRO:Download')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($ids),
			'cache_id' => $result_cache->id,
			'downloads' => $this->getApiData($downloads)
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
			$this->db->insert('download_category2usergroup', array(
				'category_id' => $category->id,
				'usergroup_id' => $group_id
			));
		}

		return $this->createApiCreateResponse(
			array('id' => $group_id),
			$this->generateUrl('api_downloads_category_group', array('category_id' => $category->id, 'group_id' => $group_id), true)
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
	 * @return \Application\DeskPRO\Entity\Download
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getDownloadOr404($id, $check_perm = false)
	{
		$download = $this->em->getRepository('DeskPRO:Download')->findOneById($id);

		if (!$download) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no download with ID $id");
		}

		if ($check_perm) {
			if ($check_perm == 'edit' && !$this->person->PermissionsManager->PublishChecker->canEdit($download)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}

			if ($check_perm == 'delete' && !$this->person->PermissionsManager->PublishChecker->canDelete($download)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
		}

		return $download;
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\DownloadCategory
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getCategoryOr404($id)
	{
		$category = $this->em->getRepository('DeskPRO:DownloadCategory')->find($id);

		if (!$category) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no category with ID $id");
		}

		return $category;
	}
}
