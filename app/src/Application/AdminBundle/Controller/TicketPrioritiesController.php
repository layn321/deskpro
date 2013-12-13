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

use Application\AdminBundle\Form\EditTicketPriorityType;

/**
 * Simple management of priorities
 */
class TicketPrioritiesController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	/**
	 * Shows the main listing of priorities
	 */
	public function listAction()
	{
		$all_priorities = $this->em->createQuery("
			SELECT p
			FROM DeskPRO:TicketPriority p
			ORDER BY p.priority ASC
		")->execute();

		return $this->render('AdminBundle:TicketPriorities:list.html.twig', array(
			'all_priorities' => $all_priorities
		));
	}



	############################################################################
	# edit
	############################################################################

	public function saveTitleAction()
	{
		$priority_id = $this->in->getUint('priority_id');
		$priority = $this->em->find('DeskPRO:TicketPriority', $priority_id);

		if (!$priority) {
			throw $this->createNotFoundException();
		}

		if ($this->in->getString('title')) {
			$priority->title = $this->in->getString('title');
		}

		$priority->priority = $this->in->getUint('priority');

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($priority);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_ticketpris');
	}

	public function saveNewAction()
	{
		$priority = new \Application\DeskPRO\Entity\TicketPriority();
		$priority->title = $this->in->getString('title');

		if (!$priority->title) {
			$priority->title = 'Untitled';
		}

		$priority->priority = $this->in->getUint('priority');

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($priority);
			$this->em->flush();

			// First priority: enable the feature
			$count = $this->db->fetchColumn("SELECT COUNT(*) FROM ticket_priorities");
			if ($count == 1) {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_priority', '1');
			}

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.task_completed_add_ticketpriority', time());

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_ticketpris');
	}

	############################################################################
	# delete
	############################################################################

	public function deleteAction($priority_id)
	{
		$priority = $this->em->getRepository('DeskPRO:TicketPriority')->find($priority_id);

		return $this->render('AdminBundle:TicketPriorities:delete.html.twig', array(
			'priority'  => $priority,
		));
	}

	public function doDeleteAction($priority_id, $security_token)
	{
		$priority = $this->em->getRepository('DeskPRO:TicketPriority')->find($priority_id);

		if (!$this->session->getEntity()->checkSecurityToken('delete_ticket_priority', $security_token)) {
			return $this->renderStandardTokenError();
		}

		$this->em->beginTransaction();
		$this->em->remove($priority);
		$this->em->flush();

		$count = $this->db->fetchColumn("SELECT COUNT(*) FROM ticket_priorities");
		if (!$count) {
			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_priority', '0');
		}

		$this->em->commit();

		$this->sendAgentReloadSignal();

		$this->session->setFlash('deleted', $priority->title);
		return $this->redirectRoute('admin_ticketpris');
	}


	############################################################################
	# toggle-feature
	############################################################################

	public function toggleFeatureAction($enable)
	{
		if ($enable) {
			$count = $this->db->fetchColumn("SELECT COUNT(*) FROM ticket_priorities");
			if (!$count) {
				return $this->redirectRoute('admin_ticketpris');
			}

			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_priority', '1');
		} else {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_priority', '0');
		}

		$url = $this->generateUrl('admin_ticketpris');
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
			$obj = $this->em->getRepository('DeskPRO:TicketPriority')->find($default_id);
			if (!$obj) {
				$default_id = 0;
			}
		}

		$this->container->getSettingsHandler()->setSetting('core.default_ticket_pri', $default_id);
		return $this->redirectRoute('admin_ticketpris');
	}
}
