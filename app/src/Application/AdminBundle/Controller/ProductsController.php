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

use Application\AdminBundle\Form\EditProductType;

/**
 * Products
 */
class ProductsController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	/**
	 * Shows the main listing of products
	 */
	public function listAction()
	{
		$all_products = $this->em->createQuery("
			SELECT p
			FROM DeskPRO:Product p
			WHERE p.parent IS NULL
			ORDER BY p.display_order ASC
		")->getResult();

		return $this->render('AdminBundle:Products:list.html.twig', array(
			'all_products' => $all_products
		));
	}



	############################################################################
	# save-title
	############################################################################

	public function saveTitleAction()
	{
		$product_id = $this->in->getUint('product_id');
		$product = $this->em->find('DeskPRO:Product', $product_id);

		if (!$product) {
			throw $this->createNotFoundException();
		}

		if ($this->in->getString('title')) {
			$product->title = $this->in->getString('title');
		}

		$parent_id = $this->in->getUint('parent_id');
		if (!count($product->getChildren())) {
			if (!$parent_id || $parent_id == $product->getId()) {
				$product->parent = null;
			} else {
				$parent_prod = $this->em->find('DeskPRO:Product', $parent_id);
				if ($parent_prod && !count($parent_prod->parent)) {
					$product->parent = $parent_prod;
				}
			}
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($product);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_products');
	}

	############################################################################
	# save-new
	############################################################################

	public function saveNewAction()
	{
		$product = new \Application\DeskPRO\Entity\Product();
		$product->title = $this->in->getString('title');

		if (!$product->title) {
			$product->title = 'Untitled';
		}

		$parent = null;
		if ($this->in->getUint('parent_id')) {
			$parent = $this->em->find('DeskPRO:Product', $this->in->getUint('parent_id'));
		}

		if ($parent and !$parent->parent) {
			$product->parent = $parent;
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($product);
			$this->em->flush();

			// Created first prod, enable the product feature
			$count = $this->db->fetchColumn("SELECT COUNT(*) FROM products");
			if ($count == 1) {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_product', '1');
			}

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_products');
	}

	############################################################################
	# edit
	############################################################################

	public function editAction($product_id)
	{
		$product = $this->em->find('DeskPRO:Product', $product_id);

		if (!$product) {
			throw $this->createNotFoundException();
		}

		$all_products = $this->em->createQuery("
			SELECT p
			FROM DeskPRO:Product p
			WHERE p.parent IS NULL
			ORDER BY p.display_order ASC
		")->getResult();

		$field_manager = $this->container->getSystemService('product_fields_manager');
		$custom_fields = $field_manager->getDisplayArrayForObject($product);

		if ($this->in->getBool('process')) {
			if ($this->in->getString('title')) {
				$product->title = $this->in->getString('title');
			}

			$parent_id = $this->in->getUint('parent_id');
			if (!count($product->getChildren())) {
				if (!$parent_id || $parent_id == $product->getId()) {
					$product->parent = null;
				} else {
					$parent_prod = $this->em->find('DeskPRO:Product', $parent_id);
					if ($parent_prod && !count($parent_prod->parent)) {
						$product->parent = $parent_prod;
					}
				}
			}

			$this->em->persist($product);
			$this->em->flush();

			$post_custom_fields = isset($_POST['custom_fields']) ? $_POST['custom_fields'] : array();
			$field_manager->saveFormToObject($post_custom_fields, $product);

			$this->em->persist($product);
			$this->em->flush();

			return $this->redirectRoute('admin_products');
		}

		return $this->render('AdminBundle:Products:edit.html.twig', array(
			'product'       => $product,
			'all_products'  => $all_products,
			'custom_fields' => $custom_fields,
		));
	}

	############################################################################
	# update-orders
	############################################################################

	public function updateOrdersAction()
	{
		$helper = new \Application\AdminBundle\Controller\Helper\DisplayOrderUpdate($this);
		return $helper->doUpdate('products');
	}

	############################################################################
	# delete
	############################################################################

	public function deleteAction($product_id)
	{
		$product = $this->em->getRepository('DeskPRO:Product')->find($product_id);

		return $this->render('AdminBundle:Products:delete.html.twig', array(
			'product'  => $product,
		));
	}

	public function doDeleteAction($product_id, $security_token)
	{
		$product = $this->em->getRepository('DeskPRO:Product')->find($product_id);

		if (!$this->session->getEntity()->checkSecurityToken('delete_product', $security_token)) {
			return $this->renderStandardTokenError();
		}

		$this->em->beginTransaction();
		foreach ($product->children as $c) {
			$this->em->remove($c);
		}
		$this->em->remove($product);
		$this->em->flush();

		$count = $this->db->fetchColumn("SELECT COUNT(*) FROM products");
		if (!$count) {
			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_product', '0');
		}

		$this->em->commit();

		$this->sendAgentReloadSignal();

		$this->session->setFlash('deleted', $product->title);
		return $this->redirectRoute('admin_products');
	}

	############################################################################
	# toggle-feature
	############################################################################

	public function toggleFeatureAction($enable)
	{
		if ($enable) {
			$count = $this->db->fetchColumn("SELECT COUNT(*) FROM products");
			if (!$count) {
				return $this->redirectRoute('admin_products');
			}

			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_product', '1');
		} else {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_product', '0');
		}

		$url = $this->generateUrl('admin_products');
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
			$obj = $this->em->getRepository('DeskPRO:Product')->find($default_id);
			if (!$obj) {
				$default_id = 0;
			}
		}

		$this->container->getSettingsHandler()->setSetting('core.default_prod_id', $default_id);
		return $this->redirectRoute('admin_products');
	}
}
