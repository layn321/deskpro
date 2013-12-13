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

class ChatController extends AbstractController
{
	############################################################################
	# editor
	############################################################################

	/**
	 * Shows the editor
	 */
	public function editorAction($department_id, $section = 'create')
	{
		// If no per-department forms, then we cant edit a department
		if ($department_id AND !$this->container->getSetting('core_chat.per_department_form')) {
			return $this->redirectRoute('admin_chat_editor');
		}

		$department = null;
		if ($department_id) {
			$department = $this->em->getRepository('DeskPRO:Department')->find($department_id);
		}

		$chat_field_defs = App::getApi('custom_fields.chats')->getEnabledFields();
		$custom_chat_fields = App::getApi('custom_fields.chats')->getFieldsDisplayArray($chat_field_defs);

		// Existing options
		$is_default = false;
		$page_data = $this->em->getRepository('DeskPRO:ChatPageDisplay')->getSectionDataResolve($department, $section, 'default', $is_default);

		if ($is_default && $this->container->getSetting('core_chat.per_department_form')) {
			return $this->initEditorAction($department_id, $section);
		}

		$dep_ids_custom = App::getDb()->fetchAllCol("
			SELECT COALESCE(department_id, 0)
			FROM chat_page_display
		");

		return $this->render('AdminBundle:Chat:editor.html.twig', array(
			'department'           => $department,
			'custom_chat_fields'   => $custom_chat_fields,
			'is_default'           => $is_default,
			'page_data'            => $page_data,
			'section'              => $section,
			'dep_ids_custom'       => $dep_ids_custom,
		));
	}

	public function saveEditorAction($department_id, $section = 'create')
	{
		$department = null;

		if ($department_id) {
			$department = $this->em->find('DeskPRO:Department', $department_id);
		}

		$page_data = $this->in->getArrayValue('items', 'post');
		$page_display = $this->em->getRepository('DeskPRO:ChatPageDisplay')->getOrCreate($department, $section, 'default');
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

		$page_display_default = $this->em->getRepository('DeskPRO:ChatPageDisplay')->getSectionDataResolve($department, $section, 'default');
		$page_display = $this->em->getRepository('DeskPRO:ChatPageDisplay')->getOrCreate($department, $section, 'default');

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
			return $this->redirectRoute('admin_chat_editor_dep', array('department_id' => $department->id, 'section' => $section));
		} else {
			return $this->redirectRoute('admin_chat_editor', array('section' => $section));
		}
	}

	public function revertEditorAction($department_id, $section)
	{
		$department = null;

		if ($department_id) {
			$department = $this->em->find('DeskPRO:Department', $department_id);
		}

		$d = $this->em->getRepository('DeskPRO:ChatPageDisplay')->findOneBy(array('department' => $department_id ? $department_id : null, 'zone' => $section, 'section' => 'default'));
		if ($d) {
			$this->em->transactional(function($em) use ($d) {
				$em->remove($d);
				$em->flush();
			});
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		if ($department_id) {
			return $this->redirectRoute('admin_chat_editor_dep', array('department_id' => $department_id, 'section' => $section));
		} else {
			return $this->redirectRoute('admin_chat_editor_dep', array('department_id' => '0', 'section' => $section));
		}
	}

	public function togglePerDepartmentAction()
	{
		$enable = $this->in->getBoolInt('enable');
		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core_chat.per_department_form', $enable);

		if (!$enable) {
			$this->container->getDb()->executeUpdate("
				DELETE FROM chat_page_display
				WHERE department_id IS NOT NULL
			");
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->redirectRoute('admin_chat_editor');
	}

	public function resetEditorAction($security_token)
	{
		$this->ensureAuthToken('reset_editor', $security_token);

		$this->container->getDb()->executeUpdate("
			DELETE FROM chat_page_display
		");

		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core_chat.per_department_form', 0);

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->redirectRoute('admin_chat_editor');
	}
}