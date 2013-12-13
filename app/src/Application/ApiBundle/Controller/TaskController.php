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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Searcher\TaskSearch;
use Application\DeskPRO\Entity\Task;
use Orb\Util\Numbers;

class TaskController extends AbstractController
{
	public function searchAction()
	{
		$search_map = array(
			'assigned_agent_id' => TaskSearch::TERM_ASSIGNED_AGENT_ID,
			'assigned_agent_team_id' => TaskSearch::TERM_ASSIGNED_AGENT_TEAM_ID,
			'is_completed' => TaskSearch::TERM_IS_COMPLETED,
			'person_id' => TaskSearch::TERM_PERSON_ID,
			'title' => TaskSearch::TERM_TITLE,
			'visibility' => TaskSearch::TERM_VISIBILITY
		);

		$terms = array();

		foreach ($search_map AS $input => $search_key) {
			$value = $this->in->getCleanValueArray($input, 'raw', 'discard');
			if ((is_string($value) && strlen($value) > 0) || (!is_string($value) && $value)) {
				$terms[] = array('type' => $search_key, 'op' => 'contains', 'options' => $value);
			}
		}

		$date_created_start = $this->in->getUint('date_created_start');
		$date_created_end = $this->in->getUint('date_created_end');
		if ($date_created_end) {
			$terms[] = array('type' => TaskSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
				'date1' => $date_created_start,
				'date2' => $date_created_end
			));
		} else if ($date_created_start) {
			$terms[] = array('type' => TaskSearch::TERM_DATE_CREATED, 'op' => 'between', 'options' => array(
				'date1' => $date_created_start
			));
		}

		$date_completed_start = $this->in->getUint('date_completed_start');
		$date_completed_end = $this->in->getUint('date_completed_end');
		if ($date_completed_end) {
			$terms[] = array('type' => TaskSearch::TERM_DATE_COMPLETED, 'op' => 'between', 'options' => array(
				'date1' => $date_completed_start,
				'date2' => $date_completed_end
			));
		} else if ($date_completed_start) {
			$terms[] = array('type' => TaskSearch::TERM_DATE_COMPLETED, 'op' => 'between', 'options' => array(
				'date1' => $date_completed_start
			));
		}

		$date_due_start = $this->in->getUint('date_due_start');
		$date_due_end = $this->in->getUint('date_due_end');
		if ($date_due_end) {
			$terms[] = array('type' => TaskSearch::TERM_DATE_DUE, 'op' => 'between', 'options' => array(
				'date1' => $date_due_start,
				'date2' => $date_due_end
			));
		} else if ($date_due_start) {
			$terms[] = array('type' => TaskSearch::TERM_DATE_DUE, 'op' => 'between', 'options' => array(
				'date1' => $date_due_start
			));
		}

		if ($this->in->checkIsset('order')) {
			$order_by = $this->in->getString('order');
		} else {
			$order_by = 'task.name:asc';
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('task', $terms, $extra, $this->in->getUint('cache_id'), new TaskSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$person_ids = $result_cache->results;

		$page_ids = \Orb\Util\Arrays::getPageChunk($person_ids, $page, $per_page);
		$tasks = App::getEntityRepository('DeskPRO:Task')->getByIds($page_ids, true);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => count($person_ids),
			'cache_id' => $result_cache->id,
			'tasks' => $this->getApiData($tasks)
		));
	}

	public function newTaskAction()
	{
		$task = new Task();
		$errors = array();

		$title = $this->in->getString('title');
		if (!$title) {
			$errors['title'] = array('required_field.title', 'title is empty or missing');
		}

		$task->title = $title;
		$task->person = $this->person;

		$ticket_id = $this->in->getUint('ticket_id');
		if (!empty($ticket_id)) {
			$ticket = $this->em->find('DeskPRO:Ticket', $ticket_id);
			if ($ticket) {
				$assoc = new \Application\DeskPRO\Entity\TaskAssociatedTicket();
				$assoc->ticket = $ticket;
				$assoc->task   = $task;

				$task->task_associations->add($assoc);
			}
		}

		if ($this->in->checkIsset('visibility')) {
			$task->setVisibility($this->in->getUint('visibility'));
		}

		$date_due = $this->in->getUint('date_due');
		if ($date_due) {
			$task->date_due = new \DateTime('@' . $date_due);
		}

		if ($this->in->checkIsset('assigned_agent_id')) {
			$assigned_agent_id = $this->in->getUint('assigned_agent_id');
			if ($assigned_agent_id) {
				$task->setAsignedAgentId($assigned_agent_id);
			} else {
				$task->assigned_agent = null;
			}
		} else {
			$task->assigned_agent = $this->person;
		}

		$assigned_agent_team_id = $this->in->getUint('assigned_agent_team_id');
		if ($assigned_agent_team_id) {
			$task->setAsignedAgentTeamId($assigned_agent_team_id);
		}

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		$this->db->beginTransaction();

		try {
			$this->em->persist($task);
			$this->em->flush();

			$labels = $this->in->getCleanValueArray('label', 'string', 'discard');
			if ($labels) {
				$task->getLabelManager()->setLabelsArray($labels, $this->em);
				$this->em->flush();
			}

			$notify = new \Application\DeskPRO\Notifications\TaskAssignNotification($task);
			$notify->send();

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createApiCreateResponse(
			array('id' => $task->id),
			$this->generateUrl('api_tasks_task', array('task_id' => $task->id), true)
		);
	}

	public function getTaskAction($task_id)
	{
		$task = $this->_getTaskOr404($task_id);

		return $this->createApiResponse(array('task' => $task->toApiData()));
	}

	public function postTaskAction($task_id)
	{
		$task = $this->_getTaskOr404($task_id);

		$title = $this->in->getString('title');
		if ($title) {
			$task->title = $title;
		}

		if ($this->in->checkIsset('visibility')) {
			$task->setVisibility($this->in->getUint('visibility'));
		}

		if ($this->in->checkIsset('date_due')) {
			$date_due = $this->in->getUint('date_due');
			if ($date_due) {
				$task->date_due = new \DateTime('@' . $date_due);
			} else {
				$task->date_due = null;
			}
		}

		if ($this->in->checkIsset('completed')) {
			$task->setCompleted($this->in->getBool('completed'));

			if ($this->in->getBool('completed')) {
				$notify = new \Application\DeskPRO\Notifications\TaskCompleteNotification($task);
				$notify->send();
			}
		}

		$send_cm = false;

		if ($this->in->checkIsset('assigned_agent_id')) {
			$assigned_agent_id = $this->in->getUint('assigned_agent_id');
			if ($assigned_agent_id) {
				$task->setAsignedAgentId($assigned_agent_id);
			} else {
				$task->assigned_agent = null;
			}
			$send_cm = true;
		}

		if ($this->in->checkIsset('assigned_agent_team_id')) {
			$assigned_agent_team_id = $this->in->getUint('assigned_agent_team_id');
			if ($assigned_agent_team_id) {
				$task->setAsignedAgentTeamId($assigned_agent_team_id);
			} else {
				$task->assigned_agent_team = null;
			}
			$send_cm = true;
		}

		$this->em->persist($task);
		$this->em->flush();

		if ($send_cm) {
			$notify = new \Application\DeskPRO\Notifications\TaskAssignNotification($task);
			$notify->send();
		}

		return $this->createSuccessResponse();
	}

	public function deleteTaskAction($task_id)
	{
		$task = $this->_getTaskOr404($task_id);

		if ($task->person->getId() != $this->person->getId()) {
			return $this->createNotFoundException();
		}

		$this->em->remove($task);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getTaskAssociationsAction($task_id)
	{
		$task = $this->_getTaskOr404($task_id);

		return $this->createApiResponse(array('associations' => $this->getApiData($task->task_associations)));
	}

	public function postTaskAssociationsAction($task_id)
	{
		$task = $this->_getTaskOr404($task_id);

		$ticket_id = $this->in->getUint('ticket_id');
		$ticket = false;
		$assoc = false;
		if (!empty($ticket_id)) {
			$ticket = $this->em->find('DeskPRO:Ticket', $ticket_id);
			if ($ticket) {
				$assoc = new \Application\DeskPRO\Entity\TaskAssociatedTicket();
				$assoc->ticket = $ticket;
				$assoc->task   = $task;

				$task->task_associations->add($assoc);
			}
		}

		if (!$assoc) {
			return $this->createApiErrorResponse('invalid_argument.ticket_id', 'ticket_id is not valid');
		}

		$this->em->persist($task);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $assoc->id),
			$this->generateUrl('api_tasks_task_associated_item', array('task_id' => $task->id, 'assoc_id' => $assoc->id), true)
		);
	}

	public function getTaskAssociationAction($task_id, $assoc_id)
	{
		$task = $this->_getTaskOr404($task_id);

		$exists = false;
		foreach ($task->task_associations AS $assoc) {
			if ($assoc->id == $assoc_id) {
				$exists = true;
				break;
			}
		}

		return $this->createApiResponse(array('exists' => $exists));
	}

	public function deleteTaskAssociationAction($task_id, $assoc_id)
	{
		$task = $this->_getTaskOr404($task_id);

		$exists = false;
		foreach ($task->task_associations AS $key => $assoc) {
			if ($assoc->id == $assoc_id) {
				$task->task_associations->remove($key);
				$this->em->persist($task);
				$this->em->flush();
				break;
			}
		}

		return $this->createSuccessResponse();
	}

	public function getTaskCommentsAction($task_id)
	{
		$task = $this->_getTaskOr404($task_id);

		return $this->createApiResponse(array('comments' => $this->getApiData($task->comments)));
	}

	public function postTaskCommentsAction($task_id)
	{
		$task = $this->_getTaskOr404($task_id);

		$comment_txt = $this->in->getString('comment');
		if (!$comment_txt) {
			return $this->createApiErrorResponse('required_field.comment', 'comment is missing or empty');
		}

		$comment = new \Application\DeskPRO\Entity\TaskComment($this->person, $comment_txt);
        $comment['person'] = $this->person;
        $comment['task'] = $task;
        $comment['content'] = $comment_txt;

        $this->em->persist($comment);
        $this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $comment->id),
			$this->generateUrl('api_tasks_task_comment', array('task_id' => $task->id, 'comment_id' => $comment->id), true)
		);
	}

	public function getTaskCommentAction($task_id, $comment_id)
	{
		$task = $this->_getTaskOr404($task_id);

		$exists = false;
		foreach ($task->comments AS $comment) {
			if ($comment->id == $comment_id) {
				$exists = true;
				break;
			}
		}

		return $this->createApiResponse(array('exists' => $exists));
	}

	public function deleteTaskCommentAction($task_id, $comment_id)
	{
		$task = $this->_getTaskOr404($task_id);

		$exists = false;
		foreach ($task->comments AS $key => $comment) {
			if ($comment->id == $comment_id) {
				if ($comment->person->getId() != $this->person->getId()) {
					return $this->createNotFoundException();
				}

				$task->comments->remove($key);
				$this->em->persist($task);
				$this->em->flush();
				break;
			}
		}

		return $this->createSuccessResponse();
	}

	public function getTaskLabelsAction($task_id)
	{
		$task = $this->_getTaskOr404($task_id);

		return $this->createApiResponse(array('labels' => $this->getApiData($task->labels)));
	}

	public function postTaskLabelsAction($task_id)
	{
		$task = $this->_getTaskOr404($task_id);
		$label = $this->in->getString('label');

		if ($label === '') {
			return $this->createApiErrorResponse('required_field.label', "Field 'label' missing or empty");
		}

		$task->getLabelManager()->addLabel($label);
		$this->em->persist($task);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('label' => $label),
			$this->generateUrl('api_tasks_task_label', array('task_id' => $task->id, 'label' => $label), true)
		);
	}

	public function getTaskLabelAction($task_id, $label)
	{
		$task = $this->_getTaskOr404($task_id);

		if ($task->getLabelManager()->hasLabel($label)) {
			return $this->createApiResponse(array('exists' => true));
		} else {
			return $this->createApiResponse(array('exists' => false));
		}
	}

	public function deleteTaskLabelAction($task_id, $label)
	{
		$task = $this->_getTaskOr404($task_id);

		$task->getLabelManager()->removeLabel($label);
		$this->em->persist($task);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\Task
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getTaskOr404($id)
	{
		$task = $this->em->getRepository('DeskPRO:Task')->findOneById($id);

		if (!$task) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no task with ID $id");
		}

		return $task;
	}
}
