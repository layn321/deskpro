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

use Application\DeskPRO\Entity\CustomDefAbstract;

use Orb\Util\Util;

/**
 * Abstract class for managing custom fields
 */
abstract class CustomDefAbstractController extends AbstractController
{
	const API_NAME = '';

	protected $route_basename;

	public function init()
	{
		parent::init();
		$this->setRouteBasename();
	}

	protected function setRouteBasename()
	{
		$this->route_basename = 'admin_' . strtolower(str_replace('Controller', '', Util::getBaseClassname($this))) . '_';
	}

	protected function getListingRoute()
	{
		return rtrim($this->route_basename, '_');
	}



	############################################################################
	# index
	############################################################################

	/**
	 * List fields
	 */
	public function indexAction()
	{
		$existing_fields = $this->getApi()->getFields();

		return $this->render($this->getTemplateName('index.html.twig'), $this->getTemplateVars(array(
			'fields' => $existing_fields
		)));
	}



	############################################################################
	# new-choose-type
	############################################################################

	public function newChooseTypeAction()
	{
		$vars = array();
		return $this->render($this->getTemplateName('edit-choosetype.html.twig'), $this->getTemplateVars($vars));
	}



	############################################################################
	# /agent/person-fields/:field_id/edit                 admin_personfields_edit
	############################################################################

	public function editAction($field_id)
	{
		if ($field_id) {
			$field = $this->getFieldOr404($field_id);
			$is_new = false;
		} else {
			$field = $this->createNewField();
			$field['handler_class'] = $this->in->getString('fielddef.handler_class');
			$is_new = true;
		}

		$basetype    = Util::getBaseClassname($field['handler_class']);
		$model_class = 'Application\\AdminBundle\\Form\\CustomField\\Model\\' . $basetype . 'Field';
		$type_class  = 'Application\\AdminBundle\\Form\\CustomField\\Type\\' . $basetype . 'FieldType';

		$editfield = new $model_class($field);
		$formtype  = new $type_class();
		$form      = $this->get('form.factory')->create($formtype, $editfield);

		if ($this->request->isPost()) {
			if (1 /*$form->isValid()*/) {

				$this->em->getConnection()->beginTransaction();
				try {
					$name = str_replace('custom_def_', '', $field->getTableName());

					if ($field['handler_class'] == 'Application\\DeskPRO\\CustomFields\\Handler\\Choice') {
						$editfield->choices_structure = $this->in->getString('choices_structure');
						$editfield->choices_removed_structure = $this->in->getString('choices_removed_structure');
						$editfield->default_option = $this->in->getString('default_option');
					}

					$form->bindRequest($this->get('request'));

					$editfield->is_agent_field = $this->in->getBool('fielddef.is_agent_field');
					$editfield->save();

					$this->clearCacheForFieldType($field);

					$this->em->getConnection()->commit();
				} catch (\Exception $e) {
					$this->em->getConnection()->rollback();
					throw $e;
				}


				if ($is_new && static::API_NAME	== 'custom_fields.tickets') {
					$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.task_completed_add_ticketfield', time());
				}

				$this->_postEditSave($field, $is_new);

				$this->sendAgentReloadSignal();

				$this->getTemplateVars(); // to get routebasename
				return $this->redirectRoute($this->getListingRoute());
			}
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

		$vars = array(
			'field' => $field,
			'editfield' => $editfield,
			'form' => $form->createView(),
			'base_edit_tpl' => $this->getTemplateName('edit.html.twig'),
			'choices_structure' => $choices_structure,
		);

		$tpl_name = 'edit-' . strtolower($basetype) . '.html.twig';

		$vars = array_merge($vars, $this->_getEditFieldData($field));

		return $this->render($this->getTemplateName($tpl_name), $this->getTemplateVars($vars));
	}

	public function _getEditFieldData($field)
	{
		return array();
	}

	public function _postEditSave($field, $is_new)
	{

	}

	############################################################################
	# set-enabled
	############################################################################

	public function setEnabledAction($field_id)
	{
		$field = $this->getFieldOr404($field_id);
		$field->is_enabled = $this->in->getBool('is_enabled');

		$this->em->transactional(function($em) use ($field) {
			$em->persist($field);
			$em->flush();
		});

		$this->sendAgentReloadSignal();

		return $this->createJsonResponse(array('success' => true));
	}


	############################################################################
	# delete
	############################################################################

	public function deleteAction($field_id, $security_token)
	{
		$this->ensureAuthToken('delete_custom_field', $security_token);
		$field = $this->getFieldOr404($field_id);

		$this->em->transactional(function($em) use ($field) {
			$em->remove($field);
			$em->flush();
		});

		$this->sendAgentReloadSignal();

		return $this->redirectRoute($this->getListingRoute());
	}



	############################################################################

	/**
	 * @return Application\DeskPRO\Entity\CustomDefAbstract
	 */
	protected function getFieldOr404($field_id)
	{
		try {
			$field = $this->em->find($this->getApi()->getEntityName(), $field_id);
		} catch (\Doctrine\ORM\NoResultException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no field with ID $field_id");
		}

		return $field;
	}



	/**
	 * Create a new custom field def object.
	 *
	 * @return CustomFieldDef
	 */
	protected function createNewField()
	{
		$classname = $this->getApi()->getEntityClassname();

		$field = new $classname();

		return $field;
	}


	/**
	 * Get template vars used on all pages.
	 *
	 * @param array $merge_vars Merge with these variables
	 * @return array
	 */
	public function getTemplateVars(array $merge_vars = null)
	{
		$template_vars = array();
		$template_vars['route_basename'] = $this->route_basename = 'admin_' . strtolower(str_replace('Controller', '', Util::getBaseClassname($this))) . '_';
		$template_vars['section'] = strtolower(str_replace(array('CustomDef', 'Controller'), '', Util::getBaseClassname($this)));
		$template_vars['sub_section'] = 'fields';

		if ($merge_vars) {
			$template_vars = array_merge($template_vars, $merge_vars);
		}

		return $template_vars;
	}



	/**
	 * Get the proper path for a template with this controller.
	 *
	 * @param string $tpl
	 */
	public function getTemplateName($tpl)
	{
		$name = 'AdminBundle:' . str_replace('Controller', '', Util::getBaseClassname($this)) . ':' . $tpl;

		if (!$this->tpl->exists($name)) {
			$name = 'AdminBundle:CustomDefAbstract:' . $tpl;
		}

		return $name;
	}



	/**
	 * Get the handler for working with custom fields.
	 *
	 * @return \Application\DeskPRO\CustomFields\AbstractFields
	 */
	public function getApi()
	{
		return App::getApi(static::API_NAME);
	}


	/**
	 * @param $field
	 */
	public function clearCacheForFieldType($field)
	{

	}
}
