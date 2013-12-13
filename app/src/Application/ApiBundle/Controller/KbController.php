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
use Application\DeskPRO\Entity\Article;
use Application\DeskPRO\Entity\ArticleAttachment;
use Application\DeskPRO\Entity\ArticleComment;
use Application\DeskPRO\Entity\ArticlePendingCreate;
use Application\DeskPRO\Entity\ResultCache;
use Application\DeskPRO\Searcher\ArticleSearch;
use Application\DeskPRO\UI\RuleBuilder;
use Orb\Util\Numbers;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Publish\RelatedContentUpdate;

use Application\DeskPRO\ContentRevision\Util as ContentRevisionUtil;

use Application\AgentBundle\Controller\Helper\ArticleResults;

class KbController extends AbstractController
{
	public function searchAction()
	{
		$search_map = array(
			'category_id' => ArticleSearch::TERM_CATEGORY,
			'category_id_specific' => ArticleSearch::TERM_CATEGORY_SPECIFIC,
			'label' => ArticleSearch::TERM_LABEL,
			'new' => ArticleSearch::TERM_NEW,
			'popular' => ArticleSearch::TERM_POPULAR,
			'status' => ArticleSearch::TERM_STATUS
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
			$terms[] = array('type' => ArticleSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
				'date1' => $date_created_start,
				'date2' => $date_created_end
			));
		} else if ($date_created_start) {
			$terms[] = array('type' => ArticleSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
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

		$result_cache = $this->getApiSearchResult('article', $terms, $extra, $this->in->getUint('cache_id'), new ArticleSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($ids, $page, $per_page);
		$articles = App::getEntityRepository('DeskPRO:Article')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($ids),
			'cache_id' => $result_cache->id,
			'articles' => $this->getApiData($articles)
		));
	}

	public function newArticleAction()
	{
		$errors = array();
		$article = new Article();

		$lang_id = $this->in->getUint('language_id');
		$lang = null;
		if ($lang_id) {
			$lang = $this->container->getLanguageData()->get($lang_id);
		}
		if (!$lang) {
			$lang = $this->container->getLanguageData()->getDefault();
		}

		$set_title    = null;
		$set_content  = null;
		$title_lang   = array();
		$content_lang = array();

		if (is_array($_POST['title']) && is_array($_POST['content'])) {

			foreach ($this->container->getLanguageData()->getAll() as $lang) {
				$lang_id = $lang->getId();

				$title       = $this->in->getString("title.$lang_id");
				$content_val = (string)$this->in->getRaw("content.$lang_id");

				if ($lang_id == $article->language->getId()) {
					$set_title   = $title;
					$set_content = $content_val;
					continue;
				}

				if (!$title && !$content_val) {
					continue;
				}

				$title_lang[$lang->getId()] = $title;
				$content_lang[$lang->getId()] = $content_val;
			}

		} else {
			$set_title   = $this->in->getString('title');
			$set_content = (string)$this->in->getRaw('content');
		}

		if ($set_title) {
			$article->title = $set_title;
		} else {
			$errors['title'] = array('required_field.title', 'title is required');
		}

		if ($set_content) {
			$article->content = $set_content;
		} else {
			$errors['content'] = array('required_field.content', 'content is required');
		}

		$status = $this->in->getString('status');
		if (!$status) {
			$status = 'published';
		}
		$article->setStatusCode($status);

		$date = $this->in->getUint('date');
		if ($date) {
			$article->date_created = new \DateTime('@' . $date);
			if ($status == 'published') {
				$article->date_published = new \DateTime('@' . $date);
			}
		}

		if ($this->in->checkIsset('date_published') && $status != 'published') {
			$date_published = $this->in->getUint('date_published');
			if ($date_published) {
				$article->date_published = new \DateTime('@' . $date_published);
			}
		}

		$date_end = $this->in->getUint('date_end');
		if ($date_end) {
			$article->date_end = new \DateTime('@' . $date_end);
			$article->end_action = $this->in->getString('end_action') ?: Article::END_ACTION_DELETE;
		}

		$cat_ids = $this->in->getCleanValueArray('category_id', 'uint', 'discard');
		$cats = $this->em->getRepository('DeskPRO:ArticleCategory')->getByIds($cat_ids);
		if (!$cats) {
			$errors['category_id'] = array('invalid_argument.category_id', 'no categories found');
		}
		$article->setCategories($cats);

		$product_ids = $this->in->getCleanValueArray('product_id', 'uint', 'discard');
		$products = $this->em->getRepository('DeskPRO:Product')->getByIds($product_ids);
		if ($products) {
			$article->setProducts($products);
		}

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		$this->_insertArticleAttachments($article);
		$article->person = $this->person;

		$this->em->persist($article);
		$this->em->flush();

		$field_manager = $this->container->getSystemService('article_fields_manager');
		$post_custom_fields = $this->getCustomFieldInput();
		if (!empty($post_custom_fields)) {
			$field_manager->saveFormToObject($post_custom_fields, $article, true);
		}

		$labels = $this->in->getCleanValueArray('label', 'string', 'discard');
		if ($labels) {
			$article->getLabelManager()->setLabelsArray($labels, $this->em);
		}

		$this->em->flush();

		// Set other langs
		foreach ($title_lang as $lang_id => $title) {
			$lang = $this->container->getLanguageData()->get($lang_id);
			$content_val = $content_lang[$lang_id];

			$rec = $this->container->getObjectLangRepository()->setRec($lang, $article, 'title', $title);
			$this->em->persist($rec);

			$rec = $this->container->getObjectLangRepository()->setRec($lang, $article, 'content', $content_val);
			$this->em->persist($rec);
		}

		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $article->id),
			$this->generateUrl('api_kb_article', array('article_id' => $article->id), true)
		);
	}

	public function getArticleAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id);

		return $this->createApiResponse(array('article' => $article->toApiData()));
	}

	public function postArticleAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id, 'edit');

		$lang_id = $this->in->getUint('language_id');
		$lang = null;
		if ($lang_id) {
			$lang = $this->container->getLanguageData()->get($lang_id);
		}
		if ($lang) {
			$article->language = $lang;
		}

		$revs = array();

		if (is_array($_POST['title']) && is_array($_POST['content'])) {

			$set_title   = null;
			$set_content = null;

			foreach ($this->container->getLanguageData()->getAll() as $lang) {
				$lang_id = $lang->getId();

				$title       = $this->in->getString("title.$lang_id");
				$content_val = (string)$this->in->getRaw("content.$lang_id");

				if ($lang_id == $article->language->getId()) {
					$set_title   = $title;
					$set_content = $content_val;
					continue;
				}

				if (!$title && !$content_val) {
					continue;
				}

				$rec = $this->container->getObjectLangRepository()->setRec($lang, $article, 'title', $title);
				$this->em->persist($rec);

				$rec = $this->container->getObjectLangRepository()->setRec($lang, $article, 'content', $content_val);
				$this->em->persist($rec);
			}
		} else {
			$set_title   = $this->in->getString('title');
			$set_content = (string)$this->in->getRaw('content');
		}

		if ($set_title && $set_title != $article->title) {
			$article->title = $set_title;

			$rev = ContentRevisionUtil::findOrCreate($article, 'title', $this->person);
			$rev->title = $article->title;

			$revs['title'] = $rev;
		}

		if ($set_content && $set_content != $article->content) {
			$article->content = $set_content;

			$rev = ContentRevisionUtil::findOrCreate($article, array('content'), $this->person);
			$rev->content = $article->content;

			$revs['content'] = $rev;
		}

		$status = $this->in->getString('status');
		if ($status) {
			$article->setStatusCode($status);
		}

		$cat_ids = $this->in->getCleanValueArray('category_id', 'uint', 'discard');
		$cats = $this->em->getRepository('DeskPRO:ArticleCategory')->getByIds($cat_ids);
		if ($cats) {
			$article->setCategories($cats);
		}

		$product_ids = $this->in->getCleanValueArray('product_id', 'uint', 'discard');
		$products = $this->em->getRepository('DeskPRO:Product')->getByIds($product_ids);
		if ($products) {
			$article->setProducts($products);
		} else if ($this->in->getBool('remove_product')) {
			$article->setProducts(array());
		}

		if ($this->in->checkIsset('date_published') && $article->status != 'published') {
			$date_published = $this->in->getUint('date_published');
			if ($date_published) {
				$article->date_published = new \DateTime('@' . $date_published);
			} else {
				$article->date_published = null;
			}
		}

		if ($this->in->checkIsset('date_end')) {
			$date_end = $this->in->getUint('date_end');
			if ($date_end) {
				$article->date_end = new \DateTime('@' . $date_end);
				$article->end_action = $this->in->getString('end_action') ?: Article::END_ACTION_DELETE;
			} else {
				$article->date_end = null;
				$article->end_action = null;
			}
		}

		$this->_insertArticleAttachments($article);

		foreach ($revs AS $rev) {
			$this->em->persist($rev);
		}
		$this->em->persist($article);

		$field_manager = $this->container->getSystemService('article_fields_manager');
		$post_custom_fields = $this->getCustomFieldInput();
		if (!empty($post_custom_fields)) {
			$field_manager->saveFormToObject($post_custom_fields, $article, true);
		}

		$this->em->flush();

		return $this->createSuccessResponse();
	}

	protected function _insertArticleAttachments(Article $article)
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
				$this->_addArticleAttachment($blob, $article);
			}
		}

		foreach ($this->in->getCleanValueArray('attach_id') as $blob_id) {
			$this->_addArticleAttachment($blob_id, $article);
		}
	}

	protected function _addArticleAttachment($blob_id, Article $article)
	{
		if ($blob_id instanceof \Application\DeskPRO\Entity\Blob) {
			$blob = $blob_id;
		} else {
			$blob = $this->em->getRepository('DeskPRO:Blob')->find($blob_id);
		}

		if ($blob) {
			$attach = new \Application\DeskPRO\Entity\ArticleAttachment();
			$attach['blob'] = $blob;
			$attach['person'] = $this->person;

			$article->addAttachment($attach);

			return $attach;
		} else {
			return false;
		}
	}

	public function deleteArticleAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id, 'delete');

		$article->status_code = 'hidden.deleted';
		$this->em->persist($article);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getArticleVotesAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id);
		$votes = App::getEntityRepository('DeskPRO:Rating')->getRatingsFor('article', $article->id);

		return $this->createApiResponse(array('votes' => $this->getApiData($votes)));
	}

	public function getArticleCommentsAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id);
		$comments = $this->em->getRepository('DeskPRO:ArticleComment')->getComments($article);

		return $this->createApiResponse(array('comments' => $this->getApiData($comments)));
	}

	public function newArticleCommentAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id);

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

		$comment = new ArticleComment();
		$comment->article = $article;
		$comment->person = $person ?: $this->person;
		$comment['content'] = $content;
		$comment['status'] = $status ?: 'visible';
		$comment['is_reviewed'] = ($comment['status'] == 'visible' && !$person);
		$comment['date_created']  = new \DateTime();

		$this->em->persist($comment);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $comment->id),
			$this->generateUrl('api_kb_article_comments_comment', array('article_id' => $article->id, 'comment_id' => $comment->id), true)
		);
	}

	public function getArticleCommentAction($article_id, $comment_id)
	{
		$article = $this->_getArticleOr404($article_id);
		$comment = $this->em->getRepository('DeskPRO:ArticleComment')->find($comment_id);
		if (!$comment || $comment->article->id != $article->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		return $this->createApiResponse(array('comment' => $comment->toApiData()));
	}

	public function postArticleCommentAction($article_id, $comment_id)
	{
		$article = $this->_getArticleOr404($article_id);
		$comment = $this->em->getRepository('DeskPRO:ArticleComment')->find($comment_id);
		if (!$comment || $comment->article->id != $article->id) {
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

	public function deleteArticleCommentAction($article_id, $comment_id)
	{
		$article = $this->_getArticleOr404($article_id);
		$comment = $this->em->getRepository('DeskPRO:ArticleComment')->find($comment_id);
		if (!$comment || $comment->article->id != $article->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->remove($comment);
		$this->em->flush();

		$this->_sendCommentDeletedNotification($comment);

		return $this->createSuccessResponse();
	}

	public function getArticleAttachmentsAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id);

		return $this->createApiResponse(array('attachments' => $this->getApiData($article->attachments)));
	}

	public function newArticleAttachmentAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id, 'edit');

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

		$attach = $this->_addArticleAttachment($blob, $article);

		$this->em->persist($article);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $attach->id),
			$this->generateUrl('api_kb_article_attachment', array('article_id' => $article->id, 'attachment_id' => $attach->id), true)
		);
	}

	public function getArticleAttachmentAction($article_id, $attachment_id)
	{
		$article = $this->_getArticleOr404($article_id);
		$exists = false;
		foreach ($article->attachments AS $attachment) {
			if ($attachment->id == $attachment_id) {
				$exists = true;
				break;
			}
		}

		return $this->createApiResponse(array('exists' => $exists));
	}

	public function deleteArticleAttachmentAction($article_id, $attachment_id)
	{
		$article = $this->_getArticleOr404($article_id);
		foreach ($article->attachments AS $k => $attachment) {
			if ($attachment->id == $attachment_id) {
				$article->attachments->remove($k);
				$this->em->remove($attachment);
				break;
			}
		}

		$this->em->persist($article);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getArticleLabelsAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id);

		return $this->createApiResponse(array('labels' => $this->getApiData($article->labels)));
	}

	public function postArticleLabelsAction($article_id)
	{
		$article = $this->_getArticleOr404($article_id, 'edit');
		$label = $this->in->getString('label');

		if ($label === '') {
			return $this->createApiErrorResponse('required_field', "Field 'label' missing or empty");
		}

		$article->getLabelManager()->addLabel($label);
		$this->em->persist($article);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('label' => $label),
			$this->generateUrl('api_kb_article_label', array('article_id' => $article->id, 'label' => $label), true)
		);
	}

	public function getArticleLabelAction($article_id, $label)
	{
		$article = $this->_getArticleOr404($article_id);

		if ($article->getLabelManager()->hasLabel($label)) {
			return $this->createApiResponse(array('exists' => true));
		} else {
			return $this->createApiResponse(array('exists' => false));
		}
	}

	public function deleteArticleLabelAction($article_id, $label)
	{
		$article = $this->_getArticleOr404($article_id, 'edit');

		$article->getLabelManager()->removeLabel($label);
		$this->em->persist($article);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getValidatingCommentsAction()
	{
		$comments = $this->em->getRepository('DeskPRO:ArticleComment')->getValidatingComments();
		$entity_key = 'article';
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
		$categories = $this->em->getRepository('DeskPRO:ArticleCategory')->getFlatHierarchy();

		return $this->createApiResponse(array('categories' => $categories));
	}

	public function postCategoriesAction()
	{
		$errors = array();

		$title = $this->in->getString('title');
		if (!$title) {
			$errors['title'] = array('required_field.title', 'title empty or missing');
		}

		$category = new \Application\DeskPRO\Entity\ArticleCategory();

		$category->title = $title;

		$parent_id = $this->in->getUint('parent_id');
		if ($parent_id) {
			$parent = $this->em->getRepository('DeskPRO:ArticleCategory')->find($parent_id);
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
				App::getDb()->insert('article_category2usergroup', array(
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
			$this->generateUrl('api_kb_category', array('category_id' => $category->id), true)
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
				$parent = $this->em->getRepository('DeskPRO:ArticleCategory')->find($parent_id);
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
			\Application\DeskPRO\Publish\CategoryEdit::deleteCategory('articles', $category_id);
		} catch (\OutOfBoundsException $e) {
			return $this->createApiErrorResponse('invalid_argument.category_id', 'category is not empty');
		}

		return $this->createSuccessResponse();
	}

	public function getCategoryArticlesAction($category_id)
	{
		$category = $this->_getCategoryOr404($category_id);

		$terms = array(
			array('type' => ArticleSearch::TERM_CATEGORY_SPECIFIC, 'op' => 'contains', 'options' => array($category->id))
		);

		$order_by = $this->in->getString('order');
		if (!$order_by) {
			$order_by = 'date:desc';
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('article', $terms, $extra, $this->in->getUint('cache_id'), new ArticleSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($ids, $page, $per_page);
		$articles = App::getEntityRepository('DeskPRO:Article')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($ids),
			'cache_id' => $result_cache->id,
			'articles' => $this->getApiData($articles)
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
			$this->db->insert('article_category2usergroup', array(
				'category_id' => $category->id,
				'usergroup_id' => $group_id
			));
		}

		return $this->createApiCreateResponse(
			array('id' => $group_id),
			$this->generateUrl('api_kb_category_group', array('category_id' => $category->id, 'group_id' => $group_id), true)
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

	public function getFieldsAction()
	{
		$field_manager = $this->container->getSystemService('article_fields_manager');
		$fields = $field_manager->getFields();

		return $this->createApiResponse(array('fields' => $this->getApiData($fields)));
	}

	public function getProductsAction()
	{
		$products = $this->em->getRepository('DeskPRO:Product')->getFlatHierarchy();

		return $this->createApiResponse(array('products' => $products));
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\Article
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getArticleOr404($id, $check_perm = false)
	{
		$article = $this->em->getRepository('DeskPRO:Article')->findOneById($id);

		if (!$article) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no article with ID $id");
		}

		if ($check_perm) {
			if ($check_perm == 'edit' && !$this->person->PermissionsManager->PublishChecker->canEdit($article)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}

			if ($check_perm == 'delete' && !$this->person->PermissionsManager->PublishChecker->canDelete($article)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
		}

		return $article;
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\ArticleCategory
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getCategoryOr404($id)
	{
		$category = $this->em->getRepository('DeskPRO:ArticleCategory')->find($id);

		if (!$category) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no category with ID $id");
		}

		return $category;
	}
}
