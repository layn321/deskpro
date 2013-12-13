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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Application\AdminBundle\Form\EditTicketCategoryType;

/**
 * Managing ticket categories
 */
class TicketCategoriesController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	/**
	 * Shows the main listing of departments
	 */
	public function listAction()
	{
		$all_categories = $this->em->createQuery("
			SELECT c
			FROM DeskPRO:TicketCategory c
			WHERE c.parent IS NULL
			ORDER BY c.display_order ASC
		")->getResult();

		return $this->render('AdminBundle:TicketCategories:list.html.twig', array(
			'all_categories' => $all_categories
		));
	}



	############################################################################
	# edit
	############################################################################

	public function saveTitleAction()
	{
		$category_id = $this->in->getUint('category_id');
		$category = $this->em->find('DeskPRO:TicketCategory', $category_id);

		if (!$category) {
			throw $this->createNotFoundException();
		}

		if ($this->in->getString('title')) {
			$category->title = $this->in->getString('title');
		}

		$parent_id = $this->in->getUint('parent_id');
		if (!count($category->getChildren())) {
			if (!$parent_id || $parent_id == $category->getId()) {
				$category->parent = null;
			} else {
				$parent_cat = $this->em->find('DeskPRO:TicketCategory', $parent_id);
				if ($parent_cat && !count($parent_cat->parent)) {
					$category->parent = $parent_cat;
				}
			}
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($category);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_ticketcats');
	}

	public function saveNewAction()
	{
		$category = new \Application\DeskPRO\Entity\TicketCategory();
		$category->title = $this->in->getString('title');

		if (!$category->title) {
			$category->title = 'Untitled';
		}

		$parent = null;
		if ($this->in->getUint('parent_id')) {
			$parent = $this->em->find('DeskPRO:TicketCategory', $this->in->getUint('parent_id'));
		}

		if ($parent and !$parent->parent) {
			$category->parent = $parent;
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($category);
			$this->em->flush();

			// First category: enable the feature
			$count = $this->db->fetchColumn("SELECT COUNT(*) FROM ticket_categories");
			if ($count == 1) {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_category', '1');
			}

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.task_completed_add_ticketcategory', time());

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_ticketcats');
	}

	############################################################################
	# delete
	############################################################################

	public function deleteAction($category_id)
	{
		$category = $this->em->getRepository('DeskPRO:TicketCategory')->find($category_id);

		return $this->render('AdminBundle:TicketCategories:delete.html.twig', array(
			'category'  => $category,
		));
	}

	public function doDeleteAction($category_id, $security_token)
	{
		$category = $this->em->getRepository('DeskPRO:TicketCategory')->find($category_id);

		if (!$category) {
			return $this->redirectRoute('admin_ticketcats');
		}

		if (!$this->session->getEntity()->checkSecurityToken('delete_ticket_category', $security_token)) {
			return $this->renderStandardTokenError();
		}

		$this->em->beginTransaction();
		foreach ($category->children as $c) {
			$this->em->remove($c);
		}
		$this->em->remove($category);
		$this->em->flush();

		$count = $this->db->fetchColumn("SELECT COUNT(*) FROM ticket_categories");
		if (!$count) {
			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_category', '0');
		}

		$this->em->commit();

		$this->sendAgentReloadSignal();

		$this->session->setFlash('deleted', $category->title);
		return $this->redirectRoute('admin_ticketcats');
	}

	############################################################################
	# update-orders
	############################################################################

	public function updateOrdersAction()
	{
		$helper = new \Application\AdminBundle\Controller\Helper\DisplayOrderUpdate($this);
		return $helper->doUpdate('ticket_categories');
	}


	############################################################################
	# toggle-feature
	############################################################################

	public function toggleFeatureAction($enable)
	{
		if ($enable) {
			$count = $this->db->fetchColumn("SELECT COUNT(*) FROM ticket_categories");
			if (!$count) {
				return $this->redirectRoute('admin_ticketcats');
			}

			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_category', '1');
		} else {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_category', '0');
		}

		$url = $this->generateUrl('admin_ticketcats');
		if ($this->in->getString('return')) {
			$url = $this->in->getString('return');
		}

		$this->sendAgentReloadSignal();

		return $this->redirect($url);
	}

	############################################################################
	# set-default
	############################################################################

	public function setDefaultAction()
	{
		$default_id = $this->in->getUint('default_value');

		if ($default_id) {
			// Verify
			$obj = $this->em->getRepository('DeskPRO:TicketCategory')->find($default_id);
			if (!$obj || count($obj->children)) {
				$default_id = 0;
			}
		}

		$this->container->getSettingsHandler()->setSetting('core.default_ticket_cat', $default_id);
		return $this->redirectRoute('admin_ticketcats');
	}
}
