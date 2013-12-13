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
 * Misc
 */
class TicketFeaturesController extends AbstractController
{
	############################################################################
	# index
	############################################################################

	public function indexAction()
	{
		$message_templates = $this->container->getDataService('TicketMessageTemplate')->getAll();
		$work_hours = unserialize(App::getSetting('core_tickets.work_hours'));

		return $this->render('AdminBundle:TicketFeatures:index.html.twig', array(
			'work_hours' => $work_hours,
			'message_templates' => $message_templates
		));
	}

	public function workHoursAction()
	{
		$work_hours = unserialize(App::getSetting('core_tickets.work_hours'));
		if (empty($work_hours['timezone'])) {
			$work_hours['timezone'] = App::getSetting('core.default_timezone');
		}

		$current_year = date('Y');
		$years = array($current_year, $current_year + 1, $current_year + 2, $current_year + 3);

		$translator = App::getTranslator();

		$holidays_sorted = $work_hours['holidays'];
		uasort($holidays_sorted, function($a, $b) {
			if ($a['month'] < $b['month']) {
				return -1;
			}
			if ($a['month'] > $b['month']) {
				return 1;
			}
			if ($a['day'] < $b['day']) {
				return -1;
			}
			if ($a['day'] > $b['day']) {
				return 1;
			}

			return 0; // same month and day
		});

		return $this->render('AdminBundle:TicketFeatures:work-hours.html.twig', array(
			'work_hours' => $work_hours,
			'holidays_sorted' => $holidays_sorted,
			'years' => $years,
			'months' => array(
				1 => $translator->phrase('agent.time.short-month_january'),
				2 => $translator->phrase('agent.time.short-month_february'),
				3 => $translator->phrase('agent.time.short-month_march'),
				4 => $translator->phrase('agent.time.short-month_april'),
				5 => $translator->phrase('agent.time.short-month_may'),
				6 => $translator->phrase('agent.time.short-month_june'),
				7 => $translator->phrase('agent.time.short-month_july'),
				8 => $translator->phrase('agent.time.short-month_august'),
				9 => $translator->phrase('agent.time.short-month_september'),
				10 => $translator->phrase('agent.time.short-month_october'),
				11 => $translator->phrase('agent.time.short-month_november'),
				12 => $translator->phrase('agent.time.short-month_december'),
			),
			'timezones' => \DateTimeZone::listIdentifiers()
		));
	}

	public function workHoursSaveAction()
	{
		$work_hours = array(
			'active_time' => $this->in->getString('active_time'),
			'start_hour' => $this->in->getUint('start_hour'),
			'start_minute' => $this->in->getUint('start_minute'),
			'end_hour' => $this->in->getUint('end_hour'),
			'end_minute' => $this->in->getUint('end_minute'),
			'timezone' => $this->in->getString('timezone'),
			'days' => array(),
			'holidays' => array()
		);
		foreach ($this->in->getCleanValueArray('days', 'uint') AS $day) {
			$work_hours['days'][$day] = true;
		}
		foreach ($this->in->getCleanValueArray('work_holidays', 'raw', 'discard') AS $holiday) {
			if (!$holiday['year']) {
				$year = null;
			} else {
				$year = intval($holiday['year']);
			}

			$month = intval($holiday['month']);
			$day = intval($holiday['day']);

			foreach ($work_hours['holidays'] AS $existing) {
				if ($existing['day'] == $day && $existing['month'] == $month && $existing['year'] === $year) {
					continue 2;
				}
			}

			$work_hours['holidays'][] = array(
				'name' => $holiday['name'],
				'day' => $day,
				'month' => $month,
				'year' => $year
			);
		}

		$this->container->getSettingsHandler()->setSetting('core_tickets.work_hours', serialize($work_hours));

		return $this->redirectRoute('admin_features');
	}

	############################################################################
	# refill-search-tables
	############################################################################

	public function regenSearchAction()
	{
		$res = '<pre>';
		$res .= 'Regenerating tickets_search_active table ... ';

		$time_start = microtime(true);
		App::getEntityRepository('DeskPRO:Ticket')->fillSearchTable();
		$time_end = microtime(true);

		$res .= sprintf("Done in %.4f seconds\n", $time_end-$time_start);
		$res .= '</pre>';

		return new \Symfony\Component\HttpFoundation\Response($res);
	}

	############################################################################
	# purge-trash
	############################################################################

	public function purgeTrashAction($security_token)
	{
		$this->ensureAuthToken('purge_trash', $security_token);

		$res = '<pre>';
		$ticket_ids = App::getDb()->fetchAllCol("
			SELECT id
			FROM tickets
			WHERE status = 'hidden' AND hidden_status = 'deleted'
		");

		$deleted = 0;
		if ($ticket_ids) {
			$ticket_ids = implode(',', $ticket_ids);
			$deleted = App::getDb()->executeUpdate("DELETE FROM tickets WHERE id IN ($ticket_ids)");
			App::getDb()->executeUpdate("DELETE FROM tickets_search_active WHERE id IN ($ticket_ids)");
			App::getDb()->executeUpdate("DELETE FROM tickets_search_message WHERE id IN ($ticket_ids)");
			App::getDb()->executeUpdate("DELETE FROM tickets_search_message_active WHERE id IN ($ticket_ids)");
			App::getDb()->executeUpdate("DELETE FROM tickets_search_subject WHERE id IN ($ticket_ids)");
		}

		$res .= sprintf("Deleted %d tickets\n", $deleted);

		$ticket_ids = App::getDb()->fetchAllCol("
			SELECT id
			FROM tickets
			WHERE status = 'hidden' AND hidden_status = 'spam'
		");

		$deleted_spam = 0;
		if ($ticket_ids) {
			$ticket_ids = implode(',', $ticket_ids);
			$deleted_spam = App::getDb()->executeUpdate("DELETE FROM tickets WHERE id IN ($ticket_ids)");
			App::getDb()->executeUpdate("DELETE FROM tickets_search_active WHERE id IN ($ticket_ids)");
			App::getDb()->executeUpdate("DELETE FROM tickets_search_message WHERE id IN ($ticket_ids)");
			App::getDb()->executeUpdate("DELETE FROM tickets_search_message_active WHERE id IN ($ticket_ids)");
			App::getDb()->executeUpdate("DELETE FROM tickets_search_subject WHERE id IN ($ticket_ids)");
		}

		$res .= sprintf("Deleted %d spam tickets\n", $deleted_spam);

		$res .= "Done\n";
		$res .= '</pre>';

		return new \Symfony\Component\HttpFoundation\Response($res);
	}
}
