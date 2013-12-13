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

namespace Cloud\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Orb\Util\Arrays;

use Application\AdminBundle\Controller\EmailGatewaysController as BaseEmailGatewaysController;
use Application\DeskPRO\Entity\EmailGatewayAddress;
use Application\DeskPRO\Entity\EmailGateway;
use Application\DeskPRO\Entity\EmailTransport;

use Application\AdminBundle\Form\EditEmailTransport as EditEmailTransportForm;
use Application\AdminBundle\FormModel\EditEmailTransport as EditEmailTransportModel;

class EmailGatewaysController extends BaseEmailGatewaysController
{
	############################################################################
	# list
	############################################################################

	public function listAction()
	{
		$all_gateways = $this->em->createQuery("
			SELECT g
			FROM DeskPRO:EmailGateway g
			WHERE g.gateway_type = 'tickets'
			ORDER BY g.title ASC
		")->getResult();

		$helpdesk_emails = explode(',', $this->container->getSetting('core.helpdesk_emails'));
		$helpdesk_emails = Arrays::removeFalsey($helpdesk_emails);

		$rejection_count = $this->em->getRepository('DeskPRO:EmailSource')->countRejectionStatus(array('ticket', 'ticketmessage'));

		return $this->render('@list.html.twig', array(
			'all_gateways' => $all_gateways,
			'helpdesk_emails' => $helpdesk_emails,
			'rejection_count' => $rejection_count,
		));
	}


	############################################################################
	# new-cloud-email
	############################################################################

	public function newCloudEmailAction()
	{
		$name = $this->in->getString('name');

		if (!$name) {
			return $this->createJsonResponse(array('error' => true, 'error_code' => 'empty_name'));
		}

		if (!preg_match('#^[a-zA-Z0-9]+[a-zA-Z0-9\-_]*(?<=[a-zA-Z0-9])$#', $name)) {
			return $this->createJsonResponse(array('error' => true, 'error_code' => 'invalid_name'));
		}

		$dupe = $this->db->fetchColumn("SELECT id FROM email_gateway_addresses WHERE match_pattern = ? LIMIT 1", array($name . '@' . DPC_SITE_DOMAIN));
		if ($dupe) {
			return $this->createJsonResponse(array('error' => true, 'error_code' => 'dupe'));
		}

		$address                     = new EmailGatewayAddress();
		$address->match_type         = 'exact';
		$address->match_pattern      = sprintf("%s@%s", $name, DPC_SITE_DOMAIN);

		$gateway                     = new EmailGateway();
		$gateway->title              = $address->match_pattern;
		$gateway->connection_type    = EmailGateway::CONN_READDIR;
		$gateway->connection_options = array(
			'dir' => '%DP_DATA_DIR%/emailstore/' . strtolower($address->match_pattern)
		);
		$gateway->gateway_type       = EmailGateway::GATEWAY_TICKETS;

		$transport                   = new EmailTransport();
		$transport->title            = $address->match_pattern;
		$transport->match_type       = 'exact';
		$transport->match_pattern    = $address->match_pattern;
		$transport->transport_type   = 'mail';

		$gateway->addresses->add($address);
		$gateway->linked_transport = $transport;
		$address->gateway = $gateway;

		$this->em->persist($transport);
		$this->em->persist($address);
		$this->em->persist($gateway);

		$this->db->beginTransaction();
		try {
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createJsonResponse(array(
			'success' => true,
			'id'      => $gateway->id,
			'title'   => $gateway->title
		));
	}


	############################################################################
	# delete
	############################################################################

	public function deleteAction($id, $security_token)
	{
		$gateway = $this->em->find('DeskPRO:EmailGateway', $id);
		$transport = null;
		if ($gateway) {
			$transport = $gateway->linked_transport;
		}

		$ret = parent::deleteAction($id, $security_token);

		if ($transport) {
			$this->em->remove($transport);
			$this->em->flush();
		}

		return $ret;
	}


	############################################################################
	# set-cloud-alias
	############################################################################

	public function setCloudAliasAction()
	{
		/** @var $gateway \Application\DeskPRO\Entity\EmailGateway */
		$gateway = $this->em->find('DeskPRO:EmailGateway', $this->in->getUint('gateway_id'));
		if (!$gateway) {
			throw $this->createNotFoundException();
		}

		$email = $this->in->getString('email');

		if ($email) {
			$email = strtolower($email);
			if (!\Orb\Validator\StringEmail::isValueValid($email) || App::getSystemService('gateway_address_matcher')->isManagedAddress($email)) {
				return $this->createJsonResponse(array('error' => 'invalid_email'));
			}

			// Check its not already used
			$is_used = App::getDb()->fetchColumn("
				SELECT id
				FROM email_gateway_addresses
				WHERE match_pattern = ? AND email_gateway_id != ?
			", array($email, $gateway->getId()));

			if ($is_used) {
				return $this->createJsonResponse(array('error' => 'dupe'));
			}
		}

		$previous = $gateway->getAliasEmailAddress(true);
		if ($previous) {
			$this->em->remove($previous);
		}

		if ($email) {
			$new = EmailGatewayAddress::newEmailAddress($gateway, $email);
			$new->run_order = 100;

			$this->em->persist($new);
		}

		$this->db->beginTransaction();
		try {
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createJsonResponse(array(
			'success'    => true,
			'email'      => $email ?: false,
			'gateway_id' => $gateway->getid()
		));
	}

	############################################################################
	# set-outgoing-account
	############################################################################

	public function getCloudOutgoingAccountFormAction()
	{
		/** @var $gateway \Application\DeskPRO\Entity\EmailGateway */
		$gateway = $this->em->find('DeskPRO:EmailGateway', $this->in->getUint('gateway_id'));
		if (!$gateway) {
			throw $this->createNotFoundException();
		}

		if (!$gateway->linked_transport) {
			$transport                   = new EmailTransport();
			$transport->title            = 'Default (send through DeskPRO)';
			$transport->match_type       = 'exact';
			$transport->match_pattern    = $gateway->getPrimaryEmailAddress();
			$transport->transport_type   = 'mail';

			$gateway->linked_transport = $transport;

			$this->em->persist($transport);
			$this->em->persist($gateway);
			$this->em->flush();
		}

		$outgoing_email_form = $this->forward('CloudAdminBundle:EmailTransports:editAccount', array('id' => $gateway->linked_transport->getId()), array('_partial' => 'cloud_email'))->getContent();

		return $this->createJsonResponse(array(
			'gateway_id' => $gateway->getId(),
			'form_html' => $outgoing_email_form,
		));
	}

	public function setCloudOutgoingAccountAction()
	{
		$id = App::getDb()->fetchColumn("
			SELECT linked_transport_id
			FROM email_gateways
			WHERE id = ?
		", array($this->in->getUint('gateway_id')));

		if (!$id) {
			throw $this->createNotFoundException();
		}

		$transport = $this->em->find('DeskPRO:EmailTransport', $id);

		$edittrans = new EditEmailTransportModel($transport);
		$form = $this->get('form.factory')->create(new EditEmailTransportForm(), $edittrans);

		$this->ensureRequestToken('edit_transport');
		$form->bindRequest($this->get('request'));

		$edittrans->save();

		return $this->createJsonResponse(array(
			'success' => true,
			'transport_id' => $transport->id,
			'title' => $transport->title
		));
	}

	############################################################################
	# edit-account
	############################################################################

	public function cloudEditAccountAction($gateway_id)
	{
		$gateway = $this->em->find('DeskPRO:EmailGateway', $gateway_id);

		if (!$gateway) {
			throw $this->createNotFoundException();
		}

		$transport = $gateway->linked_transport;
		if (!$transport) {
			$transport = new \Application\DeskPRO\Entity\EmailTransport();
		}
		$edittrans = new EditEmailTransportModel($transport);
		$trans_form = $this->get('form.factory')->create(new EditEmailTransportForm(), $edittrans);

		return $this->render('CloudAdminBundle:EmailGateways:edit-account.html.twig', array(
			'gateway' => $gateway,
			'transport' => $transport,
			'edittrans' => $edittrans,
			'trans_form' => $trans_form->createView(),
		));
	}

	public function cloudEditAccountSaveAction($gateway_id)
	{
		$gateway = $this->em->find('DeskPRO:EmailGateway', $gateway_id);

		if (!$gateway) {
			throw $this->createNotFoundException();
		}

		$old_transport = $gateway->linked_transport;
		$transport                   = new EmailTransport();
		$transport->title            = 'Default (send through DeskPRO)';
		$transport->match_type       = 'exact';
		$transport->match_pattern    = $gateway->getPrimaryEmailAddress();
		$transport->transport_type   = 'mail';
		$gateway->linked_transport = $transport;

		$edittrans = new EditEmailTransportModel($transport);
		$trans_form = $this->get('form.factory')->create(new EditEmailTransportForm(), $edittrans);
		$trans_form->bindRequest($this->get('request'));

		$transport->transport_type = $edittrans->transport_type;
		if ($edittrans->transport_type == 'smtp') {
			$transport->title = $edittrans->smtp_options['host'] . ':' . $edittrans->smtp_options['username'];
			$transport->transport_options = $edittrans->smtp_options;
		} elseif ($edittrans->transport_type == 'gmail') {
			$transport->title = 'Gmail / Google Apps: ' . $edittrans->gmail_options['username'];
			$transport->transport_options = $edittrans->gmail_options;
		} else {
			$transport->title = 'PHP mail()';
			$transport->transport_options = array();
		}

		if ($old_transport) {
			$this->em->remove($old_transport);
		}

		$this->em->persist($transport);
		$this->em->persist($gateway);
		$this->em->flush();

		$email_addresses = $this->in->getCleanValueArray('email_addresses', 'string', 'discard');
		array_unshift($email_addresses, $this->in->getString('alias_email_address'));
		$email_addresses = Arrays::removeFalsey($email_addresses);
		$email_addresses = Arrays::func($email_addresses, 'strtolower');
		$email_addresses = array_unique($email_addresses);

		$this->db->executeUpdate("
			DELETE FROM email_gateway_addresses
			WHERE email_gateway_id = ? AND id != ?
		", array($gateway->id, $gateway->getPrimaryEmailAddress(true)->id));
		$this->db->deleteIn('email_gateway_addresses', $email_addresses, 'match_pattern');

		$batch = array();
		foreach ($email_addresses as $x => $addr) {
			$batch[] = array(
				'email_gateway_id' => $gateway->id,
				'match_type' => 'exact',
				'match_pattern' => $addr,
				'run_order' => $x
			);
		}
		if ($batch) {
			$this->db->batchInsert('email_gateway_addresses', $batch);
		}

		return $this->createJsonResponse(array(
			'success' => true,
			'gateway_id' => $gateway->id
		));
	}

	####################################################################################################################

	public function editAccountAction($id) { return $this->redirectRoute('admin_emailgateways'); }
	public function ajaxTestAction() { throw $this->createNotFoundException(); }
}
