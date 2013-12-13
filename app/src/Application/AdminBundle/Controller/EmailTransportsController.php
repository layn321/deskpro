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

use Application\AdminBundle\Form\EditEmailTransport as EditEmailTransportForm;
use Application\AdminBundle\FormModel\EditEmailTransport as EditEmailTransportModel;

class EmailTransportsController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	public function listAction()
	{
		$all_transports = $this->em->getRepository('DeskPRO:EmailTransport')->findAll();

		if (!count($all_transports)) {
			return $this->redirectRoute('admin_emailtrans_setup');
		}

		return $this->render('@list.html.twig', array(
			'transports' => $all_transports,
		));
	}

	############################################################################
	# setup
	############################################################################

	public function setupAction()
	{
		$transport = $this->em->createQuery("
			SELECT t
			FROM DeskPRO:EmailTransport t
			WHERE t.match_type = 'all'
		")->getOneOrNullResult();

		if (!$transport) {
			$transport = new \Application\DeskPRO\Entity\EmailTransport();
			$transport->match_type = 'all';
			$transport->match_pattern = '*';
		}

		$edittrans = new EditEmailTransportModel($transport);
		$edittrans->match_type = 'all';
		$edittrans->match_pattern = '*';
		$form = $this->get('form.factory')->create(new EditEmailTransportForm(), $edittrans);

		return $this->render('@setup.html.twig', array(
			'transport' => $transport,
			'form' => $form->createView(),
			'edittrans' => $edittrans,
		));
	}

	############################################################################
	# edit
	############################################################################

	public function editAccountAction($id)
	{
		if ($id) {
			$transport = $this->em->find('DeskPRO:EmailTransport', $id);
			if (!$transport) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
		} else {
			$transport = new \Application\DeskPRO\Entity\EmailTransport();
		}

		$edittrans = new EditEmailTransportModel($transport);
		$form = $this->get('form.factory')->create(new EditEmailTransportForm(), $edittrans);

		if ($this->request->isPost()) {
			$this->ensureRequestToken('edit_transport');
			$form->bindRequest($this->get('request'));

			if ($form->isValid()) {

				$this->em->getConnection()->beginTransaction();
				try {
					$edittrans->save();
					$this->em->getConnection()->commit();
				} catch (\Exception $e) {
					$this->em->getConnection()->rollback();
					throw $e;
				}

				if ($transport->match_type == 'all') {

					// 'All' must always be singular
					$this->container->getDb()->executeUpdate("
						DELETE FROM email_transports
						WHERE match_type = 'all' AND id != ?
					", array($transport->getId()));

					$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.default_from_email', $this->in->getString('default_from_email'));
				}

				if ($this->request->isXmlHttpRequest()) {
					return $this->createJsonResponse(array(
						'success' => true,
						'transport_id' => $transport->id,
						'title' => $transport->title
					));
				}

				$this->session->setFlash('saved', $transport->title);
				return $this->redirectRoute('admin_emailgateways');
			}
		}

		$tpl = '@edit-account.html.twig';
		if ($this->request->isPartialRequest()) {
			$tpl = '@edit-account-form.html.twig';
		}

		$default_php_mail = $this->container->getSysConfig('instance_data.install_flags.default_php_mail');

		return $this->render($tpl, array(
			'transport' => $transport,
			'form' => $form->createView(),
			'edittrans' => $edittrans,
			'partial' => $this->request->isPartialRequest(),
			'default_php_mail' => $default_php_mail,
		));
	}

	############################################################################
	# ajax-test
	############################################################################

	public function ajaxTestAction()
	{
		$transport = new \Application\DeskPRO\Entity\EmailTransport();

		$edittrans = new EditEmailTransportModel($transport);
		$form = $this->get('form.factory')->create(new EditEmailTransportForm(), $edittrans);
		$form->bindRequest($this->get('request'));
		$edittrans->apply();

		try {
			$tr = $transport->getTransport();

			$this->container->getSettingsHandler()->setTemporarySettingValues(array('core.default_from_email' => $this->in->getString('send_from')));

			$message = $this->container->getMailer()->createMessage();
			$message->setTo($this->in->getString('send_to'));
			$message->setFrom($this->in->getString('send_from'));
			$message->setTemplate('DeskPRO:emails_agent:test-email.html.twig');
			$message->setForceTransport($tr);

			$failed = array();
			$this->container->getMailer()->sendNow($message, $failed);

			if ($failed) {
				return $this->createJsonResponse(array(
					'error' => true,
					'error_code' => 'dp_1',
					'error_message' => 'Connection succeeded, but the server was unable or unwilling to deliver the test email to ' . $this->in->getString('send_to'),
					'log' => implode("\n", $this->container->getMailer()->getLogMessages())
				));
			}
		} catch (\Exception $e) {
			return $this->createJsonResponse(array(
				'error' => true,
				'error_code' => $e->getCode(),
				'error_message' => $e->getMessage(),
				'log' => implode("\n", $this->container->getMailer()->getLogMessages())
			));
		}

		return $this->createJsonResponse(array('success' => true));
	}

	############################################################################
	# delete
	############################################################################

	public function deleteAction($id, $security_token)
	{
		$transport = $this->em->find('DeskPRO:EmailTransport', $id);
		if (!$transport || !$this->session->checkSecurityToken('delete_transport', $security_token)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->remove($transport);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->session->setFlash('deleted', "SMTP Account");

		return $this->redirectRoute('admin_emailgateways');
	}

	############################################################################
	# set-default-from
	############################################################################

	public function setDefaultFromAction()
	{
		$this->ensureRequestToken('admin_update_from');

		$default_from = $this->in->getString('default_from');
		if (!\Orb\Validator\StringEmail::isValueValid($default_from)) {
			return $this->redirectRoute('admin_emailtrans_list');
		}

		$this->settings->setSetting('core.default_from_email', $default_from);

		return $this->redirectRoute('admin_emailtrans_list');
	}
}
