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
 * @subpackage InstallBundle
 */

namespace Application\InstallBundle\Data;

use Application\DeskPRO\App;
use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;

class DataInitializer
{
	/**
	 * @var \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	protected $container;

	/**
	 * @var bool
	 */
	protected $is_import = false;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	public $admin_user;

	public function __construct(DeskproContainer $container)
	{
		$this->container = $container;
	}

	public function setImportMode()
	{
		$this->is_import = true;
	}

	public function getAdminUser()
	{
		if ($this->admin_user) {
			return $this->admin_user;
		}

		$this->admin_user = $this->container->getEm()
				->createQuery("SELECT p FROM DeskPRO:Person p WHERE p.is_agent = true AND p.can_admin = true ORDER BY p.id DESC")
				->setMaxResults(1)
				->getOneOrNullResult();

		return $this->admin_user;
	}

	public function run()
	{
		$this->runSearchIndex();
		$this->runInitPerms();
		$this->runInitAdminNotifications();
		$this->runInitDefaultSla();
		$this->runInitInitialData();
	}

	public function runInitPerms()
	{
		if ($this->is_import) {
			return;
		}

		// By default everyone can see the default categories
		$this->container->getDb()->insert('article_category2usergroup', array('category_id' => 1, 'usergroup_id' => 1));
		$this->container->getDb()->insert('news_category2usergroup', array('category_id' => 1, 'usergroup_id' => 1));
		$this->container->getDb()->insert('download_category2usergroup', array('category_id' => 1, 'usergroup_id' => 1));
		$this->container->getDb()->insert('feedback_category2usergroup', array('category_id' => 1, 'usergroup_id' => 1));

		// Initial agent has access to all deps
		$this->container->getDb()->insert('department_permissions', array('department_id' => 1, 'person_id' => 1, 'app' => 'tickets', 'name' => 'full', 'value' => 1));
		$this->container->getDb()->insert('department_permissions', array('department_id' => 2, 'person_id' => 1, 'app' => 'tickets', 'name' => 'full', 'value' => 1));
		$this->container->getDb()->insert('department_permissions', array('department_id' => 3, 'person_id' => 1, 'app' => 'chat', 'name' => 'full', 'value' => 1));
		$this->container->getDb()->insert('department_permissions', array('department_id' => 4, 'person_id' => 1, 'app' => 'chat', 'name' => 'full', 'value' => 1));

		// The everyone group has access to all deps too
		$this->container->getDb()->insert('department_permissions', array('department_id' => 1, 'usergroup_id' => 1, 'app' => 'tickets', 'name' => 'full', 'value' => 1));
		$this->container->getDb()->insert('department_permissions', array('department_id' => 2, 'usergroup_id' => 1, 'app' => 'tickets', 'name' => 'full', 'value' => 1));
		$this->container->getDb()->insert('department_permissions', array('department_id' => 3, 'usergroup_id' => 1, 'app' => 'chat', 'name' => 'full', 'value' => 1));
		$this->container->getDb()->insert('department_permissions', array('department_id' => 4, 'usergroup_id' => 1, 'app' => 'chat', 'name' => 'full', 'value' => 1));
	}

	public function runSearchIndex()
	{
		if ($this->is_import) {
			return;
		}
		$types = array(
			array('article',   'articles',  'DeskPRO:Article'),
			array('download',  'downloads', 'DeskPRO:Download'),
			array('feedback',  'feedback',  'DeskPRO:Feedback'),
			array('news',      'news',      'DeskPRO:News'),
		);

		foreach ($types as $t) {
			list ($content_type, $table, $entity) = $t;
			$all_ids = $this->container->getDb()->fetchAllCol("SELECT id FROM $table ORDER BY id ASC");
			$batch = $this->container->getEm()->getRepository($entity)->getByIds($all_ids);
			if ($batch) {
				$this->container->getSearchAdapter()->updateObjectsInIndex($batch);
			}
		}
	}

	public function runInitAdminNotifications()
	{
		if ($this->is_import) {
			return;
		}

		$agent = \Application\DeskPRO\App::getOrm()->createQuery("SELECT p FROM DeskPRO:Person p WHERE p.can_admin = 1 ORDER BY p.id ASC")
			->setMaxResults(1)
			->getOneOrNullResult();

		// Possible to be in import mode and no admin
		if (!$agent) {
			return;
		}

		for ($i = 1; $i <= 5; $i++) {
			$this->container->getDb()->insert('ticket_filter_subscriptions', array(
				'filter_id' => $i,
				'person_id' => $agent->id,
				'email_created' => 1,
				'email_new' => 1,
				'email_user_activity' => 1,
				'email_agent_activity' => 1,
				'email_agent_note' => 1,
				'email_property_change' => 1,
				'alert_created' => 1,
				'alert_new' => 1,
				'alert_user_activity' => 1,
				'alert_agent_activity' => 1,
				'alert_property_change' => 1,
			));
		}

		$prefs = array();
		$prefs['chat_message.email'] = 1;
		$prefs['login_attempt_fail.email'] = 1;
		$prefs['task_assign_self.email'] = 1;
		$prefs['task_assign_self.alert'] = 1;
		$prefs['task_assign_team.email'] = 1;
		$prefs['task_assign_team.alert'] = 1;
		$prefs['task_complete.email'] = 1;
		$prefs['task_complete.alert'] = 1;
		$prefs['task_due.email'] = 1;
		$prefs['task_due.alert'] = 1;
		$prefs['tweet_assign_self.email'] = 1;
		$prefs['tweet_assign_self.alert'] = 1;
		$prefs['tweet_assign_team.email'] = 1;
		$prefs['tweet_assign_team.alert'] = 1;
		$prefs['tweet_reply.email'] = 1;
		$prefs['tweet_reply.alert'] = 1;
		$prefs['tweet_new_dm.email'] = 1;
		$prefs['tweet_new_dm.alert'] = 1;
		$prefs['tweet_new_reply.email'] = 1;
		$prefs['tweet_new_reply.alert'] = 1;
		$prefs['tweet_new_mention.email'] = 1;
		$prefs['tweet_new_mention.alert'] = 1;
		$prefs['tweet_new_retweet.email'] = 1;
		$prefs['tweet_new_retweet.alert'] = 1;
		$prefs['new_feedback.email'] = 1;
		$prefs['new_feedback.alert'] = 1;
		$prefs['new_feedback_validate.email'] = 1;
		$prefs['new_feedback_validate.alert'] = 1;
		$prefs['new_comment.email'] = 1;
		$prefs['new_comment.alert'] = 1;
		$prefs['new_comment_validate.email'] = 1;
		$prefs['new_comment_validate.alert'] = 1;

		foreach ($prefs as $p => $v) {
			$this->container->getDb()->insert('people_prefs', array(
				'person_id' => $agent->id,
				'name' => 'agent_notif.' . $p,
				'value_str' => $v,
				'value_array' => 'N;',
			));
		}
	}

	public function runInitDefaultSla()
	{
		$db = $this->container->getDb();
		$count = $db->fetchColumn("
			SELECT COUNT(*)
			FROM slas
		");
		if ($count) {
			return;
		}

		$db->insert('slas', array(
			'title' => 'First Response',
			'sla_type' => 'first_response',
			'active_time' => 'default',
			'work_start' => 32400,
			'work_end' => 61200,
			'work_days' => serialize(array(1 => true, 2 => true,3 => true, 4 => true, 5 => true)),
			'work_timezone' => \Application\DeskPRO\App::getSetting('core.default_timezone'),
			'work_holidays' => serialize(array()),
			'apply_type' => 'all',
		));
		$sla_id = $db->lastInsertId();

		$db->insert('ticket_triggers', array(
			'title' => 'First Response - SLA Warning',
			'event_trigger' => 'sla.warning',
			'is_enabled' => 1,
			'terms' => serialize(array(
				array('type' => 'sla_status', 'op' => 'is', 'options' => array('sla_status' => 'warn', 'sla_id' => $sla_id))
			)),
			'actions' => serialize(array(
				array('type' => 'recalculate_sla_status', 'options' => array())
			)),
			'sys_name' => NULL,
			'run_order' => 5400,
			'event_trigger_options' => serialize(array(
				'time' => '90 minutes'
			)),
			'terms_any' => serialize(array()),
			'date_created' => gmdate('Y-m-d H:i:s')
		));
		$warning_trigger_id = $db->lastInsertId();

		$db->insert('ticket_triggers', array(
			'title' => 'First Response - SLA Failure',
			'event_trigger' => 'sla.fail',
			'is_enabled' => 1,
			'terms' => serialize(array(
				array('type' => 'sla_status', 'op' => 'is', 'options' => array('sla_status' => 'fail', 'sla_id' => $sla_id))
			)),
			'actions' => serialize(array(
				array('type' => 'recalculate_sla_status', 'options' => array())
			)),
			'sys_name' => NULL,
			'run_order' => 7200,
			'event_trigger_options' => serialize(array(
				'time' => '120 minutes'
			)),
			'terms_any' => serialize(array()),
			'date_created' => gmdate('Y-m-d H:i:s')
		));
		$fail_trigger_id = $db->lastInsertId();

		$db->update('slas', array(
			'warning_trigger_id' => $warning_trigger_id,
			'fail_trigger_id' => $fail_trigger_id
		), array('id' => $sla_id));
	}

	public function runInitInitialData()
	{
		if ($this->is_import) {
			return;
		}
	}

	public static function newDefaultTicket($for_agent)
	{
		$department = App::getDataService('Department')->getDefaultTicketDepartment();

		$user = App::getOrm()->getRepository('DeskPRO:Person')->findOneByEmail('support@deskpro.com');
		if (!$user) {
			$user = Person::newContactPerson(array(
				'name' => 'Christopher Padfield',
				'email' => 'support@deskpro.com',
				'is_confirmed' => true,
			));
			$user->getPrimaryEmail()->is_validated = true;
			App::getOrm()->persist($user);
		}

		$ticket = new Ticket();
		$ticket->getTicketLogger()->recordExtra('is_install', true);
		$ticket->creation_system = Ticket::CREATED_WEB_PERSON;
		$ticket->person          = $user;
		$ticket->agent           = $for_agent;
		$ticket->department      = $department;
		$ticket->subject         = 'Welcome to DeskPRO';
		$ticket->status          = Ticket::STATUS_AWAITING_AGENT;
		$ticket->setProperty('send_reply_service', 'https://support.deskpro.com/api/open/tickets/new-ticket-message');
		$ticket->setProperty('allow_send_reply_service', true);

		App::getOrm()->persist($ticket);

		$agent_name = $for_agent->getDisplayName();

		$message = new TicketMessage();
		$message->person  = $user;
		$message->ticket  = $ticket;
		$message->message = <<<STR
Hello $agent_name,<br /><br />

Welcome to DeskPRO. This is a sample ticket that demonstrates how the system will look when a user submits a new ticket. Feel free to close or delete this whenever you want.<br /><br />

If you have any questions or run into any problems, you can simply reply to this ticket or you can always visit our helpdesk at <a href="http://support.deskpro.com/">support.deskpro.com</a>.<br /><br />

Best Regards,<br /><br />

Christopher Padfield<br />
<a href="http://www.deskpro.com/">www.deskpro.com</a>
STR;
		$ticket->addMessage($message);

		App::getOrm()->persist($message);
		App::getOrm()->flush();
	}
}
