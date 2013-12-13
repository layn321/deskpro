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
use Application\DeskPRO\Entity\TicketMessageTemplate;
use Application\AdminBundle\Form\TicketMessageTemplateType;

class TicketMessageTemplatesController extends AbstractController
{
	####################################################################################################################
	# index
	####################################################################################################################

	public function indexAction()
	{
		return $this->redirectRoute('admin_features');
	}


	####################################################################################################################
	# edit
	####################################################################################################################

	public function editAction($id)
	{
		if ($id) {
			$message_template = $this->em->find('DeskPRO:TicketMessageTemplate', $id);
			if (!$message_template) {
				throw $this->createNotFoundException();
			}
		} else {
			$message_template = new TicketMessageTemplate();
		}

		$form = $this->get('form.factory')->create(new TicketMessageTemplateType(), $message_template);

		if ($this->request->isPost()) {
			$form->bindRequest($this->get('request'));

			$this->db->beginTransaction();
			try {
				$this->em->persist($message_template);
				$this->em->flush();
				$this->db->commit();
			} catch (\Exception $e) {
				$this->db->rollback();
				throw $e;
			}

			$this->sendAgentReloadSignal();
			return $this->redirectRoute('admin_features');
		}

		return $this->render('AdminBundle:TicketMessageTemplates:edit.html.twig', array(
			'message_template' => $message_template,
			'form' => $form->createView(),
		));
	}

	####################################################################################################################
	# delete
	####################################################################################################################

	public function deleteAction($id, $security_token)
	{
		$message_template = $this->em->find('DeskPRO:TicketMessageTemplate', $id);

		if (!$this->session->getEntity()->checkSecurityToken('delete_ticket_message_template', $security_token)) {
			return $this->renderStandardTokenError();
		}

		$this->em->remove($message_template);
		$this->em->flush();

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_features');
	}
}
