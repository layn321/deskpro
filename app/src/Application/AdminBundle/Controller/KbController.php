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

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Orb\Util\Arrays;

use Application\AdminBundle\Form\EditEmailGateway as EditEmailGatewayForm;
use Application\AdminBundle\FormModel\EditEmailGateway as EditEmailGatewayModel;

use Application\DeskPRO\Entity\EmailGatewayAddress;

class KbController extends AbstractController
{
	/**
	 * Shows the main listing of gateways
	 */
	public function gatewaysAction()
	{
		$all_gateways = $this->em->createQuery("
			SELECT g
			FROM DeskPRO:EmailGateway g
			WHERE g.gateway_type = 'articles'
			ORDER BY g.title ASC
		")->getResult();

		if (!count($all_gateways)) {
			return $this->redirectRoute('admin_kb_gateways_new');
		}

		$all_gateways_byemail = array();
		foreach ($all_gateways as $gateway) {
			foreach ($gateway->addresses as $addr) {
				$all_gateways_byemail[$addr->match_pattern] = $gateway;
			}
		}

		$helpdesk_emails = explode(',', $this->container->getSetting('core.helpdesk_emails'));
		$helpdesk_emails = Arrays::removeFalsey($helpdesk_emails);

		$rejection_count = $this->em->getRepository('DeskPRO:EmailSource')->countRejectionStatus(array('article'));
		$error_count = $this->em->getRepository('DeskPRO:EmailSource')->countErrorStatus(array('article'));

		$article_categories  = $this->em->getRepository('DeskPRO:ArticleCategory')->getInHierarchy();

		return $this->render('@list.html.twig', array(
			'all_gateways'            => $all_gateways,
			'all_gateways_byemail'    => $all_gateways_byemail,
			'helpdesk_emails'         => $helpdesk_emails,
			'rejection_count'         => $rejection_count,
			'error_count'             => $error_count,
			'article_categories'      => $article_categories
		));
	}

	############################################################################
	# edit
	############################################################################

	public function editGatewayAction($id)
	{
		if ($id) {
			$is_new = false;

			$gateway = $this->em->find('DeskPRO:EmailGateway', $id);
			if (!$gateway) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
		} else {
			$is_new = true;
			$gateway = new \Application\DeskPRO\Entity\EmailGateway();
		}

		$editgateway = new EditEmailGatewayModel($gateway);
		$form = $this->get('form.factory')->create(new EditEmailGatewayForm(), $editgateway);

		$errors = array();

		if ($this->request->isPost()) {
			$this->ensureRequestToken('edit_gateway');
			$form->bindRequest($this->get('request'));

			$editgateway->apply();

			if (!$errors && $form->isValid()) {

				$editgateway->define_transport = false;

				$new_addresses_info = $this->in->getCleanValueArray('new_address', 'array', 'str_simple');
				$new_addresses = array();

				foreach ($new_addresses_info as $address_info) {
					$address = new EmailGatewayAddress();
					$address->match_type    = 'exact';
					$address->match_pattern = $address_info['match_pattern'];

					$new_addresses[] = $address;
				}

				$found = false;

				foreach ($gateway->addresses as $a) {
					if ($a->match_pattern == $editgateway->address) {
						$found = true;
						break;
					}
				}

				if (!$found) {
					$address = new EmailGatewayAddress();
					$address->match_type    = 'exact';
					$address->match_pattern = $editgateway->address;

					$new_addresses[] = $address;

					if (count($gateway->addresses) == 1) {
						$this->em->remove($gateway->addresses->get(0));
						$gateway->addresses->remove(0);
					}
				}

				// Remove addresses
				$remove_address_ids = $this->in->getCleanValueArray('remove_address', 'uint', 'discard');

				$editgateway->setNewAddresses($new_addresses);
				$editgateway->setRemoveAddressIds($remove_address_ids);

				$this->em->getConnection()->beginTransaction();
				try {
					$editgateway->save();
					$this->em->flush();

					$gateway->setProcessorExtra('category_id', 0);

					$category_id = $this->in->getUint('category_id');
					if ($category_id && $this->em->find('DeskPRO:ArticleCategory', $category_id)) {
						$gateway->setProcessorExtra('category_id', $category_id);
					}

					$this->em->persist($gateway);
					$this->em->flush();

					$this->em->getConnection()->commit();
				} catch (\Exception $e) {
					$this->em->getConnection()->rollback();
					throw $e;
				}

				if ($this->request->isXmlHttpRequest()) {
					return $this->createJsonResponse(array('success' => true));
				}

				$this->session->setFlash('saved', $gateway->title);
				return $this->redirectRoute('admin_kb_gateways');
			}
		}

		$tpl = '@edit-gateway.html.twig';
		if ($this->request->isPartialRequest()) {
			$tpl = '@edit-gateway-form.html.twig';
		}

		$article_categories  = $this->em->getRepository('DeskPRO:ArticleCategory')->getInHierarchy();

		return $this->render($tpl, array(
			'errors' => $errors,
			'gateway' => $gateway,
			'form' => $form->createView(),
			'editgateway' => $editgateway,
			'partial' => $this->request->isPartialRequest(),
			'article_categories' => $article_categories
		));
	}

	public function setGatewayCategoryAction()
	{
		$gateway = $this->em->find('DeskPRO:EmailGateway', $this->in->getUint('gateway_id'));
		if (!$gateway) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$gateway->setProcessorExtra('category_id', 0);

		$category_id = $this->in->getUint('category_id');
		if ($category_id && $this->em->find('DeskPRO:ArticleCategory', $category_id)) {
			$gateway->setProcessorExtra('category_id', $category_id);
		}

		$this->em->persist($gateway);
		$this->em->flush();

		return $this->createJsonResponse(array(
			'gateway_id' => $gateway->getId(),
			'category_id' => $gateway->getProcessorExtra('category_id'),
		));
	}

	public function quickToggleGatewayAction($id)
	{
		$gateway = $this->em->find('DeskPRO:EmailGateway', $id);
		if (!$id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$gateway->is_enabled = !$gateway->is_enabled;

		$this->em->transactional(function ($em) use ($gateway) {
			$em->persist($gateway);
			$em->flush();
		});

		return $this->createJsonResponse(array('success' => true, 'is_enabled' => $gateway->is_enabled, 'gateway_id' => $gateway->getId()));
	}

	public function deleteGatewayAction($id, $security_token)
	{
		$gateway = $this->em->find('DeskPRO:EmailGateway', $id);
		if (!$gateway || !$this->session->checkSecurityToken('delete_gateway', $security_token)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->remove($gateway);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->session->setFlash('deleted', $gateway->title);

		return $this->redirectRoute('admin_kb_gateways');
	}
}
