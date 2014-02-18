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

use Application\AdminBundle\Form\EditDepartmentType;
use Application\DeskPRO\Searcher\TicketSearch;

/**
 * Handles creating/editing of API keys
 */
class DepartmentsController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	/**
	 * Shows the main listing of departments
	 */
	public function listAction($type = null)
	{
		if ($type == null) {
			return $this->redirectRoute('admin_departments', array('type' => 'tickets'));
		}

		$all_departments = $this->em->createQuery("
			SELECT dep
			FROM DeskPRO:Department dep
			WHERE dep.parent IS NULL AND " . ($type == 'tickets' ? 'dep.is_tickets_enabled = true' : 'dep.is_chat_enabled = true') . "
			ORDER BY dep.display_order ASC
		")->getResult();

		$agents     = $this->em->getRepository('DeskPRO:Person')->getAgents();
		$teams      = $this->em->getRepository('DeskPRO:AgentTeam')->findAll();
		$usergroups = $this->em->getRepository('DeskPRO:Usergroup')->findAll();

		$current_options_tickets = $this->em->getRepository('DeskPRO:DepartmentPermission')->getAllPersonPermissionsForAllDepartments('tickets', 'full', 1);
		$current_options_tickets_assign = $this->em->getRepository('DeskPRO:DepartmentPermission')->getAllPersonPermissionsForAllDepartments('tickets', 'assign', 1);
		$current_options_chat    = $this->em->getRepository('DeskPRO:DepartmentPermission')->getAllPersonPermissionsForAllDepartments('chat', 'full', 1);

		// Filter out non-agents
		$filter_outer = function(&$array) use ($agents) {
			foreach ($array as &$dep) {
				$new_list = array();
				foreach ($dep as $id) {
					if (isset($agents[$id])) $new_list[] = $id;
				}
				$dep = $new_list;
			}
		};

		$filter_outer($current_options_tickets);
		$filter_outer($current_options_chat);

		$gateway_accounts = null;
		if ($type == 'tickets') {
			$gateway_accounts = $this->em->getRepository('DeskPRO:EmailGateway')->getAllEnabled();
		}

		return $this->render('AdminBundle:Departments:list.html.twig', array(
			'all_departments' => $all_departments,
			'agents' => $agents,
			'teams' => $teams,
			'type' => $type,
			'usergroups' => $usergroups,
			'current_options_tickets' => $current_options_tickets,
			'current_options_tickets_assign' => $current_options_tickets_assign,
			'current_options_chat' => $current_options_chat,
			'gateway_accounts' => $gateway_accounts,
		));
	}

	public function saveGatewayAccountAction($department_id)
	{
		$department = $this->em->find('DeskPRO:Department', $department_id);

		if (!$department) {
			throw $this->createNotFoundException();
		}

		$email_gateway = $this->em->find('DeskPRO:EmailGateway', $this->in->getUint('gateway_account_id'));

		$this->em->getRepository('DeskPRO:Department')->linkToGateway($department, $email_gateway);

		return $this->createJsonResponse(array(
			'department_id' => $department->getId(),
			'email_gateway_id' => $email_gateway ? $email_gateway->getId() : 0
		));
	}

	public function saveAgentsAction($department_id)
	{
		$department = $this->em->find('DeskPRO:Department', $department_id);

		if (!$department) {
			throw $this->createNotFoundException();
		}

		$app = $this->in->getString('app');

		$this->db->executeUpdate("
			DELETE
			FROM department_permissions
			WHERE department_id = ?
				AND app = ?
				AND person_id IS NOT NULL
				AND name IN ('full', 'assign') AND value = 1
		", array($department_id, $app));

		$agents = $this->in->getCleanValueArray('agents', 'raw', 'uint');

		if ($agents) {
			$this->db->beginTransaction();

			foreach ($agents as $agent_id => $perms) {
				foreach ($perms AS $perm) {
					$this->db->insert('department_permissions', array(
						'department_id' => $department->id,
						'person_id' => $agent_id,
						'app' => $app,
						'name' => $perm,
						'value' => 1
					));
				}
			}

			$this->db->commit();
		}

		return $this->createJsonResponse(array('success' => true));
	}

	public function saveTitleAction()
	{
		$department_id = $this->in->getUint('department_id');
		$department = $this->em->find('DeskPRO:Department', $department_id);

		if (!$department) {
			throw $this->createNotFoundException();
		}

		if ($this->in->getString('title')) {
			$department->title = $this->in->getString('title');
		}

		$department->user_title = $this->in->getString('user_title');

		/**
		 * The Other Guys
		 * #201401181200 @Frankie -- Fill rate Doctrine metadata for insert to database (edit department)
		 * #201401191551 @Layne -- Changed getUint() to getString()
		 */
		 //add_rate($department_id, $d_rate); //backdoor work-around prior to metadata mapping of rate field
		$department->rate = $this->in->getString('rate'); 

		$parent_id = $this->in->getUint('parent_id');
		if (!count($department->getChildren())) {
			if (!$parent_id || $parent_id == $department->getId()) {
				$department->parent = null;
			} else {
				$parent_dep = $this->em->find('DeskPRO:Department', $parent_id);
				if ($parent_dep && !count($parent_dep->parent)) {
					$department->parent = $parent_dep;
				}
			}
		}

		$this->em->persist($department);

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();

		$type = $department->is_tickets_enabled ? 'tickets' : 'chat';
		return $this->redirectRoute('admin_departments', array('type' => $type));
	}

	public function saveNewAction($type)
	{
		$department = new \Application\DeskPRO\Entity\Department();
		$department->title = $this->in->getString('title');
		$department->user_title = $this->in->getString('user_title');

		/**
		 * The Other Guys
		 * #201401201153 @Frankie -- Fill department rate for insert to database (new department)
		 */
		$department->rate = $this->in->getString('rate');

		if ($type == 'tickets') {
			$department->is_tickets_enabled = true;
			$department->is_chat_enabled = false;
		} else {
			$department->is_chat_enabled = true;
			$department->is_tickets_enabled = false;
		}

		if (!$department->title) {
			$department->title = 'Untitled';
		}

		$parent = null;
		if ($this->in->getUint('parent_id')) {
			$parent = $this->em->find('DeskPRO:Department', $this->in->getUint('parent_id'));
		}

		if ($parent and !$parent->parent) {
			$department->parent = $parent;
		}

		$agent_ids = $this->db->fetchAllCol("
			SELECT id FROM people WHERE is_agent = 1
		");

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($department);
			$this->em->flush();

			$dep_perms = array();
			foreach ($agent_ids as $aid) {
				if ($type == 'tickets') {
					$dep_perms[] = array(
						'department_id' => $department->getId(),
						'usergroup_id' => null,
						'person_id' => $aid,
						'app' => 'tickets',
						'name' => 'full',
						'value' => 1
					);
					$dep_perms[] = array(
						'department_id' => $department->getId(),
						'usergroup_id' => null,
						'person_id' => $aid,
						'app' => 'tickets',
						'name' => 'assign',
						'value' => 1
					);
				} else {
					$dep_perms[] = array(
						'department_id' => $department->getId(),
						'usergroup_id' => null,
						'person_id' => $aid,
						'app' => 'chat',
						'name' => 'full',
						'value' => 1
					);
				}
			}

			if ($type == 'tickets') {
				$dep_perms[] = array(
					'department_id' => $department->getId(),
					'usergroup_id' => 1,
					'person_id' => null,
					'app' => 'tickets',
					'name' => 'full',
					'value' => 1
				);
			} else {
				$dep_perms[] = array(
					'department_id' => $department->getId(),
					'usergroup_id' => 1,
					'person_id' => null,
					'app' => 'chat',
					'name' => 'full',
					'value' => 1
				);
			}

			$this->db->batchInsert('department_permissions', $dep_perms);

			// Any tickets in the parent cant be there, assign them to this level
			if ($parent) {
				$this->container->getDb()->executeUpdate('UPDATE tickets SET department_id = ? WHERE department_id = ?', array($department->getId(), $parent->getId()));
				$this->container->getDb()->executeUpdate('UPDATE tickets_search_active SET department_id = ? WHERE department_id = ?', array($department->getId(), $parent->getId()));
				$this->container->getDb()->executeUpdate('UPDATE tickets_search_message SET department_id = ? WHERE department_id = ?', array($department->getId(), $parent->getId()));
				$this->container->getDb()->executeUpdate('UPDATE tickets_search_message_active SET department_id = ? WHERE department_id = ?', array($department->getId(), $parent->getId()));
				$this->container->getDb()->executeUpdate('UPDATE chat_conversations SET department_id = ? WHERE department_id = ?', array($department->getId(), $parent->getId()));
			}

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();

		$type = $department->is_tickets_enabled ? 'tickets' : 'chat';
		return $this->redirectRoute('admin_departments', array('type' => $type));
	}

	public function setDefaultAction($type)
	{
		$department_id = $this->in->getUint('default_value');

		if ($department_id) {
			$department = $this->em->getRepository('DeskPRO:Department')->find($department_id);

			if (!$department || ($type == 'tickets' && !$department->is_tickets_enabled) || ($type == 'chat' && !$department->is_chat_enabled)) {
				throw $this->createNotFoundException();
			}
		}

		if ($type == 'tickets') {
			$this->container->getSettingsHandler()->setSetting('core.default_ticket_dep', $department_id);
		} elseif ($type == 'chat') {
			$this->container->getSettingsHandler()->setSetting('core.default_chat_dep', $department_id);
		}

		return $this->redirectRoute('admin_departments', array('type' => $type));
	}

	public function setPhraseAction()
	{
		$phrase_singular   = strtolower($this->in->getString('phrase_singular'));
		$phrase_plural     = strtolower($this->in->getString('phrase_plural'));
		$phrase_singular_c = ucwords($phrase_singular);
		$phrase_plural_c   = ucwords($phrase_plural);

		$this->container->getSettingsHandler()->setSetting('core.phrase_department_singular', $phrase_singular);
		$this->container->getSettingsHandler()->setSetting('core.phrase_department_plural', $phrase_plural);

		$groups_reader = new \Application\DeskPRO\ResourceScanner\LanguagePhrases();
		$phrases = $groups_reader->getAllUserPhrases();

		$batch = array();
		$ids = array();

		$d = date('Y-m-d H:i:s');

		foreach ($phrases as $phrase_id => $phrase_text) {
			$new_phrase = str_replace(
				array('departments', 'Departments', 'department', 'Department'),
				array($phrase_plural, $phrase_plural_c, $phrase_singular, $phrase_singular_c),
				$phrase_text
			);

			if ($new_phrase != $phrase_text) {
				$group = \Orb\Util\Strings::extractRegexMatch('#^(.*)\.([^.]+)$#', $phrase_id, 1);
				$batch[] = array(
					'language_id' => 1,
					'name'        => $phrase_id,
					'groupname'   => $group,
					'phrase'      => $new_phrase,
					'created_at'  => $d,
					'updated_at'  => $d
				);

				$ids[] = $phrase_id;
			}
		}

		if ($ids) {
			$this->db->beginTransaction();
			try {
				$this->db->executeQuery("
					DELETE FROM phrases
					WHERE name IN (" . $this->db->quoteIn($ids) . ") AND language_id = 1
				");

				$this->db->batchInsert('phrases', $batch);

				$this->db->commit();
			} catch (\Exception $e) {
				$this->db->rollback();
				throw $e;
			}
		}

		return $this->redirectRoute('admin_departments', array('type' => 'tickets'));
	}

	############################################################################
	# delete
	############################################################################

	public function deleteAction($department_id)
	{
		$department = $this->em->getRepository('DeskPRO:Department')->find($department_id);

		if (!$department) {
			throw $this->createNotFoundException();
		}

		$type_prop = $department->is_tickets_enabled ? 'is_tickets_enabled' : 'is_chat_enabled';

		$count = $this->db->count('departments', array($type_prop => 1));
		if ($count < 2) {
			return $this->render('AdminBundle:Departments:no-delete.html.twig', array(
				'department' => $department,
				'type_prop' => $type_prop
			));
		}

		$tree_ids = $this->em->getRepository('DeskPRO:Department')->getIdsInTree($department->id, true);
		$tree_ids = implode(',', $tree_ids);

		$ticket_count = $this->db->fetchColumn("
			SELECT COUNT(*) FROM tickets
			WHERE department_id IN ($tree_ids)
		");
		$chat_count = $this->db->fetchColumn("
			SELECT COUNT(*) FROM chat_conversations
			WHERE department_id IN ($tree_ids)
		");

		$departments = $this->container->getDataService('Department')->getAll();

		return $this->render('AdminBundle:Departments:delete.html.twig', array(
			'department'   => $department,
			'type_prop'    => $department->is_tickets_enabled ? 'is_tickets_enabled' : 'is_chat_enabled',
			'ticket_count' => $ticket_count,
			'chat_count'   => $chat_count,
			'departments'  => $departments
		));
	}

	public function doDeleteAction($department_id, $security_token)
	{
		$department = $this->em->getRepository('DeskPRO:Department')->find($department_id);

		if (!$department) {
			throw $this->createNotFoundException();
		}

		$type_prop = $department->is_tickets_enabled ? 'is_tickets_enabled' : 'is_chat_enabled';

		$move_department = null;

		$tree_ids = $this->em->getRepository('DeskPRO:Department')->getIdsInTree($department->id, true);
		$tree_ids = implode(',', $tree_ids);

		$ticket_count = $this->db->fetchColumn("
			SELECT COUNT(*) FROM tickets
			WHERE department_id IN ($tree_ids)
			LIMIT 1
		");
		$chat_count = $this->db->fetchColumn("
			SELECT COUNT(*) FROM chat_conversations
			WHERE department_id IN ($tree_ids)
			LIMIT 1
		");

		$has_data = ($ticket_count || $chat_count);

		if ($has_data) {
			$move_department = $this->em->getRepository('DeskPRO:Department')->find($this->in->getUint('move_to_department'));
			if (!$move_department || !$move_department[$type_prop]) {
				return $this->renderStandardError('You need to choose a department to move existing data into.');
			} elseif (count($move_department->children)) {
				return $this->renderStandardError('You chose an invalid department to move existing data into. The new department cannot have children.');
			}
		}

		if (!$this->session->getEntity()->checkSecurityToken('delete_department', $security_token)) {
			return $this->renderStandardTokenError();
		}

		$this->em->beginTransaction();

		if ($has_data) {

			$ticket_ids = $this->db->fetchAllCol("
				SELECT id FROM tickets
				WHERE department_id IN ($tree_ids)
			");

			$ticket_ids = array_chunk($ticket_ids, 2500);

			$details_arr = serialize(array(
				'id_before' => $department->getId(),
				'id_after'  => $move_department->getId(),

				'old_department_id'    => $department->getId(),
				'old_department_title' => $department->getTitle(),
				'new_department_id'    => $move_department->getId(),
				'new_department_title' => $move_department->getTitle(),
			));

			$date_created = date('Y-m-d H:i:s');

			foreach ($ticket_ids as $ids) {
				$ids_string = implode(',', $ids);

				$this->db->beginTransaction();
				try {
					$this->db->executeUpdate("
						UPDATE tickets
						SET department_id = ?
						WHERE id IN ($ids_string)
					", array($move_department->id));

					$batch_logs = array();
					foreach ($ids as $id) {
						$batch_logs[] = array(
							'ticket_id'    => $id,
							'action_type'  => 'changed_department',
							'id_before'    => $department_id,
							'id_after'     => $move_department->getId(),
							'details'      => $details_arr,
							'date_created' => $date_created
						);
					}

					$this->db->batchInsert('tickets_logs', $batch_logs);
					$this->db->commit();
				} catch (\Exception $e) {
					$this->db->rollback();
					throw $e;
				}
			}

			$this->db->executeUpdate("
				UPDATE chat_conversations
				SET department_id = ?
				WHERE department_id IN ($tree_ids)
			", array($move_department->id));
		}

		foreach ($department->children as $c) {
			$this->em->remove($c);
		}
		$this->em->remove($department);
		$this->em->flush();
		$this->em->commit();

		$this->session->setFlash('deleted', $department->title);

		$this->sendAgentReloadSignal();

		$type = $department->is_tickets_enabled ? 'tickets' : 'chat';
		return $this->redirectRoute('admin_departments', array('type' => $type));
	}

	############################################################################
	# update-orders
	############################################################################

	public function updateOrdersAction()
	{
		$helper = new \Application\AdminBundle\Controller\Helper\DisplayOrderUpdate($this);
		return $helper->doUpdate('departments');
	}
}
