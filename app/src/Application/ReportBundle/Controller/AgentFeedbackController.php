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

namespace Application\ReportBundle\Controller;

use Application\DeskPRO\App;

class AgentFeedbackController extends AbstractController
{
    public function summaryAction($date)
    {
		if (!$date) {
			$date = date('Y-m');
		}

		$dt = new \DateTime('now', new \DateTimeZone('UTC'));
        list($year, $month) = explode('-', $date);
        $dt->setDate($year, $month, 1);
        $dt->setTime(0, 0, 0);

		$date_start = $dt->format('Y-m-d H:i:s');
		$date_end   = $dt->setTime(23,23,23)->setDate($year, $month, \Orb\Util\Dates::daysInMonth($month, $year))->format('Y-m-d H:i:s');

		$all_feedback = $this->container->getDb()->fetchAll("
			SELECT ticket_feedback.rating, UNIX_TIMESTAMP(ticket_feedback.date_created) AS created_at, tickets_messages.person_id AS agent_id
			FROM ticket_feedback
			LEFT JOIN tickets_messages ON (tickets_messages.id = ticket_feedback.message_id)
			WHERE ticket_feedback.date_created BETWEEN ? AND ?
		", array($date_start, $date_end));

		$vars = array();

        $all_agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
        $first_created = $this->em->getRepository('DeskPRO:TicketFeedback')->getFirstCreatedDate();

		$days = array();
        $days_in_month = \Orb\Util\Dates::daysInMonth($month, $year);
        $day_date = clone $dt;

		 for($i = 1; $i <= $days_in_month; $i++) {
            $days[] = $day_date;
            $day_date = clone $day_date;
            $day_date->add(new \DateInterval('P1D'));
        }

		foreach($all_agents as $agent) {
            $totals[$agent['id']] = array(-1 => 0, 1 => 0, 0 => 0);
        }

		$summary = array();
		$totals = array();

		foreach ($all_feedback as $feedback) {
			$d = date('j', $feedback['created_at']);
			$agent_id = $feedback['agent_id'];
			$rating   = $feedback['rating'];

			if (!isset($summary[$d])) {
				$summary[$d] = array();
			}
			if (!isset($summary[$d][$agent_id])) {
				$summary[$d][$agent_id] = array();
			}

			if (!isset($summary[$d][$agent_id][$rating])) {
				$summary[$d][$agent_id][$rating] = 0;
			}

			$summary[$d][$agent_id][$rating]++;

			if (!isset($totals[$agent_id])) {
				$totals[$agent_id] = array();
			}

			if (!isset($totals[$agent_id][$rating])) {
				$totals[$agent_id][$rating] = 0;
			}

			$totals[$agent_id][$rating]++;
		}

        $vars['first_created'] = $first_created;
        $vars['agents'] = $all_agents;
        $vars['summary'] = $summary;
        $vars['totals'] = $totals;
        $vars['days'] = $days;
        $vars['view_date'] = $dt;

        return $this->render('ReportBundle:AgentFeedback:summary.html.twig', $vars);
    }

    public function feedAction($page)
    {
        $vars = array();
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('DeskPRO:TicketFeedback');
        $feedback = $repo->getFeedbackForFeed($page);
        $count = $repo->getCountForPaging();

        $vars['feedback'] = $feedback;
        $vars['count'] = $count;
        $vars['page'] = $page;

        return $this->render('ReportBundle:AgentFeedback:feed.html.twig', $vars);
    }

    private function createMysqlDateRangeForUser($date)
    {
        // Let the date be reused!
        $date = clone $date;

        // Apply the user's timezone offset.
        $start_date = $date->setTimezone($this->person->getDateTimezone());

        // The timezone offset will already be applied, so no need to reapply.
        $end_date = clone $start_date;
        // Make this represent the end of the day.
        $end_date->add(new \DateInterval('P1D'));
        // Remove a single second to stop overlap, for cases where the comparison is (date >= start and date <= end).
        $end_date->sub(new \DateInterval('PT1S'));

        // Package using MySQL date format.
        $date_range = array(
            'start' => $start_date->format('Y-m-d H:i:s'),
            'end' => $end_date->format('Y-m-d H:i:s')
        );

        return $date_range;
    }

    private function mysqlDateToPhpDate($mysql_date)
    {
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $mysql_date, new \DateTimeZone('UTC'));
        $dt->setTimeZone($this->person->getDateTimezone());
        return $dt;
    }
}