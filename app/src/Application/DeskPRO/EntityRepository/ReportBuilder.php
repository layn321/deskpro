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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;
use Orb\Util\Numbers;

class ReportBuilder extends AbstractEntityRepository
{
	/**
	 * Gets all reports
	 *
	 * @return \Application\DeskPRO\Entity\ReportBuilder[]
	 */
	public function getAllReports()
	{
		return $this->getEntityManager()->createQuery('
			SELECT rb
			FROM DeskPRO:ReportBuilder rb
			ORDER BY rb.display_order, rb.title
		')->execute();
	}

	public function getByUniqueKey($key)
	{
		return $this->getEntityManager()->createQuery('
			SELECT rb
			FROM DeskPRO:ReportBuilder rb
			WHERE rb.unique_key = ?0
		')->setParameters(array($key))->getOneOrNullResult();
	}

	public function findFavorite(
		\Application\DeskPRO\Entity\ReportBuilder $report,
		\Application\DeskPRO\Entity\Person $person = null,
		array $params = array()
	)
	{
		if (!$person) {
			$person = App::getCurrentPerson();
		}

		ksort($params);

		return $this->getEntityManager()->createQuery('
			SELECT f
			FROM DeskPRO:ReportBuilderFavorite f
			WHERE f.report_builder = ?0 AND f.person = ?1 AND f.params = ?2
		')->setParameters(array($report, $person, $params ? implode(',', $params) : ''))->getOneOrNullResult();
	}

	public function getFavoritesForPerson(\Application\DeskPRO\Entity\Person $person = null)
	{
		if (!$person) {
			$person = App::getCurrentPerson();
		}

		return $this->getEntityManager()->createQuery('
			SELECT f, r
			FROM DeskPRO:ReportBuilderFavorite f
			JOIN f.report_builder r
			WHERE f.person = ?0
			ORDER BY r.title
		')->execute(array($person));
	}

	public function getFavoritesSimplified(array $favorites)
	{
		$output = array();
		foreach ($favorites AS $fav) {
			$output[] = array('id' => $fav->report_builder->id, 'params' => $fav->params);
		}

		return $output;
	}

	/**
	 * Groups a list of reports for use in the reports list. Returns lists of
	 * reports in these keys:
	 *  - custom: list of custom reports
	 *  - builtIn: grouped list of built-in reports. Grouped by printable name of the group.
	 *
	 * @return array
	 */
	public function groupReportsList()
	{
		$reports = $this->getAllReports();

		$custom = array();
		$builtIn = array();
		$categories = $this->getBuiltInCategories();

		foreach ($reports AS $report) {
			if ($report->is_custom) {
				$custom[] = $report;
			} else {
				if (isset($categories[$report->category])) {
					$categoryId = $report->category;
				} else {
					$categoryId = '';
				}
				$builtIn[$categoryId][] = $report;
			}
		}

		$builtInOrdered = array();
		foreach ($categories AS $categoryId => $categoryName)
		{
			if (isset($builtIn[$categoryId])) {
				$builtInOrdered[$categoryName] = $builtIn[$categoryId];
			}
		}

		return array(
			'custom' => $custom,
			'builtIn' => $builtInOrdered
		);
	}

	/**
	 * Gets the list of built-in report grouping categories.
	 *
	 * @return array
	 */
	public function getBuiltInCategories()
	{
		return array(
			'ticket' => 'Tickets',
			'chat' => 'Chats',
			'idea' => 'Ideas',
			'person' => 'People & Organizations',
			'kb' => 'Knowledgebase',
			'news' => 'News',
			'downloads' => 'Downloads',
			'feedback' => 'Feedback',
			'tasks' => 'Tasks',
			'twitter' => 'Twitter'
		);
	}

	/**
	 * @return boolean
	 */
	public function canManageBuiltInReports()
	{
		return (bool)App::getConfig('debug.dev');
	}

	public function getReportGroupParams()
	{
		$return = array(
			'fields' => array(
				'tickets' => array(
					'department' => array('department', 'ALIAS(STACK_GROUP(%1$s.department, COALESCE(%1$s.department.parent.title, %1$s.department.title)), \'Department\')'),
					'agent' => array('agent', '%s.agent'),
					'agent_team' => array('agent team', '%s.agent_team'),
					'person' => array('person', '%s.person'),
					'organization' => array('organization', '%s.organization'),
					'language' => array('language', '%s.language'),
					'urgency' => array('urgency', '%s.urgency'),
					'category' => array('category', '%s.category'),
					'product' => array('product', '%s.product'),
					'priority' => array('priority', '%s.priority'),
					'workflow' => array('workflow', '%s.workflow'),
					'sla' => array('SLA', '%s.ticket_slas'),
					'sla_status' => array('SLA status', '%s.ticket_slas.sla_status'),
					'agent_replies' => array('number of agent replies', 'ALIAS(%s.count_agent_replies, \'Agent Replies\')'),
					'user_replies' => array('number of user replies', 'ALIAS(%s.count_user_replies, \'User Replies\')'),
					'replies' => array('number of replies', 'ALIAS(%1$s.count_user_replies + %1$s.count_agent_replies, \'Total Replies\')'),
					// todo: ticket rating
					'hour_created' => array('hour created', 'ALIAS(HOUR(%s.date_created), \'Hour Created\')'),
					'day_week_created' => array('day of week created', 'ALIAS(DAYNAME(%s.date_created), \'Day of Week Created\')'),
					'day_month_created' => array('day of month created', 'ALIAS(DAYOFMONTH(%s.date_created), \'Day of Month Created\')'),
					'month_created' => array('month created', 'ALIAS(MONTHNAME(%s.date_created), \'Month Created\')'),
					'year_created' => array('year created', 'ALIAS(YEAR(%s.date_created), \'Year Created\')'),
					'date_created' => array('date created', 'ALIAS(DATE(%s.date_created), \'Date Created\')')
				),
				'chats' => array(
					'department' => array('department', '%s.department'),
					'agent' => array('agent', '%s.agent'),
					'agent_team' => array('agent team', '%s.agent_team'),
					'person' => array('person', '%s.person'),
					'hour_created' => array('hour created', 'ALIAS(HOUR(%s.date_created), \'Hour Created\')'),
					'day_week_created' => array('day of week created', 'ALIAS(DAYNAME(%s.date_created), \'Day of Week Created\')'),
					'day_month_created' => array('day of month created', 'ALIAS(DAYOFMONTH(%s.date_created), \'Day of Month Created\')'),
					'month_created' => array('month created', 'ALIAS(MONTHNAME(%s.date_created), \'Month Created\')'),
					'year_created' => array('year created', 'ALIAS(YEAR(%s.date_created), \'Year Created\')'),
					'date_created' => array('date created', 'ALIAS(DATE(%s.date_created), \'Date Created\')'),
					'none' => array('nothing', 'NULL')
				),
				'articles' => array(
					'person' => array('person', '%s.person'),
					'hour_created' => array('hour created', 'ALIAS(HOUR(%s.date_created), \'Hour Created\')'),
					'day_week_created' => array('day of week created', 'ALIAS(DAYNAME(%s.date_created), \'Day of Week Created\')'),
					'day_month_created' => array('day of month created', 'ALIAS(DAYOFMONTH(%s.date_created), \'Day of Month Created\')'),
					'month_created' => array('month created', 'ALIAS(MONTHNAME(%s.date_created), \'Month Created\')'),
					'year_created' => array('year created', 'ALIAS(YEAR(%s.date_created), \'Year Created\')'),
					'date_created' => array('date created', 'ALIAS(DATE(%s.date_created), \'Date Created\')'),
					'none' => array('nothing', 'NULL')
				),
				'article_comments' => array(
					'hour_created' => array('hour created', 'ALIAS(HOUR(%s.date_created), \'Hour Created\')'),
					'day_week_created' => array('day of week created', 'ALIAS(DAYNAME(%s.date_created), \'Day of Week Created\')'),
					'day_month_created' => array('day of month created', 'ALIAS(DAYOFMONTH(%s.date_created), \'Day of Month Created\')'),
					'month_created' => array('month created', 'ALIAS(MONTHNAME(%s.date_created), \'Month Created\')'),
					'year_created' => array('year created', 'ALIAS(YEAR(%s.date_created), \'Year Created\')'),
					'date_created' => array('date created', 'ALIAS(DATE(%s.date_created), \'Date Created\')'),
					'none' => array('nothing', 'NULL')
				),
				'feedback' => array(
					'type' => array('type', 'ALIAS(%s.category, \'Type\')'),
					'status' => array('status', 'ALIAS(%s.status_category, \'Status\')'),
					'category' => array('category', 'ALIAS(%s.custom_data[1], \'category\')'),
					'person' => array('person', '%s.person'),
					'hour_created' => array('hour created', 'ALIAS(HOUR(%s.date_created), \'Hour Created\')'),
					'day_week_created' => array('day of week created', 'ALIAS(DAYNAME(%s.date_created), \'Day of Week Created\')'),
					'day_month_created' => array('day of month created', 'ALIAS(DAYOFMONTH(%s.date_created), \'Day of Month Created\')'),
					'month_created' => array('month created', 'ALIAS(MONTHNAME(%s.date_created), \'Month Created\')'),
					'year_created' => array('year created', 'ALIAS(YEAR(%s.date_created), \'Year Created\')'),
					'date_created' => array('date created', 'ALIAS(DATE(%s.date_created), \'Date Created\')'),
					'none' => array('nothing', 'NULL')
				),
				'feedback_comments' => array(
					'hour_created' => array('hour created', 'ALIAS(HOUR(%s.date_created), \'Hour Created\')'),
					'day_week_created' => array('day of week created', 'ALIAS(DAYNAME(%s.date_created), \'Day of Week Created\')'),
					'day_month_created' => array('day of month created', 'ALIAS(DAYOFMONTH(%s.date_created), \'Day of Month Created\')'),
					'month_created' => array('month created', 'ALIAS(MONTHNAME(%s.date_created), \'Month Created\')'),
					'year_created' => array('year created', 'ALIAS(YEAR(%s.date_created), \'Year Created\')'),
					'date_created' => array('date created', 'ALIAS(DATE(%s.date_created), \'Date Created\')'),
					'none' => array('nothing', 'NULL')
				)
			),
			'dates' => array(
				'today' => array('today', '%TODAY%'),
				'yesterday' => array('yesterday', '%YESTERDAY%'),
				'this_week' => array('this week', '%THIS_WEEK%'),
				'this_month' => array('this month', '%THIS_MONTH%'),
				'this_year' => array('this year', '%THIS_YEAR%'),
				'last_week' => array('last week', '%LAST_WEEK%'),
				'last_month' => array('last month', '%LAST_MONTH%'),
				'last_year' => array('last year', '%LAST_YEAR%'),
				'past_24_hours' => array('in the past 24 hours', '%PAST_24_HOURS%'),
				'past_7_days' => array('in the past 7 days', '%PAST_7_DAYS%'),
				'past_30_days' => array('in the past 30 days', '%PAST_30_DAYS%'),
				'ever' => array('any time', '%EVER%')
			),
			'statuses' => array(
				'tickets' => array(
					'awaiting_user' => array('awaiting user', '%s.status = \'awaiting_user\''),
					'awaiting_agent' => array('awaiting agent', '%s.status = \'awaiting_agent\''),
					'unresolved' => array('unresolved', '%s.status IN (\'awaiting_user\', \'awaiting_agent\')'),
					'resolved' => array('resolved', '%s.status IN (\'resolved\', \'closed\')'),
					'hidden' => array('hidden', '%s.status = \'hidden\''),
					'any' => array('with any status', '1')
				)
			),
			'orders' => array(
				'tickets' => array(
					// todo: number of messages
					'date_created_asc' => array('date created (ascending)', '%s.date_created ASC'),
					'date_created_desc' => array('date created (descending)', '%s.date_created DESC'),
					'last_agent_reply_asc' => array('last agent reply (ascending)', '%s.date_last_agent_reply ASC'),
					'last_agent_reply_desc' => array('last agent reply (descending)', '%s.date_last_agent_reply DESC'),
					'last_user_reply_asc' => array('last user reply (ascending)', '%s.date_last_user_reply ASC'),
					'last_user_reply_desc' => array('last user reply (descending)', '%s.date_last_user_reply DESC'),
					'total_waiting_asc' => array('total waiting time (ascending)', '%s.total_user_waiting ASC'),
					'total_waiting_desc' => array('total waiting time (descending)', '%s.total_user_waiting DESC')
				)
			)
		);

		$fields = $this->getEntityManager()->getRepository('DeskPRO:CustomDefTicket')->getTopFields();
		foreach ($fields AS $field) {
			$escaped = addslashes($field->title);
			$return['fields']['tickets']['ticketfield' . $field->id] = array(
				$field->title, 'ALIAS(%s.custom_data[' . $field->id . '], \'' . $escaped . '\')'
			);
		}

		$fields = $this->getEntityManager()->getRepository('DeskPRO:CustomDefPerson')->getTopFields();
		foreach ($fields AS $field) {
			$escaped = addslashes($field->title);
			$return['fields']['tickets']['personfield' . $field->id] = array(
				"creator's " . $field->title, 'ALIAS(%s.person.custom_data[' . $field->id . '], \'' . $escaped . '\')'
			);
		}

		$fields = $this->getEntityManager()->getRepository('DeskPRO:CustomDefOrganization')->getTopFields();
		foreach ($fields AS $field) {
			$escaped = addslashes($field->title);
			$return['fields']['tickets']['orgfield' . $field->id] = array(
				"organizations's " . $field->title, 'ALIAS(%s.organization.custom_data[' . $field->id . '], \'' . $escaped . '\')'
			);
		}

		$return['fields']['tickets']['none'] = array('nothing', 'NULL');

		return $return;
	}
}
