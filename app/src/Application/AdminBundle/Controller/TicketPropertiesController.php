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
 * Lists fields, categories, priorities, widgets, workflows
 */
class TicketPropertiesController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	/**
	 * Shows the main listing of priorities
	 */
	public function listAction()
	{
		$counts = array();
		$counts['ticket_category'] = $this->em->getRepository('DeskPRO:TicketCategory')->countAll();
		$counts['ticket_priority'] = $this->em->getRepository('DeskPRO:TicketPriority')->countAll();
		$counts['ticket_workflow'] = $this->em->getRepository('DeskPRO:TicketWorkflow')->countAll();
		$counts['department']      = $this->em->getRepository('DeskPRO:Department')->countAll();
		$counts['product']         = $this->em->getRepository('DeskPRO:Product')->countAll();

		$fields = App::getApi('custom_fields.tickets')->getFields();

		// Build map of field to department
		$all_pages = $this->db->fetchAllKeyValue("SELECT COALESCE(department_id, 0) AS department_id, data FROM ticket_page_display WHERE zone = 'create'");
		foreach ($all_pages as &$v) {
			$v = @unserialize($v);
			if (!$v) {
				$v = array();
			}
		}

		$field_to_dep = array();
		$deps = $this->container->getDataService('Department')->getRootNodes();

		$fn_get_ids = function($name) use ($deps, $all_pages) {
			$ids = array();

			$in_default = false;
			if (isset($all_pages[0])) {
				foreach ($all_pages[0] as $info) {
					if ($info['id'] == $name) {
						$in_default = true;
					}
				}
			}

			foreach ($deps as $d) {
				if (!$d->is_tickets_enabled) continue;

				if (count($d->children)) {
					foreach ($d->children as $subd) {
						$in = $in_default;
						if (isset($all_pages[$subd->id])) {
							$in = false;
							foreach ($all_pages[$subd->id] as $info) {
								if ($info['id'] == $name) {
									$in = true;
								}
							}
						}

						if ($in) {
							$ids[$subd->id] = $subd->id;
						}
					}
				} else {
					$in = $in_default;
					if (isset($all_pages[$d->id])) {
						$in = false;
						foreach ($all_pages[$d->id] as $info) {
							if ($info['id'] == $name) {
								$in = true;
							}
						}
					}

					if ($in) {
						$ids[$d->id] = $d->id;
					}
				}
			}

			return $ids;
		};

		foreach (array('ticket_department', 'ticket_priority', 'ticket_workflow', 'ticket_product', 'ticket_category') as $name) {
			$field_to_dep[$name] = $fn_get_ids($name);
		}
		foreach ($fields as $f) {
			$field_to_dep[$f->id] = $fn_get_ids("ticket_field[{$f->id}]");
		}

		$dep_flat = $this->container->getDataService('Department')->getFullNames();

		return $this->render('AdminBundle:TicketProperties:list.html.twig', array(
			'counts'       => $counts,
			'fields'       => $fields,
			'field_to_dep' => $field_to_dep,
			'all_pages'    => $all_pages,
			'dep_names'    => $dep_flat,
		));
	}

	############################################################################
	# editor
	############################################################################

	/**
	 * Shows the editor
	 */
	public function editorAction($department_id, $section = 'create')
	{
		// If no per-department forms, then we cant edit a department
		if ($department_id AND !$this->container->getSetting('core_tickets.per_department_form')) {
			return $this->redirectRoute('admin_tickets_editor');
		}

		$departments = $this->em->getRepository('DeskPRO:Department')->getInHierarchy();
		$department_hierarchy = $this->em->getRepository('DeskPRO:Department')->getInHierarchy();

		$department = null;
		if ($department_id) {
			$department = $this->em->getRepository('DeskPRO:Department')->find($department_id);
		}

		$ticket_field_defs = App::getApi('custom_fields.tickets')->getEnabledFields();
		$custom_ticket_fields = App::getApi('custom_fields.tickets')->getFieldsDisplayArray($ticket_field_defs);

		$people_field_defs = App::getApi('custom_fields.people')->getEnabledFields();
		$custom_people_fields = App::getApi('custom_fields.people')->getFieldsDisplayArray($people_field_defs);

		$ticket_options = App::getApi('tickets')->getTicketOptions($this->person);
		$ticket_options['email_gateway_addresses'] = $this->em->getRepository('DeskPRO:EmailGatewayAddress')->getOptions();

		// Existing options
		$is_default = false;
		$page_data = $this->em->getRepository('DeskPRO:TicketPageDisplay')->getSectionDataResolve($department, $section, 'default', $is_default);

		$dep_ids_custom = App::getDb()->fetchAllCol("
			SELECT COALESCE(department_id, 0)
			FROM ticket_page_display
		");

		if ($department_id) {
			$custom_sections = App::getDb()->fetchAllCol("
				SELECT zone
				FROM ticket_page_display
				WHERE department_id = ?
			", array($department_id));
		} else {
			$custom_sections = App::getDb()->fetchAllCol("
				SELECT zone
				FROM ticket_page_display
				WHERE department_id IS NULL
			");
		}

		return $this->render('AdminBundle:TicketProperties:editor.html.twig', array(
			'departments' => $departments,
			'department_hierarchy' => $department_hierarchy,
			'department' => $department,
			'custom_ticket_fields' => $custom_ticket_fields,
			'custom_people_fields' => $custom_people_fields,
			'term_options' => $ticket_options,
			'ticket_options' => $ticket_options,
			'is_default' => $is_default,
			'page_data' => $page_data,
			'section' => $section,
			'dep_ids_custom' => $dep_ids_custom,
			'custom_sections' => $custom_sections,
		));
	}

	public function saveEditorAction($department_id, $section = 'create')
	{
		$department = null;

		if ($department_id) {
			$department = $this->em->find('DeskPRO:Department', $department_id);
		}

		$page_data = $this->in->getArrayValue('items', 'post');
		$page_display = $this->em->getRepository('DeskPRO:TicketPageDisplay')->getOrCreate($department, $section, 'default');
		$page_display->data = $page_data;

		$this->em->transactional(function($em) use ($page_display) {
			$em->persist($page_display);
			$em->flush();
		});

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->createJsonResponse(array('success' => true));
	}

	public function initEditorAction($department_id, $section)
	{
		$department = null;

		if ($department_id) {
			$department = $this->em->find('DeskPRO:Department', $department_id);
		}

		$page_display_default = $this->em->getRepository('DeskPRO:TicketPageDisplay')->getSectionDataResolve($department, $section, 'default');
		$page_display = $this->em->getRepository('DeskPRO:TicketPageDisplay')->getOrCreate($department, $section, 'default');

		if ($page_display_default) {
			$page_display->data = $page_display_default;
		}

		$this->em->transactional(function($em) use ($page_display) {
			$em->persist($page_display);
			$em->flush();
		});

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		if ($department) {
			return $this->redirectRoute('admin_tickets_editor_dep', array('department_id' => $department->id, 'section' => $section));
		} else {
			return $this->redirectRoute('admin_tickets_editor', array('section' => $section));
		}
	}

	public function revertEditorAction($department_id, $section)
	{
		$department = null;

		if ($department_id) {
			$department = $this->em->find('DeskPRO:Department', $department_id);
		}

		$d = $this->em->getRepository('DeskPRO:TicketPageDisplay')->findOneBy(array('department' => $department_id ? $department_id : null, 'zone' => $section, 'section' => 'default'));
		if ($d) {
			$this->em->transactional(function($em) use ($d) {
				$em->remove($d);
				$em->flush();
			});
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		if ($department_id) {
			return $this->redirectRoute('admin_tickets_editor_dep', array('department_id' => $department_id, 'section' => $section));
		} else {
			return $this->redirectRoute('admin_tickets_editor_dep', array('department_id' => '0', 'section' => $section));
		}
	}

	public function togglePerDepartmentAction()
	{
		$enable = $this->in->getBoolInt('enable');
		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core_tickets.per_department_form', $enable);

		if (!$enable) {
			$this->container->getDb()->executeUpdate("
				DELETE FROM ticket_page_display
				WHERE department_id IS NOT NULL
			");
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->redirectRoute('admin_tickets_editor');
	}

	public function resetEditorAction($security_token)
	{
		$this->ensureAuthToken('reset_editor', $security_token);

		$this->container->getDb()->executeUpdate("
			DELETE FROM ticket_page_display
		");

		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core_tickets.per_department_form', 0);

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->redirectRoute('admin_tickets_editor');
	}
}
