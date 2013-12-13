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

class AgentActivityController extends AbstractController
{
    public function indexAction()
    {
		$dt = $this->person->getDateTime();
		$dt->setTime(0,0,0);

        return $this->listAction('0', $dt->format('Y-m-d'));
    }

    public function listAction($agent_or_team_id, $date)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $vars = array(
            'hide_unknown' => 1,
        );
        $date = $this->createDateFromParamString($date);
        $all_agents = $em->getRepository('DeskPRO:Person')->getAgents();

		$agent_id = null;
		$team_id = null;
        if (preg_match('/^team-(\d+)$/', $agent_or_team_id, $match)) {
			$team_id = $match[1];
			$agent_list = $em->getRepository('DeskPRO:AgentTeam')->getMembers($match[1]);
		} else if ($agent_or_team_id && ctype_digit($agent_or_team_id)) {
			$agent_id = $agent_or_team_id;
            $agent_list = array($em->getRepository('DeskPRO:Person')->find($agent_or_team_id));
        } else {
			$agent_list = false;
		}

		if (!$agent_list) {
			$agent_or_team_id = '0';
            $agent_list = $all_agents;
        }

        $activity = array();
        $agents = array();

        foreach ($agent_list as $agent) {
           $logs = array();
            $chats = $this->getChatLogForAgent($agent, $date);

            if(!empty($chats)) {
                $logs = array_merge_recursive($logs, $chats);
            }

            $ticket_logs = $this->getTicketLogForAgent($agent, $date);

            if(!empty($ticket_logs)) {
                $logs = array_merge_recursive($logs, $ticket_logs);
            }

            $revisions = $this->getRevistionsForAgent($agent, $date);

            if(!empty($revisions)) {
                $logs = array_merge_recursive($logs, $revisions);
            }

            if(!empty($logs)) {
                foreach($logs as $hour => $by_minute) {
                    $minutely = array();

                    foreach($by_minute as $minute => $item) {
                        $minutely[trim($minute, '_')] = $item;
                    }

                    ksort($minutely, SORT_NUMERIC);
                    $logs[$hour] = $minutely;
                }

                $agents[$agent['id']] = $agent;
                $activity[$agent['id']] = $logs;
            }
        }

        $vars['agents'] = $agents;
        $vars['activity'] = $activity;
        $vars['agent_or_team_id'] = $agent_or_team_id;
        $vars['all_agents'] = $all_agents;
		$vars['agent_teams'] = $em->getRepository('DeskPRO:AgentTeam')->getTeams();
        $vars['view_date'] = $date;
        $vars['today'] = new \DateTime('now', new \DateTimeZone('UTC'));
		$vars['agent_id'] = $agent_id;
		$vars['team_id'] = $team_id;

		if ($date->format('Y-m-d') != date('Y-m-d')) {
			$d2 = clone $date;
			$d2->add(new \DateInterval('PT24H'));
			$vars['view_next_date'] = $d2;
		}

		$d2 = clone $date;
		$d2->sub(new \DateInterval('PT24H'));
		$vars['view_prev_date'] = $d2;

        return $this->render('ReportBundle:AgentActivity:index.html.twig', $vars);
    }

    private function getRevistionsForAgent($agent, $date) {
        $items = array('News', 'Article', 'Download', 'Feedback');
        $em = $this->getDoctrine()->getEntityManager();
        $counts_hourly = array();

        foreach($items as $item) {
            $item_lc = strtolower($item);

            $revisions = $em->getRepository('DeskPRO:'.$item.'Revision')->getRevisionsForAgent(
                $agent,
                array('date_range' => $this->createMysqlDateRangeForUser($date))
            );

            foreach($revisions as $revision) {
                $date_created = $this->mysqlDateToPhpDate($revision['date_created']->format('Y-m-d H:i:s'));
                $hour = $date_created->format('G');
                $minute = (int)$date_created->format('i');

                if(!isset($counts_hourly[$item_lc])) {
                    $counts_hourly[$item_lc] = array();
                }

                if(!isset($counts_hourly['_'.$hour]['_'.$minute])) {
                    $counts_hourly['_'.$hour]['_'.$minute] = array();
                }

                $counts_hourly['_'.$hour]['_'.$minute][] = array('type' => $item_lc, 'data' => $revision);
            }
        }

        return $counts_hourly;
    }

    private function getTicketLogForAgent($agent, $date) {
        $em = $this->getDoctrine()->getEntityManager();
        $counts_hourly = array();
        $logs = $em->getRepository('DeskPRO:TicketLog')->getLogsForAgent(
            $agent,
            array('date_range' => $this->createMysqlDateRangeForUser($date))
        );

        foreach($logs as $log) {
            $date = $this->mysqlDateToPhpDate($log['date_created']->format('Y-m-d H:i:s'));
            $hour = $date->format('G');
            $minute = (int)$date->format('i');

            if(!isset($counts_hourly['_'.$hour])) {
                $counts_hourly['_'.$hour] = array();
            }

            if(!isset($counts_hourly['_'.$hour]['_'.$minute])) {
                $counts_hourly['_'.$hour]['_'.$minute] = array();
            }

            $counts_hourly['_'.$hour]['_'.$minute][] = array('type' => 'ticket', 'data' => $log);
        }

        return $counts_hourly;
    }

    private function getChatLogForAgent($agent, $date) {
        $date_range = $this->createMysqlDateRangeForUser($date);
        $db = $this->getDoctrine()->getConnection();
        $em = $this->getDoctrine()->getEntityManager();

        // Could GROUP BY HOUR(date_created), but as timezones are in effect, it is easier to do this in PHP.
        $messages = $db->fetchAll(
            'SELECT cm.date_created, conversation_id
            FROM chat_messages AS cm
            INNER JOIN chat_conversations AS cc
            ON cc.id = conversation_id
            WHERE agent_id = ? AND cm.date_created BETWEEN ? AND ?',
            array($agent['id'], $date_range['start'], $date_range['end'])
        );

        $counts_hourly = array();

        foreach($messages as $message) {
            $date = $this->mysqlDateToPhpDate($message['date_created']);
            $hour = $date->format('G');
            $minute = (int)$date->format('i');

            if(!isset($counts_hourly[$hour])) {
                $counts_hourly[$hour] = array();
            }

            if(!isset($counts_hourly[$hour][$message['conversation_id']])) {
                $counts_hourly[$hour][$message['conversation_id']] = array('count' => 1, 'last' => $minute);
            }
            else {
                $counts_hourly[$hour][$message['conversation_id']]['count']++;

                if($counts_hourly[$hour][$message['conversation_id']]['last'] < $minute) {
                    $counts_hourly[$hour][$message['conversation_id']]['last'] = $minute;
                }
            }
        }

        $counts = array();

        foreach($counts_hourly as $hour => $stats) {
            $hour = '_'.$hour;
            $counts[$hour] = array();

            foreach($stats as $convo_id => $stat) {
                $convo = $em->getRepository('DeskPRO:ChatConversation')->find($convo_id);
                $minute = '_'.$stat['last'];

                if(!isset($counts[$hour][$minute])) {
                    $counts[$hour][$minute] = array();
                }

                $counts[$hour][$minute][] = array('type' => 'chat', 'count' => $stat['count'], 'conversation' => $convo);
            }
        }

        return $counts;
    }

    private function createDateFromParamString($date_str)
    {
		if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $date_str)) {
			$dt = new \DateTime();
			$dt->setTimezone($this->person->getDateTimezone());
			$dt->setTime(0,0,0);
			return $dt;
		}

		$dt = new \DateTime();
		$dt->setTimezone($this->person->getDateTimezone());
		$dt->setTime(0,0,0);
		list($year, $month, $day) = explode('-', $date_str);
		$dt->setDate($year, $month, $day);
		$dt->setTime(0,0,0);

        return $dt;
    }

    private function createMysqlDateRangeForUser($date)
    {
        // Let the date be reused!
        $date = clone $date;

        // Apply the user's timezone offset.
        $start_date = clone $date;
		$start_date->setTimezone(new \DateTimeZone('UTC'));

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