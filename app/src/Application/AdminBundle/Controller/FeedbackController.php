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

use Application\DeskPRO\Entity\FeedbackStatusCategory;
use Application\DeskPRO\Entity\FeedbackCategory;
use Application\AdminBundle\Form\EditFeedbackCategoryType;
use Orb\Util\Arrays;

class FeedbackController extends AbstractController
{
	############################################################################
	# statuses
	############################################################################

	public function statusesAction()
	{
		$active_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getActiveCategories();
		$closed_cats = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->getClosedCategories();

		return $this->render('AdminBundle:Feedback:statuses.html.twig', array(
			'active_cats' => $active_cats,
			'closed_cats' => $closed_cats
		));
	}

	public function updateStatusOrdersAction()
	{
		$helper = new \Application\AdminBundle\Controller\Helper\DisplayOrderUpdate($this);
		return $helper->doUpdate('feedback_status_categories');
	}

	public function ajaxNewStatusAction()
	{
		$title = $this->in->getString('cat.title');
		$type = $this->in->getString('cat.status_type');

		if (!in_array($type, array('active', 'closed'))) {
			$type = 'active';
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$cat = new FeedbackStatusCategory();
			$cat->title = $title;
			$cat->status_type = $type;
			$cat->display_order = 9999;

			$this->em->persist($cat);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->render('AdminBundle:Feedback:statuses-row.html.twig', array('cat' => $cat));
	}

	public function editStatusAction($category_id)
	{
		$cat = $this->getStatusOr404($category_id);

		if ($this->getRequest()->getMethod() == 'POST') {
			$cat->title = $this->in->getString('cat.title');
			$this->em->persist($cat);
			$this->em->flush();
		}

		return $this->render('AdminBundle:Feedback:status-edit.html.twig', array(
			'cat' => $cat,
		));
	}

	public function deleteStatusAction($category_id)
	{
		$cat = $this->getStatusOr404($category_id);
		$count_existing = $this->em->getRepository('DeskPRO:Feedback')->countInStatusCategory($cat);

		$this->em->getConnection()->beginTransaction();

		try {

			if ($count_existing) {
				$move_cat = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->find($this->in->getUint('move_to_cat'));
				if (!$move_cat) {
					$this->em->createQuery("
						SELECT c
						FROM DeskPRO:FeedbackStatusCategory c
						WHERE c.status_type = ?1 AND c != ?2
						ORDER BY c.id ASC
					")->setMaxResults(1)
					  ->setParameter(1, $cat->status_type)
					  ->setParameter(2, $cat)
					  ->getOneOrNullResult();
				}

				if (!$move_cat) {
					return $this->renderStandardError("You did not specify a status to move existing feedback into.");
				}

				$this->db->update('feedback', array('status_category_id' => $move_cat->id), array('status_category_id' => $cat->id));
			}

			$this->em->remove($cat);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_feedback_statuses');
	}

	/**
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getStatusOr404($id)
	{
		$cat = $this->em->find('DeskPRO:FeedbackStatusCategory', $id);
		if (!$cat) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no status with ID $id");
		}

		return $cat;
	}



	############################################################################
	# categories
	############################################################################

	public function categoriesAction()
	{
		$all_categories = $this->em->createQuery("
			SELECT c
			FROM DeskPRO:FeedbackCategory c
			WHERE c.parent IS NULL
			ORDER BY c.display_order ASC
		")->getResult();

		return $this->render('AdminBundle:Feedback:cats.html.twig', array(
			'all_categories' => $all_categories
		));
	}

	public function editCategoryAction($category_id)
	{
		if (!$category_id) {
			$category = new FeedbackCategory();
		} else {
			$category = $this->em->getRepository('DeskPRO:FeedbackCategory')->find($category_id);
		}

		$usergroups = $this->em->getRepository('DeskPRO:Usergroup')->getUsergroupNames();
		Arrays::unshiftAssoc($usergroups, '1', 'Everyone');
		$enabled_groups = array();
		if ($category_id) {
			$enabled_groups = $this->container->getDb()->fetchAllCol("SELECT usergroup_id FROM feedback_category2usergroup WHERE category_id = ?", array($category_id));
		}

		$form = $this->get('form.factory')->create(new EditFeedbackCategoryType($category->id ? false : true), $category);

		if ($this->in->getBool('process')) {

			$do_move = false;
			if (!$category_id && $this->in->getUint('feedback_cat.parent')) {
				$do_move = $this->db->fetchColumn("SELECT COUNT(*) FROM feedback_categories c WHERE c.parent_id = ?", array($this->in->getUint('feedback_cat.parent')));
			}

			$form->bindRequest($this->get('request'));

			$this->em->getConnection()->beginTransaction();

			try {
				$this->em->persist($category);
				$this->em->flush();

				if ($do_move) {
					$this->db->update('feedback', array('category_id' => $category->id), array('category_id' => $category->parent->id));
				}

				$this->em->getRepository('DeskPRO:FeedbackCategory')->repair();

				$this->container->getDb()->delete('feedback_category2usergroup', array('category_id' => $category->getId()));
				$vals = array();
				$uids = $this->container->getIn()->getCleanValueArray('usergroups', 'uint', 'discard');
				$uids = array_unique($uids);
				foreach ($uids as $uid) {
					if (isset($usergroups[$uid])) {
						$vals[] = array('category_id' => $category->getId(), 'usergroup_id' => $uid);
					}
				}

				if ($vals) {
					$this->container->getDb()->batchInsert('feedback_category2usergroup', $vals);
				}

				$this->em->getConnection()->commit();
			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}

			$this->session->setFlash('saved', $category->title);
			return $this->redirectRoute('admin_feedback_cats');
		}

		$other_cats = $this->em->getRepository('DeskPRO:FeedbackCategory')->getInHierarchy();
		$exclude_cat_ids = $this->em->getRepository('DeskPRO:FeedbackCategory')->getChildrenIds($category, false);
		$exclude_cat_ids[] = $category->id;

		$filter_fn = function($c) use ($exclude_cat_ids, &$filter_fn) {
			if (in_array($c['id'], $exclude_cat_ids)) {
				return false;
			}

			return true;
		};

		$trav_filter_fn = function(&$cats) use ($exclude_cat_ids, &$trav_filter_fn, &$filter_fn) {
			$cats = array_filter($cats, $filter_fn);

			foreach ($cats as &$v) {
				if ($v['children']) {
					$trav_filter_fn($v['children']);
				}
			}
		};
		$trav_filter_fn($other_cats);

		$count_existing = $this->em->getRepository('DeskPRO:Feedback')->countInCategory($category);

		if (!$category_id) {
			$leaf_ids = $this->em->getRepository('DeskPRO:FeedbackCategory')->getLeafIds();
		} else {
			$leaf_ids = array();
		}

		return $this->render('AdminBundle:Feedback:cats-edit.html.twig', array(
			'category'        => $category,
			'form'            => $form->createView(),
			'count_existing'  => $count_existing,
			'other_cats'      => $other_cats,
			'leaf_ids'        => $leaf_ids,
			'usergroups'      => $usergroups,
			'enabled_groups'  => $enabled_groups
		));
	}

	public function deleteCategoryAction($category_id)
	{
		$category = $this->em->getRepository('DeskPRO:FeedbackCategory')->find($category_id);

		$count_existing = $this->em->getRepository('DeskPRO:Feedback')->countInCategory($category);

		$move_cat = null;
		if ($count_existing) {
			$move_cat = $this->em->getRepository('DeskPRO:FeedbackStatusCategory')->find($this->in->getUint('move_to_cat'));
			if (!$move_cat) {
				$this->em->createQuery("
					SELECT c
					FROM DeskPRO:FeedbackCategory c
					WHERE c != ?2
					ORDER BY c.id ASC
				")->setMaxResults(1)
				  ->setParameter(1, $category)
				  ->getOneOrNullResult();
			}

			if (!$move_cat) {
				return $this->renderStandardError("You did not specify a category to move existing feedback into.");
			}
		}

		$this->em->getConnection()->beginTransaction();
		try {

			if ($move_cat) {
				$this->db->update('feedback', array('category_id' => $move_cat->id), array('category_id' => $category->id));
			}

			foreach ($category->children as $c) {
				$this->em->remove($c);
			}
			$this->em->remove($category);
			$this->em->flush();

			$this->em->getRepository('DeskPRO:FeedbackCategory')->repair();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->session->setFlash('deleted', $category->title);
		return $this->redirectRoute('admin_feedback_cats');
	}

	public function updateCategoryOrdersAction()
	{
		$helper = new \Application\AdminBundle\Controller\Helper\DisplayOrderUpdate($this);
		return $helper->doUpdate('feedback_categories');
	}

	############################################################################
	# user-category
	############################################################################

	public function userCategoryAction()
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

		$choices_structure = array();
		if ($field['handler_class'] == 'Application\\DeskPRO\\CustomFields\\Handler\\Choice') {
			$choices = array();
			foreach ($field->children as $child) {
				$choices[$child->getId()] = $child;
				$choices_structure[] = array(
					'id' => $child->getId(),
					'title' => $child->getTitle(),
					'parent_id' => $child->getOption('parent_id', 0)
				);
			}

			usort($choices_structure, function($a, $b) use ($choices) {
				$f1 = $choices[$a['id']];
				$f2 = $choices[$b['id']];

				if ($f1->getDisplayOrder() == $f2->getDisplayOrder()) {
					return 0;
				}

				return $f1->getDisplayOrder() < $f2->getDisplayOrder() ? -1 : 1;
			});
		}

		$cat_ids = $this->db->fetchAllCol("SELECT id FROM custom_def_feedback WHERE parent_id = 1");
		$counts  = array();
		if ($cat_ids) {
			$counts = $this->container->getDb()->fetchAllKeyValue("
				SELECT field_id, COUNT(*)
				FROM custom_data_feedback
				WHERE field_id IN (" . implode(',', $cat_ids) . ")
				GROUP BY field_id
			");
		}

		$counts = Arrays::castToType($counts, 'int', 'int');

		$vars = array(
			'field' => $field,
			'choices_structure' => $choices_structure,
			'cat_counts' => $counts
		);

		return $this->render('AdminBundle:Feedback:user-categories.html.twig', $vars);
	}
}
