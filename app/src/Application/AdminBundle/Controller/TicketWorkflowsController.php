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

use Application\AdminBundle\Form\EditTicketWorkflowType;

/**
 * Simple management of workflows
 */
class TicketWorkflowsController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	/**
	 * Shows the main listing of workflows
	 */
	public function listAction()
	{
		$all_workflows = $this->em->createQuery("
			SELECT w
			FROM DeskPRO:TicketWorkflow w
			ORDER BY w.display_order ASC
		")->execute();

		return $this->render('AdminBundle:TicketWorkflows:list.html.twig', array(
			'all_workflows' => $all_workflows
		));
	}



	############################################################################
	# edit
	############################################################################

	public function saveTitleAction()
	{
		$workflow_id = $this->in->getUint('workflow_id');
		$workflow = $this->em->find('DeskPRO:TicketWorkflow', $workflow_id);

		if (!$workflow) {
			throw $this->createNotFoundException();
		}

		if ($this->in->getString('title')) {
			$workflow->title = $this->in->getString('title');
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($workflow);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_ticketworks');
	}

	public function saveNewAction()
	{
		$workflow = new \Application\DeskPRO\Entity\TicketWorkflow();
		$workflow->title = $this->in->getString('title');

		if (!$workflow->title) {
			$workflow->title = 'Untitled';
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($workflow);
			$this->em->flush();

			// First workflow: enable the feature
			$count = $this->db->fetchColumn("SELECT COUNT(*) FROM ticket_workflows");
			if ($count == 1) {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_workflow', '1');
			}

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_ticketworks');
	}

	############################################################################
	# delete
	############################################################################

	public function deleteAction($workflow_id)
	{
		$workflow = $this->em->getRepository('DeskPRO:TicketWorkflow')->find($workflow_id);

		return $this->render('AdminBundle:TicketWorkflows:delete.html.twig', array(
			'workflow'  => $workflow,
		));
	}

	public function doDeleteAction($workflow_id, $security_token)
	{
		$workflow = $this->em->getRepository('DeskPRO:TicketWorkflow')->find($workflow_id);

		if (!$this->session->getEntity()->checkSecurityToken('delete_workflow', $security_token)) {
			return $this->renderStandardTokenError();
		}

		$this->em->beginTransaction();
		$this->em->remove($workflow);
		$this->em->flush();

		$count = $this->db->fetchColumn("SELECT COUNT(*) FROM ticket_workflows");
		if (!$count) {
			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_workflow', '0');
		}

		$this->em->commit();

		$this->sendAgentReloadSignal();

		$this->session->setFlash('deleted', $workflow->title);
		return $this->redirectRoute('admin_ticketworks');
	}

	############################################################################
	# update-orders
	############################################################################

	public function updateOrdersAction()
	{
		$helper = new \Application\AdminBundle\Controller\Helper\DisplayOrderUpdate($this);
		return $helper->doUpdate('ticket_workflows');
	}


	############################################################################
	# toggle-feature
	############################################################################

	public function toggleFeatureAction($enable)
	{
		if ($enable) {
			$count = $this->db->fetchColumn("SELECT COUNT(*) FROM ticket_workflows");
			if (!$count) {
				return $this->redirectRoute('admin_ticketworks');
			}

			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_workflow', '1');
		} else {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_ticket_workflow', '0');
		}

		$url = $this->generateUrl('admin_ticketworks');
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
			$obj = $this->em->getRepository('DeskPRO:TicketWorkflow')->find($default_id);
			if (!$obj) {
				$default_id = 0;
			}
		}

		$this->container->getSettingsHandler()->setSetting('core.default_ticket_work', $default_id);
		return $this->redirectRoute('admin_ticketworks');
	}
}
